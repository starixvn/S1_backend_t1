<?php
require_once dirname(__FILE__) . '/abstract_payment_api_yingsheng.php';
/**
 * JEEPAYMENT
 *
 * *
 * * JEEPAYMENT_BANKCARD_PAYMENT_API: 5679
 *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.yoopayment.com/rsa/deposit
 * * Extra Info:
 * > {
 * >    "jeepayment_priv_key": "## Private Key ##",
 * >    "jeepayment_pub_key": "## Public Key ##"
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_jeepayment_bankcard extends Abstract_payment_api_yingsheng
{
     public function getPlatformCode()
     {
          return JEEPAYMENT_BANKCARD_PAYMENT_API;
     }

     public function getPrefix()
     {
          return 'jeepayment_bankcard';
     }

     protected function configParams(&$params, $direct_pay_extra_info)
     {
          $params['bank_code'] = '';
          $params['service_type'] = $this->getSystemInfo('servicetype', self::SERVICETYPE_PGMT);
     }

     # Hide bank selection drop-down
     public function getPlayerInputInfo()
     {
          return array(array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'));
     }

     protected function processPaymentUrlForm($params, $secure_id)
     {
          return $this->processPaymentUrlFormPost($params, $secure_id);
     }
}
