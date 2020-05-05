<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
	* API NAME: MG PLUS
	* API docs: K2 API MG PLUS Common Wallet
	* API docs: MG PLUS K2 Common Wallet
	*
	* @category Game_platform
	* @version 1.8.10
	* @copyright 2013-2019 TripleOneTech
	* @integrator @garry.php.ph
**/

class Game_api_common_mgplus extends Abstract_game_api {
	const POST = 'POST';
	const PATCH = 'PATCH';
	const BAL = 'BAL';
	const GAME = 'GAME';
	const GET = 'GET';

	protected $do_update_sync_id; # if true, last sync id will be updated in the external system

	# Fields in og_v2_game_logs we want to detect changes for update
    const MD5_FIELDS_FOR_ORIGINAL=[
        'createddateutc',
        'gamestarttimeutc',
        'gameendtimeutc',
        'productplayerid',
        'playerid',
        'gamecode',
        'betamount',
        'payoutamount',
        'betstatus',
        'metadata',
    ];

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS = [
        'betamount',
        'payoutamount',
    ];

    # Fields in game_logs we want to detect changes for merge, and when aviaesport_game_logs.md5_sum is empty
    const MD5_FIELDS_FOR_MERGE=[
        'external_uniqueid',
        'bet_amount',
        'round',
        'game_code',
        'game_name',
        'after_balance',
        'valid_bet',
        'result_amount',
        'username',
        'start_at',
        'end_at',
        'bet_at'
    ];

    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        'bet_amount',
        'valid_bet',
        'result_amount',
	];

	const NEED_QUERY = [self::API_queryPlayerBalance, self::API_isPlayerExist];

    const MGPLUS_LIVE_GAMES_GAMECODE = 'SMG_titaniumLiveGames_Baccarat_Playboy';

	//Case-insensitive
	const VAR_LIST = ['agentCode','playerId','productId','walletTransactionId'];

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->sts_api_url = $this->getSystemInfo('sts_api_url');
		$this->currency = $this->getSystemInfo('currency');
		$this->agent_code = $this->getSystemInfo('agent_code');
		$this->agent_key = $this->getSystemInfo('agent_key');
		$this->method = null;
		$this->is_auth = false;
		$this->avail_token = false;
		$this->utc_offset = $this->getSystemInfo('utc_offset',8);
		$this->do_update_sync_id = true;
		$this->language = $this->getSystemInfo('language', 'en-US');
		$this->lobby_url = $this->getSystemInfo('lobby_url');
		$this->bank_url = $this->getSystemInfo('bank_url');

		$this->URI_MAP = array(
			self::API_generateToken => '/connect/token',
			self::API_createPlayer => '/agents/[agentCode]/players',
			self::API_queryPlayerBalance => '/agents/[agentCode]/players/[playerId]',
			self::API_isPlayerExist => '/agents/[agentCode]/players/[playerId]',
	        self::API_depositToGame => '/agents/[agentCode]/WalletTransactions',
	        self::API_withdrawFromGame => '/agents/[agentCode]/WalletTransactions',
	        self::API_queryTransaction => '/agents/[agentCode]/WalletTransactions/[walletTransactionId]',
	        self::API_queryForwardGame => '/agents/[agentCode]/players/[playerId]/sessions',
			self::API_syncGameRecords => '/agents/[agentCode]/bets',
			self::API_blockPlayer => '/agents/[agentCode]/players/[playerId]/products',
			self::API_unblockPlayer => '/agents/[agentCode]/players/[playerId]/products',
			self::API_queryBetDetailLink => '/agents/[agentCode]/players/[playerId]/betVisualizers'
		);

		$this->METHOD_MAP = array(
			self::API_generateToken => self::POST,
			self::API_createPlayer => self::POST,
			self::API_queryPlayerBalance => self::GET,
			self::API_isPlayerExist => self::GET,
			self::API_depositToGame => self::POST,
			self::API_withdrawFromGame => self::POST,
			self::API_queryTransaction => self::GET,
			self::API_queryForwardGame => self::POST,
			self::API_syncGameRecords => self::GAME,
			self::API_blockPlayer => self::PATCH,
			self::API_unblockPlayer => self::PATCH,
			self::API_queryBetDetailLink => self::POST
		);
	}

	public function getPlatformCode() {
		return $this->returnUnimplemented();
	}

	protected function getMethodName($apiName){		
		return (isset($this->METHOD_MAP[$apiName])?$this->METHOD_MAP[$apiName]:self::GET);
	}

	protected function getHttpHeaders($params){

		$headers = [];
		if(!$this->is_auth){

			$auth = $this->getAvailableApiToken();

			$headers = [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => 'Bearer '.$auth,
			];

			if($this->method == self::PATCH) {
				$headers = [
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Bearer '.$auth,
				];
			}
		}
		return $headers;
	}

	private function createVarMap($params) {
		$resultKey = array();
		$resultVal = array();
		foreach (self::VAR_LIST as $var) {
			if (array_key_exists($var, $params) && !empty($params[$var])) {
				$resultKey[] = '[' . $var . ']';
				if ($var == 'playerName') {
					$resultVal[] = urlencode(strtoupper($params[$var]));
				} else {
					$resultVal[] = urlencode($params[$var]);
				}
			}
		}
		return array($resultKey, $resultVal);
	}

	public function generateUrl($apiName, $params) {
		$this->method = $this->getMethodName($apiName);

		$apiUri = $this->URI_MAP[$apiName];
		list($keys, $values) = $this->createVarMap($params);
		//Case-insensitive
		$apiUri = str_ireplace($keys, $values, $apiUri);
		$url = $this->api_url . $apiUri;

		if($apiName == self::API_generateToken){
			$url = $this->sts_api_url . $apiUri;
		}

		if(in_array($apiName,self::NEED_QUERY)){
			$url .= '?properties=balance';
		}

		return $url;
	}

    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        return false;
    }

	protected function customHttpCall($ch, $params) {

		switch ($this->method){
			case self::POST:
				$fields=http_build_query($params);
				if(array_key_exists('type', $params) && $params['type']==='Withdraw'){
					//withdraw
					if(array_key_exists('amount', $params) && $params['amount']===null){
						//append amount
						$fields.='&amount=';
						$this->CI->utils->debug_log('withdraw all amount', $fields);
					}
				}
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$this->CI->utils->debug_log('MG PLUS REQUEST POST FIELD: ', $fields);
				break;
			case self::PATCH:
				$fields=http_build_query($params);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::PATCH);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$this->CI->utils->debug_log('MG PLUS REQUEST PATCH FIELD: ', $fields);
				break;
			case self::GAME:
				$params = array(
					'limit' => $params['limit'],
					'startingAfter' => $params['startingAfter']
				);
				$fields=http_build_query($params);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::GET);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$this->CI->utils->debug_log('MG PLUS REQUEST GET FIELD: ', $fields);
				break;
		}
	}

	public function processResultBoolean($responseResultId, $resultArr, $statusCode) {
		$success = false;
		if(@$statusCode == 200 || @$statusCode == 201){
			$success = true;
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('MGPLUS_API got error ', $responseResultId,'result', $resultArr);
		}
		return $success;
	}

	/**
	 * will check timeout, if timeout then call again
	 * @return token
	 */
    public function getAvailableApiToken(){
        return $this->getCommonAvailableApiToken(function(){
           return $this->generateToken();
        });
    }

    /**
	 * Generate Access Token
	 *
	 * Token will be invalid each 60 minutes
	 */
	public function generateToken(){

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGenerateToken',
			'playerId'=>null,
		);

		$params = array(
			'grant_type' => 'client_credentials',
			'client_id' => $this->agent_code,
			'client_secret' => $this->agent_key,
		);

		$this->is_auth = TRUE;

		$temp = $this->method;

		$return = $this->callApi(self::API_generateToken, $params, $context);

		$this->method = $temp;
		$this->is_auth = FALSE;

		return $return;
	}

	public function processResultForGenerateToken($params){
		$resultArr = $this->getResultJsonFromParams($params);
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);
        $result=['api_token'=>null, 'api_token_timeout_datetime'=>null];

		if($success){
			$api_token = @$resultArr['access_token'];
			# Token will be invalid each 30 minutes
			$token_timeout = new DateTime($this->utils->getNowForMysql());
			$minutes = ((int)$resultArr['expires_in']/60)-1;
			$token_timeout->modify("+".$minutes." minutes");
			$result['api_token']=$api_token;
			//$this->avail_token = true;
			$result['api_token_timeout_datetime']=$token_timeout->format('Y-m-d H:i:s');


		}

		return array($success,$result);
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
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername, # max 50 chars - _
		);

		//$this->method = self::POST;

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		$result = array(
			'player' => $gameUsername,
			'exists' => false
		);

		if($success){
			# update flag to registered = true
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
	        $result['exists'] = true;
		}

		return array($success, $result);
	}

	public function blockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForBlockPlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
			'isLock' => 'true',
		);

		//$this->method = self::PATCH;

		return $this->callApi(self::API_blockPlayer, $params, $context);
	}

	public function processResultForBlockPlayer($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

		if($success){
			# block player on game
			$this->blockUsernameInDB($gameUsername);
			$result['message'] = "player blocked!";
		}

		return array($success, $result);
	}

	public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForUnblockPlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
			'isLock' => 'false',
		);

		//$this->method = self::PATCH;

		return $this->callApi(self::API_unblockPlayer, $params, $context);
	}

	public function processResultForUnblockPlayer($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

		if($success){
			# block player on game
			$this->unblockUsernameInDB($gameUsername);
			$result['message'] = "player unblocked!";
		}

		return array($success, $result);
	}


	private function round_down($number, $precision = 3){
	    $fig = (int) str_pad('1', $precision, '0');
	    return (floor($number * $fig) / $fig);
	}

	public function queryPlayerBalance($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$this->is_auth = !$this->avail_token ? false : true;

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
		);

		//$this->method = self::BAL;

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

		if($success){
			$result['balance'] = $this->round_down(@floatval($resultArr['balance']['total']));
		}

		return array($success, $result);
	}

	public function isPlayerExist($playerName){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->is_auth = !$this->avail_token ? false : true;
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
		);

		//$this->method = self::BAL;

		return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = array();

		if($success){
			$result['exists'] = true;
		}else{
			if(@$statusCode == '404'){ # 404 Player not found
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

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = empty($transfer_secure_id) ? 'T'.uniqid() : $transfer_secure_id;
		$this->is_auth = !$this->avail_token ? false : true;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
        );

		$params = array(
			'agentCode' => $this->agent_code,
			'amount' => $amount,
			'externalTransactionId' => $external_transaction_id,
			'playerId' => $gameUsername,
			'type' => 'Deposit',
		);

		//$this->method = self::POST;

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$amount = $this->getVariableFromContext($params, 'amount');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);

		$result = array(
			'response_result_id' => $responseResultId,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
			$result['external_transaction_id'] = $resultArr['id'];
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasons($statusCode);
        }

        return array($success, $result);
	}

	private function getReasons($statusCode){
		switch ($statusCode) {
			case 400:
				return self::REASON_INCOMPLETE_INFORMATION;
				break;
			case 401:
				return self::REASON_INVALID_KEY;
				break;
			case 404:
				return self::REASON_INVALID_TRANSACTION_ID;
				break;
			case 409:
				return self::REASON_INVALID_TRANSFER_AMOUNT;
				break;
			case 500:
				return self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
				break;

			default:
                return self::REASON_UNKNOWN;
                break;
		}
	}
	/**
	 * withdraw all from game, ignore amount
	 * @param  string $playerName
	 * @param  double $amount
	 * @param  string $transfer_secure_id
	 * @return
	 */
    public function withdrawAllFromGame($playerName, $amount, $transfer_secure_id){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = empty($transfer_secure_id) ? 'T'.uniqid() : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => null,
			'external_transaction_id' => $external_transaction_id
        );

		$params = array(
			'agentCode' => $this->agent_code,
			'amount' => null,
			'externalTransactionId' => $external_transaction_id,
			'playerId' => $gameUsername,
			'type' => 'Withdraw',
		);

		//$this->method = self::POST;

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = empty($transfer_secure_id) ? 'T'.uniqid() : $transfer_secure_id;
		$this->is_auth = !$this->avail_token ? false : true;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
			'external_transaction_id' => $external_transaction_id
        );

		$params = array(
			'agentCode' => $this->agent_code,
			'amount' => $amount,
			'externalTransactionId' => $external_transaction_id,
			'playerId' => $gameUsername,
			'type' => 'Withdraw',
		);

		//$this->method = self::POST;

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	public function processResultForWithdrawFromGame($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$amount = $this->getVariableFromContext($params, 'amount');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);

		$result = array(
			'response_result_id' => $responseResultId,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
			$result['external_transaction_id'] = $resultArr['id'];
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasons($statusCode);
        }

        return array($success, $result);
	}

	public function queryTransaction($transactionId, $extra) {
		$playerName=$extra['playerName'];
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId=$extra['playerId'];

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'gameUsername' => $gameUsername,
			'external_transaction_id' => $transactionId,
			'playerId'=>$playerId,
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'walletTransactionId' => $transactionId,
		);

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $statusCode);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if($success){
			$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
            $result['tatus'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasons($statusCode);
		}

		return array($success, $result);
	}

	public function getLauncherLanguage($language){
		$lang='';
        switch ($language) {
            case 1:
            case 'en-us':
            case LANGUAGE_FUNCTION::INT_LANG_ENGLISH:
                $lang = 'en-US'; // english
                break;
            case 2:
            case 'zh-cn':
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
                $lang = 'zh-CN'; // chinese
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
            case LANGUAGE_FUNCTION::INT_LANG_THAI:
				$lang = 'th'; // thailand
				break;
            default:
                $lang = 'en-US'; // default as english
                break;
        }
        return $lang;
	}

	public function queryForwardGame($playerName, $extra = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->is_auth = !$this->avail_token ? false : true;
		$liveDealerExtraInfo = $this->getSystemInfo('default_live_dealer_lobby_code');

		if (!empty($this->language)) {
			$language = $this->getLauncherLanguage($this->language);
		} else {
			$language = $this->getLauncherLanguage($extra['language']);
		}

		if(isset($this->lobby_url) && !empty($this->lobby_url)) {
            $this->lobby_url = $this->getSystemInfo('lobby_url');
        } else {
            $this->lobby_url = $extra['is_mobile'] == 'true'
                             ? $this->utils->getSystemUrl('m') . $this->getSystemInfo('lobby_url')
                             : $this->utils->getSystemUrl('www') . $this->getSystemInfo('lobby_url');
        }

		$game_code = $extra['game_code'];
		if ($extra['game_code'] === 'null') {
			$game_code = !empty($liveDealerExtraInfo) ? $liveDealerExtraInfo : self::MGPLUS_LIVE_GAMES_GAMECODE;
		}

		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryForwardGame',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
			'contentCode' => $game_code,
			'contentType' => is_null($extra['game_type'])?'Game':$extra['game_type'], # default:: Game | Tournament
			'langCode' => $language,
			'platform' => $extra['is_mobile']?'Mobile':'Desktop',
			'homeUrl' => $this->lobby_url,
			'bankUrl' => $this->bank_url,
		);

		$this->CI->utils->debug_log('PARAMS ==>', $params);

		//$this->method = self::POST;

		return $this->callApi(self::API_queryForwardGame, $params, $context);
	}

	public function processResultForQueryForwardGame($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = @$this->getVariableFromContext($params, 'gameUsername');
		$playerName = @$this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = array('url'=>'');

		if($success){
			$result['url'] = @$resultArr['gameURL'];
		}

		return array($success, $result);
	}

    # notes: Attention! This domain is different from other APIs. Access Restriction: 10 seconds
	# "Query limit is 10 minutes"
	public function syncOriginalGameLogs($token = false) {
		$ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

		// if ($ignore_public_sync == true) {
		// 	$this->CI->utils->debug_log('ignore public sync'); // ignore public sync
		// 	return array('success' => true);
		// }

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$startDateTime = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
    	$startDateTime->modify($this->getDatetimeAdjust());
    	$endDateTime = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
    	$queryDateTimeStart = $startDateTime->format("Y-m-d H:i:s");
		$queryDateTimeEnd = $startDateTime->format('Y-m-d H:i:s');
		$last_sync_id = $this->getLastSyncIdFromTokenOrDB($token);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncOriginalGameLogs',
			'startDate' => $queryDateTimeStart,
			'endDate' => $queryDateTimeEnd
		);

		$params = array(
			'agentCode' => $this->agent_code,
			'limit' => 20000, # max return
			'startingAfter' => $last_sync_id,
		);

		//$this->method = self::GAME;

		return $this->callApi(self::API_syncGameRecords, $params, $context);
	}

	public function processResultForSyncOriginalGameLogs($params) {
        $this->CI->load->model('original_game_logs_model');
		$statusCode = $this->getStatusCodeFromParams($params);
		$startDate = $this->getVariableFromContext($params, 'startDate');
		$endDate = $this->getVariableFromContext($params, 'endDate');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = array('data_count'=>0);
		$gameRecords = !empty($resultArr) ? $resultArr : [];
		if($success && !empty($gameRecords)){
            $extra = ['response_result_id'=>$responseResultId];
			$this->rebuildGameRecords($gameRecords,$extra);

			$lastRecord = array_values(array_slice($gameRecords, -1))[0];

			if(array_key_exists('betuid', $lastRecord) && !empty($lastRecord['betuid'])){
				$result['last_sync_id'] = $lastRecord['betuid'];
			}
			
            if(array_key_exists('betuid', $lastRecord) && !empty($lastRecord['betuid']) && $this->do_update_sync_id) {
			  $this->CI->external_system->setLastSyncId($this->getPlatformCode(), $lastRecord['betuid']);
            } else {
              $this->CI->utils->error_log("Error: Last sync index not updated");
            }

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                $this->original_table,
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_ORIGINAL,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS
            );
			$this->CI->utils->debug_log('after process available rows', count($gameRecords), count($insertRows), count($updateRows));

            unset($gameRecords);

            if (!empty($insertRows)) {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($insertRows);

            if (!empty($updateRows)) {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($updateRows);
		}

		return array($success, $result);
	}

	private function rebuildGameRecords(&$gameRecords,$extra)
	{
		if(! empty($gameRecords)){
			
			foreach($gameRecords as $index => $record){
				$data['betuid'] = isset($record['betUID'])?$record['betUID']:null;
				$data['createddateutc'] = isset($record['createdDateUTC'])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',strtotime($record['createdDateUTC']))):null;
				$data['gamestarttimeutc'] = isset($record['gameStartTimeUTC'])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',strtotime($record['gameStartTimeUTC']))):null;
				$data['gameendtimeutc'] = isset($record['gameEndTimeUTC'])?$this->gameTimeToServerTime(date('Y-m-d H:i:s',strtotime($record['gameEndTimeUTC']))):null;
				$data['productid'] = isset($record['productId'])?$record['productId']:null;
				$data['productplayerid'] = isset($record['productPlayerId'])?$record['productPlayerId']:null;
				$data['playerid'] = isset($record['playerId'])?$record['playerId']:null;
				$data['gamecode'] = isset($record['gameCode'])?$record['gameCode']:null;
				$data['platform'] = isset($record['platform'])?$record['platform']:null;
				$data['currency'] = isset($record['currency'])?$record['currency']:null;
				$data['betamount'] = isset($record['betAmount'])?$record['betAmount']:null;
				$data['payoutamount'] = isset($record['payoutAmount'])?$record['payoutAmount']:null;
				$data['betstatus'] = isset($record['betStatus'])?$record['betStatus']:null;
				$data['pca'] = isset($record['PCA'])?$record['PCA']:null;
				$data['metadata'] = isset($record['metadata'])?json_encode($record['metadata'],true):null;
				$data['external_uniqueid'] = isset($record['betUID'])?$record['betUID']:null;
				$data['response_result_id'] = $extra['response_result_id'];

				$gameRecords[$index] = $data;
				unset($data);
			}
			
		}
	}


    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){
        $dataCount=0;
        if(!empty($rows)){
            $responseResultId=$additionalInfo['responseResultId'];
            foreach ($rows as $key => $record) {
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal($this->original_table, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal($this->original_table, $record);
                }
                # update last vendorid
                // if((count($rows)-1) == $key){
                //     $this->CI->external_system->setLastSyncId($this->getPlatformCode(), $record['betuid']);
                // }
                $dataCount++;
                unset($record);
            }
        }

        return $dataCount;
    }

    public function syncMergeToGameLogs($token) {
        $enabled_game_logs_unsettle=false;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='`mg`.`gameendtimeutc` >= ?
          AND `mg`.`gameendtimeutc` <= ?';
        if($use_bet_time){
            $sqlTime='`mg`.`gamestarttimeutc` >= ?
          AND `mg`.`gamestarttimeutc` <= ?';
        }

        $sql = <<<EOD
			SELECT
				mg.id as sync_index,
				mg.response_result_id,
				mg.betuid as round,
				mg.playerid as username,
				mg.betamount as bet_amount,
				mg.betamount as valid_bet,
				mg.payoutamount as result_amount,
				mg.gamestarttimeutc as start_at,
				mg.gameendtimeutc as end_at,
				mg.gamestarttimeutc as bet_at,
				mg.gamecode as game_code,
				mg.gamecode as game_name,
				mg.updated_at,
				mg.external_uniqueid,
				mg.md5_sum,
				game_provider_auth.player_id,
				gd.id as game_description_id,
				gd.game_name as game_description_name,
				gd.game_type_id
			FROM $this->original_table as mg
			LEFT JOIN game_description as gd ON mg.gamecode = gd.external_game_id AND gd.game_platform_id = ?
			LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
			JOIN game_provider_auth ON mg.playerid = game_provider_auth.login_name
			AND game_provider_auth.game_provider_id=?
			WHERE
            {$sqlTime}
EOD;

        $params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            $dateFrom,
            $dateTo
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

	public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
        $extra = [
            'table' =>  $row['round'],
        ];

        if(empty($row['md5_sum'])){
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }
        return [
            'game_info' => [
                'game_type_id' => $row['game_type_id'],
                'game_description_id' => $row['game_description_id'],
                'game_code' => $row['game_code'],
                'game_type' => null,
                'game' => $row['game_code']
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['username']
            ],
            'amount_info' => [
                'bet_amount' => $row['valid_bet'],
                'result_amount' => ($row['result_amount']-$row['valid_bet']),
                'bet_for_cashback' => $row['valid_bet'],
                'real_betting_amount' => $row['bet_amount'],
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => null
            ],
            'date_info' => [
                'start_at' => $row['bet_at'],
                'end_at' => $row['end_at'],
                'bet_at' => $row['bet_at'],
                'updated_at' => $this->CI->utils->getNowForMysql(),
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => Game_logs::STATUS_SETTLED,
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['round'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => $row['response_result_id'],
                'sync_index' => $row['sync_index'],
                'bet_type' => null
            ],
            'bet_details' => [],
            'extra' => $extra,
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];
    }

    public function preprocessOriginalRowForGameLogs(array &$row){
        if (empty($row['game_description_id'])) {
            $unknownGame = $this->getUnknownGame($this->getPlatformCode());
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }
        $row['status'] = Game_logs::STATUS_SETTLED;
    }

	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;
		$external_game_id = $row['game_code'];
        $extra = array('game_code' => $external_game_id,'game_name' => $row['game_name']);

        $game_type_id = $unknownGame->game_type_id ? $unknownGame->game_type_id : null;
        $game_type = $unknownGame->game_name ? $unknownGame->game_name : self::TAG_CODE_UNKNOWN_GAME;

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}

	public function queryBetDetailLink($playerName, $betid = null, $extra = null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->is_auth = !$this->avail_token ? false : true;

		$context = array(
			'callback_obj' => $this,
            'callback_method' => 'processResultForQueryBetDetailLink',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'betUid' => $betid,
        );

		$params = array(
			'agentCode' => $this->agent_code,
			'playerId' => $gameUsername,
			'betUid' => $betid,
			'langCode' => $this->getLauncherLanguage($extra['language']),
			'utcOffset' => $this->utc_offset,
		);

		//$this->method = self::POST;

		return $this->callApi(self::API_queryBetDetailLink, $params, $context);

	}

	public function processResultForQueryBetDetailLink($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = @$this->getVariableFromContext($params, 'gameUsername');
		$playerName = @$this->getVariableFromContext($params, 'playerName');
		$betUid = @$this->getVariableFromContext($params, 'betUid');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = array('url'=>'');

		if($success){
			$resultArr = reset($resultArr);
			$result['url'] = $resultArr['url'];
		}

		return array($success, $result);
	}

    public function login($playerName, $password = null, $extra = null) {
    	return $this->returnUnimplemented();
	}

	public function logout($playerName, $password = null) {
    	return $this->returnUnimplemented();
	}

	public function syncPlayerAccount($playerName, $password, $playerId) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
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

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}
}

/*end of file*/