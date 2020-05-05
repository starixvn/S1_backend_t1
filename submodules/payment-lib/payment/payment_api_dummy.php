<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * Dummy payment API
 */
class Payment_api_dummy extends Abstract_payment_api{

    const CASE_STAY = 'stay_processing';
    const CASE_SUCCESS = 'to_paid';
    const CASE_FAIL = 'to_declined';

    public function __construct($params = NULL){
        parent::__construct($params);

        # Populate $info with the following keys
        # url, key, account, secret, system_info
        $this->info = $this->getInfoByEnv();
    }

    # -- implementation of abstract functions --
    public function getPlatformCode(){
        return DUMMY_PAYMENT_API;
    }

    public function getPrefix(){
        return 'dummy';
    }

    # -- override common API functions --
    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'test_private_key', 'test_public_key');
        return $secretsInfo;
    }

    ## Constructs an URL so that the caller can redirect / invoke it to make payment through this API
    ## See controllers/redirect.php for detail.
    ##
    ## Retuns a hash containing these fields:
    ## array(
    ##	'success' => true,
    ##	'type' => self::REDIRECT_TYPE_FORM,  ## constants defined in abstract_payment_api.php
    ##	'url' => $info['url'],
    ##	'params' => $params,
    ##	'post' => true
    ## );
    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = NULL, $enabledSecondUrl = TRUE, $bankId = NULL){
        $this->utils->debug_log("Invoked in dummy payment API", $orderId, $playerId, $amount, $orderDateTime, $playerPromoId, $enabledSecondUrl, $bankId);

        if (strpos($this->utils->getRuntimeEnv(), 'local') === false) {
            return array('success' => false, 'type' => Abstract_payment_api::REDIRECT_TYPE_ERROR, 'message' => 'unimplemented');
        }

        $order = $this->CI->sale_order->getSaleOrderById($orderId);

        $data = array();
        $data['browser_callback_url'] = $this->getOrderBrowserCallbackUrl($orderId);
        $data['server_callback_url'] = $this->getOrderServerCallbackUrl($orderId);

        if ($this->getSystemInfo("show_all", false)) {
            $allInfo = $this->getAllSystemInfo();
            foreach ($allInfo as $key => $value) {
                # add space to avoid lang key match
                $data[' '.$key] = $value;
            }
        }

        $url = $this->getSystemInfo("url");
        $this->submitPreprocess($data, null, $url, null , [], $order->secure_id);


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
            'collection_text_transfer' => $collection_text_transfer,
            'is_not_display_recharge_instructions' => $is_not_display_recharge_instructions
        );
    }

    ## This will be called when the payment is async, API server calls our callback page
    ## When that happens, we perform verifications and necessary database updates to mark the payment as successful
    ## Reference: sample code, callback.php
    public function callbackFromServer($orderId, $params){
        $this->utils->debug_log("Invoked in dummy payment API", $orderId, $params);

        $response_result_id = parent::callbackFromServer($orderId, $params);
        return $this->callbackFrom('server', $orderId, $params, $response_result_id);
    }

    ## This will be called when user redirects back to our page from payment API
    public function callbackFromBrowser($orderId, $params){
        $this->utils->debug_log("Invoked in dummy payment API", $orderId, $params);

        $response_result_id = parent::callbackFromBrowser($orderId, $params);
        return $this->callbackFrom('browser', $orderId, $params, $response_result_id);
    }

    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        if (strpos($this->utils->getRuntimeEnv(), 'local') === false) {
            return $this->returnUnimplemented();
        }

        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));

        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $orderStatus = $order->status;

        if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
            $this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
            if ($source == 'server' && $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
                $this->CI->sale_order->setStatusToSettled($orderId);
            }
        } else {
            $statusCode = isset($params['callback_status_case']) ? $params['callback_status_case'] : self::CASE_SUCCESS;
            $this->CI->sale_order->updateExternalInfo($order->id, $order->id, null, null, null, $response_result_id);

            if ($source == 'browser') {
                $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
                if($statusCode == self::CASE_SUCCESS) {
                    $success = true;
                } elseif($statusCode == self::CASE_FAIL){
                    $success = false;
                }
            } elseif ($source == 'server') {
                # update player balance
                if($statusCode == self::CASE_SUCCESS) {
                    $success = $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
                } elseif($statusCode == self::CASE_FAIL){
                    $success = $this->CI->sale_order->declineSaleOrder($order->id, 'auto server callback declined ' . $this->getPlatformCode(), false);
                }
            }
        }


        $result['success'] = $success;
        if ($source == 'server') {
            $result['message'] = 'SUCCESS';
        } elseif ($source == 'browser') {
            $result['next_url'] = $this->getPlayerBackUrl();
            $result['go_success_page'] = true;
        }

        return  $result;
    }

    public function directPay($order = NULL){
        $this->utils->debug_log("Invoked in dummy payment API", $order);
        return $this->returnUnimplemented();
    }

    public function getPlayerInputInfo(){
        return [
            ['name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'],
        ];
    }
}
