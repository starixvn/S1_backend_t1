<?php
require_once dirname(__FILE__) . '/abstract_payment_api_wenpay.php';

/**
 * WENPAY 稳付
 * *
 * * WENPAY_ALIPAY_GATEWAY_PAYMENT_API, ID: 5658
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.wenpay8.com/PaymentGetway/OrderRquest
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_wenpay_alipay_gateway extends Abstract_payment_api_wenpay {

    public function getPlatformCode() {
        return WENPAY_ALIPAY_GATEWAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'wenpay_alipay_gateway';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['channelType'] = self::CHANNEL_TYPE_ALIPAY_GATEWAY;
    }

    # Hide bank selection drop-down
    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormPost($params);
    }
}
