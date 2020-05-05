<?php
require_once dirname(__FILE__) . '/abstract_payment_api_cpaycard.php';

/**
 * CPAYCARD
 * http://ncompany.cpay.life
 *
 * * CPAYCARD_BANKCARD_PAYMENT_API, ID: 5352
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 * * Secret
 *
 * Field Values:
 * * URL: http://pbsgb0micy.51http.tech/api/v2_tran
 * * Account: ## Merchant ID ##
 * * Key: ## App Key ##
 * * Secret: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_cpaycard_bankcard extends Abstract_payment_api_cpaycard {

	public function getPlatformCode() {
		return CPAYCARD_BANKCARD_PAYMENT_API;
	}

	public function getPrefix() {
		return 'cpaycard_bankcard';
	}

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }
}