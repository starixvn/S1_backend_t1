<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class ls_casino_service_api extends BaseController {
	private $player_id;
	private $currency;
	private $betLimists;
	private $gamePlatformId = LS_CASINO_GAME_API;
	private $hashSecretKey;
	private $licenseId;

	function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model', 'game_provider_auth', 'game_logs', 'common_token', 'player_model', 'ls_casino_game_logs', 'game_description_model'));
		
		$this->_api 			= $this->utils->loadExternalSystemLibObject($this->gamePlatformId);
		$this->currency 		= $this->_api->getCurrency();
		$this->betLimits 		= $this->_api->getBetLimits();
		$this->hashSecretKey 	= $this->_api->getHashSecretKey();
		$this->licenseId 		= $this->_api->getLicenseId();
	}

	public function index($apiKey = null) {
		# Check if api is active
		if ($this->external_system->isGameApiActive($this->gamePlatformId)) {
			$result 		= file_get_contents('php://input');

			# encryption combination SHA256(SHA256(<secretkey>)<parameter>))			
			# sample SHA256(SHA256("secretphrase"){"action":"initiateSession","session":"vsts3f3jadgns7ogj81j19gc43"})
			$hashRequest 	=  $this->hashSHA256($this->hashSecretKey.$result);
			$request 		= json_decode($result,true);			
			$response 		= array();			
			$callMethod 	= isset($request['action']) ? $request['action'] : null;

			$this->utils->debug_log('>>>>>>>>>>>>>> livesolution receive request', $request);
			$this->utils->debug_log('>>>>>>>>>>>>>> livesolution hashSecretKey', $this->hashSecretKey);
			$this->utils->debug_log('>>>>>>>>>>>>>> livesolution hashSecretKey.result', $this->hashSecretKey.$result);
			$this->utils->debug_log('>>>>>>>>>>>>>> livesolution apiKey vs vs hashRequest', $apiKey, " - ", $hashRequest);

			# check uri paramer vs encryption is match
			if(strtolower($apiKey) === $hashRequest) {
				try {
					if (!empty($request)) {
						switch (strtolower($callMethod)) {
							case 'initiatesession':		// initiateSession
								$response = $this->initiateSession($request);
								break;
							case 'getlimits':			// getLimits			
								$response = $this->getLimits($request);
								break;
							case 'debituser':			// debitUser
								$response = $this->bet($request);
								break;
							case 'credituser':			// credituser
								$response = $this->win($request);
								break;
							case 'presenterfanclub':	// presenterfanclub
								$response = $this->presenterFanClub($request);
								break;
							default:
								$response = $this->returnError('Invalid request!');
								break;
						}
					} else {
						$response = $this->returnError('Invalid request!');
					}
				}catch (Exception $e) {
					log_message('error', self::ERROR_CODE[$e->getMessage()]);					
					$response = $this->returnError($e->getMessage());
				}
			}else{
				$response = $this->returnError('Apikey does not match with the request!');				
			}

			# save our response result and API request on our system
			if (!isset($response['saveResponse']) || !empty($response['saveResponse'])) {
				$this->saveResponseResult(!$response["error"], $callMethod, $request, $response);	
			} else {
				unset($response['saveResponse']);
			}
			
			
			return $this->returnJsonResult($response);
		}
	}

	# validate session token
	private function initiateSession($request){
		# validate entry
		if (!isset($request['session']) || empty($request['session'])) {
			return $this->returnError('Value for session is required!');
		}

		$playerId = $this->_api->getPlayerIdByToken($request['session']);
		if (empty($playerId)) {
			return $this->returnError('Invalid token!');
		}
		
		if(!empty($playerId)){
			$username = $this->_api->getUsernameById($playerId);
			$gameUsername = $this->_api->getGameUsernameByPlayerId($playerId);
			$playerBalance = $this->_api->queryPlayerBalance($username);
			
			$this->utils->debug_log(">>>>>>>>>>>>> Live Solution initiateSession > GameUsername", $gameUsername, " >>>>>> balance > ", $playerBalance);

			return array(
				"error" => false,
				"message" => "Authorised",
				"userid" => (int) $playerId,
				"username" => $gameUsername,
				"balance" => $this->convertSbeAmountToGameAmount($playerBalance['balance']),
				"currency" => $this->currency,
			);
		}
	}
	
	# get player min and max betting
	private function getLimits($request){
		$action 	= $request['action'];
		$userId 	= $request['userid'];
		$licenseEid = $request['licenseeid'];

		# check liscense is correc
		if (!isset($licenseEid) || empty($licenseEid) || $licenseEid != $this->licenseId) {
			return $this->returnError('License ID Unknown!');			
		}

		$isPlayerExist = $this->_api->getAvailPlayerId($userId);
		if (empty($isPlayerExist)) {
			return $this->returnError('Player ID Unknown!');
		}

		return array(
			"error" => false, 
			"minBet" => $this->convertSbeAmountToGameAmount($this->betLimits['minimum_bet']),
			"maxBet" => $this->convertSbeAmountToGameAmount($this->betLimits['maximum_bet']),
		);
	}

	# this is called when player bet to game
	private function bet($request) {
		$gameId 	= $request['gameid'];
		$betAmount 	= $this->convertGameAmountToSbeAmount($request['amount']);
		$rate 		= $request['rate'];
		$transId 	= $request['transid'];
		$action 	= $request['action'];
		$bets 		= json_encode($request['bets']);
		$playerId 	= $request['userid'];
		$licenseEid = $request['licenseeid'];
		$gbpAmount 	= $this->convertGameAmountToSbeAmount($request['gbpamount']);
		$roundId 	= $request['roundid'];
		$round_key 	= $roundId . "-" . $playerId;
		$transTime 	= $this->utils->getNowForMysql();
		$md5_sum 	= md5($transId.$roundId.$playerId.$request['amount'].$transTime);

		$this->utils->debug_log('>>>>>>>>>>>> live_solution bet playerId', $playerId);

		# check if player id exist
		$username = $this->player_model->getUsernameById($playerId);
		if (empty($username)) {
			return $this->returnError('Player ID Unknown!');
		}

		$this->utils->debug_log('>>>>>>>>>>>> live_solution bet username', $username);

		# check if record already exist
		$gameRecord = $this->ls_casino_game_logs->checkGameRecordIsExist($transId, $roundId, $playerId, $action);
		if (!empty($gameRecord)) {			
			return $this->returnError('Transaction already exist', array(
				"action" => $action,
				"trans_id" => $transId,
				"round_id" => $roundId,
				"user_id" => $playerId,
			));
		}

		# check player balance if can bet
		$currentBalance = $this->_api->queryPlayerBalance($username)['balance'];
		if ($betAmount > $currentBalance) {
			return $this->returnError('Insufficient Funds');
		}

		$this->utils->debug_log('>>>>>>>>>>>> live_solution bet currentBalance', $currentBalance);

		# if can bet, subract amount to subwallet current balance
		$isAmountSubtracted = $this->subtract_amount($playerId, $betAmount);
		
		# send response if subtract amount fail
		if (!$isAmountSubtracted) {
			return $this->returnError('Sorry, System is busy, please try again!');			
		}
		# get current balance
		$afterBalance = $this->_api->queryPlayerBalance($username)['balance'];
		# save response
		$response = array(
			"error" => false, 
			"message" => "Accepted",
			"balance" => $this->convertSbeAmountToGameAmount($afterBalance),
			"transid" => $transId,
			'saveResponse' => false,
		);

		$responseId = $this->saveResponseResult(!$response["error"], $action, $request, $response);
		# dump logs if amount successfully deducted
		$transactionData = array(
			'round_key'					=> $round_key,
			'trans_id'					=> $transId,
			'round_id'					=> $roundId,
			'game_id'					=> $gameId,
			'amount'					=> $betAmount,
			'gbpamount'					=> $gbpAmount,
			'rate'						=> $rate,
			'action'					=> $action,
			'bets'						=> json_encode($bets),
			'user_id'					=> $playerId,
			'license_eid'				=> $licenseEid,
			'player_id'					=> $playerId,
			'response_result_id'		=> $responseId,
			'external_uniqueid'			=> $round_key,
			'created_at'				=> $this->utils->getNowForMysql(),
			'updated_at'				=> null,
			'md5_sum'					=> $md5_sum,
			'after_balance'				=> $afterBalance,
		);

		# insert game to original logs
		$this->ls_casino_game_logs->insertGameLogs($transactionData);
		$transactionData['bet_list'] = $transactionData['bets'];
		# sync merge to
		$this->syncMergeToGameLogsInstantly($transactionData);

		return $response;
	}

	# this is called when player win from game
	private function win($request) {
		$gameId 	= $request['gameid'];
		$betAmount 	= $this->convertGameAmountToSbeAmount($request['amount']);
		$rate 		= $request['rate'];
		$transId 	= $request['transid'];
		$action 	= $request['action'];
		$bets 		= json_encode($request['bets']);
		$playerId 	= $request['userid'];
		$licenseEid = $request['licenseeid'];
		$gbpAmount 	= $this->convertGameAmountToSbeAmount($request['gbpamount']);
		$roundId 	= $request['roundid'];
		$round_key 	= $roundId . "-" . $playerId;
		$transTime 	= $this->utils->getNowForMysql();
		$md5_sum 	= md5($transId.$roundId.$playerId.$request['amount'].$transTime);

		# check if player id exist
		$username = $this->player_model->getUsernameById($playerId);
		if (empty($username)) {
			return $this->returnError('Player ID Unknown!');
		}

		# check if record already exist
		$gameRecord = $this->ls_casino_game_logs->checkGameRecordIsExist($transId, $roundId, $playerId, $action);
		if (!empty($gameRecord)) {
			return $this->returnError('Transaction already exist', array(
				"action" => $action,
				"trans_id" => (int) $transId,
				"round_id" => (int) $roundId,
				"user_id" => (int) $playerId,
			));
		}

		# check debit transaction logs if exist
		$debitData = $this->ls_casino_game_logs->getDebitRoundKey($round_key);
		if (empty($debitData)) {
			return $this->returnError('Cannot find debit transaction logs for this credit transaction!', array(
				"action" => $action,
				"trans_id" => (int) $transId,
				"round_id" => (int) $roundId,
				"user_id" => (int) $playerId,
			));
		}

		# if can bet, subract amount to subwallet current balance
		$isAmountAdded = $this->add_amount($playerId, $betAmount);
		
		# send response if subtract amount fail
		if (!$isAmountAdded) {
			return $this->returnError('Sorry, System is busy, please try again!');
		}

		$afterBalance = $this->_api->queryPlayerBalance($username)['balance'];
		# save response
		$response =  array(
			"error" => false, 
			"message" => "Accepted",
			"balance" => $this->convertSbeAmountToGameAmount($afterBalance),
			"transid" => (int) $transId,
			'saveResponse' => false,
		);
		$responseId = $this->saveResponseResult(!$response["error"], $action, $request, $response);
		# dump logs if amount successfully deducted	
		$transactionData = array(
			'round_key'					=> $round_key,
			'trans_id'					=> $transId,
			'round_id'					=> $roundId,
			'game_id'					=> $gameId,
			'amount'					=> $betAmount,
			'gbpamount'					=> $gbpAmount,
			'rate'						=> $rate,
			'action'					=> $action,
			'bets'						=> json_encode($bets),
			'user_id'					=> $playerId,
			'license_eid'				=> $licenseEid,
			'player_id'					=> $playerId,
			'response_result_id'		=> $responseId,
			'external_uniqueid'			=> $round_key,
			'created_at'				=> $this->utils->getNowForMysql(),
			'updated_at'				=> null,
			'md5_sum'					=> $md5_sum,
			'after_balance'				=> $afterBalance,
		);

		# insert game to original logs
		$this->ls_casino_game_logs->insertGameLogs($transactionData);		
		# set bet amount based on debit transaction logs
		$transactionData['bet_amount'] = $debitData['amount'];
		$transactionData['bet_list'] = $debitData['bets'];
		# sync merge to game logs
		$this->syncMergeToGameLogsInstantly($transactionData);

		return $response;
	}

	private function syncMergeToGameLogsInstantly($data) {
		$gameDetails 		= $this->CI->game_description_model->getGameDescriptionByGamePlatformIdAndGameCode($this->_api->getPlatformCode(), $data['game_id'])[0];
		$unknownGame 		= $this->_api->getUnknownGame($this->_api->getPlatformCode());
		$username 	 		= $this->_api->getUsernameById($data['player_id']);		
		$bet_amount 		= 0;
		$result_amount 		= 0;
		$external_uniqueid 	= $data['external_uniqueid'];
		$bet_date 			= $data['created_at'];		

		$bet_list = json_decode(json_decode($data['bet_list']), true);
		$betPlaced = $this->generateBetPlaced($bet_list);
		
		$betDetails =  array(
            "bet_details" => array(
                $data['trans_id'] => array(
                    "Bets"=> $betPlaced,
                ),
            ),
        );

		$gamelogsData = array(
			"username" 				=> !empty($username ) ? $username  : null,
			"player_id" 			=> !empty($data['player_id'] ) ? $data['player_id']  	: null,
			"game_code" 			=> (!empty($gameDetails)) ? $gameDetails->game_code		: $unknownGame->game_code,
			"game_description_id" 	=> (!empty($gameDetails)) ? $gameDetails->game_description_id : $unknownGame->id,
			"game_type_id" 			=> (!empty($gameDetails)) ? $gameDetails->game_type_id 	: $unknownGame->game_type_id,			
			"game" 					=> (!empty($gameDetails)) ?	$gameDetails->game_name 	: $unknownGame->game_name,
			"game_type" 			=> (!empty($gameDetails)) ? $gameDetails->game_type 	: $unknownGame->game_type,
			"after_balance" 		=> !empty($data['after_balance'] ) ? $data['after_balance'] : null,
		);

		# if player bet, set game logs as loss
		if (strtolower($data['action']) == 'debituser') {
			$gamelogsData['bet_amount'] 	= !empty($data['amount']) ? $data['amount'] : 0;
			$gamelogsData['result_amount'] 	= !empty($data['amount']) ? -$data['amount'] : 0;
		}

		# if player win, update the game logs as win
		if (strtolower($data['action']) == 'credituser') {
			$gamelogsData['bet_amount'] 	= $data['bet_amount'];
			$gamelogsData['result_amount'] 	= !empty($data['amount']) ? ($data['amount'] - $data['bet_amount']) : 0;

			$bets = json_decode(json_decode($data['bets']), true);
			$wonSide = $this->generateBetPlaced($bets);

			# add winning bets on bet details
			if (!empty($wonSide)) {
				$betDetails['bet_details'][$data['trans_id']]['Wins'] = $wonSide;
			}
		}
	
		$extra = array(
			'trans_amount'	=> 	$gamelogsData['bet_amount'],
			'status'		=> 	Game_logs::STATUS_SETTLED,
			'bet_details'  => json_encode($betDetails),
			'table'			=> 	$data['round_id'],
		);

		$this->_api->syncGameLogs(
			$gamelogsData['game_type_id'],
			$gamelogsData['game_description_id'],
			$gamelogsData['game_code'],
			$gamelogsData['game_type'],
			$gamelogsData['game'],
			$gamelogsData['player_id'],
			$gamelogsData['username'],
			$gamelogsData['bet_amount'],
			$gamelogsData['result_amount'],
			null, # win_amount
			null, # loss_amount
			$gamelogsData['after_balance'], # after_balance
			0, # has_both_side
			$external_uniqueid,
			$bet_date, //start
			$bet_date, //end
			null,
			Game_logs::FLAG_GAME,
			$extra
		);
	}

	private function presenterFanClub($request) {
		return array(
				"error" => false,
				"message" => "Presenter Noted",
			);
	}

	private function subtract_amount($player_id, $amount) {
		if (empty($amount)) {
			return true;
		}

		$game_platform_id = $this->_api->getPlatformCode();
		$success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
			$success = $this->wallet_model->decSubWallet($player_id, $game_platform_id, $amount);
			$this->utils->debug_log('live_solution subtract_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
			return $success;
		});

		return $success;
	}

	private function add_amount($player_id, $amount) {
		if (empty($amount)) {
			return true;
		}

		$game_platform_id = $this->_api->getPlatformCode();

		$success = $this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use ($game_platform_id, $player_id, $amount) {
			$success = $this->wallet_model->incSubWallet($player_id, $game_platform_id, $amount);
			$this->utils->debug_log('live_solution add_amount', 'player_id', $player_id, 'amount', $amount, 'success', $success);
			return $success;
		});

		return $success;
	}


    private function returnError($errorMessage, $extra = null) {
		$response = array(
			"error" => true, 
			"message" => $errorMessage,			
		);

		if (!empty($extra)) {
			$response = array_merge($response, $extra);
		}

		return $response;
	}

	private function saveResponseResult($success, $callMethod, $params, $response){
        //save to db
        $this->CI->load->model("response_result");
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResult($this->gamePlatformId, $flag, $callMethod, json_encode($params), $response, 200, null, null);
    }

    public function hashSHA256($string) {
		return hash('SHA256', $string);
	}

	private function convertGameAmountToSbeAmount($amount) {
		return $amount / 100;
	}

	private function convertSbeAmountToGameAmount($amount) {
		return (int)($amount * 100);
	}

	private function generateBetPlaced($betList) {
		$tmpBetPlace = array();

		# merge same bet place then add amount
		if (!empty($betList)) {
			foreach ($betList as $key => $value) {
				if (!isset($tmpBetPlace[$value['betName']])) {
					$tmpBetPlace[$value['betName']] = $this->convertGameAmountToSbeAmount($value['amount']);
				} else {
					$tmpBetPlace[$value['betName']] += $this->convertGameAmountToSbeAmount($value['amount']);
				}
			}

			return http_build_query($tmpBetPlace,null,', ');
		}

		return false;
	}
}

///END OF FILE////////////