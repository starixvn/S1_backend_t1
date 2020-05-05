<?php
require_once dirname(__FILE__) . '/payment_api_dsdfpay_alipay.php';

/**
 * DSDFPAY 代收代付
 * https://www.dsdfpay.com/html/admin/login.html
 *
 * DSDFPAY_ALIPAY_2_PAYMENT_API, ID: 5554
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
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_dsdfpay_alipay_2 extends Payment_api_dsdfpay_alipay {

	public function getPlatformCode() {
		return DSDFPAY_ALIPAY_2_PAYMENT_API;
	}

	public function getPrefix() {
		return 'dsdfpay_alipay_2';
	}
}
