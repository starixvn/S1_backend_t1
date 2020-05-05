<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
    * API NAME: TFGaming Esports API
    *
    * @category Game_platform
    * @version not specified
    * @copyright 2013-2019 TripleOneTech
    * @integrator @mccoy.php.ph
**/

class Game_api_tfgaming_esports extends Abstract_game_api {

	const MD5_FIELDS_FOR_ORIGINAL = [
        'id_number',
        'odds',
        'game_type_name',
        'game_market_name',
        'bet_type_name',
        'competition_name',
        'event_id',
        'event_name',
        'event_datetime',
        'date_created',
        'settlement_datetime',
        'settlement_status',
        'bet_selection',
        'currency',
        'amount',
        'result_status',
        'earnings',
        'handicap',
        'member_code',
        'is_unsettled'
    ];

	const MD5_FLOAT_AMOUNT_FIELDS = [
        'odds',
        'amount',
        'earnings',
        'handicap'
    ];

	const MD5_FIELDS_FOR_MERGE = [
        'external_uniqueid',
        'bet_amount',
        'real_betting_amount',
        'result_amount',
        'round_number',
        'game_code',
        'game_name',
        'player_username',
        'start_at',
        'end_at',
        'bet_at',
    ];

	const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE = [
        'bet_amount',
        'real_betting_amount',
        'result_amount',
    ];

	const GET = 'GET';
	const POST = 'POST';
    const ORIGINAL_LOGS_TABLE_NAME = 'tfgaming_esports_game_logs';
    const PAGE_SIZE = 100;
    const EURO = 1;
    const HONGKONG = 2;
    const INDO = 3;
    const MALAY = 4;

	public function getPlatformCode()
    {
        return TFGAMING_ESPORTS_API;
    }

	public function __construct() {
		parent::__construct();
		$this->authorization = $this->getSystemInfo('authorization');
        $this->public_token = $this->getSystemInfo('public_token');
        $this->tfgaming_game_url = $this->getSystemInfo('tfgaming_game_url');
        $this->api_url = $this->getSystemInfo('url');
        $this->operator_id = $this->getSystemInfo('operator_id');
        $this->method = self::POST;

		$this->URI_MAP = array(
            self::API_createPlayer => '/api/v2/deposit/',
			self::API_depositToGame 	 => '/api/v2/deposit/',
			self::API_withdrawFromGame 	 => '/api/v2//withdraw/',
			self::API_syncGameRecords	 => '/api/v2/bet-transaction/',
			self::API_queryPlayerBalance	 => '/api/v2/balance/',
            self::API_queryTransaction => '/api/v2/transfer-status/',
		);
	}

	protected function getHttpHeaders($params) {

    	$headers = [];
    	if($this->URI_MAP != self::API_queryPlayerBalance && self::API_syncGameRecords) {
    		$headers = [
    		'Authorization' => $this->authorization,
    		'Content-Type' => 'application/json'
    		];
    	} else {
    		$headers = [
    			'Authorization' => $this->authorization
    		];
    	}

    	return $headers;

    }

    protected function customHttpCall($ch, $params) {

        switch ($this->method){
            case self::POST:
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                break;
        }

        $this->utils->debug_log('TFGaming Esports Request Field: ',json_encode($params));

    }

    public function generateUrl($apiName, $params) {
    	$url = $this->api_url.$this->URI_MAP[$apiName];

        if($this->method == self::GET&&!empty($params)){
            $url = $url . '?' . http_build_query($params);
        } else {
            $url;
        }

        return $url;
    }

    public function callback($method = null, $post = null) {

        $this->CI->load->model('common_token');
        $loginName = $this->CI->common_token->getPlayerInfoByToken($post['token']);

        if(!empty($loginName)){
            $gameUsername = $this->getGameUsernameByPlayerUsername($loginName['username']);
            $result['loginName'] = $gameUsername;
        } else {
            return "Invalid Token";
        }

        return $result;    

    }

    public function processResultBoolean($responseResultId, $resultArr, $statusCode){

        $success = false;
        if(!empty($resultArr) && $statusCode == 201 || $statusCode == 200){
            $success=true;
        }

        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('TFgaming Esports Game got error: ', $responseResultId,'result', $resultArr);
        }
        return $success;

    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {

        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $external_transaction_id = 'T'.$this->CI->utils->randomString(12);

        $context = [
                'callback_obj' => $this,
                'callback_method' => 'processResultForCreatePlayer',
                'gameUsername' => $gameUsername,
                'playerName' => $playerName,
                'playerId' => $playerId,
                'external_transaction_id' => $external_transaction_id
            ];

        $params = [
            'member' => $gameUsername,
            'operator_id' => $this->operator_id,
            'amount' => 0.01,
            'reference_no' => $external_transaction_id
        ];

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        $this->method = self::POST;

        return $this->callApi(self::API_createPlayer, $params, $context);  

    }

