<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';
/**
 * YINGSHENG 盈盛
 *
 * * YINGSHENG_PAYMENT_API, ID: 5149
 * * YINGSHENG_ALIPAY_PAYMENT_API, ID: 5150
 * * YINGSHENG_ALIPAY_H5_PAYMENT_API, ID: 5151
 * * YINGSHENG_QUICKPAY_PAYMENT_API, ID: 5152
 * * WELLPAY_UNIONPAY_PAYMENT_API: 5478
 * * WELLPAY_PAYMENT_API: 5545
 * * JEEPAYMENT_BANKCARD_PAYMENT_API: 5679
 * * YINGSHENG_ALIPAY_2_PAYMENT_API, ID: 5706
 * * YINGSHENG_ALIPAY_3_PAYMENT_API, ID: 5707
 * * YINGSHENG_ALIPAY_BANKCARD_PAYMENT_API, ID: 5708
 * * YINGSHENG_WEIXIN_PAYMENT_API, ID: 5709
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
abstract class Abstract_payment_api_yingsheng extends Abstract_payment_api {

    const SERVICETYPE_ONLINEBANK  = "1";
    const SERVICETYPE_ALIPAY      = "3";
    const SERVICETYPE_ALIPAY_H5   = "9";
    const SERVICETYPE_WEIXIN      = "2";
    const SERVICETYPE_WEIXIN_H5   = "8";
    const SERVICETYPE_QQPAY       = "4";
    const SERVICETYPE_QQPAY_H5    = "10";
    const SERVICETYPE_JDPAY       = "5";
    const SERVICETYPE_JDPAY_H5    = "12";
    const SERVICETYPE_UNIONPAY    = "11";
    const SERVICETYPE_QUICKPAY    = "7";
    const SERVICETYPE_QUICKPAY_H5 = "13";
    const SERVICETYPE_PGMT        = "22"; //网关转账
    const SERVICETYPE_ALIPAY_BANKCARD   = "17";

    const BANKCODE_ONLINEBANK = "0105";

    const RESULT_CODE_SUCCESS = 1;
    const RETURN_SUCCESS_CODE = '{"error_msg": "", "status": "1"}';
    const ERROR_MSG = array(
        'E00001' => 'merchant not found (商户不存在)',
        'E00002' => 'missing required parameter (缺少参数)',
        'E00003' => 'sign error (签名档错误)',
        'E00004' => 'invalid request amount (请求金额无效)',
        'E00005' => 'merchant_order_no already exists (订单号已存在)',
        'E00006' => 'insufficient merchant balance (商户馀额不足)',
        'E00007' => 'invalid bank code (错误的银行代码)',
        'E00008' => 'maintenance mode (系统维护中)',
        'E00009' => 'service type is not allowed for the merchant (商户不允许使用此服务类型)',
        'E00010' => 'unknown service error (未知的服务器错误)',
        'E00012' => 'ip is not allowed (IP 是不允许拜访，需要加白名单)',
        'E00013' => 'invalid parameter format (无效的参数格式)',
        'E00014' => 'no channel is available for this merchant (此商户没有可用的渠道，商户帐号设置错误)',
        'E00015' => 'order number does not exist (订单号不存在)',
        'E00016' => 'merchant is over the deposit quota limit (商户充值额度已达上限)',
        'E00017' => 'get balance API to be used every 5 seconds only (帐户馀额请求请勿在5秒内重复尝试)',
    );


    public function __construct($params = null) {
        parent::__construct($params);
        $this->_custom_curl_header = array('application/x-www-form-urlencoded');
    }

    # Implement these to specify pay type
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params, $secure_id);

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'yingsheng_pub_key', 'yingsheng_priv_key');
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
        $params['amount']            = $this->convertAmountToCurrency($amount);
        $params['platform']          = 'PC';
        $params['note']              = 'Topup';
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['merchant_user']     = $playerId;
        $params['merchant_order_no'] = $order->secure_id;
        $params['risk_level']        = '1';
        $params['callback_url']      = $this->getNotifyUrl($orderId);
        $sign = $this->sign($params);

        $secure_id = $order->secure_id;
        $submit['merchant_code']     = $this->getSystemInfo('account');
        $submit['data']              = $sign['encParam'];
        $submit['sign']              = $sign['sign'];
        $this->CI->utils->debug_log('=====================yingsheng generatePaymentUrlForm params', $params);
        $this->CI->utils->debug_log('=====================yingsheng generatePaymentUrlForm submit', $submit);
        return $this->processPaymentUrlForm($submit, $secure_id);
    }

    # Implement processPaymentUrlForm
    protected function processPaymentUrlFormPost($params, $secure_id) {
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $secure_id);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================yingsheng processPaymentUrlFormPost response', $response);
        if($response['status'] == self::RESULT_CODE_SUCCESS) {
            $decrypted = json_decode($this->decrypt($response['data']), true);
            $this->CI->utils->debug_log('=====================yingsheng processPaymentUrlFormPost response decrypted', $decrypted);

            if($decrypted && !empty($decrypted['transaction_url'])){
                $order = $this->CI->sale_order->getSaleOrderBySecureId($secure_id);
                $this->CI->sale_order->updateExternalInfo($order->id, $decrypted['trans_id']);
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $decrypted['transaction_url'],
                );
            }
            else if($decrypted && !empty($decrypted['qr_image_url'])){
                $order = $this->CI->sale_order->getSaleOrderBySecureId($secure_id);
                $this->CI->sale_order->updateExternalInfo($order->id, $decrypted['trans_id']);
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $decrypted['qr_image_url'],
                );
            }
            else{
                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                    'message' => lang('Decrypt failed.')
                );
            }
        }
        else if(isset($response['error_code'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => '['.$response['error_code'].']:'. self::ERROR_MSG[$response['error_code']]
            );
        }
        else {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => lang('Invalidte API response')
            );
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
        $this->CI->utils->debug_log("=====================yingsheng callbackFrom $source params", $params);

        if($source == 'server'){
            $raw_post_data = file_get_contents('php://input', 'r');
            $this->CI->utils->debug_log("=====================yingsheng raw_post_data", $raw_post_data);

            $raw_post_arr = explode("&", $raw_post_data);
            $post_params = array();

            if(!empty($raw_post_arr)) {
                foreach($raw_post_arr as $value) {
                    $raw_post_each_item = explode("=", $value);
                    $post_params[$raw_post_each_item[0]] = urldecode($raw_post_each_item[1]);
                }
            }

            $this->CI->utils->debug_log("=====================yingsheng json_decode post_params", $post_params);
            $params = $post_params;

            $decrypted = json_decode($this->decrypt($params['data']), true);
            $this->CI->utils->debug_log("=====================yingsheng callbackFrom decrypted", $decrypted);

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

            $this->CI->sale_order->updateExternalInfo($order->id, $decrypted['trans_id'], null, null, null, $response_result_id);
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

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================yingsheng checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        $decrypted = json_decode($this->decrypt($fields['data']), true);
        $this->CI->utils->debug_log("======================yingsheng checkCallbackOrder decrypted", $decrypted);

        $requiredFields = array(
            'amount', 'merchant_user', 'merchant_order_no', 'trans_id'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $decrypted)) {
                $this->writePaymentErrorLog("=====================yingsheng checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        if ($decrypted['amount'] != $this->convertAmountToCurrency($order->amount)) {

            if($this->getSystemInfo('allow_callback_amount_diff')){
                $diffAmount = abs($this->convertAmountToCurrency($order->amount) - floatval( $decrypted['amount']));
                if ($diffAmount >= 1) {
                    $this->writePaymentErrorLog("=====================yingsheng checkCallbackOrder Payment amounts ordAmt - payAmt > 1, expected [$order->amount]", $fields ,$diffAmount);
                    return false;
                }
                $this->CI->utils->debug_log("=====================yingsheng checkCallbackOrder amount not match expected [$order->amount]");
                $notes = $order->notes . " | callback diff amount, origin was: " . $order->amount;
                $this->CI->sale_order->fixOrderAmount($order->id, $decrypted['amount'], $notes);
            }else{
                $this->writePaymentErrorLog("======================yingsheng checkCallbackOrder Payment amount is wrong, expected [$order->amount]", $decrypted);
                return false;
            }

        }

        if ($decrypted['merchant_order_no'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================yingsheng checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $decrypted);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 2, '.', '');
    }

    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    protected function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    # -- signing --
    protected function sign($params) {
        $enc_json =  stripslashes(json_encode($params));
        $encParam_encrypted = "";
        foreach (str_split($enc_json, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $this->getPubKey(), OPENSSL_PKCS1_PADDING);
            $encParam_encrypted .= $encryptData ;
        }
        $encParam = base64_encode($encParam_encrypted);
        openssl_sign($encParam, $sign_info, $this->getPrivKey(), OPENSSL_ALGO_SHA1);
        $sign = base64_encode($sign_info);


        return array('encParam' => $encParam, 'sign' => $sign);
    }

    public function decrypt($data){
        $decParam = false;
        $data = base64_decode($data);

        foreach (str_split($data, 256) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $this->getPrivKey());
            $decParam .= $decryptData;
        }

        return $decParam;
    }

    protected function validateSign($params) {
        $valid = openssl_verify($params['data'], base64_decode($params['sign']), $this->getPubKey());

        return $valid;
    }

    protected function getPubKey() {
        $yingsheng_pub_key = $this->getSystemInfo('yingsheng_pub_key');
        $pub_key = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . chunk_split($yingsheng_pub_key, 64, PHP_EOL) . '-----END PUBLIC KEY-----' . PHP_EOL;
        return openssl_get_publickey($pub_key);
    }

    protected function getPrivKey() {
        $yingsheng_priv_key = $this->getSystemInfo('yingsheng_priv_key');
        $priv_key = '-----BEGIN RSA PRIVATE KEY-----' . PHP_EOL . chunk_split($yingsheng_priv_key, 64, PHP_EOL) . '-----END RSA PRIVATE KEY-----' . PHP_EOL;
        return openssl_get_privatekey($priv_key);
    }

}