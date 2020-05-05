<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Create Player
 * * Get player balance
 * * deposit balance to game
 * * withdraw balance to game
 * * forward game
 * * sync original game logs and merge
 * * get game log statistics
 * * get total betting amount
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 *
 *
 * @see Redirect redirect to game page
 *
 * @category Game_platform
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Game_api_fishinggame extends Abstract_game_api {

	// constants
	/*
	{
    	"cagent": "TE60",
    	"des_key": "12345678",
    	"md5_key": "123456",
    	"currency": "CNY"
	}
	*/

	public function getPlatformCode() {
		return FISHINGGAME_API;
	}

	const DEFAULT_CURRENCY = 'CNY';
	const REAL_ACCOUNT = 1;

	private $currency;

	private $demo_modes = ['demo', 'fun', 'trial'];

	public function __construct() {
		parent::__construct();

		$this->des_key = $this->getSystemInfo('des_key');
		$this->md5_key = $this->getSystemInfo('md5_key');
		$this->currency = $this->getSystemInfo('currency');
		$this->string_separator = $this->getSystemInfo('string_separator', "/\\/");
		$this->lobby_game_id = $this->getSystemInfo('lobby_game_id', 0);
		$this->demo_url = $this->getSystemInfo('demo_url', "https://demo.gg626.com");

		if (empty($this->currency)) {
			$this->currency = self::DEFAULT_CURRENCY;
		}

	}

	private function convertArrayToParamString($arr) {
		$paramString = '';
		if (!empty($arr)) {
			$rlt = array();
			foreach ($arr as $name => $value) {
				$rlt[] = $name . '=' . $value;
			}
			$paramString = implode($this->string_separator, $rlt);
		}
		return $paramString;
	}

	// DESENCRYPT //
	private function encrypt($input) {
        $size = @mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = $this->des_key;
        $td = @mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = @mcrypt_generic($td, $input);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        $data = base64_encode($data);
        return preg_replace("/\s*/", '',$data);
    }

    private function decrypt($encrypted) {
        $encrypted = base64_decode($encrypted);
        $key =$this->des_key;
        $td = @mcrypt_module_open('des','','ecb','');
        //使用MCRYPT_DES算法,cbc模式
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = @mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理
        $decrypted = @mdecrypt_generic($td, $encrypted);
        //解密
        @mcrypt_generic_deinit($td);
        //结束
        @mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }

    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }
    // DESENCRYPT END //

	public function generateUrl($apiName, $params) {
		$this->CI->utils->debug_log('FISHINGGAME (generateUrl)', $apiName, $params);		

		$this->CI->load->library(array('salt'));
		$method = $params['method'];

		$this->CI->utils->debug_log('apiName', $apiName, 'params', $params);

		$input = $this->convertArrayToParamString($params);
		$params = $this->encrypt($input);
		$this->CI->utils->debug_log('FISHINGGAME (generateUrl) params_string', $input);		
		$md5Key = MD5($params . $this->md5_key);
		$url = $this->getSystemInfo('url');
		$game_report_url = $this->getSystemInfo('game_report_url');

		if($method == "tr" || $method == "zr"){ // this is for report
			$url = rtrim($game_report_url, '/') . '/doReport.do?params=' . $params . '&key=' . $md5Key;
		}else{
			$url = rtrim($url, '/') . '/doLink.do?params=' . $params . '&key=' . $md5Key;
		}

		return $url;
	}

	protected function getHttpHeaders($params) {
		return array("GGaming"=>"WEB_GG_GI_".$this->getSystemInfo('cagent'),'Content-type' => 'text/xml');
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		$this->CI->utils->debug_log($resultText);

		$resultXml = new SimpleXMLElement($resultText);
		if ($apiName == self::API_queryPlayerBalance) {
			// return $this->processResultForQueryPlayerBalance($apiName, $params, $responseResultId, $resultXml);
		} else if ($apiName == self::API_createPlayer) {

		} else if ($apiName == self::API_prepareTransferCredit) {
			// return $this->processResultForPrepareTransferCredit($apiName, $params, $responseResultId, $resultXml);
		} else if ($apiName == self::API_transferCreditConfirm) {
			// return $this->processResultForTransferCreditConfirm($apiName, $params, $responseResultId, $resultXml);
			// } else if ($apiName == self::API_queryForwardGame) {
			// 	return $this->processResultForQueryForwardGame($apiName, $params, $responseResultId, $resultXml);
		} else if ($apiName == self::API_queryTransaction) {
			return $this->processResultForQueryTransaction($apiName, $params, $responseResultId, $resultXml);
		} else if ($apiName == self::API_isPlayerExist) {
			// return $this->processResultForIsPlayerExist($apiName, $params, $responseResultId, $resultXml);
		}

		return array(false, null);
	}

	protected function processResultBoolean($responseResultId, $result, $playerName = null) {
		$success = false;
		if ($result['code']==0) {
			$success = true;
		}
		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('FISHINGGAME_API got error', $responseResultId, 'playerName', $playerName, 'result', $result);
		}
		return $success;
	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);

		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerId' => $playerId,
			'playerName' => $playerName,
		);
		$actype = $this->getSystemInfo('actype');
		if (empty($actype)) {
			$actype = self::REAL_ACCOUNT;
		}
		$params=array(
						"cagent" => $this->getSystemInfo('cagent'),
						"loginname" => $gameUsername,
						"password" => $password,
						"method" => 'ca',
						"actype" => $actype,
						"cur" => $this->currency
					);

		return $this->callApi(self::API_createPlayer, $params,$context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$returnArr = json_decode($params['resultText'],true);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $returnArr,$playerName);

		$this->CI->utils->debug_log('======FG CREATE PLAYER RESPONSE============ ' , $playerName , $returnArr,$success);

		return array($success,$returnArr);
	}

	//===start isPlayerExist=====================================================================================
	public function isPlayerExist($playerName) {
		//parent::isPlayerExist($playerName, $playerId, $password, $email, $extra);

		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerId' => $playerId,
			'playerName' => $playerName,
		);
		$actype = $this->getSystemInfo('actype');
		if (empty($actype)) {
			$actype = self::REAL_ACCOUNT;
		}
		$params=array(
			"cagent" => $this->getSystemInfo('cagent'),
			"loginname" => $playerName,
			"password" => $password,
			"method" => 'ca',
			"actype" => $actype,
			"cur" => $this->currency
		);

		return $this->callApi(self::API_isPlayerExist, $params,$context);
	}

	public function processResultForIsPlayerExist($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr);
		if(!$success){
			$success = false;
			$result['exists'] = null;
		}
		else {
			$result['exists'] = true;
			$playerId = $this->getVariableFromContext($params, 'playerId');
			$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}
		return array($success,$result);
	}

	//===end isPlayerExist=====================================================================================
	public function queryPlayerBalance($playerName) {

		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerId' => $playerId,
			'playerName' => $playerName,
		);
		$actype = $this->getSystemInfo('actype');
		if (empty($actype)) {
			$actype = self::REAL_ACCOUNT;
		}
		$params=array(
			"cagent" => $this->getSystemInfo('cagent'),
			"loginname" => $playerName,
			"password" => $password,
			"method" => 'gb',
			"cur" => $this->currency
		);
		if(!empty($playerName)){
			return $this->callApi(self::API_queryPlayerBalance, $params,
				$context);
		}else{
			return array('success'=>false, 'exists'=>false, 'balance'=>null);
		}
	}

	public function processResultForQueryPlayerBalance($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$returnArr = json_decode($params['resultText'],true);
		$returnArr['balance'] = isset($returnArr['dbalance'])?$returnArr['dbalance']:0; // dbalance is the double value of balance.
		$success = $this->processResultBoolean($responseResultId, $returnArr);
		if (!$success){
			$success = false;
		}
		return array($success,$returnArr);
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {

		$sbe_playerName = $playerName;
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$randStr = random_string('alnum', 16); // serial number 13-16 digits
		$cagent = $this->getSystemInfo('cagent');
		$ip = $this->CI->input->ip_address();

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerId' => $playerId,
			'playerName' => $playerName,
			'sbe_playerName' => $sbe_playerName,
			'external_transaction_id' => $cagent.$randStr
		);

		$params=array(
			"cagent" => $cagent,
			"loginname" => $playerName,
			"password" => $password,
			"method" => 'tc',
			"billno"=> $cagent.$randStr,//cagent+serial number 13-16 digits
			"type"=> "IN",
			"credit"=> $amount,
			"cur" => $this->currency,
			"ip"=> $ip // players IP
		);

		return $this->callApi(self::API_depositToGame, $params,
			$context);
	}

	public function processResultForDepositToGame($params) {
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
		$amount = $params['params']['credit'];
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr);

		$result = array(
				'response_result_id' => $responseResultId,
				'external_transaction_id'=>$external_transaction_id,
				'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
				'reason_id'=>self::REASON_UNKNOWN
		);
		if ($success) {
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			// $playerInfo = $this->queryPlayerBalance($sbe_playerName);
			// $afterBalance = floatval($playerInfo['balance']);
			// $result["currentplayerbalance"] = $afterBalance;
			// $result["balance"] = $afterBalance;
			// $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			// if ($playerId) {
			// 	$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeMainWalletToSubWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			// }
			//$result["userNotFound"] = false;
			$result['didnot_insert_game_logs']=true;
		}else {
			if (isset($returnArr['code'])) {
				$error_code = @$returnArr['code'];
				switch($error_code) {
					case '1' :
					case '4' :
						$result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
						break;
					case '6' :
						$result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
						break;
					case '7' :
					case '8' :
						$result['reason_id']=self::REASON_INVALID_KEY;
						break;
					case '9' :
						$result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
						break;
				}
				$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			}

			//$result["userNotFound"] = $playerName . 'not found';
		}
		return array($success, $result);
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		$sbe_playerName = $playerName;
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$randStr = random_string('alnum', 16); // serial number 13-16 digits
		$cagent = $this->getSystemInfo('cagent');
		$ip = $this->CI->input->ip_address();

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawFromGame',
			'playerId' => $playerId,
			'playerName' => $playerName,
			'sbe_playerName' => $sbe_playerName,
			'external_transaction_id' => $cagent.$randStr
		);

		$params=array(
			"cagent" => $cagent,
			"loginname" => $playerName,
			"password" => $password,
			"method" => 'tc',
			"billno"=> $cagent.$randStr,//cagent+serial number 13-16 digits
			"type"=> "OUT",
			"credit"=> $amount,
			"cur" => $this->currency,
			"ip"=> $ip // players IP
		);

		return $this->callApi(self::API_withdrawFromGame, $params,
			$context);
	}

	public function processResultForWithdrawFromGame($params) {
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
		$amount = $params['params']['credit'];
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr);

		$result = array(
				'response_result_id' => $responseResultId,
				'external_transaction_id'=>$external_transaction_id,
				'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
				'reason_id'=>self::REASON_UNKNOWN
		);
		if ($success) {
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			// $playerInfo = $this->queryPlayerBalance($sbe_playerName);
			// $afterBalance = floatval($playerInfo['balance']);
			// $result["currentplayerbalance"] = $afterBalance;
			// $result["balance"] = $afterBalance;
			// $playerId = $this->getPlayerIdInGameProviderAuth($playerName);

			// if ($playerId) {
			// 	$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeSubWalletToMainWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			// }
			//$result["userNotFound"] = false;
			$result['didnot_insert_game_logs']=true;
		}else {
			if (isset($returnArr['code'])) {
				$error_code = @$returnArr['code'];
				switch($error_code) {
					case '1' :
					case '4' :
						$result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
						break;
					case '6' :
						$result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
						break;
					case '7' :
					case '8' :
						$result['reason_id']=self::REASON_INVALID_KEY;
						break;
					case '9' :
						$result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
						break;
				}
				$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			}
			//$result["userNotFound"] = $playerName . 'not found';
		}
		return array($success, $result);
	}

    /*
        Current available languages:
        chinese,english,vietnamese,thai,japanese
     */
    public function getLauncherLanguage($lang){
        $this->CI->load->library("language_function");
        switch ($lang) {
            case "zh-cn":
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
                $lang = 'zh-CN';
                break;
            // case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
            //     $lang = 'id';
            //     break;
            case "vi-vn":
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
                $lang = 'vi-VN';
                break;
            // case LANGUAGE_FUNCTION::INT_LANG_KOREAN:
            //     $lang = 'kr';
            //     break;
            default:
                $lang = 'en-US';
                break;
        }
        return $lang;
    }

	public function queryForwardGame($playerName, $extra) {

		if(isset($extra['game_mode']) && in_array($extra['game_mode'], $this->demo_modes)){
			$result['success'] = true;	
			$result['url'] = $this->demo_url;	
			return $result;
		}

		$game_code = $this->lobby_game_id;
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$password = $this->getPasswordString($playerName);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$randomNum = $this->getRandomSequence(); // serial number 13-16 digits
		$cagent = $this->getSystemInfo('cagent');
		$ip = $this->CI->input->ip_address();

		if(isset($extra['game_code']) && !empty($extra['game_code']) && $extra['game_code']!='null'){
			$game_code = $extra['game_code'];
		}		
		
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryForwardGame',
			'playerId' => $playerId,
			'playerName' => $playerName,
		);

		$params=array(
			"cagent" => $cagent,
			"loginname" => $playerName,
			"password" => $password,
			"method" => 'fw',
			"sid"=> $cagent.$randomNum,//cagent+serial number 13-16 digits
			"lang"=> $this->getLauncherLanguage($extra['language']),
			"gametype"=> $game_code,
			"ip"=> $ip, // players IP
			"ishttps"=> 1
		);

		return $this->callApi(self::API_queryForwardGame, $params,$context);

	}

	public function processResultForQueryForwardGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr,$playerName);

		$this->CI->utils->debug_log('======FG FORWARD GAME RESPONSE============ ' , $playerName , $returnArr,$success);

		$result = array('response_result_id' => $responseResultId);
		if (!$success){
			$result["userNotFound"] = $playerName . 'not found';
		}
		return array($success, $returnArr);
	}

	private function getRandomSequence() {
		$seed = str_split('0123456789123456'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomNum = '';
		foreach (array_rand($seed, 16) as $k) {
			$randomNum .= $seed[$k];
		}

		return $randomNum;
	}

	public function syncOriginalGameLogs($token) {

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$syncId = parent::getValueFromSyncInfo($token, 'syncId');
		$cagent = $this->getSystemInfo('cagent');

		// $dateTimeFrom = $dateTimeFrom->format('Y-m-d H:i:s');
		// $dateTimeTo = $dateTimeTo->format('Y-m-d H:i:s');

        $dateTimeFrom = $this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s'));
        $dateTimeTo = $this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s'));

		$firstTime=strtotime($dateTimeFrom);
		$lastTime=strtotime($dateTimeTo);
		//=========================testing======================
		$time=$firstTime;
		while ($time < $lastTime) {
		    $dateTimeFrom = date('Y-m-d H:i:s', $time);
		    $time = strtotime('+10 mins', $time);
		    if($time>$lastTime){
		        $time = $lastTime;
		    }
		    $dateTimeTo = date('Y-m-d H:i:s', $time);

		    $context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncOriginalGameLogs',
				'syncId' => $syncId
			);

			$params=array(
				"cagent" => $cagent,
				"startdate" => $dateTimeFrom,
				"enddate" => $dateTimeTo,
				"method" => 'tr'
			);
			$returnArr = $this->callApi(self::API_syncGameRecords, $params,
				$context);
		}
		return $returnArr;
	}

	public function processResultForSyncOriginalGameLogs($params){
		$this->CI->load->model(array('fishinggame_game_logs', 'player_model'));
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr);
		$cagent = $this->getSystemInfo('cagent');
		$this->CI->utils->debug_log('-----------------RESULT FISHING GAME TIME --------', $params['params']['startdate'].' - '.$params['params']['enddate']);
		$this->CI->utils->debug_log('-----------------RESULT FISHING GAME resultRows --------', $returnArr);
		if ($success) {

			$resultRows = array();
			foreach($returnArr['betlist'] as $betlist){
				foreach($betlist['details'] as $detail){
					$del = array();
					$del['cuuency']=$betlist['cuuency'];
					$del['autoid']=$detail['autoid'];
					$del['accountno']=$betlist['accountno'];
					$del['creditdelat']=$betlist['creditdelat'];
					$del['bet']=$detail['bet'];
					$del['gameId']=$detail['gameId'];
					$del['detail_autoid']=$detail['autoid'];
					$del['bettimeStr']=$this->gameTimeToServerTime($detail['bettimeStr']);
					$del['profit']=$detail['profit'];
					$del['external_uniqueid'] = $detail['autoid']; //add external_uniueid for og purposes
					$del['response_result_id'] = $responseResultId; //add response_result_id for og purposes
					array_push($resultRows, $del);
				}
			}

			$this->CI->utils->debug_log('-----------------SUCCESS FISHING GAME resultRows --------', count($resultRows));



			$gameRecords = $resultRows;

			if ($gameRecords) {
				$availableRows = $this->CI->fishinggame_game_logs->getAvailableRows($gameRecords);

				$this->CI->utils->debug_log('availableRows', count($availableRows), 'gameRecords', count($gameRecords));
				if (isset($availableRows[0])) {
					foreach ($availableRows[0] as $record) {
						$record['Username'] = str_replace($cagent,"",$record['accountno']);
						// $playerId = $this->getPlayerIdInGameProviderAuth($record['Username']);
						// $record['PlayerId'] = $playerId;
						// if(!empty($record['PlayerId'])){
						$this->CI->fishinggame_game_logs->insertFishinggameGameLogs($record);
						// }else{
						// 	$this->CI->utils->debug_log('ignore empty player id', $record['Username'], $record);
						// }
					}
				}

			}
		}
		return array($success);

	}

	public function syncMergeToGameLogs($token) {

		$this->CI->load->model(array('game_logs', 'player_model', 'fishinggame_game_logs'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$this->CI->utils->debug_log('dateTimeFrom', $startDate, 'dateTimeTo', $endDate);


		$rlt = array('success' => true);
		$result = $this->CI->fishinggame_game_logs->getFishinggameGameLogStatistics($startDate, $endDate);

		$cnt = 0;

		if ($result) {

			$unknownGame = $this->getUnknownGame();
			foreach ($result as $fishinggame_data) {
				$player_id = $fishinggame_data->PlayerId;

				// if (!$player_id) {
				// 	continue;
				// }

				$cnt++;

				$bet_amount = $fishinggame_data->bet_amount;
				$result_amount = $fishinggame_data->result_amount;

				$game_description_id = $fishinggame_data->game_description_id;
				$game_type_id = $fishinggame_data->game_type_id;

				if (empty($game_description_id)) {
                    list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($fishinggame_data, $unknownGame);
				}

				$extra = array('sync_index' => $fishinggame_data->id);

				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$fishinggame_data->game_code,
					$fishinggame_data->game_type,
					$fishinggame_data->game,
					$player_id,
					$fishinggame_data->Username,
					$bet_amount,
					$result_amount,
					null, # win_amount
					null, # loss_amount
					null, # after_balance
					0, # has_both_side
					$fishinggame_data->external_uniqueid,
					$fishinggame_data->date_created,
					$fishinggame_data->date_created,
					$fishinggame_data->response_result_id,
					Game_logs::FLAG_GAME,
                    $extra
				);

			}
		}

		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	//===start changePassword=====================================================================================
	public function changePassword($playerName, $oldPassword, $newPassword) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForChangePassword',
                'playerName' => $gameUsername,
                'newPassword' => $newPassword
            );

        $params = array(
			'cagent'    => $this->getSystemInfo('cagent'),
			'loginname' => $gameUsername,
			'password'  => $newPassword,
			'method'     => 'rp'
		);

		return $this->callApi(self::API_changePassword, $params,$context);
	}

    public function processResultForChangePassword($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
		$newPassword = $this->getVariableFromContext($params, 'newPassword');
        $returnArr = json_decode($params['resultText'],true);
        $success = $this->processResultBoolean($responseResultId, $returnArr);

        $result = array('response_result_id' => $responseResultId);
        if (!$success){
            $result["userNotFound"] = $playerName . 'not found';
        }else{
        	$playerName = $this->getVariableFromContext($params, 'playerName');
			$playerId = $this->getPlayerIdInGameProviderAuth($playerName);

            $result['playerName'] = $playerName;
            $result['success'] = true;
            $this->updatePasswordForPlayer($playerId, $newPassword);
		}
		
		$this->CI->utils->debug_log('processResultForChangePassword monitor ============================================>', [$success, $result, $returnArr]);
		
        return array($result);
    }
	//===end changePassword=====================================================================================

	public function queryTransaction($transactionId, $extra) {

		$playerId=$extra['playerId'];
		$playerName=$extra['playerName'];
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$cagent = $this->getSystemInfo('cagent');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'gameUsername'=>$gameUsername,
			'playerId'=>$playerId,
			'external_transaction_id'=>$transactionId,
		);

		$params=array(
			"cagent" => $cagent,
			"method" => 'qx',
			"billno"=> $transactionId,
		);

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction( $params ){
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$returnArr = json_decode($params['resultText'],true);
		$success = $this->processResultBoolean($responseResultId, $returnArr);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
			$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
			if (isset($returnArr['code'])) {
				$error_code = @$returnArr['code'];
				switch($error_code) {
					case '1' :
						$result['reason_id']=self::REASON_IP_NOT_AUTHORIZED;
						break;
					case '4' :
					case '5' :  // order no. not exist
						$result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
						$success = true;
						break;
					case '6' :
						$result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
						break;
					case '7' :
					case '8' :
						$result['reason_id']=self::REASON_INVALID_KEY;
						break;
					case '9' :
						$result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
						break;
				}
				$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
				$result['error_message'] = $this->translateReasonId($result['reason_id']);
			}
			//$result["userNotFound"] = $playerName . 'not found';
		}
		return array($success, $result);
	}

	//===start queryPlayerInfo=====================================================================================
	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}
	//===end queryPlayerInfo=====================================================================================

	//===start blockPlayer=====================================================================================
	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
	}
	//===end blockPlayer=====================================================================================

	//===start unblockPlayer=====================================================================================
	public function unblockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->unblockUsernameInDB($playerName);
		return array("success" => true);
	}
	//===end unblockPlayer=====================================================================================



	//===start login=====================================================================================
	public function login($playerName, $password = null) {
		// $playerName = $this->getGameUsernameByPlayerUsername($playerName);
		// return array("success" => true);
		return $this->returnUnimplemented();
	}
	//===end login=====================================================================================

	//===start logout=====================================================================================
	public function logout($playerName, $password = null) {
		// $playerName = $this->getGameUsernameByPlayerUsername($playerName);
		// return array("success" => true);
		return $this->returnUnimplemented();
	}
	//===end logout=====================================================================================

	//===start updatePlayerInfo=====================================================================================
	public function updatePlayerInfo($playerName, $infos) {
		// $playerName = $this->getGameUsernameByPlayerUsername($playerName);
		// return array("success" => true);
		return $this->returnUnimplemented();
	}

	//===end updatePlayerInfo=====================================================================================

	//===start queryPlayerDailyBalance=====================================================================================
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		return $this->returnUnimplemented();
	}
	//===end queryPlayerDailyBalance=====================================================================================

	//===start queryGameRecords=====================================================================================
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		$gameRecords = parent::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());
		return array('success' => true, 'gameRecords' => $gameRecords);
	}
	//===end queryGameRecords=====================================================================================

	//===start checkLoginStatus=====================================================================================
	public function checkLoginStatus($playerName) {
		// $playerName = $this->getGameUsernameByPlayerUsername($playerName);
		// return array("success" => true, "loginStatus" => true);
		return $this->returnUnimplemented();
	}
	//===end checkLoginStatus=====================================================================================

	//===start totalBettingAmount=====================================================================================
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {
		$gameBettingRecord = parent::getGameTotalBettingAmount($playerName, $dateFrom, $dateTo);
		if ($gameBettingRecord != null) {
			$result['bettingAmount'] = $gameBettingRecord['bettingAmount'];
		}
		return array("success" => true, "bettingAmount" => $result['bettingAmount']);
	}
	//===end totalBettingAmount=====================================================================================

	private function getGameDescriptionInfo($row, $unknownGame) {
        if(empty($row->game_type_id)){
            $game_type_id = $unknownGame->game_type_id;
            $row->game = $row->game_code;
            $row->game_type = $unknownGame->game_name;
        }

        $externalGameId = $row->game_code;
        $extra = array('game_code' => $row->game_code);
        return $this->processUnknownGame(
            $row->game_description_id, $row->game_type_id,
            $row->game, $row->game_type, $externalGameId, $extra,
            $unknownGame);
		// $externalGameId = $row->game;
		// $extra = array('game_code' => $row->game_code);
		// return $this->processUnknownGame(
		// 	$row->game_description_id, $row->game_type_id,
		// 	$row->game, $row->game_type, $externalGameId, $extra,
		// 	$unknownGame);
	}

	private function getGameLogStatistics($playerName, $dateTimeFrom, $dateTimeTo) {
		$this->CI->load->model('ag_game_logs');
		return $this->CI->game_logs->getGameLogStatistics($playerName, $dateTimeFrom, $dateTimeTo);
	}

	/**
	 * game time + 12 = server time
	 *
	 */
	// public function getGameTimeToServerTime() {
	// 	return '+12 hours';
	// }

	// public function getServerTimeToGameTime() {
	// 	return '-12 hours';
	// }

}

/*end of file*/