<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_lb extends Abstract_game_api {

	const STATUS_SUCCESS = '00';

	const URI_MAP = array(
		self::API_createPlayer 		 => 'keno_create_member.ashx',
		self::API_login 			 => 'login.ashx',
		self::API_logout 			 => 'SuspenseMember',
		self::API_queryPlayerBalance => 'keno_member_wallet.ashx',
		self::API_depositToGame 	 => 'keno_credit_fund.ashx',
		self::API_withdrawFromGame   => 'keno_debit_fund.ashx',
		self::API_syncGameRecords    => 'keno_game_transaction_detail.ashx',
	);

	const BET_TYPE_PARLAY = 'PARLAY';
	const MATCH_AREA_PARLAY = 'Parlay';

	public function __construct() {
		parent::__construct();
		$this->lb_api_url 			= $this->getSystemInfo('lb_api_url');
		$this->lb_secret_key 		= $this->getSystemInfo('lb_secret');
		$this->lb_operator_id 		= $this->getSystemInfo('lb_operator_id');
		$this->lb_operator_code 		= $this->getSystemInfo('lb_operator_code');
		$this->lb_site_code 		= $this->getSystemInfo('lb_site_code');
		$this->lb_product_code 		= $this->getSystemInfo('lb_product_code');
		$this->lb_min_transfer 		= $this->getSystemInfo('lb_min_transfer');
		$this->lb_max_transfer 		= $this->getSystemInfo('lb_max_transfer');
		$this->lb_currency 			= $this->getSystemInfo('lb_currency');
		$this->lb_language 			= $this->getSystemInfo('lb_language');
		$this->lb_member_type 		= $this->getSystemInfo('lb_member_type');
		$this->lb_cookie_domain 	= $this->getSystemInfo('lb_cookie_domain');
		$this->lb_game_url 			= $this->getSystemInfo('lb_game_url');
	}

	public function getPlatformCode() {
		return LB_API;
	}

	public function callback($method, $result = null) {
		$this->CI->utils->debug_log('Game_api_lb (Callback): ', $result);
		if ($method == 'validatemember') {
			parse_str($result, $resArr);
			$this->CI->load->model(array('common_token'));

			$this->CI->utils->debug_log('Game_api_lb (Callback)  Result Array:', $resArr);
			$datetime = new DateTime();

			$token = isset($resArr['session_token'])?$resArr['session_token']:null;
			$this->CI->utils->debug_log('Game_api_lb (Callback)  token:', $token);

			//default result will return if failed
			$data = array(
				"status_code" => self::STATUS_SUCCESS,
				"status_text" => "OK",
			);

			if (!empty($token)) {
				$playerId = $this->CI->common_token->getPlayerIdByToken($token);

				$this->CI->utils->debug_log('playerId', $playerId);
				if (!empty($playerId)) {
					$login_name = $this->getGameUsernameByPlayerId($playerId);
					$this->CI->utils->debug_log('playerId', $playerId, 'login_name', $login_name);
					if (!empty($login_name)) {
						//this result will return if success
						$data = array(
							"status_code"	=> self::STATUS_SUCCESS,
							"status_text" 	=> "OK",
							"currency" 		=> $this->lb_currency,
							"member_id" 	=> $login_name,
							"member_name" 	=> $login_name,
							"language" 		=> $this->lb_language,
							"member_type" 	=> $this->lb_member_type,
							"balance" 		=> "0",
							"min_transfer" 	=> $this->lb_min_transfer,
							"max_transfer" 	=> $this->lb_max_transfer,
							"member_type" 	=> "CASH",
							"datetime" 		=> $datetime->format('m/d/Y H:i:s'),
						);
					}
				}
			}

			$xml_object = new SimpleXMLElement("<?xml version='1.0'?><authenticate></authenticate>");
			$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
			$this->CI->utils->debug_log('Game_api_lb (Callback)  XML Return:', $xmlReturn);
			return $xmlReturn;
		}
	}

	protected function customHttpCall($ch, $params) {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}

	public function generateUrl($apiName, $params) {
		$this->CI->utils->debug_log('LB generateUrl', $apiName, 'params', $params);
		$apiUri = self::URI_MAP[$apiName];
		$params_string = http_build_query($params);
		$url = $this->lb_api_url . "/" . $apiUri;
		return $url;
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = ! empty($resultArr) && $resultArr['status_code'] == self::STATUS_SUCCESS;
		if ( ! $success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('OPUS got error', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}
		return $success;
	}

	//===start createPlayer=====================================================================================
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {

		parent::createPlayer($playerName, $playerId, $password, $email, $extra);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$session_token = $this->getPlayerToken($playerId);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
		);

		$params = array(
			"secret_key" => $this->lb_secret_key,
			"site_code" => $this->lb_site_code,
			"product_code" => $this->lb_product_code,
			"operator_id" => $this->lb_operator_id,
			"session_token" => $session_token,
			"member_id" => $gameUsername,
			"member_name" => $gameUsername,
			"language" => $this->lb_language,
			"currency" => $this->lb_currency,
			"member_type" => $this->lb_member_type,
			"min_transfer" => $this->lb_min_transfer,
			"max_transfer" => $this->lb_max_transfer
		);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		return array($success, $resultArr);
	}
	//===end createPlayer=====================================================================================

	//===start queryPlayerBalance=====================================================================================
	public function queryPlayerBalance($playerName) {

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
		);

		$params = array(
			"secret_key" => $this->lb_secret_key,
			"site_code" => $this->lb_site_code,
			"product_code" => $this->lb_product_code,
			"operator_id" => $this->lb_operator_id,
			"member_id" => $gameUsername,
		);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		$result = array();
		if ($success && isset($resultArr['wallet']) && $resultArr['wallet'] !== null) {
			$result["balance"] = floatval($resultArr['wallet']);
			$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			$this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName', $playerName, 'balance', $resultArr['wallet']);
			if ( ! $playerId) {
				log_message('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			}
		} else {
			$success = false;
		}
		return array($success, $result);
	}

	public function queryPlayerInfo($playerName) {
		# code...
	}

	public function changePassword($playerName, $oldPassword, $newPassword) {
		# code...
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id = NULL) {

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$reference_id = date('YmdHis').random_string('alpha');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
		);

		$params = array(
			"secret_key" => $this->lb_secret_key,
			"site_code" => $this->lb_site_code,
			"product_code" => $this->lb_product_code,
			"operator_id" => $this->lb_operator_id,
			"member_id" => $gameUsername,
			"currency" => $this->lb_currency,
			"reference_id" => $reference_id,
			"Amount" => $amount,
		);

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		$result = array('response_result_id' => $responseResultId);

		if ($success) {
			// $afterBalance = isset($resultArr['wallet_end']) ? $resultArr['wallet_end'] : null;
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeMainWalletToSubWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
		} else {
			$result["userNotFound"] = true;
		}

		return array($success, $result);
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id = NULL) {

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$reference_id = date('YmdHis').random_string('alpha');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawToGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
		);

		$params = array(
			"secret_key" => $this->lb_secret_key,
			"site_code" => $this->lb_site_code,
			"product_code" => $this->lb_product_code,
			"operator_id" => $this->lb_operator_id,
			"member_id" => $gameUsername,
			"currency" => $this->lb_currency,
			"reference_id" => $reference_id,
			"Amount" => $amount,
		);

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	public function processResultForWithdrawToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		$result = array('response_result_id' => $responseResultId);

		if ($success) {
			// $afterBalance = isset($resultArr['wallet_end']) ? $resultArr['wallet_end'] : null;
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeSubWalletToMainWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
		} else {
			$result["userNotFound"] = true;
		}

		return array($success, $result);
	}

	public function login($playerName, $password = NULL) {
	
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'playerName' => $playerName,
		);

		return $this->callApi(self::API_login,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"session_token" => $playerName,
			),
			$context);
	}

	public function processResultForLogin($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		return array($success, $resultJson);
	}

	public function logout($playerName, $password = NULL) {
		# code...
	}

	public function updatePlayerInfo($playerName, $infos) {
		# code...
	}

	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = NULL, $dateTo = NULL) {
		# code...
	}

	public function queryGameRecords($dateFrom, $dateTo, $playerName = NULL) {
		# code...
	}

	public function checkLoginStatus($playerName) {
		# code...
	}

	public function queryTransaction($transactionId, $extra) {
		# code...
	}

	public function queryForwardGame($playerName, $extra) {
		$token = $this->getPlayerTokenByUsername($playerName);
		$language = $this->getLanguage($extra['language']);

		$url = $this->lb_game_url . '?s=' . $token . '&p=' . $this->lb_operator_code . '&lng=' . $language;

		if ($extra['is_mobile']) {
			$url .= '&platform=5';
		}

		return array('success' => true, 'url' => $url);
	}

	public function getLanguage($currentLang = null) {
		if(!empty($currentLang)){
			switch ($currentLang) {
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
            case LANGUAGE_FUNCTION::PLAYER_LANG_CHINESE :
            case 'zh-cn':
                $language = 'zh-cn';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
            case LANGUAGE_FUNCTION::PLAYER_LANG_INDONESIAN :
            case 'id-id':
                $language = 'id-id';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
            case LANGUAGE_FUNCTION::PLAYER_LANG_VIETNAMESE :
            case 'vi-vn':
                $language = 'vi-vn';
                break;
            case 'ko-kr':
            case LANGUAGE_FUNCTION::INT_LANG_KOREAN:
            case LANGUAGE_FUNCTION::PLAYER_LANG_KOREAN :
                $language = 'ko-kr';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_THAI:
            case Language_function::PLAYER_LANG_THAI :
				$language = 'th-th';
				break;
            default:
                $language = 'en_us';
                break;
        }
	        return $language;
		}
        return $this->language;    
	}

	public function syncOriginalGameLogs($token) {

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($startDate->format('Y-m-d H:i:s'));
		$endDate = new DateTime($endDate->format('Y-m-d H:i:s'));

		$startDate->modify($this->getDatetimeAdjust());

		$this->CI->utils->debug_log('startDate', $startDate, 'endDate', $endDate);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncGameRecords',
		);

		return $this->callApi(self::API_syncGameRecords, array(
			"secret_key" => $this->lb_secret_key,
			"operator_id" => $this->lb_operator_id,
			"site_code" => $this->lb_site_code,
			"product_code" => $this->lb_product_code,
			"start_time" => $startDate->format('Y-m-d H:i:s'),
			"end_time" => $endDate->format('Y-m-d H:i:s'),
			"status" => 'all',
		), $context);

	}

	public function processResultForSyncGameRecords($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultArr = json_decode(json_encode($resultXml), true);
		$success = $this->processResultBoolean($responseResultId, $resultArr);
		

		// load models
		$this->CI->load->model(array('lb_game_logs', 'external_system'));
		$result = array();

		if ($success) {

			$gameRecords = array();
			if (isset($resultArr['bet']['item']['@attributes'])) {
				$gameRecords = array($resultArr['bet']['item']['@attributes']);
			} else if (isset($resultArr['bet']['item'])) {
				$gameRecords = array_column($resultArr['bet']['item'], '@attributes');
			}
			
			if (! empty($gameRecords)) {

				$availableRows = $this->CI->lb_game_logs->getAvailableRows($gameRecords);
				
				foreach ($availableRows as &$gameRecord) {
					if($gameRecord['bet_status']!="settled"){
						continue; // if not settled continue. will not save unsettled gamelogs.
					}

					$bet_content = $gameRecord['bet_content'];
					$match_area = $gameRecord['match_area'];
					$data = [];

					if ($gameRecord['bet_type'] == self::BET_TYPE_PARLAY && empty($match_area)) {
						$match_area = self::MATCH_AREA_PARLAY;
						$bet_content = $this->generateBetDetails($gameRecord['bet_content']);
					}

					$data[] =  [
						'bet_amount' => $gameRecord['bet_money'],
						'win_amount' => $gameRecord['bet_winning'],
					  	'winloss_amount' => $gameRecord['bet_winning'] - $gameRecord['bet_money'],
					  	'odds' => $gameRecord['bet_odds'],
					  	'bet_placed' => $gameRecord['bet_content'],
					  	'won_side' => $gameRecord['bet_win'],
					  	'won_side' => $gameRecord['bet_win'],
					  	'bet_type' => ($match_area == self::MATCH_AREA_PARLAY) ? Game_logs::BET_TYPE_MULTI_BET : Game_logs::BET_TYPE_SINGLE_BET,
					  	'bet_content' => $bet_content,
					];	

					$record = array();
					$record['Username'] = $gameRecord['member_id'];
					$record['PlayerId'] = $this->getPlayerIdInGameProviderAuth($record['Username']);
					$record['member_id'] = $gameRecord['member_id'];
					$record['member_type'] = $gameRecord['member_type'];
					$record['session_token'] = $gameRecord['session_token'];
					$record['bet_id'] = $gameRecord['bet_id'];
					$record['bet_no'] = $gameRecord['bet_no'];
					$record['match_no'] = $gameRecord['match_no'];
					$record['match_area'] = $match_area;
					$record['match_id'] = $gameRecord['match_id'];
					$record['bet_type'] = $gameRecord['bet_type'];
					$record['bet_content'] = $this->CI->utils->encodeJson(['bet_details'=>$data]);
					$record['bet_currency'] = $gameRecord['bet_currency'];
					$record['bet_money'] = $gameRecord['bet_money'];
					$record['bet_odds'] = $gameRecord['bet_odds'];
					$record['bet_winning'] = $gameRecord['bet_winning'];
					$record['bet_win'] = $gameRecord['bet_win'];
					$record['bet_status'] = $gameRecord['bet_status'];
					$record['bet_time'] = $gameRecord['bet_time'];
					$record['trans_time'] = $gameRecord['trans_time'];
					$record['trans_time'] = $gameRecord['trans_time'];
					$record['bet_platform'] = $gameRecord['bet_platform'];
					$record['bet_previous_id'] = $gameRecord['bet_previous_id'];
					$record['game_platform'] = $this->getPlatformCode();
					$record['external_uniqueid'] = $gameRecord['bet_id'];
					$record['response_result_id'] = $responseResultId;

					$this->CI->lb_game_logs->insertLBGameLogs($record);
					$result['data'] = $availableRows[0];
				}

			}

		}

		return array($success, $result);
	}

	private function generateBetDetails($data) {
		if ($data) {
			$data_arr = explode('*',$data);
			$res = [];
			foreach($data_arr as $key => $value) {
			  $details = explode(" ", $value, 2); 
			  $res[$key] = $details[1];
			  $this->CI->utils->debug_log('generateBetDetails', $res[$key]);
			}
		}

		return $res;
	}

	public function syncMergeToGameLogs($token) {
		$this->CI->load->model(array('game_logs', 'player_model', 'lb_game_logs'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$this->CI->utils->debug_log('dateTimeFrom', $startDate, 'dateTimeTo', $endDate);


		$rlt = array('success' => true);
		$result = $this->CI->lb_game_logs->getLogStatistics($startDate, $endDate);

		$this->CI->utils->debug_log('result count', count($result));

		$cnt = 0;

		if ($result) {

			$unknownGame = $this->getUnknownGame();

			foreach ($result as $lb_data) {
				$player_id = $lb_data->PlayerId;

				if (!$player_id) {
					continue;
				}

				$cnt++;

				$bet_amount = $lb_data->bet_amount;
				$result_amount = $lb_data->result_amount - $bet_amount;

				$game_description_id = $lb_data->game_description_id;
				$roundNumber = $lb_data->round_id;
				$game_type_id = $lb_data->game_type_id;

				if (empty($game_description_id)) {
					$game_description_id = $unknownGame->id;
					$game_type_id = $unknownGame->game_type_id;
				}
				
				// $bet_details = [];

				// $bet_details[$lb_data->external_uniqueid] =  [
				// 		'bet_amount' => $lb_data->bet_amount,
				// 		'win_amount' => $lb_data->bet_winning,
				// 	  	'winloss_amount' => $result_amount,
				// 	  	'odds' => $lb_data->bet_odds,
				// 	  	'bet_placed' => $lb_data->bet_content,
				// 	  	'won_side' => $lb_data->bet_win,
				// 	  	// 'bet_type' => $data_val[3],
				// 		];	
				
				// $bet_details[] =  [
				// 		'bet_amount' => $lb_data->bet_amount,
				// 		'win_amount' => $lb_data->bet_winning,
				// 	  	'winloss_amount' => $result_amount,
				// 	  	'odds' => $lb_data->bet_odds,
				// 	  	'bet_placed' => $lb_data->bet_content,
				// 	  	'won_side' => $lb_data->bet_win,
				// 	  	// 'bet_type' => $data_val[3],
				// 		];	

				// $bet_details = $this->CI->utils->encodeJson(['bet_details'=>$bet_details]);

				$extra = array(
						'table' => $roundNumber,
						'bet_type' => $lb_data->bet_type,
						'bet_details' => $lb_data->bet_content,
						);

				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$lb_data->game_code,
					$lb_data->game_type,
					$lb_data->game,
					$player_id,
					$lb_data->Username,
					$bet_amount,
					$result_amount,
					null, # win_amount
					null, # loss_amount
					null, # after_balance
					0, # has_both_side
					$lb_data->external_uniqueid,
					$lb_data->date_start, //start
					$lb_data->date_end, //end
					$lb_data->response_result_id,
					null,
					$extra
				);

			}
		}

		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = NULL, $extra = NULL, $resultObj = NULL) {
		return array(false, null);
	}

	/*
	private $lb_api_domain;
	private $lb_secret_key;
	private $lb_operator_id;
	private $lb_site_code;
	private $lb_product_code;
	private $lb_min_transfer;
	private $lb_max_transfer;
	private $lb_currency;
	private $lb_language;
	// private $lb_balance;
	private $lb_member_type;

	const STATUS_SUCCESS = '00';

	const URI_MAP = array(
		self::API_createPlayer => 'keno_create_member',
		self::API_login => 'login',
		self::API_logout => 'keno_kickout_member',
		self::API_queryPlayerBalance => 'keno_member_wallet',
		self::API_depositToGame => 'keno_credit_fund',
		self::API_withdrawFromGame => 'keno_debit_fund',
		self::API_syncGameRecords => 'keno_game_transaction_detail',
		self::API_blockPlayer => 'keno_suspense_member',
		self::API_unblockPlayer => 'keno_resume_member',
		self::API_isPlayerExist => 'keno_member_wallet',
	);

	public function __construct() {
		parent::__construct();

		$this->lb_api_domain = $this->getSystemInfo('url');
		$this->lb_secret_key = $this->getSystemInfo('secret');
		$this->lb_operator_id = $this->getSystemInfo('lb_operator_id');
		$this->lb_site_code = $this->getSystemInfo('lb_site_code');
		$this->lb_product_code = $this->getSystemInfo('lb_product_code');
		$this->lb_min_transfer = $this->getSystemInfo('lb_min_transfer');
		$this->lb_max_transfer = $this->getSystemInfo('lb_max_transfer');
		$this->lb_currency = $this->getSystemInfo('lb_currency');
		$this->lb_language = $this->getSystemInfo('lb_language');
		$this->lb_member_type = $this->getSystemInfo('lb_member_type');
		$this->lb_game_url = $this->getSystemInfo('lb_game_url');
		// $this->lb_balance = '10000'; //for testing
	}

	public function getPlatformCode() {
		return LB_API;
	}

	public function callback($method, $result = null) {
		$this->CI->utils->debug_log('Game_api_lb (Callback): ', $result);
		$data = array(
			"status_code" => '01',
			"status_text" => "FAILED",
		);

		$this->CI->load->model(array('common_token', 'player_model'));

		if ($method == 'validatemember' || $method == 'validate_session') {
			parse_str($result, $resArr);

			$this->CI->utils->debug_log('Game_api_lb (Callback)  Result Array:', $resArr);
			$datetime = new DateTime();

			$token = $resArr['session_token'];
			$this->CI->utils->debug_log('Game_api_lb (Callback)  token:', $token);

			if (!empty($token)) {
				$playerId = $this->CI->common_token->getPlayerIdByToken($token);

				$this->CI->utils->debug_log('playerId', $playerId);
				if (!empty($playerId)) {
					// $player = $this->getPlayerInfo($playerId);
					$login_name = $this->getGameUsernameByPlayerId($playerId);
					$this->CI->utils->debug_log('playerId', $playerId, 'login_name', $login_name);
					if (!empty($login_name)) {

						$data = array(
							"status_code" => self::STATUS_SUCCESS,
							"status_text" => "OK",
							"currency" => $this->lb_currency,
							"member_id" => $login_name,
							"member_name" => $login_name,
							// "member_id" => "testenv3", //for local test
							// "member_name" => "testenv3", //for local test
							"language" => $this->lb_language,
							"min_transfer" => $this->lb_min_transfer,
							"max_transfer" => $this->lb_max_transfer,
							"member_type" => $this->lb_member_type,
							"datetime" => $datetime->format('m/d/Y H:i:s'),
						);
					}
				}
			}
			// $login_name = $this->CI->game_provider_auth->getPasswordByUuid($uuid, LB_API);
			// $this->CI->utils->debug_log('Game_api_lb (Callback)  Player Details:', $login_name);

		} else if ($method == 'validate_password') {
			parse_str($result, $resArr);

			$secret_key = @$resArr['secret_key'];

			$operator_id = @$resArr['operator_id'];
			$site_code = @$resArr['site_code'];
			$product_code = @$resArr['product_code'];

			$login_name = @$resArr['member_id'];
			$password = @$resArr['password'];

			//login by id and password
			if ($secret_key == $this->lb_secret_key && $operator_id == $this->lb_operator_id
				&& !empty($login_name) && !empty($password)) {

				$playerId = $this->getPlayerIdInGameProviderAuth($login_name);
				$passwordInDB = $this->CI->player_model->getPasswordById($playerId);
				if ($password == $passwordInDB) {

					$session_token = $this->getPlayerToken($playerId);
					$datetime = new DateTime();
					$data = array(
						"status_code" => self::STATUS_SUCCESS,
						"status_text" => "OK",
						"currency" => $this->lb_currency,
						"member_id" => $login_name,
						"member_name" => $login_name,
						"language" => $this->lb_language,
						"min_transfer" => $this->lb_min_transfer,
						"max_transfer" => $this->lb_max_transfer,
						"member_type" => $this->lb_member_type,
						"session_token" => $session_token,
						"datetime" => $datetime->format('m/d/Y H:i:s'),
					);
				} else {
					$this->CI->utils->debug_log('login failed password error', $palyerId, 'username', $login_name);
				}
			} else {
				$this->CI->utils->debug_log('login failed', $secret_key, $this->lb_secret_key, 'operator_id', $operator_id, $this->lb_operator_id);
			}
		}

		$xml_object = new SimpleXMLElement("<?xml version='1.0'?><authenticate></authenticate>");
		$xmlReturn = $this->CI->utils->arrayToXml($data, $xml_object);
		$this->CI->utils->debug_log('Game_api_lb (Callback)  XML Return:', $xmlReturn);
		return $xmlReturn;
	}

	public function generateUrl($apiName, $params) {
		$apiUri = self::URI_MAP[$apiName];
		return $url = $this->lb_api_domain . "/" . $apiUri . ".ashx";
	}

	protected function makeHttpOptions($options) {
		//custom
		$options['call_socks5_proxy'] = $this->getSystemInfo('lb_call_socks5_proxy');
		$options['call_socks5_proxy_login'] = $this->getSystemInfo('lb_call_socks5_proxy_login');
		$options['call_socks5_proxy_password'] = $this->getSystemInfo('lb_call_socks5_proxy_password');
		return $options;
	}

	protected function customHttpCall($ch, $params) {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = !empty($resultArr) && $resultArr['status_code'] == self::STATUS_SUCCESS;
		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('LB got error', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}
		return $success;
	}

	public function syncPlayerAccount($username, $password, $playerId) {
		$this->CI->utils->debug_log('username', $username, 'playerId', $playerId);
		// $success = false;
		$balance = null;
		$rlt = $this->isPlayerExist($username);
		$success = $rlt['success'];
		if ($rlt['success']) {
			if ($rlt['exists']) {
				//update register flag
				$this->updateRegisterFlag($playerId, true);
			} else {
				$rlt = $this->createPlayer($username, $password, $playerId);
				$success = $rlt['success'];
				if ($rlt['success']) {
					$this->updateRegisterFlag($playerId, true);
				}
			}
		}
		if ($success) {
			//update balance
			$rlt = $this->queryPlayerBalance($username);
			$success = $rlt['success'];
			if ($success) {
				//for sub wallet
				$balance = isset($rlt['balance']) ? floatval($rlt['balance']) : null;
				if ($balance !== null) {
					//update
					$this->updatePlayerSubwalletBalance($playerId, $balance);
				}
			}

		}
		return array('success' => $success, 'balance' => $balance);
	}

	//===start createPlayer=====================================================================================
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		$password = uniqid();
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		return $this->callApi(self::API_createPlayer,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"session_token" => random_string('alpha'),
				"member_id" => $playerName,
				"member_name" => $playerName,
				"currency" => $this->lb_currency,
				"balance" => 0,
				"language" => $this->lb_language,
				"min_transfer" => $this->lb_min_transfer,
				"max_transfer" => $this->lb_max_transfer,
				"member_type" => $this->lb_member_type,
			),
			$context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $this->CI->utils->xmlToArray($resultXml), $playerName);
		return array($success, null);
	}

	//===end createPlayer=====================================================================================

	//===start queryPlayerInfo=====================================================================================
	public function queryPlayerInfo($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true);
	}
	//===end queryPlayerInfo=====================================================================================

	//===start changePassword=====================================================================================
	public function changePassword($playerName, $oldPassword, $newPassword) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true);
	}
	//===end changePassword=====================================================================================

	//===start blockPlayer=====================================================================================
	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForBlockPlayer',
			'playerName' => $playerName,
			// 'playerId' => $playerId,
		);

		return $this->callApi(self::API_blockPlayer,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"action" => true,
				"member_id" => $playerName,
			),
			$context);

		// return array("success" => true);
	}

	public function processResultForBlockPlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		// $playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $this->CI->utils->xmlToArray($resultXml), $playerName);
		if ($success) {
			$success = $this->blockUsernameInDB($playerName);
		}
		return array($success, null);
	}
	//===end blockPlayer=====================================================================================

	//===start unblockPlayer=====================================================================================
	public function unblockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForUnblockPlayer',
			'playerName' => $playerName,
			// 'playerId' => $playerId,
		);

		return $this->callApi(self::API_unblockPlayer,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
			),
			$context);
	}
	public function processResultForUnblockPlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		// $playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $this->CI->utils->xmlToArray($resultXml), $playerName);
		if ($success) {
			$success = $this->unblockUsernameInDB($playerName);
		}
		return array($success, null);
	}
	//===end unblockPlayer=====================================================================================

	//===start depositToGame=====================================================================================
	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
		$playerUsername = $playerName;
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$reference_id = random_string('alpha');
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerName' => $playerName,
			'amount' => $amount,
			'reference_id' => $reference_id,
		);

		return $this->callApi(self::API_depositToGame,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
				"currency" => $this->lb_currency,
				"amount" => $amount,
				"reference_id" => $reference_id, //Unique Transaction ID generated by Operator
			),
			$context);
	}

	public function processResultForDepositToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$amount = $this->getVariableFromContext($params, 'amount');
		$reference_id = $this->getVariableFromContext($params, 'reference_id');
		$success = $this->processResultBoolean($responseResultId, $this->CI->utils->xmlToArray($resultXml), $playerName);

		// $result = array();
		$result = array('response_result_id' => $responseResultId);
		if ($success) {
			//get current sub wallet balance
			$playerBalance = $this->queryPlayerBalance($playerName);

			//for sub wallet
			$afterBalance = isset($playerBalance['balance']) ? $playerBalance['balance'] : null;
			// $ptrlt = $resultJson['result'];
			//external_transaction_id means game api system transaction id , not our
			$result["external_transaction_id"] = null; // $ptrlt['ptinternaltransactionid']; // $this->getVariableFromContext($params, 'externaltranid');
			$result["currentplayerbalance"] = $afterBalance;
			$result["reference_id"] = $reference_id;
			$result["userNotFound"] = false;

			//update
			$playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			if ($playerId) {
				//deposit
				$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,
					$this->transTypeMainWalletToSubWallet());
			} else {
				$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}

		} else {
			$result["userNotFound"] = true;
		}

		return array($success, $result);
	}

	//===end depositToGame=====================================================================================

	//===start withdrawFromGame=====================================================================================
	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		$playerUsername = $playerName;
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$reference_id = random_string('alpha');
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawToGame',
			'playerName' => $playerName,
			'amount' => $amount,
			'reference_id' => $reference_id,
		);

		return $this->callApi(self::API_withdrawFromGame,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
				"currency" => $this->lb_currency,
				"amount" => $amount,
				"reference_id" => $reference_id, //Unique Transaction ID generated by Operator
			),
			$context);
	}

	public function processResultForWithdrawToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$amount = $this->getVariableFromContext($params, 'amount');
		$reference_id = $this->getVariableFromContext($params, 'reference_id');
		$success = $this->processResultBoolean($responseResultId, $this->CI->utils->xmlToArray($resultXml), $playerName);

		// $result = array();
		$result = array('response_result_id' => $responseResultId);
		if ($success) {
			//get current sub wallet balance
			$playerBalance = $this->queryPlayerBalance($playerName);

			//for sub wallet
			$afterBalance = isset($playerBalance['balance']) ? $playerBalance['balance'] : null;
			// $ptrlt = $resultJson['result'];
			//external_transaction_id means game api system transaction id , not our
			$result["external_transaction_id"] = null; // $ptrlt['ptinternaltransactionid']; // $this->getVariableFromContext($params, 'externaltranid');
			$result["currentplayerbalance"] = $afterBalance;
			$result["reference_id"] = $reference_id;
			$result["userNotFound"] = false;

			//update
			$playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			if ($playerId) {
				//withdrawal
				$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,
					$this->transTypeSubWalletToMainWallet());
			} else {
				$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}

		} else {
			$result["userNotFound"] = true;
		}

		return array($success, $result);
	}

	//===end withdrawFromGame=====================================================================================

	//===start login=====================================================================================
	public function login($playerName, $password = null) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'playerName' => $playerName,
		);

		return $this->callApi(self::API_login,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"session_token" => $playerName,
			),
			$context);
	}

	public function processResultForLogin($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		return array($success, $resultJson);
	}
	//===end login=====================================================================================

	//===start logout=====================================================================================
	public function logout($playerName, $password = null) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogout',
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		return $this->callApi(self::API_logout,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
				"action" => true,
			),
			$context);
	}

	public function processResultForLogout($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);
		return array($success, null);
	}
	//===end logout=====================================================================================

	//===start updatePlayerInfo=====================================================================================
	public function updatePlayerInfo($playerName, $infos) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true);
	}

	//===end updatePlayerInfo=====================================================================================

	//===start queryPlayerBalance=====================================================================================
	public function queryPlayerBalance($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
		);

		return $this->callApi(self::API_queryPlayerBalance,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
			),
			$context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		$result = array();
		if ($success && isset($resultArr['wallet']) && @$resultArr['wallet'] !== null) {
			$result["balance"] = floatval($resultArr['wallet']);
			// $result["exists"]=true;
			$playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			$this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName', $playerName,
				'balance', @$resultArr['wallet']);
			if ($playerId) {
				//should update database
				// $this->updatePlayerSubwalletBalance($playerId, $result["balance"]);
			} else {
				log_message('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}
			$result['exists'] = true;
		} else {
			$success = false;
			$status_code = isset($resultArr['status_code']) ? @$resultArr['status_code'] : null;
			if ($status_code == '60.01') {
				//not exists
				$result['exists'] = false;
			}
		}
		return array($success, $result);
	}
	//===end queryPlayerBalance=====================================================================================

	//===start queryPlayerDailyBalance=====================================================================================
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
	//===end queryPlayerDailyBalance=====================================================================================

	//===start queryGameRecords=====================================================================================
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		$gameRecords = parent::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());
		return array('success' => true, 'gameRecords' => $gameRecords);
	}
	//===end queryGameRecords=====================================================================================

	//===start checkLoginStatus=====================================================================================
	public function checkLoginStatus($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		return array("success" => true, "loginStatus" => true);
	}
	//===end checkLoginStatus=====================================================================================

	//===start totalBettingAmount=====================================================================================
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {

	}
	//===end totalBettingAmount=====================================================================================

	//===start queryTransaction=====================================================================================
	public function queryTransaction($transactionId, $extra) {

	}
	public function processResultForQueryTransaction($apiName, $params, $responseResultId, $resultXml) {

	}
	//===end queryTransaction=====================================================================================

	//===start queryForwardGame=====================================================================================
	public function queryForwardGame($playerName, $extra) {
		// $this->CI->load->model(array('common_token'));
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$token = $this->getPlayerToken($playerId);

		$url = $this->lb_game_url . '?s=' . $token;
		return array('success' => true, 'url' => $url);
	}
	//===end queryForwardGame=====================================================================================

	//===start syncGameRecords=====================================================================================
	const START_PAGE = 0;
	public function syncOriginalGameLogs($token) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($startDate->format('Y-m-d H:i:s'));
		$endDate = new DateTime($endDate->format('Y-m-d H:i:s'));
		$startDate->modify($this->getDatetimeAdjust());
		$this->CI->utils->debug_log('startDate', $startDate, 'endDate', $endDate);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncGameRecords',
		);
		return $this->callApi(self::API_syncGameRecords,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"start_time" => $startDate->format('Y-m-d H:i:s'),
				"end_time" => $endDate->format('Y-m-d H:i:s'),
				"status" => 'all',
			),
			$context);
	}

	protected function convertResult($resultXml) {
		$resultArr = json_decode(json_encode($resultXml), true);

		// $this->CI->utils->debug_log('resultArr', $resultArr);

		if (isset($resultArr['bet']['item'])) {
			$gameRecords = $resultArr['bet']['item'];
			$rows = array();
			foreach ($gameRecords as $item) {

				// $this->utils->debug_log('item', $item);

				if (isset($item['@attributes'])) {
					$attrs = $item['@attributes'];
					$rows[] = $attrs;
				}
			}
			$resultArr['bet'] = $rows;
		}

		// $this->CI->utils->debug_log('resultArr', $resultArr);

		return $resultArr;
	}

	public function processResultForSyncGameRecords($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		// $this->CI->utils->debug_log('resultXml', $resultXml);
		$resultArr = $this->convertResult($resultXml);
		$success = $this->processResultBoolean($responseResultId, $resultArr);

		// load models
		$this->CI->load->model(array('lb_game_logs', 'external_system'));

		if ($success) {
			$gameRecords = $resultArr['bet'];

			if (empty($gameRecords) || !is_array($gameRecords)) {
				$this->CI->utils->debug_log('wrong game records', $gameRecords);
			}

			if (!empty($gameRecords)) {

				$this->CI->utils->debug_log('gameRecords', count($gameRecords));

				//filter availabe rows first
				// $availableRows = $this->CI->lb_game_logs->getAvailableRows($gameRecords);

				list($availableRows, $maxRowId) = $this->CI->lb_game_logs->getAvailableRows($gameRecords);
				$this->CI->utils->debug_log('availableRows', count($availableRows), 'gameRecords', count($gameRecords), 'maxRowId', $maxRowId, 'responseResultId', $responseResultId);

				foreach ($availableRows as $value) {
					$this->copyRowToDB($value, $responseResultId);
				}
				if ($maxRowId) {
					$this->CI->external_system->setLastSyncId($this->getPlatformCode(), $maxRowId);
					// 	$lastRowId = $maxRowId;
					// } else {
					// 	break;
				}
			}
		}

		return array($success, null);
	}

	private function copyRowToDB($row, $responseResultId) {
		$result = array(
			'member_id' => $row['member_id'],
			'member_type' => $row['member_type'],
			'session_token' => $row['session_token'],
			'bet_id' => $row['bet_id'],
			'bet_no' => $row['bet_no'],
			'match_no' => $row['match_no'],
			'match_area' => $row['match_area'],
			'match_id' => $row['match_id'],
			'bet_type' => $row['bet_type'],
			'bet_content' => $row['bet_content'],
			'bet_currency' => $row['bet_currency'],
			'bet_money' => $row['bet_money'],
			'bet_odds' => $row['bet_odds'],
			'bet_winning' => $row['bet_winning'],
			'bet_win' => $row['bet_win'],
			'bet_status' => $row['bet_status'],
			'bet_time' => $row['bet_time'],
			'trans_time' => $row['trans_time'],
			'game_platform' => LB_API,
			'external_uniqueid' => $row['bet_id'],
		);

		$this->CI->lb_game_logs->insertLBGameLogs($result);
	}

	private function getStringValueFromXml($xml, $key) {
		$value = (string) $xml[$key];
		if (empty($value) || $value == 'null') {
			$value = '';
		}

		return $value;
	}

	const LB_GAME_CODE = 'lbkeno';
	public function syncMergeToGameLogs($token) {
		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$dateTimeFrom->modify($this->getDatetimeAdjust());

		$result = $this->getLBGameLogStatistics($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo->format('Y-m-d H:i:s'));
		if ($result) {
			$this->CI->load->model(array('game_logs', 'player_model', 'game_description_model'));

			$unknownGame = $this->getUnknownGame();

			foreach ($result as $lbdata) {
				$player_id = $this->getPlayerIdInGameProviderAuth($lbdata->member_id);

				if (!$player_id) {
					continue;
				}

				$player = $this->CI->player_model->getPlayerById($player_id);
				$player_username = $player->username;

				$gameDate = new \DateTime($lbdata->bet_time);
				$gameDateStr = $this->CI->utils->formatDateTimeForMysql($gameDate);
				$bet_amount = $lbdata->bet_money;

				$game_code = $lbdata->bet_type;
				// $game_code = self::LB_GAME_CODE;
				// list($game_description_id, $game_type_id) = $this->CI->game_description_model->getGameDescriptionIdAndGameTypeId($game_code);

				// if (empty($game_description_id)) {
				// 	$game_description_id = $unknownGame->id;
				// 	$game_type_id = $unknownGame->game_type_id;
				// }

				list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($lbdata, $unknownGame);

				$result_amount = $lbdata->bet_winning - $lbdata->bet_money;

				$this->syncGameLogs($game_type_id, $game_description_id, $game_code,
					$game_type_id, $game_code, $player_id, $player_username,
					$bet_amount, $result_amount, null, null, null, null,
					$lbdata->external_uniqueid, $gameDateStr, $gameDateStr, $lbdata->response_result_id);

				// $gameLogdata = array(
				// 	'bet_amount' => $bet_amount,
				// 	'result_amount' => $result_amount,
				// 	'win_amount' => $result_amount > 0 ? $result_amount : 0,
				// 	'loss_amount' => $result_amount < 0 ? abs($result_amount) : 0,
				// 	'start_at' => $gameDateStr,
				// 	'end_at' => $gameDateStr,
				// 	'game_platform_id' => $this->getPlatformCode(),
				// 	'game_type_id' => $this->CI->game_description_model->getGameTypeIdByGameCode($game_code),
				// 	'game_description_id' => $game_description_id,
				// 	'game_code' => $game_code,
				// 	'player_id' => $player_id,
				// 	'player_username' => $player_username,
				// 	'external_uniqueid' => $lbdata->external_uniqueid,
				// 	'flag' => Game_logs::FLAG_GAME,
				// );
				// $this->CI->game_logs->syncToGameLogs($gameLogdata);
			}
		}
	}

	private function getGameDescriptionInfo($row, $unknownGame) {
		$externalGameId = $row->bet_type;
		$extra = array('game_code' => $externalGameId);
		$game_description_id = null;
		$game_type_id = null;
		$game = $externalGameId;
		$gametype = $externalGameId;
		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$game, $gametype, $externalGameId, $extra,
			$unknownGame);
	}

	public function gameAmountToDB($amount) {
		//only need 2
		return round(floatval($amount), 2);
	}

	private function getLBGameLogStatistics($dateTimeFrom, $dateTimeTo) {
		$this->CI->load->model('lb_game_logs');
		return $this->CI->lb_game_logs->getLBGameLogStatistics($dateTimeFrom, $dateTimeTo);
	}

	//===end syncGameRecords=====================================================================================

	//===start syncBalance=====================================================================================
	//===end syncBalance=====================================================================================

	//===start isPlayerExist=====================================================================================
	// public function isPlayerExist($playerName) {
	// 	$result = $this->queryPlayerBalance($playerName);
	// 	$result["exists"] = $result['exists'];
	// 	$result['success'] = true;
	// 	return $result;
	// }
	public function isPlayerExist($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerName' => $playerName,
		);

		return $this->callApi(self::API_isPlayerExist,
			array(
				"secret_key" => $this->lb_secret_key,
				"operator_id" => $this->lb_operator_id,
				"site_code" => $this->lb_site_code,
				"product_code" => $this->lb_product_code,
				"member_id" => $playerName,
			),
			$context);
	}

	public function processResultForIsPlayerExist($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$resultArr = $this->CI->utils->xmlToArray($resultXml);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);
		$result = array();

		if ($success) {
			// $result["balance"] = floatval($resultArr['wallet']);
			// $result["exists"]=true;
			// $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			// $this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName', $playerName,
			// 	'balance', @$resultArr['wallet']);
			// if ($playerId) {
			// 	//should update database
			// 	// $this->updatePlayerSubwalletBalance($playerId, $result["balance"]);
			// } else {
			// 	log_message('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			// }
			$result['exists'] = true;
		} else {
			$status_code = isset($resultArr['status_code']) ? @$resultArr['status_code'] : null;

			$success = false;
			if ($status_code == '60.01') {
				//not exists
				$result['exists'] = false;
				$success = true;
			}
		}
		return array($success, $result);
	}

	//===end isPlayerExist=====================================================================================
	*/
}

/////END OF FILE//////