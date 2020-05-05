<?php
require_once dirname(__FILE__) . '/abstract_payment_api_kzpay.php';

/**
 *
 * * KZPAY_ALIPAY_PAYMENT_API', ID: 5611
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key

 * Field Values:
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
class Payment_api_kzpay_alipay extends Abstract_payment_api_kzpay {

    public function getPlatformCode() {
        return KZPAY_ALIPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'kzpay_alipay';
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
      
        $params['payWay'] = self::PAYWAY_ALIPAY;
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
