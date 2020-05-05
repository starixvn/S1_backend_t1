<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
/**
    * API NAME: LE GAMING Transfer Wallet
    * Document Number: 1.2.0
    *
    * Other info:
    * Don't have any Application (Mobile, Desktop)
    * Minimum deposit: 0.01
    * Maximum deposit: 10000000
    *
    * @category Game_platform
    * @version 1.8.10
    * @copyright 2013-2018 TripleOneTech
**/

class Game_api_le_gaming extends Abstract_game_api {

    const SUCCESS_API_CODE = 0;
    const GAME_FOLD = 0;

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS = [
        'AllBet',
        'CellScore',
        'Profit',
        'Revenue',
    ];

    # Fields in le_gaming_game_logs we want to detect changes for update
    const MD5_FIELDS_FOR_ORIGINAL = [
        'GameID',
        'Accounts',
        'ServerID',
        'KindID',
        'TableID',
        'ChairID',
        'UserCount',
        'CellScore',
        'AllBet',
        'Profit',
        'Revenue',
        'GameStartTime',
        'GameEndTime',
        'CardValue',
        'ChannelID',
        'LineCode',
    ];

    const API_queryGameRecordsByDate = '';

    const MD5_FIELDS_FOR_MERGE = self::MD5_FIELDS_FOR_ORIGINAL;
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE = self::MD5_FLOAT_AMOUNT_FIELDS;

    public function __construct() {
        parent::__construct();
        $this->api_url = $this->getSystemInfo('url');
        $this->agent = $this->getSystemInfo('agent');
        $this->live_key = $this->getSystemInfo('live_mode') == self::FLAG_TRUE ? $this->getSystemInfo('live_key'): $this->getSystemInfo('sandbox_key');
        $this->live_secret = $this->getSystemInfo('live_mode') == self::FLAG_TRUE ? $this->getSystemInfo('live_secret'): $this->getSystemInfo('sandbox_secret');
        $this->game_record_url = $this->getSystemInfo('game_record_url');
        $this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+59 minutes');
        $this->lineCode = $this->getSystemInfo('lineCode', '1');#for sub agents
        $this->unknownGame = [];
        $this->demo_url = $this->getSystemInfo('demo_url', 'http://demo.leg666.com');
        $this->sync_sleep_time = $this->getSystemInfo('sync_sleep_time', '60');//sleep for 1 minute

        $this->URI_MAP = array(
            self::API_createPlayer => '0',
            self::API_queryPlayerBalance => '1',
            self::API_isPlayerExist => '1',
            self::API_queryForwardGame => '0',
            self::API_depositToGame => '2',
            self::API_withdrawFromGame => '3',
            self::API_logout => '8',
            self::API_queryGameRecordsByDate => '9',
            // self::API_unblockPlayer => '/games/block',
            self::API_syncGameRecords => '6',
            self::API_queryTransaction => '4',
            self::API_queryBetDetailLink => '19',
        );

    }

    public function getPlatformCode() {
        return LE_GAMING_API;
    }

