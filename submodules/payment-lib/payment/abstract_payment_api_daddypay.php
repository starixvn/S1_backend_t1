<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * DaddyPay
 *
 * DADDYPAY_BANKCARD_PAYMENT_API, ID: 114
 * DADDYPAY_3RDPARTY_PAYMENT_API, ID: 115
 * DADDYPAY_QRCODE_PAYMENT_API, ID: 116
 * DADDYPAY_WITHDRAWAL_PAYMENT_API, ID: 126
 * DADDYPAY_WECHAT_PAYMENT_API, ID: 131
 * DADDYPAY_ALIPAY_PAYMENT_API, ID: 132
 * DADDYPAY_QQCARD_PAYMENT_API, ID: 233
 * DADDYPAY_ALIPAY_BANKCARD_PAYMENT_API, ID: 634
 * DADDYPAY_QUICKPAY_PAYMENT_API, ID: 777
 * Required Fields:
 *
 * * URL
 * * Key
 * * Extra Info
 *
 *
 * Field Values:
 *
 * * URL (sandbox): http://52.69.65.224/Mownecum_2_API_Live/Deposit?format=json
 * * Extra Info
 * > {
 * >     "daddypay_company_id" : "## company id ##"
 * > }
 *
 * @category Payment
 * @copyright 2013-2016 TripleOneTech
 */
abstract class Abstract_payment_api_daddypay extends Abstract_payment_api {
    # 1银行卡 2第三方、微信 3移动电子钱包（支付宝二维码）
    const DEPOSIT_MODE_BANKCARD = 1;
    const DEPOSIT_MODE_3RDPARTY = 2;
    const DEPOSIT_MODE_QRCODE = 3;
    const DEPOSIT_MODE_ALIPAY = 6;
    const NOTE_MODEL_PLATFORM = 1;
    const NOTE_MODEL_DP = 2;
    const TERMINAL_PC = 1;
    const TERMINAL_MOBILE = 2;
    const TERMINAL_TABLET = 3;

    const BANKID_ALIPAY_BANKCARD = '30'; //支付宝轉网银
    const BANKID_QUICKPAY = '51'; //银聯快捷

    //collection bank id to player bank id
    const BANK_INCLUDE_EMAIL=[1=>1];

    const WITHDRAWAL_RESULT_CODE_SUCCESS='1';

    public $bank_include_email;
    public $accept_diff_deposit_amount;
    protected abstract function configParams(&$params, $direct_pay_extra_info);
    public function __construct($params = null) {
        parent::__construct($params);

        $this->bank_include_email=$this->getSystemInfo('bank_include_email', self::BANK_INCLUDE_EMAIL);

        $this->accept_diff_deposit_amount=$this->getSystemInfo('accept_diff_deposit_amount', true);
    }

    public function getDepositMode(){
        return null;
    }

    public function getNoteModel($bankId){
        return null;
    }

    public function getNote($player_id, $direct_pay_extra_info){
        # overwritten in qrcode implementation
        return '';
    }

    protected function getBankId($direct_pay_extra_info) {
        # overwritten in qrcode implementation
        if (!empty($direct_pay_extra_info)) {
            $extraInfo = json_decode($direct_pay_extra_info, true);
            if (!empty($extraInfo) && array_key_exists('bank', $extraInfo)) {
                return $extraInfo['bank'];
            }
        }
    }

    public function handlePaymentFormResponse($resp, $params){
        return array(
            'success' => false,
            'type' => self::REDIRECT_TYPE_ERROR,
            'message' => lang('Invalidate response'),
        );
    }

    public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
        if ($this->shouldRedirect($enabledSecondUrl)) {
            $url = $this->CI->utils->getPaymentUrl($this->getSystemInfo('second_url'), $this->getPlatformCode(), $amount, $playerId, $playerPromoId, false, $bankId, $orderId);
            $result = array('success' => true, 'type' => self::REDIRECT_TYPE_URL, 'url' => $url);
            return $result;
        }

        $params = array();
        if($this->getSystemInfo("daddypay_company_id")) {
            $params['company_id'] = $this->getSystemInfo("daddypay_company_id");
        }
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $this->CI->utils->debug_log("==================================daddypay order extra", $order->direct_pay_extra_info);

