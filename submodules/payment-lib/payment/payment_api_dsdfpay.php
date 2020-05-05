<?php
require_once dirname(__FILE__) . '/abstract_payment_api_dsdfpay.php';

/**
 * DSDFPAY 代收代付
 * https://www.dsdfpay.com/html/admin/login.html
 *
 * DSDFPAY_PAYMENT_API, ID: 239
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
class Payment_api_dsdfpay extends Abstract_payment_api_dsdfpay {

	public function getPlatformCode() {
		return DSDFPAY_PAYMENT_API;
	}

	public function getPrefix() {
		return 'dsdfpay';
	}

	public function getBankType($direct_pay_extra_info) {
		return "online";

	}

	public function getTypeFlag($direct_pay_extra_info) {
		$this->utils->debug_log('direct_pay_extra_info', $direct_pay_extra_info);
		if (!empty($direct_pay_extra_info)) {
			$extraInfo = json_decode($direct_pay_extra_info, true);
			if (!empty($extraInfo)) {
				$bank = array_key_exists('bank', $extraInfo) ? $extraInfo['bank'] : $extraInfo['bank_type'];
			}
		}
		return $bank;
	}

	

}
