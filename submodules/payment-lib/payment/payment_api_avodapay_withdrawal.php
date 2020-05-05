<?php
require_once dirname(__FILE__) . '/abstract_payment_api_avodapay.php';

/**
 * AVODAPAY 
 *
 * * AVODAPAY_WITHDRAWAL_PAYMENT_API, ID: 5717
 *
 * Required Fields:
 * * URL
 * * Account
 * * priv key
 * * pub key
 *
 * Field Values:
 * * URL: https://api.wellpays.com/rsa/withdraw
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_avodapay_withdrawal extends Abstract_payment_api_avodapay {


    const CALLBACK_RESULT_CODE_SUCCESS = 'success';


    public function getPlatformCode() {
        return AVODAPAY_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'avodapay_withdrawal';
    }
    # Implement abstract function but do nothing
    protected function configParams(&$params, $direct_pay_extra_info)
    {
    }
    protected function processPaymentUrlForm($params)
    {
    }


    # Implement abstract function but do nothing
    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {}
    public function directPay($order = null) {}


    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        # look up bank code

        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));

        $params = array();
        $params['paymentId']    = $transId;
		$params['merchantId']   = $this->getSystemInfo("account");
		$params['amount']       = $this->convertAmountToCurrency($amount);
		$params['notifyUrl']    = $this->getNotifyUrl($transId);
		$params['chineseCardNo'] = $accNum;
        $params['chineseName']   = $name;
        $params['merchantSiteCode']   = $this->getSystemInfo('key');
        $this->CI->utils->debug_log('=====================avodapay generatePaymentUrlForm params', $params);
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
            $this->utils->error_log("========================avodapay withdrawal bank whose bankTypeId=[$bank] is not supported by avodapay");
            return array('success' => false, 'message' => 'Bank not supported by avodapay');
        }

		$params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        $this->CI->utils->debug_log('=========================avodapay submitWithdrawRequest params', $params);

        $url = $this->getSystemInfo('url');

        list($content, $response_result) = $this->submitPostForm($url, $params, false, $transId, true);

        $decodedResult =  $this->decodeResult($content);
        $this->CI->utils->debug_log('=========================avodapay submitWithdrawRequest decoded Result', $decodedResult);
        $decodedResult['response_result'] = $response_result;

        return $decodedResult;

    }


    public function decodeResult($resultString) {
		$this->utils->debug_log("=========================avodapay decodeResult resultString", $resultString);

        if(is_array($resultString) && isset($resultString['lock']) && isset($resultString['msg']) ) {
            $result = array('success' => false, 'message' => $resultString['msg'], 'lock' => true ,);
            return $result;
        }

        $response = json_decode($resultString, true);
        $this->CI->utils->debug_log('=======================avodapay submitWithdrawRequest decodeResult json decoded', $response);

        if($response['status'] == self::CALLBACK_RESULT_CODE_SUCCESS) {
            $message = "avodapay withdrawal response success! Transaction fee: " . $response['msg'];
            return array('success' => true, 'message' => $message);
        } else{
            if(!isset($response['msg'])) {
                $this->utils->error_log("========================ss return UNKNOWN ERROR!");
                $resultMsg = "未知错误";
            }
			$resultMsg = $response['msg'];
            $message = 'avodapay withdrawal failed. Error Message: ' . $resultMsg;
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

        if($params['status'] == self::CALLBACK_STATUS_SUCCESS) {
            $msg = sprintf('avodapay withdrawal was successful: trade ID [%s]',$params['paymentId']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;

        } else {
            $this->utils->debug_log('==========================avodapay withdrawal payment was failed: trade ID [%s]',$params['paymentId']);

            $msg = sprintf('avodapay withdrawal was failed: trade ID [%s]',$params['paymentId']);
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;
        }

        return $result;
    }

    public function checkCallbackOrder($order, $fields) {
        $requiredFields = array('paymentId', 'amount','status', 'hmac');

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=======================avodapay withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }

        if ($fields['hmac']!=$this->validateSign($fields)) {
            $this->writePaymentErrorLog('==========================avodapay withdrawal checkCallback signature Error',$fields);
            return false;
        }

        if ($fields['amount'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================avodapay withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
            return false;
        }

        if ($fields['paymentId'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================avodapay withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    protected function validateSign($params) {
		$signStr = $this->createSignStr($params);
		$token = $this->getSystemInfo('key');
		$sign = hash_hmac('md5', $signStr, $token);

		if ($sign == $params['hmac']) {
			return true;
		}else {
			return false;
		}
		return ($signStr == $params);
	}

	private function createSignStr($params) {
		$keys = array('paymentId', 'amount', 'status');
        $signStr = "";
        foreach($keys as $key) {
            if (array_key_exists($key, $params)) {
                $signStr .= "$key=$params[$key]&";
            }
		}
		$signStr .= 'merchantSiteCode='.$this->getSystemInfo('key');

		return $signStr;
	}


    private function getNotifyUrl($orderId) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
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
            $this->utils->debug_log("=========================avodapay bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '工商银行', 'code' => 'ICBC'),
                // '2' => array('name' => '招商银行', 'code' => '0101'),
                // '3' => array('name' => '建设银行', 'code' => '0103'),
                // '4' => array('name' => '农业银行', 'code' => '0105'),
                // '5' => array('name' => '交通银行', 'code' => '0129'),
                '6' => array('name' => '中国银行', 'code' => 'BOC'),
                // '7' => array('name' => '深圳发展银行', 'code' => 'SDB'),
                '8' => array('name' => '广东发展银行', 'code' => 'GDB'),
                // '10' => array('name' => '中信银行', 'code' => '0118'),
                '11' => array('name' => '民生银行', 'code' => 'CMBC'),
                // '12' => array('name' => '邮储银行', 'code' => 'PSBC'),
                // '13' => array('name' => '兴业银行', 'code' => '0107'),
                // '14' => array('name' => '华夏银行', 'code' => '0123'),
                // '15' => array('name' => '平安银行', 'code' => '0114'),
                // '18' => array('name' => '南京银行', 'code' => 'BON'),
                // '20' => array('name' => '光大银行', 'code' => '0109'),
                // '24' => array('name' => '上海浦东发展银行', 'code' => '0104'),
                // // '26' => array('name' => '广东发展银行', 'code' => '0131'),
                // '29' => array('name' => '北京银行', 'code' => '0111'),
                // '31' => array('name' => '上海银行', 'code' => '0128'),
                // '33' => array('name' => '北京农商', 'code' => 'BJRCB'),
            );
            $this->utils->debug_log("=========================avodapay bank info from code: ", $bankInfo);

        }
        return $bankInfo;
    }




}