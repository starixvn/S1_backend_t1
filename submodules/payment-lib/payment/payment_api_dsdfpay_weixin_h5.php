<?php
require_once dirname(__FILE__) . '/payment_api_dsdfpay_weixin.php';

/**
 * DSDFPAY 代收代付
 * https://www.dsdfpay.com/html/admin/login.html
 *
 * DSDFPAY_WEIXIN_H5_PAYMENT_API, ID: 836
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
class Payment_api_dsdfpay_weixin_h5 extends Payment_api_dsdfpay_weixin {

	public function getPlatformCode() {
		return DSDFPAY_WEIXIN_H5_PAYMENT_API;
	}

	public function getPrefix() {
		return 'dsdfpay_weixin_h5';
	}
}