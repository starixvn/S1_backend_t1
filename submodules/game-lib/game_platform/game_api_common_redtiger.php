<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
	* API NAME: REDTIGER
	*
	* @category Game_platform
	* @version not specified
	* @copyright 2013-2019 TripleOneTech
	* @integrator @andy.php.ph
**/

class Game_api_common_redtiger extends Abstract_game_api {
	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';
	const GRANT_TYPE = "client_credentials";
	const SCOPE = "playerapi";
	const REDTIGER_PROVIDER_CODE_OLD = 'TGP'; 	// old provider code - for prod
	const REDTIGER_PROVIDER_CODE_NEW = 'SBG'; 	// new provider code - for both staging and prod (march 2, 2020)

	# Fields in redtiger_idr_game_logs we want to detect changes for update
    const MD5_FIELDS_FOR_ORIGINAL=[
        'ugsbetid',//(string) TGP internal bet ID. This is the unique key that can be used to identify a specific bet.
        'txid',//(string) Identifier of the transaction or bet from the game provider.
        'betid',//(string) External bet ID (from a game provider). Do not use this field to identify a specific bet record, because there could be the same bet ID from different game providers.
        'roundid',//(string) Game Provider’s identifier of the round in which the game transactions take place.
        'roundstatus',//(string) Status of the round at the time of reporting. One of: Open/Closed
        'userid',//(string) Unique identifier of the player.
        'username',//(string) Display name of the player.
        'riskamt',//(float) Bet placement amount. The value is negative, as it represents a debit from the player’s account.
        'winamt',//(float) Winning amount of the bet. The value is positive when a win is credited to the player’s account, or zero if no win occurs.
        'winloss',//(float) Net return of the bet to the player
        'result_amount',//(float) Result that includes jackpot amount
        'beforebal',//(float) Amount of the player’s balance before the first transaction of the round
        'postbal',//(float) Amount of the player’s balance after the last transaction of the round.
        'cur',//(string) Currency that the player is using in the TGP system
        'gameprovider',//(string) Name of the game provider. e.g., “Sunbet”
        'gameprovidercode',//(string) Game provider code, e.g., “SB”
        'gamename',//(string) Name of the game, e.g., “Live Dealer Baccarat”
        'gameid',//(string) Game provider’s unique identifier of their game.
        'platformtype',//(string) Desktop,mobile or mini game
        'ipaddress',//(string) IP address of the player reported by the game provider
        'bettype',//(string) Always “PlaceBet”
        'playtype',//(string) Play type of the game, defined by game providers.
        'playertype',//(int) 1 = Real Player, 2 = Operator Test Player, 4 = TGP Test Player
        'turnover',//(float) Total turnover in the round, across all bets.
        'validbet',//(float) Total valid bet in the round, across all bets.
        'jackpotcontribution',//(float) Amount contributed to Jackpot in player's currency
        'jackpotid',//(string) The ID of the jackpot pool hit
        'jackpotwinamt',//(float) Jackpot winning amount of the bet
    ];

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS = [
        'riskamt',
        'winamt',
        'beforebal',
        'postbal',
        'turnover',
        'validbet',
        'jackpotcontribution',
    ];

