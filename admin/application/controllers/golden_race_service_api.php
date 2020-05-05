<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Golden_race_service_api extends BaseController {

	const ACTION_LOGIN = "login";
	const ACTION_LOGOUT = "logout";
	const SUCCESS = 200;

	const INVALID_REMOTE_SERVICE_IDENTIFIER = 101;
	const INVALID_PLATFORM_IDENTIFIER = 102;
	const INCOMPLETE_OR_MALFORMED_REQUEST = 103;
	const UNKNOWN_REQUEST = 104;
	const REQUEST_PROCESSING_SERVICES_UNAVAILABLE = 105;
	const INVALID_SECURE_TOKEN = 106;
	const INSUFFICIENT_FUNDS = 107;
	const PLAYER_ACCOUNT_LOCKED = 108;
	const WAGER_LIMIT_EXCEEDED = 109;
	const TRANSACTION_FAILED = 110;
	const UNSUPPORTED_GAMEID = 111;
	const GAME_CYCLE_DOES_NOT_EXIST = 112;
	const INCORRECT_PARAMETERS_FOR_A_PLAYER_SESSION = 113;
	const INCORRECT_PLAYER_IDENTIFIER_FOR_SECURE_TOKEN = 114;
	const GAME_CYCLE_EXISTS = 115;
	const TRANSACTION_ALREADY_EXISTS = 116;
	const TRANSACTION_DOES_NOT_EXISTS = 117;
	const GAME_CYCLE_ALREADY_CLOSED = 118;

	const ACTION_DEBIT = "debit";
	const ACTION_CREDIT = "credit";
	const ACTION_ROLLBACK = "rollback";


	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','golden_race_transactions','player_model'));
		$multiple_currency_domain_mapping = $this->utils->getConfig('golden_race_multiple_currency_domain_mapping');
		$host_name =  $_SERVER['HTTP_HOST'];
		$this->game_platform_id = GOLDEN_RACE_GAMING_API;
		if (array_key_exists($host_name,$multiple_currency_domain_mapping) && !empty($multiple_currency_domain_mapping)) {
		    $this->game_platform_id  = $multiple_currency_domain_mapping[$host_name];
		}
		$this->game_api = $this->utils->loadExternalSystemLibObject($this->game_platform_id);
		$this->currency = $this->game_api->getSystemInfo('currency','THB');
		$this->group = $this->game_api->getSystemInfo('group','dev');
		$this->private_key = $this->game_api->getSystemInfo('private_key','sDeUx9AcAEq0bHl7KEjZ');
		$this->use_parent_agent = $this->game_api->getSystemInfo('use_parent_agent',false);
	}

	private function doTransaction($request_arr, $method){
		$action = isset($request_arr['action']) ? strtolower($request_arr['action']) : null;
		$transaction_amount = isset($request_arr['transactionAmount']) ? $request_arr['transactionAmount'] : null;
		$game_username = isset($request_arr['playerId']) ? $request_arr['playerId'] : null;
		$fingerprint = isset($request_arr['fingerprint']) ? $request_arr['fingerprint'] : null;
		$request_id = isset($request_arr['requestId']) ? $request_arr['requestId'] : null;
		$token = isset($request_arr['sessionId']) ? $request_arr['sessionId'] : null;
		$transaction_id = isset($request_arr['transactionId']) ? $request_arr['transactionId'] : null;
		$transasction_type = isset($request_arr['transactionType']) ? $request_arr['transactionType'] : null;
		$currency = isset($request_arr['currency']) ? $request_arr['currency'] : null;
		$game_cycle = isset($request_arr['gameCycle']) ? $request_arr['gameCycle'] : null;
		$group = isset($request_arr['group']) ? $request_arr['group'] : null;
		$player_id = $this->game_api->getPlayerIdByToken($token);
		$player_name = $this->game_api->getPlayerUsernameByGameUsername($game_username);
		$extra = [];
		$agent = $this->CI->player_model->getAgentNameByPlayerId($player_id);
		if(!empty($agent) && $this->use_parent_agent){
			$this->group = $agent;
		}

		if(!$player_id && ($action == self::ACTION_ROLLBACK || $action == self::ACTION_CREDIT)){//allow expired token on rollback/credit
			$player_id = $this->game_api->getPlayerIdFromUsername($player_name);//get player id for credit amount of rollback
		}

		try {

			if(empty($request_arr)){
				$error = $this->getErrorSuccessMessage(false, self::INCOMPLETE_OR_MALFORMED_REQUEST, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if($currency != $this->currency || $method != $action){
				$error = $this->getErrorSuccessMessage(false, self::INCORRECT_PARAMETERS_FOR_A_PLAYER_SESSION, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if(empty($player_name)){
				$error = $this->getErrorSuccessMessage(false, self::INCORRECT_PLAYER_IDENTIFIER_FOR_SECURE_TOKEN, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if(!$player_id){
				$error = $this->getErrorSuccessMessage(false, self::INVALID_SECURE_TOKEN, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if($this->game_api->isBlocked($player_name)){
				$error = $this->getErrorSuccessMessage(false, self::PLAYER_ACCOUNT_LOCKED, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			$is_transactionIdExist = $this->CI->golden_race_transactions->isTransactionIdAlreadyExists($transaction_id,$action);
			if($is_transactionIdExist){
				$error = $this->getErrorSuccessMessage(false, self::TRANSACTION_ALREADY_EXISTS, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if($action == self::ACTION_DEBIT){
				$isGameCycleIdAlreadyExists = $this->CI->golden_race_transactions->isGameCycleIdAlreadyExists($game_cycle);
				//check if debit game cycle is exist
				if($isGameCycleIdAlreadyExists){
					$error = $this->getErrorSuccessMessage(false, self::GAME_CYCLE_EXISTS, $action, $request_arr, $extra);
					throw new Exception(json_encode($error));
				}
			} 

			if($action == self::ACTION_CREDIT || $action == self::ACTION_ROLLBACK){
				$isGameCycleClosed = $this->CI->golden_race_transactions->isGameCycleClosed($game_cycle);
				//check if game cycle is closed already
				if($isGameCycleClosed){
					$error = $this->getErrorSuccessMessage(false, self::GAME_CYCLE_ALREADY_CLOSED, $action, $request_arr, $extra);
					throw new Exception(json_encode($error));
				}

				$isGameCycleIdAlreadyExists = $this->CI->golden_race_transactions->isGameCycleIdAlreadyExists($game_cycle);
				//check if game cycle is existed 
				if(!$isGameCycleIdAlreadyExists){
					$error = $this->getErrorSuccessMessage(false, self::GAME_CYCLE_DOES_NOT_EXIST, $action, $request_arr, $extra);
					throw new Exception(json_encode($error));
				}

				if($action == self::ACTION_ROLLBACK){
					$isTransactionIdAlreadyExists = $this->CI->golden_race_transactions->isTransactionIdAlreadyExists($transaction_id,'debit');
					//check if debit transaction is not exist
					if(!$isTransactionIdAlreadyExists){//if debit transaction is not exist
						$error = $this->getErrorSuccessMessage(false, self::TRANSACTION_DOES_NOT_EXISTS, $action, $request_arr, $extra);
						throw new Exception(json_encode($error));
					}
				}
			}

			if($transaction_amount > $this->game_api->queryPlayerBalance($player_name)['balance'] && $method == self::ACTION_DEBIT){
				$error = $this->getErrorSuccessMessage(false, self::INSUFFICIENT_FUNDS, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			$trans_params = array(
				"transaction_id" => $transaction_id,
				"player_id" => $player_id,
				"player_name" => $player_name, 
				"transaction_amount" => $transaction_amount,
				"action" => $action,
				"transasction_type" => $transasction_type
			);

			list($trans_success, $previous_balance, $after_balance) = $this->debitCreditAmount($trans_params);

			if(!$trans_success){
				$error = $this->getErrorSuccessMessage(false, self::TRANSACTION_FAILED, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}


			$session_id = $this->game_api->getPlayerToken($player_id);
			$datetime = $this->utils->getNowDateTime();
			$timestamp = $datetime->format('c');

			$data = array(
				"playerId" => $game_username,
				"currency" => $this->currency,
				"balance" => $after_balance,
				"oldBalance" => $previous_balance,
				"transactionId" => $transaction_id,
				"sessionId" => $session_id,
				"group" => $this->group,
				"timestamp" => $timestamp,
				"requestId" => $request_id
			);
			
			$data_string= implode('', $data);
			$data['fingerprint'] = md5($data_string.$this->private_key);
			$extra['data'] = $data;

			$response = $this->getErrorSuccessMessage(true, self::SUCCESS, $action, $request_arr, $extra);
			$trans_record = array_merge($data,$response,$request_arr);
			// echo "<pre>";
			// print_r($trans_record);exit();
			$this->processTransactionRecord($trans_record);

			if($trans_success){
				$this->CI->golden_race_transactions->insertRow($trans_record);
				$this->utils->debug_log("GR_RECORD STRING ============================>", json_encode($trans_record));
			}

			$this->utils->debug_log("GR_DEBITCREDIT STRING ============================>", $data_string);
			$this->utils->debug_log("GR_DEBITCREDIT RESPONSE ============================>", $response);
			
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = json_decode($e->getMessage(),true);
		}
		unset($response['response_result_id']);
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function processTransactionRecord(&$trans_record){
		$data['action'] = isset($trans_record['action']) ? $trans_record['action'] : null;
		$data['sessionId'] = isset($trans_record['sessionId']) ? $trans_record['sessionId'] : null;
		$data['gameId'] = isset($trans_record['gameId']) ? $trans_record['gameId'] : null;
		$data['playerId'] = isset($trans_record['playerId']) ? $trans_record['playerId'] : null;
		$data['group'] = isset($trans_record['group']) ? $trans_record['group'] : null;
		$data['currency'] = isset($trans_record['currency']) ? $trans_record['currency'] : null;
		$data['gameCycle'] = isset($trans_record['gameCycle']) ? $trans_record['gameCycle'] : null;
		$data['gameCycleClosed'] = isset($trans_record['gameCycleClosed']) ? $trans_record['gameCycleClosed'] : null;
		$data['transactionId'] = isset($trans_record['transactionId']) ? $trans_record['transactionId'] : null;
		$data['transactionAmount'] = isset($trans_record['transactionAmount']) ? $trans_record['transactionAmount'] : null;
		$data['transactionCategory'] = isset($trans_record['transactionCategory']) ? $trans_record['transactionCategory'] : null;
		$data['transactionType'] = isset($trans_record['transactionType']) ? $trans_record['transactionType'] : null;
		$data['timestamp'] = isset($trans_record['timestamp']) ? $trans_record['timestamp'] : null;
		$data['requestId'] = isset($trans_record['requestId']) ? $trans_record['requestId'] : null;
		$data['siteId'] = isset($trans_record['siteId']) ? $trans_record['siteId'] : null;
		$data['fingerprint'] = isset($trans_record['fingerprint']) ? $trans_record['fingerprint'] : null;
		$data['before_balance'] = isset($trans_record['oldBalance']) ? $trans_record['oldBalance'] : null;
		$data['after_balance'] = isset($trans_record['balance']) ? $trans_record['balance'] : null;
		$data['response_result_id'] = isset($trans_record['response_result_id']) ? $trans_record['response_result_id'] : null;
		$data['external_uniqueid'] = $data['action'].'-'.$data['transactionId'];
		$trans_record = $data;
	}

	private function debitCreditAmount($params){
		$player_id = $params['player_id'];
		$player_name = $params['player_name'];
		$amount = $params['transaction_amount'];
		$action = $params['action'];
		$transasction_type = $params['transasction_type'];
		$transaction_id = $params['transaction_id'];

		$success = false;
		$controller = $this;
		$previous_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];
		if($action == self::ACTION_DEBIT){
			$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
				return $controller->wallet_model->decSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
			});
		} else {
			//note rollback means credit to player money, but need to check if debit transaction is already exist
			if($amount > 0){
				$is_debitTransactionIdExist = $this->CI->golden_race_transactions->isTransactionIdAlreadyExists($transaction_id,$transasction_type);
				if($action == self::ACTION_ROLLBACK && !$is_debitTransactionIdExist){//dont credit if debit transaction is not exist
					$success = true;
				} else {
					$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
						return $controller->wallet_model->incSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
					});
				}
				
			}
			else if($amount == 0) {
				$success = true;
			}	
		}
		$after_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];

		return array($success,$previous_balance, $after_balance);
	}



	public function debit(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);
		return $this->doTransaction($request_arr, self::ACTION_DEBIT);
	}

	public function credit(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);
		return $this->doTransaction($request_arr, self::ACTION_CREDIT);
	}

	public function rollback(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);
		return $this->doTransaction($request_arr, self::ACTION_ROLLBACK);
	}

	public function balance(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);

		$token = isset($request_arr['sessionId']) ? $request_arr['sessionId'] : null;
		$request_id = isset($request_arr['requestId']) ? $request_arr['requestId'] : null;
		$player_id = $this->game_api->getPlayerIdByToken($token);
		$fingerprint = isset($request_arr['fingerprint']) ? $request_arr['fingerprint'] : null;
		$action = isset($request_arr['action']) ? strtolower($request_arr['action']) : null;

		$extra = [];
		$agent = $this->CI->player_model->getAgentNameByPlayerId($player_id);
		if(!empty($agent) && $this->use_parent_agent){
			$this->group = $agent;
		}
		try {

			if(empty($request_arr)){
				$error = $this->getErrorSuccessMessage(false, self::INCOMPLETE_OR_MALFORMED_REQUEST, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if(!$player_id){
				$error = $this->getErrorSuccessMessage(false, self::INVALID_SECURE_TOKEN, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			$game_username = $this->game_api->getGameUsernameByPlayerId($player_id);
			$player_name = $this->game_api->getPlayerUsernameByGameUsername($game_username);

			if($this->game_api->isBlocked($player_name)){
				$error = $this->getErrorSuccessMessage(false, self::PLAYER_ACCOUNT_LOCKED, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			//balance
			$current_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];

			$session_id = $this->game_api->getPlayerToken($player_id);
			$datetime = $this->utils->getNowDateTime();
			$timestamp = $datetime->format('c');

			$data = array(
				"playerId" => $game_username,
				"currency" => $this->currency,
				"balance" => $current_balance,
				"sessionId" => $session_id,
				"group" => $this->group,
				"timestamp" => $timestamp,
				"requestId" => $request_id
			);

			$data_string= implode('', $data);
			$data['fingerprint'] = md5($data_string.$this->private_key);
			$extra['data'] = $data;

			$response = $this->getErrorSuccessMessage(true, self::SUCCESS, $action, $request_arr, $extra);
			$this->utils->debug_log("GR_BALANCE STRING ============================>", $data_string);
			$this->utils->debug_log("GR_BALANCE RESPONSE ============================>", $response);
			
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = json_decode($e->getMessage(),true);
		}
		unset($response['response_result_id']);
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function login(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);
		return $this->doLoginLogout($request_arr);
	}

	public function logout(){
		$request_json = file_get_contents('php://input');
		$request_arr = json_decode($request_json,true);
		return $this->doLoginLogout($request_arr);
	}

	public function doLoginLogout($request_arr){
		$token = isset($request_arr['token']) ? $request_arr['token'] : null;
		$action = isset($request_arr['action']) ? strtolower($request_arr['action']) : null;
		if($action == self::ACTION_LOGOUT){
			$token = isset($request_arr['sessionId']) ? $request_arr['sessionId'] : null;
		}
		$request_id = isset($request_arr['requestId']) ? $request_arr['requestId'] : null;
		$player_id = $this->game_api->getPlayerIdByToken($token);
		$fingerprint = isset($request_arr['fingerprint']) ? $request_arr['fingerprint'] : null;

		$extra = [];
		$agent = $this->CI->player_model->getAgentNameByPlayerId($player_id);
		if(!empty($agent) && $this->use_parent_agent){
			$this->group = $agent;
		}
		try {

			if(empty($request_arr)){
				$error = $this->getErrorSuccessMessage(false, self::INCOMPLETE_OR_MALFORMED_REQUEST, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			if(!$player_id){
				$error = $this->getErrorSuccessMessage(false, self::INVALID_SECURE_TOKEN, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			$game_username = $this->game_api->getGameUsernameByPlayerId($player_id);
			$player_name = $this->game_api->getPlayerUsernameByGameUsername($game_username);

			if($this->game_api->isBlocked($player_name)){
				$error = $this->getErrorSuccessMessage(false, self::PLAYER_ACCOUNT_LOCKED, $action, $request_arr, $extra);
				throw new Exception(json_encode($error));
			}

			//balance
			$current_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];

			$session_id = $this->game_api->getPlayerToken($player_id);
			$datetime = $this->utils->getNowDateTime();
			$timestamp = $datetime->format('c');

			$data = array(
				"playerId" => $game_username,
				"currency" => $this->currency,
				"playerNickname" => $game_username,
				"balance" => $current_balance,
				"sessionId" => $session_id,
				"group" => $this->group,
				"timestamp" => $timestamp,
				"requestId" => $request_id
			);

			$data_string= implode('', $data);
			$data['fingerprint'] = md5($data_string.$this->private_key);
			$extra['data'] = $data;

			$response = $this->getErrorSuccessMessage(true, self::SUCCESS, $action, $request_arr, $extra);
			$this->utils->debug_log("GR_LOGIN_LOGOUT STRING ============================>", $data_string);
			$this->utils->debug_log("GR_LOGIN_LOGOUT RESPONSE ============================>", $response);
			
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = json_decode($e->getMessage(),true);
		}
		unset($response['response_result_id']);
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function getErrorSuccessMessage($status, $code, $action, $request, $extra = null){
		switch ($code) {
			case self::INVALID_REMOTE_SERVICE_IDENTIFIER:
				$message = lang('Invalid remote service identifier.');
				break;
			case self::INVALID_PLATFORM_IDENTIFIER:
				$message = lang('Invalid platform identifier.');
				break;
			case self::INCOMPLETE_OR_MALFORMED_REQUEST:
				$message = lang('Incomplete or malformed request.');
				break;
			case self::UNKNOWN_REQUEST:
				$message = lang('Unknown request.');
				break;
			case self::REQUEST_PROCESSING_SERVICES_UNAVAILABLE:
				$message = lang('Request processing services unavailable.');
				break;
			case self::INVALID_SECURE_TOKEN:
				$message = lang('Invalid secure token.');
				break;
			case self::INCOMPLETE_OR_MALFORMED_REQUEST:
				$message = lang('Incomplete or malformed request.');
				break;
			case self::INSUFFICIENT_FUNDS:
				$message = lang('Insufficient funds.');
				break;
			case self::PLAYER_ACCOUNT_LOCKED:
				$message = lang('Player account locked.');
				break;
			case self::WAGER_LIMIT_EXCEEDED:
				$message = lang('Wager limit exceeded.');
				break;
			case self::TRANSACTION_FAILED:
				$message = lang('Transaction failed.');
				break;
			case self::UNSUPPORTED_GAMEID:
				$message = lang('Unsupported gameid.');
				break;
			case self::GAME_CYCLE_DOES_NOT_EXIST:
				$message = lang('Game cycle does not exist.');
				break;
			case self::INCORRECT_PARAMETERS_FOR_A_PLAYER_SESSION:
				$message = lang('Incorrect parameters for a player session.');
				break;
			case self::INCORRECT_PLAYER_IDENTIFIER_FOR_SECURE_TOKEN:
				$message = lang('Incorrect player identifier for secure token.');
				break;
			case self::GAME_CYCLE_EXISTS:
				$message = lang('Game cycle exists.');
				break;
			case self::TRANSACTION_ALREADY_EXISTS:
				$message = lang('Transaction already exists.');
				break;
			case self::TRANSACTION_DOES_NOT_EXISTS:
				$message = lang('Transaction does not exists.');
				break;
			case self::GAME_CYCLE_ALREADY_CLOSED:
				$message = lang('Game cycle already closed.');
				break;
			
			default:
				$message = $status ? "Success" : "Unkwon Error";
				break;
		}
		$data = array(
				"status" => $status,
				"code" 	 => $code,
				"message"=> $message
		);

		$response = array_merge($data,$extra);
		//save response
		$response_result_id = $this->saveResponseResult($status, $action, $request, $response);
		$response['response_result_id'] = $response_result_id;
		return $response;
	}

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