    public function processResultForCreatePlayer($params) {

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        
        $this->CI->utils->debug_log('processResultForCreatePlayer ==========================>', $resultArr);
        $statusCode = $this->getStatusCodeFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        #withdraw deposit amount  on create player
        if($success) {
            sleep(4);
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            $this->withdrawFromGame($playerName, 0.01, null); 
        } else {
            $success = false;
        }

        return array($success, $resultArr);
    }

    public function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {

        $isError = true;
        switch($apiName) {
            case self::API_depositToGame:
            case self::API_withdrawFromGame:
            case self::API_createPlayer:
                $isError = $errCode || intval($statusCode, 10) > 404;
                break;
            default:
                $isError =  parent::isErrorCode($apiName, $params, $statusCode, $errCode, $error);
        }

        return $isError;

    }

    public function getReasons($statusCode) {

        switch ($statusCode) {
            case 400:
                return self::REASON_INCORRECT_MERCHANT_ID;
                break;
            case 409:
                return self::REASON_TRANSFER_AMOUNT_IS_TOO_LOW;
                break;
            case 401:
                return self::REASON_ACCESS_USERTOKEN_ERROR;
                break;
            case 404:
                return self::REASON_TOKEN_VERIFICATION_FAILED;
                break;
            case 4091:
                return self::REASON_INSUFFICIENT_AMOUNT;
                break;
            case 4092:
                return self::REASON_GAME_ACCOUNT_LOCKED;
                break;

            default:
                return self::REASON_UNKNOWN;
                break;

        }

    }


    public function depositToGame($playerName, $amount, $transfer_secure_id = null) {
    	
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $external_transaction_id = empty($transfer_secure_id) ? 'T'.$this->CI->utils->randomString(12) : $transfer_secure_id;

        $context = [
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        ];

        $params = [
            'member' => $gameUsername,
            'operator_id' => $this->operator_id,
            'amount' => $amount,
            'reference_no' => $external_transaction_id
        ];

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        $this->method = self::POST;

        return $this->callApi(self::API_depositToGame, $params, $context);

    }

    public function processResultForDepositToGame($params) {

        $playerName = $this->getVariableFromContext($params, 'playerName');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        $result = [
            'response_result_id' => $responseResultId,
            'external_transaction_id' => $external_transaction_id,
            'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id' => self::REASON_UNKNOWN
        ];

        if ($success) {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs'] = true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id'] = $this->getReasons($statusCode);
        }

