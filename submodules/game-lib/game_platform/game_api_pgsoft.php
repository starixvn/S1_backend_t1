<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
	* API DOCS NAME: PG Soft Integration Document Transfer mode
	* Document Number: V2.1.0


	*
	* @category Game_platform
	* @version 1.8.10
	* @copyright 2013-2018 TripleOneTech
**/

class game_api_pgsoft extends Abstract_game_api {

	const POST = "POST";
	const GET = "GET";
	const PUT = "PUT";
	const TOURNAMENT_CODE = 3;
	const API_joinTournament = '/Tournament/v1/CreateTournamentPlayer';
	const API_syncGameRecords2 = 'syncGameRecords2';

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+5 minutes');
		$this->currency = $this->getSystemInfo('currency');
		$this->secret_key = $this->getSystemInfo('secret_key');
		$this->operator_token = $this->getSystemInfo('operator_token');
		$this->game_url = $this->getSystemInfo('game_url');
		$this->lobby_url = $this->getSystemInfo('lobby_url');
		$this->max_call_attempt = $this->getSystemInfo('max_call_attempt', 10);
		$this->max_data_set = $this->getSystemInfo('max_data_set', 500);//default from og game is 300
		$this->forward_sites = $this->getSystemInfo('forward_sites',null);
		$this->token_prefix = $this->getSystemInfo('token_prefix', '');
		$this->tournament_url = $this->getSystemInfo('tournament_url', 'https://public.pg-redirect.us/tournament/index.html');//staging tournament url
		$this->tournament_registration_url = $this->getSystemInfo('tournament_registration_url', "https://api.int.pg-bo.net"); // staging tournament registration url

		$this->round_transfer_amount =$this->getSystemInfo('round_transfer_amount', true); // use default round to 2 decimal (api rules)
		$this->sync_by_time_range =$this->getSystemInfo('sync_by_time_range', false);

