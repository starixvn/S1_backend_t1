<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_jumb extends Abstract_game_api {

    protected $api_url      = null;
    protected $web_url      = null;
    protected $dc           = null;
    protected $site         = null;
    protected $encrypt_iv   = null;
    protected $encrypt_key  = null;
    protected $access_token = null;
    protected $agent_code   = null;

    const API_queryGameLogs_slots       = 29;
    const API_queryGameLogs_fishinggame = 46;
    const API_trialGame                 = 47;

    const URI_MAP = array(
        self::API_isPlayerExist             => 15 ,
        self::API_createPlayer              => 12 ,
        self::API_checkLoginToken           => 11 ,
        self::API_updatePlayerInfo          => 13 ,
        self::API_queryPlayerBalance        => 15 ,
        self::API_checkLoginStatus          => 16 ,
        self::API_changePassword            => 36 ,
        self::API_depositToGame             => 19 ,
        self::API_withdrawFromGame          => 19 ,
        self::API_queryPlayerDailyBalance   => 42 ,
        self::API_queryTransaction          => 28 ,
    );

    public function __construct() {
        parent::__construct();
        $this->api_url      = $this->getSystemInfo('api_url');
        $this->dc           = $this->getSystemInfo('dc');
        $this->site         = $this->getSystemInfo('site');
        $this->encrypt_iv   = $this->getSystemInfo('encrypt_iv');
        $this->encrypt_key  = $this->getSystemInfo('encrypt_key');
        $this->web_url      = $this->getSystemInfo('web_url');
        $this->agent_code   = $this->getSystemInfo('agent_code');
        $this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+15 minutes');
        $this->password_suffix = $this->getSystemInfo('password_suffix','a1');
        $this->adjust_end_date = $this->getSystemInfo('adjust_end_date','+1 minute');
    }

    public function getPlatformCode() {
        return JUMB_GAMING_API;
    }

    protected function customHttpCall($ch, $params) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    }

    private function jumb_encrypt($str){
        $iv = $this->encrypt_iv;
        $td = @mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
        @mcrypt_generic_init($td, $this->encrypt_key, $iv);
        $str= $this->jumb_padString($str);
        $encrypted = @mcrypt_generic($td, $str);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        return base64_encode($encrypted);
    }


    public function jumb_decrypt($code) {
        $code = base64_decode($code);
        $iv = $this->encrypt_iv;
        $td = @mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
        @mcrypt_generic_init($td, $this->encrypt_key, $iv);
        $decrypted = @mdecrypt_generic($td, $code);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);

        return utf8_encode(trim($decrypted));
    }

    protected function jumb_padString($source) {
        $paddingChar = ' ';
        $size = 16;
        $x = strlen($source) % $size;
        $padLength = $size - $x;

        for ($i = 0; $i< $padLength; $i++) {
            $source .= $paddingChar;
        }

        return $source;
    }

    protected function jumb_now() {
        return round(microtime(true)*1000);
    }

    protected function jumb_argEncrypt($ar) {
        return $this->jumb_encrypt(json_encode($ar));
    }

    protected function transaction_sn() {
        $now = $this->jumb_now();
        $rnd = sprintf('%06x', mt_rand(0x0, 0xffffff));
        return "{$now}s{$rnd}";
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
        return $this->returnUnimplemented();
    }

    public function generateUrl($apiName, $params) {
        $url = $this->api_url.'dc='.$params['dc']. '&x=' . urlencode($params['x']);
        return $url;
    }

    protected function processResultBoolean($responseResultId, $resultArr, $player_name = null, $apiName = null) {
        $this->CI->utils->debug_log("apiName ================", $apiName);
        // $success = ($resultArr['status']=='0000'||$resultArr['status']=='7501')?true:false;
        $success = $resultArr['status']=='0000';

        # status 0000 for success, status 7501 for player not exist (will auto create player)
        if($apiName == "IsPlayerExist" && $resultArr['status'] == '7501'){
            $this->CI->utils->debug_log('if Player is not exist it this will auto create player');
            $success = true;
        }

        if($resultArr['status'] == '8006'){
            $this->CI->utils->debug_log('Jumb Sync Original Game Logs: No available game history at this date range');
        }

        if(!$success){
           $this->setResponseResultToError($responseResultId);
           $this->CI->utils->debug_log('JUMB got error ======================================>', $responseResultId, 'playerName', $player_name, 'result', $resultArr);
        }

        return $success;
    }

    public function isPlayerExist($playerName){
        $userName = $this->getGameUsernameByPlayerUsername($playerName);
        if(!empty($userName)){
            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForIsPlayerExist',
                'SbeplayerName' => $playerName
            );

            $jumb_params = array(
                'action'            => self::URI_MAP[self::API_isPlayerExist],
                'ts'                => $this->jumb_now(),
                'parent'            => $this->agent_code,
                'uid'               => !empty($userName)?$userName:$playerName
            );
            $this->CI->utils->debug_log('isPlayerExist params ======================================>', $jumb_params);
            $params = array(
                'dc'    => $this->dc,
                'x'     => $this->jumb_argEncrypt($jumb_params)
            );

            return $this->callApi(self::API_isPlayerExist, $params, $context);
        }else{
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $this->CI->utils->error_log('callback_error',"JUMB API isplayerExist empty username. trace: ",$trace);
            $result = array('success'=>false,'exists' => null); # Player not found
        }
    }

    public function processResultForIsPlayerExist($params){
        $SbeplayerName = $this->getVariableFromContext($params, 'SbeplayerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $SbeplayerName,'IsPlayerExist');

        if ($resultArr['status']!="7501") {
            $result = array('exists' => true);
        }else{
            $result = array('exists' => false); # Player not found
        }
        return array($success, $result);
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);

        $userName = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj'      => $this,
            'callback_method'   => 'processResultForCreatePlayer',
            'playerName'        => $playerName,
            'password'          => $password
        );

        $jumb_params = array(
            'action'            => self::URI_MAP[self::API_createPlayer] ,
            'ts'                => $this->jumb_now() ,
            'parent'            => $this->agent_code ,
            'uid'               => $userName,
            'name'              => $userName,
            'credit_allocated'  => 0,
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $password = $this->getVariableFromContext($params, 'password');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $playerName);

        if ($success || $resultArr['status'] == '7602') { #7602 means player already exist
            $this->changePassword($playerName,null,$password);
        }

        return array($success, $resultArr);
    }

    public function getLauncherGameType($game_type){
        $new_gameType='';
        switch ($game_type) {
            case 'slots':
                $new_gameType = '0';
                break;
            case 'fishing':
                $new_gameType = '7';
                break;
            case 'arcade':
                $new_gameType = '9';
                break;
            case 'table_and_cards':
                $new_gameType = '18';
                break;
            case 'lottery':
                $new_gameType = '12';
                break;
            default:
                $new_gameType = '0'; // by default slots
                break;
        }
        return $new_gameType;
    }

    public function getAccessToken($playerName = null, $extra = null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetAccessToken',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        if( $extra['game_mode'] == 'real'){
            $api_action = self::URI_MAP[self::API_checkLoginToken];
        } else {
            $api_action = self::API_trialGame;
        }

        $jumb_params = array(
            'action'    => $api_action,
            'ts'        => $this->jumb_now() ,
            'uid'       => $gameUsername ,
            'lang'      => $this->getLauncherLanguage($extra['language']) ,
            'gType'     => $this->getLauncherGameType($extra['game_type']), #'0' , # 0 slot , 7 Fishing machine
            'mType'    => $extra['game_code'],
            'windowMode'=> 2 # 1 - Include game hall, 2 - does not contain the game hall, hide the close button in the game gType and mType are required fields.
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        $this->utils->debug_log("JUMB jumb_params ============================>", $jumb_params);
        $this->utils->debug_log("JUMB ecrypted params ============================>", $params);

        return $this->callApi(self::API_checkLoginToken, $params, $context);
    }


    public function processResultForGetAccessToken($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,null);
        $this->utils->debug_log("JUMB getAccessToken ============================>", $resultArr);

        $result=['response_result_id'=>$responseResultId];

        if ($success) {
            if(isset($resultArr['path'])){
                $result['launcher_url']=$resultArr['path'];
            }else if(isset($resultArr['x'])){
                $result['access_token']=$resultArr['x'];
            }
            // $this->access_path = $resultArr['path'];
            // $this->access_token = $resultArr['x'];

        }

        return [$success,$result];
    }

    public function depositToGame($userName, $amount, $transfer_secure_id=null) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $trans_id =  $this->transaction_sn();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $userName,
            'amount' => $amount,
            'external_transaction_id' => $trans_id,
        );

        $jumb_params = array(
            'action'            => self::URI_MAP[self::API_depositToGame] ,
            'ts'                => $this->jumb_now() ,
            'parent'            => $this->agent_code ,
            'uid'               => $gameUsername ,
            'amount'            => $amount ,
            'credit_allocated'  => 0 ,
            'serialNo'          => $trans_id ,
            'remark'            => '' ,
            'allCashOutFlag'    => ''
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi( self::API_depositToGame, $params, $context);

    }

    function processResultForDepositToGame($params) {
        $gameUsername = $this->getVariableFromContext($params, 'playerName');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);

        $result = array(
            'response_result_id' => $responseResultId,
            'reason_id'=>self::REASON_UNKNOWN,
            'external_transaction_id'=>$external_transaction_id
        );

        $success = false;
        $this->utils->debug_log("Deposit ResultArr ============================>", $resultArr);
        $this->utils->debug_log("Deposit result from response result id ============================>", $resultArr);

        if ($this->processResultBoolean($responseResultId, $resultArr,$gameUsername)) {
            //get current sub wallet balance
            // $playerBalance = $this->queryPlayerBalance($playerName);

            //for sub wallet
            // $afterBalance = $playerBalance['balance'];
            //$result["external_transaction_id"] = $resultArr['pid'];
            // $result["currentplayerbalance"] = $afterBalance;
            // $result["userNotFound"] = false;
            //update
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     //deposit
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
            // }
            $success = true;
            $result['didnot_insert_game_logs']=true;
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {
            $error_code = @$resultArr['status'];
            $result['reason_id'] = $this->getReason($error_code);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);

    }

    function getReason($error_code) {
        switch($error_code) {
            case '7501' :
                $reason_id = self::REASON_NOT_FOUND_PLAYER;
                break;
            case '999' :
                $reason_id = self::REASON_FAILED_FROM_API;
                break;
            case '9001' :
                $reason_id = self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                break;
            case '9005' :
                $reason_id = self::REASON_INVALID_KEY;
                break;
            case '9009' :
            case '9010' :
                $reason_id = self::REASON_SESSION_TIMEOUT;
                break;
            case '9013' :
                $reason_id = self::REASON_API_MAINTAINING;
                break;
            case '8000' :
            case '8001' :
            case '8002' :
            case '8003' :
                $reason_id = self::REASON_INCOMPLETE_INFORMATION;
                break;
            case '7502' :
            case '7503' :
            case '7504' :
                $reason_id = self::REASON_GAME_ACCOUNT_LOCKED;
                break;
            case '7601' :
                $reason_id = self::REASON_NOT_FOUND_PLAYER;
                break;
            case '6001' :
            case '6002' :
            case '6006' :
                $reason_id = self::REASON_NO_ENOUGH_BALANCE;
                break;
            case '6004' :
            case '9011' :
                $reason_id = self::REASON_DUPLICATE_TRANSFER;
                break;
            default :
                $reason_id = self::REASON_UNKNOWN;
        }

        return $reason_id;
    }

    function queryPlayerBalance($userName) {

        $playerName = $this->getGameUsernameByPlayerUsername($userName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerName' => $playerName
        );

        $jumb_params = array(
            'action'    => self::URI_MAP[self::API_queryPlayerBalance] ,
            'ts'        => $this->jumb_now() ,
            'parent'    => $this->agent_code ,
            'uid'       => $playerName ,
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    function processResultForQueryPlayerBalance($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');

        $success = false;
        $result = array();
        if ($this->processResultBoolean($responseResultId, $resultArr,$playerName)) {

            $success = true;
            $result['balance'] = @floatval($resultArr['data'][0]['balance']);

            if ($playerId = $this->getPlayerIdInGameProviderAuth($playerName)) {
                $this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName', $playerName, 'balance', $result['balance']);
            } else {
                $this->CI->utils->debug_log('cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            }

        } else {
            $success = false;
            if (@$resultArr['error'] == 'PLAYER NOT FOUND') {
                $result['exists'] = false;
            } else {
                $result['exists'] = true;
            }
        }

        return array($success, $result);

    }

    function withdrawFromGame($userName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $trans_id = $this->transaction_sn();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $userName,
            'amount' => $amount,
            'external_transaction_id' => $trans_id,
        );

        $jumb_params = array(
            'action'            => self::URI_MAP[self::API_withdrawFromGame] ,
            'ts'                => $this->jumb_now() ,
            'parent'            => $this->agent_code ,
            'uid'               => $gameUsername ,
            'amount'            => '-'.$amount ,
            'credit_allocated'  => 0 ,
            'serialNo'          => $trans_id ,
            'remark'            => '' ,
            'allCashOutFlag'    => ''
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

    function processResultForWithdrawFromGame($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = false;

        $result = array(
            'response_result_id' => $responseResultId,
            'reason_id'=>self::REASON_UNKNOWN,
            'external_transaction_id'=>$external_transaction_id
        );
        if ($this->processResultBoolean($responseResultId, $resultArr,$gameUsername)) {
            //get current sub wallet balance
            // $playerBalance = $this->queryPlayerBalance($playerName);

            //for sub wallet
            // $afterBalance = $playerBalance['balance'];
            //$result["external_transaction_id"] = $resultArr['pid'];
            // $result["currentplayerbalance"] = $afterBalance;
            // $result["userNotFound"] = false;
            //update
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     //withdraw
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,$this->transTypeSubWalletToMainWallet());

            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
            // }

            $success = true;
            $result['didnot_insert_game_logs']=true;
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {
            $error_code = @$resultArr['status'];
            $result['reason_id'] = $this->getReason($error_code);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);
    }

    public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
            case LANGUAGE_FUNCTION::INT_LANG_ENGLISH:
            case 'en-us':
                $lang = 'en'; // english
                break;
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
            case 'zh-cn':
                $lang = 'ch'; // chinese
                break;
            case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
            case 'id-id':
                $lang = 'in'; // indonesia
                break;
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
            case 'vi-vn':
                $lang = 'vi'; // vietnamese
                break;
            default:
                $lang = 'en'; // default as english
                break;
        }
        return $lang;
    }

    public function queryForwardGame($playerName = null, $extra=null) {
        $result = $this->getAccessToken($playerName,$extra);
        $data = [
            'url' => '',
            'success' => false
        ];

        if ($result['success']) {
            if(isset($result['launcher_url'])){
                $url = $result['launcher_url']; // $this->web_url . '?x=' . $this->access_token;
                $data = [
                    'url' => $url,
                    'success' => true
                ];
            }else if(isset($result['access_token'])){
                $url = $this->web_url . '?x=' . $result['access_token'];
                $data = [
                    'url' => $url,
                    'success' => true
                ];
            }
        }

        return $data;
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

    public function syncOriginalGameLogs($token = false) {

        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
        $endDate->modify($this->adjust_end_date); # we adjust the minute of end date here
        $startDate->modify($this->getDatetimeAdjust());

        //observer the date format
        $queryDateTimeStart = $startDate->format('d-m-Y H:i:s');
        $queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('d-m-Y H:i:s');
        $queryDateTimeMax = $endDate->format('d-m-Y H:i:00');

        $result = [];
        while ($queryDateTimeMax  > $queryDateTimeStart) {

            $startDateParam=new DateTime($queryDateTimeStart);
            if($queryDateTimeEnd>$queryDateTimeMax){
                $endDateParam=new DateTime($queryDateTimeMax);
            }else{
                $endDateParam=new DateTime($queryDateTimeEnd);
            }
            $startDateParam = $startDateParam->format('d-m-Y H:i:00');
            $endDateParam = $endDateParam->format('d-m-Y H:i:00');

            $result['slots'.$startDateParam.' to '.$endDateParam] = $this->syncJumbGamelogs($startDateParam,$endDateParam,self::API_queryGameLogs_slots);
           //$result['fishinggame'.$startDateParam.' to '.$endDateParam] = $this->syncJumbGamelogs($startDateParam,$endDateParam,self::API_queryGameLogs_fishinggame); # we dont have action 46 as API DOC version 2.6.0

            $queryDateTimeStart = $endDateParam;
            $queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('d-m-Y H:i:00');
        }

        return array_merge(array("success"=>true),array("details"=>$result));

    }
    public function getGameRecordPath()
    {
        return $this->getSystemInfo('jumb_game_records_path');
    }

    public function syncJumbGamelogsFtp($token = false){
        $gameLogDirectoryJumb = $this->getGameRecordPath();

        if(!is_array($gameLogDirectoryJumb)){
          $gameLogDirectoryJumb = (array)$gameLogDirectoryJumb;
        }

        $playerName     = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom   = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo     = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $dateTimeFrom   = new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $dateTimeTo     = new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));
        $dateTimeFrom->modify($this->getDatetimeAdjust());

        if(!empty($gameLogDirectoryJumb)){
            foreach ($gameLogDirectoryJumb as $logDirectoryJumb => $logDirectoryJumbValue) {
                $startDate = new DateTime($dateTimeFrom->format('Y-m-d H:i:s'));
                $endDate = new DateTime($dateTimeTo->format('Y-m-d H:i:s'));
                $day_diff = $endDate->diff($startDate)->format("%a");

                if ($day_diff > 0) {
                    for ($i = 0; $i < $day_diff; $i++) {
                        if ($i == 0) {
                            $directory = $logDirectoryJumbValue . $startDate->format('Ymd');
                            $this->retrieveTXTFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName);
                        }
                        $startDate->modify('+1 day');
                        $directory = $logDirectoryJumbValue . $startDate->format('Ymd');
                        $this->retrieveTXTFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName);
                    }
                } else {
                    $directory = $logDirectoryJumbValue . $startDate->format('Ymd');
                    $this->retrieveTXTFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName);

                    $startDate->modify('+1 day');
                    $directory = $logDirectoryJumbValue . $startDate->format('Ymd');
                    $this->retrieveTXTFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName);
                }
            }

            return array('success' => true);
        }

    }

    function getFileExtension($filename)
    {
        $path_info = pathinfo($filename);
        return $path_info['extension'];
    }

    public function retrieveTXTFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName)
    {
        $extensions = array("txt");
        $this->CI->load->model(array('jumb_game_logs', 'player_model'));
        $this->CI->utils->debug_log('TXT CURRENT DIRECTORY------',$directory);
        $result = array();
        $result['count'] = $count = 0;
        if (is_dir($directory)) {
            //get file into array
            $jumbGamelogsFile = array_diff(scandir($directory), array('..', '.'));
            $dateTimeFrom = $dateTimeFrom->format('YmdHi');
            $dateTimeTo = $dateTimeTo->format('YmdHi');
            if(!empty($jumbGamelogsFile)){
                foreach ($jumbGamelogsFile as $jumbFile) {
                    $ext = $this->getFileExtension($jumbFile);
                    if (!in_array($ext,$extensions)) {//skip other extension
                        continue;
                    }
                    $fileDate = explode("_",str_replace(".txt"," ",$jumbFile));//get the date from file name
                    $fileDateFrom = $fileDate[0]; //date start of the file
                    $fileDateTo = $fileDate[1]; //date end of the file
                    if ($fileDateFrom >= $dateTimeFrom && $fileDateTo <= $dateTimeTo) {
                        $data = file($directory."/".$jumbFile)[0];//get record by the full directory of the file
                        $gameRecords = json_decode($data,true);
                        $availableRows = $this->CI->jumb_game_logs->getAvailableRows($gameRecords);
                        if (!empty($availableRows)) {
                            foreach ($availableRows as $key => $record) {
                                $insertRow = array();

                                $start_time = new DateTime($this->gameTimeToServerTime($record['gameDate']));
                                $end_time   = new DateTime($this->gameTimeToServerTime($record['lastModifyTime']));
                                $player_username = isset($record['playerId'])?$record['playerId']:null;
                                $insertRow['seqNo'] = isset($record['seqNo'])?$record['seqNo']:null;
                                $insertRow['gType'] = isset($record['gType'])?$record['gType']:null;
                                $insertRow['mtype'] = isset($record['mtype'])?$record['mtype']:null;
                                $insertRow['gameDate'] = $start_time->format('Y-m-d H:i:s');
                                $insertRow['bet'] = isset($record['bet'])?$record['bet']:null;
                                $insertRow['win'] = isset($record['win'])?$record['win']:null;
                                $insertRow['total'] = isset($record['total'])?$record['total']:null;
                                $insertRow['currency'] = isset($record['currency'])?$record['currency']:null;
                                $insertRow['jackpot'] = isset($record['jackpot'])?$record['jackpot']:null;
                                $insertRow['jackpotContribute'] = isset($record['jackpotContribute'])?$record['jackpotContribute']:null;
                                $insertRow['denom'] = isset($record['denom'])?$record['denom']:null;
                                $insertRow['lastModifyTime'] = $end_time->format('Y-m-d H:i:s');
                                $insertRow['gameName'] = isset($record['gameName'])?$record['gameName']:null;
                                $insertRow['playerIp'] = isset($record['playerIp'])?$record['playerIp']:null;
                                $insertRow['clientType'] = isset($record['clientType'])?$record['clientType']:null;
                                $insertRow['hasFreegame'] = isset($record['hasFreegame'])?$record['hasFreegame']:null;
                                $insertRow['hasGamble'] = isset($record['hasGamble'])?$record['hasGamble']:null;
                                $insertRow['gambleBet'] = isset($record['gambleBet'])?$record['gambleBet']:null;
                                $insertRow['systemTakeWin'] = isset($record['systemTakeWin'])?$record['systemTakeWin']:null;
                                # SBE use
                                $insertRow['username'] = $player_username;
                                $insertRow['playerId'] = $this->getPlayerIdInGameProviderAuth($player_username);
                                $insertRow['external_uniqueid'] = isset($record['seqNo'])?$record['seqNo']:null; //add external_uniueid for og purposes
                                $insertRow['response_result_id'] = null;
                                $this->CI->jumb_game_logs->insertJumbGameLogs($insertRow);
                                $count++;
                            }
                            $result['count'] = $count;
                        }
                    }
                }
            }
        }
        return array(true, $result);
    }

    public function syncJumbGamelogs( $startDate,$endDate, $game_type){

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncGameRecords'
        );

        $jumb_params = array(
            'action'    => $game_type ,
            'ts'        => $this->jumb_now() ,
            'parent'    => $this->agent_code ,
            'starttime' => $startDate ,
            'endtime'   => $endDate
        );

        $this->CI->utils->debug_log('JUMB request params ======================>',$jumb_params);
        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    public function processResultForSyncGameRecords($params) {
        $this->CI->load->model(array('jumb_game_logs', 'player_model'));

        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array();
        $result['count'] = $count = 0;
        if ($success) {
            $gameRecords = $resultArr['data'];
            if ($gameRecords) {
                $availableRows = $this->CI->jumb_game_logs->getAvailableRows($gameRecords);
                if (!empty($availableRows)) {
                    foreach ($availableRows as $record) {
                        $insertRow = array();

                        $start_time = new DateTime($this->gameTimeToServerTime($record['gameDate']));
                        $end_time   = new DateTime($this->gameTimeToServerTime($record['lastModifyTime']));
                        $player_username = isset($record['playerId'])?$record['playerId']:null;
                        $insertRow['seqNo'] = isset($record['seqNo'])?$record['seqNo']:null;
                        $insertRow['gType'] = isset($record['gType'])?$record['gType']:null;
                        $insertRow['mtype'] = isset($record['mtype'])?$record['mtype']:null;
                        $insertRow['gameDate'] = $start_time->format('Y-m-d H:i:s');
                        $insertRow['bet'] = isset($record['bet'])?$record['bet']:null;
                        $insertRow['win'] = isset($record['win'])?$record['win']:null;
                        $insertRow['total'] = isset($record['total'])?$record['total']:null;
                        $insertRow['currency'] = isset($record['currency'])?$record['currency']:null;
                        $insertRow['jackpot'] = isset($record['jackpot'])?$record['jackpot']:null;
                        $insertRow['jackpotContribute'] = isset($record['jackpotContribute'])?$record['jackpotContribute']:null;
                        $insertRow['denom'] = isset($record['denom'])?$record['denom']:null;
                        $insertRow['lastModifyTime'] = $end_time->format('Y-m-d H:i:s');
                        $insertRow['gameName'] = isset($record['gameName'])?$record['gameName']:null;
                        $insertRow['playerIp'] = isset($record['playerIp'])?$record['playerIp']:null;
                        $insertRow['clientType'] = isset($record['clientType'])?$record['clientType']:null;
                        $insertRow['hasFreegame'] = isset($record['hasFreegame'])?$record['hasFreegame']:null;
                        $insertRow['hasGamble'] = isset($record['hasGamble'])?$record['hasGamble']:null;
                        $insertRow['gambleBet'] = isset($record['gambleBet'])?$record['gambleBet']:null;
                        $insertRow['systemTakeWin'] = isset($record['systemTakeWin'])?$record['systemTakeWin']:null;
                        $insertRow['beforeBalance'] = isset($record['beforeBalance']) ? $record['beforeBalance'] : null;
                        $insertRow['afterBalance'] = isset($record['afterBalance']) ? $record['afterBalance'] : null;
                        # SBE use
                        $insertRow['username'] = $player_username;
                        $insertRow['playerId'] = $this->getPlayerIdInGameProviderAuth($player_username);
                        $insertRow['external_uniqueid'] = isset($record['seqNo'])?$record['seqNo']:null; //add external_uniueid for og purposes
                        $insertRow['response_result_id'] = $responseResultId;
                        $this->CI->jumb_game_logs->insertJumbGameLogs($insertRow);
                        $count++;
                    }
                    $result['count'] = $count;
                }
            }
            $this->CI->utils->debug_log('Jumb SyncOriginalGameLogs monitor count ======================>',$result['count']);
        }

        return array($success, $result);
    }

    public function syncMergeToGameLogs($token) {

        $this->CI->load->model(array('game_logs', 'player_model', 'jumb_game_logs'));

        $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeFrom->modify($this->getDatetimeAdjust());
        $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        //observer the date format
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $rlt = array('success' => true);

        $result = $this->CI->jumb_game_logs->getGameLogStatistics($startDate, $endDate);
        $cnt = 0;
        if ($result) {

            $unknownGame = $this->getUnknownGame();
            $bet_details = [];

            foreach ($result as $jumb_data) {

                $realbet = abs($jumb_data->bet_amount);
                $GameRecordId = $jumb_data->seqNo;

                $bet_details = [
                    'place_of_bet' => lang($jumb_data->game),
                    'bet_amount' => $realbet,
                    'result_amount' => $jumb_data->result_amount,
                ];

                $note = $this->convertGamedetatilsToJson($bet_details);
                if (!$jumb_data->playerId) {
                    continue;
                }

                $cnt++;

                $game_description_id = $jumb_data->game_description_id;
                $game_type_id = $jumb_data->game_type_id;

                if (empty($game_description_id)) {
                    $game_description_id = $unknownGame->id;
                    $game_type_id = $unknownGame->game_type_id;
                    $jumb_data->game_code = $unknownGame->game_name . '-' . $jumb_data->game_code;
                }

                $extra = array('table' => $GameRecordId, 'trans_amount' => $realbet, 'note' => $note);

                $this->syncGameLogs(
                    $game_type_id,
                    $game_description_id,
                    $jumb_data->game_code,
                    $jumb_data->game_type,
                    $jumb_data->game,
                    $jumb_data->playerId,
                    $jumb_data->username,
                    $realbet,
                    $jumb_data->result_amount,
                    null, # win_amount
                    null, # loss_amount
                    $jumb_data->after_balance, # after_balance
                    0, # has_both_side
                    $jumb_data->external_uniqueid,
                    $jumb_data->date_start, //start
                    $jumb_data->date_end, //end
                    $jumb_data->response_result_id,
                    null,
                    $extra
                );

            }
        }

        $this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
        return $rlt;
    }

    public function convertGamedetatilsToJson($gameDetails = null){

        $bet_details = lang("Bet amount") . ": " . $gameDetails['bet_amount'] . ", " . lang(" Bet result") . ": " .  $gameDetails['result_amount'];
        $data['bet_details'] = lang("Place of bet") . ": " . $gameDetails['place_of_bet'] . ", " . $bet_details;
        return json_encode($data);

    }

    public function changePassword($playerName, $oldPassword = null, $newPassword,$createPlayer = null) {

        $isPlayerExist = $this->isPlayerExist($playerName);
        $this->CI->utils->debug_log('isPlayerExist resultArr ======================================>', $isPlayerExist);

        if(!empty($isPlayerExist['exists'])){
            $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
            $context = array(
                    'callback_obj' => $this,
                    'callback_method' => 'processResultForchangePassword',
                    'playerName' => $playerName
                );

            $jumb_params = array(
                    "action"    => self::URI_MAP[self::API_changePassword],
                    'ts'        => $this->jumb_now() ,
                    'parent'    => $this->agent_code ,
                    "uid"       => $gameUsername,
                    "password"  => $newPassword . $this->password_suffix
                );

            $this->CI->utils->debug_log('change password params ==================>', $jumb_params);
            $params = array(
                'dc'    => $this->dc,
                'x'     => $this->jumb_argEncrypt($jumb_params)
            );

            return $this->callApi(self::API_changePassword, $params, $context);
        }
    }

    public function processResultForchangePassword($params){
        $SbeplayerName = $this->getVariableFromContext($params, 'SbeplayerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $SbeplayerName);
        $this->CI->utils->debug_log('processResultForchangePassword ==================>', $resultArr);

        if ($success) {
            $result = array('changePassword' => true);
        }else{
            $result = array('changePassword' => false);
        }

        return array($success, $result);
    }

    public function getLanguage($currentLang) {

        switch ($currentLang) {
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
                $language = 'ch';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_INDONESIAN:
                $language = 'in';
                break;
            case LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE:
                $language = 'vi';
                break;
            // case LANGUAGE_FUNCTION::INT_LANG_KOREAN:
            //     $language = 'ko';
            //     break;
            //     they don't currently support Korean
            default:
                $language = 'en';
                break;
        }
        return $language;
    }

    public function queryTransaction($transactionId, $extra) {
        $playerName = $extra['playerName'];
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'playerName' => $playerName,
            'external_transaction_id'          => $transactionId
        );

        $jumb_params = array(
            'action'            => self::URI_MAP[self::API_queryTransaction] ,
            'ts'                => $this->jumb_now() ,
            'parent'            => $this->agent_code ,
            'serialNo'          => $transactionId
        );

        $params = array(
            'dc'    => $this->dc ,
            'x'     => $this->jumb_argEncrypt($jumb_params)
        );

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);

        $success = !empty($resultArr['data']) ? true : false;

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
        );
        if ($success) {
            $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }else{
            $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);
    }

    function syncPlayerAccount($username, $password, $playerId) {
        return $this->returnUnimplemented();
    }

    function queryPlayerInfo($playerName) {
        return $this->returnUnimplemented();
    }

    function login($userName, $password = null) {
        return $this->returnUnimplemented();
    }

    function logout($playerName, $password = null) {
        return $this->returnUnimplemented();
    }

    function updatePlayerInfo($playerName, $infos) {
        return $this->returnUnimplemented();
        // return array("success" => true);
    }

    function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
        return $this->returnUnimplemented();
    }

    function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
        return $this->returnUnimplemented();
    }

    function checkLoginStatus($playerName) {
        return $this->returnUnimplemented();
    }

    public function checkLoginToken($playerName, $token) {
        return $this->returnUnimplemented();
    }

    function totalBettingAmount($playerName, $dateTimeFrom, $dateTimeTo) {
        return $this->returnUnimplemented();
    }

    // public function batchQueryPlayerBalance($playerNames, $syncId = null) {
    //     if (empty($playerNames)) {
    //         $players = $this->CI->player_model->getPlayerListOnlyAvailBal($this->getPlatformCode());
    //         $playerNames = array();
    //         foreach ($players as $player) {
    //             $username = $player->username;
    //             $playerNames[]=$username;
    //         }
    //     }

    //     return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);

    // }

}
