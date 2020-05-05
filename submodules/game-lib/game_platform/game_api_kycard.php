<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
	* API DOCS NAME: KY Card Game API
	* Document Number: V.1.1.7


	*
	* @category Game_platform
	* @version 1.8.10
	* @copyright 2013-2018 TripleOneTech
**/

class game_api_kycard extends Abstract_game_api {
	const START_PAGE = 1;

	const API_queryGameRecordsByDate = "queryGameRecordsByDate";
	const API_queryBetDetailLink = "queryBetDetailLink";

	public function __construct() {
		parent::__construct();
		$this->cipher = MCRYPT_RIJNDAEL_128;
		$this->mode = MCRYPT_MODE_ECB;
		$this->pad_method = NULL;
		$this->iv = '';
		$this->api_url = $this->getSystemInfo('url');
		$this->agent_code = $this->getSystemInfo('agent_code');
		$this->des_key = $this->getSystemInfo('des_key');
		$this->md5_key = $this->getSystemInfo('md5_key');
		$this->data_api = $this->getSystemInfo('data_api');
		$this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+60 minutes');
		$this->sync_sleep_time = $this->getSystemInfo('sync_sleep_time', '5'); //to prevent error code 43(The interval between pulls must be greater than 1 second)

		// $this->URI_MAP = array(
        //     self::API_queryGameRecordsByDate => '9',
        //     self::API_queryBetDetailLink => '19',
        // );

	}

	public function getPlatformCode() {
		return KYCARD_API;
	}

	private function require_pkcs5(){
        $this->pad_method = 'pkcs5';
    }

	private function getMillisecond(){
        list($t1, $t2) = explode(' ', microtime());
        return $t2 .  ceil( ($t1 * 1000) );
    }

    private function getOrderId($agent_code){
        list($usec, $sec) = explode(" ", microtime());
        $msec=round($usec*1000);
        return $this->agent_code.date("YmdHis").$msec;
    }

    private function pkcs5_pad($text, $blocksize){
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text){
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    private function pad_or_unpad($str, $ext){
        if (is_null($this->pad_method)){
            return $str;
        }else{
            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';
            if(is_callable($func_name)){
                $size = @mcrypt_get_block_size($this->cipher, $this->mode);
                return call_user_func($func_name, $str, $size);
            }
        }

        return $str;
    }

    private function pad($str){
        return $this->pad_or_unpad($str,'');
    }

    private function unpad($str){
        return $this->pad_or_unpad($str, 'un');
    }

    private function encrypt($str) {
        $str = $this->pad($str);
        $td = @mcrypt_module_open($this->cipher, '', $this->mode, '');
        if (empty($this->iv)){
            $iv = @mcrypt_create_iv(@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        }else{
            $iv = $this->iv;
        }

        @mcrypt_generic_init($td, $this->des_key, $iv);
        $cyper_text = @mcrypt_generic($td, $str);
        $rt=base64_encode($cyper_text);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);

        return $rt;
    }

    private function decrypt($str){
        $td = @mcrypt_module_open($this->cipher, '', $this->mode, '');

        if (empty($this->iv) ){
            $iv = @mcrypt_create_iv(@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        }else{
            $iv = $this->iv;
        }

        @mcrypt_generic_init($td, $this->des_key, $iv);
        $decrypted_text = @mdecrypt_generic($td, base64_decode($str));
        $rt = $decrypted_text;
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);

        return $this->unpad($rt);
    }

	public function generateUrl($apiName, $params) {
		# timestamp with milliseconds
		$current_timestamp = $this->getMillisecond();
		$key = md5($this->agent_code.$current_timestamp.$this->md5_key);
		$this->require_pkcs5();
		$str_param = http_build_query($params);
		$encrypted_param = $this->encrypt($str_param);

		$param = array(
			'agent' => $this->agent_code,
			'timestamp' => $current_timestamp,
			'param' => $encrypted_param,
			'key' => $key,
		);

		if($apiName == self::API_syncGameRecords || $apiName == self::API_queryBetDetailLink || $apiName == self::API_queryGameRecordsByDate) {
			$this->api_url = $this->data_api;
		}

		$url = $this->api_url.'?'.http_build_query($param);
		// echo htmlspecialchars($url);exit;
		return $url;
	}

	public function processResultBoolean($responseResultId, $resultArr, $apiName, $playerName = null) {
		$success = false;

		/** Code 16 - Data non-existence（no bet list currently）*/
		/** Code 0 - Success */

        if(isset($resultArr['d']['code']) && $resultArr['d']['code'] == 0){
            $success = true;
		}

		if($apiName == self::API_syncGameRecords){
			if(isset($resultArr['d']['code']) && $resultArr['d']['code'] == 16){
				$success = true;
			}
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('KYCARD_API API got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}

		return $success;
	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$ip = $this->utils->getIP();
		$orderid = $this->getOrderId($this->agent_code);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'playerId' => $playerId,
			'gameUsername' => $gameUsername
		);

		$params = array(
			's' => '0',
			'account' => $gameUsername,
			'money' => 0,
			'orderid' => $orderid,
			'ip' => $ip,
			'lineCode' => '',
			'KindID' => 0
		);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_createPlayer, $gameUsername);

		$result = array(
			"player" => $gameUsername,
			"exists" => false
		);

		if($success){
			# update flag to registered = truer
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
			's' => '7',
			'account' => $gameUsername
		);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_queryPlayerBalance, $gameUsername);
		$result = array();
		if($success){
			$result['balance'] = @floatval($resultArr['d']['totalMoney']);
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
			's' => '1',
			'account' => $gameUsername
		);

		return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_isPlayerExist, $gameUsername);
		$result = array();

