<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Gold_deluxe_service_api extends BaseController {

	CONST CODE_UNKOWN_ERROR = -1;
	CONST CODE_SUCCESS = 0;
	CONST CODE_INVALID_PARAMETER = 1;
	CONST CODE_INVALID_TOKEN_ID = 2;
	CONST CODE_BET_ALREADY_SETTLED = 3;
	CONST CODE_BET_DOES_NOT_EXIST = 4;
	CONST CODE_BET_ALREADY_EXIST = 5;
	CONST CODE_ACCOUNT_LOCKED = 6;
	CONST CODE_INSUFFUCIENT_FUNDS = 7;
	CONST CODE_RETRY_TRANSACTION = 8;
	CONST CODE_INSUFFUCIENT_FUNDS_1 = 201;
	CONST CODE_ACCOUNT_LOCKED_1 = 202;
	CONST CODE_ABOVE_PLAYER_LIMIT_1 = 206;

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','gd_seamless_wallet_transactions','player_model'));
		$multiple_currency_domain_mapping = $this->utils->getConfig('gd_seamless_multiple_currency_domain_mapping');
		$host_name =  $_SERVER['HTTP_HOST'];
		$this->game_platform_id = GD_SEAMLESS_API;
		if (!empty($multiple_currency_domain_mapping) && array_key_exists($host_name,$multiple_currency_domain_mapping)) {
		    $this->game_platform_id  = $multiple_currency_domain_mapping[$host_name];
		}
		$this->game_api = $this->utils->loadExternalSystemLibObject($this->game_platform_id);
		$this->currency = $this->game_api->getSystemInfo('currency','THB');


		//for xml
		$this->namespace_soap = $this->game_api->getSystemInfo('namespace_soap','SOAP-ENV');
		$this->namespace_body = $this->game_api->getSystemInfo('namespace_body','ns1');
		$this->namespace = $this->game_api->getSystemInfo('namespace','http://player.staging.sexycasino.t1t.in/gd_servive_api');//staging namespace
		$this->default_response_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
			<SOAP-ENV:Envelope 
				xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
				xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
				xmlns:ns1="'.$this->namespace.'" 
				xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
				SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
			</SOAP-ENV:Envelope>'
		);
	}

	public function index(){
		/* Sample xml request
		<?xml version="1.0" encoding="UTF-8"?>
		<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://testnamespace.org">
		<SOAP-ENV:Body>
		<ns1:GetUserBalance>
		<ns1:userId>tonysin</ns1:userId>
		<ns1:currency>CNY</ns1:currency>
		<ns1:loginToken>a1b2c3d4</ns1:loginToken>
		</ns1:GetUserBalance>
		</SOAP-ENV:Body>
		</SOAP-ENV:Envelope>

		Sample xml response
		<?xml version="1.0" encoding="UTF-8"?>
		<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://testnamespace.org" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
		<SOAP-ENV:Body>
		<ns1:GetUserBalanceResponse>
		<StatusCode xsi:type="xsd:int">0</StatusCode>
		<UserBalance xsi:type="xsd:float">88888888</UserBalance>
		</ns1:GetUserBalanceResponse>
		</SOAP-ENV:Body>
		</SOAP-ENV:Envelope>
		*/


		$request = file_get_contents('php://input');
		$xml = new SimpleXMLElement($request);
		$xml_array = (array)$xml->children($this->namespace_soap, true)->Body->children($this->namespace_body, true);
		$xml_array = json_decode(json_encode($xml_array), true);

		if(!empty($xml_array)){
			$key = array_key_first($xml_array);
			$method = lcfirst($key);
			$xml_array[$key]['method'] = $method;
			$params = array('request_xml' => $request, 'request_array' => $xml_array[$key]);

			if ( method_exists($this, $method) ) {
				return call_user_func(array($this,$method),$params);   
		    }

			return $this->returnXml('Invalid Method.');
		} else {
			return $this->returnXml('Invalid Namespace.');
		}	
	}

	/**
     * overview : generate xml response
     *
     * @param bool $success
     * @param int $status_error_code
     * @param string $action
     * @param double $balance
     * @return xml
     */

	public function getXmlResponse($success, $status_error_code, $action, $balance = null){
		$sxe = $this->default_response_xml;
		$sxe_body = $sxe->addChild('Body');
		$response_body = ucfirst($action).'Response';
		$sxe_params = $sxe_body->addChild($response_body, null, $this->namespace);

		//status
		$sxe_status_code = $sxe_params->addChild('StatusCode',$status_error_code,'');
		$sxe_status_code->addAttribute('xsi:type', 'xsd:int');
		//userbalance
		$sxe_balance = $sxe_params->addChild('UserBalance',$balance,'');
		$sxe_balance->addAttribute('xsi:type', 'xsd:float');

		$response =  $sxe->asXML();
		return $response;
	}

	/**
     * overview : generate user details
     *
     * @param array $params
     * @return array
     */

	private function getUserDetails($params){
		$balance = null;

		//params
		$game_username  = isset($params['userId']) ? $params['userId'] : null;
		$login_token = isset($params['loginToken']) ? $params['loginToken'] : null;

		$player_id = $this->game_api->getPlayerIdByToken($login_token);
		$player_username = $this->game_api->getPlayerUsernameByGameUsername($game_username);

		if($player_username){
			$balance = $this->game_api->queryPlayerBalance($player_username)['balance'];
		}

		return array($player_id, $player_username, $balance);
	}

	/**
     * overview : get user balance
     *
     * @param array $params
     * @return xml
     */

	private function getUserBalance($params){
		$request = isset($params['request_xml']) ? $params['request_xml'] : null;
		$request_array = isset($params['request_array']) ? $params['request_array'] : null;

		list($player_id, $player_username, $balance) = $this->getUserDetails($request_array);
		
		$success = FALSE;

		try {

			if(!$player_id){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_TOKEN_ID, __FUNCTION__);
				throw new Exception($error);
			}

			if(!$player_username){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_PARAMETER, __FUNCTION__);
				throw new Exception($error);
			}

			$success = TRUE;
			$response = $this->getXmlResponse(TRUE, self::CODE_SUCCESS, __FUNCTION__, $balance);
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = $e->getMessage();
		}

		$this->saveResponseResult($success, __FUNCTION__, $request, $response);
		return $this->returnXml($response);
	}

	/**
     * overview : process transaction
     *
     * @param array $params
     * @return array
     */

	private function processTransaction($params){
		
		$success = false;
		$status_code = self::CODE_UNKOWN_ERROR;//default status code

		$external_uniqueid = $params['method'].$params['transactionId'];
		$isTransactionExist = $this->CI->gd_seamless_wallet_transactions->isTransactionExist($external_uniqueid);

		list($player_id, $player_username, $balance) = $this->getUserDetails($params);
		$before_balance = $balance;

		if(!$isTransactionExist){
			switch ($params['method']) {
				case 'credit':
				case 'cancel':
					$status_code = self::CODE_BET_DOES_NOT_EXIST;//default status code for credit, need to check bet first
					$debitId = 'debit'.$params['transactionId'];
					$isDebitExist = $this->CI->gd_seamless_wallet_transactions->isTransactionExist($debitId);
					if($isDebitExist){
						list($success, $status_code, $balance, $before_balance) = $this->addBalance($params);
					} 
					break;
				case 'debit':
				case 'tip':
					list($success, $status_code, $balance, $before_balance) = $this->subtractBalance($params);
					break;
			}
		} else {
			switch ($params['method']) {
				case 'credit':
					$status_code = self::CODE_BET_ALREADY_SETTLED;
					break;
				case 'debit':
					$status_code = self::CODE_BET_ALREADY_EXIST;
					break;
			}
		}

		return array($success, $status_code, $balance, $before_balance);
	}

	/**
     * overview : Process debit transaction of the user
     *
     * @param array $params
     * @return xml
     */

	public function debit($params){
		$request = isset($params['request_xml']) ? $params['request_xml'] : null;
		$request_array = isset($params['request_array']) ? $params['request_array'] : null;

		list($player_id, $player_username, $balance) = $this->getUserDetails($request_array);
		
		$success = FALSE;

		try {

			if(!$player_id){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_TOKEN_ID, __FUNCTION__);
				throw new Exception($error);
			}

			if(!$player_username){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_PARAMETER, __FUNCTION__);
				throw new Exception($error);
			}

			list($success, $status_code, $balance, $before_balance) = $this->processTransaction($request_array);

			$response = $this->getXmlResponse($success, $status_code, __FUNCTION__, $balance);
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);

			if($success){
				$request_array['response_result_id'] = $response_result_id;
				$request_array['after_balance'] = $balance;
				$request_array['before_balance'] = $before_balance;
				$this->saveTransaction($request_array);
			}
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = $e->getMessage();
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);
		}

		return $this->returnXml($response);
	}

	/**
     * overview : A tip which needs to debit or deduct an amount from the user
     *
     * @param array $params
     * @return xml
     */

	public function tip($params){
		$request = isset($params['request_xml']) ? $params['request_xml'] : null;
		$request_array = isset($params['request_array']) ? $params['request_array'] : null;

		list($player_id, $player_username, $balance) = $this->getUserDetails($request_array);
		
		$success = FALSE;

		try {

			if(!$player_id){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_TOKEN_ID, __FUNCTION__);
				throw new Exception($error);
			}

			if(!$player_username){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_PARAMETER, __FUNCTION__);
				throw new Exception($error);
			}

			list($success, $status_code, $balance, $before_balance) = $this->processTransaction($request_array);

			$response = $this->getXmlResponse($success, $status_code, __FUNCTION__, $balance);
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);

			if($success){
				$request_array['response_result_id'] = $response_result_id;
				$request_array['after_balance'] = $balance;
				$request_array['before_balance'] = $before_balance;
				$this->saveTransaction($request_array);
			}
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = $e->getMessage();
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);
		}

		return $this->returnXml($response);
	}

	/**
     * overview : Cancel debit transaction
     *
     * @param array $params
     * @return xml
     */

	public function cancel($params){
		$request = isset($params['request_xml']) ? $params['request_xml'] : null;
		$request_array = isset($params['request_array']) ? $params['request_array'] : null;

		list($player_id, $player_username, $balance) = $this->getUserDetails($request_array);
		
		$success = FALSE;

		try {

			if(!$player_username){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_PARAMETER, __FUNCTION__);
				throw new Exception($error);
			}

			list($success, $status_code, $balance, $before_balance) = $this->processTransaction($request_array);

			$response = $this->getXmlResponse($success, $status_code, __FUNCTION__, $balance);
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);

			if($success){
				$request_array['response_result_id'] = $response_result_id;
				$request_array['after_balance'] = $balance;
				$request_array['before_balance'] = $before_balance;
				$this->saveTransaction($request_array);
			}
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = $e->getMessage();
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);
		}

		return $this->returnXml($response);
	}

	/**
     * overview : Add or credit any amount to the user
     *
     * @param array $params
     * @return array
     */

	private function addBalance($params){

		list($player_id, $player_username, $balance) = $this->getUserDetails($params);

		//allow expired token for credit
		if(empty($player_id) && $player_username){ //create player id
			$player_id = $this->game_api->getPlayerIdFromUsername($player_username);
		}
		
		$credit_amount = isset($params['amount'] ) ? (float)$params['amount'] : null;
		$before_balance = $balance;

		$success = false;//default 
		$status_code = self::CODE_UNKOWN_ERROR;//default status code
		
		$success = $this->lockAndTransForPlayerBalance($player_id, function() use( $player_id, $credit_amount, $player_username, &$balance, &$status_code) {
			$trans = $this->wallet_model->incSubWallet($player_id, $this->game_api->getPlatformCode(), $credit_amount);
			if($trans){
				$balance = $this->game_api->queryPlayerBalance($player_username)['balance'];
				$status_code = self::CODE_SUCCESS;
			}
			return $trans;
		});
		
		return array($success, $status_code, $balance, $before_balance);
	}

	/**
     * overview : Subtract or debit any amount from the user
     *
     * @param array $params
     * @return array
     */

	private function subtractBalance($params){

		list($player_id, $player_username, $balance) = $this->getUserDetails($params);

		$debit_amount = isset($params['amount'] ) ? (float)$params['amount'] : null;
		$before_balance = $balance;

		$success = false;//default
		$status_code = self::CODE_UNKOWN_ERROR;//default status code

		if($this->utils->compareResultFloat($balance, '<', $debit_amount)){
			$status_code = self::CODE_INSUFFUCIENT_FUNDS;
		}
		else {
			$success = $this->lockAndTransForPlayerBalance($player_id, function() use( $player_id, $debit_amount, $player_username, &$balance, &$status_code) {
				$trans = $this->wallet_model->decSubWallet($player_id, $this->game_api->getPlatformCode(), $debit_amount);
				if($trans){
					$balance = $this->game_api->queryPlayerBalance($player_username)['balance'];
					$status_code = self::CODE_SUCCESS;
				}
				return $trans;
			});
		}
		return array($success, $status_code, $balance, $before_balance);
	}

	/**
     * overview : Save generated transaction of the user into the database
     *
     * @param array $params
     * @return id
     */

	private function saveTransaction($params){
		$external_uniqueid = isset($params['transactionId']) ? $params['method'].$params['transactionId'] : null;
		$data = array(
			"user_id" => isset($params['userId']) ? $params['userId'] : null,
			"game_id" => isset($params['gameId']) ? $params['gameId'] : null,
			"game_type" => isset($params['gameType']) ? $params['gameType'] : null,
			"transaction_id" => isset($params['transactionId']) ? $params['transactionId'] : null,
			"amount" => isset($params['amount']) ? $params['amount'] : null,
			"currency" => isset($params['currency']) ? $params['currency'] : null,
			"ip_address" => isset($params['ipAddress']) ? $params['ipAddress'] : null,
			"game_view" => isset($params['gameView']) ? $params['gameView'] : null,
			"client_type" => isset($params['clientType']) ? $params['clientType'] : null,
			"valid_betAmount" => isset($params['validBetAmount']) ? $params['validBetAmount'] : null,
			"bet_info" => isset($params['betInfo']) ? json_encode($params['betInfo']) : null,
			"login_token" => isset($params['loginToken']) ? $params['loginToken'] : null,
			"tip_id" => isset($params['tip_id']) ? $params['tip_id'] : null,
			"anchor_id" => isset($params['anchorId']) ? $params['anchorId'] : null,
			"date_time" => $this->utils->getNowForMysql(),
			"action" => isset($params['method']) ? $params['method'] : null,

			//sbe data
			"before_balance" => isset($params['before_balance']) ? $params['before_balance'] : null,
			"after_balance" => isset($params['after_balance']) ? $params['after_balance'] : null,
			"response_result_id" => isset($params['response_result_id']) ? $params['response_result_id'] : null,
			"external_uniqueid" => $external_uniqueid,
		);

		return $this->CI->gd_seamless_wallet_transactions->insertRow($data);
	}

	/**
     * overview : Process credit transaction of the user
     *
     * @param array $params
     * @return xml
     */

	public function credit($params){

		$request = isset($params['request_xml']) ? $params['request_xml'] : null;
		$request_array = isset($params['request_array']) ? $params['request_array'] : null;

		list($player_id, $player_username, $balance) = $this->getUserDetails($request_array);
		
		$success = FALSE;

		try {

			if(!$player_username){
				$error = $this->getXmlResponse(FALSE, self::CODE_INVALID_PARAMETER, __FUNCTION__);
				throw new Exception($error);
			}

			list($success, $status_code, $balance, $before_balance) = $this->processTransaction($request_array);

			$response = $this->getXmlResponse($success, $status_code, __FUNCTION__, $balance);
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);

			if($success){
				$request_array['response_result_id'] = $response_result_id;
				$request_array['after_balance'] = $balance;
				$request_array['before_balance'] = $before_balance;
				$this->saveTransaction($request_array);
			}
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = $e->getMessage();
			$response_result_id = $this->saveResponseResult($success, __FUNCTION__, $request, $response);
		}

		return $this->returnXml($response);
	}

	/**
     * Save current request from the provider and its corresponding response 
     *
     * @param bool $success
     * @param string $callMethod
     * @param xml $params
     * @param xml $response
     * @return id
     */

	private function saveResponseResult($success, $callMethod, $params, $response){
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResult(
        	$this->game_platform_id, 
        	$flag, 
        	$callMethod, 
        	json_encode($params), 
        	json_encode($response), 
        	200, 
        	null, 
        	null
        );
    }
}

///END OF FILE////////////