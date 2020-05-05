<?php
require_once dirname(__FILE__) . '/abstract_payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_UNIONPAY_PAYMENT_API, ID: 5008
 *
 * Required Fields:
 * * Account
 * * URL
 * * Extra Info
 *
 * Field Values:
 * * Account: ## Merchant ID ##
 * * URL: https://api.onepay.solutions/payment/otoSoft/v3/getQrCode.html
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
class Payment_api_onepay_unionpay extends Abstract_payment_api_onepay {

    public function getPlatformCode() {
        return ONEPAY_UNIONPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay_unionpay';
    }

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $params = array();
        $params['app_id']     = $this->getSystemInfo('account');
        $params['currency']   = $this->getSystemInfo('currency','CNY');
        $params['amount']     = $this->convertAmountToCurrency($amount);
        $params['order_no']   = $order->secure_id;
        $this->configParams($params, $order->direct_pay_extra_info);

        $this->CI->utils->debug_log('=====================onepay generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['payment_channel'] = self::CHANNEL_UNIONPAY;
    }

    public function getPlayerInputInfo() {
        return array(
            array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
        );
    }

    protected function processPaymentUrlForm($params) {
        if($this->CI->utils->is_mobile() && $this->getSystemInfo('use_app', true)) {
            return $this->processPaymentUrlFormPost($params);
        }else{
            return $this->processPaymentUrlFormQRCode($params);
        }
    }
}
