<?php
require_once dirname(__FILE__) . '/abstract_payment_api_ysydai.php';

/**
 * YSYDAI
 *
 * * YSYDAI_WITHDRAWAL_PAYMENT_API, ID: 5265
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: http://paypaul.385mall.top/onlinepay/agentTransfer
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_ysydai_withdrawal extends Abstract_payment_api_ysydai {

    const RESULT_STATUS_SUCCESS = "0";
    const PAYMENT_STATUS_SUCCESS    = "0";
    const PAYMENT_STATUS_PROCESSING = "1";
    const PAYMENT_STATUS_FAILED     = "2";

    public function getPlatformCode() {
        return YSYDAI_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'ysydai_withdrawal';
    }

    # Implement abstract function but do nothing
    protected function configParams(&$params, $direct_pay_extra_info){}
    protected function processPaymentUrlForm($params){}

    public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
        $result = array('success' => false, 'message' => 'payment failed');

        if(!$this->isAllowWithdraw()) {
            $result['message'] = lang("Withdraw not allowed with this API");
            return $result;
        }
        if(!array_key_exists($bank, $this->getBankInfo())) {
            $this->utils->error_log("========================ysydai submitWithdrawRequest bank whose bankTypeId=[$bank] is not supported by ysydai");
            return array('success' => false, 'message' => 'Bank not supported by YSYDAI');
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        $url = $this->getWithdrawUrl();
        list($content, $response_result) = $this->submitPostForm($url, $params, true, $transId, true);

        $decodedResult = $this->decodeResult($content);
        $decodedResult['response_result'] = $response_result;
        $this->CI->utils->debug_log('=========================ysydai submitWithdrawRequest decoded Result', $decodedResult);

        return $decodedResult;
    }

    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        $this->CI->load->model(array('player_model', 'playerbankdetails', 'wallet_model'));

        # look up bank code
        $bankInfo = $this->getBankInfo();
        $playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
        $walletaccount = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);
        $this->utils->debug_log("Get playerBankDetails using [$bank] + [$accNum]", $playerBankDetails);
        if(!empty($playerBankDetails)){
            $province    = empty($playerBankDetails['province'])    ? "无" : $playerBankDetails['province'];
            $city        = empty($playerBankDetails['city'])        ? "无" : $playerBankDetails['city'];
            $bankBranch  = empty($playerBankDetails['branch'])      ? "无" : $playerBankDetails['branch'];
        } else {
            $bankBranch  = '无';
            $province    = '无';
            $city        = '无';
        }

        $datetime = new DateTime($walletaccount['dwDateTime']);

        $params = array();
        $params['cpid']                  = $this->getSystemInfo('account');
        $params['cp_df_orderno']         = $transId;
        $params['pay_type']              = $this->getSystemInfo('pay_type', self::PAYTYPE_ONLINEBANK);
        $params['pay_day']               = $datetime->format('Y-m-d');
        $params['acount_name']           = $name;
        $params['acount_bank']           = $bankInfo[$bank]['name'].$bankBranch;
        $params['acount_province']       = $province;
        $params['acount_city']           = $city;
        $params['acount_num']            = $accNum;
        $params['acount_type']           = '0'; #0-借记卡，1-贷记卡，2-对公账号
        $params['acount_bank_branch_no'] = '000000000000'; #支行联行号
        $params['apply_fee']             = $this->convertAmountToCurrency($amount);
        $params['apply_type']            = "301";
        $params['notify_url']            = $this->getNotifyUrl($transId);
        $params['sign']                  = $this->sign($params);

        return $params;
    }

    public function decodeResult($resultString, $queryAPI = false) {
        if(is_array($resultString) && isset($resultString['lock']) && isset($resultString['msg'])) {
            $result = array('success' => false, 'message' => $resultString['msg'], 'lock' => true);
            return $result;
        }
        $result = array('success' => false, 'message' => 'YSYDAI decoded fail.');
        $response = json_decode($resultString, true);
        $this->CI->utils->debug_log('==============ysydai submitWithdrawRequest decodeResult json decoded', $response);
        $returnCode = isset($response['result_code']) ? $response['result_code'] : $resultString;
        $msg        = array_key_exists($returnCode, self::ERROR_MSG) ? self::ERROR_MSG[$returnCode] : '未知错误，请联系支付商';
        $returnDesc = isset($response['msg']) ? $response['msg'] : $msg;

        if($queryAPI){
            $result = array('success' => false, 'message' => 'YSYDAI check status decoded fail.', 'payment_fail' => false);
            if($returnCode === self::PAYMENT_STATUS_SUCCESS){
                $result['success'] = true;
                $result['message'] = "YSYDAI withdrawal success! [".$returnCode."]".$returnDesc.', pay_orderno: '.$response['pay_orderno'];
            }
            elseif($returnCode === self::PAYMENT_STATUS_PROCESSING){
                $result['message'] = "YSYDAI withdrawal still processing. [".$returnCode."]".$returnDesc;
            }
            elseif($returnCode === self::PAYMENT_STATUS_FAILED){
                $result['payment_fail'] = true;
                $result['message'] = "YSYDAI withdrawal failed. [".$returnCode."]".$returnDesc;
            }
            else{
                $result['message'] = "YSYDAI withdrawal response [".$returnCode."]".$returnDesc;
            }
        }
        else{
            if($returnCode === self::RESULT_STATUS_SUCCESS) {
                $result['success'] = true;
                $result['message'] = "YSYDAI withdrawal response success! [".$returnCode."]".$returnDesc;
            }
            else{
                $result['message'] = "YSYDAI withdrawal response failed. [".$returnCode."]: ".$returnDesc;
            }
        }
        return $result;
    }

    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);
        $result = array('success' => false, 'message' => 'Payment failed');
        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        if (empty($params)) {
            $raw_post_data = file_get_contents('php://input', 'r');
            $this->CI->utils->debug_log("=====================ysydai withdrawal callbackFromServer raw_post_data", $raw_post_data);
            $params = json_decode($raw_post_data,true);
            $this->CI->utils->debug_log("=====================ysydai withdrawal callbackFromServer json_decode params", $params);
        }
        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        $statusCode = $params['result_code'];
        if($statusCode == self::CALLBACK_STATUS_SUCCESS) {
            $msg = "YSYDAI withdrawal success!";
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;
        }
        else if($statusCode == self::CALLBACK_STATUS_FAILED){
            $msg = "YSYDAI withdrawal failed.";
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
            $result['message'] = $msg;
        }
        else {
            $msg = "YSYDAI withdrawal response [".$params['resultcode']."]: ". $params['resultmsg'];
            $this->writePaymentErrorLog($msg, $fields);
            $result['message'] = $msg;
        }

        return $result;
    }

    private function checkCallbackOrder($order, $fields) {
        $requiredFields = array(
            'cpid', 'cp_df_orderno', 'success_fee', 'result_code', 'sign'
        );
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("======================ysydai withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }

        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=========================ysydai withdrawal checkCallback signature Error', $fields);
            return false;
        }

        if ($fields['success_fee'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================ysydai withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['cp_df_orderno'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================ysydai withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function checkWithdrawStatus($transId) {
        $this->CI->load->model(array('wallet_model'));
        $walletaccount = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);
        $datetime = new DateTime($walletaccount['dwDateTime']);

        $params = array();
        $params['cpid']                  = $this->getSystemInfo('account');
        $params['pay_day']               = $datetime->format('Y-m-d');
        $params['cp_df_orderno']         = $transId;
        $params['sign']                  = $this->sign($params);
        $this->CI->utils->debug_log('======================================ysydai checkWithdrawStatus params: ', $params);

        $url = $this->getSystemInfo('check_status_url', 'http://df.ysydai.cn/dfresult.php');
        $response = $this->submitPostForm($url, $params, true, $transId);
        $decodedResult = $this->decodeResult($response, true);

        $this->CI->utils->debug_log('======================================ysydai checkWithdrawStatus result: ', $response);
        return $decodedResult;
    }

    public function getBankInfo() {
        $bankInfo = array();
        $bankInfoArr = $this->getSystemInfo("withdrawal_bank_info");
        if(!empty($bankInfoArr)) {
            foreach($bankInfoArr as $system_bank_type_id => $bankInfoItem) {
                if(isset($bankInfoItem['name'])){
                    $bankInfo[$system_bank_type_id]['name'] = $bankInfoItem['name'];
                }
                if(isset($bankInfoItem['code'])){
                    $bankInfo[$system_bank_type_id]['code'] = $bankInfoItem['code'];
                }
            }
            $this->utils->debug_log("=========================ysydai bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '工商银行'),
                '2' => array('name' => '招商银行'),
                '3' => array('name' => '建设银行'),
                '4' => array('name' => '农业银行'),
                '5' => array('name' => '交通银行'),
                '6' => array('name' => '中国银行'),
                '7' => array('name' => '深圳发展银行'),
                '8' => array('name' => '广东发展银行'),
                '9' => array('name' => '东莞农商银行'),
                '10' => array('name' => '中信银行'),
                '11' => array('name' => '民生银行'),
                '12' => array('name' => '邮储银行'),
                '13' => array('name' => '兴业银行'),
                '14' => array('name' => '华夏银行'),
                '15' => array('name' => '平安银行'),
                '18' => array('name' => '南京银行'),
                '20' => array('name' => '光大银行'),
                '24' => array('name' => '上海浦东发展银行'),
                '26' => array('name' => '广东发展银行'),
                '27' => array('name' => '上海浦东发展银行'),
                '29' => array('name' => '北京银行'),
                '31' => array('name' => '上海银行'),
                '33' => array('name' => '北京农商'),
            );
            $this->utils->debug_log("=========================ysydai bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }
}