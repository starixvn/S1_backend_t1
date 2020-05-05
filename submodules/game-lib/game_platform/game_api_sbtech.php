<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/** SAMPLE DATA
 *
    url : http://whlapi3.sbtech.com/WHLCustomers.asmx
    {
        "prefix_for_username": "lb",
        "agent_username": "LbbetWHL",
        "agent_password": "4maVyRffy8n4pRXu",
        "url_web_launch": "http://lbbet.staging.agent1818.com",
        "url_mobile_launch": "http://mlbbet.staging.agent1818.com",
        "data_api": "http://dataapi.sbtech.com/dataAPI",
        "gameTimeToServerTime": "+8 hours",
        "serverTimeToGameTime": "-8 hours"
    }
 *
 * JS : l8b8mlgb8.staging.lebo.t1t.games/resources/sbtech/sbt.js
 *
 * callback (change if update domain)
 *   http://l8b8mlgb8.lebo.t1t.games/callback/game/484/status
 *   http://l8b8mlgb8.lebo.t1t.games/callback/game/484/refresh_session
 *
 *
 * Back Office
 *   url :
 *   username :
 *   password :
 *
 * SYNC game : /cli/sync/sync_game_description_sbtech_new
 */
class Game_api_sbtech extends Abstract_game_api {

    private $url;
    private $is_data_api;
    private $token;

    const URI_MAP = array(
        self::API_createPlayer => 'CreateUser',
        self::API_checkLoginToken => 'GetCustomerAuthToken',
        self::API_queryPlayerBalance => 'GetBalance',
        self::API_depositToGame => 'TransferToWHL',
        self::API_withdrawFromGame => 'TransferFromWHL',
        self::API_isPlayerExist => 'GetBalance',
        self::API_syncGameRecords => 'GetBetsFeedFilesListForPeriod',
        self::API_queryPlayerInfo => 'MemberBalance',
        self::API_queryForwardGame => 'PlayGame',
        self::API_queryTransaction => 'CheckTransaction ',

        self::API_generateToken => 'gettoken',
        self::API_queryGameRecords => 'bettinghistory',
        'openBets' => 'openBets',
    );

    const DEFAULT_VIP_LEVEL_IN_GAME = 1;
    const ERROR_CODE_NO_ERR = 'NoError';
    const WHL_STATUS_CALLBACK = 'status';
    const WHL_REFRESH_SESSION_CALLBACK = 'refresh_session';
    const SUCCESS_CODE = 0;
    const is_combo_bets = "Combo bets";

    # Don't ignore on refresh
    const DEFAULT_IGNORED_0_ON_REFRESH = FALSE;

    public function __construct() {

        parent::__construct();
        $this->url = $this->getSystemInfo('url');
        $this->agent_username = $this->getSystemInfo('agent_username');
        $this->agent_password = $this->getSystemInfo('agent_password');
        $this->currency_code = $this->getSystemInfo('currency_code', 'CNY');
        $this->country_code = $this->getSystemInfo('country_code', 'CN');
        $this->language = $this->getSystemInfo('language', 'en');
        $this->url_web_launch = $this->getSystemInfo('url_web_launch');
        $this->url_mobile_launch = $this->getSystemInfo('url_mobile_launch');
        $this->data_api = $this->getSystemInfo('data_api');

        $this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+1 day');

        // default to false due to so slow api. to follow to game provider
        $this->sync_open_bets = $this->getSystemInfo('sync_open_bets', false);

        // try to add in info. game provider js use method get in getting token
        $this->use_iframe_in_web_launch = $this->getSystemInfo('use_iframe_in_web_launch', true);

        // to add min width as per game provider ( due to design issue )
        $this->set_min_width = $this->getSystemInfo('set_min_width', true);
        $this->default_frame_min_width = $this->getSystemInfo('default_frame_min_width', '1280px');

        $this->prefix_for_username = $this->getSystemInfo('prefix_for_username');
        $this->token_prefix = $this->getSystemInfo('token_prefix', '');

        $this->is_data_api = false;
        $this->token = '';

        # set in main site
        $this->forward_sites = $this->getSystemInfo('forward_sites');
        $this->token_prefix_count = $this->getSystemInfo('token_prefix_count', '5');    # should be same length prefix_for_username
    }