        return [$success, $result];

    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id = null) {
    	
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $external_transaction_id = empty($transfer_secure_id) ? 'T'.$this->CI->utils->randomString(12) : $transfer_secure_id;

        $context = [
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_transaction_id
        ];

        $params = [
            'member' => $gameUsername,
            'operator_id' => $this->operator_id,
            'amount' => $amount,
            'reference_no' => $external_transaction_id
        ];

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        $this->method = self::POST;

        return $this->callApi(self::API_withdrawFromGame, $params, $context);

    }

    public function processResultForWithdrawFromGame($params){

        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        $result = [
            'response_result_id' => $responseResultId,
            'external_transaction_id' => $external_transaction_id,
            'transfer_status' => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id' => self::REASON_UNKNOWN
        ];

        if ($success) {
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs'] = true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id'] = $this->getReasons($statusCode);
        }

        return [$success, $result];

    }

    public function queryTransaction($transactionId, $extra) {

        $playerName = $extra['playerName'];

        $context = [
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'playerName' => $playerName,
            'external_transaction_id' => $transactionId,
        ];

        $params = [
            'member_code' => $playerName,
            'reference_no' => $transactionId
        ];

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        $this->method = self::GET;

        return $this->callApi(self::API_queryTransaction, $params, $context);

    }

    public function processResultForQueryTransaction($params) {

        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $statusCode = $this->getStatusCodeFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        if($success){
            $result['status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {
            $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);

    }

    public function queryPlayerBalance($playerName) {
    	
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = [
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
        ];

        $params = [
            'LoginName' => $gameUsername,
        ];

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        $this->method = self::GET;

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);

    }

    public function processResultForQueryPlayerBalance($params) {

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $statusCode = $this->getStatusCodeFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);
        $result = ['response_result_id'=>$responseResultId];
        
        if($success){
            if(isset($resultArr['results'][0]['balance'])){
                $result['balance'] = $resultArr['results'][0]['balance'];
            }else{
                //wrong result, call failed
                $success=false;
            }
        }

        return [$success, $result];

    }

    public function queryForwardGame($playerName, $extra) {
    	
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
        $token = $this->getPlayerToken($playerId);

        $params = [
            'auth' => $this->public_token,
            'token' => $token
        ];

        $url = $this->tfgaming_game_url . "?" . http_build_query($params);

        $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

        return array('success' => true, 'url' => $url);

    }

    public function syncOriginalGameLogs($token = false) {

    	$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDateTime = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $startDateTime->modify($this->getDatetimeAdjust());
        $endDateTime = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));

        $startDate = $startDateTime->format('Y-m-d\TH:i:s\Z');
        $endDate   = $endDateTime->format('Y-m-d\TH:i:s\Z');

        $success=false;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncOriginalGameLogs'
        );

        $rowsCount=0;
        //always start from 1
        $currentPage = 1;
        $done = false;
        while (!$done) {
            $params = [
                'from_datetime' => $startDate,
                'to_datetime' => $endDate,
                'page' => $currentPage,
                'page_size' => self::PAGE_SIZE,
            ];

            $this->method = self::GET;

            $this->CI->utils->debug_log('<-------------------------PARAMS------------------------->', $params);

            $api_result = $this->callApi(self::API_syncGameRecords, $params, $context);

            $done = true;
            if ($api_result && $api_result['success']) {
                $totalPages = @$api_result['totalPages'];
                $totalCount = @$api_result['totalCount'];
                $data_count = @$api_result['data_count'];
                $rowsCount+=$totalCount;
                //next page
                $currentPage += 1;
                $done = $currentPage >= $totalPages;
                $this->debug_log('currentPage: ',$currentPage,'totalCount',$totalCount,'totalPages', $totalPages, 'done', $done, 'result', $api_result);
            }
            if ($done) {
                $success = true;
            }
        }

        

        return array('success' => $success, 'rows_count'=>$rowsCount);

    }

    public function processResultForSyncOriginalGameLogs($params){
        $this->CI->load->model('original_game_logs_model');
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $statusCode = $this->getStatusCodeFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $statusCode);

        $result = ['data_count' => 0];
        $gameRecords = isset($resultArr['results']) ? $resultArr['results'] : null;

        if($success && !empty($gameRecords)) {
            $extra = ['response_result_id' => $responseResultId];
            $this->processGameRecords($gameRecords, $extra);

            list($insertRows, $updateRows) = $this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                self::ORIGINAL_LOGS_TABLE_NAME,
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

            if (!empty($insertRows)){
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($insertRows);

            if (!empty($updateRows)){
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($updateRows);

        }


        return array($success, $result);
    }

    private function processGameRecords(&$gameRecords, $extra) {

        if(!empty($gameRecords)){
            foreach($gameRecords as $index => $record) {
                $data['id_number'] = isset($record['id']) ? $record['id'] : null;
                $data['odds'] = isset($record['odds']) ? $record['odds'] : null;
                $data['game_type_name'] = isset($record['game_type_name']) ? $record['game_type_name'] : ($record['is_combo'] == true ? 'COMBO' : null);
                $data['game_market_name'] = isset($record['game_market_name']) ? $record['game_market_name'] : ($record['is_combo'] == true ? 'COMBO' : null);
                $data['bet_type_name'] = isset($record['bet_type_name']) ? $record['bet_type_name'] : null;
                $data['competition_name'] = isset($record['competition_name']) ? $record['competition_name'] : null;
                $data['event_id'] = isset($record['event_id']) ? $record['event_id'] : null;
                $data['event_name'] = isset($record['event_name']) ? $record['event_name'] : ($record['is_combo'] == true ? 'COMBO' : null);
                $data['event_datetime'] = isset($record['event_datetime']) ? $this->gameTimeToServerTime($record['event_datetime']) : null;
                $data['date_created'] = isset($record['date_created']) ? $this->gameTimeToServerTime($record['date_created']) : null;
                $data['settlement_datetime'] = isset($record['settlement_datetime']) ? $this->gameTimeToServerTime($record['settlement_datetime']) : null;
                $data['bet_selection'] = isset($record['bet_selection']) ? $record['bet_selection'] : null;
                $data['currency'] = isset($record['currency']) ? $record['currency'] : null;
                $data['amount'] = isset($record['amount']) ? $record['amount'] : null;
                $data['settlement_status'] = isset($record['settlement_status']) ? $record['settlement_status'] : null;
                $data['result_status'] = isset($record['result_status']) ? $record['result_status'] : null;
                $data['earnings'] = isset($record['earnings']) ? $record['earnings'] : null;
                $data['handicap'] = isset($record['handicap']) ? $record['handicap'] : null;
                $data['member_code'] = isset($record['member_code']) ? $record['member_code'] : null;
                $data['is_combo'] = isset($record['is_combo']) ? $record['is_combo'] : null;
                $data['tickets'] = isset($record['tickets']) ? json_encode($record['tickets']) : null;
                $data['is_unsettled'] = isset($record['is_unsettled']) ? $record['is_unsettled'] : null;
                $data['malay_odds'] = isset($record['malay_odds']) ? $record['malay_odds'] : null;
                $data['euro_odds'] = isset($record['euro_odds']) ? $record['euro_odds'] : null;
                $data['member_odds'] = isset($record['member_odds']) ? $record['member_odds'] : null;
                $data['member_odds_style'] = isset($record['member_odds_style']) ? $record['member_odds_style'] : null;
                $data['ticket_type'] = isset($record['ticket_type']) ? $record['ticket_type'] : null;
                // //default data
                $data['external_uniqueid'] = $record['id'];
                $data['response_result_id'] = $extra['response_result_id'];
                $gameRecords[$index] = $data;
                unset($data);

            }
        }
    }

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){

        $dataCount = 0;
        if(!empty($rows)) {
            foreach ($rows as $key => $record) {
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
                }
                $dataCount++;
                unset($record);
            }
        }
        return $dataCount;
    }

    public function syncMergeToGameLogs($token) {
    	$enabled_game_logs_unsettle=true;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }


         /* queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        //only one time field
        $sqlTime='tf.date_created >= ? AND tf.date_created <= ?';
        if($use_bet_time){
            $sqlTime='tf.date_created >= ? AND tf.date_created <= ?';
        }

        $sql = <<<EOD
SELECT
tf.id as sync_index,
tf.response_result_id,
tf.external_uniqueid,
tf.md5_sum,

tf.id_number as round_number,
tf.odds,
tf.game_type_name as game_name,
tf.game_type_name as game_code,
tf.game_market_name,
tf.bet_type_name,
tf.competition_name,
tf.event_id,
tf.event_name,
tf.event_datetime,
tf.date_created as bet_at,
tf.date_created as start_at,
tf.date_created as end_at,
tf.settlement_datetime,
tf.bet_selection,
tf.currency,
tf.amount as bet_amount,
tf.amount as real_betting_amount,
tf.settlement_status,
tf.result_status,
tf.earnings as result_amount,
tf.handicap,
tf.is_combo,
tf.member_code as player_username,
tf.is_unsettled,
tf.tickets,
tf.malay_odds,
tf.euro_odds,
tf.member_odds,
tf.member_odds_style,
tf.ticket_type,

game_provider_auth.player_id,
gd.id as game_description_id,
gd.game_type_id

FROM tfgaming_esports_game_logs as tf
LEFT JOIN game_description as gd ON tf.game_type_name = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON tf.member_code = game_provider_auth.login_name
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

        $this->debug_log('merge sql', $sql, $params);

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }


    public function makeParamsForInsertOrUpdateGameLogsRow(array $row) {

        if(empty($row['md5_sum'])){
            $this->CI->utils->error_log('no md5 on ', $row['external_uniqueid']);
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }

        if(!empty($row['settlement_datetime'])) {
            $row['end_at'] = $row['settlement_datetime'];
        }

        if(empty($row['result_amount'])) {
            $row['result_amount'] = 0;
        } else {
            $row['result_amount'] = $row['result_status'] == 'WIN' || $row['result_status'] == 'DRAW' ? $row['result_amount'] - $row['bet_amount'] : $row['result_amount'];
        }


        return [
            'game_info' => [
                'game_type_id' => $row['game_type_id'],
                'game_description_id' => $row['game_description_id'],
                'game_code' => $row['game_code'],
                'game_type' => null,
                'game' => $row['game_name'],
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['player_username'],
            ],
            'amount_info' => [
                'bet_amount' => $row['bet_amount'],
                'result_amount' => $row['result_amount'],
                'bet_for_cashback' => $row['bet_amount'],
                'real_betting_amount' => $row['real_betting_amount'],
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => null,
            ],
            'date_info' => [
                'start_at' => $row['start_at'],
                'end_at' => $row['end_at'],
                'bet_at' => $row['bet_at'],
                'updated_at' => $this->CI->utils->getNowForMysql(),
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => $row['status'],
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['round_number'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => $row['response_result_id'],
                'sync_index' => $row['sync_index'],
                'bet_type' => $row['bet_type']
            ],
            'bet_details' => $row['bet_details'],
            'extra' => [
                'odds' => $row['odds'],
                'handicap' => $row['handicap'],
                'odds_type' => $row['odds_type'],
                ],
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
        $status = $this->getGameRecordStatus($row['settlement_status']);
        $row['status'] = $status;
        $bet_details = $this->processBetDetails($row);
        $row['bet_details'] = $bet_details;
        $row['bet_type'] = $row['game_market_name'] . "<br />" . $row['bet_selection'];
        $row['odds'] = isset($row['member_odds']) && !empty($row['member_odds']) ? $row['member_odds'] : $row['odds'];
        $row['odds_type'] = $this->processOddType($row['member_odds_style']);

    }

    public function getGameDescriptionInfo($row, $unknownGame) {

        $game_description_id = null;
        $game_name = $row['game_name'];
        $external_game_id = $row['game_code'];
        $extra = array('game_code' => $external_game_id,'game_name' => $game_name);

        $game_type_id = $unknownGame->game_type_id ? $unknownGame->game_type_id : null;
        $game_type = $unknownGame->game_name ? $unknownGame->game_name : self::TAG_CODE_UNKNOWN_GAME;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $external_game_id, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

    private function getGameRecordStatus($status) {

        $this->CI->load->model(array('game_logs'));
        switch ($status) {
        case "confirmed":
            $status = Game_logs::STATUS_ACCEPTED;
            break;
        case "settled":
            $status = Game_logs::STATUS_SETTLED;
            break;
        case "cancelled":
            $status = Game_logs::STATUS_CANCELLED;
            break;
        }

        return $status;

    }

    public function processBetDetails($gameRecords) {
        if(!empty($gameRecords)) {
            $bet_details = array();
            if(isset($gameRecords['tickets']) && !empty($gameRecords['tickets'])) {
                $tickets = json_decode($gameRecords['tickets'], true);
                foreach ($tickets as $ticket => $value) {
                    $bet_details[] = [
                        'GameName' => 'GameName: '.$value['game_market_name'],
                        'BetType' => 'BetType: '.$value['bet_type_name'],
                        'RoundNo' => 'RoundNo: '.$value['id'],
                        'CompetitionName' => 'CompetitionName: '.$value['competition_name'],
                        'EventName' => 'EventName: '.$value['event_name'],
                        'Bet' => 'Bet: '.$value['bet_selection'],
                        'TicketType' => 'TicketType: '.$value['ticket_type']
                    ];
                }
                return $bet_details;
            } 
             else {
                $bet_details = [
                    'BetType' => 'BetType: '.$gameRecords['bet_type_name'],
                    'RoundNo' => 'RoundNo: '.$gameRecords['round_number'],
                    'CompetitionName' => 'CompetitionName: '.$gameRecords['competition_name'],
                    'EventName' => 'EventName: '.$gameRecords['event_name'],
                    'Bet' => 'Bet: '.$gameRecords['bet_selection'],
                    'TicketType' => 'TicketType: '.$gameRecords['ticket_type']
                ];
            }
            
        }

        return $bet_details;

    }

    public function processOddType($odd_type) {
        if(isset($odd_type) && !empty($odd_type)) {
            switch ($odd_type) {
                case 'euro':
                    $odd_type = self::EURO;
                    break;
                case 'hongkong':
                    $odd_type = self::HONGKONG;
                    break;
                case 'indo':
                    $odd_type = self::INDO;
                    break;
                case 'malay':
                    $odd_type = self::MALAY;
                    break;
            }

            return $odd_type;
        }

    }

    public function getOddsType($id = null) {

        $array = array(
            "1" => 'euro',
            "2" => 'hongkong',
            "3" => 'indo',
            "4" => 'malay',
        );

        return $array[$id];

    }

}