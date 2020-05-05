<?php
require_once dirname(__FILE__) . '/abstract_payment_api_onepay.php';
/**
 * ONEPAY
 *
 * * ONEPAY_PAYMENT_API, ID: 976
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
class Payment_api_onepay extends Abstract_payment_api_onepay {

    public function getPlatformCode() {
        return ONEPAY_PAYMENT_API;
    }

    public function getPrefix() {
        return 'onepay';
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
        $params['sign']            = $this->sign($params);

        $this->CI->utils->debug_log('=====================onepay generatePaymentUrlForm params', $params);
        return $this->processPaymentUrlForm($params);
    }

    protected function configParams(&$params, $direct_pay_extra_info) {
        $params['payType'] = 'EC';

        if (!empty($direct_pay_extra_info)) {
            $extraInfo = json_decode($direct_pay_extra_info, true);
            if (!empty($extraInfo)) {
                $params['subIssuingBank'] = array_key_exists('bank', $extraInfo) ? $extraInfo['bank'] : $extraInfo['bank_type'];
            }
        }
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