    # Fields in game_logs we want to detect changes for merge, and when redtiger_idr_game_logs.md5_sum is empty
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

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        'bet_amount',
        'valid_bet',
        'result_amount',
    ];

	const URI_MAP = array(
		self::API_createPlayer => '/api/player/authorize',
		self::API_queryPlayerBalance => '/api/player/balance',
		self::API_depositToGame => '/api/wallet/credit',
		self::API_withdrawFromGame => '/api/wallet/debit',
		self::API_generateToken => '/api/oauth/token',
		self::API_login => '/api/player/authorize',
		self::API_logout => '',
		self::API_queryForwardGame => '/gamelauncher',
		self::API_syncGameRecords => '/api/history/bets',
		self::API_getGameProviderGamelist => '/api/games',
	);

	const ORIGINAL_GAMELOGS_TABLE = "redtiger_game_logs";
	const DESKTOP_PLATFORM_TYPE = 0;
	const MOBILE_PLATFORM_TYPE = 1;
	const MINIGAME_PLATFORM_TYPE = 4;
	const ICON_RESOLUTION_343 = "343x200";

	public function __construct() {
		parent::__construct();

		$this->original_gamelogs_table = self::ORIGINAL_GAMELOGS_TABLE;
		$this->api_url = $this->getSystemInfo('url');
		$this->client_id = $this->getSystemInfo('key');
		$this->client_secret = $this->getSystemInfo('secret');
		$this->game_launch_url = $this->getSystemInfo('game_launch_url');
		$this->loginurl = $this->getSystemInfo('loginurl');
		$this->cashierurl = $this->getSystemInfo('cashierurl');
		$this->helpurl = $this->getSystemInfo('helpurl');
		$this->istestplayer = $this->getSystemInfo('istestplayer',false);
		$this->gamelist_testplayer = $this->getSystemInfo('gamelist_testplayer','testt1dev');
		$this->betlimitid = $this->getSystemInfo('betlimitid',1);
		$this->launch_game_bcode = $this->getSystemInfo('launch_game_bcode',"Ent22");
		$this->generated_token = null;
	}

	/**
	 * will check timeout, if timeout then call again
	 * @return token
	 */
    public function getAvailableApiToken(){
    	$result = $this->getCommonAvailableApiToken(function(){
           return $this->generateToken();
        });
        $this->utils->debug_log("REDTIGER Available Token: ".$result);
        return $result;
    }

    /**
	 * Generate Access Token
	 *
	 * Token will be invalid each 60 minutes
	 */
	public function generateToken()
	{
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultGenerateToken',
		);

		$params = array(
			'grant_type' => self::GRANT_TYPE,
			'scope' => self::SCOPE,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
		);

		$this->utils->debug_log("REDTIGER: Generate Token");
		$this->method = self::METHOD_POST;
		return $this->callApi(self::API_generateToken, $params, $context);
	}

	public function processResultGenerateToken($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		if($success){
			$api_token = @$resultArr['access_token'];
			# Token will be invalid each 30 minutes
			$token_timeout = new DateTime($this->utils->getNowForMysql());
			$minutes = ((int)$resultArr['expires_in']/60)-1;
			$token_timeout->modify("+".$minutes." minutes");
			$result['api_token']=$api_token;
			$result['api_token_timeout_datetime']=$token_timeout->format('Y-m-d H:i:s');
		}

		return array($success,$result);
	}

	public function getPlatformCode()
	{
		return $this->returnUnimplemented();
	}

	public function generateUrl($apiName, $params)
	{
		$apiUri = self::URI_MAP[$apiName];
		$url = $this->api_url.$apiUri;

		if($this->method == self::METHOD_GET){
			return $url.'?'.http_build_query($params);
		}

		return $url;
	}

	protected function customHttpCall($ch, $params)
	{
        if ($this->method == self::METHOD_POST) {
            $data_json = json_encode($params);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }
    }

    protected function getHttpHeaders($params)
	{
		$headers['Content-Type'] = 'application/json';
		$headers['X-Tgp-Accept'] = 'application/json';
		$headers['Authorization'] = 'Bearer '.$this->generated_token;
		return $headers;
	}

	public function processResultBoolean($responseResultId, $resultArr, $statusCode)
	{
		$success = false;
		if((@$statusCode == 200 || @$statusCode == 201)){
			$success = true;
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('REDTIGER got error ', $responseResultId,'result', $resultArr);
		}
		return $success;
	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null)
	{
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerId' => $playerId,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'ipaddress' => $this->CI->utils->getIP(),
			'username' => $gameUsername,
			'userid' => $gameUsername,
			'lang' => $this->processPlayerLanguageForParams($this->getPlayerDetails($playerId)->language),
			'betlimitid' => $this->betlimitid,
			'cur' => $this->currency_type,
			'istestplayer' => $this->istestplayer,
			'platformtype' => self::DESKTOP_PLATFORM_TYPE
		);

		if($this->loginurl){
			$params['loginurl'] = $this->loginurl;
		}
		if($this->cashierurl){
			$params['cashierurl'] = $this->cashierurl;
		}
		if($this->helpurl){
			$params['helpurl'] = $this->helpurl;
		}

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_POST;
			return $this->callApi(self::API_createPlayer, $params, $context);
		}
	}

	private function processPlayerLanguageForParams($lang){
		switch ($lang) {
			case "Chinese": 
			case "zh-CN": 
				return "zh-CN"; 
				break;
			case "English": 
			case "en-US": 
				return "en-US"; 
				break;
			case "Japanese": 
			case "ja-JP": 
				return "ja-JP"; 
				break;
			case "Korean": 
			case "ko-KR": 
				return "ko-KR"; 
				break;
			case "Thai": 
			case "th-TH": 
				return "th-TH"; 
				break;
			case "Vietnamese": 
			case "vi-VN": 
				return "vi-VN"; 
				break;
			case "Indonesian":
			case "id-ID": 
				return "id-ID"; 
				break;

			default:
				return "zh-CN";
				break;
		}
	}

	public function processResultForCreatePlayer($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		$result = ['player' => $gameUsername];
		if($success){
			# update flag to registered = true
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
	        $result['exists'] = true;
		}
		return array($success, $result);
	}

	public function queryPlayerBalance($playerName)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'gameUsername' => $gameUsername
		);

		$params = array(
			'cur' => $this->currency_type,
			'userid' => $gameUsername,
		);

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_GET;
			return $this->callApi(self::API_queryPlayerBalance, $params, $context);
		}
	}

	public function processResultForQueryPlayerBalance($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

		if($success){
			$result['balance'] = $resultArr['bal'];
		}

		return array($success, $result);
	}

	public function batchQueryPlayerBalance($playerNames, $syncId = null)
	{
        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }
        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }

	public function depositToGame($playerName, $amount, $transfer_secure_id=null)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = empty($transfer_secure_id) ? 'TD'.uniqid() : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        );

		$timestamp = new DateTime($this->utils->getNowForMysql());
		$ts = $timestamp->format("Y-m-d\TH:i:s")."+00:00";

		$params = array(
			'cur' => $this->currency_type,
			'userid' => $gameUsername,
			'amt' => $this->dBtoGameAmount($amount),
			'txid' => $external_transaction_id,
			'timestamp' => $ts,
		);

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_POST;
			return $this->callApi(self::API_depositToGame, $params, $context);
		}
	}

	public function processResultForDepositToGame($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

		if ($success) {
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasons($statusCode);
        }

        return array($success, $result);
	}

	private function getReasons($statusCode)
	{
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

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = empty($transfer_secure_id) ? 'TW'.uniqid() : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        );

		$timestamp = new DateTime($this->utils->getNowForMysql());
		$ts = $timestamp->format("Y-m-d\TH:i:s")."+00:00";

		$params = array(
			'cur' => $this->currency_type,
			'userid' => $gameUsername,
			'amt' => $this->dBtoGameAmount($amount),
			'txid' => $external_transaction_id,
			'timestamp' => $ts,
		);

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_POST;
			return $this->callApi(self::API_withdrawFromGame, $params, $context);
		}
	}

	public function processResultForWithdrawFromGame($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

		if ($success) {
			$result['external_transaction_id'] = $external_transaction_id;
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasons($statusCode);
        }

        return array($success, $result);
	}

	/*
	 *	To Launch Game
	 *
	 *  Desktop Game launch URL
	 *  ~~~~~~~~~~~~~~~~~~~~~~~
	 *
	 * 	Note: Game Type here is not actual game_type like ('slots') this game type is for 'gpcode' param of launch game api
	 *  Format: gotogame/<game_provider_id>/<game_code>/<game_mode>/<game_type>
	 *  Ex. Url: gotogame/5450/Lion_Dance/real/TGP
	 *
	 *  Remarks: language will get from player details,if you need to invoke language in the url add it as a parameter
	 *  Ex: gotogame/5450/Lion_Dance/real/TGP/null/en-US
	 *
	 *  Mobile Game Launch URL
	 *  ~~~~~~~~~~~~~~~~~~~~~~
	 *
	 *  Format: gotogame/5450/<game_code>/<game_mode>/<sub_game_provider>/<language>/<extra>/<is_redirect>/<is_mobile>
	 *
	 *  Ex.
	 *  	TGP Url: gotogame/5450/Lion_Dance/real/TGP/null/zh-cn/null/false/true
	 *  	SB Url: gotogame/5450/Lion_Dance/real/TGP/null/en-US/null/false/true
	 *
	 *  Game Launch with BETLIMIT URL
	 *  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	 *
	 *  Used to launch a Live Dealer game.
	 *  BetLimit Option: 1-Bronze(basic), 2-Silver(upgraded), 3-Gold(high), 4-platinum(vip)
	 *
	 *  We used <extra> param to pass betlimitid (optional), having this null will use the one set in extra_info
	 *  Format: gotogame/5450/SB-Sunbet_Lobby/real/<sub_game_provider>/<language>/<extra>/<is_redirect>/<is_mobile>
	 *
	 *  Desktop URL:
	 *  gotogame/5450/Lion_Dance/real/TGP/null/zh-cn/1
	 *
	 *  Mobile URL:
	 *  gotogame/5450/Lion_Dance/real/TGP/null/zh-cn/1/false/true
	 *
	 *  Game Launch as Demo
	 *  ~~~~~~~~~~~~~~~~~~~
	 *
	 *  Appendix N - Supported Game Providers for Unauthorized Game Launcher
	 *  ESB - ESportsbull
	 *  GBA - Globalbet Asia
	 *
	 *  So according to game provider, these 2 games are allowed to have unauthorized game launch or demo
	 */
	public function queryForwardGame($playerName, $extra = null)
	{
		$playerId = $this->getPlayerIdFromUsername($playerName);

		## DEFAULT LANGUAGE
		$lang = $this->processPlayerLanguageForParams('en-US');

		#GET LANG FROM PLAYER DETAILS
		if (!empty($playerId)) {
			$lang = $this->processPlayerLanguageForParams($this->getPlayerDetails($playerId)->language);
		}


		$loginParams = [];
		#IDENTIFY IF LANGUAGE IS INVOKED IN GAME URL, THEN INCLUDE IN LOGIN TOKEN
		if(isset($extra['language'])){
			$lang = $extra['language'];
			$loginParams['lang'] = $lang;
		}

		$result = $this->login($playerName,null,$loginParams);
		$params = [
					'gpcode' => $extra['game_type'],
					'gcode' => $extra['game_code'],
				   ];

		#IDENTIFY MOBILE GAME
		$platform = self::DESKTOP_PLATFORM_TYPE;
		if(isset($extra['is_mobile'])){
			$ismobile = $extra['is_mobile'] ? true : false;
			if($ismobile){
				$platform = self::MOBILE_PLATFORM_TYPE;
			}
		}

		#IDENTIFY BETLIMITID EXISTS IN LAUNCH GAME URL
		$betlimitid = null;
		if(isset($extra['extra'])){
			$betlimitid = $extra['extra'];
		}

		#IDENTIFY LOGIN TOKEN IS SUCCESS
		if($result['success']){
			$params['token'] = $result['authtoken'];
			$params['betlimitid'] = $betlimitid ?: $this->betlimitid;
			$url = $this->game_launch_url."/gamelauncher?".http_build_query($params);
		}

		#IDENTIFY DEMO GAME
		$is_demo = false;
		if(isset($extra['game_mode'])){
			unset($params['token'],$params['betlimitid']);
			if($extra['game_mode'] == "demo" || !$extra['game_mode']){
				$is_demo = true;
			}
		}

		#IDENTIFY IF LOGIN TOKEN IS FAILED OR LAUNCH GAME IS DEMO, RESET PARAMS ARRAY & VALUE
		if(!$result['success'] || $is_demo){
			$params['platform'] = $platform;
			$params['bcode'] = $this->launch_game_bcode;
			$params['lang'] = $lang;
			$url = $this->game_launch_url."/demolauncher?".http_build_query(['gpcode' => $extra['game_type'], 'gcode' => $extra['game_code'], 'lang' => $lang]);
		}
		return ['success' => true,'url' => $url];
	}

	public function login($playerName, $password = null, $extra = null)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId = $this->getPlayerIdFromUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'gameUsername' => $gameUsername
		);

		#GET LANG FROM PLAYER DETAILS
		if (!empty($playerId)) {
			$lang = $this->processPlayerLanguageForParams($this->getPlayerDetails($playerId)->language);
		}

		if(isset($extra['lang'])){
			$lang = $extra['lang'];
		}

		$params = array(
			'ipaddress' => $this->CI->utils->getIP(),
			'username' => $gameUsername,
			'userid' => $gameUsername,
			'lang' => $lang,
			'betlimitid' => $this->betlimitid,
			'cur' => $this->currency_type,
			'istestplayer' => $this->istestplayer,
			'platformtype' => self::DESKTOP_PLATFORM_TYPE
		);

		if($this->loginurl){
			$params['loginurl'] = $this->loginurl;
		}
		if($this->cashierurl){
			$params['cashierurl'] = $this->cashierurl;
		}
		if($this->helpurl){
			$params['helpurl'] = $this->helpurl;
		}

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_POST;
			return $this->callApi(self::API_login, $params, $context);
		}
	}

	public function processResultForLogin($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		$result = [];
		if($success){
			$result = ['authtoken' => $resultArr['authtoken']];
		}
		return array($success, $result);
	}

	/*
		Game provider provided Game List API
	 */
	public function getGameList($game_type_id=null,
								$where=null,
								$limit=null,
								$offset=null,
								$full_game_list=null){

		$lang = $this->processPlayerLanguageForParams("English");
        $context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetGameList',
		);

		$platformtype = [
						 self::DESKTOP_PLATFORM_TYPE,
						 self::MOBILE_PLATFORM_TYPE,
						 self::MINIGAME_PLATFORM_TYPE
						];
		$gameListArr = [];

		$result = $this->login($this->gamelist_testplayer);
		if($result['success']){
			$params = array(
					'lang' => $lang,
					'authtoken' => $result['authtoken'],
					'iconres' => self::ICON_RESOLUTION_343,
				);
			$this->method = self::METHOD_GET;

			foreach ($platformtype as $value)
			{
				$params['platformtype'] = $value;
				$result = $this->callApi(self::API_getGameProviderGamelist, $params, $context);
				if($result['success']){
					array_push($gameListArr,$result['games']);
				}
			}
		}
		return $gameListArr;
	}

	public function processResultForGetGameList($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$statusCode);
		return array($success, ['games'=>$resultArr['games']]);
	}

	/*
	 * Note: You can only search data within the past 60 days.
	 * 7.6.3 Game History
	 */
	public function syncOriginalGameLogs($token = false)
	{
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$startDateTime = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
    	$startDateTime->modify($this->getDatetimeAdjust());
    	$endDateTime = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
    	$queryDateTimeStart = $startDateTime->format("Y-m-d\TH:i:s")."+00:00";
		$queryDateTimeEnd = $endDateTime->format('Y-m-d\TH:i:s')."+00:00";

		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncOriginalGameLogs'
        );

		$params = array(
			'startdate' => $queryDateTimeStart,
			'enddate' => $queryDateTimeEnd,
		);

		$this->generated_token = $this->getAvailableApiToken();
		if($this->generated_token){
			$this->method = self::METHOD_GET;
			return $this->callApi(self::API_syncGameRecords, $params, $context);
		}
	}

	public function processResultForSyncOriginalGameLogs($params)
	{
        $this->CI->load->model('original_game_logs_model');
		$statusCode = $this->getStatusCodeFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		$result = ['data_count' => 0];
		$gameRecords = !empty($resultArr)?$resultArr:[];

		if($success&&!empty($gameRecords))
		{
            $extra = ['response_result_id' => $responseResultId];
            $this->rebuildGameRecords($gameRecords,$extra);

            list($insertRows, $updateRows) = $this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                $this->original_gamelogs_table,
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

            if (!empty($insertRows))
            {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($insertRows);

            if (!empty($updateRows))
            {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($updateRows);
		}

		return array($success, $result);
	}

	private function rebuildGameRecords(&$gameRecords,$extra)
	{
		$newGR =[];
        foreach($gameRecords as $i => $gr)
        {
        	$win_amount = isset($gr['winamt'])?$gr['winamt']:null;
        	$bet_amount = isset($gr['validbet'])?$gr['validbet']:null;
        	$ugsbetid = isset($gr['ugsbetid'])?$gr['ugsbetid']:null;
        	$bet_result_amount = isset($gr['winloss'])?$gr['winloss']:null;
        	$jackpot_win_amount = isset($gr['jackpotwinamt'])?$gr['jackpotwinamt']:null;

        	$newGR[$i]['ugsbetid'] = $ugsbetid;
        	$newGR[$i]['txid'] = isset($gr['txid'])?$gr['txid']:null;
			$newGR[$i]['betid'] = isset($gr['betid'])?$gr['betid']:null;
			$newGR[$i]['beton'] = isset($gr['beton'])?$gr['beton']:null;
			$newGR[$i]['betclosedon'] = isset($gr['betclosedon'])?$gr['betclosedon']:null;
			$newGR[$i]['betupdatedon'] = isset($gr['betupdatedon'])?$gr['betupdatedon']:null;
			$newGR[$i]['timestamp'] = isset($gr['timestamp'])?$gr['timestamp']:null;
			$newGR[$i]['roundid'] = isset($gr['roundid'])?$gr['roundid']:null;
			$newGR[$i]['roundstatus'] = isset($gr['roundstatus'])?$gr['roundstatus']:null;
			$newGR[$i]['userid'] = isset($gr['userid'])?$gr['userid']:null;
			$newGR[$i]['username'] = isset($gr['username'])?$gr['username']:null;
			$newGR[$i]['riskamt'] = isset($gr['riskamt'])?$gr['riskamt']:null;
			$newGR[$i]['winamt'] = $win_amount;
			$newGR[$i]['winloss'] = $bet_result_amount;
			$newGR[$i]['beforebal'] = isset($gr['beforebal'])?$gr['beforebal']:null;
			$newGR[$i]['postbal'] = isset($gr['postbal'])?$gr['postbal']:null;
			$newGR[$i]['cur'] = isset($gr['cur'])?$gr['cur']:null;
			$newGR[$i]['gameprovider'] = isset($gr['gameprovider'])?$gr['gameprovider']:null;
			$newGR[$i]['gameprovidercode'] = isset($gr['gameprovidercode'])?$gr['gameprovidercode']:null;
			$newGR[$i]['gamename'] = isset($gr['gamename'])?$gr['gamename']:null;
			$newGR[$i]['gameid'] = isset($gr['gameid'])?$gr['gameid']:null;
			$newGR[$i]['platformtype'] = isset($gr['platformtype'])?$gr['platformtype']:null;
			$newGR[$i]['ipaddress'] = isset($gr['ipaddress'])?$gr['ipaddress']:null;
			$newGR[$i]['bettype'] = isset($gr['bettype'])?$gr['bettype']:null;
			$newGR[$i]['playtype'] = isset($gr['playtype'])?$gr['playtype']:null;
			$newGR[$i]['playertype'] = isset($gr['playertype'])?$gr['playertype']:null;

			# According to game provider
			# turnover basically same as bet amount
			# turnover = bet amount - jackpot contribution
			# so far we can ignore jackpot contribution
			$newGR[$i]['turnover'] = isset($gr['turnover'])?$gr['turnover']:null;
			$newGR[$i]['validbet'] = isset($gr['validbet'])?$gr['validbet']:null;
			$newGR[$i]['jackpotcontribution'] = isset($gr['jackpotcontribution'])?$gr['jackpotcontribution']:null;
			$newGR[$i]['jackpotid'] = isset($gr['jackpotid'])?$gr['jackpotid']:null;
			$newGR[$i]['jackpotwinamt'] = $jackpot_win_amount;
			$newGR[$i]['result_amount'] = $bet_result_amount + $jackpot_win_amount;

            $newGR[$i]['external_uniqueid'] = $ugsbetid;
            $newGR[$i]['response_result_id'] = $extra['response_result_id'];
        }
        $gameRecords = $newGR;
	}

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[])
    {
        $dataCount = 0;
        if(!empty($rows))
        {
            foreach ($rows as $key => $record)
            {
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal($this->original_gamelogs_table, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal($this->original_gamelogs_table, $record);
                }
                $dataCount++;
                unset($record);
            }
        }
        return $dataCount;
    }

    public function syncMergeToGameLogs($token)
    {
        $enabled_game_logs_unsettle=false;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time)
    {
        $sqlTime='`rt`.`betclosedon` >= ?
          AND `rt`.`betclosedon` <= ?';
        if($use_bet_time){
            $sqlTime='`rt`.`beton` >= ?
          AND `rt`.`beton` <= ?';
        }

        $sql = <<<EOD
			SELECT
				rt.id as sync_index,
				rt.response_result_id,
				rt.roundid as round,
				rt.userid as username,
				rt.validbet as bet_amount,
				rt.validbet as valid_bet,
				rt.result_amount,
				rt.beton as start_at,
				rt.betclosedon as end_at,
				rt.beton as bet_at,
				rt.gameid as game_code,
				rt.external_uniqueid,
				rt.postbal as after_balance,
				rt.md5_sum,
				game_provider_auth.player_id,
				gd.id as game_description_id,
				gd.game_name as game_description_name,
				gd.game_type_id
			FROM $this->original_gamelogs_table as rt
			LEFT JOIN game_description as gd ON rt.gameid = gd.external_game_id AND gd.game_platform_id = ?
			LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
			JOIN game_provider_auth ON rt.userid = game_provider_auth.login_name
			AND game_provider_auth.game_provider_id=?
			AND (rt.gameprovidercode=? OR rt.gameprovidercode=?)
			WHERE
            {$sqlTime}
EOD;

        $params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            self::REDTIGER_PROVIDER_CODE_OLD,
            self::REDTIGER_PROVIDER_CODE_NEW,
            $dateFrom,
            $dateTo,
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

	public function makeParamsForInsertOrUpdateGameLogsRow(array $row)
	{
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
                'game_type' => $row['game_type_id'],
                'game' => $row['game_code']
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['username']
            ],
            'amount_info' => [
                'bet_amount' => $row['valid_bet'],
                'result_amount' => $row['result_amount'],
                'bet_for_cashback' => $row['valid_bet'],
                'real_betting_amount' => $row['bet_amount'],
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => $row['after_balance'],
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

    public function preprocessOriginalRowForGameLogs(array &$row)
    {
        if (empty($row['game_description_id']))
        {
            $unknownGame = $this->getUnknownGame($this->getPlatformCode());
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }
        $row['status'] = Game_logs::STATUS_SETTLED;
    }

	private function getGameDescriptionInfo($row, $unknownGame)
	{
		$game_description_id = null;
		$game_name = str_replace("알수없음",$row['game_code'],
					 str_replace("不明",$row['game_code'],
					 str_replace("Unknown",$row['game_code'],$unknownGame->game_name)));
		$external_game_id = $row['game_code'];
        $extra = array('game_code' => $external_game_id,'game_name' => $game_name);

        $game_type_id = $unknownGame->game_type_id ? $unknownGame->game_type_id : null;
        $game_type = $unknownGame->game_name ? $unknownGame->game_name : self::TAG_CODE_UNKNOWN_GAME;

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}

	public function blockPlayer($playerName)
	{
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
	}

	public function unblockPlayer($playerName)
	{
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->unblockUsernameInDB($playerName);
		return array("success" => true);
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
        $success=true;
        $playerId = $this->getPlayerIdInPlayer($playerName);
        if(!empty($playerId)){
            $this->updatePasswordForPlayer($playerId, $newPassword);
        }

        return array('success' => $success);
    }

	public function isPlayerExist($playerName){
		return array(true, ['success' => true, 'exists' => true]);
    }

    public function queryTransaction($transactionId, $extra) {
		$this->unimplemented();
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

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}
}
/*end of file*/