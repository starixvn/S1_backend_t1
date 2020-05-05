<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingsheng.php';
/**
 * YINGSHENG 盈盛
 *
 * * YINGSHENG_ALIPAY_PAYMENT_API, ID: 5150
 * *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * Key: ## Terminal ID##
 * * URL: https://api.yspay365.com/rsa/deposit
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
class Payment_api_yingsheng_alipay extends Abstract_payment_api_yingsheng {

    public function getPlatformCode() {
        return YINGSHENG_ALIPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'yingsheng_alipay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['bank_code'] = '';
        $params['service_type'] = ($this->CI->utils->is_mobile()) ? $this->getSystemInfo('servicetype',self::SERVICETYPE_ALIPAY_H5) : $this->getSystemInfo('servicetype',self::SERVICETYPE_QUICKPAY);
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
