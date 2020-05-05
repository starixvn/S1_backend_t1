<?php
require_once dirname(__FILE__) . '/abstract_payment_api_cpaycard.php';

/**
 * CPAYCARD
 * http://ncompany.cpay.life
 *
 * * CPAYCARD_ALIPAY_BANKCARD_PAYMENT_API, ID: 5353
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 * * Secret
 *
 * Field Values:
 * * URL: http://pbsgb0micy.51http.tech/
 * * Account: ## Merchant ID ##
 * * Key: ## App Key ##
 * * Secret: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_cpaycard_alipay_bankcard extends Abstract_payment_api_cpaycard {

	public function getPlatformCode() {
		return CPAYCARD_ALIPAY_BANKCARD_PAYMENT_API;
	}

	public function getPrefix() {
		return 'cpaycard_alipay_bankcard';
	}

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }
}