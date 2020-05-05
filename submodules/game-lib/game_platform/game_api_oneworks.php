<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

require_once dirname(__FILE__).'/oneworks_betdetails_module.php';
require_once dirname(__FILE__).'/oneworks_mobile_app_module.php';

/**
 * General behaviors include:
 * * Gets Platform code
 * * Generates Url
 * * Generates Security Token
 * * Create Player
 * * Sets Member Setting
 * * Update Member setting
 * * Check Player info
 * * Change password
 * * Block/Unblock Player
 * * Deposit To Game
 * * Withdraw from Game
 * * Checks Fund Transfer
 * * Check Player's Balance
 * * Check Player's daily balance
 * * Check Game Records
 * * Check Forwarded Game
 * * Synchronize Original Game Logs
 * * Synchronize and Merge Oneworks to Game Logs
 * * Gets Game Description
 * Behavior Not implement:
 * * Login/Logout
 * * Update Player's Information
 * * Checks Login Status
 * * Check Total Betting Amount
 * * Check Transcactions
 * *

extra info
{
url
oneworks_vendor_id
oneworks_vendor_operator_id
oneworks_player_min_transfer
oneworks_player_max_transfer
oneworks_odds_type
oneworks_player_id_suffix
oneworks_currency
oneworks_language
oneworks_game_url
oneworks_demo_account
oneworks_secret_key
oneworks_bet_setting
oneworks_demo_url
oneworks_mobile_game_url
oneworks_recheck_last_version_key_zero_flag
oneworks_mobile_home_url
oneworks_mobile_extend_session_url
live_mode
oneworks_domains
}

 * @see Redirect redirect to game page
 *
 * @category Game API
 * @version 3.36.01
 * @copyright 2013-2018 TripleOneTech
 */
class Game_api_oneworks extends Abstract_game_api {

	use oneworks_betdetails_module;
	use oneworks_mobile_app_module;

	private $oneworks_api_url;
	private $oneworks_vendor_id;
	private $oneworks_vendor_operator_id;
	private $oneworks_operator_currency;
	private $oneworks_player_min_transfer;
	private $oneworks_player_max_transfer;
	private $oneworks_odds_type;
	private $oneworks_player_id_suffix;
	private $oneworks_currency;
	private $oneworks_language;
	private $oneworks_game_url;
	private $oneworks_demo_url;
	private $oneworks_demo_account;
	private $oneworks_secret_key;
	private $oneworks_bet_setting;
	private $oneworks_mobile_game_url;
	private $forward_sites;
	private $token_prefix;
	private $use_callback_game_launch;
	private $sync_by_trans_id;

    //means we only update when those fields are changed
    const MD5_FIELDS_FOR_ORIGINAL=[
        'trans_id', 'ticket_status', 'stake', 'winlost_amount',
        'vendor_member_id', 'transaction_time', 'winlost_datetime', 'sport_type', 'version_key', 'settlement_time'];

    const MD5_FLOAT_AMOUNT_FIELDS=[
        'stake', 'winlost_amount',];

	const STATUS_SUCCESS = '0';
	const PENDING_FUND_TRANSFER_1 = '2';
	const PENDING_FUND_TRANSFER_2 = '3';
	const SPORTSBOOK_WALLET_ID = 1;
	const MEMBER_OFFLINE = '3';
	const MEMBER_NOT_FOUND = '2';

	const START_PAGE = 0;
	const ITEM_PER_PAGE = 500;

	const DEPOSIT_TRANSACTION = 1;
	const WITHDRAW_TRANSACTION = 0;

	const SPORTSBOOK_GAME = 1;
	const LIVECASINO_GAME = 2;
	const CASINO_GAME = 3;
	const MOBILE_WEB = 4;
	const KENO_GAME = "keno";
	const NUMBERGAME = "numbergame";
    const API_getLeagueName = "GetLeagueName";
    const API_getTeamName = "GetTeamName";
    const API_getGameDetail = "GetGameDetail";
    const API_getVirtualResult = "GetVirtualGameResult";
    const API_getBetDetailByTransId = "GetBetDetailByTransId";
    const API_getSystemParlayDetail = "GetSystemParlayDetail";
    const API_getMemberBetSetting = "GetMemberBetSetting";
    const NUMBER_WHEEL = 90;
	const VIRTUAL_WINPLACE_BET = array(1231,1232,1233,1237,1238);
	//sporty type
	const VIRTUAL_SPORTS = array(180,181,182,183,184,185,186,);
	const VIRTUAL_SPORTS2 = array(190,191,192,193,194,195,196,199);
	const RNG_CASINO = 251;
	const RNG = array(208,209,210,219);
	const KENO = array(202,220);
	const THIRD_PARTY = array(212);
	const NUMBER_GAME = array(161,164);
	const LIVE_CASINO = 162;
	const RACING = 154;
	//end of sport type
	const NUMBER_BALL = 37.5;
	const OUTRIGHT = 10;
	const HANDICAP_BET = array(1,7,17,20,21,25,27,153,154,155,168,183,219,301,303,453,448,460,609,613,614,2705,2801,2802,2805,2806,2809,1303,1308,501,1201,1311,1316,1324,468);
	const OVERUNDER_BET = array(
		//sports
		3,8,18,85,156,178,197,198,204,205,220,302,304,401,402,403,404,461,462,463,464,610,615,616,2703,2704,2803,2804,2811,2812,1306,1203,
		//number games
		51,52,53,54,55,56,81,82,
		//x total games
		1312
	);
	const ODDEVEN_BET = array(
		//sports
		2,12,86,136,137,138,139,157,184,194,203,428,611,1305,1318,
		//number games
		83,84
	);
	const ONEXTWO_BET = array(5,15,28,124,125,160,164,176,177,430,458,459,502,2701,2702);
	const CORRECT_SCORE_BET = array(4,30,152,158,165,166,405,413,414,416,2707);
	const YES_NO_BET = array(133,438,134,439,135,145,146,433,147,436,148,437,149,440,150,441,173,174,188,427,189,434,190,435,210,211,212,213,214,215);
	const HALF_SCORING_BET = array(140,141,142,442,443,444,452);
	const DOUBLE_CHANCE_BET = array(24,151,186,410,431);
	const EXACT_GOALS_BET = array(129,130,159,161,162,179,181,182,187,195,196,200,201,406,407,409,412);
	const FIRST_LAST_CORNER_BET = array(206,207,208,209);
	const QUARTERXY_BETTYPE= array(609,610,611,612,613,614,615,616,617,
		//other
		154,155,156,219,220
	);
	const NO_HDP_DISPLAY = array(5);
	const STATUS_DRAW = "draw";
	const IS_LIVE = 1;
	const IS_HOME = 'h';

	const URI_MAP = array(
		self::API_createPlayer => 'CreateMember',
		self::API_login => 'Login',
		self::API_logout => 'KickUser',
        self::API_queryPlayerBalance => 'CheckUserBalance',
		self::API_isPlayerExist => 'CheckUserBalance',
		self::API_depositToGame => 'FundTransfer',
		self::API_withdrawFromGame => 'FundTransfer',
		self::API_queryTransaction => 'CheckFundTransfer',
		self::API_syncGameRecords => 'GetBetDetail ',
		self::API_batchQueryPlayerBalance => 'CheckUserBalance',
		self::API_setMemberBetSetting => "SetMemberBetSetting",
		self::API_updatePlayerInfo => "UpdateMember",
        self::API_getLeagueName => "GetLeagueName",
        self::API_getTeamName => "GetTeamName",
        self::API_getGameDetail => 'GetGameDetail',
        self::API_getVirtualResult => 'GetVirtualGameResult',
        self::API_getBetDetailByTransId => 'GetBetDetailByTransId',
        self::API_getSystemParlayDetail => 'GetSystemParlayDetail',
		self::API_getMemberBetSetting => 'GetMemberBetSetting',
		self::API_checkLoginToken => 'GetToken',
	);

	# Don't ignore on refresh
	const DEFAULT_IGNORED_0_ON_REFRESH = FALSE;

	public function __construct() {
		parent::__construct();
		$this->CI->load->helper('cookie');
		$this->oneworks_api_url = $this->getSystemInfo('url');
		$this->oneworks_vendor_id = $this->getSystemInfo('oneworks_vendor_id');
		$this->oneworks_vendor_operator_id = $this->getSystemInfo('oneworks_vendor_operator_id');
		$this->oneworks_player_min_transfer = $this->getSystemInfo('oneworks_player_min_transfer');
		$this->oneworks_player_max_transfer = $this->getSystemInfo('oneworks_player_max_transfer');
		$this->oneworks_odds_type = $this->getSystemInfo('oneworks_odds_type');
		$this->oneworks_player_id_suffix = $this->getSystemInfo('oneworks_player_id_suffix');
		$this->oneworks_currency = $this->getSystemInfo('oneworks_currency');
		$this->oneworks_language = $this->getSystemInfo('oneworks_language');
		$this->oneworks_game_url = $this->getSystemInfo('oneworks_game_url');
		$this->oneworks_demo_account = $this->getSystemInfo('oneworks_demo_account');
		$this->oneworks_secret_key = $this->getSystemInfo('oneworks_secret_key');
		$this->oneworks_bet_setting = $this->getSystemInfo('oneworks_bet_setting');
		$this->oneworks_demo_url = $this->getSystemInfo('oneworks_demo_url');
		$this->oneworks_mobile_demo_url = $this->getSystemInfo('oneworks_mobile_demo_url');
        $this->oneworks_mobile_game_url = $this->getSystemInfo('oneworks_mobile_game_url');
		$this->oneworks_recheck_last_version_key_zero_flag = $this->getSystemInfo('oneworks_recheck_last_version_key_zero_flag');
        $this->bet_detail_default_lang = $this->getSystemInfo('bet_detail_default_lang','en');

		$this->oneworks_mobile_home_url = $this->getSystemInfo('oneworks_mobile_home_url');
		$this->oneworks_mobile_extend_session_url = $this->getSystemInfo('oneworks_mobile_extend_session_url');
		$this->forward_sites = $this->getSystemInfo('forward_sites');
		$this->token_prefix = $this->getSystemInfo('token_prefix', '');
		$this->prefix_count = $this->getSystemInfo('prefix_count', 3);
		$this->white_launcher_domain = $this->getSystemInfo('white_launcher_domain');
		$this->enable_player_language = $this->getSystemInfo('enable_player_language',false);
		$this->system_code = $this->getSystemInfo('system_code');
		$this->web_skin_type = $this->getSystemInfo('web_skin_type','0');
		$this->use_callback_game_launch = $this->getSystemInfo('use_callback_game_launch', false);
		$this->default_odd_type = $this->getSystemInfo('default_odd_type', 4);
		$this->prefix_for_username = $this->getSystemInfo('prefix_for_username');
		$this->oneworks_use_subsidiary = $this->getSystemInfo('oneworks_use_subsidiary', false); // use for subsite
		$this->enable_skin_color = $this->getSystemInfo('enable_skin_color', false);
		$this->skincolor = $this->getSystemInfo('skincolor','');
		$this->sync_by_trans_id = $this->getSystemInfo('sync_by_trans_id',false);
        //don't support
        $this->is_enabled_direct_launcher_url=$this->getSystemInfo('is_enabled_direct_launcher_url', false);
        $this->max_retry_empty_attempt = $this->getSystemInfo('max_retry_empty_attempt',2);;
		$this->retry_empty_attempt = 0;
	}

