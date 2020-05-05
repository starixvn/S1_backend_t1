<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
 * extra_info
{
	"launcher_url": "https://ams-api.ttms2.co:8443/casino/generic/game/game.html",
	"mob_launcher_url": " https://ams2-games.ttms.co/casino/default/game/casino5.html",
    "currency": "CNY",
    "country": "CN",
    "common_wallet": 0,
    "tester": 0,
    "default_language": "en",
    "game_image_directory": "http://ams-games.ttms2.co/player/assets/images/games/",
    "prefix_for_username": "3t_",
    "balance_in_game_log": false,
    "adjust_datetime_minutes": 20
}
 *
 */
class Game_api_ttg extends Abstract_game_api {
	const TRANSFER_FLAG_SUCCESS = 0;
	
	public function getPlatformCode() {
		return TTG_API;
	}

	private $api_url;
	private $partner_id;
	private $affiliate_id;
	private $affiliate_login;

	private $account;
	private $country;
	private $common_wallet;
	private $tester;
	private $default_language;
	private $data_feed_url;
	private $prefix_for_username;

	private $http_headers = array('Content-Type' => 'application/xml');

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->data_feed_url = $this->getSystemInfo('data_feed_url');
		$this->partner_id = $this->getSystemInfo('account');
		$this->affiliate_id = $this->getSystemInfo('key');
		$this->affiliate_login = $this->getSystemInfo('secret');

		$this->launcher_url = $this->getSystemInfo('launcher_url');
		$this->mob_launcher_url = $this->getSystemInfo('mob_launcher_url');
		$this->launcher_url_demo = $this->getSystemInfo('launcher_url_demo', 'http://pff.ttms.co/casino/default/game/game.html');
		$this->mob_launcher_url_demo = $this->getSystemInfo('mob_launcher_url_demo', 'http://pff.ttms.co/casino/default/game/casino5.html');
		$this->add_partner_id_to_game_launch = $this->getSystemInfo('add_partner_id_to_game_launch',false);

		$this->account = $this->getSystemInfo('currency');
		$this->country = $this->getSystemInfo('country');
		$this->common_wallet = $this->getSystemInfo('common_wallet');
		$this->tester = $this->getSystemInfo('tester');
		$this->default_language = $this->getSystemInfo('default_language');
		$this->prefix_for_username = $this->getSystemInfo('prefix_for_username');
		$this->partners = array(
			'partner' => array(
				array(
					'partnerId_attr' => 'zero',
					'partnerType_attr' => 0,
				),
				array(
					'partnerId_attr' => 'gm',
					'partnerType_attr' => 1,
				),
				array(
					'partnerId_attr' => $this->partner_id,
					'partnerType_attr' => 1,
				),
			),
		);

