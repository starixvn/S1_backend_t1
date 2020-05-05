<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * KZPAY  
 *
 * * 'KZPAY_PAYMENT_API', ID 5609
 * * 'KZPAY_WEIXIN_PAYMENT_API', ID 5038
 * * 'KZPAY_ALIPAY_PAYMENT_API', ID 5039
 * * 'KZPAY_ALIPAY_BANKCARD_PAYMENT_API', ID 5040
 *
 * Required Fields:
 *
 * * URL
 * * Account - Merchant ID
 * * Key - Signing key
 * * Extra Info
 *
 *
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
abstract class Abstract_payment_api_kzpay extends Abstract_payment_api {

    const PAYWAY_ONLINE= '1'; 
    const PAYWAY_WEIXIN= '2'; 
    const PAYWAY_ALIPAY= '3'; 
    const PAYWAY_ALIPAY_BANKCARD= '5'; 
   

    const RETURN_SUCCESS_CODE = '8888';
    const RETURN_FAILED_CODE = 'fail';
    const PAY_RESULT_SUCCESS = 'SUCCESS';

	# Implement these for specific pay type
	protected abstract function configParams(&$params, $direct_pay_extra_info);
	protected abstract function processPaymentUrlForm($params);

	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		if ($this->shouldRedirect($enabledSecondUrl)) {
			$url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
			$result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
			return $result;
		}

		$order = $this->CI->sale_order->getSaleOrderById($orderId);
		$params['mchNo'] = $this->getSystemInfo("account");
		$params['mchOrderNo'] = $order->secure_id;
		$params['orderMoney'] = $this->convertAmountToCurrency($amount); //元
		$params['callBackUrl'] = $this->getNotifyUrl($orderId);
	
		$this->configParams($params, $order->direct_pay_extra_info);


		$params['sign'] = $this->sign($params);
		$this->CI->utils->debug_log("=====================kzpay generatePaymentUrlForm", $params);