	/**
	 * overview : get platform code
	 *
	 * @return int
	 */
	public function getPlatformCode() {
		return ONEWORKS_API;
	}

	/**
	 * overview : custom http call
	 *
	 * @param $ch
	 * @param $params
	 */
	protected function customHttpCall($ch, $params) {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	}

	/**
	 * overview : generate url
	 *
	 * @param $apiName
	 * @param $params
	 * @return string
	 */
	public function generateUrl($apiName, $params) {
		$apiUri = self::URI_MAP[$apiName];
		return $url = $this->oneworks_api_url . "/" . $apiUri; # . "?" . http_build_query($params);
	}

	/**
	 * overview : generate security token
	 *
	 * @param $str
	 * @return string
	 */
	private function generateSecurityToken($str) {
		return strtoupper(md5($str));
	}

	/**
	 * overview : after process result
	 *
	 * @param $apiName
	 * @param $params
	 * @param $responseResultId
	 * @param $resultText
	 * @param $statusCode
	 * @param null $statusText
	 * @param null $extra
	 * @param null $resultObj
	 * @return array
	 */
	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	/**
	 * overview : processing result
	 *
	 * @param $responseResultId
	 * @param $resultArr
	 * @param null $playerName
	 * @return bool
	 */
	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = !empty($resultArr);
		if (!$success || $resultArr['error_code'] != self::STATUS_SUCCESS) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('oneworks got error', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
			$success = false;
		}
		return $success;
	}

	/**
	 * overview : call back
	 *
	 * @param null $result
	 * @return mixed
	 */
	public function callback($result = null) {
		$this->CI->utils->debug_log('Game_api_oneworks, callback: ', $result);
		if (!$result) {
			$data = array(
				"vendor_member_id" => lang('N/A'),
				"status_code" => 3,
				"message" => lang('Invalid Request'),
			);
			$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><authenticate version='2.0'></authenticate>");
			$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
			$this->CI->utils->debug_log('Game_api_oneworks (Callback)  XML Return:', $data);
			return $xmlReturn;
		}

		# forward to site
		if ($this->forward_sites) {
			//forward site for session
			if($result['op'] == "check_online") {
				// $str = $result['vendor_member_id'];
				// $prefix = substr($str, 0, $this->prefix_count)."_";
				// if (isset($this->forward_sites[$prefix])) {
				// 	$url = $this->forward_sites[$prefix];
				// 	return $this->forwardCallback($url, $result);
				// }
				if ($result['secret_key'] == $this->oneworks_secret_key) {
					//multiple sites without prefix
					$data['message'] = "OK";
					$data['status_code'] = self::STATUS_SUCCESS;
					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><member_online version='2.0'></member_online>");
					$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
					$this->CI->utils->debug_log('Game_api_oneworks (Callback) multiple sites check_online XML Return:', $data);
					return $xmlReturn;
				}else{
					$data['message'] = "Invalid secret key";
					$data['status_code'] = 1;
					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><member_online version='2.0'></member_online>");
					$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
					$this->CI->utils->debug_log('Game_api_oneworks (Callback) multiple sites check_online XML Return:', $data);
					return $xmlReturn;
				}
			}
			//forward site for authentication
			$this->CI->utils->debug_log('Game_api_oneworks forward_sites token', $result['session_token']);
			if (preg_match("#^(?P<prefix>" . implode('|', array_keys($this->forward_sites)) . ")#", $result['session_token'], $matches)) {
				$this->CI->utils->debug_log('Game_api_oneworks forward_sites matches', $matches);
				if (isset($this->forward_sites[$matches['prefix']])) {
					$url = $this->forward_sites[$matches['prefix']];
					$this->CI->utils->debug_log('Game_api_oneworks forward_sites url', $url);
					return $this->forwardCallback($url, $result);
				}
			}
		}

		if ($result['op'] == "auth" || $result['op'] == "check_online") {
			$tokenStatus = array("VALID_TOKEN" => 0, "INVALID_SECRET_KEY" => 1, "SESSION_TOKEN_NOT_FOUND" => 2, "SESSION_TOKEN_INVALID" => 3);

			if ($result['secret_key'] == $this->oneworks_secret_key) {
				$this->CI->load->model(array('common_token'));
				if($result['op'] == "check_online") {
					$playerId = $this->getPlayerIdInGameProviderAuth($result['vendor_member_id']);
					$token = $this->CI->common_token->getValidPlayerToken($playerId);
					if(!empty($playerId)){
						if(!empty($token)){
							$data['message'] = "OK";
							$data['status_code'] = self::STATUS_SUCCESS;
						} else {
							$data['message'] = "Member is offline";
							$data['status_code'] = self::MEMBER_OFFLINE;
						}
					} else {
						$data['message'] = "vendor_member_id not found";
						$data['status_code'] = self::MEMBER_NOT_FOUND;
					}

					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><authenticate version='2.0'></authenticate>");
					$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
					$this->CI->utils->debug_log('Game_api_oneworks (Callback)  XML Return:', $data);
					return $xmlReturn;
				}
				# remove token prefix
				if (!empty($this->token_prefix) && !empty($result['session_token'])) {
					$result['session_token'] = str_replace($this->token_prefix, "", $result['session_token']);
					$this->CI->utils->debug_log('Game_api_oneworks session token', $result['session_token']);
				}

				$playerId = $this->CI->common_token->getPlayerIdByToken($result['session_token']);
				$this->CI->utils->debug_log('Game_api_oneworks playerId:', $playerId);
				if (!empty($playerId)) {
					$login_name = $this->getGameUsernameByPlayerId($playerId);
					$this->CI->utils->debug_log('Game_api_oneworks, getGameUsernameByPlayerId: ', $login_name);
					if (!empty($login_name)) {
						$data = array(
							"vendor_member_id" => $login_name,
							"status_code" => self::STATUS_SUCCESS,
							"message" => "OK",
						);

						$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><authenticate version='2.0'></authenticate>");
						$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
						$this->CI->utils->debug_log('Game_api_oneworks (Callback)  XML Return:', $xmlReturn);
						return $xmlReturn;
					}
				} else {
					$msg = lang("Invalid Session Token");
					$data = array(
						"vendor_member_id" => $result['session_token'],
						"status_code" => $tokenStatus['SESSION_TOKEN_INVALID'],
						"message" => $msg,
					);
					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><authenticate version='2.0'></authenticate>");
					$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
					$this->CI->utils->debug_log('Game_api_oneworks (Callback)  XML Return:', $data);
					return $xmlReturn;
				}
			} else {
				$msg = lang("Invalid Secret Key");
				$data = array(
					"vendor_member_id" => $result['session_token'],
					"status_code" => $tokenStatus['INVALID_SECRET_KEY'],
					"message" => $msg,
				);
				$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><authenticate version='2.0'></authenticate>");
				$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
				$this->CI->utils->debug_log('Game_api_oneworks (Callback)  XML Return:', $data);
				return $xmlReturn;
			}
		}
	}

	function forwardCallback($url, $params) {
		list($header, $resultXml) = $this->httpCallApi($url, $params);
		$this->CI->utils->debug_log('forwardCallback', $url, $header, $resultXml);
		return $resultXml;
	}

	public function getPlayerOneworksCurrency($gameUsername){
		# use correct currency code
		$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
		if(!is_null($playerId)){
			$this->CI->load->model(array('player_model'));
			$currencyCode = $this->CI->player_model->getPlayerCurrencyByPlayerId($playerId);
			if(!is_null($currencyCode)){
				switch ($currencyCode) {
					case 'RMB':
					case 'CNY':
						$currencyCode = 13;
						break;
					case 'MYR':
						$currencyCode = 2;
						break;
					case 'IDR':
						$currencyCode = 15;
						break;
					case 'VND':
						$currencyCode = 51;
						break;
					case 'THB':
						$currencyCode = 4;
						break;
				}
				return $currencyCode;
			}else{
				return $this->oneworks_currency;
			}
		}else{
			return $this->oneworks_currency;
		}
	}

	/**
	 * overview : create player game
	 *
	 * @param $playerName
	 * @param $playerId
	 * @param $password
	 * @param null $email
	 * @param null $extra
	 * @return array
	 */
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		#$secureId = $this->getPlayerSecureIdByPlayerUsername($playerName);
        #use_secure_id_as_game_username
		#$gameUsernameSecureId = $secureId . $this->oneworks_player_id_suffix;
		// $gameName = $playerName . $this->oneworks_player_id_suffix;
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->CI->utils->debug_log('create player gameUsername: ' . $gameUsername);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
            'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'playerId' => $playerId,
			'bet_limit_settings' => null,
		);

		# add oneworks_bet_setting from T1 gamegateway added on extra
		if(!empty($extra) && is_array($extra)){
			if(array_key_exists('bet_limit_settings',$extra)){
				$context['bet_limit_settings'] = $extra['bet_limit_settings'];
			}
		}
		
		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
			"operatorid" => $this->oneworks_vendor_operator_id,
			"firstname" => $gameUsername,
			"lastname" => $gameUsername,
			"username" => $gameUsername,
			"oddstype" => $this->oneworks_odds_type,
			"currency" => (int) $this->getPlayerOneworksCurrency($gameUsername),
			"maxtransfer" => $this->oneworks_player_max_transfer,
			"mintransfer" => $this->oneworks_player_min_transfer,
		);
		$this->CI->utils->debug_log('create player params: ' . json_encode($params));
		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	/**
	 * overview : process result for createPlayer
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		$this->CI->utils->debug_log('create player result: ' . json_encode($resultJsonArr));

		if ($success) {
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
			# FROM T1oneworks bet settings
			$bet_limit_settings = $this->getVariableFromContext($params, 'bet_limit_settings');
			$this->setMemberBetSetting($playerName,$bet_limit_settings);
		}

		return array($success, $resultJsonArr);
	}

	/**
	 * overview : set member settings
	 *
	 * @param $playerName
	 * @param $newBetSetting
	 * @return array
	 */
	public function setMemberBetSetting($playerName,$newBetSetting=null) {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForMemberSettings',
			'playerName' => $playerName,
		);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$sportsTypeArr = $this->oneworks_bet_setting;
		$betSettingArr = array();

		if (!empty($sportsTypeArr)) {
			foreach ($sportsTypeArr as $key) {
				if (!empty($key['sport_type'])) {
					foreach ($key['sport_type'] as $type) {
						$betSettingArr[$type] = array(
							"sport_type" => $type,
							"min_bet" => $key['min_bet'],
							"max_bet" => $key['max_bet'],
							"max_bet_per_match" => $key['max_bet_per_match'],
						);
					}
				}
			}
		}

		# Update new settings
		if(!empty($newBetSetting)){
			foreach ($newBetSetting as $setting) {
				$betSettingArr[$setting['sport_type']] = $setting;
			}
		}

		# RE-KEY ARRAY
		$betSettingArr = array_values($betSettingArr);

		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
			"bet_setting" => json_encode($betSettingArr),
		);

		return $this->callApi(self::API_setMemberBetSetting, $params, $context);
	}

	/**
	 * overview : process result for member settings
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForMemberSettings($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		$result = array('response_result_id' => $responseResultId, 'result' => $resultJson);
		return array($success, $result);
	}

	/**
	 * overview : update member setting
	 *
	 * @param $playerName
	 * @return array
	 */
	public function updateMemberSetting($playerName,$json_params = null) {

		$rlt=parent::isPlayerExist($playerName);
		if($rlt['exists']){
			$playerName = $this->getGameUsernameByPlayerUsername($playerName);
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processUpdateForMemberSettings',
				'playerName' => $playerName,
			);

			$params = array(
				"vendor_id" => $this->oneworks_vendor_id,
				"vendor_member_id" => $playerName,
				"firstname" => $playerName,
				"lastname" => $playerName,
				"maxtransfer" => $this->oneworks_player_max_transfer,
				"mintransfer" => $this->oneworks_player_min_transfer,
			);
			if(!empty($json_params)){
				$params = $json_params;
			}
			return $this->callApi(self::API_updatePlayerInfo, $params, $context);
		}else{
			return ['success' => true, 'exists'=>$rlt['exists']];
		}
	}

	/**
	 * overview : process update for member settings
	 *
	 * @param $params
	 * @return array
	 */
	public function processUpdateForMemberSettings($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		$result = array('response_result_id' => $responseResultId, 'result' => $resultJson);
		return array($success, $result);
	}

	/**
	 * overview : get member settings
	 *
	 * @param $playerName
	 * @return array
	 */
	public function getMemberBetSetting($playerName) {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetMemberBetSetting',
			'playerName' => $playerName,
		);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);


		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
		);
		return $this->callApi(self::API_getMemberBetSetting, $params, $context);
	}

	/**
	 * overview : process result for getMemberBetSetting
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForGetMemberBetSetting($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		$result = array('response_result_id' => $responseResultId, 'result' => $resultJson);
		return array($success, $result);
	}

	/**
	 * overview : query player information
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerInfo($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true);
	}

	/**
	 * overview : change player password
	 *
	 * @param $playerName
	 * @param $oldPassword
	 * @param $newPassword
	 * @return array
	 */
	public function changePassword($playerName, $oldPassword, $newPassword) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true);
	}

	/**
	 * overview : block player
	 *
	 * @param $playerName
	 * @return array
	 */
	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
	}

	/**
	 * overview : unblock player
	 *
	 * @param $playerName
	 * @return array
	 */
	public function unblockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->unblockUsernameInDB($playerName);
		return array("success" => true);
	}

	/**
	 * overview : deposit to game
	 *
	 * @param $playerName
	 * @param $amount
	 * @param null $transfer_secure_id
	 * @return array
	 */
	public function depositToGame($playerName, $amount, $transfer_secure_id = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		// use subsidiary if vendor id is under parent
		$transId = $this->oneworks_use_subsidiary ? $this->prefix_for_username.$transfer_secure_id : $transfer_secure_id; //random_string('numeric');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'gameUsername' => $gameUsername,
			'amount' => $amount,
			'external_transaction_id'=>$transId,
		);
		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
			"vendor_trans_id" => $transId,
			"amount" => $amount,
			"currency" => (int) $this->getPlayerOneworksCurrency($gameUsername),
			"direction" => self::DEPOSIT_TRANSACTION,
			"wallet_id" => self::SPORTSBOOK_WALLET_ID,
		);
		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	/**
	 * overview : process result for deposit to game
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForDepositToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $gameUsername);
		$result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

		if ($success) {
			//for sub wallet
			// $afterBalance = @$resultJsonArr['Data']['after_amount'];
			// $result["external_transaction_id"] = @$resultJsonArr['Data']['system_id'];
			// $result["currentplayerbalance"] = $afterBalance;
			//update
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

			// if ($playerId) {
			// 	$this->CI->utils->debug_log('insert transaction to game logs before');
			// 	//deposit
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeMainWalletToSubWallet());
			// 	$this->CI->utils->debug_log('insert transaction to game logs after');

			// } else {
			// 	$this->CI->utils->error_log('cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultJsonArr['error_code']);
			$result['transfer_status'] = $this->getTransferStatusCode(@$resultJsonArr['Data']['status']);

			// if (@$resultJsonArr['Data']['status'] == '1') {
			// 	$this->CI->utils->debug_log('set pending to success');
			// 	$success=true;
			// 	// $this->checkFundTransfer($transId);
			// } else {
			// 	// $result["userNotFound"] = true;
			// }

		}

		return array($success, $result);
	}

	public function queryTransaction($transactionId,$extra) {

		$playerName=$extra['playerName'];
		$playerId=$extra['playerId'];
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'playerId'=>$playerId,
			'external_transaction_id' => $transactionId
		);
		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_trans_id" => $transactionId,
			"wallet_id" => self::SPORTSBOOK_WALLET_ID,
		);

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$transId = $this->getVariableFromContext($params, 'external_transaction_id');

		$success = $this->processResultBoolean($responseResultId, $resultJsonArr);

		$this->CI->utils->debug_log('oneworks query response', $resultJsonArr, 'transaction id', $transId);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$transId,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);
		if($success) {
			$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultJsonArr['error_code']);
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		if (!$success) {
		//	if (@$resultJsonArr['Data']['status'] != self::PENDING_FUND_TRANSFER_2) {
		//		sleep(300);
		//		$this->queryTransaction($transId);
		//	} else {
		//		$this->setResponseResultToError($responseResultId);
		//		$this->CI->utils->debug_log('oneworks got error', $responseResultId, 'result', $resultJsonArr);
		//	}
		}
		return array($success, $result);
	}

	/**
	 * overview : withdraw from game
	 *
	 * @param $playerName
	 * @param $amount
	 * @param null $transfer_secure_id
	 * @return array
	 */
	public function withdrawFromGame($playerName, $amount, $transfer_secure_id = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		// use subsidiary if vendor id is under parent
		$transId = $this->oneworks_use_subsidiary ? $this->prefix_for_username.$transfer_secure_id : $transfer_secure_id; //random_string('numeric');
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawToGame',
			'gameUsername' => $gameUsername,
			'amount' => $amount,
			'external_transaction_id' => $transId,
			// 'transId' => $transId,
		);

		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
			"vendor_trans_id" => $transId,
			"amount" => $amount,
			"currency" => (int) $this->getPlayerOneworksCurrency($gameUsername),
			"direction" => self::WITHDRAW_TRANSACTION,
			"wallet_id" => self::SPORTSBOOK_WALLET_ID,
		);
		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	/**
	 * process result for withdraw to game
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForWithdrawToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $gameUsername);
		$result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

		if ($success) {
			// $afterBalance = @$resultJsonArr['Data']['after_amount'];
			#$result["external_transaction_id"] = @$resultJsonArr['Data']['system_id'];
			//update
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	//withdrawal
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeSubWalletToMainWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
		} else {
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultJsonArr['error_code']);
			$result['transfer_status'] = $this->getTransferStatusCode(@$resultJsonArr['Data']['status']);
			// $result["userNotFound"] = true;
		}

		return array($success, $result);
	}

	public function getTransferErrorReasonCode($apiErrorCode) {
		$reasonCode = self::COMMON_TRANSACTION_STATUS_APPROVED;

		switch ((int)$apiErrorCode) {
			case 1:
				$reasonCode = self::REASON_UNKNOWN;						# Failed during executed
				break;
			case 2:
			$reasonCode = self::REASON_NOT_FOUND_PLAYER;				# User does not exist
				break;
			case 3:
				$reasonCode = self::REASON_NO_ENOUGH_BALANCE;			# not enough balance
				break;
			case 4:
				$reasonCode = self::REASON_LOWER_OR_GREATER_THAN_MIN_OR_MAX_TRANSFER;	# lower or greater than min or max transfer
				break;
			case 5:
				$reasonCode = self::REASON_DUPLICATE_TRANSFER;			# dupplicate transaction id
				break;
			case 6:
				$reasonCode = self::REASON_CURRENCY_ERROR;				# currency error
				break;
			case 7:
				$reasonCode = self::REASON_INCOMPLETE_INFORMATION;		# input parameters error
				break;
			case 8:
				$reasonCode = self::REASON_UNKNOWN;						# member reach limit
				break;
			case 9:
				$reasonCode = self::REASON_AGENT_NOT_EXISTED;			# invalid vendor id
				break;
			case 10:
				$reasonCode = self::REASON_API_MAINTAINING;				# under maintenance
				break;
			case 11:
				$reasonCode = self::REASON_SESSION_TIMEOUT;				# system busy, try again after 2 minutes
				break;
		}

		return $reasonCode;
	}

	public function getTransferStatusCode($apiStatusCode) {
		$statusCode = self::COMMON_TRANSACTION_STATUS_UNKNOWN;

		switch ((int)$apiStatusCode) {
			case 1:
				$statusCode = self::COMMON_TRANSACTION_STATUS_DECLINED;
				break;
			case 2:
				$statusCode = self::COMMON_TRANSACTION_STATUS_PROCESSING;
				break;
		}
		return $statusCode;
	}

	/**
	 * overview : login player
	 *
	 * @param $playerName
	 * @param null $password
	 * @return array
	 */
	public function login($playerName, $extra = null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogin',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
		);

        return $this->callApi(self::API_login, $params, $context);
    }

    public function processResultForLogin($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $gameUsername);
        return array($success, $resultJsonArr);
    }

	/**
	 * overview : logout player
	 *
	 * @param $playerName
	 * @param null $password
	 * @return array
	 */
	public function logout($playerName, $password = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogout',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_id" => $gameUsername,
        );

        return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $gameUsername);
		$result = array("error_code"=> $resultJsonArr['error_code']);
        return array($success, $result);
	}

	/**
	 * overview : update player information
	 *
	 * @param $playerName
	 * @param $infos
	 * @return array
	 */
	public function updatePlayerInfo($playerName, $json_info) {
		return $this->updateMemberSetting($playerName,$json_info);
	}

    public function isPlayerExist($playerName) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "vendor_member_ids" => $playerName,
            "wallet_id" => self::SPORTSBOOK_WALLET_ID,
        );

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    /**
     * overview : process result for query player balance
     *
     * @param $params
     * @return array
     */
    public function processResultForIsPlayerExist($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
        $result = array();
        if ($success) {
        	switch ($resultJsonArr['Data'][0]['error_code']) {
			    case 0://success
			    case 6://user not transfer yet means exist
			        $result = array(
		                "success" => true,
		                "exists" => true
		            );
			        break;
			    case 2: //user not exist
			        $result = array(
		                "success" => true,
		                "exists" => false
		            );
			        break;
			    case 7://third party check fail
			        $result = array(
		                "success" => true,
		                "exists" => null
		            );
			        break;
			    default:
			    	$result = array(
		                "success" => false,
		                "exists" => null
		            );
			}
        } else {
            $result = array(
                "success" => false,
                "exists" => null
            );
        }

        return array($success, $result);
    }

	/**
	 * overview : query player balance
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerBalance($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
		);

		$params = array(
			"vendor_id" => $this->oneworks_vendor_id,
			"vendor_member_ids" => $playerName,
			"wallet_id" => self::SPORTSBOOK_WALLET_ID,
		);
		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	/**
	 * overview : process result for query player balance
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForQueryPlayerBalance($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');

		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
		$result = array();
		if ($success && isset($resultJsonArr['Data'][0]['balance'])) {
			$result['exists'] = true;
			$result["balance"] = floatval(@$resultJsonArr['Data'][0]['balance']);
			$result["response_result_id"] = $responseResultId;
			$playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			$this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName',
				$playerName, 'balance');
			if ($playerId) {
				//should update database
				// $this->updatePlayerSubwalletBalance($playerId, $result["balance"]);
			} else {
				log_message('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}
		} else {
            if(!empty($resultJsonArr)){
                $success = true;
            } else {
                $success = false;
            }
		}
		return array($success, $result);
	}

	/**
	 * overview : query player daily balance
	 *
	 * @param $playerName
	 * @param $playerId
	 * @param null $dateFrom
	 * @param null $dateTo
	 * @return array
	 */
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
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

	/**
	 * overview : check login status
	 *
	 * @param $playerName
	 * @return array
	 */
	public function checkLoginStatus($playerName) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : get total betting amount
	 *
	 * @param $playerName
	 * @param $dateFrom
	 * @param $dateTo
	 * @return array
	 */
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : query transaction
	 *
	 * @param $transactionId
	 * @param $extra
	 * @return array
	 */
	// public function queryTransaction($transactionId, $extra) {
	// 	return $this->returnUnimplemented();
	// }

	/**
	 * overview : process result for query transaction
	 *
	 * @param $apiName
	 * @param $params
	 * @param $responseResultId
	 * @param $resultXml
	 * @return array
	 */
	// public function processResultForQueryTransaction($apiName, $params, $responseResultId, $resultXml) {
	// 	return $this->returnUnimplemented();
	// }

    public function getLanguage($currentLang) {

        switch ($currentLang) {
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
            case LANGUAGE_FUNCTION::PLAYER_LANG_CHINESE :
            case 'zh-cn':
                $language = 'cs';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
            case LANGUAGE_FUNCTION::PLAYER_LANG_INDONESIAN :
            case 'id-id':
                $language = 'id';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
            case LANGUAGE_FUNCTION::PLAYER_LANG_VIETNAMESE :
            case 'vi-vn':
                $language = 'vn';
                break;
            case 'ko-kr':
            case LANGUAGE_FUNCTION::INT_LANG_KOREAN:
            case LANGUAGE_FUNCTION::PLAYER_LANG_KOREAN :
                $language = 'ko';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_THAI:
            case Language_function::PLAYER_LANG_THAI :
				$language = 'th';
				break;
            default:
                $language = 'en';
                break;
        }
        return $language;
    }

    public function generateGotoUri($playerName, $extra){
		return '/player_center/goto_oneworks_game/'.$extra['game_type'].'/1/web/'.($extra['is_mobile'] ? 'true' : 'false').'/'.$extra['language'];
	}

	/**
	 * overview : query forward game
	 *
	 * @param $playerName
	 * @param $extra
	 * @return array
	 */
	public function queryForwardGame($playerName, $extra) {
		if ($this->use_callback_game_launch) {
			return $this->gameLaunchWithCallback($playerName, $extra);
		} else {
			return $this->normalGameLaunch($playerName, $extra);
		}
	}

	# launch game passing token in url
	public function normalGameLaunch($playerName, $extra) {
		$this->CI->utils->debug_log('oneworks queryForwardGame normalGameLaunch');
		$this->CI->load->model(array('player_model', 'common_token'));
		$this->CI->utils->debug_log('oneworks queryForwardGame login playerName: ', $playerName);

		# forward url for if white launcher is defined
		if(!empty($this->white_launcher_domain) && !empty($playerName)) {
			$nextUrl=$this->generateGotoUri($playerName, $extra);
			$result=$this->forwardToWhiteDomain($playerName, $nextUrl);
			if($result['success']){
				return $result;
			}
		}

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
		$language = $this->getLanguage($extra['language']);
		$game_type = empty($extra['game_type']) ? self::SPORTSBOOK_GAME : $extra['game_type'];
		$is_mobile = $extra['is_mobile'];

		if($this->enable_player_language){
			$player_language = $this->CI->player_model->getLanguageFromPlayer($playerId);
			$language = $this->getLanguage($player_language);
		}

		$mobile_home_url = $this->utils->getSystemUrl('m');
		$mobile_login_url = $this->utils->getPlayerLoginUrl();
		$mobile_registration_url = $this->utils->getSystemUrl('m','/player_center/iframe_register');

        # for gamegateway lobby url for mobile to redirect to client player center not gamegateway player center
        if (isset($extra['extra']['t1_lobby_url']) && !empty($extra['extra']['t1_lobby_url'])) {
            $mobile_home_url = $extra['extra']['t1_lobby_url'];
        }

        # for gamegateway login and registration for mobile to redirect to client player center not gamegateway player center
        if (isset($extra['extra']['t1_player_login_url']) && !empty($extra['extra']['t1_player_login_url'])) {
            $mobile_login_url = $extra['extra']['t1_player_login_url'];
			$mobile_registration_url = $extra['extra']['t1_player_login_url'];
        }

		# run demo game if no username
		if (empty($gameUsername)) {
			$cookie_lang = get_cookie('_lang');
			if($cookie_lang){
				$language = $cookie_lang;
			}else {
				$language = $this->oneworks_language;
			}

			if($is_mobile){
				//sample
				//http://ismart.{yourdomain}/DepositLogin/bfindex?lang=en&homeUrl=http://{yourhomeURL}&signupUrl=http://{yoursignupURL}&LoginUrl=http://{yourloginURL}
				$mobile_url = $this->oneworks_mobile_demo_url . '?lang=' . $language . '&homeUrl=' . $mobile_home_url . '&signupUrl=' .$mobile_registration_url . '&LoginUrl=' . $mobile_login_url;
				return array(
					'success' => true,
					'sessionToken' => null,
					'url' => $mobile_url
				);
			}
			$this->CI->utils->debug_log('oneworks queryForwardGame login demoURL: ', $this->oneworks_game_url);
			return array(
				'success' => true,
				'sessionToken' => null,
				'url' => $this->oneworks_demo_url . '?lang=' . $language . '&WebSkinType=' . $this->web_skin_type . '&skincolor=' . $this->skincolor,
				'&is_mobile' => $is_mobile);
		}

		$loginToken = $this->login($playerName, $extra);
		if (!$loginToken["success"]) {
			return array("success" => false);
		}

		$token = $loginToken["Data"];
		$oneworks_mobile_game_url_subdomain = $this->getSystemInfo('oneworks_mobile_game_url_subdomain');

		if (!empty($oneworks_prefix_sub_domain) && !empty($oneworks_mobile_game_url_subdomain)) {
			$this->oneworks_game_url = $oneworks_prefix_sub_domain . '.' . $this->utils->stripSubdomain($_SERVER['HTTP_HOST']);
			$this->oneworks_mobile_game_url = $oneworks_mobile_game_url_subdomain . '.' . $this->utils->stripSubdomain($_SERVER['HTTP_HOST']);
		} else {
			$this->oneworks_game_url = $this->getSystemInfo('oneworks_game_url');
			$this->oneworks_mobile_game_url = $this->getSystemInfo('oneworks_mobile_game_url');
		}

		if ($game_type == self::SPORTSBOOK_GAME) {
			if (!$is_mobile) {
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&WebSkinType=' . $this->web_skin_type;
				if($this->enable_skin_color && $this->skincolor){
					$url .= "&skincolor=".$this->skincolor;
				}
			} else {
				switch ($language){// start OGP-10173
						case 'cs':
							$oType = 2;
							break;
						case 'id':
							$oType = 3;
							break;
						case 'th':
							$oType = 4;
							break;
						default:
			                $oType = $this->default_odd_type;
			                break;
					} // end OGP-10173

				$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&OType=' . $oType . '&homeUrl='. $mobile_home_url. '&signupUrl=' .$mobile_registration_url . '&LoginUrl=' . $mobile_login_url;

				if ($this->oneworks_mobile_extend_session_url) {
					$url .= '&extendSessionUrl=' . $this->oneworks_mobile_extend_session_url;
				}
			}
		} elseif ($game_type == self::LIVECASINO_GAME) {
			$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=livecasino';
		} elseif ($game_type == self::CASINO_GAME) {
			$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=casino';
		} elseif (strtolower($game_type) == self::NUMBERGAME) {
			$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=numbergame';
		} elseif (strtolower($game_type) == self::KENO_GAME) {
			$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&game=keno';
			if($is_mobile){
				$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types=Keno';
			}
		} else {
			//for deeplink
			$game_type = implode(",",explode('_', $extra['game_type']));
			$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types='.$game_type;
			if($is_mobile){
				$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types='.$game_type;
			}
		}

		$url .= "&token=".$token;
		$this->CI->utils->debug_log('oneworks queryForwardGame login URL--------->: ', $url);

		return array('success' => true,
			'sessionToken' => $token,
			'url' => $url,
			'domains' => $this->getSystemInfo('oneworks_cookie_domains'),
			'system_code' => $this->system_code,
			'use_callback_game_launch' => false,
		);
	}

	public function gameLaunchWithCallback($playerName, $extra) {
		$this->CI->load->model(array('player_model'));
		$this->CI->utils->debug_log('oneworks queryForwardGame gameLaunchWithCallback');
		$language = $this->getLanguage($extra['language']);
		$oneworks_mobile_home_url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		$game_url_domain = $this->utils->stripSubdomain($_SERVER['HTTP_HOST']);
		$oneworks_prefix_sub_domain = $this->getSystemInfo('oneworks_game_url_subdomain');
		$oneworks_mobile_game_url_subdomain = $this->getSystemInfo('oneworks_mobile_game_url_subdomain');
		if (!empty($oneworks_prefix_sub_domain) && !empty($oneworks_mobile_game_url_subdomain)) {
			$this->oneworks_game_url = $oneworks_prefix_sub_domain . '.' . $this->utils->stripSubdomain($_SERVER['HTTP_HOST']);
			$this->oneworks_mobile_game_url = $oneworks_mobile_game_url_subdomain . '.' . $this->utils->stripSubdomain($_SERVER['HTTP_HOST']);
		} else {
			$this->oneworks_game_url = $this->getSystemInfo('oneworks_game_url');
			$this->oneworks_mobile_game_url = $this->getSystemInfo('oneworks_mobile_game_url');
		}

		$this->CI->utils->debug_log('oneworks queryForwardGame login playerName: ', $playerName);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->CI->utils->debug_log('oneworks queryForwardGame login playerName: ', $gameUsername);
		if (empty($gameUsername)) {
			$this->CI->utils->debug_log('oneworks queryForwardGame login playerName: Empty');
			$this->CI->utils->debug_log('oneworks queryForwardGame login demoURL: ', $this->oneworks_game_url);
			$result = array('success' => true, 'sessionToken' => null, 'url' => $this->oneworks_game_url . '/vender.aspx?lang=' . $language . '&WebSkinType=' . $this->web_skin_type . '&skincolor=' . $this->skincolor, 'is_mobile' => $extra['is_mobile']);

		} else {
			$this->CI->utils->debug_log('oneworks queryForwardGame login playerName: Not Empty');
			if($this->white_launcher_domain){
				$this->CI->utils->debug_log('oneworks queryForwardGame with white launcher domain');
				$nextUrl=$this->generateGotoUri($playerName, $extra);
				$result=$this->forwardToWhiteDomain($playerName, $nextUrl);
				if($result['success']){
					return $result;
				}
			}

			$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			$this->CI->load->model(array('common_token'));
			$token = $this->token_prefix.$this->CI->common_token->createTokenBy($playerId, 'player_id');
			if($this->enable_player_language){
				$player_language = $this->CI->player_model->getLanguageFromPlayer($playerId);
				$language = $this->getLanguage($player_language);
			}
			if ($extra['game_type'] == self::SPORTSBOOK_GAME) {
				if (!$extra['is_mobile']) {
					$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&WebSkinType=' . $this->web_skin_type;
					if($this->enable_skin_color && $this->skincolor){
						$url .= "&skincolor=".$this->skincolor;
					}
				// } elseif ($extra['is_mobile'] == 'mobile') {
				} else {
					switch ($language){// start OGP-10173
						case 'cs':
							$oType = 2;
							break;
						case 'id':
							$oType = 3;
							break;
						case 'th':
							$oType = 4;
							break;
					} // end OGP-10173
					$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&OType=' . $oType;

					if ($oneworks_mobile_home_url) {
						$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
						$url .= '&homeUrl='.$protocol . $oneworks_mobile_home_url;
					}
					if ($this->oneworks_mobile_extend_session_url) {
						$url .= '&extendSessionUrl=' . $this->oneworks_mobile_extend_session_url;
					}
				}
			} elseif ($extra['game_type'] == self::LIVECASINO_GAME) {
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=livecasino';
			} elseif ($extra['game_type'] == self::CASINO_GAME) {
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=casino';
			} elseif (strtolower($extra['game_type']) == self::NUMBERGAME) {
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&act=numbergame';
			} elseif (strtolower($extra['game_type']) == self::KENO_GAME) {
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&game=keno';
				if($is_mobile){
					$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types=Keno';
				}
			} else {
				//for deeplink
				$game_type = implode(",",explode('_', $extra['game_type']));
				$url = $this->oneworks_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types='.$game_type;
				if($is_mobile){
					$url = $this->oneworks_mobile_game_url . '/Deposit_ProcessLogin.aspx?lang=' . $language . '&types='.$game_type;
				}
			}
			$result = array('success' => true,
				'sessionToken' => $token,
				'url' => $url,
				'domains' => $this->getSystemInfo('oneworks_cookie_domains'),
				'redirect' => ($this->white_launcher_domain) ? true : false,
				'system_code' => $this->system_code,
				'use_callback_game_launch' => true,
			);
			$this->CI->utils->debug_log('oneworks queryForwardGame login URL--------->: ', $url);
		}
		return $result;
	}


	/**
	 * overview : sync original game logs
	 *
	 * detail :  It is to get next 100 betting transactions detail which depends on version ID key.
	 * For 1st time call versioin_key must be 0
	 *
	 * @param $token
	 * @return array
	 */
	public function syncOriginalGameLogs($token) {
		$this->retry_empty_attempt = 0;
		$sync_by_trans_id = $this->getValueFromSyncInfo($token,'sync_by_trans_id'); # used in manual sync by trans_id
		$sync_trans_id = $this->getValueFromSyncInfo($token,'trans_id');# the trans_id to be manual sync
		
		# we will trigger here the sync by trans_id, only one result, same result as GetBetDetail
		if($this->sync_by_trans_id || $sync_by_trans_id){
			return $this->getBetDetailByTransId($sync_trans_id,null,"processResultForSyncGameRecords");
		}

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$startDate = new DateTime($startDate->format('Y-m-d H:i:s'));
		$endDate = new DateTime($endDate->format('Y-m-d H:i:s'));
		$startDate->modify($this->getDatetimeAdjust());
		$this->CI->utils->debug_log('startDate', $startDate, 'endDate', $endDate);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncGameRecords',
			'sync_by_trans_id' => ((! $sync_by_trans_id) ? false : true)
		);
		//don't use anymore
		$this->CI->load->model(array('original_game_logs_model'));

        $this->ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

        $debug_last_version_key=$this->CI->utils->getConfig('debug_last_version_key');

		$done = false;
		$result = array("success" => false);
		while (!$done) {

			# get the last version key in db
			$last_version_key = $this->CI->external_system->getLastSyncIdByGamePlatform($this->getPlatformCode());
			$context['current_version_key'] = $last_version_key;
			if(!empty($debug_last_version_key)){
				$last_version_key=$debug_last_version_key;
				$this->CI->utils->debug_log('load debug_last_version_key', $debug_last_version_key);
			}
			//init value
			$version_key = $last_version_key;

			# check if last version key exist, if null it means it needs to call the first record which is 0 version key
			if (!empty($last_version_key)) {
				if ($this->oneworks_recheck_last_version_key_zero_flag) {
					$version_key = 0;
				} else {
					$version_key = $last_version_key;
				}
			} else {
				$version_key = 0; #for first time call
			}
			$params = array(
				"vendor_id" => $this->oneworks_vendor_id,
				"version_key" => $version_key,
			);

			$this->CI->utils->debug_log('version_key in loop -----------------------------------------> ', $version_key);
			$resultData = $this->callApi(self::API_syncGameRecords, $params, $context);


			$result = ["success" => $resultData['success']];
			//error or done
			$done = !$resultData['success'] ||  $resultData['done'];
			if(!$resultData['success']){
				$this->CI->utils->error_log('wrong result', $resultData);
				$result['error_message']=$resultData['error_message'];
			}

			if(!empty($debug_last_version_key)){
				$last_version_key = $this->CI->external_system->getLastSyncIdByGamePlatform($this->getPlatformCode());
				$this->CI->utils->debug_log('exists debug_last_version_key, so exit', $last_version_key);
				$done=true;
			}

			#if last version key is 0 it means no next record
			// if (@$resultData['last_version_key'] == 0 || empty($resultData)) {
			// 	// $result = array("success" => true);
			// 	$done = true;
			// 	break;
			// }
		}

		return $result;
	}
	public function preProcessData(&$gamegameRecords){
		if(!empty($gamegameRecords)){
			foreach ($gamegameRecords as $key => $value) {
				if(!array_key_exists('settlement_time', $value)){
					//set default value if settlement time is not exist, this is used for checking md5
					$gamegameRecords[$key]['settlement_time'] = $value['transaction_time'];
				}
			}
		}
	}
	/**
	 * overview : processing result for syncgameRecords
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForSyncGameRecords($params) {

        $this->CI->load->model(array('original_game_logs_model'));

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$sync_by_trans_id = $this->getVariableFromContext($params,'sync_by_trans_id'); # if true, it's manual sync by trans_id

		// load models
		$this->CI->load->model(array('oneworks_game_logs', 'external_system'));
		$result = ['last_version_key'=>null, 'done'=>false, 'data_count'=>0];
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr);
		if ($success) {
			$betDetailsArr = array();
			$betNumberDetailsArr = array();
			$betVirtualSportDetailsArr = array();
			$gameRecords = array();
			$lastVersionKey = null;
			if (isset($resultJsonArr['Data']) && !empty(@$resultJsonArr['Data'])) {
				$lastVersionKey = (! $sync_by_trans_id) ? @$resultJsonArr['Data']['last_version_key'] : @$resultJsonArr['Data']['BetDetails'][0]['version_key'];

				$result['last_version_key'] = $lastVersionKey;

				if (isset($resultJsonArr['Data']['BetDetails']) && !empty($resultJsonArr['Data']['BetDetails'])) {
					$betDetailsArr = $resultJsonArr['Data']['BetDetails'];
				}
				if (isset($resultJsonArr['Data']['BetNumberDetails']) && !empty($resultJsonArr['Data']['BetNumberDetails'])) {
					$betNumberDetailsArr = $resultJsonArr['Data']['BetNumberDetails'];
				}
				if (isset($resultJsonArr['Data']['BetVirtualSportDetails']) && !empty($resultJsonArr['Data']['BetVirtualSportDetails'])) {
					$betVirtualSportDetailsArr = $resultJsonArr['Data']['BetVirtualSportDetails'];
				}

				$this->CI->utils->debug_log('betDetailsArr: '.count($betDetailsArr).', betNumberDetailsArr:'.count($betNumberDetailsArr).', betVirtualSportDetailsArr:'.count($betVirtualSportDetailsArr));

				//check array
				$gameRecords = array_merge( $betDetailsArr,  $betNumberDetailsArr, $betVirtualSportDetailsArr);
			}
			$this->preProcessData($gameRecords);
			if (!empty($gameRecords) && is_array($gameRecords)) {

				$oldCnt=count($gameRecords);
				$this->CI->original_game_logs_model->removeDuplicateUniqueid($gameRecords, 'trans_id', function($row1st, $row2nd){
					//compare status
					$status1st=strtolower($row1st['ticket_status']);
					$status2nd=strtolower($row2nd['ticket_status']);
					//if same status, keep sencond
					if($status1st==$status2nd){
						return 2;
					}else if($status1st=='waiting'){
						return 2;
					}else if($status2nd=='waiting'){
						return 1;
					}else if($status1st=='running'){
						return 2;
					}else if($status2nd=='running'){
						return 1;
					}
					//default is last
					return 2;
				});
				$cnt=count($gameRecords);

				$this->CI->utils->debug_log('removeDuplicateUniqueid oldCnt:'.$oldCnt.', cnt:'.$cnt);

				// $this->CI->utils->debug_log('gameRecords', count($gameRecords));
				//gameRecords to insertRows and updateRows by external_uniqueid and md5
                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('oneworks_game_logs', $gameRecords,
                    'trans_id', 'external_uniqueid', self::MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', self::MD5_FLOAT_AMOUNT_FIELDS);

                $this->CI->utils->debug_log('after process available rows', count($gameRecords), count($insertRows), count($updateRows));

                unset($gameRecords);

                if (!empty($insertRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    	['responseResultId'=>$responseResultId, 'lastVersionKey'=>$lastVersionKey]);
                }
                unset($insertRows);

                if (!empty($updateRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                		['responseResultId'=>$responseResultId, 'lastVersionKey'=>$lastVersionKey]);
                }
                unset($updateRows);

			    $debug_last_version_key=$this->CI->utils->getConfig('debug_last_version_key');
				//will update last sync id, not debug
				# if sync_by_trans_id is true, dont update the last sync id(last_version_key)
				if (!empty($lastVersionKey) && empty($debug_last_version_key) && (! $sync_by_trans_id)) {
					$this->CI->external_system->setLastSyncId($this->getPlatformCode(), $lastVersionKey);
				}
			}else{
				$current_version_key = $this->getVariableFromContext($params,'current_version_key');
				$lastVersionKey =isset($resultJsonArr['Data']['last_version_key'])? $resultJsonArr['Data']['last_version_key'] : $current_version_key;

				if($lastVersionKey > $current_version_key && $this->retry_empty_attempt < $this->max_retry_empty_attempt){
					$this->CI->external_system->setLastSyncId($this->getPlatformCode(), $lastVersionKey);
					$this->retry_empty_attempt++;
					$result['done']=false;
				} else {
					if(!is_array($gameRecords)){
						$this->CI->utils->error_log('wrong game records', $gameRecords);
						$result['error_message']='wrong type of game records';
					}
					// $result = array('last_version_key' => 0);
					$result['done']=true;
					//empty result
				}
			}
		} else {
			$result['done']=true;
			$result['error_message']='get wrong result from api';
			// $result = array('last_version_key' => 0);
		}

		return array($success, $result);
	}

    public function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){
        $dataCount=0;
        if(!empty($rows)){
            $lastVersionKey=$additionalInfo['lastVersionKey'];
            $responseResultId=$additionalInfo['responseResultId'];
            foreach ($rows as $record) {

				$transaction_time = $this->gameTimeToServerTime(@$record['transaction_time']);
				$bet_amount = @$record['stake'];
				$result_amount = @$record['winlost_amount'];
				$ticket_status = @$record['ticket_status'];
				$game_code = @$record['sport_type'] ?: 'parlay';
				$external_uniqueid=$trans_id = @$record['trans_id'];
				$data = [
					'last_version_key' => $lastVersionKey,
					'trans_id' => $trans_id,
					'vendor_member_id' => @$record['vendor_member_id'],
					'operator_id' => @$record['operator_id'],
					'league_id' => @$record['league_id'],
					'match_id' => @$record['match_id'],
					'home_id' => @$record['home_id'],
					'away_id' => @$record['away_id'],
					'match_datetime' => $this->gameTimeToServerTime(@$record['match_datetime']),
					'settlement_time' => $this->gameTimeToServerTime(@$record['settlement_time']),
					'bonus_code' => @$record['bonus_code'],
					'wallet_type' => @$record['wallet_type'],
					'ref_code' => @$record['ref_code'],
					'sport_type' => $game_code,
					'bet_type' => @$record['bet_type'],
					'parlay_ref_no' => @$record['parlay_ref_no'],
					'odds' => @$record['odds'],
					'stake' => $bet_amount,
					'transaction_time' => $this->gameTimeToServerTime(@$record['transaction_time']),
					'ticket_status' => $ticket_status,
					'winlost_amount' => $result_amount,
					'after_amount' => @$record['after_amount'],
					'currency' => @$record['currency'],
					'winlost_datetime' => $this->gameTimeToServerTime(@$record['winlost_datetime']),
					'odds_type' => @$record['odds_type'],
					'bet_team' => @$record['bet_team'],
					'home_hdp' => @$record['home_hdp'],
					'away_hdp' => @$record['away_hdp'],
					'hdp' => @$record['hdp'],
					'betfrom' => @$record['betfrom'],
					'islive' => @$record['islive'],
					'home_score' => @$record['home_score'],
					'away_score' => @$record['away_score'],
					'custom_info_1' => @$record['customInfo1'],
					'custom_info_2' => @$record['customInfo2'],
					'custom_info_3' => @$record['customInfo3'],
					'custom_info_4' => @$record['customInfo4'],
					'custom_info_5' => @$record['customInfo5'],
					'ba_status' => @$record['ba_status'],
					'version_key' => @$record['version_key'],
					'parlay_data' =>  isset($record['ParlayData'])? json_encode(@$record['ParlayData']): null,
					'external_uniqueid' => $external_uniqueid,
					'parlay_type' => @$record['parlay_type'],
					'combo_type' => @$record['combo_type'],
					'cash_out_data' => isset($record['CashOutData']) ? json_encode($record['CashOutData']) : NULL,
					'original_stake' => isset($record['original_stake']) ? @$record['original_stake'] : NULL,
					'last_updated_time'=>null, // $this->gameTimeToServerTime(@$record['transaction_time']), //?
                    'response_result_id' =>$responseResultId,
                    'md5_sum'=>$record['md5_sum'],
                    'last_sync_time'=>$this->CI->utils->getNowForMysql(),
				];

                //insert or update data to t1lottery API gamelogs table database
                if ($update_type=='update') {
                    $data['id']=$record['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('oneworks_game_logs', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('oneworks_game_logs', $data);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }

	public function syncOriginalGameResult($token){
		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$this->CI->load->model(array('oneworks_game_logs'));
		$data = $this->CI->oneworks_game_logs->getOneworksGameMatchIds($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo->format('Y-m-d H:i:s'));
		$match_ids = implode(",",array_unique($data));
		if(!empty($match_ids)){
			return $this->getGameDetail($match_ids);
		}
		return array("success"=> true);
	}

    //======start merge=====================

	/**
	 * overview : get game record status
	 *
	 * @param $status
	 * @return int
	 */
	private function getGameRecordsStatus($status) {
		$this->CI->load->model(array('game_logs'));
		$status = strtolower($status);

		switch ($status) {
		case 'running':
			$status = Game_logs::STATUS_ACCEPTED;
			break;
		case 'reject':
			$status = Game_logs::STATUS_REJECTED;
			break;
		case 'void':
			$status = Game_logs::STATUS_VOID;
			break;
		case 'refund':
			$status = Game_logs::STATUS_REFUND;
			break;
		case 'won':
		case 'draw':
		case 'lose':
		case 'half won':
		case 'half lose':
			$status = Game_logs::STATUS_SETTLED;
			break;
		}
		return $status;
	}

	/**
	 * overview : get game description information
	 *
	 * @param $row
	 * @param $unknownGame
	 * @param $gameDescIdMap
	 * @return array
	 */
	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;
		$game_type_id = null;
		if (isset($row['game_description_id'])) {
			$game_description_id = $row['game_description_id'];
			$game_type_id = $row['game_type_id'];
		}

		if(empty($game_description_id)){
			$gameDescId=$this->CI->game_description_model->processUnknownGame($this->getPlatformCode(),
				$unknownGame->game_type_id, $row['game'], $row['game_code']);
		}

		return [$game_description_id, $game_type_id];
	}

    /**
     *
     * perpare original rows, include process unknown game, pack bet details, convert game status
     *
     * @param  array &$row
     */
    public function preprocessOriginalRowForGameLogs(array &$row){

        $game_description_id = $row['game_description_id'];
        $game_type_id = $row['game_type_id'];

        if (empty($game_description_id)) {
            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $this->getUnknownGame());
        }
        $row['game_description_id']=$game_description_id;
        $row['game_type_id']=$game_type_id;

		$status = $this->getGameRecordsStatus($row['status_in_db']);

		$_bet_details = [
			'bet_details' => $this->generateBetDetails($row),
			'match_details' => $row['parlay_data'] ? 'N/A': $this->generateMatchDetails($row),
		];
		
		$row['bet_details']=json_encode($_bet_details);