    public function callback($token, $method) {
        $this->CI->load->model(array('external_common_tokens', 'player_model'));

        if ($this->forward_sites) {
            $token_prefix = substr($token, 0, $this->token_prefix_count);
            $this->CI->utils->debug_log('<<<<< FORWARD SITES ENABLED >>>>>', ' token prefix '.$token_prefix.' token count '.$this->token_prefix_count);
            $player_id = $balance = 0;
            if (isset($this->forward_sites[$token_prefix])) {
                $result['token'] = substr($token, $this->token_prefix_count);
                $url = $this->forward_sites[$token_prefix].$method. '?token='.$result['token'];
                $this->CI->utils->debug_log('<<<<< FORWARD URL >>>>>>',$url);
                return $this->forwardCallback($url, array());
            } else {
                $this->CI->utils->debug_log('INVALID FORWARDING, token prefix ====> '.$token_prefix);
            }
        } else {
            $this->CI->utils->debug_log('player token ====> '.$token);
            $player_id = $this->CI->external_common_tokens->getPlayerIdByExternalToken($token, $this->getPlatformCode());
            $balance = '';
            if($player_id) {
                $player = $this->CI->player_model->getPlayerUsername($player_id);
                if(!empty($player['username'])) {
                    $result = $this->queryPlayerBalance($player['username']);
                    if($result['success']) {
                        $balance = $result['balance'] == 0 ? $result['balance'] : '';
                    }
                }
                $this->CI->utils->debug_log('player info  ====> '.json_encode($player), ' balance ==> '.$balance);
            } else {
                $this->CI->utils->debug_log('TOKEN NOT FOUND ====> '.$token);
            }
        }

        $result = [];
        if($method == self::WHL_STATUS_CALLBACK ) {
            $result = array(
                'uid' => random_string('unique'),
                'token' => $token,
                'status' => $player_id ? 'real' : 'anon',
                'message' => '',
                'balance' => $balance
            );
        } else if($method == self::WHL_REFRESH_SESSION_CALLBACK) {
            $result = array(
                'status' => $player_id ? 'success' : 'failure',
                'message' => '',
                'balance' => $balance
            );
        }
        return $result;
    }

    public function forwardCallback($url, $params) {
        list($header, $resultText) = $this->httpCallApi($url, $params);
        $resultArr = json_decode(substr($resultText, 7, -2), true);
        $this->CI->utils->debug_log('forwardCallback', $url, $params, $header, $resultText);
        return $resultArr;
    }

    public function getPlatformCode() {
        return SBTECH_API;
    }

    public function generateUrl($apiName, $params) {
        if ($this->is_data_api) {
            if (self::URI_MAP[$apiName] == self::URI_MAP[self::API_queryGameRecords]
                || self::URI_MAP[$apiName] == 'openBets') {
                return $this->data_api.'/'.self::URI_MAP[$apiName].'?token='.$this->token;
            } else {
                return $this->data_api.'/'.self::URI_MAP[$apiName];
            }
        } else {
            return $this->url.'/'.self::URI_MAP[$apiName];
        }
	}

    protected function getHttpHeaders($params) {

        if ($this->is_data_api) {
            return array("Content-Type" => "application/json");
        } else {
            return array('Content-Type' =>'application/x-www-form-urlencoded');
        }
    }

    protected function customHttpCall($ch, $params) {
        if ($this->is_data_api) {
            $post_data = json_encode($params);
        } else {
            $post_data = http_build_query($params);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    }

    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        return $errCode || intval($statusCode, 10) >= 501;
    }

