<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';
/**
 * ONEPAY
 *
 * * ONEPAY_PAYMENT_API, ID: 976
 * * ONEPAY_ALIPAY_PAYMENT_API, ID: 977
 * * ONEPAY_ALIPAY_H5_PAYMENT_API, ID: 978
 * * ONEPAY_WEIXIN_PAYMENT_API, ID: 979
 * * ONEPAY_WEIXIN_H5_PAYMENT_API, ID: 980
 * * ONEPAY_QUICKPAY_PAYMENT_API, ID: 981
 * * ONEPAY_UNIONPAY_PAYMENT_API, ID: 5008
 * * ONEPAY_BANKCARD_PAYMENT_API, ID: 5334
 * * FPGPAY_PAYMENT_API, ID: 5418
 * * FPGPAY_ALIPAY_PAYMENT_API, ID: 5419
 * * FPGPAY_ALIPAY_H5_PAYMENT_API, ID: 5420
 * * FPGPAY_QUICKPAY_PAYMENT_API, ID: 5421
 * * FPGPAY_WEIXIN_PAYMENT_API, ID: 5422
 * * FPGPAY_WEIXIN_H5_PAYMENT_API, ID: 5423
 * *
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
abstract class Abstract_payment_api_onepay extends Abstract_payment_api {

    const CHANNEL_ALIPAY    = 'ALIPAY';
    const CHANNEL_WEIXIN    = 'WECHAT';
    const CHANNEL_UNIONPAY  = 'UNIONPAY';
    const CHANNEL_QUICKPAY  = 'quickpay';

    const RESULT_CODE_SUCCESS = "SUCCESS";
    const CALLBACK_SUCCESS = 'PS_PAYMENT_SUCCESS';
    const RETURN_SUCCESS_CODE = 'SUCCESS';


    public function __construct($params = null) {
        parent::__construct($params);
    }

    # Implement these to specify pay type
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params);

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'onepay_pub_key', 'onepay_priv_key');
        return $secretsInfo;
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
        $params['order_no']   = $order->secure_id;
        $params['body']       = 'Topup';
        $params['return_url'] = $this->getReturnUrl($orderId);
        $params['currency']   = $this->getSystemInfo('currency','CNY');
        $params['amount']     = $this->convertAmountToCurrency($amount);
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['signType']   = 'RSA';
        $params['sign']       = $this->sign($params);

        $this->CI->utils->debug_log('=====================onepay generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    # Implement processPaymentUrlForm
    protected function processPaymentUrlFormRedirect($params) {
        return array(
            'success' => true,
            'type' => self::REDIRECT_TYPE_FORM,
            'url' => $this->getSystemInfo('url'),
            'params' => $params,
            'post' => true,
        );
    }

    protected function processPaymentUrlFormPost($params) {
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['order_no']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================onepay processPaymentUrlFormPost response', $response);

        if($response['flag'] == self::RESULT_CODE_SUCCESS && isset($response['data']['qrUrl'])) {
            if($this->validateSign($response['data'])){
                if(isset($response['data']['message'])){
                    if(!empty($response['data']['message'])) {
                        return array(
                            'success' => false,
                            'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                            'message' => 'Response result: ['.$response['data']['message'].']'
                        );
                    }
                }
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $response['data']['qrUrl'],
                );
            }
            else{
                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                    'message' => lang('Validate Sign Error')
                );
            }
        }
        else if(isset($response['errorCode'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => 'Response result: ['.$response['errorCode'].']'.$response['errorMsg']
            );
        }
        else {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => lang('Invalidate API response')
            );
        }
    }

    protected function processPaymentUrlFormQRCode($params) {
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['order_no']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================onepay processPaymentUrlFormQRCode response', $response);

        if($response['flag'] == self::RESULT_CODE_SUCCESS && isset($response['data']['qrUrl'])) {
            if($this->validateSign($response['data'])){
                if(isset($response['data']['message'])){
                    if(!empty($response['data']['message'])) {
                        return array(
                            'success' => false,
                            'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                            'message' => 'Response result: ['.$response['data']['message'].']'
                        );
                    }
                }
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_QRCODE,
                    'url' => $response['data']['qrUrl'],
                );
            }
            else{
                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                    'message' => lang('Validate Sign Error')
                );
            }
        }
        else if(isset($response['errorCode'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => 'Response result: ['.$response['errorCode'].']'.$response['errorMsg']
            );
        }
        else {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => lang('Invalidate API response')
            );
        }
    }

    # Callback URI: /callback/fixed_process/<payment_id>
    public function getOrderIdFromParameters($flds) {
        if (isset($flds['order_no'])) {
            $this->CI->load->model(array('sale_order'));
            $order = $this->CI->sale_order->getSaleOrderBySecureId($flds['order_no']);
            return $order->id;
        }
        else {
            $this->utils->debug_log('=====================onepay callbackOrder cannot get any order_id when getOrderIdFromParameters', $flds);
            return;
        }
    }

    public function callbackFromServer($orderId, $params) {
        $response_result_id = parent::callbackFromServer($orderId, $params);
        return $this->callbackFrom('server', $orderId, $params, $response_result_id);
    }

    public function callbackFromBrowser($orderId, $params) {
        $response_result_id = parent::callbackFromBrowser($orderId, $params);
        return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
    }

    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        $this->CI->utils->debug_log("=====================onepay callbackFrom $source params", $params);

        if($source == 'server'){
            if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
                return $result;
            }
        }

        # Update order payment status and balance
        $success = true;

        # Update player balance based on order status
        # if it's STATUS_SETTLED or STATUS_BROWSER_CALLBACK, put log, and ignore
        $orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderId);
        if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
            $this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
            if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
                $this->CI->sale_order->setStatusToSettled($orderId);
            }
        } else {
            # update player balance
            if(isset($params['payment_id'])){
                $this->CI->sale_order->updateExternalInfo($order->id, $params['payment_id'], '', null, null, $response_result_id);
            }
            else if(isset($params['pwTradeId'])){
                $this->CI->sale_order->updateExternalInfo($order->id, $params['pwTradeId'], '', null, null, $response_result_id);
            }


            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
            }
        }

        $result['success'] = $success;
        if ($processed) {
            $result['message'] = self::RETURN_SUCCESS_CODE;
        } else {
            $result['return_error'] = 'Error';
        }

        if ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return $result;
    }

    private function checkCallbackOrder($order, $fields, &$processed = false) {
        if(isset($fields['payment_id'])){
            $requiredFields = array(
                'order_no', 'amount', 'status', 'sign'
            );
            $status = $fields['status'];
            $amount = $fields['amount'];
            $secure_id = $fields['order_no'];
        }
        else if(isset($fields['pwTradeId'])){
            $requiredFields = array(
                'merchantTradeId', 'amountFee', 'tradeStatus', 'sign'
            );
            $status = $fields['tradeStatus'];
            $amount = $fields['amountFee'];
            $secure_id = $fields['merchantTradeId'];
        }
        else{
            $this->writePaymentErrorLog("=====================onepay checkCallbackOrder field not found.", $fields);
            return false;
        }


        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================onepay checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================onepay checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        if ($status != self::CALLBACK_SUCCESS) {
            $this->writePaymentErrorLog("======================onepay checkCallbackOrder Payment status is not success", $fields);
            return false;
        }

        if ($amount != $this->convertAmountToCurrency($order->amount)) {
            $this->writePaymentErrorLog("======================onepay checkCallbackOrder Payment amount is wrong, expected [$order->amount]", $fields);
            return false;
        }

        if ($secure_id != $order->secure_id) {
            $this->writePaymentErrorLog("======================onepay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }


    # -- signing --
    protected function sign($params) {
        $signStr = $this->createSignStr($params);
        openssl_sign(urldecode($signStr), $sign_info, $this->getPrivKey(), OPENSSL_ALGO_SHA1);
        $sign = bin2hex(base64_encode($sign_info));
        return $sign;
    }

    protected function validateSign($params) {
        $signStr = $this->createSignStr($params);
        $sign = base64_decode(hex2bin($params['sign']));
        $valid = openssl_verify($signStr, $sign, $this->getPubKey());
        return $valid;
    }

    protected function createSignStr($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if($value == null || $value == "null" || $key == 'signType' || $key == 'sign' || $key == 'totalFactorage' || $key == 'detailList') {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        return rtrim($signStr, '&');
    }

    protected function getPubKey() {
        $onepay_pub_key = $this->getSystemInfo('onepay_pub_key');
        $pub_key = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . chunk_split($onepay_pub_key, 64, PHP_EOL) . '-----END PUBLIC KEY-----' . PHP_EOL;
        return openssl_get_publickey($pub_key);
    }

    protected function getPrivKey() {
        $onepay_priv_key = $this->getSystemInfo('onepay_priv_key');
        $priv_key = '-----BEGIN RSA PRIVATE KEY-----' . PHP_EOL . chunk_split($onepay_priv_key, 64, PHP_EOL) . '-----END RSA PRIVATE KEY-----' . PHP_EOL;
        return openssl_get_privatekey($priv_key);
    }


    # -- Private functions --
    protected function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    protected function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 2, '.', '');
    }
}