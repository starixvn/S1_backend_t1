<?php
require_once dirname(__FILE__) . '/abstract_payment_api_hfpay.php';

/**
 * HAOFU 豪富
 * *
 * * HAOFU_QUICKPAY_PAYMENT_API, ID: 5449
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://mmszbjachb.6785151.com/payCenter/unionqrpay
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_haofu_quickpay extends Abstract_payment_api_hfpay {

    public function getPlatformCode() {
        return HAOFU_QUICKPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'haofu_quickpay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
    }

    # Hide bank selection drop-down
    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlRedirectFormPost($params);
    }
}
