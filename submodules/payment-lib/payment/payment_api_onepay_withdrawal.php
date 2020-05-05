<?php
require_once dirname(__FILE__) . '/abstract_payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_WITHDRAWAL_PAYMENT_API, ID: 982
 * *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.onepay.solutions/v2/distribute/withdraw.html
 * * Extra Info:
 * > {
 * >    "onepay_priv_key": "## Private Key ##",
 * >    "onepay_pub_key": "## Public Key ##",
 * >    "curl_headers": [
            "Content-Type: application/json"
        ]
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_onepay_withdrawal extends Abstract_payment_api_onepay {
    const FLAG_SUCCESS = "SUCCESS";
    const STATUS_SUCCESS = 5;
    const STATUS_FAILED = 7;

    public function getPlatformCode() {
        return ONEPAY_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay_withdrawal';
    }

    # Implement abstract function but do nothing
    protected function configParams(&$params, $direct_pay_extra_info) {}
    protected function processPaymentUrlForm($params) {}

    public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
        $result = array('success' => false, 'message' => 'payment failed');

        if(!$this->isAllowWithdraw()) {
            $result['message'] = lang("Withdraw not allowed with this API");
            return $result;
        }

        if(!array_key_exists($bank, $this->getBankInfo())) {
            $this->CI->utils->debug_log('======================================onepay submitWithdrawRequest bank whose bankTypeId=[$bank] is not supported by onepay');
            return array('success' => false, 'message' => 'Bank not supported by onepay');
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        $url = $this->getWithdrawUrl();
        list($response, $response_result) = $this->submitPostForm($url, $params, true, $transId, true);

        $decodedResult = $this->decodeResult($response);
        $decodedResult['response_result'] = $response_result;

        $this->CI->utils->debug_log('======================================onepay submitWithdrawRequest url: ', $url );
        $this->CI->utils->debug_log('======================================onepay submitWithdrawRequest params: ', $params);
        $this->CI->utils->debug_log('======================================onepay submitWithdrawRequest response', $response);
        $this->CI->utils->debug_log('======================================onepay submitWithdrawRequest decoded Result', $decodedResult);

        return $decodedResult;
    }

    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));
        # look up bank code
        $bankInfo = $this->getBankInfo();
        $bankName = $bankInfo[$bank];

        $playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
        $this->utils->debug_log("Get playerBankDetails using [$bank] + [$accNum]", $playerBankDetails);
        if(!empty($playerBankDetails)){
            $bankBranch = empty($playerBankDetails['branch']) ? "无" : $playerBankDetails['branch'];
            $province = empty($playerBankDetails['province']) ? "无" : $playerBankDetails['province'];
            $city = empty($playerBankDetails['city']) ? "无" : $playerBankDetails['city'];
        } else {
            $bankBranch = '无';
            $province = '无';
            $city = '无';
        }

        $params = array();
        $params['merchantId']    = $this->getSystemInfo("account");
        $params['batchNo']       = $transId;
        $params['batchRecord']   = "1";
        $params['currencyCode']  = "CNY";
        $params['totalAmount']   = $this->convertAmountToCurrency($amount);
        $params['payDate']       = date("Ymd");
        $params['isWithdrawNow'] = $this->getSystemInfo("isWithdrawNow",'2');
        $params['notifyUrl']     = $this->getNotifyUrl($transId);
        $params['signType']      = 'RSA';
        $params['sign']          = $this->sign($params);
        $params['detailList']    = array(
            array(
                'receiveType'  => '个人',
                'accountType'  => '储蓄卡',
                'serialNo'     => $transId,
                'amount'       => $this->convertAmountToCurrency($amount),
                'purpose'      => '1013',
                'bankName'     => $bankName,
                'subBankName'  => $bankBranch,
                'bankNo'       => $accNum,
                'bankProvince' => $province,
                'bankCity'     => $city,
                'receiveName'  => $name
            )
        );

        return $params;
    }

    public function decodeResult($resultString, $queryAPI = false) {
        if(is_array($resultString) && isset($resultString['lock']) && isset($resultString['msg'])) {
            $result = array('success' => false, 'message' => $resultString['msg'], 'lock' => true);
            return $result;
        }
        $this->utils->debug_log("=========================onepay decodeResult resultString", $resultString);

        $result = json_decode($resultString, true);
        $this->utils->debug_log("=========================onepay decodeResult json decoded", $result);

        #when success
        if($result['flag'] == self::FLAG_SUCCESS) {
            $success = true;
            $message = "Onepay withdrawal successful. BatchNo: ". $result['data']['batchNo'];

            if($queryAPI) {
                $statusCode = $result['data']['status'];

                if($statusCode == self::STATUS_SUCCESS){
                    $message = "Onepay withdrawal success! BatchNo: ". $result['data']['batchNo'];
                }
                else if ($statusCode == self::STATUS_FAILED) {
                    $message = "Onepay withdrawal failed. [".$statusCode."]: ". $result['detailList']['reason'];
                    return array('success' => false, 'message' => $message, 'payment_fail' => true);
                }
                else {
                    $message = "Onepay withdrawal response [".$statusCode."]: ". $result['detailList']['reason'];
                    return array('success' => false, 'message' => $message);
                }

                $message = "Onepay withdrawal success! batchNo: ". $result['data']['batchNo'];
            }

            return array('success' => $success, 'message' => $message);
        } else {
            $message = "Onepay response failed. [".$result['errorCode']."]: ".$result['errorMsg'];

            return array('success' => false, 'message' => $message);
        }

        return array('success' => false, 'message' => "Decode failed");
    }

    ## This function provides a way to manually check withdraw status. Useful when API does not provide a callback.
    ## Returns array('success' => false, 'payment_fail' => false, 'message' => 'Error message')
    ## 'success' means whether payment is successful, 'payment_fail' means if payment is not successful, shall we mark it as failed or shall we wait
    public function checkWithdrawStatus($transId) {

        $params = array();
        $params['merchantId'] = $this->getSystemInfo("account");
        $params['batchNo']    = $transId;
        $params['signType']   = 'RSA';
        $params['sign']       = $this->sign($params);

        $url = $this->getSystemInfo('check_status_url');
        $response = $this->submitPostForm($url, $params, true, $transId);
        $decodedResult = $this->decodeResult($response, true);

        $this->CI->utils->debug_log('======================================onepay checkWithdrawStatus params: ', $params);
        $this->CI->utils->debug_log('======================================onepay checkWithdrawStatus url: ', $url );
        $this->CI->utils->debug_log('======================================onepay checkWithdrawStatus result: ', $response);
        return $decodedResult;
    }


    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);
        if (empty($params)) {
            $raw_post_data = file_get_contents('php://input', 'r');
            $this->CI->utils->debug_log("=====================onepay raw_post_data", $raw_post_data);
            $params = json_decode($raw_post_data, true);
            $this->CI->utils->debug_log("=====================onepay json_decode params", $params);
        }

        $result = array('success' => false, 'message' => 'Payment failed');

        $this->CI->utils->debug_log('=========================onepay callbackFromServer transId', $transId);
        $this->CI->utils->debug_log("=========================onepay callbackFromServer params", $params);

        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        if (!$this->checkCallbackOrder($order, $params['data'])) {
            return $result;
        }

        if ($params['data']['status'] == self::STATUS_SUCCESS) {
            $msg = sprintf('Onepay withdrawal success: trade ID [%s]', $params['data']['batchNo']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;
        }
        else if ($params['data']['status'] == self::STATUS_FAILED) {
            $msg = sprintf('Onepay withdrawal failed: [%s]', $params['detailList']['reason']);
            $this->writePaymentErrorLog($msg, $fields);
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
            $result['message'] = $msg;
        }
        else {
            $msg = sprintf('Onepay withdrawal payment was not successful: [%s]', $params['detailList']['reason']);
            $this->writePaymentErrorLog($msg, $fields);
            $result['message'] = $msg;
        }

        return $result;
    }

    private function checkCallbackOrder($order, $fields) {
        $requiredFields = array(
            'totalAmount', 'batchNo', 'status', 'sign'
        );
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("======================onepay withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }

        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=========================onepay withdrawal checkCallback signature Error', $fields);
            return false;
        }

        if ($fields['totalAmount'] != $order['amount']) {
            $this->writePaymentErrorLog('=========================onepay withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['batchNo'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================onepay withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function callbackFromBrowser($transId, $params) {
        return array('success' => false, 'next_url' => null, 'message' => 'Error: not implemented');
    }

    public function getBankInfo() {
        $bankInfo = array();
        $bankInfoArr = $this->getSystemInfo("withdrawal_bank_info");
        if(!empty($bankInfoArr)) {
               foreach ($bankInfoArr as $system_bank_type_id => $bankInfoItem) {
                    $bankInfo[$system_bank_type_id] = $bankInfoItem['code'];
               }
            $this->utils->debug_log("==================getting onepay bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1'  => 'ICBC',    #中国工商银行
                '2'  => 'CMB',     #招商银行
                '3'  => 'CCB',     #中国建设银行
                '4'  => 'ABC',     #中国农业银行
                '5'  => 'COMM',    #交通银行
                '6'  => 'BOC',     #中国银行
                '8'  => 'GDB',     #广发银行
                '10' => 'CITIC',   #中信银行
                '11' => 'CMBC',    #民生银行
                '13' => 'CIB',     #兴业银行
                '14' => 'HXB',     #华夏银行
                '15' => 'SPABANK', #平安银行
                '20' => 'CEB',     #中国光大银行
                '32' => 'SPDB',    #浦发银行
            );
            $this->utils->debug_log("=======================getting onepay bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }

    # -- signing --
    protected function validateSign($params) {
        $signStr = $this->createSignStr($params);
        $sign = base64_decode($params['sign']); #not hex
        $valid = openssl_verify($signStr, $sign, $this->getPubKey());
        if(!$valid){
            #try hex
          
            $sign = base64_decode(hex2bin($params['sign']));
            $valid = openssl_verify($signStr, $sign, $this->getPubKey());
            if(!$valid){
                $this->utils->debug_log("=======================onepay validateSign still not valid, recheck try include totalFactorage", $valid);
                $signStr = $this->createWithdrawalSignStr($params);
                $valid = openssl_verify($signStr, $sign, $this->getPubKey());
                if(!$valid){
                    $this->writePaymentErrorLog("=========================onepay validateSign error", $valid);
                }
            }
        }
        return $valid;
    }

    protected function createWithdrawalSignStr($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if($value == null || $key == 'signType' || $key == 'sign' || $key == 'detailList') { #'totalFactorage' need to be include
                continue;
            }
            $signStr .= "$key=$value&";
        }
        return rtrim($signStr, '&');
    }

}