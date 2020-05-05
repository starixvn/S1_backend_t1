<?php
require_once dirname(__FILE__) . '/abstract_payment_api_dsdfpay.php';

/**
 * DSDFPAY 代收代付
 * https://www.dsdfpay.com/html/admin/login.html
 *
 * DSDFPAY_QUICKPAY_H5_PAYMENT_API, ID: 782
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
class Payment_api_dsdfpay_quickpay_h5 extends Abstract_payment_api_dsdfpay {

	public function getPlatformCode() {
		return DSDFPAY_QUICKPAY_H5_PAYMENT_API;
	}

	public function getPrefix() {
		return 'dsdfpay_quickpay_h5';
	}

	public function getBankType($direct_pay_extra_info) {
		return $this->getSystemInfo("type", 'quickp2p');
	}

	public function getTypeFlag($direct_pay_extra_info) {
		return "";
	}

	/* hide bar */
	public function getPlayerInputInfo() {
	    return array(
	        array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
	    );
	}

}