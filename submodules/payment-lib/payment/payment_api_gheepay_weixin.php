<?php
require_once dirname(__FILE__) . '/abstract_payment_api_gheepay.php';
/**
 * 贯贺支付 - 微信扫码支付
 * Ref. to Payment_api_bohaipay_alipay
 *
 * GHEEPAY_WEIXIN_PAYMENT_API, ID: 5473
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values: (http://admin.og.local/payment_api/viewPaymentApi/)
 * * URL: http://api.gheepay.cn/api.aspx
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
class Payment_api_gheepay_weixin extends Abstract_payment_api_gheepay {

	/**
	 * Reference by submodules/core-lib/application/config/apis.php
	 */
	public function getPlatformCode() {
		// return TEMPLATE_ALIPAY_PAYMENT_API; // TEST
		return GHEEPAY_WEIXIN_PAYMENT_API;
	}

	public function getPrefix() {
		$preFix = array();
		$preFix[] = $this->getPayStrFromClassName( get_class($this) );
		$preFix[] = $this->getBillingStrFromClassName( get_class($this) );
		$preFixStr = implode('_', $preFix);
$this->CI->utils->debug_log("=====================GHEEPAY getPrefix", $preFixStr);
		return $preFixStr; // 'gheepay_weixin'; // @todo 待驗證
	}

	protected function configParams(&$params, $direct_pay_extra_info) {
		// $params['pay_bankcode'] = self::PAY_BANKCODE_WEIXIN;

		// frome getSystemInfo().
		$params['pay_bankcode'] = $this->pay_bankcode_list["PAY_BANKCODE_". strtoupper($this->getPayStrFromClassName( get_class($this) )) ];

		if($params['pay_bankcode'] == ''){ // default, should be self::PAY_BANKCODE_WEIXIN.
			$params['pay_bankcode'] = self::PAY_BANKCODE_WEIXIN;
		}
	}

	# Hide bank selection drop-down
	public function getPlayerInputInfo() {
		return array(
			array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
		);
	}

	protected function processPaymentUrlForm($params) {
		return $this->processPaymentUrlFormPost($params);
	}

}
