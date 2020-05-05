<?php
require_once dirname(__FILE__) . '/payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_QUICKPAY_PAYMENT_API, ID: 981
 *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.onepay.solutions/payment/v3/checkOut.html
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
class Payment_api_onepay_quickpay extends Payment_api_onepay {

    public function getPlatformCode() {
        return ONEPAY_QUICKPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay_quickpay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['payType']         = 'NC';
        $params['cardType']        = 'D';

        if($this->CI->utils->is_mobile()) {
            $params['deviceType'] = 'H5';
        }else{
            $params['deviceType'] = 'WEB';
        }
    }

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return array(
            'success' => true,
            'type' => self::REDIRECT_TYPE_FORM,
            'url' => $this->getSystemInfo('url'),
            'params' => $params,
            'post' => true,
        );
    }
}
