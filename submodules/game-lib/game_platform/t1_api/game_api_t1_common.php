<?php
require_once dirname(__DIR__.'../') .'/abstract_game_api.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Getting platform code
 * * Generate URL
 * * Get Player's Session ID
 * * Create Player
 * * Check Player if exist
 * * Check Player Balance
 * * Change Password
 * * Login/Logout player
 * * Check Player login token
 * * Deposit To game
 * * Withdraw from Game
 * * Check Last  Fund Transfer Request
 * * Check Fund Transfer
 * * Check Forward Game
 * * Synchronize Original Game Logs
 * * Get Game Description Information
 * Behaviors not implemented
 * * Update Player's information
 * * Check Player Daily Balance
 * * Check Game records
 * * Check player's login status
 * * Check Transaction
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 * @see Redirect redirect to game page
 *
 * @category Game_platform
 * @version 1.8.10
 * @copyright 2013-2017 TripleOneTech
 * @since 3.1.00.1 add block/unblock
 * @author Garry
 */
class Game_api_t1_common extends Abstract_game_api {

	protected $api_merchant_code;
	protected $api_secured_key;
	protected $api_sign_key;
	protected $api_token;
	protected $api_agent_id;

	protected $prefix_for_username;
	protected $suffix_for_username;
	protected $language;
	protected $currency;
	protected $api_url;
	protected $game_url;
	protected $method;
	protected $max_allow_sync_time_interval;
	const API_syncUnsettledGameRecords = "syncUnsettledGameRecords";
	const UNSETELLED_GAMELOGS = "unsettle";
	const SETELLED_GAMELOGS = "settled";

	const GAME_HISTORY_STATUS_NORMAL='normal';
	const GAME_HISTORY_STATUS_OTHERS='others';

	const URI_MAP = array(
		self::API_generateToken => 'generate_token',
		self::API_createPlayer => 'create_player_account',
		self::API_isPlayerExist => 'is_player_account_exist',
		self::API_queryPlayerInfo => 'query_player_account',
		self::API_updatePlayerInfo => 'update_player_account',
		self::API_changePassword =>	'change_player_password',
		self::API_blockPlayer => 'block_player_account',
		self::API_unblockPlayer => 'unblock_player_account',
		self::API_isPlayerBlocked => 'query_player_block_status',
		self::API_kickOutGame => 'kick_out_game',
		self::API_queryPlayerBalance => 'query_player_balance',
		self::API_queryGameRecords => 'query_game_history',
		// self::API_TransferCredit => 'transfer_player_fund',

		self::API_depositToGame=>'transfer_player_fund',
		self::API_withdrawFromGame=>'transfer_player_fund',
		self::API_queryForwardGame => 'query_game_launcher',
		self::API_queryTransaction => 'query_transaction',
		self::API_syncGameRecords => 'query_game_history',
		self::API_syncMultiplePlatformGameRecords => 'query_multiple_platform_game_history',
		self::API_syncMultiplePlatformStreamGameHistory=>'query_multiple_platform_stream_game_history',
		self::API_queryBetDetailLink=>'query_bet_detail_link',
	);

	const SUCCESS_CODE = 0;
	const TEST_AGEND_ID = '0';

	const CODE_DUPLICATE_USERNAME='8';

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	const ACTION_TYPE_DEPOSIT = 'deposit';
	const ACTION_TYPE_WITHDRAW = 'withdraw';

	const API_isPlayerBlocked = 'isPlayerBlocked';
	const API_kickOutGame = 'kickOutGame';

	const API_syncMultiplePlatformGameRecords='syncMultiplePlatformGameRecords';
	const API_syncMultiplePlatformStreamGameHistory='syncMultiplePlatformStreamGameHistory';
	const PARLAY_GAME = "parlay";

	// const API_TransferCredit ='transferCredit';

	public function __construct()
	{
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->api_merchant_code = $this->getSystemInfo('api_merchant_code');
		$this->api_secured_key = $this->getSystemInfo('key');
		$this->api_sign_key = $this->getSystemInfo('secret');

		$this->game_url = $this->getSystemInfo('game_url');
		$this->language = $this->getSystemInfo('language');
		$this->currency = $this->getSystemInfo('currency');
		$this->prefix_for_username = $this->getSystemInfo('prefix_for_username');
		$this->is_update_original_row = $this->getSystemInfo('is_update_original_row',false);
		$this->mobile_logout_path = $this->getSystemInfo('mobile_logout_path', '');
		$this->mobile_lobby_path = $this->getSystemInfo('mobile_lobby_path', '');
		$this->support_url = $this->getSystemInfo('support_url', '');
		$this->only_transfer_positive_integer = $this->getSystemInfo('only_transfer_positive_integer', false);
		$this->use_www_as_dm = $this->getSystemInfo('use_www_as_dm', false);

		$this->max_allow_sync_time_interval = $this->getSystemInfo('max_allow_sync_time_interval', '+5 minutes');
		$this->method = self::METHOD_POST;

		$this->query_forward_api_url=$this->getSystemInfo('query_forward_api_url', $this->api_url);
		$this->generate_forward_url_on_sever=$this->getSystemInfo('generate_forward_url_on_sever', false);

		$this->size_per_page=$this->getSystemInfo('size_per_page', 5000);

		// $this->api_token_timeout_on_cache_seconds=$this->getSystemInfo('api_token_timeout_on_cache_seconds', 3600);
		$this->try_get_real_url=$this->getSystemInfo('try_get_real_url', true);

		$this->current_player_id=null;

		//it will be sync all t1 together and ignore original part
		// $this->enabled_sync_t1_quickly=$this->CI->utils->getConfig('enabled_sync_t1_quickly');
    	$this->enabled_t1_directly_sync_api_list=$this->CI->utils->getConfig('enabled_t1_directly_sync_api_list');
    	$this->enabled_sync_t1_quickly=in_array($this->getPlatformCode(), $this->enabled_t1_directly_sync_api_list);

		//enabled_game_logs_unsettle
		$this->enabled_game_logs_unsettle=$this->CI->utils->getConfig('enabled_game_logs_unsettle');
		//call unsettle api
		$this->enable_sync_t1_unsettle=$this->CI->utils->getConfig('enable_sync_t1_unsettle');

		# ONEWORKS BET LIMITS SETTINGS
        $this->bet_limit_settings = $this->getSystemInfo('bet_limit_settings');
        $this->append_target_db=$this->getSystemInfo('append_target_db', true);
		// $this->api_token_timeout_datetime=null;
		// $this->api_token=null;
	}


	/**
	 * Getting platform code
	 *
	 * @return int
	 */
	public function getPlatformCode(){
		return -1;
	}

	public function isPostMethod(){
		return self::METHOD_POST == $this->method;
	}

	/**
	 * Generate URL
	 *
	 * @param $apiName
	 * @param $params
	 * @return string
	 */
	public function generateUrl($apiName, $params) {
		# generate signature
		$apiUri = self::URI_MAP[$apiName];

		$apiUrl=$this->api_url;
		if($apiName==self::API_queryForwardGame){
			$apiUrl=$this->query_forward_api_url;
		}

		if ($this->isPostMethod()) {
			$url = $apiUrl .'/gamegateway/'. $apiUri;
		}else{
			$params['sign'] = $this->generateSignatureByParams($params);
			$url = $apiUrl .'/gamegateway/'. $apiUri . '?' . http_build_query($params);
		}
		// echo "<pre>" . $url;
        // echo json_encode($params);

		$this->CI->utils->debug_log('apiName', $apiName,'url', $url, '====================params', $params);
		return $url;
	}

    protected function getHttpHeaders($params) {
		if ($this->isPostMethod()) {
        	return ["Accept" => "application/json", 'Content-Type'=> 'application/json'];
    	}else{
    		return [];
    	}
    }

