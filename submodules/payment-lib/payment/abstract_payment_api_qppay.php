<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * QPPAY
 *
 * * QPPAY_ALIPAY_PAYMENT_API, ID: 5681
 * * QPPAY_ALIPAY_H5_PAYMENT_API, ID: 5682
 *
 * Required Fields:
 * * URL
 * * Key
 * * Account
 *
 * Field Values:
 * * URL: https://c2cpayapi.hb8game.com/pay/v1/gateway/order
 * * Key: ## Secret Key ##
 * * Account: ## Merchant Code ##
 *
 * @category Payment
 * @copyright 2013-2020 TripleOneTech
 */
abstract class Abstract_payment_api_qppay extends Abstract_payment_api {
    const CHANNELCODE_ALIPAY = 'alipay';
    const RESULT_CODE_SUCCESS = 200;

    const CALLBACK_SUCCESS = 2;
    const RETURN_SUCCESS_CODE = 'ok';

    public function __construct($params = null) {
        parent::__construct($params);
        $this->_custom_curl_header = array('Content-Type:application/json');
    }

    # Implement these to specify pay type
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params);

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $params = array();
        $params['merchantCode'] = $this->getSystemInfo('account');
        $params['payId']        = $order->secure_id;
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['clientIp']     = $this->getClientIP();
        $params['price']        = $this->convertAmountToCurrency($amount);
        $params['notifyUrl']    = $this->getNotifyUrl($orderId);
        $params['returnUrl']    = $this->getReturnUrl($orderId);
        $this->CI->utils->debug_log('=====================qppay generatePaymentUrlForm params', $params);
        $params['sign']         = $this->sign($params);

        return $this->processPaymentUrlForm($params);
    }

    # Implement processPaymentUrlForm
    protected function processPaymentUrlFormPost($params) {
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, true, $params['payId']);
        $response = json_decode($response, true);

        if(is_array($response)) {
            if($response['status'] == self::RESULT_CODE_SUCCESS) {
                $order = $this->CI->sale_order->getSaleOrderBySecureId($params['payId']);
                $this->CI->sale_order->updateExternalInfo($order->id, $response['data']['orderId']);
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $response['data']['payUrl'],
                );
            } else {
                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR,
                    'message' => '['.$response['status'].'] '.$response['msg']
                );
            }
        } else {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => lang('Invalidate API response')
            );
        }
    }

    protected function processPaymentUrlFormRedirect($params) {
        $response = $this->processCurl($params);

        if(isset($response['uri'])) {
            $order = $this->CI->sale_order->getSaleOrderBySecureId($params['out_trade_no']);
            $this->CI->sale_order->updateExternalInfo($order->id, $response['trade_no']);
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_URL,
                'url' => $response['uri'],
            );
        }
        else if(isset($response['message'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => $response['message']
            );
        }
        else if($response) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => $response
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

        $this->CI->utils->debug_log("=====================qppay callbackFrom $source params", $params);

        if($source == 'server' ){
            if(empty($params)){
                $raw_post_data = file_get_contents('php://input');
                $params = json_decode($raw_post_data, true);
                $this->CI->utils->debug_log("=====================qppay raw_post_data", $raw_post_data);
                $this->CI->utils->debug_log("=====================qppay json_decode params", $params);
            }
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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['orderId'], null, null, null, $response_result_id);
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
        $requiredFields = array(
            'status', 'price', 'realPrice', 'payId', 'orderId'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================qppay checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================qppay checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        if ($fields['status'] != self::CALLBACK_SUCCESS) {
            $this->writePaymentErrorLog("======================qppay checkCallbackOrder Payment status is not success", $fields);
            return false;
        }

        if ($fields['realPrice'] != $this->convertAmountToCurrency($order->amount)) {
            $this->writePaymentErrorLog("======================qppay checkCallbackOrder Payment amount is wrong, expected [$order->amount]", $fields);
            return false;
        }

        if ($fields['payId'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================qppay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    # -- signatures --
    private function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = strtoupper(md5($signStr));
        return $sign;
    }

    private function createSignStr($params, $validate=false) {
        if ($validate) {
            $keys = array('merchantCode', 'payId', 'orderId', 'price', 'realPrice', 'channelCode', 'payUrl', 'status', 'param');
        } else {
            $keys = array('merchantCode', 'payId', 'price', 'channelCode', 'clientIp', 'notifyUrl', 'param');
        }

        $signStr = "";
        foreach($keys as $key) {
            if (array_key_exists($key, $params) && !empty($params[$key])) {
                $signStr .= $params[$key];
            }
        }
        $signStr .= $this->getSystemInfo('key');
        return $signStr;
    }

    private function validateSign($params) {
        $sign = $this->sign($params, true);
        if($params['sign'] == $sign){
            return true;
        }
        else{
            return false;
        }
    }

    # -- Private functions --
    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    private function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 1, '.', '');
    }
}