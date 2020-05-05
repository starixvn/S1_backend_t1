<?php
require_once dirname(__FILE__) . '/abstract_payment_api_wanweipay.php';
/**
 * WANWEIPAY
 *
 * * WANWEIPAY_WITHDRAWAL_PAYMENT_API, ID: 5665
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 * * URL
 *
 * Field Values:
 * * Account: ## APP ID ##
 * * Key: ## APP KEY ##
 * * Secret: ## APP SECRET ##
 * * URL: https://pre-prod.api.247pay.site/api/pay/query_order
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_wanweipay_withdrawal extends Abstract_payment_api_wanweipay {
    const CALLBACK_STATUS_SUCCESS    = 2;
    const CALLBACK_STATUS_FAILED     = 3;
    const RESPONSE_CODE_SUCCESS = '100';
    const RETURN_SUCCESS_CODE     = 'OK';

    public function getPlatformCode() {
        return WANWEIPAY_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'wanweipay_withdrawal';
    }

    protected function configParams(&$params, $direct_pay_extra_info){}
    protected function processPaymentUrlForm($params){}

    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
        $result = array('success' => false, 'message' => 'payment failed');

        if(!$this->isAllowWithdraw()) {
            $result['message'] = lang("Withdraw not allowed with this API");
            $this->utils->debug_log($result);
            return $result;
        }

        $bankInfo = $this->getBankInfo();
        if(!array_key_exists($bank, $bankInfo)) {
            $this->utils->error_log("========================wanweipay withdrawal bank whose bankTypeId=[$bank] is not supported by wanweipay");
            return array('success' => false, 'message' => 'Bank not supported by wanweipay');
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);

        $url = $this->getSystemInfo('url');

        list($content, $response_result) = $this->submitPostForm($url, $params, false, $transId, true);

        $decodedResult = $this->decodeResult($content);
        $this->CI->utils->debug_log('=========================wanweipay submitWithdrawRequest decoded Result', $decodedResult);
        $decodedResult['response_result'] = $response_result;

        return $decodedResult;
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));
        # look up bank code
        $bankInfo = $this->getBankInfo();

        $params = array();
        $params['accountName']  = $name;
        $params['accountNo']   = $accNum;
        $params['amount']  = $this->convertAmountToCurrency($amount);
        $params['mchId']  = $this->getSystemInfo("account");
        $params['mchOrderNo'] = $transId;
        $params['notifyUrl'] = $this->getNotifyUrl($transId);
        $params['payoutBankCode'] = $bankInfo[$bank]['code'];
        $params['reqTime'] = time();
        $params['sign'] = $this->sign($params);
        $this->utils->debug_log("=========================wanweipaywithdraw params ", $params);
        return $params;
    }

    public function decodeResult($resultString, $queryAPI = false) {
        $result = json_decode($resultString, true);
        $this->utils->debug_log("=========================wanweipay json_decode result", $result);

        $respCode = $result['retCode'];

        if (isset($result['errCode'])){
            $errCode = $result['errCode'];
        }else{
            $errCode = '';
        }

        if($respCode == self::RESPONSE_CODE_SUCCESS ) {
            $message = "wanweipay request successful.";
            return array('success' => true, 'message' => $message);
        }
        else {
            if($errCode == '' || $errCode == false) {
                $this->utils->error_log("========================wanweipay return UNKNOWN ERROR!");
                $errCode = "未知错误";
            }

            $message = "wanweipay withdrawal response, Code: [ ".$errCode." ]";
            return array('success' => false, 'message' => $message);
        }
    }

    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);
        $result = array('success' => false, 'message' => 'Payment failed');

        if(empty($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }

        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);
        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        $statusCode = $params['status'];
        if($statusCode == self::CALLBACK_STATUS_SUCCESS) {
            $msg = "wanweipay withdrawal success!";
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;
        }
        else if($statusCode == self::CALLBACK_STATUS_FAILED){
            $msg = "wanweipay withdrawal failed.";
            $result['message'] = self::RETURN_SUCCESS_CODE;
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
        }
        else {
            $msg = "wanweipay withdrawal response order_state: [".$params['status']."]";
            $this->debug_log($msg, $params);
            $result['message'] = $msg;
        }

        return $result;
    }

    private function checkCallbackOrder($order, $fields) {
        $requiredFields = array('mchOrderNo', 'amount', 'sign');
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("======================wanweipay withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }

        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=========================wanweipay withdrawal checkCallback signature Error', $fields);
            return false;
        }

        if ($fields['amount'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================wanweipay withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['mchOrderNo'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================wanweipay withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields);
            return false;
        }

        # everything checked ok
        return true;
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
            $this->utils->debug_log("=========================wanweipay bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('label' => '中国工商银行', 'code' => 'ICBC'),
                '2' => array('label' => '招商银行', 'code' => 'CMB'),
                '3' => array('label' => '中国建设银行', 'code' => 'CCB'),
                '5' => array('label' => '交通银行', 'code' => 'BCOM'),
                '6' => array('label' => '中国银行', 'code' => 'BOC'),
                '10' => array('label' => '中信银行', 'code' => 'CITIC'),
                '11' => array('label' => '中国民生银行', 'code' => 'CMBC'),
                '12' => array('label' => '中国邮政储蓄银行', 'code' => 'PSBC'),
                '13' => array('label' => '中国兴业银行', 'code' => 'CIB'),
                '15' => array('label' => '平安银行', 'code' => 'PAB'),
                '20' => array('label' => '中国光大银行', 'code' => 'CEB'),
            );
            $this->utils->debug_log("=========================wanweipay bank info from code: ", $bankInfo);

        }
        return $bankInfo;
    }

}