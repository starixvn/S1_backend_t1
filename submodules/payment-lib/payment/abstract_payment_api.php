<?php
require_once dirname(__FILE__) . '/payment_api_interface.php';
require_once dirname(__FILE__) . '/../ProxySoapClient.php';

/**
 * Defines general behavior of payment API classes.
 *
 * General behaviors include:
 * * Creating sales order for payment
 * * Reading from API configuration
 * * Generating payment forms
 * * Receiving callbacks
 * * Submitting withdrawal requests
 *
 * The functions implemented by child class:
 * * Populating payment form parameters
 * * Handling callbacks
 *
 * All payment APIs accept the following parameter defined in Extra Info:
 * * callback_host - specify the host of callback URL
 * * bank_list - specify the bank list with _json formatted translation, sample:
 *
 * > "bank_list": {
 * >     "ALIPAY": "_json: { \"1\": \"ALIPAY\", \"2\": \"支付宝\" }"
 * > }
 *
 *
 * @see Redirect redirect to bank page
 *
 * @category Payment
 * @version 1.8.10 add bank list
 * @copyright 2013-2016 TripleOneTech
 */
abstract class Abstract_payment_api implements Payment_api_interface {

	public $ignore_callback_sign=false;

	public $response_result_id;

    public $call_socks5_proxy;
    public $call_socks5_proxy_login;
    public $call_socks5_proxy_password;

    public $call_http_proxy_host;
    public $call_http_proxy_port;
    public $call_http_proxy_login;
    public $call_http_proxy_password;

	function __construct($params = null) {
		$this->CI = &get_instance();
		$this->PLATFORM_CODE = $this->getPlatformCode();
		$this->SYSTEM_TYPE_ID = $this->getPlatformCode();

		$this->CI->load->model(array('sale_order', 'wallet_model','playerbankdetails'));
		$this->CI->load->helper('string');
		$this->utils = $this->CI->utils;

		$this->loadSystemInfo();

		$this->white_ip_list= $this->getSystemInfo('white_ip_list', []);
		$this->ignore_callback_sign=$this->getSystemInfo('ignore_callback_sign', false);

        $this->call_socks5_proxy = $this->getSystemInfo('call_socks5_proxy');
        $this->call_socks5_proxy_login = $this->getSystemInfo('call_socks5_proxy_login');
        $this->call_socks5_proxy_password = $this->getSystemInfo('call_socks5_proxy_password');

        $this->call_http_proxy_host = $this->getSystemInfo('call_http_proxy_host');
        $this->call_http_proxy_port = $this->getSystemInfo('call_http_proxy_port');
        $this->call_http_proxy_login = $this->getSystemInfo('call_http_proxy_login');
        $this->call_http_proxy_password = $this->getSystemInfo('call_http_proxy_password');
	}

	/**
	 * Readable return of json_last_error() after json_decode().
	 * @param integer $jsonLastError The return of json_last_error().
	 * @return string JSON_ERROR_XXX intro Text.
	 */
	public function handleJsonLastError($jsonLastError = NULL){

		if($jsonLastError === NULL){
			$jsonLastError = json_last_error();
		}
		$echo = 'DEFAULT';
		switch ($jsonLastError) {
			case JSON_ERROR_NONE:
			$echo = ' - No errors';
			break;
			case JSON_ERROR_DEPTH:
			$echo = ' - Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
			$echo = ' - Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
			$echo = ' - Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
			$echo = ' - Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
			$echo = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
			$echo = ' - Unknown error';
			break;
		}
		return $echo;
	}

	public function loadSystemInfo() {
		$this->CI->load->model('external_system');
		$systemInfo = $this->CI->external_system->getSystemById($this->PLATFORM_CODE);
		$this->allowDepositWithdraw = $systemInfo->allow_deposit_withdraw;
		# based on whether it's live_mode, provide corresponding extra_info field. Use live field by default.
		$extraInfoJson = (!isset($systemInfo->live_mode) || $systemInfo->live_mode) ? $systemInfo->extra_info : $systemInfo->sandbox_extra_info;
		$extraInfo = json_decode($extraInfoJson, true) ?: array();
		if (empty($extraInfo) && (json_last_error() !== JSON_ERROR_NONE)) {
			$jsonLastError = $this->handleJsonLastError(json_last_error());
			$this->CI->utils->debug_log("====================json_last_error:", $jsonLastError );
		}

		$this->CI->utils->debug_log("====================extraInfo:", $extraInfo);


		if($systemInfo->go_live) {
			$payment_api = (array)$systemInfo;
			$secret_list = $this->getSecretInfoList();
			foreach ($secret_list as $key) {
			    if (array_key_exists($key, $payment_api)) {
			        $systemInfo->$key = $this->CI->external_system->decryptSecrets($payment_api[$key]);
			    } elseif (array_key_exists($key, $extraInfo)) {
			        $extraInfo[$key] = $this->CI->external_system->decryptSecrets($extraInfo[$key]);
			    }
			}
		}


		$this->SYSTEM_INFO = array_merge(((array) $systemInfo), $extraInfo);

		# Determine variable for sandbox/live
		$varNames = array('url', 'key', 'secret', 'account');
		foreach ($varNames as $aName) {
			$arrKey = ($this->getSystemInfo('live_mode') ? 'live' : 'sandbox') . '_' . $aName;
			$this->SYSTEM_INFO[$aName] = array_key_exists($arrKey, $this->SYSTEM_INFO) ? $this->SYSTEM_INFO[$arrKey] : "";
		}

        $this->_custom_curl_header = $this->getSystemInfo('curl_headers', []);

		$this->SYSTEM_INFO['_system_code'] = $systemInfo->system_code;
	}

	protected $SYSTEM_TYPE_ID;
	protected $PLATFORM_CODE;
	protected $EXTERNAL_SYSTEM_INFO;
	protected $SYSTEM_INFO;

	protected $api_config;
	protected $allowDepositWithdraw;

    protected $_custom_curl_header = null;

	const FROM_REQUEST = 'request';
	const FROM_SERVER = 'server';
	const FROM_BROWSER = 'browser';

	const CALLBACK_EXCEPTION = 'payment_exception';
	const DEPOSIT_API = 'deposit';
	const DEPOSIT_RESPONSE_API = 'deposit_response';
	const WITHDRAWAL_API = 'withdrawal';
	const WITHDRAWAL_RESPONSE_API = 'withdrawal_response';
	const DIRECT_API = 'direct_pay';
	const VALID_HOUR = 12;
	const SUBMIT_FORM_TARGET_BLANK = '_blank';
	const SUBMIT_FORM_TARGET_SELF = '_self';

	const STATUS_OK = '200';
	const STATUS_FAILED_CODE = '400';

	const REDIRECT_TYPE_ERROR_MODAL = -2;
	const REDIRECT_TYPE_ERROR = -1;
	const REDIRECT_TYPE_URL = 1;
	const REDIRECT_TYPE_FORM = 2;
	const REDIRECT_TYPE_DIRECT_PAY = 3;
	const REDIRECT_TYPE_QRCODE = 4;
	const REDIRECT_TYPE_STATIC = 5;
	const REDIRECT_TYPE_QRCODE_MODAL = 6;
	const REDIRECT_TYPE_HTML = 7;

	const ALLOW_DEPOSIT = 1;
	const ALLOW_WITHDRAW = 2;

	private $__sale_order_extra_info = [];

	abstract public function getPlatformCode();

	abstract public function getPrefix();

	# Note: If you are getting the following properties: url, key, secret, account, the value in 'live_mode'
	# determines whether it will return live_X or sandbox_X value.
	public function getSystemInfo($key, $def_val='') {
		return isset($this->SYSTEM_INFO[$key]) ? $this->SYSTEM_INFO[$key] : $def_val;
	}

	public function getAllSystemInfo() {
		return $this->SYSTEM_INFO;
	}

	public function getSecretInfoList() {
		$secretsInfo = array('live_key', 'live_secret', 'sandbox_key', 'sandbox_secret');
		return $secretsInfo;
	}

	# API can overwrite this function to mark itself unavailable
	public function isAvailable() {
		return true;
	}

