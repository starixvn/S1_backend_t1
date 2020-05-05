<?php
require_once dirname(__FILE__) . '/abstract_payment_api_ss.php';

/**
 *
 * * SS_ALIPAY_BANKCARD_PAYMENT_API, ID: 5711
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: http://43.242.33.147:81/SS/api/apply/deposit
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
class Payment_api_ss_alipay_bankcard extends Abstract_payment_api_ss {

	public function getPlatformCode() {
		return SS_ALIPAY_BANKCARD_PAYMENT_API;
	}

	public function getPrefix() {
		return 'ss_alipay_bankcard';
	}

	protected function configParams(&$params, $direct_pay_extra_info) {
		$params['AppliedBankID'] = self::ALIPAY_BANKID;
		$params['NoteModelID'] = '3';
		$params['TerminalID'] = '1';
	}

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

	protected function processPaymentUrlForm($params) {
        return $this->handlePaymentFormResponse($params);
    }

}
