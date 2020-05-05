<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
    * API NAME: HUB88
    * Hash: RSA-SHA256
    * Encryption: OPENSSL PEM KEY
    * Ticket No: OGP-15737
    *
    * @category Game_platform
    * @version not specified
    * @copyright 2013-2020 TripleOneTech
    * @author   Pedro P. Vitor Jr. <toto.php.ph@tripleonetech.net>
 */

abstract class Abstract_game_api_common_hub88 extends Abstract_game_api {
    const METHOD_POST = 'POST';
    const SUCCESS_CODE = "RS_OK";
    const SUCCESS_TRANSFER = "CTS_SUCCESS";
    const DECLINE_TRANSFER = "CTS_DECLINED";

    const WIN_KIND = "TK_WIN";
    const BET_KIND = "TK_BET";
    const ROLLBACK_KIND = "TK_ROLLBACK";

    const API_confirmTransferCredit = "confirmTransferCredit";

    const GAME_ENABLED = 1;
    const GAME_DISABLED = 0;

    const MD5_FIELDS_FOR_ORIGINAL = [
        "user",
        "hub88_updated_at",
        "transaction_uuid",
        "status",
        "round",
        "reference_transaction_uuid",
        "kind",
        "inserted_at",
        "currency",
        "external_game_id",
    ];