	public function returnUnimplemented() {
		return array('success' => true, 'unimplemented' => true);
	}

	protected function getTimeoutSecond() {
		return $this->CI->utils->getConfig('default_http_timeout');
	}

	protected function getConnectTimeout() {
		return $this->CI->utils->getConfig('default_connect_timeout');
	}

	//====implements Payment_api_interface start===================================
	public function shouldRedirect($enabledSecondUrl) {
		$info = $this->getInfoByEnv();
		return $enabledSecondUrl && $info['live_mode'] && !empty(@$info['second_url']);
	}

	public function createSaleOrder($playerId, $amount, $player_promo_id = null, $directPayExtraInfo = null,
			$subWalletId=null, $group_level_id=null, $is_mobile = null,
			$player_deposit_reference_no = null, $deposit_time = null,
			$promo_info=null) {
		$this->CI->load->model(array('payment_account', 'sale_orders_notes', 'sale_orders_timelog'));
		$defaultCurrency = $this->CI->config->item('default_currency');
		//load payment account by system id
		$payment_account_id = $this->CI->payment_account->getPaymentAccountIdBySystemId($this->getPlatformCode());
		//create sale order
		$create_sale_orders_id = $this->CI->sale_order->createSaleOrder( $this->getPlatformCode() // #1
				, $playerId // #2
				, $amount // #3
				, Sale_order::PAYMENT_KIND_DEPOSIT // #4
				, Sale_order::STATUS_PROCESSING // #5
				, null // #6
				, $player_promo_id // #7
				, $defaultCurrency // #8
				, $payment_account_id // #9
				, null // #10
				, $directPayExtraInfo // #11
				, $subWalletId // #12
				, $group_level_id // #13
				, $is_mobile // #14
				, $player_deposit_reference_no // #15
				, $deposit_time // #16
				, $promo_info // #17
			);
		#add notes to action log
		if(isset($create_sale_orders_id)){
			$this->CI->sale_orders_notes->add('create ' . $amount . ' from ' . $this->getPlatformCode(), Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $create_sale_orders_id);
			$this->CI->sale_orders_timelog->add($create_sale_orders_id, Sale_orders_timelog::PLAYER_USER, $playerId, array('before_status' => Sale_order::STATUS_PROCESSING, 'after_status' => null));
		}

		return $create_sale_orders_id;
	}

	/**
	 * detail: get player id using order id
	 *
	 * @param int $oderId
	 */
	protected function getPlayerIdByOrderId($orderId) {
		//search walletaccount if starts with 'W'. In this case the orderId is for withdrawal, references table walletaccount
		if (strtoupper(substr($orderId, 0, 1)) == 'W') {
			//search walletaccount
			$walletAccount = $this->getWalletAccountInfo($orderId);

			if ($walletAccount && array_key_exists('playerId', $walletAccount)) {
				return $walletAccount['playerId'];
			}
		} else { # In this case the orderId is for deposit, references table sale_order
			$ord = $this->getOrderInfo($orderId);
			if ($ord) {
				return $ord->player_id;
			}
		}
		return null;
	}

	/**
	 * detail: get wallet account records
	 *
	 * @param string $transCode
	 * @return array
	 */
	protected function getWalletAccountInfo($transCode) {
		$this->CI->load->model(array('wallet_model'));
		$walletAccount = null;
		if (!empty($transCode)) {
			$walletAccount = $this->CI->wallet_model->getWalletAccountByTransactionCode($transCode);
		}
		return $walletAccount;
	}

	/**
	 * detail: get order records
	 *
	 * @param int $orderId
	 * @return int
	 */
	protected function getOrderInfo($orderId) {
		$this->CI->load->model(array('sale_order'));
		$ord = null;
		if ($orderId) {
			$ord = $this->CI->sale_order->getSaleOrderById($orderId);
		}
		return $ord;
	}

