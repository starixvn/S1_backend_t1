<?php
require_once dirname(__FILE__) . '/abstract_payment_api_wanweipay.php';

/**
 *
 * * WANWEIPAY_QUICKPAY_PAYMENT_API, ID: 5663
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.247pay.site/api/v1/payin/pay_info
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
class Payment_api_wanweipay_quickpay extends Abstract_payment_api_wanweipay {

	public function getPlatformCode() {
		return WANWEIPAY_QUICKPAY_PAYMENT_API;
	}

	public function getPrefix() {
		return 'wanweipay_quickpay';
	}

	protected function configParams(&$params, $direct_pay_extra_info) {
		$params['channelId'] = self::CHANNELID_QUICKPAY;
		unset($params['depositName']);
	}

	protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormPost($params);
    }

    public function getPlayerInputInfo() {
		return array(
			array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
		);
	}
}
