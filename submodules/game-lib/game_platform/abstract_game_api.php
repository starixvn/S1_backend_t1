<?php
require_once dirname(__FILE__) . '/game_api_interface.php';
require_once dirname(__FILE__) . '/../ProxySoapClient.php';

/**
 *
 * Abstract Adapter of Game
 *
 * General behaviors include:
 *
 * * Create player
 * * Exists Player
 * * Reset Player
 * * Change password
 * * Query Player Balance
 *
 * * Deposit/Withdraw
 * * Query Transaction
 * * Block/Unblock/IsBlocked
 * * Generate Game Launcher: url or form or domain
 * * Callback
 * * Login/Logout
 * * Check Login Status on Game
 * * Sync Game Logs: syncOriginalGameLogs => syncMergeToGameLogs
 *
 *
 * extra_info:
 *
 * ```json
 *	{
 *	...
 *	}
 * ```
 *
 * @see gotogame_module.php
 * @see Sync_game_records
 *
 * @category Game API
 * @version 2.8.10 new ftp structure
 * @copyright 2013-2016 TripleOneTech
 */
abstract class Abstract_game_api implements Game_api_interface {

    protected $api_config;
    public $syncInfo = array();

    //default is true
    public $enabled_guess_success_for_curl_errno_on_this_api;
    public $guess_success_curl_errno;
    public $is_update_original_row;
    public $is_update_game_logs;
    // public $is_timeout_mock;

    public $use_unique_id_as_username;
    public $call_socks5_proxy;
    public $call_socks5_proxy_login;
    public $call_socks5_proxy_password;

    public $call_http_proxy_host;
    public $call_http_proxy_port;
    public $call_http_proxy_login;
    public $call_http_proxy_password;

    public $ignore_ssl_verify;

    public $transfer_retry_times;
    public $common_retry_times;
    public $common_wait_seconds;

    public $status_map;
    //default is 1 hour
    public $api_token_timeout_on_cache_seconds=3600;

    public $unknownGameDescription=null;

    public $ignored_0_on_refresh = TRUE;

    //running platform
    const PLATFORM_UNKNOWN=1;
    const PLATFORM_PC_FLASH=2;
    const PLATFORM_PC_H5=3;
    const PLATFORM_PC_DOWNLOAD=4;
    const PLATFORM_MOBILE_H5_UNKNOWN=5;
    const PLATFORM_MOBILE_H5_IOS=6;
    const PLATFORM_MOBILE_H5_ANDROID=7;
    const PLATFORM_MOBILE_APP_UNKNOWN=8;
    const PLATFORM_MOBILE_APP_IOS=9;
    const PLATFORM_MOBILE_APP_ANDROID=10;

    // const COMMON_TRANSACTION_STATUS_SETTLED='settled';
    const COMMON_TRANSACTION_STATUS_APPROVED='approved';
    const COMMON_TRANSACTION_STATUS_DECLINED='declined';
    const COMMON_TRANSACTION_STATUS_PROCESSING='processing';
    const COMMON_TRANSACTION_STATUS_UNKNOWN='unknown';

    const DEFAULT_TRANSFER_RETRY_TIMES=3;
    const DEFAULT_COMMON_RETRY_TIMES=3;
    const DEFAULT_COMMON_WAIT_SECOND_TIME=2;

    const DEFAULT_TRANSFER_MIN_AMOUNT = 0.01;
    const DEFAULT_TRANSFER_MAX_AMOUNT = 'unlimited';

    const DEFAULT_IGNORED_0_ON_REFRESH = TRUE;

    const DEFAULT_GUESS_SUCCESS_CODE=[CURLE_OPERATION_TIMEOUTED , CURLE_OUT_OF_MEMORY, CURLE_READ_ERROR,
        CURLE_WRITE_ERROR, CURLE_SEND_ERROR, CURLE_RECV_ERROR];

    const TIMEOUT_MOCK_FOR_API=[self::API_depositToGame, self::API_withdrawFromGame];

    const OTHER_STATUS_SHOULD_BE_DECLINED='{_other_}';
    const TAG_CODE_UNKNOWN_GAME     = 'unknown';

    const DEFAULT_PRECISION = 2;

    // const BET_TYPE_MULTI_BET='Multi Bet';
    // const BET_TYPE_SINGLE_BET='Single Bet';

    public function getDefaultTransactionStatusApproved(){
        return '{_unknown_approved_status_}';
    }

    public function getDefaultTransactionStatusDeclined(){
        return '{_unknown_declined_status_}';
    }

    public function __construct() {
        $this->CI = &get_instance();
        $this->PLATFORM_CODE = $this->getPlatformCode();
        $this->SYSTEM_TYPE_ID = $this->getPlatformCode();

        $this->CI->config->set_item('app_debug_log', APPPATH . 'logs/game_api.log');
        $this->utils = $this->CI->utils;
        $this->CI->load->model(['game_logs', 'player_model']);

        $this->loadSystemInfo();

        $this->use_unique_id_as_username = $this->getSystemInfo('use_unique_id_as_username');
        //always update original, don't use available rows
        $this->is_update_original_row = $this->getSystemInfo('is_update_original_row', false);
        //useless, don't use anymore
        $this->is_update_game_logs = $this->getSystemInfo('is_update_game_logs', true);

        //it could be set it on extra_info, default value is true
        $this->enabled_guess_success_for_curl_errno_on_this_api= $this->getSystemInfo('enabled_guess_success_for_curl_errno_on_this_api', true);

        //it's about what's code will be converted to success
        $this->guess_success_curl_errno = $this->getSystemInfo('guess_success_curl_errno', self::DEFAULT_GUESS_SUCCESS_CODE);
        //proxy for http and soap call
        $this->call_socks5_proxy = $this->getSystemInfo('call_socks5_proxy');
        $this->call_socks5_proxy_login = $this->getSystemInfo('call_socks5_proxy_login');
        $this->call_socks5_proxy_password = $this->getSystemInfo('call_socks5_proxy_password');

        $this->call_http_proxy_host = $this->getSystemInfo('call_http_proxy_host');
        $this->call_http_proxy_port = $this->getSystemInfo('call_http_proxy_port');
        $this->call_http_proxy_login = $this->getSystemInfo('call_http_proxy_login');
        $this->call_http_proxy_password = $this->getSystemInfo('call_http_proxy_password');

        $this->ignore_ssl_verify = $this->getSystemInfo('ignore_ssl_verify', true);

        // required_login_on_launching_trial default as false
        $this->required_login_on_launching_trial = $this->getSystemInfo('required_login_on_launching_trial', false);

        //for test timeout
        $this->is_timeout_mock = $this->getSystemInfo('is_timeout_mock', false);
        //it will forward to white domain, like pt or opus
        $this->enabled_forward_white_domain = $this->getSystemInfo('enabled_forward_white_domain', false);
        //white launcher domain
        $this->white_launcher_domain = $this->getSystemInfo('white_launcher_domain');

        $this->transfer_retry_times=$this->getSystemInfo('transfer_retry_times', self::DEFAULT_TRANSFER_RETRY_TIMES);

        $this->common_retry_times=$this->getSystemInfo('common_retry_times', self::DEFAULT_COMMON_RETRY_TIMES);

        $this->common_wait_seconds=$this->getSystemInfo('common_wait_seconds', self::DEFAULT_COMMON_WAIT_SECOND_TIME);

        $this->transfer_min_amount=$this->getSystemInfo('transfer_min_amount', self::DEFAULT_TRANSFER_MIN_AMOUNT);
        $this->transfer_max_amount=$this->getSystemInfo('transfer_max_amount', self::DEFAULT_TRANSFER_MAX_AMOUNT);
        $this->ignored_0_on_refresh=!!$this->getSystemInfo('ignored_0_on_refresh', static::DEFAULT_IGNORED_0_ON_REFRESH);

        $this->round_transfer_amount =$this->getSystemInfo('round_transfer_amount', false); // add control, so that other game won't affect
        $this->decimal_places_count=$this->getSystemInfo('decimal_places_count',self::DEFAULT_PRECISION);

        $this->mock_settings=$this->getSystemInfo('mock_settings', [
            'let_transfer_timeout'=>false,
            'let_withdraw_exceed_max'=>false,
            'mock_transfer_exceed_available_balance'=>PHP_INT_MAX,
            'timeout_mock_for_api'=>self::TIMEOUT_MOCK_FOR_API,
            'timeout_mock_only_player_id'=>null,
        ]);

        $this->transaction_status_approved= $this->getSystemInfo('transaction_status_approved', $this->getDefaultTransactionStatusApproved());
        $this->transaction_status_declined= $this->getSystemInfo('transaction_status_declined', $this->getDefaultTransactionStatusDeclined());

        $this->status_map=[
            $this->transaction_status_approved => self::COMMON_TRANSACTION_STATUS_APPROVED,
            $this->transaction_status_declined => self::COMMON_TRANSACTION_STATUS_DECLINED,
        ];

        // force testing notif
        $this->test_notif_player = $this->getSystemInfo('test_notif_player');
        $this->test_notif_error_code = $this->getSystemInfo('test_notif_error_code');

        $this->force_to_http = $this->getSystemInfo('force_to_http', false);

        $this->is_enabled_direct_launcher_url=$this->getSystemInfo('is_enabled_direct_launcher_url', true);

        $this->api_token_timeout_on_cache_seconds=$this->getSystemInfo('api_token_timeout_on_cache_seconds', 3600);
        $this->enable_rake_commission = $this->getSystemInfo('enable_rake_commission', false);

        $this->fix_username_limit = $this->getSystemInfo('fix_username_limit', false);

        $this->max_rows_of_bet_details=$this->getSystemInfo('max_rows_of_bet_details', 400);
        $this->float_round=$this->getSystemInfo('float_round', 4);
        $this->deposit_max_balance_on_auto_transfer = $this->getSystemInfo('deposit_max_balance_on_auto_transfer', false);

    }

    public function loadSystemInfo() {
        $this->CI->load->model('external_system');
        $systemInfo = $this->CI->external_system->getSystemById($this->getPlatformCode());

        if(empty($systemInfo)){
            $this->CI->utils->error_log('extra info is empty, system platform code', $this->getPlatformCode());
            // return;
        }

        # based on whether it's live_mode, provide corresponding extra_info field. Use live field by default.
        $extraInfoJson = (!isset($systemInfo->live_mode) || $systemInfo->live_mode) ? $systemInfo->extra_info : $systemInfo->sandbox_extra_info;
        $extraInfo = json_decode($extraInfoJson, true) ?: array();
        if (empty($extraInfo)) {
            $this->CI->utils->error_log('game platform id', $this->getPlatformCode(), 'wrong extra info', $extraInfoJson);
        }
        $this->SYSTEM_INFO = array_merge(((array) $systemInfo), $extraInfo);
        // $this->CI->utils->debug_log('extraInfo', $extraInfo, 'extraInfoJson', $extraInfoJson);

        # Determine other variables for sandbox/live
        $varNames = array('url', 'key', 'secret', 'account');
        foreach ($varNames as $aName) {
            $arrKey = (!isset($this->SYSTEM_INFO['live_mode']) || $this->SYSTEM_INFO['live_mode'] ? 'live' : 'sandbox') . '_' . $aName;
            $this->SYSTEM_INFO[$aName] = array_key_exists($arrKey, $this->SYSTEM_INFO) ? $this->SYSTEM_INFO[$arrKey] : "";
        }

        $this->SYSTEM_INFO['_system_code']=$systemInfo->system_code;
        // $this->CI->utils->debug_log($this->SYSTEM_INFO);
    }

    public function getAllSystemInfo(){
        return $this->SYSTEM_INFO;
    }

    protected $SYSTEM_TYPE_ID;
    protected $PLATFORM_CODE;
    public $SYSTEM_INFO;

    const UNKNOWN_GAME_ID = 'unknown';

    const TRANS_STATUS_APPROVED = 'approved';
    const TRANS_STATUS_DECLINED = 'declined';
    const TRANS_STATUS_PROCESS = 'process';

    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;

    const CALL_TYPE_HTTP = 1;
    const CALL_TYPE_SOAP = 2;
    const CALL_TYPE_XMLRPC = 3;

    const ERROR_CONTENT = 1000;

    const API_createPlayer = "createPlayer";
    const API_queryPlayerInfo = "queryPlayerInfo";
    const API_getPassword = "getPassword";
    const API_changePassword = "changePassword";
    const API_isBlocked = "isBlocked";
    const API_blockPlayer = "blockPlayer";
    const API_unblockPlayer = "unblockPlayer";
    const API_depositToGame = "depositToGame";
    const API_withdrawFromGame = "withdrawFromGame";
    const API_login = "login";
    const API_logout = "logout";
    const API_updatePlayerInfo = "updatePlayerInfo";
    const API_queryPlayerBalance = "queryPlayerBalance";
    const API_queryPlayerDailyBalance = "queryPlayerDailyBalance";
    const API_queryGameRecords = "queryGameRecords";
    const API_checkLoginStatus = "checkLoginStatus";
    const API_totalBettingAmount = "totalBettingAmount";
    const API_queryTransaction = "queryTransaction";
    const API_queryForwardGame = "queryForwardGame";
    const API_syncGameRecords = "syncGameRecords";
    const API_syncBalance = "syncBalance";
    const API_syncLostAndFound = "syncLostAndFound";
    const API_isPlayerExist = "isPlayerExist";
    const API_isPlayerOnline = "isPlayerOnline";
    const API_checkLoginToken = "checkLoginToken";
    const API_batchQueryPlayerBalance = "batchQueryPlayerBalance";
    const API_resetPlayer = "resetPlayer";
    const API_syncGameRecordsByPlayer = "syncGameRecordsByPlayer";
    const API_operatorLogin = "operatorLogin";
    const API_revertBrokenGame = "revertBrokenGame";
    const API_checkFundTransfer = "checkFundTransfer";
    const API_transfer = "transfer";
    const API_setMemberBetSetting = "setMemberBetSetting";
    const API_queryBetDetailLink = 'queryBetDetailLink';
    const API_generateToken = "generateToken";
    const API_createBackOfficeUser = "createBackOfficeUser";
    const API_getGameProviderGamelist = "getGameProviderGamelist";
    const API_queryGameListFromGameProvider = "queryGameListFromGameProvider";
    const API_playerBet = "playerBet";
    const API_queryGameResult='queryGameResult';
    const API_syncIncompleteGames='syncIncompleteGames';
    const API_queryIncompleteGames='queryIncompleteGames';
    const API_createPlayerGameSession = 'createPlayerGameSession';
    const API_queryPlayerCurrency = 'queryPlayerCurrency';

    const INT_LANG_ENGLISH = '1';
    const INT_LANG_CHINESE = '2';
    const INT_LANG_INDONESIAN = '3';
    const INT_LANG_VIETNAMESE = '4';
    const INT_LANG_KOREAN = '5';
    const INT_LANG_THAI = '6';

    /**
     * @return string , code from constants.php
     */
    abstract public function getPlatformCode();

    public function getConfig($key) {
        return $this->CI->utils->getConfig($key);
    }

    # Note: the 'live_mode' value determines whether it will return live_X or sandbox_X value.
    public function getSystemInfo($key, $defaultValue='') {
        return isset($this->SYSTEM_INFO[$key]) ? $this->SYSTEM_INFO[$key] : $defaultValue;
    }

    public function getMinSizeUsername() {
        return $this->getSystemInfo('min_size_username') ?: $this->getConfig('default_min_size_username');
    }

    public function getMaxSizeUsername() {
        return $this->getSystemInfo('max_size_username') ?: $this->getConfig('default_max_size_username');
    }

    public function getMinSizePassword() {
        return $this->getSystemInfo('min_size_password') ?: $this->getConfig('default_min_size_password');
    }

    public function getMaxSizePassword() {
        return $this->getSystemInfo('max_size_password') ?: $this->getConfig('default_max_size_password');
    }

    protected function getFirstResultFromObject($rltObj, $propName = null) {
        if ($rltObj) {
            $rltArr = $rltObj;
            if (!empty($propName)) {
                $rltArr = $rltObj->$propName;
            }
            // if (is_array($rltArr)) {
            foreach ($rltArr as $rlt) {
                return $rlt;
                break;
            }
            // }
        }
        return null;
    }

    protected function getResponseResultIdFromParams($params) {
        return $this->getValueFromParams($params, 'responseResultId');
    }

    protected function getResultJsonFromParams($params) {
        return $this->convertResultJsonFromParams($params);
    }

    protected function getResultObjFromParams($params) {
        return $this->getValueFromParams($params, 'resultObj');
    }

    protected function getResultXmlFromParams($params) {
        return $this->convertResultXmlFromParams($params);
    }

    protected function getResultTextFromParams($params) {
        return $this->getValueFromParams($params, 'resultText');
    }

    protected function getStatusCodeFromParams($params) {
        return $this->getValueFromParams($params, 'statusCode');
    }

    protected function getValueFromParams($params, $paramName) {
        return isset($params[$paramName]) ? $params[$paramName] : null;
    }

    protected function getVariableFromContext($params, $varName, $defaultValue=null) {
        $context = $params['context'];
        return isset($context[$varName]) ? $context[$varName] : $defaultValue;
    }

    protected function getParamValueFromParams($params, $paramName) {
        $paramsForApi = $params['params'];
        return @$paramsForApi[$paramName];
    }

    protected function convertResultJsonFromParams($params) {
        $resultText = @$params['resultText'];
        $resultJson = null;
        if (!empty($resultText)) {
            $resultJson = json_decode($resultText, true);
        }
        return $resultJson;
    }

    protected function convertResultXmlFromParams($params) {
        $resultText = @$params['resultText'];
        $resultXml = null;
        if (!empty($resultText)) {
            try{
                $resultXml = new SimpleXMLElement($resultText, LIBXML_NOERROR);
            }catch(Exception $e){
                $resultXml=null;
                $this->CI->utils->error_log('convert xml failed', $e->getMessage());
            }
        }
        return $resultXml;
    }

    protected function convertResultCsvFromParams($csv, $row_delimiter = "\n", $column_delimiter = ",", $escape = "\\") {
        $data = str_getcsv(trim($csv),$row_delimiter);
        $data = array_map(function($a) use ($column_delimiter, $escape) {
            return str_getcsv(trim($a), $column_delimiter, $escape);
        }, $data);
        $headears = array_shift($data); # remove column header
        array_walk($data, function(&$a) use ($headears) {
          $a = array_combine($headears, $a);
        });
        return $data;
    }

