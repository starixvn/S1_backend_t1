<?php
require_once dirname(__FILE__) . '/abstract_payment_api_hfpay.php';

/**
 * HAOFU 豪富
 *
 * * HAOFU_WITHDRAWAL_PAYMENT_API, ID: 5455
 *
 * Required Fields:
 * * URL:https://mmszbjachb.6785151.com/payCenter/agentPay
 * * Account
 * * Key
 * * Secret
 *
 * Field Values:
 * * URL: 
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_haofu_withdrawal extends Abstract_payment_api_hfpay {
    const CALLBACK_MSG_SUCCESS = 'success';
    const RETURN_SUCCESS = 'T';
    const CALLBACK_SUCCESS = 1;
    const CALLBACK_FAILED = 2;

    public function getPlatformCode() {
        return HAOFU_WITHDRAWAL_PAYMENT_API;
    }

    public function getPrefix() {
        return 'haofu_withdrawal';
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
        $bankno = $bankInfo[$bank]['code'];

        $this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));
        $playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
        $this->utils->debug_log("Get playerBankDetails using [$bank] + [$accNum]", $playerBankDetails);
        if(!empty($playerBankDetails)){
            $province    = empty($playerBankDetails['province'])    ? "无" : $playerBankDetails['province'];
            $city        = empty($playerBankDetails['city'])        ? "无" : $playerBankDetails['city'];
            $bankBranch  = empty($playerBankDetails['branch'])      ? "无" : $playerBankDetails['branch'];
            $bankAddress = empty($playerBankDetails['bankAddress']) ? "无" : $playerBankDetails['bankAddress'];
        } else {
            $bankBranch  = '无';
            $province    = '无';
            $city        = '无';
            $bankAddress = '无';
        }
        
		$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        $params = array();
		$params['partner'] = $this->getSystemInfo("account");
        $params['notify_url'] = $this->getNotifyUrl($transId);
		$params['request_time'] = date('Y-m-d H:i:s');
        $params['trade_no'] = $transId;
        $params['amount'] = $this->convertAmountToCurrency($amount); //元
        $params['bank_sn'] = $bankInfo[$bank]['code'];  //銀行編號
        $params['bank_site_name'] = $bankAddress;  //银行所在地
        $params['bank_account_name'] = $name;  //收款人
        $params['bank_province'] = $province;
		$params['bank_city'] = $city;
		$params['bank_account_no'] = $accNum;
		$params['bank_mobile_no'] = $this->randomNum(13);
        $params['sign'] = $this->sign($params);
        
        $this->CI->utils->debug_log('=========================haofu getWithdrawParams params', $params);
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
            $this->utils->error_log("========================haofu withdrawal bank whose bankTypeId=[$bank] is not supported by haofu");
            return array('success' => false, 'message' => 'Bank not supported by haofu');
        }

		$params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);
        
        $url = $this->getSystemInfo('url');	
        
        list($content, $response_result) = $this->submitPostForm($url, $params, false, $transId, true);

        $decodedResult = $this->decodeResult($content);
        $this->CI->utils->debug_log('=========================haofu submitWithdrawRequest decoded Result', $decodedResult);
        $decodedResult['response_result'] = $response_result;

        return $decodedResult;

	}

    public function decodeResult($resultString, $queryAPI = false) {
		$result = json_decode($resultString, true);
		$this->utils->debug_log("=========================haofu json_decode result", $result);

        $respCode = $result['is_success'];
		$resultMsg = $result['fail_msg'];
		$this->utils->debug_log("=========================haofu withdrawal resultMsg", $resultMsg);
		
		if($queryAPI){
			if($respCode == self::RETURN_SUCCESS) {
                $message = 'haofu payment response successful!';
                return array('success' => true, 'message' => $message);
			}else{
				$message = "haofu payment result_code is [ ".$result['fail_code']. " ] , Query failed msg: ".$resultMsg;
				return array('success' => false, 'message' => $message);
			}
		}
		else{ 
			if($respCode == self::RETURN_SUCCESS) {
	            $message = "haofu request successful.";
	            return array('success' => true, 'message' => $message);
            } 
            else {
				if($resultMsg == '' || $resultMsg == false) {
						$this->utils->error_log("========================haofu return UNKNOWN ERROR!");
						$resultMsg = "未知错误";
				}

				$message = "haofu withdrawal response, Code: [ ".$result['fail_code']." ] , Msg: ".$resultMsg;
				return array('success' => false, 'message' => $message);
			}	
		}

	}

	public function checkWithdrawStatus($transId) {
        $params = array();
		$params['partner'] = $this->getSystemInfo("account");
		$params['request_time'] = date('Y-m-d H:i:s');
		$params['sign'] = $this->sign($params);

		$url = $this->getSystemInfo('check_withdraw_status_url', 'https://mmszbjachb.6785151.com/payCenter/orderQuery');
		$response = $this->submitPostForm($url, $params, false, $transId);
		$decodedResult = $this->decodeResult($response, true);

		$this->CI->utils->debug_log('======================================haofu checkWithdrawStatus params: ', $params);
		$this->CI->utils->debug_log('======================================haofu checkWithdrawStatus url: ', $url );
		$this->CI->utils->debug_log('======================================haofu checkWithdrawStatus result: ', $response );
		$this->CI->utils->debug_log('======================================haofu checkWithdrawStatus decoded Result', $decodedResult);

		return $decodedResult;
    }
    
    public function callbackFromServer($transId, $params) {
        $response_result_id = parent::callbackFromServer($transId, $params);

        if(empty($params) || is_null($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }
        $result = array('success' => false, 'message' => 'Payment failed');

        $this->utils->debug_log("==========================haofu checkCallback params", $params);

        $order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

        if (!$this->checkCallbackOrder($order, $params)) {
            return $result;
        }

        if($params['status'] == self::CALLBACK_SUCCESS) {
            $this->utils->debug_log('==========================haofu withdrawal payment was successful: trade ID [%s]', $params['out_trade_no']);
            
            $msg = sprintf('haofu withdrawal was successful: trade ID [%s]',$params['out_trade_no']);
            $this->CI->wallet_model->withdrawalAPIReturnSuccess($transId, $msg);

            $result['message'] = self::CALLBACK_MSG_SUCCESS;
            $result['success'] = true;

       }elseif($params['status'] == self::CALLBACK_FAILED){
            $this->utils->debug_log('==========================haofu withdrawal payment was failed: trade ID [%s]', $params['out_trade_no']);

            $msg = sprintf('haofu withdrawal payment was failed: trade ID [%s] ',$params['out_trade_no']);
            $this->writePaymentErrorLog($msg, $params);
            $this->CI->wallet_model->withdrawalAPIReturnFailure($transId, $msg);
            $result['message'] = self::CALLBACK_MSG_SUCCESS;
            $result['success'] = true;

       }else {
            $msg = sprintf('haofu withdrawal payment was not successful  trade ID [%s] ',$params['out_trade_no']);
            $this->debug_log($msg, $params);
            $result['message'] = $msg;
        }

        return $result;
    }

 
    public function checkCallbackOrder($order, $fields) {
        $requiredFields = array('trade_id', 'status', 'amount_str', 'out_trade_no', 'sign');

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=======================haofu withdrawal checkCallbackOrder missing parameter: [$f]", $fields);
                return false;
            }
        }
        
        if ($fields['sign'] != $this->validateSign($fields)) {
            $this->writePaymentErrorLog('==========================haofu withdrawal checkCallback signature Error',$fields);
            return false;
        }

		if ($fields['amount_str'] != $this->convertAmountToCurrency($order['amount'])) {
            $this->writePaymentErrorLog('=========================haofu withdrawal checkCallbackOrder payment amount is wrong, expected =>'. $order['amount'], $fields);
			return false;
		}

        if ($fields['out_trade_no'] != $order['transactionCode']) {
            $this->writePaymentErrorLog('=========================haofu withdrawal checkCallbackOrder order IDs do not match, expected =>'. $order['transactionCode'], $fields); 
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
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if(($key == 'sign') || $value == '') {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        return $signStr.$this->getSystemInfo('key');
    }


    # -- bankinfo --
    public function getBankInfo() {
        $bankInfo = array();
        $bankInfoArr = $this->getSystemInfo("withdrawal_bank_info");
        if(!empty($bankInfoArr)) {
            foreach($bankInfoArr as $bankInfoItem) {
                $bankInfo[$bankInfoItem[0]] = $bankInfoItem[1];
            }
            $this->utils->debug_log("==================getting haofu bank info from extra_info: ", $bankInfo);
        } else  {
            $bankInfo = array(
                '1' => array('name' => '中国工商银行', 'code' => 'ICBC'),
                '2' => array('name' => '招商银行', 'code' => 'CMB'),
                '3' => array('name' => '中国建设银行', 'code' => 'CCB'),
                '4' => array('name' => '中国农业银行', 'code' => 'ABC'),
                '5' => array('name' => '交通银行', 'code' => 'COMM'),
                '6' => array('name' => '中国银行', 'code' => 'BOC'),
                // '7' => array('name' => '深圳发展银行', 'code' => 'SDB'),
                '8' => array('name' => '广发银行', 'code' => 'GDB'),
                // '9' => array('name' => '东莞农村商业银行', 'code' => 'BRCB'),
                '10' => array('name' => '中信银行', 'code' => 'CITIC'),
                '11' => array('name' => '中国民生银行', 'code' => 'CMBC'),
                '12' => array('name' => '中国邮政储蓄银行', 'code' => 'PSBC'),
                '13' => array('name' => '兴业银行', 'code' => 'CIB'),
                '14' => array('name' => '华夏银行', 'code' => 'HXB'),
                '15' => array('name' => '平安银行', 'code' => 'SZPAB'),
                // '16' => array('name' => '广西农村信用社联合社', 'code' => 'GXNXB'),
                // '17' => array('name' => '广州银行', 'code' => 'GZCB'),
                '18' => array('name' => '南京银行', 'code' => 'NJCB'),
                // '19' => array('name' => '广州农商银行', 'code' => 'GRCB'),
                '20' => array('name' => '中国光大银行', 'code' => 'CEB'),
                '32' => array('name' => '上海浦东发展银行', 'code' => 'SPDB'),

                // '' => array('name' => '北京银行', 'code' => 'BCCB'),
                // '' => array('name' => '汉口银行', 'code' => 'HKBCHINA'),
                // '' => array('name' => '杭州银行', 'code' => 'HCCB'),
                // '' => array('name' => '晋城银行', 'code' => 'SXJS'),
                // '' => array('name' => '宁波银行', 'code' => 'NBCB'),
                // '' => array('name' => '上海银行', 'code' => 'BOS'),
                // '' => array('name' => '长沙银行', 'code' => 'CSCB'),
                // '' => array('name' => '浙江稠州商业银行', 'code' => 'CZCB'),
                // '' => array('name' => '顺德农村商业银行', 'code' => 'SDBC'),
                // '' => array('name' => '恒丰银行', 'code' => 'EGBANK'),
                // '' => array('name' => '浙商银行', 'code' => 'CZB'),BJRCB
                // '' => array('name' => '渤海银行', 'code' => 'CBHB'),
                // '' => array('name' => '徽商银行', 'code' => 'HSBANK'),
                // '' => array('name' => '上海农商银行', 'code' => 'SHRCB'),
                // '' => array('name' => '北京农村商业行', 'code' => 'BJRCB'),
                // '' => array('name' => '深圳农商行', 'code' => 'SNXS')
            );
            $this->utils->debug_log("=======================getting haofu bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }

    
	protected function getNotifyUrl($orderId) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }
    
	public function randomNum($length) {
	    $str="12345678901234567890";
	    $result=substr(str_shuffle($str),0,$length);
	    return $result;
	}
}