	protected function customHttpCall($ch, $params) {
		if ($this->isPostMethod()) {
			if(!isset($params['sign']) || empty($params['sign'])){
				// don't genereate twice
				// generate signature
				$params['sign'] = $this->generateSignatureByParams($params);
			}

			// $params['merchant_code'] = $this->api_merchant_code;
			// $params['auth_token'] = $this->auth_token;
			$data_json = json_encode($params);

			// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
	}

	/**
	 * Concat all non-empty values to one string ,
	 * append secret key to last, then use SHA1 to get signature
	 * The order should be sorted by parameter name in alphabetical order
	 * Ignore any json field and sign field
	 * Always use utf-8
	 *
	 * @param $params
	 * @return string
	 */
	public function generateSignatureByParams($params, $except=['sign']){
		$signString=$this->getSignString($params, $except);

		if(empty($signString)){
			return '';
		}

		$this->CI->utils->debug_log('signString----------------', $signString);

		$sign=strtolower(sha1($signString.$this->api_sign_key));

		return $sign;
	}

	public function getSignString($fields, $except=['sign']){
		$params=[];
		if(!empty($fields)){
			foreach ($fields as $key => $value) {
				if( in_array($key, $except) || is_array($value)){
					continue;
				}
				$params[$key]=$value;
			}
		}

		if(empty($params)){
			return '';
		}

		ksort($params);

		$this->CI->utils->debug_log('get values from params', array_values($params));

		return implode('', array_values($params));

	}

	public function processResultBoolean($responseResultId, $resultJson, $playerName=null) {
		if(!isset($resultJson['code'])){
			return false;
		}
		$success = self::SUCCESS_CODE == $resultJson['code'];
		if (!$success) {
			$this->utils->error_log('return error', $resultJson, 'responseResultId', $responseResultId);
		}
		return $success;
	}

	/**
	 * will check timeout, if timeout then call again
	 * @return token
	 */
    public function getAvailableApiToken(){
        return $this->getCommonAvailableApiToken(function(){
           $forceNew=true;
           return $this->generateToken($forceNew);
        });
    }

	/**
	 * Generate token by merchant code and secure key
	 *
	 * $forceNew boolean = will always create new token if it’s true
	 */
	public function generateToken($forceNew=false){

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGenerateToken',
			'playerId'=>isset($this->current_player_id) ? $this->current_player_id : null,
		);

		$params = array(
			'merchant_code'=>$this->api_merchant_code,
			'secure_key'=>$this->api_secured_key,
			'force_new'=>$forceNew,
		);
		if($this->currency){
			$params['currency'] = $this->currency;
		}
		return $this->callApi(self::API_generateToken, $params, $context);
	}

	public function processResultForGenerateToken($params){
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
        $result=['api_token'=>null, 'api_token_timeout_datetime'=>null];

		if($success){
			$api_token = $resultArr['detail']['auth_token'];
			//minus 30 seconds
			$api_token_timeout_datetime = $this->CI->utils->getMinusSecondsForMysql($resultArr['detail']['timeout_datetime'], 30);
			$result['api_token']=$api_token;
			$result['api_token_timeout_datetime']=$api_token_timeout_datetime;
		}
		return array($success,$result);
	}

