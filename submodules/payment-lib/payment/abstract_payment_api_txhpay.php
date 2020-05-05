<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * tianxiahui 天下汇
 *
 * * 'TXHPAY_ALIPAY_PAYMENT_API', ID 5655
 * * 'TXHPAY_ALIPAY_H5_PAYMENT_API', ID 5656
 *
 * Required Fields:
 *
 * * URL: https://merchantgatewayapi.tianxiahui.biz/api/deposit
 * * Account - Merchant ID
 * * Key - Signing key
 * * Extra Info
 *
 *
 *
 * @category Payment
 * @copyright 2013-2020 TripleOneTech
 */
abstract class Abstract_payment_api_txhpay extends Abstract_payment_api {

    const PLATFORM_PC       = '1';
    const PLATFORM_MOBILE   = '2';

    const PAYTMETHOD_ALIPAY = '2';

    const SUCCESS_CODE      = 'S00';
    const RETURN_SUCCESS    = 'SUCCESS';
    const RETURN_FAILED     = 'FAIL';


    # Implement these for specific pay type
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params);

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'webwt_server_pub_key', 'webwt_pri_key');
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
        $params['merchantNumber']      = $this->getSystemInfo("account");
        $params['merchantOrderNumber'] = $order->secure_id;
        $params['requestedAmount']     = $this->convertAmountToCurrency($amount); //元
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['callbackUrl']         = $this->getNotifyUrl($orderId);
        $params['customerRequestedIp'] = $this->getClientIP();
        $params['sign'] = $this->sign($params);


        $this->CI->utils->debug_log("=====================txhpay generatePaymentUrlForm", $params);
        return $this->processPaymentUrlForm($params);
    }

    # Submit URL form
    protected function processPaymentUrlFormURL($params) {
        $url = $this->getSystemInfo('url');
        $response = $this->processCurl($url, $params, $params['merchantOrderNumber']);
        $decode_data = json_decode($response, true);
        $this->CI->utils->debug_log('========================================txhpay processPaymentUrlFormQRcode response json to array', $decode_data);

        $msg = lang('Invalidate API response');
        if($decode_data['code'] == self::SUCCESS_CODE) {
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_URL,
                'url' => $decode_data['data']['paymentUrl'],
            );
        }else {
            if(!empty($decode_data['message'])) {
                $msg = $decode_data['message'];
            }
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => $msg
            );
        }
    }

    protected function processCurl($url, $params, $orderSecureId=NULL) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->CI->utils->encodeJson($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $this->setCurlProxyOptions($ch);

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

        $response = curl_exec($ch);
        $this->CI->utils->debug_log('=========================submitPostForm curl content ', $response);

        $errCode = curl_errno($ch);
        $error = curl_error($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $content = substr($response, $header_size);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        $statusText = $errCode . ':' . $error;
        curl_close($ch);

        $this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

        $response_result_content = is_array($content) ? json_encode($content) : $content;

        #save response result
        $response_result_id = $this->submitPreprocess($params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $orderSecureId);

        return $content;
    }


    /**
     * detail: This will be called when the payment is async, API server calls our callback page,
     * When that happens, we perform verifications and necessary database updates to mark the payment as successful
     *
     * @param int $orderId order id
     * @param array $params
     * @return array
     */
    public function callbackFromServer($orderId, $params) {
        $response_result_id = parent::callbackFromServer($orderId, $params);
        return $this->callbackFrom('server', $orderId, $params, $response_result_id);
    }

    /**
     * detail: This will be called when user redirects back to our page from payment API
     *
     * @param int $orderId order id
     * @param array $params
     * @return array
     */
    public function callbackFromBrowser($orderId, $params) {
        $response_result_id = parent::callbackFromBrowser($orderId, $params);
        return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
    }

    # $source can be 'server' or 'browser'
    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        $this->CI->utils->debug_log("=======================txhpay callbackFromServer $source callbackFrom", $params);

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
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
            }
        }

        $result['success'] = $success;
        if ($processed) {
            $result['message'] = self::RETURN_SUCCESS;
        } else {
            $result['return_error'] = self::RETURN_FAIL;
        }

        if ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return $result;
    }

    /**
     * detail: Validates whether the callback from API contains valid info and matches with the order
     *
     * @return boolean
     */

    private function checkCallbackOrder($order, $fields, &$processed = false) {
        $requiredFields = array('code', 'actualAmount', 'merchantOrderNumber', 'sign');

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================txhpay missing parameter: [$f]", $fields);
                return false;
            }
        }
        # is signature authentic?
        if (!$this->validateSign($fields,$fields['sign'])) {
            $this->writePaymentErrorLog('=====================txhpay checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        if ($fields['code'] != self::SUCCESS_CODE) {
            $payStatus = $fields['code'];
            $this->writePaymentErrorLog("=====================txhpay Payment was not successful, payStatus is [$payStatus]", $fields);
            return false;
        }

        if ($fields['actualAmount'] != $this->convertAmountToCurrency($order->amount)) {
            $this->writePaymentErrorLog("======================txhpay checkCallbackOrder Payment amount is wrong, expected [$order->amount]", $fields);
            return false;
        }

        if ($fields['merchantOrderNumber'] != $order->secure_id) {
            $this->writePaymentErrorLog("========================txhpay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    # -- amount --
    protected function convertAmountToCurrency($amount) {
        return number_format($amount , 0, '.', '');
    }

    # -- notifyURL --
    public function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    # -- returnURL --
    public function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

   # -- signatures --
    private function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = strtoupper(md5($signStr));
        return $sign;
    }

    private function createSignStr($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if($key == 'sign'){
                continue;
            }
            $signStr.=$key."=".$value."&";
        }
        $signStr = $signStr."merchantKey=".$this->getSystemInfo('key');
        return $signStr;
    }

    public function validateSign($params) {
        $signStr = $this->createSignStr($params);
        $sign = strtoupper(md5($signStr));

        if($params['sign'] == $sign){
            return true;
        }
        else{
            return false;
        }
    }
}