		$this->method = self::POST; # default as POST
		$this->URI_MAP = array(
			self::API_createPlayer => '/v1/player/create',
			self::API_queryPlayerBalance => '/v3/Cash/GetPlayerWallet',
			self::API_isPlayerExist => '/v1/Player/Check',
			self::API_blockPlayer => '/v1/Player/Suspend',
			self::API_unblockPlayer => '/v1/Player/Reinstate',
			self::API_depositToGame => '/v3/Cash/TransferIn',
			self::API_withdrawFromGame => '/v3/Cash/TransferOut',
			self::API_queryTransaction => '/v3/Cash/GetSingleTransaction',
			self::API_logout => '/v1/Player/Kick',
			self::API_syncGameRecords => '/v2/Bet/GetHistory',
			self::API_joinTournament => '/external/Tournament/v2/CreateTournamentPlayersAsync',
			self::API_syncGameRecords2 => '/Bet/v2/GetHistoryForSpecificTimeRange'
		);
	}

	const ORIGINAL_LOGS_TABLE_NAME = 'pgsoft_game_logs';
	const MD5_FIELDS_FOR_ORIGINAL=[
		"gameid",
		"betamount",
		"winamount",
		"jackpotrtpcontributionamount",
		"jackpotwinamount",
		"balancebefore",
		"balanceafter",
		"rowversion",
		"bettime"
	];
	const MD5_FLOAT_AMOUNT_FIELDS=[
		'betamount',
		'winamount',
		"jackpotrtpcontributionamount",
		"jackpotwinamount",
		"balancebefore",
		"balanceafter",
	];

	public function getPlatformCode() {
		return PGSOFT_API;
	}

	public function generateUrl($apiName, $params) {
		$apiUri = $this->URI_MAP[$apiName];
		$url = $this->api_url . $apiUri;

		if ($apiUri == self::API_joinTournament) {
			$url = $this->tournament_registration_url . $this->URI_MAP[$apiName];
		}

		return $url;
	}

	protected function customHttpCall($ch, $params) {
		if($this->method == self::POST){
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);     
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
	}

	public function forwardCallback($url, $params) {
		list($header, $resultText) = $this->httpCallApi($url, $params);
		$this->CI->utils->debug_log('forwardCallback', $url, $params, $header, $resultText);
		return $resultText;
	}

	public function callback($post_params = null, $method){
		$secret_key = @$post_params['secret_key'];
		$player_token = urldecode(@$post_params['operator_player_session']);
		$invalid_request = array(
			'data' => null,
			'error'=> [
				'code' => '1200',
				'message' => 'Invalid request.'
			]
		);

		if($secret_key != $this->secret_key){
			return $invalid_request;
		}

		if(isset($post_params["operator_token"]) && $post_params["operator_token"] != $this->operator_token){
			return $invalid_request;
		}

		# forward to site
		if ($this->forward_sites) {
			@list($prefix, $player_token) = explode('|', $player_token);
			
			if(isset($this->forward_sites[$prefix])){
				$url = $this->forward_sites[$prefix];
				$this->CI->utils->debug_log('PGSOFT_API FORWARD URL',$url);
				return $this->forwardCallback($url, $post_params);
			}
		}

		# remove prefix on token
		if(!empty($this->token_prefix)){
			@list($prefix, $player_token) = explode('|', $player_token);
		}

		$prefix = !empty($prefix) ? $prefix : null;

		$this->CI->utils->debug_log(' PLAYER TOKEN : ',$player_token,' TOKEN PREFIX : ',$prefix);
		$success = false;
		$this->CI->load->model(array('common_token', 'player_model'));
		$playerId = $this->CI->common_token->getPlayerIdByToken($player_token);

		if (!empty($playerId)) {
			$success = true;
			$playerInfo = $this->CI->player_model->getPlayerInfoById($playerId);
		}
		if ($success) {
			$gameUsername = $this->getGameUsernameByPlayerUsername($playerInfo['username']);
			$params = array(
				'data' => [
					'player_name' => $gameUsername,
					'nickname' => $gameUsername,
					'currency' => $this->getPlayerPGSoftCurrency($gameUsername),
					'reminder_time' => (time()*1000)
				],
				'error'=> null
			);
		}else{
			$params = $invalid_request;
		}

		return $params;
	}

    private function getPlayerPGSoftCurrency($username){
		# use correct currency code
		$playerId = $this->getPlayerIdInGameProviderAuth($username);
		if(!is_null($playerId)){
			$this->CI->load->model(array('player_model'));
			$currencyCode = $this->CI->player_model->getPlayerCurrencyByPlayerId($playerId);
			if(!is_null($currencyCode)){
				return $currencyCode;
			}else{
				return $this->currency;
			}
		}else{
			return $this->currency;
		}
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = false;
		if(is_null(@$resultArr['error'])){
			$success = true;
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('PGSOFT_API API got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}

		return $success;
	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'playerId' => $playerId,
			'gameUsername' => $gameUsername
		);

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername,
			"nickname" => $gameUsername,
			"currency" => $this->getPlayerPGSoftCurrency($gameUsername)
		);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$result = array(
			"player" => $gameUsername,
			"exists" => false
		);

		if($success){
			# update flag to registered = true
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
	        $result["exists"] = true;
		}

		return array($success, $result);
	}

	public function queryPlayerBalance($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance', 
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
		);

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername
		);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$result = array();
		if($success){
			$result['balance'] = @floatval($resultArr['data']['cashBalance']);
		}

		return array($success, $result);
	}

	public function isPlayerExist($playerName){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist', 
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
		);

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername
		);

		return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$result = array();

		if($success){
			$result['exists'] = true;
		}else{
			# Player not Exists
			if(isset($resultArr['error']['code'])&&$resultArr['error']['code']==1305){
				$success = true;
				$result['exists'] = false;
			}else{
				$result['exists'] = null;
			}

		}

		return array($success, $result);
    }

	public function batchQueryPlayerBalance($playerNames, $syncId = null) {
        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }


	public function blockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForBlockPlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
        );

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername
		);

		return $this->callApi(self::API_blockPlayer, $params, $context);
    }

    public function processResultForBlockPlayer($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);
		$result = array();

    	if ($success) {
			$this->blockUsernameInDB($gameUsername);
		}

		return array($success, $result);
    }

    public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForUnblockPlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
        );

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername
		);

		return $this->callApi(self::API_unblockPlayer, $params, $context);
    }

    public function processResultForUnblockPlayer($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);
		$result = array();

		if ($success) {
			$this->unblockUsernameInDB($gameUsername);
		}

		return array($success, $result);
    }

	public function logout($playerName, $password = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogout',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
        );

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername
		);

		return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);
		$result = array();

		return array($success, $result);
	}

	private function getReasons($error_code){
        switch ($error_code) {
            case 3013:
                return self::REASON_NO_ENOUGH_BALANCE;
                break;
            case 3001:
                return self::REASON_INVALID_TRANSFER_AMOUNT;
                break;
            case 1034: 
                return self::REASON_INCOMPLETE_INFORMATION;
                break;
            case 1200:
            case 1303:
            case 1035: 
            case 3040: 
                return self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                break;
            case 1204: 
                return self::REASON_INVALID_KEY;
                break;
            case 3004: 
            case 3005: 
            case 1305: 
                return self::REASON_NOT_FOUND_PLAYER;
                break;
            default:
                return self::REASON_UNKNOWN;
                break;
        }
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		if(is_null($transfer_secure_id)){
			$transfer_secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
		}

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $transfer_secure_id,
        );

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername,
			"amount" => $amount,
			"currency" => $this->getPlayerPGSoftCurrency($gameUsername),
			"transfer_reference" => $transfer_secure_id
		);

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$amount = $this->getVariableFromContext($params, 'amount');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

		if($success){			
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if($playerId){
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
            // }else{
            //     $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            //     $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
            // }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        }else{
			$error_code = @$resultArr['error']['code'];
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=$this->getReasons($error_code);
        }

        return array($success, $result);

	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		if(is_null($transfer_secure_id)){
			$transfer_secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
		}

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'amount' => $amount,
            'external_transaction_id' => $transfer_secure_id
        );

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername,
			"amount" => $amount,
			"currency" => $this->getPlayerPGSoftCurrency($gameUsername),
			"transfer_reference" => $transfer_secure_id
		);

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	public function processResultForWithdrawFromGame($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$amount = $this->getVariableFromContext($params, 'amount');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
    //         $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
    //         if ($playerId) {
	   //          $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,$this->transTypeSubWalletToMainWallet());
    //         } else {
    //             $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
    //             $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
    //         }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        }else{
			$error_code = @$resultArr['error']['code'];
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=$this->getReasons($error_code);
        }

        return array($success, $result);
	}

	public function queryTransaction($transactionId, $extra) {
        $playerName=$extra['playerName'];
        $playerId=$extra['playerId'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'playerId' => $playerId,
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'external_transaction_id' => $transactionId,
		);

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_name" => $gameUsername,
			"transfer_reference" => $transactionId
		);

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		$result = array(
			'response_result_id' => $responseResultId,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN,
			'external_transaction_id'=>$external_transaction_id
		);

		if($success){
			$result['status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
		}else{
			$error_code = @$resultArr['error']['code'];
            $result['reason_id']=$this->getReasons($error_code);
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}


	public function processResultForLogin($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $params,$playerName);
		$result = array();

		if($success){
			$result['token'] = $resultArr;
		}

		return array($success, $result);
	}	

	# Support Simplified Chinese, other languages are still under development.
	public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
        	case 1:
            case 'en-us':
                $lang = 'en'; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 'zh'; // chinese
                break;
            default:
                $lang = 'en'; // default as english
                break;
        }
        return $lang;
    }

    public function joinTournament($playerName, $extra = null) {		
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForJoinTournament',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			"secret_key" => $this->secret_key,
			"operator_token" => $this->operator_token,
			"player_names" => $gameUsername,
			"tournament_id" => $extra["tournament_id"],
			"allow_re-register" => $extra["allow_re-register"]
		);
		return $this->callApi(self::API_joinTournament, $params, $context);
	}

	public function processResultForJoinTournament($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		return array($success, $resultArr);
	}

	public function queryForwardGame($playerName, $extra = null) {
		if (isset($extra['tournament_id']) && !empty($extra['tournament_id'])) {
			$this->joinTournament($playerName, $extra);
		}

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerToken = null;

		if(in_array(strtolower($extra['game_mode']), ["real", "tournament"])){
			$playerToken = $this->getPlayerTokenByUsername($playerName);
			# add token prefix for callback forwarding 
			if(!empty($this->token_prefix)){
				$playerToken = $this->token_prefix.'|'.$playerToken;
			}
		}

		switch (@$extra['game_mode']) {
			case 'real': # Real game
				$bet_type = 1;
				break;
			case 'demo': # Trial game
			case 'fun':
				$bet_type = 2;
				break;
			case "tournament": # Tournament game
				$bet_type = 3;
				break;
			default:
				$bet_type = 1;
				break;
		}

		$params = array(
			"language" => $this->getLauncherLanguage($extra['language']),
			"bet_type" =>  $bet_type,
			"operator_token" => $this->operator_token,
			"operator_player_session" => $playerToken, 
			"from" => $extra['home_url'] 
		);
		# build game link
		$url = $this->game_url."/".$extra['game_code']."/index.html?".http_build_query($params);

		// if($extra['game_mode'] == self::TOURNAMENT_CODE){
		// 	$url = $this->tournament_url."?".http_build_query($params);
		// }
		return array("success"=>true,"url"=>$url);
	}

	public function getHistoryForSpecificTime($token){
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
		$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
		$startDate->modify($this->getDatetimeAdjust());
		//observer the date format
		$startDate = $startDate->format('Y-m-d H:i:s');
		$endDate   = $endDate->format('Y-m-d H:i:s');
		$result = array();

		$result[] = $this->CI->utils->loopDateTimeStartEnd($startDate, $endDate, '+60 minutes', function($startDate, $endDate)  {
			$startDate = strtotime($startDate->format('Y-m-d H:i:s')) * 1000;
			$endDate = strtotime($endDate->format('Y-m-d H:i:s')) * 1000;
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'sync_by_time' => true
			);

	        $params = array(
				"secret_key" => $this->secret_key,
				"operator_token" => $this->operator_token,
				"count" => $this->max_data_set,
				"bet_type" => 1,
				"from_time" => $startDate,
				"to_time" => $endDate,
				
			);
			// echo "<pre>";
			// print_r($params);exit();
			$this->CI->utils->debug_log('PGSOFT API:PARAMS: ',json_encode($params));
			return $this->callApi(self::API_syncGameRecords2, $params, $context);
	    });
	    return array(true, $result);
	}
	
	public function syncOriginalGameLogs($token = false) {
		$sync_by_time_range = $this->getValueFromSyncInfo($token, 'sync_by_time_range');
		$ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');
		if($this->sync_by_time_range || $sync_by_time_range || $ignore_public_sync){
			return $this->getHistoryForSpecificTime($token);
		}

		if ($ignore_public_sync == true) {
			$this->CI->utils->debug_log('ignore public sync');
			return array('success' => true);
		}
		$attempt = 0;
		$result = [];

		do {
			$last_sync_id = $this->getLastSyncIdFromTokenOrDB($token);
	    	$last_sync_id = !empty($last_sync_id)?$last_sync_id:0;

	    	$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'sync_by_time' => false,
				'token' => $token
			);

			$params = array(
				"secret_key" => $this->secret_key,
				"operator_token" => $this->operator_token,
				"bet_type" => 1,
				"row_version" => $last_sync_id,
				"count" => $this->max_data_set
			);
			$this->CI->utils->debug_log('PGSOFT API:PARAMS: ',json_encode($params));
			$result =  $this->callApi(self::API_syncGameRecords, $params, $context);
			
			sleep(1);
			$attempt++;
			$this->CI->utils->debug_log('PGSOFT API:syncOriginalGameLogs attempt: ',$attempt);
			$this->CI->utils->debug_log('PGSOFT API:syncOriginalGameLogs orginal data: ',$result['original_data_count']);
		} while(($attempt < $this->max_call_attempt) && ($result['original_data_count'] >= $this->max_data_set));
		if (!empty($result['last_sync_id'])) {
			return $result;
		}
	}

	private $last_rowversion=0;

	public function processGameRecords(&$gameRecords,$responseResultId) {
		$preResult = array();
		foreach ($gameRecords as $index => $row) {
			$preResult[$index]['betid'] = isset($row['betId'])?$row['betId']:null;
			$preResult[$index]['parentbetid'] = isset($row['parentBetId'])?$row['parentBetId']:null;
			$preResult[$index]['playername'] = isset($row['playerName'])?$row['playerName']:null;
			$preResult[$index]['currency'] = isset($row['currency'])?$row['currency']:null;
			$preResult[$index]['gameid'] = isset($row['gameId'])?$row['gameId']:null;
			$preResult[$index]['platform'] = isset($row['platform'])?$row['platform']:null;
			$preResult[$index]['bettype'] = isset($row['betType'])?$row['betType']:null;
			$preResult[$index]['transactiontype'] = isset($row['transactionType'])?$row['transactionType']:null;
			$preResult[$index]['betamount'] = isset($row['betAmount'])?$row['betAmount']:null;
			$preResult[$index]['winamount'] = isset($row['winAmount'])?$row['winAmount']:null;
			$preResult[$index]['jackpotrtpcontributionamount'] = isset($row['jackpotRtpContributionAmount'])?$row['jackpotRtpContributionAmount']:null;
			$preResult[$index]['jackpotwinamount'] = isset($row['jackpotWinAmount'])?$row['jackpotWinAmount']:null;
			$preResult[$index]['balancebefore'] = isset($row['balanceBefore'])?$row['balanceBefore']:null;
			$preResult[$index]['balanceafter'] = isset($row['balanceAfter'])?$row['balanceAfter']:null;
			$preResult[$index]['rowversion'] = isset($row['rowVersion'])?$row['rowVersion']:null;
			$preResult[$index]['bettime'] = isset($row['betTime'])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',($row['betTime']/1000))):null;
			
			# SBE USE
			$preResult[$index]['external_uniqueid'] = isset($row['betId'])?$row['betId']:null;
			$preResult[$index]['response_result_id'] = $responseResultId;
			$preResult[$index]['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
			$preResult[$index]['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
		}
		$gameRecords = $preResult;
	}

	private function updateOrInsertOriginalGameLogs($data, $queryType){
		$dataCount=0;
		if(!empty($data)){
			foreach ($data as $record) {
				if ($queryType == 'update') {
					$this->CI->original_game_logs_model->updateRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
				} else {
					unset($record['id']);
					$this->CI->original_game_logs_model->insertRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
				}
				$dataCount++;
				unset($record);
			}
		}
		return $dataCount;
	}

	public function processResultForSyncOriginalGameLogs($params) {
		$this->CI->load->model(array('pgsoft_game_logs','original_game_logs_model','external_system'));
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
		$token = $this->getVariableFromContext($params, 'token');
		$sync_by_time = $this->getVariableFromContext($params, 'sync_by_time');
        $resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$gameRecords = isset($resultArr['data'])?$resultArr['data']:array();
		$result = [];
		$dataCount = 0;
		$gameRecordsCount = is_array($gameRecords) ? count($gameRecords) : 0;

		$this->CI->utils->debug_log('PGSOFT gameRecords: ',$gameRecordsCount);

		$result = array('original_data_count'=> 0);
		if($success){	
			// reprocess game records base on database field in mg_game_logs
			$this->processGameRecords($gameRecords,$responseResultId);
			
			$gameRecordsKey = array_keys($gameRecords);

			$last_record = end($gameRecordsKey);
			$maxRowId = (!$sync_by_time) ? $gameRecords[$last_record]['rowversion'] : null;
			list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
				self::ORIGINAL_LOGS_TABLE_NAME,		# original table logs
				$gameRecords,						# api record (format array)
				'external_uniqueid',				# unique field in api
				'external_uniqueid',				# unique field in mg_game_logs table
				self::MD5_FIELDS_FOR_ORIGINAL,
				'md5_sum',
				'id',
				self::MD5_FLOAT_AMOUNT_FIELDS
			);

			$insertRowsCount = is_array($insertRows) ? count($insertRows) : 0;
			$updateRowsCount = is_array($updateRows) ? count($updateRows) : 0;


			$this->CI->utils->debug_log('PG game api after process available rows', 'gamerecords ->',$gameRecordsCount,
							'insertrows->',$insertRowsCount, 'updaterows->',$updateRowsCount);

			if (!empty($insertRows)) {
				$result['original_data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
			}
			unset($insertRows);

			if (!empty($updateRows)) {
				$result['original_data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
			}
			unset($updateRows);

			$sync_id = $this->getValueFromSyncInfo($token, 'manual_last_sync_id');
			if ($sync_id) {
				$result['last_sync_id'] = $maxRowId;
				$this->CI->utils->debug_log('PGSOFT MANUAL_SYNC_LAST_ID', $maxRowId);
			} else if ($maxRowId) {
				$this->CI->external_system->setLastSyncId($this->getPlatformCode(), $maxRowId);
				$lastRowId = $maxRowId;
			} 
			// echo "<pre>";
			// print_r($insertRows);exit();

			// $availableRows = $this->CI->pgsoft_game_logs->getAvailableRows($gameRecords);
			// $this->CI->utils->debug_log('PGSOFT availableRows: ',count($availableRows));
			// foreach ($availableRows as $row) {
			// 	$insertRecord = [];

			// 	$insertRecord['betid'] = isset($row['betId'])?$row['betId']:null;
			// 	$insertRecord['parentbetid'] = isset($row['parentBetId'])?$row['parentBetId']:null;
			// 	$insertRecord['playername'] = isset($row['playerName'])?$row['playerName']:null;
			// 	$insertRecord['currency'] = isset($row['currency'])?$row['currency']:null;
			// 	$insertRecord['gameid'] = isset($row['gameId'])?$row['gameId']:null;
			// 	$insertRecord['platform'] = isset($row['platform'])?$row['platform']:null;
			// 	$insertRecord['bettype'] = isset($row['betType'])?$row['betType']:null;
			// 	$insertRecord['transactiontype'] = isset($row['transactionType'])?$row['transactionType']:null;
			// 	$insertRecord['betamount'] = isset($row['betAmount'])?$row['betAmount']:null;
			// 	$insertRecord['winamount'] = isset($row['winAmount'])?$row['winAmount']:null;
			// 	$insertRecord['jackpotrtpcontributionamount'] = isset($row['jackpotRtpContributionAmount'])?$row['jackpotRtpContributionAmount']:null;
			// 	$insertRecord['jackpotwinamount'] = isset($row['jackpotWinAmount'])?$row['jackpotWinAmount']:null;
			// 	$insertRecord['balancebefore'] = isset($row['balanceBefore'])?$row['balanceBefore']:null;
			// 	$insertRecord['balanceafter'] = isset($row['balanceAfter'])?$row['balanceAfter']:null;
			// 	$insertRecord['rowversion'] = isset($row['rowVersion'])?$row['rowVersion']:null;
			// 	$insertRecord['bettime'] = isset($row['betTime'])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',($row['betTime']/1000))):null;
				
			// 	# SBE USE
			// 	$insertRecord['external_uniqueid'] = isset($row['betId'])?$row['betId']:null;
			// 	$insertRecord['response_result_id'] = $responseResultId;
			// 	$insertRecord['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
			// 	$insertRecord['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
			// 	//insert data to PGSOFT gamelogs table database
			// 	$this->CI->pgsoft_game_logs->insertGameLogs($insertRecord);

			// 	$this->last_rowversion = $insertRecord['rowversion'];

			// 	$dataCount++;
			// }

			# synced last rowversion 
			// if($this->last_rowversion!=0){
			// 	$this->CI->external_system->setLastSyncId($this->getPlatformCode(), $this->last_rowversion);
			// }

			$result['original_data_count'] = $gameRecordsCount;

		}

		return array($success, $result);
	}

	public function syncMergeToGameLogs($token) {
		$this->CI->load->model(array('game_logs', 'player_model', 'pgsoft_game_logs'));
		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$rlt = array('success' => true);

		$result = $this->CI->pgsoft_game_logs->getGameLogStatistics($startDate, $endDate);
		$cnt = 0;
		if (!empty($result)) {

			$unknownGame = $this->getUnknownGame();

			foreach ($result as $row) {
				$cnt++;

				$game_description_id = $row->game_description_id;
				$game_type_id = $row->game_type_id;

				if(empty($row->game_type_id)&&empty($row->game_description_id)){
					list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $unknownGame);
				}

	            $extra = array(
                    'table'       => $row->round_id,
                    'trans_amount'=> $row->real_bet_amount
                );

                #result amount is winloss - bet amount
	            $result_amount = ((float)$row->result_amount-(float)$row->real_bet_amount);

				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$row->gameid,
					$row->game_type,
					$row->game,
					$row->player_id,
					$row->playername,
					$row->bet_amount,
					$result_amount,
					null, # win_amount
					null, # loss_amount
					$row->after_balance,
					0, # has_both_side
					$row->external_uniqueid,
					$row->end_datetime, //start
					$row->end_datetime, //end
					$row->response_result_id,
					Game_logs::FLAG_GAME,
                    $extra
				);

			}
		}

		$this->CI->utils->debug_log('PGSOFT PLAY API =========================>', 'startDate: ', $startDate,'EndDate: ', $endDate);
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;

		$external_game_id = $row->gameid;
        $extra = array('game_code' => $external_game_id,'game_name' => $row->gameid);

        $game_type_id = isset($unknownGame->game_type_id) ? $unknownGame->game_type_id:null;
        $game_type = isset($unknownGame->game_name) ? $unknownGame->game_name:"Unknown";

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}

    public function login($playerName, $password = null, $extra = null) {
    	return $this->returnUnimplemented();
	}
	
	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		return $this->returnUnimplemented();
	}

}

/*end of file*/