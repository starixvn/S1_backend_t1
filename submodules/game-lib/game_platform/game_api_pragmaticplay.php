<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_pragmaticplay extends Abstract_game_api {

	const TRANSFER_IN = 'IN';
	const TRANSFER_OUT = 'OUT';
	const API_TransferCredit ='transfer';

	const API_syncNewGameRecords = 'syncNewGameRecords';

	const FISHING_GAME = "pp3fish";

	const URI_MAP = array(
		self::API_createPlayer => '/http/CasinoGameAPI/player/account/create/',
		self::API_queryPlayerBalance => '/http/CasinoGameAPI/balance/current/',
		self::API_TransferCredit => '/http/CasinoGameAPI/balance/transfer/',
		self::API_isPlayerExist => '/http/CasinoGameAPI/balance/current/',
		self::API_queryForwardGame => '/http/CasinoGameAPI/game/start/',
		self::API_logout => '/http/CasinoGameAPI/game/session/terminate/',
		self::API_syncGameRecords => '/DataFeeds/transactions/',
		self::API_queryTransaction => '/http/CasinoGameAPI/balance/transfer/status/',
		self::API_getGameProviderGamelist => '/http/CasinoGameAPI/getCasinoGames/',
		self::API_syncNewGameRecords => '/DataFeeds/gamerounds/',
	);

    const GAME_WIN = 'W';
    const GAME_BET = 'B';

    const MD5_FIELDS_FOR_ORIGINAL =[
    	'playerID',
		'extPlayerID',
		'gameID',
		'playSessionID',
		'timestamp',
		'referenceID',
		'type',
		'amount',
		// include new sync field
		'bet',
		'win',
		'start_date',
		'end_date',
		'status',
		'type_game_round'
	];
	const MD5_FLOAT_AMOUNT_FIELDS=[
		'amount',
		// include new sync field
		'bet',
		'win'
	];

	# Fields in game_logs we want to detect changes for merge, and when pragmaticplay_game_logs.md5_sum is empty
    const MD5_FIELDS_FOR_MERGE=['game_code', 'amount', 'game_date','type','UserName'];
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=['amount'];

	public function __construct() {
        parent::__construct();
        $this->original_logs_table_name = 'pragmaticplay_game_logs';
		$this->api_url = $this->getSystemInfo('url');
		$this->currency = $this->getSystemInfo('currency');
		$this->secureLogin = $this->getSystemInfo('secureLogin');
		$this->secretKey = $this->getSystemInfo('secretKey');
		$this->lobby_url = $this->utils->getSystemUrl('m') . $this->getSystemInfo('lobby_url');
		$this->demo_url = $this->getSystemInfo('demo_url');
		$this->fishing_demo_url = $this->getSystemInfo('fishing_demo_url');
		$this->game_records_path = $this->getSystemInfo('game_records_path');
		$this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+10 minutes');
		$this->cashier_url = $this->getSystemInfo('cashier_url');
		$this->use_new_sync_game_records = $this->getSystemInfo('use_new_sync_game_records', false); // set to true if apply to all clients

		// applicable only for new sync
		$this->merge_free_spin_data = $this->getSystemInfo('merge_free_spin_data', false);
		$this->convertGamelogsToCsvFile = $this->getSystemInfo('convertGamelogsToCsvFile', true);
		$this->enabled_lobby = $this->getSystemInfo('enabled_lobby', true);
        $this->extra_info_currency_overwrite_player_currency=$this->getSystemInfo('extra_info_currency_overwrite_player_currency', false);
	}

	public function getPlatformCode() {
		return PRAGMATICPLAY_API;
	}

	public function getHttpHeaders($params){
		return array("Content-Type" => "application/x-www-form-urlencoded","Cache-Control" => "no-cache");
	}

	public function generateUrl($apiName, $params) {
		$apiUri = self::URI_MAP[$apiName];
		$req_params = http_build_query($params);
		if (!array_key_exists('timepoint', $params)) {
			$url = $this->api_url . $apiUri ;
		} else {
			$url = $this->api_url . $apiUri . "?" . $req_params;
		}
		return $url;
	}

	protected function customHttpCall($ch, $params) {
		if (!array_key_exists("timepoint", $params)) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		}
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = false;
		if(isset($resultArr['error'])&&$resultArr['error']=='0'||$resultArr['error']=='17'){
			$success = true;
		}
		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('PRAGMATICPLAY_API got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}
		return $success;
	}

	public function isPlayerExist($userName){
       	$playerName = $this->getGameUsernameByPlayerUsername($userName);
        $playerId   = $this->getPlayerIdInGameProviderAuth($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerName' => $playerName,
			'playerId' => $playerId
		);

		$params = array(
			'externalPlayerId' => empty($playerName)?$userName:$playerName,
			'secureLogin' => $this->secureLogin,
		);
		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
		$playerId = $this->getVariableFromContext($params, 'playerId');
        $responseResultId = $this->getResponseResultIdFromParams($params);
      	$resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
        if($success){
        	if ($resultArr['error']=="17") {
	        	$result = array('exists' => false); # Player not found
	        }else{
	        	$result = array('exists' => true);
	        	$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
	        }
        }else{
            $result = array('exists' => null); #api other error
	    }
        return array($success, $result);
    }

    private function getPlayerPragmaticPlayCurrency($username){
        if($this->extra_info_currency_overwrite_player_currency){
            return $this->currency;
        }

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

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);

		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'playerId' => $playerId
		);

		$params = array(
			'currency' => $this->getPlayerPragmaticPlayCurrency($playerName),
			'externalPlayerId' => $playerName,
			'secureLogin' => $this->secureLogin,
		);

		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		if ($success){
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        }
		return array($success, $resultArr);
	}

	private function round_down($number, $precision = 2){
	    $fig = (int) str_pad('1', $precision, '0');
	    return (floor($number * $fig) / $fig);
	}

	public function QueryBalanceGameAmountToDB($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount / $conversion_rate);
        return $this->round_down($value,3);
        // return $amount / $conversion_rate;
    }

	public function queryPlayerBalance($userName) {
		$playerName = $this->getGameUsernameByPlayerUsername($userName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName
		);

		$params = array(
			'externalPlayerId' => $playerName,
			'secureLogin' => $this->secureLogin,
		);

		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {

		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$result = array();
		$result['balance'] = $this->QueryBalanceGameAmountToDB($resultArr['balance']);

		$success = true;
		if($playerId = $this->getPlayerIdInGameProviderAuth($playerName)) {
			$this->CI->utils->debug_log('PRAGMATIC PLAY GAME API query balance playerId', $playerId, 'playerName', $playerName, 'balance', $result['balance']);
		}else{
			$this->CI->utils->debug_log('PRAGMATIC PLAY GAME API cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
		}
		$result['exists'] = true;
		return array($success, $result);

	}

	public function depositToGame($userName, $amount, $transfer_secure_id=null){
		$type = self::TRANSFER_IN;
		return $this->transferCredit($userName, $amount, $type, $transfer_secure_id);
	}

	public function withdrawFromGame($userName, $amount, $transfer_secure_id=null){
		$type = self::TRANSFER_OUT;
		return $this->transferCredit($userName, $amount, $type, $transfer_secure_id);
	}

	public function transferCredit($playerName, $amount,$type, $transfer_secure_id=null){

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$ext = $gameUsername.date("ymdHis");

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForTransferCredit',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'amount' => $amount,
			'type' => $type,
			'external_transaction_id' => $ext,
		);

		$params = array(
			'amount' => $type==self::TRANSFER_IN ? $this->dBtoGameAmount($amount) : '-'.$this->dBtoGameAmount($amount),  # self::TRANSFER_IN positive number self::TRANSFER_OUT negative number
			'externalPlayerId'=> $gameUsername,
			'externalTransactionId' => $ext,
			'secureLogin' => $this->secureLogin
		);

		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

		return $this->callApi(self::API_TransferCredit, $params, $context);

	}

	public function processResultForTransferCredit($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$type = $this->getVariableFromContext($params, 'type');
		$amount = $this->getVariableFromContext($params, 'amount');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);
		if ($success) {

            $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

            if ($playerId) {
                // $playerBalance = $this->queryPlayerBalance($playerName);
                // $afterBalance = 0;

				// if(isset($resultArr['balance'])) {
				// 	$resultArr['balance'] = $this->gameAmountToDB($resultArr['balance']);
				// }

                // if($type == self::TRANSFER_IN){ // Deposit
                // 	if ($playerBalance && $playerBalance['success']) {
	               //      $afterBalance = $playerBalance['balance'];
	               //  } else {
	               //      //IF GET PLAYER BALANCE FAILED
	               //      $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
	               //      $afterBalance = $rlt->totalBalanceAmount;
	               //      $this->CI->utils->debug_log('============= PRAGMATIC PLAY AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
	               //  }
	               //  // $responseResultId = $result['response_result_id'];
	               //  // Deposit
	               //  $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
	               //      $this->transTypeMainWalletToSubWallet());
                // }else{ // Withdraw
                // 	if ($playerBalance && $playerBalance['success']) {
	               //      $afterBalance = $playerBalance['balance'];
	               //      $this->CI->utils->debug_log('============= PRAGMATIC PLAY AFTER BALANCE FROM API '.$type.' ######### ', $afterBalance);
	               //  } else {
	               //      //IF GET PLAYER BALANCE FAILED
	               //      $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
	               //      $afterBalance = $rlt->totalBalanceAmount;
	               //      $this->CI->utils->debug_log('============= PRAGMATIC PLAY AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
	               //  }
	               //  // $responseResultId = $result['response_result_id'];
	               //  // Withdraw
	               //  $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
	               //      $this->transTypeSubWalletToMainWallet());
                // }
            	$result['didnot_insert_game_logs']=true;
				$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            } else {
				$result['reason_id'] = self::REASON_NOT_FOUND_PLAYER;
				$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
                $this->CI->utils->debug_log('error', '=============== cannot get player id from '.$gameUsername.' getPlayerIdInGameProviderAuth');
            }
        } else {
			$error_code = @$resultArr['error'];
			switch($error_code) {
				case '1' :
					$result['reason_id'] = self::REASON_NO_ENOUGH_BALANCE;
					break;
				case '2' :
					$result['reason_id'] = self::REASON_NOT_FOUND_PLAYER;
					break;
				case '4' :
				case '5' :
					$result['reason_id'] = self::REASON_INVALID_KEY;
					break;
				case '6' :
					$result['reason_id'] = self::REASON_GAME_ACCOUNT_LOCKED;
					break;
				case '7' :
					$result['reason_id'] = self::REASON_INCOMPLETE_INFORMATION;
					break;
				case '100' :
				case '120' :
					$result['reason_id'] = self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
					break;
			}
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

        return array($success, $result);

	}

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
            case 3:
            case 'id-id':
                $lang = 'id'; // indo
                break;
			case 4:
            case 'vi-vi':
				$lang = 'vi'; // vietnamese
				break;
			case 5:
            case 'ko-kr':
				$lang = 'ko'; // korean
				break;
			case 6:
            case 'th-th':
				$lang = 'th'; // thailand
				break;
            default:
                $lang = 'en'; // default as english
                break;
        }
        return $lang;
    }

	public function queryForwardGame($playerName,$extra=null) {

		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$language = $this->getLauncherLanguage($extra['language']);
		$platform = $extra['is_mobile']?"MOBILE":"WEB";

		$this->lobby_url = $extra['is_mobile']
				  		 ? $this->utils->getSystemUrl('m') . $this->getSystemInfo('lobby_url')
				  		 : $this->utils->getSystemUrl('www') . $this->getSystemInfo('lobby_url');

		$this->CI->utils->debug_log('extra =============>',$extra);
		if (array_key_exists("extra", $extra)) {
		    $this->lobby_url = $extra['extra']['t1_lobby_url'];
		}
		if($extra['game_mode'] == "fun"||$extra['game_mode'] == "trial"){
			if ($extra['game_code'] == self::FISHING_GAME) {
				$demoUrl = $this->fishing_demo_url;
			} else {
				$demoUrl = $this->demo_url."?gameSymbol=".$extra['game_code']."&lang=".$language."&cur=".$this->getPlayerPragmaticPlayCurrency($playerName)."&lobbyURL=".$this->lobby_url;
			}
			return array("success"=>true,"url"=>$demoUrl);
		}else{

			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForQueryForwardGame',
				'language' => $language,
				'playerName' => $playerName
			);

			$params = array(
				'cashierURL' => isset($extra['cashierURL'])?$extra['cashierURL']:$this->cashier_url,
				'externalPlayerId' => $playerName,
				'gameId' => $extra['game_code'],
				'language' => $language,
				'lobbyURL' => $this->enabled_lobby ? $this->lobby_url : "",
				'platform' => $platform,
				'secureLogin' => $this->secureLogin
			);

			ksort($params);
			$param_build = '';
			foreach ($params as $key => $value) {
				$param_build .= "&{$key}={$value}";
			}
			$param_build = trim($param_build, '&');
			$params['hash'] = MD5($param_build.$this->secretKey);
			$this->CI->utils->debug_log('PRAGMATIC PLAY API FORWARD GAME =========================>',$params);

			return $this->callApi(self::API_queryForwardGame, $params, $context);
		}

	}

	public function processResultForQueryForwardGame($params){

		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$result = array();
		$result['url'] = '';
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		if($success){
			$result['url'] = $resultArr["gameURL"];
		}

		return array($success, $result);

	}

	public function logout($userName, $password = null) {
		$playerName = $this->getGameUsernameByPlayerUsername($userName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogout',
			'playerName' => $playerName
		);

		$params = array(
			'externalPlayerId'=> !empty($playerName)?$playerName:$userName,
			'secureLogin' => $this->secureLogin
		);
		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

		return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params){
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);
		return array($success, $resultArr);
	}

	public function syncOriginalGameLogs($token = false) {

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
    	$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
    	$startDate->modify($this->getDatetimeAdjust());
		//observer the date format
		$queryDateTimeStart = $startDate->format('Y-m-d H:i:s');
		$queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
	    $queryDateTimeMax = $endDate->format('Y-m-d H:i:s');
		$rtn = array();
		while ($queryDateTimeMax  > $queryDateTimeStart) {

			$startDateParam=new DateTime($queryDateTimeStart);
			if($queryDateTimeEnd>$queryDateTimeMax){
				$endDateParam=new DateTime($queryDateTimeMax);
			}else{
				$endDateParam=new DateTime($queryDateTimeEnd);
			}
			$startDateParam = $startDateParam->format('Y-m-d H:i:s');
			$endDateParam = $endDateParam->format('Y-m-d H:i:s');

			$timepoint = strtotime($startDateParam)*1000;

			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'startDate' => $startDateParam,
				'endDate' => $endDateParam
			);
			$params = array(
				'login' => $this->secureLogin,
				'password' => $this->secretKey,
				'timepoint' => $timepoint
			);

			if ($this->use_new_sync_game_records) {
				$api = self::API_syncNewGameRecords;
			} else {
				$api = self::API_syncGameRecords;
			}

			$rtn[] = $this->callApi($api, $params, $context);

			$queryDateTimeStart = $endDateParam;
	    	$queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
		}

		return array("success"=>true,"sync_details" => $rtn);
	}
	private function convertGamelogsToCsvFileAndRead($startDate,$endDate,$csvtext){
		if(!$this->convertGamelogsToCsvFile){
			$arr = explode("\n", $csvtext);
			$gameRecord = array();
			if(!empty($arr)){
				foreach ($arr as &$line) {
				  $gameRecord[] = str_getcsv($line);
				}
			}

			unset($gameRecord[0],$gameRecord[1]);//remove header column and timepoint
			array_pop($gameRecord);//remove last data

			$gameRecord = array_values($gameRecord);//rebase
			return $gameRecord;
		}

		$this->game_records_path; #game record path

		$file_name = $this->game_records_path . '/' . $startDate . '_to_' . $endDate .rand(10,9999). '.csv';
		$renamed_name = str_replace(array(":"," "),"_", $file_name);
		file_put_contents($renamed_name,$csvtext); #save it to file
		$csv = array_map('str_getcsv', file($renamed_name)); #read file
		unset($csv[0],$csv[1]);

		//delete csv
		unlink($renamed_name);

		return $csv;
	}

	public function preProcessGameRecords(&$gameRecords,$extra){
		$preResult = array();
		foreach($gameRecords as $index => $record) {
			//Data from Pragmatic Play API
			$preResult[$index]['playerID'] 		   = isset($record['0']) ? $record['0'] : NULL;
			$preResult[$index]['extPlayerID'] 	   = isset($record['1']) ? $record['1'] : NULL;
			$preResult[$index]['gameID'] 			   = isset($record['2']) ? $record['2'] : NULL;
			$preResult[$index]['playSessionID'] 	   = isset($record['3']) ? $record['3'] : NULL;
			$preResult[$index]['timestamp'] 		   = isset($record['4']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', $record['4']/1000)) : NULL;
			$preResult[$index]['referenceID'] 	   = isset($record['5']) ? $record['5'] : NULL;
			$preResult[$index]['type'] 			   = isset($record['6']) ? $record['6'] : NULL;
			$preResult[$index]['amount'] 			   = isset($record['7']) ? $record['7'] : NULL;
			$preResult[$index]['currency'] 		   = isset($record['8']) ? $record['8'] : NULL;

			//extra info from SBE
			$preResult[$index]['Username'] 		   = isset($record['1']) ? $record['1'] : NULL;
			$preResult[$index]['SbePlayerId'] 	   = NULL;
			$preResult[$index]['related_uniqueid']   = $record['0'].$record['2'].$record['3'];
			if ($record['2'] == self::FISHING_GAME) {
				$preResult[$index]['external_uniqueid']  = $record['0'].$record['2'].$record['3'].$record['5']; //playerid + gameid + playsessionid+referenceID	
			} else {
				$preResult[$index]['external_uniqueid']  = $record['0'].$record['2'].$record['3'].$record['6']; //playerid + gameid + playsessionid+type
			}
			
			$preResult[$index]['response_result_id'] = $extra['responseResultId'];

			// new sync(data) set to null included in md5
			$preResult[$index]['start_date']       = NULL;
			$preResult[$index]['end_date']         = NULL;
			$preResult[$index]['bet']              = NULL;
			$preResult[$index]['win']              = NULL;
			$preResult[$index]['status']           = NULL;
			$preResult[$index]['type_game_round']  = NULL;

			$preResult[$index]['last_sync_time']	 = $this->CI->utils->getNowForMysql();
		}
		$gameRecords = $preResult;
	}

	 public function getGameTimeToServerTimeNewSync() {
        return $this->getSystemInfo('gameTimeToServerTimeNewSync','+8 hours');
    }

    public function gameTimeToServerTimeNewSync($dateTimeStr) {
        if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
            $dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
        }
        $modify = $this->getGameTimeToServerTimeNewSync();
        return $this->utils->modifyDateTime($dateTimeStr, $modify);
    }

	// for new api /DataFeeds/gamerounds/
	public function preProcessNewGameRecords(&$gameRecords,$extra){
		$preResult = array();
		foreach($gameRecords as $index => $record) {
			$preResult[$index]['playerID'] 		   = isset($record['0']) ? $record['0'] : NULL;
			$preResult[$index]['extPlayerID'] 	   = isset($record['1']) ? $record['1'] : NULL;
			$preResult[$index]['gameID'] 		   = isset($record['2']) ? $record['2'] : NULL;
			$preResult[$index]['playSessionID']    = isset($record['3']) ? $record['3'] : NULL;
			$preResult[$index]['parent_session_id']= isset($record['4']) ? $record['4'] : NULL;
			$preResult[$index]['start_date']       = isset($record['5']) && $record['5'] != "null" ? $this->gameTimeToServerTimeNewSync($record['5']) : NULL;

			# use start date if end date is empty (meaning round not yet finished)
			$preResult[$index]['end_date']         = isset($record['6']) && $record['6'] != "null" ? $this->gameTimeToServerTimeNewSync($record['6']) : $preResult[$index]['start_date'];

			$preResult[$index]['status']           = isset($record['7']) ? $record['7'] : NULL;    # status (I - Inprogress , C - Complete)
			$preResult[$index]['type_game_round']  = isset($record['8']) ? $record['8'] : NULL;	   # type ( R - game round, F - free spin)
			$preResult[$index]['bet']              = isset($record['9']) ? $record['9'] : NULL;
			$preResult[$index]['win']              = isset($record['10']) ? $record['10'] : NULL;
			$preResult[$index]['currency'] 		   = isset($record['11']) ? $record['11'] : NULL;
			$preResult[$index]['jackpot'] 		   = isset($record['12']) ? $record['12'] : NULL;

			// set null for old api  ===> self::API_syncGameRecords => '/DataFeeds/transactions/',
			$preResult[$index]['timestamp'] 	   = NULL;
			$preResult[$index]['referenceID'] 	   = NULL;
			$preResult[$index]['type'] 			   = NULL;
			$preResult[$index]['amount'] 		   = NULL;
			$preResult[$index]['SbePlayerId'] 	   = NULL;

			$preResult[$index]['last_sync_time']	 = $this->CI->utils->getNowForMysql();

			//extra info from SBE
			$preResult[$index]['Username'] 		     = isset($record['1']) ? $record['1'] : NULL;

			$preResult[$index]['external_uniqueid']  = $preResult[$index]['playSessionID'];
			$preResult[$index]['related_uniqueid']   = $preResult[$index]['playSessionID'];
			$preResult[$index]['response_result_id'] = $extra['responseResultId'];
		}
		$gameRecords = $preResult;
	}

	private function updateOrInsertOriginalGameLogs($data, $queryType, $additionalInfo=[]){
        $dataCount=0;
        if(!empty($data)){

            foreach ($data as $record) {
                if ($queryType == 'update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal($this->original_logs_table_name, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal($this->original_logs_table_name, $record);
                }
                $dataCount++;
                unset($record);
            }
        }

        return $dataCount;
    }

	public function processResultForSyncOriginalGameLogs($params) {
		$this->CI->load->model(array('pragmaticplay_game_logs','original_game_logs_model','external_system'));
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$csvtext = $params['resultText'];
		$gameRecords = $this->convertGamelogsToCsvFileAndRead($startDate,$endDate,$csvtext);

		$result = array(
			'data_count'=> 0
		);
		$dataCount = 0;
		if(!empty($gameRecords)){
			$extra = ['responseResultId'=>$responseResultId];

			if ($this->use_new_sync_game_records) {
				$this->preProcessNewGameRecords($gameRecords,$extra);
			} else {
				$this->preProcessGameRecords($gameRecords,$extra);
			}

			list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                $this->original_logs_table_name,
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_ORIGINAL,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS
            );
            $this->CI->utils->debug_log('after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));
			if (!empty($insertRows)) {
				$result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
			}
			unset($insertRows);

			if (!empty($updateRows)) {
				$result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
			}
			unset($updateRows);
		}
		//old
  //       $availableRows = $this->CI->pragmaticplay_game_logs->getAvailableRows($gameRecords);

		// if (!empty($availableRows)) {
		// 	foreach ($availableRows as $record) {
		// 		$insertRecord = array();
		// 		$playerID = $this->getPlayerIdInGameProviderAuth($record['1']);

		// 		//Data from Pragmatic Play API
		// 		$insertRecord['playerID'] = isset($record['0']) ? $record['0'] : NULL;
		// 		$insertRecord['extPlayerID'] = isset($record['1']) ? $record['1'] : NULL;
		// 		$insertRecord['gameID'] = isset($record['2']) ? $record['2'] : NULL;
		// 		$insertRecord['playSessionID'] = isset($record['3']) ? $record['3'] : NULL;
		// 		$insertRecord['timestamp'] = isset($record['4']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', $record['4']/1000)) : NULL;
		// 		$insertRecord['referenceID'] = isset($record['5']) ? $record['5'] : NULL;
		// 		$insertRecord['type'] = isset($record['6']) ? $record['6'] : NULL;
		// 		$insertRecord['amount'] = isset($record['7']) ? $record['7'] : NULL;
		// 		$insertRecord['currency'] = isset($record['8']) ? $record['8'] : NULL;

		// 		//extra info from SBE
		// 		$insertRecord['Username'] = isset($record['1']) ? $record['1'] : NULL;
		// 		$insertRecord['SbePlayerId'] = $playerID;
		// 		$insertRecord['related_uniqueid'] = $insertRecord['playerID'].$insertRecord['gameID'].$insertRecord['playSessionID'];
		// 		$insertRecord['external_uniqueid'] = $insertRecord['playerID'].$insertRecord['gameID'].$insertRecord['playSessionID'].$insertRecord['type']; //playerid + gameid + playsessionid+type
		// 		$insertRecord['response_result_id'] = $responseResultId;
		// 		//insert data to Pragmatic Play gamelogs table database
		// 		$this->CI->pragmaticplay_game_logs->insertGameLogs($insertRecord);
		// 		$dataCount++;
		// 	}
		// }
		// $result['data_count'] = $dataCount;

		return array(true, $result);
	}

	public function syncMergeToGameLogs($token) {

		if ($this->use_new_sync_game_records) {
			$enabled_game_logs_unsettle = true;
		} else {
			$enabled_game_logs_unsettle = false;    # game provider response for this function  /req/settles is only settle and dont provide game status on there response
		}

        return $this->commonSyncMergeToGameLogs($token,
	        $this,
	        [$this, 'queryOriginalGameLogs'],
	        [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
	        [$this, 'preprocessOriginalRowForGameLogs'],
	        $enabled_game_logs_unsettle);
		//old
		// $this->CI->load->model(array('game_logs', 'player_model', 'pragmaticplay_game_logs'));

		// $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		// $dateTimeFrom->modify($this->getDatetimeAdjust());
		// $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		// //observer the date format
		// $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		// $endDate = $dateTimeTo->format('Y-m-d H:i:s');

		// $rlt = array('success' => true);

		// $result = $this->CI->pragmaticplay_game_logs->getGameLogStatistics($startDate, $endDate);
		// $cnt = 0;
		// if (!empty($result)) {
  //           $this->processedGameRecordsBeforeMerging($result);
		// 	$unknownGame = $this->getUnknownGame();
		// 	foreach ($result as $row) {
  //               $realbet = (float)$row['bet_amount'];
  //               // $result_amount = (float)$row['result_amount'] - (float)$row['bet_amount'];
  //               $result_amount = (float)$row['result_amount'];
		// 		$cnt++;

		// 		$game_description_id = $row['game_description_id'];
		// 		$game_type_id = $row['game_type_id'];

		// 		if (empty($game_description_id)) {
  //                   list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
		// 		}

		// 		$extra = array(
		// 				'trans_amount' => $this->gameAmountToDB($realbet),
		// 				'table' => $row['external_uniqueid'],
		// 				'sync_index' => $row['id'],
		// 			);

		// 		$this->syncGameLogs(
		// 			$game_type_id,
		// 			$game_description_id,
		// 			$row['game_code'],
		// 			$row['game_type'],
		// 			$row['game'],
		// 			$row['SbePlayerId'],
		// 			$row['UserName'],
		// 			$this->gameAmountToDB($realbet),
		// 			$this->gameAmountToDB($result_amount),
		// 			null, # win_amount
		// 			null, # loss_amount
		// 			null, # after_balance
		// 			0, # has_both_side
		// 			$row['external_uniqueid'],
		// 			$row['game_date'], //start
		// 			$row['game_date'], //end
		// 			$row['external_uniqueid'],
		// 			Game_logs::FLAG_GAME,
  //                   $extra
		// 		);

		// 	}
		// }

		// $this->CI->utils->debug_log('PRAGMATIC PLAY API =========================>', 'startDate: ', $startDate,'EndDate: ', $endDate);
		// $this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		// return $rlt;
	}

	 private function getGameRecordsStatus($status) {
        $this->CI->load->model(array('game_logs'));
        $status = strtolower($status);

        switch ($status) {
            case 'i':
                $status = Game_logs::STATUS_PENDING;
                break;
            case 'c':
                $status = Game_logs::STATUS_SETTLED;
                break;
        }
        return $status;
    }

	public function preprocessOriginalRowForGameLogs(array &$row){

		if (empty($row['game_description_id'])) {
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$this->unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }

        if ($this->use_new_sync_game_records) {
        	$row['status'] = $this->getGameRecordsStatus($row['status']);
        } else {
	        $row['status'] = Game_logs::STATUS_SETTLED;
        }
	}


	public function makeParamsForInsertOrUpdateGameLogsRow(array $row){

        if(empty($row['md5_sum'])){
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }

        if ($this->use_new_sync_game_records) {
        	$calc_result = $row['win'] - $row['bet'];
        	$bet_amount =  $this->gameAmountToDB($row['bet']);
        	$result_amount = $this->gameAmountToDB($calc_result);

        	$start_date = $row['start_date'];
        	$end_date = $row['end_date'];
        } else {
        	$bet_amount = $this->gameAmountToDB($row['bet_amount']);
        	$result_amount = $this->gameAmountToDB($row['result_amount']);

        	$start_date = $row['game_date'];
        	$end_date = $row['game_date'];
        }

        $data = [
            'game_info' => [
                'game_type_id' => $row['game_type_id'],
                'game_description_id' => $row['game_description_id'],
                'game_code' => $row['game_code'],
                'game_type' => null,
                'game' => $row['game_code']
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['UserName']
            ],
            'amount_info' => [
                'bet_amount' => $bet_amount,
                'result_amount' => $result_amount,
                'bet_for_cashback' => $bet_amount,
                'real_betting_amount' => $bet_amount,
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => null
            ],
            'date_info' => [
                'start_at' => $start_date,
                'end_at' => $end_date,
                'bet_at' => $start_date,
                'updated_at' => $end_date,
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => $row['status'],
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['external_uniqueid'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => $row['response_result_id'],
                'sync_index' => $row['id'],
                'bet_type' => null
            ],
            'bet_details' => [],
            'extra' => [],
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null
        ];

        return $data;
    }

    /**
     * [processedGameRecordsBeforeMerging
     *   - merge related uniqueids and compute result amount and bet amount
     *   - query related uniqueids in original game logs
     * ]
     * @param  [array] &$rows [unprocessed game logs]
     * @return [array]        [returns prepared game logs]
     */
    private function processedGameRecordsBeforeMerging(&$rows){
        if (!empty($rows)) {
	        $processedGameRecords = $mapExternalRelatedIds = [];
	        $relatedExternalUniqueids = array_unique(array_column($rows, 'external_uniqueid'));

	        $mapOriginalGameLogs = $this->getRelatedUniqueIds($relatedExternalUniqueids);
	        $this->prepareGameLogsBeforeRecalculating($mapOriginalGameLogs);

	        foreach ($rows as $key => $row) {
	            // if ($row['external_uniqueid'] != '14565419bjma384830134002') continue;
	            if (in_array($row['external_uniqueid'], $mapExternalRelatedIds) || $row['type']==self::GAME_WIN) continue;
	            array_push($mapExternalRelatedIds, $row['external_uniqueid']);

	            $betCount = 0;
	            if ($row['type'] == self::GAME_BET) {
	                $processedGameRecords[$key] = $row;

	                #handle multiple bets: $row['type'] = "B"
	                $betCount = count($mapOriginalGameLogs[$row['external_uniqueid']][self::GAME_BET]);
	                if (count($betCount > 1)){
	                    $betAmount = array_column($mapOriginalGameLogs[$row['external_uniqueid']][self::GAME_BET], 'amount');
	                    $processedGameRecords[$key]['bet_amount'] = array_sum($betAmount);
	                    $processedGameRecords[$key]['result_amount'] = -array_sum($betAmount);
	                }else{
	                    $processedGameRecords[$key]['bet_amount'] = $row['amount'];
	                    $processedGameRecords[$key]['result_amount'] = -$row['amount'];
	                }
	            }

	            #compute bet amount and win amount - override value of 'result_amount' above
	            if (!empty($mapOriginalGameLogs[$row['external_uniqueid']][self::GAME_BET]) && !empty($mapOriginalGameLogs[$row['external_uniqueid']][self::GAME_WIN])) {
	                // print_r($processedGameRecords);exit;
	                $winAmountAndBetAmount = $mapOriginalGameLogs[$row['external_uniqueid']][self::GAME_WIN]['amount'];
	                if (!empty($betCount) && $betCount > 1) {
	                    $processedGameRecords[$key]['result_amount'] = $winAmountAndBetAmount - array_sum($betAmount);
	                }else{
	                    $processedGameRecords[$key]['result_amount'] = $winAmountAndBetAmount - $row['amount'];
	                }
	            }
	        }

	        $rows = $processedGameRecords;
	        unset($processedGameRecords,$mapOriginalGameLogs,$relatedExternalUniqueids,$mapExternalRelatedIds);
        }
    }

    /**
     * [getRelatedUniqueIds query all related unique ids]
     * @param  [array] $externalUniqueIds [related unique ids]
     * @return [array]                    [returns all realted uniqueids]
     */
    public function getRelatedUniqueIds($externalUniqueIds){
        foreach ($externalUniqueIds as &$row) {
            $row = "'". $row ."'";
        }
        $externalUniqueIds = implode(",", $externalUniqueIds);

        $this->CI->load->model('original_game_logs_model');
        $sql = <<<EOD
            SELECT * FROM pragmaticplay_game_logs WHERE related_uniqueid in ($externalUniqueIds)
EOD;

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, []);
    }

    /**
     * [prepareGameLogsBeforeRecalculating set related_uniqueid as key of the array]
     * @param  [array] &$rows [unprocessed game logs]
     * @return [array]        [returns an array with key is equal to related_uniqueid]
     */
    private function prepareGameLogsBeforeRecalculating(&$rows){
        $data = [];
        foreach ($rows as $key => $row) {
            if ($row['type']==self::GAME_BET) {
                $data[$row['related_uniqueid']][$row['type']][$key] = $row;
            }else{
                $data[$row['related_uniqueid']][$row['type']] = $row;
            }
        }
        $rows = $data;
        unset($data);
    }

    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;

        $external_game_id = $row['game_code'];
        $extra = array('game_code' => $row['game_code']);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $external_game_id, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

    public function queryTransaction($transactionId, $extra) {
		$playerName=$extra['playerName'];
		$playerId=$extra['playerId'];
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'playerId' => $playerId,
			'external_transaction_id' => $transactionId,
        );

        $params = array(
            'externalTransactionId' =>$transactionId,
            'secureLogin' => $this->secureLogin
        );

        $params['hash'] = MD5(http_build_query($params).$this->secretKey);

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'playerName');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

        if(isset($resultArr['error']) && $resultArr['error'] == "0"){
			$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }else{
			if($resultArr['status'] == 'Not found') {
				$result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
			} else {
				$error_code = @$resultArr['error'];
				switch($error_code) {
					case '2' :
						$result['reason_id'] = self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
						break;
				}
			}
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){

    	$sqlFreeSpin = '';
    	if($this->use_new_sync_game_records) {
      		$sqlTime='pgl.end_date >= ? and pgl.end_date <= ?';

			// R - Game Round , F - Free Spin
      		if(!$this->merge_free_spin_data) {
				$sqlFreeSpin=' and pgl.type_game_round = "R" ';
      		}
    	} else {
    		$sqlTime='pgl.timestamp >= ? and pgl.timestamp <= ?';
    	}

        $sql = <<<EOD
SELECT pgl.id as id,
pgl.SbePlayerId,
pgl.UserName,
pgl.related_uniqueid as external_uniqueid,
pgl.timestamp AS game_date,
pgl.gameID AS game_code,
pgl.response_result_id,
pgl.amount,
pgl.type,
pgl.md5_sum,
pgl.start_date,
pgl.end_date,
pgl.status,
pgl.type_game_round,
pgl.bet,
pgl.win,
pgl.jackpot,
pgl.last_sync_time as updated_at,

game_provider_auth.player_id,
gd.id as game_description_id,
gd.game_type_id

FROM {$this->original_logs_table_name} as pgl

left JOIN game_description as gd ON pgl.gameID = gd.game_code and gd.game_platform_id=?
JOIN game_provider_auth ON pgl.UserName = game_provider_auth.login_name and game_provider_auth.game_provider_id=?

WHERE

{$sqlTime}
{$sqlFreeSpin}
EOD;
	$params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            $dateFrom,
            $dateTo
        ];

        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);

        // only for old syncing (cause merging slow)
        if (!$this->use_new_sync_game_records) {
	        $this->processedGameRecordsBeforeMerging($result);
        }



        return $result;
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

	/**
	 * overview : get game time to server time
	 *
	 * @return string
	 */
	/*public function getGameTimeToServerTime() {
		// return '+8 hours';
	}*/

	/**
	 * overview : get server time to game time
	 *
	 * @return string
	 */
	/*public function getServerTimeToGameTime() {
		// return '-8 hours';
	}*/

	public function syncPlayerAccount($username, $password, $playerId) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
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

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		return $this->returnUnimplemented();
	}

	public function getGameProviderGameList() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processGetGameProviderGameList',

		);

		$params = array(
			'secureLogin' => $this->secureLogin,
		);

		$params['hash'] = MD5(http_build_query($params).$this->secretKey);

		return $this->callApi(self::API_getGameProviderGamelist, $params, $context);
	}

	public function processGetGameProviderGameList($params) {
      	$resultJson = $this->getResultJsonFromParams($params);
		$success = isset($resultJson['gameList']) ? true:false;

		if ( $success && $resultJson['gameList'] > 0 ) {
			$list = [];

			$gameTypeList = array(
				'Video Slots' => 'slots',
				'Classic Slots' => 'slots',
				'Blackjack' => 'table_games',
				'Video Poker' => 'video_poker',
				'Keno' => 'table_games',
				'Roulette' => 'table_games',
				'Baccarat' => 'table_games',
				'Racing games' => 'others',
			);

			$this->CI->load->model(['game_description_model','game_type_model']);
			$dbGameTypeList = $this->getDBGametypeList();

			foreach ($resultJson['gameList'] as $key => $gameDetail) {

				$gameTypeCode = isset( $gameTypeList[$gameDetail['typeDescription']]) ? $gameTypeList[$gameDetail['typeDescription']] : 'others';
				$gameTypeId = $dbGameTypeList[$gameTypeCode]['id'];

				$lang_arr = [
					self::INT_LANG_ENGLISH 		=> $gameDetail['gameName'],
					self::INT_LANG_CHINESE 		=> $gameDetail['gameName'],
					self::INT_LANG_INDONESIAN   => $gameDetail['gameName'],
					self::INT_LANG_VIETNAMESE   => $gameDetail['gameName'],
					self::INT_LANG_KOREAN 		=> $gameDetail['gameName']
				];

				$list[$key] = [
					'game_platform_id' 	 => $this->getPlatformCode(),
					'game_type_id' 	  	 => $gameTypeId,
					'game_code' 	 	 => $gameDetail['gameID'],
					'attributes' 	 	 => '{"game_launch_code":"'. $gameDetail['gameID'] .'""}',
					'english_name' 		 => $gameDetail['gameName'],
					'external_game_id' 	 => $gameDetail['gameID'],
					'enabled_freespin' 	 => Game_description_model::DB_FALSE,
					'sub_game_provider'  => null,
					'enabled_on_android' => $this->checkGameAttribute('html5',$gameDetail['technology']),
					'enabled_on_ios' 	 => $this->checkGameAttribute('html5',$gameDetail['technology']),
					'status' 			 => Game_description_model::DB_TRUE,
					'flash_enabled' 	 => $this->checkGameAttribute('flash',$gameDetail['technology']),
					'mobile_enabled' 	 => $this->checkGameAttribute('html5',$gameDetail['technology']),
					'html_five_enabled'  => $this->checkGameAttribute('html5',$gameDetail['technology']),
					'game_name' 		 => $this->processLanguagesToJson($lang_arr),
				];
			}

			$result = $this->CI->game_description_model->syncGameDescription($list,null, false, true, null, $this->getGameListAPIConfig());

		}
		return array($success, $result);
	}

	public function checkGameAttribute ($key,$data) {

		if( strpos($data, ',') == false) {
			return ($key == $data) ? Game_description_model::DB_TRUE:Game_description_model::DB_FALSE;
		}else{
			$array = explode(',', $data);
			return in_array($key,$array) ? Game_description_model::DB_TRUE:Game_description_model::DB_FALSE;
		}
	}
}

/*end of file*/