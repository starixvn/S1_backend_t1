<?php
require_once dirname(__FILE__) . '/abstract_payment_api_ss.php';

/**
 *
 * * SS_BANKCARD_PAYMENT_API, ID: 5710
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
class Payment_api_ss_bankcard extends Abstract_payment_api_ss {

	public function getPlatformCode() {
		return SS_BANKCARD_PAYMENT_API;
	}

	public function getPrefix() {
		return 'ss_bankcard';
	}

	public function getBankType($direct_pay_extra_info) {
		if (!empty($direct_pay_extra_info)) {
			$extraInfo = json_decode($direct_pay_extra_info, true);
			if (!empty($extraInfo)) {
				return array_key_exists('bank', $extraInfo) ? $extraInfo['bank'] : $extraInfo['bank_type'];
			}
		} else {
			return parent::getBankType($direct_pay_extra_info);
		}
	}

	protected function configParams(&$params, $direct_pay_extra_info) {
		$this->utils->debug_log('direct_pay_extra_info', $direct_pay_extra_info);
		if (!empty($direct_pay_extra_info)) {
            $extraInfo = json_decode($direct_pay_extra_info, true);
            if (!empty($extraInfo)) {
                $bank = array_key_exists('bank', $extraInfo) ? $extraInfo['bank'] : $extraInfo['bank_type'];
            }
        }
		$params['AppliedBankID'] = $bank;
		$params['NoteModelID'] = '2';
		$params['TerminalID'] = '1';
	}

	protected function processPaymentUrlForm($params) {
        return $this->handlePaymentFormResponse($params);
    }
}
