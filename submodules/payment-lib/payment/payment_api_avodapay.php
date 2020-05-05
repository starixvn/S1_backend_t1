<?php
require_once dirname(__FILE__) . '/abstract_payment_api_avodapay.php';
/**
 * AVODAPAY
 *
 * * AVODAPAY_PAYMENT_API, ID: 5716
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://secure.avodapay.com/api/v1/one-page/generate-url/
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_avodapay extends Abstract_payment_api_avodapay {

    public function getPlatformCode() {
        return AVODAPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'avodapay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $this->utils->debug_log('direct_pay_extra_info', $direct_pay_extra_info);
        if (!empty($direct_pay_extra_info)) {
            $extraInfo = json_decode($direct_pay_extra_info, true);
            if (!empty($extraInfo)) {
                $bank = array_key_exists('bank', $extraInfo) ? $extraInfo['bank'] : $extraInfo['bank_type'];
            }
        }

        $params['bankCode'] = $bank;

    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormPost($params);
    }
}
