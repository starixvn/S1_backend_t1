<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * WANWEIPAY
 *
 * * WANWEIPAY_PAYMENT_API, ID: 5662
 * * WANWEIPAY_QUICKPAY_PAYMENT_API, ID: 5663
 * * WANWEIPAY_BANKCARD_PAYMENT_API, ID: 5664
 * * WANWEIPAY_WITHDRAWAL_PAYMENT_API, ID: 5665
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: https://api.247pay.site/api/v1/payin/pay_info
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_wanweipay extends Abstract_payment_api {
    const ORDER_STATUS_SUCCESS  = "2";
    const RETURN_SUCCESS_CODE   = "OK";
    const CHANNELID_BANK        = "8035";
    const CHANNELID_QUICKPAY    = "8034";
    const CHANNELID_OFFLINEBANK = "8033";

    public function __construct($params = null) {
        parent::__construct($params);
    }
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params);

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'application_id');
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
        $params['amount'] = $this->convertAmountToCurrency($amount);
        $params['appId'] = $this->getSystemInfo('application_id');
        $params['depositName'] = $this->getAccName($playerId);
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['currency'] = 'cny';
        $params['mchId'] = $this->getSystemInfo('account');
        $params['mchOrderNo'] = $order->secure_id;
        $params['notifyUrl'] = $this->getNotifyUrl($orderId);
        $params['returnUrl'] = $this->getReturnUrl($orderId);
        $params['version'] = '1.0';
        $params['sign'] = $this->sign($params);

        $this->CI->utils->debug_log('=====================wanweipay generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    protected function processPaymentUrlFormPost($params) {
        return array(
            'success' => true,
            'type' => self::REDIRECT_TYPE_FORM,
            'url' => $this->getSystemInfo('url'),
            'params' => $params,
            'post' => true,
        );
    }

    public function callbackFromServer($orderId, $params) {
        $response_result_id = parent::callbackFromServer($orderId, $params);
        return $this->callbackFrom('server', $orderId, $params, $response_result_id);
    }

    ## This will be called when user redirects back to our page from payment API
    public function callbackFromBrowser($orderId, $params) {
        $response_result_id = parent::callbackFromBrowser($orderId, $params);
        return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
    }

    # $source can be 'server' or 'browser'
    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        $this->CI->utils->debug_log("=====================wanweipay callbackFrom $source params", $params);

        if(empty($params)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $this->CI->utils->debug_log("=====================wanweipay raw_post_data", $raw_post_data);
            $params = json_decode($raw_post_data,true);
            $this->CI->utils->debug_log("=====================wanweipay json_decode params", $params);
        }

        if($source == 'server' ){
            if (!$order || !$this->checkCallbackOrder($order, $params, $processed)) {
                return $result;
            }
        }

        $success = true;

        $orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderId);
        if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
            $this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
            if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
                $this->CI->sale_order->setStatusToSettled($orderId);
            }
        } else {
            # update player balance
            $this->CI->sale_order->updateExternalInfo($order->id, $params['payOrderId'], '', null, null, $response_result_id);
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
            'amount','mchOrderNo','payOrderId','status','sign'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================wanweipay Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================wanweipay Signature Error', $fields);
            return false;
        }

        if ($fields['status'] != self::ORDER_STATUS_SUCCESS) {
            $this->writePaymentErrorLog('=====================wanweipay Payment was not successful', $fields);
            return false;
        }

        $check_amount = $this->convertAmountToCurrency($order->amount);

        if ($fields['amount'] != $check_amount) {
            $this->writePaymentErrorLog("======================wanweipay Payment amount is wrong, expected <= ". $check_amount, $fields);
            return false;
        }

        if ($fields['mchOrderNo'] != $order->secure_id) {
            $this->writePaymentErrorLog("=====================wanweipay checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            return false;
        }


        $processed = true; # processed is set to true once the signature verification pass

        # everything checked ok
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    # -- signatures --
    # Reference: PHP Demo
    public function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = strtoupper(md5($signStr));
        return $sign;
    }

    public function createSignStr($params) {
        $signStr = '';
        ksort($params);
        foreach($params as $key => $value) {
            if( $key == 'sign' ) {
                continue;
            }
            $signStr .= "$key=$value&";
        }
        $signStr .= "key=".$this->getSystemInfo('key');
        return $signStr;
    }

    public function validateSign($params) {
        $sign = $this->sign($params);
        if($params['sign'] == $sign)
            return true;
        else
            return false;
    }

    # -- Private functions --
    # After payment is complete, the gateway will invoke this URL asynchronously
    public function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## After payment is complete, the gateway will send redirect back to this URL
    public function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## Format the amount value for the API
    protected function convertAmountToCurrency($amount) {
        return number_format($amount * 100, 0, '.', '');
    }

    # Config in extra_info will overwrite this
    public function getBankListInfoFallback() {
        return array(
            array('label' => '中信银行', 'value' => 'CITIC'),
            array('label' => '中国兴业银行', 'value' => 'CIB'),
            array('label' => '广发银行', 'value' => 'CGB'),
            array('label' => '招商银行', 'value' => 'CMB'),
            array('label' => '中国邮政储蓄银行', 'value' => 'PSBC'),
            array('label' => '中国银行', 'value' => 'BOC'),
            array('label' => '中国农业银行', 'value' => 'ABC'),
            array('label' => '中国光大银行', 'value' => 'CEB'),
            array('label' => '中国建设银行', 'value' => 'CCB'),
            array('label' => '平安银行', 'value' => 'PAB'),
            array('label' => '交通银行', 'value' => 'BCOM'),
            array('label' => '中国工商银行', 'value' => 'ICBC'),
            array('label' => '浦发银行', 'value' => 'SPDB'),
            array('label' => '中国民生银行', 'value' => 'CMBC'),
        );
    }

    public function getAccName($playerId){
        $this->CI->load->model(array('player_model'));
        $playerDetails = $this->CI->player_model->getPlayerDetails($playerId);
        if(!empty($playerDetails[0]['firstName']) && !empty($playerDetails[0]['lastName'])){
                $accName = $playerDetails[0]['firstName'].$playerDetails[0]['lastName'];
            }
        return $accName;
    }
}

