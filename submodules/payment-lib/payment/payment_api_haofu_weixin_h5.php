<?php
require_once dirname(__FILE__) . '/abstract_payment_api_hfpay.php';

/**
 * HAOFU 豪富
 * *
 * * HAOFU_WEIXIN_H5_PAYMENT_API, ID: 5445
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://mmszbjachb.6785151.com/payCenter/wxPay
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_haofu_weixin_h5 extends Abstract_payment_api_hfpay {

    public function getPlatformCode() {
        return HAOFU_WEIXIN_H5_PAYMENT_API;
    }

    public function getPrefix() {
        return 'haofu_weixin_h5';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['pay_type'] = self::PAY_TYPE_H5;
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
