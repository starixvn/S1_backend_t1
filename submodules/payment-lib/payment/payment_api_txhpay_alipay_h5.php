<?php
require_once dirname(__FILE__) . '/abstract_payment_api_txhpay.php';

/**
 * tianxiahui 天下汇
 * *
 * * TXHPAY_ALIPAY_H5_PAYMENT_API, ID: 5656
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://merchantgatewayapi.tianxiahui.biz/api/deposit
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2020 TripleOneTech
 */
class Payment_api_txhpay_alipay_h5 extends Abstract_payment_api_txhpay {

    public function getPlatformCode() {
        return TXHPAY_ALIPAY_H5_PAYMENT_API;
    }

    public function getPrefix() {
        return 'txhpay_alipay_h5';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['paymentMethod'] = self::PAYTMETHOD_ALIPAY;
        $params['paymentPlatform'] = self::PLATFORM_MOBILE;
    }

    # Hide bank selection drop-down
    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        return $this->processPaymentUrlFormURL($params);
    }
}
