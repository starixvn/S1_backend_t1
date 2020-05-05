<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';
/**
 * HFPAY
 *
 * * HFPAY_ALIPAY_PAYMENT_API, ID: 5117
 * * HFPAY_ALIPAY_H5_PAYMENT_API, ID: 5118
 * * HAOFU_WEIXIN_PAYMENT_API, ID: 5444
 * * HAOFU_WEIXIN_H5_PAYMENT_API, ID: 5445
 * * HAOFU_QUICKPAY_PAYMENT_API, ID: 5449
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.woniu97.com/payCenter/aliPay2
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_hfpay extends Abstract_payment_api {

    const PAY_TYPE_PC = "sm";
    const PAY_TYPE_H5 = "h5";
    const RESULT_SUCCESS = "T";

    const CALLBACK_SUCCESS = 1;
    const RETURN_SUCCESS_CODE = "success";


    public function __construct($params = null) {
        parent::__construct($params);
        $this->_custom_curl_header = array('Content-Type:application/json');
    }

    protected abstract function processPaymentUrlForm($params);

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $params = array();
        $params['partner']      = $this->getSystemInfo('account');
        $params['amount']       = $this->convertAmountToCurrency($amount);
        $params['request_time'] = $orderDateTime->getTimestamp();
        $params['trade_no']     = $order->secure_id;
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['notify_url']   = $this->getNotifyUrl($orderId);
        if($this->getSystemInfo('use_returnurl')){
            $params['callback_url'] = $this->getReturnUrl($orderId);
        }
        $params['sign']         = $this->sign($params);


        $this->CI->utils->debug_log("=====================hfpay generatePaymentUrlForm params", $params);

        return $this->processPaymentUrlForm($params);
    }


    protected function processPaymentUrlFormPost($params) {
        $response = $this->process($params, $params['trade_no']);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================hfpay processPaymentUrlFormPost response', $response);

        if($response['is_success'] == self::RESULT_SUCCESS) {
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_URL,
                'url' => $response['result'],
            );
        }
        else if(isset($response['fail_code'])) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => '['.$response['fail_code'].']: '.$response['fail_msg']
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

    protected function processPaymentUrlRedirectFormPost($params) {
		$url = $this->getSystemInfo('url');
		return array(
			'success' => true,
			'type' => self::REDIRECT_TYPE_FORM,
			'url' => $url,
			'params' => $params,
			'post' => true,
		);
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

        $this->CI->utils->debug_log("=====================hfpay callbackFrom $source params", $params);

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
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                $this->CI->sale_order->updateExternalInfo($order->id, $params['trade_id'], null, null, null, $response_result_id);
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
            'trade_id', 'status', 'amount_str', 'out_trade_no', 'sign'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================hfpay checkCallbackOrder Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog("======================hfpay checkCallbackOrder Signature Error", $fields);
            return false;
        }

        $processed = true; # processed is set to true once the signature verification pass

        if ($fields['status'] != self::CALLBACK_SUCCESS) {
            $this->writePaymentErrorLog("======================hfpay checkCallbackOrder Payment status is not success", $fields);
            return false;
        }

        $check_amount = $this->convertAmountToCurrency($order->amount);
        if ($fields['amount_str'] != $check_amount) {
            $this->writePaymentErrorLog("======================hfpay checkCallbackOrder Payment amount is wrong, expected <= ". $check_amount, $fields);
            return false;
        }

        if ($fields['out_trade_no'] != $order->secure_id) {
            $this->writePaymentErrorLog("======================hfpay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    public function getBankListInfoFallback() {
        return array(
            array('label' => '中国工商银行', 'value' => 'ICBC'),
            array('label' => '中国农业银行', 'value' => 'ABC'),
            array('label' => '中国银行', 'value' => 'BOC'),
            array('label' => '中国建设银行', 'value' => 'CCB'),
            array('label' => '交通银行', 'value' => 'COMM'),
            array('label' => '中信银行', 'value' => 'CITIC'),
            array('label' => '中国光大银行', 'value' => 'CEB'),
            array('label' => '华夏银行', 'value' => 'HXB'),
            array('label' => '中国民生银行', 'value' => 'CMBC'),
            array('label' => '广发银行', 'value' => 'GDB'),
            array('label' => '平安银行', 'value' => 'SZPAB'),
            array('label' => '招商银行', 'value' => 'CMB'),
            array('label' => '兴业银行', 'value' => 'CIB'),
            array('label' => '北京银行', 'value' => 'BCCB'),
            array('label' => '上海浦东发展银行', 'value' => 'SPDB'),
            array('label' => '汉口银行', 'value' => 'HKBCHINA'),
            array('label' => '杭州银行', 'value' => 'HCCB'),
            array('label' => '晋城银行', 'value' => 'SXJS'),
            array('label' => '南京银行', 'value' => 'NJCB'),
            array('label' => '宁波银行', 'value' => 'NBCB'),
            array('label' => '上海银行', 'value' => 'BOS'),
            array('label' => '长沙银行', 'value' => 'CSCB'),
            array('label' => '浙江稠州商业银行', 'value' => 'CZCB'),
            array('label' => '顺德农村商业银行', 'value' => 'SDBC'),
            array('label' => '恒丰银行', 'value' => 'EGBANK'),
            array('label' => '浙商银行', 'value' => 'CZB'),
            array('label' => '渤海银行', 'value' => 'CBHB'),
            array('label' => '徽商银行', 'value' => 'HSBANK'),
            array('label' => '上海农商银行', 'value' => 'SHRCB'),
            array('label' => '北京农村商业行', 'value' => 'BJRCB'),
            array('label' => '深圳农商行', 'value' => 'SNXS'),
            array('label' => '中国邮政储蓄银行', 'value' => 'PSBC'),
        );
    }

    # -- signatures --
    private function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);
        return $sign;
    }

    private function createSignStr($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if(empty($value) || $key == 'key') {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        return $signStr.$this->getSystemInfo('key');
    }

    private function validateSign($params) {
        ksort($params);
        $signStr = '';
        foreach($params as $key => $value) {
            if( ($key == 'sign') || (empty($value)) ) {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        $sign = md5($signStr.$this->getSystemInfo('key'));
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
        $convert_multiplier = $this->getSystemInfo('convert_multiplier', 1);
        return number_format($amount * $convert_multiplier, 2, '.', '');
    }

    public function process($params, $orderSecureId) {
        $url = $this->getSystemInfo('url');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $this->setCurlProxyOptions($ch);
        $response    = curl_exec($ch);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode     = curl_errno($ch);
        $error       = curl_error($ch);
        curl_close($ch);
        $this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

        #save response result
        $response_result_id = $this->submitPreprocess($params, $response, $url, $response, array('errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode), $orderSecureId);

        return $response;
    }
}