<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * WENPAY 稳付
 * *
 * * WENPAY_ALIPAY_PAYMENT_API, ID: 983
 * * WENPAY_ALIPAY_H5_PAYMENT_API, ID: 984
 * * WENPAY_WEIXIN_PAYMENT_API, ID: 985
 * * WENPAY_WEIXIN_H5_PAYMENT_API, ID: 986
 * * WENPAY_QUICKPAY_PAYMENT_API, ID: 5437
 * * WENPAY_PAYMENT_API, ID: 5657
 * * WENPAY_ALIPAY_GATEWAY_PAYMENT_API, ID: 5658
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.wenpay8.com/PaymentGetway/OrderRquest
 * * Account: ## Merchant ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_wenpay extends Abstract_payment_api {


    const CHANNEL_TYPE_ALIPAY    = 'C200';
    const CHANNEL_TYPE_ALIPAY_H5 = 'C201';
    const CHANNEL_TYPE_WEIXIN    = 'C100';
    const CHANNEL_TYPE_WEIXIN_H5 = 'C101';
    const CHANNEL_TYPE_UNIONPAY  = 'UNION_SCAN';
    const CHANNEL_TYPE_QUICKPAY  = 'QUICK_PAY';
    const CHANNEL_TYPE_ONLINE  = 'BANK_PAY';
    const CHANNEL_TYPE_ALIPAY_GATEWAY    = 'ALIGATE_PAY';

    const RESULT_CODE_SUCCESS = 'true';
    const RETURN_SUCCESS_CODE = 'OK';


    public function __construct($params = null) {
        parent::__construct($params);
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
        $params['merchantId']      = $this->getSystemInfo('account');
        $params['merchantOrderId'] = $order->secure_id;
        $params['orderAmount']     = $this->convertAmountToCurrency($amount);
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['notifyUrl']       = $this->getNotifyUrl($orderId);
        $params['returnUrl']       = $this->getReturnUrl($orderId);
        $params['ip']              = $this->getClientIP();
        $params['remark']          = 'Topup';
        $params['jsonResult']      = $this->getSystemInfo('jsonResult', 1);
        $params['sign']            = $this->sign($params);
        $this->CI->utils->debug_log('=====================wenpay generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    # Implement processPaymentUrlForm
    protected function processPaymentUrlFormPost($params) {
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['merchantOrderId']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================wenpay processPaymentUrlFormPost response', $response);

        if(isset($response['Success']) && $response['Success'] == self::RESULT_CODE_SUCCESS) {
            if (!preg_match('#^https?://#i', $response['Qrcode'])) {
                $query = parse_url($response['Qrcode'], PHP_URL_QUERY);
                parse_str($query, $output);

                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $output['qrcode'],
                );
            }
            else{
                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_URL,
                    'url' => $response['Qrcode'],
                );
            }
        }
        else if(isset($response['ErrorCode'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => '['.$response['ErrorCode'].']'.$response['ErrorMessage']
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
        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false, $params['merchantOrderId']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================wenpay processPaymentUrlFormPost response', $response);

        if(isset($response['Success']) && $response['Success'] == self::RESULT_CODE_SUCCESS) {
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_QRCODE,
                'url' => $response['Qrcode'],
            );
        }
        else if(isset($response['ErrorCode'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => '['.$response['ErrorCode'].']'.$response['ErrorMessage']
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

        $this->CI->utils->debug_log("=====================wenpay callbackFrom $source params", $params);

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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['systemOrderId'], '', null, null, $response_result_id);
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
            'merchantId', 'systemOrderId', 'merchantOrderId', 'orderAmount', 'sign'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================wenpay checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================wenpay checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass


        if ($fields['orderAmount'] != $this->convertAmountToCurrency($order->amount)) {
            if($this->getSystemInfo('allow_callback_amount_diff')){
                $diffAmount = abs($this->convertAmountToCurrency($order->amount) - floatval( $fields['orderAmount']));
                if ($diffAmount >= 1) {
                    $this->writePaymentErrorLog("=====================wenpay checkCallbackOrder Payment amounts ordAmt - payAmt > 1, expected [$order->amount]", $fields ,$diffAmount);
                    return false;
                }

            }else{
                $this->writePaymentErrorLog("=====================wenpay checkCallbackOrder Payment amounts do not match, expected [$order->amount]", $fields);
                return false;
            }
        }

        if ($fields['merchantOrderId'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================wenpay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    # -- signatures --
    protected function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);
        return $sign;
    }

    private function createSignStr($params) {
        $keys = array('merchantId', 'merchantOrderId', 'orderAmount', 'notifyUrl', 'channelType', 'remark', 'ip');
        $signStr = "";
        foreach($keys as $key) {
            if (array_key_exists($key, $params)) {
                $signStr .= $key.'='.$params[$key].'&';
            }
        }
        $signStr = rtrim($signStr, '&').$this->getSystemInfo('key');
        return $signStr;
    }

    private function validateSign($params) {
        $keys = array('merchantId', 'merchantOrderId', 'orderAmount', 'systemOrderId', 'channelType', 'remark', 'ip');
        $signStr = "";
        foreach($keys as $key) {
            if (array_key_exists($key, $params)) {
                $signStr .= $key.'='.$params[$key].'&';
            }
        }
        $signStr = rtrim($signStr, '&').$this->getSystemInfo('key');
        $sign = md5($signStr);

        if($params['sign'] == $sign){
            return true;
        }
        else{
           
            return false;
        }
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