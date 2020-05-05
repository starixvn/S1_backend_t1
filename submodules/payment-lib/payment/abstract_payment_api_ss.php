<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * SS
 *
 * * SS_BANKCARD_PAYMENT_API, ID: 5710
 * * SS_ALIPAY_BANKCARD_PAYMENT_API, ID: 5711
 * * SS_WITHDRAWAL_PAYMENT_API, ID: 5712
 *
 * Required Fields:
 * * URL
 * * Account
 * * Key
 *
 * Field Values:
 * * URL: http://43.242.33.147:81/SS/api/apply/deposit
 * * Account: ## User ID ##
 * * Key: ## Secret Key ##
 *
 *
 * @category Payment
 * @copyright 2013-2018 TripleOneTech
 */
abstract class Abstract_payment_api_ss extends Abstract_payment_api {
    const ALIPAY_BANKID = '30';
    const RETURN_SUCCESS_CODE = '1';
    const RETURN_ERROR_CODE = '2';

    public function __construct($params = null) {
        parent::__construct($params);
    }
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
        $params['CompanyID'] = $this->getSystemInfo('account');
        $this->configParams($params, $order->direct_pay_extra_info);
        $params['Amount'] = $this->convertAmountToCurrency($amount);
        $params['MerchantOrderNumber'] = $order->secure_id;
        $params['CustomerName'] = $playerId;
        $params['DepositMode'] = '1';
        $params['ClientNotifyUrl'] = $this->getNotifyUrl($orderId);
        $params['Memo'] = 'Memo';
        ksort($params);
        $params['sign'] = $this->sign($params);

        $this->CI->utils->debug_log('=====================ss generatePaymentUrlForm params', $params);

