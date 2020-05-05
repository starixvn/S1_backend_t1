<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';
/**
 * GHEEPAY 贯贺支付
 * Ref. to Abstract_payment_api_bohaipay.php
 *
 * GHEEPAY_WEIXIN_PAYMENT_API, ID: 5473
 * GHEEPAY_ALIPAY_PAYMENT_API, ID: 5474
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: http://api.gheepay.cn/api.aspx
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2016 TripleOneTech
 */
abstract class Abstract_payment_api_gheepay extends Abstract_payment_api {

    const PAY_BANKCODE_ALIPAY = 'alipay'; //支付宝扫码支付 OK
    // const PAY_BANKCODE_ALIPAY_H5 = 'alipaywap'; //支付宝 WAP 支付 (H5)
    const PAY_BANKCODE_WEIXIN = 'weixin'; //微信扫码支付
    // const PAY_BANKCODE_WEIXIN_H5 = 'weixinwap'; //微信 WAP 支付 (H5)


	const RETURN_SUCCESS_CODE = 'opstate=0';
	const RETURN_FAIL_CODE = 'opstate=';
	const P_ERRORCODE_SUCCESS = '0';

	public function __construct($params = null) {

        parent::__construct($params);


        $ExtraInfoDefault = array();
        $ExtraInfoDefault['pay_bankcode_list'] = array();
        $ExtraInfoDefault['pay_bankcode_list']['PAY_BANKCODE_ALIPAY'] = self::PAY_BANKCODE_ALIPAY;
        $ExtraInfoDefault['pay_bankcode_list']['PAY_BANKCODE_WEIXIN'] = self::PAY_BANKCODE_WEIXIN;


        $this->pay_bankcode_list = $this->getSystemInfo("pay_bankcode_list", $ExtraInfoDefault['pay_bankcode_list']);
    }

	# Implement these for specific pay type
	protected abstract function configParams(&$params, $direct_pay_extra_info);
	protected abstract function processPaymentUrlForm($params);

