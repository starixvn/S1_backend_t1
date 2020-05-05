<?php
require_once dirname(__FILE__) . '/abstract_payment_api_wenpay.php';

/**
 * WENPAY 稳付
 * *
 * * WENPAY_UNIONPAY_PAYMENT_API, ID: 983
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://www.zhizeng-pay.net/mas/mobile/create.do
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_wenpay_unionpay extends Abstract_payment_api_wenpay {

    public function getPlatformCode() {
        return WENPAY_UNIONPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'wenpay_unionpay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['channelType'] = self::CHANNEL_TYPE_UNIONPAY;
    }

    # Hide bank selection drop-down
    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormQRCode($params);
    }

    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 0, '.', '');
    }
}