        return $this->processPaymentUrlForm($params);
    }

    protected function handlePaymentFormResponse($params) {

        $response = $this->submitPostForm($this->getSystemInfo('url'), $params, false);
        $response = json_decode($response, true);
        $this->CI->utils->debug_log('=====================ss processPaymentUrlFormPost response', $response);

        if(isset($response['Status'])){
            if($response['Status'] == '1'){
                $data = array();
                $data['cashier.68']   = $response['AccountName'];
                $data['cashier.69']   = $response['AccountNumber'];
                $data['Beneficiary Bank']   = $this->getBankName($response['CollectingBankID']);
                $data['Beneficiary Bank Address']   = $response['IssuingBankAddress'];
                $data['Amount']   = $response['Amount'];
                $data['xpj.promohistory.applydate']   = $response['TransactionTime'];
                $data['Remarks']   = $response['Remarks'];
                $collection_text_transfer = '';
                $collection_text = $this->getSystemInfo("collection_text_transfer", array(''));
                if(is_array($collection_text)){
                    $collection_text_transfer = $collection_text;
                }
                $is_not_display_recharge_instructions = $this->getSystemInfo('is_not_display_recharge_instructions');

                return array(
                    'success' => true,
                    'type' => self::REDIRECT_TYPE_STATIC,
                    'data' => $data,
                    'hide_timeout' => true,
                    'collection_text_transfer' => $collection_text_transfer,
                    'is_not_display_recharge_instructions' => $is_not_display_recharge_instructions
                );
            }
            else{

                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                    'message' => $response['ErrorMessage'],
                );

            }
        }
        else{
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => lang('Invalidate API response')
            );
        }
    }

    # Submit POST form
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

        $this->CI->utils->debug_log("=====================ss callbackFrom $source params", $params);

        if($source == 'server' ){
            $raw_post_data = file_get_contents('php://input', 'r');
            $this->CI->utils->debug_log("=====================ss raw_post_data", $raw_post_data);
            $params = json_decode($raw_post_data,true);
            $this->CI->utils->debug_log("=====================ss json_decode params", $params);
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
            $this->CI->sale_order->updateExternalInfo($order->id, $params['MerchantOrderNumber'], '', null, null, $response_result_id);
            if ($source == 'browser') {
                $success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
            } elseif ($source == 'server') {
                $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
            }
        }

        $result['success'] = $success;
        if ($processed) {
            $returnSuccess = [
                'OrderNumber' => $params['OrderNumber'],
                'MerchantOrderNumber' => $params['MerchantOrderNumber'],
                'Status' => self::RETURN_SUCCESS_CODE
            ];
            $result['message'] = json_encode($returnSuccess);
        } else {
            $returnError = [
                'OrderNumber' => $params['OrderNumber'],
                'MerchantOrderNumber' => $params['MerchantOrderNumber'],
                'Status' => self::RETURN_ERROR_CODE,
                'ErrorMessage' => 'Error'
            ];
            $result['return_error'] = json_encode($returnError);
        }

        if ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return $result;
    }

    private function checkCallbackOrder($order, $fields, &$processed = false) {

        $requiredFields = array(
            'MerchantOrderNumber','Amount','sign'
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================ss Missing parameter: [$f]", $fields);
                return false;
            }
        }

        # is signature authentic?
        if (!$this->validateSign($fields)) {
            $this->writePaymentErrorLog('=====================ss Signature Error', $fields);
            return false;
        }

        if ($fields['Amount'] != $this->convertAmountToCurrency($order->amount)) {
            #because player need to enter amount at Alipay
            if($this->getSystemInfo('allow_callback_amount_diff')){
                $this->CI->utils->debug_log('=====================ss amount not match expected [$order->amount]');
                $notes = $order->notes . " | callback diff amount, origin was: " . $order->amount;
                $this->CI->sale_order->fixOrderAmount($order->id, $fields['money_true'], $notes);
            }
            else{
                $this->writePaymentErrorLog("=====================ss Payment amounts do not match, expected [$order->amount]", $fields);
                return false;
            }
        }

        if ($fields['MerchantOrderNumber'] != $order->secure_id) {
            $this->writePaymentErrorLog("=====================ss checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
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
    private function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);
        return $sign;
    }

    private function createSignStr($params) {
        $signStr = md5($this->getSystemInfo('key'));
        ksort($params);
        foreach($params as $key => $value) {
            if($key == 'sign') {
                continue;
            }else if($key == 'TerminalID'){
                $signStr .= "$key=$value";
            }else{
                $signStr .= "$key=$value&";
            }
        }
        return $signStr;
    }

    private function validateSign($params) {
        $signStr = md5($this->getSystemInfo('key'));
        $signStr .= $params['TransactionTime'].$params['BankID'].$params['Amount'].$params['MerchantOrderNumber'].
            $params['OrderNumber'].$params['PayAccountNumber'].$params['PayAccountName'].$params['Channel'].
            $params['Channel'].$params['Area'].$params['Fee'].$params['BusinessServiceFee'].$params['DepositMode'];
        $sign = md5($signStr);

        if($params['sign'] == $sign)
            return true;
        else
            return false;
    }

    # -- Private functions --
    # After payment is complete, the gateway will invoke this URL asynchronously
    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## After payment is complete, the gateway will send redirect back to this URL
    private function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## Format the amount value for the API
    protected function convertAmountToCurrency($amount) {
        return number_format($amount, 2, '.', '');
    }

    # Config in extra_info will overwrite this
    public function getBankListInfoFallback() {
        return array(
            array('label' => '中國工商銀行', 'value' => '1'),
            array('label' => '招商银行', 'value' => '2'),
            array('label' => '中國建設銀行', 'value' => '3'),
            array('label' => '农业银行', 'value' => '4'),
            array('label' => '中国银行', 'value' => '5'),
            array('label' => '交通银行', 'value' => '6'),
            array('label' => '中信银行', 'value' => '8'),
            array('label' => '浦发银行', 'value' => '9'),
            array('label' => '邮政银行', 'value' => '10'),
            array('label' => '中国光大银行', 'value' => '11'),
            array('label' => '平安银行', 'value' => '12'),
            array('label' => '广发银行', 'value' => '13'),
            array('label' => '华夏银行', 'value' => '14'),
            array('label' => '兴业银行', 'value' => '15'),
        );
    }

    private function getBankName($bankCode){
        $bankList = $this->getBankListInfo();
        foreach($bankList as $aBankArray){
            if(strtoupper($bankCode) == $aBankArray['value']) {
                return $aBankArray['label'];
            }
        }
    }

}

