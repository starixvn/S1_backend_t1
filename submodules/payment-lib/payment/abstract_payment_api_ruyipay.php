<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * RUYIPAY 如意
 *
 * * RUYIPAY_PAYMENT_API, ID: 436
 * * RUYIPAY_WEIXIN_PAYMENT_API, ID: 437
 * * RUYIPAY_WEIXIN_WAP_PAYMENT_API, ID: 438
 * * RUYIPAY_ALIPAY_PAYMENT_API, ID: 439
 * * RUYIPAY_ALIPAY_WAP_PAYMENT_API, ID: 440
 * * RUYIPAY_QQPAY_PAYMENT_API, ID: 441
 * * RUYIPAY_QQPAY_WAP_PAYMENT_API, ID: 442
 * * RUYIPAY_JDPAY_PAYMENT_API, ID: 443
 * * RUYIPAY_JDPAY_WAP_PAYMENT_API, ID: 444
 * * RUYIPAY_UNIONPAY_PAYMENT_API, ID: 445
 * * RUYIPAY_QUICKPAY_PAYMENT_API, ID: 455
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
abstract class Abstract_payment_api_ruyipay extends Abstract_payment_api {

	const PAYTYPE_BANKPAY = '10';
	const PAYTYPE_QUICKPAY = '20';

	const PAYTYPE_WEIXIN = '30';
	const PAYTYPE_WEIXIN_WAP = '31';
	const PAYTYPE_ALIPAY = '40';
	const PAYTYPE_ALIPAY_WAP = '41';
	const PAYTYPE_QQPAY = '50';
	const PAYTYPE_QQPAY_WAP = '51';
	const PAYTYPE_JDPAY = '60';
	const PAYTYPE_JDPAY_WAP = '61';
	const PAYTYPE_UNIONPAY = '11';

	const BANKCODE_WEIXIN = 'WECHATQR';
	const BANKCODE_WEIXIN_WAP = 'WECHATWAP';
	const BANKCODE_ALIPAY = 'ALIPAYQR';
	const BANKCODE_ALIPAY_WAP = 'ALIPAYWAP';
	const BANKCODE_QQPAY = 'QQWALLET';
	const BANKCODE_QQPAY_WAP = 'QQWAP';
	const BANKCODE_JDPAY = 'JDWALLET';
	const BANKCODE_JDPAY_WAP = 'JDWAP';
	const BANKCODE_QUICKPAY = 'QUICK';
	const BANKCODE_UNIONPAY = 'UNIONQR';


	const QRCODE_REPONSE_CODE_SUCCESS = 'SUCCESS';
	const ORDER_STATUS_SUCCESS = 'success002';
	const RETURN_SUCCESS_CODE = 'stopnotify';
	const RETURN_FAILED_CODE = 'faile';

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

		$params['merId'] = $this->getSystemInfo("account");
		$params['merOrdId'] = $order->secure_id;
		$params['merOrdAmt'] = $this->convertAmountToCurrency($amount);
		$params['remark'] = 'Deposit';
		$params['returnUrl'] = $this->CI->utils->site_url_with_http()."callback/show_pending/".$this->getPlatformCode()."/".$orderId;
		$params['notifyUrl'] = $this->getNotifyUrl($orderId);
		$params['signType'] = 'MD5';

		$this->configParams($params, $order->direct_pay_extra_info);

		$params['signMsg'] = $this->sign($params);

		$this->CI->utils->debug_log("=====================ruyipay generatePaymentUrlForm", $params);

