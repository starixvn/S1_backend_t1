<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
* Game Provider: Booming games 
* Game Type: Slots
* Wallet Type: Seamless
*
/**
* API NAME: BOOMING
*
* @category Game_platform
* @version not specified
* @copyright 2013-2019 TripleOneTech
* @integrator @renz.php.ph
**/

abstract class Abstract_game_api_common_gpk extends Abstract_game_api {

	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';

	const PROCESS_TRANSACTION = true;
	const DONT_PROCESS_TRANSACTION = false;

	const TP_GPK_SUPPLIER_CODE = 52;
	const TP_GPK_HTML5_PLATFORM = 900;

    const GAMELIST_TABLE = 'api_gamelist';

	# Fields in fg_seamless_gamelogs we want to detect changes for update
    const MD5_FIELDS_FOR_GAME_LOGS = [
        'AgentId',
        'UserAccount',
        'GameId',
        'WagersId',
        'GameAccount',
        'GameWagersId',
        'Bet',
        'ValidBet',
        'PayOff',
        'BetTime',
        'BalanceTime',
        'GameGroupType',
        'UpdateTime',
        'GameSupplier'
    ];

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_GAME_LOGS = [
        'Bet',
        'ValidBet',
        'PayOff'
    ];

	# Fields in game_logs we want to detect changes for merge, and when redtiger_idr_game_logs.md5_sum is empty
    const MD5_FIELDS_FOR_MERGE=[
        'external_uniqueid',
        'bet_amount',
        'game_code',
        'real_betting_amount',
        'result_amount',
        'start_at',
        'end_at'
    ];

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        'bet_amount',
        'real_betting_amount',
        'result_amount',
    ];    

    const MD5_FIELDS_FOR_GAMES = [
        'game_platform_id',
        'game_code',
        'json_fields'
    ];

    const LANGUAGES = [
        'SIMPLIFIED_CN'  => 0,
        'TRADITIONAL_CN' => 1,
        'ENGLISH'		 => 2,
        'KOREAN'		 => 3,
        'THAI'			 => 4,
        'MALAYSIAN'	     => 5,
        'VIETNAMESE'	 => 6
    ];

    const GAME_TYPES = [
        'SLOTS'  => 1,
        'FISHING' => 4
    ];


	const URI_MAP = array(
		self::API_createPlayer => '/api/V2/Game/CreateAccount',
		self::API_queryPlayerBalance => '/api/v2/Game/GetBalance',
		self::API_depositToGame => '/api/v2/Game/TransferTo',
		self::API_withdrawFromGame => '/api/v2/Game/TransferBack',
		self::API_queryTransaction => '/api/v2/Game/CheckTransfer',
		self::API_queryForwardGame => '/api/V2/Game/GetGameUrl',
		self::API_syncGameRecords => '/api/v2/Record/GetBetRecord',
		self::API_queryBetDetailLink => '/api/v2/Record/GetGameDetail',
        self::API_queryGameListFromGameProvider => '/api/V2/Game/GetGameList',
	);

	public function __construct() {
		parent::__construct();
		
		$this->agent_code = $this->getSystemInfo('agent_code');
		$this->api_key = $this->getSystemInfo('api_key');
		$this->sign_key = $this->getSystemInfo('sign_key');
		$this->game_url = $this->getSystemInfo('game_url');
		$this->record_url = $this->getSystemInfo('record_url');
		$this->currency = $this->getSystemInfo('currency');
		$this->language = $this->getSystemInfo('language', 'zh-cn');
		$this->prefix_for_username = $this->getSystemInfo('prefix_for_username');
		$this->adjust_datetime_minutes = $this->getSystemInfo('adjust_datetime_minutes', 10);
		$this->gameTimeToServerTime = $this->getSystemInfo('gameTimeToServerTime', '+8 hours');
		$this->serverTimeToGameTime = $this->getSystemInfo('serverTimeToGameTime', '-8 hours');
		$this->default_fix_name_length = $this->getSystemInfo('default_fix_name_length', 10);
		$this->maximum_user_length = $this->getSystemInfo('maximum_user_length', 20);
		$this->minimum_user_length = $this->getSystemInfo('minimum_user_length', 4);

		$this->use_game_url = false;

        $this->CI->load->model(['game_provider_auth', 'original_game_logs_model', 'response_result', 'game_description_model', 'player_model', 'common_token']);
	}

	public function getUnieuqID() {
		return hexdec(uniqid());
	}

	public function generateUrl($apiName, $params)
	{
		$apiUri = self::URI_MAP[$apiName];

		$url = $this->record_url . $apiUri;
		if ($this->use_game_url) {
			$url = $this->game_url . $apiUri;
		}
		return $url;
	}

	protected function customHttpCall($ch, $params)
	{
        if ($this->method == self::METHOD_POST || $this->method == self::METHOD_PUT) {
        	if ($this->method == self::METHOD_POST) {
	            curl_setopt($ch, CURLOPT_POST, true);
        	} else {
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_PUT);
        	}
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    }
    }

    protected function getHttpHeaders($params)
	{
		$headers['Key'] = $this->api_key;
		$headers['Content-Type'] = 'application/json';
		return $headers;
	}

	public function processResultBoolean($responseResultId, $resultArr, $statusCode, $process_transaction = false)
	{
		$success = false;
		if ((@$statusCode == 200 || @$statusCode == 201) && $resultArr['Code'] === 0 && $process_transaction == false) {
			$success = true;
		} else if ((@$statusCode == 200 || @$statusCode == 201) && $resultArr['Code'] === 0  && $process_transaction == true && $resultArr['Data']['Result'] === 1) {
			$success = true;
		}

		if (!$success) {
	        // $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $success, $callback_method, json_encode($resultArr), $callback_method, 200, null, null);
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('TP got error ', $responseResultId,'result', $resultArr);
		}
		return $success;
	}

	public function createPlayer($playerName = null, $playerId = null, $password = null, $email = null, $extra = null)
	{

        $extra = [
            'prefix' => $this->prefix_for_username,
            # fix exceed game length name
            'fix_username_limit' => true,
            'minimum_user_length' => $this->minimum_user_length,
            'maximum_user_length' => $this->maximum_user_length,
            'default_fix_name_length' => $this->default_fix_name_length,
            'check_username_only' => true,
            'strict_username_with_prefix_length' => true,
            'force_lowercase' => false
        ];

		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerId' => $playerId,
            'gameUsername' => $gameUsername
        );

		$params = array(
			'Account' => $gameUsername,
			'Agent'   => $this->agent_code,
			'key'     => $this->sign_key
		);

		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP createPlayer params ----------', $params);

        return $this->callApi(self::API_createPlayer, $params, $context);
	}

    public function processResultForCreatePlayer($params)
    {
		$statusCode = $this->getStatusCodeFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        $this->CI->utils->debug_log('---------- TP createPlayer response ----------', $resultArr);

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
			'Supplier' => self::TP_GPK_SUPPLIER_CODE,
			'Account' => $gameUsername,
			'Agent'   => $this->agent_code,
			'key'     => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP queryPlayerBalance params ----------', $params);

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

        $this->CI->utils->debug_log('---------- TP processResultForQueryPlayerBalance response ----------', $resultArr);

		if($success){
			$result['balance'] = $resultArr['Data'];
		}

		return array($success, $result);
	}

	private function getLanguageLauncher($language)
	{
		switch ($language) {
			case 'en-us':
			case 'EN-US':
			case 1:
				return self::LANGUAGES['ENGLISH'];
				break;
			case 'zh-cn':
			case 'ZH-CN':
            case 2:
				return self::LANGUAGES['SIMPLIFIED_CN'];
				break;
			case 'vi-vn':
			case 'VI-VN':
            case 4:
				return self::LANGUAGES['VIETNAMESE'];
				break;
			case 'ko-kr':
			case 'KO-KR':
            case 5:
				return self::LANGUAGES['KOREAN'];
				break;
			case 'th-th':
			case 'TH-TH':
			case 'thb-thb':
			case 'THB-THB':
            case 6:
				return self::LANGUAGES['THAI'];
				break;
			case 'my-mly':
			case 'MY-MLY':
				return self::LANGUAGES['MALAYSIAN'];
				break;
			default:
				return self::LANGUAGES['ENGLISH'];
                break;
		}
	}

	private function getReasons($responseCode)
	{
		switch ($responseCode) {
			case 1004:
				return self::REASON_INVALID_TRANSACTION_ID;
				break;
			case 1003:
				return self::REASON_INVALID_TRANSFER_AMOUNT;
				break;
			default:
                return self::REASON_UNKNOWN;
                break;
		}
	}

	private function getReasonsTransaction($responseCode)
	{
		switch ($responseCode) {
			case 2:
				return self::REASON_INVALID_TRANSFER_AMOUNT;
				break;
			default:
                return self::REASON_UNKNOWN;
                break;
		}
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = $this->getUnieuqID();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        );

		$params = array(
			'Supplier'   => self::TP_GPK_SUPPLIER_CODE,
			'Account'    => $gameUsername,
			'Agent'      => $this->agent_code,
			'Amount'     => $amount,
			'TransferSn' => $external_transaction_id,
			'key'        => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP depositToGame params ----------', $params);

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode, self::PROCESS_TRANSACTION);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

        $this->CI->utils->debug_log('---------- TP processResultForDepositToGame response ----------', $resultArr);

		if ($success) {
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        } else if (!empty($resultArr['Data']['Result'])) {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasonsTransaction($resultArr['Data']['Result']);
        } else {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = self::REASON_UNKNOWN;
        }

        return array($success, $result);
	}

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null,$notRecordTransaction=false)
    {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$external_transaction_id = $this->getUnieuqID();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        );

		$params = array(
			'Supplier'   => self::TP_GPK_SUPPLIER_CODE,
			'Account'    => $gameUsername,
			'Agent'      => $this->agent_code,
			'Amount'     => $amount,
			'TransferSn' => $external_transaction_id,
			'key'        => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP depositToGame params ----------', $params);

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

	public function processResultForWithdrawFromGame($params)
	{
		$statusCode = $this->getStatusCodeFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode, self::PROCESS_TRANSACTION);

		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id' => $external_transaction_id,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

        $this->CI->utils->debug_log('---------- TP processResultForWithdrawFromGame response ----------', $resultArr);

		if ($success) {
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        } else if (!empty($resultArr['Data']['Result'])) {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasonsTransaction($resultArr['Data']['Result']);
        } else {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = self::REASON_UNKNOWN;
        }

        return array($success, $result);
	}

	public function queryTransaction($transactionId, $extra) {
        $playerName=$extra['playerName'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'transaction_id' => $transactionId
        );

		$params = array(
			'Account'    => $gameUsername,
			'Agent'      => $this->agent_code,
			'TransferSn' => $transactionId,
			'key'        => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP queryTransaction params ----------', $params);
        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

	/**
	 * overview : process result for queryTransaction
	 * @param $apiName
	 * @param $params
	 * @param $responseResultId
	 * @param $resultXml
	 * @return array
	 */
	public function processResultForQueryTransaction($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$transactionId = $this->getVariableFromContext($params, 'transactionId');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode, self::PROCESS_TRANSACTION);

		$result = array(
			'response_result_id' => $responseResultId,
			'transactionId' => $transactionId,
			'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id' => self::REASON_UNKNOWN
		);

        $this->CI->utils->debug_log('---------- TP processResultForQueryTransaction response ----------', $resultArr);

		if ($success) {
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs'] = true;
        } else if (!empty($resultArr['Data']['Result'])) {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getReasonsTransaction($resultArr['Data']['Result']);
        } else {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = self::REASON_UNKNOWN;
        }
        return array($success, $result);
    }

	/*
	 *	To Launch Game
	 *
	*/
	public function queryForwardGame($playerName, $extra = null)
	{
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerId = $this->getPlayerIdInPlayer($playerName);

		$context = array(
			'callback_obj'    => $this,
			'callback_method' => 'processResultForQueryForwardGame',
			'gameUsername'    => $gameUsername
		);

		$params = array(
			'Account'      => $gameUsername,
			'Agent'        => $this->agent_code,
			'GId'          => $extra['game_code'],
			'PlatformType' => self::TP_GPK_HTML5_PLATFORM,
			'key'          => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;
		$params['Language'] = $this->getLanguageLauncher($this->language);

		$this->use_game_url = true;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP queryForwardGame params ----------', $params);
		return $this->callApi(self::API_queryForwardGame, $params, $context);
	}

	public function processResultForQueryForwardGame($params)
	{
		$statusCode       = $this->getStatusCodeFromParams($params);
		$resultArr        = $this->getResultJsonFromParams($params);
		$gameUsername     = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success          = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result           = array('url' => '');

        $this->CI->utils->debug_log('---------- TP processResultForQueryForwardGame response ----------', $resultArr);

		if ($success) {
			$result['url'] = @$resultArr['Data'];
			$this->CI->utils->debug_log('URL RESULT ==>', @$resultArr['Data']);
		}
		return array($success, $result);
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

    	$queryDateTimeStart = $startDateTime->format('Y/m/d H:i:s');
		$queryDateTimeEnd = $endDateTime->format('Y/m/d H:i:s');

		$result = array();
        $result[] = $this->CI->utils->loopDateTimeStartEnd($queryDateTimeStart, $queryDateTimeEnd, '+10 minutes', function($queryDateTimeStart, $queryDateTimeEnd)  {

            $queryDateTimeStart = $queryDateTimeStart->format('Y/m/d H:i:s');
            $queryDateTimeEnd = $queryDateTimeEnd->format('Y/m/d H:i:s');

			$context = array(
	            'callback_obj' => $this,
	            'callback_method' => 'processResultForSyncOriginalGameLogs'
	        );

			$params = array(
				'StartTime'    => $queryDateTimeStart . 'Z',
				'EndTime'      => $queryDateTimeEnd . 'Z',
				'WagersId' 	   => '',
				'IsUpdateTime' => 'false',
				'key'          => $this->sign_key
			);
			// FOR DATE BECAUSE HTTP_BUILD_QUERY ALONE ENCODE '/' to '%2F'
			$md5_sign = md5(urldecode(utf8_encode(http_build_query($params))));
			$params = ['Sign' => $md5_sign] + $params;

	        $this->CI->utils->debug_log('---------- TP syncOriginalGameLogs params ----------', $params, '------ MD5 SIGN -------', urldecode(utf8_encode(http_build_query($params))));

			$this->use_game_url = false;
			$this->method = self::METHOD_POST;

			return $this->callApi(self::API_syncGameRecords, $params, $context);
        });
	}

	public function processResultForSyncOriginalGameLogs($params)
	{
        $this->CI->load->model('original_game_logs_model');
		$statusCode = $this->getStatusCodeFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

		$result = ['data_count' => 0];
		$responseRecords = !empty($resultArr)?$resultArr:[];
		$gameRecords = !empty($responseRecords['Data']) ? $responseRecords['Data'] : [];

        $this->CI->utils->debug_log('---------- TP syncOriginalGameLogs response ----------', $params);

		if ($success && !empty($gameRecords)) {
            $extra = ['response_result_id' => $responseResultId];
            $gameRecords = $this->rebuildGameRecords($gameRecords,$extra);

            list($insertRows, $updateRows) = $this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                $this->original_gamelogs_table,
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_GAME_LOGS,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_GAME_LOGS
            );

            unset($gameRecords);

            if (!empty($insertRows))
            {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    ['responseResultId'=>$responseResultId], $this->original_gamelogs_table);
            }
            unset($insertRows);

            if (!empty($updateRows))
            {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                    ['responseResultId'=>$responseResultId], $this->original_gamelogs_table);
            }
            unset($updateRows);
		}
		$result['total_data'] = count($responseRecords['Data']);
		return array($success, $result);
	}

	private function rebuildGameRecords($gameRecords,$extra)
	{
		$newGR =[];
        foreach($gameRecords as $i => $gr)
        {
			$newGR[$i]['AgentId']            = isset($gr['AgentId']) ? $gr['AgentId'] : null;
			$newGR[$i]['UserAccount']		 = isset($gr['UserAccount']) ? $gr['UserAccount'] : null;
			$newGR[$i]['GameId']		     = isset($gr['GameId']) ? $gr['GameId'] : null;
			$newGR[$i]['WagersId']	 		 = isset($gr['WagersId']) ? $gr['WagersId'] : null;
			$newGR[$i]['GameAccount']		 = isset($gr['GameAccount']) ? $gr['GameAccount'] : null;
			$newGR[$i]['GameWagersId']	 	 = isset($gr['GameWagersId']) ? $gr['GameWagersId'] : null;
			$newGR[$i]['Bet']	 			 = isset($gr['Bet']) ? $gr['Bet'] : null;
			$newGR[$i]['ValidBet']	 		 = isset($gr['ValidBet']) ? $gr['ValidBet'] : null;
			$newGR[$i]['PayOff']	 		 = isset($gr['PayOff']) ? $gr['PayOff'] : null;
			$newGR[$i]['BetTime']	 		 = isset($gr['BetTime']) ? $this->gameTimeToServerTime($gr['BetTime']) : null;
			$newGR[$i]['BalanceTime']	 	 = isset($gr['BalanceTime']) ? $this->gameTimeToServerTime($gr['BalanceTime']) : null;
			$newGR[$i]['GameGroupType']	 	 = isset($gr['GameGroupType']) ? $gr['GameGroupType'] : null;
			$newGR[$i]['UpdateTime']	 	 = isset($gr['UpdateTime']) ? $this->gameTimeToServerTime($gr['UpdateTime']) : null;
			$newGR[$i]['GameSupplier']	 	 = isset($gr['GameSupplier']) ? $gr['GameSupplier'] : null;
			$newGR[$i]['response_result_id'] = isset($extra['response_result_id']) ? $extra['response_result_id'] : null;
			$newGR[$i]['external_uniqueid']  = isset($gr['WagersId']) ? $gr['WagersId'] : null;
        }
        $gameRecords = $newGR;
        return $gameRecords;
	}

	// This syncMerge is for bulk records which date time can be applied
    public function syncMergeToGameLogs($token)
    {
        $enabled_game_logs_unsettle=true;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }

   public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time)
    {
        $sqlTime = '`gpk`.`BetTime` >= ?
          AND `gpk`.`BetTime` <= ?';

        $sql = <<<EOD
SELECT 
    gd.game_type_id AS game_type_id,
    gd.id AS game_description_id,
    gpk.GameId as game_code,
    gt.game_type AS game_type,
    gd.game_name AS game_name,
    gpa.player_id AS player_id,
    gpk.UserAccount AS player_username,
    gpk.GameWagersId AS round_id,
    gpk.Bet AS bet_amount,
    gpk.ValidBet AS real_betting_amount,
    gpk.Payoff AS result_amount,
    gpk.BetTime AS start_at,
    gpk.UpdateTime AS end_at,
    gpk.external_uniqueid AS external_uniqueid,
    gpk.md5_sum AS md5_sum
    FROM {$this->original_gamelogs_table} AS gpk
	LEFT JOIN game_description AS gd ON gpk.GameId = gd.external_game_id AND gd.game_platform_id = ?
	LEFT JOIN game_type AS gt ON gd.game_type_id = gt.id
	LEFT JOIN game_provider_auth AS gpa ON gpk.UserAccount = gpa.login_name
	AND gpa.game_provider_id = ?
WHERE
	gpk.GameSupplier = ?
AND
    {$sqlTime}
EOD;

        $params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            self::TP_GPK_SUPPLIER_CODE,
            $dateFrom,
            $dateTo
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

	public function makeParamsForInsertOrUpdateGameLogsRow(array $row)
	{
        $extra = [
            'table' =>  $row['round_id'],
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
                'game_type' => $row['game_type'],
                'game' => $row['game_name']
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['player_username']
            ],
            'amount_info' => [
                'bet_amount' => $row['bet_amount'],
                'result_amount' => $row['result_amount'],
                'bet_for_cashback' => $row['bet_amount'],
                'real_betting_amount' => $row['real_betting_amount'],
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => 0,
            ],
            'date_info' => [
                'start_at' => $row['start_at'],
                'end_at' => $row['end_at'],
                'bet_at' => $row['start_at'],
                'updated_at' => $this->CI->utils->getNowForMysql(),
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => Game_logs::STATUS_SETTLED,
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['round_id'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => null,
                'sync_index' => null,
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

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[], $table_name)
    {
        $dataCount = 0;
        if(!empty($rows))
        {
            foreach ($rows as $key => $record)
            {
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal($table_name, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal($table_name, $record);
                }
                $dataCount++;
                unset($record);
            }
        }
        return $dataCount;
    }

	public function saveToResponseResult($success, $callMethod, $params, $response){
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResult($this->getPlatformCode(), $flag, $callMethod, json_encode($params), $response, 200, null, null);
    }    

    /** 
    *  The api will return the bet details URL link for viewing the details
    */
    public function queryBetDetailLink($playerUsername, $betid = NULL, $extra = NULL)
    {        
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);
        $playerId = $this->getPlayerIdInPlayer($playerUsername);

		$context = array(
			'callback_obj'    => $this,
			'callback_method' => 'processResultForQueryBetDetailLink',
			'gameUsername'    => $gameUsername
		);

		// Some parameters need a value of blank since they require this on generating Sign
		$params = array(
			'WagersId'     => $betid,
			'BetTime'      => '',
			'key'          => $this->sign_key
		);
		$md5_sign = md5(http_build_query($params));
		$params = ['Sign' => $md5_sign] + $params;
		$params['Language'] = $this->getLanguageLauncher($this->language);
		$params['Account'] = $gameUsername;

		$this->use_game_url = false;
		$this->method = self::METHOD_POST;

        $this->CI->utils->debug_log('---------- TP queryBetDetailLink params ----------', $params);
		return $this->callApi(self::API_queryBetDetailLink, $params, $context);
    }

    /** 
     * Process Result of queryBetDetailLink method
    */
    public function processResultForQueryBetDetailLink($params)
    {
		$statusCode = $this->getStatusCodeFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = ['url' => ''];

        $this->CI->utils->debug_log('---------- TP processResultForQueryBetDetailLink response ----------', $resultArr);

		if ($success) {
			$result['url'] = $resultArr['Data'];
        }

        return array($success, $result);
    }

    public function queryGameListFromGameProvider($extra = NULL) {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryGameListFromGameProvider',
        );
        $params = '';
		$this->use_game_url = true;
		$this->method = self::METHOD_POST;
        return $this->callApi(self::API_queryGameListFromGameProvider, $params, $context);
    }

    public function processResultForQueryGameListFromGameProvider($params) {
		$statusCode = $this->getStatusCodeFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
		$result = [];

        if ($success) {
            $result['games'] = $resultArr['Data'];
        }
        return array($success, $result);
    }

    public function rebuildGameList($games) {
        $data = [];
        foreach ($games as $game) {
            $newGame = [];
            $external_uniqueid = isset($game['Id']) ? $game['Id'] . '-' . $this->getPlatformCode() : '';

            $newGame['game_platform_id']  = $this->getPlatformCode();
            $newGame['game_code'] 		  = isset($game['Id']) ? $game['Id'] : '';
            $newGame['json_fields'] 	  = !empty($game) ? json_encode($game) : '';
            $newGame['external_uniqueid'] = isset($external_uniqueid) ? $external_uniqueid : '';
	        $data[] = $newGame;
        }
        return $data;
    }

    public function updateGameList($games) {

        $this->CI->load->model(array('original_game_logs_model'));
        $games = $this->rebuildGameList($games);

        list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
            self::GAMELIST_TABLE,
            $games,
            'external_uniqueid',
            'external_uniqueid',
            self::MD5_FIELDS_FOR_GAMES,
            'md5_sum',
            'id',
            []
        );

        $dataResult = [
            'data_count' => count($games),
            'data_count_insert' => 0,
            'data_count_update' => 0
        ];

        if (!empty($insertRows)) {
            $dataResult['data_count_insert'] += $this->updateOrInsertGameList($insertRows, 'insert');
        }
        unset($insertRows);

        if (!empty($updateRows)) {
            $dataResult['data_count_update'] += $this->updateOrInsertGameList($updateRows, 'update');
        }
        unset($updateRows);

        return $dataResult;
    }

    private function processGameListGameType($game_type) {
        $data = "";
        switch($game_type) {
            case self::GAME_TYPES['SLOTS']:
                $data = "Slots";
                break;
            case self::GAME_TYPES['FISHING']:
                $data = "Fishing";
                break;
			default:
                $data = "Others, Need to contact DEV | ";
                break;
        }
        return $data;
    }

    private function processGameListSupportedPlatform($platform) {
        $data = "";
        foreach ($platform as $val) {
	        switch($val) {
	            case self::TP_GPK_HTML5_PLATFORM:
	                $data .= "HTML5 | ";
	                break;
				default:
	                $data .= "Others, Need to contact DEV | ";
	                break;
	        }
        }
        return $data;
    }

    private function updateOrInsertGameList($data, $queryType){
        $dataCount = 0;
        if (!empty($data)) {
            $caption = [];
            if ($queryType == 'update') {
                $caption = "## UPDATE TP GAME LIST\n";
            }
            else {
                $caption = "## ADD NEW TP GAME LIST\n";
            }

            $body = "| English Name  | Chinese Name  | Game Code | Game Type | Supported Platform |\n";
            $body .= "| :--- | :--- | :--- |\n";

            foreach ($data as $record) {
            	$game = $record;
            	$record = json_decode($record['json_fields'], true);
                if ($queryType == 'update') {
                    $record['updated_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->updateRowsToOriginal(self::GAMELIST_TABLE, $game);
                    $body .= "| {$record['GameNameEn']} | {$record['GameName']} | {$record['Id']} | {$this->processGameListGameType($record['GameGroupType'])} | {$this->processGameListSupportedPlatform($record['PlatFormType'])} |\n";
                } else {
                    unset($record['id']);
                    $record['created_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->insertRowsToOriginal(self::GAMELIST_TABLE, $game);
                    $body .= "| {$record['GameNameEn']} | {$record['GameName']} | {$record['Id']} | {$this->processGameListGameType($record['GameGroupType'])} | {$this->processGameListSupportedPlatform($record['PlatFormType'])} |\n";
                }
                $dataCount++;
                unset($record);
            }

            $this->sendMatterMostMessage($caption, $body);
        }
        return $dataCount;
    }

    public function sendMatterMostMessage($caption, $body){
        $message = [
            $caption,
            $body,
            "#TP/GPK_API"
        ];

        $channel = $this->utils->getConfig('gamelist_notification_channel');
        $this->CI->load->helper('mattermost_notification_helper');
        $user = 'TP/GPK Game List';

        sendNotificationToMattermost($user, $channel, [], $message);
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
