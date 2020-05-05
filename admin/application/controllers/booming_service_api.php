<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Booming_service_api extends BaseController {

	private $APIauth;
	private $player_id;
	private $currencyCode;

	const REFUND = 'refund';
	const BET = 'bet';
	const CALLBACK = 'callback';
	const ROLLBACK = 'rollback';
	const DO_PLAYER_WALLET_REFUND = 'DO_PLAYER_WALLET_REFUND';
	const DO_PLAYER_WALLET_BET = 'DO_PLAYER_WALLET_BET';

	const ERROR_MESSAGE = array(
		"low_balance"   		=> "Not enough funds for the wager",
		"reality_check" 		=> "Reality check message in the operators jurisdiction.",
		"self_excluded" 		=> "Player has excluded self from further game play",
		"loss_limit"  		    => "Player has reached a loss limit",
		"wager_limit"  		    => "Player has reached a wager limit",
		"custom"        		=> "Invalid Session Id or (can occur only on init call) Session not unique",
		"custom_error_internal" => "Something !. Happen please contact customer support service"
	);

	const ERROR_CODE = array(
		"low_balance"   		=> "low_balance",
		"reality_check" 		=> "reality_check",
		"self_excluded" 		=> "self_excluded",
		"loss_limit"    		=> "loss_limit",
		"wager_limit"   		=> "wager_limit",
		"custom"        		=> "custom",
		"custom_error_internal" => "custom"
	);

	function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','player_model','boomingseamless_game_logs', 'boomingseamless_history_logs'));
		$this->game_api_booming = null;
	}

	private function init($game_platform_id) {
		$this->game_api_booming = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$this->gamePlatformId = $game_platform_id;

		// Set the table name for game logs model base on the instantiated booming api table name
		$this->boomingseamless_game_logs->tableName = $this->game_api_booming->original_gamelogs_table;
	}

	public function processMethod($method, $gamePlatformId, $game_code) {

		$this->init($gamePlatformId);

		if ($this->external_system->isGameApiActive($gamePlatformId)) {
			$postJson = file_get_contents("php://input");
			$post = !empty($postJson) ? json_decode($postJson,true) : array();
			$this->utils->debug_log("BOOMING Callback ====> ", $post);

			http_response_code(200);

			$reqParams = ['method' => $method, 'data' => $post];
			$response = 'Booming Service API request';
			$methodRequest = ($method == self::CALLBACK) ? BOOMINGSEAMLESS_GAME_LOGS::RECEIVE_CALLBACK : BOOMINGSEAMLESS_GAME_LOGS::RECEIVE_ROLLBACK;

			$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, $methodRequest, $reqParams, $response);

			if ($method == 'callback' || $method == 'bonus') {

				$responseBet = $this->doPlayerBet($post, $game_code);
				$this->utils->debug_log('POST DATA ===>', $post, 'RESPONSE Callback ===>', $responseBet, 'METHOD ===>', $method, 'PROCESS GAMELOGS  ===>', true);

				echo json_encode($responseBet);;

			} else {

				$this->doCancelBet($post, $game_code);

				$responseBalance = $this->doPlayerBalance($post);
				$this->utils->debug_log('POST DATA ===>', $post, 'RESPONSE Callback ===>', $responseBalance, 'METHOD ===>', $method, 'CANCEL GAMELOGS  ===>', true);

				echo json_encode($responseBalance);

			}
		}
	}

	public function getGameList($gamePlatformId = BOOMING_SEAMLESS_API, $useJsonEncode = 'false') {
		$this->init($gamePlatformId);
		$gameList = $this->game_api_booming->queryGameList()['games'];
		if ($useJsonEncode == 'true') {
			echo json_encode($gameList);
		} else {
			var_dump($gameList);
		}
	}

	// This will do query player balance although error occured
	private function doPlayerBalance($postData) {

		if (empty($postData)) {
			return $this->getResponseButtons(self::ERROR_CODE['custom_error_internal'], self::ERROR_MESSAGE['custom_error_internal']);
		}

		$username = $postData['player_id'];
		$playerId = $this->game_provider_auth->getPlayerIdByPlayerName($username, $this->gamePlatformId);
		$playerName = $this->game_provider_auth->getPlayerUsernameByGameUsername($username, $this->gamePlatformId);
		$queryPlayerBalanceResult = $this->game_api_booming->queryPlayerBalance($playerName);
		$playerBalance = floatval($queryPlayerBalanceResult['balance']);
		$response = array(
			'balance' => $playerBalance
		);

		$reqParams = ['game_username' => $username, 'playerId' => $playerId, 'balance' => $playerBalance];
		$responseResult = 'Booming Service API queryPlayerBalance';
		$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS,BOOMINGSEAMLESS_GAME_LOGS::QUERY_PLAYER_BALANCE, $reqParams, $responseResult);

		return $response;

	}

	// This will cancel the bet that we received because when error occured the callback always go first then rollback (refund) will follow which means that the bet will cancel because a rollback happens and the BO will not save that transaction
	// Because when they proceed on callback it will automatically do wallet manipulation
	
	// This function will do the opposite of doPlayerBet because this will restore back the previous wallet adjustment that happens on callback like if wallet deduction happens during callback this will do wallet addition and vice versa
	private function doCancelBet($postData, $game_code) {

		$username = $postData['player_id'];
		$round = $postData['round'];
		$session_id = $postData['session_id'];
		$bet = $postData['bet'];
		$win = $postData['win'];
		$method = self::ROLLBACK;

		$isExists = $this->boomingseamless_game_logs->checkIfDataExists(['session_id' => $session_id, 'round' => $round, 'external_uniqueid' => $session_id . $round, 'check_rollback' => true]);
		# If the transaction is not exists
		if(empty($isExists)){
			return $this->doPlayerBalance($postData);
		}


		$external_uniqueid = $session_id . $round;
		$data = array('bet_status' => BOOMINGSEAMLESS_GAME_LOGS::BET_UNSETTLED);

		## If updating sync original success then do wallet manipulation
		$bSuccess = $this->boomingseamless_game_logs->updateGameLogsByExternalUniqueId($external_uniqueid, $data);
		if ($bSuccess) {
			$playerId = $this->game_provider_auth->getPlayerIdByPlayerName($username, $this->gamePlatformId);

			$resultAmount = $win - $bet;

			$walletMethod = $this->processWalletTransaction($postData, $playerId, $win, $resultAmount, $method);
			$external_uniqueid = $session_id . $round;

			$reqParams = ['game_username' => $username, 'game_external_uniqueid' => $game_code, 'result_amount' => $resultAmount, 'walletMethod' => $walletMethod, 'external_uniqueid' => $external_uniqueid, 'api_response_data' => $postData];
			$responseResult = 'Booming Service API do player refund';
			$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, self::DO_PLAYER_WALLET_REFUND, $reqParams, $responseResult);
			$this->processHistoryLogs($username, $game_code, $resultAmount, $walletMethod, $external_uniqueid, $postData, self::REFUND);
		}
	}


	private function doPlayerBet($postData, $game_code) {

		if (empty($postData)) {
			return $this->getResponseButtons(self::ERROR_CODE['custom_error_internal'], self::ERROR_MESSAGE['custom_error_internal']);
		}

		$username = $postData['player_id'];
		$type = $postData['type'];
		$round = $postData['round'];
		$bet = $postData['bet'];
		$win = $postData['win'];
		$session_id = $postData['session_id'];
		$freespins = json_encode($postData['freespins']);
		$customer_id = $postData['customer_id'];
		$timeStampNow = date('Y-m-d H:i:s');
		$method = self::CALLBACK;
		$external_uniqueid = $session_id . $round;

		$isExists = $this->boomingseamless_game_logs->checkIfDataExists(['session_id' => $session_id, 'round' => $round, 'external_uniqueid' => $session_id . $round, 'check_rollback' => false]);

		// If callback tiggers again with the same data to cancel duplicates
		if(!empty($isExists)){
			return $this->doPlayerBalance($postData);
		}

		$playerId = $this->game_provider_auth->getPlayerIdByPlayerName($username, $this->gamePlatformId);

		$playerName = $this->game_provider_auth->getPlayerUsernameByGameUsername($username, $this->gamePlatformId);
		$insertData = array(
			"player_id"         => $username,
			"game_id"           => $game_code,
			"round"             => $round,
			"type"              => $type,
			"bet"               => $bet,
			"win"               => $win,
			"freespins_details" => $freespins,
			"customer_id"       => $customer_id,
			"bonus"    		    => !empty($postData['bonus']) ? 1 : 0,
			"game_date"         => $timeStampNow,
			"external_uniqueid" => $external_uniqueid,
			"created_at"        => $timeStampNow,
			"updated_at"        => $timeStampNow,
			"session_id"		=> $session_id,
			"bet_status"		=> BOOMINGSEAMLESS_GAME_LOGS::BET_SETTLED
		);

		$queryPlayerBalanceResult = $this->game_api_booming->queryPlayerBalance($playerName);
		$playerBalance = floatval($queryPlayerBalanceResult['balance']);

		$resultAmount = $win - $bet;

		if (($playerBalance - $bet) < 0) {
			return $this->getResponseButtons(self::ERROR_CODE['low_balance'], self::ERROR_MESSAGE['low_balance']);
		}

		$queryPlayerBalanceResult = $this->game_api_booming->queryPlayerBalance($playerName);
		$playerBalance = floatval($queryPlayerBalanceResult['balance']);

		# The actual wallet adjustment will happen after doing sync original successfully 
		$insertData['before_balance'] = $playerBalance;
		$insertData['after_balance'] = $resultAmount > 0 ? $playerBalance + abs($resultAmount) : $playerBalance - abs($resultAmount);

		# insert gamelogs to boomingseamless_game_logs table
		$data = array($insertData);
		$syncOriginalResult = $this->game_api_booming->doSyncOriginal($data);

		if ($syncOriginalResult['success']) {
			$walletMethod = $this->processWalletTransaction($postData, $playerId, $win, $resultAmount, $method);
			
			$reqParams = ['game_username' => $username, 'game_external_uniqueid' => $game_code, 'result_amount' => $resultAmount, 'walletMethod' => $walletMethod, 'external_uniqueid' => $external_uniqueid, 'api_response_data' => $postData];
			$responseResult = 'Booming Service API do player bet';
			$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, self::DO_PLAYER_WALLET_BET, $reqParams, $responseResult);
			$this->processHistoryLogs($username, $game_code, $resultAmount, $walletMethod, $external_uniqueid, $postData, self::BET);
			if ($this->game_api_booming->syncMerge_every_bet) {
				$this->processMergeToGamelogs();
			}
		}
		
		$bSuccess = ($syncOriginalResult['success']) ? BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS : BOOMINGSEAMLESS_GAME_LOGS::PROCESS_FAIL;

		$reqParams = ['insert_game_logs_data' => $insertData];
		$response = 'Booming sync original';
		$this->game_api_booming->saveToResponseResult($bSuccess ,BOOMINGSEAMLESS_GAME_LOGS::SYNC_ORIGINAL, $reqParams, $response);

		$reqParams = ['data' => $insertData];
		$response = 'Booming Player Bet';
		$this->game_api_booming->saveToResponseResult($bSuccess ,BOOMINGSEAMLESS_GAME_LOGS::DO_PLAYER_BET, $reqParams, $response);

		$response = array(
			'balance' => $insertData['after_balance']
		);

		return $response;
	}

	private function processHistoryLogs($username, $game_code, $resultAmount, $walletMethod, $external_uniqueid, $postData, $adjustmentMethod = 'bet') {

		$historyLogs = array(
			'game_username'             => $username,
			'game_id'                   => $game_code,
			'amount'                    => abs($resultAmount),
			'method'                    => $walletMethod,
			'external_uniqueid'         => $this->game_api_booming->getApiNonce(),
			'booming_external_uniqueid' => $external_uniqueid,
			'details'                   => json_encode($postData),
			'game_platform_id'			=> $this->gamePlatformId
		);

		$bAdjustmentMethod = BOOMINGSEAMLESS_HISTORY_LOGS::HISTORY_WALLET_DO_PLAYER_CANCEL_BET;
		$response = 'Booming rollback history logs';
		if ($adjustmentMethod == 'bet') {
			$bAdjustmentMethod = BOOMINGSEAMLESS_HISTORY_LOGS::HISTORY_WALLET_DO_PLAYER_BET;
		$response = 'Booming callback history logs';
		}
		$this->boomingseamless_history_logs->insertGameLogs($historyLogs);

		$reqParams = ['game_username' => $username, 'external_uniqueid' => $external_uniqueid, 'amount' => $resultAmount, 'other_data' => $postData];
		$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, $bAdjustmentMethod, $reqParams, $response);

	}

	private function processWalletTransaction($postData, $playerId, $win, $resultAmount, $method) {

		## WALLET TRANSACTION FOR CALLBACK and BONUS
		if ($method != 'rollback') {
			## if the bonus spins has a value of true the bet must be 0
			if (!empty($postData['bonus'])) {
				$this->add_amount($playerId, $win);
				$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_ADDITION_FROM_CALLBACK;
			# if bonus exists on call & the data is not from gamble then just add to wallet no deduction
			# gamble spins have a bonus field = true but the type is gamble which should do wallet subtraction or addition			
			}
			else if ($postData['type'] == 'freespin') {
				$this->add_amount($playerId, $win);
				$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_ADDITION_FROM_CALLBACK;
			}
			# if result amount is + then add to wallet else subtract the wallet
		    else if ($resultAmount > 0) { 
				$this->add_amount($playerId, abs($resultAmount));
				$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_ADDITION_FROM_CALLBACK;
			}
			else if ($resultAmount < 0) { 
				$this->subtract_amount($playerId, abs($resultAmount));
				$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_DEDUCTION_FROM_CALLBACK;
			}
			return $method;
		}

		## WALLET TRANSACTION FOR ROLLBACK SINCE THEIR API ALWAYS DO CALLBACK FIRST BEFORE ROLLBACK (REFUND)
		## if the bonus spins has a value of true the bet must be 0
		if (!empty($postData['bonus'])) {
			$this->subtract_amount($playerId, $win);
			$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_DEDUCTION_FROM_ROLLBACK;
		# if bonus exists on call & the data is not from gamble then just add to wallet no deduction
		# gamble spins have a bonus field = true but the type is gamble which should do wallet subtraction or addition			
		}else if ($postData['type'] == 'freespin') {
			$this->subtract_amount($playerId, $win);
			$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_DEDUCTION_FROM_ROLLBACK;
		}
		# if result amount is + then subtract to wallet else add the wallet
	    else if ($resultAmount > 0) { 
			$this->subtract_amount($playerId, abs($resultAmount));
			$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_DEDUCTION_FROM_ROLLBACK;
		}
		else if ($resultAmount < 0) { 
			$this->add_amount($playerId, abs($resultAmount));
			$method = BOOMINGSEAMLESS_HISTORY_LOGS::DO_WALLET_ADDITION_FROM_ROLLBACK;
		}
		return $method;
	}

	## Trigger sync merge every time the player do sync original via callback
	private function processMergeToGamelogs() {
		$dateTimeFrom = new \DateTime(date("Y-m-d"));
		$dateTimeTo = new \DateTime(date("Y-m-d", strtotime(date("Y-m-d")." +1 days")));
		$token = random_string('unique');
		$ignore_public_sync = true;
		$this->game_api_booming->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, null, null, null,
			array('ignore_public_sync' => $ignore_public_sync));
		$rlt = $this->game_api_booming->syncMergeToGameLogs($token);
		$this->utils->debug_log('PROCESS MERGE GAME LOGS ========================> RESULT : ', $rlt);
        $this->game_api_booming->clearSyncInfo($token);
	}

	private function subtract_amount($playerId, $amount) {
		$lockedKey = NULL;
		$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		$this->utils->debug_log('lock subtract_amount', 'id', $playerId, $lock_it);

		if ($lock_it) {
			try {
				$reqParams = ['playerId' => $playerId, 'amount' => $amount, 'gamePlatformId' => $this->gamePlatformId];
				$response = 'Booming deduct wallet process';
				$this->startTrans();
				$this->wallet_model->decSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, BOOMINGSEAMLESS_GAME_LOGS::WALLET_SUBTRACT_METHOD, $reqParams, $response);
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
		$success = false;

		if ($lock_it) {
			try {
				$reqParams = ['playerId' => $playerId, 'amount' => $amount, 'gamePlatformId' => $this->gamePlatformId];
				$response = 'Booming add wallet process';
				$this->startTrans();
				$this->wallet_model->incSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->game_api_booming->saveToResponseResult(BOOMINGSEAMLESS_GAME_LOGS::PROCESS_SUCCESS, BOOMINGSEAMLESS_GAME_LOGS::WALLET_ADD_METHOD, $reqParams, $response);
				$this->endTransWithSucc();
			} finally {
				$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
				$this->utils->debug_log('release add_amount lock', 'id', $playerId);
			}
		}
	}

	private function getResponseButtons($responseMethod,  $message) {
		if ($responseMethod == 'low_balance') {
			$error_code = self::ERROR_CODE['low_balance']; 
		} else if ($responseMethod == 'custom_error_internal') {
			$error_code = self::ERROR_CODE['custom'];
		}

		return array(
					'error'   => $error_code,
					'message' => $message,
					'buttons' => array(
						array(
						'title' => 'Exit',
						'action' => 'exit'
						),
					)
				);

	}
}

///END OF FILE////////////