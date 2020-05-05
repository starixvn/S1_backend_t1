<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * CPAYCARD
 * http://ncompany.cpay.life
 *
 * * CPAYCARD_BANKCARD_PAYMENT_API, ID: 5352
 * * CPAYCARD_ALIPAY_BANKCARD_PAYMENT_API, ID: 5353
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 * * Secret
 *
 * Field Values:
 * * URL: http://pbsgb0micy.51http.tech/
 * * Account: ## Merchant ID ##
 * * Key: ## App Key ##
 * * Secret: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
abstract class Abstract_payment_api_cpaycard extends Abstract_payment_api {
    const SOURCE_PC    = 1;
    const SOURCE_PHONE = 2;

    const RESULT_SUCCESS = 0;

    const CALLBACK_SUCCESS = 3;
    const CALLBACK_FAILED  = 1;

    const RETURN_FAIL_CODE = 'FAIL';
    const RETURN_SUCCESS_CODE = '{"msg": "success"}';

    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $params = array();
        $params['psd_order_id']   = $order->secure_id;
        $params['psd_order_time'] = $orderDateTime->format('YmdHis');
        $params['order_amount']   = $this->convertAmountToCurrency($amount);
        $params['order_source']   = ($this->CI->utils->is_mobile()) ? self::SOURCE_PHONE : self::SOURCE_PC;
        $this->CI->utils->debug_log('=====================cpaycard generatePaymentUrlForm params', $params);
        return $this->processPaymentUrlFormRedirect($params);
    }


    protected function processPaymentUrlFormRedirect($params) {
        $response = $this->processCurl($params);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================cpaycard processPaymentUrlFormRedirect response', $response);

        if(isset($response['error'])) {
            if($response['error'] === self::RESULT_SUCCESS){
                $order = $this->CI->sale_order->getSaleOrderBySecureId($params['psd_order_id']);
                $this->CI->sale_order->updateExternalInfo($order->id, $response['data']['cpay_order_id']);
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $response['data']['redirect_url'],
                );
            }
            else{
                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR,
                    'message' => '['.$response['error'].'] '.$response['msg']. ' - '.$response['name']
                );
            }
        }
        elseif($response) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => $response
            );
        }
        else {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => lang('Invalidate API response')
            );
        }
    }

    # Callback URI: /callback/fixed_process/<payment_id>
    public function getOrderIdFromParameters($flds) {
        $this->CI->utils->debug_log('=====================cpaycard getOrderIdFromParameters flds', $flds);

        if(isset($flds['psd_order_id'])) {
            $order = $this->CI->sale_order->getSaleOrderBySecureId($flds['psd_order_id']);
            return $order->id;
        }
        else {
            $this->utils->debug_log('=====================cpaycard getOrderIdFromParameters cannot get psd_order_id', $flds);
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

    # $source can be 'server' or 'browser'
    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        $this->CI->utils->debug_log("=====================cpaycard callbackFrom $source params", $params);

        if($source == 'server' ){
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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['cpay_order_id'], null, null, null, $response_result_id);
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {                #redirect to success/fail page according to return params
                if($params['state'] == self::CALLBACK_SUCCESS){
                    $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
                }
                elseif($params['state'] == self::CALLBACK_FAILED){
                    $this->CI->sale_order->declineSaleOrder($order->id, 'auto server callback declined ' . $this->getPlatformCode() . ': ' . $params['message'], false);
                }
            }
        }

        $result['success'] = $success;
        if ($processed) {
            $result['message'] = self::RETURN_SUCCESS_CODE;
        } else {
            $result['return_error'] = self::RETURN_FAIL_CODE;
        }

        if ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return $result;
    }

    ## Validates whether the callback from API contains valid info and matches with the order
    ## Reference: code sample, callback.php
    private function checkCallbackOrder($order, $fields, &$processed = false) {
        $requiredFields = array(
            'cpay_order_id', 'psd_order_id', 'order_amount', 'fact_amount', 'state', 'desc'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================cpaycard checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }


        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================cpaycard checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        $check_amount = $this->convertAmountToCurrency($order->amount);
        if ($fields['order_amount'] != $check_amount) {
            $this->writePaymentErrorLog("=====================cpaycard Payment amounts do not match, expected [$order->amount]", $fields);
            return false;
        }

        if ($fields['psd_order_id'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================cpaycard checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }


    protected function processCurl($params,$transId=NULL,$return_all=false) {
        $url = $this->getSystemInfo('url');
        $this->_custom_curl_header = array(
            'Content-Type: application/x-www-form-urlencoded',
            'appkey: '.$this->getSystemInfo('key'),
            'token: '.$this->sign($params)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());
        $this->setCurlProxyOptions($ch);

        $response    = curl_exec($ch);
        $errCode     = curl_errno($ch);
        $error       = curl_error($ch);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $header = substr($response, 0, $header_size);
        $content = substr($response, $header_size);

        $this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $content, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

        #withdrawal lock processing
        if(substr($transId, 0, 1) == 'W' && $errCode == '28') {	//curl_errno means timeout
            // $content = '{"lock": true, "msg": "Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error.'" }';
            $content = array('lock' => true, 'msg' => 'Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error);
        }

        $response_result_content = is_array($content) ? json_encode($content) : $content;

        #save response result
        $response_result_id = $this->submitPreprocess($params, $content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $params['psd_order_id']);

        if($return_all){
            $response_result = [
                $params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $transId
            ];
            $this->CI->utils->debug_log('=========================cpaycard  return_all response_result', $response_result);
            return array($content, $response_result);
        }

        return $content;
    }

    # -- signatures --
    protected function sign($params) {
        ksort($params);
        $secret_key = $this->getSystemInfo('secret');
        $signStr = $this->pad2Length(http_build_query($params), 16);

        $res = openssl_encrypt($signStr, "AES-256-ECB", $secret_key, OPENSSL_RAW_DATA|OPENSSL_NO_PADDING);
        $sign = bin2hex($res);

        $token = hash_hmac("md5", $sign, $secret_key);;
        return $token;
    }

    # 将$text补足$padlen倍数的长度
    protected function pad2Length($text, $padlen){
        $len = strlen($text)%$padlen;
        $span = $padlen - $len;
        if($len == 0){
            $span = 0;
        }
        for($i=0; $i<$span; $i++){
            $text .= chr(0);
        }
        return $text;
    }

    private function validateSign($params) {
        $token = $this->sign($params);
        $headers = $this->CI->input->request_headers();
        $headers_token = $headers['Token'];
        if($token == $headers_token){
            return true;
        }
        else{
            return false;
        }
    }

    ## Format the amount value for the API
    protected function convertAmountToCurrency($amount) {
        return number_format($amount*100, 0, '.', '');
    }
}