<?php
require_once dirname(__DIR__.'../') .'/abstract_game_api.php';

/**
 *
 * demo bo: http://demo.ssc.tot.bet:7300/
 *
 */
class Game_api_lottery_t1 extends Abstract_game_api {

    protected $api_merchant_code;
    protected $api_secured_key;
    protected $api_sign_key;
    protected $api_token;
    protected $api_agent_id;

    protected $prefix_for_username;
    protected $suffix_for_username;
    protected $language;
    protected $country;
    protected $currency;
    protected $api_url;
    protected $game_url;
    protected $game_mobile_url;
    protected $agent_url;
    protected $fastbet_url;
    protected $mobile_fastbet_url;
    protected $annoucement_url;
    protected $mobile_annoucement_url;
    protected $award_url;
    protected $mobile_award_url;
    protected $sdk_url;
    protected $backoffice_url;
    protected $backoffice_username;
    protected $backoffice_password;
    protected $method;
    protected $max_allow_sync_time_interval;
    protected $token;

    protected $game_host;
    protected $bo_host;

    protected $last_login_ret;

    const URI_MAP = array(
        self::API_generateToken => 'generate_token',
        self::API_createPlayer => 'create_player_account',
        self::API_isPlayerExist => 'is_player_account_exist',
        self::API_queryPlayerInfo => 'query_player_account',
        self::API_updatePlayerInfo => 'update_player_account',
        self::API_changePassword => 'change_player_password',
        self::API_isBlocked => 'query_player_block_status',
        self::API_blockPlayer => 'block_player_account',
        self::API_unblockPlayer => 'unblock_player_account',
        self::API_isPlayerBlocked => 'query_player_block_status',
        self::API_kickOutGame => 'kick_out_game',
        self::API_queryPlayerBalance => 'query_player_balance',
        self::API_queryGameRecords => 'query_game_history',
        self::API_TransferCredit => 'transfer_player_fund',
        self::API_depositToGame => 'transfer_player_fund',
        self::API_withdrawFromGame => 'transfer_player_fund',
        self::API_queryTransaction => 'query_transaction',
        // self::API_queryForwardGame => 'query_game_launcher',
        self::API_login => 'query_game_launcher',
        self::API_syncGameRecords => 'query_game_history',
        self::API_createBackOfficeUser => 'create_sub_account' ,
        self::API_updateBackOfficeUser => 'update_sub_account' ,
        self::API_deleteBackOfficeUser => 'delete_sub_account' ,
        self::API_playerBet => 'bet' ,
        self::API_queryGameResult => 'query_game_period' ,
    );

    const SUCCESS_CODE = 0;
    const PLAYER_EXISTS = 8;
    const TEST_AGEND_ID = '0';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    const ACTION_TYPE_DEPOSIT = 'deposit';
    const ACTION_TYPE_WITHDRAW = 'withdraw';

    const API_isPlayerBlocked = 'isPlayerBlocked';
    const API_kickOutGame = 'kickOutGame';
    const API_TransferCredit ='transferCredit';
    const API_updateBackOfficeUser = 'updateBackOfficeUser';
    const API_deleteBackOfficeUser = 'deleteBackOfficeUser';

    const GAME_AGENT_ROLE_ALLOW = 2;

    const GAME_API_URI='/gameapi/v1';

    const ROLE_ADMIN=1;
    const ROLE_FINANCE=10;
    const ROLE_GAME=100;
    const ROLE_MARKETING=1000;

    const ROLE_READ=1;
    const ROLE_READ_WRITE=2;

    const ROLE_PLAYER_PLAYER=3;
    const ROLE_PLAYER_AGENT=2;

    const ADDITIONAL_BONUS_MODE_WITH_MIN_BONUS='with_min_bonus';
    const ADDITIONAL_BONUS_MODE_NORMAL_BONUS='normal_bonus';

    public function __construct($args){
        parent::__construct();
        $use_alt_creds = $args['params']['use_alt_creds'] ?: false;
        $alt_creds = $this->getSystemInfo('alt_creds', [ 'merchant_code' => null , 'key' => null , 'secret' => null ]);
        $this->CI->utils->debug_log('lottery_t1 api load use_alt_creds', $use_alt_creds);
        $this->api_url = $this->getSystemInfo('url');

        if(!empty($alt_creds) && $use_alt_creds){
            $this->api_merchant_code =$alt_creds['merchant_code'];
            $this->api_secured_key = $alt_creds['secure_key'];
            $this->api_sign_key = $alt_creds['sign_key'];
        }else{
            $this->api_merchant_code = $this->getSystemInfo('api_merchant_code');
            $this->api_secured_key = $this->getSystemInfo('key');
            $this->api_sign_key = $this->getSystemInfo('secret');
        }
        $this->original_platform_code = $this->getSystemInfo('original_platform_code');
        $this->bonus = $this->getSystemInfo('bonus',null);
        $this->rebate = $this->getSystemInfo('rebate',null);

        $this->game_host = $this->getSystemInfo('game_host');
        $this->bo_host = $this->getSystemInfo('bo_host');

        $this->game_url = $this->getSystemInfo('game_url', $this->game_host.'/lottery');
        $this->game_mobile_url = $this->getSystemInfo('game_mobile_url', $this->game_host.'/mlottery');
        $this->agent_url = $this->getSystemInfo('agent_url', $this->game_host.'/agents');
        $this->mobile_agent_url= $this->getSystemInfo('mobile_agent_url', $this->game_host.'/magents');

        $this->salary_url = $this->getSystemInfo('salary_url', $this->game_host.'/salary');
        $this->mobile_salary_url= $this->getSystemInfo('mobile_salary_url', $this->game_host.'/msalary');

        $this->fastbet_url = $this->getSystemInfo('fastbet_url', $this->game_host.'/fastbet');
        $this->mobile_fastbet_url = $this->getSystemInfo('mobile_fastbet_url', $this->game_host.'/mfastbet');
        $this->annoucement_url = $this->getSystemInfo('annoucement_url', $this->game_host.'/annoucement');
        $this->mobile_annoucement_url = $this->getSystemInfo('mobile_annoucement_url', $this->game_host.'/annoucement');
        $this->award_url = $this->getSystemInfo('award_url', $this->game_host.'/award');
        $this->mobile_award_url = $this->getSystemInfo('mobile_award_url', $this->game_host.'/maward');
        $this->sdk_url = $this->getSystemInfo('sdk_url', $this->game_host.'/lotteryh.js');
        $this->backoffice_url = $this->getSystemInfo('backoffice_url', $this->bo_host.'/auth/login2');
        $this->language = $this->getSystemInfo('language');
        $this->country = $this->getSystemInfo('country');
        $this->currency = $this->getSystemInfo('currency','CNY');
        $this->prefix_for_username = $this->getSystemInfo('prefix_for_username');

		$this->backoffice_username = $this->getSystemInfo('backoffice_username');
		$this->backoffice_password = $this->getSystemInfo('backoffice_password');

        $this->game_api_uri = $this->getSystemInfo('game_api_uri', self::GAME_API_URI);

		$this->max_allow_sync_time_interval = $this->getSystemInfo('max_allow_sync_time_interval', '+5 minutes');
        $this->method = self::METHOD_POST;

        $this->default_player_role=$this->getSystemInfo('default_player_role', self::ROLE_PLAYER_PLAYER);

        $this->default_min_bonus_rate=$this->getSystemInfo('default_min_bonus_rate', 1900);
        //with_min_bonus, normal_bonus
        $this->additional_bonus_mode=$this->getSystemInfo('additional_bonus_mode', self::ADDITIONAL_BONUS_MODE_NORMAL_BONUS);

        $this->force_additional_rebate_rate=$this->getSystemInfo('force_additional_rebate_rate', -1);
        $this->force_additional_rebate_rate_map=$this->getSystemInfo('force_additional_rebate_rate_map', []);

        // Optional
        // 1: filter by bet time
        // 2: filter by last updated time (used by default)
        $this->game_history_filter_by = $this->getSystemInfo('game_history_filter_by',2);

        // $this->CI->utils->debug_log('lottery_t1 api load creds', [ 'merchant_code' => $this->api_merchant_code , 'key' => $this->api_secured_key , 'secret' => $this->api_sign_key ]);
    }