	abstract public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null);
	protected function callbackPreprocess($orderId, $callbackExtraInfo, $requestParam) {
		$player_id=null;
		$secure_id=null;
		$method = self::CALLBACK_EXCEPTION;
		if(!empty($orderId)){
			#save to response result
			$this->utils->error_log("========================callbackPreprocess orderId", $orderId);
			//withdrawal order id
			if(substr($orderId, 0, 1) == 'W') {
				$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);
				$playerId  = $order['playerId'];
				$secure_id = $orderId;
				$orderId   = $order['walletAccountId'];
				$method = self::WITHDRAWAL_API;
			}
			else {
				$player_id = $this->getPlayerIdByOrderId($orderId);
				$order = $this->CI->sale_order->getSaleOrderById($orderId);
				$secure_id = $order->secure_id;
				$method = self::DEPOSIT_API;
			}
		}
		$this->CI->load->model(array('response_result'));
		#save more $_REQUEST and getallheaders()
		$resultAll['type'] = 'callback';
		$resultAll['callbackExtraInfo'] = $callbackExtraInfo;
		$resultAll['_REQUEST'] = $_REQUEST;
		$resultAll['_SERVER'] = $_SERVER;
		$resultAll['_RAW_POST'] = base64_encode( file_get_contents("php://input") ); #encode with base64 to avoid sql insert error
		$resultAll['_RAW_POST_XML_JSON'] = base64_encode( file_get_contents("php://input", "r") ); #encode with base64 to avoid sql insert error

		/// Patch for OGP-12954 在 payment api callback request 內加上可重複發送的機制
		$extra  = NULL;
		if( !empty($_REQUEST['reSendBySecureId']) ){
			$extra = 'reSendBySecureId='. $_REQUEST['reSendBySecureId'];
		}

		$responseResultId = $this->CI->response_result->saveResponseResult( $this->getPlatformCode() // #1
			, Response_result::FLAG_NORMAL // #2
			, $method // #3
			, $requestParam // #4
			, json_encode($resultAll) // #5
			, $_SERVER['REDIRECT_STATUS'] // #6
			, null // #7
			, $extra // #8
			, array('player_id' => $player_id, 'related_id1' => $orderId, 'related_id2' => $secure_id) // #9
		);
		return $responseResultId;
	} // EOF callbackPreprocess

	public function callbackFailed($orderId, $callbackExtraInfo, $requestParam, $msg) {
		$this->CI->load->model(array('response_result', 'sale_order', 'walletaccount_notes', 'sale_orders_notes'));
		$player_id = null;
		$secure_id = "(Not Found)";
		$method = "callbackFailed";
		/// Patch for OGP-12954 在 payment api callback request 內加上可重複發送的機制
		$extra  = NULL;
		if(!empty($orderId)){
			#save to response result
			$this->utils->error_log("========================callbackFailed orderId", $orderId);
			//withdrawal order id
			if(substr($orderId, 0, 1) == 'W') {
				$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);
				$player_id  = $order['playerId'];
				$secure_id = $orderId;
				$orderId   = $order['walletAccountId'];
				$method = self::WITHDRAWAL_API;
				if(!$this->CI->walletaccount_notes->checkNoteExist($orderId, $msg)){
					$this->CI->walletaccount_notes->add($msg, Users::SUPER_ADMIN_ID, Walletaccount_notes::ACTION_LOG, $orderId);
				}
			} else {
				$order = $this->CI->sale_order->getSaleOrderById($orderId);
				$player_id = $order->player_id;
				$secure_id = $order->secure_id;
				$method = self::DEPOSIT_API;
				if(!$this->CI->sale_orders_notes->checkNoteExist($orderId, $msg)){
					$this->CI->sale_orders_notes->add($msg, Users::SUPER_ADMIN_ID, Sale_orders_notes::ACTION_LOG, $orderId);
				}

				/// Patch for OGP-12954 在 payment api callback request 內加上可重複發送的機制
				if( !empty($_REQUEST['reSendBySecureId']) ){
					$extra = 'reSendBySecureId='. $_REQUEST['reSendBySecureId'];
				}
			}
		} else {
			$secure_id = "(EMPTY)";
		}

		#save more $_REQUEST and getallheaders()
		$resultAll['type'] = 'callback';
		$resultAll['fail_msg'] = $msg;
		$resultAll['callbackExtraInfo'] = $callbackExtraInfo;
		$resultAll['_REQUEST'] = $_REQUEST;
		$resultAll['_SERVER'] = $_SERVER;
		$resultAll['_RAW_POST'] = base64_encode( file_get_contents("php://input") ); #encode with base64 to avoid sql insert error
		$resultAll['_RAW_POST_XML_JSON'] = base64_encode( file_get_contents("php://input", "r") ); #encode with base64 to avoid sql insert error


		return $this->CI->response_result->saveResponseResult( $this->getPlatformCode() // #1
			, Response_result::FLAG_ERROR // #2
			, $method // #3
			, $requestParam // #4
			, json_encode($resultAll) // #5
			, self::STATUS_FAILED_CODE // #6
			, null // #7
			, $extra // #8
			, array('player_id' => $player_id, 'related_id1' => $orderId, 'related_id2' => $secure_id) // #9
		);
	}

	public function callbackNeedToBeNotify($orderId, $callbackExtraInfo, $requestParam, $msg) {
		$this->CI->load->model(array('response_result', 'sale_order'));
	    $this->CI->load->helper('mattermost_notification_helper');

		$player_id = NULL;
		$secure_id = "(Not Found)";
		$method = "Unexpected Callback";

		if(!empty($orderId)){
			#save to response result
			$this->utils->error_log("========================callbackNeedToBeNotify orderId", $orderId);
			//withdrawal order id
			if(substr($orderId, 0, 1) == 'W') {
				$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);
				if(!empty($order)){
					$player_id  = $order['playerId'];
					$secure_id = $orderId;
					$orderId   = $order['walletAccountId'];
				}
			} else {
				$order = $this->CI->sale_order->getSaleOrderById($orderId);
				if(!empty($order)){
					$player_id = $order->player_id;
					$secure_id = $order->secure_id;
				}
			}
		} else {
			$secure_id = "(EMPTY)";
		}

		#save more $_REQUEST and getallheaders()
		$resultAll['type'] = 'callback';
		$resultAll['fail_msg'] = $msg;
		$resultAll['callbackExtraInfo'] = $callbackExtraInfo;
		$resultAll['_REQUEST'] = $_REQUEST;
		$resultAll['_SERVER'] = $_SERVER;
		$resultAll['_RAW_POST'] = base64_encode( file_get_contents("php://input") ); #encode with base64 to avoid sql insert error
		$resultAll['_RAW_POST_XML_JSON'] = base64_encode( file_get_contents("php://input", "r") ); #encode with base64 to avoid sql insert error

		$response_result_id = $this->CI->response_result->saveResponseResult( $this->getPlatformCode() // #1
			, Response_result::FLAG_ERROR // #2
			, $method // #3
			, $requestParam // #4
			, json_encode($resultAll) // #5
			, self::STATUS_FAILED_CODE // #6
			, null // #7
			, null // #8
			, array('player_id' => $player_id, 'related_id1' => $orderId, 'related_id2' => $secure_id) // #9
		);

		if(strpos($_SERVER['HTTP_HOST'], 'og.local') !== false){
			return;
		}

		$from_ip = $this->getClientIP();
		$x_forwarderd = !empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'(EMPTY)';
		$ifconfig = file_get_contents('https://ifconfig.co/json');
		$ifconfig = json_decode($ifconfig, true);

        $user = $this->utils->getConfig('payment_account_notify_user');
        $channel = "payment_alert";
		$title = $msg;

		# markdown format
		$message =
			"**Order ID**   :  `" .$secure_id. "`\n".
			"**Access IP**  :  `" .$from_ip. "`\n".
			"**Access URL**  :  `http://" .$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']. "`\n".
			"**Response Result**  :  " ."http://".$this->utils->getSystemHost('admin')."/system_management/view_resp_result?result_id=".$response_result_id. "\n\n".
			"**Header Info**"."\n".
			"  REQUEST_METHOD  :  `" .$_SERVER['REQUEST_METHOD'] . "`\n".
			"  REMOTE_ADDR  :  `"    .$_SERVER['REMOTE_ADDR'] . "`\n".
			"  X_FORWARDED_FOR  :  `".$x_forwarderd. "`\n".
			"**Other Info**"."\n".
			"  ifconfig.co  :  `"    .$ifconfig['ip']. "`\n".
			"  try_get_prefix  :  `" .try_get_prefix(). "`\n".
			"  gethostname  :  `"  .gethostname()."`\n".
			"  php_uname  :  `"    .php_uname()."`"
        ;

        $this->utils->debug_log('============getUnexpectedCallbackNotifyMessage, '.$message);
        $texts_and_tags = array("#".$user."_payment_alert", "#ip-".$from_ip);
        $messages = [
            [
                'text' => $message,
                'type' => 'danger',
                'title' => $title,
            ]
        ];

        sendNotificationToMattermost($user, $channel, $messages, $texts_and_tags);

		return;
	}

	public function callbackFromServer($orderId, $callbackExtraInfo) {
		$this->response_result_id = $this->callbackPreprocess($orderId, $callbackExtraInfo, 'server');
		return $this->response_result_id;
	}
	public function callbackFromBrowser($orderId, $callbackExtraInfo) {
		return $this->callbackPreprocess($orderId, $callbackExtraInfo, 'browser');
	}
	public function getOrderValidation($callbackExtraInfo) {
		return;
	}

	abstract public function directPay($order);
	//====implements Payment_api_interface end===================================

	/**
	 *
	 *
	 * @return array (live_mode=>0/1, second_url=>, url=>, key=>, account=>, secret=>)
	 */
	public function getInfoByEnv() {
		$result = array(
			'live_mode' => ($this->getSystemInfo('live_mode') == 1),
			'second_url' => $this->getSystemInfo('second_url'),
		);
		$result['url'] = $this->getSystemInfo('url');
		$result['key'] = $this->getSystemInfo('key');
		$result['account'] = $this->getSystemInfo('account');
		$result['secret'] = $this->getSystemInfo('secret');
		$result['system_info'] = $this->getAllSystemInfo();
		return $result;
	}

	public function createHtmlForm($urlForm) {
		$formId = 'f_' . random_string('unique');
		$method = (isset($urlForm['post'])) ? ($urlForm['post'] == true ? 'POST' : 'GET') : 'GET';
		$html = '<form name="' . $formId . '" id="' . $formId . '" method="' . $method . '" action="' . $urlForm['url'] . '">';
		if (!empty($urlForm['params'])) {
			foreach ($urlForm['params'] as $name => $val) {
				$html = $html . "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . htmlentities($val) . "\">\n";
			}
		}
		$html = $html . "<button type=\"hidden\" style=\"display:none\" id=\"submit_form_btn_" . $formId . "\">\n";
		$html = $html . '</form>';
		return array($html, $formId);
	}

	public function getClientIP() {
		return $this->CI->utils->getIP();
	}

	public function validateWhiteIp($ip){
		return empty($this->white_ip_list) || in_array($ip, $this->white_ip_list);
	}

	# This function returns the data used to construct bank list dropdowns when making payment.
	# It can take information from the following sources, from priority high to low:
	# * payment API configuration's extra_info->bank_list - This allows in-place multi-language using _json
	# * subclass' getBankListInfoFallback function override - This is used as a fallback when extra_info->bank_list is not defined
	# * database bank_list table - This is used as general default when the above two are not defined
	public function getPlayerInputInfo() {
		if (!empty($this->getBankList())) {

			if ($this->CI->utils->getConfig('enable_bank_box_for_deposit')) {
				$bankList=array('name' => 'bank_list', 'type' => 'bank_box', 'label_lang' => 'pay.bank',
					'external_system_id' => $this->getPlatformCode(),
					'bank_list' => $this->getBankList(), 'bank_tree' => $this->getBankListTree(), 'bank_list_default' => $this->getSystemInfo('bank_list_default'));
			}else{
				$bankList=array('name' => 'bank_list', 'type' => 'bank_list', 'label_lang' => 'pay.bank',
					'external_system_id' => $this->getPlatformCode(),
					'bank_list' => $this->getBankList(), 'bank_tree' => $this->getBankListTree(), 'bank_list_default' => $this->getSystemInfo('bank_list_default'));
			}
			return array(
				// array('name' => 'banktype', 'type' => 'list', 'label_lang' => 'pay.bank',
				// 	'list' => $this->getBankList(), 'list_tree' => $this->getBankListTree()),
				$bankList,
				array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
			);
		} else {
			$this->utils->debug_log($this->getPrefix() . ": Using database bank_list");
			$this->CI->load->model(array('bank_list'));
			list($bankList, $bankTree) = $this->CI->bank_list->getBankTypeTree($this->getPlatformCode());

			if ($this->CI->utils->getConfig('enable_bank_box_for_deposit')) {
				return array(
					array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
					array('name' => 'bank_list', 'type' => 'bank_box', 'label_lang' => 'cashier.81',
						'external_system_id' => $this->getPlatformCode(),
						'bank_tree' => $bankTree, 'bank_list' => $bankList, 'bank_list_default' => $this->getSystemInfo('bank_list_default')),
				);
			} else {
				return array(
					array('name' => 'bank_list', 'type' => 'bank_list', 'label_lang' => 'cashier.81',
						'external_system_id' => $this->getPlatformCode(),
						'bank_tree' => $bankTree, 'bank_list' => $bankList, 'bank_list_default' => $this->getSystemInfo('bank_list_default')),
					array('name' => 'deposit_amount', 'type' => 'float_amount', 'label_lang' => 'cashier.09'),
				);
			}
		}
	}

	protected function getBankList() {
		$list = array();
		$bankListInfo = $this->getBankListInfo();
		foreach ($bankListInfo as $bankInfo) {
			$list[$bankInfo['value']] = $bankInfo['label'];
		}
		return $list;
	}

	protected function getBankListTree() {
		$tree = array();
		$bankListInfo = $this->getBankListInfo();
		foreach ($bankListInfo as $bankInfo) {
			$subList = array();
			if (!empty($bankInfo['sub_list'])) {
				foreach ($bankInfo['sub_list'] as $aBankValue => $aBankName) {
					$subList[] = array('label' => lang($aBankName), 'value' => $aBankValue);
				}
			}
			$tree[$bankInfo['value']] = $subList;
		}
		return $tree;
	}

	private $_bankListInfo = array();
	protected function getBankListInfo() {
		if (empty($this->_bankListInfo)) {
			$bankList = $this->getSystemInfo('bank_list');
			if (empty($bankList)) {
				$this->_bankListInfo = $this->getBankListInfoFallback();
				if (!empty($this->_bankListInfo)) {
					$this->utils->debug_log($this->getPrefix() . ": Using fallback bank_list");
				}
			} else {
				$this->utils->debug_log($this->getPrefix() . ": Using extra_info->bank_list");
				foreach ($bankList as $aBankValue => $aBankName) {
					if (gettype($aBankName) == "string") {
						$this->_bankListInfo[] = array('label' => lang($aBankName), 'value' => $aBankValue);
					} else {
						$this->_bankListInfo[] = array('label' => lang($aBankName['name']), 'value' => $aBankValue, 'sub_list' => $aBankName['sub_list']);
					}
				}
			}
		}
		return $this->_bankListInfo;
	}

	# This function is used when bank_list is not defined in extra_info
	# subclasses are expected to override this function with hard-coded bank_list
	protected function getBankListInfoFallback() {
		return array();
	}

	public function getAmount($fields) {
		return isset($fields['deposit_amount']) ? $fields['deposit_amount'] : null;
	}

	public function getNextOrderCooldownTime() {
	    $cooldown_time_system_info = $this->getSystemInfo('cooldown_time');
        if(!empty($cooldown_time_system_info)){
            return $cooldown_time_system_info;
        }else{
            return 0;
        }
	}

	public function getCurrentTimeStamp() {
		return time();
	}

	protected function buildUrl($url, $params) {
		if (!empty($params)) {
			if (strpos($url, '?') === FALSE) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			$url .= http_build_query($params);
		}

		return $url;
	}

	/**
	 * Request With CURL
	 *
	 * @param string $orderId For log failed. sale_order.id
	 * @param string $url The active uri. Sometimes for GET METHOD.
	 * @param mixed $params May be an array or object containing properties. Ref. to http://docs.php.net/manual/da/function.http-build-query.php
	 * @param string $method GET or POST or UPDATE ...
	 * @param string $mime The attr. XML, JSON,... of  \Httpful\Mime,
	 * ex: FORM mean to "application/x-www-form-urlencoded".
	 * Just work in $method eq. POST case.
	 * @return false|Response $response with parsed results from Httpful.
	 */
	public function httpCall($orderId, $url, $params, $method = 'GET', $mime = NULL) {
		// require_once dirname(__FILE__) . '/../vendor/autoload.php';
		$response = null;
		try {

			if ($method == 'GET') {
				$url = $this->buildUrl($url, $params);
				$this->CI->utils->debug_log('url', $url);
				$request = \Httpful\Request::get($url);
			} else {
				if( empty( $mime ) ){
					$mime = \Httpful\Mime::FORM;
				}

				$request = \Httpful\Request::post($url, $params, $mime);
			}

			$response = $request->send();
		} catch (Exception $ex) {
			$this->CI->utils->debug_log('order id', $orderId, $ex);
			$this->logFailedHttpCall($orderId, $params, $ex->getMessage());
		}
		return $response;
	} // EOF httpCall

	public function resendHttpCall($orderId, $url, $params, $method = 'GET', $mime = NULL , $reSendType = NULL) {
		// require_once dirname(__FILE__) . '/../vendor/autoload.php';
		$response = null;
		try {

			if ($method == 'GET') {
				$url = $this->buildUrl($url, $params);
				$this->CI->utils->debug_log('url', $url);
				$request = \Httpful\Request::get($url);
			} else {
				if( empty( $mime ) ){
					$mime = \Httpful\Mime::FORM;
				}

				if($reSendType == 'json_decode'){
					$request = \Httpful\Request::post($url)->sendsJson()->body($params);
				} else {
					$request = \Httpful\Request::post($url, $params, $mime);
				}
			}

			$response = $request->send();
		} catch (Exception $ex) {
			$this->CI->utils->debug_log('resend order id', $orderId, $ex);
			$this->logFailedHttpCall($orderId, $params, $ex->getMessage());
		}
		return $response;
	} // EOF resendHttpCall

	public function logFailedHttpCall($orderId, $params, $errMessage, $response = null) {
		$ord = $this->CI->sale_order->getSaleOrderById($orderId);
		$this->CI->load->model(array('response_result'));

		if ($response) {
			$resultText = $response->raw_body;
			$code = $response->code;
			$status = $response->code;
			$extra = null;
		} else {
			$resultText = $errMessage;
			$code = self::STATUS_FAILED_CODE;
			$status = self::STATUS_FAILED_CODE;
			$extra = null;
		}

		$response_result_id = $this->CI->response_result->saveResponseResult($this->getPlatformCode(), Response_result::FLAG_NORMAL,
			self::DIRECT_API, json_encode($params), $resultText, $code, $status, $extra,
			array('player_id' => $ord->player_id, 'related_id1' => $orderId));

		$this->CI->utils->debug_log('order id', $orderId, 'response_result_id', $response_result_id, $errMessage);
		return $response_result_id;
	}

	public function isResponseError($response) {
		$error = $response->hasErrors();
		return $error;
	}

	protected function logSuccessHttpCall($orderId, $response, $params) {
		//save to response result
		$ord = $this->CI->sale_order->getSaleOrderById($orderId);
		$this->CI->load->model(array('response_result'));

		$resultText = $response->raw_body;
		$code = $response->code;
		$status = $response->code;
		$extra = null;
		return $this->CI->response_result->saveResponseResult($this->getPlatformCode(), Response_result::FLAG_NORMAL,
			self::DIRECT_API, json_encode($params), $resultText, $code, $status, $extra,
			array('player_id' => $ord->player_id, 'related_id1' => $orderId));

	}

	protected function logHeader() {
		return $this->CI->utils->getNowForMysql() . ' [' . $this->getPlatformCode() . '] ';
	}

	protected function writePaymentErrorLog($msg, $flds) {
		$this->CI->utils->error_log($msg, $flds);
		$rand = random_string('alnum', 4);

		#update response_result
		$insert = ',"error_msg-'.$rand.'":"' . trim($msg, '=') . '"';
		$response_result_id = $this->response_result_id;

		if(!empty($response_result_id)){
			$response = $this->CI->response_result->getResponseResultById($response_result_id);
			$result   = $this->CI->response_result->getRespResultByTableField($response->filepath);

			$content = json_decode($result['content'], true);
			$content['resultText'] = substr_replace($content['resultText'], $insert, -1, -1);
			$content = json_encode($content);

			$this->CI->response_result->setResponseResultToError($response_result_id);
			$this->CI->response_result->updateResponseResultContentByFilepath($response->filepath, $content);
		}
	}

	public function getValueFromApiConfig($key, $defaultValue = null) {
		$val = $this->getSystemInfo($key);
		return $val ? $val : $defaultValue;
	}

	public function getCallbackUrl($uri) {
		if(strpos($uri, 'browser')!==FALSE){

			return $this->getBrowserCallbackUrl($uri);

		}

		return $this->getServerCallbackUrl($uri);
	}

	public function getServerCallbackUrl($uri) {
		return $this->CI->utils->site_url_with_http($uri, $this->getSystemInfo('callback_host'));
	}

	public function getBrowserCallbackUrl($uri){
		return $this->CI->utils->site_url_with_http($uri);
	}

	public function getOrderServerCallbackUrl($orderId){
		return $this->getCallbackUrl('/callback/process/' . $this->getPlatformCode() . '/' . $orderId);
	}

	public function getOrderBrowserCallbackUrl($orderId){
		return $this->getBrowserCallbackUrl('/callback/browser/success/' . $this->getPlatformCode() . '/' . $orderId);
	}

	// protected function lockActionById($anyId, $action) {
	// 	return $this->CI->utils->lockActionById($anyId, $action);
	// }

	// protected function releaseActionById($anyId, $action) {
	// 	return $this->CI->utils->releaseActionById($anyId, $action);
	// }

	// public function lockPlayerBalance($playerId) {
	// 	$this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }

	// public function releasePlayerBalance($playerId) {
	// 	$this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
	// }

	public function lockResourceBy($anyId, $action, &$lockedKey) {
		return $this->CI->utils->lockResourceBy($anyId, $action, $lockedKey);
	}

	public function releaseResourceBy($anyId, $action, &$lockedKey) {
		return $this->CI->utils->releaseResourceBy($anyId, $action, $lockedKey);
	}

	public function lockPlayerBalanceResource($playerId, &$lockedKey){
		return $this->CI->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	}

	public function releasePlayerBalanceResource($playerId, &$lockedKey){
		return $this->CI->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
	}

	public function isAllowDeposit(){
		return $this->allowDepositWithdraw && ($this->allowDepositWithdraw & self::ALLOW_DEPOSIT) > 0;
	}

	public function isAllowWithdraw(){
		return $this->allowDepositWithdraw && ($this->allowDepositWithdraw & self::ALLOW_WITHDRAW) > 0;
	}

	public function getPlayerBackUrl() {
		if ($this->CI->utils->getPlayerCenterTemplate() == 'iframe') {
			return '/iframe_module/iframe_viewCashier';
		} else {
			return '/player_center2';
		}
	}

	public function approveSaleOrder($orderId, $note, $show_to_player, &$extra_info=null){
		$this->CI->load->model(['users', 'sale_order', 'promorules', 'withdraw_condition', 'point_transactions', 'player_model', 'group_level']);

        $approvedSubWallet = TRUE;
        $approve_promotion = TRUE;
        $approve_player_group_level_request = TRUE;

        $saleOrder = NULL;
        $player = NULL;
        $extra_info = [];
        $adminUserId = $this->CI->users->getSuperAdminId();

        try {
            $saleOrder = $this->CI->sale_order->getSaleOrderWithPlayerById($orderId);

            if(empty($saleOrder)){
                return FALSE;
            }

            $player = $saleOrder->player;

            $extra_info['approve_SubWallet'] = $approvedSubWallet;
            $extra_info['approve_promotion'] = $approve_promotion;
            $extra_info['is_payment_account_promo'] = FALSE;

            $success=$this->CI->sale_order->approveSaleOrder($orderId, $note, $show_to_player, $extra_info);
            if(!$success && isset($extra_info['error_message'])){
                $error_message = lang($extra_info['error_message']);
            }

            if($success && isset($extra_info['apply_promo_success'])){
                $promo_result=['apply_promo_success'=>$extra_info['apply_promo_success'],
                    'apply_promo_message'=>lang('Deposit Successfully').' '.@$extra_info['apply_promo_message']];
            }

            //get deposit convert rate of this player
            $depositConvertRate = $this->CI->group_level->getVipGroupLevelDetails($player->levelId)['deposit_convert_rate'];

            //point based on convert rate
            $point = $saleOrder->amount * $depositConvertRate / 100;

            //player current point
            $beforePointBalance = $player->point;
            $newPointBalance = $beforePointBalance + $point;
            $this->CI->point_transactions->createPointTransaction($adminUserId, $player->playerId, $point, $beforePointBalance, $newPointBalance, $saleOrder->id, $saleOrder->player_promo_id, Point_transactions::DEPOSIT_POINT);

            //update player point balance
            $this->CI->player_model->updatePlayerPointBalance($player->playerId, $newPointBalance);

            // change player group level
            if ($approve_player_group_level_request && isset($saleOrder->group_level_id) && $saleOrder->group_level_id > 0) {
                $this->CI->group_level->adjustPlayerLevel($player->playerId, $saleOrder->group_level_id);
            }

            $this->__sale_order_extra_info = $extra_info;

            return $success;
        }catch(Exception $e){
            return FALSE;
        }
	}

	public function processSaleOrderAddonFunction($orderId, $success = FALSE){
        $this->CI->load->model(['users', 'sale_order', 'promorules', 'withdraw_condition', 'point_transactions', 'player_model', 'group_level']);

        $saleOrder = $this->CI->sale_order->getSaleOrderWithPlayerById($orderId);

        if(empty($saleOrder)){
            return FALSE;
        }

        $this->CI->load->library(['player_notification_library']);
        if($success){
            $this->CI->player_notification_library->success($saleOrder->player_id, Player_notification::SOURCE_TYPE_DEPOSIT, [
                'player_notify_success_deposit_title',
                $saleOrder->secure_id,
                $saleOrder->created_at,
                $this->CI->utils->displayCurrency($saleOrder->amount),
                $this->CI->utils->getPlayerHistoryUrl('deposit')
            ], [
                'player_notify_success_deposit_message',
                $saleOrder->secure_id,
                $saleOrder->created_at,
                $this->CI->utils->displayCurrency($saleOrder->amount),
                $this->CI->utils->getPlayerHistoryUrl('deposit')
            ]);
        }else{
            $this->CI->player_notification_library->danger($saleOrder->player_id, Player_notification::SOURCE_TYPE_DEPOSIT, [
                'player_notify_danger_deposit_title',
                $saleOrder->secure_id,
                $saleOrder->created_at,
                $this->CI->utils->displayCurrency($saleOrder->amount),
                $this->CI->utils->getLiveChatLink(),
                $this->CI->utils->getLiveChatOnClick()
            ], [
                'player_notify_danger_deposit_message',
                $saleOrder->secure_id,
                $saleOrder->created_at,
                $this->CI->utils->displayCurrency($saleOrder->amount),
                $this->CI->utils->getLiveChatLink(),
                $this->CI->utils->getLiveChatOnClick()
            ]);

            return FALSE;
        }

        $extra_info = $this->__sale_order_extra_info;
        //lock balance in approveSaleOrderSubWalletWithLock
        $result_approveSubWallet = $this->CI->sale_order->approveSaleOrderSubWalletWithLock($saleOrder->id, $extra_info);
        if($result_approveSubWallet === FALSE){
            return FALSE;
        }
        //lock balance in approveSaleOrderPlayerPromotionWithLock
        $result_approveSaleOrderPromotion = $this->CI->sale_order->approveSaleOrderPlayerPromotionWithLock($saleOrder->id, $extra_info);
        return $result_approveSaleOrderPromotion;
    }

	public function processStandaloneTrans($extra_info){
		$this->CI->load->model(['promorules']);
		try{

			$rlt=$this->CI->promorules->releaseToAfterApplyPromo($extra_info);
		}catch(Exception $e){
			$this->CI->utils->error_log('processStandaloneTrans', $extra_info, $e);
			$rlt=['success'=>false];
		}

		return $rlt;
	}

	public function callbackException($params) {

		//when some deposit is exception which means can't find order
		$response_result_id = $this->callbackFromServer(null, $params);

		$rlt=$this->validateExceptionCallback($params);
		if($rlt['success']){
			// Unable to executed, always return false from validateExceptionCallback().
			$rlt=$this->processExceptionCallback($params);
			return $rlt;
		}else{
			return $rlt;
		}

	}
	/**
	 * Why always return the false array?
	 *
	 * @return array The formats,
	 * - success = false
	 * - return_error = 'FAILED'
	 */
	public function validateExceptionCallback($params){
		return ['success'=>false, 'return_error'=>'FAILED'];
	}

	public function generateInfoForException($params){
		return ['amount'=>null, 'external_order_id'=>null, 'external_order_datetime'=>null,
		'player_bank_name'=>null, 'player_bank_account_name'=>null, 'player_bank_account_number'=>null, 'player_bank_address'=>null,
		'collection_bank_name'=>null, 'collection_bank_account_name'=>null, 'collection_bank_account_number'=>null, 'collection_bank_address'=>null,
		];
	}

	public function preprocessExceptionCallback($params){
		//save to response
		$this->CI->load->model(array('response_result'));
		//save more $_REQUEST and getallheaders()
		$resultAll['callbackExtraInfo'] = $params;
		$resultAll['_REQUEST'] = $_REQUEST;
		$resultAll['_SERVER'] = $_SERVER;
		#$resultAll['getallheaders'] = getallheaders(); # commented out as this is causing run-time error on certain environment
		$response_result_id= $this->CI->response_result->saveResponseResult($this->getPlatformCode(), Response_result::FLAG_NORMAL,
			self::DEPOSIT_API, 'exception_order', json_encode($resultAll), self::STATUS_OK, null, null,
			[]);

		$this->CI->utils->debug_log('save_resp_rlt', $response_result_id, $params);

		//save to db
		$this->CI->load->model(['sale_order']);
		$external_system_id=$this->getPlatformCode();
		$info=$this->generateInfoForException($params);
		$amount=$info['amount'];
		$external_order_id=$info['external_order_id'];
		$external_order_datetime=$info['external_order_datetime'];
		$player_bank_name=$info['player_bank_name'];
		$player_bank_account_name=$info['player_bank_account_name'];
		$player_bank_account_number=$info['player_bank_account_number'];
		$player_bank_address=$info['player_bank_address'];
		$collection_bank_name=$info['collection_bank_name'];
		$collection_bank_account_name=$info['collection_bank_account_name'];
		$collection_bank_account_number=$info['collection_bank_account_number'];
		$collection_bank_address=$info['collection_bank_address'];

		$exception_order_id=$this->CI->sale_order->createExceptionDeposit($external_system_id, $amount, $external_order_id, $external_order_datetime, $response_result_id,
			$player_bank_name, $player_bank_account_name, $player_bank_account_number, $player_bank_address,
			$collection_bank_name, $collection_bank_account_name, $collection_bank_account_number, $collection_bank_address,
			$params);

		$this->CI->utils->debug_log('exception_order_id', $exception_order_id);
		return $exception_order_id;
	}

	public function processExceptionCallback($params){

		if($this->preprocessExceptionCallback($params)){

			return $this->generateResultContentForException($params);

		}
		return ['success'=>false, 'return_error'=>'FAILED'];

	}

	public function generateResultContentForException($params){

		return ['success'=>false, 'return_error'=>'FAILED'];

	}

	public function submitGetForm($url, $params, $url_encode=true, $orderSecureId=NULL, $return_all=false) {
		try {
			$ch = curl_init();

			$query_string = '';
			if($url_encode == false) {
				foreach ($params as $key => $value) {
					$query_string .= $key . '=' . $value . '&';
				}
				$query_string = rtrim($query_string,'&');
			}

			if(strpos($url, '?')!==FALSE){
				//found ?
				$url = empty($query_string) ? rtrim($url,'&').'&'.http_build_query($params) : rtrim($url,'&').'&'.$query_string;
				//$url=rtrim($url,'&').'&'.http_build_query($params);
			}else{
				//no ?
				$url = empty($query_string) ? $url.'?'.http_build_query($params) : $url.'?'.$query_string;
				// $url=$url.'?'.http_build_query($params);
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);

            if(!empty($this->_custom_curl_header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
            }

            $this->setCurlProxyOptions($ch);

			// curl_setopt($ch, CURLOPT_POSTFIELDS, $this->utils->encodeJson($params) );
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

			$response = curl_exec($ch);
			$errCode = curl_errno($ch);
			$error = curl_error($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$content = substr($response, $header_size);

			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

			$statusText = $errCode . ':' . $error;
			curl_close($ch);

			$this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

			#withdrawal lock processing
			if(substr($orderSecureId, 0, 1) == 'W' && $errCode == '28') {	//curl_errno means timeout
				// $content = '{"lock": true, "msg": "Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error.'" }';
				$content = array('lock' => true, 'msg' => 'Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error);
			}

			$response_result_content = is_array($content) ? json_encode($content) : $content;

			#save response result
			$response_result_id = $this->submitPreprocess($params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $orderSecureId);

			if($return_all){
				$response_result = [
					$params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $orderSecureId
				];
				$this->CI->utils->debug_log('=========================submitPostForm return_all response_result', $response_result);
				return array($content, $response_result);
			}

			return $content;
		} catch (Exception $e) {
			$this->CI->utils->error_log('POST failed', $e);
		}
	}

	public function submitPostForm($url, $params, $postJson=false, $orderSecureId=NULL, $return_all=false) {
		try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            if(!empty($this->_custom_curl_header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
            }

			if($postJson){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->CI->utils->encodeJson($params) );
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params) );
			}

            $this->setCurlProxyOptions($ch);

			curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

			$response = curl_exec($ch);

			# If Extra Info has 'gb2312' set to execute encoding
			$response_encode = $this->getSystemInfo("response_encode") ? $this->getSystemInfo("response_encode") : "";
			if(!empty($response_encode)) {
				$response = iconv("UTF-8", $response_encode."//IGNORE", $response);
			}

			$response_encode = $this->getSystemInfo("response_mb_convert_encode") ? $this->getSystemInfo("response_mb_convert_encode") : "";
			if(!empty($response_encode)) {
				$response = mb_convert_encoding($response, 'UTF-8', $response_encode);
			}

			$this->CI->utils->debug_log('=========================submitPostForm curl content ', $response);

			$errCode = curl_errno($ch);
			$error = curl_error($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$content = substr($response, $header_size);

			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

			$statusText = $errCode . ':' . $error;
			curl_close($ch);

			$this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

			#withdrawal lock processing
			if(substr($orderSecureId, 0, 1) == 'W' && $errCode == '28') {	//curl_errno means timeout
				// $content = '{"lock": true, "msg": "Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error.'" }';
				$content = array('lock' => true, 'msg' => 'Ready to lock processing withdrawal order. curl error message: errCode = '.$errCode.' - '.$error);
			}

			$response_result_content = is_array($content) ? json_encode($content) : $content;

			#save response result
			$response_result_id = $this->submitPreprocess($params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $orderSecureId);

			if($return_all){
				$response_result = [
					$params, $response_result_content, $url, $response, ['errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode], $orderSecureId
				];
				$this->CI->utils->debug_log('=========================submitPostForm return_all response_result', $response_result);
				return array($content, $response_result);
			}

			return $content;
		} catch (Exception $e) {
			$this->CI->utils->error_log('POST failed', $e);
		}
	}

	public function submitPreprocess($params, $content, $url, $response, $fields=NULL, $orderSecureId=NULL) {
		$player_id = NULL;
		$secure_id = NULL;
		$order_id  = NULL;
		$method = self::DEPOSIT_RESPONSE_API;
		if(!empty($orderSecureId)){
			#save to response result
			if(substr($orderSecureId, 0, 1) == 'W') {
				$order = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderSecureId);
				$player_id = $order['playerId'];
				$order_id  = $order['walletAccountId'];
				$secure_id = $orderSecureId; #Transaction Code
				$method = self::WITHDRAWAL_RESPONSE_API;
				$this->CI->utils->debug_log('==================================submitPreprocess player_id: ', $player_id );
			}
			else{
				$order     = $this->CI->sale_order->getSaleOrderBySecureId($orderSecureId);
				$order_id  = $order->id;
				$player_id = $this->getPlayerIdByOrderId($order_id);
				$secure_id = $orderSecureId;
				$method = self::DEPOSIT_RESPONSE_API;
			}
		}
		$this->CI->load->model(array('response_result'));
		#save more
		$resultAll['type']       = 'submit';
		$resultAll['url']        = $url;
		$resultAll['params']     = $params;
		$resultAll['content']    = $content;
		$statusCode = (array_key_exists("statusCode", $fields)) ? $fields['statusCode'] : NULL;
		$errCode    = (array_key_exists("errCode", $fields)) ? $fields['errCode'] : NULL;
		$error      = (array_key_exists("error", $fields)) ? $fields['error'] : NULL;
		$statusText = $errCode.":".$error;

		return $this->CI->response_result->saveResponseResult($this->getPlatformCode(), Response_result::FLAG_NORMAL,
			$method, 'submit', json_encode($resultAll), $statusCode, $statusText, NULL,
			array('player_id' => $player_id, 'related_id1' => $order_id, 'related_id2' => $secure_id));
	}

	public function setCurlProxyOptions($curl_resources){
        // set proxy
        $settle_proxy = FALSE;
        if ($settle_proxy === FALSE && !empty($this->call_socks5_proxy)) {
            $this->CI->utils->debug_log('http call with socks5 proxy', $this->call_socks5_proxy);
            curl_setopt($curl_resources, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($curl_resources, CURLOPT_PROXY, $this->call_socks5_proxy);
            if (!empty($this->call_socks5_proxy_login) && !empty($this->call_socks5_proxy_password)) {
                curl_setopt($curl_resources, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                curl_setopt($curl_resources, CURLOPT_PROXYUSERPWD, $this->call_socks5_proxy_login . ':' . $this->call_socks5_proxy_password);
            }
            $settle_proxy = TRUE;
        }

        if($settle_proxy === FALSE && !empty($this->call_http_proxy_host)){
            //http proxy
            $this->CI->utils->debug_log('http call with http proxy', $this->call_http_proxy_host);
            curl_setopt($curl_resources, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($curl_resources, CURLOPT_PROXY, $this->call_http_proxy_host);
            curl_setopt($curl_resources, CURLOPT_PROXYPORT,  (empty($this->call_http_proxy_port)) ? 3128 : $this->call_http_proxy_port);
            if (!empty($this->call_http_proxy_login) && !empty($this->call_http_proxy_password)) {
                curl_setopt($curl_resources, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                curl_setopt($curl_resources, CURLOPT_PROXYUSERPWD, $this->call_http_proxy_login . ':' . $this->call_http_proxy_password);
            }
        }

        return $this;
    }

	/**
	 * only post json
	 *
	 * @param  [type] $url    [description]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function postForm($url, $params) {

		return $this->submitPostForm($url, $params, true);

	}

	public function isPopupNewWindowOnDeposit(){
		return true;
	}

	public function addTransactionFee($orderId, $fee){

	}

	/**
	 * Rand submit amount
	 * plus/minus random amount to the origin amount for security reason
	 * amount_rand_unit = cent|dollar
	 * amount_rand_min|amount_rand_max = 1-99
	 * amount_rand_mode = minus|plus
	 * min_deposit = {int}
	 * max_deposit = {int}
	 * @param float $amount
	 * @return float
	 */
	public function randAmount($amount) {
		$unit = ($this->getSystemInfo('amount_rand_unit')) ? $this->getSystemInfo('amount_rand_unit') : "cent";
		$min = ($this->getSystemInfo('amount_rand_min')) ? $this->getSystemInfo('amount_rand_min') : 0;
		$max = ($this->getSystemInfo('amount_rand_max')) ? $this->getSystemInfo('amount_rand_max') : 0;
		$minDeposit = ($this->getSystemInfo('min_deposit')) ? $this->getSystemInfo('min_deposit') : 0;
		$maxDeposit = ($this->getSystemInfo('max_deposit')) ? $this->getSystemInfo('max_deposit') : 1000000;
		$randMode = $this->getSystemInfo('amount_rand_mode') ? $this->getSystemInfo('amount_rand_mode') : "minus";

		if($amount >= $maxDeposit){
			$randMode = 'minus';
		}
		else if($amount <= $minDeposit){
			$randMode = 'plus';
		}

		#default: rand decimal
		#110.12 => ok; 111 => fail; 110 => fail
		if($unit == "cent" && $amount*100%100 == 0) {
			if($randMode == 'minus'){
				$amount = $amount - rand($min,$max)/100; #rand(1,299) => 0.01-2.99
			}
			else if($randMode == 'plus'){
				$amount = $amount + rand($min,$max)/100;
			}
		}
		#rand digit in ones
		#110.12 => ok; 111 => ok; 110 => fail
		else if($unit == "dollar" && ($amount%10 == 0 && $amount*100%100 == 0)) {
			if($randMode == 'minus'){
				$amount = $amount - rand($min,$max); #rand(1,299) => 1-299
			}
			else if($randMode == 'plus'){
				$amount = $amount + rand($min,$max);
			}
		}

		return $amount;
	}

	/**
	 * Check callback for rand amount
	 *
	 * @param float $origin send $order->amount
	 * @param float $randed send $fields['']
	 * @return boolean
	 */
	public function randAmountOverLimit($origin, $randed){
		$unit = ($this->getSystemInfo('amount_rand_unit')) ? $this->getSystemInfo('amount_rand_unit') : "cent";
		$limit = ($this->getSystemInfo('amount_rand_max')) ? $this->getSystemInfo('amount_rand_max') : 0;
		if($unit == "cent"){
			$limit = $limit/100;
		}

		$checkLimit = abs($origin - $randed);
		$this->utils->debug_log("=====================randAmountOverLimit checkLimit [$checkLimit], limit [$limit]");

		if ($checkLimit <= $limit)
			return false;
		else
			return true;
	}

	// --------- For Withdrawal ------------
	public function submitWithdrawRequest($bank, $accNum, $name, $amount, $transId) {
		$result = array('success' => false);
		if(!$this->isAllowWithdraw()) {
			$result['message'] = lang("Withdraw not allowed with this API");
			$this->utils->debug_log($result);
			return $result;
		}

		$fullParams = $this->getWithdrawParams($bank, $accNum, $name, $amount, $transId);

		if(isset($fullParams['success'])) {
			if($fullParams['success'] == false) {
				$result['message'] = $fullParams['message'];
				$this->utils->debug_log($result);
				return $result;
			}
		}

		$url = $this->getWithdrawUrl();
		$postString = is_array($fullParams) ? http_build_query($fullParams) : $fullParams;
		$curlConn = curl_init($url);
		curl_setopt($curlConn, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curlConn, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
		curl_setopt($curlConn, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlConn, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlConn, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curlConn, CURLOPT_POSTFIELDS, $postString);

        $this->setCurlProxyOptions($curlConn);

        if(!empty($this->_custom_curl_header)){
            curl_setopt($curlConn, CURLOPT_HTTPHEADER, $this->_custom_curl_header);
        }

		$result['result'] = curl_exec($curlConn);
		$result['success'] = (curl_errno($curlConn) == 0);
		$result['message'] = curl_error($curlConn);
		$this->utils->debug_log("Post String", $postString, "Result", $result);
		curl_close($curlConn);

		$decodedResult = $this->decodeResult($result['result']);
		$this->utils->debug_log("Decoded Result", $decodedResult);
		return $decodedResult;
	}

	# Note: to avoid breaking current APIs, these abstract methods are not marked abstract
	# APIs with withdraw function need to implement these methods
	## This function returns the URL to submit withdraw request to
	public function getWithdrawUrl() {}
	## This function returns the params to be submitted to the withdraw URL
	public function getWithdrawParams($bank, $accNum, $name, $amount, $transId) {}
	## This function takes in the return value of the URL and translate it to the following structure
	## array('success' => false, 'message' => 'Error message')
	public function decodeResult($resultString) {}
	## This function provides a way to manually check withdraw status. Useful when API does not provide a callback.
	## Returns array('success' => false, 'payment_fail' => false, 'message' => 'Error message')
	## 'success' means whether payment is successful, 'payment_fail' means if payment is not successful, shall we mark it as failed or shall we wait
	public function checkWithdrawStatus($orderId) {}
    public function checkDepositStatus($orderId) {}

	/**
	 * Generate url string from data array
	 *
	 * ref: origin from __createLinkString() in the "abstract_payment_api_lepay.php"
	 *
	 * @param array $para   input data array
	 * @param bool  $sort   (default: false ) sort data array by key or not
	 * @param bool  $encode (default: false ) apply urlencode or not
	 * @return string
	 */
	public static function genUrlParamsArray($para, $sort=false, $encode=false){
		if($para == NULL || !is_array($para)){
			return "";
		}

		$linkString = "";
		if ($sort) {
			//$para = $this->__argSort ( $para );
			ksort ( $para );
			reset ( $para );
		}
		foreach ( $para as $key => $value) {
			if ($encode) {
				$value = urlencode ( $value );
			}
			$linkString .= $key . "=" . $value . "&";
		}
		// 去掉最后一个&字符
		$linkString = substr ( $linkString, 0,  -1 );

		return $linkString;
	}

	public function getAmountHint(){
		$this->CI->load->library(array('language_function'));

		#deposit_hint_english|chinese|indonesian|vietnamese|korean|thai
		$language = $this->CI->language_function->getCurrentLanguageName();

		$default_custom_hint = $this->getSystemInfo('deposit_hint');
		$custom_hint = $this->getSystemInfo('deposit_hint_'.$language, $default_custom_hint);

	    $default_hint = NULL;

	    if($this->utils->isEnabledFeature('show_decimal_amount_hint')){
            $default_hint = lang('Please enter amount with decimal values for faster processing.');
        }

        if($this->utils->isEnabledFeature('enable_deposit_amount_note')){
            $default_hint = lang('collection_deposit_amount');
        }

	    return (!empty($custom_hint)) ? $custom_hint : $default_hint;
    }

    public function showPaymentPopWindowStatus(){
        return intval($this->getSystemInfo('popInWindow'));
    }

    public function allowSubmitSameOrderId() {
		$checkIfAllowSubmitSameOrderId = $this->getSystemInfo('allow_submit_same_order_id') ? $this->getSystemInfo('allow_submit_same_order_id') : false;
		return $checkIfAllowSubmitSameOrderId;
    }

    /**
     * Check if now over valid hour
     * valid_hour = {int}
     * @param  int  $orderId
     * @return boolean
     */
   public function isOrderExpired($orderId){
		#config need to set callback_none_valid_hour_api_list ,string all or array api id
		$callback_none_valid_hour_api_list = $this->CI->config->item('callback_none_valid_hour_api_list');
		if(is_array($callback_none_valid_hour_api_list)) {
			$compare_array = in_array($this->PLATFORM_CODE, $callback_none_valid_hour_api_list);
			$this->utils->debug_log("====Show compare_array====", $compare_array,$this->PLATFORM_CODE);
			if($compare_array){
				return false;
			}
		}elseif($callback_none_valid_hour_api_list == 'all'){
			return false;
		}

		if(substr($orderId, 0, 1) == 'W') {
			$row = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);
			$created_at = $row['dwDateTime'];
		}
		else{
			$row = $this->CI->sale_order->getSaleOrderById($orderId);
			$created_at = $row->created_at;
		}

		$validHour = ($this->getSystemInfo('valid_hour')) ? $this->getSystemInfo('valid_hour') : self::VALID_HOUR;
		$interval  = ( strtotime(date('Y-m-d H:i:s')) - strtotime($created_at) )/3600;
		if(	$interval < $validHour ){
			return false;
		}
        return true;
    }

    public function getValidHour(){
		$validHour = ($this->getSystemInfo('valid_hour')) ? $this->getSystemInfo('valid_hour') : self::VALID_HOUR;
        return $validHour;
    }

     public function getPaymentFormTarget(){
		$formtarget = self::SUBMIT_FORM_TARGET_BLANK;
		if($this->CI->utils->getConfig('self_target_user_agent')) {
			$self_target_user_agent_list = $this->CI->utils->getConfig('self_target_user_agent');
			foreach ($self_target_user_agent_list as $user_agent_key) {
				# won't open new page when user agent has this key
				if(strpos($_SERVER['HTTP_USER_AGENT'], $user_agent_key)) {
					$formtarget = self::SUBMIT_FORM_TARGET_SELF;
				}
			}
		}
		$formtarget = $this->getSystemInfo('submit_form_target', $formtarget);
		return $formtarget;
	}

    public function getFloatAmountLimit(){
        $floatAmountLimit = ($this->getSystemInfo('float_amount_limit')) ? $this->getSystemInfo('float_amount_limit') : NULL;
        return $floatAmountLimit;
    }

    public function gameAmountToDBByCurrency($amount, $date, $base, $target) {
        $this->CI->load->model('daily_currency');
        $rate = $this->CI->daily_currency->getCurrentCurrencyRate($date, $base, $target);
        $this->CI->utils->debug_log("========================gameAmountToDBByCurrency rate", $rate);
        if(empty($rate)){
            return $amount = round($amount*$this->currency_rate, 2);
        }
        return $amount = round($amount*$rate->rate, 2);
    }

    public function getExternalApiResponseByOrder($saleOrder){}

    /**
     * Use crypt instead of password_hash
     * @param  string  $hashVal
     * @param  string  $salt
     * @return string
     */
	public function passwordHashToCrypt($hashVal, $salt=null){
		$signature = crypt($hashVal,$salt);
		return $signature;
	}

	public function getPlayerBank(){
		$playerId = $this->utils->getLoggedPlayerId();
		$playerBankListType = $this->getSystemInfo('player_bank_list_type');
		$convertToBankCode = $this->getSystemInfo('convert_to_playerbank_code');
		$paymentSupportPlayerBankList = array();

		switch ($playerBankListType){
			case 'withdrawal':
				$playerBankList = $this->CI->playerbankdetails->getPlayerWithdrawalBankList($playerId);
				break;
			case 'deposit':
			default:
				$playerBankList = $this->CI->playerbankdetails->getPlayerDepositBankList($playerId);
				break;
		}

        if(is_array($playerBankList) && is_array($convertToBankCode)){
        	foreach ($playerBankList as $playerbank) {
        		$playerBankcode = $this->getConvertBankCode($convertToBankCode, $playerbank['bankTypeId']);
        		$playerbank['bankCode'] = $playerBankcode;
        		$paymentSupportPlayerBankList[] =  $playerbank;
        	}
        }

        return $paymentSupportPlayerBankList;
	}

	 /**
     * Get bankcode through banktype id
     * @param  array  $convertToBankCode
     * @param  string  $bankTypeId
     * @return string
     */
	public function getConvertBankCode($convertToBankCode, $bankTypeId){

		if(is_array($convertToBankCode)){
            foreach ($convertToBankCode as $convertBankTypeId => $convertBankCode) {
                if( $bankTypeId == $convertBankTypeId){
                    $bankCode = $convertBankCode;
                }
            }
        	return $bankCode;
        }else{
        	return null;
        }
	}

}
///END OF FILE///////////
