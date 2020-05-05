<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingsheng.php';

/**
 * YINGSHENG 盈盛
 *
 * * YINGSHENG_WITHDRAWAL_PAYMENT_API, ID: 5567
 *
 * Required Fields:
 * * URL
 * * Account
 * * priv key
 * * pub key
 *
 * Field Values:
 * * URL: https://api.wellpays.com/rsa/withdraw
 * * Account: ## MerId ##
 * * "yingsheng_priv_key": "## Private Key ##",
 * * "yingsheng_pub_key": "## Public Key ##"
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_yingsheng_withdrawal extends Abstract_payment_api_yingsheng {

    const CALLBACK_STATUS_SUCCESS = 1;


    public function getPlatformCode() {
        return YINGSHENG_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'yingsheng_withdrawal';
    }
    # Implement abstract function but do nothing
    protected function configParams(&$params, $direct_pay_extra_info)
    {
    }
    protected function processPaymentUrlForm($params, $secure_id)
    {
    }


    # Implement abstract function but do nothing
    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {}
    public function directPay($order = null) {}

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'yingsheng_pub_key', 'yingsheng_priv_key');
        return $secretsInfo;
    }

    public function getWithdrawUrl() {
        return $this->getSystemInfo('url');
    }

    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        # look up bank code

        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));
        $playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
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

        $bankInfo = $this->getBankInfo();
        $params = array();
        $params['bank_code']         = $bankInfo["$bank"]["code"];
        $params['amount']            = $this->convertAmountToCurrency($amount);
        $params['merchant_user']     = $name;
        $params['merchant_order_no'] = $transId;
        $params['platform']          = 'PC';
        $params['card_num']          = $accNum;
        $params['card_name']         = $name;
        $params['bank_branch']       = $bankBranch;
        $params['bank_province']     = $province;
        $params['bank_city']         = $city;
        $params['callback_url']      = $this->getNotifyUrl($transId);
        $sign = $this->sign($params);
        $submit['merchant_code']     = $this->getSystemInfo('account');
        $submit['data']              = $sign['encParam'];
        $submit['sign']              = $sign['sign'];
        $this->CI->utils->debug_log('=====================yingsheng generatePaymentUrlForm params', $params);
        $this->CI->utils->debug_log('=====================yingsheng generatePaymentUrlForm submit', $submit);

        return $submit;
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
            $this->utils->error_log("========================yingsheng withdrawal bank whose bankTypeId=[$bank] is not supported by yingsheng");
            return array('success' => false, 'message' => 'Bank not supported by yingsheng');
        }

		$params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        $this->CI->utils->debug_log('=========================yingsheng submitWithdrawRequest params', $params);

        $url = $this->getSystemInfo('url');

        list($content, $response_result) = $this->submitPostForm($url, $params, false, $transId, true);

        $decodedResult =  $this->decodeResult($content);
        $this->CI->utils->debug_log('=========================yingsheng submitWithdrawRequest decoded Result', $decodedResult);
        $decodedResult['response_result'] = $response_result;

        return $decodedResult;

    }


    public function decodeResult($resultString, $queryAPI = false) {
		$result = json_decode($resultString, true);
		$this->utils->debug_log("=========================yingsheng json_decode result", $result);

        $respCode = $result['error_code'];
		$resultMsg = self::ERROR_MSG["$respCode"];
		$this->utils->debug_log("=========================yingsheng withdrawal resultMsg", $resultMsg);

		if($queryAPI){
			if($result['status'] == self::RESULT_CODE_SUCCESS) {
                $message = 'yingsheng payment response successful!';
                return array('success' => true, 'message' => $message);
			}else{
				$message = "yingsheng payment result_code is [ ".$respCode. " ] , Query failed msg: ".$resultMsg;
				return array('success' => false, 'message' => $message);
			}
		}
		else{
			if($respCode == self::RESULT_CODE_SUCCESS) {
	            $message = "yingsheng request successful.";
	            return array('success' => true, 'message' => $message);
            }
            else {
				if($resultMsg == '' || $resultMsg == false) {
						$this->utils->error_log("========================yingsheng return UNKNOWN ERROR!");
						$resultMsg = "未知错误";
				}

				$message = "yingsheng withdrawal response, Code: [ ".$respCode." ] , Msg: ".$resultMsg;
				return array('success' => false, 'message' => $message);
			}
		}


	}


    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);

        if(empty($params) || is_null($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }
        $result = array('success' => false, 'message' => 'Payment failed');

        $this->utils->debug_log("==========================yingsheng checkCallback params", $params);

        $params = json_decode($this->decrypt($params['data']), true);
        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        if($params['status'] == self::CALLBACK_STATUS_SUCCESS) {
            $this->utils->debug_log('=========================yingsheng withdrawal payment was successful: trade ID [%s]', $params['merchant_order_no']);

            $msg = sprintf('yingsheng withdrawal was successful: trade ID [%s]',$params['merchant_order_no']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;

        }else{
            $this->utils->debug_log('==========================yingsheng withdrawal payment was failed: trade ID [%s]',$params['merchant_order_no']);

            $msg = sprintf('yingsheng withdrawal was failed: trade ID [%s]',$params['merchant_order_no']);
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);

            $result['message'] = self::RETURN_SUCCESS_CODE;
            $result['success'] = true;

        }

        return $result;
    }

    public function checkCallbackOrder($order, $fields) {
        $this->utils->debug_log('==========================yingsheng withdrawal checkCallbackOrder fields', $fields);

         # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================yingsheng checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $requiredFields = array('amount', 'merchant_user', 'merchant_order_no', 'trans_id');

        $decrypted = json_decode($this->decrypt($fields['data']), true);
        $this->CI->utils->debug_log("======================yingsheng checkCallbackOrder decrypted", $decrypted);

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $decrypted)) {
                $this->writePaymentErrorLog("=======================yingsheng withdrawal checkCallbackOrder missing parameter: [$f]", $decrypted);
                return false;
            }
        }


		if ($decrypted['amount'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================yingsheng withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $decrypted);
			return false;
		}

        if ($decrypted['merchant_order_no'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================yingsheng checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $decrypted);
            return false;
        }

        # everything checked ok
        return true;
    }

    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 2, '.', '');
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
            $this->utils->debug_log("=========================yingsheng bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '工商银行', 'code' => '0102'),
                '2' => array('name' => '招商银行', 'code' => '0101'),
                '3' => array('name' => '建设银行', 'code' => '0103'),
                '4' => array('name' => '农业银行', 'code' => '0105'),
                '5' => array('name' => '交通银行', 'code' => '0129'),
                '6' => array('name' => '中国银行', 'code' => '0110'),
                // '7' => array('name' => '深圳发展银行', 'code' => 'SDB'),
                // '8' => array('name' => '广东发展银行', 'code' => 'GDB'),
                '10' => array('name' => '中信银行', 'code' => '0118'),
                '11' => array('name' => '民生银行', 'code' => '0106'),
                '12' => array('name' => '邮储银行', 'code' => '0117'),
                '13' => array('name' => '兴业银行', 'code' => '0107'),
                '14' => array('name' => '华夏银行', 'code' => '0123'),
                '15' => array('name' => '平安银行', 'code' => '0114'),
                // '18' => array('name' => '南京银行', 'code' => 'BON'),
                '20' => array('name' => '光大银行', 'code' => '0109'),
                '24' => array('name' => '上海浦东发展银行', 'code' => '0104'),
                // '26' => array('name' => '广东发展银行', 'code' => '0131'),
                // '29' => array('name' => '北京银行', 'code' => '0111'),
                // '31' => array('name' => '上海银行', 'code' => '0128'),
                // '33' => array('name' => '北京农商', 'code' => 'BJRCB'),
            );
            $this->utils->debug_log("=========================yingsheng bank info from code: ", $bankInfo);

        }
        return $bankInfo;
    }




}