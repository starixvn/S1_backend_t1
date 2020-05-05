<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Genesis_play_service_api extends BaseController {

	const _SESSION = "session";
    const _BALANCE = "balance";
    const _DEBIT = "debit";
	const _CREDIT = "credit";
	const _ROLLBACK = "rollback";
	const _TOKEN = "generateToken";

	// response status
	const STATUS_400 = 400;
	const STATUS_402 = 402;
	const STATUS_403 = 403;
	const STATUS_401 = 401;
	const STATUS_500 = 500;
	const STATUS_200 = 200;

	// response messages
	const _SUCCESS = 0;
	const ERROR_METHOD_NOT_EXIST = 1;
	const ERROR_INVALID_PARAMETERS = 2;
	const ERROR_INSUFFICIENT_FUNDS = 3;
	const ERROR_EXPIRED_TOKEN = 4;
	const ERROR_PLAYER_LOCKED = 5;
	const ERROR_INVALID_SHARED_KEY = 6;
	const ERROR_INVALID_PROVIDER_ID = 7;
	const ERROR_INVALID_PLAYER_TOKEN = 8;
	const ERROR_INVALID_REQUEST = 9;
	const ERROR_PLAYER_NOT_FOUND = 10;
	const ERROR_TRANSACTION_EXISTS = 11;
	const ERROR_TRANSACTION_FAILED = 12;
	const ERROR_TRANSACTION_NOT_FOUND= 13;
	const ERROR_TRANSACTION_ROLLBACKED= 14;
	const ERROR_PLAYER_ID= 15;
	const ERROR_TIMEOUT = 98;
	const ERROR_UNKNOWN = 99;

	// TRANSACTION STATUS 
	const DEFAULT_STATUS = 0;
	// rollbacked
	const CANCELLED_STATUS = 1;
	// failed / 500 api
	const FAILED_STATUS = 2;

	const _TRUE = true;
	const _FALSE = false;
	const DEFAULT_PLAYER_TYPE = 'REGULAR';

	private $gamePlatformId = GENESIS_SEAMLESS_THB1_API;
	
	public function __construct() {
		parent::__construct();

        # load model
        $this->load->model([
            "game_provider_auth",
            "common_token",
            "player_model",
            "genesis_seamless_transactions",
        ]);

        $this->game_api = $this->utils->loadExternalSystemLibObject(GENESIS_SEAMLESS_THB1_API);
        // $this->gamePlatformId = $this->game_api->getPlatformCode();
        $this->currency = $this->game_api->getSystemInfo('currency');
		$this->provider_id = $this->game_api->getSystemInfo('X-Provider-ID');
		$this->provider_shared_key = $this->game_api->getSystemInfo('X-Provider-Shared-key');
		$this->partnerToken = $this->game_api->getSystemInfo('partnerToken');
		$this->default_timeout = $this->CI->utils->getConfig('default_http_timeout');
		
		$table = $this->game_api->original_transaction_table;
		$this->CI->genesis_seamless_transactions->init($table); 

	}

	public function generateToken($playerId=null) {
		$data = [];
		$params = array('playerId' => $playerId);
		if(empty($playerId)){
			$status = self::STATUS_400;
			$code = self::ERROR_PLAYER_ID;
		} else {
			$player = (array) $this->CI->game_provider_auth->getPlayerByGameUsername($playerId, $this->gamePlatformId);

			if (!empty($player)) {
				$playerId = $this->player_model->getPlayerIdByUsername($player['username']);

				//check available token
				$token = $this->CI->common_token->getAvailableToken($playerId, 'player_id');
				if (empty($token)) {
					$token = $this->CI->common_token->createTokenBy($playerId, 'player_id');
				}
				$this->utils->debug_log('get player token ', $playerId, $token);
				$status = self::STATUS_200;
				$code = self::_SUCCESS;
				$data = array(
					'token' => $token,
				);
			} else {
				$status = self::STATUS_400;
				$code = self::ERROR_PLAYER_NOT_FOUND;
			}
		}
		$response = $this->getResponseMessage($status,$code,self::_TOKEN,$params,$data);
		unset($response['response_id']);
		unset($response['status']);
		return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($response));
	}

	private function validateHeaders($method) {
		$headers = getallheaders();
		if ($headers) {
			$player_token = (isset($headers['X-Player-Token'])) ? $headers['X-Player-Token'] : "";
			$provider_id = (isset($headers['X-Provider-Id'])) ? $headers['X-Provider-Id'] : "";
			$provider_key = (isset($headers['X-Provider-Shared-Key'])) ? $headers['X-Provider-Shared-Key'] : "";

			$req_headers = array(
					'X-Player-Token' => $player_token,
					'X-Provider-Id' => $provider_id,
					'X-Provider-Shared-Key' => $provider_key,
			);
			
			if (empty($player_token)) {
				if ($method != self::_BALANCE || $method != self::_CREDIT || $method != self::_ROLLBACK) {
					return array(
						"valid" => false,
						"code" => self::ERROR_INVALID_PLAYER_TOKEN,
						"req_headers" => $req_headers,
					);
				}
			} elseif (empty($provider_id) || $provider_id != $this->provider_id) {
				return array(
					"valid" => false,
					"code" => self::ERROR_INVALID_PROVIDER_ID,
					"req_headers" => $req_headers,
				);
			} elseif (empty($provider_key) || $provider_key != $this->provider_shared_key) {
				return array(
					"valid" => false,
					"code" => self::ERROR_INVALID_SHARED_KEY,
					"req_headers" => $req_headers,
				);
			} elseif ($player_token && $provider_id && $provider_key) {
				return array(
					"valid" => true,
					"player_token" => $player_token,
					"req_headers" => $req_headers,
				);
			}
		}
	}

	public function api($version, $method, $params=null) {
		
		if ($this->external_system->isGameApiActive($this->gamePlatformId)) {

			$action = $this->getAction($method, $params);
			$extra = [];
			$req_params = [];
			$response = [];
			$status = self::STATUS_500;
			try {
				$validHeaders = $this->validateHeaders($method);
				$token = isset($validHeaders['player_token']) ? $validHeaders['player_token'] : "";
				$req_headers = isset($validHeaders['req_headers']) ? $validHeaders['req_headers'] : "";
				$playerInfo = $this->game_api->getPlayerInfoByToken($token);
				$start_time = strtotime(date('Y-m-d H:i:s'));

				if (!$validHeaders['valid']) {
					$response = $this->getResponseMessage(self::STATUS_401,$validHeaders['code'],$action,$req_params,$extra,$req_headers);
		        	throw new Exception(json_encode($response));
				}

				$req_params = file_get_contents('php://input');
		        $req_params = json_decode($req_params,true);
		        
		        if (empty($req_params)) {
		        	$req_params = $this->getInputGetAndPost();
		        	if (empty($req_params) && $action != self::_BALANCE) {
		        		$response = $this->getResponseMessage(self::STATUS_400,self::ERROR_INVALID_PARAMETERS,$action,$req_params,$extra,$req_headers);
		        		throw new Exception(json_encode($response));
		        	}
		        }
		        
			 	if (!$this->isPostMethod() && !$this->isGetMethod()) {
	            	$response = $this->getResponseMessage(self::STATUS_401,self::ERROR_METHOD_NOT_EXIST,$action,$req_params,$extra,$req_headers);
	            	throw new Exception(json_encode($response));
				}

				if (empty($playerInfo) && ($action == self::_SESSION || $action == self::_DEBIT)) {
					$response = $this->getResponseMessage(self::STATUS_403,self::ERROR_EXPIRED_TOKEN,$action,$req_params,$extra,$req_headers);
	            	throw new Exception(json_encode($response));
				}

				$player_id = $playerInfo['playerId'];
				$player_name = $playerInfo['username'];

				list($resp_data, $code, $status) = $this->callAction($action,$playerInfo,$req_params,$params,$req_headers);

				$end_time = strtotime(date('Y-m-d H:i:s'));
			    $processing_time = $end_time - $start_time;

			    if ($processing_time >= $this->default_timeout) {
			 		if ($action == self::_DEBIT || $action == self::_ROLLBACK || $action == self::_CREDIT) {
			 			$this->CI->genesis_seamless_transactions->updateTransactionStatus($req_params['txId'],$action,self::FAILED_STATUS);
			 		}
			 		$response = $this->getResponseMessage(self::STATUS_500,self::ERROR_TIMEOUT,$action,$req_params,$extra,$req_headers);
	            	throw new Exception(json_encode($response));
			 	}
				
				if ($method == 'game-transactions' && $code == 0) {
					$response = $resp_data;
					$response['status'] = $status;
				} else {
					$extra = $resp_data;
					$req_params = ($req_params) ? $req_params : array("playerId"=> $params);
					$response = $this->getResponseMessage($status,$code,$action,$req_params,$extra,$req_headers);
				}
				$this->utils->debug_log('GENESIS success',  json_encode($response));
			} catch (Exception $e) {
				$this->utils->debug_log('GENESIS error',  $e->getMessage());
				$response = json_decode($e->getMessage(),true);
			}
		
			if ($response) {
				$status = $response['status'];
				unset($response['response_id']);
				unset($response['status']);
			}

			return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($response));
		}
	}

	private function callAction($action,$playerInfo,$req_params,$params,$req_headers){
		switch ($action){
			case 'session':
				return $this->session($action,$playerInfo,$req_params);
				break;
			case 'balance':
				return $this->balance($action,$playerInfo,$req_params,$params);
				break;
			case 'debit':
				return $this->gameTransactions($action,$playerInfo,$req_params,$req_headers);
				break;
			case 'credit': 
				return $this->gameTransactions($action,$playerInfo,$req_params,$req_headers);
				break;
			case 'rollback': 
				return $this->gameTransactions($action,$playerInfo,$req_params,$req_headers);
				break;

			default:
				throw new Exception('ERROR_INVALID_REQUEST');
				break;
		}
	}

	private function session($action,$playerInfo,$req_params) {
		$partnerToken = isset($req_params['partnerToken']) ? $req_params['partnerToken'] : '';
		$game_code = isset($req_params['gameCode']) ? $req_params['gameCode'] : '';
		$device = isset($req_params['device']) ? $req_params['device'] : '';

		if ($partnerToken == $this->partnerToken) {
			$gameUsername = $this->game_api->getGameUsernameByPlayerUsername($playerInfo['username']);
			//balance
			$current_balance = $this->game_api->queryPlayerBalance($playerInfo['username'])['balance'];
			$datetime = $this->utils->getNowDateTime();
			$timestamp = $datetime->format('Y-m-d H:i:s');

			$data = array(
				"playerId" => $gameUsername,
				"currency" => $this->currency,
				"balance" => $current_balance,
				"playerType" => self::DEFAULT_PLAYER_TYPE,
				// "timestamp" => $timestamp,
			);

			return array($data,self::_SUCCESS,self::STATUS_200);
		} else {
			return array([],self::ERROR_INVALID_PARAMETERS,self::STATUS_400);
		}
	}

	private function balance($action,$playerInfo,$req_params,$params) {
		$playerId = $params;
		// $player = (array) $this->CI->player_model->getPlayerById($playerId);
		$player = (array) $this->CI->game_provider_auth->getPlayerByGameUsername($playerId, $this->gamePlatformId);
		
		if ($player) {
			$gameUsername = $this->game_api->getGameUsernameByPlayerUsername($player['username']);
			$current_balance = $this->game_api->queryPlayerBalance($player['username'])['balance'];

			$datetime = $this->utils->getNowDateTime();
			$timestamp = $datetime->format('Y-m-d H:i:s');
			
			$data = array(
				"playerId" => $gameUsername,
				"playerType" => self::DEFAULT_PLAYER_TYPE,
				"currency" => $this->currency,
				"balance" => $current_balance,
				// "affiliate" => $session_id,
				// "timestamp" => $timestamp,
			);

			return array($data,self::_SUCCESS,self::STATUS_200);
		} else {
			return array([],self::ERROR_PLAYER_NOT_FOUND,self::STATUS_403);
		}
	}

	public function gameTransactions($action,$playerInfo,$req_params,$req_headers) {
		$req_data = $this->buildRequestParams($req_params);
		$player_name = $this->game_api->getPlayerUsernameByGameUsername($req_data['gameUsername']);
		$player_id = $this->game_api->getPlayerIdFromUsername($player_name);
		$is_retry = false;
		$extra = [];
		
		if (!empty($req_data)) {
			if (empty($req_data['txId'])) {
				return array([],self::ERROR_INVALID_PARAMETERS,self::STATUS_400);
			}
			if (empty($req_data['gameUsername'])) {
				return array([],self::ERROR_INVALID_PARAMETERS,self::STATUS_400);
			}
		}

		if ($action == self::_ROLLBACK && $req_data['debitTxId']) {
			$is_rollback = $this->CI->genesis_seamless_transactions->getTransaction($req_data['debitTxId'],self::_DEBIT,self::CANCELLED_STATUS);
			
			if (!$is_rollback) {
				$debit_info = $this->CI->genesis_seamless_transactions->getDebitInfo($req_data['debitTxId'],self::_DEBIT);
				if (empty($debit_info)) {
					return array([],self::ERROR_TRANSACTION_NOT_FOUND,self::STATUS_400);
				}
				$player_name = $this->game_api->getPlayerUsernameByGameUsername($debit_info['gameUsername']);
				$player_id = $debit_info['playerId'];
				$amount = $debit_info['amount'];
			} else {
				return array([],self::ERROR_TRANSACTION_ROLLBACKED,self::STATUS_400);
			}
		}

		if ($player_id) {

			$txId_exists = $this->CI->genesis_seamless_transactions->isTxIdAlreadyExists($req_data['txId'],$action);

			if ($txId_exists) {
				$trans_data = (array) $this->CI->genesis_seamless_transactions->getTransaction($req_data['txId'],$action,self::FAILED_STATUS);
				if(!empty($trans_data)) {
					$req_data = $this->buildRequestParams($trans_data);
					$is_retry = true;
					$txId_exists = false;
				}
			}
			
			if (!$txId_exists) {
				$amount = ($req_data['amount']) ? $req_data['amount'] : $amount;
				$params = array(
					"txId" => $req_data['txId'],
					"player_id" => $player_id,
					"player_name" => $player_name, 
					"amount" => $amount,
					"action" => $action,
				);

				if ($action == self::_ROLLBACK) {
					$params['debitTxId'] = $req_data['debitTxId'];
				}

				list($result, $prev_balance, $after_balance, $resp_data) = $this->debitCreditWallet($params);
				
				if ($result) {
					$datetime = $this->utils->getNowDateTime();
					$timestamp = $datetime->format('Y-m-d H:i:s');
					$player_token = $this->game_api->getPlayerToken($player_id);
					$partnerTxId = $this->utils->getTimestampNow() . random_string('numeric', '6');

					$data = array(
						"txId" => $req_data['txId'],
						"partnerTxId" => $partnerTxId,
						"balance" => $after_balance,
						// "timestamp"  => $timestamp,
					);

					$extra = $data;

					$response = $this->getResponseMessage(self::STATUS_200,self::_SUCCESS,$action,$req_params,$extra,$req_headers);
					$response_id = $response['response_id'];

					$additional_data = array(
						"response_result_id" => $response_id,
						"token" => $player_token,
						"prev_balance" => $prev_balance,
						"after_balance" => $after_balance,
						"playerId" => $req_data['gameUsername'],
						"amount" => $amount,
					);

					$insert_data = array_merge($data,$additional_data,$params,$req_params);

					$this->buildTransactionData($insert_data);

					if ($is_retry) {
						$res  = $this->CI->genesis_seamless_transactions->updateTransactionStatus($req_params['txId'],$action,self::DEFAULT_STATUS);
					} else {
						$res = $this->CI->genesis_seamless_transactions->insertRow($insert_data);
					}

					$this->utils->debug_log("GENESIS INSERT ============================>", json_encode($insert_data) , 'success', $res);
					
					if ($res && $action == self::_ROLLBACK) {
						$update_res = $this->CI->genesis_seamless_transactions->updateTransactionStatus($params['debitTxId'],self::_DEBIT,self::CANCELLED_STATUS);
						$this->utils->debug_log("GENESIS UPDATE ============================>", $params['debitTxId'] ,'succes', $update_res);
					}
					
					return array($data,self::_SUCCESS,self::STATUS_200);
				} else {
					return array([],$resp_data['code'],$resp_data['status']);
				}
			} else {
				return array([],self::ERROR_TRANSACTION_EXISTS,self::STATUS_400);
			}
		} else {
			return array([],self::ERROR_PLAYER_NOT_FOUND,self::STATUS_400);
		}

	}

	private function buildRequestParams($req_params) {
		$data['txId'] = isset($req_params['txId']) ? $req_params['txId'] : '';
		$data['gameUsername'] = isset($req_params['playerId']) ? $req_params['playerId'] : '';
		$data['gameCode'] = isset($req_params['gameCode']) ? $req_params['gameCode'] : '';
		$data['currency'] = isset($req_params['currency']) ? $req_params['currency'] : '';
		$data['created'] = isset($req_params['created']) ? $req_params['created'] : '';
		$data['amount'] = isset($req_params['amount']) ? $req_params['amount'] : '';
		$data['roundId'] = isset($req_params['roundId']) ? $req_params['roundId'] : '';
		$data['completed'] = isset($req_params['completed']) ? $req_params['completed'] : '';
		$data['roundType'] = isset($req_params['roundType']) ? $req_params['roundType'] : '';
		$data['bonusRefId'] = isset($req_params['bonusRefId']) ? $req_params['bonusRefId'] : '';
		$data['jpContrib'] = isset($req_params['jpContrib']) ? $req_params['jpContrib'] : '';
		$data['debitTxId'] = isset($req_params['debitTxId']) ? $req_params['debitTxId'] : '';

		return $data;
	}

	private function debitCreditWallet($params) {
		$player_id = $params['player_id'];
		$player_name = $params['player_name'];
		$amount = $params['amount'];
		$action = $params['action'];
		$txId = $params['txId'];
		$debitTxId = (isset($params['debitTxId'])) ? $params['debitTxId'] : "";
		$resp_data = [];

		$success = false;
		$controller = $this;
		$prev_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];

		if ($action == self::_DEBIT) {
			if ($amount > $prev_balance) {
				$resp_data['code'] = self::ERROR_INSUFFICIENT_FUNDS;
				$resp_data['status'] = self::STATUS_402;
			} else {
				$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
					return $controller->wallet_model->decSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
				});
			}
		} elseif ($action == self::_CREDIT) {
			$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
				return $controller->wallet_model->incSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
			});
		} elseif ($action == self::_ROLLBACK) {
			$debitTransaction = $this->CI->genesis_seamless_transactions->getTransaction($debitTxId,self::_DEBIT,self::DEFAULT_STATUS);
			if ($debitTransaction) {
				$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
					return $controller->wallet_model->incSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
				});
				if (!$success) {
					$resp_data['code'] = self::ERROR_TRANSACTION_FAILED;
					$resp_data['status'] = self::STATUS_400;
				}
			} else {
				$success = false;
				$resp_data['message'] = 'Debit Transaction not exists';
			}
		}

		$after_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];

		return array($success,$prev_balance, $after_balance, $resp_data);
	}

	private function buildTransactionData(&$insert_data){
		$data['txId'] = isset($insert_data['txId']) ? $insert_data['txId'] : null;
		$data['debitTxId'] = isset($insert_data['debitTxId']) ? $insert_data['debitTxId'] : null;
		$data['playerId'] = isset($insert_data['player_id']) ? $insert_data['player_id'] : null;
		$data['gameUsername'] = isset($insert_data['playerId']) ? $insert_data['playerId'] : null;
		$data['gameCode'] = isset($insert_data['gameCode']) ? $insert_data['gameCode'] : null;
		$data['currency'] = isset($insert_data['currency']) ? $insert_data['currency'] : null;
		$data['amount'] = isset($insert_data['amount']) ? $insert_data['amount'] : null;
		$data['progressiveWin'] = isset($insert_data['progressiveWin']) ? $insert_data['progressiveWin'] : null;
		$data['roundId'] = isset($insert_data['roundId']) ? $insert_data['roundId'] : null;
		$data['completed'] = isset($insert_data['completed']) ? $insert_data['completed'] : null;
		$data['roundType'] = isset($insert_data['roundType']) ? $insert_data['roundType'] : null;
		$data['bonusRefId'] = isset($insert_data['bonusRefId']) ? $insert_data['bonusRefId'] : null;
		$data['jpContrib'] = isset($insert_data['jpContrib']) ? $insert_data['jpContrib'] : null;

		$data['partnerTxId'] = isset($insert_data['partnerTxId']) ? $insert_data['partnerTxId'] : null;
		$data['action'] = isset($insert_data['action']) ? $insert_data['action'] : null;
		$data['token'] = isset($insert_data['token']) ? $insert_data['token'] : null;
		$data['before_balance'] = isset($insert_data['prev_balance']) ? $insert_data['prev_balance'] : null;
		$data['after_balance'] = isset($insert_data['balance']) ? $insert_data['balance'] : null;
		$data['response_result_id'] = isset($insert_data['response_result_id']) ? $insert_data['response_result_id'] : null;
		$data['external_uniqueid'] = $data['action'].'-'.$data['txId'];

		$datetime = new Datetime();
    	// $datetime->setTimestamp($insert_data['created']);
    	$data['created'] = isset($insert_data['created']) ? $datetime->format('Y-m-d H:i:s') : null;

		$data['status'] = self::DEFAULT_STATUS;
		
		$insert_data = $data;
	}

	private function getAction($method, $trans_type) {
		switch ($method) {
			case 'sessions':
				$action = self::_SESSION;
				break;
			case 'players':
				$action = self::_BALANCE;
				break;
			case 'game-transactions':
				if ($trans_type == 'debits') {
					$action = self::_DEBIT;
				}  elseif ($trans_type == 'credits') {
					$action = self::_CREDIT;
				} elseif ($trans_type == 'rollback') {
					$action = self::_ROLLBACK;
				}
				break;
			
			default:
				# code...
				break;
		}
		return $action;
	}

	private function getResponseMessage($status, $code, $action, $req_params, $extra=null, $headers=null){
		switch ($code) {
			case self::ERROR_INVALID_PARAMETERS:
				$message = lang('Invalid Parameters');
				break;
			case self::ERROR_INSUFFICIENT_FUNDS:
				$message = lang('Insufficient Funds');
				break;
			case self::ERROR_PLAYER_LOCKED:
				$message = lang('Player or Partner is locked');
				break;
			case self::ERROR_EXPIRED_TOKEN:
				$message = lang('Player token has expired');
				break;
			case self::ERROR_INVALID_SHARED_KEY:
				$message = lang('Invalid Shared Key');
				break;
			case self::ERROR_INVALID_PROVIDER_ID:
				$message = lang('Invalid Provider ID');
				break;
			case self::ERROR_INVALID_PLAYER_TOKEN:
				$message = lang('Player token required');
				break;
			case self::ERROR_UNKNOWN:
				$message = lang('Unknown Error');
				break;
			case self::ERROR_METHOD_NOT_EXIST:
				$message = lang('Method not exists');
				break;
			case self::ERROR_INVALID_REQUEST:
				$message = lang('Invalid Request');
				break;
			case self::ERROR_PLAYER_NOT_FOUND:
				$message = lang('Player not found');
				break;
			case self::ERROR_TRANSACTION_EXISTS:
				$message = lang('Transaction exists');
				break;
			case self::ERROR_TRANSACTION_NOT_FOUND:
				$message = lang('Debit Transaction not found');
				break;
			case self::ERROR_TRANSACTION_ROLLBACKED:
				$message = lang('Debit Transaction Rollbacked already');
				break;
			case self::ERROR_PLAYER_ID:
				$message = lang('Player ID required');
				break;
				
			default:
				$message = $status == 200 ? "Success" : lang("Unknown Error");
				break;
		}

		$data = [];

		if ($code != 0) {
			$data = array (
					"code"=> $code,
					"message"=> $message
			);
		}

		$response = array_merge($data,$extra);

		//save response
		$response_result_id = $this->saveResponseResult($status, $action, $req_params, $response, $headers);
		$response['status'] = $status;
		$response['response_id'] = $response_result_id;
		return $response;
	}

	private function saveResponseResult($status, $method, $params, $response, $headers){
        //save to db
        $this->CI->load->model("response_result");
        $flag = $status == 200 ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        $headers = ($headers) ? json_encode($headers) : $headers;

        return $this->CI->response_result->saveResponseResult($this->gamePlatformId, $flag, $method, json_encode($params), json_encode($response), $status, null, $headers);
    }

}
