<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * Avodapay
 *
 * * AVODAPAY_PAYMENT_API, ID: 5716
 *
 * Required Fields:
 *
 * * URL
 * * Account - Merchant ID
 * * accessKey
 *
 * Field Values:
 *
 * * Extra Info:
 * > {
 * >
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
abstract class Abstract_payment_api_avodapay extends Abstract_payment_api {
	const RETURN_SUCCESS_CODE = 'SUCCESS';
	const CALLBACK_STATUS_SUCCESS = '0000';
    const CALLBACK_STATUS_FAILED = '0001';

	public function __construct($params = null) {
		parent::__construct($params);
	}

	# Implement these to specify pay type
	protected abstract function configParams(&$params, $direct_pay_extra_info);
	protected abstract function processPaymentUrlForm($params);

	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		if ($this->shouldRedirect($enabledSecondUrl)) {
			$url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
			$result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
			return $result;
		}
		$order = $this->CI->sale_order->getSaleOrderById($orderId);
		$params = array();
		$params['paymentId'] = $order->secure_id;
		$params['merchantId'] = $this->getSystemInfo("account");
		$params['amount'] = $this->convertAmountToCurrency($amount);
		$params['notifyUrl'] = $this->getNotifyUrl($orderId);
		$params['returnUrl'] = $this->getReturnUrl($orderId);
		$params['accessKey'] = $this->getSystemInfo('key');
		$params['mode'] = 'cup';
		$this->configParams($params, $order->direct_pay_extra_info);

		$this->CI->utils->debug_log("===================avodapay params", $params);
		return $this->processPaymentUrlForm($params);
	}

	# Submit POST form

	protected function processPaymentUrlFormPost($params) {
		$response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['paymentId']);
		$this->CI->utils->debug_log('=====================avodapay processPaymentUrlFormPost response', $response);
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_URL,
			'url' => $response,
		);
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
        $this->CI->utils->debug_log("===============avodapay callbackFrom $source params", $params);

        if($source == 'server'){
            if (empty($params)) {
                $raw_post_data = file_get_contents('php://input', 'r');
                $this->CI->utils->debug_log("=====================avodapay raw_post_data", $raw_post_data);
                $params = json_decode($raw_post_data, true);
                $this->CI->utils->debug_log("=====================avodapay json_decode params", $params);
            }

            if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
                return $result;
            }
        }

        # Update order payment status and balance
        $success = true;

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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['paymentId'], null, null, null, $response_result_id);
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
            }
        }

        $result['success'] = $success;
        if ($processed) {
            $result['message'] = self::RETURN_SUCCESS_CODE;
        } else {
            $result['return_error'] = 'Error';
        }

        if ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return $result;
	}

	# returns true if callback is valid and payment is successful
	# sets the $callbackValid parameter if callback is valid
	private function checkCallbackOrder($order, $fields, &$callbackValid) {
		# does all required fields exist?
		$requiredFields = array(
			'paymentId', 'amount','status', 'hmac'
		);
		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
				$this->writePaymentErrorLog("Missing parameter: [$f]", $fields);
				return false;
			}
		}

		# is signature authentic?
		if (!$this->validateSign($fields)) {
			$this->writePaymentErrorLog('Signature Error', $fields);
			return false;
		}

		$callbackValid = true; # callbackValid is set to true once the signature verification pass

		if ($fields['paymentId'] != $order->secure_id) {
			$this->writePaymentErrorLog("Order IDs do not match, expected [$order->secure_id]", $fields);
			return false;
		}

		if ($fields['status'] != self::CALLBACK_STATUS_SUCCESS) {
			$this->writePaymentErrorLog('Payment was not successful', $fields);
			return false;
		}

		if ($this->convertAmountToCurrency($order->amount) != $fields['amount']) {
			$this->writePaymentErrorLog("Payment amounts do not match, expected [$order->amount]", $fields);
			return false;
		}

		# everything checked ok
		return true;
	}


	public function directPay($order = null) {
		return array('success' => false); # direct pay not supported by this API
	}

	# -- private helper functions --
	public function getBankListInfoFallback() {
        return array(
            array('label' => '中国工商银行' , 'value' => 'ICBC'),
            // array('label' => '中国农业银行' , 'value' => 'ABC'),
            // array('label' => '中国建设银行' , 'value' => 'CCB'),
            array('label' => '中国银行' , 'value' => 'BOC'),
            // array('label' => '浦发银行' , 'value' => 'SPDB'),
            // array('label' => '光大银行' , 'value' => 'CEB'),
            // array('label' => '平安银行' , 'value' => 'PINGAN'),
            // array('label' => '兴业银行' , 'value' => 'CIB'),
            // array('label' => '邮政储蓄银行' , 'value' => 'POST'),
            // array('label' => '中信银行' , 'value' => 'ECITIC'),
            // array('label' => '华夏银行' , 'value' => 'HXB'),
            // array('label' => '招商银行' , 'value' => 'CMBCHINA'),
             array('label' => '广发银行' , 'value' => 'GDB'),
             array('label' => '邮储银行' , 'value' => 'PSBC'),
            // array('label' => '上海银行' , 'value' => 'SHB'),
             array('label' => '民生银行' , 'value' => 'CMBC'),
            // array('label' => '北京农村商业银行' , 'value' => 'BJRCB')
        );
    }


	public function convertAmountToCurrency($amount) {
		return number_format($amount*100, 0, '.', '');
	}

	private function getNotifyUrl($orderId) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
	}

	private function getReturnUrl($orderId) {
		return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
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

	}

	private function createSignStr($params) {
		$keys = array('paymentId', 'amount', 'status');
        $signStr = "";
        foreach($keys as $key) {
            if (array_key_exists($key, $params)) {
                $signStr .= "$key=$params[$key]&";
            }
		}
		$signStr .= 'accesskey='.$this->getSystemInfo('key');
		return $signStr;
	}
}
