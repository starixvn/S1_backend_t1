<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingxinpay.php';

/**
 * 盈信 YINGXINPAY
 *
 * * YINGXINPAY_WITHDRAWAL_PAYMENT_API, ID: 5589
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.yingxinpay.com/PaymentGetway/SinglePay
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */

class Payment_api_yingxinpay_withdrawal extends Abstract_payment_api_yingxinpay {
    const CALLBACK_MSG_SUCCESS = 'OK';   
    const CALLBACK_SUCCESS = 3;
    const CALLBACK_FAILED = 4;
    const ORDERTYPE_WITHDRAW = 1;
    const ORDERTYPE_REVERSE = 2;
    const ORDERTYPE_REVERSE_MSG = 'This order is a reverse order';
 
    public function getPlatformCode() {
        return YINGXINPAY_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'yingxinpay_withdrawal';
    }

    public function __construct($params = null) {
        parent::__construct($params);
        $this->_custom_curl_header = array('application/x-www-form-urlencoded');
    }
    
    # Implement abstract function but do nothing
    protected function configParams(&$params, $direct_pay_extra_info){}
    protected function processPaymentUrlForm($params){}

    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        # look up bank code
        $bankInfo = $this->getBankInfo();
        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));
        $playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
        $this->utils->debug_log("Get playerBankDetails using [$bank] + [$accNum]", $playerBankDetails);
        if(!empty($playerBankDetails)){                        
            $bankBranch  = empty($playerBankDetails['branch'])      ? "无" : $playerBankDetails['branch'];            
        } else {
            $bankBranch  = '无';            
        }
        
        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        $params = array();
        $params['merchantId'] = $this->getSystemInfo("account");
        $params['merchantOrderId'] = $transId;
        $params['orderAmount'] = $this->convertAmountToCurrency($amount);
        $params['payType'] = '1';
        $params['accountHolderName'] = $name;  //收款人
        $params['accountNumber'] = $accNum;
        $params['bankType'] = $bankInfo[$bank]['code'];
        $params['notifyUrl'] = $this->getNotifyUrl($transId);
        $params['reverseUrl'] = $this->getNotifyUrl($transId);                 
        $params['submitIp'] = $this->utils->getIP();
        $params['subBranch'] = $bankBranch;                            
        $params['sign'] = $this->sign($params);
        
        $this->CI->utils->debug_log('=========================yingxinpay getWithdrawParams params', $params);
        return $params;
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
            $this->utils->error_log("========================yingxinpay withdrawal bank whose bankTypeId=[$bank] is not supported by yingxinpay");
            return array('success' => false, 'message' => 'Bank not supported by yingxinpay');
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        
        $url = $this->getSystemInfo('url'); 
        
        list($content, $response_result) = $this->submitPostForm($url, $params, false, $transId, true);

        $decodedResult = $this->decodeResult($content);
        $this->CI->utils->debug_log('=========================yingxinpay submitWithdrawRequest decoded Result', $decodedResult);
        $decodedResult['response_result'] = $response_result;

        return $decodedResult;
    }

    public function decodeResult($resultString, $queryAPI = false) {
        $result = json_decode($resultString, true);
        $this->utils->debug_log("=========================yingxinpay json_decode result", $result);

        $respCode = $result['ErrorCode'];
        $resultMsg = $result['ErrorMessage'];
        $this->utils->debug_log("=========================yingxinpay withdrawal resultMsg", $resultMsg);

        if($respCode == null && $resultMsg == null) {
            $message = "yingxinpay request successful.";
            return array('success' => true, 'message' => $message);
        } 
        else {
            if($resultMsg == '' || $resultMsg == false) {
                $this->utils->error_log("========================yingxinpay return UNKNOWN ERROR!");
                $resultMsg = "未知错误";
            }

            $message = "yingxinpay withdrawal response, Code: [ ".$respCode." ] , Msg: ".$resultMsg;
            return array('success' => false, 'message' => $message);
        }   
    }

    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);
        if(empty($params) || is_null($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }
        $result = array('success' => false, 'message' => 'Payment failed');

        $this->utils->debug_log("==========================yingxinpay checkCallback params", $params);

        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        if(($params['status'] == self::CALLBACK_SUCCESS) && ($params['orderType'] == self::ORDERTYPE_WITHDRAW))  {
            
            $this->utils->debug_log('=====================yingxinpay withdrawal payment was successful: trade ID [%s]', $params['merchantOrderId']); 
            $msg = sprintf('yingxinpay withdrawal was successful: trade ID [%s]',$params['merchantOrderId']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);
            $result['message'] = self::CALLBACK_MSG_SUCCESS;
            $result['success'] = true;

       }else if($params['orderType'] == self::ORDERTYPE_REVERSE){

            $this->utils->debug_log('=========================yingxinpay withdrawal payment was failed: trade ID [%s]', $params['merchantOrderId']);
            $this->paymentExceptionOrder($order,$params,$response_result_id); 
            $msg = sprintf('yingxinpay withdrawal payment was not successful: trade ID [%s]',$params['merchantOrderId']);                        
            $result['message'] = self::CALLBACK_MSG_SUCCESS;
            $result['success'] = true; 

        }else if($params['status'] == self::CALLBACK_FAILED){
            $msg = sprintf('yingxinpay withdrawal was failed: trade ID [%s]',$params['merchantOrderId']);
            $this->writePaymentErrorLog($msg, $fields);
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
            $result['message'] = $msg;
        }
        else {
            $msg = sprintf('yingxinpay withdrawal payment was not successful  trade ID [%s] ',$params['merchantOrderId']);
            $this->debug_log($msg, $params);
            $result['message'] = $msg;
        }

        return $result;
    }

    public function checkCallbackOrder($order, $fields) {
        $requiredFields = array('merchantOrderId','orderAmount','sign');
                                
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=======================yingxinpay withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }
        
        if ($fields['sign'] != $this->validateSign($fields)) {
            $this->writePaymentErrorLog('==========================yingxinpay withdrawal checkCallback signature Error',$fields);
            return false;
        }

        if ($fields['orderAmount'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================yingxinpay withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['merchantOrderId'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================yingxinpay withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields); 
            return false;
        }
        # everything checked ok
        return true;
    }


    # -- signatures --
    public function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);        
        return $sign;
    }


    private function validateSign($params){
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);
        
        if($params['sign'] == $sign){
            return true;
        }
        else{            
            return false;
        }

    }

    private function createSignStr($params) {        
        $signStr = '';        
        foreach($params as $key => $value) {
            if(($key == 'sign') || ($key == 'subBranch')) {
                continue;
            }
            if($key == 'submitIp'){
               $signStr .="$key=$value";
            }
            else{
               $signStr .= "$key=$value&"; 
            }                            
        }        
        return $signStr.$this->getSystemInfo('key');                
    }

    # -- bankinfo --
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
            $this->utils->debug_log("==================getting yingxinpay bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '工商银行', 'code' => '20'),
                '2' => array('name' => '招商银行', 'code' => '17'),
                '3' => array('name' => '建设银行', 'code' => '11'),
                '4' => array('name' => '农业银行', 'code' => '3'),
                '5' => array('name' => '交通银行', 'code' => '16'),
                '6' => array('name' => '中国银行', 'code' => '10'),
                '8' => array('name' => '广发银行', 'code' => '4'),                              
                '10' => array('name' => '中信银行', 'code' => '19'),
                '11' => array('name' => '民生银行', 'code' => '12'),                                
                '13' => array('name' => '兴业银行', 'code' => '22'),
                '14' => array('name' => '华夏银行', 'code' => '13'),
                '15' => array('name' => '平安银行', 'code' => '14'),
                '20' => array('name' => '光大银行', 'code' => '8'),                                                                
            );

            $this->utils->debug_log("=======================getting yingxinpay bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }

    protected function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    private function paymentExceptionOrder($order,$params,$response_result_id) {
        $this->CI->load->model(['sale_order']);        
        $external_system_id=$this->getPlatformCode();
        $amount=$params['orderAmount'];
        $external_order_id=$params['systemOrderId'];
        $external_order_datetime='';
        $player_bank_name='';
        $player_bank_account_name='';
        $player_bank_account_number='';
        $player_bank_address='';
        $collection_bank_name=''; 
        $collection_bank_account_name=''; 
        $collection_bank_account_number=''; 
        $collection_bank_address=''; 
        $saleOrderId=null;
        $withdrawal_order_id=$order['walletAccountId'];
        $remarks= self::ORDERTYPE_REVERSE_MSG;          
        //write to exception order
        $exception_order_id=$this->CI->sale_order->createExceptionDeposit($external_system_id, $amount, $external_order_id, 
            $external_order_datetime, $response_result_id,
            $player_bank_name, $player_bank_account_name, $player_bank_account_number, $player_bank_address,
            $collection_bank_name, $collection_bank_account_name, $collection_bank_account_number, $collection_bank_address,
            $params, $saleOrderId , $withdrawal_order_id, $remarks);
        $message = self::ORDERTYPE_REVERSE_MSG;
    }
}