		return $this->processPaymentUrlForm($params);
	}

	protected function processPaymentUrlFormPost($params) {
		$this->_custom_curl_header= ["Content-Type: application/json"];
		$response = $this->submitPostForm($this->getSystemInfo('url'), $params, true, $params['mchOrderNo']);
		
		$this->CI->utils->debug_log('=====================kzpay processPaymentUrlFormPost response', $response);

		$dataJson = $this->isJson($response);
		if ($dataJson) {
			$response = json_decode($response, true);
			return array(
				'success' => false,
				'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
				'message' => lang('Invalidate API response')
			);
		}

		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_URL,
			'url' => $response
		);
		
	}

	protected function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	

	public function callbackFromServer($orderId, $params) {
		$response_result_id = parent::callbackFromServer($orderId, $params);
		return $this->callbackFrom('server', $orderId, $params, $response_result_id);
	}

	public function callbackFromBrowser($orderId, $params) {
		$response_result_id = parent::callbackFromBrowser($orderId, $params);
		return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
	}

	# $source can be 'server' or 'browser'
	private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        if(empty($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $params = json_decode($raw_post_data, true);
        }

        if($source == 'server'){
            $this->CI->utils->debug_log('=======================kzpay callbackFromServer server callbackFrom', $params);
            if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
                return $result;
            }
        }

		# Update order payment status and balance
		$success=true;

		# Update player balance based on order status
		# if it's STATUS_SETTLED or STATUS_BROWSER_CALLBACK, put log, and ignore
		$orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderId);
		if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
			$this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
			if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
				$this->CI->sale_order->setStatusToSettled($orderId);
			}
		} else {
			# update player balance
            $this->CI->sale_order->updateExternalInfo($order->id, $params['mchOrderNo'], null, null, null, $response_result_id);
			if ($source == 'browser') {
				$success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
			} elseif ($source == 'server') {
				$this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
			}
		}

		$result['success'] = $success;
		if ($success) {
			$resultContent = ['code' => self::RETURN_SUCCESS_CODE];
			$result['message'] = json_encode($resultContent);
		} else {
			$resultContent = ['code' => self::RETURN_SUCCESS_CODE];
			$result['message'] = $processed ? json_encode($resultContent) : self::RETURN_FAILED_CODE;
		}

		if ($source == 'browser') {
			$result['next_url'] = $this->getPlayerBackUrl();
			$result['go_success_page'] = true;
		}

		return $result;
	}

	private function checkCallbackOrder($order, $fields, &$processed = false) {
		$requiredFields = array('mchOrderNo', 'orderMoney','payTime','status','sign');

		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
				$this->writePaymentErrorLog("=====================kzpay missing parameter: [$f]", $fields);
				return false;
			}
		}

        # is signature authentic?
        if (!$this->verifySignature($fields)) {
            $this->writePaymentErrorLog('=======================kzpay checkCallbackOrder verify signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

		if ($fields['status'] != self::PAY_RESULT_SUCCESS) {
			$payStatus = $fields['result'];
			$this->writePaymentErrorLog("=====================kzpay Payment was not successful, payStatus is [$payStatus]", $fields);
			return false;
		}

		if ( $this->convertAmountToCurrency($order->amount) != $fields['orderMoney'] ) {
			if($this->getSystemInfo('allow_callback_amount_diff') && $this->checkAmountRange($order->amount,$fields['orderMoney'])){
				$this->CI->utils->debug_log('=====================kzpay amount not match expected [$order->amount]');
				$notes = $order->notes . " | callback diff amount, origin was: " . $order->amount;
				
				$this->CI->sale_order->fixOrderAmount($order->id, $fields['orderMoney'], $notes);
    
			}
			 else{
				$this->writePaymentErrorLog("=====================kzpay Payment amounts do not match, expected [$order->amount]", $fields);
				return false;
			}
		}

        if ($fields['mchOrderNo'] != $order->secure_id) {
            $this->writePaymentErrorLog("========================kzpay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
	   }
	  

		# everything checked ok
		return true;
	}

	public function directPay($order = null) {
		return array('success' => false); # direct pay not supported by this API
	}

	public function getBankListInfoFallback() {
		return array(
            array('label' => '工商银行', 'value' => 'ICBC'),
            array('label' => '农业银行', 'value' => 'ABC'),
            array('label' => '中国银行', 'value' => 'BOC'),
            array('label' => '建设银行', 'value' => 'CCB'),
            array('label' => '交通银行', 'value' => 'BOCOM'),
            array('label' => '中信银行', 'value' => 'CNCB'),
            array('label' => '光大银行', 'value' => 'CEB'),
            array('label' => '华夏银行', 'value' => 'HXB'),
            array('label' => '民生银行', 'value' => 'CMBC'),
            array('label' => '广发银行', 'value' => 'GDB'),
            array('label' => '平安银行', 'value' => 'PAB'),
            array('label' => '招商银行', 'value' => 'CMB'),
            array('label' => '兴业银行', 'value' => 'CIB'),
            array('label' => '浦发银行', 'value' => 'SPDB'),
            array('label' => '北京银行', 'value' => 'BCCB'),
            array('label' => '上海银行', 'value' => 'BOS'),
            array('label' => '邮政储蓄银行', 'value' => 'PSBC'),
		);
	}

	protected function checkAmountRange($originAmount , $realAmount) {
		$maxAmount = (float) $originAmount + 1;
		$minAmount = (float) $originAmount -1;
		if ( (float) $realAmount > $minAmount &&  (float) $realAmount < $maxAmount) {
			return true;
		}
		return false;
	}

	# -- Private functions --
	private function getNotifyUrl($orderId) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
	}

	private function getReturnUrl($orderId) {
		return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
	}

	protected function convertAmountToCurrency($amount) {
		return number_format($amount, 2, '.', '');
	}

	# -- private helper functions --
	public function sign($params) {
       	$signStr = $this->createSignStr($params);
        	$sign = md5($signStr);

		return strtoupper($sign);
	}

    public function verifySignature($params) {
		$data_keys = array('mchOrderNo','orderMoney','payTime');
		$signStr = '';
		foreach ($data_keys as $value) {
			$signStr .= $value."=".$params[$value]."&";
		}
	
		$signStr .= 'key='. $this->getSystemInfo('key');
		$sign = md5($signStr);

        	return $sign == $params['sign'];
    }

    private function createSignStr($params) {
	
		$data_keys = array('callBackUrl','mchNo','mchOrderNo','orderMoney','payWay');
		$signStr = '';
		foreach ($data_keys as $value) {
			  $signStr .= $value."=".$params[$value]."&";
		}
		
		$signStr .= 'key='. $this->getSystemInfo('key');
		return $signStr;
	}
}