        // if bank_id = 30, it's alipay
        $params['bank_id']                = $this->getBankId($order->direct_pay_extra_info);
        $params['amount']                 = $this->convertAmountToCurrency($amount);
        $params['company_order_num']      = $order->secure_id;
        $params['company_user']           = $this->getSystemInfo("daddypay_company_id");
        $params['estimated_payment_bank'] = $params['bank_id'];
        $params['deposit_mode']           = $this->getDepositMode(); # Defined by implementing class
        $params['group_id']               = 0;
        $params['web_url']                = $this->getNotifyUrl($orderId);
        $params['memo']                   = $this->getSystemInfo("daddypay_company_id");
        $params['note']                   = $this->getNote($playerId, $order->direct_pay_extra_info);
        if($this->getSystemInfo("use_order_num_as_note")){
            $params['note'] = substr($params['company_order_num'],-5);
        }
        if($this->getSystemInfo("note")) {
            $params['note'] = $this->getSystemInfo("note");
        }
        $note_model =  $this->getNoteModel($params['bank_id']);
        $params['note_model'] = $this->getSystemInfo("note_model",$note_model);

        # Defined by implementing class
        $this->configParams($params, $order->direct_pay_extra_info);

        $params['key'] = $this->sign($params);

        $data = array(
            'company_id' 			 => $params['company_id'],
            'bank_id' 				 => $params['bank_id'],
            'amount' 				 => $params['amount'],
            'company_order_num' 	 => $params['company_order_num'],
            'company_user' 			 => $params['company_user'],
            'key' 					 => $params['key'],
            'estimated_payment_bank' => $params['estimated_payment_bank'],
            'deposit_mode' 			 => $params['deposit_mode'],
            'group_id' 				 => $params['group_id'],
            'web_url' 				 => $params['web_url'],
            'memo' 					 => $params['memo'],
            'note' 					 => $params['note'],
            'note_model' 			 => $params['note_model'],
            'terminal' 				 => $params['terminal']
        );

        if($this->getSystemInfo('post_json')===true){
            $this->_custom_curl_header = array('Content-Type: application/json');
            $jsonResultRaw = $this->submitPostForm($this->getSystemInfo('url'), $data, true, $params['company_order_num']);
        }else{
            $jsonResultRaw = $this->submitGetForm($this->getSystemInfo('url'), $data, false, $params['company_order_num']);
        }

        $this->CI->utils->debug_log("==================================daddypay generatePaymentUrlForm params", $params);
        $this->CI->utils->debug_log("==================================daddypay generatePaymentUrlForm submitGetForm and get return", $jsonResultRaw);