	/**
	 * Create Player
	 *
	 * @param $playerName
	 * @param $playerId
	 * @param $password
	 * @param null $email
	 * @param null $extra
	 * @return array
	 */
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$playerId;
		// $this->generateToken(); #generate token

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		# ADD ONEWORKS BET LIMITS ON
		if(!empty($this->bet_limit_settings)){
			$extra['bet_limit_settings'] = $this->bet_limit_settings;
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername,
			'password' => $password,
			'realname' => $gameUsername,
			'extra' => $extra//extra parameters
		);

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);

		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$result=['response_result_id'=>$responseResultId, 'exists'=>false];
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		if(@$resultArr['code']==self::CODE_DUPLICATE_USERNAME){
			//if duplicate means success
			$success=true;
			$result['exists']=true;
		}
		if($success){
			$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}

		return array($success,$result);
	}

	/**
	 * Check Player if exist
	 *
	 * @param $playerName
	 * @return array
	 */
	public function isPlayerExist($playerName, $extra = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'gameUsername' => $gameUsername,
			'playerId'=>$this->current_player_id,
			'playerName' => $playerName,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername,
			'extra' => $extra//extra parameters
		);

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi(self::API_isPlayerExist, $params, $context);
	}

	public function processResultForIsPlayerExist($params){
        $playerName = $this->getVariableFromContext($params, 'playerName');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		// $playerId = $this->getPlayerIdInPlayer($playerName);
		if($success){
	        if ($resultArr['code']=="0") {
		        $playerId = $this->getVariableFromContext($params, 'playerId');
	        	$result = array('exists' => $resultArr['detail']['exists']);
	        	# update flag to registered = true
	        	$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
	        }else{
	            $result = array('exists' => false); # Player not found
	        }
	    }else{
	    	$success = true;
			$result = array('exists' => false); # Player not found
	    }

		return array($success,$result);
	}

	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

	public function changePassword($playerName, $oldPassword, $newPassword) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		// $this->generateToken(); #generate token

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForChangePassword',
			'gameUsername' => $gameUsername,
			'playerId'=>$this->current_player_id,
			'playerName' => $playerName,
			'password' => $newPassword
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername,
			'password' => $newPassword,
			'old_password' => $oldPassword
		);

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi(self::API_changePassword, $params, $context);
	}

	public function processResultForChangePassword($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$result = array();

		if ($success && !isset($resultArr['detail']['unimplemented'])) {

			$result["password"] = $this->getVariableFromContext($params, 'password');
			$playerName = $this->getVariableFromContext($params, 'playerName');
			$playerId = $this->getVariableFromContext($params, 'playerId');
			if ($playerId) {
				//sync password to game_provider_auth
				$this->updatePasswordForPlayer($playerId, $result["password"]);
			} else {
				$this->CI->utils->debug_log('cannot find player', $playerName);
			}
		} elseif (isset($resultArr['detail']['unimplemented'])) {
			$result['unimplemented'] = $resultArr['detail']['unimplemented'];
		}

		return array($success, $result);
	}

	public function blockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForBlockPlayer',
			'gameUsername' => $gameUsername,
			'playerId'=>$this->current_player_id,
			'playerName' => $playerName,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername
		);

		return $this->callApi(self::API_blockPlayer, $params, $context);
	}

	public function processResultForBlockPlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$result = array();

		if($success){
			$result['blocked'] = $resultArr['detail']['blocked'];
	        $this->blockUsernameInDB($gameUsername);
		}

		return array($success, $result);
	}

	public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForUnblockPlayer',
			'gameUsername' => $gameUsername,
			'playerId'=>$this->current_player_id,
			'playerName' => $playerName,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername
		);

		return $this->callApi(self::API_unblockPlayer, $params, $context);
	}

	public function processResultForUnblockPlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$result = array();

		if($success){
			$result['blocked'] = $resultArr['detail']['blocked'];
	        $this->unblockUsernameInDB($gameUsername);
		}

		return array($success, $result);
	}

	/**
	 * Check Player Balance
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerBalance($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'gameUsername' => $gameUsername,
			'playerId'=>$this->current_player_id,
			'playerName' => $playerName,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername
		);

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

		$result = array();
		if($success){
			$result['balance'] = @floatval($resultArr['detail']['game_platform_balance']);
		}

		return array($success, $result);
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$type = self::ACTION_TYPE_DEPOSIT;
		return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$type = self::ACTION_TYPE_WITHDRAW;
		return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
	}

	public function transferCredit($playerName, $amount, $type, $transfer_secure_id){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		$extTransId = $transfer_secure_id; // $gameUsername.date("ymdHis");
		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return [
				'success'=>false,
				'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
				'reason_id'=>self::REASON_INVALID_KEY,
				'external_transaction_id'=>$extTransId,
			];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForTransferCredit',
			'playerId'=>$this->current_player_id,
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'amount' => $amount,
			'type' => $type,
			'extTransId' => $extTransId,
			'external_transaction_id'=>$extTransId,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername,
			'action_type' => $type,
			'amount' => $amount,
			'external_trans_id' => $extTransId
		);

		$apiType=self::API_withdrawFromGame;
		if($type==self::ACTION_TYPE_DEPOSIT){
			$apiType=self::API_depositToGame;
		}

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi($apiType, $params, $context);
	}

	public function processResultForTransferCredit($params){
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$type = $this->getVariableFromContext($params, 'type');
		$amount = $this->getVariableFromContext($params, 'amount');
		$resultArr = $this->getResultJsonFromParams($params);
		#$resultArr['external_transaction_id'] = $this->getVariableFromContext($params, 'extTransId');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);


		$result = array(
			'response_result_id' => $responseResultId,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN,
			'external_transaction_id'=>$this->getVariableFromContext($params, 'external_transaction_id'),
		);
		if ($success) {

			if(isset($resultArr['detail']['transfer_status'])) {
				$result['transfer_status'] = $resultArr['detail']['transfer_status'];
			}else{
				$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			}
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

			// check only api that support query status
			if (isset($resultArr['detail']['transaction_id'])) {
				$result['external_transaction_id'] = $resultArr['detail']['transaction_id'];
			}

            $result['didnot_insert_game_logs']=true;
            // if ($playerId) {
            //     // $playerBalance = $this->queryPlayerBalance($playerName);
            //     $afterBalance = null;

            //     if($type == self::ACTION_TYPE_DEPOSIT){ // Deposit
            //     	// if ($playerBalance && $playerBalance['success']) {
	           //      //     $afterBalance = $playerBalance['balance'];
	           //      // } else {
	           //          //IF GET PLAYER BALANCE FAILED
	           //          // $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
	           //          // $afterBalance = $rlt->totalBalanceAmount;
	           //          // $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
	           //      // }
	           //      // $responseResultId = $result['response_result_id'];
	           //      // Deposit
	           //      $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
	           //          $this->transTypeMainWalletToSubWallet());
            //     }else{ // Withdraw
            //     	// if ($playerBalance && $playerBalance['success']) {
	           //      //     $afterBalance = $playerBalance['balance'];
	           //      //     $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM API '.$type.' ######### ', $afterBalance);
	           //      // } else {
	           //      //     //IF GET PLAYER BALANCE FAILED
	           //      //     $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
	           //      //     $afterBalance = $rlt->totalBalanceAmount;
	           //      //     $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
	           //      // }
	           //      // $responseResultId = $result['response_result_id'];
	           //      // Withdraw
	           //      $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
	           //          $this->transTypeSubWalletToMainWallet());
            //     }
            // } else {
            //     $this->CI->utils->debug_log('error', '=============== cannot get player id from '.$gameUsername.' getPlayerIdInGameProviderAuth');
            // }
		}else{
			if(isset($resultArr['detail']['reason_id'])) {
				$result['reason_id'] = $resultArr['detail']['reason_id'];
				// $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			}
			if(isset($resultArr['detail']['transfer_status'])) {
				$result['transfer_status'] = $resultArr['detail']['transfer_status'];
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

    public function queryTransaction($transactionId, $extra){
		$playerName=$extra['playerName'];
		$playerId=$extra['playerId'];
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->current_player_id=$playerId;

		// $this->generateToken(); #generate token
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'playerId'=>$playerId,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->getOriginalPlatformCode(),
            'external_trans_id' => $transactionId,
            'username' => $gameUsername
        );

        if($this->currency){
			$params['currency'] = $this->currency;
		}

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $success = $this->processResultBoolean($responseResultId, $resultArr);

        #$result = array();
        #$result["order_status"] = isset($resultArr['detail']['trans_status'])?($resultArr['detail']['trans_status']=="settled"?true:false):false;
        #$result["order_message"] = isset($resultArr['detail']['order_message'])?$resultArr['detail']['order_message']:"API Failed!";

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);
		// if ($success) {
		// 	$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		// } else {
		// 	$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		// }
		if(isset($resultArr['detail']['status']) && !empty($resultArr['detail']['status'])) {
			$result['status'] = $resultArr['detail']['status'];
		}

		return array($success, $result);
	}

	public function login($playerName, $password = null){
		return $this->returnUnimplemented();
	}

	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null){
		$daily_balance = parent::getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);

        $result = array();

        if ($daily_balance != null) {
            foreach ($daily_balance as $key => $value) {
                $result[$value['updated_at']] = $value['balance'];
            }
        }

        return array_merge(array('success' => true, "balanceList" => $result));
	}

	/**
	 * overview : query game records
	 *
	 * @param $dateFrom
	 * @param $dateTo
	 * @param null $playerName
	 * @return array
	 */
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
        $gameRecords = parent::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());
        return array('success' => true, 'gameRecords' => $gameRecords);
    }

	public function queryForwardGame($playerName, $extra){
		// $this->generateToken(); #generate token

		$this->current_player_id=$this->getPlayerIdFromUsername($playerName);
		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryForwardGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
			'playerId'=>$this->current_player_id,
        );

        $player_token = $this->getPlayerToken($this->current_player_id);
        $next = $this->CI->utils->getSystemUrl('player','/iframe_module/iframe_viewMiniCashier/'. $this->getPlatformCode() );
        $mini_cashier_url = $this->CI->utils->getSystemUrl('player','/iframe/auth/login_with_token/' . $player_token ."?next=".$next);
        # add return url lobby / mobile
        $lobby_url = $this->CI->utils->getSystemUrl('www');
        if($extra['platform']=="mobile"){
        	$lobby_url = $this->CI->utils->getSystemUrl('m') . $this->mobile_lobby_path;
        }

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->getOriginalPlatformCode(),
            'username' => $gameUsername,
            'generate_forward_url_on_sever' => $this->generate_forward_url_on_sever,
            'is_mobile' => $extra['platform'] == 'mobile' ? true : false, //OGP-12852 For MGPLUS
            'launcher_settings' => array(
                'game_unique_code'=>$extra['game_code'],
                'language'=>$extra['language'],
                'mode'=>$extra['mode'],
                'platform'=>$extra['platform'],
                'game_type'=>$extra['game_type'],
                'redirection'=>isset($extra['redirection'])?$extra['redirection']:null,
                'try_get_real_url'=>$this->try_get_real_url, //it's safe to get real url , instead of launch_game_with_token
                'append_target_db'=>$this->append_target_db,
                'extra'=>array(
                	'game_id'=>isset($extra['game_id'])?$extra['game_id']:null,
                	't1_mobile_logout_url' => $this->CI->utils->getSystemUrl('m') . $this->mobile_logout_path,
                	't1_lobby_url' => $lobby_url,
                	't1_mini_cashier_url' => $mini_cashier_url,
                	't1_support_url' => $this->support_url,
                	't1_merchant_code' => $this->api_merchant_code
                )
            )
        );

        if($this->currency){
			$params['currency'] = $this->currency;
		}

        # add dm (dynamic domain) for AG parameter
        if ($this->getPlatformCode() == T1AGIN_API) {
			if ($extra['platform']=="mobile") {
				$params['launcher_settings']['extra']['t1_ag_dm'] = $this->CI->utils->getSystemUrl('m');
			} else {
				if ($this->use_www_as_dm) {
					$params['launcher_settings']['extra']['t1_ag_dm'] = $this->CI->utils->getSystemUrl('www');
				} else {
					$params['launcher_settings']['extra']['t1_ag_dm'] = $this->CI->utils->getSystemUrl('player');
				}
			}
        }

		$this->method = self::METHOD_POST;

        return $this->callApi(self::API_queryForwardGame, $params, $context);
	}

	public function processResultForQueryForwardGame($params){
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);

        $result = array();
        $result['url'] = '';
       	if($success && !empty($resultArr['detail']['launcher']['url'])){
			$result['url'] =  $resultArr['detail']['launcher']['url'];

	       	// if($this->generate_forward_url_on_sever){
	       	// 	//call url to get real
	       	// 	list($header, $resultText, $statusCode, $statusText, $errCode, $error, $obj)=$this->httpCallApi($result['url'], null);

	        //     $this->CI->utils->debug_log("processResultForQueryForwardGame http call", "header", $header, "resultText", $resultText, "statusCode", $statusCode, "statusText", $statusText, 'callerrCode', $errCode, 'callerr', $error);

	        //     $headerArr = array_map(function($x) { return array_map("trim", explode(":", $x, 2)); },array_filter(array_map("trim", explode("\n", $header))));
	        //     $this->CI->utils->debug_log('headerArr', $headerArr);

	        //     if(!empty($headerArr)){
	        //     	$found302=strpos($headerArr[0][0], '302');
	        //     	$realUrl=null;
	        //     	foreach ($headerArr as $headerRow) {
	        //     		if($headerRow[0]=='Location'){
	        //     			$realUrl=$headerRow[1];
	        //     		}
	        //     	}
	        //     	$this->CI->utils->debug_log('found302', $found302, 'realUrl', $realUrl);
	        //     	//TODO check if full url, maybe need to call again
	        //     	if($found302 && !empty($realUrl)){
	        //     		$result['url']=$realUrl;
	        //     	}
	        //     }
	       	// }
       	}

        return array($success, $result);
	}

	public function syncOriginalGameLogs($token) {
		if($this->enabled_sync_t1_quickly){
			return $this->returnUnimplemented();
		}

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
        //observer the date format
        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
         # adjust range for GG poker
        if($this->getOriginalPlatformCode() == GGPOKER_GAME_API){
	        $startDate = $startDate->format('Y-m-d 00:00:00');
			$endDate = $endDate->format('Y-m-d 23:59:59');
		}else{
			$startDate->modify($this->getDatetimeAdjust());
        	$startDate=$startDate->format('Y-m-d H:i:s');
        	$endDate=$endDate->format('Y-m-d H:i:s');
		}

        $this->always_manually_sync_t1_gamegateway = $this->getValueFromSyncInfo($token, 'ignore_public_sync');
        if(!$this->existsValueFromSyncInfo($token, 'ignore_public_sync')){
        	//try get config
        	$this->always_manually_sync_t1_gamegateway = $this->CI->utils->getConfig('always_manually_sync_t1_gamegateway');
        }

		//pagination, start from 1
        $page = 1;
		$success=$this->syncPaginate($startDate, $endDate, $page, self::SETELLED_GAMELOGS);
		$success=$success && $this->syncPaginate($startDate, $endDate, $page, self::UNSETELLED_GAMELOGS);

        return array('success'=>$success);
	}

	private function syncPaginate($startDate, $endDate, $page, $query_status){

		$this->CI->utils->debug_log('start syncPaginate================',$startDate, $endDate, $page, $query_status);

		$done=false;
		while (!$done) {
		    $rlt = $this->syncT1Gamelogs($startDate, $endDate, $page, $query_status);
		    if($rlt['success']){

		    	$this->CI->utils->info_log('sync game logs api result', $rlt);

		    	if($rlt['total_pages']>$rlt['current_page']){
		    		$page=$rlt['current_page']+1;
		    		$this->CI->utils->debug_log('not done================',$rlt['total_pages'],$rlt['current_page']);
		    	}else{
		    		$done=true;
		    		$this->CI->utils->debug_log('done===================',$rlt['total_pages'],$rlt['current_page']);
		    	}
		    	$success=true;
		    }else{
		    	$success=false;
		    	$done=true;
		    	$this->CI->utils->error_log('sync game logs api error', $rlt);
		    }
        }

        return $success;
	}

	// public function _continueSync($startDate, $endDate, $page = 1, $query_status = 'settled') {
	//     $return = $this->syncT1Gamelogs($startDate, $endDate, $page, $query_status);
 //        if(isset($return['page_number'])){
 //            if( $return['page_number'] <= $page ){
 //                $page = $page+1;
 //                return $this->_continueSync( $startDate, $endDate, $page );
 //            }
 //        }
 //        return $return;
 //    }

    private function syncT1Gamelogs($startDate, $endDate, $page, $query_status){
        // $this->generateToken(false); #generate token

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncT1Gamelogs',
            'from' => $startDate,
            'to' => $endDate,
        );

        $use_bet_time=$this->always_manually_sync_t1_gamegateway;

        $date_mode = $use_bet_time ? 'by_bet_time' : 'by_last_update_time';

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no api token'];
		}

        $params = [
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->getOriginalPlatformCode(),
            'from' => $startDate,
            'to' => $endDate,
            'page_number' => $page,
            'size_per_page' => $this->size_per_page,
            'game_status' => $query_status,
            'date_mode' => $date_mode
        ];

        if($this->currency){
			$params['currency'] = $this->currency;
		}

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    public function processResultForSyncT1Gamelogs($params) {
        $this->CI->load->model(array('t1games_game_logs'));
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $from = $this->getVariableFromContext($params, 'from');
        $to = $this->getVariableFromContext($params, 'to');
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $dataCount = 0;
        $result = [];

        if($success && isset($resultArr['detail'])){
        	$detail=$resultArr['detail'];
	        $gameRecords = isset($detail['game_history']) ? $detail['game_history'] : null;
	        if(!empty($gameRecords)){

	        	if($this->is_update_original_row){
	        		$availableRows = $gameRecords;
	        	}else{
	        		$availableRows = $this->CI->t1games_game_logs->getAvailableRows($gameRecords,$this->getOriginalPlatformCode());
	        	}

		        if($success && !empty($availableRows)){
		            foreach ($availableRows as $record) {
		                $insertRecord = array();
		                //Data from t1lottery API
		                $insertRecord['uniqueid'] = isset($record['uniqueid']) ? $record['uniqueid'] : NULL;
		                $insertRecord['username'] = isset($record['username']) ? $record['username'] : NULL;
		                $insertRecord['merchant_code'] = isset($record['merchant_code']) ? $record['merchant_code'] : NULL;
		                $insertRecord['game_platform_id'] = isset($record['game_platform_id']) ? $record['game_platform_id'] : NULL;
		                $insertRecord['game_code'] = isset($record['game_code']) ? $record['game_code'] : NULL;
		                $insertRecord['game_name'] = isset($record['game_name']) ? $record['game_name'] : NULL;
		                $insertRecord['game_details'] = isset($record['game_details']) ? $record['game_details'] : NULL;
		                $insertRecord['game_finish_time'] = isset($record['game_finish_time']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", (strtotime($record['game_finish_time'])))) : NULL;
		                $insertRecord['updated_at'] = isset($record['updated_at']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", (strtotime($record['updated_at'])))) : NULL;
		                $insertRecord['bet_time'] = isset($record['bet_time']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", (strtotime($record['bet_time'])))) : NULL;
		                $insertRecord['payout_time'] = isset($record['payout_time']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", (strtotime($record['payout_time'])))) : NULL;
		                $insertRecord['round_number'] = isset($record['round_number']) ? $record['round_number'] : NULL;
		                $insertRecord['real_bet_amount'] = isset($record['real_bet_amount']) ? $record['real_bet_amount'] : NULL;
		                $insertRecord['effective_bet_amount'] = isset($record['effective_bet_amount']) ? $record['effective_bet_amount'] : NULL;
		                $insertRecord['result_amount'] = isset($record['result_amount']) ? $record['result_amount'] : NULL;
		                $insertRecord['payout_amount'] = isset($record['payout_amount']) ? $record['payout_amount'] : NULL;
		                $insertRecord['odds'] = isset($record['odds']) ? $record['odds'] : NULL;
		                $insertRecord['after_balance'] = isset($record['after_balance']) ? $record['after_balance'] : NULL;
		                $insertRecord['game_status'] = isset($record['game_status']) ? $record['game_status'] : NULL;
		                $insertRecord['bet_details'] = isset($record['bet_details']) ? $record['bet_details'] : NULL;
		                //extra info from SBE
		                $external_uniqueid = $insertRecord['external_uniqueid'] = isset($record['external_uniqueid']) ? $record['external_uniqueid'] : NULL;
		                $insertRecord['response_result_id'] = $responseResultId;

		                # insert or sync  data t1games gamelogs table database
		                if($this->is_update_original_row){
							$this->CI->t1games_game_logs->syncGameLogs($insertRecord);
		                }else{
		                	$this->CI->t1games_game_logs->insertGameLogs($insertRecord);
		                }

		                $dataCount++;
		            }
		        }
	        }
	        $result['total_pages']=isset($detail['total_pages']) ? $detail['total_pages'] : null;
	        $result['current_page']=isset($detail['current_page']) ? $detail['current_page'] : null;
	        $result['total_rows_current_page']=isset($detail['total_rows_current_page']) ? $detail['total_rows_current_page'] : null;
        }

        $result['data_count'] = $dataCount;

        return array($success, $result);
    }

 //    public function getUnknownGame() {
	// 	$this->CI->load->model(array('game_description_model'));
	// 	return $this->CI->game_description_model->getUnknownGame($this->getPlatformCode());
	// }

	private function getGameRecordsStatus($status) {
		$this->CI->load->model(array('game_logs'));
		$status = strtolower($status);

		switch ($status) {
			case 'unsettle':
				$status = Game_logs::STATUS_PENDING;
				break;
			case 'settled':
				$status = Game_logs::STATUS_SETTLED;
				break;
			default:
				$status = Game_logs::STATUS_SETTLED;
				break;
		}

		return $status;
	}

	public function syncMergeToGameLogs($token) {
		if($this->enabled_sync_t1_quickly){
			return $this->returnUnimplemented();
		}

		$this->CI->load->model(array('game_logs', 'player_model', 't1games_game_logs'));
        $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeFrom->modify($this->getDatetimeAdjust());
        $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        //observer the date format
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        # adjust range for GG poker
        if($this->getOriginalPlatformCode() == GGPOKER_GAME_API){
	        $startDate = $dateTimeFrom->format('Y-m-d 00:00:00');
			$endDate = $dateTimeTo->format('Y-m-d 23:59:59');
		}

        $this->CI->utils->debug_log('dateTimeFrom', $startDate, 'dateTimeTo', $endDate);
        $rlt = array('success' => true);

        $result = $this->CI->t1games_game_logs->getGameLogStatistics($startDate, $endDate,$this->getPlatformCode(),$this->getOriginalPlatformCode());
        $cnt = 0;

        if ($result) {

            $unknownGame = $this->getUnknownGame($this->getPlatformCode());

            foreach ($result as $row) {

                $player_id = $this->getPlayerIdInGameProviderAuth($row->username);

                $cnt++;

                $game_description_id = $row->game_description_id;
                $game_type_id = $row->game_type_id;

                if (empty($game_description_id)) {
                    $game_description_id = $unknownGame->id;
                    $game_type_id = $unknownGame->game_type_id;
                }

       			$game_status = $this->getGameRecordsStatus($row->game_status);

                $extra_info=[
                	'trans_amount' => $row->real_bet_amount,
                	'table' => $row->external_uniqueid,
                    'status' => $game_status,
                	'note' => $row->game_details,
                	'bet_details' => $row->bet_details,
                	'bet_type'=>((strtolower($row->game_code)) == self::PARLAY_GAME) ? 'Combo Bets' : 'Single Bets',
                ];

                $this->syncGameLogs(
                    $game_type_id,
                    $game_description_id,
                    $row->game_code,
                    $row->game_type,
                    $row->game,
                    $player_id,
                    $row->username,
                    $row->bet_amount,
                    $row->result_amount,
                    null, # win_amount
                    null, # loss_amount
                    $row->after_balance, # after_balance
                    0, # has_both_side
                    $row->external_uniqueid,
                    $row->bet_time, //start
                    $row->game_finish_time, //end
                    $row->response_result_id,
                    Game_logs::FLAG_GAME,
                    $extra_info
                );

            }
        }

        $this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
        return $rlt;
	}

	public function logout($playerName, $password = null){
		return $this->returnUnimplemented();
	}

	public function checkLoginStatus($playerName){
		return $this->returnUnimplemented();
	}

	public function onlyTransferPositiveInteger(){
        return $this->only_transfer_positive_integer;
    }

    /**
     * sync directly to game logs
     *
     * @param  string $token
     * @return array ['success']
     */
	public function syncDirectlyAllT1GameLogs($token) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $adjust_datetime_str = $this->getValueFromSyncInfo($token, 'adjust_datetime_str');
        //observer the date format
        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
         # adjust range for GG poker
        if($this->getOriginalPlatformCode() == GGPOKER_GAME_API){
	        $startDate = $startDate->format('Y-m-d 00:00:00');
			$endDate = $endDate->format('Y-m-d 23:59:59');
		}else{
			if(empty($adjust_datetime_str)){
				$adjust_datetime_str=$this->getDatetimeAdjust();
			}
			$startDate->modify($adjust_datetime_str);
        	$startDate= $this->CI->utils->formatDateTimeForMysql($startDate);
        	$endDate= $this->CI->utils->formatDateTimeForMysql($endDate);
		}
		$this->multiplePlatformIdMap=$this->getValueFromSyncInfo($token, 'multiplePlatformIdMap');
		$this->unknownGameTypeMap=$this->getValueFromSyncInfo($token, 'unknownGameTypeMap');
		//use_bet_time > ignore_public_sync > config
		if($this->existsValueFromSyncInfo($token, 'use_bet_time')){
        	$this->always_manually_sync_t1_gamegateway = $this->getValueFromSyncInfo($token, 'use_bet_time');
		}else if($this->existsValueFromSyncInfo($token, 'ignore_public_sync')){
	        $this->always_manually_sync_t1_gamegateway = $this->getValueFromSyncInfo($token, 'ignore_public_sync');
		}else{
        	//try get config
        	$this->always_manually_sync_t1_gamegateway = $this->CI->utils->getConfig('always_manually_sync_t1_gamegateway');
        }

        if(empty($this->multiplePlatformIdMap)){
	        return array('success'=>false, 'error_message'=>'multiplePlatformIdMap is empty');
        }

        $error_message=null;
        $normalResult=null;
        $otherResult=null;
		//pagination, start from 1
        $page = 1;
		$this->CI->utils->debug_log('---------------try get settled game logs', $this->multiplePlatformIdMap, $startDate, $endDate,
			$adjust_datetime_str, $page, self::GAME_HISTORY_STATUS_NORMAL, $this->always_manually_sync_t1_gamegateway);
		$successOthers=false;
		$successNormal=$this->syncDirectlyAllPaginate($startDate, $endDate, $page, self::GAME_HISTORY_STATUS_NORMAL, $normalResult);
		if(!$successNormal){
			$error_message='sync GAME_HISTORY_STATUS_NORMAL failed';
		}else if($this->enable_sync_t1_unsettle){
			$this->CI->utils->debug_log('---------------try get unsettled game logs', $this->multiplePlatformIdMap, $startDate, $endDate,
				$adjust_datetime_str, $page, self::GAME_HISTORY_STATUS_OTHERS, $this->always_manually_sync_t1_gamegateway);
			$successOthers=$this->syncDirectlyAllPaginate($startDate, $endDate, $page, self::GAME_HISTORY_STATUS_OTHERS, $otherResult);
			if(!$successOthers){
				$error_message='sync GAME_HISTORY_STATUS_OTHERS failed';
			}
		}else{
			//disable unsettle
			$successOthers=true;
		}
		$success= $successNormal && $successOthers;

        return array('success'=>$success, 'error_message'=>$error_message, 'normalResult'=>$normalResult, 'otherResult'=>$otherResult);
	}

	public function syncDirectlyAllPaginate($startDate, $endDate, $page, $query_status, &$result){

		$this->CI->utils->debug_log('start syncPaginate================',$startDate, $endDate, $page, $query_status);

		$result=['total_pages'=>0, 'applied_data_count'=>0, 'api_data_count'=>0];
		$done=false;
		while (!$done) {
		    $rlt = $this->callT1GamelogsApi($startDate, $endDate, $page, $query_status);
		    if($rlt['success']){

		    	$this->CI->utils->info_log('sync game logs api result', $rlt);

		    	if($rlt['total_pages']>$rlt['current_page']){
		    		$page=$rlt['current_page']+1;
		    		$this->CI->utils->debug_log('not done================',$rlt['total_pages'],$rlt['current_page']);
		    	}else{
		    		$done=true;
		    		$this->CI->utils->debug_log('done===================',$rlt['total_pages'],$rlt['current_page']);
		    	}
		    	$result['applied_data_count']+=$rlt['applied_data_count'];
		    	$result['api_data_count']+=$rlt['api_data_count'];
		    	if(isset($rlt['total_pages'])){
		    		$result['total_pages']=$rlt['total_pages'];
		    	}
		    	$success=true;
		    }else{
		    	$success=false;
		    	$done=true;
		    	$this->CI->utils->error_log('sync game logs api error', $rlt);
		    }
        }

        return $success;
	}

    public function callT1GamelogsApi($startDate, $endDate, $page, $query_status){
        // $this->generateToken(false); #generate token

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCallT1GamelogsApi',
            'from' => $startDate,
            'to' => $endDate,
        );

        $use_bet_time=$this->always_manually_sync_t1_gamegateway;

        $date_mode = $use_bet_time ? 'by_bet_time' : 'by_last_update_time';

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no api token'];
		}
		//key is original , value is current code
		$platformIdArray=array_keys($this->multiplePlatformIdMap);
        $params = [
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'multiple_game_platform'=>$platformIdArray,
            // 'game_platform_id' => $this->getOriginalPlatformCode(),
            'from' => $startDate,
            'to' => $endDate,
            'page_number' => $page,
            'size_per_page' => $this->size_per_page,
            'game_history_status' => $query_status,
            'date_mode' => $date_mode
        ];

        if($this->currency){
			$params['currency'] = $this->currency;
		}

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_syncMultiplePlatformGameRecords, $params, $context);
    }

    public function processResultForCallT1GamelogsApi($params) {

        $this->CI->load->model(array('game_logs', 'original_game_logs_model'));

        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $from = $this->getVariableFromContext($params, 'from');
        $to = $this->getVariableFromContext($params, 'to');
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $dataCount = 0;
        $apiDataCount=0;
        $result = [];

        if($success && isset($resultArr['detail'])){
        	$detail=$resultArr['detail'];
	        $gameRecords = isset($detail['game_history']) ? $detail['game_history'] : null;
	        if(!empty($gameRecords)){
	        	$apiDataCount=count($gameRecords);
	        	// $this->CI->utils->debug_log('t1 gameRecords ==>', $gameRecords);
	        	//check or generate md5
	        	$this->batchProcessBeforeSplitRows($gameRecords, $responseResultId);

	        	//$gameRecords to $insertRows, $updateRows
	        	//will set game_logs_id to updateRows
	            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->generateInsertAndUpdateForGameLogs(
	            	$gameRecords, 'external_uniqueid', $this->getPlatformCode());
	            unset($gameRecords);
	        	// if($this->is_update_original_row){
	        		// $availableRows = $gameRecords;
	        	// }else{
	        	// 	$availableRows = $this->CI->t1games_game_logs->getAvailableRows($gameRecords,$this->getOriginalPlatformCode());
	        	// }

	            //preprocessOriginalRowForGameLogs
	            //then

		        // $cnt = 0;

		        if(!empty($insertRows)){
		        	$this->batchPreprocessOriginalRowForGameLogs($insertRows);
		        }
		        if(!empty($updateRows)){
		        	$this->batchPreprocessOriginalRowForGameLogs($updateRows);
		        }

		        // $this->CI->utils->debug_log($insertRows);

		        // $this->CI->utils->debug_log($updateRows);
		        $makeParamsForInsertOrUpdateGameLogsRow=[$this, 'makeParamsForInsertOrUpdateGameLogsRow'];
		        $api=$this;
		        // only for lottery, spoorts or any game which exists status change, it will delete game_logs or game_logs_unsettle record depends status
		        // and insert/update game_logs_unsettle
		        // and update $insertRows and $updateRows
		        // no need on live casino/slots
		        if($this->enabled_game_logs_unsettle && (!empty($insertRows) || !empty($updateRows)) ){
		            $this->CI->game_logs->processUnsettleGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                $insertRows, $updateRows);
		        }

		        if(!empty($insertRows)){
		            $success=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                'insert', $insertRows, $dataCount);
		        }
		        unset($insertRows);
		        if($success && !empty($updateRows)){
		            $success=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                'update', $updateRows, $dataCount);
		        }
		        unset($updateRows);
		        // unset($unknownGame);

	        }
	        $result['total_pages']=isset($detail['total_pages']) ? $detail['total_pages'] : null;
	        $result['current_page']=isset($detail['current_page']) ? $detail['current_page'] : null;
	        $result['total_rows_current_page']=isset($detail['total_rows_current_page']) ? $detail['total_rows_current_page'] : null;
        }
        unset($resultArr);

        $result['applied_data_count'] = $dataCount;
        $result['api_data_count'] = $apiDataCount;

        return array($success, $result);
    }

	private function getGameHistoryStatus($status) {
		$this->CI->load->model(array('game_logs'));
		$status = strtolower($status);

		switch ($status) {
			case self::GAME_HISTORY_STATUS_OTHERS:
				$status = Game_logs::STATUS_PENDING;
				break;
			case self::GAME_HISTORY_STATUS_NORMAL:
				$status = Game_logs::STATUS_SETTLED;
				break;
			default:
				$status = Game_logs::STATUS_SETTLED;
				break;
		}

		return $status;
	}

    /**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){

    	//overwrite game platform id
        $extra_info=[
        	// 'external_log_id'=>$row['response_result_id'],
        	't1_game_platform_id'=>$row['t1_game_platform_id'],
        	'odds' => $row['odds'],
        	'odds_type' => $row['odds_type']
        ];

		$bet_details = $row['bet_details'];
		//should be string
		if (!empty($bet_details) && is_string($bet_details)){ // && $this->isJsonData($bet_details)) {
			$bet_details =  json_decode($row['bet_details'], true);
		}
		if(empty($row['updated_at'])){
			$row['updated_at']=$row['payout_time'];
		}

        return [
            'game_info'=>['game_type_id'=>$row['game_type_id'], 'game_description_id'=>$row['game_description_id'],
                'game_code'=>$row['game_code'], 'game_type'=>null, 'game'=>$row['game_name']],
            'player_info'=>['player_id'=>$row['player_id'], 'player_username'=>$row['username']],
            'amount_info'=>['bet_amount'=>$row['effective_bet_amount'], 'result_amount'=>$row['result_amount'],
                'bet_for_cashback'=>$row['effective_bet_amount'],
                'real_betting_amount'=> ($row['real_bet_amount']===null ? $row['effective_bet_amount'] : $row['real_bet_amount']),
                'win_amount'=>null, 'loss_amount'=>null, 'after_balance'=>$row['after_balance']],
            'date_info'=>['start_at'=>$row['bet_time'], 'end_at'=>$row['payout_time'], 'bet_at'=>$row['bet_time'],
                'updated_at'=>$row['updated_at']],
            'flag'=>Game_logs::FLAG_GAME,
            'status'=>$row['status'],
            'additional_info'=>['has_both_side'=>0, 'external_uniqueid'=>$row['external_uniqueid'], 'round_number'=>$row['round_number'],
                'md5_sum'=>$row['md5_sum'], 'response_result_id'=>$row['response_result_id'], 'sync_index'=>null,
                'bet_type'=>$row['bet_type'] ],
            'bet_details'=> $bet_details,
            'extra'=>$extra_info,
            //from exists game logs or game_logs_unsettle_id
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];

    }

    /**
     *
     * batch setup player id and game_description_id and game_type_id
     *
     * @param  array &$rows
     */
    public function batchPreprocessOriginalRowForGameLogs(array &$rows){

    	// original code to current code
    	// $this->multiplePlatformIdMap
    	// get all username and game_code and game platform id
        $batchGameUsername=[];
        $batchGameCode=[];
        $cntOfRows=count($rows);
        for($i=0; $i<$cntOfRows; $i++) {
        	// $row=$rows[$i];
    		//convert original code to t1 code
    		$originalPlatformId=$rows[$i]['game_platform_id'];
    		if(isset($this->multiplePlatformIdMap[$originalPlatformId])){
    			// batch username
    			// batch game_code
    			$t1PlatformId=$this->multiplePlatformIdMap[$originalPlatformId];
	        	$key=$t1PlatformId.'-'.$rows[$i]['username'];
    			$batchGameUsername[]=$key;

	    		//get game key, game code is external_gameid
	        	$gameKey=$t1PlatformId.'-'.$rows[$i]['game_code'];
	        	$batchGameCode[]=$gameKey;
    		}else{
    			//doesn't exist , should remove
        		$this->CI->utils->error_log('wrong game_platform_id and remove it', $rows[$i]['game_platform_id']);
        		unset($rows[$i]);
    		}

    	}
    	$usernamePlayerIdMap=null;
    	$gameCodeGameDescIdMap=null;
    	$this->CI->load->model(['game_provider_auth','game_description_model', 'game_logs']);
    	//batchGameUsername is unique
    	if(!empty($batchGameUsername)){
    		//only run one sql
	    	$usernamePlayerIdMap=$this->CI->game_provider_auth->batchGetPlayerIdByGameUsernames($batchGameUsername);
    	}
        //try process unknown game
    	if(!empty($batchGameCode)){
    		$gameCodeGameDescIdMap=$this->CI->game_description_model->batchGetGameDescIdByCode($batchGameCode);
    	}

    	unset($batchGameUsername);
    	unset($batchGameCode);
        $cntOfRows=count($rows);
        for($i=0; $i<$cntOfRows; $i++) {
        	//use lang to process game name
        	if(isset($rows[$i]['game_name'])){
        		$rows[$i]['game_name']=lang($rows[$i]['game_name']);
        	}
        	// $row=$rows[$i];
    		$originalPlatformId=$rows[$i]['game_platform_id'];
    		$t1PlatformId=$this->multiplePlatformIdMap[$originalPlatformId];
        	$key=$t1PlatformId.'-'.$rows[$i]['username'];
        	if(isset($usernamePlayerIdMap[$key])) {
        		$rows[$i]['player_id']=$usernamePlayerIdMap[$key];
        		$rows[$i]['t1_game_platform_id']=$t1PlatformId;

				//process game code and unknown game
		        $gameKey=$t1PlatformId.'-'.$rows[$i]['game_code'];
	        	if(isset($gameCodeGameDescIdMap[$gameKey])) {
	        		$rows[$i]['game_description_id']=$gameCodeGameDescIdMap[$gameKey]['game_description_id'];
	        		$rows[$i]['game_type_id']=$gameCodeGameDescIdMap[$gameKey]['game_type_id'];
	        	}else{
	        		if(isset($this->unknownGameTypeMap[$t1PlatformId])){
		        		$unknownGame=$this->unknownGameTypeMap[$t1PlatformId];

		        		//it's unknown game
		        		$gameDescId=$this->CI->game_description_model->processUnknownGame($t1PlatformId,
		        			$unknownGame['game_type_id'], $rows[$i]['game_name'], $rows[$i]['game_code']);
		        		if(empty($gameDescId)){
		        			$gameDescId=$unknownGame['game_description_id'];
		        		}
		        		$rows[$i]['game_description_id']=$gameDescId;
		        		$rows[$i]['game_type_id']=$unknownGame['game_type_id'];
	        		}else{
	        			//error, remove it
		        		$this->CI->utils->error_log('lost unknown game and remove it', $t1PlatformId, $rows[$i]['game_name'], $rows[$i]['game_code'], $originalPlatformId);
		        		//remove it
		        		unset($rows[$i]);
	        		}
	        	}

        	}else{
        		//lost username
        		$this->CI->utils->error_log('lost username and remove it', $rows[$i]['username'], $originalPlatformId);
        		//remove it
        		unset($rows[$i]);
        	}

        }

        unset($usernamePlayerIdMap);
        unset($gameCodeGameDescIdMap);

        //detail_status to status and response_result_id
    //     $cntOfRows=count($rows);
    //     for($i=0; $i<$cntOfRows; $i++) {
    //     // 	//same status
    //     // 	$rows[$i]['status']=$rows[$i]['detail_status'];
	   //      //too big , so process it here
	   //      if(!empty($rows[$i]['bet_details']) && is_string($rows[$i]['bet_details'])){
				// // prevent double encode data
				// if (!$this->isJsonData($rows[$i]['bet_details'])) {
				// 	$this->CI->utils->debug_log('encodeJson bet_details');
				// 	$rows[$i]['bet_details']=$this->CI->utils->encodeJson($rows[$i]['bet_details']);;
				// }
	   //      }
	   //     //  $rows[$i]['response_result_id']=$responseResultId;
    //     }

    }

	// public function isJsonData($string) {
	// 	json_decode($string);
	// 	return (json_last_error() == JSON_ERROR_NONE);
	// }

    const MD5_FIELDS_FOR_API=[
        'external_uniqueid', 'effective_bet_amount', 'result_amount', 'real_bet_amount', 'payout_time',
        'username', 'bet_time', 'game_platform_id', 'game_code', 'updated_at', 'detail_status', 'rent'];

    const MD5_FLOAT_AMOUNT_FIELDS=[
    	'effective_bet_amount', 'result_amount', 'real_bet_amount', 'rent',
    ];

    /**
     * batchProcessBeforeSplitRows
     * @param  array &$rows
     * @param  int $responseResultId
     */
    public function batchProcessBeforeSplitRows(array &$rows, $responseResultId){
    	$this->CI->load->model(['game_logs']);
        $cntOfRows=count($rows);
        for($i=0; $i<$cntOfRows; $i++) {
	        //generate md5 sum if empty
	        if(empty($rows[$i]['md5_sum'])){
	        	//any model, it's on base model
	        	$rows[$i]['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($rows[$i], self::MD5_FIELDS_FOR_API, self::MD5_FLOAT_AMOUNT_FIELDS);
	        }

	        if(is_null($rows[$i]['effective_bet_amount'])){
	        	$rows[$i]['effective_bet_amount']=0;
	        }
	        if(is_null($rows[$i]['real_bet_amount'])){
	        	$rows[$i]['real_bet_amount']=$rows[$i]['effective_bet_amount'];
	        }
	        if(is_null($rows[$i]['result_amount'])){
	        	$rows[$i]['result_amount']=0;
	        }

	        //detail_status to status and response_result_id
        	//same status
        	$rows[$i]['status']=$rows[$i]['detail_status'];
	        $rows[$i]['response_result_id']=$responseResultId;
        }
    }

	public function syncDirectlyT1GameLogsStream($token) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');

        $adjust_datetime_str = $this->getValueFromSyncInfo($token, 'adjust_datetime_str');
        //observer the date format
        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
		$this->multiplePlatformIdMap=$this->getValueFromSyncInfo($token, 'multiplePlatformIdMap');
		$this->unknownGameTypeMap=$this->getValueFromSyncInfo($token, 'unknownGameTypeMap');

        if(empty($this->multiplePlatformIdMap)){
	        return array('success'=>false, 'error_message'=>'multiplePlatformIdMap is empty');
        }

        $error_message=null;
        $normalResult=null;
        $otherResult=null;

		$this->CI->utils->debug_log('---------------try get settled game logs', $this->multiplePlatformIdMap, $startDate,
			$adjust_datetime_str, self::GAME_HISTORY_STATUS_NORMAL);
		$successOthers=false;
		$normalResult=$this->callT1GamelogsStreamApi($startDate, self::GAME_HISTORY_STATUS_NORMAL);
		$successNormal=$normalResult['success'];
		if(!$successNormal){
			$error_message='sync GAME_HISTORY_STATUS_NORMAL failed';
		}else if($this->enable_sync_t1_unsettle){
			$this->CI->utils->debug_log('---------------try get unsettled game logs', $this->multiplePlatformIdMap, $startDate,
				$adjust_datetime_str, self::GAME_HISTORY_STATUS_OTHERS);
			$otherResult=$this->callT1GamelogsStreamApi($startDate, self::GAME_HISTORY_STATUS_OTHERS);
			$successOthers=$otherResult['success'];
			if(!$successOthers){
				$error_message='sync GAME_HISTORY_STATUS_OTHERS failed';
			}
		}else{
			//disable unsettle
			$successOthers=true;
		}
		$success= $successNormal && $successOthers;

        return ['success'=>$success, 'error_message'=>$error_message,
        	'normalResult'=>$normalResult, 'otherResult'=>$otherResult];
	}

    public function callT1GamelogsStreamApi($startTime, $query_status){
        // $this->generateToken(false); #generate token

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCallT1GamelogsStreamApi',
        );

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no api token'];
		}
		//key is original , value is current code
		$platformIdArray=array_keys($this->multiplePlatformIdMap);
        $params = [
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'multiple_game_platform'=>$platformIdArray,
            // 'game_platform_id' => $this->getOriginalPlatformCode(),
            'last_update_time' => $startTime->format('Y-m-d H:i:s'),
            'min_size' => $this->size_per_page,
            'game_history_status' => $query_status,
        ];

        if($this->currency){
			$params['currency'] = $this->currency;
		}

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_syncMultiplePlatformStreamGameHistory, $params, $context);
    }

    public function processResultForCallT1GamelogsStreamApi($params) {

        $this->CI->load->model(array('game_logs', 'original_game_logs_model'));

        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $dataCount = 0;
        $apiDataCount=0;
        $result = [];

        if($success && isset($resultArr['detail'])){
        	$detail=$resultArr['detail'];
	        $gameRecords = isset($detail['game_history']) ? $detail['game_history'] : null;
	        if(!empty($gameRecords)){
	        	$apiDataCount=count($gameRecords);
	        	// $this->CI->utils->debug_log('t1 gameRecords ==>', $gameRecords);
	        	//check or generate md5
	        	$this->batchProcessBeforeSplitRows($gameRecords, $responseResultId);

	        	//$gameRecords to $insertRows, $updateRows
	        	//will set game_logs_id to updateRows
	            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->generateInsertAndUpdateForGameLogs(
	            	$gameRecords, 'external_uniqueid', $this->getPlatformCode());
	            unset($gameRecords);
	        	// if($this->is_update_original_row){
	        		// $availableRows = $gameRecords;
	        	// }else{
	        	// 	$availableRows = $this->CI->t1games_game_logs->getAvailableRows($gameRecords,$this->getOriginalPlatformCode());
	        	// }

	            //preprocessOriginalRowForGameLogs
	            //then

		        // $cnt = 0;

		        if(!empty($insertRows)){
		        	$this->batchPreprocessOriginalRowForGameLogs($insertRows);
		        }
		        if(!empty($updateRows)){
		        	$this->batchPreprocessOriginalRowForGameLogs($updateRows);
		        }

		        // $this->CI->utils->debug_log($insertRows);

		        // $this->CI->utils->debug_log($updateRows);
		        $makeParamsForInsertOrUpdateGameLogsRow=[$this, 'makeParamsForInsertOrUpdateGameLogsRow'];
		        $api=$this;
		        // only for lottery, spoorts or any game which exists status change, it will delete game_logs or game_logs_unsettle record depends status
		        // and insert/update game_logs_unsettle
		        // and update $insertRows and $updateRows
		        // no need on live casino/slots
		        if($this->enabled_game_logs_unsettle && (!empty($insertRows) || !empty($updateRows)) ){
		            $this->CI->game_logs->processUnsettleGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                $insertRows, $updateRows);
		        }

		        if(!empty($insertRows)){
		            $success=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                'insert', $insertRows, $dataCount);
		        }
		        unset($insertRows);
		        if($success && !empty($updateRows)){
		            $success=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
		                'update', $updateRows, $dataCount);
		        }
		        unset($updateRows);
		        // unset($unknownGame);

	        }
	        // $result['total_pages']=isset($detail['total_pages']) ? $detail['total_pages'] : null;
	        // $result['current_page']=isset($detail['current_page']) ? $detail['current_page'] : null;
	        $result['total_count']=isset($detail['total_count']) ? $detail['total_count'] : null;
        	$result['next_datetime']=$detail['next_datetime'];
        	$result['last_datetime']=$detail['last_datetime'];
        }
        unset($resultArr);

        $result['applied_data_count'] = $dataCount;
        $result['api_data_count'] = $apiDataCount;

        return array($success, $result);
    }

    /**
     *
     * Used for Bet Details in Game Logs.
     * OGP-13506 -> T1MGPLUS_API
     *
     */

    public function queryBetDetailLink($playerUsername, $betid = null, $extra = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);

		$api_token=$this->getAvailableApiToken();
		if(empty($api_token)){
			return ['success'=>false, 'error_message'=>'no auth token'];
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryBetDetailLink',
			'gameUsername' => $gameUsername,
			'betUid' => $betid,
		);

		$params = array(
			'auth_token' => $api_token,
			'merchant_code' => $this->api_merchant_code,
			'game_platform_id' => $this->getOriginalPlatformCode(),
			'username' => $gameUsername,
			'bet_detail_id' => $betid,
		);

		if($this->currency){
			$params['currency'] = $this->currency;
		}

		return $this->callApi(self::API_queryBetDetailLink, $params, $context);
	}

	public function processResultForQueryBetDetailLink($params){
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = @$this->getVariableFromContext($params, 'gameUsername');
		$betUid = @$this->getVariableFromContext($params, 'betUid');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = array('url'=>'');

		if($success){
			$result['url'] = $resultArr['detail']['original_reason']['url'];
		}

		return array($success, $result);
	}

}