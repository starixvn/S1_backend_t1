<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_sagaming extends Abstract_game_api {

    private $api_url;
    private $secret_key;
    private $md5_key;
    private $currency;
    private $encrypt_key;
    private $fix_check_key;
    private $key;
    private $iv;
    private $game_url;

    public function __construct() {
        parent::__construct();
        $this->api_url = $this->getSystemInfo('url');
        $this->secret_key = $this->getSystemInfo('secret_key');
        $this->md5_key = $this->getSystemInfo('md5_key');
        $this->encrypt_key = $this->getSystemInfo('encrypt_key');
        $this->currency = $this->getSystemInfo('currency');
        $this->fix_check_key = $this->getSystemInfo('fix_check_key');
        $this->game_url = $this->getSystemInfo('game_url');
        $this->backoffice_url = $this->getSystemInfo('backoffice_url');
        $this->lobby_code = $this->getSystemInfo('lobby_code', 'A222');
        $this->sync_by_date = $this->getSystemInfo('sync_by_date', false);
        $this->sync_by_loop = $this->getSystemInfo('sync_by_loop', false);
        $this->sync_interval = $this->getSystemInfo('sync_interval', "+ 5 minutes");//default per call
        $this->game_launch_options_param = $this->getSystemInfo('game_launch_options_param',[]); # game launch option
        $this->lobby_url = $this->getSystemInfo('lobby_url');
        //don't support
        $this->is_enabled_direct_launcher_url=$this->getSystemInfo('is_enabled_direct_launcher_url', false);
        $this->language = $this->getSystemInfo('language', 'zh-cn');

        ## for syncOriginalGameLogsByLoop
        $this->loop_interval = $this->getSystemInfo('loop_interval', '+6 hours');
        $this->loop_timeout = $this->getSystemInfo('loop_timeout', 180);
        $this->use_sleep_on_sync = $this->getSystemInfo('use_sleep_on_sync', false);
        $this->force_empty_lobbyurl = $this->getSystemInfo('force_empty_lobbyurl', false);
    }

    const LIST_OF_ROULLETE_KEYS = ['0~36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122', '123', '124', '125', '126', '127', '128', '129', '130', '131', '132', '133', '134', '135', '136', '137', '138', '139', '140', '141', '142', '143', '144', '145', '146', '147', '148', '149', '150', '151', '152', '153', '154', '155', '156'];

    const ORIGINAL_LOGS_TABLE_NAME = 'sagaming_game_logs';

    # Fields in sagaming_game_logs we want to detect changes for update
    const MD5_FIELDS_FOR_ORIGINAL=[
        "PayoutTime",
        "HostID",
        "GameID",
        "Round",
        "Set",
        "BetID",
        "BetAmount",
        "ResultAmount",
        "Balance",
        "GameType",
        "BetType"
    ];

    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS = [
        "Balance",
        "BetAmount",
        "ResultAmount",
    ];

    # Fields in sagaming_game_logs we want to detect changes for update
    const MD5_FIELDS_FOR_MERGE=[
        "updated_at",
        "round",
        "start_at",
        "end_at",
        "bet_at",
        "BetAmount",
        "result_amount",
        "Balance",
        "game_type",
        "BetType",
        "external_uniqueid",
        "username",
    ];


    # Values of these fields will be rounded when calculating MD5
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE = [
        "Balance",
        "BetAmount",
        "result_amount",
    ];

    public function getPlatformCode() {
        return SA_GAMING_API;
    }

    public function generateUrl($apiName, $params) {
        $this->CI->utils->debug_log('generateUrl ===========================================>'. $params['method']);
        if ($params['method'] == 'GetAllBetDetailsForTimeIntervalDV' OR $params['method'] == 'GetAllBetDetailsDV') {
            return $this->backoffice_url;
        } else {
            return $this->api_url;
        }

    }

    public function getHttpHeaders($params){
        return array("Content-Type" => "application/x-www-form-urlencoded");
    }

    protected function customHttpCall($ch, $params) {

        $ctime = $params["Time"];
        $this->DES($this->encrypt_key);
        $params = http_build_query($params);
        $urlemstr = urlencode($this->encrypt($params));
        $PreMD5Str = $params . $this->md5_key . $ctime . $this->secret_key;
        $OutMD5 = md5($PreMD5Str);
        $postFields = "q=".$urlemstr."&s=".$OutMD5;

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    }

    private function DES( $key, $iv=0 ) {
        $this->key = $key;
        if( $iv == 0 ) {
            $this->iv = $key;
        } else {
            $this->iv = $iv;
        }
    }

    private function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen ( $text ) % $blocksize);
        return $text . str_repeat ( chr ( $pad ), $pad );
    }

    private function encrypt($str) {

        $size = @mcrypt_get_block_size ( MCRYPT_DES, MCRYPT_MODE_CBC);
        $str = $this->pkcs5Pad ( $str, $size );
        return base64_encode( @mcrypt_encrypt(MCRYPT_DES, $this->key, $str, MCRYPT_MODE_CBC,$this->iv));
    }

    public function callback($method, $result = null) {
        $this->CI->utils->debug_log('Game_api_opus (Callback): ', $result);

        if ($method == 'checkkey') {
            if($result == $this->fix_check_key){
                $returnTxt = "checkkeyok";
            }else{
                $returnTxt = "checkkeyfailed";
            }
            return $returnTxt;
        }

    }

    public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
            case 1:
            case 'en-us':
                $lang = 'en_US'; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 'zh_CN'; // chinese
                break;
            case 3:
            case 'id-id':
                $lang = 'id';
                break;
            case 4:
            case 'vi-vn':
                $lang = 'vn'; // vietnamese
                break;
            case 5:
            case 'ko-kr':
                $lang = 'ko-KR'; // korean
                break;
            case 6:
            case 'th-th':
                $lang = 'th'; // thai
                break;
            default:
                $lang = 'en_US'; // default as english
                break;
        }
        return $lang;
    }


    function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
        return array(false, null);
    }

    function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
        $success = false;
        if($resultArr['ErrorMsgId']==0){
            $success = true;
        }
        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('SA GAMING got error', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
        }
        return $success;
    }

    function login($userName, $password = null) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogin',
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'method' => 'LoginRequest',
            'Key' => $this->secret_key,
            'Time' => date("YmdHis"),
            'Checkkey' => $this->fix_check_key,
            'Username' => $gameUsername,
            'CurrencyType' => $this->currency
        );

        return $this->callApi(self::API_login, $params, $context);
    }

    function processResultForLogin($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXmlArray = (array)$this->getResultXmlFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $success = $this->processResultBoolean($responseResultId, $resultXmlArray, $gameUsername);

        return array($success, $resultXmlArray);
    }

    function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'gameUsername' => $gameUsername
        );

        $params = array(
            'method' => 'RegUserInfo',
            'Key' => $this->secret_key,
            'Time' => date("YmdHis"),
            'Username' => $gameUsername,
            'Checkkey' => $this->fix_check_key,
            'CurrencyType' => $this->currency
        );

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    function processResultForCreatePlayer($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXmlArray = (array)$this->getResultXmlFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $success = $this->processResultBoolean($responseResultId, $resultXmlArray, $gameUsername);

        return array($success, $resultXmlArray);

    }

    function isPlayerExist($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername
        );

        $params = array(
            'method' => 'VerifyUsername',
            'Key' => $this->secret_key,
            'Time' => date("YmdHis"),
            'Username' => $gameUsername,
            'Checkkey' => $this->fix_check_key
        );

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXmlArray = (array)$this->getResultXmlFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $success = $this->processResultBoolean($responseResultId, $resultXmlArray, $gameUsername);
        $playerId = $this->getPlayerIdInPlayer($playerName);

        if(empty($resultXmlArray)){
            $success = false;
            $result = array('exists' => null);
        }else{
            if ($resultXmlArray['ErrorMsgId']=="0") {
                $result = array('exists' => true);
                # update flag to registered = true
                $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            }else if($resultXmlArray['ErrorMsgId']=="116"){
                $result = array('exists' => false); # Player not found
            }else{
                $result = array('exists' => null);
            }
        }

        return array($success, $result);
    }

    function queryPlayerBalance($userName) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'method' => 'GetUserStatusDV',
            'Key' => $this->secret_key,
            'Time' => date("YmdHis"),
            'Username' => $gameUsername,
            'Checkkey' => $this->fix_check_key
        );

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);

    }

    function processResultForQueryPlayerBalance($params) {

        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXmlArray = (array)$this->getResultXmlFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultXmlArray,$gameUsername);
        $result = array();

        if ($success) {
            $result['balance'] = floatval($resultXmlArray['Balance']);
            $result['exists'] = true;
        }

        return array($success, $result);

    }

    function depositToGame($userName, $amount, $transfer_secure_id=null) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $cdate = date("YmdHis");
        $orderId = "IN".$cdate.$gameUsername;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'sbe_playerName' => $userName,
            'external_transaction_id' => $orderId,
            'amount' => $amount
        );

        $params = array(
            'method' => 'CreditBalanceDV',
            'Key' => $this->secret_key,
            'Time' => $cdate,
            'Username' => $gameUsername,
            'OrderId' => $orderId,
            'CreditAmount' => $amount,
            'Checkkey' => $this->fix_check_key
        );

        return $this->callApi(self::API_depositToGame, $params, $context);

    }

    function processResultForDepositToGame($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = (array)$this->getResultXmlFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if($success){
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $error_code = @$resultArr['ErrorMsgId'];
            $result['reason_id'] = $this->getReasons($error_code);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);

    }

    private function getReasons($statusCode)
    {
        switch($statusCode) {
                case '102' :
                    return self::REASON_INVALID_KEY;
                    break;
                case '108' :
                case '115' :
                case '116' :
                case '118' :
                case '119' :
                    return self::REASON_NOT_FOUND_PLAYER;
                    break;
                case '120' :
                    return self::REASON_TRANSFER_AMOUNT_IS_TOO_LOW;
                    break;
                case '121' :
                    return self::REASON_NO_ENOUGH_BALANCE;
                    break;
                case '122' :
                case '127' :
                    return self::REASON_INVALID_TRANSACTION_ID;
                    break;
                case '106' :
                case '124' :
                    return self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    break;
                case '129' :
                    return self::REASON_API_MAINTAINING;
                    break;
        }
    }

    function withdrawFromGame($userName, $amount, $transfer_secure_id=null) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($userName);

        $cdate = date("YmdHis");
        $orderId =  "OUT".$cdate.$gameUsername;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'sbe_playerName' => $userName,
            'external_transaction_id' => $orderId,
            'amount' => $amount
        );

        $params = array(
            'method' => 'DebitBalanceDV',
            'Key' => $this->secret_key,
            'Time' => $cdate,
            'Username' => $gameUsername,
            'OrderId' => $orderId,
            'DebitAmount' => $amount,
            'Checkkey' => $this->fix_check_key
        );

        return $this->callApi(self::API_withdrawFromGame, $params, $context);

    }

    function processResultForWithdrawFromGame($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = (array)$this->getResultXmlFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if ($success) {
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $error_code = @$resultArr['ErrorMsgId'];
            $result['reason_id'] = $this->getReasons($error_code);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);
    }

    function queryForwardGame($playerName,$extra=null) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $language = $this->getLauncherLanguage($this->language);

        if(isset($this->lobby_url) && !empty($this->lobby_url)) {
            $this->lobby_url = $this->getSystemInfo('lobby_url');
        } else {
            $this->lobby_url = $extra['is_mobile'] == 'true'
                             ? $this->utils->getSystemUrl('m') . $this->getSystemInfo('lobby_url')
                             : $this->utils->getSystemUrl('www') . $this->getSystemInfo('lobby_url');
        }
        $token = $this->login($playerName);
        if($token['success']){

            if($this->force_empty_lobbyurl){
                $this->lobby_url = '';
            }

            $params = array(
                'username' => $gameUsername,
                'token' => $token['Token'],
                'lobby' => $this->lobby_code,
                'lang' => $language,
                'mobile' => (isset($extra["extra"]["game_launch_with_token"]) && $extra["extra"]["game_launch_with_token"]) ? ($extra['is_mobile'] == 1 ? "true" : "false") : $extra['is_mobile'],
                'options' => '',
                'returnurl' => $this->lobby_url,
            );

            $url = $this->game_url . "?" . http_build_query($params);

            # for options param
            $game_launch_options_param = $this->game_launch_options_param;

            if(is_array($game_launch_options_param) && count($game_launch_options_param) > 0){
                $opt = [];
                foreach($game_launch_options_param as $key => $val){
                   $opt[] .= ($key . "=".$val);
                }
                $params['options'] = urlencode(implode(',',$opt));
            }

            return array('url' => $url, 'success' => true);

        }else{
            $return = array("success"=>false, "note"=>"token failed!");
            return $return;
        }

    }


    function blockPlayer($playerName) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->blockUsernameInDB($playerName);
        return array("success" => true);
    }

    function unblockPlayer($playerName) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->unblockUsernameInDB($playerName);
        return array("success" => true);
    }

    /* instead of checking last_sync_id per calling API it will be 2 minutes sleep before calling API so that the 10 calls per 5 minutes restrication
    *  will be avoided it's like on auto sync it will call 2 times every 5 minutes
    * 
    *  This function is usefull for clients that has 2 or more brands with the same creds since they are calling one backoffice URL with 10 calls per 5 minutes
    *  restriction
    */
    function doSleep() {
        if ($this->use_sleep_on_sync) {
            sleep($this->loop_timeout);
            $this->CI->db->_reset_select();
            $this->CI->db->reconnect();
            $this->CI->db->initialize();
        }
        return;
    }

    function syncOriginalGameLogsByLoop($token) {
        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
        $startDate->modify($this->getDatetimeAdjust());

        $startDate = $startDate->format('Y-m-d H:i:s');
        $endDate   = $endDate->format('Y-m-d H:i:s');

        $result = array();
        $result[] = $this->CI->utils->loopDateTimeStartEnd($startDate, $endDate, $this->loop_interval, function($startDate, $endDate)  {
            $this->CI->utils->info_log('<===== SAGAMING ' . $this->loop_timeout .' seconds SLEEP syncOriginalGameLogsByLoop =====>');
            // It will sleep first if there's a setting for sleep in extra info before calling API
            $this->doSleep();

            $startDate = $startDate->format('Y-m-d H:i:s');
            $endDate = $endDate->format('Y-m-d H:i:s');

            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForSyncGameRecords',
                'startDate' => $startDate,
                'endDate' => $endDate
            );

            $cdate = date("YmdHis");

            $params = array(
                'method' => 'GetAllBetDetailsForTimeIntervalDV',
                'Key' => $this->secret_key,
                'Time' => $cdate,
                'FromTime' => $startDate,
                'ToTime' => $endDate
            );
            return $this->callApi(self::API_syncGameRecords, $params, $context);
        });

        return array(true, $result);
    }

    function syncOriginalGameLogs($token = false) {   
        if ($this->sync_by_loop) {
            return $this->syncOriginalGameLogsByLoop($token);
        }
        ## It will sleep first if there's a setting for sleep in extra info before calling API
        $this->doSleep();
        if($this->sync_by_date){
            return $this->syncOriginalGameLogsDaily($token);
        }

        //$sagamingTimeout = $this->utils->getJsonFromCache("SAGAMING-timeout");
        $sagamingTimeout = $this->CI->external_system->getLastSyncId($this->getPlatformCode());
        $currentTime = time();
        $this->CI->utils->debug_log(' SAGAMING TIMEOUT = '.$sagamingTimeout.' ========= CURRENT TIME = '.$currentTime);
       
       if($sagamingTimeout >= $currentTime){
            $this->CI->utils->debug_log('SA GAMING API ==============> skip Syncing due to 1 call per minute restriction.');
            return array("success"=>true,"details"=>"skip Syncing due to 1 call per minute restriction. ");
        }

        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
        $startDate->modify($this->getDatetimeAdjust());

        //observer the date format
        $startDate = $startDate->format('Y-m-d H:i:s');
        $endDate = $endDate->format('Y-m-d H:i:s');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncGameRecords',
            'startDate' => $startDate,
            'endDate' => $endDate
        );

        $cdate = date("YmdHis");

        $params = array(
            'method' => 'GetAllBetDetailsForTimeIntervalDV',
            'Key' => $this->secret_key,
            'Time' => $cdate,
            'Checkkey' => $this->fix_check_key,
            'FromTime' => $startDate,
            'ToTime' => $endDate
        );

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    function processResultForSyncGameRecords($params) {
        $this->CI->load->model('original_game_logs_model');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        $resultArr = json_decode(json_encode($resultXml), true);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array('data_count'=>0);
        $gameRecords = isset($resultArr['BetDetailList']['BetDetail'])?$resultArr['BetDetailList']['BetDetail']:array();



        if(array_key_exists("ErrorMsgId", $resultArr) && $resultArr["ErrorMsgId"]=="112"){
            //$this->utils->saveJsonToCache("SAGAMING-timeout",strtotime("+ 1 minutes"));
            $this->CI->external_system->setLastSyncId($this->getPlatformCode(), strtotime($this->sync_interval));
            $this->CI->utils->debug_log('SA GAMING API ADD timeout ==============> '. $this->sync_interval);
            return array(false,['error'=>"add sleep time"]);
        }

        # for local testing of data only
        // if (!$success) {
        //     $gameRecords = $this->fakeData();
        //     $success = true;
        //     $responseResultId = 1111;
        // }

        if ($success) {
            $this->prepareGameRecords($gameRecords,$responseResultId);

            if ($gameRecords){
                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                    self::ORIGINAL_LOGS_TABLE_NAME,
                    $gameRecords,
                    'external_uniqueid',
                    'external_uniqueid',
                    self::MD5_FIELDS_FOR_ORIGINAL,
                    'md5_sum',
                    'id',
                    self::MD5_FLOAT_AMOUNT_FIELDS
                );
                $this->CI->utils->debug_log('SA Gaming after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));
                $insertRows = json_encode($insertRows);
                unset($gameRecords);
                if (!empty($insertRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
                }
                unset($insertRows);

                if (!empty($updateRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
                }
                unset($updateRows);
            }
            return array($success, $result);
        }
    }

    private function updateOrInsertOriginalGameLogs($data, $queryType){
        $dataCount=0;
        if(!empty($data)){
            if (!is_array($data)) {
                $data = json_decode($data,true);
            }
            if (is_array($data)) {
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
        }

        return $dataCount;
    }

    public function prepareGameRecords(&$gameRecords, $responseResultId){
        if (isset($gameRecords['Username'])){
            $newGameRecords[0] = $gameRecords;
                $gameRecords = [];
                $gameRecords = $newGameRecords;
        }
        $new_gameRecords =array();
        $listOfGameId = array();

            foreach($gameRecords as $index => $record) {
                    $new_gameRecords[$index] = array();
                    $playerID = $this->getPlayerIdInGameProviderAuth(strtolower($record['Username']));
                    $new_gameRecords[$index]['PlayerId']  = $playerID;
                    $new_gameRecords[$index]['BetTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['BetTime'])));
                    $new_gameRecords[$index]['PayoutTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['PayoutTime'])));
                    $new_gameRecords[$index]['Username'] = isset($record['Username'])? $record['Username'] : NULL;
                    $new_gameRecords[$index]['HostID'] = isset($record['HostID'])? $record['HostID'] : NULL;
                    $new_gameRecords[$index]['GameID'] = isset($record['GameID'])? $record['GameID'] : NULL;
                    $new_gameRecords[$index]['Round'] = isset($record['Round'])? $record['Round'] : NULL;
                    $new_gameRecords[$index]['Set'] = isset($record['Set'])? $record['Set'] : NULL;
                    $new_gameRecords[$index]['BetID'] = isset($record['BetID'])? $record['BetID'] : NULL;
                    $new_gameRecords[$index]['BetAmount'] = isset($record['BetAmount'])? $record['BetAmount'] : NULL;
                    $new_gameRecords[$index]['ResultAmount'] = isset($record['ResultAmount'])? $record['ResultAmount'] : NULL;
                    $new_gameRecords[$index]['Balance'] = isset($record['Balance'])? $record['Balance'] : NULL;
                    $new_gameRecords[$index]['GameType'] = isset($record['GameType'])? $record['GameType'] : NULL;
                    $new_gameRecords[$index]['BetType'] = isset($record['BetType'])? $record['BetType'] : NULL;
                    $new_gameRecords[$index]['BetSource'] = isset($record['BetSource'])? $record['BetSource'] : NULL;
                    $new_gameRecords[$index]['State'] = isset($record['State'])? $record['State'] : NULL;
                    $new_gameRecords[$index]['Detail'] = is_array($record['Detail']) ? $this->CI->utils->encodeJson($record['Detail']) : $record['Detail'];
                    $new_gameRecords[$index]['external_uniqueid'] = isset($record['BetID']) ? $record['BetID'] : NULL;
                    $new_gameRecords[$index]['response_result_id'] = $responseResultId;
                    $new_gameRecords[$index]['extra'] = null;
                    $new_gameRecords[$index]['extGameCode'] = null;
                    if($record['GameType'] == "slot" || $record['GameType'] == "minigame" || $record['Detail'] == 'FishermenGold'){
                        $new_gameRecords[$index]['extGameCode'] = isset($record['Detail']) ? $record['Detail'] : NULL;
                    }else{
                        $new_gameRecords[$index]['extGameCode'] = isset($record['HostID']) ? $record['HostID'] : NULL;
                    }
                }
        $gameRecords = $new_gameRecords;
    }

    public function prepareGameRecordExtra($current_record = null, $new_record){

        if(!empty($current_record['extra'])){
            $extra = json_decode($current_record['extra'],true);
        }

        $extra[$new_record['BetID']] = [
            'odds' => null,
            'bet_amount' => $new_record["BetAmount"],
            'win_amount' => ($new_record["ResultAmount"] > 0) ? $new_record["ResultAmount"]:0,
            'place_of_bet' => $new_record["BetType"],
            'after_balance' => $new_record["Balance"],
            'winloss_amount' => $new_record["ResultAmount"],
        ];

        return $extra;
    }

    function syncMergeToGameLogs($token) {
        $enabled_game_logs_unsettle=false;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='`sag`.`PayoutTIme` >= ?
          AND `sag`.`PayoutTIme` <= ? AND sag.PlayerId IS NOT NULL';
        if($use_bet_time){
            $sqlTime='`sag`.`BetTime` >= ?
          AND `sag`.`BetTime` <= ? AND sag.PlayerId IS NOT NULL';
        }

        $sql = <<<EOD
            SELECT
                sag.id as sync_index,
                sag.PlayerId as player_id,
                sag.UserName as username,
                sag.external_uniqueid,
                sag.BetTime as start_at,
                sag.PayoutTime as end_at,
                sag.BetTime as bet_at,
                sag.PayoutTime as updated_at,
                sag.GameType as game_type,
                sag.extGameCode as game_code,
                sag.extGameCode as game_name,
                sag.response_result_id,
                sag.ResultAmount as result_amount,
                sag.BetAmount,
                sag.GameID as round,
                sag.GameID,
                sag.BetID,
                sag.BetType,
                sag.GameType,
                sag.extra,
                sag.md5_sum,
                sag.Balance,
                gd.id as game_description_id,
                gd.game_name as game,
                gd.game_type_id,
                gd.void_bet,
                gt.game_type
            FROM sagaming_game_logs as sag
            LEFT JOIN game_description as gd ON sag.extGameCode = gd.game_code AND gd.void_bet != 1 AND gd.game_platform_id =?
            LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
            WHERE
            {$sqlTime}
EOD;

        $params=[
            $this->getPlatformCode(),
            $dateFrom,
            $dateTo
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
        $extra = [
            'table' =>  $row['round'],
        ];

        if(empty($row['md5_sum'])){
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }
        return [
            'game_info' => [
                'game_type_id'          => $row['game_type_id'],
                'game_description_id'   => $row['game_description_id'],
                'game_code'             => $row['game_code'],
                'game_type'             => $row['GameType'],
                'game'                  => $row['game_code']
            ],
            'player_info' => [
                'player_id'             => $row['player_id']!=null?$row['player_id']:0,
                'player_username'       => $row['username']
            ],
            'amount_info' => [
                'bet_amount'            => $row['valid_bet_amount'],
                'result_amount'         => $row['result_amount'],
                'bet_for_cashback'      => $row['valid_bet_amount'],
                'real_betting_amount'   => $row['real_bet_amount'],
                'win_amount'            => null,
                'loss_amount'           => null,
                'after_balance'         => $row['Balance']
            ],
            'date_info' => [
                'start_at'              => $row['start_at'],
                'end_at'                => $row['end_at'],
                'bet_at'                => $row['bet_at'],
                'updated_at'            => $row['updated_at']
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => Game_logs::STATUS_SETTLED,
            'additional_info' => [
                'has_both_side'         => 0,
                'external_uniqueid'     => $row['external_uniqueid'],
                'round_number'          => $row['round'],
                'md5_sum'               => $row['md5_sum'],
                'response_result_id'    => $row['response_result_id'],
                'sync_index'            => $row['sync_index'],
                'bet_type'              => $row['bet_type']
            ],
            'bet_details' => $row['bet_details'],
            'extra' => [],
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
        ];
    }

    public function preprocessOriginalRowForGameLogs(array &$row){
        if (empty($row['game_description_id'])) {
            $unknownGame = $this->getUnknownGame($this->getPlatformCode());
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }

        if($row['GameType'] != "slot" && $row['GameType'] != "minigames"){
            $bet_details = $this->processBetDetails($row);
            $row['bet_details'] = $bet_details['bet_details'];
            $row['bet_type'] = $bet_details['multibet'] ? 'Combo Bet':'Single Bet';
        } else {
            $row['bet_type'] = "N/A";
            $row['bet_details'] = "N/A";
        }

        $after_balance = $row['Balance'];
        $bet_amount = $row['BetAmount'];
        $valid_bet_amount = $bet_amount;
        $result_amount = $row['result_amount'];
        $real_bet_amount = $bet_amount;
        if ( ! empty($row['extra']) && $row['extra'] != 'null') {
            $extra = json_decode($row['extra'],true);
            # check and remove duplicate betID in extra
            if (isset($extra[$row['BetID']])) {
                unset($extra[$row['BetID']]);
            }

            if (!empty($extra)) {
                $real_bet_amount = $bet_amount;
                if (is_array($extra)) {
                    foreach ($extra as $key => $value) {
                        $real_bet_amount+= $value['bet_amount'];
                    }
                }

                $extra['current_game_id'] = $row['GameID'];
                // prepareValidBetAmount($current_game_type,$current_bet_type,$current_bet_amount,$current_result_amount,$extra){
                list($result_amount, $valid_bet_amount) = $this->prepareValidBetAmount($row['GameType'],$row['BetType'],$bet_amount,$result_amount,$extra);
            }
        }
        $row['real_bet_amount'] = $real_bet_amount;
        $row['result_amount'] = $result_amount;
        $row['valid_bet_amount'] = $valid_bet_amount;
        $row['status'] = Game_logs::STATUS_SETTLED;
    }

    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;
        $external_game_id = $row['game_code'];
        $extra = array('game_code' => $external_game_id,'game_name' => $row['game_name']);

        $game_type_id = $unknownGame->game_type_id ? $unknownGame->game_type_id : null;
        $game_type = $unknownGame->game_name ? $unknownGame->game_name : self::TAG_CODE_UNKNOWN_GAME;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $external_game_id, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

public function prepareValidBetAmount($current_game_type,$current_bet_type,$current_bet_amount,$current_result_amount,$extra){
        $list_of_opposite_bet_types_per_game_type = [
            "bac" => [
                'player_banker' =>[1,2],
            ],
            "dtx"=> [
                "dragon_tiger" => [1,2],
            ],
            "sicbo"=> [
                "small_big" => [0,1],
                "odd_even" => [2,3],
            ],
            "rot" =>[
                "odd_even"=>[152,153],
                "small_big"=>[150,151],
                "red_black"=>[154,155],
            ],
        ];

        $list_of_opposite_bet_type_keys = [
            'rot' => [152,153,150,151,154,155],
            'bac' => [1,2],
            'sicbo' => [0,1,2,3],
            'dtx' => [1,2],
        ];

        $bet_type_map=[
            'bac' => [],
            'rot' => [],
            'sicbo' => [],
            'dtx' => [],
            'ftan' => [],
        ];

        $list_of_valid_bet_amount = [];
        $valid_bet_amount = 0;
        $result_amount = 0;

        #merge the data from original game logs to extra
        $extra[$extra['current_game_id']] = [
            "bet_amount" => $current_bet_amount,
            "place_of_bet" => $current_bet_type,
            "winloss_amount" => $current_result_amount,
        ];
        unset($extra['current_game_id']);
        #end

        if(is_array($extra)){
            foreach ($extra as $key => $game_record) {

                #merge the result and bet amount if player bet twice on same place ex: ("player" or "banker")
                if(isset($list_of_current_bet_type[$current_game_type][$game_record['place_of_bet']])){

                    $bet_amount = $list_of_current_bet_type[$current_game_type][$game_record['place_of_bet']]['bet_amount'] + $game_record['bet_amount'];
                    $this_result_amount = $list_of_current_bet_type[$current_game_type][$game_record['place_of_bet']]['result_amount'] + $game_record['winloss_amount'];

                    $list_of_current_bet_type[$current_game_type][$game_record['place_of_bet']] = [
                        "bet_amount" => $bet_amount,
                        "result_amount" => $this_result_amount,
                    ];

                }else{
                     $list_of_current_bet_type[$current_game_type][$game_record['place_of_bet']] = [
                        "bet_amount" => $game_record['bet_amount'],
                        "result_amount" => $game_record['winloss_amount'],
                    ];

                }
            }
        }

        #put data to map
        if ( ! empty($list_of_opposite_bet_types_per_game_type[$current_game_type])) {
            $list_non_opposite_bets = [];
            foreach ($list_of_opposite_bet_types_per_game_type[$current_game_type] as $bet_type_key => $opposite_bets) {
                foreach ($list_of_current_bet_type[$current_game_type] as $place_of_bet_key => $game_record) {
                    #check if current game type is exist
                    if ( ! empty($list_of_opposite_bet_type_keys[$current_game_type])) {
                        #opposite bets
                        if (in_array($place_of_bet_key, $list_of_opposite_bet_type_keys[$current_game_type])) {

                            #put the bet details in bet type map per opposite bet type
                            if(in_array($place_of_bet_key, $opposite_bets, true)){
                                $bet_type_map[$current_game_type][$bet_type_key][$place_of_bet_key]= $game_record;
                            }
                        }

                        #non opposite bets
                        if ( ! in_array($place_of_bet_key, $list_of_opposite_bet_type_keys[$current_game_type])) {
                            if ( ! in_array($place_of_bet_key, $list_non_opposite_bets)) {
                                $valid_bet_amount += $game_record['bet_amount'];
                                $result_amount += $game_record['result_amount'];
                                array_push($list_non_opposite_bets, $place_of_bet_key);
                            }
                        }

                    }else{
                        #calculation for non opposite bet
                        $valid_bet_amount += $game_record['bet_amount'];
                        $result_amount += $game_record['result_amount'];
                    }
                }
            }
        } else {
            #if current game type is not exist
            #calculate bet amount and result amount for non opposite bet
            foreach ($list_of_current_bet_type[$current_game_type] as $place_of_bet_key => $game_record) {
                 $valid_bet_amount += $game_record['bet_amount'];
                 $result_amount += $game_record['result_amount'];
            }
        }

        #prepare the valid bet amount per opposite bet type
        foreach ($bet_type_map[$current_game_type] as $opposite_bet_name => $current_bet_map) {

            #always clear the data
            $bet_map = [];

            foreach ($current_bet_map as $key => $current_map) {
                #check if there's a multiple bets
                if(!empty($bet_map['bet_amount'])){

                    #use result amount when bet amount are equal
                    if($bet_map['bet_amount'] == $current_map['bet_amount']){
                        $current_valid_bet_amount = abs($bet_map['result_amount']+$current_map['result_amount']);
                    } else {
                        $current_valid_bet_amount = abs($bet_map['bet_amount']-$current_map['bet_amount']);
                    }

                    #replace initialized the values
                    #always add the result amount and bet amount
                    if ($bet_map['bet_amount'] == 0) {
                        $current_valid_bet_amount = $current_map['bet_amount'];
                    }

                    $current_result_amount = $bet_map['result_amount'] + $current_map['result_amount'];

                }else{

                    #for single row only
                    $bet_map = [
                        'bet_amount' => $current_map['bet_amount'],
                        'result_amount' => $current_map['result_amount'],
                    ];

                    #initialize the values
                    $current_result_amount = $current_map['result_amount'];
                    $current_valid_bet_amount  = $current_map['bet_amount'];
                }

                $list_of_valid_bet_amount[$opposite_bet_name]['result_amount'] = $current_result_amount;
                $list_of_valid_bet_amount[$opposite_bet_name]['valid_bet_amount'] = $current_valid_bet_amount;
            }
        }

        #finalize the result for valid bet amount and result amount
        if( ! empty($list_of_valid_bet_amount)){
            foreach ($list_of_valid_bet_amount as $key => $value) {
                $valid_bet_amount+=$value['valid_bet_amount'];
                $result_amount+=$value['result_amount'];
            }
        }
        // print_r(array($result_amount, $valid_bet_amount));exit;
        return array($result_amount, $valid_bet_amount);

    }

    public function getGameBetDetails($gameType,$betType){
        $game_type_details = [
           'bac' => [ #baccarat
                '0' => 'Tie', '1' => 'Player', '2' => 'Banker', '3' => 'Player Pair', '4' => 'Banker Pair', '5' => 'Player Point Odd', '6' => 'Banker Point Odd', '7' => 'Total Point Odd', '8' => 'Player Point Even', '9' => 'Banker Point Even', '10' => 'Total Point Even', '11' => 'Player Point Small', '12' => 'Banker Point Small', '13' => 'Total Point Small', '14' => 'Player Point Big', '15' => 'Banker Point Big', '16' => 'Total Point Big', '17' => 'Player Card Small', '18' => 'Banker Card Small', '19' => 'Total Card Small', '20' => 'Player Card Big', '21' => 'Banker Card Big', '22' => 'Total Card Big', '23' => 'Player Dragon', '24' => 'Banker Dragon', '25' => 'SuperSix Tie', '26' => 'SuperSix Player Win', '27' => 'SuperSix Banker Win', '28' => 'SuperSix Player Pair', '29' => 'SuperSix Banker Pair', '30' => 'SuperSix', '31' => 'Super Baccarat Tie', '32' => 'Super Baccarat Player Win', '33' => 'Super Baccarat Banker Win', '34' => 'Super Baccarat Player Pair', '35' => 'Super Baccarat Banker Pair', '36' => 'Player Natural', '37' => 'Banker Natural', '38' => 'Super Baccarat Player Natural', '39' => 'Super Baccarat Banker Natural', '40' => 'SuperSix Player Natura', '41' => 'SuperSix Banker Natural', '42' => 'Cow Cow Player', '43' => 'Cow Cow Banker', '44' => 'Cow Cow Tie', '53' => 'NC.LuckySix', '54' => 'LuckySix',
            ],
            'dtx' => [ #dragon tiger
                '0' => 'Tie', '1' => 'Dragon', '2' => 'Tiger',
            ],
            'rot' => [ #roullete
                '0~36' => '0~36', '37' => '0,1 ', '38' => '0,2 ', '39' => '0,3 ', '40' => '1,2 ', '41' => '1,4 ', '42' => '2,3 ', '43' => '2,5 ', '44' => '3,6 ', '45' => '4,5 ', '46' => '4,7 ', '47' => '5,6 ', '48' => '5,8 ', '49' => '6,9 ', '50' => '7,8 ', '51' => '7,10 ', '52' => '8,9 ', '53' => '8,11 ', '54' => '9,12 ', '55' => '10,11 ', '56' => '10,13 ', '57' => '11,12 ', '58' => '11,14 ', '59' => '12,15 ', '60' => '13,14 ', '61' => '13,16 ', '62' => '14,15 ', '63' => '14,17 ', '64' => '15,18 ', '65' => '16,17 ', '66' => '16,19 ', '67' => '17,18 ', '68' => '17,20 ', '69' => '18,21 ', '70' => '19,20 ', '71' => '19,22 ', '72' => '20,21 ', '73' => '20,23 ', '74' => '21,24 ', '75' => '22,23 ', '76' => '22,25 ', '77' => '23,24 ', '78' => '23,26 ', '79' => '24,27 ', '80' => '25,26 ', '81' => '25,28 ', '82' => '26,27 ', '83' => '26,29 ', '84' => '27,30 ', '85' => '28.29 ', '86' => '28,31 ', '87' => '29,30 ', '88' => '29,32 ', '89' => '30,33 ', '90' => '31,32 ', '91' => '31,34 ', '92' => '32,33 ', '93' => '32,35 ', '94' => '33,36 ', '95' => '34,35 ', '96' => '35,36', '97' => '0,1,2', '98' => '0,2,3', '99' => '1,2,3', '100' => '4,5,6', '101' => '7,8,9', '102' => '10,11,121', '103' => '13,14,15', '104' => '16,17,18', '105' => '19,20,21', '106' => '22,23,24', '107' => '25,26,27', '108' => '28,29,30', '109' => '31,32,33', '110' => '34,35,36', '111' => '1,2,4,5', '112' => '2,3,5,6', '113' => '4,5,7,8', '114' => '5,6,8,9', '115' => '7,8,10,11', '116' => '8,9,11,12', '117' => '10,11,13,14', '118' => '11,12,14,15', '119' => '13,14,16,17', '120' => '14,15,17,18', '121' => '16,17,19,20', '122' => '17,18,20,21', '123' => '19,20,22,23', '124' => '20,21,23,24', '125' => '22,23,25,26', '126' => '23,24,26,27', '127' => '25,26,28,29', '128' => '26,27,29,30', '129' => '28,29,31,32', '130' => '29,30,32,33', '131' => '31,32,34,35', '132' => '32,33,35,36', '133' => '1,2,3,4,5,6', '134' => '4,5,6,7,8,9', '135' => '7,8,9,10,11,12', '136' => '10,11,12,13,14,15', '137' => '13,14,15,16,17,18', '138' => '16,17,18,19,20,21', '139' => '19,20,21,22,23,24', '140' => '22,23,24,25,26,27', '141' => '25,26,27,28,29,30', '142' => '28,29,30,31,32,33', '143' => '31,32,33,34,35,36', '144' => '1st 12 (1~12)', '145' => '2nd 12 (13~24)', '146' => '3rd 12 (25~36)', '147' => '1st Row (1~34)', '148' => '2nd Row (2~35)', '149' => '3rd Row (3~36)', '150' => '1~18 (Small)', '151' => '19~36 (Big)', '152' => 'Odd', '153' => 'Even', '154' => 'Red', '155' => 'Black', '156' => '0,1,2,3',
            ],
            'sicbo' => [ #sicbo
                '0' => 'Small', '1' => 'Big ', '2' => 'Odd ', '3' => 'Even ', '4' => 'Number 1 ', '5' => 'Number 2 ', '6' => 'Number 3 ', '7' => 'Number 4 ', '8' => 'Number 5 ', '9' => 'Number 6 ', '10' => 'All 1 ', '11' => 'All 2 ', '12' => 'All 3 ', '13' => 'All 4 ', '14' => 'All 5', '15' => 'All 6', '16' => 'All same', '17' => 'Point 4', '18' => 'Point 5', '19' => 'Point 6', '20' => 'Point 7', '21' => 'Point 8', '22' => 'Point 9', '23' => 'Point 10', '24' => 'Point 11', '25' => 'Point 12', '26' => 'Point 13', '27' => 'Point 14', '28' => 'Point 15', '29' => 'Point 16', '30' => 'Point 17', '31' => 'Point 1 and 2', '32' => 'Point 1 and 3', '33' => 'Point 1 and 4', '34' => 'Point 1 and 5', '35' => 'Point 1 and 6', '36' => 'Point 2 and 3', '37' => 'Point 2 and 4', '38' => 'Point 2 and 5', '39' => 'Point 2 and 6', '40' => 'Point 3 and 4', '41' => 'Point 3 and 5', '42' => 'Point 3 and 6', '43' => 'Point 4 and 5', '44' => 'Point 4 and 6', '45' => 'Point 5 and 6', '46' => 'Point 1', '47' => 'Point 2', '48' => 'Point 3', '49' => 'Point 4', '50' => 'Point 5', '51' => 'Point 6', '52' => 'Three Odd', '53' => 'Two Odd One Even', '54' => 'Two Even One Odd', '56' => '1 2 3 4', '57' => '2 3 4 5', '58' => '2 3 5 6', '59' => '3 4 5 6', '60' => '112', '61' => '113', '62' => '114', '63' => '115', '64' => '116', '65' => '221', '66' => '223', '67' => '224', '68' => '225', '69' => '226', '70' => '331', '71' => '332', '72' => '334', '73' => '335', '74' => '336', '75' => '441', '76' => '442', '77' => '443', '78' => '445', '79' => '446', '80' => '551', '81' => '552', '82' => '553', '83' => '554', '84' => '556', '85' => '661', '86' => '662', '87' => '663', '88' => '664', '89' => '665', '90' => '126', '91' => '135', '92' => '234', '93' => '256', '94' => '346', '95' => '123', '96' => '136', '97' => '145', '98' => '235', '99' => '356', '100' => '124', '101' => '146', '102' => '236', '103' => '245', '104' => '456', '105' => '125', '106' => '134', '107' => '156', '108' => '246', '109' => '345',
            ],
            'ftan' => [ #fan tan
                '0' => 'Odd','1' => 'Even','2' => '1 Zheng','3' => '2 Zheng','4' => '3 Zheng','5' => '4 Zheng','6' => '1 Fan','7' => '2 Fan','8' => '3 Fan','9' => '4 Fan','10' => '1 Nim 2','11' => '1 Nim 3','12' => '1 Nim 4','13' => '2 Nim 1','14' => '2 Nim 3','15' => '2 Nim 4','16' => '3 Nim 1','17' => '3 Nim 2','18' => '3 Nim 4','19' => '4 Nim 1','20' => '4 Nim 2','21' => '4 Nim 3','22' => '12 Kwok','23' => '14 Kwok','24' => '23 Kwok','25' => '34 Kwok','26' => '1 Tong 23','27' => '1 Tong 24','28' => '1 Tong 34','29' => '2 Tong 13','30' => '2 Tong 14','31' => '2 Tong 34','32' => '3 Tong 12','33' => '3 Tong 14','34' => '3 Tong 24','35' => '4 Tong 12','36' => '4 Tong 13','37' => '4 Tong 23','38' => '123 Chun','39' => '124 Chun','40' => '134 Chun','41' => '234 Chun',
            ],
            'lottery' => [ #lottery
                '0' => 'Single (6 numbers)', '1' => 'Multiple (> 6 numbers)', '2' => 'Banker', '3' => 'Extra number 1', '4' => 'Extra number 2', '5' => 'Extra number 3', '6' => 'Extra number 4', '7' => 'Extra number 5', '8' => 'Extra number 6', '9' => 'Extra
                number 7', '10' => 'Extra number 8', '11' => 'Extra number 9', '12' => 'Extra number 10', '13' => 'Extra number 11', '14' => 'Extra number 12', '15' => 'Extra number 13', '16' => 'Extra number 14', '17' => 'Extra number 15', '18' => 'Extra number
                16', '19' => 'Extra number 17', '20' => 'Extra number 18', '21' => 'Extra number 19', '22' => 'Extra number 20', '23' => 'Extra number 21', '24' => 'Extra number 22', '25' => 'Extra number 23', '26' => 'Extra number 24', '27' => 'Extra number
                25', '28' => 'Extra number 26', '29' => 'Extra number 27', '30' => 'Extra number 28', '31' => 'Extra number 29', '32' => 'Extra number 30', '33' => 'Extra number 31', '34' => 'Extra number 32', '35' => 'Extra number 33', '36' => 'Extra number
                34', '37' => 'Extra number 35', '38' => 'Extra number 36', '39' => 'Extra number 37', '40' => 'Extra number 38', '41' => 'Extra number 39', '42' => 'Extra number 40', '43' => 'Extra number 41', '44' => 'Extra number 42', '45' => 'Extra number
                43', '46' => 'Extra number 44', '47' => 'Extra number 45', '48' => 'Extra number 46', '49' => 'Extra number 47', '50' => 'Extra number 48', '51' => 'Extra number Odd', '52' => 'Extra number Even', '53' => 'Extra number Big', '54' => 'Extra number
                Small', '55' => 'Extra number Red', '56' => 'Extra number Blue', '57' => 'Extra number Green',
            ]
        ];

        if(isset($gameType) && isset($betType)){

            if ($gameType == 'rot') {
                if( ! in_array($betType, self::LIST_OF_ROULLETE_KEYS)){
                    return $betType;
                }else{
                    return isset($game_type_details[$gameType][$betType]) ? $game_type_details[$gameType][$betType]: null;
                }
            }else{
                return isset($game_type_details[$gameType][$betType]) ? $game_type_details[$gameType][$betType]: null;
            }

        }else{
            return null;
        }
    }

    public function processBetDetails($gameRecord){
        switch ($gameRecord['GameType']) {
            case 'bac':
                $gameTypeName = lang("baccarat");
                break;
            case 'lottery':
                $gameTypeName = lang("Lottery");
                break;
            case 'ftan':
                $gameTypeName = lang("Fan Tan");
                break;
            case 'sicbo':
                $gameTypeName = lang("sicbo");
                break;
            case 'rot':
                $gameTypeName = lang("rouletteWheel");
                break;
            case 'dtx':
                $gameTypeName = lang("dragonTiger");
                break;
            default :
            break;
        }

        $bet_placed_name = $this->getGameBetDetails($gameRecord['GameType'],$gameRecord['BetType']);

        $is_multibet = false;
        $bet_detail = array();
        if(!empty($gameRecord['extra'])){
            $extra = json_decode($gameRecord['extra'],true);
            $is_multibet = true;
            if(!empty($extra)){
                if (is_array($extra)) {
                    foreach ($extra as $key => $data) {
                        #for in extra data
                        $bet_detail['bet_details'][$key] = [
                            "odds" => null,
                            "win_amount" => $data['win_amount'],
                            "bet_amount" =>  $data['bet_amount'],
                            "bet_placed" => $this->getGameBetDetails($gameRecord['GameType'],$data['place_of_bet']),
                            "won_side" => null,
                            "winloss_amount" => $data['winloss_amount'],
                        ];
                    }
                }
            }

            #for current data
            $bet_detail['bet_details'][$gameRecord['external_uniqueid']] = [
                "odds" => null,
                "win_amount" => ($gameRecord['result_amount'] > 0) ? $gameRecord['result_amount']:0,
                "bet_amount" =>  $gameRecord['BetAmount'],
                "bet_placed" => $this->getGameBetDetails($gameRecord['GameType'],$gameRecord['BetType']),
                "won_side" => null,
                "winloss_amount" => $gameRecord['result_amount'],
            ];

        }else{
             $bet_detail['bet_details'][$gameRecord['external_uniqueid']] = [
                "odds" => null,
                "win_amount" => ($gameRecord['result_amount'] > 0) ? $gameRecord['result_amount']:0,
                "bet_amount" =>  $gameRecord['BetAmount'],
                "bet_placed" => $this->getGameBetDetails($gameRecord['GameType'],$gameRecord['BetType']),
                "won_side" => null,
                "winloss_amount" => $gameRecord['result_amount'],
            ];
        }

        $bet_details = array(
            "bet_details" => $bet_detail,
            "multibet" => $is_multibet,
        );

        return $bet_details;

    }

    function syncOriginalGameLogsDaily($token = false) {
        $sagamingTimeout = $this->utils->getJsonFromCache("SAGAMING-timeout");
        if($sagamingTimeout >= time()){
            $this->CI->utils->debug_log('SA GAMING API ==============> skip Syncing due to 1 call per minute restriction.');
            return array("success"=>true,"details"=>"skip Syncing due to 1 call per minute restriction. ");
        }

        $date = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');

        $date = new DateTime($this->serverTimeToGameTime($date->format('Y-m-d H:i:s')));

        //observer the date format
        $date = $date->format('Y-m-d');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncGameRecordsDaily',
            'date' => $date,
        );

        $cdate = date("YmdHis");

        $params = array(
            'method' => 'GetAllBetDetailsDV',
            'Key' => $this->secret_key,
            'Time' => $cdate,
            'Checkkey' => $this->fix_check_key,
            'Date' => $date,#optional
            // 'Username' => "kgvtt6367", optional
        );

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    function processResultForSyncGameRecordsDaily($params) {
        $this->CI->load->model('original_game_logs_model');
        $resultArr = json_decode(json_encode($this->getResultXmlFromParams($params)),true);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        if(isset($resultArr["ErrorMsgId"])&& @$resultArr["ErrorMsgId"]=="112"){
            $this->utils->saveJsonToCache("SAGAMING-timeout",strtotime($this->sync_interval));
            $this->CI->utils->debug_log('SA GAMING API ADD timeout ==============> '.$this->sync_interval);
            return array(false, $resultArr);
        }

        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array('data_count'=>0);
        $gameRecords = isset($resultArr['BetDetailList']['BetDetail'])?$resultArr['BetDetailList']['BetDetail']:array();

        if ($success) {

            $this->prepareGameRecords($gameRecords,$responseResultId);

            if ($gameRecords){
                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                    self::ORIGINAL_LOGS_TABLE_NAME,
                    $gameRecords,
                    'external_uniqueid',
                    'external_uniqueid',
                    self::MD5_FIELDS_FOR_ORIGINAL,
                    'md5_sum',
                    'id',
                    self::MD5_FLOAT_AMOUNT_FIELDS
                );
                $this->CI->utils->debug_log('SA Gaming after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));
                $insertRows = json_encode($insertRows);
                unset($gameRecords);
                if (!empty($insertRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
                }
                unset($insertRows);

                if (!empty($updateRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
                }
                unset($updateRows);
            }
            return array($success,array('count'=>$result['data_count']));
        }

    }

    function changePassword($playerName, $oldPassword = null, $newPassword) {
        return $this->returnUnimplemented();
    }

    public function queryTransaction($transactionId, $extra) {

        $playerName=$extra['playerName'];
        $playerId=$extra['playerId'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $cdate = date("YmdHis");

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'gameUsername' => $gameUsername,
            'external_transaction_id' => $transactionId,
            'playerId'=>$playerId,
        );

        $params = array(
            'method' => 'CheckOrderId',
            'Key' => $this->secret_key,
            'Time' => $cdate,
            'OrderId ' => $transactionId
        );

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction( $params ){
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = (array)$this->getResultXmlFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if($success){
            $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {
            $error_code = @$resultArr['ErrorMsgId'];
            switch($error_code) {
                case '102' :
                    $result['reason_id']=self::REASON_INVALID_KEY;
                    break;
                case '106' :
                case '124' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    break;
            }
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

    /**
     * overview : get game time to server time
     *
     * @return string
     */
    // function getGameTimeToServerTime() {
        //return '+8 hours';
    // }

    /**
     * overview : get server time to game time
     *
     * @return string
     */
    // function getServerTimeToGameTime() {
        // return '-8 hours';
    // }

    // private function fakeData() {
    //     return [
    //         0 => [
    //             'BetTime' => '2019-03-05T00:00:00',
    //             'PayoutTime' => '2019-03-05T00:01:02',
    //             'Username' => 'testt1dev',
    //             'HostID' => 'DemoUser001',
    //             'Detail' => '',
    //             'GameID' => '1234567890123456',
    //             'Round' => '10',
    //             'Set' => '23',
    //             'BetID' => '1234567890',
    //             'BetAmount' => '123.45',
    //             'Balance' => '434456.35',
    //             'ResultAmount' => '246.90',
    //             'GameType' => 'bac',
    //             'BetType' => '123',
    //             'BetSource' => '2',
    //             'State' => 'True',
    //         ],
    //         1 => [
    //             'BetTime' => '2019-03-05T00:00:00',
    //             'PayoutTime' => '2019-03-05T00:01:02',
    //             'Username' => 'testt1dev',
    //             'HostID' => 'DemoUser001',
    //             'Detail' => '',
    //             'GameID' => '1234567890123456',
    //             'Round' => '10',
    //             'Set' => '23',
    //             'BetID' => '1234567891',
    //             'BetAmount' => '123.46',
    //             'Balance' => '434456.35',
    //             'ResultAmount' => '246.90',
    //             'GameType' => 'bac',
    //             'BetType' => '123',
    //             'BetSource' => '2',
    //             'State' => 'True',
    //         ],

    //         2 => [
    //             'BetTime' => '2019-03-05T00:00:00',
    //             'PayoutTime' => '2019-03-05T00:01:02',
    //             'Username' => 'testt1dev',
    //             'HostID' => 'DemoUser001',
    //             'Detail' => '',
    //             'GameID' => '1234567890123456',
    //             'Round' => '10',
    //             'Set' => '23',
    //             'BetID' => '1234567892',
    //             'BetAmount' => '789123.47',
    //             'Balance' => '434456.35',
    //             'ResultAmount' => '246.90',
    //             'GameType' => 'bac',
    //             'BetType' => '123',
    //             'BetSource' => '2',
    //             'State' => 'True',
    //         ]
    //     ];
    // }
}

/*end of file*/
