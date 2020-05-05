<?php
require_once dirname(__FILE__) . '/abstract_payment_api_dsdfpay.php';

/**
 * DSDFPAY 代收代付-出款
 * https://www.dsdfpay.com/dsdf/api/outer_withdraw
 *
 * * dsdfpay_WITHDRAWAL_PAYMENT_API, ID: 332
 *
 * Required Fields:
 *
 * * URL
 * * Account
 * * Extra Info
 *
 * Field Values:
 *
 * * URL: https://www.dsdfpay.com/dsdf/api/outer_withdraw
 * * Extra Info
 * > {
 * >	"dsdfpay_partner" : "## Partner ID ##"
 * > }
 *
 *
 * @category Payment
 * @copyright 2013-2017 TripleOneTech
 */
class Payment_api_dsdfpay_withdrawal extends Abstract_payment_api_dsdfpay {
	const CALLBACK_STATUS_SUCCESS = 1;

	public function getPlatformCode() {
		return DSDFPAY_WITHDRAWAL_PAYMENT_API;
	}

	public function getPrefix() {
		return 'dsdfpay_withdrawal';
	}

	# Implement abstract function but do nothing
	protected function configParams(&$params, $direct_pay_extra_info) {}
	protected function processPaymentUrlForm($params) {}

	/**
	 * detail: override common API functions
	 *
	 * @return void
	 */
	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		return $this->returnUnimplemented();
	}

	# APIs with withdraw function need to implement these methods
	## This function returns the URL to submit withdraw request to
	public function getWithdrawUrl() {
		return $this->getSystemInfo('url');
	}

	## This function returns the params to be submitted to the withdraw URL
	## Note that $bank param is the bank_type ID in database, we compare it with the supported bank_codes by this API
	private $errMsg = 'Payment failed'; # This variable is used to store error message that's available upon submit
	public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {
		$params = array();
		$this->CI->load->model(array('wallet_model', 'player_model', 'playerbankdetails'));

		$params['cid'] = $this->getSystemInfo("account");

		# Get player contact number
		$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($transId);

		$playerId = $order['playerId'];
		$player = $this->CI->player->getPlayerById($playerId);

		$username = $player['username'];
		$params['uid'] = $username;
		$params['time'] = time();
		$params['amount'] = $this->convertAmountToCurrency($amount);
		$params['order_id'] = $transId;

		# look up bank code
		$bankInfo = $this->getDsdfpayBankInfo();
		if(!array_key_exists($bank, $bankInfo)) {
			$this->utils->error_log("========================dsdfpay withdraw bank whose bankTypeId=[$bank] is not supported by dsdfpay");
			return array('success' => false, 'message' => 'Bank not supported by dsdfpay');
		}

		$params['to_bank_flag'] = $bankInfo[$bank]['code']; # bank SN mapping
		$params['to_cardnumber'] = $accNum;
		$params['to_username'] = $name;

		# look up bank detail from playerbankdetails table, using bank_type ID and accountNumber
		# but if we cannot look up those info, will leave the fields blank
		$playerBankDetails = $this->CI->playerbankdetails->getBankDetailsByBankAccount($bank, $accNum);
		$this->utils->debug_log("=========================dsdfpay get playerBankDetails using [$bank] + [$accNum]", $playerBankDetails);


		return $params;
	}

	public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
		$result = array('success' => false, 'message' => 'payment failed');
		$success = false;
		$message = 'payment failed';
		/*if(!$this->isAllowWithdraw()) {
			$result['message'] = lang("Withdraw not allowed with this API");
			$this->utils->debug_log($result);
			return $result;
		}*/

		$url = $this->getSystemInfo('url');

		$data = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);

		if(isset($data['success'])) {
			if($data['success'] == false) {
				$result['message'] = $data['message'];
				$this->utils->debug_log($result);
				return $result;
			}
		}

		$data_string = json_encode($data);
		$dig64 = base64_encode(hash_hmac('sha1', $data_string, $this->getSystemInfo('key'), true));

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Content-Hmac:'.$dig64));
		$result = curl_exec($ch);
		$errCode = curl_errno($ch);
		$error = curl_error($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$this->utils->debug_log("=========================dsdfpay curl_exec to resultContent", $result);
		$this->submitPreprocess($data_string, $result, $url, $result, array('errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode), $transId);
		$decodedResult = json_decode($result, true);

		if(is_array($decodedResult) && $decodedResult['success']){
			$this->utils->debug_log("===============dsdfpay withdraw decoded Result", $decodedResult);
			$success = true;
			$message = 'dsdfpay payment response successful';
		}
		else {
			$message = $decodedResult['msg'];
		}

		$result = array('success' => $success, 'message' => $message);

		return $result;
	}

	public function getDsdfpayBankInfo() {
		$bankInfo = array();
		$bankInfoArr = $this->getSystemInfo("dsdfpay_bank_info");
		if(!empty($bankInfoArr)) {
			foreach($bankInfoArr as $system_bank_type_id => $bankInfoItem) {
				$bankInfo[$system_bank_type_id] = array('name' => $bankInfoItem['name'], 'code' => $bankInfoItem['code']);
			}
			$this->utils->debug_log("==================getting dsdfpay bank info from extra_info: ", $bankInfo);
		} else {
			$bankInfo = array(
				'1' => array('name' => '工商银行', 'code' => 'ICBC'),
				'2' => array('name' => '招商银行', 'code' => 'CMB'),
				'3' => array('name' => '建设银行', 'code' => 'CCB'),
				'4' => array('name' => '农业银行', 'code' => 'ABC'),
				'5' => array('name' => '交通银行', 'code' => 'BCM'),
				'6' => array('name' => '中国银行', 'code' => 'BOC'),
				'8' => array('name' => '广发银行', 'code' => 'GDB'),
				'10' => array('name' => '中信银行', 'code' => 'CNCB'),
				'12' => array('name' => '中国邮政', 'code' => 'PSBC'),
				'13' => array('name' => '兴业银行', 'code' => 'CIB'),
				'14' => array('name' => '华夏银行', 'code' => 'HXB'),
				'15' => array('name' => '平安银行', 'code' => 'PAB'),
				'16' => array('name' => '农村信用社', 'code' => 'RCC'),
				'20' => array('name' => '光大银行', 'code' => 'CEB'),
				'24' => array('name' => '浦发银行', 'code' => 'SPDB')
			);
			$this->utils->debug_log("=======================getting dsdfpay bank info from code: ", $bankInfo);
		}
		return $bankInfo;
	}

	public function getBankType($direct_pay_extra_info) {}

	public function getTypeFlag($direct_pay_extra_info) {}
}