//		$row['bet_details'] = $this->generateBetDetails($row);

        $row['status']=$status;
        $row['bet_type']=$row['parlay_data'] ? Game_logs::BET_TYPE_MULTI_BET : Game_logs::BET_TYPE_SINGLE_BET;

        //$row['match_details']=$row['parlay_data'] ? 'N/A':json_encode($this->generateMatchDetails($row));

        $row['match_type']= $this->getBetType($row['bet_type_oneworks']);
        $row['is_parlay']= !empty($row['parlay_data']) ? true : false;

    }

    /**
     * queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){

        $sqlTime='oneworks.last_sync_time >= ? and oneworks.last_sync_time <= ?';
        if($use_bet_time){
          	$sqlTime='oneworks.transaction_time >= ? and oneworks.transaction_time <= ?';
        }

		$sql = <<<EOD
SELECT oneworks.id as sync_index,
oneworks.trans_id,
oneworks.response_result_id,
oneworks.vendor_member_id as player_username,
oneworks.stake as bet_amount,
oneworks.stake as real_bet_amount,
oneworks.winlost_amount as result_amount,
oneworks.transaction_time as start_at,
oneworks.transaction_time as end_at,
oneworks.transaction_time as bet_at,
oneworks.settlement_time,
oneworks.bonus_code,
oneworks.wallet_type,
oneworks.ref_code,
oneworks.odds,
oneworks.odds_type,
oneworks.hdp,
oneworks.league_id,
oneworks.home_id,
oneworks.away_id,
oneworks.bet_team,
oneworks.parlay_data,
oneworks.isLive,
oneworks.ticket_status as status_in_db,
oneworks.bet_type as bet_type_oneworks,
oneworks.match_id,
oneworks.sport_type as game_code,
oneworks.sport_type as game,
oneworks.sport_type,
oneworks.home_score,
oneworks.away_score,
oneworks.parlay_ref_no,
oneworks.parlay_type,
oneworks.combo_type,
oneworks.cash_out_data,
oneworks.home_hdp,
oneworks.away_hdp,
oneworks.original_stake,
oneworks.after_amount as after_balance,
oneworks.last_sync_time as updated_at,
oneworks.external_uniqueid,
oneworks.md5_sum,
oneworks.winlost_datetime,
oneworks.version_key,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id

FROM oneworks_game_logs as oneworks
LEFT JOIN game_description as gd ON oneworks.sport_type = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON oneworks.vendor_member_id = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE

{$sqlTime}

EOD;
#AND oneworks.ticket_status IN ('WON','LOSE','DRAW')
		// $this->utils->debug_log($sql);

        $params=[$this->getPlatformCode(), $this->getPlatformCode(),
          $dateFrom,$dateTo];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);

    }

    const MD5_FIELDS_FOR_MERGE=[
        'trans_id', 'status_in_db', 'bet_amount', 'result_amount',
        'player_username', 'end_at', 'winlost_datetime', 'sport_type', 'version_key','odds_type','odds','settlement_time'];

    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        'bet_amount', 'result_amount',];

    /**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){

        $extra_info=[
        	// 'match_details'=>$row['match_details'],
        	'match_type' => $row['match_type'],
        	'handicap' => $row['hdp'],
        	'odds' => $row['odds'],
        	'odds_type' => $row['odds_type'],
        	'is_parlay' => $row['is_parlay']
        ];

        $has_both_side=0;
        if(!empty($row['cash_out_data']))
        {
        	$cash_out_stake = 0;
        	$cash_out_data = json_decode($row['cash_out_data']);
    		foreach ($cash_out_data as $key => $cashout) {
    			$row['result_amount'] += ((float)$cashout->buyback_amount - $cashout->real_stake);
    			$cash_out_stake += (float)$cashout->stake;
            }
            $extra_info['trans_amount'] = (!empty($row['original_stake'])) ? $row['original_stake'] : $cash_out_stake + $row['bet_amount'];
        }

        if(strtolower($row['status_in_db']) == self::STATUS_DRAW){//no available amount when draw status
        	$row['bet_amount'] = 0;
        	$extra_info['note'] = lang("Draw");
        }

        //fix md5_sum
        if(empty($row['md5_sum'])){
        	//genereate md5 sum
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
            	self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }

        if(!empty($row['settlement_time'])){
        	$row['end_at'] = $row['settlement_time'];
        }

        return [
        	//set game_type to null unless we know exactly game type name from original game logs
            'game_info'=>['game_type_id'=>$row['game_type_id'], 'game_description_id'=>$row['game_description_id'],
                'game_code'=>$row['game_code'], 'game_type'=>null, 'game'=>$row['game']],
            'player_info'=>['player_id'=>$row['player_id'], 'player_username'=>$row['player_username']],
            'amount_info'=>['bet_amount'=>$row['bet_amount'], 'result_amount'=>$row['result_amount'],
                'bet_for_cashback'=>$row['bet_amount'], 'real_betting_amount'=>$row['real_bet_amount'],
                'win_amount'=>null, 'loss_amount'=>null, 'after_balance'=>$row['after_balance']],
            'date_info'=>['start_at'=>$row['start_at'], 'end_at'=>$row['end_at'], 'bet_at'=>$row['bet_at'],
                'updated_at'=>$row['updated_at']],
            'flag'=>Game_logs::FLAG_GAME,
            'status'=>$row['status'],
            'additional_info'=>['has_both_side'=>$has_both_side, 'external_uniqueid'=>$row['external_uniqueid'], 'round_number'=>$row['external_uniqueid'],
                'md5_sum'=>$row['md5_sum'], 'response_result_id'=>$row['response_result_id'], 'sync_index'=>$row['sync_index'],
                'bet_type'=>$row['bet_type'] ],
            'bet_details'=>$row['bet_details'],
            'extra'=>$extra_info,
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];

    }

	/**
	 * overview : syncMergeTogameLogs
	 *
	 * @param $token
	 * @return array
	 */
	public function syncMergeToGameLogs($token) {

        $enabled_game_logs_unsettle=true;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);

	}

    private function getTeamCacheKey($team_id, $bet_type){
    	return 'game-api-'.$this->getPlatformCode().'-team-'.$team_id.'-'.$bet_type;
    }

    public function getTeamName($team_id, $bet_type = null) {

    	$teamKey=$this->getTeamCacheKey($team_id, $bet_type);

    	//try get from cache
    	$rlt=$this->CI->utils->getJsonFromCache($teamKey);
    	if(!empty($rlt)){
    		return $rlt;
    	}

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetTeamName',
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "team_id" => $team_id,
        );
        if(!empty($bet_type)){
        	$params['bet_type'] = $bet_type;
        }

        $rlt=$this->callApi(self::API_getTeamName, $params, $context);
        //save it to cache
        if($rlt['success']){
        	$this->CI->utils->saveJsonToCache($teamKey, $rlt);
        }
        return $rlt;
    }

    public function processResultForGetTeamName($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        $result = array();
        $data = array();
        $availableLanguages = array('cs','en','ko','vi','id');
        if($success){
            foreach ($resultJsonArr['Data']['names'] as $key => $teamNames) {
                if(in_array($teamNames['lang'], $availableLanguages)){
                    $data['teamName'][$teamNames['lang']] = $teamNames['name'];
                }
            }
            if (!array_key_exists($this->bet_detail_default_lang,$data['teamName'])){
            	//if set language not exist on the team name translatetion, will create and default will be english
            	$data['teamName'][$this->bet_detail_default_lang] = $data['teamName']['en'];
            }
        }
        $result['teamName'] = isset($data['teamName'][$this->bet_detail_default_lang])? $data['teamName'][$this->bet_detail_default_lang] : null;
        return array($success, $result);
    }

    public function getGameDetail($matchIds)
    {
    	$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetGameDetail',
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "match_ids" => $matchIds,
        );
        return $this->callApi(self::API_getGameDetail, $params, $context);
    }

    public function processResultForGetGameDetail($params)
    {
    	$this->CI->load->model(array('oneworks_game_logs'));
    	$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        if($success){
        	$results =  $resultJsonArr['Data'];
        	if(!empty($results)){
        		foreach ($results as $record) {
        			$insertRecord = array();
        			//Data from Oneworks API
					$insertRecord['match_id'] 		= isset($record['match_id']) ? $record['match_id'] : NULL;
					$insertRecord['league_id'] 		= isset($record['league_id']) ? $record['league_id'] : NULL;
					$insertRecord['home_id'] 		= isset($record['home_id']) ? $record['home_id'] : NULL;
					$insertRecord['away_id'] 		= isset($record['away_id']) ? $record['away_id'] : NULL;
					$insertRecord['home_score'] 		= isset($record['home_score']) ? $record['home_score'] : NULL;
					$insertRecord['away_score'] 		= isset($record['away_score']) ? $record['away_score'] : NULL;
					$insertRecord['ht_home_score'] 		= isset($record['ht_home_score']) ? $record['ht_home_score'] : NULL;
					$insertRecord['ht_away_score'] 		= isset($record['ht_away_score']) ? $record['ht_away_score'] : NULL;
					$insertRecord['game_status'] 		= isset($record['game_status']) ? $record['game_status'] : NULL;
					$insertRecord['sport_type'] 		= isset($record['sport_type']) ? $record['sport_type'] : NULL;
					$insertRecord['is_neutral'] 		= isset($record['is_neutral']) ? $record['is_neutral'] : NULL;
					$insertRecord['VirtualSport_info'] 		= isset($record['VirtualSport_info']) ? $record['VirtualSport_info'] : NULL;
					$insertRecord['first_ball'] 		= isset($record['first_ball']) ? $record['first_ball'] : NULL;
					$insertRecord['second_ball'] 		= isset($record['second_ball']) ? $record['second_ball'] : NULL;
					$insertRecord['third_ball'] 		= isset($record['third_ball']) ? $record['third_ball'] : NULL;
					$insertRecord['match_datetime'] 		= isset($record['match_datetime']) ? $record['match_datetime'] : NULL;
					$insertRecord['total_sum'] 		= isset($record['total_sum']) ? $record['total_sum'] : NULL;
					$insertRecord['over_count'] 		= isset($record['over_count']) ? $record['over_count'] : NULL;
					$insertRecord['win_item'] 		= isset($record['win_item']) ? $record['win_item'] : NULL;
					$insertRecord['table_no'] 		= isset($record['table_no']) ? $record['table_no'] : NULL;
					$insertRecord['shoe_no'] 		= isset($record['shoe_no']) ? $record['shoe_no'] : NULL;
					$insertRecord['hand_no'] 		= isset($record['hand_no']) ? $record['hand_no'] : NULL;
					$insertRecord['game_no'] 		= isset($record['GameNo']) ? $record['GameNo'] : NULL;
					$insertRecord['settlement_time'] 		= isset($record['settlement_time']) ? $record['settlement_time'] : NULL;
					// $insertRecord['bonus_code']		= isset($record['bonus_code']) ? $record['bonus_code'] : NULL;
					// $insertRecord['wallet_type']		= isset($record['wallet_type']) ? $record['wallet_type'] : NULL;
					// $insertRecord['ref_code']		= isset($record['ref_code']) ? $record['ref_code'] : NULL;
					//virtual sports result
					$insertRecord['colours'] 		= isset($record['Colours']) ? $record['Colours'] : NULL;
					$insertRecord['event_date'] 		= isset($record['EventDate']) ? $record['EventDate'] : NULL;
					$insertRecord['event_id'] 		= isset($record['EventID']) ? $record['EventID'] : NULL;
					$insertRecord['event_status'] 		= isset($record['EventStatus']) ? $record['EventStatus'] : NULL;
					$insertRecord['human_id'] 		= isset($record['HumanID']) ? $record['HumanID'] : NULL;
					$insertRecord['is_favor'] 		= isset($record['isFavor']) ? $record['isFavor'] : NULL;
					$insertRecord['key'] 		= isset($record['Key']) ? $record['Key'] : NULL;
					$insertRecord['kick_off_time'] 		= isset($record['KickOffTime']) ? $record['KickOffTime'] : NULL;
					$insertRecord['lane'] 		= isset($record['Lane']) ? $record['Lane'] : NULL;
					$insertRecord['place_odds'] 		= isset($record['PlaceOdds']) ? $record['PlaceOdds'] : NULL;
					$insertRecord['placing'] 		= isset($record['Placing']) ? $record['Placing'] : NULL;
					$insertRecord['racer_id'] 		= isset($record['RacerID']) ? $record['RacerID'] : NULL;
					$insertRecord['racer_num'] 		= isset($record['RacerNum']) ? $record['RacerNum'] : NULL;
					$insertRecord['win_odds'] 		= isset($record['WinOdds']) ? $record['WinOdds'] : NULL;
					//extra info from SBE
					$insertRecord['response_result_id'] = $responseResultId;
					$isExists = $this->CI->oneworks_game_logs->isMatchIdAlreadyExists($insertRecord['match_id']);
					if ($isExists) {
						$insertRecord['updated_at'] = date('Y-m-d H:i:s');
						$this->CI->oneworks_game_logs->updateOneworksGameResult($insertRecord);
					} else {
						$insertRecord['created_at'] = date('Y-m-d H:i:s');
						$this->CI->oneworks_game_logs->insertOneworksGameResult($insertRecord);
					}
        		}
        	}
        }
        return array($success);
    }

    private function getLeagueCacheKey($league_id){
    	return 'game-api-'.$this->getPlatformCode().'-league-'.$league_id;
    }

    public function getLeagueName($league_id) {
    	$leagueKey=$this->getLeagueCacheKey($league_id);

    	//try get from cache
    	$rlt=$this->CI->utils->getJsonFromCache($leagueKey);
    	if(!empty($rlt)){
    		return $rlt;
    	}

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetLeagueName',
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "league_id" => $league_id,
        );

        $rlt=$this->callApi(self::API_getLeagueName, $params, $context);
        if($rlt['success']){
        	$this->CI->utils->saveJsonToCache($leagueKey, $rlt);
        }
        return $rlt;
    }

    public function processResultForGetLeagueName($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        $result = array();
        $data = array();
        $availableLanguages = array('cs','en','ko','vi','id');
        if($success){
            foreach ($resultJsonArr['Data']['names'] as $key => $leagueName) {
                if(in_array($leagueName['lang'], $availableLanguages)){
                    $data['leagueName'][$leagueName['lang']] = $leagueName['name'];
                }
            }
            if (!array_key_exists($this->bet_detail_default_lang,$data['leagueName'])){
            	//if set language not exist on the team name translatetion, will create and default will be english
            	$data['leagueName'][$this->bet_detail_default_lang] = $data['leagueName']['en'];
            }
        }
        $result['leagueName'] = isset($data['leagueName'][$this->bet_detail_default_lang])? $data['leagueName'][$this->bet_detail_default_lang] : null;
        return array($success, $result);
    }

    public function getVirtualResultCacheKey($event_ids, $sport_type){

    	return 'game-api-'.$this->getPlatformCode().'-virtualresult-'.$event_ids.'-'.$sport_type;

    }

    public function getVirtualResult($sport_type,$event_ids) {
    	$key=$this->getVirtualResultCacheKey($event_ids,$sport_type);
    	$rlt=$this->CI->utils->getJsonFromCache($key);
    	if(!empty($rlt)){
    		return $rlt;
    	}

    	$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetVirtualResult',
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "sport_type" => $sport_type,
            "event_ids" => $event_ids
        );

        $rlt=$this->callApi(self::API_getVirtualResult, $params, $context);

        if($rlt['success']){
        	$this->CI->utils->saveJsonToCache($key, $rlt);
        }
        return $rlt;

    }

    public function processResultForGetVirtualResult($params){
    	$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        return array($success, $resultJsonArr);
    }

    public function getTranIdCacheKey($trans_id, $sport_type){

    	return 'game-api-'.$this->getPlatformCode().'-transid-'.$trans_id.'-'.$sport_type;

    }

    public function getBetDetailByTransId($trans_id,$sport_type = null,$callback = "processResultForGetBetDetailByTransId"){

    	$key=$this->getTranIdCacheKey($trans_id,$sport_type);
    	$rlt=$this->CI->utils->getJsonFromCache($key);
    	if(!empty($rlt)){
    		return $rlt;
    	}

    	$context = array(
            'callback_obj' => $this,
            'callback_method' => $callback,
			'sport_type' => $sport_type,
			'sync_by_trans_id' => true
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "trans_id" => $trans_id,
        );

        $rlt=$this->callApi(self::API_getBetDetailByTransId, $params, $context);
        if($rlt['success']){
        	$this->CI->utils->saveJsonToCache($key, $rlt);
        }
        return $rlt;
    }

    public function processResultForGetBetDetailByTransId($params){
    	$sport_type = $this->getVariableFromContext($params, 'sport_type');
    	$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $result = array();
        if(!empty($resultJsonArr)){
        	if($sport_type == self::NUMBER_GAME) {
        		$result = $resultJsonArr['Data']['BetNumberDetails'][0];
        	} else if(in_array($sport_type, self::VIRTUAL_SPORTS)) {
        		$result = $resultJsonArr['Data']['BetVirtualSportDetails'][0];
        	} else {
        		$result = $resultJsonArr['Data']['BetDetails'][0];
        	}
        }
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        return array($success, $result);
    }

    public function getSystemParlayDetail($parlay_ref_no){
    	$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetSystemParlayDetail',
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "ref_no" => $parlay_ref_no,
        );

        return $this->callApi(self::API_getSystemParlayDetail, $params, $context);
    }

    public function processResultForGetSystemParlayDetail($params){
    	$responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr);
        $result = array();
        if(!empty($resultJsonArr)){
        	$result = $resultJsonArr['Data'][0];
        }
        return array($success, $result);
    }

	/**
	 * overview : query player balance
	 *
	 * @param $playerNames
	 * @param null $syncId
	 * @return array
	 */
	public function batchQueryPlayerBalance($playerNames, $syncId = null) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : game amount to db
	 *
	 * @param $amount
	 * @return float
	 */
	public function gameAmountToDB($amount) {
		//only need 2
		return round(floatval($amount), 2);
	}

    public function syncOriginalMd5Sum($token){

        $startDate = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');

    	$tableName='oneworks_game_logs';
    	$qryStr=' and transaction_time>="'.$this->CI->utils->formatDateTimeForMysql($startDate).'"'.
			' and transaction_time<="'.$this->CI->utils->formatDateTimeForMysql($endDate).'"';

    	return $this->commonSyncOriginalMD5Sum($tableName, $qryStr, self::MD5_FIELDS_FOR_ORIGINAL, self::MD5_FLOAT_AMOUNT_FIELDS,
            'md5_sum', 'id');

	}

}

/*end of file*/