<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingsheng.php';
/**
 * YINGSHENG 盈盛
 *
 * * YINGSHENG_WEIXIN_PAYMENT_API, ID:5709
 * *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * Key: ## Terminal ID##
 * * URL: https://api.yoopayment.com/rsa/deposit
 * * Extra Info:
 * > {
 * >    "yingsheng_priv_key": "## Private Key ##",
 * >    "yingsheng_pub_key": "## Public Key ##"
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
class Payment_api_yingsheng_weixin extends Abstract_payment_api_yingsheng {

    public function getPlatformCode() {
        return YINGSHENG_WEIXIN_PAYMENT_API;
    }

    public function getPrefix() {
        return 'yingsheng_weixin';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['bank_code'] = '';
        $params['service_type'] = $this->getSystemInfo('servicetype',self::SERVICETYPE_WEIXIN);
    }

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params, $secure_id) {
        return $this->processPaymentUrlFormPost($params, $secure_id);
    }
}
