<?php
require_once dirname(__FILE__) . '/abstract_payment_api.php';

/**
 * JBP 聚宝盆
 *
 * JBP_V2_ALIPAY_PAYMENT_API, ID: 5547
 * JBP_V2_ALIPAY_H5_PAYMENT_API, ID: 5548
 * JBP_V2_WEIXIN_PAYMENT_API, ID: 5549
 * JBP_V2_WEIXIN_H5_PAYMENT_API, ID: 5550
 * JBP_V2_UNIONPAY_PAYMENT_API, ID: 5551
 * JBP_V2_UNIONPAY_H5_PAYMENT_API, ID: 5552
 * JBP_V2_WITHDRAWAL_PAYMENT_API, ID: 5574
 * Required Fields:
 *
 * * URL https://api.jbp-pay.com/apply/Deposit
 * * Account - Merchant ID
 * * Key - Secret key
 * * Extra Info
 *
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
abstract class Abstract_payment_api_jbp_v2 extends Abstract_payment_api {
    const DEPOSIT_MODE_3RDPARTY = 2; //第三方

    const TERMINAL_PC = 1;
    const TERMINAL_MOBILE = 2;

    const BANKID_ALIPAY = '30'; //支付宝
    const BANKID_WEIXIN = '40'; //微信
    const BANKID_UNIONPAY = '51'; //银联无卡支付
    const BANKID_UNIONPAY_QR = '58'; //银联云闪付

	const WITHDRAWAL_RESULT_CODE_SUCCESS='1';

    public function __construct($params = null) {
        parent::__construct($params);
        $this->_custom_curl_header = array('Content-Type:application/json');
    }


	# Implement these for specific pay type
	protected abstract function configParams(&$params, $direct_pay_extra_info);
    protected abstract function processPaymentUrlForm($params);

    public function getSecretInfoList() {
        $secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret', 'secret_key_config');
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
        $params['company_id']             = $this->getSystemInfo("account");
        $this->configParams($params, $order->direct_pay_extra_info); //$params['bank_id'] $params['terminal']
        $params['amount']                 = $this->convertAmountToCurrency($amount);
        $params['company_order_num']      = $order->secure_id;
        $params['company_user']           = $playerId;
        $params['estimated_payment_bank'] = $params['bank_id'];
        $params['deposit_mode']           = self::DEPOSIT_MODE_3RDPARTY;
        $params['group_id']               = 0;
        $params['web_url']                = $this->getNotifyUrl($orderId);
        $params['memo']                   = 'Deposit';
        $params['note']                   = 'Deposit';
        $params['key']                    = $this->sign($params);

		$this->CI->utils->debug_log("=====================jbp generatePaymentUrlForm", $params);

		return $this->processPaymentUrlForm($params);
    }

    protected function processPaymentUrlFormRedirect($params) {
    	$url = $this->getSystemInfo('url');
		$response = $this->submitPostForm($url, $params, true, $params['company_order_num']);
		$decode_data = json_decode($response,true);
        $this->CI->utils->debug_log('========================================jbp processPaymentUrlFormRedirect response json to array', $decode_data);


        $msg = lang('Invalidate API response');
		if(isset($decode_data['break_url'])) {
            return array(
                'success' => true,
                'type' => self::REDIRECT_TYPE_URL,
                'url' => $decode_data['break_url'],
            );
        }else {
            if(!empty($decode_data['error_msg'])){
                $msg = $decode_data['status'].":".$decode_data['error_msg'];
            }
            return array(
                'success' => false,
                'type' => self::REDIRECT_TYPE_ERROR, # will be redirected to a view for error display
                'message' => $msg
            );
        }
    }

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

        #For Withdrawal
        $company_order_num = $params['company_order_num'];
        if(substr($company_order_num , 0, 1) == 'W'){
            $result = $this->isWithdrawal($params, $company_order_num);
            $this->CI->utils->debug_log('=======================jbp callbackFrom company_order_num', $company_order_num);
            return $result;
        }

        #For Deposit
        $respParams = array();
        $respParams['company_order_num'] = $order->secure_id;
        $respParams['mownecum_order_num'] = $params['mownecum_order_num'];
        $respParams['status'] = 0;
        $respParams['error_msg'] = '';

        if($source == 'server'){
            $this->CI->utils->debug_log('=======================jbp callbackFromServer server callbackFrom', $params);
            if (!$order || !$this->checkCallbackOrder($order, $params, $processed, $message)) {
				$respParams['error_msg'] = $message;
				$result['return_error'] =  json_encode($respParams);
				return $result;
            }
        }

		# Update order payment status and balance
		$success=true;

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
			$this->CI->sale_order->updateExternalInfo($order->id, $params['mownecum_order_num'], null, null, null, $response_result_id);
			if ($source == 'browser') {
				$success = $this->CI->sale_order->browserCallbackSaleOrder($order->id, 'auto broswer callback ' . $this->getPlatformCode(), false);
			} elseif ($source == 'server') {
				$this->approveSaleOrder($order->id, 'auto server callback ' . $this->getPlatformCode(), false);
			}
		}

		$result['success'] = $success;

        # success message is a json string. Ref: Document section 4.5
		if ($success) {
			$respParams['status'] = 1;
			$result['message'] = json_encode($respParams);
		} else {
            $respParams['status'] = 0;
			$result['return_error'] = json_encode($respParams);
		}


		if ($source == 'browser') {
			$result['next_url'] = $this->getPlayerBackUrl();
			$result['go_success_page'] = true;
		}

		return $result;
	}

    private function checkCallbackOrder($order, $fields, &$processed = false, &$message = '') {
        # does all required fields exist?
        $requiredFields = array(
            'amount', 'company_order_num', 'key'
        );
		foreach ($requiredFields as $f) {
			if (!array_key_exists($f, $fields)) {
                $this->writePaymentErrorLog("=====================jbp missing parameter: [$f]", $fields);
                $message = "Missing parameter: [$f]";
				return false;
			}
		}
		# is signature authentic?
		if (!$this->validateSign($fields)) {
			$this->writePaymentErrorLog('=====================jbp checkCallbackOrder Signature Error', $fields);
            $message = "Signature validation failure";
			return false;
		}

		$processed = true; # processed is set to true once the signature verification pass

		if ($fields['amount'] != $this->convertAmountToCurrency($order->amount)) {
			$this->writePaymentErrorLog("======================jbp checkCallbackOrder Payment amount is wrong, expected [$order->amount]", $fields);
			$message = "Payment amounts do not match, expected [$order->amount]";
            return false;
        }

        if ($fields['company_order_num'] != $order->secure_id) {
            $this->writePaymentErrorLog("========================jbp checkCallbackOrder order IDs do not match, expected [$order->secure_id]", $fields);
            $message = "Order IDs do not match, expected [$order->secure_id]";
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
            array('label' => '福建兴业银行', 'value' => 15)
        );
    }

	# -- amount --
	protected function convertAmountToCurrency($amount) {
		return number_format($amount, 2, '.', '');
	}

    # After payment is complete, the gateway will invoke this URL asynchronously
    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }

    ## After payment is complete, the gateway will send redirect back to this URL
    private function getReturnUrl($orderId) {
        return parent::getCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
    }

    # -- signatures --
    private function sign($params) {
        $signStr = $this->createSignStr($params);
        $sign = md5($signStr);
        return $sign;
    }

    public function createSignStr($params) {
        $signStr = '';
        foreach($params as $key => $value) {
			if( ($key == 'key') || ($key == 'terminal')) {
				continue;
			}
			$signStr .= $value;
        }

        $config = md5($this->getSystemInfo('secret_key_config'));
        $signStr = $config.$signStr;
		return $signStr;
    }

	# -- 驗簽 --
    public function validateSign($params) {
		$data = array(
			'pay_time' => $params['pay_time'],
			'bank_id' => $params['bank_id'],
			'amount' => $params['amount'],
			'company_order_num' => $params['company_order_num'],
			'mownecum_order_num' => $params['mownecum_order_num'],
			'fee' => $params['fee'],
			'transaction_charge' => $params['transaction_charge'],
			'deposit_mode' => $params['deposit_mode']
		);
        $signStr = $this->createSignStr($data);
        $sign = md5($signStr);

		if($params['key'] == $sign){
			return true;
		}
		else{
			return false;
		}
	}

	# Callback URI: /callback/fixed_process/<payment_id>
	public function getOrderIdFromParameters($flds) {
        $this->utils->debug_log('=========================jbp getOrderIdFromParameters flds', $flds);
        if(empty($flds) || is_null($flds)){
            $raw_post_data = file_get_contents('php://input', 'r');
            $flds = json_decode($raw_post_data, true);
        }

		if (isset($flds['company_order_num'])) {
            $order_num = $flds['company_order_num'];

            if(substr($order_num, 0, 1) != 'W'){
                #Deposit
                $this->CI->load->model(array('sale_order'));
                $order = $this->CI->sale_order->getSaleOrderBySecureId($order_num);

                if(is_null($order)){
                    $this->utils->debug_log('=====================jbp getOrderIdFromParameters cannot find order by txid', $flds);
                    return;
                }
                return $order->id;
            }
            else{
                #Withdrawal
                $this->utils->debug_log('=====================jbp getOrderIdFromParameters get Withdrawal order num', $order_num);
                return $order_num;
            }
		}
		else {
			$this->utils->debug_log('====================================jbp callbackOrder cannot get any order_id when getOrderIdFromParameters', $flds);
            return;
		}
    }

    #For Withdrawal
    public function isWithdrawal($params, $orderId){
        if($params['type'] == 'requestWithdrawApproveInformation'){
            $this->CI->utils->debug_log('process requestWithdrawApproveInformation order id', $orderId);
            $sign = $this->signWithdrawalApprove($params);

            if($sign != $params['key']){
                $result = ['success' => false, 'return_error' => json_encode(['status' => 0, 'error_msg' => 'signature failed'])];
                return $result;
            }

            //load orderId from walletaccount
            $walletAccount=$this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);

            if($walletAccount['amount'] != $params['amount']){
                $result = ['success' => false, 'return_error' => json_encode(['status' => 0, 'error_msg' => 'amount is not right'])];
                return $result;
            }

            $status='';
            //check status first
            if(!$this->CI->wallet_model->isAvailableWithdrawal($walletAccount['walletAccountId'], $status)){
                $this->CI->utils->debug_log('====================================isAvailableWithdrawal '.$walletAccount['walletAccountId'], $walletAccount['transactionCode'], $status);
                $result = ['success' => false, 'return_error' => json_encode(['status' => 0,'error_msg' => 'withdrawal is not available, status is '.$status])];
                return $result;
            }

            $resultContent=[
                'mownecum_order_num' => $params['mownecum_order_num'],
                'company_order_num' => $params['company_order_num'],
                'status' => 4,
                'error_msg' => ""
            ];

            $result['success'] = true;

            $result['message'] = $result['return_error'] = json_encode($resultContent);

            return $result;

        }elseif($params['type'] == 'withdrawalResult'){
            $this->CI->utils->debug_log('====================================process withdrawalResult order id', $orderId);
            $sign = $this->signWithdrawalResult($params);

            if($sign != $params['key']){
                $result = ['success' => false, 'return_error' => json_encode(['status' => 0, 'error_msg' => 'signature failed'])];
                return $result;
            }

            if($orderId != $params['company_order_num']){
                $result=['success' => false, 'return_error' =>  json_encode(['status' => 0,'error_msg' => 'wrong order id'])];
                return $result;
            }

            $walletAccount = $this->CI->wallet_model->getWalletAccountByTransactionCode($params['company_order_num']);

            if($walletAccount['amount']<$params['amount']){
                $result = ['success' => false, 'return_error' =>  json_encode(['status' => 0,'error_msg' => 'wrong amount, must <= '.$walletAccount['amount']])];
                return $result;
            }

            $result['success'] = true;

            $this->CI->load->model(['wallet_model']);
            if($params['status'] == '1' || $params['status'] == '2'){

                $reason = isset($params['error_msg']) ? $params['error_msg'] : $params['detail'];
                $reason = empty($reason) ? 'Success' : $reason;

                $fee = $params['exact_transaction_charge'];
                $amount = "";
                if($params['status'] == '2'){
                    $amount = $params['amount'];
                }

                $this->CI->wallet_model->withdrawalAPIReturnSuccess($orderId, $reason, $fee, $amount);

                $resultContent = [
                    'mownecum_order_num' => $params['mownecum_order_num'],
                    'company_order_num' => $params['company_order_num'],
                    'status' => 1,
                ];

                $result['message'] = $result['return_error'] = json_encode($resultContent);

			}
			else{

                $reason = isset($params['error_msg']) ? $params['error_msg'] : $params['detail'];
                $reason = empty($reason) ? 'Fail' : $reason;

                $this->CI->wallet_model->withdrawalAPIReturnFailure($orderId, $reason);

                $resultContent = [
                    'mownecum_order_num' => $params['mownecum_order_num'],
                    'company_order_num' => $params['company_order_num'],
                    'status' => 1,
                ];

                $result['message'] = $result['return_error'] = json_encode($resultContent);
            }
            return $result;
        }
    }

    //MD5(MD5(config)+company_id+bank_id+company_order_num+amount+card_num+card_name+company_user+issue_bank_name+issue_bank_address+memo)
    public function signWithdrawal($params){
        $key = $this->getSystemInfo('secret_key_config');

        $signStr = md5($key);
        $dataKeys = array('company_id', 'bank_id', 'company_order_num', 'amount', 'card_num', 'card_name', 'company_user', 'issue_bank_name', 'issue_bank_address', 'memo');
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $params)) {
                $signStr .= $params[$dataKey];
            }
        }
        $md5 = md5($signStr);
        return $md5;
    }

    //MD5(MD5(config)+company_order_num+mownecum_order_num+amount+card_num+card_name+company_user)
    public function signWithdrawalApprove($params){
        $key = $this->getSystemInfo('secret_key_config');

        $signStr = md5($key);
        $dataKeys = array('company_order_num', 'mownecum_order_num', 'amount', 'card_num', 'card_name', 'company_user');
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $params)) {
                $signStr .= $params[$dataKey];
            }
        }
        $md5 = md5($signStr);
        return $md5;
    }

    //MD5(MD5(config)+mownecum_order_num+company_order_num+status+amount+exact_transaction_charge)
    public function signWithdrawalResult($params){
        $key = $this->getSystemInfo('secret_key_config');

        $signStr = md5($key);
        $dataKeys = array('mownecum_order_num', 'company_order_num', 'status', 'amount', 'exact_transaction_charge');
        foreach($dataKeys as $dataKey) {
            if(array_key_exists($dataKey, $params)) {
                $signStr .= $params[$dataKey];
            }
        }
        $md5 = md5($signStr);
        return $md5;
    }
}