    /**
     * [generateUrl generate api url per api call]
     * @param  [string] $apiName    [which api call like (createPlayer)]
     * @param  [array] $params      [details of request]
     * @return [string]             [api request url encoded]
     */
    public function generateUrl($apiName, $params) {
        if ($apiName == self::API_syncGameRecords || $apiName == self::API_queryBetDetailLink || $apiName == self::API_queryGameRecordsByDate) {
            $url = $this->game_record_url . '?' . http_build_query($params);
        }else{
            $url = $this->api_url . '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * [processResultBoolean check per api call if call is success]
     * @param  [int] $responseResultId  [response result id]
     * @param  [array] $resultArr       [contains parameter before call of response after call]
     * @param  [string] $playerName     [player game username]
     * @return [boolean]                [true/false]
     */
    public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {

        $resultArray = json_decode($resultArr['resultText'],true);
        $success = false;
        if (isset($resultArray['d']['code']) && $resultArray['d']['code'] == self::SUCCESS_API_CODE) {
            $success = true;
        }

        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('LE GAMING API got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
        }
        return $success;
    }

    /**
     * [createPlayer (request) create player on game provider auth and on api side]
     * @param  [string] $playerName [player real username]
     * @param  [int] $playerId      [player id]
     * @param  [string] $password   [player's password]
     * @param  [string] $email      [email]
     * @param  [array] $extra       [player extra details]
     * @return [array]              [response of api]
     */
    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerName'      => $playerName,
            'playerId'        => $playerId,
            'gameUsername'    => $gameUsername
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $time_str = $this->timestamp_str('YmdHis', '');
        $orderId = $this->agent . $time_str . $gameUsername;

        $processed_params = http_build_query([
            's'        => $this->URI_MAP[self::API_createPlayer],
            'account'  => $gameUsername,
            'money'    => 0,
            'orderid'  => $orderId,
            'ip'       => $this->CI->input->ip_address(),
            'lineCode' => $this->lineCode,
            'KindID'   => '',
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    /**
     * [processResultForCreatePlayer callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForCreatePlayer($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $success = $this->processResultBoolean($responseResultId, $params, $gameUsername);

        $result = array(
            "player" => $gameUsername,
            "exists" => false
        );

        if($success){
            $pid = isset($resultArr['pid'])?$resultArr['pid']:null;
            //update external AccountID
            $this->updateExternalAccountIdForPlayer($playerId,$pid);
            # update flag to registered = true
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            $result["exists"] = true;
        }

        $this->CI->utils->debug_log('processResultForCreatePlayer ===========>',$resultArr);
        return array($success, $result);
    }

    /**
     * [isPlayerExist (request) check if player exist on api side]
     * @param  [string]  $playerName [player real username]
     * @return [array]              [response of api]
     */
    public function isPlayerExist($playerName){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername
        );

        $params = [
            'agent' => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key' => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's'=> $this->URI_MAP[self::API_isPlayerExist],
            'account'=> $gameUsername,
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    /**
     * [processResultForIsPlayerExist callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForIsPlayerExist($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $success = $this->processResultBoolean($responseResultId, $params, $gameUsername);

        $resul["player"] = $gameUsername;
        if ($success) {
            $result["exists"] = true;
        }else{
            $result['exists'] = false;
        }

        $this->CI->utils->debug_log('processResultForIsPlayerExist ===========>',$resultArr);
        return array($success, $result);
    }

    /**
     * [queryPlayerBalance (request) check if player exist on api side]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function queryPlayerBalance($playerName) {
        $playerID = $this->getExternalAccountIdByPlayerUsername($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerName'      => $playerName,
            'gameUsername'    => $gameUsername
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's' => $this->URI_MAP[self::API_queryPlayerBalance],
            'account'=> $gameUsername,
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    /**
     * [processResultForQueryPlayerBalance callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForQueryPlayerBalance($params) {
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params, $gameUsername);

        $result = array();
        if($success){
            $result['balance'] = @floatval($resultArr['d']['money']);
        }

        $this->CI->utils->debug_log('processResultForQueryPlayerBalance ===========>',$resultArr);
        return array($success, $result);
    }

    /**
     * [queryTransaction (request) check if withdraw/deposit transaction succeeded]
     * @param  [string] $transactionId [description]
     * @param  [array] $extra         [description]
     * @return [array]                [description]
     */
    public function queryTransaction($transactionId, $extra) {
        $playerName = $extra['playerName'];
        $playerId = $extra['playerId'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj'              => $this,
            'callback_method'           => 'processResultForqueryTransaction',
            'gameUsername'              => $gameUsername,
            'external_transaction_id'   => $transactionId
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's' => $this->URI_MAP[self::API_queryTransaction],
            'orderid'=> $transactionId,
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);
        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    /**
     * [processResultForqueryTransaction description]
     * API possible response:
         0 : success
        -1 : transactionId not found
         2 : transaction failed
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForqueryTransaction($params){
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params, $gameUsername);

        $result = array(
            'response_result_id'        => $responseResultId,
            'external_transaction_id'   => $external_transaction_id,
            'status'                    => self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'                 => self::REASON_UNKNOWN
        );

        if ($success) {

            switch ($resultArr['d']['status']) {
                case '0':
                    $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
                    break;
                case '-1':
                    $result['reason_id'] = self::REASON_TRANSACTION_NOT_FOUND;
                    $result['status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case '2':
                    $result['reason_id'] = self::REASON_TRANSACTION_DENIED;
                    $result['status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
            }

        }

        $this->CI->utils->debug_log('processResultForqueryTransaction ===========>',$resultArr);
        return array($success, $result);
    }

    /**
     * [changePassword no api call, internal changes only]
     * @param  [string] $playerName  [player real username]
     * @param  [string] $oldPassword [old password]
     * @param  [string] $newPassword [new password]
     * @return [array]               [returns processed result of the api]
     */
    public function changePassword($playerName, $oldPassword = null, $newPassword) {
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $this->updatePasswordForPlayer($playerId, $newPassword);

        return ['success' => true];
    }

    public function batchQueryPlayerBalance($playerNames, $syncId = null) {

        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }

    /**
     * [depositToGame (request) deposit to api]
     * @param  [string] $playerName         [player real username]
     * @param  [int] $amount                [amount to be deposit]
     * @param  [string] $transfer_secure_id [unique transaction id that can be track on game provider side]
     * @return [array]         [returns processed result of the api]
     */
    public function depositToGame($playerName, $amount, $transfer_secure_id=null){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerID = $this->getExternalAccountIdByPlayerUsername($playerName);
        $time_str = $this->timestamp_str('YmdHis', '');
        $external_transaction_id  = $this->agent . $time_str . $gameUsername;

        $context = array(
            'callback_obj'   => $this,
            'callback_method'=> 'processResultForDepositToGame',
            'gameUsername'   => $gameUsername,
            'playerName'     => $playerName,
            'amount'         => $amount,
            'external_transaction_id' => $external_transaction_id
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's' => $this->URI_MAP[self::API_depositToGame],
            'account' => $gameUsername,
            'orderid' => $external_transaction_id,
            'money' => $this->dBtoGameAmount($amount),
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);
        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    /**
     * [processResultForDepositToGame callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForDepositToGame($params) {
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params,$playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        if ($success) {
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

            // if ($playerId) {
            //     if ($resultArr['d']['code'] == self::SUCCESS_API_CODE) {
            //         // Deposit
            //         $afterBalance = $resultArr['d']['money'];
            //         $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            //         $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
            //     }else{
            //         $result['reason_id'] = $this->getReasons($resultArr['d']['code']);
            //     }
            // } else {
            //     $this->CI->utils->debug_log('error', 'LE GAMING =============== cannot get player id from '.$playerName.' getPlayerIdInGameProviderAuth');
            //     $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            //     $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
            // }
            if ($resultArr['d']['code'] == self::SUCCESS_API_CODE) {
                $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            }else{
                $result['reason_id'] = $this->getReasons($resultArr['d']['code']);
            }
            $result['didnot_insert_game_logs']=true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
        }

        $this->CI->utils->debug_log('processResultForDepositToGame ===========>',$resultArr);
        return array($success, $result);

    }

    /**
     * [withdrawFromGame (request) withdraw from api]
     * @param  [string] $playerName         [player real username]
     * @param  [int] $amount                [amount to be withdraw]
     * @param  [string] $transfer_secure_id [unique transaction id that can be track on game provider side]
     * @return [array]         [returns processed result of the api]
     */
    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $playerID = $this->getExternalAccountIdByPlayerUsername($playerName);
        $time_str = $this->timestamp_str('YmdHis', '');
        $external_transaction_id  = $this->agent . $time_str . $gameUsername;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $external_transaction_id
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's' => $this->URI_MAP[self::API_withdrawFromGame],
            'account' => $gameUsername,
            'orderid' => $external_transaction_id,
            'money' => $this->dBtoGameAmount($amount),
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

    /**
     * [processResultForDepositToGame callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForWithdrawFromGame($params){
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params,$playerName);
        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
        );

        if ($success) {
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     if ($resultArr['d']['code'] == self::SUCCESS_API_CODE) {
            //         // Withdraw
            //         $afterBalance = $resultArr['d']['money'];
            //         $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            //         $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,$this->transTypeSubWalletToMainWallet());
            //     }else{
            //         $result['reason_id'] = $this->getReasons($resultArr['d']['code']);
            //     }
            // } else {
            //     $this->CI->utils->debug_log('error', 'RTG =============== cannot get player id from '.$playerName.' getPlayerIdInGameProviderAuth');
            //     $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            //     $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
            // }
            if ($resultArr['d']['code'] == self::SUCCESS_API_CODE) {
                $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            }else{
                $result['reason_id'] = $this->getReasons($resultArr['d']['code']);
            }
            $result['didnot_insert_game_logs']=true;
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
        }

        $this->CI->utils->debug_log('processResultForWithdrawFromGame ===========>',$resultArr);
        return array($success, $result);
    }

    private function getReasons($error_code){

        switch ($error_code) {
            case 38:
                return self::REASON_NO_ENOUGH_BALANCE;
                break;
            case 31:
                return self::REASON_INVALID_TRANSFER_AMOUNT;
                break;
            case 6:
                return self::REASON_INCOMPLETE_INFORMATION;
                break;
            case 15:
                return self::REASON_INVALID_KEY;
                break;
            case 33:
                return self::REASON_NOT_FOUND_PLAYER;
                break;
            case 5:
                return self::REASON_IP_NOT_AUTHORIZED;
                break;
            default:
                return self::REASON_UNKNOWN;
                break;
        }
    }

    /**
     * [getLauncherLanguage not applicable right now, this game supports chinese only]
     * @param  [type] $language [description]
     * @return [type]           [description]
     */
    public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
            case 1:
            case 'en-us':
                $lang = 1; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 2; // chinese
                break;
            case '3':
            case 'id-id':
                $lang = 1; // indonesia
                break;
            case '4':
            case 'vi-vn':
                $lang = 5; // vietnamese
                break;
            case '5':
            case 'ko-kr':
                $lang = 6; // korean
                break;
            default:
                $lang = 1; // default as english
                break;
        }
        return $lang;
    }

    /**
     * [queryForwardgame (request) get game launch url from api)]
     * @param  [string] $playerName [real player username]
     * @param  [array] $extra      [details needed for launching game]
     * @return [array]         [returns processed result of the api]
     */
    public function queryForwardGame($playerName, $extra = null) {
        if(array_key_exists('game_mode',$extra) && ($extra['game_mode'] == 'demo' || $extra['game_mode'] == 'trial' || $extra['game_mode'] == 'fun')){
            return array(
                'url' => $this->demo_url,
                'success' => true,
            );
        }
        # for gamegateway lobby url for mobile to redirect to client player center not gamegateway player center
        $t1_lobby ='';
        if (isset($extra['extra']['t1_lobby_url']) && !empty($extra['extra']['t1_lobby_url'])) {
            $t1_lobby = $extra['extra']['t1_lobby_url'];
        }

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryForwardgame',
            'playerName'      => $playerName,
            'gameUsername'    => $gameUsername,
            't1_lobby'    => $t1_lobby
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $time_str = $this->timestamp_str('YmdHis', '');
        $orderId = $this->agent . $time_str . $gameUsername;

        $processed_params = http_build_query([
            's'        => $this->URI_MAP[self::API_createPlayer],
            'account'  => $gameUsername,
            'money'    => 0,#money can be set to 0, when in game player will load automatic
            'orderid'  => $orderId,
            'ip'       => $this->CI->input->ip_address(),
            'lineCode' => $this->lineCode,
            'KindID'   => $extra['game_code'],
        ]);

        $this->CI->utils->debug_log('processed_params =====>',$processed_params);
        $params['param'] = $this->desEncode($this->live_key,$processed_params);
        return $this->callApi(self::API_queryForwardGame, $params, $context);
    }

    /**
     * [processResultForDepositToGame callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForQueryForwardgame($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params);
        $t1_lobby = $this->getVariableFromContext($params, 't1_lobby');

        $result['success'] = false;
        $result['reason'] = $this->getReasons($resultArr['d']['code']);
        if ( $success && ! empty($resultArr['d']['url']) ) {
            $result['url'] = !empty($t1_lobby)?($resultArr['d']['url'].'&jumpType=3&backUrl='.$t1_lobby):($resultArr['d']['url']);
            $result['success'] = true;
        };

        $this->CI->utils->debug_log('processResultForQueryForwardgame =====>',$resultArr);
        return array($success, $result);
    }

    /**
     * [logout (request) logout player(force logout)]
     * @param  [type] $playerName [description]
     * @param  [type] $password   [description]
     * @return [type]             [description]
     */
    public function logout($playerName, $password = null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForLogout',
            'playerName'      => $playerName,
            'gameUsername'    => $gameUsername
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $time_str = $this->timestamp_str('YmdHis', '');
        $orderId = $this->agent . $time_str . $gameUsername;

        $processed_params = http_build_query([
            's'        => $this->URI_MAP[self::API_logout],
            'account'  => $gameUsername,
        ]);

        $params['param'] = $this->desEncode($this->live_key,$processed_params);
        return $this->callApi(self::API_logout, $params, $context);
    }

    /**
     * [processResultForLogout callback - validate if api call is success]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForLogout($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params);

        $this->CI->utils->debug_log('processResultForLogout ===========>',$resultArr);
        return array($success, ['response_result_id' => $responseResultId]);
    }

    /**
     * [syncOriginalGameLogs (request) Get Game record from the api:
     *     Api requirements:
         *    1. Pull the data 3 minutes before the current time
         *    2. Recommend time range is 1 - 5 minutes, maximum time range is 60 minutes more than 60 minutes will get an error
         *    3. Time should be in timestamp with milliseconds
         *    3. No Api call limit
     * ]
     * @param  boolean $token [token]
     * @return [array]         [returns processed result of the api]
     */
    public function syncOriginalGameLogs($token = false) {

        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s') ));
        $startDate->modify($this->getDatetimeAdjust());

        //observer the date format
        $queryDateTimeStart = $startDate->format('Y-m-d H:i:s');
        $queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
        $queryDateTimeMax = $endDate->format('Y-m-d H:i:s');

        $result = [];
        while ($queryDateTimeMax  > $queryDateTimeStart) {
            $startDateParam=new DateTime($queryDateTimeStart);
            if($queryDateTimeEnd>$queryDateTimeMax){
                $endDateParam=new DateTime($queryDateTimeMax);
            }else{
                $endDateParam=new DateTime($queryDateTimeEnd);
            }

            $temp =$this->syncLeGamingGameLogs($startDateParam,$endDateParam);
            if ( ! $temp['success'] && $temp['data']['d']['code'] != 16) {
                $this->CI->utils->debug_log("LE gaming =======>sleeptime", $this->sync_sleep_time);
                sleep($this->sync_sleep_time);
                $this->CI->db->_reset_select();
                $this->CI->db->reconnect();
                $this->CI->db->initialize();
                continue;
            }
            $result[$startDateParam->format('Y-m-d H:i:s').' to '.$endDateParam->format('Y-m-d H:i:s')] = $temp;

            $queryDateTimeStart = $endDateParam->format('Y-m-d H:i:s');
            $queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
            $this->CI->utils->debug_log("LE gaming =======> result range", $startDateParam->format('Y-m-d H:i:s').' to '.$endDateParam->format('Y-m-d H:i:s'));
            $this->CI->utils->debug_log("LE gaming =======> result", json_encode($result[$startDateParam->format('Y-m-d H:i:s').' to '.$endDateParam->format('Y-m-d H:i:s')]));
            if($result[$startDateParam->format('Y-m-d H:i:s').' to '.$endDateParam->format('Y-m-d H:i:s')]['data_count'] > 0){
                $this->CI->utils->debug_log("LE gaming =======>sleeptime", $this->sync_sleep_time);
                sleep($this->sync_sleep_time);
                $this->CI->db->_reset_select();
                $this->CI->db->reconnect();
                $this->CI->db->initialize();
            }else{
                sleep($this->sync_sleep_time);
                $this->CI->db->_reset_select();
                $this->CI->db->reconnect();
                $this->CI->db->initialize();
            }
        }

        return array_merge(array("success"=>true),array("details"=>$result));
    }

    /**
     * [syncLeGamingGameLogs sync original game logs per hour]
     * @param  [date] $startDate [start date of request]
     * @param  [date] $endDate   [end date of request]
     * @return [array]            [description]
     */
    private function syncLeGamingGameLogs($startDate,$endDate){
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncOriginalGameLogs',
            'startDate' => $startDate,
            'endDate' => $endDate
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = [
            's'           => $this->URI_MAP[self::API_syncGameRecords],
            'startTime'   => $startDate->format("U") * 1000,
            'endTime'     => $endDate->format("U") * 1000,
        ];

        $params['param'] = $this->desEncode($this->live_key,http_build_query($processed_params));
        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    /**
     * [processResultForSyncOriginalGameLogs description]
     * @param  [array] $params [contains all details about the before api call and api call response]
     * @return [array]         [returns processed result of the api]
     */
    public function processResultForSyncOriginalGameLogs($params) {
        // $this->CI->load->model(array('le_gaming_game_logs'));
        $this->CI->load->model(array('original_game_logs_model'));
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params);

        $result = array('data_count'=>0);
        $gameRecords = !empty($resultArr['d']['list']) ? $resultArr['d']['list'] : array();

        if($success && !empty($gameRecords)){
            if (empty($gameRecords)) {
                return array($success, ['dataCount' => 0]);
            }
            #process api result
            foreach ($resultArr['d']['list'] as $field => $rows) {
                $tempArr = [];
                foreach ($rows as $key => $row) {
                    if ( ! empty($gameRecords[$key])) {
                        $gameRecords[$key][$field] = $row;
                    }else{
                        $tempArr[$key][$field] = $row;
                        $gameRecords = $tempArr;
                    }
                }
            }

            $extra = ['responseResultId'=>$responseResultId];
            $this->rebuildLeGamingGameRecords($gameRecords,$extra);
            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                'le_gaming_game_logs',
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_ORIGINAL,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS
            );

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
        } else {
            $result['data'] = $resultArr;
        }

        return array($success, $result);
    }

    public function rebuildLeGamingGameRecords(&$gameRecords,$extra){
        foreach($gameRecords as $index => $record) {
            $gameRecords[$index]['external_uniqueid'] = $record['GameID'];
            $gameRecords[$index]['AllBet'] = $this->gameAmountToDB($record['AllBet']);
            $gameRecords[$index]['Profit'] = $this->gameAmountToDB($record['Profit']);
            $gameRecords[$index]['CellScore'] = $this->gameAmountToDB($record['CellScore']);
            $gameRecords[$index]['Revenue'] = $this->gameAmountToDB($record['Revenue']);
            $gameRecords[$index]['player_username'] = str_replace($this->agent . "_", "", $record['Accounts']);
            $gameRecords[$index]['response_result_id'] = $extra['responseResultId'];
            $gameRecords[$index]['GameStartTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['GameStartTime'])));
            $gameRecords[$index]['GameEndTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['GameEndTime'])));
        }
    }

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){
        $dataCount=0;
        if(!empty($rows)){
            $responseResultId=$additionalInfo['responseResultId'];
            foreach ($rows as $record) {
                $record['last_sync_time'] = $this->utils->getNowForMysql();
                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal('le_gaming_game_logs', $record);
                }else{
                    $this->CI->original_game_logs_model->insertRowsToOriginal('le_gaming_game_logs', $record);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }

    /**
     * [syncMergeToGameLogs sync game logs in original to game_logs table]
     * @param  [string] $token [token]
     * @return [array]         [processed result]
     */
    public function syncMergeToGameLogs($token) {
        $this->unknownGame = $this->getUnknownGame($this->getPlatformCode());
        return $this->commonSyncMergeToGameLogs($token,
        $this,
        [$this, 'queryOriginalGameLogs'],
        [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
        [$this, 'preprocessOriginalRowForGameLogs'],
        false);
    }

    /**
     * [queryOriginalGameLogs get all available data for merging]
     * @param  [datetime] $dateFrom     [description]
     * @param  [datetime] $dateTo       [description]
     * @param  [datetime] $use_bet_time [use bet time or update time]
     * @return [array]               [game records]
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='`le_gaming_game_logs`.`GameEndTime` >= ?
          AND `le_gaming_game_logs`.`GameEndTime` <= ?';
        if($use_bet_time){
            $sqlTime='`le_gaming_game_logs`.`GameStartTime` >= ?
          AND `le_gaming_game_logs`.`GameStartTime` <= ?';
        }

        $sql = <<<EOD
            SELECT
              le_gaming_game_logs.id,
              le_gaming_game_logs.player_username as username,
              le_gaming_game_logs.external_uniqueid,
              le_gaming_game_logs.GameStartTime AS start_date,
              le_gaming_game_logs.GameEndTime AS end_date,
              le_gaming_game_logs.KindID AS game_code,
              le_gaming_game_logs.response_result_id,
              le_gaming_game_logs.Profit AS result_amount,
              le_gaming_game_logs.AllBet AS real_bet_amount,
              le_gaming_game_logs.CellScore AS valid_bet_amount,
              le_gaming_game_logs.Revenue AS revenue,
              le_gaming_game_logs.md5_sum,
              game_description.id AS game_description_id,
              game_description.game_type_id,
              game_provider_auth.player_id AS player_id
            FROM
              le_gaming_game_logs
              LEFT JOIN game_description
                ON (
                  le_gaming_game_logs.KindID = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `le_gaming_game_logs`.`player_username` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE
                {$sqlTime}

EOD;

        $params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            $dateFrom,
            $dateTo,
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

    /**
     * [makeParamsForInsertOrUpdateGameLogsRow map data]
     * @param  array  $row [row]
     * @return [array]     [map data]
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
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
                'bet_amount' => $row['valid_bet_amount'],
                'result_amount' => $row['result_amount'],
                'bet_for_cashback' => $row['valid_bet_amount'],
                'real_betting_amount' => $row['real_bet_amount'],
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => null
            ],
            'date_info' => [
                'start_at' => $row['end_date'],
                'end_at' => $row['end_date'],
                'bet_at' => $row['start_date'],
                'updated_at' => $row['end_date']
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => true,
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['external_uniqueid'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => $row['response_result_id'],
                'sync_index' => $row['id'],
                'bet_type' => null
            ],
            'bet_details' => ['Created At' => $this->CI->utils->getNowForMysql()],
            'extra' => [
                'rent' => $row['revenue']
            ],
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
        ];
    }

    /**
     * [preprocessOriginalRowForGameLogs game details checking]
     * @param  array  &$row [row]
     * @return [array]      [overwrite $row]
     */
    public function preprocessOriginalRowForGameLogs(array &$row){
        if (empty($row['game_description_id'])) {
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$this->unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }
    }

    /**
     * [getGameDescriptionInfo create game if not exist under unknown game_type]
     * @param  [object] $row         [specific game logs row]
     * @param  [object] $unknownGame [description]
     * @return [array]              [returns game_type_id, game_description_id]
     */
    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;

        $external_game_id = $row['game_code'];
        $extra = array('game_code' => $external_game_id,'game_name' => $row['game_code']);
        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $external_game_id, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

    /** 
    *  The api will return the bet details URL link for viewing the details
    */
    public function queryBetDetailLink($playerUsername, $game_id = NULL, $extra = NULL)
    {
        $record_id = 0;
        $url = '';
        $success = false;
        $bet_details = $this->queryGameLogsDetailsByGameID($game_id);

        // Record not found
        if (empty($bet_details)) {
            return array($success);
        }

        $record_details = $this->queryGameRecordID($playerUsername, $game_id, $bet_details);
        if (!empty($record_details) && $record_details['success']) {

            // Will check the array key number of the given game id for getting record_id on RecordID list
            $record_count   = $record_details['d']['count'];
            $game_ids = $record_details['d']['list']['GameID'];
            for ($i=0; $i <= $record_count - 1 ; $i++) {
                if ($game_id === $game_ids[$i]) {
                    $record_id = $i;
                }
            }
            $record_id = $record_details['d']['list']['RecordID'][$record_id];
            $url_response = $this->queryGameDetailsURL($bet_details, $record_id);
            if (!empty($url_response) && $url_response['success']) {
                $success = true;
                $url = $url_response['d']['gameLogURL'];
            }
        }
        // return $this->callApi(self::API_queryBetDetailLink, $params, $context);
        return array($success, 'url' => $url);
    }

    /** 
    *  The api will return the GameRecords together with recordID that will use on getting bet detail URL on the given timestamp from GameID bet dates
    */
    public function queryGameRecordID($playerUsername, $game_id = null, $bet_details = null)
    {        
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);
        $playerId = $this->getPlayerIdInPlayer($playerUsername);

        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryGameRecordID',
            'gameUsername'    => $gameUsername,
            'extra'           => $bet_details
        );

        $GameStartTime = new DateTime($bet_details[0]['GameStartTime']);
        $GameEndTime = new DateTime($bet_details[0]['GameEndTime']);

        $GameStartTime = new DateTime($this->serverTimeToGameTime($GameStartTime->format('Y-m-d H:i:s')));
        $GameEndTime   = new DateTime($this->serverTimeToGameTime($GameEndTime->format('Y-m-d H:i:s')));

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's'         => $this->URI_MAP[self::API_queryGameRecordsByDate],
            'startTime' => $GameStartTime->format('U') * 1000,
            'endTime'   => $GameEndTime->format('U') * 1000
        ]);

        $this->CI->utils->debug_log('preprocess_params =====>', $params,'processed_params =====>',$processed_params);
        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_queryGameRecordsByDate, $params, $context);
    }

    /** 
     * Process Result of queryBetDetailLink method
    */
    public function processResultForQueryGameRecordID($params)
    {
        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params, $statusCode);
        $this->CI->utils->debug_log('---------- LE GAMING processResultForQueryGameRecordID response ----------', $resultArr);

        return array($success, $resultArr);
    }

    /** 
    *  The api will return the bet detail URL
    */
    public function queryGameDetailsURL($game_logs_details, $recordID)
    {
        $context = array(
            'callback_obj'    => $this,
            'callback_method' => 'processResultForQueryGameDetailsURL',
            'extra'           => $game_logs_details
        );

        $params = [
            'agent'     => $this->agent,
            'timestamp' => $this->microtime_int(),
            'key'       => md5($this->agent . $this->microtime_int() . $this->live_secret),
        ];

        $processed_params = http_build_query([
            's'          => $this->URI_MAP[self::API_queryBetDetailLink],
            'gameuserno' => $game_logs_details['0']['GameID'],
            'id'         => $recordID,
            'account'    => $game_logs_details['0']['Accounts'],
            'serverID'   => $game_logs_details['0']['ServerID']
        ]);

        $this->CI->utils->debug_log('preprocess_params =====>', $params,'processed_params =====>',$processed_params);
        $params['param'] = $this->desEncode($this->live_key,$processed_params);

        return $this->callApi(self::API_queryBetDetailLink, $params, $context);
    }

    /** 
     * Process Result of queryGameDetailsURL method
    */
    public function processResultForQueryGameDetailsURL($params)
    {
        $statusCode = $this->getStatusCodeFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $params, $statusCode);
        $this->CI->utils->debug_log('---------- LE GAMING processResultForQueryGameDetailsURL response ----------', $resultArr);

        return array($success, $resultArr);
    }


    /**
     * get game logs detail using GameId
     * @param string $queryGameLogsDetailsByGameID unique identifier for logs
     * @return array
     */
    private function queryGameLogsDetailsByGameID($game_id) {
        $this->CI->load->model(array('original_game_logs_model'));
        $sql = <<<EOD
            SELECT GameID, GameStartTime, GameEndTime, Accounts, ServerID, KindID, TableID, ChairID, ChannelID, LineCode, player_username
            FROM
            le_gaming_game_logs
            WHERE
                GameID = ?
EOD;

        $params=[
            $game_id,
        ];
        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }


    public function syncPlayerAccount($playerName, $password, $playerId) {
        return $this->returnUnimplemented();
    }

    public function queryPlayerInfo($playerName) {
        return $this->returnUnimplemented();
    }

    public function updatePlayerInfo($playerName, $infos) {
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

    public function blockPlayer($playerName) {
        return $this->returnUnimplemented();
    }

    public function login($playerName, $password = null, $extra = null) {
        return $this->returnUnimplemented();
    }

    public function unblockPlayer($playerName) {
        return $this->returnUnimplemented();
    }

    #==================================================================== For this API purposes only ==================================================================
    /**
     * [timestamp_str set time zone]
     * @param  [string] $format     [time format]
     * @param  [string] $timezone   [timezone]
     * @return [date]               [return formated date]
     */
    private function timestamp_str($format, $timezone = null)
    {
        $micro_date = microtime();
        $date_array = explode(" ",$micro_date);
        // $this->set_timezone($timezone);
        return date($format,$date_array[1]);
    }

    /**
     * [set_timezone set time zone]
     * @param [string] $default [timezone]
     */
    private function set_timezone($default)
    {
        $timezone = "";

        // On many systems (Mac, for instance) "/etc/localtime" is a symlink
        // to the file with the timezone info
        if (is_link("/etc/localtime")) {

            // If it is, that file's name is actually the "Olsen" format timezone
            $filename = readlink("/etc/localtime");

            $pos = strpos($filename, "zoneinfo");
            if ($pos) {
                // When it is, it's in the "/usr/share/zoneinfo/" folder
                $timezone = substr($filename, $pos + strlen("zoneinfo/"));
            } else {
                // If not, bail
                $timezone = $default;
            }
        }
        else {
            // On other systems, like Ubuntu, there's file with the Olsen time
            // right inside it.
            $timezone = file_get_contents("/etc/timezone");
            if (!strlen($timezone)) {
                $timezone = $default;
            }
        }

        $this->CI->utils->debug_log("LE gaming =======>set_timezone", $timezone);
        $timezone = trim($timezone);
        // if ($timezone == 'Etc/UTC\n' || $timezone == 'Etc/UTC') {
        //     $timezone = "UTC";
        // }

        $this->CI->utils->debug_log("LE gaming =======>set_timezone TRIMED", $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * [desEncode encode parameters before sending request to api]
     * @param  [string] $key [DES KEY]
     * @param  [string] $str [url encoded params]
     * @return [string]      [returns encoded params]
     */
    private function desEncode($key, $str)
    {
        $str = $this->pkcs5_pad(trim($str), 16);
        $encrypt_str = openssl_encrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        return base64_encode($encrypt_str);
    }

    /**
     * [desDecode didn't used, just added in future purposes]
     * @param  [string] $key [DES KEY]
     * @param  [string] $str [encoded params]
     * @return [string]      [returns url encoded params]
     */
    private function desDecode($key, $str)
    {
        $str = base64_decode($str);
        $decrypt_str = openssl_decrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        return trim($this->pkcs5_unpad($decrypt_str));
    }

    private function mcrypt_desEncode($encryptKey, $str)
    {
        $str = trim($str);
        $blocksize = @mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $str = pkcs7_pad($str, $blocksize);
        $iv = @mcrypt_create_iv(@mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $encrypt_str = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptKey, $str, MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypt_str);
    }

    private function mcrypt_desDecode($encryptKey, $str)
    {
        $str = base64_decode($str);
        $iv = @mcrypt_create_iv(@mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypt_str = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryptKey, $str, MCRYPT_MODE_ECB, $iv);
        return pkcs7_unpad(trim($decrypt_str));
    }

    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    private function microtime_int()
    {
        return (int)(microtime(true) * 1000);
    }

    #==================================================================== end ==================================================================
}

/*end of file*/