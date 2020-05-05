<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_ggpoker extends Abstract_game_api {

	const URI_MAP = array(
		self::API_createPlayer		 => '/create',
		self::API_changePassword	 => '/create',
		self::API_queryPlayerBalance => '/balance',
		self::API_isPlayerExist	     => '/balance',
		self::API_depositToGame		 => '/credit',
		self::API_withdrawFromGame   => '/debit',
		self::API_queryTransaction 	 => '/check',
		self::API_syncGameRecords    => '/advanced-ggr',
		// self::API_syncGameRecords    => '/ggr',
	);

	public function __construct() {
		parent::__construct();
		$this->CI->load->model('daily_currency');
		$this->api_url = $this->getSystemInfo('url');
		$this->siteId = $this->getSystemInfo('siteId');
		$this->secretKey = $this->getSystemInfo('key');
		$this->downlineId = $this->getSystemInfo('downlineId');
		$this->download_url = $this->getSystemInfo('download_url');
		$this->currency_rate_cny_usd = $this->getSystemInfo('currency_rate_cny_usd');
		$this->is_fix_rate_cny_usd = $this->getSystemInfo('is_fix_rate_cny_usd',false);//check if use fix rate.
	}

	public function getPlatformCode() {
		return GGPOKER_GAME_API;
	}

	public function generateUrl($apiName, $params) {
		$url = $this->api_url.$params["method"];
		unset($params["method"]);
		$url .= '?' . http_build_query($params);
		return $url;
	}

	protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
		return $errCode || intval($statusCode, 10) >= 503;
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		//$success = false;
        $success = array_key_exists("code",$resultArr)&&($resultArr['code'] == "SUCCESS") ? true : false;
        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('GGPOKER got error ======================================>', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
            $success = false;
        }
        return $success;
	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'playerId' => $playerId
        );
		$fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.$password.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"password" 		=> $password,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_createPlayer]
        );
        $this->utils->debug_log("CreatePlayer params ============================>", $params);
	    return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForCreatePlayer ==========================>', $resultJsonArr);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

		//update register
		if ($success) {
			$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}

		return array($success, $resultJsonArr);
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForChangePassword',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'newPassword' => $newPassword,
            'playerId' => $playerId
        );
		$fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.$newPassword.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"password" 		=> $newPassword,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_changePassword]
        );
        $this->utils->debug_log("ChangePassword params ============================>", $params);
	    return $this->callApi(self::API_changePassword, $params, $context);
	}

	public function processResultForChangePassword($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForChangePassword ==========================>', $resultJsonArr);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		if($success){
			$playerId = $this->getVariableFromContext($params, 'playerId');
			$result["newPassword"] = $this->getVariableFromContext($params, 'newPassword');
			if ($playerId) {
				//sync password to game_provider_auth
				$this->updatePasswordForPlayer($playerId, $result["newPassword"]);
			} else {
				$this->CI->utils->debug_log('cannot find player', $playerName);
			}
		}
		return array($success, $resultJsonArr);
	}

	public function isPlayerExist($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId   = $this->getPlayerIdInGameProviderAuth($gameUsername);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId
        );
        $fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_isPlayerExist]
        );
        $this->utils->debug_log("isPlayerExist params ============================>", $params);
	    return $this->callApi(self::API_isPlayerExist, $params, $context);
	}

	public function processResultForIsPlayerExist($params) {
		$playerId         = $this->getVariableFromContext($params, 'playerId');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr    = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForIsPlayerExist ==========================>', $resultJsonArr);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		if($success){
			$result['exists'] = true;
            //update flag to registered = true
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}
		elseif($resultJsonArr['code'] == "USER_NOT_FOUND"){
			$success = true;
			$result['exists'] = false;
		}
		else{
			$success = false;
			$result['exists'] = false;
		}
		return array($success, $result);
	}

	public function queryPlayerBalance($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId   = $this->getPlayerIdInGameProviderAuth($gameUsername);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId
        );
        $fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_queryPlayerBalance]
        );
        $this->utils->debug_log("queryPlayerBalance params ============================>", $params);
	    return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$playerId         = $this->getVariableFromContext($params, 'playerId');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr    = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForQueryPlayerBalance ==========================>', $resultJsonArr);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		$result =[];
		if($success) {
			$amount = $this->gameAmountToDb($resultJsonArr['amount']);
        	$result['balance'] = @floatval($amount);
        	if ($playerId) {
				$this->CI->utils->debug_log('GGPOKER GAME API query balance playerId', $playerId, 'playerName', $playerName, 'balance', $amount);
			} else {
				$this->CI->utils->debug_log('GGPOKER GAME API cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}
        }

		return array($success, $result);
	}
	# Get current rate for CNY to USD
	public function getCurrencyRateCnyUsd(){
		$result = $this->CI->daily_currency->getCurrentCurrencyRate($this->utils->getTodayForMysql(),'CNY','USD');
		if(!empty($result) && (!$this->is_fix_rate_cny_usd)){
			return $result->rate;
		}
		return $this->currency_rate_cny_usd;
	}

	public function gameAmountToDb($amount) {
	    $conversion_rate = $this->getCurrencyRateCnyUsd();
	    $converted_amount = (float) ($amount/$conversion_rate);
	    return round($converted_amount,2);
	}

	public function dBtoGameAmount($amount) {
	    $conversion_rate = $this->getCurrencyRateCnyUsd();
	    $converted_amount = (float) ($amount*$conversion_rate);
	    return round($converted_amount,2);
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId   = $this->getPlayerIdInGameProviderAuth($gameUsername);
		$requestId = substr(date('YmdHis'), 2) . random_string('alnum', 10);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId,
            'amount' => @floatval($amount),
            'external_transaction_id' => $requestId,
        );
        $query_transaction_params = array(
        	'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId,
            'amount' => @floatval($amount),
            'requestId' => $requestId,
        );
        $amount = $this->dBtoGameAmount($amount);
        $fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.@floatval($amount).$requestId.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"amount"		=> @floatval($amount),
			"requestId"		=> $requestId,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_depositToGame]
        );
        $this->utils->debug_log("depositToGame params ============================>", $params);
        $result = $this->callApi(self::API_depositToGame, $params, $context);
        $result['query_transaction_params'] = $query_transaction_params;
        return $result;
	}
	
	public function processResultForDepositToGame($params) {
   		$playerId         = $this->getVariableFromContext($params, 'playerId');
   		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
   		$playerName = $this->getVariableFromContext($params, 'playerName');
   		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $amount = $this->getVariableFromContext($params, 'amount');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr    = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		$result = array(
			'response_result_id' 		=> $responseResultId,
			'external_transaction_id'	=> $external_transaction_id,
			'transfer_status'			=> ($success) ? self::COMMON_TRANSACTION_STATUS_APPROVED : self::COMMON_TRANSACTION_STATUS_DECLINED,
			'reason_id' 				=> $this->getReasonId($resultJsonArr),
			);

		$this->utils->debug_log("check Deposit params ============================>", $params);
		if ($success) {
			$result['didnot_insert_game_logs']=true;
		} else {
			$result["reason_id"] = $this->getReasonId($resultJsonArr);
		}
		return array($success, $result);
    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null,$notRecordTransaction=false) {
    	$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId   = $this->getPlayerIdInGameProviderAuth($gameUsername);
		$requestId = substr(date('YmdHis'), 2) . random_string('alnum', 10);
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId,
            'amount' => @floatval($amount),
            'external_transaction_id' => $requestId,
        );
        $query_transaction_params = array(
        	'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId,
            'amount' => @floatval($amount),
            'requestId' => $requestId,
        );
        $amount = $this->dBtoGameAmount($amount);
        $fingerprint = MD5($this->siteId.$this->downlineId.$gameUsername.@floatval($amount).$requestId.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"username" 		=> $gameUsername,
			"amount"		=> @floatval($amount),
			"requestId"		=> $requestId,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_withdrawFromGame]
        );
        $this->utils->debug_log("withdrawFromGame params ============================>", $params);
        $result = $this->callApi(self::API_withdrawFromGame, $params, $context);
        $result['query_transaction_params'] = $query_transaction_params;
        return $result;
    }

    public function processResultForWithdrawFromGame($params) {
      	$playerId         = $this->getVariableFromContext($params, 'playerId');
   		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
   		$playerName = $this->getVariableFromContext($params, 'playerName');
   		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $amount = $this->getVariableFromContext($params, 'amount');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr    = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		$result = array(
			'response_result_id' 		=> $responseResultId,
			'external_transaction_id'	=> $external_transaction_id,
			'transfer_status'			=> ($success) ? self::COMMON_TRANSACTION_STATUS_APPROVED : self::COMMON_TRANSACTION_STATUS_DECLINED,
			'reason_id' 				=> $this->getReasonId($resultJsonArr),
			);

		$this->utils->debug_log("check Witdraw params ============================>", $params);
		if ($success) {
			$result['didnot_insert_game_logs']=true;
		} else {
			$result["reason_id"] = $this->getReasonId($resultJsonArr);
		}
		return array($success, $result);
    }

    public function getReasonId($params){
    	$result = $params['code'];
    	switch ($result) {
		    case "USER_NOT_FOUND":
		        return self::REASON_NOT_FOUND_PLAYER;
		        break;
		    case "INSUFFICIENT_BALANCE":
		        return self::REASON_NO_ENOUGH_BALANCE;
		        break;
		    case "INVALID_REQUEST":
		    case "SITE_NOT_FOUND":
		    case "DOWNLINE_NOT_FOUND":
		        return self::REASON_INVALID_KEY;
		        break;
		    case "TRANSACTION_NOT_FOUND":
		        return self::REASON_INVALID_TRANSACTION_ID;
		        break;
		    case "TRANSACTION_ALREADY_EXISTS":
		        return self::REASON_DUPLICATE_TRANSFER;
		        break;
		    default:
		        return self::REASON_UNKNOWN ;
		}
    }

	public function queryForwardGame($playerName, $extra = null) {
		$language = $this->getLauncherLanguage($extra['language']);
		$params = array(
			"lang" 		=> $language,
			"btag1" 	=> $this->siteId,
			"btag2"		=> $this->downlineId,
		);
		$url_params = http_build_query($params);
		$download_url = $this->download_url."?".$url_params;

		$data = [
            'url' => $download_url,
            'success' => true
        ];
        $this->utils->debug_log(' GGPOKER download_url - =================================================> ' . $download_url);
        return $data;
	}

	public function syncOriginalGameLogs($token = false) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
		$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));

		$startDate = $startDate->format('Y-m-d 00:00:00');
		$endDate   = $endDate->format('Y-m-d 23:59:59');
		$count = 0;
		$this->CI->utils->loopDateTimeStartEnd($startDate, $endDate, '+24 hours', function($startDate, $endDate) {
			$includeDownline = true;
			$startDate = $startDate->format('Y-m-d');
			$endDate = $startDate;
			$context = array(
	            'callback_obj' => $this,
	            'callback_method' => 'processResultForSyncGameRecords',
	            'startDate' => $startDate,
		        'endDate' => $endDate,
	        );
	        $text = $this->siteId.$startDate.$endDate.$this->secretKey;
	        $fingerprint = MD5($this->siteId.$this->downlineId.$includeDownline.$startDate.$endDate.$this->secretKey);
	        $params = array(
	            "siteId" 		=> $this->siteId,
	            "downlineId" 	=> $this->downlineId,
	            "includeDownline" => $includeDownline,
				"fromDate" 		=> $startDate,
				"toDate"		=> $endDate,
				"fingerprint" 	=> $fingerprint,
				"method" 		=> self::URI_MAP[self::API_syncGameRecords]
	        );
			$this->utils->debug_log("syncOriginalGameLogs params ============================>", $params);
		    return $this->callApi(self::URI_MAP[self::API_syncGameRecords], $params, $context);
		});
	}

	public function processResultForSyncGameRecords($params) {
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
		$this->CI->load->model(array('ggpoker_game_logs'));
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr    = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, Null);
		$result['data_count'] = 0;
		if($success){
			$gameRecords = $resultJsonArr['data'];
			if (!empty($gameRecords)) {
				$count = 0;
				foreach ($gameRecords as $record) {
					$insertRecord = array();
					$playerId = $this->getPlayerIdInGameProviderAuth(strtolower($record['username']));
					if(!empty($playerId)){
						$playerUsername = $this->getGameUsernameByPlayerId($playerId);
						//data
						$insertRecord['downline_id'] = isset($record['downlineId']) ? $record['downlineId'] : NULL;
						$insertRecord['game_name'] = isset($record['username']) ? $record['username'] : NULL;
						$insertRecord['net_revenue'] = isset($record['ggr']) ? $record['ggr'] : NULL;
						$insertRecord['profit_and_loss'] = isset($record['profitAndLoss']) ? $record['profitAndLoss'] : NULL;
						$insertRecord['game_date'] = $startDate;
						$insertRecord['converted_profit_and_loss'] = $this->gameAmountToDb($record['profitAndLoss']);
						$insertRecord['rake_or_fee'] = isset($record['rakeOrFee']) ? $record['rakeOrFee'] : NULL;
						$insertRecord['profit_and_loss_poker'] = isset($record['profitAndLossPoker']) ? $record['profitAndLossPoker'] : NULL;
						$insertRecord['profit_and_loss_side_game'] = isset($record['profitAndLossSideGame']) ? $record['profitAndLossSideGame'] : NULL;
						$insertRecord['fish_buffet_reward'] = isset($record['fishBuffetReward']) ? $record['fishBuffetReward'] : NULL;
						$insertRecord['network_give_away'] = isset($record['networkGiveaway']) ? $record['networkGiveaway'] : NULL;
						$insertRecord['network_paid'] = isset($record['networkPaid']) ? $record['networkPaid'] : NULL;
						$insertRecord['brand_promotion'] = isset($record['brandPromotion']) ? $record['brandPromotion'] : NULL;
						$insertRecord['tournament_over_lay'] = isset($record['tournamentOverlay']) ? $record['tournamentOverlay'] : NULL;
						//converted data
						$insertRecord['converted_rake_or_fee'] = $this->gameAmountToDb($record['rakeOrFee']);
						$insertRecord['converted_profit_and_loss_poker'] = $this->gameAmountToDb($record['profitAndLossPoker']);
						$insertRecord['converted_profit_and_loss_side_game'] = $this->gameAmountToDb($record['profitAndLossSideGame']);
						$insertRecord['converted_fish_buffet_reward'] = $this->gameAmountToDb($record['fishBuffetReward']);
						$insertRecord['converted_network_give_away'] = $this->gameAmountToDb($record['networkGiveaway']);
						$insertRecord['converted_network_paid'] = $this->gameAmountToDb($record['networkPaid']);
						$insertRecord['converted_brand_promotion'] = $this->gameAmountToDb($record['brandPromotion']);
						$insertRecord['converted_tournament_over_lay'] = $this->gameAmountToDb($record['tournamentOverlay']);
						//extra info from SBE
						$insertRecord['Username'] = $playerUsername;
						$insertRecord['player_id'] = $playerId;
						$insertRecord['external_uniqueid'] = $playerId.'-'.$startDate.'to'.$endDate; //add external_uniueid for og purposes
						$insertRecord['response_result_id'] = $responseResultId;
						$isToday = $this->CI->ggpoker_game_logs->isRowIdAlreadyExists($insertRecord['external_uniqueid'],$playerId);
						if ($isToday) {
							$insertRecord['updated_at'] = date('Y-m-d H:i:s');
							$this->CI->ggpoker_game_logs->updateGameLogs($insertRecord);
						} else {
							$insertRecord['created_at'] = date('Y-m-d H:i:s');
							$this->CI->ggpoker_game_logs->insertGameLogs($insertRecord);
						}
						$count++;
					}
				}
				$result['data_count'] = $count;
			}
		}
		return array($success, $result);
	}

	public function syncMergeToGameLogs($token) {
		$this->CI->load->model(array('game_logs', 'player_model', 'ggpoker_game_logs'));
		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		// $dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d 00:00:00');
		$endDate = $dateTimeTo->format('Y-m-d 23:59:59');
		$rlt = array('success' => true);
		$result = $this->CI->ggpoker_game_logs->getGameLogStatistics($startDate, $endDate);
		$cnt = 0;
		if ($result) {
			$unknownGame = $this->getUnknownGame();
			foreach ($result as $ggpoker_data) {
				if (!$ggpoker_data['player_id']) {
					continue;
				}
				$cnt++;
				$game_description_id = $ggpoker_data['game_description_id'];
				$game_type_id = $ggpoker_data['game_type_id'];
				if (empty($game_description_id)) {
					$game_description_id = $unknownGame->id;
					$game_type_id = $unknownGame->game_type_id;
				}
				$extra = [
                    'rent'  => $ggpoker_data['converted_rake_or_fee'],
                    'updated_at'  => $ggpoker_data['updated_at'],
                    'trans_amount'  => 0,
                ];
				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$ggpoker_data['game_code'],
					$ggpoker_data['game_type'],
					$ggpoker_data['game'],
					$ggpoker_data['player_id'],
					$ggpoker_data['username'],
					0,//$ggpoker_data['BetAmount'],
					$ggpoker_data['result_amount'],
					null, # win_amount
					null, # loss_amount
					null,//$ggpoker_data['after_balance'], # after_balance
					0, # has_both_side
					$ggpoker_data['external_uniqueid'],
					$ggpoker_data['game_date'], //start
					$ggpoker_data['game_date'], //end
					$ggpoker_data['response_result_id'],
					Game_logs::FLAG_GAME,
					$extra
				);

			}
		}
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
	}

	public function unblockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->unblockUsernameInDB($playerName);
		return array("success" => true);
	}


	public function login($username, $password = null) {
		return $this->returnUnimplemented();
	}

	public function processResultForgetVendorId($params) {
		return $this->returnUnimplemented();
	}


	public function queryTransaction($transactionId, $extra) {
		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'external_transaction_id' => $transactionId
        );
        $text = $this->siteId.$transactionId.$this->secretKey;
        $fingerprint = MD5($this->siteId.$this->downlineId.$transactionId.$this->secretKey);
        $params = array(
            "siteId" 		=> $this->siteId,
            "downlineId" 	=> $this->downlineId,
			"requestId" => $transactionId,
			"fingerprint" 	=> $fingerprint,
			"method" 		=> self::URI_MAP[self::API_queryTransaction]
        );
        $this->utils->debug_log("queryTransaction params ============================>", $params);
	    return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForQueryTransaction ==========================>', $resultJsonArr);
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, null);
		$result = array(
            'response_result_id' 		=> $responseResultId,
            'external_transaction_id'	=>$external_transaction_id,
            'status'					=> ($success) ? self::COMMON_TRANSACTION_STATUS_APPROVED : self::COMMON_TRANSACTION_STATUS_DECLINED,
            'reason_id'					=>self::REASON_UNKNOWN
        );
		return array($success, $result);
	}

	public function getLauncherLanguage($currentLang) {
		switch ($currentLang) {
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
            case "zh-cn":
                $language = 'zh-cn';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
            case "id-id":
                $language = 'id';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
            case "vi-vn":
                $language = 'vi';
                break;
            case "en-us":
                $language = 'en';
                break;
            default:
                $language = 'en';
                break;
        }
        return $language;
	}

	public function syncPlayerAccount($username, $password, $playerId) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function logout($playerName, $password = null) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
		// return array("success" => true);
	}

	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		return $this->returnUnimplemented();
	}

	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		return $this->returnUnimplemented();
	}

	public function checkLoginStatus($playerName) {
		return $this->returnUnimplemented();
	}

	public function checkLoginToken($playerName, $token) {
		return $this->returnUnimplemented();
	}

	public function totalBettingAmount($playerName, $dateTimeFrom, $dateTimeTo) {
		return $this->returnUnimplemented();
	}


}

/*end of file*/