<?php
require_once dirname(__FILE__) . '/abstract_payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_ALIPAY_H5_PAYMENT_API, ID: 978
 *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.onepay.solutions/payment/otoSoft/v3/getQrCode.html
 * * Extra Info:
 * > {
 * >    "onepay_priv_key": "## Private Key ##",
 * >    "onepay_pub_key": "## Public Key ##"
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_onepay_alipay_h5 extends Abstract_payment_api_onepay {

    public function getPlatformCode() {
        return ONEPAY_ALIPAY_H5_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay_alipay_h5';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['payment_channel'] = self::CHANNEL_ALIPAY;
    }

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormRedirect($params);
    }
}