		$lsdid = $this->getSystemInfo('lsdid');
		if ($lsdid) {

			$lsdid = explode('|', $lsdid);

			$partners = array_map(function($lsdid) {

				list($partnerId, $partnerType) = explode(',', $lsdid);

				return array(
					'partnerId_attr' => $partnerId,
					'partnerType_attr' => $partnerType,
				);

			}, $lsdid);

			$this->partners['partner'] = $partners;

		}

	}

	const PLAYER_EXIST = 1;
	const GAME_SUITE = 'Flash';
	const DATETIMERANGE_INTERVAL = '+2 hours 59 mins';

	const URI_MAP = array(
		self::API_login => '/cip/gametoken/{uid}',
		self::API_createPlayer => '/cip/gametoken/{uid}',
		self::API_isPlayerExist => '/cip/player/{uid}/existence',
		self::API_queryPlayerBalance => '/cip/player/{uid}/balance',
		self::API_depositToGame => '/cip/transaction/{partnerId}/{ext_transaction_ID}',
		self::API_withdrawFromGame => '/cip/transaction/{partnerId}/{ext_transaction_ID}',
		self::API_syncGameRecords => '/dataservice/datafeed/transaction/current',
		self::API_batchQueryPlayerBalance => '/dataservice/datafeed/player/balance',
		self::API_queryTransaction => '/cip/transaction/{partnerId}/{ext_transaction_ID}',
		// self::API_queryForwardGame => '/casino/generic/game/game.html',
	);

	public function generateUrl($apiName, $params) {
		$apiUri = self::URI_MAP[$apiName];
		$url = $this->api_url . $apiUri;
		if(self::URI_MAP[self::API_syncGameRecords] == $apiUri && !empty($this->data_feed_url)) {
			$dfUrl = $this->data_feed_url. $apiUri;
			return $dfUrl;
		}
		return $url;
	}

	protected function getHttpHeaders($params) {
		return $this->http_headers;
	}

	protected function customHttpCall($ch, $params) {

		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		preg_match_all("#\{([^\}]+)\}#", $url, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$url = str_replace($match[0], $params[$match[1]], $url);
			unset($params[$match[1]]);
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		if (!empty($params)) {
			if (count($params) == 1 && is_array(current($params))) {
				curl_setopt($ch, CURLOPT_POST, TRUE);
				$xml = $this->CI->utils->arrayToXml($params);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			} else {
				$url .= '?' . http_build_query($params);
			}
		}

	}

	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {

		parent::createPlayer($playerName, $playerId, $password, $email, $extra);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		$params = array(
			'uid' => $gameUsername,
			'logindetail' => array(
				'player' => array(
					'userName_attr' => $gameUsername,
					'nickName_attr' => $gameUsername,
					'country_attr' => $this->country,
					'account_attr' => $this->account,
					'partnerId_attr' => $this->partner_id,
					'commonWallet_attr' => $this->common_wallet,
					'tester_attr' => $this->tester,
				),
				'partners' => $this->partners,
			),
		);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$resultXml = $resultXml['@attributes'];
		$success = $this->processResultBoolean($responseResultId, $resultXml, $playerName);

        $result = array(
            "player" => $gameUsername,
            "exists" => false
        );

		if($success){
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
            $result["exists"] = true;
		}
		return array($success, $result);
	}

	public function login($playerName, $password = NULL) {

		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'playerName' => $playerName,
		);

		$params = array(
			'uid' => $playerName,
			'logindetail' => array(
				'player' => array(
					'userName_attr' => $playerName,
					'nickName_attr' => $playerName,
					'country_attr' => $this->country,
					'account_attr' => $this->account,
					'partnerId_attr' => $this->partner_id,
					'commonWallet_attr' => $this->common_wallet,
					'tester_attr' => $this->tester,
				),
				'partners' => $this->partners,
			),
		);

		return $this->callApi(self::API_login, $params, $context);
	}

	public function processResultForLogin($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$resultXml = $resultXml['@attributes'];
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		return array($success, $resultXml);
	}

	public function isPlayerExist($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
		);

		$params = array(
			'uid' => $gameUsername,
		);

		return $this->callApi(self::API_isPlayerExist, $params, $context);
	}

	public function processResultForIsPlayerExist($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$resultXml = $resultXml['@attributes'];
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		$reuslt = [];

        if($success){
        	$success = true;
        	$result = array('exists' => $resultXml['exist'] == self::PLAYER_EXIST); 
        }else{
        	$result = array('exists' => null);
	    }

        return array($success, $result);
	}

	public function queryPlayerBalance($playerName) {

		$playerName = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
		);

		$params = array(
			'uid' => $playerName,
		);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$resultXml = $resultXml['@attributes'];
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		return array($success, array('balance' => $resultXml['totalWithdrawable']));
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $external_transaction_id = $transfer_secure_id;
        if(empty($external_transaction_id)){
            $external_transaction_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        }
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
		);

		$params = array(
			'partnerId' => $this->partner_id,
			'ext_transaction_ID' => $external_transaction_id,
			'transactiondetail' => array(
				'uid_attr' => $gameUsername,
				'amount_attr' => $amount,
			),
		);

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$amount = $this->getVariableFromContext($params, 'amount');
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);
		if ($success) {
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	$playerBalance = $this->queryPlayerBalance($playerName);
			// 	if(isset($playerBalance['success']) && $playerBalance['success']){
			// 		$afterBalance = $playerBalance['balance'];
			// 		$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeMainWalletToSubWallet());
			// 	}
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		}else{
            $result['response_result_id'] = $this->getReason($resultXml['response_result_id']);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

		return array($success, $resultXml);
	}

    private function getReason($responseCode){
        switch($responseCode) {
            case '1000' :
                return self::REASON_NOT_FOUND_PLAYER;
                break;
            case '1001' :
            case '1002' :
                return self::REASON_INVALID_TRANSFER_AMOUNT;
                break;
            case '1004' :
                return self::REASON_GAME_ACCOUNT_LOCKED;
                break;
            case '9999' :
                return self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                break;
            default:
                return self::REASON_UNKNOWN;
            break;
        }
    }

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $external_transaction_id = $transfer_secure_id;
        if(empty($external_transaction_id)){
            $external_transaction_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        }

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawFromGame',
			'gameUsername' => $gameUsername,
			'playerName' => $playerName,
			'amount' => $amount,
			'external_transaction_id' => $external_transaction_id,
		);

		$params = array(
			'partnerId' => $this->partner_id,
			'ext_transaction_ID' => $external_transaction_id,
			'transactiondetail' => array(
				'uid_attr' => $gameUsername,
				'amount_attr' => -$amount,
			),
		);

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	public function processResultForWithdrawFromGame($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		//$resultXml = $resultXml['@attributes'];
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		
		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	$playerBalance = $this->queryPlayerBalance($playerName);
			// 	$afterBalance = $playerBalance['balance'];

			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeSubWalletToMainWallet());

			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		}else{
            $result['response_result_id'] = $this->getReason($resultXml['response_result_id']);
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
                $lang = 'zh-cn'; // chinese
                break;
            case 6:
            case 'th-th':
				$lang = 'th'; // thailand
				break;
            default:
                $lang = $this->default_language; // default as english
                break;
        }
        return $lang;
    }

	public function queryForwardGame($playerName, $extra) {
		// $suite = $extra['extra']['deviceType'] == "web"?(self::GAME_SUITE):$extra['extra']['deviceType'];
		// $suite = $extra['deviceType'] == "web"?(self::GAME_SUITE):$extra['deviceType'];
		$params = array(
			'gameId' => isset($extra['extra']['game_id'])?$extra['extra']['game_id']:$extra['game_id'],
			'gameName' => $extra['game_code'],
			'gameType' => $extra['game_type'],
			// 'gameSuite' => $suite, // as coordinate with game provider , they dont have parameter like this.
			'deviceType' => !$extra['is_mobile']?"web":"mobile",
			'lang' => $this->getLauncherLanguage($extra['language']),
		);

		if (isset($extra['game_mode']) && ($extra['game_mode']=="trial"||$extra['game_mode']=="demo")) {

			$params['playerHandle'] = 999999;
			$params['account'] = 'FunAcct';

            $url = $this->launcher_url_demo . '?' . http_build_query($params);

		} else {

			$result = $this->login($playerName);

			if ( ! $result['success']) {
				return $result;
			}

			$params['playerHandle'] = $result['token'];
			$params['account'] = $this->account;

            $url = $this->launcher_url . '?' . http_build_query($params);
		}

		if ($this->add_partner_id_to_game_launch) {
			$url .= "&lsdId=". $this->partner_id;
		}

		return array(
			'success' => true,
			'url' => $url,
		);
	}

	public function syncOriginalGameLogs($token) {

		$playerName = parent::getValueFromSyncInfo($token, 'playerName');
		$syncId = parent::getValueFromSyncInfo($token, 'syncId');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$startDate->modify($this->getDatetimeAdjust());


		$startDate = $this->serverTimeToGameTime($startDate);
		$endDate = $this->serverTimeToGameTime($endDate);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncOriginalGameLogs',
			'playerName' => $playerName,
		);

		$dont_save_response_in_api = $this->getConfig('dont_save_response_in_api',false);

		$this->http_headers = array(
			'T24-Affiliate-Id' => $this->affiliate_id,
			'T24-Affiliate-Login' => $this->affiliate_login,
			'Content-Type' => 'text/xml',
		);

		$params = array(
			'searchdetail' => array(
				'requestId_attr' => '',
				'daterange' => [],
				'account' => array(
					'currency_attr' => $this->account,
				),
				'transaction' => array(
					'transactionType_attr' => 'Game',
				),
			),
		);

		$this->utils->debug_log("sync original headers ===============================================================", $this->http_headers);
		$datetime_range_periods = $this->CI->utils->dateTimeRangePeriods($startDate, $endDate, self::DATETIMERANGE_INTERVAL);

		foreach ($datetime_range_periods as $datetime_range_period) {
			$from = $datetime_range_period['from']; 
			$to   = $datetime_range_period['to']; 
			$requestId = random_string('unique');
			$params['searchdetail']['requestId_attr'] = $requestId;
			$params['searchdetail']['daterange']= array(
					'startDate_attr' => date('Ymd', strtotime($from)),
					'startDateHour_attr' => intval(date('H', strtotime($from))),
					'startDateMinute_attr' => intval(date('i', strtotime($from))),
					'endDate_attr' => date('Ymd', strtotime($to)),
					'endDateHour_attr' => intval(date('H', strtotime($to))),
					'endDateMinute_attr' => intval(date('i', strtotime($to))),
			);

			$this->utils->debug_log("sync original params ===============================================================", $params);
			$attempts = 0;
			do {
				$result = $this->callApi(self::API_syncGameRecords, $params, $context);
				if($result['success']){
					$attempts = 0;
					break;
				}else{
					$attempts++;
					$this->utils->debug_log("retry count ===============================================================", ['attempts' => $attempts, $datetime_range_period['from'], $datetime_range_period['to']]);
					continue;
				}
				sleep(0.5);
			} while($attempts < $this->common_retry_times);
		}
	}

	public function processResultForSyncOriginalGameLogs($params) {

		# NOTE: API only return 10000 rows

		$this->CI->load->model('ttg_game_logs');

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = json_decode(json_encode((array) $resultXml), true);
		$this->utils->debug_log("sync original response ===============================================================", $resultXml);
		$success = $this->processResultBoolean($responseResultId, $resultXml);
		$result = null;

		if ($success) {

			$sum = 0;

			$gameRecords = isset($resultXml['details']['transaction']) ? $resultXml['details']['transaction'] : null;

			if ($gameRecords) {

				if (@$resultXml['@attributes']['pageSize'] == 1) {
					$gameRecords = array($gameRecords);
				}

				# GET WAGERS
				$wagers = array_filter($gameRecords, function($record) {
					return $record['detail']['@attributes']['transactionSubType'] == 'Wager';
				});

				# GROUP WAGERS
				$grouped_wagers = array();
				foreach ($wagers as $wager) {

					$handId = $wager['detail']['@attributes']['handId'];
					$bet 	= $wager['detail']['@attributes']['amount'];
					$date   = date('Y-m-d H:i:s', strtotime($wager['detail']['@attributes']['transactionDate']));

					if ( ! isset($grouped_wagers[$handId])) {
						$grouped_wagers[$handId] = array(
							'bet' => $bet,
							'start_at' => $date,
						);
					} else {
						$grouped_wagers[$handId]['bet'] += $bet;
						$grouped_wagers[$handId]['start_at']  = min($grouped_wagers[$handId]['start_at'], $date);
					}
				}

				foreach ($gameRecords as &$record) {

					$record = array(
						'partnerId' => $record['player']['@attributes']['partnerId'],
						'playerId' => $record['player']['@attributes']['playerId'],
						'amount' => $record['detail']['@attributes']['amount'],
						'handId' => $record['detail']['@attributes']['handId'],
						'transactionSubType' => $record['detail']['@attributes']['transactionSubType'],
						'game' => $record['detail']['@attributes']['game'],
						'currency' => $record['detail']['@attributes']['currency'],
						'transactionDate' => $record['detail']['@attributes']['transactionDate'],
						'transactionId' => $record['detail']['@attributes']['transactionId'],
					);

					$record['transactionDate'] = DateTime::createFromFormat('Ymd H:i:s', $record['transactionDate'])->format('Y-m-d H:i:s');

					$gameshortcode = $record['game'];
					$uniqueId = $record['transactionId'];
					$external_uniqueid = $uniqueId;

					$record['response_result_id'] = $responseResultId;
					$record['uniqueid'] = $uniqueId;
					$record['external_uniqueid'] = $external_uniqueid;
					$record['gameshortcode'] = $gameshortcode;

					# RESOLVER
					# TODO: IF 2 BETS, 1 IN THE DATABASE, 1 IN RECORDS
					if ($record['transactionSubType'] == 'Resolve') {
						$handId = $record['handId'];
						$wager  = isset($grouped_wagers[$handId]) ? $grouped_wagers[$handId] : $this->CI->ttg_game_logs->getWager($handId); # IF WAGER DOES NOT EXIST, SEARCH THE DATABASE
						$record = array_merge($record, $wager);
					}

				}

				$availableRows = $this->CI->ttg_game_logs->getAvailableRows($gameRecords);
				$this->CI->utils->debug_log('availableRows', count($availableRows), 'gameRecords', count($gameRecords));

				foreach ($availableRows as $availableRow) {
					if (!$this->isInvalidRow($availableRow)) {

						if ($availableRow['transactionSubType'] == 'Wager') {
							$sum += floatval($availableRow['amount']);
						}
						$this->CI->ttg_game_logs->insertTtgGameLogs($availableRow);
					}
				}

			}

			$result['pageSize'] = @$resultXml['@attributes']['pageSize'];
			$result['totalRecords'] = @$resultXml['@attributes']['totalRecords'];
			$result['sum'] = $sum;
		}

		return array($success, $result);

	}

	public function syncMergeToGameLogs($token) {

		$this->CI->load->model(array('game_logs', 'player_model', 'ttg_game_logs', 'game_description_model'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeFrom = $dateTimeFrom->format('Y-m-d H:i:s');

		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$dateTimeTo = $dateTimeTo->format('Y-m-d H:i:s');

		$this->CI->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

		$cnt 	= 0;
		$rlt 	= array('success' => true);
		$result = $this->CI->ttg_game_logs->getTtgGameLogStatistics($dateTimeFrom, $dateTimeTo);

		if ($result) {

            $unknownGame = $this->getUnknownGame();

			foreach ($result as $ttg_data) {
				if ($player_id = $this->getPlayerIdInGameProviderAuth($ttg_data->gameUsername)) {

					$cnt++;

					$player 		= $this->CI->player_model->getPlayerById($player_id);
					$bet_amount 	= $this->gameAmountToDB($this->getBetAmount($ttg_data));
					$result_amount 	= $this->gameAmountToDB($this->getResultAmount($ttg_data));
					$after_balance 	= $this->gameAmountToDB($this->getAfterBalance($ttg_data));
					$has_both_side 	= $bet_amount >= $result_amount && $result_amount > 0 ? 1 : 0;

                    if (empty($ttg_data->game_description_id)) {
                        list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($ttg_data, $unknownGame);
                    }else{
                        $game_description_id = $ttg_data->game_description_id;
                        $game_type_id = $ttg_data->game_type_id;
                    }

                    $extra = array(
                    	'table' => $ttg_data->external_uniqueid
                    );

					$this->syncGameLogs(
						$game_type_id,
						$game_description_id,
						$ttg_data->ttg_game,
						$game_type_id,
						$ttg_data->ttg_game,
						$player_id,
						$ttg_data->gameUsername,
						$bet_amount,
						$result_amount,
						null, # win_amount
						null, # loss_amount
						$after_balance,
						$has_both_side,
						$ttg_data->external_uniqueid,
						$ttg_data->start_at,
						$ttg_data->end_at,
						$ttg_data->response_result_id,
						Game_logs::FLAG_GAME,
						$extra
					);

				}
			}
		}

		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);

		return array('success' => true);
	}

	public function batchQueryPlayerBalance($playerNames, $syncId = null) {

		$requestId = random_string('unique');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processBatchQueryPlayerBalance',
		);

		$dont_save_response_in_api = $this->getConfig('dont_save_response_in_api',false);

		$this->http_headers = array(
			'T24-Affiliate-Id' => $this->affiliate_id,
			'T24-Affiliate-Login' => $this->affiliate_login,
			'Content-Type' => 'text/xml',
		);

		$params = array(
			'searchdetail' => array(
				'requestId_attr' => $requestId,
				'account' => array(
					'currency_attr' => $this->account,
				),
				'partner' => array(
					'partnerId_attr' => $this->partner_id,
				),
			),
		);

		return $this->callApi(self::API_batchQueryPlayerBalance, $params, $context);

	}

	public function processBatchQueryPlayerBalance($params) {

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = json_decode(json_encode((array) $resultXml), true);
		$success = $this->processResultBoolean($responseResultId, $resultXml);

		$result = array('balances' => null);
		$cnt = 0;

		if ($success && isset($resultXml['details']['balance']) && !empty($resultXml['details']['balance'])) {

			if (@$resultXml['@attributes']['pageSize'] == 1) {
				$resultXml['details']['balance'] = array($resultXml['details']['balance']);
			}

			foreach ($resultXml['details']['balance'] as $balResult) {

				$balResult = $balResult['@attributes'];

				$gameUsername = $balResult['playerId'];
				$playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

				if ($playerId) {
					$bal = floatval($balResult['amount']);
					$result['balances'][$playerId] = $bal;
					$this->updatePlayerSubwalletBalance($playerId, $bal);
					$cnt++;
				}

			}
		}

		$this->CI->utils->debug_log('sync balance', $cnt, 'success', $success);

		$result['pageSize'] = isset($resultXml['@attributes']['pageSize']) ? $resultXml['@attributes']['pageSize'] : null;
		$result['totalRecords'] = isset($resultXml['@attributes']['totalRecords']) ? $resultXml['@attributes']['totalRecords'] : null;

		return array($success, $result);

	}

	# UTILS
	public function processResultBoolean($responseResultId, $resultXml, $playerName = null) {
		$success = !empty($resultXml);
		return $success;
	}

	private function isInvalidRow($row) {
		return FALSE;
	}

	public function gameAmountToDB($amount) {
		$amount = floatval($amount);
		return round($amount, 2);
	}

	private function getBetAmount($row) {
		$bet = -$row->bet;
		return $bet;
	}

	private function getResultAmount($row) {
		$bet = $row->bet;
		$result = $row->result;
		return $result + $bet;
	}

	private function getAfterBalance($row) {
		return NULL;
	}

	private function getGameDescriptionInfo($row, $unknownGame) {

        $game_description_id = null;
        $game_type_id = $unknownGame->game_type_id;
		$external_game_id = $row->ttg_game;

		$extra = array('game_code' => $row->ttg_game,'external_game_id' => $external_game_id);

		return $this->processUnknownGame(null, $game_type_id, $row->ttg_game, $unknownGame->game_name, $external_game_id, $extra);
	}

	public function queryTransaction($transactionId, $extra) {
        $playerName = $extra['playerName'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $amt = $extra['amount'];

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amt,
		);
		# param should be negative if withdraw
		$amt = $extra['transfer_method']=="withdrawal"?("-".$amt):$amt;
		$params = array(
			'partnerId' => $this->partner_id,
			'ext_transaction_ID' => $transactionId,
			'transactiondetail' => array(
				'transactionTime_attr' => $extra['transfer_time'],
				'amount_attr' => $amt,
				'uid_attr' => $gameUsername,
			),
		);
		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultXml = $this->getResultXmlFromParams($params);
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$resultXml = $resultXml['@attributes'];
		$success = $this->processResultBoolean($responseResultId, $resultXml);

        if (isset($resultXml['retry']) && $resultXml['retry'] == self::TRANSFER_FLAG_SUCCESS){
        	$result["order_message"] ="Transaction Success";
        	$result["order_status"] = true;
        }else{
        	$result["order_message"] ="API Failed!";
        	$result["order_status"] = false;
        }

        return array($success, $result);
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	public function syncPlayerAccount($username, $password, $playerId) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function processResultForQueryPlayerInfo($params) {
		return $this->returnUnimplemented();
	}

	public function changePassword($playerName, $oldPassword, $newPassword) {
		return $this->returnUnimplemented();
	}

	public function processResultForChangePassword($params) {
		return $this->returnUnimplemented();
	}

	//default on abstract
	// public function blockPlayer($playerName) {
	// 	return $this->returnUnimplemented();
	// }
	// public function processResultForBlockPlayer($params) {
	// 	return $this->returnUnimplemented();
	// }

	// public function unblockPlayer($playerName) {
	// 	return $this->returnUnimplemented();
	// }

	// public function processResultForUnblockPlayer($params) {
	// 	return $this->returnUnimplemented();
	// }

	public function logout($playerName, $password = null) {
		return $this->returnUnimplemented();
	}

	public function processResultForLogout($params) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

	public function processResultForUpdatePlayerInfo($apiName, $params, $responseResultId, $resultJson) {
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

	public function listPlayers($playerNames) {
		return $this->returnUnimplemented();
	}

	public function resetPlayer($playerName) {
		return $this->returnUnimplemented();
	}

	public function revertBrokenGame($playerName) {
		return $this->returnUnimplemented();
	}

	public function processGameList($game) {

		$attributes = json_decode($game['attributes']);
		$game_id = @$attributes->gameId;
		$game_type = @$attributes->gameType;
		$game_image = $game_id . '.' . $this->getGameImageExtension();

		return array(
			'game_id' => $game_id,
			'game_code' => $game['game_code'],
			'game_type' => $game_type,
			'game_name' => lang($game['game_name']),
			'game_image' => $game_image,
		);
	}

}

/*end of file*/
