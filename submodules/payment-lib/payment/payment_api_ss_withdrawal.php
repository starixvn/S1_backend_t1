<?php
require_once dirname(__FILE__) . '/abstract_payment_api_ss.php';

/**
 *
 * SS
 *
 * SS_WITHDRAWAL_PAYMENT_API, ID: 5712
 *
 * Required Fields:
 *
 * * URL
 * * Account
 * * Extra Info
 *
 * Field Values:
 *
 * * URL http://43.242.33.147:81/SS/api/apply/Withdraw
 * * Account - Merchant ID
 * * Key - Secret key
 * * Extra Info
 *
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_ss_withdrawal extends Abstract_payment_api_ss  {
    const WITHDRAWAL_RESULT_CODE_SUCCESS = '1';
    const WITHDRAWAL_RESULT_CODE_FAILED = '2';


	public function getPlatformCode() {
		return SS_WITHDRAWAL_PAYMENT_API;
	}

	public function getPrefix() {
		return 'ss_withdrawal';
	}

	public function generatePaymentUrlFormgeneratePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = NULL, $enabledSecondUrl = true, $bankId = NULL) {}
	protected function configParams(&$params, $direct_pay_extra_info) {}
    protected function processPaymentUrlForm($params) {}

    /**
     * detail: Initiates a withdraw request
     *
     * note: Note that the $bank is the bank_type ID, we should map it to the bank_code and bank_name required by this API
     *
     * @param int $bank
     * @param string $accNum
     * @param string $name
     * @param float $amount
     * @param int $transId
     * @return json
     */
    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        $bankInfo = $this->getBankInfo();

        $params = array();
        $params['AccountName'] = $name;
        $params['AccountNumber'] = $accNum;
        $params['Amount'] = $this->convertAmountToCurrency($amount);
        $params['AppliedBankID'] = $bankInfo[$bank]['code'];
        $params['ClientNotifyUrl'] = $this->getNotifyUrl($transId);
        $params['CompanyID'] = $this->getSystemInfo("account");
        $params['IpAddress'] = $this->getClientIp();
        $params['Memo'] = 'Memo';
        $params['MerchantOrderNumber'] = $transId;
        $params['Sign'] = $this->signWithdrawal($params);

        $this->CI->utils->debug_log('========================ss getWithdrawParams params: ', $params);

        return $params;
    }

    public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
        $result = array('success' => false, 'message' => 'payment failed');

        if(!$this->isAllowWithdraw()) {
            $result['message'] = lang("Withdraw not allowed with this API");
            return $result;
        }

        if(!array_key_exists($bank, $this->getBankInfo())) {
            $this->utils->error_log("========================ss withdraw bank whose bankTypeId=[$bank] is not supported by ss");
            $result['message'] = lang("Bank not supported by ss");
            return $result;
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);

        list($response, $response_result) = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $transId, true);

        $decodedResult = $this->decodeResult($response);
        $decodedResult['response_result'] = $response_result;
        $this->CI->utils->debug_log('=======================ss submitWithdrawRequest decoded Result', $decodedResult);

        return $decodedResult;
    }

    public function decodeResult($resultString) {
        $this->utils->debug_log("=========================ss decodeResult resultString", $resultString);

        if(is_array($resultString) && isset($resultString['lock']) && isset($resultString['msg'])) {
            $result = array('success' => false, 'message' => $resultString['msg'], 'lock' => true);
            return $result;
        }

        $response = json_decode($resultString, true);
        $this->CI->utils->debug_log('=======================ss submitWithdrawRequest decodeResult json decoded', $response);

        if($response['Status'] == self::WITHDRAWAL_RESULT_CODE_SUCCESS) {
            $message = "ss withdrawal response success! Transaction fee: " . $response['TransactionCharge'];
            return array('success' => true, 'message' => $message);
        } else{
			if(!isset($response['ErrorMessage'])) {
                $this->utils->error_log("========================ss return UNKNOWN ERROR!");
                $resultMsg = "未知错误";
            }

            $resultMsg = $response['ErrorMessage'];
            $message = 'ss withdrawal failed. Error Message: ' . $resultMsg;
            return array('success' => false, 'message' => $message);
        }
    }

    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);
        if(empty($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }
        $result = array('success' => false, 'message' => 'Payment failed');


        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);
        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        if($params['Status'] == self::WITHDRAWAL_RESULT_CODE_SUCCESS) {
            $msg = sprintf('ss withdrawal was successful: trade ID [%s]',$params['MerchantOrderNumber']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);
            $returnSuccess = [
                'OrderNumber' => $params['OrderNumber'],
                'MerchantOrderNumber' => $params['MerchantOrderNumber'],
                'Status' => self::RETURN_SUCCESS_CODE
            ];
            $result['message'] = json_encode($returnSuccess);
            $result['success'] = true;
        } elseif($params['Status'] == self::WITHDRAWAL_RESULT_CODE_FAILED) {
            $msg = sprintf('ss withdrawal was failed: trade ID ['.$params['MerchantOrderNumber'].']');
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
            $returnError = [
                'OrderNumber' => $params['OrderNumber'],
                'MerchantOrderNumber' => $params['MerchantOrderNumber'],
                'Status' => self::RETURN_ERROR_CODE,
                'ErrorMessage' => 'Error'
            ];
            $result['message'] = json_encode($returnError);
            $result['success'] = true;
        } else {
            $msg = sprintf('ss withdrawal was not success: [%s]', $params['Status']);
            $this->debug_log($msg, $params);
            $result['message'] = $msg;
        }

        return $result;
    }


    public function checkCallbackOrder($order, $fields) {
        $requiredFields = array('MerchantOrderNumber', 'Amount','sign');

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=======================ss withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }

        if ($fields['sign']!=$this->validateSign($fields)) {
            $this->writePaymentErrorLog('==========================ss withdrawal checkCallback signature Error',$fields);
            return false;
        }

        if ($fields['Amount'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================ss withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['MerchantOrderNumber'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================ss withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields);
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
            $this->utils->debug_log("=========================ss bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '中国工商银行', 'code' => 1),
                '2' => array('name' => '招商银行', 'code' => 2),
                '3' => array('name' => '中国建设银行', 'code' => 3),
                '4' => array('name' => '中国农业银行', 'code' => 4),
                '5' => array('name' => '中国银行', 'code' => 5),
                '6' => array('name' => '交通银行', 'code' => 6),
                '8' => array('name' => '中信银行', 'code' => 8),
                '9' => array('name' => '上海浦东发展银行', 'code' => 9),
                '10' => array('name' => '邮政储汇', 'code' => 10),
                '11' => array('name' => '中国光大银行', 'code' => 11),
                '12' => array('name' => '平安银行', 'code' => 12),
                '13' => array('name' => '广发银行股份有限公司', 'code' => 13),
                '14' => array('name' => '华夏银行', 'code' => 14),
                '15' =>array('name' => '福建兴业银行', 'code' => 15)
            );
            $this->utils->debug_log("=======================getting ss bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }

    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    private function signWithdrawal($params) {
        $signStr = md5($this->getSystemInfo('key'));
        ksort($params);

        foreach($params as $key => $value) {
            if($key == 'sign') {
                continue;
            }else if($key == 'MerchantOrderNumber'){
                $signStr .= "$key=$value";
            }else{
                $signStr .= "$key=$value&";
            }
        }

        $sign = md5($signStr);
        return $sign;
    }

    private function validateSign($params) {
        $signStr = md5($this->getSystemInfo('key'));
        $signStr .= $params['OrderNumber'].$params['MerchantOrderNumber'].$params['Status'].$params['Notes'].
            $params['Amount'].$params['TransactionCharge'].$params['OperationTime'];
        $sign = md5($signStr);

        if($params['sign'] == $sign)
            return true;
        else
            return false;
    }


}
