<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * YSYDAI
 *
 * * YSYDAI_PAYMENT_API, ID: 5263
 * * YSYDAI_QUICKPAY_PAYMENT_API, ID: 5264
 * * YSYDAI_WITHDRAWAL_PAYMENT_API, ID: 5265
 * * YSYDAI_WITHDRAWAL_2_PAYMENT_API, ID: 5268
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: http://api.ysydai.cn/pay/ap.php
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_ysydai extends Abstract_payment_api {

    const PAYTYPE_ONLINEBANK = "51";
    const PAYTYPE_ALIPAY     = "21";
    const PAYTYPE_ALIPAY_H5  = "22";
    const PAYTYPE_WEIXIN     = "11";
    const PAYTYPE_WEIXIN_H5  = "12";
    const PAYTYPE_QUICKPAY   = "52";
    const PAYTYPE_ALIPAY_CODE= "60";

    const RESULT_CODE_SUCCESS = "0";
    const CALLBACK_STATUS_SUCCESS = "0";

    const RETURN_FAIL_CODE = 'FAIL';
    const RETURN_SUCCESS_CODE = 'success';

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
        $player = $this->CI->player->getPlayerById($playerId);

        $params = array();
        $params['cpid']        = $this->getSystemInfo('account');
        $params['cp_trade_no'] = $order->secure_id;
        $params['fee']         = $this->convertAmountToCurrency($amount);
        $params['jump_url']    = $this->getReturnUrl($orderId);
        $params['notify_url']  = $this->getNotifyUrl($orderId);
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['sign']        = $this->sign($params);

        $this->CI->utils->debug_log('=====================ysydai generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    protected function processPaymentUrlFormPost($params) {
        return array(
            'success' => true,
            'type' => self::REDIRECT_TYPE_FORM,
            'url' => $this->getSystemInfo('url'),
            'params' => $params,
            'post' => false,
        );
    }

    protected function processPaymentUrlFormRedirect($params) {
        $response = $this->submitGetForm($this->getSystemInfo('url'), $params, false, $params['cp_trade_no']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================ysydai processPaymentUrlFormPost response', $response);

        if($response['result_code'] == self::RESULT_CODE_SUCCESS) {
            $order = $this->CI->sale_order->getSaleOrderBySecureId($params['cp_trade_no']);
            $this->CI->sale_order->updateExternalInfo($order->id, $response['pay_transaction_id']);
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_URL,
                'url' => $response['pay_info'],
            );
        }
        else if(isset($response['msg'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => '['.$response['result_code'].']: '.$response['msg']
            );
        }
        else if(isset($response['result_code'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => '['.$response['result_code'].']: '.self::ERROR_MSG[$response['result_code']]
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

    # $source can be 'server' or 'browser'
    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        $this->CI->utils->debug_log("=====================ysydai callbackFrom $source params", $params);

        if($source == 'server'){
            if (empty($params)) {
                $raw_post_data = file_get_contents('php://input', 'r');
                $this->CI->utils->debug_log("=====================ysydai callbackFrom $source raw_post_data", $raw_post_data);
                $params = json_decode($raw_post_data,true);
                $this->CI->utils->debug_log("=====================ysydai callbackFrom $source json_decode params", $params);
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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['pay_transaction_id'], null, null, null, $response_result_id);
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                if ($params['result_code'] == self::CALLBACK_STATUS_SUCCESS){
                    $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
                }
                else {
                    $this->CI->sale_order->declineSaleOrder($order->id, 'auto server callback declined ' . $this->getPlatformCode(), false);
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
            'cp_trade_no', 'fee', 'result_code', 'pay_transaction_id', 'sign'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================ysydai checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================ysydai checkCallbackOrder Signature Error', $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        if ($fields['fee'] != $this->convertAmountToCurrency($order->amount)) {
            $this->writePaymentErrorLog("=====================ysydai Payment amounts do not match, expected [$order->amount]", $fields);
            return false;
        }

        if ($fields['cp_trade_no'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================ysydai checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
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
        $sign = strtoupper(md5($signStr));
     
        return $sign;
    }

    private function createSignStr($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if($key == 'sign') {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        return $signStr.'key='.$this->getSystemInfo('key');
    }

    protected function validateSign($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if($key == 'sign') {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        $sign = strtoupper(md5($signStr.'key='.$this->getSystemInfo('key')));
        if($params['sign'] == $sign){
            return true;
        }
        else{
           
            return false;
        }
    }

    # -- Private functions --
    # After payment is complete, the gateway will invoke this URL asynchronously
    protected function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## After payment is complete, the gateway will send redirect back to this URL
    protected function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## Format the amount value for the API
    protected function convertAmountToCurrency($amount) {
        return number_format($amount*100, 0, '.', '');
    }

    const ERROR_MSG = array(
        '7001' => '请求参数为空（支付要 GET请求）',
        '7002' => '金额为空或小于等于0',
        '7003' => '必填参数 cpid或pay_type为空',
        '7011' => '可疑攻击行为',
        '7103' => '同IP短时间内请求频繁',
        '7104' => '同订单号短时间内请求频繁',
        '8101' => '合作状态关闭',
        '8102' => '签名错误',
        '8103' => '未配置路由',
        '8106' => '未配置业务',
        '8107' => '对接期间单笔金额超限',
        '8108' => '超过日限额度',
        '8109' => '无可用业务',
        '8110' => '业务限制',
        '8201' => '上游返回错误',
        'df2001' => '请求数据异常',
        'df2002' => '必填参数未填写',
        'df2003' => '请求金额有误',
        'df2004' => 'IP白名单未设置',
        'df2005' => '签名错误',
        'df2006' => '系统繁忙',
        'df2007' => '账户黑名单',
        'df2010' => '可用余额不足',
        'df2011' => '子资金池余额均不足',
        'df2012' => '与上游交互失败(这种情况重试就好)',
        'df1061' => '订单号重复',
    );
}