	/**
     * Getting platform code
     *
     * @return int
     */
    public function getPlatformCode(){
        return T1LOTTERY_API;
    }

    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        // $statusCode = intval($statusCode, 10);
        return $errCode || intval($statusCode, 10) >= 404;
    }

    /**
     * Generate URL
     *
     * @param $apiName
     * @param $params
     * @return string
     */
    public function generateUrl($apiName, $params) {
        # generate signature
        $params['sign'] = $this->generateSignatureByParams($params);
        $apiUri = self::URI_MAP[$apiName];
        if (self::METHOD_POST == $this->method) {
            $url = $this->api_url .$this->game_api_uri.'/'. $apiUri;
        }else{
            $url = $this->api_url .$this->game_api_uri.'/'. $apiUri . '?' . http_build_query($params);
        }

        $this->CI->utils->debug_log('apiName', $apiName, 'url', $url);
        $this->CI->utils->debug_log('====================params', $params);
        return $url;
    }

    protected function customHttpCall($ch, $params) {
        # generate signature
        $params['sign'] = $this->generateSignatureByParams($params);

        if (self::METHOD_POST == $this->method) {

            $data_json = json_encode($params);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null){
        return $this->returnFailed();
    }

    /**
     * Concat all non-empty values to one string ,
     * append secret key to last, then use SHA1 to get signature
     * The order should be sorted by parameter name in alphabetical order
     * Ignore any json field and sign field
     * Always use utf-8
     *
     * @param $params
     * @return string
     */
    public function generateSignatureByParams($params, $except=['sign']){
        $signString=$this->getSignString($params, $except);

        if(empty($signString)){
            return '';
        }

        $sign=strtolower(sha1($signString.$this->api_sign_key));

        return $sign;
    }

    public function getSignString($fields, $except=['sign']){
        $params=[];
        foreach ($fields as $key => $value) {
            if( in_array($key, $except) || is_array($value)){
                continue;
            }
            $params[$key]=$value;
        }

        if(empty($params)){
            return '';
        }

        ksort($params);

        return implode('', array_values($params));

    }

    public function processResultBoolean($responseResultId, $resultJson, $playerName=null) {
        if(!isset($resultJson['code'])){
            $this->utils->debug_log('T1 lottery failed ', $resultJson, 'responseResultId', $responseResultId);

            return false;
        }

        if(!is_int($resultJson['code'])){
            $this->utils->debug_log('T1 lottery failed ', $resultJson, 'responseResultId', $responseResultId);

            return false;
        }

        $success = self::SUCCESS_CODE == $resultJson['code'];
        if (!$success) {
            $this->utils->debug_log('T1 lottery failed ', $resultJson, 'responseResultId', $responseResultId);
        }
        return $success;
    }

    /**
     * will check timeout, if timeout then call again
     * @return token
     */
    public function getAvailableApiToken(){
        return $this->getCommonAvailableApiToken(function(){
           return $this->generateToken();
        });
    }

    /**
     * Generate token by merchant code and secure key
     *
     */
    public function generateToken(){

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGenerateToken',
            'playerId'=>isset($this->current_player_id) ? $this->current_player_id : null,
        );

        $params = array(
            'merchant_code'=>$this->api_merchant_code,
            'secure_key'=>$this->api_secured_key,
        );
        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_generateToken, $params, $context);
    }

    public function processResultForGenerateToken($params){
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result=['api_token'=>null, 'api_token_timeout_datetime'=>null];

        if($success){
            if(isset($resultArr['detail']['auth_token'])){
                $api_token = $resultArr['detail']['auth_token'];
                //minus 30s
                $timeout=isset($resultArr['detail']['timeout']) ? intval($resultArr['detail']['timeout'])-30 : 3600;
                //convert to datetime
                $api_token_timeout_datetime = $this->CI->utils->formatDateTimeForMysql(
                    new DateTime('+'.$timeout.' seconds'));
                $result['api_token']=$api_token;
                $result['api_token_timeout_datetime']=$api_token_timeout_datetime;
            }else{
                $success=false;
            }
        }

        return [$success, $result];
    }

    private function processAdditionalBonusRate($additionalInfo, &$params){

        $bonus_rate=$additionalInfo['bonus_rate'];
        $rebate_rate=(!isset($additionalInfo['rebate_rate'])) ? $this->rebate : $additionalInfo['rebate_rate'];

        if($this->additional_bonus_mode==self::ADDITIONAL_BONUS_MODE_WITH_MIN_BONUS){

            $bonus_rate=$additionalInfo['bonus_rate'];
            if($bonus_rate>$this->default_min_bonus_rate){
                $max_bonus_rate=$bonus_rate;
                $bonus_rate=$this->default_min_bonus_rate;
                $rebate_rate=round(($max_bonus_rate-$bonus_rate)/2000, 4);
            }

        }

        if(empty($bonus_rate)){
            //set default bonus
            $bonus_rate = $this->bonus;
        }

        //explain bonus rate
        $params['extra']['bonus'] = $bonus_rate;
        $player_type=intval($additionalInfo['player_type']);
        if($player_type===AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER){
            $params['extra']['role'] = self::ROLE_PLAYER_PLAYER;
        }else if($player_type===AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT){
            $params['extra']['role'] = self::ROLE_PLAYER_AGENT;
        }else{
            $params['extra']['role'] = $this->default_player_role;
        }
        if(!empty($additionalInfo['parent_agent_username'])){
            $params['extra']['parent']=$additionalInfo['parent_agent_username'];
        }

        if($this->force_additional_rebate_rate>=0){
            $rebate_rate=$this->force_additional_rebate_rate;
        }

        if(!empty($this->force_additional_rebate_rate_map)){

            if(isset($params['extra']['parent'])){
                if(isset($this->force_additional_rebate_rate_map[$params['extra']['parent']])){
                    $rebate_rate=$this->force_additional_rebate_rate_map[$params['extra']['parent']];
                }
            }

        }

        $params['extra']['rebate'] = $rebate_rate;

    }

    /**
     * Create Player
     *
     * @param $playerName
     * @param $playerId
     * @param $password
     * @param null $email
     * @param null $extra
     * @return array
     */
    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$playerId;
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $playerId,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername,
            'password' => $password,
            'realname' => $gameUsername,
            'extra' =>array(
                'currency' => $this->currency,
                // 'bonus' => $this->bonus,
                // 'rebate' => $this->rebate
            )
        );

        $this->CI->load->model(['game_provider_auth']);
        $additionalInfo=$this->CI->game_provider_auth->getAdditionalInfo($playerId, $this->getPlatformCode());
        if(!empty($additionalInfo)){

            $this->processAdditionalBonusRate($additionalInfo, $params);

            $this->CI->utils->debug_log('create player :'.$playerName, $additionalInfo, $params);
            // $bonus_rate=$additionalInfo['bonus_rate'];
            // //explain bonus rate
            // $params['extra']['bonus'] = $bonus_rate;
            // $player_type=intval($additionalInfo['player_type']);
            // if($player_type===AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER){
            //     $params['extra']['role'] = self::ROLE_PLAYER_PLAYER;
            // }else if($player_type===AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT){
            //     $params['extra']['role'] = self::ROLE_PLAYER_AGENT;
            // }else{
            //     $params['extra']['role'] = $this->default_player_role;
            // }
            // if(!empty($additionalInfo['parent_agent_username'])){
            //     $params['extra']['parent']=$additionalInfo['parent_agent_username'];
            // }
            // $params['extra']['rebate'] = $this->rebate;
        }else{
            if(!is_null($this->bonus)){
                $params['extra']['bonus'] = $this->bonus;
            }
            if(!is_null($this->rebate)){
                $params['extra']['rebate'] = $this->rebate;
            }
        }

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result=null;

        if($success){
            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        }else{
            //if exist
            if($resultArr['code']==self::PLAYER_EXISTS){
                $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
                $success=true;
                $result=['user_exists'=>true];
            }
        }

        return array($success,$result);
    }

    /**
     * Check Player if exist
     *
     * @param $playerName
     * @return array
     */
    public function isPlayerExist($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        if(empty($playerName) || empty($gameUsername)){
            return ['success'=>false, 'exists'=>false];
        }

        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = !empty($resultArr) && isset($resultArr['code']); // $this->processResultBoolean($responseResultId, $resultArr);
        $playerId = $this->getPlayerIdInPlayer($playerName);
        if ($resultArr['code']==0) {
            $result = array('exists' => !!$resultArr['detail']['exists']);

            // only exist
            if($result['exists']){
                //update flag to registered = true
                $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            }
        }else{
            $result = array('exists' => false); # Player not found
        }

        return array($success,$result);
    }

    public function queryPlayerInfo($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processQueryPlayerInfo',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_queryPlayerInfo, $params, $context);
    }

    public function processQueryPlayerInfo($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);

        $result = array();
        if($success){
            $result = $resultArr['detail'];
        }
        return array($success,$result);
    }

    public function updatePlayerInfo($playerName, $infos) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processUpdatePlayerInfo',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername,
            'realname' => $gameUsername,
            'extra' =>array(
                // 'bonus' => $this->bonus,
                // 'rebate' => $this->rebate
            )
        );

        if(!is_null($this->bonus)){
            $params['extra']['bonus'] = $this->bonus;
        }
        if(!is_null($this->rebate)){
            $params['extra']['rebate'] = $this->rebate;
        }

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_updatePlayerInfo, $params, $context);
    }

    public function processUpdatePlayerInfo($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array();
        if($success){
            $result = $resultArr['detail'];
        }
        return array($success,$result);
    }

    public function changePassword($playerName, $oldPassword, $newPassword) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);

        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForChangePassword',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'password' => $newPassword
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername,
            'password' => $newPassword
        );

        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_changePassword, $params, $context);
    }

    public function processResultForChangePassword($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array();
        if ($success) {
            $result["password"] = $this->getVariableFromContext($params, 'password');
            $playerName = $this->getVariableFromContext($params, 'playerName');
            $playerId = $this->getPlayerIdInPlayer($playerName);
            if ($playerId) {
                //sync password to game_provider_auth
                $this->updatePasswordForPlayer($playerId, $result["password"]);
            } else {
                $this->CI->utils->debug_log('cannot find player', $playerName);
            }
        }

        return array($success, $result);
    }

    public function isBlocked($playerName) {
        $result = $this->isBlockedResult($playerName);
        $result_boolean = false;
        if(@$result['success'] && @$result['blocked']){
            $result_boolean = true;
        }

        return $result_boolean;
    }

    /**
     * is blocked , return array
     * @param  string  $playerName
     * @return array
     */
    public function isBlockedResult($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsBlocked',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId'=>$this->current_player_id,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_isBlocked, $params, $context);
    }

    public function processResultForIsBlocked($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $result = array();

        if($success){
            $result['blocked'] = $resultArr['detail']['blocked'];
        }

        return array($success, $result);
    }

    public function blockPlayer($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForBlockPlayer',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        return $this->callApi(self::API_blockPlayer, $params, $context);
    }

    public function processResultForBlockPlayer($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $result = array();

        if($success){
            $result['blocked'] = $resultArr['detail']['blocked'];
            $this->blockUsernameInDB($gameUsername);
        }

        return array($success, $result);
    }

    public function unblockPlayer($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForUnblockPlayer',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        return $this->callApi(self::API_unblockPlayer, $params, $context);
    }

    public function processResultForUnblockPlayer($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $result = array();

        if($success){
            $result['blocked'] = $resultArr['detail']['blocked'];
            $this->unblockUsernameInDB($gameUsername);
        }

        return array($success, $result);
    }

    /**
     * Check Player Balance
     *
     * @param $playerName
     * @return array
     */
    public function queryPlayerBalance($playerName) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername
        );

        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    public function processResultForQueryPlayerBalance($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
        $result = array();
        if($success){
            $result['balance'] = $this->convertTransactionAmount($resultArr['detail']['game_platform_balance']);
        }

        return array($success, $result);
    }

    public function depositToGame($playerName, $amount, $transfer_secure_id=null){
        $type = self::ACTION_TYPE_DEPOSIT;
        return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
        $type = self::ACTION_TYPE_WITHDRAW;
        return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
    }

    public function transferCredit($playerName, $amount, $type, $transfer_secure_id){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        $extTransId = $transfer_secure_id;
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return [
                'success'=>false,
                'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
                'reason_id'=>self::REASON_INVALID_KEY,
                'external_transaction_id'=>$extTransId,
            ];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForTransferCredit',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId' => $this->current_player_id,
            'amount' => $amount,
            'type' => $type,
            'external_transaction_id' => $extTransId,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername,
            'action_type' => $type,
            'amount' => $amount,
            'external_trans_id' => $extTransId
        );
        $this->method = self::METHOD_POST;

        return $this->callApi( $type==self::ACTION_TYPE_DEPOSIT ? self::API_depositToGame : self::API_withdrawFromGame , $params, $context);
    }

    public function processResultForTransferCredit($params){
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $type = $this->getVariableFromContext($params, 'type');
        $amount = $this->getVariableFromContext($params, 'amount');
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$gameUsername);
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

        $result = [
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        ];

        $result["order_status"]=$success;
        if ($success) {

            if ($playerId) {
                // $playerBalance = $this->queryPlayerBalance($playerName);
                $afterBalance = null;

                if($type == self::ACTION_TYPE_DEPOSIT){ // Deposit
                    // if ($playerBalance && $playerBalance['success']) {
                    //     $afterBalance = $playerBalance['balance'];
                    // } else {
                        //IF GET PLAYER BALANCE FAILED
                        // $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
                        // $afterBalance = $rlt->totalBalanceAmount+$amount;
                        // $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
                    // }
                    // $responseResultId = $result['response_result_id'];
                    // Deposit
                    $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
                        $this->transTypeMainWalletToSubWallet());
                }else{ // Withdraw
                    // if ($playerBalance && $playerBalance['success']) {
                    //     $afterBalance = $playerBalance['balance'];
                    //     $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM API '.$type.' ######### ', $afterBalance);
                    // } else {
                        //IF GET PLAYER BALANCE FAILED
                        // $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
                        // $afterBalance = $rlt->totalBalanceAmount+$amount;
                        // $this->CI->utils->debug_log('============= PLAYER AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
                    // }
                    // $responseResultId = $result['response_result_id'];
                    // Withdraw
                    $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
                        $this->transTypeSubWalletToMainWallet());
                }

            } else {
                $this->CI->utils->debug_log('error', '=============== cannot get player id from '.$userName.' getPlayerIdInGameProviderAuth');
            }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }else{
            if(isset($resultJson['code']) && is_int($resultJson['code'])){
                //return code means declined
                //TODO check reason id
                $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
            }
        }

        // $result["external_transaction_id"] = $external_transaction_id;
        // $result["tansfer_status"] = $result["order_status"] ? parent::COMMON_TRANSACTION_STATUS_APPROVED : parent::COMMON_TRANSACTION_STATUS_DECLINED;

        return array($success, $result);
    }

    public function queryTransaction($transactionId, $extra=null){
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $playerName=$extra['playerName'];
        $playerId=$extra['playerId'];
        $this->current_player_id=$playerId;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'external_transaction_id' => $transactionId,
            'playerId'=>$playerId,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'external_trans_id' => $transactionId
        );
        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array();
        $result["order_status"] = isset($resultArr['detail']['trans_status'])?($resultArr['detail']['trans_status']=="settled"?true:false):false;
        $result["order_message"] = isset($resultArr['detail']['trans_status'])?$resultArr['detail']['trans_status']:"API Failed!";

        $result["status"] = $result["order_status"] ? parent::COMMON_TRANSACTION_STATUS_APPROVED : parent::COMMON_TRANSACTION_STATUS_DECLINED;

        return array($success, $result);
    }

    public function batchQueryPlayerBalance($playerNames, $syncId = null) {
        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
    }

    public function login($playerName, $password = null,$extra=null){
        $username = $this->getGameUsernameByPlayerUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForLogin',
            'username' => $username
        );
        $ip=isset($extra['ip']) ? $extra['ip'] : null;

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $username,
            'launcher_settings' => array(
                'game_unique_code'=>'',
                'language'=>'',
                'mode'=>'real',
                'platform'=>'pc',
                'ip'=>$ip,
                'extra'=>array()
            )
        );
        $this->method = self::METHOD_GET;

        $this->last_login_ret = $this->callApi(self::API_login, $params, $context);

        return $this->last_login_ret;
    }

    public function processResultForLogin($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array();
        if($success){
            $result['token'] = $resultArr['detail']['launcher'];
        }
        return array($success, $result);
    }

    public function queryForwardGame($playerName, $extra){
        $loginRet =  $this->login($playerName,null,$extra);

        $lottery_type = (empty($extra['lottery_type'])) ? 'official' : (($extra['lottery_type'] == 'double') ? 'double' : 'official');
        $mode = (empty($extra['mode'])) ? 'real' : (($extra['mode'] == 'trial') ? 'trial' : 'real');

//        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
//        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
//        $sessionToken = $this->getPlayerToken($playerId);
//
//        $cashier_url = $this->utils->getSystemUrl('player') . '/iframe/auth/login_by_token/redirect_url';
//        $cashier_url .= '/' . $this->getPlatformCode();
//        $cashier_url .= '/' . $playerName;
//        $cashier_url .= '/' . $sessionToken;
//        $cashier_url .= '?redirect_url=/player_center2/deposit';

        $success = $loginRet['success'];
        $url=null;
        if($success){
            $params = [
                'token' => $loginRet['token']
//                , 'cashier_url' => $cashier_url
            ];
            $url = ($extra['is_mobile']) ? $this->game_mobile_url : $this->game_url;
            if(!empty($extra['lottery_game_id'])){
                $url .= '/' . $extra['lottery_game_id'].'/' . $lottery_type.'/' . $mode;
            }else if(!empty($extra['game_code'])){
                $url .= '/' . $extra['game_code'].'/' . $lottery_type.'/' . $mode;
            }
            if ($mode == 'real') {
                $url .= '?' . http_build_query($params);
            } else {
                (!empty($extra['lottery_game_id']) || !empty($extra['game_code'])) ?  "" : $url = $this->game_host .'/guest';
            }
        }

        return array("success"=>$success,'url'=>$url);
    }

    public function queryForwardAgent($playerName, $extra){
        $ret = [
            'success' => TRUE,
            'url' => NULL,
            'message' => NULL
        ];

        $url = (!$this->CI->utils->is_mobile()) ? $this->agent_url : $this->mobile_agent_url;

        if(!empty($url) && $this->isAllowAccessAgent($playerName)){
            $url .= '?token='. $this->last_login_ret['token'];
            $ret['url'] = $url;
        }else{
            $ret['success'] = FALSE;
            $ret['message'] = lang('Sorry, no permission');
        }

        return $ret;
    }

    public function queryForwardSalary($playerName, $extra){
        $ret = [
            'success' => TRUE,
            'url' => NULL,
            'message' => NULL
        ];

        $url = (!$this->CI->utils->is_mobile()) ? $this->salary_url : $this->mobile_salary_url;

        if(!empty($url) && $this->isAllowAccessAgent($playerName)){
            $url .= '?token='. $this->last_login_ret['token'];
            $ret['url'] = $url;
        }else{
            $ret['success'] = FALSE;
            $ret['message'] = lang('Sorry, no permission');
        }

        return $ret;
    }


    public function queryForwardFastbet($playerName = NULL){
        $url = (!$this->CI->utils->is_mobile()) ? $this->fastbet_url : $this->mobile_fastbet_url;

        $success = TRUE;
        if($playerName){
            $loginRet =  $this->login($playerName);

            $success = $loginRet['success'];
            if($success){
                $url .= '?token='.$loginRet['token'];
            }
        }

        return array("success"=> $success,'url'=>$url);
    }

    public function queryForwardAnnoucement($playerName = null){
        $url = (!$this->CI->utils->is_mobile()) ? $this->annoucement_url : $this->mobile_annoucement_url;
        if($playerName){
            $loginRet =  $this->login($playerName);

            $success = $loginRet['success'];
            if($success){
                $url .= '?token='.$loginRet['token'];
            }
        }
        return array("success" => TRUE,'url' => $url);
    }

    public function queryForwardAward(){
        $url = (!$this->CI->utils->is_mobile()) ? $this->award_url : $this->mobile_award_url;

        return array("success" => TRUE,'url' => $url);
    }

    public function queryForwardSDK(){
        $url = $this->sdk_url;

        return array("success" => TRUE, 'url' => $url);
    }

    public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null){
        $daily_balance = parent::getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);

        $result = array();

        if ($daily_balance != null) {
            foreach ($daily_balance as $key => $value) {
                $result[$value['updated_at']] = $value['balance'];
            }
        }

        return array_merge(array('success' => true, "balanceList" => $result));
    }

     /**
     * overview : query game records
     *
     * @param $dateFrom
     * @param $dateTo
     * @param null $playerName
     * @return array
     */
    public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
        $gameRecords = parent::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());
        return array('success' => true, 'gameRecords' => $gameRecords);
    }

    //means we only update when those fields are changed
    const MD5_FIELDS_FOR_ORIGINAL=[
        'uniqueid', 'status', 'effective_bet_amount', 'result_amount',
        'username', 'bet_time', 'last_updated_time', 'game_code', 'game_platform_id', 'bet_details', ];

    const MD5_FLOAT_AMOUNT_FIELDS=[
        'effective_bet_amount', 'result_amount',];

    public function syncOriginalGameLogs($token = false) {
        $startDate = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        //observer the date format
        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
        $startDate->modify($this->getDatetimeAdjust());

        $startDateStr=$startDate->format('Y-m-d H:i:s');
        $endDateStr=$endDate->format('Y-m-d H:i:s');
        $page = 1;

        $this->ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');
        $rows_count=0;
        $success= $this->syncPaginate( $startDateStr, $endDateStr, $page, $rows_count );

        $this->CI->utils->debug_log('result rows_count', $rows_count);

        return array('success'=>$success, 'rows_count'=>$rows_count);

    }

    private function syncPaginate($startDate, $endDate, $page, &$rows_count=0){

        $this->CI->utils->debug_log('start syncPaginate================',$startDate, $endDate, $page);

        $success=true;
        $done=false;
        while (!$done) {
            $rlt = $this->syncT1Lottery($startDate, $endDate, $page);
            if($rlt['success']){
                if(isset($rlt['total_rows_current_page'])){
                    $rows_count+=$rlt['total_rows_current_page'];
                }
                $this->CI->utils->debug_log('sync game logs api result', $rlt, $rows_count);

                if($rlt['total_pages']>$rlt['current_page']){
                    $page=$rlt['current_page']+1;
                    $this->CI->utils->debug_log('not done================',$rlt['total_pages'],$rlt['current_page']);
                }else{
                    $done=true;
                    $this->CI->utils->debug_log('done===================',$rlt['total_pages'],$rlt['current_page']);
                }
                $success=true;
            }else{
                $success=false;
                $done=true;
                $this->CI->utils->error_log('sync game logs api error', $rlt);
            }
        }

        return $success;
    }

    const SYNC_TIME_TYPE_BY_BET_TIME=1;
    const SYNC_TIME_TYPE_BY_LAST_UPDATED_TIME=2;

    public function syncT1Lottery($startDate, $endDate, $page ){
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $use_bet_time=$this->ignore_public_sync;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncT1Lottery',
            'from' => $startDate,
            'to' => $endDate
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'from' => $startDate,
            'to' => $endDate,
            'time_type' => $use_bet_time ? self::SYNC_TIME_TYPE_BY_BET_TIME : self::SYNC_TIME_TYPE_BY_LAST_UPDATED_TIME,
            'page_number' => $page,
        );

        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_syncGameRecords, $params, $context);
    }

    public function processResultForSyncT1Lottery($params) {

        $this->CI->load->model(array('original_game_logs_model'));

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $from = $this->getVariableFromContext($params, 'from');
        $to = $this->getVariableFromContext($params, 'to');
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $result = array('data_count'=>0);

        if($success && isset($resultArr['detail'])){
            $dataCount = 0;
            $detail=$resultArr['detail'];
            $gameRecords = isset($detail['game_history']) ? $detail['game_history'] : null;
            if(!empty($gameRecords)){

                // $availableRows = $this->CI->t1lottery_game_logs->getAvailableRows($gameRecords);

                list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal('t1lottery_game_logs', $gameRecords,
                    'uniqueid', 'external_uniqueid', self::MD5_FIELDS_FOR_ORIGINAL, 'md5_sum', 'id', self::MD5_FLOAT_AMOUNT_FIELDS);

                $this->CI->utils->debug_log('after process available rows', count($gameRecords), count($insertRows), count($updateRows));

                unset($gameRecords);

                if (!empty($insertRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows,$responseResultId, 'insert');
                }
                unset($insertRows);

                if (!empty($updateRows)) {
                    $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows,$responseResultId, 'update');
                }

                unset($updateRows);

            }
            $result['total_pages']=isset($detail['total_pages']) ? $detail['total_pages'] : null;
            $result['current_page']=isset($detail['current_page']) ? $detail['current_page'] : null;
            $result['total_rows_current_page']=isset($detail['total_rows_current_page']) ? $detail['total_rows_current_page'] : null;

            unset($detail);
        }else{
            // detail field is required
            $success=false;
        }
        unset($resultArr);
        // $result['data_count'] = $dataCount;

        return array($success, $result);
    }

    public function updateOrInsertOriginalGameLogs($rows, $responseResultId, $update_type){
        $dataCount=0;
        if(!empty($rows)){
            foreach ($rows as $record) {

                // avoid incorrect time. set null if empty
                $game_finish_time = !empty($record['game_finish_time']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", $record['game_finish_time'])) : null;
                $payout_time = !empty($record['payout_time']) ? $this->gameTimeToServerTime(date("Y-m-d H:i:s", $record['payout_time'])) : null;

                $data = [
                    //required
                    'uniqueid' => $record['uniqueid'],
                    'username' => $record['username'],
                    'game_code' => $record['game_code'],
                    'game_name' => $record['game_name'],
                    'game_finish_time' => $game_finish_time,
                    'bet_time' => $this->gameTimeToServerTime(date("Y-m-d H:i:s", $record['bet_time'])),
                    'payout_time' => $payout_time,
                    'last_updated_time' => $this->gameTimeToServerTime(date("Y-m-d H:i:s", $record['last_updated_time'])),
                    'bet_details' => $record['bet_details'],
                    'real_bet_amount' => $record['real_bet_amount'],
                    'effective_bet_amount' => $record['effective_bet_amount'],
                    'result_amount' => $record['result_amount'],
                    'payout_amount' => $record['payout_amount'],
                    'status' => $record['status'],
                    'external_uniqueid' =>$record['uniqueid'],
                    'response_result_id' =>$responseResultId,
                    'md5_sum'=>$record['md5_sum'],
                    'last_sync_time'=>$this->CI->utils->getNowForMysql(),

                    //optional
                    'game_platform_id' => isset($record['game_platform_id']) ? $record['game_platform_id'] : NULL,
                    'after_balance' => isset($record['after_balance']) ? $record['after_balance'] : NULL,
                    'remark' => isset($record['remark']) ? $record['remark'] : NULL,
                    'rule_id' => isset($record['rule_id']) ? $record['rule_id'] : NULL,
                    'period' => isset($record['period']) ? $record['period'] : NULL,
                    'bonus' => isset($record['bonus']) ? $record['bonus'] : NULL,
                    'mode' => isset($record['mode']) ? $record['mode'] : NULL,
                    'multiple' => isset($record['multiple']) ? $record['multiple'] : NULL,
                    'count' => isset($record['count']) ? $record['count'] : NULL,
                ];

                //insert or update data to t1lottery API gamelogs table database
                if ($update_type=='update') {
                    $data['id']=$record['id'];
                    $this->CI->original_game_logs_model->updateRowsToOriginal('t1lottery_game_logs', $data);
                } else {
                    $this->CI->original_game_logs_model->insertRowsToOriginal('t1lottery_game_logs', $data);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }

    //======start merge=====================
    /**
     *
     * perpare original rows, include process unknown game, pack bet details, convert game status
     *
     * @param  array &$row
     */
    public function preprocessOriginalRowForGameLogs(array &$row){

        $game_description_id = $row['game_description_id'];
        $game_type_id = $row['game_type_id'];

        if (empty($game_description_id)) {
            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $this->getUnknownGame());
        }
        $row['game_description_id']=$game_description_id;
        $row['game_type_id']=$game_type_id;

        $status = $this->getGameRecordsStatus($row['status_in_db']);
        $bet_details = array(
            'odds'=>$row['bonus'],
            "bet_amount" =>  $row['bet_amount'],
            "bet_placed" => $row['bet_placed'],
            "won_side" => null,
            "winloss_amount" => $row['result_amount'],
            'issue_number' => $row['period'],
            "win_amount" => $row['result_amount']>0 ? $row['result_amount'] : 0,
            "payout_time" => $row['payout_time'],
            "status_details" => $row['status_in_db'],
            "status" => $status,
        );

        $row['bet_details']=$bet_details;
        $row['status']=$status;
        $row['bet_type']=Game_logs::BET_TYPE_SINGLE_BET;
    }

    /**
     * queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='t1lottery_game_logs.last_updated_time >= ? and t1lottery_game_logs.last_updated_time <= ?';
        if($use_bet_time){
          $sqlTime='t1lottery_game_logs.bet_time >= ? and t1lottery_game_logs.bet_time <= ?';
        }

        //md5_sum and external_uniqueid are required
        $sql = <<<EOD
SELECT
  t1lottery_game_logs.id as sync_index,
  t1lottery_game_logs.username as player_username,
  t1lottery_game_logs.external_uniqueid,
  t1lottery_game_logs.bet_time as start_at,
  t1lottery_game_logs.bet_time as bet_at,
  t1lottery_game_logs.bet_time as end_at,
  t1lottery_game_logs.payout_time,
  t1lottery_game_logs.last_updated_time,
  t1lottery_game_logs.game_finish_time,
  t1lottery_game_logs.result_amount,
  t1lottery_game_logs.effective_bet_amount AS bet_amount,
  t1lottery_game_logs.real_bet_amount,
  t1lottery_game_logs.after_balance,
  t1lottery_game_logs.response_result_id,
  t1lottery_game_logs.bet_details as bet_placed,
  t1lottery_game_logs.period,
  t1lottery_game_logs.bonus,
  t1lottery_game_logs.game_code,
  t1lottery_game_logs.game_name as game,
  t1lottery_game_logs.status as status_in_db,
  t1lottery_game_logs.md5_sum,
  game_provider_auth.player_id,
  game_description.id AS game_description_id,
  game_description.game_type_id
FROM
  t1lottery_game_logs
  join game_provider_auth on game_provider_auth.login_name=t1lottery_game_logs.username and game_provider_auth.game_provider_id=?
  LEFT JOIN game_description
    ON (
      t1lottery_game_logs.game_code = game_description.external_game_id
      AND game_description.game_platform_id = ?
    )
WHERE

  {$sqlTime}

EOD;
        $params=[$this->getPlatformCode(), $this->getPlatformCode(),
          $dateFrom,$dateTo];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }


    /**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){

        $extra_info=null;

        if(!empty($row['payout_time'])) {
            $row['end_at'] = $row['payout_time'];
        }

        return [
            'game_info' => [
                'game_type_id'          => $row['game_type_id'],
                 'game_description_id'  => $row['game_description_id'],
                'game_code'             => $row['game_code'],
                 'game_type'            => null,
                 'game'                 => $row['game']
             ],
            'player_info' => [
                'player_id'             => $row['player_id'],
                'player_username'       => $row['player_username']
            ],
            'amount_info' => [
                'bet_amount'            => $row['bet_amount'],
                'result_amount'         => $row['result_amount'],
                'bet_for_cashback'      => $row['bet_amount'],
                'real_betting_amount'   => $row['real_bet_amount'],
                'win_amount'            => null,
                'loss_amount'           => null,
                'after_balance'         => $row['after_balance']
             ],
            'date_info' => [
                'start_at'              => $row['start_at'],
                'end_at'                => $row['end_at'],
                'bet_at'                => $row['bet_at'],
                'updated_at'            => $row['last_updated_time']
            ],
            'additional_info' => [
                'has_both_side'         => 0,
                'external_uniqueid'     => $row['external_uniqueid'],
                'round_number'          => $row['external_uniqueid'],
                'md5_sum'               => $row['md5_sum'],
                'response_result_id'    => $row['response_result_id'],
                'sync_index'            => $row['sync_index'],
                'bet_type'              => $row['bet_type']
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => $row['status'],
            'bet_details' => $row['bet_details'],
            'extra' => $extra_info,

            //from exists game logs
            'game_logs_id' => isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id' => isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,

        ];

    }

    public function syncMergeToGameLogs($token) {

        // callable $queryOriginalGameLogs,
        // callable $makeParamsForInsertOrUpdateGameLogsRow,
        // callable $preprocessOriginalRowForGameLogs,

        $enabled_game_logs_unsettle=true;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);

    }

    /**
     * overview : get game record status
     *
     * @param $status
     * @return int
     */
    private function getGameRecordsStatus($status) {
        $this->CI->load->model(array('game_logs'));
        $status = (int)$status;

        switch ($status) {
        case 1:
            $status = Game_logs::STATUS_PENDING;
            break;
        case 2:
            $status = Game_logs::STATUS_REFUND;
            break;
        case 3:
            $status = Game_logs::STATUS_CANCELLED;
            break;
        case 6:
            $status = Game_logs::STATUS_REJECTED;
            break;
        case 4:
        case 5:
        case 7:
            $status = Game_logs::STATUS_SETTLED;
            break;
        }
        return $status;
    }

    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;
        $game_type_id = null;
        if (isset($row['game_description_id'])) {
            $game_description_id = $row['game_description_id'];
            $game_type_id = $row['game_type_id'];
        }

        if(empty($game_description_id)){
            $game_description_id=$this->CI->game_description_model->processUnknownGame($this->getPlatformCode(),
                $unknownGame->game_type_id, $row['game'], $row['game_code']);
            $game_type_id=$unknownGame->game_type_id;
        }

        return [$game_description_id, $game_type_id];
    }
    //======end merge=====================

    public function checkLoginStatus($playerName){
        return $this->returnUnimplemented();
    }

    public function logout($playerName, $password = null){
        return $this->returnUnimplemented();
    }

    public function isAllowAccessAgent($playerName){
        $loginRet =  $this->login($playerName);

        $success = $loginRet['success'];

        if($success){
            $result = $this->queryPlayerInfo($playerName);

            return ($result && isset($result['role']) && $result['role'] === self::GAME_AGENT_ROLE_ALLOW) ? TRUE : FALSE;
        }else{
            return FALSE;
        }
    }

    /**
     *
     * @return array url,username,password
     */
    public function getBackOfficeInfo(){

        return [
            'backoffice_url'=>$this->backoffice_url,
            'backoffice_username'=>$this->backoffice_username,
            'backoffice_password'=>$this->backoffice_password,
        ];

    }

    /**
     *
     * @param  array  $roleMap [Game_api_lottery_t1::ROLE_GAME=>Game_api_lottery_t1::ROLE_READ, Game_api_lottery_t1::ROLE_MARKETING=>=>Game_api_lottery_t1::ROLE_READ_WRITE]
     * @return int $role or null
     */
    public function generateBackofficeRole(array $roleMap){
        $role=null;
        if(!empty($roleMap)){
            $role=0;
            foreach ($roleMap as $roleCode => $rolePermission) {
                $role=$role+$roleCode*$rolePermission;
            }
        }

        return $role;
    }

    public function createBackOfficeUser($backofficeUsername, $password, $role, $realname=null){

        $result=$this->returnFailed('Wrong Parameter');

        if(!empty($backofficeUsername) && !empty($password) && !empty($role)){
            //call api
            // $this->generateToken(true); #generate token
            $api_token=$this->getAvailableApiToken();
            if(empty($api_token)){
                return ['success'=>false, 'error_message'=>'no auth token'];
            }

            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForCreateBackOfficeUser',
                'backofficeUsername' => $backofficeUsername
            );
            //convert role to
            $backofficeRole=$role;

            $params = array(
                'auth_token' => $api_token,
                'merchant_code' => $this->api_merchant_code,
                'game_platform_id' => $this->original_platform_code,
                'username' => $backofficeUsername,
                'password' => $password,
                'role' => $backofficeRole
            );

            $result= $this->callApi(self::API_createBackOfficeUser, $params, $context);
        }

        return $result;

    }

    public function processResultForCreateBackOfficeUser($params){
        $resp_result_id = $this->getResponseResultIdFromParams($params);
        $api_results = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($resp_result_id, $api_results);
        $results = [];
        if ($success) {
            $result['code'] = $api_results['code'];
            $details = $api_results['detail'];
            unset($details['password']);
            $result['detail'] = $details;
        }
        else {
            $result['code'] = $api_results['code'];
            $result['error'] = $api_results['error'];
        }

        return [ $success, $result ];
    }

    public function updateBackOfficeUser($backofficeUsername, $password, $role, $realname=null){

        $result=$this->returnFailed('Wrong Parameter');

        if(!empty($backofficeUsername) && !empty($password) && !empty($role)){
            //call api
            // $this->generateToken(true); #generate token
            $api_token=$this->getAvailableApiToken();
            if(empty($api_token)){
                return ['success'=>false, 'error_message'=>'no auth token'];
            }

            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForUpdateBackOfficeUser',
                'backofficeUsername' => $backofficeUsername
            );
            //convert role to
            $backofficeRole=$role;

            $params = array(
                'auth_token' => $api_token,
                'merchant_code' => $this->api_merchant_code,
                'game_platform_id' => $this->original_platform_code,
                'username' => $backofficeUsername,
                'password' => $password,
                'role' => $backofficeRole
            );

            $result= $this->callApi(self::API_updateBackOfficeUser, $params, $context);
        }

        return $result;

    }

    public function processResultForUpdateBackOfficeUser($params){
        $resp_result_id = $this->getResponseResultIdFromParams($params);
        $api_results = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($resp_result_id, $api_results);
        $results = [];
        if ($success) {
            $result['code'] = $api_results['code'];
            $details = $api_results['detail'];
            unset($details['password']);
            $result['detail'] = $details;
        }

        return [ $success, $result ];
    }

    public function deleteBackOfficeUser($backofficeUsername){

        $result=$this->returnFailed('Wrong Parameter');

        if(!empty($backofficeUsername)){
            //call api
            // $this->generateToken(true); #generate token
            $api_token=$this->getAvailableApiToken();
            if(empty($api_token)){
                return ['success'=>false, 'error_message'=>'no auth token'];
            }

            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'deleteResultForUpdateBackOfficeUser',
                'backofficeUsername' => $backofficeUsername
            );
            //convert role to
            $backofficeRole=$role;

            $params = array(
                'auth_token' => $api_token,
                'merchant_code' => $this->api_merchant_code,
                'game_platform_id' => $this->original_platform_code,
                'username' => $backofficeUsername,
            );

            $result= $this->callApi(self::API_deleteBackOfficeUser, $params, $context);
        }

        return $result;

    }

    public function deleteResultForUpdateBackOfficeUser($params){
        $resp_result_id = $this->getResponseResultIdFromParams($params);
        $api_results = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($resp_result_id, $api_results);
        $results = [];
        if ($success) {
            $result['code'] = $api_results['code'];
            $details = $api_results['detail'];
            unset($details['password']);
            $result['detail'] = $details;
        }

        return [ $success, $result ];
    }

    public function convertTransactionAmount($amount){
        $amount =floor($amount*100)/100;
        return $amount;
    }

    public function playerBatchBet($playerName, array $batchBetDetails=[]){
        return $this->unimplemented();
    }

    public function playerBet($playerName, $amount, $gameCode, array $betDetails=[]){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForPlayerBet',
            'playerName' => $playerName,
            'gameUsername' => $gameUsername,
            'playerId'=>$this->current_player_id,
        );
        $bet=['mode'=>0.01, 'multiple'=>intval($amount*100)];
        if(!empty($betDetails)){
            $bet['rule']=$betDetails['rule'];
            $bet['number']=$betDetails['number'];
        }

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            'username' => $gameUsername,
            'game'=>$gameCode,
            'bet'=>[$bet],
        );
        $this->method = self::METHOD_POST;

        return $this->callApi(self::API_playerBet, $params, $context);
    }

    public function processResultForPlayerBet($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $result = ['updated'=>false, 'original_result'=>$resultArr];

        if($success && $resultArr['code']==self::SUCCESS_CODE){
            if(isset($resultArr['detail']['amount']) && $resultArr['detail']['total']>=1){
                $result['updated']=true;
                $result['detail_items']=$resultArr['detail']['items'];
            }
        }

        return array($success, $result);
    }

    public function queryGameResult($gameCode, $betCode, $extra=[]){
        // $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        // $this->current_player_id=$this->getPlayerIdFromUsername($playerName);
        // $this->generateToken(true); #generate token
        $api_token=$this->getAvailableApiToken();
        if(empty($api_token)){
            return ['success'=>false, 'error_message'=>'no auth token'];
        }

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryGameResult',
            // 'playerName' => $playerName,
            // 'gameUsername' => $gameUsername,
            // 'playerId'=>$this->current_player_id,
        );

        $params = array(
            'auth_token' => $api_token,
            'merchant_code' => $this->api_merchant_code,
            'game_platform_id' => $this->original_platform_code,
            // 'username' => $gameUsername,
            'game'=>$gameCode,
            'period'=>$betCode,
        );
        $this->method = self::METHOD_GET;

        return $this->callApi(self::API_queryGameResult, $params, $context);
    }

    public function processResultForQueryGameResult($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);

        $this->CI->load->model(['game_logs']);
        // $playerName = $this->getVariableFromContext($params, 'playerName');
        // $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $result = ['game_status'=>Game_logs::STATUS_PENDING];

        if($success && $resultArr['code']==self::SUCCESS_CODE){
            switch ($resultArr['detail']['status']) {
                case 0: //No open code yet
                    $result['game_status']=Game_logs::STATUS_PENDING;
                    break;
                case 10: //Payout
                    $result['game_status']=Game_logs::STATUS_SETTLED;
                    break;
                case 11: //Open code verified
                    $result['game_status']=Game_logs::STATUS_PENDING;
                    break;
                case 12:  //Open code verify failed
                    $result['game_status']=Game_logs::STATUS_PENDING;
                    break;
                case 20:  //Terminated
                    $result['game_status']=Game_logs::STATUS_CANCELLED;
                    break;
                case 21:  //Suspended
                    $result['game_status']=Game_logs::STATUS_PENDING;
                    break;
            }
        }
        $result['original_result']=$resultArr;

        return array($success, $result);
    }

}