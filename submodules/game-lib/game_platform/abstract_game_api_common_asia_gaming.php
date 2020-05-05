<?php
require_once dirname(__FILE__) . '/game_api_suncity.php';

/**
* Game Provider: AG (AsiaGaming)
* Game Type: live casino
* Wallet Type: Transfer
* Currency: RMB (CNY)
/**
* ORIGINAL API NAME: ASIA_GAMING_API
*
* @category game_platform
* @version not specified
* @copyright 2013-2020 TripleOneTech
* @integrator emmanuel.php.ph
**/
abstract class Abstract_game_api_common_asia_gaming extends game_api_suncity
{
    private $_original_gamelogs_table;
    private $_game_platform_id;
    private $_use_bearer_authentication;
    private $_access_token;

    const GRANT_TYPE = 'client_credentials';
    const SCOPE = 'playerapi';

    const API_AUTHORIZE = '_authorize';

    const URI_MAP = array(
        self::API_createPlayer => 'api/player/authorize',
        self::API_login => 'api/player/authorize',
        self::API_logout => 'api/player/deauthorize',
        self::API_queryPlayerBalance => 'api/player/balance',
        self::API_isPlayerExist => 'api/player/balance',
        self::API_depositToGame => 'api/wallet/credit',
        self::API_withdrawFromGame => 'api/wallet/debit',
        // self::API_syncGameRecords => 'api/report/bethistory',
        self::API_syncGameRecords => 'api/history/bets/rollingturnover',
        self::API_queryGameRecords => 'api/report/ProviderGameHistory',
        self::API_AUTHORIZE => 'api/oauth/token'
    );

    public function getPlatformCode()
    {
        return $this->returnUnimplemented();
    }
    
    public function __construct($data)
    {
        parent::__construct();
        $this->_original_gamelogs_table = $data['original_gamelogs_table'];
        $this->_game_platform_id = $data['game_platform_id'];

        $this->api_url = $this->getSystemInfo('url', 'https://tgpaccess.com/');
        $this->currency = $this->getSystemInfo('currency');
        $this->language = $this->getSystemInfo('language');
        $this->clientId = $this->getSystemInfo('clientId');
        $this->betlimitid = $this->getSystemInfo('betlimitid');
        $this->clientSecret = $this->getSystemInfo('clientSecret');
        $this->game_url = $this->getSystemInfo('game_url');
        $this->method = "POST"; # default as POST
        $this->agent_name = $this->getSystemInfo('agent_name');
        $this->api_key = $this->getSystemInfo('api_key');
        $this->update_original = $this->getSystemInfo('update_original_logs');

        $this->gpcode = $this->getSystemInfo('gpcode', 'AG');
        $this->gcode = $this->getSystemInfo('gcode', 'Asia_Gaming_Lobby');

        $this->gameTimeToServerTime = $this->getSystemInfo('gameTimeToServerTime', '+8 hours');
        $this->serverTimeToGameTime = $this->getSystemInfo('serverTimeToGameTime', '-8 hours');
    }

    public function getHttpHeaders($params)
    {
        $current_utc = gmdate("Y-m-d\TH:i:s\Z");
        $stringToSign = $this->clientSecret.$current_utc;
        $signature = base64_encode(hash_hmac('sha1', utf8_encode($stringToSign), utf8_encode($this->clientSecret), true));

        if ($this->_use_bearer_authentication) {
            $bearer_token = $this->getAvailableApiToken();
            $authorization = "Bearer {$bearer_token}";

            $headers = array(
                "Accept" => "application/json",
                "Content-Type" => "application/json",
                "Authorization" => $authorization
            );
        } else {
            $authorization = "SGS ".$this->clientId.':'.$signature;

            $headers = array(
                "Accept" => "application/json",
                "Content-Type" => "application/json",
                "Authorization" => $authorization,
                "X-Sgs-Date" => $current_utc,
            );
        }

        $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'RETURN:', $headers);