    public function processResultBoolean($responseResultId, $resultText, $playerName) {
        $success = false;

        if (strpos($resultText, '@ReturnedError') !== false) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->error_log("==========SBTECH API GOT ERROR=============",$resultText, $playerName);
        } else {
            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);

            if( isset($resultArr['ErrorCode']) && $resultArr['ErrorCode'] == self::ERROR_CODE_NO_ERR ) {
                $success = true;
                $this->CI->utils->debug_log("==========SBTECH SUCCESS RESPONSE =============",$resultArr, $playerName);
            } else {
                $this->CI->utils->error_log("==========SBTECH API GOT ERROR=============",$resultArr, $playerName);
            }
        }
        return $success;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameName = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'gameName' => $gameName,
            'playerId' => $playerId,
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameName,
            'LoginName' => $gameName,
            'CurrencyCode' => $this->currency_code,
            'CountryCode' => $this->country_code,
            'City' => '',
            'FirstName' => $gameName,
            'LastName' => $gameName,
            'Group1ID' => self::DEFAULT_VIP_LEVEL_IN_GAME,
            'CustomerMoreInfo'=> '',
            'CustomerDefaultLanguage' => $this->language,
            'DateOfBirth'=>'',
            'DomainID' => '',
        );

        return $this->callApi(self::API_createPlayer, $params, $context);
	}

    public function processResultForCreatePlayer($params) {
        $gameName = $this->getVariableFromContext($params, 'gameName');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        // response return string if error else xml data
        $success = $this->processResultBoolean($responseResultId, $resultText, $gameName);

        $result['response_result_id'] = $responseResultId;
        $result['exists'] = true;
        if ($success) {

            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);
            $token = $resultArr['AuthToken'];
            $this->CI->load->model('external_common_tokens');
            $this->CI->external_common_tokens->addPlayerToken($playerId, $token, SBTECH_API);

            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
            $result['exists'] = false;
        }
        return array($success, $result);
    }


    public function getPlayerAuthToken($playerName) {

        $playerId = $this->getPlayerIdInPlayer($playerName);

        if(!$playerId) {
            return 'Invalid Player';
        }
        $gameName = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetPlayerAuthToken',
            'gameName' => $gameName,
            'playerId' => $gameName,
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameName,
        );

        return $this->callApi(self::API_checkLoginToken, $params, $context);
    }

    public function processResultForGetPlayerAuthToken($params) {
        $gameName = $this->getVariableFromContext($params, 'gameName');
        $playerId = $this->getVariableFromContext($params, 'playerId');

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        // response return string if error else xml data
        $success = $this->processResultBoolean($responseResultId, $resultText, $gameName);
        $result = [];
        if ($success) {
            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);

            $result['auth'] = $resultArr['AuthToken'];

           # $this->CI->load->model('external_common_tokens');
           # $this->CI->external_common_tokens->setPlayerToken($playerId, $result['auth'], SBTECH_API);
        }
        return array($success, $result);
    }

    public function queryPlayerBalance($playerName) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'gameUsername' => $gameUsername,
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameUsername,
        );

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    public function processResultForQueryPlayerBalance($params) {
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultText, $gameUsername);

        $result = [];
        if($success) {
            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);
            $result['balance'] = $this->gameAmountToDB($resultArr['Balance']);
        }
        return array($success, $result);
    }

    public function getDataApiToken() {

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetDataApiToken',
        );

        $params = array(
            'agentUserName' => $this->agent_username,
            'agentPassword' =>  $this->agent_password,
        );

        $this->is_data_api = true;

        return $this->callApi(self::API_generateToken, $params, $context);
    }

    public function processResultForGetDataApiToken($params) {

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJson = $this->getResultJsonFromParams($params);

        $success = false;
        $result = [];
        if (isset($resultJson['token'])) {
            $success = true;
            $result['token'] = $resultJson['token'];
        }
        $result['response_result_id'] = $responseResultId;

        return array($success, $result);
    }

    public function depositToGame($playerName, $amount, $transfer_secure_id=null){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        $external_trans_id = $transfer_secure_id ? $transfer_secure_id : $secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_trans_id
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameUsername,
            'Amount' => $amount,
            'RefTransactionCode' => $external_trans_id,
            'BonusCode' => '',
        );

        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    public function processResultForDepositToGame($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultText, $gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if($success) {
            // $resultXml = new SimpleXMLElement($resultText);
            // $resultArr = json_decode(json_encode($resultXml), true);
            // $afterBalance = $this->gameAmountToDB($resultArr['Balance']);
            // $result["current_player_balance"] = $afterBalance;
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeMainWalletToSubWallet());
            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            // }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);

            $error_code = @$resultArr['ErrorCode'];
            switch($error_code) {
                case 'AuthenticationFailed' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                    break;
                case 'MerchantIsFrozen' :
                case 'MerchantNotActive' :
                    $result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
                    break;
                case 'Exception' :
                    $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
                    break;
                case 'InsufficientFunds' :
                    $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
                    break;
                case 'TransactionCodeNotFound' :
                    $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                    break;
            }

            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);
    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null,$notRecordTransaction=false) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $secure_id = $this->getSecureId('transfer_request', 'secure_id', false, 'T');
        $external_trans_id = $transfer_secure_id ? $transfer_secure_id : $secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'external_transaction_id' => $external_trans_id
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameUsername,
            'Amount' => $amount,
            'RefTransactionCode' => $external_trans_id,
            'BonusCode' => '',
        );

        return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

    public function processResultForWithdrawFromGame($params) {
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultText, $gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );
        if($success) {
            // $resultXml = new SimpleXMLElement($resultText);
            // $resultArr = json_decode(json_encode($resultXml), true);
            // $afterBalance = $this->gameAmountToDB($resultArr['Balance']);
            // $result["current_player_balance"] = $afterBalance;
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeSubWalletToMainWallet());
            // } else {
            //     $this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            // }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['didnot_insert_game_logs']=true;
        } else {
            $resultXml = new SimpleXMLElement($resultText);
            $resultArr = json_decode(json_encode($resultXml), true);

            $error_code = @$resultArr['ErrorCode'];
            switch($error_code) {
                case 'AuthenticationFailed' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                    break;
                case 'MerchantIsFrozen' :
                case 'MerchantNotActive' :
                    $result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
                    break;
                case 'Exception' :
                    $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
                    break;
                case 'InsufficientFunds' :
                    $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
                    break;
                case 'TransactionCodeNotFound' :
                    $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                    break;
            }
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }
        return array($success, $result);
    }

    public function isPlayerExist($playerName) {

        $gameName = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'gameName' => $gameName,
        );

        $params = array(
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'MerchantCustomerCode' => $gameName,
        );

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params) {
        $gameName = $this->getVariableFromContext($params, 'gameName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        $isExist = $this->processResultBoolean($responseResultId, $resultText, $gameName);

        $success = $isExist ? false : true; // if not exist set to true

        $result['exists'] = $isExist ? true : false;

        return array($success, $result);
    }

    public function queryForwardGame($playerName, $extra = null) {
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $response = $this->getPlayerAuthToken($playerName);

        $auth_token =  $response['auth'];

        $this->CI->load->model('external_common_tokens');
        $this->CI->external_common_tokens->setPlayerToken($playerId, $auth_token, SBTECH_API);
        $game_url = !empty($extra['is_mobile']) ? $this->url_mobile_launch : $this->url_web_launch;


        if(!empty($this->token_prefix)){
            $auth_token = $this->token_prefix.$auth_token;
            $this->CI->utils->debug_log('player token for forward site ====> '.$auth_token, ' player name ===> '.$playerName);
        }

        $url = $game_url . '?token=' . $auth_token;

        return array(
            'url' => $url,
            'use_iframe_in_web_launch' => $this->use_iframe_in_web_launch,
            'set_min_width' => $this->set_min_width,
            'default_frame_min_width' => $this->default_frame_min_width,
        );
    }

    public function syncOriginalGameLogs($token = false) {

        $openSettledApi = array('openBets', self::API_queryGameRecords);

        $rtn = array();
        foreach($openSettledApi as $api) {
            if (!$this->sync_open_bets && $api == 'openBets') {  # make sure sync open bets
                continue;
           }

            $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
            $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

            $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
            $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
            $startDate->modify($this->getDatetimeAdjust());

            //observer the date format
            $queryDateTimeStart = $startDate->format('Y-m-d H:i:s');
            $queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
            $queryDateTimeMax = $endDate->format('Y-m-d H:i:s');

            while ($queryDateTimeMax  > $queryDateTimeStart) {

                $startDateParam=new DateTime($queryDateTimeStart);
                if($queryDateTimeEnd>$queryDateTimeMax){
                    $endDateParam=new DateTime($queryDateTimeMax);
                }else{
                    $endDateParam=new DateTime($queryDateTimeEnd);
                }
                $startDateParam = $startDateParam->format('Y-m-d\TH:i:s');
                $endDateParam = $endDateParam->format('Y-m-d\TH:i:s');

                $result = $this->getDataApiToken();
                if ($result['success']) {
                    $this->token = $result['token'];
                }

                $context = array(
                    'callback_obj' => $this,
                    'callback_method' => 'processResultForSyncGameRecords',
                    'startDate' => $startDateParam,
                    'endDate' => $endDateParam,
                    'isOpenBets' => $api == 'openBets' ? true : false  # check if open bets api
                );

                $params = array(
                    'From' => $startDateParam,
                    'To' => $endDateParam,
                );

                $this->is_data_api = true;

                $rtn[] = $this->callApi($api, $params, $context);

                $queryDateTimeStart = $endDateParam;
                $queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
            }
        }

        return array("success"=>true,"sync_details" => $rtn);
    }

    public function processResultForSyncGameRecords($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJson = $this->getResultJsonFromParams($params);

        $isOpenBets	= $this->getVariableFromContext($params, 'isOpenBets');

        # bet settled date
        $startDate 	= $this->getVariableFromContext($params, 'startDate');
        $endDate 	= $this->getVariableFromContext($params, 'endDate');

        $this->CI->load->model(array('external_system', 'sbtech_new_game_logs'));

        $success = false;
        if(isset($resultJson['errorCode']) && $resultJson['errorCode'] == self::SUCCESS_CODE) {
            $success = true;

            $gameLogs = $resultJson['Bets'];
            if (!empty($gameLogs)) {
                foreach($gameLogs as $logs) {

                    $playerID = $this->getPlayerIdInGameProviderAuth($logs['UserName']);

                    $selections = '';
                    $branch_name = '';
                    $bet_id = '';
                    if (!empty($logs['Selections'])) {
                        foreach($logs['Selections'] as $bet_detail) {
                            $branch_name = $bet_detail['BranchName'];
                        }
                        $bet_id = $logs['Selections'][0]['BetID'];

                        $selections = json_encode($logs['Selections']);
                    }

                    $purchase_id = isset($logs['PurchaseID']) ? $logs['PurchaseID'] : null;

                    # create own unique id ( Purchase ID + Bet ID )
                    $unique_id = $purchase_id . '-' . $bet_id;

                    $data['pl'] = isset($logs['PL']) ? $logs['PL'] : 0;  # set to 0 if open bets
                    $data['non_cashout_amount'] = isset($logs['NonCashOutAmount']) ? $logs['NonCashOutAmount'] : 0;
                    $data['bet_settled_date'] = isset($logs['BetSettledDate']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($logs['BetSettledDate']))) : null;
                    $data['purchase_id'] = $purchase_id;
                    $data['update_date'] = isset($logs['UpdateDate']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($logs['UpdateDate']))) : null;
                    $data['odds'] = isset($logs['Odds']) ? $logs['Odds'] : null;
                    $data['odds_in_user_style'] = isset($logs['OddsInUserStyle']) ? $logs['OddsInUserStyle'] : null;
                    $data['total_stake'] = isset($logs['TotalStake']) ? $logs['TotalStake'] : null;
                    $data['odds_dec'] = isset($logs['OddsDec']) ? $logs['OddsDec'] : null;
                    $data['system_name'] = isset($logs['SystemName']) ? $logs['SystemName'] : null;
                    $data['platform'] = isset($logs['Platform']) ? $logs['Platform'] : null;
                    $data['username'] = isset($logs['UserName']) ? $logs['UserName'] : null;
                    $data['bet_type_name'] = isset($logs['BetTypeName']) ? $logs['BetTypeName'] : null;
                    $data['bet_type_id'] = isset($logs['BetTypeId']) ? $logs['BetTypeId'] : null;
                    $data['creation_date'] = isset($logs['CreationDate']) ?  $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($logs['CreationDate']))) : null;
                    if ($isOpenBets){
                        $status = isset($logs['Status']) ? $logs['Status'] : null;
                    } else {
                        $status = isset($logs['BetStatus']) ? $logs['BetStatus'] : null;
                    }
                    $data['status'] = $status;
                    $data['customer_id'] = isset($logs['CustomerID']) ? $logs['CustomerID'] : null;
                    $data['merchant_customer_id'] = isset($logs['MerchantCustomerID']) ? $logs['MerchantCustomerID'] : null;
                    $data['currency'] = isset($logs['Currency']) ? $logs['Currency'] : null;
                    $data['selections'] = isset($logs['Selections']) ? $selections : null;
                    $data['branch_name'] = $branch_name;

                    //extra info from SBE
                    $data['player_id'] = $playerID ? $playerID : 0;
                    $data['external_uniqueid'] = $unique_id;
                    $data['response_result_id'] = $responseResultId;

                    $this->CI->sbtech_new_game_logs->syncGameLogs($data, $isOpenBets);
                }
            } else {
                $this->CI->utils->debug_log('[sync_original_sbtech] no data', 'startDate', $startDate, 'endDate', $endDate);
            }

        }

        if($isOpenBets) {
            $resultJson = array('openBets' => $resultJson);
        } else {
            $resultJson = array('settledLogs' => $resultJson);
        }

        return array($success, $resultJson);
    }

    public function changePassword($playerName, $oldPassword = null, $newPassword) {
        return $this->returnUnimplemented();
    }

    public function syncMergeToGameLogs($token) {
        $this->CI->load->model(array('game_logs', 'player_model', 'sbtech_new_game_logs'));

        $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeFrom->modify($this->getDatetimeAdjust());
        $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $result = $this->CI->sbtech_new_game_logs->getGameLogStatistics($startDate, $endDate);

        $count = 0;
        if($result) {
            $unknownGame = $this->getUnknownGame();

            foreach ($result as $data) {
                $count++;

                $game_description_id = $data['game_description_id'];
                $game_type_id = $data['game_type_id'];

                if (empty($game_description_id)) {
                    $game_description_id = $unknownGame->id;
                    $game_type_id = $unknownGame->game_type_id;
                }
                $result_amount = $data['result_amount'];

                $match_info = $this->betDetails($data);
                $is_parlay = ($data['bet_type_name'] == self::is_combo_bets) ? true : false;
                $bet_details = $this->CI->utils->encodeJson(array('sports_bet' => $this->betDetails($data),'is_parlay' => $is_parlay));

                $status = $this->getGameRecordsStatus($data['status']);
                $extra = array(
                    'table'=> $data['purchase_id'],
                    'trans_amount' => $data['bet_amount'],
                    'bet_details' => $bet_details,
                    'status'		=> 	$status,
                    'is_parlay' => $is_parlay
                );

                $playerId = $data['player_id'];
                if(empty($playerId)) {
                    $playerId = null;
                }

                $sportsGameFields = array(
                    'match_details'     => '',
                    'match_type'        => '',
                    'handicap'          => '',
                    'bet_type'          => $data['bet_type_name']
                );

                $this->syncGameLogs(
                    $game_type_id,
                    $game_description_id,
                    $data['game_code'],
                    $data['game_type'],
                    $data['game'],
                    $playerId,
                    $data['username'],
                    $data['bet_amount'],
                    $result_amount,
                    null, // win_amount
                    null, // loss_amount
                    null, // after balance
                    0,    // has both side
                    $data['external_uniqueid'],
                    $data['creation_date'],     // use bet time
                    $data['game_date'],  // end
                    $data['response_result_id'],
                    Game_logs::FLAG_GAME,
                    $extra,
                    $sportsGameFields
                );
            }
        }
        return  array('success' => true );
    }

    public function betDetails($data) {
        $bet_details = [];
        $selections = json_decode($data['selections'], true);
        foreach($selections as $key => $game) {
            $bet_details[$key] = array(
                'yourBet' => $game['YourBet'],
                'isLive' => ($game['LiveScore1'] || $game['LiveScore2']) > 0,
                'odd' => $game['Odds'],
                'hdp' => 'N/A',
                'htScore'=> $game['Score'],
                'eventName' => $game['HomeTeam'].' vs '.$game['AwayTeam'],
                'league' => $game['LeagueName'],
            );
        }
        return $bet_details;
    }

    private function getGameRecordsStatus($status) {
        $this->CI->load->model(array('game_logs'));
        $status = strtolower($status);

        switch ($status) {
            case 'opened':
            case 'open':
                $status = Game_logs::STATUS_ACCEPTED;
                break;
            case 'canceled':
                $status = Game_logs::STATUS_CANCELLED;
                break;
            case 'won':
            case 'half won':
            case 'draw':
            case 'lost':
            case 'half lost':
            case 'cashout':
                $status = Game_logs::STATUS_SETTLED;
                break;
        }
        return $status;
    }

    public function batchQueryPlayerBalance($playerNames, $syncId = null) {

        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);

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


    public function login($username, $password = null) {
        return $this->returnUnimplemented();
    }

    public function processResultForgetVendorId($params) {
        return $this->returnUnimplemented();
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
            'AgentUserName' => $this->agent_username,
            'AgentPassword' =>  $this->agent_password,
            'RefTransactionCode' => $transactionId,
        );

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction( $params ){
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);

        $success = $this->processResultBoolean($responseResultId, $resultText, $gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        $resultXml = new SimpleXMLElement($resultText);
        $resultArr = json_decode(json_encode($resultXml), true);

        if($success) {
            $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        } else {

            $error_code = @$resultArr['ErrorCode'];
            switch($error_code) {
                case 'AuthenticationFailed' :
                    $result['reason_id']=self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                    break;
                case 'MerchantIsFrozen' :
                case 'MerchantNotActive' :
                    $result['reason_id']=self::REASON_AGENT_NOT_EXISTED;
                    break;
                case 'Exception' :
                    $result['reason_id']=self::REASON_INCOMPLETE_INFORMATION;
                    break;
                case 'InsufficientFunds' :
                    $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
                    break;
                case 'TransactionCodeNotFound' :
                    $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                    break;
            }

            $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
        }

        return array($success, $result);
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

    public function totalBettingAmount($playerName, $dateTimeFrom, $dateTimeTo) {
        return $this->returnUnimplemented();
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
        return array(false, null);
    }
}

/*end of file*/