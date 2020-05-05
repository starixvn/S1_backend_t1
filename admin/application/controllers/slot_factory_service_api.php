<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class slot_factory_service_api extends BaseController {

	const ACTION_LOGIN = "Login";
	const ACTION_PLAY = "Play";
	const ACTION_REWARDBONUS = "RewardBonus";
	const ACTION_GETBALANCE = "GetBalance";

	private $gamePlatformId = SLOT_FACTORY_SEAMLESS_API;
	private $currencyID;
	private $countryID;

	const ERROR_CODE = array(
		"-1" => "Unknown Error",
		"0" => "Successfully processed",
		"1" => "Invalid SessionID",
		"2" => "Invalid request. Missing parameters in the request or invalid format",
		"3" => "Invalid hash. Security hash validation fails",
		"4" => "Invalid player. Player IP address is mismatched, or AccountID not found",
		"5" => "Invalid AuthToken",
		"6" => "Invalid Action Type. None of Login, Play, RewardBonus or PlayReport",
		"7" => "Invalid Timestamp. Not the latest timestamp.",
		"100" => "AuthToken already in used",
		"101" => "Player is blocked",
		"102" => "Player account is not enabled in customer’s system",
		"200" => "Insufficient balance to bet",
		"201" => "Wager permission denied or blocked by customer",
		"202" => "RoundID not found",
		"300" => "Customer site denied to add win to player’s balance",
		"301" => "RoundID not found",
		"600" => "Invalid partner",
		"601" => "Invalid AccountID",
		"602" => "Invalid game name",
		"603" => "Invalid customer name",
		"604" => "Invalid duration",
		"605" => "Transaction ID Already Exist",
	);

	public function __construct() {

		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','player_model', 'game_description_model', 'slot_factory_transactions'));
		$this->game_api_slot_factory = $this->utils->loadExternalSystemLibObject($this->gamePlatformId);
		$this->currencyID = $this->game_api_slot_factory->getCurrency();
		$this->countryID = $this->game_api_slot_factory->getCountryCode();

	}

	public function index() {

		if ($this->external_system->isGameApiActive($this->gamePlatformId)) {

			$params = file_get_contents('php://input');
			$hmacHeader = $this->input->request_headers()['Hmac'];
			$HMAC = $this->game_api_slot_factory->generateHMAC($params);
			$reqParams = json_decode($params, true);
			// $header = $this->game_api_slot_factory->getHttpHeaders($params);
			// print_r($header);exit();

			$this->utils->debug_log('<---------------Slot Factory------------> request params: ', $reqParams);

			$response = [];
			$success = true;

			if($HMAC == $hmacHeader) {
				try{
					$fullRequest = $reqParams;
					$gameUsername = isset($fullRequest['AccountID']) ? $fullRequest['AccountID'] : null;
					$playerUsername = $this->game_api_slot_factory->getPlayerUsernameByGameUsername($gameUsername, $this->gamePlatformId);
					$playerId = $this->player_model->getPlayerIdByUsername($playerUsername);

					# check player if exists
					if(empty($playerUsername)){
						throw new Exception('4');
					}
					
					$callMethod = $reqParams['Action'];
					$response_result_id = $this->saveResponseResult($success, $callMethod, $reqParams, $response);
					$fullRequest['response_result_id'] = $response_result_id;
					$response = $this->callMethod($playerId,$gameUsername,$callMethod,$fullRequest);


				} catch (Exception $e) {
					log_message('error', self::ERROR_CODE[$e->getMessage()]);
					$response = $this->returnError($e->getMessage());
				}

			} else {
				$success = false;
				$response = $this->returnError('3');
			}
				
			$hmacResponse = $this->game_api_slot_factory->generateHMAC(json_encode($response));

			header('HMAC: ' . $hmacResponse);
			header('Content-Type: application/json');
			header('Content-Length: ' . strlen(json_encode($response)));

			# save response result and API request
			$this->saveResponseResult($success,$callMethod,$reqParams,$response);

			return $this->returnJsonResult($response);
		}

	}

	private function callMethod($playerId,$gameUsername,$callMethod,$fullRequest=null){

		switch ($callMethod){
			case self::ACTION_LOGIN:
				return $this->Login($playerId,$gameUsername,$fullRequest);
				break;
			case self::ACTION_PLAY:
				return $this->Play($playerId,$gameUsername,$fullRequest);
				break;
			case self::ACTION_REWARDBONUS:
				return $this->rewardBonus($playerId,$gameUsername,$fullRequest);
				break;
			case self::ACTION_GETBALANCE: 
				return $this->getBalance($playerId,$gameUsername,$fullRequest);
				break;

			default:
				throw new Exception('2');
				break;
		}
	}

	public function Login($playerId,$gameUsername,$fullRequest) {

		$token = isset($fullRequest['AuthToken']) ? $fullRequest['AuthToken'] : null;
		$validateToken = $this->common_token->isTokenValid($playerId, $token);
		$slotFactoryPlayerIp = isset($fullRequest['PlayerIP']) ? $fullRequest['PlayerIP'] : null;
		$gameName = isset($fullRequest['GameName']) ? $fullRequest['GameName'] : null;
		$playerIp = $this->game_api_slot_factory->getPlayerIp();		
		
		if($validateToken) {
			$playerDetails = $this->player_model->getPlayerInfoDetailById($playerId);
			$gameCode = $this->game_description_model->checkIfGameCodeExist($this->gamePlatformId, $gameName);

			# check if gameCode exists
			if(empty($gameCode)) {
				throw new Exception('602');
			}

			# check if Player Exists
			if(empty($gameUsername)){
				throw new Exception('601');
			}

			$isRegistered = $this->game_provider_auth->isRegisterd($playerId, $this->gamePlatformId);

			# check if Player is Registered
			if( ! $isRegistered) {
				throw new Exception('102');
			}

			# check if Player is Blocked
			if($this->game_api_slot_factory->isBlocked($playerDetails['username'])){
				throw new Exception('101');
			}

			# check if player ip from site is same with factory slot player ip
			// if($slotFactoryPlayerIp != $playerIp){
			// 	throw new Exception('4');
			// }

			$playerBalance = $this->game_api_slot_factory->queryPlayerBalance($playerDetails['username'])['balance'];

			$convertedPlayerBalance = $this->convertBalance($playerBalance);

			$newAuthToken = $this->common_token->createTokenBy($playerId, 'player_id');

			if($newAuthToken) {
				$this->common_token->disableToken($token);
			}

			/* 
				** 
				****
				****  All parameters should have a value, if one of the response param is null it will cause an error on provider side
				****
				**  
			*/

			$response = array(
				"StatusCode" 		=> 0, 
				"StatusDescription" => "Player is Valid", 
				"SessionID" 		=> "", 
				"AccountID" 		=> $gameUsername,
				"PlayerIP" 			=> $playerIp,
				"Timestamp" 		=> $this->timeStamp(),
				"FirstName" 		=> $playerDetails['firstName'],
				"LastName" 			=> $playerDetails['lastName'],
				"DateOfBirth" 		=> !empty($playerDetails['birthdate']) ? $playerDetails['birthdate'] : '1995-01-01',
				"CountryID" 		=> $this->countryID,
				"CurrencyID" 		=> $this->currencyID,
				"Balance" 			=> strval($convertedPlayerBalance),
				"AuthToken" 		=> $newAuthToken,
			);

			$this->CI->utils->debug_log("Login Params response: ", $response);

			return $response;

		} else {
			throw new Exception('5');
		}


	}

	/*
		***
		*****	
		*****	BetAmount and WinAmount should be converted to x100. Provider doesn't use deceimal so the last 2 digits represent the decimal
		*****	eg. If the user balance is 100, we should send them 10000.
		*****	Provider: 'Ignoring decimal is to prevent any data type error'
		*****
		***
	*/

	public function Play($playerId,$gameUsername,$fullRequest) {

		$token = isset($fullRequest['AuthToken']) ? $fullRequest['AuthToken'] : null;
		$validateToken = $this->common_token->isTokenValid($playerId, $token);
		$gameName = isset($fullRequest['GameName']) ? $fullRequest['GameName'] : null;
		$transaction_id = isset($fullRequest['TransactionID']) ? $fullRequest['TransactionID'] : null;
		$betAmount = isset($fullRequest['BetAmount']) ? $this->convertAmount($fullRequest['BetAmount']) : null;
		$winAmount = isset($fullRequest['WinAmount']) ? $this->convertAmount($fullRequest['WinAmount']) : null;
		$round_id = @$fullRequest['RoundID'];
		$sessionId = @$fullRequest['SessionID'];
		$playerIp = $this->game_api_slot_factory->getPlayerIp();

		if($validateToken) {
			$playerInfo = $this->common_token->getPlayerInfoByToken($token);
			$gameCode = $this->game_description_model->checkIfGameCodeExist($this->gamePlatformId, $gameName);

			# check if Round ID is not empty
			if(empty($round_id)){
				throw new Exception('202');
			}

			$existingRow = $this->slot_factory_transactions->isTransactionIdAlreadyExists($transaction_id);

			# check if Transaction ID is existing
			if($existingRow){
				throw new Exception('605');
			}

			# check if player is allowed to bet
			if($this->game_api_slot_factory->isBlocked($playerInfo['username'])){
				throw new Exception('201');
			}

			$playerBeforeBalanceResult = $this->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

			$convertedPlayerBalance = $playerBeforeBalanceResult['balance'] * 100;

			$checkIfNotAllowedToBet = ($convertedPlayerBalance - $betAmount) < 0;

			if ($checkIfNotAllowedToBet) {

				throw new Exception('200');

			} else {

				$debitCreditBalance = $this->debitCreditBalance($playerId,$betAmount,$winAmount,$playerInfo,$fullRequest);

				$playerAfterBalanceResult = $this->game_api_slot_factory->queryPlayerBalance($playerInfo['username'])['balance'];

				$convertedPlayerBalance = $this->convertBalance($playerAfterBalanceResult);

			}
			
			$response = array(
				"StatusCode" 		=> 0, 
				"StatusDescription" => $checkIfNotAllowedToBet ? "Insufficient Balance" : "Player balance is updated and is allowed to take a spin", 
				"SessionID"			=> $sessionId, 
				"AccountID"			=> $gameUsername,
				"PlayerIP" 			=> $playerIp,
				"Timestamp" 		=> $this->timeStamp(),
				"Balance" 			=> strval($convertedPlayerBalance),
			);

			$this->CI->utils->debug_log("Play Params response: ", $response);

			return $response;

		} else {

			throw new Exception('5');

		}


	}

	private function debitCreditBalance($playerId,$betAmount,$winAmount,$playerInfo,$data) {

		$controller = $this;

		$isSuccess = $this->lockAndTransForPlayerBalance($playerId, function() use($controller,$playerId,$betAmount,$winAmount,$playerInfo,$data) {

			$before_balance = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

			$isDeduct = $controller->wallet_model->decSubWallet($playerId, $controller->gamePlatformId, $betAmount);

			if(isset($winAmount) && !empty($winAmount) && $isDeduct) {

				$after_deduct = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username'])['balance'];

				$controller->wallet_model->incSubWallet($playerId, $controller->gamePlatformId, $winAmount);

        		$after_balance = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

        		$controller->processTransactionRecord($data,$before_balance['balance'],$after_balance['balance']);

        		$controller->CI->utils->debug_log("Slot Factory deduct to subwallet is: ", $isDeduct, "Balance After Deduct: ", $after_deduct);


			} 
			elseif (isset($winAmount) || !empty($winAmount) && !isset($betAmount) || empty($betAmount)) {

				$after_deduct = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username'])['balance'];

				$controller->wallet_model->incSubWallet($playerId, $controller->gamePlatformId, $winAmount);

				$after_balance = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

				$controller->processTransactionRecord($data,$before_balance['balance'],$after_balance['balance']);

				$controller->CI->utils->debug_log("Slot Factory subwallet balance after winning: ", $after_balance);

			}

			return $after_balance;

		});

		return $isSuccess;

	}

	public function getBalance($playerId,$gameUsername,$fullRequest) {

		$token = isset($fullRequest['AuthToken']) ? $fullRequest['AuthToken'] : null;
		$validateToken = $this->common_token->isTokenValid($playerId, $token);
		$sessionId = @$fullRequest['SessionID'];
		$playerIp = $this->game_api_slot_factory->getPlayerIp();

		if($validateToken) {
			$playerDetails = $this->common_token->getPlayerInfoByToken($token);
			$playerBalance = $this->game_api_slot_factory->queryPlayerBalance($playerDetails['username'])['balance'];
			$convertedPlayerBalance = $playerBalance * 100;

			$response = array(
				"StatusCode" 		=> 0, 
				"StatusDescription" => "Player Successfully update Balance", 
				"SessionID" 		=> $sessionId, 
				"AccountID"			=> $gameUsername,
				"PlayerIP" 			=> $playerIp,
				"Timestamp" 		=> $this->timeStamp(),
				"Balance" 			=> strval($convertedPlayerBalance),
			);

			$this->CI->utils->debug_log("Get Balance Params response: ", $response);

			return $response;

		} else {

			throw new Exception('5');

		}

	}

	public function rewardBonus($playerId,$gameUsername,$fullRequest) {

		$token = isset($fullRequest['AuthToken']) ? $fullRequest['AuthToken'] : null;
		$validateToken = $this->common_token->isTokenValid($playerId, $token);
		$gameName = isset($fullRequest['GameName']) ? $fullRequest['GameName'] : null;
		$transaction_id = isset($fullRequest['TransactionID']) ? $fullRequest['TransactionID'] : null;
		$isFreeGames = isset($fullRequest['FreeGames']) ? $fullRequest['FreeGames'] : null;
		$winAmount = isset($fullRequest['WinAmount']) ? $this->convertAmount($fullRequest['WinAmount']) : null;
		$round_id = @$fullRequest['RoundID'];
		$sessionId = @$fullRequest['SessionID'];

		if($validateToken) {
			$playerInfo = $this->common_token->getPlayerInfoByToken($token);
			$playerDetails = $this->player_model->getPlayerInfoDetailById($playerId);
			$gameCode = $this->game_description_model->checkIfGameCodeExist($this->gamePlatformId, $gameName);

			# check if player is Allowed/not Blocked
			if($this->game_api_slot_factory->isBlocked($playerInfo['username'])){
				throw new Exception('201');
			}

			$existingRow = $this->slot_factory_transactions->isTransactionIdAlreadyExists($transaction_id);

			# check if Transaction ID is existing
			if($existingRow){
				throw new Exception('605');
			}

			# check if RoundID is not empty
			if(empty($round_id)) {
				throw new Exception('301');
			} else {
				$this->creditBonus($playerId,$winAmount,$playerInfo,$fullRequest);

				$playerBalance = $this->game_api_slot_factory->queryPlayerBalance($playerInfo['username'])['balance'];

				$convertedPlayerBalance = $this->convertBalance($playerBalance);
			}

			$response = array(
				"StatusCode" 		=> 0, 
				"StatusDescription" => "Player Successfully added Reward Bonus", 
				"SessionID" 		=> $sessionId, 
				"AccountID"			=> $gameUsername,
				"PlayerIP" 			=> $playerDetails['lastLoginIp'],
				"Timestamp" 		=> $this->timeStamp(),
				"Balance" 			=> strval($convertedPlayerBalance),
			);

			$this->CI->utils->debug_log("Reward Bonus Params response: ", $response);

			return $response;

		} else {
			throw new Exception('5');
		}

	}

	private function creditBonus($playerId,$winAmount,$playerInfo,$data) {

		$controller = $this;

		$isSuccess = $this->lockAndTransForPlayerBalance($playerId, function() use($controller,$playerId,$winAmount,$playerInfo,$data) {

			$before_balance = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

			$isAdded = $controller->wallet_model->incSubWallet($playerId, $controller->gamePlatformId, $winAmount);

			if($isAdded) {

				$after_balance = $controller->game_api_slot_factory->queryPlayerBalance($playerInfo['username']);

				$controller->processTransactionRecord($data,$before_balance['balance'],$after_balance['balance']);

				$controller->CI->utils->debug_log("Slot Factory balance after BonusCredit: ", $after_balance);

			}

			return $isAdded;

		});

		return $isSuccess;

	}

	public function processTransactionRecord(&$trans_record, $before_balance=null, $after_balance=null) {
		
		$data['SessionID'] = isset($trans_record['SessionID']) ? $trans_record['SessionID'] : null;
		$data['AccountID'] = isset($trans_record['AccountID']) ? $trans_record['AccountID'] : null;
		$data['GameName'] = isset($trans_record['GameName']) ? $trans_record['GameName'] : null;
		$data['AuthToken'] = isset($trans_record['AuthToken']) ? $trans_record['AuthToken'] : null;
		$data['Action'] = isset($trans_record['Action']) ? $trans_record['Action'] : null;
		$data['PlayerIP'] = isset($trans_record['PlayerIP']) ? $trans_record['PlayerIP'] : null;
		$data['Timestamp'] = isset($trans_record['Timestamp']) ? $trans_record['Timestamp'] : null;
		$data['TransactionID'] = isset($trans_record['TransactionID']) ? $trans_record['TransactionID'] : null;
		$data['RoundID'] = isset($trans_record['RoundID']) ? $trans_record['RoundID'] : null;
		$data['BetAmount'] = isset($trans_record['BetAmount']) ? $this->convertAmount($trans_record['BetAmount']) : null;
		$data['WinAmount'] = isset($trans_record['WinAmount']) ? $this->convertAmount($trans_record['WinAmount']) : null;
		$data['GambleGames'] = isset($trans_record['GambleGames']) ? $trans_record['GambleGames'] : null;
		$data['Type'] = isset($trans_record['Type']) ? $trans_record['Type'] : null;
		$data['FreeGames'] = isset($trans_record['FreeGames']) ? $trans_record['FreeGames'] : null;

		$data['response_result_id'] = isset($trans_record['response_result_id']) ? $trans_record['response_result_id'] : null;
		$data['external_uniqueid'] = $data['TransactionID'];
		$data['before_balance'] = $before_balance;
		$data['after_balance'] = $after_balance;
		$trans_record = $data;

		$this->insertTransactionWithLog($trans_record);

	}

	private function returnError($errorCode) {
		$response = array(
			"status" => "error", 
			"code" => $errorCode, 
			"message" => self::ERROR_CODE[$errorCode],
		);
		
		return $response;
	}

	private function saveResponseResult($success, $callMethod, $params, $response){
        //save to db
        $this->CI->load->model("response_result");
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;

        return $this->CI->response_result->saveResponseResult($this->gamePlatformId, $flag, $callMethod, json_encode($params), $response, 200, null, null);

    }

    private function timeStamp() {
    	$timeStamp = new DateTime();
		$newTimeStamp = $timeStamp->getTimestamp();

		return $newTimeStamp;
    }

    private function insertTransactionWithLog($data) {
        $affected_rows = $this->slot_factory_transactions->insertTransaction($data);

        return $affected_rows;
    }

    private function convertBalance($balance) {
    	$bal = $balance * 100;

    	return $bal;
    }

    private function convertAmount($amount) {
    	$amt = $amount / 100;

    	return $amt;
    }


}