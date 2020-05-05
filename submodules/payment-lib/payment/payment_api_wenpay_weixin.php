<?php
require_once dirname(__FILE__) . '/abstract_payment_api_wenpay.php';

/**
 * WENPAY 稳付
 * *
 * * WENPAY_WEIXIN_PAYMENT_API, ID: 985
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
class Payment_api_wenpay_weixin extends Abstract_payment_api_wenpay {

    public function getPlatformCode() {
        return WENPAY_WEIXIN_PAYMENT_API;
    }

    public function getPrefix() {
        return 'wenpay_weixin';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        if($this->CI->utils->is_mobile()) {
            $params['channelType'] = self::CHANNEL_TYPE_WEIXIN_H5;
        }
        else {
            $params['channelType'] = self::CHANNEL_TYPE_WEIXIN;
        }
    }

    # Hide bank selection drop-down
    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        if($this->getSystemInfo('useurlform') == true || $this->CI->utils->is_mobile()) {
            return $this->processPaymentUrlFormPost($params);
        }
        else {
            return $this->processPaymentUrlFormQRCode($params);
        }
    }
}
