<?php
require_once dirname(__FILE__) . '/abstract_payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_BANKCARD_PAYMENT_API, ID: 5334
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
class Payment_api_onepay_bankcard extends Abstract_payment_api_onepay {

    public function getPlatformCode() {
        return ONEPAY_BANKCARD_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay_bankcard';
    }

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $params = array();
        $params['version']         = '1.0';
        $params['inputCharset']    = 'UTF-8';
        $params['returnUrl']       = $this->getReturnUrl($orderId);
        $params['notifyUrl']       = $this->getNotifyUrl($orderId);
        $params['merchantId']      = $this->getSystemInfo('account');
        $params['merchantTradeId'] = $order->secure_id;
        $params['currency']        = $this->getSystemInfo('currency','CNY');
        $params['amountFee']       = $this->convertAmountToCurrency($amount);
        $params['goodsTitle']      = 'Topup';
        $params['issuingBank']     = 'UNIONPAY';
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['signType']        = 'RSA';
        $params['cardType']        = 'D';
        
        $params['sign']            = $this->sign($params);
        $params['paymentCard']     = $this->getPaymentCard($order->direct_pay_extra_info);
        $params['userName']        = $this->getUserName($order->direct_pay_extra_info);
        

        $this->CI->utils->debug_log('=====================onepay generatePaymentUrlForm params', $params);
        return $this->processPaymentUrlForm($params);
    }

    protected function getPaymentCard($direct_pay_extra_info){
        $decode_direct_pay_extra_info = json_decode($direct_pay_extra_info,true);
        $paymentcard = $decode_direct_pay_extra_info['get_payment_bank_num'];
        return $paymentcard;
    }

    protected function getUserName($direct_pay_extra_info){
        $decode_direct_pay_extra_info = json_decode($direct_pay_extra_info,true);
        $username = $decode_direct_pay_extra_info['get_card_name'];
        return $username;
    }
    
    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['payType'] = 'CARDBANK';
    }

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'get_payment_bank_num', 'type' => 'text', 'label_lang' => 'cashier.player.get_payment_bank_num', 'value' => '', 'hint' => $this->getSystemInfo('get_payment_bank_num_hint'), 'attr_maxlength' => $this->getSystemInfo('get_payment_bank_num_maxlength')),
            array('name' => 'get_card_name', 'type' => 'text', 'label_lang' => 'cashier.player.get_card_name', 'value' => '', 'hint' => $this->getSystemInfo('get_card_name_hint'), 'attr_maxlength' => $this->getSystemInfo('get_card_name_maxlength')),
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
