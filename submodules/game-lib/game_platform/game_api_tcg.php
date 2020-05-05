<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_tcg extends Abstract_game_api {

    private $url;

    const URI_MAP = [
        self::API_generateToken => '/oauth/token',
        self::API_createPlayer => '/v1/account/member',
        self::API_depositToGame => '/v1/transaction',
        self::API_withdrawFromGame => '/v1/transaction',
        self::API_changePassword => '/v1/account/member',        
    ];

    const START_PAGE=1;

    const STATUS_SUCCESS = 0;
    const PRODUCT_TYPE_LOTTO = 2; // LOTTO
    const TRANSACTION_STATUS = 'SUCCESS';
    const DEFAULT_PLATFORM = 'web';

    const FUND_DEPOSIT = 1;
    const FUND_WITHDRAW = 2;

    const GAME_METHOD_SETTLED = 'elsbd';
    const GAME_METHOD_UNSETTLED = 'elubd';

    const MINIMUM_USERNAME_LENGTH = 4;
    const MAXIMUM_USERNAME_LENGTH = 14;

    const MAXIMUM_PASSWORD_LENGTH = 12;

    const ORIGINAL_LOGS_TABLE_NAME = 'tcg_game_logs';

    const MD5_FIELDS_FOR_ORIGINAL=[
        'bet_amount',
        'game_code',
        'bet_order_no',
        'bet_time',
        'trans_time',
        'bet_content_id',
        'win_amount',
        'net_pnl',
        'bet_status',
        'settlement_time',
        'username',
    ];
    const MD5_FLOAT_AMOUNT_FIELDS=[
        'bet_amount',
        'net_pnl',
        'win_amount'
    ];
    const MD5_FIELDS_FOR_MERGE=[
        'bet_amount',
        'game_code',
        'bet_order_no',
        'bet_time',
        'trans_time',
        'bet_content_id',
        'win_amount',
        'net_pnl',
        'bet_status',
        'settlement_time',
        'username',
        'bet_details'
    ];
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=[
        'bet_amount',
        'net_pnl',
    ];

    public function __construct() {
        parent::__construct();
        $this->url = $this->getSystemInfo('url');
        $this->merchant_code = $this->getSystemInfo('merchant_code');
        $this->des_key = $this->getSystemInfo('des_key');
        $this->sign_key = $this->getSystemInfo('sign_key');
        $this->currency = $this->getSystemInfo('currency', 'CNY');
        $this->game_mode = $this->getSystemInfo('game_mode', 1);
        $this->prefix_for_username = $this->getSystemInfo('prefix_for_username');

        $this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+15 minutes');

        # fix exceed game username length
        $this->fix_username_limit = $this->getSystemInfo('fix_username_limit', true);
        $this->minimum_user_length = $this->getSystemInfo('minimum_user_length', 4);
        $this->maximum_user_length = $this->getSystemInfo('maximum_user_length', 14);
        $this->default_fix_name_length = $this->getSystemInfo('default_fix_name_length', 8);

        $this->game_api_url = array(
            'lotto.ole888.net',
            'lotto.ole788.net',
            'lotto.gol881.com',
            'lotto.gol888.net',
            'lotto.haoli178.com',
            'lotto.haoli178.net'
        );
        $this->default_game_url = $this->getSystemInfo('default_game_url', 'http://lotto.ole888.net');

        #game draw result
        $this->gdr_default_limit = $this->getSystemInfo('gdr_default_limit', 100);
        $this->gdr_default_page = $this->getSystemInfo('gdr_default_page', 10);
        $this->allow_sync_game_draw_results = $this->getSystemInfo('allow_sync_game_draw_results',false);
    }

    public function getPlatformCode() {
        return TCG_API;
    }

    public function generateUrl($apiName, $params) {
        return $this->url;
    }

    protected function getHttpHeaders($params) {
        return array("Content-Type" => "application/x-www-form-urlencoded");
    }

    protected function customHttpCall($ch, $params) {
        $encrypt_params = $this->encryptText(json_encode($params),$this->des_key);
        $sign = hash('sha256', $encrypt_params . $this->sign_key);
        $data = array('merchant_code' => $this->merchant_code, 'params' => $encrypt_params , 'sign' => $sign);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        return $errCode || intval($statusCode, 10) >= 501;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {

        $password =  random_string('alnum', self::MAXIMUM_PASSWORD_LENGTH);

        $extra = [
            'prefix' => $this->prefix_for_username,

            # fix exceed game length name
            'fix_username_limit' => $this->fix_username_limit,
            'minimum_user_length' => $this->minimum_user_length,
            'maximum_user_length' => $this->maximum_user_length,
            'default_fix_name_length' => $this->default_fix_name_length,
        ];

        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerName' => $playerName,
            'playerId' => $playerId,
        );

        $params = array(
            'username' =>  $gameUsername,
            'password' => $password,
            'currency' => $this->currency,
            'method' => 'cm',
        );

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $playerId = $this->getVariableFromContext($params, 'playerId');

        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
        if ($success) {
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        }

        return array($success, null);
    }

    public function processResultBoolean($responseResultId, $resultJson, $playerName = null) {
        $success = false;
        if (isset($resultJson['status'])) {
            if ($resultJson['status'] == self::STATUS_SUCCESS) {
                $success = true;
            } else {
                $this->CI->utils->debug_log('TCG got error', $responseResultId, 'playerName', $playerName, 'result', $resultJson);
            }
        }
        return $success;
    }

    public function queryPlayerBalance($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'username' => $gameUsername,
            'method' => 'gb',
            'product_type' => self::PRODUCT_TYPE_LOTTO
        );

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    private function round_down($number, $precision = 2){
        $fig = (int) str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }

    // just get 2 decimal places ( no round up )
    public function gameAmountBalance($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount / $conversion_rate);
        return $this->round_down($value,3);
    }

    public function processResultForQueryPlayerBalance($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

        $result = array();
        if ($success) {
            $result['balance'] =  $this->gameAmountBalance($resultJsonArr['balance']);
        }
        return array($success, $result);
    }

    public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        if (empty($transfer_secure_id)) {
            $transfer_secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        }

        $amount = $this->dBtoGameAmount($amount);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $transfer_secure_id,
            'amount' => $amount
        );

        $params = array(
            'username' => $gameUsername,
            'method' => 'ft',
            'product_type' => self::PRODUCT_TYPE_LOTTO,
            'fund_type' => self::FUND_DEPOSIT,
            'amount' => $amount,
            'reference_no' => $transfer_secure_id,
        );

        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    public function processResultForDepositToGame($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $amount = $this->getVariableFromContext($params, 'amount');
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if ($success) {
            // $playerBalance = $this->queryPlayerBalance($playerName);
            // $afterBalance = $playerBalance['balance'];

            // $result['current_player_balance'] = $playerBalance['balance'];

            // $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
            // if ($playerId) {
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeMainWalletToSubWallet());
            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            // }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $error_code = @$resultJsonArr['status'];

            switch($error_code) {
                case '1' :
                    $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                    break;
                case '2' :
                    $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
                    break;
                case '3' :
                    $result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
                    break;
                case '7' :
                    $result['reason_id']=self::REASON_INVALID_KEY;
                    break;
                case '8' :
                    $result['reason_id']=self::REASON_CURRENCY_ERROR;
                    break;
                case '9' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                    break;
                case '11' :
                    $result['reason_id']=self::REASON_NO_ENOUGH_CREDIT_IN_SYSTEM;
                    break;
                case '12' :
                    $result['reason_id']=self::REASON_DUPLICATE_TRANSFER;
                    break;
                case '15' :
                    $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
                    break;
            }
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);
    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null,$notRecordTransaction=false) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        if (empty($transfer_secure_id)) {
            $transfer_secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        }

        $amount = $this->dBtoGameAmount($amount);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $transfer_secure_id,
            'amount' => $amount
        );

        $params = array(
            'username' => $gameUsername,
            'method' => 'ft',
            'product_type' => self::PRODUCT_TYPE_LOTTO,
            'fund_type' => self::FUND_WITHDRAW,
            'amount' => $amount,
            'reference_no' => $transfer_secure_id,
        );

        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    public function processResultForWithdrawFromGame($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $amount = $this->getVariableFromContext($params, 'amount');
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if ($success) {
            // $playerBalance = $this->queryPlayerBalance($playerName);
            // $afterBalance = $playerBalance['balance'];

            // $result['current_player_balance'] = $playerBalance['balance'];

            // $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
            // if ($playerId) {
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeSubWalletToMainWallet());
            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            // }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $error_code = @$resultJsonArr['status'];
            switch($error_code) {
                case '1' :
                    $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                    break;
                case '2' :
                    $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
                    break;
                case '3' :
                    $result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
                    break;
                case '7' :
                    $result['reason_id']=self::REASON_INVALID_KEY;
                    break;
                case '8' :
                    $result['reason_id']=self::REASON_CURRENCY_ERROR;
                    break;
                case '9' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                    break;
                case '11' :
                    $result['reason_id']=self::REASON_NO_ENOUGH_CREDIT_IN_SYSTEM;
                    break;
                case '12' :
                    $result['reason_id']=self::REASON_DUPLICATE_TRANSFER;
                    break;
                case '15' :
                    $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
                    break;
            }
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);
    }

    public function queryTransaction($transactionId, $extra) {
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
            'ref_no' => $transactionId,
            'product_type' => self::PRODUCT_TYPE_LOTTO,
            'method' => 'cs',
        );
        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params){
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        if($resultJsonArr['transaction_status'] == 'SUCCESS') {
            $success = true;
            $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {
            $success = false;
            $error_code = @$resultJsonArr['transaction_status'];
            switch($error_code) {
                case 'NOT FOUND' :
                    $result['reason_id'] = self::REASON_INVALID_TRANSACTION_ID;
                    break;
            }
            $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);
    }

    public function isPlayerExist($playerName) {
        /*$query_bal = $this->queryPlayerBalance($playerName);
        $playerId = $this->getPlayerIdInGameProviderAuth($playerName);

        if (isset($query_bal['balance'])) {
            $success = false;
            $result['exists'] = true;
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        } else {
            $success = true;
            $result['exists'] = false;
        }
        return array('success' => $success, 'exists' => $result['exists']);*/
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'username' => $gameUsername,
            'method' => 'gb',
            'product_type' => self::PRODUCT_TYPE_LOTTO//will use querybalance as method to check if player exist
        );

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

        $result = array('exists' => false, 'response_result_id'=>$responseResultId);
        if ($success) {
            $result['exists'] = true;
        }

        return array($success, $result);
    }

    public function queryForwardGame($playerName, $extra = null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryForwardGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
        );

        $params = array(
            'username'=> $gameUsername,
            'method'=> 'lg',
            'game_mode'=> $this->game_mode,
            'product_type'=> self::PRODUCT_TYPE_LOTTO,
        );

        $platform = isset($extra['platform']) ? $extra['platform'] : self::DEFAULT_PLATFORM;

        if (strtolower($platform) == 'web' ) {
            $params['game_code'] = 'Lobby';
            $params['view'] = 'Lobby';
            $params['lottery_bet_mode'] = 'Elott_Traditional';
            $params['platform'] = 'flash';
        } else {
            $params['game_code'] = 'game_list';
            $params['view'] = 'game_list';
            $params['lottery_bet_mode'] = 'Elott_Traditional_Mobile_V2';
            $params['platform'] = 'html5';
        }

        return $this->callApi(self::API_queryForwardGame, $params, $context);
    }

    public function processResultForQueryForwardGame($params){
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = false;
        $result = array();
        if (isset($resultJsonArr['game_url'])) {
            $success = true;
            $result['url'] = $this->default_game_url.'/'.$resultJsonArr['game_url'];
        }
        return array($success, $result);
    }

    public function syncOriginalGameLogs($token = false) {
        // elubd if unsettled,  elsbd if settled
        $gameLogsMethod = [self::GAME_METHOD_UNSETTLED]; //, self::GAME_METHOD_SETTLED); # , self::GAME_METHOD_SETTLED
    
        $ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

        if ($ignore_public_sync == true) {
            $this->CI->utils->debug_log('ignore unsettled'); // ignore public sync
            $gameLogsMethod=[];
        }

        $cnt = 0;
        $real_count=0;
        $success = true;
        $queryDateTimeStart=null;
        $queryDateTimeEnd=null;
        $queryDateTimeMax=null;

        foreach($gameLogsMethod as $method) {

            $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
            $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

            $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
            $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
            $startDate->modify($this->getDatetimeAdjust());

            $minute = $startDate->format('i');
            if($method==self::GAME_METHOD_UNSETTLED){
                /**
                 * only for unsettle
                 * update base on api rules (00 15 35 45) 15mins interval
                 *
                 * 201808010000 - from 2018-08-01 00:00:00 to 2018-08-01 00:14:59
                 * 201808010015 - from 2018-08-01 00:15:00 to 2018-08-01 00:29:59
                 * 201808010030 - from 2018-08-01 00:30:00 to 2018-08-01 00:44:59
                 * 201808010045 - from 2018-08-01 00:45:00 to 2018-08-01 00:59:59
                 */
                if($minute >= '00' && $minute <= '14') {
                    $minute = '00';
                } elseif ($minute >= '15' && $minute <= '29') {
                    $minute = '15';
                } elseif ($minute >= '30' && $minute <= '44') {
                    $minute = '30';
                } elseif ($minute >= '45' && $minute <= '59') {
                    $minute = '45';
                }
            }else{
                //5 minutes
                if($minute >= '00' && $minute <= '04') {
                    $minute = '00';
                } elseif ($minute >= '05' && $minute <= '09') {
                    $minute = '05';
                } elseif ($minute >= '10' && $minute <= '14') {
                    $minute = '10';
                } elseif ($minute >= '15' && $minute <= '19') {
                    $minute = '15';
                } elseif ($minute >= '20' && $minute <= '24') {
                    $minute = '20';
                } elseif ($minute >= '25' && $minute <= '29') {
                    $minute = '25';
                } elseif ($minute >= '30' && $minute <= '34') {
                    $minute = '30';
                } elseif ($minute >= '35' && $minute <= '39') {
                    $minute = '35';
                } elseif ($minute >= '40' && $minute <= '44') {
                    $minute = '40';
                } elseif ($minute >= '45' && $minute <= '49') {
                    $minute = '45';
                } elseif ($minute >= '50' && $minute <= '54') {
                    $minute = '50';
                } elseif ($minute >= '55' && $minute <= '59') {
                    $minute = '55';
                }
            }

            $queryDateTimeStart = $startDate->format("Y-m-d H:$minute:s");
            $queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format("Y-m-d H:$minute:s");
            $queryDateTimeMax = $endDate->format('Y-m-d H:i:s');

            // $page = 1;
            while ($queryDateTimeMax  > $queryDateTimeStart) {

                $startDateParam=new DateTime($queryDateTimeStart);
                if($queryDateTimeEnd>$queryDateTimeMax){
                    $endDateParam=new DateTime($queryDateTimeMax);
                }else{
                    $endDateParam=new DateTime($queryDateTimeEnd);
                }
                $startDateParam = $startDateParam->format('YmdHi');
                $endDateParam = $endDateParam->format('Y-m-d H:i:s');

                $done = false;
                $currentPage = self::START_PAGE;

                while (!$done) {

                    $context = array(
                        'callback_obj' => $this,
                        'callback_method' => 'processResultForSyncGameRecords',
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                    );
                    $params = array(
                        'method' => $method,
                        'batch_name' => $startDateParam,
                        'page' => $currentPage,
                    );

                    $rlt = $this->callApi(self::API_syncGameRecords, $params, $context);
                    if(!$rlt['success'] && $rlt['error_status']==23){
                        //busy status, sleep and retry
                        $this->CI->utils->debug_log('api busy, sleep and retry', $rlt, $params);
                        sleep($this->common_wait_seconds);
                        continue;
                    }
                    $done = true;
                    if ($rlt) {
                        $success = $rlt['success'];
                    }else{
                        $success=false;
                    }
                    if ($rlt && $rlt['success']) {
                        $currentPage = $rlt['currentPage'];
                        $totalPages = $rlt['totalPages'];
                        //next page
                        $currentPage += 1;

                        $done = $currentPage > $totalPages;
                        $cnt += $rlt['totalCount'];
                        $real_count+=$rlt['data_count'];
                        $this->CI->utils->debug_log('currentPage', $currentPage, 'totalPages', $totalPages, 'done', $done, 'result', $rlt);
                    }else{
                        $this->CI->utils->error_log('found error', $rlt);
                    }
                    if(!$done){
                        sleep($this->common_wait_seconds);
                    }
                }
                if(!$success){
                    $this->CI->utils->error_log('found error so quit', $success);
                    break;
                }

                $queryDateTimeStart = $endDateParam;
                $queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format("Y-m-d H:i:s");
                if($queryDateTimeMax  > $queryDateTimeStart){
                    sleep($this->common_wait_seconds);
                }
            }
        }
        $this->CI->utils->debug_log('queryDateTimeStart', $queryDateTimeStart, 'queryDateTimeEnd', $queryDateTimeEnd,
            'queryDateTimeMax', $queryDateTimeMax, 'count', $cnt, 'real_count', $real_count);

        //for settled
        //load file
        $rlt=$this->syncOriginalFromFile($token);
        $success=$rlt['success'];
        $cnt+=$rlt['data_count'];

        return array("success"=>$success, 'count'=>$cnt, 'real_count'=>$real_count);
    }

    public function processGameRecords(&$gameRecords,$responseResultId) {
        $preResult = array();
        $gameRecords = $this->filterDuplicateInRecords($gameRecords,"betOrderNo");
        foreach ($gameRecords as $index => $record) {
            $player_id = $this->getPlayerIdInGameProviderAuth($record['username']);

            $bet_order_no = isset($record['betOrderNo']) ? $record['betOrderNo'] : null;
            $bet_content_id = isset($record['betContentId']) ? $record['betContentId'] : null;

            $preResult[$index]['bet_amount'] = isset($record['betAmount']) ?  $this->gameAmountToDB($record['betAmount']) : null;
            $preResult[$index]['game_code'] = isset($record['gameCode']) ? $record['gameCode'] : null;
            $preResult[$index]['bet_order_no'] = $bet_order_no;
            $preResult[$index]['bet_time'] = isset($record['betTime']) ? $record['betTime'] : null;
            $preResult[$index]['trans_time'] = isset($record['transTime']) ? $record['transTime'] : null;
            $preResult[$index]['bet_content_id'] = $bet_content_id;
            $preResult[$index]['play_code'] = isset($record['playCode']) ? $record['playCode'] : null;
            $preResult[$index]['order_num'] = isset($record['orderNum']) ? $record['orderNum'] : null;
            $preResult[$index]['chase'] = isset($record['chase']) ? $record['chase'] : null;
            $preResult[$index]['numero'] = isset($record['numero']) ? $record['numero'] : null;
            $preResult[$index]['win_amount'] = isset($record['winAmount']) ? $record['winAmount'] : 0;
            $preResult[$index]['net_pnl'] = isset($record['netPNL']) ? $this->gameAmountToDB($record['netPNL']): 0;
            $preResult[$index]['bet_status'] = isset($record['betStatus']) ? $record['betStatus'] : null;

            $preResult[$index]['bettingContent'] = isset($record['bettingContent']) ? $record['bettingContent'] : null;

            // add bet_time in settlement_time in bet_time
            $preResult[$index]['settlement_time'] = isset($record['settlementTime']) ? $record['settlementTime'] : $preResult[$index]['bet_time'];

            $preResult[$index]['username'] = isset($record['username']) ? $record['username'] : null;
            if(isset($record['productType'])){
                if(is_numeric($record['productType'])){
                    $preResult[$index]['product_type'] = $record['productType'];
                }else{
                    $preResult[$index]['product_type'] = 2;
                }
            }

            $preResult[$index]['last_updated_time'] = $this->CI->utils->getNowForMysql();
            $preResult[$index]['md5_sum'] = md5($record['betAmount'].$record['transTime'].$record['betTime']);
            $preResult[$index]['round_key'] = $bet_order_no.'-'.$bet_content_id; # bet_order_no + bet_content_id

            //extra info from SBE
            $preResult[$index]['player_id'] = $player_id ? $player_id : 0;
            $preResult[$index]['external_uniqueid'] = $bet_order_no.'-'.$bet_content_id;
            $preResult[$index]['response_result_id'] = $responseResultId;
        }
        $gameRecords = $preResult;
    }

    public function gameAmountToDB($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount / $conversion_rate);
        return round($value, 3); # OGP-13350
    }

    private function filterDuplicateInRecords($gameRec, $keyToFind) {
        $filteredArr = [];
        $keyArr = [];
        $i = 0;
        foreach($gameRec as $rec) {
            if (!in_array($rec[$keyToFind], $keyArr)) {
                $keyArr[$i] = $rec[$keyToFind];
                $filteredArr[$i] = $rec;
            }
            $i++;
        }
        unset($keyArr);
        return $filteredArr;
    }

    private function updateOrInsertOriginalGameLogs($data, $queryType){
        $dataCount=0;
        if(!empty($data)){
            foreach ($data as $record) {
                if ($queryType == 'update') {
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

    public function processResultForSyncGameRecords($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        // $resultJsonArr = json_decode($resultText,TRUE);

        $this->CI->load->model(array('tcg_game_logs','original_game_logs_model','external_system'));
        $this->CI->utils->debug_log('TCG game result', count($resultJsonArr));

        $result = ['data_count'=>0, 'totalPages'=>null, 'currentPage'=>null, 'totalCount'=>null];
        $success = !empty($resultJsonArr['page_info']) && isset($resultJsonArr['details']);
        if ($success) {
            $gameRecords = $resultJsonArr['details'];
            if(!empty($gameRecords)){
                $this->processGameRecords($gameRecords,$responseResultId);
                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                    self::ORIGINAL_LOGS_TABLE_NAME,		# original table logs
                    $gameRecords,						# api record (format array)
                    'external_uniqueid',				# unique field in api
                    'external_uniqueid',				# unique field in tcg_game_logs table
                    self::MD5_FIELDS_FOR_ORIGINAL,
                    'md5_sum',
                    'id',
                    self::MD5_FLOAT_AMOUNT_FIELDS
                );
                $this->CI->utils->debug_log('after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));

                if (!empty($insertRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
                }
                unset($insertRows);

                if (!empty($updateRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
                }
                unset($updateRows);
            }
            $result['totalPages'] = @$resultJsonArr['page_info']['totalPage'];
            $result['currentPage'] = @$resultJsonArr['page_info']['currentPage'];
            $result['totalCount'] = @$resultJsonArr['page_info']['totalCount'];
        }else{
            $result['error_status']=@$resultJsonArr['status'];
            $this->CI->utils->error_log('call api failed', $resultJsonArr);
        }
        unset($resultJsonArr);

        return array($success, $result);
    }

    public function syncOriginalFromFile($token) {

        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        // $syncId = parent::getValueFromSyncInfo($token, 'syncId');
        $startDate=new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $endDate=new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));
        $startDate->modify($this->getDatetimeAdjust());

        $result = ['success'=>true, 'data_count'=>0];
        $this->CI->load->model(array('tcg_game_logs','original_game_logs_model','external_system'));

        $logDirectory=rtrim($this->getSystemInfo('game_records_path'), '/');
        $fileArr=scandir($logDirectory);
        if(!empty($fileArr)){
            foreach ($fileArr as $file) {
                if($file=='.' || $file=='..'){
                    continue;
                }
                $dateDir=$logDirectory.'/'.$file;
                if(!is_dir($dateDir)){
                    $this->CI->utils->debug_log('ignore file '.$file.' on '.$logDirectory);
                    continue;
                }
                if(!$this->isDirInThisDay($file, $startDate, $endDate)){
                    continue;
                }
                $responseResultId=null;

                $jsonFileArr=scandir($dateDir);
                if(!empty($jsonFileArr)){
                    $this->CI->utils->debug_log('process '.$dateDir, count($jsonFileArr));
                    foreach ($jsonFileArr as $f) {
                        $jsonFile=$dateDir.'/'.$f;
                        if(!$this->isFileInThisTime($f, $startDate, $endDate)){
                            continue;
                        }

                        $this->CI->utils->debug_log('=======jsonFile: '.$jsonFile);
                        $gameRecords=json_decode(file_get_contents($jsonFile), true);

                        if(array_key_exists('list', $gameRecords)) { //check new content format they passed - OGP-15287
                            $gameRecords=$gameRecords['list'];
                        }

                        if(!empty($gameRecords)){
                            $this->processGameRecords($gameRecords, $responseResultId);
                            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                                self::ORIGINAL_LOGS_TABLE_NAME,     # original table logs
                                $gameRecords,                       # api record (format array)
                                'external_uniqueid',                # unique field in api
                                'external_uniqueid',                # unique field in tcg_game_logs table
                                self::MD5_FIELDS_FOR_ORIGINAL,
                                'md5_sum',
                                'id',
                                self::MD5_FLOAT_AMOUNT_FIELDS
                            );
                            $this->CI->utils->debug_log('after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));

                            if (!empty($insertRows)) {
                                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
                            }
                            unset($insertRows);

                            if (!empty($updateRows)) {
                                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
                            }
                            unset($updateRows);
                        }

                        unset($gameRecords);
                    }
                }else{
                    $this->CI->utils->debug_log('empty '.$dateDir);
                }

            }
        }

        return $result;
    }

    /**
     * isDirInThisDay
     * @param  string    $dir       sample 20190414
     * @param  \DateTime $startDate
     * @param  \DateTime $endDate
     * @return boolean
     */
    private function isDirInThisDay($dir, \DateTime $startDate, \DateTime $endDate){
        $start=$startDate->format('Ymd');
        $end=$endDate->format('Ymd');

        return $dir>=$start && $dir<=$end;
    }

    private function isFileInThisTime($file, \DateTime $startDate, \DateTime $endDate){
        $start=$startDate->format('YmdHi');
        $end=$endDate->format('YmdHi');

        if(substr($file, strlen($file)-5)=='.json'){
            //remove .json
            $file=substr($file, 0, strlen($file)-5);
        }

        return $file>=$start && $file<=$end;
    }

    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;

        $external_game_id = $row['game_code'];
        $extra = array('game_code' => $row['game_code']);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $external_game_id, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='tcg.settlement_time >= ? and tcg.settlement_time <= ?';
        if($use_bet_time){
            $sqlTime='tcg.bet_time >= ? and tcg.bet_time <= ?';
        }

        $sql = <<<EOD
SELECT
tcg.id as sync_index,
tcg.id,
tcg.username,
tcg.external_uniqueid,
tcg.bet_time,
tcg.settlement_time,
tcg.net_pnl AS result_amount,
tcg.bet_amount,
tcg.trans_time,
tcg.bet_content_id,
tcg.response_result_id,
tcg.username,
tcg.game_code,
tcg.bet_order_no,
tcg.win_amount,
tcg.last_updated_time,
tcg.bet_order_no as round_id,
tcg.bet_status AS status,
tcg.round_key,
tcg.external_uniqueid,
tcg.response_result_id,
tcg.md5_sum,
tcg.order_num,
tcg.bettingContent,
tcg.numero,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id

FROM tcg_game_logs as tcg

left JOIN game_description as gd ON tcg.game_code = gd.external_game_id and gd.game_platform_id=?
JOIN game_provider_auth ON tcg.username = game_provider_auth.login_name and game_provider_auth.game_provider_id=?

WHERE

{$sqlTime}

EOD;

        $params=[$this->getPlatformCode(), $this->getPlatformCode(), $dateFrom,$dateTo];

        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);

        return $result;
    }

    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
        $this->CI->load->model(array('tcg_game_logs_result'));
        $winningNo = $this->CI->tcg_game_logs_result->getWinningGameDrawResult($row['numero'],$row['game_code']);
        $extra_info=[
            "match_details" => !empty($winningNo) ? lang('Winning no').": {$winningNo}" : ""
        ];
        if(empty($row['md5_sum'])){
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }
        $data = [
            'game_info'=>['game_type_id'=>$row['game_type_id'], 'game_description_id'=>$row['game_description_id'],
                'game_code'=>$row['game_code'], 'game_type'=>null, 'game'=>$row['game_code']],
            'player_info'=>['player_id'=>$row['player_id'], 'player_username'=>$row['username']],
            'amount_info'=>['bet_amount'=>$row['bet_amount'], 'result_amount'=>$row['result_amount'],
                'bet_for_cashback'=>$row['bet_amount'], 'real_betting_amount'=>$row['bet_amount'],
                'win_amount'=>null, 'loss_amount'=>null, 'after_balance'=>null],
            'date_info'=>['start_at'=>$row['bet_time'], 'end_at'=>$row['settlement_time'], 'bet_at'=>$row['bet_time'],
                'updated_at'=>$this->CI->utils->getNowForMysql()],
            'flag'=>Game_logs::FLAG_GAME,
            'status'=>$row['status'],
            'additional_info'=>['has_both_side'=>0, 'external_uniqueid'=>$row['external_uniqueid'], 'round_number'=>$row['order_num'],
                'md5_sum'=>$row['md5_sum'], 'response_result_id'=>$row['response_result_id'], 'sync_index'=>$row['sync_index'],
                'bet_type'=>null ],
            'bet_details'=> $row['bettingContent'],
            'extra'=>$extra_info,
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];
        return $data;
    }

    public function preprocessOriginalRowForGameLogs(array &$row){
        $game_description_id = $row['game_description_id'];
        $game_type_id = $row['game_type_id'];
        if (empty($game_description_id)) {
            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $this->getUnknownGame());
        }

        $row['game_description_id']=$game_description_id;
        $row['game_type_id']=$game_type_id;
        $row['status'] =  $this->getGameRecordsStatus($row['status']);
    }

    public function syncMergeToGameLogs($token) {

        $enabled_game_logs_unsettle = true;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }

    private function getGameRecordsStatus($status) {
        $this->CI->load->model(array('game_logs'));
        $status = strtolower($status);

        switch ($status) {
            case '':
                $status = Game_logs::STATUS_PENDING;
                break;
            case '3':
                $status = Game_logs::STATUS_CANCELLED;
                break;
            case '1':
            case '2':
            case '4':
                $status = Game_logs::STATUS_SETTLED;
                break;
        }
        return $status;
    }

	public function changePassword($playerName, $oldPassword, $newPassword) {

        $newPassword = $this->utils->create_random_password(12);

		$this->utils->debug_log("TCG: (changePassword) playerName:", $playerName);

		$playerUsername = $playerName;
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForChangePassword',
			'playerName' => $playerUsername,
			'password' => $newPassword,
		);

		$params = array(
            "method" => 'up',
            'username' =>  $gameUsername,
			"password" => $newPassword
		);

		return $this->callApi(self::API_changePassword, $params, $context);
	}

    public function processResultForChangePassword($params) {
        $this->utils->debug_log("TCG: (processResultForChangePassword) params:", $params);
        
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $playerId = $this->getPlayerIdInPlayer($playerName);

        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText,TRUE);

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
        if ($success) {
            $result["password"] = $this->getVariableFromContext($params, 'password');			
			if($playerId) {
				$this->utils->debug_log("TCG: (processResultForChangePassword:updatePasswordForPlayer) params:", $params);
				$this->updatePasswordForPlayer($playerId, $result["password"]);
			}else{
				$this->utils->debug_log("TCG: (processResultForChangePassword:cannotFindPlayer) playerName: ".$playerName);
			}
        }

        return array($success, null);
    }

    public function batchQueryPlayerBalance($playerNames, $syncId = null) {
    }

    public function login($username, $password = null) {
        return $this->returnUnimplemented();
    }

    public function processResultForgetVendorId($params) {
        return $this->returnUnimplemented();
    }

    public function syncPlayerAccount($username, $password, $playerId) {
        return $this->returnUnimplemented();
    }

    public function queryPlayerInfo($playerName) {
        return $this->returnUnimplemented();
    }

    public function logout($playerName, $password = null) {
        return $this->returnUnimplemented();
    }

    public function updatePlayerInfo($playerName, $infos) {
        return $this->returnUnimplemented();
        // return array("success" => true);
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

    public function totalBettingAmount() {
        return $this->returnUnimplemented();
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
        return array(false, null);
    }

    public function encryptText($plainText, $key) {;
        $padded = $this->pkcs5_pad($plainText,@mcrypt_get_block_size("des", "ecb"));
        $encText = @mcrypt_encrypt("des",$key, $padded, "ecb");
        return base64_encode($encText);
    }

    public function decryptText($encryptText, $key) {
        $cipherText = base64_decode($encryptText);
        $res = @mcrypt_decrypt("des", $key, $cipherText, "ecb");
        $resUnpadded = $this->pkcs5_unpad($res);
        return $resUnpadded;
    }

    public function pkcs5_pad ($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }


    public function getGameProviderGamelist() {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetGameProviderGamelist'
        );

        $params = array(
            'lotto_type' => 'ELOTTO',
            'method' => 'glgl',
        );
        return $this->callApi(self::API_getGameProviderGamelist, $params, $context);
    }

    public function processResultForGetGameProviderGamelist($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $arrayResult);
        return array($success, $arrayResult);
    }

    public function syncOriginalGameResult($token) {
		if($this->allow_sync_game_draw_results){
            $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
            $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

            $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
            $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
            $startDate->modify($this->getDatetimeAdjust());
            $startDate = $startDate->format('Y-m-d H:i:s');
		    $endDate   = $endDate->format('Y-m-d H:i:s');
            
            $gameCodeResults = $this->queryGameCodeForGameDrawResult($startDate, $endDate);
            $this->CI->utils->debug_log('tcg gameCodeResults',$gameCodeResults);
            $result = array();
            if(!empty($gameCodeResults)){
                foreach($gameCodeResults as $index => $record) {
                    for ($page = 0; $page <= $this->gdr_default_page; $page++) {
                        $result[] = $this->getGameResult($record['game_code'], $page);
                    }
                }
            }
            return array("success" =>true, $result);
		}
		return array("success" =>false);
    }

    /**
     * queryGameCodeForGameDrawResult
     * @param  string $dateFrom
     * @param  string $dateTo
     * @return array
     */
    public function queryGameCodeForGameDrawResult($dateFrom, $dateTo){
        $this->CI->load->model(array('original_game_logs_model'));
        $sqlTime='tgl.bet_time >= ? and tgl.settlement_time <= ?';
        $sql = <<<EOD
SELECT distinct(tgl.game_code)
FROM tcg_game_logs as tgl
WHERE
{$sqlTime}
EOD;
        $params=[$dateFrom,$dateTo];
        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;
    }

    public function getGameResult($gameCode, $page, $showResults = false) {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetGameResult',
            'gameCode' => $gameCode,
            'count' => 100,
            'showResults' => $showResults,
        );

        $params = array(
            'method' => 'gwnh',
            'game_code' => $gameCode,
            'count' => $this->gdr_default_limit,
            'page' => $page//$this->gdr_default_page
        );
        // echo "<pre>";
        // print_r($params);
        return $this->callApi(self::API_queryGameResult, $params, $context);
    }

    /* Sample json response ofr draw results
        {"status":0,"winningNumber":[{"numero":"202003110102","gameCode":"TXFFC","winNo":"43901","winningTime":"2020-03-11 01:42:00"}],"error_desc":null};
    */
    const MD5_FIELDS_FOR_GAME_DRAW_RESULT = ['numero','game_code','win_no','winning_time'];

    public function processResultForGetGameResult($params){
        $this->CI->load->model(array('original_game_logs_model'));
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $arrayResult = $this->getResultJsonFromParams($params);
        if($this->getVariableFromContext($params, 'showResults')){
            $this->CI->utils->debug_log('after process ', 'draw_results ->',$arrayResult);
        }
        $dataResult = array(
			'data_count' => 0,
			'data_count_insert'=> 0,
			'data_count_update'=> 0
        );

        $success = $this->processResultBoolean($responseResultId, $arrayResult);
        if($success){
            if(isset($arrayResult['winningNumber']) && !empty($arrayResult['winningNumber'])){
                $draw_results = $arrayResult['winningNumber'];
                $this->processGameDrawResult($draw_results);
                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                    'tcg_game_draw_results',
                    $draw_results,
                    'unique_id',
                    'unique_id',
                    self::MD5_FIELDS_FOR_GAME_DRAW_RESULT,
                    'md5_sum',
                    'id',
                    []
                );
    
                $this->CI->utils->debug_log('after process available rows', 'draw_results ->',count($draw_results), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));
    
                $dataResult['data_count'] = count($draw_results);
                if (!empty($insertRows)) {
                    $dataResult['data_count_insert'] += $this->updateOrInsertGameResults($insertRows, 'insert');
                }
                unset($insertRows);
    
                if (!empty($updateRows)) {
                    $dataResult['data_count_update'] += $this->updateOrInsertGameResults($updateRows, 'update');
                }
                unset($updateRows);
            }
        }
        return array($success, $dataResult);
    }

    public function processGameDrawResult(&$draw_results){
        if(!empty($draw_results)){
            foreach($draw_results as $index => $record) {
				$data['numero'] = isset($record['numero']) ? $record['numero'] : null;
				$data['game_code'] = isset($record['gameCode']) ? $record['gameCode'] : null;
				$data['win_no'] = isset($record['winNo']) ? $record['winNo'] : null;
                $data['winning_time'] = isset($record['winningTime']) ? $record['winningTime'] : null;
                $data['unique_id'] = isset($record['numero']) ? $record['numero'] . '-' . $data['game_code']: null;
				$draw_results[$index] = $data;
				unset($data);
			}
        }
    }

    private function updateOrInsertGameResults($data, $queryType){
        $dataCount=0;
        if(!empty($data)){
            foreach ($data as $record) {
                if ($queryType == 'update') {
                	$record['updated_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->updateRowsToOriginal('tcg_game_draw_results', $record);
                } else {
                    unset($record['id']);
                    $record['created_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->insertRowsToOriginal('tcg_game_draw_results', $record);
                }
                $dataCount++;
                unset($record);
            }
        }
        return $dataCount;
    }
}

/*end of file*/