		return $this->processPaymentUrlForm($params);
	}

	# Submit POST form
	protected function processPaymentUrlFormPost($params) {
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_FORM,
			'url' => $this->getSystemInfo('url'),
			'params' => $params,
			'post' => true,
		);
	}

	# Display QRCode get from curl
	//protected function processPaymentUrlFormQRCode($params) {
		// $this->CI->utils->debug_log('=====================ruyipay scan url', $this->getSystemInfo('url'));
		// $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['merOrdId']);
		// $response = json_decode($response, true);

		// $this->CI->utils->debug_log('========================================ruyipay response', $response);

		// if($response['retCode'] && $response['retCode'] == self::ORDER_STATUS_SUCCESS) {
		// 	return array(
		// 		'success' => true,
		// 		'type' => self::REDIRECT_TYPE_QRCODE,
		// 		'url' => $response['data']['url']
		// 	);
		// }
		// else if($response['msg']) {
		// 	return array(
		// 		'success' => false,
		// 		'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
		// 		'message' => $response['msg']
		// 	);
		// }
		// else {
		// 	return array(
		// 		'success' => false,
		// 		'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
		// 		'message' => lang('Invalidte API response')
		// 	);
		// }
	//}



	/**
	 * detail: This will be called when the payment is async, API server calls our callback page,
	 * When that happens, we perform verifications and necessary database updates to mark the payment as successful
	 *
	 * @param int $orderId order id
	 * @param array $params
	 * @return array
	 */
	public function callbackFromServer($orderId, $params) {
		$response_result_id = parent::callbackFromServer($orderId, $params);
		return $this->callbackFrom('server', $orderId, $params, $response_result_id);
	}

	/**
	 * detail: This will be called when user redirects back to our page from payment API
	 *
	 * @param int $orderId order id
	 * @param array $params
	 * @return array
	 */
	public function callbackFromBrowser($orderId, $params) {
		$response_result_id = parent::callbackFromBrowser($orderId, $params);
		return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
	}

	# $source can be 'server' or 'browser'
	private function callbackFrom($source, $orderId, $params, $response_result_id) {
		$result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
		$order = $this->CI->sale_order->getSaleOrderById($orderId);
		$processed = false;

		if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
			return $result;
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
			/*$this->CI->sale_order->updateExternalInfo($order->id,
				$params['OrdNo'], '',
				null, null, $response_result_id);*/
			if ($source == 'browser') {
				$success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
			} elseif ($source == 'server') {
				$this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
			}
		}

		$result['success'] = $success;
		if ($success) {
			$result['message'] = self::RETURN_SUCCESS_CODE;
		} else {
			$result['return_error'] = $processed ? self::RETURN_SUCCESS_CODE : self::RETURN_FAILED_CODE;
		}

		if ($source == 'browser') {
			$result['next_url'] = $this->getPlayerBackUrl();
			$result['go_success_page'] = true;
		}

		return $result;
	}

	/**
	 * detail: Validates whether the callback from API contains valid info and matches with the order
	 *
	 * @return boolean
	 */

	private function checkCallbackOrder($order, $fields, &$processed = false) {
		$requiredFields = array(
			'merId', 'merOrdId', 'merOrdAmt', 'sysOrdId', 'tradeStatus', 'remark', 'signType' ,'signMsg'
		);

		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
				$this->writePaymentErrorLog("=====================ruyipay missing parameter: [$f]", $fields);
				return false;
			}
		}

		if ($fields['tradeStatus'] != self::ORDER_STATUS_SUCCESS) {
			$payStatus = $fields['tradeStatus'];
			$this->writePaymentErrorLog("=====================ruyipay Payment was not successful, payStatus is [$payStatus]", $fields);
			return false;
		}

		if ( $this->convertAmountToCurrency($order->amount) != floatval( $fields['merOrdAmt'] )
		) {
			$this->writePaymentErrorLog("=====================ruyipay Payment amounts do not match, expected [$order->amount]", $fields);
			return false;
		}

		$processed = true; # processed is set to true once the signature verification pass

		# everything checked ok
		return true;
	}

	public function directPay($order = null) {
		return array('success' => false); # direct pay not supported by this API
	}

	public function getBankListInfoFallback() {
		return array(
			array('label' => '农业银行', 'value' => 'ABC'),
			array('label' => '工商银行', 'value' => 'ICBC'),
			array('label' => '建设银行', 'value' => 'CCB'),
			array('label' => '交通银行', 'value' => 'BCOM'),
			array('label' => '中国银行', 'value' => 'BOC'),
			array('label' => '招商银行', 'value' => 'CMB'),
			array('label' => '民生银行', 'value' => 'CMBC'),
			array('label' => '光大银行', 'value' => 'CEBB'),
			array('label' => '兴业银行', 'value' => 'CIB'),
			array('label' => '中国邮政', 'value' => 'PSBC'),
			array('label' => '平安银行', 'value' => 'SPABANK'),
			array('label' => '中信银行', 'value' => 'ECITIC'),
			array('label' => '广东发展银行', 'value' => 'GDB'),
			array('label' => '华夏银行', 'value' => 'HXB'),
			array('label' => '浦发银行', 'value' => 'SPDB')
		);
	}

	# -- Private functions --
	/**
	 * detail: After payment is complete, the gateway will invoke this URL asynchronously
	 *
	 * @param int $orderId
	 * @return void
	 */
	private function getNotifyUrl($orderId) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
	}

	/**
	 * detail: After payment is complete, the gateway will send redirect back to this URL
	 *
	 * @param int $orderId
	 * @return void
	 */
	private function getReturnUrl($orderId) {
		return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
	}

	/**
	 * detail: Format the amount value for the API
	 *
	 * @param float $amount
	 * @return float
	 */
	protected function convertAmountToCurrency($amount) {
		return number_format($amount, 2, '.', '');
	}

	# -- private helper functions --

	/**
	 * detail: getting the signature
	 *
	 * @param array $data
	 * @return	string
	 */
	public function sign($params) {
		$keys = array('merId', 'merOrdId', 'merOrdAmt', 'payType', 'bankCode', 'remark', 'returnUrl', 'notifyUrl', 'signType');
		$signStr = '';
		foreach($keys as $key) {
			$signStr .= $key.'='.$params[$key].'&';
		}
		$signStr .= 'merKey='.$this->getSystemInfo('key');
		$sign = md5($signStr);
		return $sign;

	}
}