        if(empty($jsonResultRaw)) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => lang('Invalid API response'),
            );
        }
        $resp = $this->CI->utils->decodeJson($jsonResultRaw);
        $this->CI->utils->debug_log("==================================daddypay generatePaymentUrlForm decodeJson resp", $resp);
        //check status first
        if(empty($resp) || $resp['status']=='0'){
            //wrong status
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => lang('Original error message').': '.lang($resp['error_msg']),
            );
        }

        # validate signature
        if($this->signResp($resp) !=$resp['key']) {
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR,
                'message' => lang('Invalid Signature'),
            );
        }

        # $resp is now an array containing key-values. define a few keys to check for.
        $compareKeys = array('amount', 'company_order_num');
        $respValid = true;
        foreach($compareKeys as $key) {
            if($resp[$key] != $params[$key]) {
                $this->utils->error_log("Response error in [$key]: expected [$params[$key]], found [$resp[$key]]");

                return array(
                    'success' => false,
                    'type' => self::REDIRECT_TYPE_ERROR,
                    'message' => lang('Invalid response'). " Response error in [$key]: expected [$params[$key]], found [$resp[$key]]",
                );
            }
        }

        return $this->handlePaymentFormResponse($resp, $params);
    }

    public function callbackFromServer($orderId, $params) {
        $response_result_id = parent::callbackFromServer($orderId, $params);

        if(@$params['type']=='requestWithdrawApproveInformation'){
            $this->CI->utils->debug_log('process requestWithdrawApproveInformation order id', $orderId);
            $sign=$this->signWithdrawalApprove($params);

            if($sign!=$params['key']){
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'signature failed'])];
                return $result;
            }

            //load orderId from walletaccount
            $walletAccount=$this->CI->wallet_model->getWalletAccountByTransactionCode($params['company_order_num']);

            if($walletAccount['amount']!=$params['amount']){
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'amount is not right'])];
                return $result;
            }

            $status='';
            //check status first
            if(!$this->CI->wallet_model->isAvailableWithdrawal($walletAccount['walletAccountId'], $status)){
                $this->CI->utils->debug_log('isAvailableWithdrawal '.$walletAccount['walletAccountId'], $walletAccount['transactionCode'], $status);
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'withdrawal is not available, status is '.$status])];
                return $result;
            }

            $resultContent=[
                'mownecum_order_num'=>$params['mownecum_order_num'],
                'company_order_num'=>$params['company_order_num'],
                'status'=>4,
            ];

            $result['success']=true;

            $result['message'] = $result['return_error'] = json_encode($resultContent);

            return $result;

        }elseif(@$params['type']=='withdrawalResult'){
            $this->CI->utils->debug_log('process withdrawalResult order id', $orderId);
            $sign=$this->signWithdrawalResult($params);

            if($sign!=$params['key']){
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'signature failed'])];
                return $result;
            }

            if($orderId!=$params['company_order_num']){
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'wrong order id'])];
                return $result;
            }

            $walletAccount=$this->CI->wallet_model->getWalletAccountByTransactionCode($params['company_order_num']);

            if($walletAccount['amount']<$params['amount']){
                $result=['success'=>false, 'return_error'=> json_encode(['status'=>0,'error_msg'=>'wrong amount , must <= '.$walletAccount['amount']])];
                return $result;
            }

            $result['success'] = true;

            $this->CI->load->model(['wallet_model']);
            if($params['status']=='1' || $params['status']=='2'){

                $reason= isset($params['error_msg']) ? $params['error_msg'] : @$params['detail'];
                $reason = empty($reason) ? 'Success' : $reason;

                $fee=@$params['exact_transaction_charge'];
                $amount=null;
                if($params['status']=='2'){
                    $amount=$params['amount'];
                }

                $this->CI->wallet_model->withdrawalAPIReturnSuccess($orderId, $reason, $fee, $amount);

                $resultContent=[
                    'mownecum_order_num'=>$params['mownecum_order_num'],
                    'company_order_num'=>$params['company_order_num'],
                    'status'=>1,
                ];

                $result['message'] = $result['return_error'] = json_encode($resultContent);

            }else{

                $reason= isset($params['error_msg']) ? $params['error_msg'] : @$params['detail'];
                $reason = empty($reason) ? 'Fail' : $reason;

                $this->CI->wallet_model->withdrawalAPIReturnFailure($orderId, $reason);

                $resultContent=[
                    'mownecum_order_num'=>$params['mownecum_order_num'],
                    'company_order_num'=>$params['company_order_num'],
                    'status'=>1,
                ];

                $result['message'] = $result['return_error'] = json_encode($resultContent);
            }
            return $result;
        }

        return $this->callbackFrom('server', $orderId, $params, $response_result_id);
    }

    ## This will be called when user redirects back to our page from payment API
    public function callbackFromBrowser($orderId, $params) {
        $this->CI->utils->error_log("Error: browser callback not supported");
        return;
    }

    //============exception order=========================
    public function validateExceptionCallback($params) {

        $result=['success' => false, 'return_error' => 'FAILED'];

        //MD5(MD5(config)+exception_order_num+company_id+exact_payment_bank+pay_card_name+pay_card_num+receiving_bank+receiving_account_name+channel+note+area+exact_time+amount+fee+transaction_charge)
        //validate callback content
        $requiredFields = array(
            'exception_order_num', 'company_id', 'receiving_bank', 'receiving_account_name', 'exact_time', 'amount', 'transaction_charge', 'key',
        );

        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $params)) {
                $this->writePaymentErrorLog("Missing parameter: [$f]", $params);

                // $resultContent=['status'=> 0 , 'error_msg'=>"Missing parameter"];
                $result['success'] = false;
                $result['return_error'] = "Missing parameter";

                return $result;
            }
        }

        $key = $this->getSystemInfo('key');
        $signStr = md5($key);
        $dataKeys = array("exception_order_num", "company_id", "exact_payment_bank", "pay_card_name",
            "pay_card_num", "receiving_bank", "receiving_account_name", "channel", "note", "area",
            "exact_time", "amount", "fee", "transaction_charge");
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $params)) {
                $signStr .= $params[$dataKey];
            }
        }

        $md5_sign=md5($signStr);

        $validateSign=$params['key'] == $md5_sign;
        $this->utils->debug_log("Validation sign string and params ", $signStr, $md5_sign, 'result', $result, $params);

        # is signature valid?
        if(!$validateSign) {
            $this->writePaymentErrorLog("Signature validation failure", $params);

            $result['success'] = false;
            $result['return_error'] = "Signature validation failure";

            return $result;
        }

        # all checks done
        $processed = true;

        //write to exception deposit
        $result=['success' => true];

        return $result;
    }

    public function generateInfoForException($params){
        return ['amount'=>$params['amount'], 'external_order_id'=>$params['exception_order_num'],
            'external_order_datetime'=>$this->convertApiDateTimeTo($params['exact_time']),
            'player_bank_name'=>$params['exact_payment_bank'], 'player_bank_account_name'=>$params['pay_card_name'], 'player_bank_account_number'=>$params['pay_card_num'], 'player_bank_address'=>$params['area'],
            'collection_bank_name'=>$params['receiving_bank'], 'collection_bank_account_name'=>$params['receiving_account_name'], 'collection_bank_account_number'=>null, 'collection_bank_address'=>null,
        ];
    }

    public function generateResultContentForException($params){

        $resultContent=['status'=> 1, 'exception_order_num'=> $params['exception_order_num']];

        $result=['success' => true, 'message'=> json_encode($resultContent)];

        return $result;
    }
    //============exception order=========================

    # $source can be 'server' or 'browser'
    private function callbackFrom($source, $orderId, $params, $response_result_id) {
        $result = array('success' => false, 'next_url' => null, 'message' => lang('error.payment.failed'));
        $order = $this->CI->sale_order->getSaleOrderById($orderId);
        $processed = false;

        if(empty($order)){
            return array('success' => false, 'return_error' => lang('Not found order'));
        }

        # Decrypt callback info
        if (!$this->checkCallbackOrder($order, $params, $processed, $message)) {
            $respParams = array();
            $respParams['company_order_num'] = $order->secure_id;
            $respParams['mownecum_order_num'] = $params['mownecum_order_num'];

            $respParams['status'] = 1;
            $respParams['error_msg'] = '';

            return array('success' => false, 'return_error' => $processed ? json_encode($respParams) : json_encode(['status'=>0, 'error_msg'=>$message]));
        }

        # Update player balance based on order status
        # if it's STATUS_SETTLED or STATUS_BROWSER_CALLBACK, put log, and ignore
        $orderStatus = $this->CI->sale_order->getSaleOrderStatusById($orderId);
        if ($orderStatus == Sale_order::STATUS_BROWSER_CALLBACK || $orderStatus == Sale_order::STATUS_SETTLED) {
            $this->CI->utils->debug_log('callbackFrom' . ucfirst($source) . ', already get callback for order:' . $order->id, $params);
            if ($order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
                $this->CI->sale_order->setStatusToSettled($orderId);
            }
        } else {
            # OGP-6652 : avoid changing order's amount from fake callback params and changing order's amount only at first callback
            $amount=floatval($params['amount']);
            if (
                $this->convertAmountToCurrency($order->amount) !=
                $this->convertAmountToCurrency($amount)
                && $this->accept_diff_deposit_amount
            ) {
                //update sale order number

                $notes = $order->notes . " diff amount, old amount is " . $order->amount;
                $success = $this->CI->sale_order->fixOrderAmount($order->id, $amount, $notes);

                if(!$success){

                    $respParams = array();
                    $respParams['status'] = 0;
                    $respParams['error_msg'] = 'Internet Error, change amount failed';

                    return ['success' => false, 'return_error' => json_encode($respParams)];

                }

            }

            # update player balance
            $this->CI->sale_order->updateExternalInfo($order->id,
                $params['mownecum_order_num'], $params['base_info'],
                null, null, $response_result_id);
            $this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
        }

        $result['success'] = true;

        # success message is a json string. Ref: Documentation section 4.2
        $respParams = array();
        $respParams['company_order_num'] = $order->secure_id;
        $respParams['mownecum_order_num'] = $params['mownecum_order_num'];

        if ($result['success']) {
            $respParams['status'] = 1;
            $respParams['error_msg'] = '';
        } else {
            $respParams['status'] = ($processed ? 1 : 0);
            $respParams['error_msg'] = ($respParams['status'] == 0 ? $message : '');
        }

        $result['message'] = $result['return_error'] = json_encode($respParams);

        return $result;
    }

    private function checkCallbackOrder($order, $fields, &$processed = false, &$message = '') {
        # does all required fields exist?
        $requiredFields = array(
            'amount', 'company_order_num', 'key'
        );
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("Missing parameter: [$f]", $fields);
                $message = "Missing parameter: [$f]";
                return false;
            }
        }

        # does order_no match?
        if ($fields['company_order_num'] != $order->secure_id) {
            $this->writePaymentErrorLog("Order IDs do not match, expected [$order->secure_id]", $fields);
            $message = "Order IDs do not match, expected [$order->secure_id]";
            return false;
        }

        # is signature valid?
        if(!$this->validate($fields)) {
            $this->writePaymentErrorLog("Signature validation failure", $fields);
            $message = "Signature validation failure";
            return false;
        }

        # does amount match?
        if (
            $this->convertAmountToCurrency($order->amount) !=
            $this->convertAmountToCurrency(floatval($fields['amount']))
        ) {
            $processed=true;

            if($this->accept_diff_deposit_amount){

                $this->CI->utils->debug_log('accept_diff_deposit_amount', $order->amount,  $fields);

            }else{

                $this->CI->load->model(['sale_order']);
                $external_system_id=$this->getPlatformCode();
                $amount=$fields['amount'];
                $external_order_id=$fields['mownecum_order_num'];
                $external_order_datetime=$this->convertApiDateTimeTo($fields['operating_time']);
                $player_bank_name=@$fields['player_bank_name'];
                $player_bank_account_name=@$fields['pay_card_name'];
                $player_bank_account_number=@$fields['pay_card_num'];
                $player_bank_address=@$fields['area'];
                $collection_bank_name=''; //$fields['collection_bank_name'];
                $collection_bank_account_name=''; //$fields['collection_bank_account_name'];
                $collection_bank_account_number=''; //$fields['collection_bank_account_number'];
                $collection_bank_address=''; //$fields['collection_bank_address'];
                //write to exception order
                $exception_order_id=$this->CI->sale_order->createExceptionDeposit($external_system_id, $amount, $external_order_id, $external_order_datetime, $response_result_id,
                    $player_bank_name, $player_bank_account_name, $player_bank_account_number, $player_bank_address,
                    $collection_bank_name, $collection_bank_account_name, $collection_bank_account_number, $collection_bank_address,
                    $params, $order->id);

                $this->writePaymentErrorLog("Payment amounts do not match, save to ".$exception_order_id." expected [$order->amount]", $fields);
                $message = "Payment amounts do not match, expected [$order->amount]";
                return false;

            }

        }

        # all checks done
        $processed = true;
        return true;
    }

    public function directPay($order = null) {
        return array('success' => false); # direct pay not supported by this API
    }

    public function getBankListInfoFallback() {
        return array(
            array('label' => '中国工商银行', 'value' => 1),
            array('label' => '招商银行', 'value' => 2),
            array('label' => '中国建设银行', 'value' => 3),
            array('label' => '中国农业银行', 'value' => 4),
            array('label' => '中国银行', 'value' => 5),
            array('label' => '交通银行', 'value' => 6),
            array('label' => '中国民生银行', 'value' => 7),
            array('label' => '中信银行', 'value' => 8),
            array('label' => '上海浦东发展银行', 'value' => 9),
            array('label' => '邮政储汇', 'value' => 10),
            array('label' => '中国光大银行', 'value' => 11),
            array('label' => '平安银行', 'value' => 12),
            array('label' => '广发银行股份有限公司', 'value' => 13),
            array('label' => '华夏银行', 'value' => 14),
            array('label' => '福建兴业银行', 'value' => 15),
            array('label' => '支付宝', 'value' => 30),
            array('label' => '财付通', 'value' => 31),
            array('label' => '微信支付（二维码）', 'value' => 40),
        );
    }

    # -- Private functions --
    protected function getTerminal() {
        if($this->utils->is_mobile()) {
            return self::TERMINAL_MOBILE;
        }
        return self::TERMINAL_PC;
    }

    ## After payment is complete, the gateway will invoke this URL asynchronously
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

    public function sign($data) {
        $key = $this->getSystemInfo('key');
        $signStr = md5($key);
        $dataKeys = array('company_id', 'bank_id', 'amount', 'company_order_num', 'company_user', 'estimated_payment_bank', 'deposit_mode', 'group_id', 'web_url', 'memo', 'note', 'note_model');
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $data)) {
                $signStr .= $data[$dataKey];
            }
        }
        $this->utils->debug_log("Sign string: ", $signStr);
        return md5($signStr);
    }

    /**
     *
     * MD5(MD5(config)+bank_card_num+bank_acc_name+amount+email+company_order_num+datetime+note+mownecum_order_num+status+error_msg+mode+issuing_bank_address+break_url+deposit_mode+collection_bank_id)
     *
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function signResp($data) {
        $key = $this->getSystemInfo('key');
        $signStr = md5($key);
        $dataKeys = array('bank_card_num', 'bank_acc_name', 'amount', 'email', 'company_order_num', 'datetime', 'note', 'mownecum_order_num', 'status', 'error_msg', 'mode', 'issuing_bank_address', 'break_url', 'deposit_mode', 'collection_bank_id');
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $data)) {
                if($dataKey == 'amount' && $this->getSystemInfo('post_json')){
                    $signStr .= $this->convertAmountToCurrency($data[$dataKey]);
                }
                else{
                    $signStr .= $data[$dataKey];
                }
            }
        }
        return md5($signStr);
    }

    public function validate($data) {
        $key = $this->getSystemInfo('key');
        $signStr = md5($key);
        $dataKeys = array("pay_time", "bank_id", "amount", "company_order_num", "mownecum_order_num", "pay_card_num", "pay_card_name", "channel", "area", "fee", "transaction_charge", "deposit_mode");
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $data)) {
                $signStr .= $data[$dataKey];
            }
        }

        $result=$data['key'] == md5($signStr);
        return $result;
    }

    public function getOrderIdFromParameters($flds) {
        if($flds['type']=='withdrawalResult' || $flds['type']=='requestWithdrawApproveInformation'){
            return @$flds['company_order_num'];
        }else{
            $orderId = null;
            //for fixed return url on browser
            if (isset($flds['company_order_num'])) {
                $secure_id = $flds['company_order_num'];

                $this->CI->load->model(array('sale_order'));
                $order = $this->CI->sale_order->getSaleOrderBySecureId($secure_id);
                if(!empty($order)){
                    $orderId = $order->id;
                }
            }

            return $orderId;
        }
    }

    //===withdrawal=====================================


    public function convertApiDateTimeTo($datetime){
        if(!empty($datetime)){
            $d=DateTime::createFromFormat('YmdHis', $datetime);

            return $this->CI->utils->formatDateTimeForMysql($d);
        }

        return '';
    }

    public function getPlayerDetails($playerId) {
        $this->CI->load->model(array('player_model'));
        $player = $this->CI->player_model->getPlayerDetails($playerId);

        return $player;
    }

}

