<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';
/**
 * DSDFPAY 代收代付
 * https://www.dsdfpay.com/html/admin/login.html
 *
 * DSDFPAY_PAYMENT_API, ID: 239
 * DSDFPAY_ALIPAY_PAYMENT_API, ID: 240
 * DSDFPAY_WEIXIN_PAYMENT_API, ID: 241
 * DSDFPAY_QQPAY_PAYMENT_API, ID: 242
 * DSDFPAY_REMIT_PAYMENT_API, ID: 333
 * DSDFPAY_JDPAY_PAYMENT_API, ID: 778
 * DSDFPAY_UNIONPAY_PAYMENT_API, ID: 779
 * DSDFPAY_QUICKPAY_PAYMENT_API, ID: 780
 * DSDFPAY_BIDUPAY_PAYMENT_API, ID: 781
 * DSDFPAY_MEITUAN_PAYMENT_API, ID: 782
 * DSDFPAY_QUICKPAY_H5_PAYMENT_API, ID: 783
 * DSDFPAY_WEIXIN_H5_PAYMENT_API, ID: 836
 * DSDFPAY_QQPAY_H5_PAYMENT_API, ID: 837
 * DSDFPAY_JDPAY_H5_PAYMENT_API, ID: 838
 * DSDFPAY_UNIONPAY_H5_PAYMENT_API, ID: 839
 * DSDFPAY_ALIPAY_H5_PAYMENT_API, ID: 846
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://www.dsdfpay.com/dsdf/customer_pay/init_din
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_dsdfpay extends Abstract_payment_api {
	const RETURN_SUCCESS_CODE = 'true';
	const RETURN_FAILED_CODE = '{"success":true,"msg":"failed"}';

	public abstract function getBankType($direct_pay_extra_info);
	public abstract function getTypeFlag($direct_pay_extra_info);

	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		if ($this->shouldRedirect($enabledSecondUrl)) {
			$url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
			$result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
			return $result;
		}

		$order = $this->CI->sale_order->getSaleOrderById($orderId);
        $this->CI->load->model(array('player'));
		$player = $this->CI->player->getPlayerById($playerId);
		$username = $player['username'];

        $playerDetails = $this->CI->player_model->getPlayerDetails($playerId);
        $firstname = (isset($playerDetails[0]) && !empty($playerDetails[0]['firstName']))     ? $playerDetails[0]['firstName']     : '';
		$lastname  = (isset($playerDetails[0]) && !empty($playerDetails[0]['lastName']))      ? $playerDetails[0]['lastName']      : '';

		$params['cid'] = $this->getSystemInfo("account");
		$params['uid'] = $username;
		$params['time'] = time();
		$params['amount'] = $this->convertAmountToCurrency($amount);
		$params['order_id'] = $order->secure_id;
		$params['ip'] = $this->getClientIP();
		$params['sign'] = $this->sign($params);
		$params['type'] = $this->getBankType($order->direct_pay_extra_info);
		$params['tflag'] = $this->getTypeFlag($order->direct_pay_extra_info);
		if($params['type'] == 'remit' || $params['type'] == 'quickp2p'){
			$params['from_username'] = $lastname.$firstname;
		}

		$this->CI->utils->debug_log('======================================dsdfpay generatePaymentUrlForm: ', $params);
		return $this->processPaymentUrlForm($params);
	}

	protected function processPaymentUrlForm($params) {
		$url = $this->getSystemInfo('url');
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_FORM,
			'url' => $url,
			'params' => $params,
			'post' => true,
		);
	}

	# Callback URI: /callback/fixed_process/<payment_id>
	public function getOrderIdFromParameters($params) {
		$raw_post_data = file_get_contents('php://input', 'r');
        $flds = json_decode($raw_post_data, true);

		$orderId = null;
		//for fixed return url on browser
		if (isset($flds['order_id'])) {
			$secure_id = $flds['order_id'];

			if($flds['direction'] == 'in') {
				$this->CI->load->model(array('sale_order'));
				$order = $this->CI->sale_order->getSaleOrderBySecureId($secure_id);

				$orderId = $order->id;
			}
			else if($flds['direction'] == 'out') {
				$transId = $flds['order_id'];
				$this->CI->load->model(array('wallet_model'));
				$walletAccount = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

				$orderId = $walletAccount['transactionCode'];
			}
			else {
				$this->utils->debug_log('====================================dsdfpay callbackOrder direction is not out or in when getOrderIdFromParameters', $flds);
			}
		}
		else {
			$this->utils->debug_log('====================================dsdfpay callbackOrder cannot get any order_id when getOrderIdFromParameters', $flds);
		}
		return $orderId;
	}

	public function callbackFromServer($orderId, $params) {
		$response_result_id = parent::callbackFromServer($orderId, $params);

        $this->CI->utils->debug_log('==============dsdfpay process entry ===' );
        $this->CI->utils->debug_log('==============dsdfpay process withdrawalResult starting...id', $orderId, $params );

		$raw_post_data = file_get_contents('php://input', 'r');
        $flds = json_decode($raw_post_data, true);
        $params = array_merge( $params, $flds );

		if($params['direction']=='out'){
			$this->CI->utils->debug_log('==============dsdfpay process withdrawalResult order id', $orderId);
			$result = array('success' => false, 'message' => 'Payment failed');

			$walletAccount=$this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);

			if (!$this->verify()) {
				if(!$this->verify(true)){
					$this->writePaymentErrorLog('====================dsdfpay withdrawal checkCallback signature Recheck Error', $params);
					$result = ['success' => false, 'message' => 'dsdfpay withdrawal checkCallback signature Error'];
					return $result;
				}
			}

			if ($params['cmd'] != "order_success" && $params['cmd'] != "order_revoked") {
				$this->writePaymentErrorLog('==============dsdfpay withdrawal checkCallback payment was not successful or been revoked', $params);
				$result = ['success' => false, 'message' => 'dsdfpay withdrawal checkCallback payment was not successful or been revoked'];
				return $result;
			}

			$walletAccount=$this->CI->wallet_model->getWalletAccountByTransactionCode($params['order_id']);

			if (
				$this->convertAmountToCurrency($walletAccount['amount']) !=
				$this->convertAmountToCurrency(floatval($params['amount'] / 100.0 ))
			) {
				$this->writePaymentErrorLog("==============dsdfpay withdrawal checkCallback payment amounts do not match, expected ".$walletAccount['amount'], $fields);
				$result = ['success' => false, 'message' => 'dsdfpay withdrawal checkCallback payment amounts do not match'];
				return $result;
			}

			if($params['cmd'] == "order_success") {
				$msg = sprintf('dsdfpay payment was successful: trade ID [%s]', $params['order_id']);
				$fee = $this->convertAmountToCurrency($params['trans_fee'] / 100.0);
				$amount = $this->convertAmountToCurrency(floatval($params['amount'] / 100.0 ));
				$this->CI->wallet_model->withdrawalAPIReturnSuccess($orderId, $msg, $fee, $amount);
				$result['message'] = self::RETURN_SUCCESS_CODE;
				$result['success'] = true;
			}
			else if($params['cmd'] == "order_revoked") {
				$msg = sprintf('dsdfpay payment was been revoked: trade ID [%s]', $params['order_id']);
				$this->CI->wallet_model->withdrawalAPIReturnFailure($orderId, $msg);
				$result['message'] = self::RETURN_FAILED_CODE;
				$result['success'] = true;
			}
			else {
				$result = ['success' => false, 'message' => 'Callback failed for unknown reason.'];
			}

			return $result;
		}

		return $this->callbackFrom('server', $orderId, $params, $response_result_id);
	}

	public function callbackFromBrowser($orderId, $params) {
		$response_result_id = parent::callbackFromBrowser($orderId, $params);
		# According to documentation, callback from browser cannot be used to change order status. We need to rely on callback from server.
		# Return success here.
		return array('success' => true, 'next_url' => $this->getPlayerBackUrl());
	}

	# $source can be 'server' or 'browser'
	private function callbackFrom($source, $orderId, $params, $response_result_id) {
		$result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
		$order = $this->CI->sale_order->getSaleOrderById($orderId);
		$processed = false;

		$raw_post_data = file_get_contents('php://input', 'r');
        $flds = json_decode($raw_post_data, true);
        $params = array_merge( $params, $flds );

		if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
			return $result;
		}

		# Update order payment status and balance
		$success=true;

		# Update player balance based on order status
		# if it's STATUS_SETTLED or STATUS_BROWSER_CALLBACK, put log, and ignore
		$orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderId);
		if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
			$this->CI->utils->debug_log('====================callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
			if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
				$this->CI->sale_order->setStatusToSettled($orderId);
			}
		} else {
			# update player balance
			$this->CI->sale_order->updateExternalInfo($order->id, $params['sysorderid'], null, null, null, $response_result_id);
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

	private function checkCallbackOrder($order, $fields, &$processed = false) {
		$requiredFields = array(
			'order_id', 'amount', 'customer_name', 'verified_time', 'created_time', 'cmd', 'type' , 'customer_bankflag', 'orig_amount'
		);
		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
				$this->writePaymentErrorLog("====================dsdfpay checkCallbackOrder Missing parameter: [$f]", $fields);
				return false;
			}
		}

		if (!$this->verify()) {
			if(!$this->verify(true)){
				$this->writePaymentErrorLog('====================dsdfpay checkCallbackOrder Signature Recheck Error', $fields);
				return false;
			}
		}

		$processed = true;

		if ($fields['cmd'] != "order_success") {
			$this->writePaymentErrorLog('====================dsdfpay checkCallbackOrder Payment was not successful', $fields);
			return false;
		}

		if (
			$this->convertAmountToCurrency($order->amount) !=
			$this->convertAmountToCurrency(floatval($fields['orig_amount'] / 100.0 ))
		) {
			$this->writePaymentErrorLog("====================dsdfpay checkCallbackOrder Payment amounts do not match, expected [$order->amount]", $fields);
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
			array('label' => '工商银行'     , 'value' => 'ICBC') ,
			array('label' => '农业银行'     , 'value' => 'ABC')  ,
			array('label' => '建设银行'     , 'value' => 'CCB')  ,
			array('label' => '浦发银行'     , 'value' => 'SPDB') ,
			array('label' => '兴业银行'     , 'value' => 'CIB')  ,
			array('label' => '民生银行'     , 'value' => 'CMBC') ,
			array('label' => '交通银行'     , 'value' => 'BCM')  ,
			array('label' => '中信银行'     , 'value' => 'CNCB') ,
			array('label' => '光大银行'     , 'value' => 'CEB')  ,
			array('label' => '北京银行'     , 'value' => 'BCCB') ,
			array('label' => '招商银行'     , 'value' => 'CMB')  ,
			array('label' => '广发银行'     , 'value' => 'GDB')  ,
			array('label' => '上海银行'     , 'value' => 'SHB')  ,
			array('label' => '中国银行'     , 'value' => 'BOC')  ,
			array('label' => '华夏银行'     , 'value' => 'HXB')  ,
			array('label' => '平安银行'     , 'value' => 'PAB')  ,
			array('label' => '中国邮政'     , 'value' => 'PSBS') ,
			// array('label' => '深圳发展银行' , 'value' => 'SDB')  ,
			array('label' => '农村信用社'   , 'value' => 'RCC')  ,
			array('label' => '徽商银行'     , 'value' => 'HSB')  ,
		);
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

	public function sign($data) {
		$dataStr = $this->genUrlParamsArray($data, false, false);
		$api_key = $this->getSystemInfo('key');
		$dig64 = base64_encode(hash_hmac('sha1', $dataStr, $api_key, true));
		$signature = urlencode($dig64);
		return $signature;
	}

	public function verify($recheck = false) {
		$postdata = file_get_contents("php://input");
		$api_key = $this->getSystemInfo('key');
		$headers = $this->CI->input->request_headers();

		$hmac = $headers['Content-Hmac'];
		$dig64 = base64_encode(hash_hmac('sha1', $postdata, $api_key, true));
		$this->CI->utils->debug_log("=======================dsdfpay verify signature, expected [$dig64], signature is [$hmac].");

		if ( $hmac == $dig64 ) {
			return true;
		} else {
			if($recheck){ #the broken sign would contain a space
				$this->CI->utils->debug_log("=======================dsdfpay verify signature rechecking, expected [$dig64], signature is [$hmac].");
				$sub = explode(" ", $hmac); #seperate the signature by space
				if(sizeof($sub) == 2){ #if the signature contains exactly a space
					if((strpos($dig64, $sub[0]) !== false) && (strpos($dig64, $sub[1]) !== false)) { #check if the rest parts can match
						return true;
					}
				}
				$this->writePaymentErrorLog("=======================dsdfpay verify signature recheck fail, expected [$dig64], signature is [$hmac].");
				return false;
			}
			return false;
		}
	}
}