    const MD5_FLOAT_AMOUNT_FIELDS = [
        "hub88_id",
        "game_id",
        "amount",
    ];
    const MD5_FIELDS_FOR_MERGE=[
        "user",
        "hub88_updated_at",
        "transaction_uuid",
        "status",
        "round",
        "reference_transaction_uuid",
        "kind",
        "inserted_at",
        "currency",
        "external_uniqueid",
        "external_game_id",
    ];

    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        "hub88_id",
        "game_id",
        "amount",
    ];

    public function __construct() {
        parent::__construct();

        $this->method   = self::METHOD_POST;

        $this->api_url = $this->getSystemInfo('url');
        $this->operator_id  = $this->getSystemInfo('operator_id', 836);
        $this->sub_partner_id  = $this->getSystemInfo('sub_partner_id', 'soon88uat');
        $this->prefix_for_username = $this->getSystemInfo('prefix_for_username', 'dev');
        $this->product = $this->getSystemInfo('product','Microgaming'); 
        $this->conversion_rate = $this->getSystemInfo('conversion_rate',100000); 
        $this->language = $this->getSystemInfo('language', 'zh');                           // should be ISO 639-1 Code
        $this->lobby_url = $this->getSystemInfo('lobby_url', 'www.google.com');             // Lobby URL
        $this->deposit_url = $this->getSystemInfo('deposit_url', 'www.google.com');         // Deposit URL
        $this->currency_code = $this->getSystemInfo('currency_code', 'CNY');
        $this->country_code = $this->getSystemInfo('country_code', 'CN');
        $this->gameTimeToServerTime = $this->getSystemInfo('gameTimeToServerTime', '+8 hours');
        $this->serverTimeToGameTime = $this->getSystemInfo('serverTimeToGameTime', '-8 hours');
        $this->pem_file = $this->getSystemInfo('API_PEM_FILENAME');
        $this->pub_file = $this->getSystemInfo('API_PUB_FILENAME');
        $this->operator = $this->getSystemInfo('operator','T1');

        $this->URI_MAP = array(
            self::API_createPlayer => '/operator/generic/v2/transfer_wallet/create_wallet',
            self::API_queryPlayerBalance => '/operator/generic/v2/transfer_wallet/balance',
            self::API_depositToGame => '/operator/generic/v2/transfer_wallet/deposit',
            self::API_withdrawFromGame => '/operator/generic/v2/transfer_wallet/withdraw',
            self::API_queryTransaction => '/operator/generic/v2/transfer_wallet/check_transfer',
            self::API_queryGameListFromGameProvider => '/operator/generic/v2/game/list',
            self::API_queryForwardGame => '/operator/generic/v2/game/url',
            self::API_syncGameRecords => '/operator/generic/v2/transactions/list',
        );

    }

    public function getPlatformCode() {
        return $this->returnUnimplemented();
    }

    public function generateUrl($apiName, $params) {
        $apiUri = $this->URI_MAP[$apiName];
        $url=$this->api_url . $apiUri;

        $this->debug_log('********** HUB88 GeneratedUrl: '.$apiName, $url, 'Params: '.$params);
        return $url;
    }

    protected function customHttpCall($ch, $params) {
        switch ($this->method){
            case self::METHOD_POST:
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'X-Hub88-Signature: ' . $this->generateSignature($params),
                ));

                $this->utils->debug_log("HUB88: (customHttpCall) Params:", $params);
                break;
        }                
    }

    public function generateSignature($params) {
        $pem = APPPATH . '../../secret_keys/'.$this->pem_file; 
        $pub = APPPATH . '../../secret_keys/'.$this->pub_file; 

        $this->CI->utils->debug_log('********** HUB88 PEM File: ', $pem, 'PUB File: ', $pub);

        if (!file_exists($pub) || !file_exists($pem)) {
            $this->CI->utils->debug_log('********** HUB88 file not found', 'pem', $pem, 'pub', $pub);
            return false; 
        } else {
            $this->CI->utils->debug_log('********** HUB88 Params: ', $params);

            //$privateKeyId = openssl_pkey_get_private(($pem));
            $privateKeyId = openssl_pkey_get_private(file_get_contents($pem));
            openssl_sign($params, $signature, $privateKeyId, 'RSA-SHA256');
            $hub88_sign = base64_encode($signature);
            openssl_free_key($privateKeyId);
            $this->CI->utils->debug_log('********** HUB88 generated Signature: ', $hub88_sign);
            //$hub88_pub_key = openssl_pkey_get_public(($pub));
            $hub88_pub_key = openssl_pkey_get_public(file_get_contents($pub));
            openssl_verify($params, base64_decode($hub88_sign), $hub88_pub_key, 'RSA-SHA256');
            openssl_free_key($hub88_pub_key);

            return $hub88_sign;
        }

//        return $hub88_sign;
    }

    private function generateUUID($data) {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); 
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); 

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function processResultBoolean($responseResultId, $resultArr = NULL, $playerName = null) {
        $success = false;
        if(!empty($resultArr)){
            $success=true;
        }

        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('HUB88 Game got error: ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
        }

        return $success;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerName' => $playerName,
            'playerId' => $playerId,
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "user" => $gameUsername,
            "sub_partner_id" => $this->sub_partner_id,
            "request_uuid" => $uuid,
            "product" => $this->product,
            "operator_id" => $this->operator_id,
            "currency" => $this->currency_code
        ]);

        $this->CI->utils->debug_log('********** HUB88 createPlayer params **********', $params);

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);

        $result = ['response_result_id' => $responseResultId];

        if($success){
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        }
        return array($success, $result);
    }

    public function queryPlayerBalance($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $apiType = self::API_queryPlayerBalance;        

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "user" => $gameUsername,
            "sub_partner_id" => $this->sub_partner_id,
            "request_uuid" => $uuid,
            "product" => $this->product,
            "operator_id" => $this->operator_id,
            "currency" => $this->currency_code
        ]);

        $this->CI->utils->debug_log('********** HUB88 queryPlayerBalance **********', $params);

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    public function processResultForQueryPlayerBalance($params) {
        $gameName = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);

        $result = ['response_result_id'=>$responseResultId];

        if($resultArr['status']==self::SUCCESS_CODE){
            if(isset($resultArr['balance']) && !empty($resultArr['balance'])){
                $result['balance'] = $this->gameAmountToDB(floatval($resultArr['balance']));
            }else{
                $result['balance'] = 0;
            }
        }

        $success = ($resultArr['status']==self::SUCCESS_CODE) ? true : false;

        $this->CI->utils->debug_log('********** HUB88 result for queryPlayerBalance **********', $success);

        return array($success, $result, $resultArr);
    }


    public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $transaction_id = empty($transfer_secure_id) ? $this->generateSerialNo() : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'playerName' => $playerName,
            'external_transaction_id'=> $transaction_id,
            'amount' => $amount
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "user" => $gameUsername,
            "transaction_uuid" => $transaction_id,
            "sub_partner_id" => $this->sub_partner_id,
            "request_uuid" => $uuid,
            "product" => $this->product,
            "operator_id" => $this->operator_id,
            "currency" => $this->currency_code,
            "amount" => $this->dBtoGameAmount($amount)
        ]);

        $this->CI->utils->debug_log('********** HUB88 params for depositToGame **********', $params);

        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    public function processResultForDepositToGame($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult, $playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        if($success){
            $result["deposit_amount"] = $amount;
            $result["game_balance"] = $this->gameAmountToDB(floatval($arrayResult['balance'])); 
            $result['didnot_insert_game_logs']=true;
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }  else {
            $status = $arrayResult['status'];
            $result['reason_id'] = $this->getTransferErrorReasonCode($status);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_UNKNOWN;
        }
        return array($success, $result);
    }


    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null,$notRecordTransaction=false) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $transaction_id = empty($transfer_secure_id) ? $this->generateSerialNo() : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'external_transaction_id'=> $transaction_id,
            'amount' => $amount
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "user" => $gameUsername,
            "transaction_uuid" => $transaction_id,
            "sub_partner_id" => $this->sub_partner_id,
            "request_uuid" => $uuid,
            "product" => $this->product,
            "operator_id" => $this->operator_id,
            "currency" => $this->currency_code,
            "amount" => $this->dBtoGameAmount($amount)
        ]);

        $this->CI->utils->debug_log('********** HUB88 params for withdrawFromGame **********', $params);

        return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

    public function processResultForWithdrawFromGame($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult, $playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        if($success){
            $result["withdrawal_amount"] = $amount;
            $result["game_balance"] = $this->gameAmountToDB(floatval($arrayResult['balance'])); 
            $result['didnot_insert_game_logs']=true;
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }  else {
            $status = $arrayResult['status'];
            $result['reason_id'] = $this->getTransferErrorReasonCode($status);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_UNKNOWN;
        }
        return array($success, $result);
    }

    public function getTransferErrorReasonCode($errorCode) {
        switch ($errorCode) {
            case 'ACCESS_DENIED':
                $reasonCode = self::REASON_INVALID_KEY;
                break;
            case 'PLAYER_NOT_FOUND':
                $reasonCode = self::REASON_NOT_FOUND_PLAYER;
                break;
            case 'CURRENCY_MISMATCH':
                $reasonCode = self::REASON_CURRENCY_ERROR;
                break;
            case 'BAD_REQUEST':
                $reasonCode = self::REASON_INCOMPLETE_INFORMATION;
                break;
            default:
                $reasonCode = self::REASON_UNKNOWN;
        }

        return $reasonCode;
    }

    public function queryForwardGame($playerName, $extra = null) {
        if($playerName !== null) {
            $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
            $playerId = $this->getPlayerIdInPlayer($playerName);
        } 

        $game_mode = $extra['game_mode'];
        $game_code = explode("-",$extra['game_code']); //(int)$extra['game_code']; 
        $game_id = (int)$game_code[0]; 

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryForwardGame',
            'playerName' => $playerName
        );

        $playerToken = md5(random_bytes(16));

        if($game_mode == 'trial'){
            $params = json_encode([
                "sub_partner_id" => $this->sub_partner_id,
                "platform" => $extra['is_mobile'] ? 'GPL_MOBILE' : 'GPL_DESKTOP',
                "operator_id" => $this->operator_id,
                "lobby_url" =>$this->lobby_url,
                "lang" => $this->language,
                "ip" => $this->CI->input->ip_address(),
                //"game_id" => (int)$extra['game_code'],      //this is external_game_id on goto_hub88
                "game_id" => $game_id,
                "deposit_url" => $this->deposit_url,
                "currency" => 'XXX',
                "country" => $this->country_code
            ]);

            $this->CI->utils->debug_log('********** HUB88 params for TRIAL queryForwardGame **********', $params);
            return $this->callApi(self::API_queryForwardGame, $params, $context);

        } else {
            $params = json_encode([
                "user" => $gameUsername,
                "token" => $playerToken,
                "sub_partner_id" => $this->sub_partner_id,
                "platform" => $extra['is_mobile'] ? 'GPL_MOBILE' : 'GPL_DESKTOP',
                "operator_id" => $this->operator_id,
                "lobby_url" =>$this->lobby_url,
                "lang" => $this->language,
                "ip" => $this->CI->input->ip_address(),
                "game_id" => $game_id,
                "deposit_url" => $this->deposit_url,
                "currency" => $this->currency_code,
                "country" => $this->country_code
            ]);

            $this->CI->utils->debug_log('********** HUB88 params for queryForwardGame **********', $params);
            return $this->callApi(self::API_queryForwardGame, $params, $context);
        }
    }


    public function processResultForQueryForwardGame($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $arrayResult = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult, $playerName);

        $result['success'] = false;
        if($success){
            $result['success'] = true;
            $result['url'] = $arrayResult['url'];

            $this->CI->utils->debug_log('********** HUB88 result url for queryForwardGame **********', $result['url']);
        }
        return array($success, $result);
    }

    private function generateSerialNo() {
        $dt = new DateTime($this->utils->getNowForMysql());
        return $dt->format('Ymd').random_string('numeric', 6);
    }

    public function syncOriginalGameLogs($token = false) {
        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));

        $startDate->modify($this->getDatetimeAdjust());
        
        $startDate = $startDate->format('Y-m-d H:i:s');
        $endDate   = $endDate->format('Y-m-d H:i:s');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncGameRecords',
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "sub_partner_id" => $this->sub_partner_id,
            "start_time" => $startDate,
            "request_uuid" => $uuid,
            'operator_id' => $this->operator_id,
            "end_time" => $endDate,
            "currency" => $this->currency_code
        ]);

        $this->CI->utils->debug_log('********** HUB88 params for syncOriginalGameLogs **********', $params);

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    public function processResultForSyncGameRecords($params) {
        $this->CI->load->model(array('original_game_logs_model'));
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult, null);

        $dataResult = array(
            'data_count' => 0,
            'data_count_insert'=> 0,
            'data_count_update'=> 0
        );

        if($success && isset($arrayResult)) {
            $gameRecords = $arrayResult;

            $this->processGameRecords($gameRecords,$responseResultId);

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                $this->originalTable,
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_ORIGINAL,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS
            );

            $dataResult['data_count'] = count($gameRecords);
            if (!empty($insertRows)) {
                $dataResult['data_count_insert'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
            }
            unset($insertRows);

            if (!empty($updateRows)) {
                $dataResult['data_count_update'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
            }
            unset($updateRows);

        }
        return array($success, $dataResult);
    }

    public function processGameRecords(&$gameRecords, $responseResultId) {
        if(!empty($gameRecords)){
            foreach($gameRecords as $index => $record) {
                $data['user'] = isset($record['user']) ? $record['user'] : null;
                $data['hub88_updated_at'] = isset($record['updated_at']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['updated_at']))) : null;
                $data['transaction_uuid'] = isset($record['transaction_uuid']) ? $record['transaction_uuid'] : null;
                $data['status'] = isset($record['status']) ? $record['status'] : null;
                $data['round'] = isset($record['round']) ? $record['round'] : null;
                $data['reference_transaction_uuid'] = isset($record['reference_transaction_uuid']) ? $record['reference_transaction_uuid'] : null;
                $data['kind'] = isset($record['kind']) ? $record['kind'] : null;
                $data['inserted_at'] = isset($record['inserted_at']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['inserted_at']))) : null;
                $data['hub88_id'] = isset($record['id']) ? $record['id'] : null;
                //$data['game_id'] = isset($record['game_id']) ? $record['game_id'] : null;
                $data['game_id'] = isset($record['game_id']) ? $record['game_id'] : null;
                $data['external_game_id'] = isset($record['game_id']) ? $record['game_id'].'-'.$this->operator_id : null; // for matching of games in gamelist
                $data['currency'] = isset($record['currency']) ? $record['currency'] : null;
                $data['amount'] = isset($record['amount']) ? $record['amount'] : null;

                $data['response_result_id'] = $responseResultId;
                $data['external_uniqueid'] = $data['transaction_uuid']; //add external_uniueid for og purposes
                $gameRecords[$index] = $data;

                unset($data);
            }
        }
    }

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){
        $dataCount = 0;
        if(!empty($rows))
        {
            foreach ($rows as $key => $record) {
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal($this->originalTable, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal($this->originalTable, $record);
                }
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

        /**
     * queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time) {
        $sqlTime='hub88.inserted_at >= ? and hub88.hub88_updated_at <= ?';
        $sql = <<<EOD
SELECT
hub88.id as sync_index,
hub88.response_result_id,
hub88.user,
hub88.hub88_updated_at as end_at,
hub88.transaction_uuid,
hub88.round as round_number,
hub88.reference_transaction_uuid,
hub88.kind,
hub88.inserted_at as start_at,
hub88.inserted_at as bet_at,
hub88.hub88_id,
hub88.game_id,
hub88.currency,
hub88.amount as result_amount,
hub88.external_uniqueid,
hub88.md5_sum,
hub88.created_at,
hub88.updated_at,
hub88.external_game_id,

game_provider_auth.player_id,
game_provider_auth.login_name as player_username,

gd.id as game_description_id,
gd.game_name,
gd.game_type_id,
gd.game_code,
gt.game_type

FROM $this->originalTable as hub88
LEFT JOIN game_description as gd ON hub88.external_game_id = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON hub88.user = game_provider_auth.login_name
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

    /**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row) {
        if(!array_key_exists('md5_sum', $row)){
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow(
                $row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE
            );
        }

        $result_amt = $this->gameAmountToDB(floatval($row['result_amount'])); 

        if ($row['kind']==self::BET_KIND) {
            $result_amt = $result_amt * -1;
        }

        return [
            'game_info' => [
                'game_type_id'          => $row['game_type_id'],
                'game_description_id'   => $row['game_description_id'],
                'game_code'             => $row['game_code'],
                'game_type'             => $row['game_type'],
                'game'                  => $row['game_name']
            ],
            'player_info' => [
                'player_id'             => $row['player_id'],
                'player_username'       => $row['player_username']
            ],
            'amount_info' => [
                'bet_amount'            => ($row['kind'] == self::BET_KIND) ? abs($result_amt) : 0,
                'result_amount'         => $result_amt,
                'bet_for_cashback'      => ($row['kind'] == self::BET_KIND) ? abs($result_amt) : 0,
                'real_betting_amount'   => ($row['kind'] == self::BET_KIND) ? abs($result_amt) : 0,
                'win_amount'            => null,
                'loss_amount'           => null,
                'after_balance'         => null
            ],
            'date_info' => [
                'start_at'              => $row['start_at'],
                'end_at'                => $row['end_at'],
                'bet_at'                => $row['bet_at'],
                'updated_at'            => $row['updated_at']
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => $row['status'],
            'additional_info' => [
                'has_both_side'         => 0,
                'external_uniqueid'     => $row['external_uniqueid'],
                'round_number'          => $row['round_number'],
                'md5_sum'               => $row['md5_sum'],
                'response_result_id'    => $row['response_result_id'],
                'sync_index'            => $row['sync_index'],
                'bet_type'              => null
            ],
            'bet_details' => ['Created At' => $this->CI->utils->getNowForMysql()],
            'extra' => [],

            'game_logs_id' => isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
        ];

    }

     /**
     *
     * perpare original rows, include process unknown game, pack bet details, convert game status
     *
     * @param  array &$row
     */
    public function preprocessOriginalRowForGameLogs(array &$row){
        if (empty($row['game_description_id']))
        {
            $unknownGame = $this->getUnknownGame($this->getPlatformCode());
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }

        $row['status'] = Game_logs::STATUS_SETTLED; 
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

    public function blockPlayer($playerName) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->blockUsernameInDB($playerName);
        return array("success" => true);
    }

    public function unblockPlayer($playerName) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->unblockUsernameInDB($playerName);
        return array("success" => true);
    }

    public function queryTransaction($transactionId, $extra) {
        $playerName=$extra['playerName'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'external_transaction_id' => $transactionId
        );

        $uuid = $this->generateUUID(openssl_random_pseudo_bytes(16));

        $params = json_encode([
            "user" => $gameUsername,
            "transaction_uuid" => $transactionId,
            "sub_partner_id" => $this->sub_partner_id,
            "request_uuid" => $uuid,
            "product" => $this->product,
            "operator_id" => $this->operator_id,
            "currency" => $this->currency_code
        ]);

        $this->CI->utils->debug_log('********** HUB88 params for queryTransaction **********', $params);
        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params){
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult);
        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
        );

        if ($success) {
            switch ($arrayResult['transaction']['status']) {
                case self::SUCCESS_TRANSFER:
                    $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
                    break;
                case self::DECLINE_TRANSFER:
                    $result['status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
            }

        }else{

            $result['status']=self::COMMON_TRANSACTION_STATUS_UNKNOWN;
            $result['reason_id'] = self::REASON_TRANSACTION_NOT_FOUND;
            $this->CI->utils->debug_log('********** HUB88 processResultForQueryTransaction ********** External transaction id not found!');
        }

        $this->CI->utils->debug_log('********** HUB88 processResultForQueryTransaction **********',$arrayResult['transaction']['status'], $result['status'], $success);
        return array($success, $result);
    }

    private function getReasons($reason_id) {
        switch ($reason_id) {
            case 0:
                return self::COMMON_TRANSACTION_STATUS_APPROVED;
                break;
            case 1:
                return self::COMMON_TRANSACTION_STATUS_UNKNOWN;
                break;
            default:
                return self::COMMON_TRANSACTION_STATUS_DECLINED;
                break;
        }
    }

    /* For gamelist API */    
    public function queryGameListFromGameProvider($extra=null) {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryGameListFromGameProvider'
        );

        $params = json_encode([
            "operator_id" => $this->operator_id
        ]);

        $this->CI->utils->debug_log('********** HUB88 params for queryGameListFromGameProvider **********', $params);
        return $this->callApi(self::API_queryGameListFromGameProvider, $params, $context);
    }

    public function processResultForQueryGameListFromGameProvider($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult);
        $result = [
            'games'
        ];

        if($success) {
            $result['games'] = $arrayResult;
        }
        
        return array($success, $result);
    }

    const MD5_FIELDS_FOR_GAMES = [
        'game_name',
        'game_id',
        'game_code',
        'category',
        'enabled',
        'product',
        'platforms',
        'url_background',
        'url_thumb',
        'blocked_countries'
    ];

    const GAMELIST_TABLE = 'hub88_gamelist';

    public function preProccessGames($games) {
        $data = [];

        foreach ($games as $game) {
            $newGame = [];
            $newGame['game_name'] = isset($game['name']) ? $game['name'] : '';
            $newGame['game_id'] = isset($game['game_id']) ? $game['game_id'] : '';
            $newGame['game_code'] = isset($game['game_code']) ? $game['game_code'] : '';
            $newGame['category'] = isset($game['category']) ? $game['category'] : '';
            $newGame['enabled'] = isset($game['enabled']) ? $game['enabled'] : '';
            $newGame['product'] =  isset($game['product']) ? $game['product'].'-'.$this->operator : '';
            $newGame['platforms'] = isset($game['platforms']) ? json_encode($game['platforms']) : '';
            $newGame['url_background'] = isset($game['url_background']) ? $game['url_background'] : '';
            $newGame['url_thumb'] = isset($game['url_thumb']) ? $game['url_thumb'] : '';
            $newGame['blocked_countries'] = isset($game['blocked_countries']) ? json_encode($game['blocked_countries']) : '';
            $newGame['external_uniqueid'] = isset($game['game_code']) ? $game['game_code'].'-'.$game['game_id'] : '';

            $md5sum = "";
            foreach(self::MD5_FIELDS_FOR_GAMES as $field) {
                $md5sum .= $newGame[$field];
            }
            $md5sum = md5($md5sum);
            $newGame['md5_sum'] = $md5sum;

            $data[$newGame['external_uniqueid']] = $newGame;
        }

        return $data;
    }

    public function updateGameList($games) {

        $this->CI->load->model(array('original_game_logs_model'));
        $games = $this->preProccessGames($games);


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

    private function processGameListStatus($status) {
        $data = "";
        switch($status) {
            case self::GAME_ENABLED:
                $data = "Enabled";
                break;
            case self::GAME_DISABLED:
                $data = "Disabled";
                break;
        }
        return $data;
    }

    private function updateOrInsertGameList($data, $queryType){
        $dataCount=0;
        if(!empty($data)){
            $caption = [];
            if ($queryType == 'update') {
                $caption = "## UPDATE HUB88 GAME LIST\n";
            }
            else {
                $caption = "## ADD NEW HUB88 GAME LIST\n";
            }

            $body = "| English Name | Game Code | Game ID | Game Type | Product | Platforms | External Game ID | URL Thumb |\n";
            $body .= "| :--- | :--- | :--- |\n";
            foreach ($data as $record) {
                $external_game_id = $record['game_id'].'-'.$this->operator_id;
                if ($queryType == 'update') {
                    $record['updated_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->updateRowsToOriginal(self::GAMELIST_TABLE, $record);
                    $body .= "| {$record['game_name']} | {$record['game_code']} | {$record['game_id']} | {$record['category']} | {$record['product']} | {$record['platforms']} | {$external_game_id} | -{$record['url_thumb']} |\n";
                } else {
                    unset($record['id']);
                    $record['created_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->insertRowsToOriginal(self::GAMELIST_TABLE, $record);
                    $body .= "| {$record['game_name']} | {$record['game_code']} | {$record['game_id']} | {$record['category']} | {$record['product']} | {$record['platforms']} | {$external_game_id} |  -{$record['url_thumb']} |\n";
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
            "#MG-HUB88-For-".$this->operator
        ];

        $channel = $this->utils->getConfig('gamelist_notification_channel');
        $user = 'Hub88 Game List';

        $this->CI->load->helper('mattermost_notification_helper');
        sendNotificationToMattermost($user, $channel, [], $message);    
    }    
}