	# -- override common API functions --
	## Constructs an URL so that the caller can redirect / invoke it to make payment through this API
	## See controllers/redirect.php for detail.
	##
	## Retuns a hash containing these fields:
	## array(
	##	'success' => true,
	##	'type' => self::REDIRECT_TYPE_FORM,  ## constants defined in abstract_payment_api.php
	##	'url' => $info['url'],
	##	'params' => $params,
	##	'post' => true
	## );
	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		if ($this->shouldRedirect($enabledSecondUrl)) {
			$url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderid);
			$result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
			return $result;
		}

		$order = $this->CI->sale_order->getSaleOrderById($orderId);

		$params = array();
		$params['pay_memberid'] = $this->getSystemInfo("account");
		$params['pay_orderid'] = $order->secure_id;
		$params['pay_amount'] = $this->convertAmountToCurrency($amount);
        $params['pay_applydate'] = date("YmdHis");
        $params["pay_version"] = 'vb1.0';
		$params['pay_notifyurl'] = $this->getNotifyUrl($orderId);
		$this->configParams($params, $order->direct_pay_extra_info);

		ksort($params);
        reset($params);

		$sign = $this->sign($params);
		$params['pay_md5sign'] = $sign;

		$this->CI->utils->debug_log("=====================GHEEPAY generatePaymentUrlForm", $params);

		return $this->processPaymentUrlForm($params);
	}

	protected function processPaymentUrlFormPost($params){
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_FORM,
			'url' => $this->getSystemInfo('url'),
			'params' => $params,
			'post' => false,
		);
	}

	## This will be called when the payment is async, API server calls our callback page
	## When that happens, we perform verifications and necessary database updates to mark the payment as successful
	## Reference: sample code, callback.php
	public function callbackFromServer($orderid, $params) {
		$response_result_id = parent::callbackFromServer($orderid, $params);
		return $this->callbackFrom('server', $orderid, $params, $response_result_id);
	}

	## This will be called when user redirects back to our page from payment API
	public function callbackFromBrowser($orderid, $params) {
		$response_result_id = parent::callbackFromBrowser($orderid, $params);
		return $this->callbackFrom('browser', $orderid, $params, $response_result_id);
	}

	# $source can be 'server' or 'browser'
	private function callbackFrom($source, $orderid, $params, $response_result_id) {
		$result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
		$order = $this->CI->sale_order->getSaleOrderById($orderid);
		$processed = false;

		$this->CI->utils->debug_log("=====================GHEEPAY callbackFrom $source params", $params);

		if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
			return $result;
		}

		# Update order payment status and balance
		$success = true;

		# Update player balance based on order status
		# if it's STATUS_SETTLED or STATUS_BROWSER_CALLBACK, put log, and ignore
		$orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderid);
		if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
			$this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
			if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
				$this->CI->sale_order->setStatusToSettled($orderid);
			}
		} else {
			# update player balance
			$this->CI->sale_order->updateExternalInfo($order->id, null, null, null, null, $response_result_id);
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
			$result['return_error'] = self::RETURN_FAIL_CODE .$params['opstate'] ;
		}

		if ($source == 'browser') {
			$result['next_url'] = $this->getPlayerBackUrl();
			$result['go_success_page'] = true;
		}

		return $result;
	}

	## Validates whether the callback from API contains valid info and matches with the order
	## Reference: code sample, callback.php
	private function checkCallbackOrder($order, $fields, &$processed = false) {
		$requiredFields = array(
			'orderid', 'opstate', 'ovalue'
		);
		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
				$this->writePaymentErrorLog("=====================GHEEPAY checkCallbackOrder missing parameter: [$f]", $fields);
				return false;
			}
		}

		# is signature authentic?
		if (!$this->validateSign($fields)) {
			$this->writePaymentErrorLog('=====================GHEEPAY checkCallbackOrder signature Error', $fields);
			return false;
		}

		$processed = true; # processed is set to true once the signature verification pass

		if ($fields['opstate'] != self::P_ERRORCODE_SUCCESS) {
			$this->writePaymentErrorLog('=====================GHEEPAY checkCallbackOrder payment was not successful', $fields);
			return false;
		}

		if (
			$this->convertAmountToCurrency($order->amount) !=
			$this->convertAmountToCurrency(floatval($fields['ovalue']))
		) {
			$this->writePaymentErrorLog("=====================GHEEPAY checkCallbackOrder payment amounts do not match, expected [$order->amount]", $fields);
			return false;
		}

		# everything checked ok
		return true;
	}

	public function directPay($order = null) {
		return array('success' => false); # direct pay not supported by this API
	}

	# -- Private functions --
	# After payment is complete, the gateway will invoke this URL asynchronously
	private function getNotifyUrl($orderid) {
		return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderid);
	}

	## After payment is complete, the gateway will send redirect back to this URL
	private function getReturnUrl($orderid) {
		return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderid);
	}

	## Format the amount value for the API
	protected function convertAmountToCurrency($amount) {
		return number_format($amount, 2, '.', '');
	}

    private function assemble($arr){
        $str = '';
        foreach($arr as $key=>$val){
            $str .= $key."=>".$val."&";
        }

        $str .= $this->getSystemInfo("key");
        return $str ;
    }

	private function sign($params) {
        $data = array(
			'pay_memberid'  => $params['pay_memberid'],
			'pay_bankcode'  => $params['pay_bankcode'],
			'pay_amount'    => $params['pay_amount'],
			'pay_orderid'   => $params['pay_orderid'],
			'pay_applydate' => $params['pay_applydate'],
			'pay_notifyurl' => $params['pay_notifyurl']
		);
		ksort($data);
        reset($data);
    	$str = $this->assemble($data);
        $sign = strtolower(md5($str));
		return $sign;
	}

	private function validateSign($params) {
		$keys = array('orderid', 'opstate', 'ovalue');

		$signStr = "";
        foreach($keys as $key) {
			if (array_key_exists($key, $params)) {
				$signStr .= $key .'='.$params[$key].'&';
			}
		}
		$signStr = rtrim($signStr,"&");
		$signStr .= $this->getSystemInfo('key');
		$sign = md5($signStr);
		return strcasecmp($sign, $params['sign']) === 0;
	}

	protected function getBankListInfoFallback() {
		return array(
			array('label' => '中信银行', 'value' => '962'),
			array('label' => '中国银行', 'value' => '963'),
			array('label' => '中国农业银行', 'value' => '964'),
			array('label' => '中国建设银行', 'value' => '965'),
			array('label' => '中国工商银行', 'value' => '967'),
			array('label' => '浙商银行', 'value' => '968'),
			array('label' => '浙江稠州商业银行', 'value' => '969'),
			array('label' => '招商银行', 'value' => '970'),
			array('label' => '邮政储蓄', 'value' => '971'),
			array('label' => '兴业银行', 'value' => '972'),
			array('label' => '顺德农村信用合作社', 'value' => '973'),
			array('label' => '深圳发展银行', 'value' => '974'),
			array('label' => '上海银行', 'value' => '975'),
			array('label' => '上海农村商业银行', 'value' => '976'),
			array('label' => '浦东发展银行', 'value' => '977'),
			array('label' => '平安银行', 'value' => '978'),
			array('label' => '南京银行', 'value' => '979'),
			array('label' => '民生银行', 'value' => '980'),
			array('label' => '交通银行', 'value' => '981'),
			array('label' => '华夏银行', 'value' => '982'),
			array('label' => '杭州银行', 'value' => '983'),
			array('label' => '广州市农村信用社|广州市商业银行', 'value' => '984'),
			array('label' => '广东发展银行', 'value' => '985'),
			array('label' => '光大银行', 'value' => '986'),
			array('label' => '东亚银行', 'value' => '987'),
			array('label' => '渤海银行', 'value' => '988'),
            array('label' => '北京银行', 'value' => '989'),
            array('label' => '北京农村商业银行', 'value' => '990')
		);
    }

    /**
	 * 取得金流商，銀行/渠道代號字串
	 * 通常在此物件名稱的最後一段字串
	 * @param string $className 物件的名稱
	 */
	public function getPayStrFromClassName($className = ''){
		if($className == ''){
			$className = get_class($this);
		}

		$classNameExplode = explode('_', $className);
		$classNameExplodeLength = count($classNameExplode);
		$PayStr = $classNameExplode[$classNameExplodeLength -1];
		return $PayStr;
	}
	/**
	 * 取得金流商代號字串
	 * 通常在此物件名稱的倒數第二段字串
	 * @param string $className 物件的名稱
	 */
	public function getBillingStrFromClassName($className = ''){
		if($className == ''){
			$className = get_class($this);
		}
		$classNameExplode = explode('_', $className);
		$classNameExplodeLength = count($classNameExplode);
		$BillingStr = $classNameExplode[$classNameExplodeLength -2];
		return $BillingStr;
	}
}
