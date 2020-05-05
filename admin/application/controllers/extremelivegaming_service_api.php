<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Extremelivegaming_service_api extends BaseController {

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','extreme_live_gaming_game_logs'));
		$this->game_api = $this->utils->loadExternalSystemLibObject(EXTREME_LIVE_GAMING_API);
	}

	const ACTIVE = 'Active';

	public function getUserInfo($gameName){
		$token = filter_input(INPUT_GET,"NrgsUserToken",FILTER_SANITIZE_STRING);
		$playerId = $this->CI->game_provider_auth->getPlayerIdByPlayerName($gameName, EXTREME_LIVE_GAMING_API);
		$playerToken = $this->game_api->getPlayerToken($playerId);

		try {
			if($playerId == null) {
				$error = $this->getErrorMessage(404,'UserUnknown','The user is not known.');
				throw new Exception(json_encode($error));
			}
			if($playerToken !== $token) {
				$error = $this->getErrorMessage(404,'TokenInvalid','Token is invalid, was not found, or is already expired.');
				throw new Exception(json_encode($error));
			}

			$playerInfo = $this->game_api->getPlayerInfo($playerId);
			$balance_result = $this->game_api->queryPlayerBalance($playerInfo->username);
			$response = array(
				"UserId" 		=> $gameName,
				"State" 		=> self::ACTIVE,
				"LanguageCode" 	=> $this->game_api->getLanguage(),
				"CountryCode" 	=> $this->game_api->getCountryCode(),
				"CurrencyCode" => $this->game_api->getCurrency(),
				"RealMoney" 	=> $this->game_api->dBtoGameAmount($balance_result['balance']),
				"BonusMoney" 	=> 0,
			);

		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response['Error'] = json_decode($e->getMessage());
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function prolongToken($gameName,$token){
		$playerId = $this->CI->game_provider_auth->getPlayerIdByPlayerName($gameName, EXTREME_LIVE_GAMING_API);
		$checkToken = $this->CI->common_token->getIdByAvailableToken('id', $token);

		$dateTimeNow = new DateTime(date('Y-m-d H:i:s'));
		$dateTimeNow->modify('+2 hours');
		$timeout = $dateTimeNow->format('Y-m-d H:i:s');
		
		try {
			if(empty($playerId)) {
				$error = $this->getErrorMessage(404,'UserUnknown','The user is not known.');
				throw new Exception(json_encode($error));
			}
			if(empty($checkToken)) {
				$error = $this->getErrorMessage(404,'TokenInvalid','Token is invalid, was not found, or is already expired.');
				throw new Exception(json_encode($error));
			}

			$this->CI->common_token->updatePlayerToken($playerId, $token, 7200);

			$response = array(
				"NrgsUserToken" 	=> $token,
				"ExpiryDate" 		=> $this->game_api->serverTimeToGameTime($timeout),
			);

		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response['Error'] = json_decode($e->getMessage());
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function transaction($gameName){
		$token = filter_input(INPUT_GET,"NrgsUserToken",FILTER_SANITIZE_STRING);
		$playerId = $this->CI->game_provider_auth->getPlayerIdByPlayerName($gameName, EXTREME_LIVE_GAMING_API);
		$playerToken = $this->game_api->getPlayerToken($playerId);
		$reqString = file_get_contents('php://input');
		$reqArray = json_decode($reqString, true);
		$sbe_username =  $this->CI->game_provider_auth->getPlayerUsernameByGameUsername($reqArray['UserId'], EXTREME_LIVE_GAMING_API);
		$this->createResponseResult($reqArray['TransactionReferenceId'],$reqArray['TransactionType'],$reqArray);
		$this->utils->debug_log('>>>>>>>>>>>>>> ELG request monitor', $reqArray);
		
		try {
			if($playerId == null) {
				$error = $this->getErrorMessage(404,'UserUnknown','The user is not known.');
				throw new Exception(json_encode($error));
			}
			if($playerToken !== $token) {
				$error = $this->getErrorMessage(404,'TokenInvalid','Token is invalid, was not found, or is already expired.');
				throw new Exception(json_encode($error));
			}

			$transactionReferenceCreationDate = new DateTime($reqArray['TransactionReferenceCreationDate']);
			$transactionReferenceCreationDate = $transactionReferenceCreationDate->format('Y-m-d H:i:s');

			$entityReferences = json_decode(json_encode($reqArray['EntityReferences'][0]),true);
			$references = json_decode(json_encode($entityReferences['References'][0]),true);
			$bet_referenceId = $references['ReferenceId'];
			$transaction = array(
				"userId" => isset($reqArray['UserId']) ? $reqArray['UserId'] : NULL,
				"nrgsUserToken" => isset($reqArray['NrgsUserToken']) ? $reqArray['NrgsUserToken'] : NULL,
				"transactionType" => isset($reqArray['TransactionType']) ? $reqArray['TransactionType'] : NULL,
				"transactionReferenceId" => isset($reqArray['TransactionReferenceId']) ? $reqArray['TransactionReferenceId'] : NULL,
				"transactionReferenceCreationDate" => isset($reqArray['TransactionReferenceCreationDate']) ? $this->game_api->gameTimeToServerTime($transactionReferenceCreationDate) : NULL,
				// "transactionCreationDate" => isset($reqArray['TransactionCreationDate']) ? $this->game_api->gameTimeToServerTime($TransactionCreationDate) : NULL,
				// "transactionId" => isset($reqArray['TransactionId']) ? $reqArray['TransactionId'] : NULL,
				"userIpAddress" => isset($reqArray['UserIpAddress']) ? $reqArray['UserIpAddress'] : NULL,
				"realMoney" => isset($reqArray['RealMoney']) ? $this->game_api->gameAmountToDB((float)$reqArray['RealMoney']) : NULL,
				"realRake" => isset($reqArray['RealRake']) ? $reqArray['RealRake'] : NULL,
				"bonusMoney" => isset($reqArray['BonusMoney']) ? $reqArray['BonusMoney'] : NULL,
				"bonusRake" => isset($reqArray['BonusRake']) ? $reqArray['BonusRake'] : NULL,
				"currencyCode" => isset($reqArray['CurrencyCode']) ? $reqArray['CurrencyCode'] : NULL,
				"entityReferences" => isset($reqArray['EntityReferences']) ? json_encode($reqArray['EntityReferences']) : NULL,
				"game" => isset($reqArray['Game']) ? json_encode($reqArray['Game']) : NULL,
				"bets" => isset($reqArray['Bets ']) ? json_encode($reqArray['Bets ']) : NULL,
				"bet_referenceId" => isset($bet_referenceId) ? $bet_referenceId : NULL,
				"username" => $sbe_username,
				"playerId" => $playerId,
				"external_uniqueid" => isset($reqArray['TransactionReferenceId']) ? $reqArray['TransactionReferenceId'] : NULL,
				"created_at" => date('Y-m-d H:i:s'),
				"updated_at" => date('Y-m-d H:i:s'),
			);
			$this->utils->debug_log('>>>>>>>>>>>>>> ELG transaction monitor', $transaction);
			$exist = $this->CI->extreme_live_gaming_game_logs->isRowIdAlreadyExists($transaction['transactionReferenceId']);
			if(!$exist) {
				$this->extreme_live_gaming_game_logs->insertGameLogs($transaction);
			}
			$checkTransaction = $this->checkTransaction($transaction);
			$response = $checkTransaction;

		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response['Error'] = json_decode($e->getMessage());
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	public function checkTransaction($params){
		switch ($params['transactionType']) {
			case 'NrgsB2bRound_Stake':
			case 'NrgsB2bRound_Win':
				return $this->placeBetOrWin($params);
				break;
			case 'NrgsB2bRound_StakeCancel':
				return $this->placeBetOrWin($params,true);
				break;
		}
	}

	public function placeBetOrWin($params, $cancel = false){
		$balance_result = $this->game_api->queryPlayerBalance($params['username']);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		if (($balance - abs($params['realMoney'])) < 0) {
			$error = $this->getErrorMessage(412,'InsufficientFunds','The user does not have the required funds..');
			throw new Exception(json_encode($error));
		}
		$transactionReferenceCreationDate = $this->game_api->serverTimeToGameTime($params['transactionReferenceCreationDate']);
		$transactionReferenceCreationDate = new DateTime($transactionReferenceCreationDate);
		$transactionReferenceCreationDate = $transactionReferenceCreationDate->format('Y-m-d\TH:i:s\Z');

		$transactionId = time();
		$transactionCreationDate = $this->utils->getNowDateTime();
		$transactionCreationDate = new DateTime($this->game_api->serverTimeToGameTime($transactionCreationDate));
		$transactionCreationDate = $transactionCreationDate->format('Y-m-d\TH:i:s\Z');
		($params['realMoney'] > 0) ? $this->add_amount($params['playerId'],abs($params['realMoney'])) : $this->subtract_amount($params['playerId'],abs($params['realMoney']));
		$afterBalanceResult = $this->game_api->queryPlayerBalance($params['username']);
		$afterBalance = isset($afterBalanceResult['success']) && $afterBalanceResult['success'] ? $afterBalanceResult['balance'] : 0;
		$merge = $this->syncMergeToGameLogsInstantly($params);
		return $transaction = array(
				"TransactionType" 					=> $params['transactionType'],
				"TransactionReferenceId" 			=> $params['transactionReferenceId'],
				"TransactionReferenceCreationDate" 	=> $transactionReferenceCreationDate,
				"TransactionId" 					=> $transactionId,
				"TransactionCreationDate" 			=> $transactionCreationDate,
				"RealMoney" 						=> $this->game_api->dBtoGameAmount($params['realMoney']),
				"BonusMoney" 						=> $this->game_api->dBtoGameAmount($params['bonusMoney']),
				"CurrencyCode" 						=> $params['currencyCode'],
				"User" => array(
					"UserId" 		=> $params['playerId'],
					"State" 		=> self::ACTIVE,
					"LanguageCode" 	=> $this->game_api->getLanguage(),
					"CountryCode " 	=> $this->game_api->getCountryCode(),
					"CurrencyCode " => $this->game_api->getCurrency(),
					"RealMoney" 	=> $this->game_api->dBtoGameAmount($afterBalance),
					"BonusMoney" 	=> 0.0,
				),
			);

	}

	public function syncMergeToGameLogsInstantly($params){
		$this->CI->load->model('game_description_model');
		$game = array(array_key_exists('game', $params)) ? json_decode($params['game']) : NULL;
		$gameDetails = $this->CI->game_description_model->getGameDescriptionByGamePlatformIdAndGameCode(EXTREME_LIVE_GAMING_API,$game->GameName);
		$unknownGame = $this->game_api->getUnknownGame();
		//bet deatails
		$realMoney = isset($params['realMoney']) ? $params['realMoney'] : NULL;
		$bet_amount = abs($realMoney);
		$result_amount = $realMoney;
		$external_uniqueid = isset($params['external_uniqueid']) ? $params['external_uniqueid'] : NULL;
		$bet_date = isset($params['transactionReferenceCreationDate']) ? $params['transactionReferenceCreationDate'] : NULL;
		//end
		$checkStake =  $this->CI->extreme_live_gaming_game_logs->getStakeByReferenceId($params['bet_referenceId']);
		if($params['transactionType'] !== "NrgsB2bRound_Stake" && (!empty($checkStake))) {
			$bet_amount = abs($checkStake->realMoney);
			$result_amount = $realMoney - $bet_amount;	
			$external_uniqueid = $checkStake->external_uniqueid;
			$bet_date = $checkStake->transactionReferenceCreationDate;
		}
		$data = array(
			"player_id" 			=> isset($params['playerId']) ? $params['playerId'] : NULL,
			"username" 				=> isset($params['username']) ? $params['username'] : NULL,
			"game_code" 			=> (!empty($gameDetails)) ? $gameDetails[0]->game_code : $unknownGame->game_code,
			"game_description_id" 	=> (!empty($gameDetails)) ? $gameDetails[0]->id : $unknownGame->id,
			"game_type_id" 			=> (!empty($gameDetails)) ? $gameDetails[0]->game_type_id : $unknownGame->game_type_id,
			"gameshortcode" 		=> (!empty($gameDetails)) ? $gameDetails[0]->game_code : $unknownGame->game_code,
			"game" 					=> (!empty($gameDetails)) ?	$gameDetails[0]->game_name : $unknownGame->game_name,
			"game_type" 			=> (!empty($gameDetails)) ? $gameDetails[0]->game_type : $unknownGame->game_type,
			"transactionType"		=> isset($params['transactionType']) ? $params['transactionType'] : NULL,
		);	
		
		$status = $this->getGameRecordsStatus($data['transactionType']);
		$extra = array(
			'trans_amount'	=> 	$bet_amount,
			'status'		=> 	$status,
			// 'table'			=>  'test',
		);
		// return $extra;
		$this->game_api->syncGameLogs(
			$data['game_type_id'],
			$data['game_description_id'],
			$data['game_code'],
			$data['game_type'],
			$data['game'],
			$data['player_id'],
			$data['username'],
			$bet_amount,
			$result_amount,
			null, # win_amount
			null, # loss_amount
			null,//$data['after_balance'], # after_balance
			0, # has_both_side
			$external_uniqueid,
			$bet_date, //start
			$bet_date, //end
			null,
			Game_logs::FLAG_GAME,
			$extra
		);
	}

	public function getErrorMessage($statusCode,$status,$description){
		$data = array(
				"HttpStatus" => $statusCode,
				"DetailStatus" => $status,
				"ErrorDescription" => $description
		);
		return $data;
	}

	public function createResponseResult($requestId,$methodName,$params) {
		$this->db->insert('response_results', array(
			'note' 				=> $requestId,
			'system_type_id' 	=> $this->game_api->getPlatformCode(),
			'request_api' 		=> $methodName,
			'request_params' 	=> json_encode($params),
			'created_at' 		=> date('Y-m-d H:i:s'),
		));
		$this->response_result_id = $this->db->insert_id();
	}

	private function subtract_amount($playerId, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($playerId, function() use($controller, $playerId, $amount) {
			return $controller->wallet_model->decSubWallet($playerId, $controller->game_api->getPlatformCode(), $amount);			
		});
	}

	private function add_amount($playerId, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($playerId, function() use($controller, $playerId, $amount) {
			return $controller->wallet_model->incSubWallet($playerId, $controller->game_api->getPlatformCode(), $amount);
		});
	}

	/**
	 * overview : get game record status
	 *
	 * @param $status
	 * @return int
	 */
	private function getGameRecordsStatus($status) {
		$this->CI->load->model(array('game_logs'));
		$status = strtolower($status);

		switch ($status) {
		case 'nrgsb2bround_stakecancel':
			$status = Game_logs::STATUS_CANCELLED;
			break;
		case 'nrgsb2bround_stake':
		case 'nrgsb2bround_win':
			$status = Game_logs::STATUS_SETTLED;
			break;
		}
		return $status;
	}
}

///END OF FILE////////////