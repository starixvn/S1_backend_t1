<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingsheng.php';
/**
 * WellPay
 *
 * *
 * * WELLPAY_PAYMENT_API: 5545
 *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.wellpays.com/rsa/deposit
 * * Extra Info:
 * > {
 * >    "yingsheng_priv_key": "## Private Key ##",
 * >    "yingsheng_pub_key": "## Public Key ##"
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_wellpay extends Abstract_payment_api_yingsheng { 

     public function getPlatformCode(){
          return WELLPAY_PAYMENT_API;
     }

     public function getPrefix(){
          return 'wellpay';
     }

     protected function configParams(&$params, $direct_pay_extra_info){
          $params['bank_code'] = self::BANKCODE_ONLINEBANK;
          $params['service_type'] = self::SERVICETYPE_ONLINEBANK;
     }

     # Hide bank selection drop-down
     public function getPlayerInputInfo(){
          return array(array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'));
     }

     protected function processPaymentUrlForm($params, $secure_id){
          return $this->processPaymentUrlFormPost($params, $secure_id);
     }
}
