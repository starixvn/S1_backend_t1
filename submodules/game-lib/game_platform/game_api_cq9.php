<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
	* API DOCS NAME: Transfer Wallet GameBoy
	* Document Number: none
	* API Doc: https://hackmd.io/s/ryWBwyI4M#Header
	*
	* @category Game_platform
	* @version 1.8.10
	* @copyright 2013-2018 TripleOneTech
**/

class game_api_cq9 extends Abstract_game_api {

	const POST = "POST";
	const GET = "GET";
	const API_getGameProviderList = "getGameProviderList";
	const API_getCq9GameList = "API_getCq9GameList";
	const API_queryLaunchLobby = "API_queryLaunchLobby";
	const PAGE_SIZE = 20000;
	const FISHING_GAME = 'fishing_game';

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->api_token = $this->getSystemInfo('api_token');
		$this->gamehall = $this->getSystemInfo('gamehall', 'cq9');

		$this->method = "POST";
		$this->URI_MAP = array(
			self::API_createPlayer => '/gameboy/player',
			self::API_login => '/gameboy/player/login',
			self::API_logout => '/gameboy/player/logout',
			self::API_queryPlayerBalance => '/gameboy/player/balance',
			self::API_isPlayerExist => '/gameboy/player/check',
			self::API_depositToGame => '/gameboy/player/deposit',
			self::API_withdrawFromGame => '/gameboy/player/withdraw',
			self::API_queryTransaction => '/gameboy/transaction/record',
			self::API_changePassword => '/gameboy/player/pwd',
			self::API_getGameProviderList => '/gameboy/game/halls',
			self::API_getCq9GameList => '/gameboy/game/list',
			self::API_queryForwardGame => '/gameboy/player/gamelink',
			self::API_syncGameRecords => '/gameboy/order/view',
			self::API_queryLaunchLobby => '/gameboy/player/lobbylink',
		);
	}

	public function getPlatformCode() {
		return CQ9_API;
	}

	protected function customHttpCall($ch, $params) {
		if($this->method == self::POST){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}
	}

	protected function getHttpHeaders($params){
		switch ($this->method) {
			case self::GET:
				$contentType = 'application/json';
				break;
			
			default:
				$contentType = 'application/x-www-form-urlencoded';
				break;
		}

		$headers = array(
			'Content-type' => $contentType,
			'Authorization' => $this->api_token
		);

		return $headers;
	}

	public function generateUrl($apiName, $params) {
		$apiUri = $this->URI_MAP[$apiName];
		$url = $this->api_url.$apiUri;
		if($this->method == self::GET){
			if($apiName == self::API_queryPlayerBalance){
				$url = $this->api_url.$apiUri."/".$params["account"];
			}
			if($apiName == self::API_isPlayerExist){
				$url = $this->api_url.$apiUri."/".$params["account"];
			}
			if($apiName == self::API_queryTransaction){
				$url = $this->api_url.$apiUri."/".$params["mtcode"];
			}
			if($apiName == self::API_getCq9GameList){
				$url = $this->api_url.$apiUri."/".$params["gamehall"];
			}
			if($apiName == self::API_syncGameRecords){
				$url = $this->api_url.$apiUri."?".http_build_query($params);
			}
		}
		return $url;
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = false;

		if(isset($resultArr['status']['code'])&&$resultArr['status']['code']==0){
			$success = true;
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('CQ9_API API got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}

		return $success;
	}
	
	public function getGameProviderList(){
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetGameProviderList'
		);

		$this->method = self::GET;

		return $this->callApi(self::API_getGameProviderList, array(), $context);
	}

	public function processResultForGetGameProviderList($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$result = [];
		if($success){
			$result["providers"] = $resultArr["data"];
		}

		return array($success, $result);
	}
	
	public function getCq9GameList($gamehall){
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetGameList'
		);

		$params = array(
			'gamehall' => $gamehall
		);

		$this->method = self::GET;

		return $this->callApi(self::API_getCq9GameList, $params, $context);
	}

	public function processResultForGetGameList($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$result = [];
		if($success){
			$result["gamelist"] = $resultArr["data"];
		}

		return array($success, $result);
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
			'account' => $gameUsername,
			'password' => $password,
			'nickname' => $gameUsername,
		);

		$this->method = self::POST;

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
			# update flag to registered = truer
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
	        $result["exists"] = true;
		}else{
			$result["message"] = @$resultArr["status"]["message"];
		}

		return array($success, $result);
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForChangePassword',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'newPassword' => $newPassword
		);

		$params = array(
			'account' => $gameUsername,
			'password' => $newPassword,
		);

		$this->method = self::POST;

		return $this->callApi(self::API_changePassword, $params, $context);

	}

	public function processResultForChangePassword($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$newPassword = $this->getVariableFromContext($params, 'newPassword');

		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		if ($success) {
			$playerId = $this->getPlayerIdInPlayer($playerName);
			//sync password to game_provider_auth
			$this->updatePasswordForPlayer($playerId, $newPassword);
		}

		return array($success, $resultArr);
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
			'account' => $gameUsername
		);

		$this->method = self::GET;

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$result = array();
		if($success){
			$result['balance'] = @floatval($resultArr['data']['balance']);
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
			'account' => $gameUsername
		);

		$this->method = self::GET;

		return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$result = array();

		if($success){
			if($resultArr["data"]){
				$result['exists'] = true;
				$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
				$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
			}else{
				$result['exists'] = false;
			}
		}else{
			$result['exists'] = null;
		}

		return array($success, $result);
    }


	public function batchQueryPlayerBalance($playerNames, $syncId = null) {
        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }

	private function getReasons($error_code){
        switch ($error_code) {
            case 1: 
            	return self::REASON_NO_ENOUGH_BALANCE;
                break;
            case 2: 
            	return self::REASON_NOT_FOUND_PLAYER;
                break;
            case 3: 
            case 4: 
            	return self::REASON_INVALID_KEY;
                break;
            case 5: 
            	return self::REASON_INCOMPLETE_INFORMATION;
                break;
            case 9: 
            	return self::REASON_DUPLICATE_TRANSFER;
                break;
            case 100: 
            	return self::REASON_FAILED_FROM_API;
                break;
            case 101: 
            	return self::REASON_IP_NOT_AUTHORIZED;
                break;
            case 105: 
            	return self::REASON_FAILED_FROM_API;
                break;
            case 200: 
            case 201: 
            	return self::REASON_LOCKED_GAME_MERCHANT;
                break;
            default:
                return self::REASON_UNKNOWN;
                break;
        }
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$eventTime = new DateTime($this->serverTimeToGameTime(date("Y-m-d\TH:i:s-04:00")));
    	$formatedEventTime = $eventTime->format("Y-m-d\TH:i:s-04:00");
   	 	$external_transaction_id = $gameUsername.date("YmdHis").uniqid();//$transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $external_transaction_id,
        );

		$params = array( 
			'account' => $gameUsername,
			'mtcode' => $external_transaction_id,
			'amount' => $amount,
			'eventTime' => $formatedEventTime
		);

		$this->method = self::POST;

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

		if ($success) {			
    //         $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
    //         if ($playerId) {
    //             $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
    //         } else {
    //             $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
    //             $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
    //         }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        }else{
			$error_code = @$resultArr['status']['code'];
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=$this->getReasons($error_code);
        }

        return array($success, $result);

	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$eventTime = new DateTime($this->serverTimeToGameTime(date("Y-m-d\TH:i:s-04:00")));
    	$formatedEventTime = $eventTime->format("Y-m-d\TH:i:s-04:00");
   	 	$external_transaction_id = $gameUsername.date("YmdHis").uniqid();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'amount' => $amount,
            'external_transaction_id' => $external_transaction_id
        );

		$params = array( 
			'account' => $gameUsername,
			'mtcode' => $external_transaction_id,
			'amount' => $amount,
			'eventTime' => $formatedEventTime
		);


		$this->method = self::POST;

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
			$error_code = @$resultArr['status']['code'];
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
			'mtcode' => $transactionId
		);

		$this->method = self::GET;

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
			switch ($resultArr['data']['status']) {
				case 'success':
					$result['status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
					break;
				case 'failed':
					$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
					break;
				case 'pending':
					$result['status'] = self::COMMON_TRANSACTION_STATUS_PROCESSING;
					break;
			}
		}else{
			$error_code = @$resultArr['status']['code'];
            $result['reason_id']=$this->getReasons($error_code);
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
    }

    public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->unblockUsernameInDB($gameUsername);
		return array("success" => true);
    }

    public function login($playerName, $password = null, $extra = null) {
    	$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogin',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'password' => $password,
        );

		$params = array(
			'account' => $gameUsername,
			'password' => $password
		);

		$this->method = self::POST;

    	return $this->callApi(self::API_login, $params, $context);
	}

	public function processResultForLogin($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);
		$result = array();

		if($success){
			$result['usertoken'] = $resultArr["data"]["usertoken"];
		}

		return array($success, $result);
	}	

	public function logout($playerName, $password = null) {
    	$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogout',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName
        );

		$params = array(
			'account' => $gameUsername,
		);

		$this->method = self::POST;

    	return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		return array($success, $resultArr);
	}

	# available code: “zh-cn”, “en”
	# ※ some games also support “th” (thai)
	public function getLauncherLanguage($language){
		switch (strtolower($language)) {
            case Language_function::INT_LANG_ENGLISH:
            case "en-us":
                return "en";
                break;
            case Language_function::INT_LANG_CHINESE:
             case "zh-cn":
                return "zh-cn";
                break;

            case Language_function::INT_LANG_INDONESIAN:
            case "id-id":
                return "id";
                break;
            case Language_function::INT_LANG_VIETNAMESE:
            case "vi-vi":
                return "vi";
                break;
            case Language_function::INT_LANG_KOREAN:
            case "ko-kr":
                return "kr";
                break;
            case Language_function::INT_LANG_THAI:
            case "th-th":
                return "th";
                break;
            default:
                return "en";
                break;
        }
    }

	public function queryForwardGame($playerName, $extra = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$password = $this->getPasswordByGameUsername($gameUsername);
		$loginData = $this->login($playerName,$password);

		if($loginData["success"]){
			$context = array(
	            'callback_obj' => $this,
	            'callback_method' => 'processResultForQueryForwardGame',
	            'playerName' => $playerName,
	            'gameUsername' => $gameUsername,
	        );

			$this->method = self::POST;

			if ($extra['game_code'] === self::FISHING_GAME) {
				$params = array(
					'usertoken' => $loginData['usertoken'],
					'lang' => $this->getLauncherLanguage($extra['language']),
				);
				return $this->callApi(self::API_queryLaunchLobby, $params, $context);
			} else {
				$params = array(
					'usertoken' => $loginData['usertoken'],
					'gamehall' => $this->gamehall,
					'gamecode' => $extra['game_code'],
					'gameplat' => $extra['is_mobile']?"MOBILE":"WEB",
					'lang' => $this->getLauncherLanguage($extra['language']),
				);
				return $this->callApi(self::API_queryForwardGame, $params, $context);
			}
		}else{
			return $loginData;
		}
	}

	public function processResultForQueryForwardGame($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);
        $result = array();
        $result['url'] = '';
       	if($success){
			$result['url'] =  $resultArr['data']['url'];
       	}

        return array($success, $result);
	}

	# notes: the bet order is based on the time of game award sending; pull the data 3 minutes before the current time; recommending interval is 1-5 minutes, maximum of 60 minutes 
	public function syncOriginalGameLogs($token = false) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
    	$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
    	$startDate->modify($this->getDatetimeAdjust());

    	#timestamp format
    	$queryDateTimeStart = $startDate->format("Y-m-d\TH:i:s-04:00");
		$queryDateTimeEnd = $endDate->format("Y-m-d\TH:i:s-04:00");

    	$result = array();
    	$continueSync = true;
    	$page = 1;
    	while ($continueSync) {
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'startDate' => $queryDateTimeStart,
				'endDate' => $queryDateTimeEnd
			);

			$params = array(
				'starttime' => $queryDateTimeStart,
				'endtime' => $queryDateTimeEnd,
				'page' => $page,
				'pagesize' => self::PAGE_SIZE,
			);
			$this->method = self::GET;

			$resultArr[] = $result = $this->callApi(self::API_syncGameRecords, $params, $context);
			$continueSync = @$result['continueSync'];

			$page++;
    	}

    	return array("success"=>true, $resultArr);
	}

	public function processResultForSyncOriginalGameLogs($params) {
		$this->CI->load->model(array('cq9_game_logs'));
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
        $resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$result = [];
		$dataCount = 0;
		$result['continueSync'] = false;
		if($success){	
			$gameRecords = isset($resultArr['data']['Data'])?$resultArr['data']['Data']:array();
			$availableRows = !empty($gameRecords)?$this->CI->cq9_game_logs->getAvailableRows($gameRecords):array();
			foreach ($availableRows as $record) {
				# don't record if in progress bet
				if($record['status']!="complete"){
					continue;
				}

				$insertRecord = array();
				$insertRecord['gamehall'] = isset($record['gamehall'])?$record['gamehall']:null;
				$insertRecord['gametype'] = isset($record['gametype'])?$record['gametype']:null;
				$insertRecord['gameplat'] = isset($record['gameplat'])?$record['gameplat']:null;
				$insertRecord['gamecode'] = isset($record['gamecode'])?$record['gamecode']:null;
				$insertRecord['account'] = isset($record['account'])?$record['account']:null;
				$insertRecord['round'] = isset($record['round'])?$record['round']:null;
				$insertRecord['balance'] = isset($record['balance'])?$record['balance']:null;
				$insertRecord['win'] = isset($record['win'])?$record['win']:null;
				$insertRecord['bet'] = isset($record['bet'])?$record['bet']:null;
				$insertRecord['jackpot'] = isset($record['jackpot'])?$record['jackpot']:null;
				$insertRecord['status'] = isset($record['status'])?$record['status']:null;
				$insertRecord['endroundtime'] = isset($record['endroundtime'])?date('Y-m-d H:i:s', strtotime($record['endroundtime'])):null;
				$insertRecord['createtime'] = isset($record['createtime'])?date('Y-m-d H:i:s', strtotime($record['createtime'])):null;
				$insertRecord['bettime'] = isset($record['bettime'])?date('Y-m-d H:i:s', strtotime($record['bettime'])):null;
				$insertRecord['detail'] = isset($record['detail'])?json_encode($record['detail']):null;
				$insertRecord['gamerole'] = isset($record['gameRole'])?$record['gameRole']:null;
				$insertRecord['bankertype'] = isset($record['bankerType'])?$record['bankerType']:null;
				$insertRecord['rake'] = isset($record['rake'])?$record['rake']:null;

				//extra info from SBE
				$insertRecord['external_uniqueid'] = isset($record['gamehall']) ? $record['gamehall'].$record['round'] : NULL;
				$insertRecord['response_result_id'] = $responseResultId;
				$insertRecord['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
				$insertRecord['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');

				//insert data to rtg gamelogs table database
				$this->CI->cq9_game_logs->insertGameLogs($insertRecord);
				$dataCount++;
			}

			$result['dataCount'] = $dataCount;
			$result['continueSync'] = $resultArr['data']['TotalSize'] == self::PAGE_SIZE;
		}

		return array($success, $result);
	}

	public function syncMergeToGameLogs($token) {

		$this->CI->load->model(array('game_logs', 'player_model', 'cq9_game_logs'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$rlt = array('success' => true);

		$result = $this->CI->cq9_game_logs->getGameLogStatistics($startDate, $endDate);

		$cnt = 0;
		if (!empty($result)) {

			$unknownGame = $this->getUnknownGame();

			foreach ($result as $row) {
				$cnt++;

				$game_description_id = $row->game_description_id;
				$game_type_id = $row->game_type_id;
				$game_type = $row->game_type;

				if(empty($row->game_type_id)&&empty($row->game_description_id)){
					list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $unknownGame);
				}

				// our client entaplay TH do not have table table, so result amount will (bet win - bet) - rake
				$hasNotTableBattle = $this->CI->utils->getConfig('has_not_table_battle_for_result_amount_in_game_logs');
				if($hasNotTableBattle){
					$result_amount = ($row->win_amount-$row->bet_amount)-$row->rake;
				}else{
					if($game_type == "table"){
						$result_amount = $row->win_amount-$row->rake; // bet is not included in win, then we must not deduct bet amount into win amount
					}else{
						$result_amount = ($row->win_amount-$row->bet_amount)-$row->rake;
					}
				}

	            $extra = array(
                    'table'	=> $row->round_id,
                );
                
				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$row->game_code,
					$row->game_type,
					$row->game,
					$row->player_id,
					$row->username,
					$row->bet_amount,
					$result_amount,
					null, # win_amount
					null, # loss_amount
					$row->after_balance, # after_balance
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

		$this->CI->utils->debug_log('CQ9_API PLAY API =========================>', 'startDate: ', $startDate,'EndDate: ', $endDate);
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;

		$external_game_id = $row->game_id;
        $extra = array('game_code' => $external_game_id,'game_name' => $row->game_id);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}
	
	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

}

/*end of file*/