<?php
require_once dirname(__FILE__) . '/abstract_payment_api_jbp_v2.php';

/**
 * JBP 聚宝盆
 *
 * * JBP_V2_WEIXIN_H5_PAYMENT_API, ID: 5550
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.jbp-pay.com/apply/Deposit
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_jbp_v2_weixin_h5 extends Abstract_payment_api_jbp_v2 {

	public function getPlatformCode() {
		return JBP_V2_WEIXIN_H5_PAYMENT_API;
	}

	public function getPrefix() {
		return 'jbp_v2_weixin_h5';
	}

	protected function configParams(&$params, $direct_pay_extra_info) {
		$params['bank_id'] = self::BANKID_WEIXIN;
		$params['terminal'] = self::TERMINAL_MOBILE;
	}

	# Hide bank selection drop-down
	public function getPlayerInputInfo() {
		return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
		);
	}

	protected function processPaymentUrlForm($params) {
		return $this->processPaymentUrlFormRedirect($params);
	}

}
