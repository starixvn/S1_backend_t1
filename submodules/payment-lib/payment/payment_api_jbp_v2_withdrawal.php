<?php
require_once dirname(__FILE__) . '/abstract_payment_api_jbp_v2.php';

/**
 *
 * JBP 聚宝盆
 *
 * JBP_V2_WITHDRAWAL_PAYMENT_API, ID: 5574
 *
 * Required Fields:
 *
 * * URL
 * * Account
 * * Extra Info
 *
 * Field Values:
 *
 * * URL https://api.jbp-pay.com/apply/Withdraw
 * * Account - Merchant ID
 * * Key - Secret key
 * * Extra Info
 *
 *
 *
 * @category Payment
 * @copyright 2013-2019 TripleOneTech
 */
class Payment_api_jbp_v2_withdrawal extends Abstract_payment_api_jbp_v2 {

	public function getPlatformCode() {
		return JBP_V2_WITHDRAWAL_PAYMENT_API;
	}

	public function getPrefix() {
		return 'jbp_v2_withdrawal';
	}

	public function generatePaymentUrlFormgeneratePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = NULL, $enabledSecondUrl = true, $bankId = NULL) {}
	protected function configParams(&$params, $direct_pay_extra_info) {}
    protected function processPaymentUrlForm($params) {}

    /**
     * detail: Initiates a withdraw request
     *
     * note: Note that the $bank is the bank_type ID, we should map it to the bank_code and bank_name required by this API
     *
     * @param int $bank
     * @param string $accNum
     * @param string $name
     * @param float $amount
     * @param int $transId
     * @return json
     */
    public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
        $bankInfo = $this->getBankInfo();

        $params = array();
        $params['company_id'] = $this->getSystemInfo("account");
        $params['bank_id'] = $bankInfo[$bank]['code'];
        $params['company_order_num'] = $transId;
        $params['amount'] = $this->convertAmountToCurrency($amount);
        $params['card_num'] = $accNum;
        $params['card_name'] = $name;
        $params['company_user'] = $transId;
        $params['issue_bank_name'] = '';
        $params['issue_bank_address'] = '';
        $params['web_url'] = $this->getNotifyUrl($orderId);
        $params['key'] = $this->signWithdrawal($params);

        $this->CI->utils->debug_log('========================jbp getWithdrawParams params: ', $params);

        return $params;
    }

    public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
        $result = array('success' => false, 'message' => 'payment failed');

        if(!$this->isAllowWithdraw()) {
            $result['message'] = lang("Withdraw not allowed with this API");
            return $result;
        }

        if(!array_key_exists($bank, $this->getBankInfo())) {
            $this->utils->error_log("========================jbp withdraw bank whose bankTypeId=[$bank] is not supported by jbp");
            $result['message'] = lang("Bank not supported by jbp");
            return $result;
        }

        $params = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);

        list($response, $response_result) = $this->submitPostForm($this->getSystemInfo('url'), $params, true, $transId, true);

        $decodedResult = $this->decodeResult($response);
        $decodedResult['response_result'] = $response_result;
        $this->CI->utils->debug_log('=======================jbp submitWithdrawRequest decoded Result', $decodedResult);

        return $decodedResult;
    }

    public function decodeResult($resultString) {
        $this->utils->debug_log("=========================jbp decodeResult resultString", $resultString);

        if(is_array($resultString) && isset($resultString['lock']) && isset($resultString['msg'])) {
            $result = array('success' => false, 'message' => $resultString['msg'], 'lock' => true);
            return $result;
        }

        $response = json_decode($resultString, true);
        $this->CI->utils->debug_log('=======================jbp submitWithdrawRequest decodeResult json decoded', $response);

        if($response['status'] == self::WITHDRAWAL_RESULT_CODE_SUCCESS) {
            $message = "Jbp withdrawal response success! Transaction fee: " . $response['transaction_charge'];
            return array('success' => true, 'message' => $message);
        } else{
			if(!isset($response['error_msg'])) {
                $this->utils->error_log("========================Jbp return UNKNOWN ERROR!");
                $resultMsg = "未知错误";
            }

            $resultMsg = $response['error_msg'];
            $message = 'Jbp withdrawal failed. Error Message: ' . $resultMsg;
            return array('success' => false, 'message' => $message);
        }
    }

    public function getBankInfo() {
        $bankInfo = array();
        $bankInfoArr = $this->getSystemInfo("withdrawal_bank_info");
        if(!empty($bankInfoArr)) {
            foreach($bankInfoArr as $system_bank_type_id => $bankInfoItem) {
                if(isset($bankInfoItem['name'])){
                    $bankInfo[$system_bank_type_id]['name'] = $bankInfoItem['name'];
                }
                if(isset($bankInfoItem['code'])){
                    $bankInfo[$system_bank_type_id]['code'] = $bankInfoItem['code'];
                }
            }
            $this->utils->debug_log("=========================jbp bank info from extra_info: ", $bankInfo);
        } else {
            $bankInfo = array(
                '1' => array('name' => '中国工商银行', 'code' => 1),
                '2' => array('name' => '招商银行', 'code' => 2),
                '3' => array('name' => '中国建设银行', 'code' => 3),
                '4' => array('name' => '中国农业银行', 'code' => 4),
                '5' => array('name' => '中国银行', 'code' => 5),
                '6' => array('name' => '交通银行', 'code' => 6),
                '7' => array('name' => '中国民生银行', 'code' => 7),
                '8' => array('name' => '中信银行', 'code' => 8),
                '9' => array('name' => '上海浦东发展银行', 'code' => 9),
                '10' => array('name' => '邮政储汇', 'code' => 10),
                '11' => array('name' => '中国光大银行', 'code' => 11),
                '12' => array('name' => '平安银行', 'code' => 12),
                '13' => array('name' => '广发银行股份有限公司', 'code' => 13),
                '14' => array('name' => '华夏银行', 'code' => 14),
                '15' =>array('name' => '福建兴业银行', 'code' => 15)
            );
            $this->utils->debug_log("=======================getting jbp bank info from code: ", $bankInfo);
        }
        return $bankInfo;
    }

    private function getNotifyUrl($orderId) {
        return parent::getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
    }
}