		if($success){
			$result['exists'] = true;
		}else{
			$error_code = @$resultArr['d']['code'];
			if(isset($resultArr['d']['code'])&&($error_code==35||$error_code==11)){
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

	private function getReasons($error_code){
        switch ($error_code) {
            case 31:
            case 1009:
                return self::REASON_INVALID_TRANSFER_AMOUNT;
                break;
            case 6:
            case 29:
            case 1018:
                return self::REASON_INCOMPLETE_INFORMATION;
                break;
            case 28:
                return self::REASON_IP_NOT_AUTHORIZED;
                break;
            case 24:
                return self::REASON_NETWORK_ERROR;
                break;
            case 20:
            case 1016:
                return self::REASON_GAME_ACCOUNT_LOCKED;
                break;
            case 22:
                return self::REASON_INVALID_KEY;
                break;
            case 29:
                return self::REASON_INVALID_TRANSACTION_ID;
                break;
            case 34:
            case 1011:
            case 1022:
                return self::REASON_DUPLICATE_TRANSFER;
                break;
            case 999:
                return self::REASON_FAILED_FROM_API;
                break;
            case 1002:
            case 1014:
                return self::REASON_NO_ENOUGH_CREDIT_IN_SYSTEM;
                break;
            case 1005:
            case 38:
                return self::REASON_NO_ENOUGH_BALANCE;
                break;
            case 1023:
                return self::REASON_CANT_TRANSFER_WHILE_PLAYING_THE_GAME;
                break;
            default:
                return self::REASON_UNKNOWN;
                break;
        }
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = $this->getOrderId($this->agent_code);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $external_transaction_id,
        );

		$params = array(
			's' => '2',
			'account' => $gameUsername,
			'money' => $amount,
			'orderid' => $external_transaction_id
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
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_depositToGame, $playerName);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

		if ($success) {
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
            // } else {
            //     $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            //     $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
            // }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        }else{
			$error_code = @$resultArr['d']['code'];
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=$this->getReasons($error_code);
        }

        return array($success, $result);

	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = $this->getOrderId($this->agent_code);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'amount' => $amount,
            'external_transaction_id' => $external_transaction_id
        );

		$params = array(
			's' => '3',
			'account' => $gameUsername,
			'money' => $amount,
			'orderid' => $external_transaction_id
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
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_withdrawFromGame, $playerName);
		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
	           //  $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,$this->transTypeSubWalletToMainWallet());
            // } else {
            //     $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            //     $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
            // }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        }else{
			$error_code = @$resultArr['d']['code'];
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
			's' => '4',
			'orderid' => $transactionId
		);

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_queryTransaction, $playerName);

		$result = array(
			'response_result_id' => $responseResultId,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN,
			'external_transaction_id'=>$external_transaction_id
		);

		if($success){
			if ($resultArr['d']['status']==0){ # check for status if 0 success else declined.
				$result['status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			} else {
				$error_code = @$resultArr['d']['code'];
            	$result['reason_id']=$this->getReasons($error_code);
				$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			}
		}else{
			$error_code = @$resultArr['d']['code'];
            $result['reason_id']=$this->getReasons($error_code);
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	public function blockPlayer($playerName) {
		#used for kickout player only
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForBlockPlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
        );

		$params = array(
			's' => '8',
			'account' => $gameUsername
		);

		return $this->callApi(self::API_blockPlayer, $params, $context);
    }

    public function processResultForBlockPlayer($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_blockPlayer, $playerName);
		$result = array();

    	if ($success) {
			$this->blockUsernameInDB($gameUsername);
		}

		return array($success, $result);
    }

    public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->unblockUsernameInDB($gameUsername);
		return array("success" => true);
    }

    public function login($playerName, $password = null, $extra = null) {
    	$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerID = $this->getExternalAccountIdByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogin',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
        );

		$params = array(
			'pid' => $playerID,
			'token_type' => 'external_token'
		);

		$this->method = self::POST;

    	return $this->callApi(self::API_login, $params, $context);
	}

	public function processResultForLogin($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $params, self::API_login, $playerName);
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
                $lang = 1; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 2; // chinese
                break;
            case '3':
            case 'id-id':
                $lang = 1; // indonesia
                break;
            case '4':
            case 'vi-vn':
                $lang = 5; // vietnamese
                break;
            case '5':
            case 'ko-kr':
                $lang = 6; // korean
                break;
            default:
                $lang = 1; // default as english
                break;
        }
        return $lang;
    }

	public function queryForwardGame($playerName, $extra = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$ip = $this->utils->getIP();
		$orderid = $this->getOrderId($this->agent_code);

		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryForwardGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

		$params = array(
			's' => '0',
			'account' => $gameUsername,
			'money' => 0,
			'orderid' => $orderid,
			'ip' => $ip,
			'lineCode' => '',
			'KindID' => $extra['game_code']
		);

		return $this->callApi(self::API_queryForwardGame, $params, $context);

	}

	public function processResultForQueryForwardGame($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, self::API_queryForwardGame, $gameUsername);
        $result = array();
        $result['url'] = '';
       	if($success){
			$result['url'] =  $resultArr['d']['url'];
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
    	$queryDateTimeStart = $startDate->format("Y-m-d H:i:s");
		$queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
    	$queryDateTimeMax = $endDate->format("Y-m-d H:i:s");

    	$result = array();

    	while ($queryDateTimeMax  > $queryDateTimeStart) {
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'startDate' => $queryDateTimeStart,
				'endDate' => $queryDateTimeEnd
			);

			$params = array(
				's' => '6',
				'startTime' => strtotime($queryDateTimeStart)*1000,
				'endTime' => strtotime($queryDateTimeEnd)*1000
			);

			$result[] = $this->callApi(self::API_syncGameRecords, $params, $context);

			$queryDateTimeStart = $queryDateTimeEnd ;
	    	$queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
	    	sleep($this->sync_sleep_time);
		}

		return array("success" => true, "results"=>$result);
	}

	public function processResultForSyncOriginalGameLogs($params) {
		$this->CI->load->model(array('kycard_game_logs'));
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
        $resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_syncGameRecords);
		$gameRecords = isset($resultArr['d']['list'])?$resultArr['d']['list']:array();
		$result = [];

		# no data
		if(@$resultArr['d']['code'] == 16){
			$success = true;
			$result['startDate'] = $startDate;
			$result['endDate'] = $endDate;
		}

		$dataCount = 0;

		if($success){
			$gameIds = isset($resultArr['d']['list']['GameID'])?$resultArr['d']['list']['GameID']:array();
			$result['dataCount'] = 0;

			if(!empty($gameIds)){
				$availableRows = !empty($gameIds)?$this->CI->kycard_game_logs->getAvailableRows($gameIds):array();
				foreach ($availableRows as $key => $value) {
					$username = explode("_",$gameRecords['Accounts'][$value]);
					$insertRecord = array();
					$insertRecord['gameid'] = isset($gameRecords['GameID'][$value])?$gameRecords['GameID'][$value]:null;
					$insertRecord['username'] = $username[1];
					$insertRecord['accounts'] = isset($gameRecords['Accounts'][$value])?$gameRecords['Accounts'][$value]:null;
					$insertRecord['serverid'] = isset($gameRecords['ServerID'][$value])?$gameRecords['ServerID'][$value]:null;
					$insertRecord['kindid'] = isset($gameRecords['KindID'][$value])?$gameRecords['KindID'][$value]:null;
					$insertRecord['tableid'] = isset($gameRecords['TableID'][$value])?$gameRecords['TableID'][$value]:null;
					$insertRecord['chairid'] = isset($gameRecords['ChairID'][$value])?$gameRecords['ChairID'][$value]:null;
					$insertRecord['usercount'] = isset($gameRecords['UserCount'][$value])?$gameRecords['UserCount'][$value]:null;
					$insertRecord['cellscore'] = isset($gameRecords['CellScore'][$value])?$gameRecords['CellScore'][$value]:null;
					$insertRecord['allbet'] = isset($gameRecords['AllBet'][$value])?$gameRecords['AllBet'][$value]:null;
					$insertRecord['profit'] = isset($gameRecords['Profit'][$value])?$gameRecords['Profit'][$value]:null;
					$insertRecord['revenue'] = isset($gameRecords['Revenue'][$value])?$gameRecords['Revenue'][$value]:null;
					$insertRecord['gamestarttime'] = isset($gameRecords['GameStartTime'][$value])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',strtotime($gameRecords['GameStartTime'][$value]))):null;
					$insertRecord['gameendtime'] = isset($gameRecords['GameEndTime'][$value])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',strtotime($gameRecords['GameEndTime'][$value]))):null;
					$insertRecord['cardvalue'] = isset($gameRecords['CardValue'][$value])?$gameRecords['CardValue'][$value]:null;
					$insertRecord['channelid'] = isset($gameRecords['ChannelID'][$value])?$gameRecords['ChannelID'][$value]:null;
					$insertRecord['linecode'] = isset($gameRecords['LineCode'][$value])?$gameRecords['LineCode'][$value]:null;

					# SBE USE
					$insertRecord['uniqueid'] = isset($gameRecords['GameID'][$value])?$gameRecords['GameID'][$value]:null;
					$insertRecord['external_uniqueid'] = isset($gameRecords['GameID'][$value])?$gameRecords['GameID'][$value]:null;
					$insertRecord['response_result_id'] = $responseResultId;
					$insertRecord['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
					$insertRecord['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
					$insertRecord['external_game_id'] = isset($gameRecords['KindID'][$value]) ? $gameRecords['KindID'][$value].$gameRecords['ServerID'][$value] : null;
					//insert data to KYCARD gamelogs table database
					$this->CI->kycard_game_logs->insertGameLogs($insertRecord);
					$dataCount++;
				}

				$result['dataCount'] = $dataCount;

			}
		}

		return array($success, $result);
	}

	public function syncMergeToGameLogs($token) {

		$this->CI->load->model(array('game_logs', 'player_model', 'kycard_game_logs'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$rlt = array('success' => true);

		$result = $this->CI->kycard_game_logs->getGameLogStatistics($startDate, $endDate);
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
                    'trans_amount'=> $row->real_bet_amount,
					'rent' => $row->rake, # rake
                );


				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$row->game_id,
					$row->game_type,
					$row->game,
					$row->player_id,
					$row->username,
					$row->bet_amount,
					$row->result_amount,
					null, # win_amount
					null, # loss_amount
					null, # after_balance
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

		$this->CI->utils->debug_log('KYCARD PLAY API =========================>', 'startDate: ', $startDate,'EndDate: ', $endDate);
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;

		$external_game_id = $row->game_id;
        $extra = array('game_code' => $external_game_id,'game_name' => $row->game_id);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        if (!empty($row->game_description_id)) {
			$game_description_id = $row->game_description_id;
			$game_type_id = $row->game_type_id;
		}

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}

	public function logout($playerName, $password = null) {
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

/** Toto Here
    *  The api will return the bet details URL link for viewing the details
    */
    public function queryBetDetailLink($playerUsername, $game_id = NULL, $extra = NULL)
    {
        $record_id = 0;
        $url = '';
        $success = false;
        $bet_details = $this->queryGameLogsDetailsByGameID($game_id);

        // Record not found
        if (empty($bet_details)) {
            return array($success);
        }

		$record_details = $this->queryGameRecordID($playerUsername, $game_id, $bet_details);
        if (!empty($record_details) && $record_details['success']) {

            // Will check the array key number of the given game id for getting record_id on RecordID list
            $record_count   = $record_details['d']['count'];
            $game_ids = $record_details['d']['list']['GameID'];
            for ($i=0; $i < $record_count - 1 ; $i++) {
                if ($game_id === $game_ids[$i]) {
                    $record_id = $i;
                }
            }
			$record_id = $record_details['d']['list']['RecordID'][$record_id];

			$url_response = $this->queryGameDetailsURL($bet_details, $record_id);

            if (!empty($url_response) && $url_response['success']) {
                $success = true;
				$url = $url_response['d']['gameLogURL'];
			}
		}

        return array($success, 'url' => $url);
    }

    /**
    *  The api will return the GameRecords together with recordID that will use on getting bet detail URL on the given timestamp from GameID bet dates
    */
    public function queryGameRecordID($playerUsername, $game_id = null, $bet_details = null)
    {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);
        $playerId = $this->getPlayerIdInPlayer($playerUsername);

        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryGameRecordID',
            'gameUsername'    => $gameUsername,
            'extra'           => $bet_details
        );

        $GameStartTime = new DateTime($bet_details[0]['gamestarttime']);
        $GameEndTime = new DateTime($bet_details[0]['gameendtime']);

		$GameStartTime = new DateTime($this->serverTimeToGameTime($GameStartTime->format('Y-m-d H:i:s')));
        $GameEndTime   = new DateTime($this->serverTimeToGameTime($GameEndTime->format('Y-m-d H:i:s')));

		$params = array(
			's' => '9',
            'startTime' => $GameStartTime->format('U') * 1000,
            'endTime'   => $GameEndTime->format('U') * 1000
		);

        return $this->callApi(self::API_queryGameRecordsByDate, $params, $context);
    }

    /**
     * Process Result of queryBetDetailLink method
    */
    public function processResultForQueryGameRecordID($params)
    {
        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, self::API_queryGameRecordsByDate);
        $this->CI->utils->debug_log('---------- KYCARD processResultForQueryGameRecordID response ----------', $resultArr, 'Success result: ', $success);

        return array($success, $resultArr);
    }

    /**
    *  The api will return the bet detail URL
    */
    public function queryGameDetailsURL($game_logs_details, $recordID) {
		$playername = explode("_",$game_logs_details['0']['accounts']); //get only proper account

        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryGameDetailsURL',
            'extra'           => $game_logs_details
        );

		$params = array(
			's' => '19',
			'gameuserno' => $game_logs_details['0']['gameid'],
			'id' => $recordID,
			'account' => $playername[1],
			'serverID' => $game_logs_details['0']['serverid']
		);

		$this->CI->utils->debug_log('---------- KYCARD bet detail PARAMS ----------', $params);
        return $this->callApi(self::API_queryBetDetailLink, $params, $context);
    }

    /**
     * Process Result of queryGameDetailsURL method
    */
    public function processResultForQueryGameDetailsURL($params)
    {
        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);

		$success = $this->processResultBoolean($responseResultId, $resultArr, self::API_queryBetDetailLink);

		$this->CI->utils->debug_log('---------- KYCARD bet detail URL PARAMS ----------', $resultArr, $params);

        return array($success, $resultArr);
    }


    /**
     * get game logs detail using GameId
     * @param string $queryGameLogsDetailsByGameID unique identifier for logs
     * @return array
     */
    private function queryGameLogsDetailsByGameID($game_id) {
        $this->CI->load->model(array('original_game_logs_model'));
        $sql = <<<EOD
			SELECT username, gameid, accounts, serverid, kindid, tableid, chairid, usercount, cellscore, allbet, profit, revenue, gamestarttime, gameendtime,
			cardvalue, channelid, linecode
            FROM
            kycard_game_logs
            WHERE
                gameid = ?
EOD;

        $params=[
            $game_id,
        ];
        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

}

/*end of file*/