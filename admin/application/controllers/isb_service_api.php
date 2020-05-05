<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class isb_service_api extends BaseController {

	private $APIauth;
	private $player_id;
	private $currencyCode;
	private $gamePlatformId = ISB_SEAMLESS_API;

	const ERROR_CODE = array(
		"ER01" => "Internal Error",
		"R_02" => "Invalid request.",
		"R_03" => "Invalid HMAC.",
		"R_09" => "Player not found.",
		"R_10" => "Game is not configured for this licensee.",
		"R_11" => "Invalid Session Id or (can occur only on init call) Session not unique",
		"R_13" => "Invalid Currency",
		"I_03" => "Invalid Token.",
		"I_04" => "Token already used.",
		"B_03" => "Insufficient Funds.",
		"B_04" => "Duplicate Transaction Id.",
		"B_05" => "Transaction has been cancelled.",
		"B_06" => "Round is closed.",
		"B_07" => "There is an active Round in current Session.",
		"W_03" => "Invalid Round.",
		"W_06" => "Duplicate Transaction Id.",
		"W_07" => "Operator should remain the same during the round.",
		"C_03" => "Invalid cancel, Transaction does not exist.",
		"C_04" => "Trying to cancel a bet from an already closed round.",
		"C_05" => "Transaction details do not match.",
	);

	function __construct() {
		parent::__construct();
		$this->game_api_isb = $this->utils->loadExternalSystemLibObject($this->gamePlatformId);
		$this->currencyCode = $this->game_api_isb->getCurrency();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','player_model','isbseamless_game_logs'));
	}

	
	public function index() {
		if ($this->external_system->isGameApiActive($this->gamePlatformId)) {
			$params = file_get_contents('php://input');
			$hash = $_GET["hash"];
			$HMAC = $this->game_api_isb->generateHMAC($params);
			$reqParams = json_decode($params,true);
			$this->utils->debug_log('>>>>>>>>>>>>>> ISB SEAMLESS request params', $reqParams);
			$response = [];
			$success = true;
			// echo self::ACTION_COMMAND[$callMethod];exit;
			// echo $HMAC .' '. $hash;exit;

			# "R_10" => "Game is not configured for this licensee.",

			if($HMAC === $hash){
				try {	
					$fullRequest = $reqParams;
					$username = isset($fullRequest['playerid'])?$fullRequest['playerid']:null;
					$playerId = $this->player_model->getPlayerIdByUsername($username);

					# check currency
					if(isset($reqParams['currency'])&&$reqParams['currency']!=$this->currencyCode){
						throw new Exception('R_13');
					}

					# check player if exists
					if (empty($playerId)){
						throw new Exception('R_09');
					}

					if($reqParams['state'] == 'multi'){
						foreach ($reqParams['actions'] as $req) {
							$callMethod = $req['command'];
							$response = $this->callMethod($username,$playerId,$callMethod,$req,$fullRequest);
						}
					}else{
						$callMethod = $reqParams['action']['command'];
						$req = $reqParams['action'];
						$response = $this->callMethod($username,$playerId,$callMethod,$req,$fullRequest);
					}	
				}catch (Exception $e) {	
					log_message('error', self::ERROR_CODE[$e->getMessage()]);
					$response = $this->returnError($e->getMessage());
				}
			}else{
				$success = false;
				$response = $this->returnError('R_03');
			}
			
			# save our response result and API request on our system
			$this->saveResponseResult($success,$callMethod,$reqParams,$response);

			return $this->returnJsonResult($response);
		}
	}

	private function callMethod($username,$playerId,$callMethod,$actions,$fullRequest=null){
		switch ($callMethod){
			case 'init':
				return $this->playerInit($username,$playerId,$actions,$fullRequest);
				break;
			case 'balance':
				return $this->getbalance($username,$playerId,$actions,$fullRequest);
				break;
			case 'bet':
				return $this->playerBet($username,$playerId,$actions,$fullRequest);
				break;
			case 'win': 
				return $this->playerWin($username,$playerId,$actions,$fullRequest);
				break;
			case 'transactiondetails':
				# code...
				break;
			case 'rounddetails':
				# code...
				break;
			case 'creditbonus':
				return $this->creditBonus($username,$playerId,$actions,$fullRequest);
				break;
			case 'cancel':
				return $this->canceTransaction($username,$playerId,$actions,$fullRequest);
			case 'end':
				return $this->endPlayerSession($username,$playerId,$actions,$fullRequest);
				break;
			case 'dialog':
				# code...
				break;

			default:
				throw new Exception('R_02');
				break;
		}
	}

	private function endPlayerSession($username,$playerId,$reqParams,$fullRequest){
		# load external_common_tokens model
		$this->load->model(array('external_common_tokens'));
		$sessionId = $fullRequest['sessionid'];
		$sessionStatus = $reqParams['parameters']['sessionstatus'];

		# Update status base on GP status
		$this->external_common_tokens->updatePlayerExternalTokenStatus($playerId, $sessionId, $sessionStatus);
		# get player balance
		$playerBalance = $this->game_api_isb->queryPlayerBalance($username);

		$response = array(
			"status" => 'success', 
			"balance" => $playerBalance['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;
	}

	private function canceTransaction($username,$playerId,$reqParams,$fullRequest){

		$betAmount = @$reqParams['parameters']['amount']; 
		$transaction_id = @$reqParams['parameters']['transaction_id']; 
		$roundid = @$reqParams['parameters']['roundid']; 
		$froundid = isset($reqParams['parameters']['froundid'])?$reqParams['parameters']['froundid']:0;
		$sessionid = isset($fullRequest['sessionid'])?$fullRequest['sessionid']:null;
		$skinid = isset($fullRequest['skinid'])?$fullRequest['skinid']:null;

		$existingRow = $this->isbseamless_game_logs->isRowByTransactionId($transaction_id);

		# throw error if transaction doesnt exists.
		if(empty($existingRow)){
			throw new Exception('C_03');
		}

		# check if transaction params is same on existing one.
		foreach($reqParams['parameters'] as $key => $val){
			if($existingRow[$key] != $val){
				if($key == "timestamp"){
					continue;
				}
				# transaction details do not match.
				throw new Exception('C_05');
			}
		}

		# refund bet amount due to cancelation
		# if already canceled don't refund balance
		if($existingRow['transaction_status']!="canceled"){
			$this->add_amount($playerId, $betAmount);
			# update transaction to cancel
			$this->isbseamless_game_logs->updateGameLog($existingRow['id'],$dataUpate);
		}else{
			# check if round is already closed. 
			throw new Exception('C_04');
		}

		$playerBalance = $this->game_api_isb->queryPlayerBalance($username);

		$dataUpate = array(
			"transaction_status" => "canceled"
		);

		$response = array(
			"status" => 'success', 
			"balance" => $playerBalance['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;
	}
	
	private function playerInit($username,$playerId,$reqParams,$fullRequest){
		$token = isset($reqParams['parameters']['token'])?$reqParams['parameters']['token']:null;
		$playerIdByToken = $this->game_api_isb->getPlayerIdByToken($token);
		if(!empty($playerIdByToken)){
			# load external_common_tokens model
			$this->load->model(array('external_common_tokens'));

			$activeSession = $this->external_common_tokens->getExternalToken($playerId,$this->gamePlatformId);
			# get sessionid, check if already exist , if yes return error R_11
			if(!empty($activeSession)){
				throw new Exception('R_11');
			}

			# add sessionid on external_common_tokens table
			$this->external_common_tokens->addPlayerToken($playerId,$fullRequest['sessionid'],$this->gamePlatformId);

			$playerBalance = $this->game_api_isb->queryPlayerBalance($username);
			$response = array(
				"status" => 'success', 
				"playerid" => $username, 
				"sessionid" => $fullRequest['sessionid'], 
				"currency" => $this->currencyCode,
				"balance" => $playerBalance['balance'], 
			);
			return $response;
		}else{
			throw new Exception('I_03');
		}

	}

	private function getbalance($username,$playerId,$reqParams,$fullRequest){
		$playerBalance = $this->game_api_isb->queryPlayerBalance($username);
		
		$response = array(
			"status" => 'success', 
			"balance" => $playerBalance['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;
	}

	private function playerBet($username,$playerId,$reqParams,$fullRequest){

		# "B_06" => "Round is closed.",
		# "B_07" => "There is an active Round in current Session.",

		$betAmount = @$reqParams['parameters']['amount']; 
		$transaction_id = @$reqParams['parameters']['transaction_id']; 
		$roundid = @$reqParams['parameters']['roundid']; 
		$froundid = isset($reqParams['parameters']['froundid'])?$reqParams['parameters']['froundid']:0;
		$sessionid = isset($fullRequest['sessionid'])?$fullRequest['sessionid']:null;
		$skinid = isset($fullRequest['skinid'])?$fullRequest['skinid']:null;

		$insertData = array(
			"username" => $username,
			"sessionid" => $sessionid,
			"skinid" => $skinid,
			"transaction_id" => $transaction_id,
			"roundid" => $roundid,
			"amount" => $betAmount,
			"jpc" => @$reqParams['parameters']['jpc'],
			"froundid" => $froundid,
			"fround_coin_value" => @$reqParams['parameters']['fround_coin_value'],
			"fround_lines" => @$reqParams['parameters']['fround_lines'],
			"fround_line_bet" => @$reqParams['parameters']['fround_line_bet'],
			"timestamp" => @$reqParams['parameters']['timestamp'],
			"command" => @$reqParams['command'],
			"created_at" => date('Y-m-d H:i:s'),
			"updated_at" => date('Y-m-d H:i:s')
		);

		$existingRow = $this->isbseamless_game_logs->isRowByTransactionId($transaction_id);
		
		if(!empty($existingRow)){
			# check if transaction params is same on existing one.
			foreach($reqParams['parameters'] as $key => $val){
				if($existingRow[$key] != $val){
					if($key == "timestamp"){
						continue;
					}
					throw new Exception('B_04');
				}
			}

			# check if transaction is cancelled
			if($existingRow['transaction_status'] == "cancelled"){
				throw new Exception('B_05');
			}

			# if same transaction return same value as berfore. 
			$response = array(
				"status" => 'success', 
				"balance" => $existingRow['after_balance'], 
				"currency" => $this->currencyCode,
			);

			return $response;

		}

		# check if Round is Closed.
		# CODE
		# if yes, return error B_06 Round is closed.
		#

		# Check if there is another open round in current session
		# CODE
		# if yes, return error B_07 There is an active Round in current Session.
		#

		$queryPlayerBalanceResult = $this->game_api_isb->queryPlayerBalance($username);

		if (($queryPlayerBalanceResult['balance'] - $betAmount) < 0) {
			throw new Exception('B_03');
		}

		# if froundid > 0 , this is free spin don't deduct money from player balance.
		if($froundid == 0){ 
			$this->subtract_amount($playerId, $betAmount);
		}

		$playerBalance = $this->game_api_isb->queryPlayerBalance($username);

		# include after balance on gamelogs 
		$insertData['after_balance'] = $playerBalance['balance']; 

		# insert gamelogs to isbseamless_game_logs table
		$this->isbseamless_game_logs->insertGameLogs($insertData);

		$response = array(
			"status" => 'success', 
			"balance" => $playerBalance['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;
		
	}

	private function playerWin($username,$playerId,$reqParams,$fullRequest){
		#"W_03" => "Invalid Round.",
		#"W_07" => "Operator should remain the same during the round.",

		$winAmount = @$reqParams['parameters']['amount']; 
		$transaction_id = @$reqParams['parameters']['transaction_id']; 
		$roundid = @$reqParams['parameters']['roundid']; 
		$froundid = isset($reqParams['parameters']['froundid'])?$reqParams['parameters']['froundid']:0;
		$sessionid = isset($fullRequest['sessionid'])?$fullRequest['sessionid']:null;
		$skinid = isset($fullRequest['skinid'])?$fullRequest['skinid']:null;

		$insertData = array(
			"username" => $username,
			"sessionid" => $sessionid,
			"skinid" => $skinid,
			"transaction_id" => $transaction_id,
			"roundid" => $roundid,
			"amount" => $winAmount,
			"jpw" => @$reqParams['parameters']['jpw'],
			"closeround" => @$reqParams['parameters']['closeround'],
			"jpw_from_jpc" => @$reqParams['parameters']['jpw_from_jpc'],
			"froundid" => @$reqParams['parameters']['froundid'],
			"timestamp" => @$reqParams['parameters']['timestamp'],
			"command" => @$reqParams['command'],
			"created_at" => date('Y-m-d H:i:s'),
			"updated_at" => date('Y-m-d H:i:s')
		);

		$existingRow = $this->isbseamless_game_logs->isRowByTransactionId($transaction_id);
		if(!empty($existingRow)){
			# check if transaction params is same on existing one.
			foreach($reqParams['parameters'] as $key => $val){
				if($existingRow[$key] != $val){
					if($key == "timestamp"){
						continue;
					}
					throw new Exception('W_06'); # dublicate transaction_id
				}
			}

			# if same transaction return same value as berfore. 
			$response = array(
				"status" => 'success', 
				"balance" => $existingRow['after_balance'], 
				"currency" => $this->currencyCode,
			);

			return $response;
		}

		# check if round exists and is not inactive and has got a bet with transaction status OK
		#
		# CODE 
		# if no, return error W_03
		#

		# check if round already belongs to current session
		# check if operator is the same as for the previous session
		# if no, return error W_07
		#
		
		# check if closeround parameters is true
		# if yes, set round status to INACTIVE

		# add balance to Wallet
		$this->add_amount($playerId, $winAmount);

		$playerBalance = $this->game_api_isb->queryPlayerBalance($username);

		# include after balance on gamelogs 
		$insertData['after_balance'] = $playerBalance['balance']; 

		# insert gamelogs to isbseamless_game_logs table
		$this->isbseamless_game_logs->insertGameLogs($insertData);

		$response = array(
			"status" => 'success', 
			"balance" => $playerBalance['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;
	}

	private function creditBonus($username,$playerId,$reqParams,$fullRequest){

		$amount = isset($reqParams['parameters']['amount'])?$reqParams['parameters']['amount']:0;
		$this->add_amount($playerId,$amount);
		$playerBlanace = $this->game_api_isb->queryPlayerBalance($username);

		$response = array(
			"status" => 'success', 
			"balance" => $playerBlanace['balance'], 
			"currency" => $this->currencyCode,
		);

		return $response;

	}

	private function subtract_amount($playerId, $amount) {
		$lockedKey = NULL;
		$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		$this->utils->debug_log('lock subtract_amount', 'id', $playerId, $lock_it);

		if ($lock_it) {
			try {
				$this->startTrans();
				$this->wallet_model->decSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->endTransWithSucc();
			} finally {
				$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
				$this->utils->debug_log('release subtract_amount lock', 'id', $playerId);
			}
		}
	}

	private function add_amount($playerId, $amount) {
		$lockedKey = NULL;
		$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		$this->utils->debug_log('lock add_amount', 'id', $playerId, $lock_it);

		if ($lock_it) {
			try {
				$this->startTrans();
				$this->wallet_model->incSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->endTransWithSucc();
			} finally {
				$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
				$this->utils->debug_log('release add_amount lock', 'id', $playerId);
			}
		}
	}


    private function returnError($errorCode) {
		$response = array(
			"status" => "error", 
			"code" => $errorCode, 
			"message" => self::ERROR_CODE[$errorCode],
			# "action" => "continue", //optional
			# "display" => true //optional
		);
		
		return $response;
	}

	private function saveResponseResult($success, $callMethod, $params, $response){
        //save to db
        $this->CI->load->model("response_result");
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResult($this->gamePlatformId, $flag, $callMethod, json_encode($params), $response, 200, null, null);
    }


}

///END OF FILE////////////