        return $headers;
    }

    protected function customHttpCall($ch, $params)
    {
        if ($this->method == self::POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));
        }

        $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'METHOD:', $this->method);
    }

    const API_USING_BEARER_AUTHENTICATION = array(
        self::API_login,
        self::API_queryPlayerBalance,
        self::API_depositToGame,
        self::API_withdrawFromGame,
        self::API_syncGameRecords
    );

    /**
     * will check timeout, if timeout then call again
     * @return token
     */
    public function getAvailableApiToken(){
        $token = $this->getCommonAvailableApiToken(function(){
           return $this->_authorize();
        });
        $this->utils->debug_log("TGP AG Bearer Token: ".$token);
        return $token;
    }

    public function generateUrl($apiName, $params)
    {
        $this->_use_bearer_authentication = false;
        if(in_array($apiName, self::API_USING_BEARER_AUTHENTICATION)){
            $this->_use_bearer_authentication = true;
        }

        $apiUri = self::URI_MAP[$apiName];
        $url = $this->api_url.$apiUri;
        if ($this->method == self::GET) {
            $url = $url.'?'.http_build_query($params);
        }
        return $url;
    }

    // public function processResultBoolean($responseResultId, $resultArr, $playerName = null)
    // {
    //     $success = false;
    //     if ((isset($resultArr['err']) && $resultArr['err']==null) || !array_key_exists('err', $resultArr)) {
    //         $success = true;
    //     }

    //     if (!$success) {
    //         $this->setResponseResultToError($responseResultId);
    //         $this->CI->utils->debug_log('AsiaGaming got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
    //     }
    //     return $success;
    // }

    private function _authorize()
    {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForAuthorize',
            'old_method' => $this->method,
        );

        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => self::GRANT_TYPE,
            'scope' => self::SCOPE
        );

        $this->method = self::POST;

        $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params);

        return $this->callApi(self::API_AUTHORIZE, $params, $context);
    }

    public function processResultForAuthorize($params)
    {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $this->method = $this->getVariableFromContext($params, 'old_method');
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr);

        if ($success) {
            // $this->_access_token = $resultArr['access_token'];
            if($resultArr['access_token']){
                $token_timeout = new DateTime($this->utils->getNowForMysql());
                $minutes = ((int)$resultArr['expires_in']/60)-1;
                $token_timeout->modify("+".$minutes." minutes");
                $result['api_token']=$resultArr['access_token'];
                $result['api_token_timeout_datetime']=$token_timeout->format('Y-m-d H:i:s');
            } 
        }

        $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'RETURN:', $success, $resultArr);

        return array($success, $result);
    }

    public function queryForwardGame($playerName, $extra = null) {
        /*
            TGP Red Tiger
            SB Sunbet
            GB Globalbet
            GD Gold Deluxe
            LAX Laxino
            FC Fly Cow
        */
        $resultArr = $this->login($playerName,null,$extra);

        if ($resultArr['success']) {
            $params = array(
                'gpcode'    => $this->gpcode,
                'gcode'     => $this->gcode,
                #'platform'  => $extra['is_mobile'] ? 1 : 0, # Since the platform type is identified in player authorization method, the ‘platform’ parameter is not needed in game launcher method, and will be removed. (OGP-10807)
                'token'     => $resultArr['authtoken'],
            );

            $params_http = http_build_query($params);
            $url = $this->game_url.'gamelauncher?'.$params_http;

            return array("success" =>$resultArr['success'],"url"=>$url);
        }

        return array("success" =>$resultArr['success']);




        // $resultArr = $this->login($playerName,null,$extra);
        // if($extra['game_code'] == 'lobby'){
        //     $game_url = $this->getSystemInfo('game_desktop_lobby_url');
        //     if($extra['is_mobile']){ # if mobile 
        //         $game_url = $this->getSystemInfo('game_mobile_lobby_url');
        //     }
        //     $url = $game_url.'?token='.$resultArr['authtoken'];
        // }else{
        //     $params = array(
        //         'gpcode'    => $extra['game_type'],
        //         'gcode'     => $extra['game_code'],
        //         #'platform'  => $extra['is_mobile'] ? 1 : 0, # Since the platform type is identified in player authorization method, the ‘platform’ parameter is not needed in game launcher method, and will be removed. (OGP-10807)
        //         'token'     => $resultArr['authtoken'],
        //     );

        //     $params_http = http_build_query($params);
        //     $url = $this->game_url.'gamelauncher?'.$params_http;
        // }

        // return array("success" =>$resultArr['success'],"url"=>$url);
    }

    // public function login($playerName, $password = null, $extra = null)
    // {
    //     $result = $this->_authorize();
    //     $this->CI->load->model('game_provider_auth');
    //     $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
    //     $is_demo_account = $this->CI->game_provider_auth->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
    //     if (array_key_exists('is_mobile', $extra)) {
    //         if ($extra['is_mobile']) {
    //             $platformtype = 1;
    //         } else {
    //             $platformtype = 0;
    //         }
    //     } else {
    //         $platformtype = 0;
    //     }
    //     $is_demo = false;
    //     if ((isset($extra['is_demo_flag'])&&$extra['is_demo_flag'])||(isset($extra['game_mode'])&&$extra['game_mode']!='real')) {
    //         $is_demo = true;
    //     }

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForLogin',
    //         'playerName' => $playerName,
    //         'gameUsername' => $gameUsername
    //     );

    //     $params = array(
    //         'ipaddress' => $this->CI->input->ip_address(),
    //         'username' => $gameUsername,
    //         'userid' => $gameUsername,
    //         # 'tag' => array(), // used to include metadata about the player. This field may be included in player reports.
    //         'lang' => isset($extra['language'])?$this->getLauncherLanguage($extra['language']):$this->language,//$gameUsername,
    //         'cur' => $this->currency,
    //         'betlimitid' => $this->betlimitid, # 1 Bronze - basic limits, 2 Silver - upgraded limits, 3 Gold - high limits, 4 platinum VIP, 5 Diamond - VVIP limits
    //         'istestplayer' => $is_demo_account, # true is player is demo account
    //         'platformtype' => $platformtype, #interger 0 desktop, 1 mobile (OGP-10807)
    //         'access_token' => isset($result['access_token']) ? $result['access_token'] : null
    //     );

    //     $this->method = self::POST;

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'CONTEXT:', $context);

    //     return $this->callApi(self::API_login, $params, $context);
    // }

    // public function processResultForLogin($params)
    // {
    //     $responseResultId = $this->getResponseResultIdFromParams($params);
    //     $resultArr = $this->getResultJsonFromParams($params);
    //     $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
    //     $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'RETURN:', $success, $resultArr);

    //     return array($success, $resultArr);
    // }

    // public function queryPlayerBalance($playerName)
    // {
    //     $result = $this->_authorize();

    //     $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForQueryPlayerBalance',
    //         'playerName' => $playerName,
    //         'gameUsername' => $gameUsername
    //     );

    //     $params = array(
    //         'userid' => $gameUsername,
    //         'cur' => $this->currency,
    //         'access_token' => $result['access_token']
    //     );
    //     $this->method = self::GET;

    //     return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    // }

    // public function processResultForQueryPlayerBalance($params)
    // {
    //     $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
    //     $responseResultId = $this->getResponseResultIdFromParams($params);
    //     $resultArr = $this->getResultJsonFromParams($params);
    //     $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

    //     $result = array();
    //     if ($success) {
    //         $result['balance'] = @floatval($resultArr['bal']);
    //     }

    //     return array($success, $result);
    // }

    // public function depositToGame($playerName, $amount, $transfer_secure_id = null)
    // {
    //     $result = $this->_authorize();

    //     $transfer_secure_id = is_null($transfer_secure_id) ? 'AsiaGaming-' . rand() : $transfer_secure_id;

    //     $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

    //     $timestamp = date('Y-m-d\TH:i:s', time()) . '+00:00';

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForDepositToGame',
    //         'playerName' => $playerName,
    //         'gameUsername' => $gameUsername,
    //         'amount' => $amount,
    //         'transfer_secure_id' => $transfer_secure_id
    //     );

    //     $params = array(
    //         'userid' => $gameUsername,
    //         'amt' => $amount,
    //         'cur' => $this->currency,
    //         'txid' => $transfer_secure_id,
    //         'timestamp' => $timestamp,
    //         'access_token' => $result['access_token']
    //     );

    //     $this->method = self::POST;

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'CONTEXT:', $context);

    //     return $this->callApi(self::API_depositToGame, $params, $context);
    // }

    // public function processResultForDepositToGame($params)
    // {
    //     $responseResultId = $this->getResponseResultIdFromParams($params);
    //     $resultArr = $this->getResultJsonFromParams($params);
    //     $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
    //     $playerName = $this->getVariableFromContext($params, 'playerName');
    //     $amount = $this->getVariableFromContext($params, 'amount');
    //     $transfer_secure_id = $this->getVariableFromContext($params, 'transfer_secure_id');
    //     $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

    //     if (isset($resultArr['dup'])) {
    //         if (!$resultArr['dup']) {
    //             $external_transaction_id = $transfer_secure_id;

    //             return array(
    //                 'success' => true,
    //                 'external_transaction_id' => $external_transaction_id,
    //                 'response_result_id ' => null,
    //                 'didnot_insert_game_logs'=>true,
    //             );
    //         } else {
    //             $this->depositToGame($playerName, $amount);
    //         }
    //     }

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'RETURN:', $success, $resultArr);

    //     return array(false, $resultArr);
    // }

    // public function withdrawFromGame($playerName, $amount, $transfer_secure_id = null)
    // {
    //     $result = $this->_authorize();

    //     $transfer_secure_id = is_null($transfer_secure_id) ? 'AsiaGaming-' . rand() : $transfer_secure_id;

    //     $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

    //     $timestamp = date('Y-m-d\TH:i:s', time()) . '+00:00';

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForWithdrawFromGame',
    //         'playerName' => $playerName,
    //         'gameUsername' => $gameUsername,
    //         'amount' => $amount,
    //         'transfer_secure_id' => $transfer_secure_id
    //     );

    //     $params = array(
    //         'userid' => $gameUsername,
    //         'amt' => $amount,
    //         'cur' => $this->currency,
    //         'txid' => $transfer_secure_id,
    //         'timestamp' => $timestamp,
    //         'access_token' => $result['access_token']
    //     );

    //     $this->method = self::POST;

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'CONTEXT:', $context);

    //     return $this->callApi(self::API_withdrawFromGame, $params, $context);
    // }

    // public function processResultForWithdrawFromGame($params)
    // {
    //     $responseResultId = $this->getResponseResultIdFromParams($params);
    //     $resultArr = $this->getResultJsonFromParams($params);
    //     $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
    //     $playerName = $this->getVariableFromContext($params, 'playerName');
    //     $amount = $this->getVariableFromContext($params, 'amount');
    //     $transfer_secure_id = $this->getVariableFromContext($params, 'transfer_secure_id');
    //     $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

    //     if (isset($resultArr['dup'])) {
    //         if (!$resultArr['dup']) {
    //             $external_transaction_id = $transfer_secure_id;

    //             return array(
    //                 'success' => true,
    //                 'external_transaction_id' => $external_transaction_id,
    //                 'response_result_id ' => null,
    //                 'didnot_insert_game_logs'=>true,
    //             );
    //         } else {
    //             $this->withdrawFromGame($playerName, $amount);
    //         }
    //     }

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'PARAMS:', $params, 'RETURN:', $success, $resultArr);

    //     return array(false, $resultArr);
    // }

    // public function queryForwardGame($playerName, $extra = null)
    // {
    //     /*
    //         TGP Red Tiger
    //         SB Sunbet
    //         GB Globalbet
    //         GD Gold Deluxe
    //         LAX Laxino
    //         FC Fly Cow
    //     */
    //     $resultArr = $this->login($playerName, null, $extra);

    //     if (empty($resultArr['authtoken'])) {
    //         $resultArr['authtoken'] = null;
    //     }

    //     if ($extra['game_code'] == 'lobby') {
    //         $game_url = $this->getSystemInfo('game_desktop_lobby_url');
    //         if ($extra['is_mobile']) { # if mobile
    //             $game_url = $this->getSystemInfo('game_mobile_lobby_url');
    //         }
    //         $url = $game_url.'?token='.$resultArr['authtoken'];
    //     }

    //     $this->CI->utils->debug_log('AsiaGaming: (' . __FUNCTION__ . ')', 'RETURN:', 'result', $resultArr, 'url', $url);

    //     return array("success" =>$resultArr['success'],"url"=>$url);
    // }

    private function getGameDescriptionInfo($row, $unknownGame)
    {
        $game_description_id = null;

        $external_game_id = $row->gameid;
        $extra = array('game_code' => $external_game_id,'game_name' => $row->gamename);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        return $this->processUnknownGame(
            $game_description_id,
            $game_type_id,
            $external_game_id,
            $game_type,
            $external_game_id,
            $extra,
            $unknownGame
        );
    }

    private function getRoundIdKey($roundid)
    {
        return 'game-api-'.$this->_game_platform_id.'-roundid-'.$roundid;
    }

    public function syncOriginalGameLogs($token = false) {

        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
        $endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
        $startDate->modify($this->getDatetimeAdjust());

        $result = array();
        $result[] = $this->CI->utils->loopDateTimeStartEnd($startDate, $endDate, '+24 hours', function($startDate, $endDate)  {
            $startDate=$startDate->format('Y-m-d\TH:i:s');
            $endDate=$endDate->format('Y-m-d\TH:i:s');
            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForSyncOriginalGameLogs',
                'startDate' => $startDate,
                'endDate' => $endDate
            );

            $params = array(
                #'userid' => $playerName, // not required.
                'startdate' => $startDate,
                'enddate' => $endDate,
                'includetestplayers' => false,
                'issettled' => true, // return settled bet only 
            );

            $this->method = self::GET;
            return $this->callApi(self::API_syncGameRecords, $params, $context);
        });
        return array(true, $result);
        // $context = array(
        //     'callback_obj' => $this,
        //     'callback_method' => 'processResultForSyncOriginalGameLogs',
        //     'startDate' => $startDate,
        //     'endDate' => $endDate
        // );

        // $params = array(
        //     #'userid' => $playerName, // not required.
        //     'startdate' => $startDate,
        //     'enddate' => $endDate,
        //     'includetestplayers' => false,
        //     'issettled' => true, // return settled bet only 
        // );
        // $this->method = self::GET;

        // return $this->callApi(self::API_syncGameRecords, $params, $context);

    }

    public function processResultForSyncOriginalGameLogs($params)
    {
        $this->CI->load->model(array($this->_original_gamelogs_table));
        $csvtext = $this->getResultTextFromParams($params);
        $gameRecords = $this->convertResultCsvFromParams($csvtext);
        $responseResultId = $this->getResponseResultIdFromParams($params);

        $success = false;
        if ($params['statusCode'] == 200) {
            $success = true;
        }

        $dataCount = 0;
        $existUgsBetIds = array();
        if (!empty($gameRecords)&&$success) {
            if (!$this->update_original) {
                $gameRecords = $this->CI->asia_gaming_game_logs->getAvailableRows($gameRecords);
            } else {
                $existingRecords = $this->CI->asia_gaming_game_logs->getExistingRows($gameRecords);
                $existUgsBetIds = array_column($existingRecords, 'ugsbetid');
            }
            foreach ($gameRecords as $record) {
                if ($record['roundstatus'] != "Closed") {
                    continue;
                }
                $insertRecord = array();
                //Data from AsiaGaming API
                $insertRecord['ugsbetid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : null;
                $insertRecord['txid'] = isset($record['txid']) ? $record['txid'] : null;
                $insertRecord['betid'] = isset($record['betid']) ? $record['betid'] : null;
                $insertRecord['beton'] = isset($record['beton']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['beton']))) : null;
                $insertRecord['betclosedon'] = isset($record['betclosedon']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['betclosedon']))) : null;
                $insertRecord['betupdatedon'] = isset($record['betupdatedon']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['betupdatedon']))) : null;
                $insertRecord['timestamp'] = isset($record['timestamp']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['timestamp']))) : null;
                $insertRecord['roundid'] = isset($record['roundid']) ? $record['roundid'] : null;
                $insertRecord['roundstatus'] = isset($record['roundstatus']) ? $record['roundstatus'] : null;
                $insertRecord['userid'] = isset($record['userid']) ? $record['userid'] : null;
                $insertRecord['username'] = isset($record['username']) ? $record['username'] : null;
                $insertRecord['riskamt'] = isset($record['riskamt']) ? $record['riskamt'] : null;
                $insertRecord['winamt'] = isset($record['winamt']) ? $record['winamt'] : null;
                $insertRecord['winloss'] = isset($record['winloss']) ? $record['winloss'] : null;
                $insertRecord['rollingturnover'] = isset($record['rollingturnover']) ? $record['rollingturnover'] : null;
                $insertRecord['beforebal'] = isset($record['beforebal']) ? $record['beforebal'] : null;
                $insertRecord['postbal'] = isset($record['postbal']) ? $record['postbal'] : null;
                $insertRecord['cur'] = isset($record['cur']) ? $record['cur'] : null;
                $insertRecord['gameprovider'] = isset($record['gameprovider']) ? $record['gameprovider'] : null;
                $insertRecord['gameprovidercode'] = isset($record['gameprovidercode']) ? $record['gameprovidercode'] : null;
                $insertRecord['gamename'] = isset($record['gamename']) ? $record['gamename'] : null;
                $insertRecord['gameid'] = isset($record['gameid']) ? $record['gameprovidercode'].$record['gameid'] : null;
                $insertRecord['platformtype'] = isset($record['platformtype']) ? $record['platformtype'] : null;
                $insertRecord['ipaddress'] = isset($record['ipaddress']) ? $record['ipaddress'] : null;
                $insertRecord['bettype'] = isset($record['bettype']) ? $record['bettype'] : null;
                $insertRecord['playtype'] = isset($record['playtype']) ? $record['playtype'] : null;
                $insertRecord['playertype'] = isset($record['playertype']) ? $record['playertype'] : null;
                $insertRecord['turnover'] = isset($record['turnover']) ? $record['turnover'] : null;
                $insertRecord['validbet'] = isset($record['validbet']) ? $record['validbet'] : null;
                $game_details = $this->getGameHistory($record['roundid'], $record['username'],$insertRecord['gameprovidercode']);
                $insertRecord['match_detail'] = isset($game_details['url']) ? $game_details['url'] : null;

                //extra info from SBE
                $insertRecord['uniqueid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : null;
                $insertRecord['external_uniqueid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : null;
                $insertRecord['response_result_id'] = $responseResultId;
                $insertRecord['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
                //insert data to AsiaGaming gamelogs table database

                if ($this->update_original && in_array($insertRecord['ugsbetid'], $existUgsBetIds)) {
                    $this->CI->asia_gaming_game_logs->updateGameLogs($insertRecord);
                } else {
                    $insertRecord['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
                    $this->CI->asia_gaming_game_logs->insertGameLogs($insertRecord);
                }
                $dataCount++;
            }
        }
        

        $result['data_count'] = $dataCount;

        return array($success, $result);
    }

            /**
     * queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $sqlTime='original_gamelogs.betclosedon >= ? and original_gamelogs.betclosedon <= ?';
        if($use_bet_time){
            $sqlTime='original_gamelogs.beton >= ? and original_gamelogs.beton <= ?';
        }
        $sql = <<<EOD
SELECT original_gamelogs.id as sync_index,
original_gamelogs.userid as player_username,
original_gamelogs.gameid,
original_gamelogs.gamename,
original_gamelogs.external_uniqueid,
original_gamelogs.beton,
original_gamelogs.betclosedon,
original_gamelogs.winloss AS result_amount,
original_gamelogs.rollingturnover AS bet_amount,
original_gamelogs.bettype,
original_gamelogs.playtype,
original_gamelogs.match_detail,
ABS(original_gamelogs.riskamt) AS real_bet_amount,
original_gamelogs.response_result_id,
original_gamelogs.postbal AS after_balance,
original_gamelogs.roundid AS round_number,
original_gamelogs.gameid AS game_code,
original_gamelogs.gamename AS game,
original_gamelogs.beton as bet_at,
original_gamelogs.beton as start_at,
original_gamelogs.betclosedon as end_at,
original_gamelogs.updated_at,
original_gamelogs.md5_sum,



game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id

FROM {$this->_original_gamelogs_table} as original_gamelogs
LEFT JOIN game_description as gd ON original_gamelogs.gameid = gd.external_game_id AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON original_gamelogs.userid = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE

{$sqlTime}
EOD;

        $params=[$this->getPlatformCode(), $this->getPlatformCode(),
          $dateFrom,$dateTo];

        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
        return $result;
    }

    const MD5_FIELDS_FOR_MERGE = ['beton','betclosedon','bettype','playtype','match_detail'];
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE = ['result_amount','bet_amount','real_bet_amount','after_balance'];

    /**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
        $extra_info=array(
            'bet_type' => $row['bettype'],
            'match_type' => $row['playtype'],
        );
        $has_both_side=0;

        if(empty($row['md5_sum'])){
            //genereate md5 sum
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
        }

        return [
            //set game_type to null unless we know exactly game type name from original game logs
            'game_info'=>['game_type_id'=>$row['game_type_id'], 'game_description_id'=>$row['game_description_id'],
                'game_code'=>$row['game_code'], 'game_type'=>null, 'game'=>$row['game']],
            'player_info'=>['player_id'=>$row['player_id'], 'player_username'=>$row['player_username']],
            'amount_info'=>['bet_amount'=>$row['bet_amount'], 'result_amount'=>$row['result_amount'],
                'bet_for_cashback'=>$row['bet_amount'], 'real_betting_amount'=>$row['real_bet_amount'],
                'win_amount'=>null, 'loss_amount'=>null, 'after_balance'=>$row['after_balance']],
            'date_info'=>['start_at'=>$row['start_at'], 'end_at'=>$row['end_at'], 'bet_at'=>$row['bet_at'],
                'updated_at'=>$row['updated_at']],
            'flag'=>Game_logs::FLAG_GAME,
            'status'=>$row['status'],
            'additional_info'=>['has_both_side'=>$has_both_side, 'external_uniqueid'=>$row['external_uniqueid'], 'round_number'=>$row['round_number'],
                'md5_sum'=>$row['md5_sum'], 'response_result_id'=>$row['response_result_id'], 'sync_index'=>$row['sync_index'],
                'bet_type'=>null ],
            'bet_details'=>$row['bet_details'],
            'extra'=>$extra_info,
            //from exists game logs
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];

    }

    /**
     *
     * perpare original rows, include process unknown game, pack bet details, convert game status
     *
     * @param  array &$row
     */
    public function preprocessOriginalRowForGameLogs(array &$row){
        $this->CI->load->model(array('game_logs'));
        $game_description_id = $row['game_description_id'];
        $game_type_id = $row['game_type_id'];

        if (empty($game_description_id)) {
            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $this->getUnknownGame());
        }
        $row['game_description_id']=$game_description_id;
        $row['game_type_id']=$game_type_id;
        $row['bet_details']= array('url' => $row['match_detail']);
        $row['status'] = Game_logs::STATUS_SETTLED;
    }

    public function syncMergeToGameLogs($token)
    {
        $enabled_game_logs_unsettle=false;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);

        //old
        // $this->CI->load->model(array('game_logs', 'player_model', $this->_original_gamelogs_table));

        // $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        // $dateTimeFrom->modify($this->getDatetimeAdjust());
        // $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        // //observer the date format
        // $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        // $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        // $rlt = array('success' => true);

        // $result = $this->CI->asia_gaming_game_logs->getGameLogStats($startDate, $endDate, $this->_original_gamelogs_table, $this->_game_platform_id);

        // $cnt = 0;
        // if (!empty($result)) {
        //     $unknownGame = $this->getUnknownGame();
        //     foreach ($result as $row) {
        //         $cnt++;

        //         $game_description_id = $row->game_description_id;
        //         $game_type_id = $row->game_type_id;

        //         if (empty($row->game_type_id)&&empty($row->game_description_id)) {
        //             list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $unknownGame);
        //         }
        //         $extra = array(
        //             'bet_for_cashback' => $row->valid_bet_amount,
        //             'trans_amount' => $row->bet_amount,
        //             'table' => $row->round_id,
        //             'bet_type' => $row->bettype,
        //             'match_type' => $row->playtype,
        //             'bet_details' => array('url' => $row->match_detail),
        //         );

        //         $this->syncGameLogs(
        //             $game_type_id,
        //             $game_description_id,
        //             $row->game_code,
        //             $row->game_type,
        //             $row->game,
        //             $row->player_id,
        //             $row->userid,
        //             $row->valid_bet_amount,
        //             $row->result_amount,
        //             null, # win_amount
        //             null, # loss_amount
        //             $row->after_balance, # after_balance
        //             0, # has_both_side
        //             $row->external_uniqueid,
        //             $row->beton, //start
        //             $row->betclosedon, //end
        //             $row->response_result_id,
        //             Game_logs::FLAG_GAME,
        //             $extra
        //         );
        //     }
        // }

        // $this->CI->utils->debug_log('AsiaGaming API =========================>', 'startDate: ', $startDate, 'EndDate: ', $endDate);
        // $this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
        // return $rlt;
    }

}