    protected function convertResultXmlToSimpleXmlLoadStringFromParams($params) {
        $xmlArr = null;
        $xmlData = @$params['resultText'];
        if (!empty($xmlData)) {
            $xml = simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA);
            $xmlJson = json_encode($xml);
            $xmlArr = json_decode($xmlJson, 1);
        }
        return $xmlArr;
    }

    public function getPrepareData() {
        return null;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        return $this->createPlayerInDB($playerName, $playerId, $password, $email, $extra);
    }

    public function createPlayerInDB($playerName, $playerId, $password, $email = null, $extra = null) {

        //write to db
        $this->CI->load->model(array('game_provider_auth', 'player_model', 'agency_model'));

        $row = $this->CI->game_provider_auth->getByPlayerIdGamePlatformId($playerId, $this->getPlatformCode());
        if (empty($row)) {
            //convert username, not right name
            // $playerName = $this->convertUsernameToGame($playerName);

            $source = Game_provider_auth::SOURCE_REGISTER;
            if ($extra && array_key_exists('source', $extra) && $extra['source']) {
                $source = $extra['source'];
            }

            $is_demo_flag = false;
            if ($extra && array_key_exists('is_demo_flag', $extra) && $extra['is_demo_flag']) {
                $is_demo_flag = $extra['is_demo_flag'];
            }

            $player = (array) $this->CI->player_model->getPlayerById($playerId);

            $result = $this->CI->game_provider_auth->savePasswordForPlayer(
                array(
                    'username' => $playerName,
                    "id" => $playerId,
                    "password" => $password,
                    "source" => $source,
                    "is_demo_flag" => $is_demo_flag,
                    "agent_id" => @$player['agent_id'],
                    "sma_id" => (array_key_exists("root_agent_id",$player)) ? $player['root_agent_id'] : NULL
                    // "sma_id" => (!isset($player->root_agent_id)) ?: NULL
                ),
                $this->getPlatformCode(), $extra);
        } else {
            $result = true;
        }

        return $result;
    }

    public function setGameAccountRegistered($playerId){
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->setRegisterFlag($playerId, $this->getPlatformCode(),
            Game_provider_auth::DB_TRUE);
    }

    public function updatePasswordForPlayer($playerId, $password) {
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->updatePasswordForPlayer($playerId, $password, $this->getPlatformCode());

    }

    public function updateUsernameForPlayer($playerId, $username) {
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->updateUsernameForPlayer($playerId, $username, $this->getPlatformCode());

    }

    public function updatePasswordByPlayerName($gameUsername, $password) {
        //get player id first
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->updatePasswordForPlayer($playerId, $password, $this->getPlatformCode());
    }

    public function updateExternalAccountIdForPlayer($playerId, $externalAccountId) {
        //get data from db
        $this->CI->load->model('game_provider_auth');

        $data['external_account_id'] = $externalAccountId;
        $platformCode = $this->getPlatformCode();

        $this->CI->game_provider_auth->updateExternalAccountIdForPlayer($playerId, $platformCode, $data);
    }

    public function updateRegisterFlag($playerId, $register, $isDemoFlag = false) {
        //get data from db
        $this->CI->load->model('game_provider_auth');

        if ($isDemoFlag) {
            $data['is_demo_flag'] = true;
        }
        $data['register'] = $register;
        $platformCode = $this->getPlatformCode();

        $this->CI->game_provider_auth->updateRegisterFlag($playerId, $platformCode, $data);
    }

    public function getGameRecords($dataFrom, $dateTo, $playerName, $platformCode) {
        //get data from db
        $this->CI->load->model('game_logs');

        return $this->CI->game_logs->queryGameRecords($dataFrom, $dateTo, $playerName, $platformCode);
    }

    public function getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
        //get data from db
        $this->CI->load->model('daily_balance');

        return $this->CI->daily_balance->queryPlayerDailyBalance($playerName, $playerId, $dateFrom, $dateTo);
    }

    public function getPasswordByGameUsername($gameUsername) {

        $this->CI->load->model('game_provider_auth');
        $password = $this->CI->game_provider_auth->getPasswordByLoginName($gameUsername, $this->getPlatformCode());
        return $password;
    }

    public function getPassword($playerName) {
        //convert to game username
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        //get password from db
        $this->CI->load->model('game_provider_auth');
        $password = $this->CI->game_provider_auth->getPasswordByLoginName($gameUsername, $this->getPlatformCode());

        $this->CI->utils->debug_log('GameUsername ====================================================>', $gameUsername, 'password', $password);

        return $this->returnResult(true, "password", $password);
    }

    public function isGameAccountDemoAccount($gameName) {
        $this->CI->load->model('game_provider_auth');
        $is_demo_flag = $this->CI->game_provider_auth->isGameAccountDemoAccount($gameName, $this->getPlatformCode());
        return $this->returnResult(true, "is_demo_flag", $is_demo_flag);
    }

    //playerName from table 'player'
    public function getPasswordString($playerName) {
        $rlt = $this->getPassword($playerName);
        if ($rlt && $rlt["success"] && !empty($rlt['password'])) {
            return $rlt['password'];
        }
        return '';
    }
    public function getPasswordFromPlayer($playerName) {
        $this->CI->load->model('player_model');
        return $this->CI->player_model->getPasswordByUsername($playerName);
    }
    public function syncGamePassword($gameUsername) {

        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

        $this->utils->debug_log('game username', $gameUsername, 'playerId', $playerId);
        if (!empty($playerId)) {
            $this->CI->load->model('player_model');
            $password = $this->CI->player_model->getPasswordById($playerId);
            $this->utils->debug_log('password', $password, 'playerId', $playerId);
            if (!empty($password)) {
                //update password
                return !!$this->updatePasswordForPlayer($playerId, $password);
            }
        }

        return false;

    }

    abstract public function depositToGame($playerName, $amount, $transfer_secure_id = null);
    abstract public function withdrawFromGame($playerName, $amount, $transfer_secure_id = null);
    abstract public function queryPlayerBalance($playerName);

    public function changePassword($playerName, $oldPassword, $newPassword){
        return ['success'=>$this->changePasswordInDB($playerName, $newPassword)];
    }
    public function changePasswordInDB($playerName, $newPassword){
        $playerId=$this->getPlayerIdInPlayer($playerName);
        return $this->updatePasswordForPlayer($playerId, $newPassword);
    }
    public function login($playerName, $password = null){
        return $this->returnUnimplemented();
    }
    public function logout($playerName, $password = null){
        return $this->returnUnimplemented();
    }
    public function queryPlayerInfo($playerName) {
        return $this->returnUnimplemented();
    }
    public function updatePlayerInfo($playerName, $infos) {
        return $this->returnUnimplemented();
    }
    public function queryGameListFromGameProvider($extra=null){
        return $this->returnUnimplemented();
    }

    public function formatAmountBeforeTransfer($amount) {
        if($this->onlyTransferPositiveInteger()){
            $amount=intval($amount);
        } else {
            if($this->round_transfer_amount) {
                $amount=round($amount, $this->decimal_places_count);
            }
        }
        return $amount;
    }

    /**
     *
     * @param  string $transactionId
     * @param  array $extra ['playerName'=>$playerName, 'playerId'=>$playerId];
     * @return array result
     */
    abstract public function queryTransaction($transactionId, $extra);

    abstract public function queryForwardGame($playerName, $extra);
    public function generateGotoUri($playerName, $extra){
        return $this->returnUnimplemented();
    }

    public function getCurrentProtocol(){
        $current_protocol= $this->CI->utils->isHttps() ? 'https' : 'http' ;
        if($this->force_to_http){
            $current_protocol = "http";
        }
        return $current_protocol;
    }

    public function getCurrentDomain(){
        return $this->CI->utils->getHttpHost();
    }

    public function stripCurrentSubdomain(){
        return $this->CI->utils->stripSubdomain($this->getCurrentDomain());
    }

    public function forwardToWhiteDomain($playerName, $nextUrl){

        $success=$this->enabled_forward_white_domain && !empty($this->white_launcher_domain);

        $this->CI->utils->debug_log('forwardToWhiteDomain, enabled_forward_white_domain', $this->enabled_forward_white_domain,
            'white_launcher_domain', $this->white_launcher_domain, 'playerName', $playerName, 'nextUrl', $nextUrl);

        $forward_url=null;
        if($success){

            if($this->white_launcher_domain!=$this->getCurrentDomain()){

                $this->CI->load->model(array('player_model'));

                $playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
                $token=$this->getPlayerToken($playerId);

                $forward_url=$this->getCurrentProtocol().'://'.$this->white_launcher_domain.'/iframe/auth/login_with_token/'.$token.'?next='.urlencode($nextUrl);
                $this->CI->utils->debug_log($this->getPlatformCode().' forward to', $forward_url);
                // redirect($forward_url);

            }else{
                $this->CI->utils->debug_log($this->getPlatformCode().' same white domain');
                $success = false;
            }
        }else{
            $this->CI->utils->debug_log($this->getPlatformCode().' donot use white domain');
        }

        return ['success'=>$success, 'forward_url'=> $forward_url];
    }

    public function isPlayerExist($playerName) {
        return ['success'=>true, 'exists'=>$this->isPlayerExistInDB($playerName)];
    }

    public function isPlayerExistInDB($playerName){
        $this->CI->load->model(array('player_model', 'game_provider_auth'));
        $exists=false;

        $playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
        $platformId = $this->getPlatformCode();

        if ($this->CI->game_provider_auth->isRegisterd($playerId, $platformId)) {
            $exists = true;
        } else {
            $exists = false;
        }
        return $exists;
    }

    public function updatePlayerSubwalletBalance($playerId, $balance) {

        $this->CI->load->model(array('wallet_model', 'game_provider_auth'));
        $systemId = $this->getPlatformCode();
        $self = $this;
        $this->CI->wallet_model->lockAndTransForPlayerBalance($playerId, function ()
        use ($self, $playerId, $systemId, $balance) {
            // $this->CI->utils->debug_log('updatePlayerSubwalletBalance', $playerId, $balance);
            // if ($this->CI->game_provider_auth->isEnabledSyncBalance($systemId, $playerId)) {
            $self->CI->wallet_model->refreshSubWalletOnBigWallet($playerId, $systemId, $balance);
            //also update daily balance, very slow
            // $this->CI->daily_balance->syncBalanceInfo($playerId, $systemId, $this->CI->utils->getTodayForMysql(), $balance);
            // }
            //update flag too
            $self->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            //update history
            // log_message('info', 'updatePlayerSubwalletBalance done', ['playerId'=>$playerId,'systemId'=>$systemId, 'balance'=>$balance]);
            return true;
        });
    }

    public function updatePlayerSubwalletBalanceWithoutLock($playerId, $balance) {
        // $this->CI->utils->debug_log('updatePlayerSubwalletBalance', $playerId, $balance);
        $systemId = $this->getPlatformCode();
        $this->CI->load->model(array('wallet_model', 'game_provider_auth'));
        // if ($this->CI->game_provider_auth->isEnabledSyncBalance($systemId, $playerId)) {
        $success = $this->CI->wallet_model->refreshSubWalletOnBigWallet($playerId, $systemId, $balance);
        //also update daily balance, very slow
        // $this->CI->daily_balance->syncBalanceInfo($playerId, $systemId, $this->CI->utils->getTodayForMysql(), $balance);
        // }
        //update flag too
        if($success) {
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        }
        //update history
        return $success;
    }

     /**
     * only for setting blocked or old Game api  subwallet to zero
     *
     */
     public function setPlayerOldBlockedApiSubwalletToZeroWithLock($playerId, $balance) {

        $this->CI->load->model(array('wallet_model', 'game_provider_auth'));
        $systemId = $this->getPlatformCode();
        $self = $this;
        $this->CI->wallet_model->lockAndTransForPlayerBalance($playerId, function ()
            use ($self, $playerId, $systemId, $balance) {
                $self->CI->wallet_model->updateSubWalletBalanceToZeroOnBigWallet($playerId, $systemId, $balance);
                return true;
            });
    }

    /**
     * only for sync function
     *
     */
    public function putValueToSyncInfo($token, $key, $value) {
        $this->syncInfo[$token][$key] = $value;
    }
    /**
     * only for sync function
     *
     */
    public function getValueFromSyncInfo($token, $key) {
        $info = isset($this->syncInfo[$token]) ? @$this->syncInfo[$token] : array();

        if($this->getPlatformCode() == SPORTSBOOK_API) { // initialize key if empty
            if (!array_key_exists("external_ids",$info)) {
                $info['external_ids'] = new \stdClass;
            }
        }

        if (array_key_exists($key, $info)) {
            return @$info[$key];
        }
        return null;
    }

    public function existsValueFromSyncInfo($token, $key) {
        $info = isset($this->syncInfo[$token]) ? @$this->syncInfo[$token] : array();
        return array_key_exists($key, $info);
    }

    /**
     *
     *
     * @param DateTime dateTimeFrom
     * @param DateTime dateTimeTo
     *
     *
     */
    public function syncGameRecords($dateTimeFrom, $dateTimeTo, $playerName = null, $gameName = null) {
        //clear syncInfo
        // $this->syncInfo = array();
        //create token for this task
        $token = random_string('unique');
        $this->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
        // $this->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo, "playerName" => $playerName, "gameName" => $gameName);
        $this->syncOriginalGameLogs($token);
        $this->syncConvertResultToDB($token);
        $this->syncMergeToGameLogs($token);
        $this->syncTotalStats($token);

        $this->clearSyncInfo($token);
        // $this->syncInfo = null;
        // $this->syncInfo[$token] = null;
    }

    public function saveSyncInfoByToken($token, $dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $gameName = null, $syncId = null, $extra = null) {
        // $this->syncInfo = array();
        $this->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo,
            "playerName" => $playerName, "gameName" => $gameName, 'syncId' => $syncId);

        if (!empty($extra) && is_array($extra)) {
            $this->syncInfo[$token] = array_merge($this->syncInfo[$token], $extra);
        }

        $this->CI->utils->info_log('print token content', $this->syncInfo[$token]);
    }

    public function clearSyncInfo($token) {
        // $this->syncInfo[$token] = null;
        unset($this->syncInfo[$token]);
        // $this->syncInfo = null;
    }

    public function updateToExternalSystem($data) {
        $this->CI->load->model('external_system');
        return $this->CI->external_system->updateRecord($data);
    }

    public function saveToTransactions($data) {
        $this->CI->load->model('transactions');
        return $this->CI->transactions->addRecord($data);
    }

    public function getPlayerIdInPlayer($playerUsername) {
        // $this->CI->utils->debug_log('Abstract getPlayerIdInPlayer playerUsername: ', $playerUsername);
        if (!empty($playerUsername)) {
            $this->CI->load->model('player_model');
            return $this->CI->player_model->getPlayerIdByUsername($playerUsername);
        }
        return null;
    }

    public function getPlayerIdInGameProviderAuth($gameUsername) {
        if (!empty($gameUsername)) {
            $this->CI->load->model('game_provider_auth');
            return $this->CI->game_provider_auth->getPlayerIdByPlayerName($gameUsername, $this->getPlatformCode());
        }
        return null;
    }

    public function getPlayerIdByExternalAccountId($externalAccountId) {
        if (!empty($externalAccountId)) {
            $this->CI->load->model('game_provider_auth');
            return $this->CI->game_provider_auth->getPlayerIdByExternalAccountId($externalAccountId, $this->getPlatformCode());
        }
        return null;
    }

    public function getGameUsernameByPlayerUsername($playerUsername) {
        if (!empty($playerUsername)) {
            $this->CI->load->model('game_provider_auth');
            $gameUsername = $this->CI->game_provider_auth->getGameUsernameByPlayerUsername($playerUsername, $this->getPlatformCode());
            if (empty($gameUsername)) {
                //sync db, fix game_provider_auth
                //get player info from player

                $this->CI->load->model('player_model');
                $player = $this->CI->player_model->getPlayerByUsername($playerUsername);
                if (!empty($player)) {
                    $gameUsername = $this->convertUsernameToGame($player->username);
                    $this->CI->load->library('salt');
                    $decryptedPwd = $this->CI->salt->decrypt($player->password, $this->getConfig('DESKEY_OG'));

                    $this->createPlayerInDB($gameUsername, $player->playerId, $decryptedPwd);
                }
            }

            return $gameUsername;
        }
        return null;
    }

    public function getPlayerUsernameByGameUsername($gameUsername){

        if (!empty($gameUsername)) {
            $this->CI->load->model('game_provider_auth');
            $playerUsername=$this->CI->game_provider_auth->getPlayerUsernameByGameUsername($gameUsername, $this->getPlatformCode());

            return $playerUsername;
        }
        return null;

    }

    public function getPlayerSecureIdByPlayerUsername($playerUsername) {
        if (!empty($playerUsername)) {
            $this->CI->load->model('player_model');
            return $this->CI->player_model->getSecureIdByPlayerUsername($playerUsername);
        }
        return null;
    }

    public function getPlayerPlayerIdBySecureId($secureId) {
        if (!empty($secureId)) {
            $this->CI->load->model('player_model');
            return $this->CI->player_model->getPlayerIdBySecureId($secureId);
        }
        return null;
    }

    public function getGameUsernameByPlayerId($playerId) {
        if (!empty($playerId)) {
            $this->CI->load->model('game_provider_auth');
            $gameUsername = $this->CI->game_provider_auth->getGameUsernameByPlayerId($playerId, $this->getPlatformCode());

            if (empty($gameUsername)) {
                //sync db, fix game_provider_auth
                //get player info from player

                $this->CI->load->model('player_model');
                $player = $this->CI->player_model->getPlayerById($playerId);
                if (!empty($player)) {
                    $gameUsername = $this->convertUsernameToGame($player->username);
                    $this->CI->load->library('salt');
                    $decryptedPwd = $this->CI->salt->decrypt($player->password, $this->getConfig('DESKEY_OG'));

                    $this->createPlayerInDB($gameUsername, $player->playerId, $decryptedPwd);
                }
            }
            return $gameUsername;
        }
        return null;
    }

    public function getExternalAccountIdByPlayerUsername($playerUsername) {
        if (!empty($playerUsername)) {
            $this->CI->load->model('game_provider_auth');
            return $this->CI->game_provider_auth->getExternalAccountIdByPlayerUsername($playerUsername, $this->getPlatformCode());
        }
        return null;
    }

    public function getPlayerInfo($playerId) {
        $this->CI->load->model('player_model');
        return $this->CI->player_model->getPlayerById($playerId);
    }

    public function getPlayerGamePasswordPrefix() {
        return null;
    }

    public function getPlayerDetails($playerId) {
        $this->CI->load->model('player_model');
        return $this->CI->player_model->getPlayerDetailsById($playerId);
    }

    /**
     * download game logs to local file and save to response results
     *
     * @param string token to get syncInfo
     */
    abstract public function syncOriginalGameLogs($token);
    /**
     * convert original result to db
     *
     * @param string token to get syncInfo
     */
    public function syncConvertResultToDB($token) {
        return $this->returnUnimplemented();
    }
    /**
     * merge to game logs
     *
     * @param string token to get syncInfo
     */
    abstract public function syncMergeToGameLogs($token);
    /**
     * total stats
     *
     * @param string token to get syncInfo
     */
    public function syncTotalStats($token) {
        $rlt = array('success'=>true);
        //sync balance
        $rlt['syncBalanceInGameLog'] = $this->syncBalanceInGameLog($token);
        //total game_logs in minute
        $rlt['syncTotalPlayerGameMinute'] = $this->syncTotalPlayerGameMinute($token);
        //total game_logs
        $rlt['syncTotalPlayerGameHour'] = $this->syncTotalPlayerGameHour($token);
        //hour to day
        $rlt['syncTotalPlayerGameDay'] = $this->syncTotalPlayerGameDay($token);
        //day to month
        // $this->syncTotalPlayerGameMonth($token);
        //month to year
        // $this->syncTotalPlayerGameYear($token);
        //player minute to operator minute
        // $this->syncOperatorGameMinute($token);
        //player hour to operator hour
        // $this->syncOperatorGameHour($token);
        //hour to day
        // $this->syncOperatorGameDay($token);
        //day to month
        // $this->syncOperatorGameMonth($token);
        //month to year
        // $this->syncOperatorGameYear($token);
        //sync to daily balance, very slow
        // $this->syncDailyBalance($token);
        //sync total betting amount for player
        // $rlt['syncPlayerInfo'] = $this->syncPlayerInfo($token);

        return $rlt;
    }

    public function syncLongTotalStats($token) {
        $rlt = array('success'=>true);
        //hour to day
        // $this->syncTotalPlayerGameDay($token);
        //day to month
        $this->syncTotalPlayerGameMonth($token);
        //month to year
        $this->syncTotalPlayerGameYear($token);

        //hour to day
        // $this->syncOperatorGameDay($token);
        //day to month
        // $this->syncOperatorGameMonth($token);
        //month to year
        // $this->syncOperatorGameYear($token);

        return $rlt;
    }

    public function convertGameLogsToSubWallet($token) {

        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);

        $playerId = $this->getPlayerIdInGameProviderAuth($playerName);

        $from = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $to = $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $dateTimeFrom = null;
        $dateTimeTo = null;
        if ($from) {
            $dateTimeFrom = $from;
        }
        if ($to) {
            $dateTimeTo = $to;
        }

        // $dateTimeFrom, $dateTimeTo, $gamePlatformId, $playerId = null
        // $apiArray = $this->utils->getApiListByBalanceInGameLog();

        // if (!empty($apiArray)) {

        $gamePlatformId = $this->getPlatformCode();
        $this->CI->load->model(array('game_logs'));
        // }
        $success = $this->CI->game_logs->convertGameLogsToSubWallet($gamePlatformId, $playerId, $dateTimeFrom, $dateTimeTo);

        return array('success' => $success);
    }

    public function syncBalanceInGameLog($token) {
        //only sync balance
        $apiArray = $this->CI->utils->getApiListByBalanceInGameLog();
        if (!empty($apiArray)) {

            $this->utils->debug_log('try sync balance in game log', $apiArray);

            //only sync today
            $from = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
            if ($from == null || $from->format('Y-m-d') != $this->CI->utils->getTodayForMysql()) {
                $this->CI->utils->debug_log('ignore not today', $from);
                return array('success' => true);
            }

            foreach ($apiArray as $apiId) {
                $api = $this->CI->utils->loadExternalSystemLibObject($apiId);
                if ($api) {
                    $api->convertGameLogsToSubWallet($token);
                }
            }
        }

        return array('success' => true);
    }

    public function syncPlayerInfo($token) {
        //update player.totalBettingAmount
        $this->CI->load->model(array('player_model', 'group_level'));
        $success = $this->CI->player_model->updateAllPlayersTotalBettingAmount();
        // $this->CI->player_model->updateAllPlayersTotalDepositAmount();
        //player.refereeEnabledStatus, update wallet
        // $this->CI->player_model->updateAllPlayersReferee();
        //TODO playeraccount cashbackwallet, all cashback - last cashback
        // $this->CI->group_level->totalCashbackDaily();
        //TODO withdraw_conditions

        return array('success' => $success);
    }

    // const DEFAULT_DATETIME_ADJUST = '-10 minutes';

    // public function syncDailyBalance($token) {
    //     //game_logs to
    //     $this->CI->load->model(array('daily_balance'));
    //     $playerName = $this->getValueFromSyncInfo($token, 'playerName');
    //     $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
    //     $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
    //     $playerId = $this->getPlayerIdInPlayer($playerName);

    //     $this->CI->daily_balance->convertGameLogsToDailyBalance($this->CI->utils->formatDateTimeForMysql($dateTimeFrom),
    //         $this->CI->utils->formatDateTimeForMysql($dateTimeTo), $playerId);

    //     $this->CI->daily_balance->updateWalletFromDailyBalance($playerId);
    // }

    public function syncTotalPlayerGameMinute($token) {
        $this->CI->load->model(array('game_logs', 'total_player_game_minute'));
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $dateTimeFrom->modify($this->getDatetimeAdjustSyncTotal());
        $mark = 'total_player_game_minute';
        $this->CI->utils->markProfilerStart($mark);

        $this->CI->total_player_game_minute->startTrans();

        $playerId = null;
        if (!empty($playerName)) {
            $playerId = $this->getPlayerIdInPlayer($playerName);
        }

        $count = $this->CI->total_player_game_minute->sync($dateTimeFrom, $dateTimeTo, $playerId);

        $this->CI->utils->debug_log('syncTotalPlayerGameMinute dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $count);

        $success = $this->CI->total_player_game_minute->endTransWithSucc();

        $this->CI->utils->markProfilerEndAndPrint($mark);

        return $success;
    }

    public function syncTotalPlayerGameHour($token) {
        $this->CI->load->model(array('game_logs', 'total_player_game_hour'));
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $dateTimeFrom->modify($this->getDatetimeAdjustSyncTotal());

        // $dateTime = new DateTime();

        // $fromStr = $dateTimeFrom->format('Y-m-d H') . ':00:00';
        // $toStr = $dateTimeTo->format('Y-m-d H') . ':59:59';

        // $this->CI->utils->debug_log('dateTimeFrom', $fromStr, 'dateTimeTo', $toStr);
        $mark = 'total_player_game_hour';
        $this->CI->utils->markProfilerStart($mark);

        $this->CI->total_player_game_hour->startTrans();

        $playerId = null;
        if (!empty($playerName)) {
            $playerId = $this->getPlayerIdInPlayer($playerName);
        }

        $count = $this->CI->total_player_game_hour->sync($dateTimeFrom, $dateTimeTo, $playerId);

        $this->CI->utils->debug_log('syncTotalPlayerGameHour dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $count);

        $success = $this->CI->total_player_game_hour->endTransWithSucc();

        $this->CI->utils->markProfilerEndAndPrint($mark);

        return $success;

        // $gamelogs = null;
        // if (empty($playerName)) {
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfAllPlayer($dateTimeFrom, $dateTimeTo);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfAllPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfAllPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // 	// } else {

        // 	//just for current hour and last hour
        // 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfAllPlayer($fromStr, $toStr);

        // 	// }

        // } else {
        // 	$playerId = $this->getPlayerIdInPlayer($playerName);
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();

        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfPlayer($dateTimeFrom, $dateTimeTo, $playerName);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'), $playerName);
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo, $playerName);
        // 	// } else {
        // 	$gamelogs = $this->CI->game_logs->getAllRecordPerHourOfPlayer($fromStr, $toStr, $playerId);
        // 	// }

        // }
        // if (!empty($gamelogs)) {
        // 	$updated_at = $this->CI->utils->getNowForMysql(); //(new \DateTime)->format('Y-m-d H:i:s');

        // 	$cnt = count($gamelogs);
        // 	$sum = 0;
        // 	foreach ($gamelogs as $key) {
        // 		$sum += $key->betting_amount;
        // 		$data['player_id'] = $key->player_id;
        // 		$data['betting_amount'] = $key->betting_amount;
        // 		$data['result_amount'] = $key->result_amount;
        // 		$data['hour'] = $key->hour;
        // 		$data['date'] = $key->game_date;
        // 		$data['updated_at'] = $updated_at;
        // 		$data['game_platform_id'] = $key->game_platform_id;
        // 		$data['game_type_id'] = $key->game_type_id;
        // 		$data['game_description_id'] = $key->game_description_id;
        // 		$data['uniqueid'] = $key->player_id . '_' . $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $key->game_date . '_' . $key->hour;

        // 		// if ($data['player_id'] == '16832') {
        // 		// $this->CI->utils->debug_log($data);
        // 		// }
        // 		$this->CI->total_player_game_hour->syncToTotalPlayerGameHour($data);
        // 	}
        // 	$this->CI->utils->debug_log('syncTotalPlayerGameHour', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $cnt, 'sum', $sum);
        // }
    }

    private function syncTotalPlayerGameDay($token) {
        $this->CI->load->model(array('total_player_game_day'));
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $dateTimeFrom->modify($this->getDatetimeAdjustSyncTotal());

        $mark = 'total_player_game_day';
        $this->CI->utils->markProfilerStart($mark);

        $this->CI->total_player_game_day->startTrans();
        $playerId = null;
        if (!empty($playerName)) {
            $playerId = $this->getPlayerIdInPlayer($playerName);
        }

        $count = $this->CI->total_player_game_day->sync($dateTimeFrom, $dateTimeTo, $playerId);

        $this->CI->utils->debug_log('syncTotalPlayerGameDay dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $count);

        $success = $this->CI->total_player_game_day->endTransWithSucc();

        $this->CI->utils->markProfilerEndAndPrint($mark);

        return $success;

        // $gamelogs = null;
        // if (!$playerName) {
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_hour->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_hour->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom, $dateTimeTo);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_hour->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_hour->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom->format('Y-m-d'), $dateTimeTo->format('Y-m-d'));
        // 	// }

        // } else {
        // 	// $player = $this->getPlayerId($playerName);
        // 	$playerId = $this->getPlayerIdInPlayer($playerName);
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_hour->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_hour->getLastRecordDateTime();

        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom, $dateTimeTo, $playerId);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_hour->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'), $playerId);
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_hour->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo, $playerId);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_hour->getAllRecordPerDayOfAllPlayer($dateTimeFrom->format('Y-m-d'), $dateTimeTo->format('Y-m-d'), $playerId);
        // 	// }

        // }
        // if (!empty($gamelogs)) {
        // 	$updated_at = (new \DateTime)->format('Y-m-d H:i:s');

        // 	$cnt = count($gamelogs);
        // 	$sum = 0;

        // 	foreach ($gamelogs as $key) {
        // 		$sum += $key->betting_amount;

        // 		$data['player_id'] = $key->player_id;
        // 		$data['betting_amount'] = $key->betting_amount;
        // 		$data['result_amount'] = $key->result_amount;
        // 		$data['date'] = $key->date;
        // 		$data['updated_at'] = $updated_at;
        // 		$data['game_description_id'] = $key->game_description_id;
        // 		$data['game_platform_id'] = $key->game_platform_id;
        // 		$data['game_type_id'] = $key->game_type_id;
        // 		$data['uniqueid'] = $key->player_id . '_' . $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $key->date;

        // 		$this->CI->total_player_game_day->syncToTotalPlayerGameDay($data);
        // 	}
        // 	$this->CI->utils->debug_log('syncTotalPlayerGameDay', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $cnt, 'sum', $sum);

        // }
    }

    private function syncTotalPlayerGameMonth($token) {
        $this->CI->load->model(array('total_player_game_month'));
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $dateTimeFrom->modify($this->getDatetimeAdjustSyncTotal());

        $this->CI->total_player_game_month->startTrans();
        $playerId = null;
        if (!empty($playerName)) {
            $playerId = $this->getPlayerIdInPlayer($playerName);
        }

        $count = $this->CI->total_player_game_month->sync($dateTimeFrom, $dateTimeTo, $playerId);

        $this->CI->utils->debug_log('syncTotalPlayerGameMonth dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $count);

        $success = $this->CI->total_player_game_month->endTransWithSucc();

        return $success;

        // $dateTime = new DateTime();
        // $gamelogs = null;
        // if ($playerName == null) {
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_day->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_day->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfAllPlayer($dateTimeFrom, $dateTimeTo);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_day->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfAllPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_day->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfAllPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfAllPlayer($dateTimeFrom->format('Ym'), $dateTimeTo->format('Ym'));
        // 	// }

        // } else {
        // 	// $player = $this->CI->player->getPlayerIdByPlayerName($playerName);
        // 	$playerId = $this->getPlayerIdInPlayer($playerName); // $player['playerId'];

        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_day->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_day->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfPlayer($dateTimeFrom, $dateTimeTo, $playerId);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_day->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'), $playerId);
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_day->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo, $playerId);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_day->getAllRecordPerMonthOfPlayer($dateTimeFrom->format('Ym'), $dateTimeTo->format('Ym'), $playerId);
        // 	// }
        // }
        // if (!empty($gamelogs)) {
        // 	$updated_at = (new \DateTime)->format('Y-m-d H:i:s');

        // 	$cnt = count($gamelogs);
        // 	$sum = 0;

        // 	foreach ($gamelogs as $key) {
        // 		$sum += $key->betting_amount;
        // 		$month = $key->month;
        // 		$data['player_id'] = $key->player_id;
        // 		$data['betting_amount'] = $key->betting_amount;
        // 		$data['result_amount'] = $key->result_amount;
        // 		$data['month'] = $month;
        // 		$data['updated_at'] = $updated_at;
        // 		$data['game_platform_id'] = $key->game_platform_id;
        // 		$data['game_type_id'] = $key->game_type_id;
        // 		$data['game_description_id'] = $key->game_description_id;
        // 		$data['uniqueid'] = $key->player_id . '_' . $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $month;

        // 		$this->CI->total_player_game_month->syncToTotalPlayerGameMonth($data);
        // 	}
        // 	$this->CI->utils->debug_log('syncTotalPlayerGameMonth', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $cnt, 'sum', $sum);
        // }
    }

    private function syncTotalPlayerGameYear($token) {
        $this->CI->load->model(array('total_player_game_year'));
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $this->CI->total_player_game_year->startTrans();
        $playerId = null;
        if (!empty($playerName)) {
            $playerId = $this->getPlayerIdInPlayer($playerName);
        }

        $count = $this->CI->total_player_game_year->sync($dateTimeFrom, $dateTimeTo, $playerId);

        $this->CI->utils->debug_log('syncTotalPlayerGameYear dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $count);

        $success = $this->CI->total_player_game_year->endTransWithSucc();

        return $success;

        // $gamelogs = null;
        // if ($playerName == null) {
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_month->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_month->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfAllPlayer($dateTimeFrom, $dateTimeTo);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_month->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfAllPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d'));
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_month->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfAllPlayer($dateTimeFromo->format('Y-m-d'), $dateTimeTo);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfAllPlayer($dateTimeFrom->format('Y'), $dateTimeTo->format('Y'));
        // 	// }

        // } else {
        // 	// $player = $this->CI->player->getPlayerIdByPlayerName($playerName);
        // 	$playerId = $this->getPlayerIdInPlayer($playerName); //$player['playerId'];
        // 	// if (!$dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_month->getFirstRecordDateTime();
        // 	// 	$dateTimeTo = $this->CI->total_player_game_month->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfPlayer($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo, $playerId);
        // 	// } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	// 	$dateTimeFrom = $this->CI->total_player_game_month->getFirstRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfPlayer($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'), $playerId);
        // 	// } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	// 	$dateTimeTo = $this->CI->total_player_game_month->getLastRecordDateTime();
        // 	// 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfPlayer($dateTimeFromo->format('Y-m-d H:i:s'), $dateTimeTo, $playerId);
        // 	// } else {
        // 	$gamelogs = $this->CI->total_player_game_month->getAllRecordPerYearOfPlayer($dateTimeFrom->format('Y'), $dateTimeTo->format('Y'), $playerId);
        // 	// }

        // }
        // if (!empty($gamelogs)) {
        // 	$cnt = count($gamelogs);
        // 	$sum = 0;

        // 	$updated_at = (new \DateTime)->format('Y-m-d H:i:s');
        // 	foreach ($gamelogs as $key) {
        // 		$sum += $key->betting_amount;
        // 		$year = $key->year;
        // 		$data['player_id'] = $key->player_id;
        // 		$data['betting_amount'] = $key->betting_amount;
        // 		$data['result_amount'] = $key->result_amount;
        // 		$data['year'] = $year;
        // 		$data['updated_at'] = $updated_at;
        // 		$data['game_description_id'] = $key->game_description_id;
        // 		$data['game_platform_id'] = $key->game_platform_id;
        // 		$data['game_type_id'] = $key->game_type_id;
        // 		$data['uniqueid'] = $key->player_id . '_' . $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $year;

        // 		$this->CI->total_player_game_year->syncToTotalPlayerGameYear($data);
        // 	}
        // 	$this->CI->utils->debug_log('syncTotalPlayerGameYear', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'count', $cnt, 'sum', $sum);
        // }
    }

    private function syncOperatorGameMinute($token) {
        $this->CI->load->model(array('total_player_game_minute', 'total_operator_game_minute'));
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        $gamelogs = $this->CI->total_player_game_minute->getOperatorRecordPerMinute($dateTimeFrom->format('Y-m-d H:i'), $dateTimeTo->format('Y-m-d H:i'));

        if (!empty($gamelogs)) {
            $updated_at = (new \DateTime)->format('Y-m-d H:i:s');
            foreach ($gamelogs as $key) {
                $data['betting_amount'] = $key->betting_amount;
                $data['result_amount'] = $key->result_amount;
                $data['minute'] = $key->minute;
                $data['hour'] = $key->hour;
                $data['date'] = $key->date;
                $data['updated_at'] = $updated_at;
                $data['game_description_id'] = $key->game_description_id;
                $data['game_platform_id'] = $key->game_platform_id;
                $data['game_type_id'] = $key->game_type_id;
                $data['uniqueid'] = $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $key->date . '_' . $key->hour . '_' . $key->minute;

                $this->CI->total_operator_game_minute->syncToOperatorGameMinute($data);
            }
        }
    }
    private function syncOperatorGameHour($token) {
        $this->CI->load->model(array('total_player_game_hour', 'total_operator_game_hour'));
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        // if (!$dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->game_logs->getOperatorRecordPerHour($dateTimeFrom, $dateTimeTo);
        // } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$gamelogs = $this->CI->game_logs->getOperatorRecordPerHour($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->game_logs->getOperatorRecordPerHour($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // } else {
        $gamelogs = $this->CI->total_player_game_hour->getOperatorRecordPerHour($dateTimeFrom->format('Y-m-d H'), $dateTimeTo->format('Y-m-d H'));
        // }

        if (!empty($gamelogs)) {
            $updated_at = (new \DateTime)->format('Y-m-d H:i:s');
            foreach ($gamelogs as $key) {
                $data['betting_amount'] = $key->betting_amount;
                $data['result_amount'] = $key->result_amount;
                $data['hour'] = $key->hour;
                $data['date'] = $key->date;
                $data['updated_at'] = $updated_at;
                $data['game_description_id'] = $key->game_description_id;
                $data['game_platform_id'] = $key->game_platform_id;
                $data['game_type_id'] = $key->game_type_id;
                $data['uniqueid'] = $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $key->date . '_' . $key->hour;

                $this->CI->total_operator_game_hour->syncToOperatorGameHour($data);
            }
        }
    }

    private function syncOperatorGameDay($token) {
        $this->CI->load->model(array('total_operator_game_hour', 'total_operator_game_day'));
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        // if (!$dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_hour->getOperatorRecordPerDay($dateTimeFrom, $dateTimeTo);
        // } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_hour->getOperatorRecordPerDay($dateTimeFrom, $dateTimeTo->format('Y-m-d'));
        // } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_hour->getOperatorRecordPerDay($dateTimeFrom->format('Y-m-d'), $dateTimeTo);
        // } else {
        $gamelogs = $this->CI->total_operator_game_hour->getOperatorRecordPerDay($dateTimeFrom->format('Y-m-d'), $dateTimeTo->format('Y-m-d'));
        // }
        // $dateTime = new DateTime();

        if (!empty($gamelogs)) {
            $updated_at = (new \DateTime)->format('Y-m-d H:i:s');
            foreach ($gamelogs as $key) {
                $data['betting_amount'] = $key->betting_amount;
                $data['result_amount'] = $key->result_amount;
                $data['date'] = $key->date;
                $data['updated_at'] = $updated_at;
                $data['game_description_id'] = $key->game_description_id;
                $data['game_platform_id'] = $key->game_platform_id;
                $data['game_type_id'] = $key->game_type_id;
                $data['uniqueid'] = $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $key->date;

                $this->CI->total_operator_game_day->syncToOperatorGameDay($data);
            }
        }
    }

    private function syncOperatorGameMonth($token) {
        $this->CI->load->model(array('total_operator_game_day', 'total_operator_game_month'));
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        // if (!$dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_day->getOperatorRecordPerMonth($dateTimeFrom, $dateTimeTo);
        // } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_day->getOperatorRecordPerMonth($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_day->getOperatorRecordPerMonth($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // } else {
        $gamelogs = $this->CI->total_operator_game_day->getOperatorRecordPerMonth($dateTimeFrom->format('Ym'), $dateTimeTo->format('Ym'));
        // }

        // $dateTime = new DateTime();
        if (!empty($gamelogs)) {
            $updated_at = (new \DateTime)->format('Y-m-d H:i:s');
            foreach ($gamelogs as $key) {
                $month = $key->month;
                $data['betting_amount'] = $key->betting_amount;
                $data['result_amount'] = $key->result_amount;
                $data['month'] = $month;
                $data['updated_at'] = $updated_at;
                $data['game_description_id'] = $key->game_description_id;
                $data['game_platform_id'] = $key->game_platform_id;
                $data['game_type_id'] = $key->game_type_id;
                $data['uniqueid'] = $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $month;

                $this->CI->total_operator_game_month->syncToOperatorGameMonth($data);
            }
        }
    }

    private function syncOperatorGameYear($token) {
        $this->CI->load->model(array('total_operator_game_month', 'total_operator_game_year'));
        $dateTimeFrom = $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = $this->getValueFromSyncInfo($token, 'dateTimeTo');

        // if (!$dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_month->getOperatorRecordPerYear($dateTimeFrom, $dateTimeTo);
        // } else if (!$dateTimeFrom && $dateTimeTo) {
        // 	$dateTimeFrom = $this->CI->game_logs->getFirstRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_month->getOperatorRecordPerYear($dateTimeFrom, $dateTimeTo->format('Y-m-d H:i:s'));
        // } else if ($dateTimeFrom && !$dateTimeTo) {
        // 	$dateTimeTo = $this->CI->game_logs->getLastRecordDateTime();
        // 	$gamelogs = $this->CI->total_operator_game_month->getOperatorRecordPerYear($dateTimeFrom->format('Y-m-d H:i:s'), $dateTimeTo);
        // } else {
        $gamelogs = $this->CI->total_operator_game_month->getOperatorRecordPerYear($dateTimeFrom->format('Y'), $dateTimeTo->format('Y'));
        // }

        // $dateTime = new DateTime();
        if (!empty($gamelogs)) {
            $updated_at = (new \DateTime)->format('Y-m-d H:i:s');
            foreach ($gamelogs as $key) {
                $year = $key->year;
                $data['betting_amount'] = $key->betting_amount;
                $data['result_amount'] = $key->result_amount;
                $data['year'] = $year;
                $data['updated_at'] = $updated_at;
                $data['game_description_id'] = $key->game_description_id;
                $data['game_platform_id'] = $key->game_platform_id;
                $data['game_type_id'] = $key->game_type_id;
                $data['uniqueid'] = $key->game_platform_id . '_' . $key->game_type_id . '_' . $key->game_description_id . '_' . $year;

                $this->CI->total_operator_game_year->syncToOperatorGameYear($data);
            }
        }
    }

    public function syncBalance($dateTimeFrom, $dateTimeTo, $playerName = null, $gameName = null) {

    }

    /**
     * merge to game logs
     *
     * @param string token to get syncInfo
     */
    public function syncLostAndFound($token) {
        //nothing
        return $this->returnUnimplemented();
    }

    // abstract public function beforeProcessResult($apiName, $params, $resultText, $statusCode, $statusText = null, $extra = null);
    /**
     * @param string apiName
     * @param array params
     * @param boolean success
     * @param string resultText <result info="" msg="" />
     * @param string statusCode
     * @param string statusText
     * @param Object resultObj only for soap
     * @return array success
     */
    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null){
        return $this->returnFailed("doesn't support anymore");
    }
    /**
     * @param string apiName
     * @param array params name=>value
     * @return string url
     */
    abstract public function generateUrl($apiName, $params);

    protected function getCallType($apiName, $params) {
        //overwrite in sub-class
        return self::CALL_TYPE_HTTP;
    }

    public function returnUnimplemented() {
        return array('success' => true, 'unimplemented' => true);
    }

    public function returnFailed($err = null) {
        return array('success' => false, 'errorMessage' => $err);
    }

    public function isDisabled() {
        return $this->CI->utils->isDisabledGameApi($this->getPlatformCode());
    }

    public function isEnabled() {
        return $this->CI->utils->isEnabledGameApi($this->getPlatformCode());
    }

    public function callApi($apiName, $params, $context = null) {
        if ($this->isDisabled()) {
            return array('success' => false);
        }

        //validate last function name with api name, only withdraw/deposit

        $context_transfer_type= !empty($context) && !empty($context['transfer_type']) ? $context['transfer_type'] : null;

        $enabled_guess_success_for_curl_errno_on_this_api=false;
        if(isset($context['enabled_guess_success_for_curl_errno_on_this_api'])){

            $enabled_guess_success_for_curl_errno_on_this_api=$context['enabled_guess_success_for_curl_errno_on_this_api'];

        }else if( $apiName==self::API_depositToGame || $context_transfer_type==self::API_depositToGame ){

            $enabled_guess_success_for_curl_errno_on_this_api=true;

        }

        $let_transfer_timeout=false;
        if(in_array($apiName, $this->mock_settings['timeout_mock_for_api'])){

            $let_transfer_timeout=$this->mock_settings['let_transfer_timeout'];
            $timeout_mock_only_player_id=$this->mock_settings['timeout_mock_only_player_id'];// $this->getSystemInfo('timeout_mock_only_player_id', null);
            if(!empty($timeout_mock_only_player_id)){
                //only for this player
                $playerId = $this->searchPlayerIdFromContext($context);
                if($timeout_mock_only_player_id!=$playerId){
                    $let_transfer_timeout=false;
                }
            }

            if($let_transfer_timeout){
                $this->CI->utils->debug_log('setup let_transfer_timeout to true', $let_transfer_timeout, $this->mock_settings);
            }

        }

        // $is_timeout_mock= isset($context['is_timeout_mock']) ? $context['is_timeout_mock'] : false ;

        $url = $this->generateUrl($apiName, $params);

        $callType = $this->getCallType($apiName, $params);

        $this->CI->utils->debug_log("url", $url, 'callType', $callType, "params", is_array($params) ? count($params) : '', "apiName", $apiName);
        if ($callType == self::CALL_TYPE_HTTP) {
            list($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj) = $this->httpCallApi($url, $params);

            $this->CI->utils->debug_log("http call", "header", strlen($header), "resultText", strlen($resultText), "statusCode", $statusCode, "statusText", $statusText, 'callerrCode', $errCode, 'callerr', $error,
                'enabled_guess_success_for_curl_errno_on_this_api', $enabled_guess_success_for_curl_errno_on_this_api, 'guess_success_curl_errno', implode(',', $this->guess_success_curl_errno), 'let_transfer_timeout', $let_transfer_timeout);
            if($let_transfer_timeout){
                $errCode=CURLE_OPERATION_TIMEOUTED;
            }

            //enabled_guess_success_for_curl_errno_on_this_api is from call context
            //for example: deposit will add this, but query balance can't add this
            //guess_success_curl_errno is from extra_info
            $is_invalid_code= in_array($errCode, $this->guess_success_curl_errno) || $this->shouldGuessSuccessStatusCode($statusCode);
            //&& in_array($errCode, $this->guess_success_curl_errno)
            if($enabled_guess_success_for_curl_errno_on_this_api && !empty($this->guess_success_curl_errno)
                    && $is_invalid_code){
                //convert to success
                $this->CI->utils->debug_log('convert error code to success platform:'.$this->getPlatformCode(), $errCode);
                $errCode=CURLE_OK;
                if($statusCode>=400){
                    $statusCode=0;
                }
                //set flag to notify it's guess timeout is success
                $context['is_guess_success']=true;
            }

            return $this->processResult($url, $apiName, $params, $resultText, $statusCode, $statusText, $header, $errCode, $error, $resultObj, $context);

        } elseif ($callType == self::CALL_TYPE_XMLRPC) {

            list($method, $methodParams, $resultType) = $this->generateXmlRpcMethod($apiName, $params);
            list($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj) = $this->xmlrpcCallApi($url, $method, $methodParams, $resultType);

            $this->CI->utils->debug_log("xmlrpc call", "header", count($header), "resultText", count($resultText), "statusCode", $statusCode, "statusText", $statusText, 'callerrCode', $errCode, 'callerr', $error,
                'enabled_guess_success_for_curl_errno_on_this_api', $enabled_guess_success_for_curl_errno_on_this_api, 'guess_success_curl_errno', implode(',', $this->guess_success_curl_errno), 'let_transfer_timeout', $let_transfer_timeout);

            if($let_transfer_timeout){
                $errCode=CURLE_OPERATION_TIMEOUTED;
            }

            $is_invalid_code= in_array($errCode, $this->guess_success_curl_errno) || $this->shouldGuessSuccessStatusCode($statusCode);
            //&& in_array($errCode, $this->guess_success_curl_errno)
            if($enabled_guess_success_for_curl_errno_on_this_api && !empty($this->guess_success_curl_errno)
                    && $is_invalid_code){
                //convert to success
                $this->CI->utils->debug_log('convert error code to success platform:'.$this->getPlatformCode(), $errCode);
                $errCode=CURLE_OK;
                if($statusCode>=400){
                    $statusCode=0;
                }
                //set flag to notify it's guess timeout is success
                $context['is_guess_success']=true;
            }

            return $this->processResult($url, $apiName, $params, $resultText, $statusCode, $statusText, $header, $errCode, $error, $resultObj, $context);

        } elseif ($callType == self::CALL_TYPE_SOAP) {
            list($method, $methodParams) = $this->generateSoapMethod($apiName, $params);
            list($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj) = $this->soapCallApi($url, $method, $methodParams);

            $this->CI->utils->debug_log("soap call", "header", strlen($header), "resultText", strlen($resultText), "statusCode", $statusCode, "statusText", $statusText, 'callerrCode', $errCode, 'callerr', $error,
                'enabled_guess_success_for_curl_errno_on_this_api', $enabled_guess_success_for_curl_errno_on_this_api, 'guess_success_curl_errno', implode(',', $this->guess_success_curl_errno), 'let_transfer_timeout', $let_transfer_timeout);

            if($let_transfer_timeout){
                $errCode=CURLE_OPERATION_TIMEOUTED;
            }

            $is_invalid_code= in_array($errCode, $this->guess_success_curl_errno) || $this->shouldGuessSuccessStatusCode($statusCode);
            //&& in_array($errCode, $this->guess_success_curl_errno)
            if($enabled_guess_success_for_curl_errno_on_this_api && !empty($this->guess_success_curl_errno)
                    && $is_invalid_code){
                //convert to success
                $this->CI->utils->debug_log('convert error code to success platform:'.$this->getPlatformCode(), $errCode);
                $errCode=CURLE_OK;
                if($statusCode>=400){
                    $statusCode=0;
                }
                //set flag to notify it's guess timeout is success
                $context['is_guess_success']=true;
            }

            return $this->processResult($url, $apiName, $params, $resultText, $statusCode, $statusText, $header, $errCode, $error, $resultObj, $context);
        } else {
            return ["success" => false];
        }
    }

    protected function getHttpHeaders($params) {
        return array();
    }

    protected function convertArrayToHeaders($headers) {
        $result = array();
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $result[] = $key . ": " . $value;
            }
        }
        return $result;
    }

    protected function initSSL($ch) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }

    protected $soapAuthResult = null;

    protected function generateSoapMethod($apiName, $params) {
        return array($apiName, $params);
    }

    protected function authSoap($client) {
        //overwrite in sub-class
        //save auth result
        return true;
    }

    protected function makeSoapOptions($options) {
        //overwrite in sub-class
        return $options;
    }

    protected function prepareSoap($client) {
        //overwrite in sub-class
        return $client;
    }

    /**
     * @param $url
     * @param $options
     * @return ProxySoapClient
     */
    protected function createSoapClient($url, $options) {
        return new ProxySoapClient($url, $options);
    }

    protected function soapCallApi($url, $method, $params) {
        //no exception , return SoapFault
        $options = $this->makeSoapOptions(array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'save_response' => true,
            'soap_timeout' => $this->getConfig('default_soap_timeout'),
            'exceptions' => true,
            'ignore_ssl_verify' => $this->ignore_ssl_verify,
            'call_http_proxy_host' => $this->call_http_proxy_host,
            'call_http_proxy_port' => $this->call_http_proxy_port,
            'call_http_proxy_login' => $this->call_http_proxy_login,
            'call_http_proxy_password' => $this->call_http_proxy_password,

            'call_socks5_proxy' => $this->call_socks5_proxy,
            'call_socks5_proxy_login' => $this->call_socks5_proxy_login,
            'call_socks5_proxy_password' => $this->call_socks5_proxy_password,
        ));

        $this->CI->utils->debug_log('SOAP CALL PARAMS >-------------------> ', $params, $options);
        try {

            $client = $this->createSoapClient($url, $options);

            if ($this->authSoap($client)) {
                //call action

                $client = $this->prepareSoap($client);
                $resultObj = $client->$method($params);
                // $this->CI->utils->debug_log('resultObj', (array) $resultObj);
                //var_dump($header, $resultText, $statusCode, $statusText, $errCode, $error);
                // if(!$client->getResponse()){
                // return array(array(), array(), array(), array(), array(), array(), $resultObj);
                // }else{
                // $rlt=call_user_func_array(array($client,'_fullResponse'),null);
                // $this->CI->utils->debug_log('resultText',$client->resultText);
                list($header, $resultText, $statusCode, $statusText, $errCode, $error, $requestXml) = $client->_fullResponse();
                if(strlen($requestXml)<2000){
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.$requestXml);
                }else{
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.substr($requestXml, 0, 2000));
                }
                // }
                // $resultText = $result;

                return array($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj);
            } else {
                //error

                list($header, $resultText, $statusCode, $statusText, $errCode, $error, $requestXml) = $client->_fullResponse();
                if(strlen($requestXml)<2000){
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.$requestXml);
                }else{
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.substr($requestXml, 0, 2000));
                }
                // $statusCode=;
                if ($statusCode == 0) {
                    $statusCode = 400;
                    $statusText = 'SoapFault';
                }
                return array($header, $resultText, $statusCode, $statusText, $errCode, $error, null);
            }
        } catch (SoapFault $f) {
            $this->utils->debug_log('SoapFault', $f);

            //first try $client
            if (isset($client)) {
                list($header, $resultText, $statusCode, $statusText, $errCode, $error, $requestXml) = $client->_fullResponse();
                if(strlen($requestXml)<2000){
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.$requestXml);
                }else{
                    $this->CI->utils->debug_log('url: '.$url.', requestXml: '.substr($requestXml, 0, 2000));
                }
                //append exception message
                $statusCode = $statusCode < 400 ? 400 : $statusCode;
                // $statusText .= ' | ' . $f->getMessage();
                $header .= ' | ' . $f->getMessage();
            } else {
                $statusCode = 400;
                $statusText = $f->getMessage();
                $header = $f->faultcode;
                $resultText = $f->faultstring;
                $errCode = $f->faultcode;
                $error = $f->faultstring;
            }
            return array($header, $resultText, $statusCode, $statusText, $errCode, $error, null);
        }

    }

    protected function customHttpCall($ch, $params) {
        // return null;
    }

    protected function getTimeoutSecond() {
        return $this->CI->utils->getConfig('default_http_timeout');
    }

    protected function getConnectTimeout() {
        return $this->CI->utils->getConfig('default_connect_timeout');
    }

    protected function makeHttpOptions($options) {
        //overwrite in sub-class
        return $options;
    }

    protected function httpCallApi($url, $params) {
        //call http
        $content = null;
        $header = null;
        $statusCode = null;
        $statusText = '';
        $ch = null;

        $options = $this->makeHttpOptions([
            'ignore_ssl_verify' => $this->ignore_ssl_verify,
            'call_http_proxy_host' => $this->call_http_proxy_host,
            'call_http_proxy_port' => $this->call_http_proxy_port,
            'call_http_proxy_login' => $this->call_http_proxy_login,
            'call_http_proxy_password' => $this->call_http_proxy_password,

            'call_socks5_proxy' => $this->call_socks5_proxy,
            'call_socks5_proxy_login' => $this->call_socks5_proxy_login,
            'call_socks5_proxy_password' => $this->call_socks5_proxy_password,

        ]);

        $this->CI->utils->debug_log('HTTP CALL PARAMS >-------------------> ', $this->CI->utils->encodeJson($params), $this->CI->utils->encodeJson($options));

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //set timeout
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

            $settle_proxy=false;
            // set proxy
            if (isset($options['call_socks5_proxy']) && !empty($options['call_socks5_proxy'])) {
                $this->CI->utils->debug_log('http call with proxy', $options['call_socks5_proxy']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
                curl_setopt($ch, CURLOPT_PROXY, $options['call_socks5_proxy']);
                if (!empty($options['call_socks5_proxy_login']) && !empty($options['call_socks5_proxy_password'])) {
                    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['call_socks5_proxy_login'] . ':' . $options['call_socks5_proxy_password']);
                }
                $settle_proxy=true;

            }

            if(!$settle_proxy){
                //http proxy
                if (isset($options['call_http_proxy_host']) && !empty($options['call_http_proxy_host'])) {
                    $this->CI->utils->debug_log('http call with http proxy', $options['call_http_proxy_host']);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt($ch, CURLOPT_PROXY, $options['call_http_proxy_host']);
                    curl_setopt($ch, CURLOPT_PROXYPORT, $options['call_http_proxy_port']);
                    if (!empty($options['call_http_proxy_login']) && !empty($options['call_http_proxy_password'])) {
                        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['call_http_proxy_login'] . ':' . $options['call_http_proxy_password']);
                    }

                }
            }

            if($options['ignore_ssl_verify']){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }

            $this->initSSL($ch);

            $headers = $this->convertArrayToHeaders($this->getHttpHeaders($params));
            // $this->CI->utils->debug_log('HTTP CALL Headers >------------------------------------> ', $headers);
            if (!empty($headers)) {
                $this->CI->utils->debug_log('HTTP CALL CURL Headers >------------------------------> ', $headers);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            //process post
            $this->customHttpCall($ch, $params);

            $response = curl_exec($ch);
            $errCode = curl_errno($ch);
            $error = curl_error($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $content = substr($response, $header_size);

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            // $this->CI->utils->debug_log('response from http', $response);

            $statusText = $errCode . ':' . $error;
            curl_close($ch);

        } catch (Exception $e) {
            $this->processError($e);
        }
        return array($header, $content, $statusCode, $statusText, $errCode, $error, null);

    }

    protected function makeXmlrpcOptions($options) {
        //overwrite in sub-class
        return $options;
    }

    protected function xmlrpcCallApi($url, $method, $params, $resultType) {

        $content = null;
        $header = null;
        $statusCode = null;
        $statusText = '';
        $response = null;
        $error = null;
        $errCode = null;

        $options = $this->makeXmlrpcOptions([
            'ignore_ssl_verify' => $this->ignore_ssl_verify,
            'call_http_proxy_host' => $this->call_http_proxy_host,
            'call_http_proxy_port' => $this->call_http_proxy_port,
            'call_http_proxy_login' => $this->call_http_proxy_login,
            'call_http_proxy_password' => $this->call_http_proxy_password,

            'call_socks5_proxy' => $this->call_socks5_proxy,
            'call_socks5_proxy_login' => $this->call_socks5_proxy_login,
            'call_socks5_proxy_password' => $this->call_socks5_proxy_password,

        ]);

        try {

            $xml = xmlrpc_encode_request($method, $params);
            // $include_path = true;

            // $params=[
            //  'http' => [
            //      'timeout' => $this->getConnectTimeout(),
            //      'method' => "POST",
            //      'header' => "Content-Type: text/xml",
            //      'content' => $request
            //  ],
            // ];

            // if(!empty($options['call_http_proxy_host'])){
            //  $params['http']['proxy']='tcp://'.$options['call_http_proxy_host'];
            //  if(!empty($options['call_http_proxy_port'])){
            //      $params['http']['proxy'].=':'.$options['call_http_proxy_port'];
            //  }
            // }

            // if(!empty($options['ignore_ssl_verify'])){
            //  $params['ssl']=['verify_peer'=>false, 'allow_self_signed'=>true];
            // }

            $this->CI->utils->debug_log('xmlrpc call: '.$url, $params);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //set timeout
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeoutSecond());
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

            $settle_proxy=false;
            // set proxy
            if (isset($options['call_socks5_proxy']) && !empty($options['call_socks5_proxy'])) {
                $this->CI->utils->debug_log('http call with proxy', $options['call_socks5_proxy']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                curl_setopt($ch, CURLOPT_PROXY, $options['call_socks5_proxy']);
                if (!empty($options['call_socks5_proxy_login']) && !empty($options['call_socks5_proxy_password'])) {
                    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['call_socks5_proxy_login'] . ':' . $options['call_socks5_proxy_password']);
                }
                $settle_proxy=true;

            }

            if(!$settle_proxy){
                //http proxy
                if (isset($options['call_http_proxy_host']) && !empty($options['call_http_proxy_host'])) {
                    $this->CI->utils->debug_log('http call with http proxy', $options['call_http_proxy_host']);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt($ch, CURLOPT_PROXY, $options['call_http_proxy_host']);
                    curl_setopt($ch, CURLOPT_PROXYPORT, $options['call_http_proxy_port']);
                    if (!empty($options['call_http_proxy_login']) && !empty($options['call_http_proxy_password'])) {
                        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['call_http_proxy_login'] . ':' . $options['call_http_proxy_password']);
                    }

                }
            }

            if($options['ignore_ssl_verify']){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }

            $this->initSSL($ch);

            $headerArray=$this->getHttpHeaders($params);

            $headerArray['Content-Type']='text/xml';

            $headers = $this->convertArrayToHeaders($headerArray);
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

            //process post
            $this->customHttpCall($ch, $params);

            $response = curl_exec($ch);
            $errCode = curl_errno($ch);
            $error = curl_error($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $content = substr($response, $header_size);

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            // var_dump($url);
            // var_dump($response);

            $statusText = $errCode . ':' . $error;
            // var_dump($statusText);
            curl_close($ch);

            // $context = stream_context_create($params);

            // $text = @file_get_contents($xmlrpcServer, true, $context);
            $xmlArr = null;
            if(!empty($content)){

                if ($resultType == 'xml' && !empty($content)) {
                    $response = xmlrpc_decode($content);
                    // }else{
                }

                if (is_array($response) && xmlrpc_is_fault($response)) {

                    $error = $response['faultString'];
                    $errCode = $response['faultCode'];

                } else {
                    $xmlArr = $response;
                }
            }

        } catch (Exception $e) {
            $this->processError($e);
        }

        return array($header, $content, $statusCode, $statusText, $errCode, $error, $xmlArr);

    }

    // protected function saveResponseError($apiName, $params, $errorText, $statusCode, $statusText = null, $extra = null) {
    // 	//save error
    // 	$this->CI->load->model("response_error");
    // 	return $this->CI->response_error->saveResponseError($this->SYSTEM_TYPE_ID, $apiName, json_encode($params), $errorText, $statusCode, $statusText, $extra);
    // }

    protected function saveResponseResult($success, $apiName, $params, $resultText, $statusCode, $statusText = null, $extra = null, $field = null, $dont_save_response_in_api = false) {
        //save to db
        $this->CI->load->model("response_result");
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        if($field==null){
            $field=[];
        }
        //try add decoded_result_text
        $decoded_result_text=$this->getDecodedResultText($resultText, $apiName, $params, $statusCode);
        if(!empty($decoded_result_text)){
            $field['decoded_result_text']=$decoded_result_text;
        }

        return $this->CI->response_result->saveResponseResult($this->SYSTEM_TYPE_ID, $flag, $apiName, json_encode($params), $resultText, $statusCode, $statusText, $extra, $field, $dont_save_response_in_api);
    }

    public function getDecodedResultText($resultText, $apiName, $params, $statusCode){
        return null;
    }

    protected function setResponseResultToError($responseResultId) {
        $this->CI->load->model("response_result");
        return $this->CI->response_result->setResponseResultToError($responseResultId);
    }

    protected function saveResponseResultForFile($success, $apiName, $params, $resultFilePath, $field = null) {
        //save to db
        $this->CI->load->model("response_result");
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResultForFile($this->SYSTEM_TYPE_ID, $flag, $apiName, json_encode($params), $resultFilePath, $field);
    }
    /**
     * overwrite it , if not http call
     *
     * @return boolean true=error, false=ok
     */
    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        // $statusCode = intval($statusCode, 10);
        return $errCode || intval($statusCode, 10) >= 400;
    }

    public function shouldGuessSuccessStatusCode($statusCode){
        return false;
    }

    /**
     *
     * @param context {'callback_obj'=>, 'callback_method'=>, mixin }
     */
    // public function processResult($apiName, $params, $resultText, $statusCode, $statusText = null, $extra = null, $errCode = null, $error = null, $resultObj = null, $context = null) {
    // 	// list($resultText, $statusCode, $statusText) = $this->beforeProcessResult($apiName, $params, $resultText, $statusCode, $statusText, $extra);
    // 	//process result
    // 	//if error
    // 	$rltArr = null;
    // 	$success = !$this->isErrorCode($apiName, $params, $statusCode, $errCode, $error);
    // 	// $this->CI->utils->debug_log('success', $success);

    // 	// $responseResultId = 0;

    // 	$dont_save_response_in_api = false;
    // 	if ($context && array_key_exists('dont_save_response_in_api', $context) && @$context['dont_save_response_in_api']) {
    // 		$dont_save_response_in_api = true;
    // 	}

    // 	$syncId = null;
    // 	if ($context && array_key_exists('syncId', $context)) {
    // 		$syncId = @$context['syncId'];
    // 	}
    // 	if ($context && array_key_exists('sync_id', $context)) {
    // 		$syncId = @$context['sync_id'];
    // 	}
    // 	$responseResultId = $this->saveResponseResult($success, $apiName, $params, $resultText,
    // 		$statusCode, $statusText, $extra, array('sync_id' => $syncId), $dont_save_response_in_api);
    // 	if ($success) {
    // 		// 	$this->saveResponseError($apiName, $params, $resultText, $statusCode, $statusText, $extra);
    // 		// } else {
    // 		//if success
    // 		if ($context && $context['callback_obj'] && $context['callback_method']) {
    // 			list($success, $rltArr) = call_user_func(
    // 				array($context['callback_obj'], $context['callback_method']),
    // 				array(
    // 					'apiName' => $apiName,
    // 					'params' => $params,
    // 					'responseResultId' => $responseResultId,
    // 					'resultText' => $resultText,
    // 					'statusCode' => $statusCode,
    // 					'statusText' => $statusText,
    // 					'extra' => $extra,
    // 					'resultObj' => $resultObj,
    // 					'context' => $context,
    // 				)
    // 			);
    // 		} else {
    // 			list($success, $rltArr) = $this->afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText, $extra, $resultObj);
    // 		}
    // 		// log_message('error', 'success:' . $success . ' rlt:' . var_export($rltArr, true));
    // 	}
    // 	if (!$rltArr) {
    // 		$rltArr = array();
    // 	}
    // 	return array_merge(array("success" => $success), $rltArr);
    // }

    public function getAvailPlayerId($playerId) {
        $this->CI->load->model(array('player_model'));
        if (!empty($this->CI->player_model->getUsernameById($playerId))) {
            return $playerId;
        }
        return null;
    }

    public function getPlayerIdFromUsername($playerName) {
        $playerId = null;
        $this->CI->load->model(array('player_model'));
        $row = $this->CI->player_model->getPlayerByUsername($playerName);
        if (!empty($row)) {
            $playerId = $row->playerId;
        }
        // if (empty($playerId)) {
        // 	//try search game provider
        // 	$playerId = $this->getPlayerIdInGameProviderAuth($playerName);
        // }
        return $playerId;
    }

    public function searchPlayerIdFromContext($context) {
        $playerId = null;
        try {

            // $this->CI->utils->debug_log('context', @$context['playerName']);
            if (!empty($context)) {
                //try player_id , playerId
                if (empty($playerId)) {
                    if (isset($context['playerId']) && !empty($context['playerId'])) {
                        $playerId = $this->getAvailPlayerId($context['playerId']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['player_id']) && !empty($context['player_id'])) {
                        $playerId = $this->getAvailPlayerId($context['player_id']);
                    }
                }
                //try playerName, sbe_username , sbe_userName, playerUsername, username
                if (empty($playerId)) {
                    if (isset($context['playerName']) && !empty($context['playerName'])) {
                        $playerId = $this->getPlayerIdFromUsername($context['playerName']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['sbe_username']) && !empty($context['sbe_username'])) {
                        $playerId = $this->getPlayerIdFromUsername($context['sbe_username']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['sbe_userName']) && !empty($context['sbe_userName'])) {
                        $playerId = $this->getPlayerIdFromUsername($context['sbe_userName']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['username']) && !empty($context['username'])) {
                        $playerId = $this->getPlayerIdFromUsername($context['username']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['playerUsername']) && !empty($context['playerUsername'])) {
                        $playerId = $this->getPlayerIdFromUsername($context['playerUsername']);
                    }
                }
                if (empty($playerId)) {
                    if (isset($context['gameUsername']) && !empty($context['gameUsername'])) {
                        $playerId = $this->getPlayerIdInGameProviderAuth($context['gameUsername']);
                    }
                }

            }
        } catch (Exception $e) {
            $this->utils->debug_log($e);
        }

        return $playerId;
    }

    public function searchExternalTransactionIdFromContext($context){
        $external_transaction_id = null;
        try {

            // $this->CI->utils->debug_log('context', @$context['playerName']);
            if (!empty($context)) {
                //try player_id , playerId
                if (empty($external_transaction_id)) {
                    if (isset($context['external_transaction_id']) && !empty($context['external_transaction_id'])) {
                        $external_transaction_id = $context['external_transaction_id'];
                    }
                }
                if (empty($external_transaction_id)) {
                    if (isset($context['transId']) && !empty($context['transId'])) {


                        $external_transaction_id = $context['transId'];
                    }
                }
                //try transacationid, externaltranid, transacationId
                if (empty($external_transaction_id)) {
                    if (isset($context['transacationid']) && !empty($context['transacationid'])) {
                        $external_transaction_id = $context['transacationid'];
                    }
                }
                if (empty($external_transaction_id)) {
                    if (isset($context['transacationId']) && !empty($context['transacationId'])) {
                        $external_transaction_id = $context['transacationId'];
                    }
                }
                if (empty($external_transaction_id)) {
                    if (isset($context['externaltranid']) && !empty($context['externaltranid'])) {
                        $external_transaction_id = $context['externaltranid'];
                    }
                }

            }
        } catch (Exception $e) {
            $this->CI->utils->error_log($e);
        }

        return $external_transaction_id;
    }

    public function processGuessSuccess($params, &$success, &$result){
        $is_guess_success=$this->getVariableFromContext($params, 'is_guess_success', false);

        if($is_guess_success){
            $responseResultId = $this->getResponseResultIdFromParams($params);
            $context=$params['context'];
            // $external_transaction_id=$this->getVariableFromContext($params, 'external_transaction_id');
            $external_transaction_id = $this->searchExternalTransactionIdFromContext($context);
            $success=true;
            $result['reason_id']=self::REASON_NETWORK_ERROR;
            $result['response_result_id'] = $responseResultId;
            $result['external_transaction_id']=$external_transaction_id;
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_UNKNOWN;
        }

        return $is_guess_success;
    }

    public function processResult($url, $apiName, $params, $resultText, $statusCode, $statusText = null,
            $extra = null, $errCode = null, $error = null, $resultObj = null, $context = null) {
        // list($resultText, $statusCode, $statusText) = $this->beforeProcessResult($apiName, $params, $resultText, $statusCode, $statusText, $extra);
        //process result
        //if error
        $rltArr = [];
        // print_r($resultText);
        $success = !$this->isErrorCode($apiName, $params, $statusCode, $errCode, $error);
        if(!$success){
            $this->CI->utils->error_log('call api error', $url, $apiName, $params, $resultText, $statusCode, $statusText, $extra, $errCode, $error);
        }

        $dont_save_response_in_api = isset($context['dont_save_response_in_api']) ? $context['dont_save_response_in_api'] : false;
        // $this->CI->utils->debug_log('success', $success);

        $fields = null;
        $playerId = $this->searchPlayerIdFromContext($context);
        if (!empty($playerId)) {
            $fields['player_id'] = $playerId;
        }
        if (!$success) {
            if (empty($resultText) && !empty($statusText)) {
                $resultText = $statusText;
            }
        }
        $external_transaction_id = $this->searchExternalTransactionIdFromContext($context);
        if (!empty($external_transaction_id)) {
            $fields['external_transaction_id'] = $external_transaction_id;
        }
        $fields['full_url']=$url;

        $responseResultId = $this->saveResponseResult($success, $apiName, $params, $resultText,
            $statusCode, $statusText, $extra, $fields, $dont_save_response_in_api);
        if ($success) {
            // 	$this->saveResponseError($apiName, $params, $resultText, $statusCode, $statusText, $extra);
            // } else {
            //if success
            if ($context && @$context['callback_obj'] && $context['callback_method']) {

                $pass_params=[
                    'apiName' => $apiName,
                    'params' => $params,
                    'responseResultId' => $responseResultId,
                    'resultText' => $resultText,
                    'statusCode' => $statusCode,
                    'statusText' => $statusText,
                    'extra' => $extra,
                    'resultObj' => $resultObj,
                    'context' => $context,
                ];
                $rlt=[];
                if($this->processGuessSuccess($pass_params, $success, $rlt)){

                    return array_merge(array("success" => $success), $rlt);

                }

                $procRlt = call_user_func(
                    array($context['callback_obj'], $context['callback_method']),
                    $pass_params
                );

                // $this->CI->utils->debug_log('result of context call', $procRlt);

                if (is_array($procRlt)) {
                    if (count($procRlt) > 0 && isset($procRlt[0])) {
                        $success = @$procRlt[0];
                        if (count($procRlt) > 1) {
                            $rltArr = @$procRlt[1];
                        }
                    }
                } else {
                    $success = $procRlt;
                }
            } else {

                // $rltArr=['error_message'=>"doesn't support old api anymore"];
                // $success=false;

                $this->CI->utils->error_log("doesn't support old api anymore", $this->getPlatformCode());

                $procRlt = $this->afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText, $extra, $resultObj);
                if (is_array($procRlt)) {
                    if (count($procRlt) > 0) {
                        $success = $procRlt[0];
                        if (count($procRlt) > 1) {
                            $rltArr = $procRlt[1];
                        }
                    }
                } else {
                    $success = $procRlt;
                }
            }
            // log_message('error', 'success:' . $success . ' rlt:' . var_export($rltArr, true));
        } else {
            // $apiName, $params, $resultText,
            // $statusCode, $statusText, $extra, $fields
            $this->CI->utils->error_log('ignore call result because call failed', $apiName);
            //$this->CI->utils->error_log('save response result failed', $apiName);
            $rltArr['reason_id']=self::REASON_NETWORK_ERROR;
        }

        if (empty($rltArr)) {
            //reset empty result
            $rltArr=[];
        }

        $rltArr['response_result_id'] = $responseResultId;

        if(!isset($rltArr['external_transaction_id']) && !empty($external_transaction_id)){
            //try get external transaction id
            // $external_transaction_id = $this->searchExternalTransactionIdFromContext($context);
            $rltArr['external_transaction_id']=$external_transaction_id;
        }
        return array_merge(array("success" => $success), $rltArr);
    }

    public function batchCreatePlayer($playerInfos) {
        $success = false;
        $result = array();
        try {

            if ($this->is_array_and_not_empty($playerInfos)) {
                foreach ($playerInfos as $playerName => $playerInfo) {
                    // $playerName = $playerInfo['playerName'];
                    $rlt = $this->createPlayer($playerName, $playerInfo['password'], $playerInfo['email'], $playerInfo['extra']);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerCreated", $result);
    }
    public function batchQueryPlayerInfo($playerNames) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->queryPlayerInfo($playerName);
                    $result[$playerName] = $rlt['playerInfo'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerInfos", $result);
    }
    public function batchBlockPlayer($playerNames) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->blockPlayer($playerName);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerBlocked", $result);
    }
    public function batchUnblockPlayer($playerNames) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->unblockPlayer($playerName);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerUnblocked", $result);
    }
    public function batchDepositToGame($playerDepositInfos) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerDepositInfos)) {
                foreach ($playerDepositInfos as $playerName => $amount) {
                    $rlt = $this->depositToGame($playerName, $amount);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerDeposited", $result);

    }
    public function batchWithdrawFromGame($playerWithdrawInfos) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerWithdrawInfos)) {
                foreach ($playerWithdrawInfos as $playerName => $amount) {
                    $rlt = $this->withdrawFromGame($playerName, $amount);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerWithdrawed", $result);

    }
    public function batchLogin($playerNames) {
        try {
            $success = false;
            $result = array();
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->login($playerName);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerLoggedIn", $result);

    }
    public function batchLogout($playerNames) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->logout($playerName);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerLoggedOut", $result);

    }
    public function batchUpdatePlayerInfo($playerInfos) {
        $success = false;
        $result = array();
        try {

            if ($this->is_array_and_not_empty($playerInfos)) {
                foreach ($playerInfos as $playerName => $playerInfo) {
                    // $playerName = $playerInfo['playerName'];
                    $rlt = $this->updatePlayerInfo($playerName, $playerInfo['password'], $playerInfo['email'], $playerInfo['extra']);
                    $result[$playerName] = $rlt['success'];
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "playerUpdated", $result);

    }
    public function batchQueryPlayerBalance($playerNames, $syncId = null) {
        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }

    public function batchQueryPlayerBalanceOneByOne($playerNames, $syncId = null) {
        $success = true;
        $result = array();
        try {
            if (empty($playerNames)) {
                $players = $this->CI->player_model->getPlayerListOnlyAvailBal($this->getPlatformCode());
                $playerNames = array();
                if (!empty($players)) {
                    foreach ($players as $player) {
                        $username = $player->username;
                        if(!empty($username)){
							$playerNames[$player->playerId]=$username;
                        }
                    }
                }
            }

            if (!empty($playerNames) && is_array($playerNames)) {
				foreach ($playerNames as $playerId=>$playerName) {
					$rlt = $this->queryPlayerBalance($playerName);
					if($rlt['success'] && isset($rlt['balance'])){
						$result[$playerId] = $rlt['balance'];
                        $this->utils->info_log('queryPlayerBalance player id', $playerId, $rlt);
					}
                    // $this->utils->debug_log('query balance', $playerId, $playerName, $rlt);
				}
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }

        return $this->returnResult($success, "balances", $result);

    }

    public function batchTotalBettingAmount($playerNames) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($playerNames)) {
                foreach ($playerNames as $playerName) {
                    $rlt = $this->queryPlayerBalance($playerName);
                    $result[$playerName] = $rlt['success'] ? $rlt['bettingAmount'] : null;
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "totalBettingAmount", $result);

    }
    public function batchQueryTransaction($transactionIds) {
        $success = false;
        $result = array();
        try {
            if ($this->is_array_and_not_empty($transactionIds)) {
                foreach ($transactionIds as $transactionId) {
                    $rlt = $this->queryTransaction($transactionId);
                    $result[$transactionId] = $rlt['success'] ? $rlt['transactionInfo'] : null;
                }
                $success = true;
            }
        } catch (\Exception $e) {
            $this->processError($e);
        }
        return $this->returnResult($success, "transactions", $result);

    }

    protected function returnResult($success, $resultName, $result) {
        return array("success" => $success, $resultName => $result);
    }

    protected function is_array_and_not_empty($arr) {
        return !empty($arr) && is_array($arr);
    }

    protected function processError($e) {
        $this->CI->utils->error_log('error', $e);
        //write error to log
        // log_message("error", $e->getTraceAsString());
    }

    public function getUnknownGame($game_platform_id = null) {
        $this->CI->load->model(array('game_description_model','game_type_model'));

        if(!isset($this->unknownGameDescription) || empty($this->unknownGameDescription)){
            if (empty($game_platform_id)) {
                $game_platform_id = $this->getPlatformCode();
            }
            $this->unknownGameDescription=$this->CI->game_description_model->getUnknownGame($game_platform_id);
        }

        if (empty($this->unknownGameDescription)) {
            $this->getGameTags('unknown',$gameTag);
            $extra = ['game_type_code' =>$gameTag['tag_code'],'game_type'=>$gameTag['tag_name']];
            $gameTypeId = $this->CI->game_type_model->checkGameType($game_platform_id,$gameTag['tag_name'],$extra);
            $this->unknownGameDescription = [
                'game_name' => $gameTag['tag_name'],
                'game_type_id' => $gameTypeId,
                'game_code' => $gameTag['tag_code'],
            ];

            $this->unknownGameDescription = json_decode(json_encode($this->unknownGameDescription));
        }

        return $this->unknownGameDescription;
    }

    private function getGameTags($gameTagCode = null, &$gameTag){
        $this->CI->load->model(array('game_type_model'));
        $gameTags = $this->CI->game_type_model->getAllGameTags();

        foreach ($gameTags as $key => $gameTagDetails) {
            if ($gameTagDetails['tag_code'] == $gameTagCode) {
                $gameTag = $gameTagDetails;
            }
        }
    }

    /**
     * try remove prefix if agent setting
     * @param  string $playerUsername
     * @param  int $playerId
     * @param  int $agentId
     * @return string
     */
    public function removeAgentPrefixFromPlayerUsername($playerUsername, $playerId, $agentId){
        $this->CI->load->model(['agency_model']);
        list($no_prefix_on_username, $player_prefix)=$this->CI->agency_model->getNoPrefixInfoAndPrefixByAgentId($agentId);
        //means remove prefix from username
        // if($no_prefix_on_username==Agency_model::DB_TRUE){
            //remove it
            if($this->CI->utils->startsWith($playerUsername, $player_prefix)){
                $this->CI->utils->debug_log('remove prefix from '.$playerUsername, $no_prefix_on_username, $player_prefix);
                $playerUsername=substr($playerUsername, strlen($player_prefix));
                $this->CI->utils->debug_log('removed prefix', $playerUsername, $player_prefix);
            }
        // }

        return $playerUsername;
    }

    /**
     * convert username to game account
     * @param  string $username
     * @return array
     */
    public function convertUsernameToGame($username) {

        //NOTICE: on gw001, game account doesn't include prefix , even username does
        if($this->CI->utils->getConfig('remove_agent_prefix_on_all_game_account')){
            $this->CI->load->model(['player_model']);
            //search agent
            list($playerId, $agentId)=$this->CI->player_model->getPlayerIdAndAgentIdByUsername($username);
            //found agent, check if we need to remove prefix from username
            if(!empty($agentId) && !empty($playerId)){
                $username=$this->removeAgentPrefixFromPlayerUsername($username, $playerId, $agentId);
            }
        }

        //load config
        $prefix = $this->getSystemInfo('prefix_for_username');
        if (!empty($prefix)) {
            $username = $prefix . $username;
        }

        return $username;
    }
    //player username
    public function blockUsernameInDB($gameUsername) {
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername, $this->getPlatformCode());
        $block = true;
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->updateBlockStatusInDB($playerId, $this->getPlatformCode(), $block);
    }

    public function unblockUsernameInDB($gameUsername) {
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername, $this->getPlatformCode());
        $block = false;
        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->updateBlockStatusInDB($playerId, $this->getPlatformCode(), $block);
    }

    public function isBlockedUsernameInDB($gameUsername) {
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername, $this->getPlatformCode());

        $this->CI->load->model('game_provider_auth');
        return $this->CI->game_provider_auth->isBlockedUsernameInDB($playerId, $this->getPlatformCode());
    }

    public function blockPlayer($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->blockUsernameInDB($gameUsername);
        return array('success' => $success);
    }
    public function unblockPlayer($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->unblockUsernameInDB($gameUsername);
        return array('success' => $success);
    }
    /**
     * is blocked
     * @param  string  $playerUsername
     * @return boolean
     */
    public function isBlocked($playerUsername) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);
        return $this->isBlockedUsernameInDB($gameUsername);
    }
    /**
     * is blocked , return array
     * @param  string  $playerUsername
     * @return array
     */
    public function isBlockedResult($playerUsername) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);
        return ['success'=>true , 'blocked'=>$this->isBlockedUsernameInDB($gameUsername)];
    }

    public function checkLoginToken($playerName, $token) {
        return array('success' => false);
    }

    public function resetPlayer($playerName) {
        return $this->unblockPlayer($playerName);
    }

    public function revertBrokenGame($playerName) {
        return $this->returnUnimplemented($playerName);
    }

    public function insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $respResultId, $transType = null) {
        $this->CI->load->model(array('game_logs'));
        return $this->CI->game_logs->insertGameTransaction($this->getPlatformCode(), $playerId, $gameUsername, $afterBalance, $amount, $respResultId, $transType);
    }

    public function getValueFromApiConfig($key, $defaultValue = null) {
        return $this->getSystemInfo($key, $defaultValue);
    }

    protected function getDefaultAdjustDatetimeMinutes() {
        return 30;
    }

    protected function getDatetimeAdjust() {
        $minutes = $this->getValueFromApiConfig('adjust_datetime_minutes', $this->getDefaultAdjustDatetimeMinutes());
        return '-' . $minutes . ' minutes';
    }

    protected function getDatetimeAdjustSyncOriginal() {
        $minutes = $this->getValueFromApiConfig('adjust_datetime_minutes_sync_original');
        if($minutes===null || $minutes===''){
            $minutes=$this->getValueFromApiConfig('adjust_datetime_minutes', $this->getDefaultAdjustDatetimeMinutes());
        }
        return '-' . $minutes . ' minutes';
    }

    protected function getDatetimeAdjustSyncMerge() {
        $minutes = $this->getValueFromApiConfig('adjust_datetime_minutes_sync_merge');
        if($minutes===null || $minutes===''){
            $minutes=$this->getValueFromApiConfig('adjust_datetime_minutes', $this->getDefaultAdjustDatetimeMinutes());
        }
        return '-' . $minutes . ' minutes';
    }

    protected function getDatetimeAdjustSyncTotal() {
        $adjust_sync_datetime=intval($this->CI->utils->getConfig('adjust_sync_datetime'));
        if(empty($adjust_sync_datetime)){
            //default value
            $adjust_sync_datetime=60;
        }

        return '-' . $adjust_sync_datetime . ' minutes';
    }

    public function transTypeMainWalletToSubWallet() {
        $this->CI->load->model('game_logs');
        return Game_logs::TRANS_TYPE_MAIN_WALLET_TO_SUB_WALLET;
    }

    public function transTypeSubWalletToMainWallet() {
        $this->CI->load->model('game_logs');
        return Game_logs::TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET;
    }

    //like tip, player pay to game platform
    public function transTypeSubWalletToGamePlatform() {
        $this->CI->load->model('game_logs');
        return Game_logs::TRANS_TYPE_SUB_WALLET_TO_GAME_PLATFORM;
    }

    public function transTypeGamePlatformToSubWallet() {
        $this->CI->load->model('game_logs');
        return Game_logs::TRANS_TYPE_GAME_PLATFORM_TO_SUB_WALLET;
    }

    protected function getAllGameUsernames() {
        // $this->CI->load->model('game_provider_auth');
        //load config
        // $active_only_in_api = $this->getConfig('active_only_in_api');
        // return $this->CI->game_provider_auth->getAllGameUsernames($this->getPlatformCode(), $active_only_in_api);

        $this->CI->load->model(['game_logs']);
        //only last one hour
        return $this->CI->game_logs->getPlayerListLastHour($this->getPlatformCode());

    }

    public function processUnknownGame($game_description_id, $game_type_id, $gameNameStr, $gameTypeStr,
                                       $externalGameId, $extra = null, $unknownGame = null) {
        if (empty($game_description_id)) {
            if (empty($unknownGame)) {
                $unknownGame = $this->getUnknownGame();
            }

            $gamePlatformId = $this->getPlatformCode();

            $this->CI->load->model(array('game_description_model'));
            $this->CI->game_description_model->startTrans();

            $extra['flag_new_game'] = Game_description_model::DB_TRUE;
            $extra['flag_show_in_site'] = Game_description_model::DB_FALSE;
            $extra['game_type_code'] = self::TAG_CODE_UNKNOWN_GAME;
            $extra['game_type'] = $gameTypeStr;

            list($game_description_id, $game_type_id) = $this->CI->game_description_model->checkGameDesc(
                $gamePlatformId, $gameNameStr, $externalGameId, $gameTypeStr, $extra);
            if (empty($game_description_id)) {
                //if still failed, use unknown game
                $game_description_id = $unknownGame->id;
                $game_type_id = $unknownGame->game_type_id;
            }
            $succ = $this->CI->game_description_model->endTransWithSucc();
            if (!$succ) {
                $this->CI->utils->debug_log('create new game description failed');
            }
        }

        return array($game_description_id, $game_type_id);
    }

    const BET_DETAILS_INVALID = "Invalid";

    /**
     *
     * sync data to game logs table
     * business logic here
     *
     * @param  int $game_type_id        game type id
     * @param  int $game_description_id game description id
     * @param  string $game_code           game code
     * @param  string $game_type           game type
     * @param  string $game                game name from original
     * @param  int $player_id           player id
     * @param  string $gameUsername     username on game provider
     * @param  double $bet_amount          valid bet amount
     * @param  double $result_amount       result of game, doesn't include bet amount
     * @param  double $win_amount          real win amount
     * @param  double $loss_amount         real loss amount
     * @param  double $after_balance       after balance
     * @param  int $has_both_side       bet both, like player and banker on baccart
     * @param  string $external_uniqueid
     * @param  string $start_at
     * @param  string $end_at
     * @param  int $response_result_id
     * @param  int $flag
     * @param  array $extra               other fields, odds/trans_amount/real_betting_amount/bet_for_cashback
     * @return bool
     */
    public function syncGameLogs($game_type_id, $game_description_id, $game_code,
                $originalGameTypeName, $originalGameName, $player_id, $gameUsername,
                $bet_amount, $result_amount, $win_amount, $loss_amount, $after_balance, $has_both_side,
                $external_uniqueid, $start_at, $end_at, $response_result_id, $flag = Game_logs::FLAG_GAME,
                $extra = null, $sportsGameFields = null, &$execType='insert') {

        if (empty($player_id)) {
            $player_id = $this->getPlayerIdInGameProviderAuth($gameUsername);
        }
        if (empty($player_id)) {
            $this->CI->utils->debug_log('ignore player', $gameUsername);
            return false;
        }
        if (empty($result_amount)) {
            $result_amount = 0;
        }
        if (empty($win_amount)) {
            $win_amount = $result_amount > 0 ? $result_amount : 0;
        }
        if (empty($loss_amount)) {
            $loss_amount = $result_amount < 0 ? abs($result_amount) : 0;
        }
        if (empty($has_both_side)) {
            $has_both_side = 0;
        }
        if (empty($after_balance)) {
            $after_balance = 0;
        }

        $real_betting_amount=$bet_amount;
        if(isset($extra['trans_amount'])){
            $real_betting_amount=$extra['trans_amount'];
            unset($extra['trans_amount']);
        }else if(isset($extra['real_betting_amount'])){
            $real_betting_amount=$extra['real_betting_amount'];
            unset($extra['real_betting_amount']);
        }

        $bet_for_cashback=$bet_amount;
        if(isset($extra['bet_for_cashback'])){
            $bet_for_cashback=$extra['bet_for_cashback'];
            unset($extra['bet_for_cashback']);
        }

        // $this->CI->utils->debug_log('bet_for_cashback', $bet_for_cashback);

        if(isset($extra['running_platform'])){
            $running_platform=$extra['running_platform'];
            unset($extra['running_platform']);
        }else{
            $running_platform=$this->getCommonRunningPlatform();
        }

        $odds=0;
        if(isset($extra['odds'])){
            $odds=$extra['odds'];
            unset($extra['odds']);
        }elseif(isset($extra['note'])){
            $betDetail=$this->CI->utils->decodeJson($extra['note']);
            if(!empty($betDetail) && isset($betDetail['rate'])){
                $odds=$betDetail['rate'];
            }
        }

        # Specify the type of odds used in this data, possible value: eu, hk
        # This (for now) will not be recorded in game logs DB but will be used to determine bet_for_cashback
        $odds_type = '';
        if(isset($extra['odds_type'])) {
            $odds_type = $extra['odds_type'];
            // unset($extra['odds_type']);
        }

        // $date_from=null;
        // if(isset($extra['query_date_from'])) {
        //     $date_from = $extra['query_date_from'];
        //     unset($extra['query_date_from']);
        // }
        // $date_to=null;
        // if(isset($extra['query_date_to'])) {
        //     $date_to = $extra['query_date_to'];
        //     unset($extra['query_date_to']);
        // }

        $game_platform_id = $this->getPlatformCode();
        if(isset($extra['game_platform_id'])) {
            //overwrite
            $game_platform_id = $extra['game_platform_id'];
            unset($extra['game_platform_id']);
        }

        $data = array(
            'game_platform_id' => $game_platform_id,
            'game_type_id' => $game_type_id,
            'game_description_id' => $game_description_id,
            'game_code' => $game_code,
            'game_type' => $originalGameTypeName,
            'game' => $originalGameName,
            'player_id' => $player_id,
            'player_username' => $gameUsername,
            'bet_amount' => $bet_amount,
            'real_betting_amount' => $real_betting_amount,
            'trans_amount' => $real_betting_amount,
            'bet_for_cashback' => $bet_for_cashback,
            'odds' => $odds,
            'result_amount' => $result_amount,
            'win_amount' => $win_amount,
            'loss_amount' => $loss_amount,
            'after_balance' => $after_balance,
            'has_both_side' => $has_both_side,
            'external_uniqueid' => $external_uniqueid,
            'response_result_id' => $response_result_id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'flag' => $flag,
            'running_platform' => $running_platform ,
            'odds_type' => $odds_type,
        );
        if (!empty($extra)) {
            $data = array_merge($data, $extra);
        }
        if(!empty($sportsGameFields)){
            $data = array_merge($data, $sportsGameFields);
        }

        # Invalidate / adjust bets
        # odds_type is defined in actual API implementation, can be hk or eu
        # If this is defined, means there will be odds and we need to apply the adjustment
        if (!empty($data['odds_type'])) {
            $odds_type = $data['odds_type'];
            // unset($data['odds_type']);//will save odds type

            # Sample config
            # $config['adjust_bet_by_odds'] = array(
            # IBC_API => array(
            #     'hk' => 0.5,
            #     'eu' => 1.5,
            #     'method' => 'invalidate',
            #     'invalid-game-types' => [134,135]
            # ))
            $adjust_bet_by_odds = $this->CI->utils->getConfig('adjust_bet_by_odds');
            // $api_id = $data['game_platform_id'];
            if(!empty($adjust_bet_by_odds) && array_key_exists($game_platform_id, $adjust_bet_by_odds)) {
                $adjust_params = $adjust_bet_by_odds[$game_platform_id];
                $odd_threshold = $adjust_params[$odds_type];
                $bet_adjust_method = $adjust_params['method'];

                if($data['odds'] < $odd_threshold) {
                    if($bet_adjust_method == 'invalidate') {
                        $data['bet_for_cashback'] = 0;
                        $data['bet_details'] = json_encode(array("Status" => self::BET_DETAILS_INVALID));
                    } elseif($bet_adjust_method == 'adjust') {
                        $odds = $data['odds'] - ($odds_type == 'eu' ? 1 : 0);
                        $data['bet_for_cashback'] *= $odds;
                    }
                    $this->CI->utils->debug_log("adjust_bet_by_odds: Record's odds [$data[odds]] less than threshold [$odd_threshold], based on method [$bet_adjust_method], adjusted bet_for_cashback as [$data[bet_for_cashback]]");
                }

                # Invalidate tie bet
                if (isset($data['result_amount']) && abs($data['result_amount']) < 0.0001) {
                    $data['bet_for_cashback'] = 0;
                    $data['bet_details'] = json_encode(array("Status" => self::BET_DETAILS_INVALID));
                }

                # Invalidate configured game types, e.g. horse racing from IBC
                if (array_key_exists('invalid-game-types', $adjust_params)){
                    $invalid_game_types = $adjust_params['invalid-game-types'];
                    if(is_array($invalid_game_types) && in_array($data['game_type_id'], $invalid_game_types)) {
                        # Detected current game log is from an invalid game type
                        $data['bet_for_cashback'] = 0;
                        $data['bet_details'] = json_encode(array("Status" => self::BET_DETAILS_INVALID));
                    }
                }
            }
        }
        # odds_type is not a db column, unset to avoid inserting it
        // if(isset($data['odds_type'])) {
            // unset($data['odds_type']);will save odds type
        // }

        // $betDetailsExistInGamelogs = $this->getGamelogsBetDetailsByExternalUniqueId($data['external_uniqueid']);

        if (isset($data['bet_details'])) {
            $bet_details = is_array($data['bet_details']) ? $data['bet_details'] : json_decode($data['bet_details'], true);
            // $betDetailsExistInGamelogs = json_decode($this->getGamelogsBetDetailsByExternalUniqueId($data['external_uniqueid']), true);
            if(!empty($bet_details)){
                $bet_details['Created At'] = $this->CI->utils->getNowForMysql();
                // if (!empty($betDetailsExistInGamelogs) && isset($betDetailsExistInGamelogs['Created At'])) {
                //  $bet_details['Created At'] = $betDetailsExistInGamelogs['Created At'];
                // }
                $data['bet_details'] = json_encode($bet_details);
            }else{
                $data['bet_details'] = json_encode(array("Created At" => $this->CI->utils->getNowForMysql()));
            }
        } else {
            $data['bet_details'] = json_encode(array("Created At" => $this->CI->utils->getNowForMysql()));
        }

        return $this->CI->game_logs->syncToGameLogs($data, $execType);

    }

    /**
     * game time + 12 = server time
     *
     */
   public function getGameTimeToServerTime() {
        return $this->getSystemInfo('gameTimeToServerTime','+0 hours');
    }

    public function getServerTimeToGameTime() {
        return $this->getSystemInfo('serverTimeToGameTime','-0 hours');
    }

    /**
     *
     * @param  string $dateTimeStr
     * @return string
     */
   public function gameTimeToServerTime($dateTimeStr) {
        if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
            $dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
        }
        $modify = $this->getGameTimeToServerTime();
        return $this->utils->modifyDateTime($dateTimeStr, $modify);
    }

    /**
     *
     * @param  string $dateTimeStr
     * @return string
     */
    public function serverTimeToGameTime($dateTimeStr) {
        if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
            $dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
        }
        $modify = $this->getServerTimeToGameTime();
        return $this->utils->modifyDateTime($dateTimeStr, $modify);
    }

    public function isPrintVerbose() {
        return $this->getConfig('print_verbose_game_api');
    }

    public function getPlayerInfoByUsername($username) {
        $this->CI->load->model('player_model');
        return $this->CI->player_model->getPlayerByUsername($username);
    }

    public function batchQueryPlayerBalanceOnlyAvailable($syncId = null) {
        $playerNames = $this->getAvailablePlayerNames();
        if (!empty($playerNames)) {
            $this->CI->utils->debug_log('playerNames', count($playerNames));
            //load
            //batchQueryPlayerBalance($playerNames, $syncId = null);
            $this->batchQueryPlayerBalance($playerNames, $syncId);
        }
        return $this->returnUnimplemented();
    }

    public function getAvailablePlayerNames() {
        $this->CI->load->model(array('wallet_model'));
        //only balance of sub-wallet > 0
        return $this->CI->wallet_model->getAvailableBalancePlayerNames($this->getPlatformCode());
    }

    public function useQueryAvailableBalance() {
        return false;
    }

    public function checkPlayerExistInDB($playerName) {
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $this->CI->load->model(array('game_provider_auth'));
        $this->CI->game_provider_auth->getOrCreateLoginInfoByPlayerId(
            $playerId, $this->getPlatformCode());
        return $playerId;
    }

    public function quickCheckAccount($player_id) {
        //check register flag
        $this->CI->load->model(array('game_provider_auth'));
        $rlt = array('success' => true);
        if (!$this->CI->game_provider_auth->isRegisterd($player_id, $this->getPlatformCode())) {
            $this->CI->load->library('salt');
            $player = $this->getPlayerInfo($player_id);

            $decryptedPwd = $this->CI->salt->decrypt($player->password, $this->getConfig('DESKEY_OG'));
            $rlt = $this->createPlayer($player->username, $player->playerId, $decryptedPwd);
            if ($rlt && $rlt['success']) {
                $this->updateRegisterFlag($player_id, Abstract_game_api::FLAG_TRUE);
            }
        }

        return $rlt;
    }

    public function getPlayerTokenByUsername($playerUsername) {

        $playerId = $this->getPlayerIdFromUsername($playerUsername);
        if (!empty($playerId)) {
            return $this->getPlayerToken($playerId);
        } else {
            return null;
        }

    }

    public function getPlayerToken($playerId) {
        $this->CI->load->model(array('common_token'));
        return $this->CI->common_token->getPlayerToken($playerId);
    }

    public function getPlayerInfoByToken($token) {
        $this->CI->load->model(array('common_token', 'player_model'));
        return $this->CI->common_token->getPlayerInfoByToken($token);
    }

    public function getPlayerInfoByOldToken($token) {
        $this->CI->load->model(array('common_token', 'player_model'));
        return $this->CI->common_token->getPlayerInfoByOldToken($token);
    }

    public function getPlayerIdByToken($token) {
        $this->CI->load->model(array('common_token', 'player_model'));
        return $this->CI->common_token->getPlayerIdByToken($token);
    }

    public function asyncCall($funcName, $params, $callerType = null, $caller = null, $state = null) {
        $this->CI->load->library(array('lib_queue'));
        $this->CI->load->model(array('queue_result'));
        if ($callerType == null) {
            $callerType = Queue_result::CALLER_TYPE_ADMIN;
        }
        if ($caller == null) {
            $caller = 1;
        }
        $token = $this->CI->lib_queue->addApiJob($this->getPlatformCode(),
            $funcName, $params, $callerType, $caller, $state);
        return $token;
    }

    public function syncPlayerAccount($username, $password, $playerId) {
        return $this->returnUnimplemented();
    }

    public function decryptPassword($encryptedPassword) {
        $this->CI->load->library('salt');
        return $this->CI->salt->decrypt($encryptedPassword, $this->getConfig('DESKEY_OG'));
    }

    public function syncAllPlayerAccount() {
        //sync all player
        $this->CI->load->model(array('player_model'));
        $players = $this->CI->player_model->getPlayerListOnlyAvailBal($this->getPlatformCode());
        $result = array();
        foreach ($players as $player) {
            $username = $player->username; //$this->getGameUsernameByPlayerUsername($player->username);
            $password = $this->decryptPassword($player->password);
            $playerId = $player->playerId;

            $this->CI->utils->debug_log('username', $username, 'balance', $player->bal);

            $rlt = $this->syncPlayerAccount($username, $password, $playerId);
            $result[] = array('username' => $username, 'success' => $rlt['success'], 'old balance' => $player->bal, 'new balance' => $rlt['balance']);
        }
        return array('success' => true, 'result' => $result);
    }

    public function isActive() {
        $this->CI->load->model(array('external_system'));
        $isActive = $this->CI->external_system->isGameApiActive($this->getPlatformCode());
        return $isActive;
    }

    // public function alwaysActive() {
    // 	return $this->getConfig('enable_always_active_for_api') && $this->getSystemInfo('always_active');
    // }

    # START - ALL ABOUT GAME TYPE LIST AND GAME LIST ##############################################################################################################

    # EXTRA INFO
    # game_image_directory
    # game_image_extension
    # game_image_default

    public function getGameTypeList($where = null, $limit = null, $offset = null) {

        $this->CI->load->model(array('game_description_model'));

        $game_platform_id = $this->getPlatformCode();

        if ($where) {
            $this->CI->db->where($where);
        }

        $this->CI->db->select('game_type.*');
        $this->CI->db->from('game_type');
        $this->CI->db->join('external_system', 'external_system.id = game_type.game_platform_id');
        $this->CI->db->where('external_system.id', $game_platform_id);
        $this->CI->db->where('game_type.flag_show_in_site', 1);

        $total = $this->CI->db->count_all_results('', false);

        if ($limit) {
            $this->CI->db->limit($limit, $offset);
        }

        $this->CI->db->order_by('(-game_type.order_id)', 'desc'); # ORDER BY order_id ASC NULLS LAST
        $this->CI->db->order_by('game_type.game_type_lang', 'asc');

        $query = $this->CI->db->get();
        $list = $query->result_array();

        array_walk($list, function (&$gameType) {
            $gameType['is_mobile_enabled'] = $this->CI->game_description_model->isGameTypeHasMobileVersion($gameType['id']);
            $gameType['game_type'] = lang($gameType['game_type']);
            $gameType['game_type_lang'] = lang($gameType['game_type_lang']);
        });

        return array(
            'list' => $list,
            'count' => $query->num_rows(),
            'limit' => intval($limit),
            'offset' => intval($offset),
            'total' => $total,
        );
    }

    #####################################################################
    # NOTE:
    # Example: MG
    #####################################################################
    public function getGameList($game_type_id = null, $where = null, $limit = null, $offset = null, $full_game_list = null) {

        $game_platform_id = $this->getPlatformCode();

        if ($game_type_id) {
            $this->CI->db->where('game_description.game_type_id', $game_type_id);
        }

        if ($where) {
            $this->CI->db->where($where);
        }

        $this->CI->db->select('game_description.*');
        $this->CI->db->from('game_description');
        $this->CI->db->join('game_type', 'game_type.id = game_description.game_type_id');
        $this->CI->db->join('external_system', 'external_system.id = game_type.game_platform_id');
        $this->CI->db->where('external_system.id', $game_platform_id);
        $this->CI->db->where('game_description.flag_show_in_site', 1);
        $this->CI->db->where('game_type.flag_show_in_site', 1);
        $this->CI->db->where('game_description.game_code !=', '');

        $total = $this->CI->db->count_all_results('', false);

        if ($limit) {
            $this->CI->db->limit($limit, $offset);
        }

        $this->CI->db->order_by('(-game_type.order_id)', 'desc'); # ORDER BY game_type.order_id ASC NULLS LAST
        $this->CI->db->order_by('game_type.game_type_lang', 'asc');
        $this->CI->db->order_by('(-game_description.game_order)', 'desc'); # ORDER BY game_description.game_order ASC NULLS LAST
        $this->CI->db->order_by('game_description.game_name', 'asc');

        $query = $this->CI->db->get();

        # NOTE: You can override keys depending on your template
        if ($full_game_list) {
            $result = $query->result_array();
        }else{
            $result  = array(
                'l' => array_map(array($this, 'processGameList'), $query->result_array()),
                'p' => $this->getGameImageDirectory(),
                'count' => $query->num_rows(),
                'limit' => intval($limit),
                'offset' => intval($offset),
                'total' => $total,
            );
        }

        return $result;
    }

    #####################################################################
    # NOTE:
    # Example: PT
    #####################################################################
    public function processGameList($game) {

        $game_image = $game['game_code'] . '.' . $this->getGameImageExtension();
        $game_image = $this->checkIfGameImageExist($game_image) ? $game_image : $this->getDefaultGameImage();

        return array(
            'c' => $game['game_code'], # C - GAME CODE
            'n' => lang($game['game_name']), # N - GAME NAME
            'i' => $game_image, # I - GAME IMAGE
            'g' => $game['game_type_id'], # G - GAME TYPE ID
            'r' => $game['offline_enabled'] == 1, # R - TRIAL
            'm' => $game['mobile_enabled'],	# M -  MOBILE ENABLED STATUS
        );
    }

    public function checkIfGameImageExist($game_image) {
        return true; # TODO:
    }

    public function getGameImageDirectory() {
        return $this->getSystemInfo('game_image_directory') ?: '/game_images/' . $this->getPlatformCode() . '/';
    }

    public function getGameImageExtension() {
        return $this->getSystemInfo('game_image_extension') ?: 'jpg';
    }

    public function getDefaultGameImage() {
        return $this->getSystemInfo('game_image_default') ?: $this->getPlatformCode() . '.' . $this->getGameImageExtension();
    }

    # END - ALL ABOUT GAME TYPE LIST AND GAME LIST ##############################################################################################################

    public function loginGameProviderAuth($gameUsername, $password) {
        if (!empty($gameUsername)) {
            $this->CI->load->model('game_provider_auth');
            return $this->CI->game_provider_auth->loginGameProviderAuth($gameUsername, $password, $this->getPlatformCode());
        }
        return false;
    }

    //====generate fake game logs============================================================
    public function generateFakeGameLogsForRegisteredPlayer($gamePlatformId, $playerName, $numberOfGameLogs = 10) {
        $this->load->model(array('ebet_game_logs'));
        for ($i = 0; $i < $numberOfGameLogs; $i++) {
            $gameLogData = $this->getFakeGameLogs($gamePlatformId, $playerName);
            if ($gameLogData) {
                $this->ebet_game_logs->insertEbetGameLogs($gameLogData);
            }
        }
    }

    public function getFakeGameLogs($game_platform_id, $playerName) {
        $this->load->model("game_description_model");
        $api_game_name = $this->game_description_model->getGameDescriptionListByGamePlatformId($game_platform_id, 'game_name');

        if ($api_game_name) {
            $game_api = array(EBET_API => $api_game_name);

            if (!array_key_exists($game_platform_id, $game_api)) {
                return;
            }

            $rand_game_name = array_rand($game_api[$game_platform_id], 1);
            $game_name = $game_api[$game_platform_id][$rand_game_name];
            $game_code = $this->game_description_model->getGameCodeByGameName($game_name, $game_platform_id);
            $uniqueid = uniqid();
            $result_amount = mt_rand(-50, 50);
            $bet_amount = $result_amount < 0 ? abs($result_amount) : mt_rand(10, 50);
            $now = $this->utils->getNowForMysql();
            $game_api_fields = array(
                EBET_API => array(
                    'gameType' => $game_name,
                    'gameshortcode' => $game_code,
                    'roundNo' => random_string('numeric'),
                    'payout' => $result_amount,
                    'createTime' => $now,
                    'payoutTime' => $now,
                    'betHistoryId' => random_string('numeric'),
                    'validBet' => $bet_amount,
                    'userId' => $playerName,
                    'username' => $playerName,
                    'uniqueid' => $uniqueid,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
                PT_API => array(
                    'playername' => $playerName,
                    'gamename' => $playerName,
                    'gameshortcode' => $game_code,
                    'gamecode' => $game_code,
                    'bet' => $bet_amount,
                    'win' => $result_amount,
                    'gamedate' => $now,
                    'sessionid' => random_string('numeric'),
                    'gametype' => $game_name,
                    'currentbet' => $currentbet,
                    'gameid' => $game_code,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
                MG_API => array(
                    'row_id' => $uniqueid,
                    'account_number' => $playerName,
                    'display_name' => $game_name,
                    'gamecode' => $game_code,
                    'session_id' => $uniqueid,
                    'total_payout' => $result_amount,
                    'total_wager' => $bet_amount,
                    'game_end_time' => $now,
                    'uniqueid' => $uniqueid,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
                BBIN_API => array(
                    'username' => $playerName,
                    'wagers_id' => $uniqueid,
                    'wagers_date' => $now,
                    'game_type' => $game_code,
                    'result' => $result_amount,
                    'bet_amount' => $bet_amount,
                    'payoff' => $result_amount,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
                LB_API => array(
                    'member_id' => $playerName,
                    'bet_id' => $uniqueid,
                    'wagers_date' => $now,
                    'match_id' => $game_code,
                    'bet_money' => $bet_amount,
                    'bet_winning' => $result_amount,
                    'bet_winning' => $result_amount < 0 ? 'lost' : 'win',
                    'bet_status' => 'settled',
                    'bet_time' => $now,
                    'trans_time' => $now,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
                IBC_API => array(
                    'trans_id' => $uniqueid,
                    'player_name' => $playerName,
                    'transaction_time' => $now,
                    'match_id' => $game_code,
                    'league_id' => $game_code,
                    'stake' => $bet_amount,
                    'winlose_amount' => $result_amount,
                    'external_uniqueid' => $uniqueid,
                    'response_result_id' => random_string('numeric'),
                ),
            );
            return $game_api_fields[$game_platform_id];
        }
    }

    public function generateTransferId($transferId = null) {
        if (empty($transferId)) {
            $transferId = 'T' . date('YmdHis') . random_string('numeric', 8);
        }
        return $transferId;
    }

    public function replaceNullValue($arr){
        if(!empty($arr)){
            foreach ($arr as $key => &$value) {
                if($value===null){
                    $value='';
                }
            }
        }
        return $arr;
    }

    public function createHtmlForm($urlForm) {
        $formId = 'f_' . random_string('unique');
        $method = $urlForm['post'] ? 'POST' : 'GET';
        $html = '<form name="' . $formId . '" id="' . $formId . '" method="' . $method . '" action="' . $urlForm['url'] . '">';
        if (!empty($urlForm['params'])) {
            foreach ($urlForm['params'] as $name => $val) {
                $html = $html . "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . htmlentities($val) . "\">\n";
            }
        }
        $html = $html . '</form>';
        return array($html, $formId);
    }

    public function convertTransactionAmount($amount){
        return $amount;
    }

    public function updateExternalSystemExtraInfo($gamePlatformId, $data) {
        $this->CI->load->model('external_system');
        return $this->CI->external_system->updateExtraInfoByGamePlatformId($gamePlatformId, $data);
    }

    public function gameAmountToDB($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount / $conversion_rate);
        return round($value,2);
        // return $amount / $conversion_rate;
    }

    public function convertAmountToDB($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount / $conversion_rate);
		return $this->roundDownAmount($value);
    }

	private function roundDownAmount($number){
		$conversion_precision = floatval($this->getSystemInfo('conversion_precision', 2));
		$fig = (int) str_pad('1', $conversion_precision+1, '0');
		return (floor($number * $fig) / $fig);
	}

    public function dBtoGameAmount($amount) {
        $conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
        $value = floatval($amount * $conversion_rate);
        return round($value,2);
        // return $amount * $conversion_rate;
    }

    private function buildForwardUrl($protocol, $domain, $token, $game_code, $language, $mode, $platform, $game_type, $merchant_code, $redirection, $extra){
        $forward_url=$protocol.'://'.$domain.'/player_center/launch_game_with_token/'.$token.'/'.$this->getPlatformCode().'/'.$game_code.'/'.$language.'/'.$mode.'/'.$platform.'/'.$game_type.'/'.$merchant_code.'/'.$redirection;
        if($this->CI->utils->isEnabledMDB() && $extra['append_target_db']){
            if(empty($extra)){
                $extra=[];
            }
            $extra[Multiple_db::__OG_TARGET_DB]=$this->CI->utils->getActiveTargetDB();
        }
        if(!empty($extra) && is_array($extra)){
            $forward_url.= '?'.http_build_query($extra);
        }

        return $forward_url;
    }

    /**
     * get goto url, can be overwriiten by sub-class
     * @param string  $playerName
     * @param array $launcher_settings
     * @param string $merchant_code
     * @return array
     */
    public function getGotoUrl($playerName, $launcher_settings,$merchant_code = null){

//        $success=$this->enabled_forward_white_domain && !empty($this->white_launcher_domain);
//
//        $this->CI->utils->debug_log('forwardToWhiteDomain, enabled_forward_white_domain', $this->enabled_forward_white_domain,
//            'white_launcher_domain', $this->white_launcher_domain, 'playerName', $playerName, 'nextUrl', $nextUrl);
//
//        $forward_url=null;
//        if($success){

//            if($this->white_launcher_domain!=$this->getCurrentDomain()){

        $domain=$this->CI->utils->getSystemHost('player');

        $useWhiteDomain=$this->enabled_forward_white_domain && !empty($this->white_launcher_domain);

//        $domain=$this->getCurrentDomain();
        if($useWhiteDomain){
            $domain=$this->white_launcher_domain;
        }

        $this->CI->load->model(array('player_model'));

        $playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
        //TODO cache token
        $token=$this->getPlayerToken($playerId);

//        $game_code = '_null', $language = 'zh-cn', $game_mode = 'real', $platform = '_null', $game_type = '_null'

        $game_code=@$launcher_settings['game_unique_code'];
        if(empty($game_code)){
            if($game_code===0 || $game_code==='0'){
                $game_code = $game_code;
            }else{
                $game_code='_null';
            }
        }

        $language=@$launcher_settings['language'];
        if(empty($language)){
            $language='_null';
        }

        $mode=@$launcher_settings['mode'];
        if(empty($mode)){
            $mode='_null';
        }

        $platform=@$launcher_settings['platform'];
        if(empty($platform)){
            $platform='_null';
        }

        $game_type=@$launcher_settings['game_type'];
        if (empty($game_type)) {
            if($game_type===0 || $game_type==='0'){
                $game_type = $game_type;
            } else {
                $game_type='_null';
            }
        }

        $redirection=@$launcher_settings['redirection'];
        if(empty($redirection)){
            $redirection='_null';
        }
        //it's boolean
        $try_get_real_url=isset($launcher_settings['try_get_real_url']) ? $launcher_settings['try_get_real_url'] : null;
        // $try_get_real_url=$try_get_real_url=='true';

        $this->CI->utils->debug_log('Launcher settings =================> ', $launcher_settings,'TOKEN: ==>> ',$token);
        $protocol=$this->getCurrentProtocol();
        $extra=isset($launcher_settings['extra']) ? $launcher_settings['extra'] : null;
        if(empty($extra)){
            $extra=[];
        }
        if(isset($launcher_settings['home_link'])){
            $extra['home_link']=$launcher_settings['home_link'];
        }
        if(isset($launcher_settings['cashier_link'])){
            $extra['cashier_link']=$launcher_settings['cashier_link'];
        }
        if(isset($launcher_settings['append_target_db'])){
            $extra['append_target_db']=boolval($launcher_settings['append_target_db']);
        }else{
            $extra['append_target_db']=false;
        }
        $forward_url=$this->buildForwardUrl($protocol, $domain, $token, $game_code, $language, $mode, $platform, $game_type, $merchant_code, $redirection, $extra);
        // $this->getCurrentProtocol().'://'.$domain.'/player_center/launch_game_with_token/'.$token.'/'.$this->getPlatformCode().'/'.$game_code.'/'.$language.'/'.$mode.'/'.$platform.'/'.$game_type.'/'.$merchant_code.'/'.$redirection;

        // if(!empty(@$launcher_settings['extra']) && is_array($launcher_settings['extra'])){
        //     $forward_url.= '?'.http_build_query($launcher_settings['extra']);
        // }

        $this->CI->utils->debug_log($this->getPlatformCode().' get goto url', $forward_url);

        if($try_get_real_url){
            $game_platform_id=$this->getPlatformCode();
            $this->CI->utils->debug_log('try get_real_url', $game_platform_id, $this->is_enabled_direct_launcher_url, $useWhiteDomain );
            //it will try get real url
            if($this->is_enabled_direct_launcher_url && !$useWhiteDomain){
                $this->CI->load->model(['game_provider_auth']);
                $loginInfo = $this->CI->game_provider_auth->getLoginInfoByPlayerId($playerId, $game_platform_id);
                if(!empty($loginInfo)){
                    $game_name=isset($extra['game_name']) ? $extra['game_name'] : null;
                    $side_game_api=isset($extra['side_game_api']) ? $extra['side_game_api'] : null;
                    if (empty($platform)) {
                        $is_mobile = $this->utils->is_mobile();
                    } else {
                        $is_mobile = $platform == 'mobile';
                    }
                    $rlt=$this->commonGetGotoUrl($playerName, $loginInfo->password,
                        $game_code, $mode, $game_type, $game_name, $language, $side_game_api, $is_mobile, $extra);

                    $this->CI->utils->debug_log('commonGetGotoUrl', $rlt);
                    if(!empty($rlt) && $rlt['success'] && !empty($rlt['url'])){
                        $forward_url=$rlt['url'];

                        $this->CI->utils->debug_log('replace forward_url', $forward_url);
                    }
                }

                //redirect only
                // $redirection='newtab';
                // $forward_url=$this->buildForwardUrl($protocol, $domain, $token, $game_code, $language, $mode, $platform, $game_type, $merchant_code, $redirection, $extra);
                // //http call
                // //($header, $content, $statusCode, $statusText, $errCode, $error, null);
                // list($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj) = $this->CI->utils->callHttp($forward_url, 'GET', null);
                // $headerArr = array_map(function($x) { return array_map("trim", explode(":", $x, 2)); },array_filter(array_map("trim", explode("\n", $header))));
                // $this->CI->utils->debug_log('headerArr', $headerArr);
                // // $this->httpCallApi($forward_url);
                // // $this->queryForwardGame();
                // if(!empty($headerArr)){
                //     $found302=strpos($headerArr[0][0], '302');
                //     $realUrl=null;
                //     foreach ($headerArr as $headerRow) {
                //          if($headerRow[0]=='Location'){
                //              $realUrl=$headerRow[1];
                //          }
                //     }
                //     $this->CI->utils->debug_log('found302', $found302, 'realUrl', $realUrl);
                //     //TODO check if full url, maybe need to call again
                //     if($found302 && !empty($realUrl)){
                //         $forward_url=$realUrl;
                //     }
                // }
            }else{
                $this->CI->utils->debug_log('not is_enabled_direct_launcher_url or useWhiteDomain', $game_platform_id, $this->is_enabled_direct_launcher_url, $useWhiteDomain);
            }
        }

        $success=true;

                // redirect($forward_url);

//            }else{
//                $this->CI->utils->debug_log($this->getPlatformCode().' same white domain');
//                $success = false;
//            }
//        }else{
//            $this->CI->utils->debug_log($this->getPlatformCode().' donot use white domain');
//        }

        return ['success'=>$success, 'url'=> $forward_url];
    }

    /**
     * commonGetGotoUrl will call query forward game
     *
     * @param  string $playerName
     * @param  string $gameAccountPassword
     * @param  string $game_code
     * @param  string $game_mode
     * @param  string $game_type
     * @param  string $game_name
     * @param  string $language
     * @param  string $side_game_api
     * @param  boolean $is_mobile
     * @param  string $extra
     * @return array
     */
    public function commonGetGotoUrl($playerName, $gameAccountPassword, $game_code, $game_mode, $game_type, $game_name, $language, $side_game_api, $is_mobile, $extra){

        $this->CI->utils->debug_log('params of commonGetGotoUrl', $playerName, $gameAccountPassword, $game_code, $game_mode, $game_type, $game_name, $language, $side_game_api, $is_mobile, $extra);

        $rlt = $this->queryForwardGame(
            $playerName,
            array(
                'game_code' => $game_code,
                'game_mode' => $game_mode,
                'game_type' => $game_type,
                'game_name' => $game_name,
                'password' => $gameAccountPassword,
                'language' => $language,
                'side_game_api' => $side_game_api,
                'is_mobile' => $is_mobile,
                'extra' => $extra,
            )
        );

        $this->CI->utils->debug_log('result of commonGetGotoUrl', $rlt);

        return $rlt;
    }

    public function getSecureId($tableName, $fldName, $needUnique, $prefix, $random_length = 12) {
        $this->CI->load->model('player_model');
        return $this->CI->player_model->getSecureId($tableName, $fldName, $needUnique, $prefix);
    }

    public function isRedirectLaunchGame($is_mobile){
        return $is_mobile===true || $is_mobile=='true' || $is_mobile=='1';
    }

    public function queryBetDetailLink($playerUsername, $betid = null, $extra=null) {
        return ['success'=>false, 'url'=>null];
    }

    public function onlyTransferPositiveInteger(){
        return false;
    }

    public function setBetDetails($fields) {
        return false;
    }

    public function getCommonRunningPlatform(){
        return self::PLATFORM_UNKNOWN;
    }

    public function analyzeRunningPlatform($row){
        return self::PLATFORM_UNKNOWN;
    }

    public function checkGameIpWhitelistByGameProvider($visitorIp){
        $IPwhitelisting = $this->getSystemInfo('enabled_callback_whitelist', false);
        $whitelist_ips = $this->getSystemInfo('callback_whitelist_ip', false);

        $this->utils->debug_log('the visitor IP ------> : ', $visitorIp, 'whitelisted ips : ', $whitelist_ips);

        # check true
        if(!$IPwhitelisting){
            return true;
        }else{
            if(!$whitelist_ips){
                return false;
            }else{
               if(in_array($visitorIp, $whitelist_ips)){
                   return true;
               }else{
                   return false;
               }
            }
        }
    }

    public function syncOriginalGameResult($token) {
        return $this->returnUnimplemented();
    }

    public function isSeamLessGame(){
        return false;
    }

    public function isValidTransferStatus($status){
        return in_array($status, [self::COMMON_TRANSACTION_STATUS_PROCESSING,
            self::COMMON_TRANSACTION_STATUS_DECLINED, self::COMMON_TRANSACTION_STATUS_APPROVED,
            self::COMMON_TRANSACTION_STATUS_UNKNOWN]);
    }

    public function translateTransferStatus($status){
        switch ($status) {
            case self::COMMON_TRANSACTION_STATUS_PROCESSING:
                return lang('Processing');
                break;

            case self::COMMON_TRANSACTION_STATUS_DECLINED:
                return lang('Declined');
                break;

            case self::COMMON_TRANSACTION_STATUS_APPROVED:
                return lang('Approved');
                break;
            default:
                return lang('Unknown');
                break;
        }
    }

    //===reaon of api failed=============================================================================
    const REASON_NOT_FOUND_PLAYER=1;
    const REASON_NO_ENOUGH_BALANCE=2;
    //means don't call processResultForXXXX function
    const REASON_NETWORK_ERROR=3;
    const REASON_GAME_ACCOUNT_LOCKED=4;
    const REASON_TRANSFER_AMOUNT_IS_TOO_HIGH=5;
    const REASON_TRANSFER_AMOUNT_IS_TOO_LOW=6;
    const REASON_LOGIN_PROBLEM=7;
    const REASON_SESSION_TIMEOUT=8;
    const REASON_NO_ENOUGH_CREDIT_IN_SYSTEM=9;
    const REASON_GAME_PROVIDER_ACCOUNT_PROBLEM=10;
    const REASON_GAME_PROVIDER_INTERNAL_PROBLEM=11;
    const REASON_INVALID_TRANSFER_AMOUNT=12;
    const REASON_INVALID_KEY=13;
    const REASON_DUPLICATE_TRANSFER=14;
    const REASON_GAME_PROVIDER_NETWORK_ERROR=15;
    const REASON_DISABLED_DEPOSIT_BY_GAME_PROVIDER=16;
    const REASON_IP_NOT_AUTHORIZED=17;
    const REASON_INVALID_TRANSACTION_ID=18;
    const REASON_AGENT_NOT_EXISTED = 19;
    const REASON_INCOMPLETE_INFORMATION = 20;
    const REASON_LOWER_OR_GREATER_THAN_MIN_OR_MAX_TRANSFER = 21;
    const REASON_CURRENCY_ERROR = 22;
    const REASON_LOCKED_GAME_MERCHANT = 23;
    const REASON_BALANCE_NOT_SYNC = 24;
    const REASON_INVALID_PRODUCT_WALLET = 25;
    const REASON_CANT_TRANSFER_WHILE_PLAYING_THE_GAME = 26;
    const REASON_INVALID_API_VERSION = 27;
    const REASON_TRANSACTION_DENIED = 28;
    const REASON_TRANSACTION_PENDING = 29;
    const REASON_INCORRECT_MERCHANT_ID = 30;
    const REASON_USERS_WALLET_LOCKED = 31;
    const REASON_INSUFFICIENT_AMOUNT = 32;
    const REASON_ACCESS_USERTOKEN_ERROR = 33;
    const REASON_TOKEN_VERIFICATION_FAILED = 34;
    const REASON_TRANSACTION_NOT_FOUND= 35;
    const REASON_INVALID_TIME_RANGE = 36;
    const REASON_PARAMETER_ERROR = 37;
    const REASON_OPERATOR_NOT_EXIST = 38;
    const REASON_ACCOUNT_NOT_EXIST = 39;
    const REASON_ACCOUNT_FUNDS_ERROR = 40;
    const REASON_INVALID_SERVER = 41;

    const REASON_API_MAINTAINING=997;
    const REASON_FAILED_FROM_API=998; //don't know really reason, only know api said failed
    const REASON_UNKNOWN=999;

    public function translateReasonId($reason_id, $status_text=null){
        switch ($reason_id) {
            case self::REASON_NOT_FOUND_PLAYER:
                return lang('Not found player');
                break;

            case self::REASON_NO_ENOUGH_BALANCE:
                return lang('No enough balance');
                break;

            case self::REASON_NETWORK_ERROR:
                return lang('Network Error').' '.$status_text;
                break;
            case self::REASON_GAME_ACCOUNT_LOCKED:
                return lang('Game Account is locked');
                break;
            case self::REASON_TRANSFER_AMOUNT_IS_TOO_HIGH:
                return lang('Transfer Amount is too high');
                break;
            case self::REASON_TRANSFER_AMOUNT_IS_TOO_LOW:
                return lang('Transfer Amount is too low');
                break;
            case self::REASON_LOGIN_PROBLEM:
                return lang('Login problem');
                break;
            case self::REASON_SESSION_TIMEOUT:
                return lang('Session timeout');
                break;
            case self::REASON_NO_ENOUGH_CREDIT_IN_SYSTEM:
                return lang('No enough credit in system');
                break;
            case self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM:
                return lang('Game provider account problem');
                break;
            case self::REASON_API_MAINTAINING:
                return lang('API Maintaining');
                break;
            case self::REASON_FAILED_FROM_API:
                return lang('API failed');
                break;
            case self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM:
                return lang('Game Provider internal problem');
                break;
            case self::REASON_INVALID_TRANSFER_AMOUNT:
                return lang('Invalid transfer amount');
                break;
            case self::REASON_INVALID_KEY:
                return lang('Invalid key');
                break;
            case self::REASON_DUPLICATE_TRANSFER:
                return lang('Duplicate transfer');
                break;
            case self::REASON_GAME_PROVIDER_NETWORK_ERROR:
                return lang('Game provider network error');
                break;
            case self::REASON_DISABLED_DEPOSIT_BY_GAME_PROVIDER:
                return lang('Disabled deposit by game provider');
                break;
            case self::REASON_IP_NOT_AUTHORIZED:
                return lang('Ip not authorized');
                break;
            case self::REASON_INVALID_TRANSACTION_ID:
                return lang('Invalid transaction id');
                break;
            case self::REASON_AGENT_NOT_EXISTED:
                return lang('Agent not existed');
                break;
            case self::REASON_INCOMPLETE_INFORMATION:
                return lang('Incomplete Information');
                break;
            case self::REASON_LOWER_OR_GREATER_THAN_MIN_OR_MAX_TRANSFER:
                return lang('Lower or greated than min or max transfer');
                break;
            case self::REASON_CURRENCY_ERROR :
                return lang('Currency error');
                break;
            case self::REASON_LOCKED_GAME_MERCHANT:
                return lang('Locked game merchant');
                break;
            case self::REASON_BALANCE_NOT_SYNC:
                return lang('Player balance not in sync');
                break;
            case self::REASON_INVALID_PRODUCT_WALLET:
                return lang('Invalid product wallet');
                break;
            case self::REASON_CANT_TRANSFER_WHILE_PLAYING_THE_GAME:
                return lang('Transfer funds not available while playing game');
                break;
            case self::REASON_TRANSACTION_DENIED:
                return lang('Transaction denied');
                break;
            case self::REASON_TRANSACTION_PENDING:
                return lang('Transaction pending');
                break;
            case self::REASON_INVALID_API_VERSION:
                return lang('Invalid version');
                break;
            case self::REASON_INCORRECT_MERCHANT_ID:
                return lang('Incorrect Merchant ID');
                break;
            case self::REASON_USERS_WALLET_LOCKED:
                return lang('User\'s Wallet Locked, Please try again later.');
                break;
            case self::REASON_INSUFFICIENT_AMOUNT:
                return lang('Insufficient Amount');
                break;
            case self::REASON_ACCESS_USERTOKEN_ERROR:
                return lang('Access User Token Error');
                break;
            case self::REASON_TOKEN_VERIFICATION_FAILED:
                return lang('Third party verification token failed');
                break;
            case self::REASON_TRANSACTION_NOT_FOUND:
                return lang('Transaction not found.');
                break;
            default:
                return lang('Unknown');
                break;
        }
    }

    public function isAllowedQueryTransactionWithoutId(){
        return false;
    }

    // public function totalBettingAmount($playerName, $dateFrom, $dateTo)
    // {
    //     $gameBettingRecord = self::getGameTotalBettingAmount($playerName, $dateFrom, $dateTo);
    //     if ($gameBettingRecord != null) {
    //         $result['bettingAmount'] = $gameBettingRecord['bettingAmount'];
    //     }

    //     return array('success' => true, 'bettingAmount' => $result['bettingAmount']);
    // }

    public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null)
    {
        $daily_balance = self::getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);

        $result = array();

        if ($daily_balance != null) {
            foreach ($daily_balance as $key => $value) {
                $result[$value['updated_at']] = $value['balance'];
            }
        }

        return array_merge(array('success' => true, 'balanceList' => $result));
    }

    public function checkLoginStatus($playerName){
        return $this->isPlayerOnline($playerName);
    }

    public function isPlayerOnline($playerName){
        return $this->returnUnimplemented();
    }

    public function queryGameRecords($dateFrom, $dateTo, $playerName = null)
    {
        $gameRecords = self::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());

        return array('success' => true, 'gameRecords' => $gameRecords);
    }

    public function isPlayerIdBlockedInGame($playerId) {
        $gameUsername = $this->getGameUsernameByPlayerId($playerId);
        return $this->isBlockedUsernameInDB($gameUsername);
    }

    public function getCommonAvailableApiToken(callable $callbackGenerateToken){

        //check api token and not timeout

        $api_token=$this->getTokenAndNoTimeoutFromCache();
        if(!empty($api_token)){
            return $api_token;
        }

        $this->CI->utils->debug_log('token timeout or empty', $api_token);

        //generate token from api
        $rlt=$callbackGenerateToken();
        // $this->generateToken($forceNew);

        if($rlt['success']){
            $api_token=$rlt['api_token'];
            $api_token_timeout_datetime=$rlt['api_token_timeout_datetime'];
            if(!empty($api_token)){

                $this->saveTokenWithTimeoutToCache($api_token, $api_token_timeout_datetime);
                // $this->CI->utils->saveJsonToCache($this->getCacheKeyOfApiToken(), ['token'=>$this->api_token,
                //     'timeout'=> $this->api_token_timeout_datetime], $this->api_token_timeout_on_cache_seconds);
            }
            return $api_token;
        }

        return null;

    }

    public function generateCacheKeyOfApiToken(){
        return '_game-api-token-'.$this->getPlatformCode();
    }

    /**
     * get token with timeout from cache
     *
     * @return token
     */
    public function getTokenAndNoTimeoutFromCache(){
        $key=$this->generateCacheKeyOfApiToken();
        //try load from cache
        $tokenArr=$this->CI->utils->getJsonFromCache($key);
        $this->CI->utils->debug_log('try get cache from '.$key, $tokenArr);
        if(!empty($tokenArr)){
            $api_token=$tokenArr['token'];
            //timeout format Y-m-d H:i:s
            $api_token_timeout_datetime=$tokenArr['timeout'];
            if(!empty($api_token)){
                //null means unlimited
                if($api_token_timeout_datetime==null || $api_token_timeout_datetime > $this->CI->utils->getNowForMysql()){
                    return $api_token;
                }
            }
        }

        return null;
    }

    /**
     * saveTokenWithTimeoutToCache
     * @param  [type] $api_token                  [description]
     * @param  [type] $api_token_timeout_datetime [description]
     * @return [type]                             [description]
     */
    public function saveTokenWithTimeoutToCache($api_token, $api_token_timeout_datetime){

        $this->CI->utils->debug_log('save cache '.$api_token, $api_token_timeout_datetime, $this->api_token_timeout_on_cache_seconds);

        //api_token_timeout_on_cache_seconds can setup by each api
        return $this->CI->utils->saveJsonToCache($this->generateCacheKeyOfApiToken(),
            ['token'=>$api_token,
            'timeout'=> $api_token_timeout_datetime],
            $this->api_token_timeout_on_cache_seconds);

    }

    /**
     *
     * api should create functions: queryOriginalGameLogs, preprocessOriginalRowForGameLogs, makeParamsForInsertOrUpdateGameLogsRow
     *
     * @param  string  $token
     * @param  callable queryOriginalGameLogs(string $startDate, string $endDate, bool $use_bet_time)
     * @param  callable makeParamsForInsertOrUpdateGameLogsRow(array $row)
     * @param  callable preprocessOriginalRowForGameLogs(array &$row)
     * @param  boolean $enabled_game_logs_unsettle
     * @param  callable prepareOriginalRows(array &$rows) any db access before insert/update, like bet details
     * @return array ['success']
     */
    public function commonSyncMergeToGameLogs($token,
            $api,
            callable $queryOriginalGameLogs,
            callable $makeParamsForInsertOrUpdateGameLogsRow,
            callable $preprocessOriginalRowForGameLogs,
            $enabled_game_logs_unsettle,
            callable $prepareOriginalRows=null) {

        if(!is_object($api) || $api==null){
            return ['success'=>false];
        }

        $this->CI->load->model(array('game_logs', 'player_model', 'original_game_logs_model'));
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeFrom->modify($this->getDatetimeAdjustSyncMerge());
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');

        //observer the date format
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $this->CI->utils->debug_log('dateTimeFrom', $startDate, 'dateTimeTo', $endDate);
        $rlt = array('success' => true);

        $use_bet_time=$this->getValueFromSyncInfo($token, 'ignore_public_sync');

        /**
         * reupdate merge when processing some late multiple records in original game logs e.g
         *
         *  trans_type    date_time             trans_id
         *  GAME_BET      2019-03-04 14:00:00   8227944081
         *  GAME_WIN      2019-03-04 16:00:00   8227944081
         */
        $reupdate_multiple_orig_logs_in_merge =  $this->getSystemInfo('reupdate_multiple_orig_logs_in_merge', false);

        $insertRows=null;$updateRows=null;
        $rows = $queryOriginalGameLogs($startDate, $endDate, $use_bet_time);

        if(!empty($rows)){
            //$rows => $insertRows, $updateRows , always add game_logs_id to $updateRows
            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->generateInsertAndUpdateForGameLogs($rows, 'external_uniqueid', $reupdate_multiple_orig_logs_in_merge, $this->getPlatformCode());
        }

        unset($rows);

        $cnt = 0;

        if(!empty($insertRows)){
            $this->CI->utils->debug_log('preprocessOriginalRowForGameLogs insertRows', count($insertRows));
            if(!empty($prepareOriginalRows)){
                $prepareOriginalRows($insertRows);
            }
            foreach ($insertRows as &$row) {
                $preprocessOriginalRowForGameLogs($row);
            }
        }
        if(!empty($updateRows)){
            $this->CI->utils->debug_log('preprocessOriginalRowForGameLogs updateRows', count($updateRows));
            if(!empty($prepareOriginalRows)){
                $prepareOriginalRows($updateRows);
            }
            foreach ($updateRows as &$row) {
                $preprocessOriginalRowForGameLogs($row);
            }
        }

        // $this->CI->utils->debug_log($insertRows);

        // $this->CI->utils->debug_log($updateRows);

        // only for lottery, spoorts or any game which exists status change, it will delete game_logs or game_logs_unsettle record depends status
        // and insert/update game_logs_unsettle
        // and update $insertRows and $updateRows
        // no need on live casino/slots
        if($enabled_game_logs_unsettle && (!empty($insertRows) || !empty($updateRows)) ){
            $this->CI->game_logs->processUnsettleGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
                $insertRows, $updateRows);
        }

        if(!empty($insertRows)){
            $rlt['success']=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
                'insert', $insertRows, $cnt);
        }
        unset($insertRows);
        if($rlt['success'] && !empty($updateRows)){
            $rlt['success']=$this->commonUpdateOrInsertGameLogs($api, $makeParamsForInsertOrUpdateGameLogsRow,
                'update', $updateRows, $cnt);
        }
        unset($updateRows);
        unset($unknownGame);

        $this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
        $rlt['count']=$cnt;
        return $rlt;

    }

    public function commonUpdateOrInsertGameLogs($api, callable $makeParamsForInsertOrUpdateGameLogsRow,
            $update_type, array $rows, &$cnt){
        $success=false;
        $maxCount=count($rows);
        foreach ($rows as $row) {
            $cnt++;
            $params=$makeParamsForInsertOrUpdateGameLogsRow($row);
            $success=$this->CI->game_logs->commonInsertOrUpdateGameLogsOrUnsettleRow(
                $api, $params, $update_type
            );
            $this->CI->utils->debug_log('insert or update row', $success, $cnt, $maxCount, $api->getPlatformCode());

            if(!$success){
                $this->CI->utils->error_log('insertOrUpdateGameLogsRow failed', $this->getPlatformCode(), $cnt, $update_type, $row, $params);
                return $success;
            }
            unset($params);
        }

        return $success;
    }

    /**
     * only create unknown game description
     * @param  int $gamePlatformId
     * @param  int $game_description_id
     * @param  int $game_type_id
     * @param  string $gameNameStr
     * @param  int $externalGameId
     * @param  array $extra
     * @return array [$game_description_id, $game_type_id]
     */
    // public function processUnknownGameOnlyWithTrans($gamePlatformId, $game_description_id, $game_type_id, $gameNameStr,
    //                                    $externalGameId, $extra = null) {
    //     if (empty($game_description_id)) {
    //         $unknownGame = $this->getUnknownGame();

    //         $this->CI->load->model(array('game_description_model'));
    //         $this->CI->game_description_model->startTrans();

    //         $game_description_id = $this->CI->game_description_model->processUnknownGame(
    //             $gamePlatformId, $game_type_id, $gameNameStr, $externalGameId, $extra);
    //         if (empty($game_description_id)) {
    //             //if still failed, use unknown game
    //             $game_description_id = $unknownGame->id;
    //             $game_type_id = $unknownGame->game_type_id;
    //         }

    //         $succ = $this->CI->game_description_model->endTransWithSucc();
    //         if (!$succ) {
    //             $this->CI->utils->error_log('create new game description failed', $gamePlatformId, $game_description_id, $game_type_id,
    //                 $gameNameStr, $externalGameId, $extra);
    //         }
    //     }

    //     return array($game_description_id, $game_type_id);
    // }

    public function syncOriginalMd5Sum($token){
        return $this->returnUnimplemented();
    }

    public function commonSyncOriginalMD5Sum($tableName, $qryStr, $md5FieldsForOriginal, $md5FloatAmountFields,
            $md5_sum_field, $id_field){

        $this->CI->load->model(['original_game_logs_model']);

        $success=$this->CI->original_game_logs_model->commonGenerateOriginalMD5($tableName, $qryStr,
            $md5FieldsForOriginal, $md5FloatAmountFields, $md5_sum_field, $id_field);

        return ['success'=>$success];

    }

    public function getTransferMinAmount(){
        return $this->transfer_min_amount;
    }

    public function getTransferMaxAmount(){
        return $this->transfer_max_amount;
    }

    public function getTransferAmountFloat(){
        return $this->getSystemInfo('amount_float', 2);
    }

    public function standardErrorResponse($errorCode, $errorCode2=null, $gameErrorCode=null) {
        $error_response = array('error_code' => $errorCode);
        $error1 = unserialize(STANDARD_ERROR_MSG)[$errorCode];
        if($errorCode && $errorCode2) {
            $error2 = unserialize(STANDARD_ERROR_MSG)[$errorCode2];
            $error_response['error_message'] = $error1.'/'.$error2;
        } else if($errorCode) {
            $error_response['error_message'] = $error1;
        }
        // display original error code
        if($gameErrorCode) {
            $error_response['game_error_code'] = $gameErrorCode;
        }
        return $error_response;
    }

    public function isMaintenance() {
        $this->CI->load->model(array('external_system'));
        $isMaintenance = $this->CI->external_system->isGameApiMaintenance($this->getPlatformCode());
        return $isMaintenance;
    }

    /**
     * preTransfer: Pre-transfer event handler
     * Invoked by Utils::transferWallet() before transfer transaction
     * @used-by     Utils::transferWallet()
     * @see         Utils::transferWallet()
     */
    public function preTransfer($transactionType, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $user_id = null, $walletType = null, $originTransferAmount = null, $ignore_promotion_check = null, $reason = null) {

        $extra_details = [ 'user_id' => $user_id, 'walletType' => $walletType, 'originTransferAmount' => $originTransferAmount, 'ignore_promotion_check' => $ignore_promotion_check, 'reason' => $reason ];

        switch ($transactionType) {
            case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET :
                return $this->preDepositToGame($player_id, $playerName, $transfer_from, $transfer_to, $amount,$extra_details);
                break;
            case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET :
                return $this->preWithdrawFromGame($player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details);
                break;
            default :
                break;
        }
    }

    public function preDepositToGame($player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details = []) {
        return $this->returnUnimplemented();
    }

    public function preWithdrawFromGame($player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details = []) {
        return $this->returnUnimplemented();
    }

    /**
     * postTransfer: Post-transfer event handler
     * Invoked by Utils::transferWallet() after transfer transaction
     * @used-by     Utils::transferWallet()
     * @see         Utils::transferWallet()
     */
    public function postTransfer($transactionType, $result, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $user_id = null, $walletType = null, $originTransferAmount = null, $ignore_promotion_check = null, $reason = null) {

        $this->CI->load->model(['game_logs']);
        if(@$result['success'] && isset($result['didnot_insert_game_logs']) && $result['didnot_insert_game_logs']){
            $transType=Game_logs::TRANS_TYPE_MAIN_WALLET_TO_SUB_WALLET;
            if($transactionType==Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET){
                $transType=Game_logs::TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET;
            }

            #Add after balance if key exists
            $after_balance = null;
            if(array_key_exists('after_balance', $result)){
                $after_balance = is_float($result['after_balance'])?round($result['after_balance'],$this->float_round):null;
            }

            $this->CI->game_logs->insertGameTransaction($this->getPlatformCode(), $player_id,
                $playerName, $after_balance, $amount, @$result['response_result_id'], $transType);
        }

        $extra_details = [ 'user_id' => $user_id, 'walletType' => $walletType, 'originTransferAmount' => $originTransferAmount, 'ignore_promotion_check' => $ignore_promotion_check, 'reason' => $reason ];

        switch ($transactionType) {
            case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET :
                return $this->postDepositToGame($result, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details);
                break;
            case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET :
                return $this->postWithdrawFromGame($result, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details);
                break;
            default :
                break;
        }
    }

    public function postDepositToGame($result, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details = []) {
        return $this->returnUnimplemented();
    }

    public function postWithdrawFromGame($result, $player_id, $playerName, $transfer_from, $transfer_to, $amount, $extra_details = []) {
        return $this->returnUnimplemented();
    }

    public function getDBGametypeList(){
        $this->CI->load->model(['game_type_model']);
        $dbGameTypeList = $this->CI->game_type_model->getGameTypeByQuery('*','game_platform_id ='.$this->getPlatformCode());
        $gameTypeList = [];
        foreach ($dbGameTypeList as $key => $gameType) {
            $gameTypeList[$gameType['game_type_code']] = $gameType;
        }

        unset($dbGameTypeList);
        return $gameTypeList;
    }

    public function getGameListAPIConfig(){
        return $this->CI->utils->getConfig('game_provider_with_game_list_api')[$this->getPlatformCode()];
    }

    public function processLanguagesToJson($lang_arr){
        return "_json:" . json_encode($lang_arr);
    }

    public function isIgnoredZeroOnRefresh(){
        return $this->ignored_0_on_refresh;
    }

    public function playerBet($playerName, $amount, $betCode, array $betDetails=[]){
        return $this->returnUnimplemented();
    }

    public function playerBatchBet($playerName, array $batchBetDetails=[]){
        return $this->returnUnimplemented();
    }

    public function queryGameResult($gameCode, $betCode, $extra=[]){
        return $this->returnUnimplemented();
    }

    public function updateAfterBalanceOnGamelogs($data){
        $chunkedMergeToGamelogs = array_chunk($data,500);
        foreach ($chunkedMergeToGamelogs as $chunkedData) {
            $fields = ['external_uniqueid','id'];
            $exists_rows = $this->CI->game_logs->batchGetFieldsByExternalUniqueid($fields,array_column($chunkedData, 'external_uniqueid'),'game_logs');

            $rowsToUpdate = [];
            foreach ($exists_rows as $key => $row) {
                $array_key = array_search($row["external_uniqueid"], array_column($chunkedData,'external_uniqueid'));
                $rowsToUpdate[$key]['after_balance'] = $chunkedData[$array_key]['after_balance'];
                $rowsToUpdate[$key]['id'] = $row['id'];
                $rowsToUpdate[$key]['updated_at'] = $this->CI->utils->getNowForMysql();
            }
            # update batch row on gamelogs
            if(!empty($rowsToUpdate)){
                $this->CI->game_logs->batchUpdateRowToGameLogs($rowsToUpdate);
            }
            unset($exists_rows);
            unset($rowsToUpdate);
        }

        return ['success'=>true];
    }

    public function syncIncompleteGames($token) {
        return $this->returnUnimplemented();
    }

    public function queryIncompleteGames($username) {
        return $this->returnUnimplemented();
    }

    public function queryGameListBy($game_type_unique_code, $game_unique_code, &$sqlInfo=null) {
        $this->CI->load->model(['game_description_model']);
        return $this->CI->game_description_model->queryByCode($this->getPlatformCode(),
            $game_type_unique_code, $game_unique_code, $sqlInfo);
    }

    public function queryGameTypeList($game_type_unique_code, &$sqlInfo=null) {
        $this->CI->load->model(['game_type_model']);
        return $this->CI->game_type_model->queryByGamePlatformId($this->getPlatformCode(), $game_type_unique_code, $sqlInfo);
    }

    /**
     * will ignore amount and withdraw
     * @param  string $playerName
     * @param  double $amount  ignore it
     * @param  string $transfer_secure_id
     * @return array ['success'=>,]
     */
    public function withdrawAllFromGame($playerName, $amount, $transfer_secure_id){
        //query first then withdraw
        $rlt=$this->queryPlayerBalance($playerName);
        if(!empty($rlt) && $rlt['success'] && isset($rlt['balance'])){
            $amount=$rlt['balance'];
            if($amount>0){
                return $this->withdrawFromGame($playerName, $amount, $transfer_secure_id);
            }else{
                //no need to call api
                return ['success'=>false, 'error'=>'no enough balance'];
            }
        }else{
            return ['success'=>false, 'error'=>'query balance failed'];
        }
    }

    public function getMD5Fields(){
        return [
            'md5_fields_for_original'=>null,
            'md5_float_fields_for_original'=>null,
            'md5_fields_for_merge'=>null,
            'md5_float_fields_for_merge'=>null,
        ];
    }

    public function testMD5Fields($resultText, $externalUniqueId){
        return null;
    }

    public function getOriginalTable(){
        return null;
    }

    private $debugLogArr=[];
    private $errorLogArr=[];

    public function debug_log(){
        $context = func_get_args();

        if (empty($context)) {
            return;
        }
        foreach ($context as &$val) {
            if(!is_array($val) && strlen($val)>1000){
                $val=substr($val, 0, 2000);
            }
        }
        $this->debugLogArr[]=['context'=>$context];
        $log=array_shift($context);
        log_message('debug', $log, $context);
    }

    public function error_log(){
        $context = func_get_args();

        if (empty($context)) {
            return;
        }
        foreach ($context as &$val) {
            if(!is_array($val) && strlen($val)>1000){
                $val=substr($val, 0, 2000);
            }
        }
        $this->debugLogArr[]=['context'=>$context];
        $this->errorLogArr[]=['context'=>$context];
        $log=array_shift($context);
        log_message('error', $log, $context);
    }

    public function getInternalLogs(){
        return ['debug'=>$this->debugLogArr, 'error'=>$this->errorLogArr];
    }

    /**
     *
     * encrypt by openssl
     *
     * @param  string  $orignial
     * @param  string  $secretKey
     * @param  string  $method
     * @param  integer $options
     * @param  string  $iv
     * @return string base64
     */
    public function encryptByOpenssl($orignial, $secretKey, $method='AES-256-ECB', $options = 0, $iv = ''){
        $ciphertext=openssl_encrypt($orignial, $method, $secretKey, $options, $iv);
        return base64_encode($iv . $ciphertext);
    }

    /**
     * decryptByOpenssl
     * @param  string  $encrypted base64
     * @param  string  $secretKey
     * @param  string  $method
     * @param  integer $options
     * @param  integer $ivsize
     * @param  string  $iv
     * @return string
     */
    public function decryptByOpenssl($encrypted, $secretKey, $method='AES-256-ECB', $options = 0, $ivsize=32, $iv = ''){
        $encrypted    = base64_decode($encrypted);
        $ciphertext = mb_substr($encrypted, $ivsize, null, '8bit');

        return openssl_decrypt($ciphertext, $method, $secretKey, $options, $iv);
    }

    public function depositToGameInDB($playerName, $amount, $transfer_secure_id = null){
        $result = [
            'success'=>true,
            'response_result_id' => null,
            'external_transaction_id'=>$transfer_secure_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_APPROVED,
            'reason_id'=>self::REASON_UNKNOWN,
            'didnot_insert_game_logs'=>true,
        ];

        return $result;
    }

    public function withdrawFromGameInDB($playerName, $amount, $transfer_secure_id = null){
        $result = [
            'success'=>true,
            'response_result_id' => null,
            'external_transaction_id'=>$transfer_secure_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_APPROVED,
            'reason_id'=>self::REASON_UNKNOWN,
            'didnot_insert_game_logs'=>true,
        ];

        return $result;
    }

    public function queryTransactionInDB($transactionId, $extra){
        $secureId=$extra['secure_id'];

        $result=[
            'success'=>true,
            'response_result_id'=>null,
            'external_transaction_id'=>$transactionId,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN,
        ];
        $this->CI->load->model(['wallet_model']);
        $transferRequest=$this->CI->wallet_model->getTransferRequestByExternalTransactionIdAndExternalSystemId(
            $transactionId, $this->getPlatformCode());

        if(!empty($transferRequest)){
            if(!empty($transferRequest['transfer_status'])){
                $result['status']=$transferRequest['transfer_status'];
            }
            if(!empty($transferRequest['reason_id'])){
                $result['reason_id']=$transferRequest['reason_id'];
            }
        }else{
            $result['success']=false;
        }
        return $result;
    }

    public function queryPlayerBalanceInDB($playerName) {
        $result = ['success'=>false];

        $this->CI->load->model(['player_model', 'wallet_model']);
        $playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
        if(!empty($playerId)){
            $result['success']=true;
            $result['balance']=$this->CI->wallet_model->getSubWalletTotalNofrozenOnBigWalletByPlayer(
                $playerId, $this->getPlatformCode());
        }

        return $result;
    }

    /**
     * If manual sync is triggered it will return the last sync id from the last sync id parameter in the command otherwise in external system list
     *
     * @param string $token
     *
     * @return string
    */
    public function getLastSyncIdFromTokenOrDB($token) {

        $sync_id = $this->getValueFromSyncInfo($token, 'manual_last_sync_id');

        if(! empty($sync_id)){

            $this->do_update_sync_id = false;

            return $sync_id;
        }

        $this->do_update_sync_id = true;

        return $this->CI->external_system->getLastSyncId($this->getPlatformCode());
    }

    /**
     * queryPlayerReport
     * 
     * Ticket Number: OGP-16994
     *
     * @param datetime $startDate
     * @param datetime $endDate
     * @return mixed
     */
    public function queryPlayerReport($startDate,$endDate)
    {
        return $this->returnUnimplemented();
    }
}

/*end of file*/
