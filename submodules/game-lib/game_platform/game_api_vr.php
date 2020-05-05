<?php

require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
*
*/
class Game_api_vr extends Abstract_game_api {
    private $api_url;
    private $agent_id;
    // private $operator_username;
    private $api_salt;
    private $session_token;
    private $game_history_url;
    // private $checklogin = false;
    private $serverTimeToGameTime;
    private $gameTimeToServerTime;
    private $max_limit_of_page_on_sync_record;
    private $opposite_bets_serial_numbers = [];

    const TYPE_DEPOSIT=0;
    const TYPE_WITHDRAW=1;

    const ERROR_PLAYER_NOT_FOUND=8;
    const ERROR_PLAYER_EXISTS=18;

    const CANCELLED_BET=1;

    // const STATE_WIN = "3";
    const STATE_WIN_CHINESE = "中奖";
    // const STATE_CANCELED = "1";
    const STATE_CANCELED_CHINESE = "撤单";
    // const STATE_NOT_WINNING = "2";
    const STATE_NOT_WINNING_CHINESE = "未中奖";

    // For Game History
    // private $gamelogs_APIID;
    // private $gamelogs_APIUser;
    // private $gamelogs_APIAccess;
    // private $gamelogs_APIUrl;

    const VR_VERSION = "1.0";

    const URI_MAP = array(
        self::API_createPlayer => '/Account/CreateUser',
        self::API_login => '/Account/LoginValidate',
        self::API_logout => '/Account/KickUser',
        self::API_queryPlayerBalance => '/UserWallet/Balance',
        self::API_depositToGame => '/UserWallet/Transaction',
        self::API_withdrawFromGame => '/UserWallet/Transaction',
        self::API_syncGameRecords => '/MerchantQuery/Bet',
        self::API_syncLostAndFound => '/MerchantQuery/GameBet',
        self::API_isPlayerExist => '/UserWallet/Balance',
        self::API_queryTransaction => '/UserWallet/TransactionRecord',

        // self::API_changePassword => '/agent_api/player/change_password.php',
        // self::API_depositToGame => '/agent_api/cashier/funds_transfer_to_player.php',
        // self::API_withdrawFromGame => '/agent_api/cashier/funds_transfer_from_player.php',
        // self::API_queryPlayerBalance => '/agent_api/player/balance.php',
        // self::API_isPlayerExist => '/agent_api/player/balance.php',
        // self::API_queryForwardGame => '/agent_api/player/game_token.php',
    );

    const PLAYER = "闲赢";
    const BANKER = "庄赢";
    const BACCARAT = 15;

    private $combined_opposite_bets = [
        "lottery" => [
            "small_big_1" => ['01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24',
                            '25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49'],
            "small_big_2" => ['01234,01234,01234,01234,01234', '56789,56789,56789,56789,56789'],
            "small_big_3" => ['01 02 03 04 05,01 02 03 04 05,01 02 03 04 05', '06 07 08 09 10 11,06 07 08 09 10 11,06 07 08 09 10 11'],
            "small_big_4" => ['01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05',
                             '06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10'],
            "small_big_5" => ['0,1,2,3,4,5,6,7,8,9,10,11,12,13', '14,15,16,17,18,19,20,21,22,23,24,25,26,27'],
            "small_big_6" => ['小,,,,', '大,,,,'],
            "small_big_7" => ['01234,01234,01234','56789,56789,56789'],
            "odd_even_1"  => ['01,03,05,07,09,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49',
                            '02,04,06,08,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48'],
            "odd_even_2"  => ['13579,13579,13579,13579,13579', '02468,02468,02468,02468,02468'],
            "odd_even_3"  => ['01 03 05 07 09 11,01 03 05 07 09 11,01 03 05 07 09 11', '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10'],
            "odd_even_4"  => ['01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09',
                            '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10'],
            "odd_even_5"  => ['1,3,5,7,9,11,13,15,17,19,21,23,25,27', '0,2,4,6,8,10,12,14,16,18,20,22,24,26'],
            "odd_even_6"  => ['02468,02468,02468','13579,13579,13579'],
        ],
        "baccarat" => [
            'player_banker' =>[self::PLAYER,self::BANKER],
        ]
    ];

    private $uncombined_opposite_bets = [
        'baccarat' => [self::PLAYER,self::BANKER],
        "lottery" => [
            '01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24', '25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49',
            '01,03,05,07,09,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49', '02,04,06,08,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48',
            '01234,01234,01234,01234,01234', '56789,56789,56789,56789,56789','01 02 03 04 05,01 02 03 04 05,01 02 03 04 05', '06 07 08 09 10 11,06 07 08 09 10 11,06 07 08 09 10 11',
            '13579,13579,13579,13579,13579', '02468,02468,02468,02468,02468', '01 03 05 07 09 11,01 03 05 07 09 11,01 03 05 07 09 11', '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10',
            '01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09',
            '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10',
            '01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05',
            '06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10',
            '0,1,2,3,4,5,6,7,8,9,10,11,12,13', '14,15,16,17,18,19,20,21,22,23,24,25,26,27','1,3,5,7,9,11,13,15,17,19,21,23,25,27', '0,2,4,6,8,10,12,14,16,18,20,22,24,26','小,,,,', '大,,,,',
            '02468,02468,02468','13579,13579,13579','01234,01234,01234','56789,56789,56789'
        ],
    ];

    public function __construct() {
        parent::__construct();
        $this->api_url = $this->getSystemInfo('url');
        $this->vr_merchant_id = $this->getSystemInfo('vr_merchant_id');

        $this->merchant_key = $this->getSystemInfo('merchant_key');

        $this->player_odds = $this->getSystemInfo('player_odds');

        $this->launcher_url=$this->getSystemInfo('launcher_url', $this->api_url);
        $this->drop_third_decimal_places=$this->getSystemInfo('drop_third_decimal_places', true);
        $this->excel_default_modify_hours=$this->getSystemInfo('excel_default_modify_hours', "-6 hours");

        $this->merge_same_issue_number_to_bet_details=$this->getSystemInfo('merge_same_issue_number_to_bet_details', true);

        $this->max_limit_of_page_on_sync_record=$this->getSystemInfo('max_limit_of_page_on_sync_record', 1000);
        if(empty($this->max_limit_of_page_on_sync_record)){
            $this->max_limit_of_page_on_sync_record=1000;
        }
        // $this->agent_id = $this->getSystemInfo('agent_id');
        // $this->operator_username = $this->getSystemInfo('operator_username');
        // $this->api_salt = $this->getSystemInfo('api_salt');
        // $this->gamelogs_APIID = $this->getSystemInfo('gamelogs_APIID');
        // $this->gamelogs_APIUser = $this->getSystemInfo('gamelogs_APIUser');
        // $this->gamelogs_APIAccess = $this->getSystemInfo('gamelogs_APIAccess');
        // $this->gamelogs_APIUrl = $this->getSystemInfo('gamelogs_APIUrl');
        
        $this->encrpytion   = 'aes-256-ecb';
    }

    public function getPlatformCode() {
        return VR_API;
    }

    public function generateUrl($apiName, $params) {
        $apiUri = self::URI_MAP[$apiName];
        $url = $this->api_url . $apiUri;

        return $url;
    }

    public function getHttpHeaders($params){
        return array();
    }

    protected function customHttpCall($ch, $params) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
        return array(false, null);
    }

    public function processResultBoolean($responseResultId, $resultArr, $playerName = null,$method = null) {
        // $success = false;
        //try errorCode and error_code
        $errorCode= isset($resultArr['errorCode']) ? $resultArr['errorCode'] : @$resultArr['error_code'];

        $success = (isset($errorCode) && $errorCode == 0) ? true: false;

        if($method == self::API_depositToGame || $method == self::API_withdrawFromGame){
            $success = isset($resultArr['state']) && empty($resultArr['state']);
        }else if($method == "syncOriginalGamelogs"){
            $success = isset($resultArr['betRecords']) ? true: false;
        }else if($method == self::API_queryPlayerBalance){
           $success = (isset($resultArr['balance'])  && $resultArr['balance'] < 0)? false: true;
        }else if($method == self::API_isPlayerExist){
           $success = isset($resultArr['playerName']);
        }else if($method == self::API_queryTransaction){
           $success = isset($resultArr['totalRecords']);
        }
        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('===== VR got error: [response: ' . $responseResultId . ', playerName: ' . $playerName . ', result: ' . json_encode($resultArr) . '] =====');
        }

        return $success;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $ip_address = $this->CI->input->ip_address();

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerName'=>$playerName,
            'gameUsername'=>$gameUsername,
        );

        $data = ["playerName" => $gameUsername];

        $this->CI->utils->debug_log('===== VR createPlayer ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('===== VR createPlayer ' . json_encode($params) . ' =====');

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($this->apiDecode($resultText), true);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $gameUsername,
            self::API_createPlayer);
        $this->CI->utils->debug_log('===== VR createPlayer result =====', $resultJsonArr);

        $result=['response_result_id'=>$responseResultId];
        if ($success) {

            $this->session_token = isset($resultJsonArr['session']) ? $resultJsonArr['session']['session_token'] : '';

            $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);

            //update external AccountID
            // $this->updateExternalAccountIdForPlayer($playerId, $resultJsonArr['player']['id']);
        }else{

            if(isset($resultJsonArr['errorCode']) && $resultJsonArr['errorCode']== self::ERROR_PLAYER_EXISTS){
                //exists
                //return true
                $success=true;
                $result['exists']=true;
            }

        }

        return array($success, $result);
    }

    public function login($username, $password = null) {
        $username = $this->getGameUsernameByPlayerUsername($username);
        $loginTime = $this->serverTimeToGameTime(date("Y-m-d H:i:s"));
        // $playerOdds = 1960;
        // $channelId = 1;
        // $departureUrl = null;

        $data = "playerName=" . $username . "&loginTime=" . $loginTime. "&playerOdds=".$this->player_odds;

        $this->CI->utils->debug_log('===== VR login params=====', $data);

        $encrypt_data = $this->apiEncode($data);
        $url_encode_data = urlencode($encrypt_data);

        $url = $this->api_url . self::URI_MAP[self::API_login] . '?version=' . self::VR_VERSION . '&id=' . $this->vr_merchant_id .'&data=' . $url_encode_data;

        return array('url' => $url, 'success' => true);
    }

    public function playerForceLogOff($username) {
        $username = $this->getGameUsernameByPlayerUsername($username);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForPlayerForceLogOff',
            'player_username' => $username
        );

        $data = ["playerName" => $username];

        $this->CI->utils->debug_log('===== VR playerForceLogOff ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('===== VR login params: ' . json_encode($params) . ' =====');

        return $this->callApi(self::API_logout, $params, $context);
    }

    public function processResultForPlayerForceLogOff($params) {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultJsonArr = json_decode($resultText, TRUE);
        $playerName = $this->getVariableFromContext($params, 'player_username');
        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

        $this->CI->utils->debug_log('===== VR playerForceLogOff params: ' . json_encode($params) . ' =====');

        return array($success, $resultJsonArr);
    }

    public function queryPlayerBalance($playerName) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        if(!empty($gameUsername)){
            $context = array(
                'callback_obj' => $this,
                'callback_method' => 'processResultForQueryPlayerBalance',
                'gameUsername' => $gameUsername,
                'playerName' => $playerName
            );

            $data = ["playerName" => $gameUsername];

            $this->CI->utils->debug_log('===== VR queryPlayerBalance data: ' . json_encode($data) . ' =====');

            $json_data = json_encode($data);
            $json_encrypt_data = $this->apiEncode($json_data);

            $params = array(
                'version' => self::VR_VERSION,
                'id' => $this->vr_merchant_id,
                'data' => $json_encrypt_data
            );

            $this->CI->utils->debug_log('===== VR queryPlayerBalance params: ' . json_encode($params) . ' =====');

            $request_token = hash ("sha256", $this->api_salt . http_build_query($params));
            $params['request_token'] = $request_token;

            return $this->callApi(self::API_queryPlayerBalance, $params, $context);
        }else{
            return array('success'=>false, 'balance' => null, 'exists' => false);
        }
    }

    public function processResultForQueryPlayerBalance($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultArr = json_decode($this->apiDecode($resultText), true);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');

        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername, self::API_queryPlayerBalance);
        $this->CI->utils->debug_log('================================= VR queryPlayerBalance result: ' . json_encode($resultArr) . ' =====');

        $result=['response_result_id'=>$responseResultId];
        $this->CI->utils->debug_log('============================================= VR drop_third_decimal_places: ' . $this->drop_third_decimal_places . ' =====');
        if (!empty($success)) {
            if ($this->drop_third_decimal_places) {
                $result['balance'] = round(intval($resultArr['balance'] * 100)/100,2);
            }else{
                $result['balance'] = @floatval($resultArr['balance']);
            }

            $this->CI->utils->debug_log('============================================= VR queryPlayerBalance: ' . $result['balance'] . ' =====');
            $result['exists'] = true;
            // if ($playerId = $this->getPlayerIdInGameProviderAuth($gameUsername)) {
            //     $this->CI->utils->debug_log('============================================= VR queryPlayerBalance [playerIdasd: ' . $playerId . ', playerName: ' . $playerName . ', balance: ' . $result['balance'] . ']');
            // } else {
            //     $this->CI->utils->debug_log('===== VR queryPlayerBalance cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
            // }
        }

        return array($success, $result);
    }

    public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $transfer_secure_id = empty($transfer_secure_id) ? 'T' . random_string('unique') : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $transfer_secure_id,
        );

        $createTime = $this->serverTimeToGameTime(date("Y-m-d H:i:s"));

        $data = [
            "serialNumber" => $transfer_secure_id,
            "playerName" => $gameUsername,
            "type" => self::TYPE_DEPOSIT,
            "amount" => floatval($amount),
            "createTime" => $createTime
        ];

        $json_data = $this->CI->utils->encodeJson($data);

        $this->CI->utils->debug_log('===== VR depositToGame data: ' . $json_data . ' =====');

        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('========================================================== VR depositToGame params: ' . json_encode($params) . ' ===========================================');

        return $this->callApi(self::API_depositToGame, $params, $context);
    }

    public function processResultForDepositToGame($params) {
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultArr = $this->CI->utils->decodeJson($this->apiDecode($resultText));
        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername, self::API_depositToGame);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        $this->CI->utils->debug_log('========================================================== VR depositToGame result ' . json_encode($resultArr) . ' ===========================================================');

        if ($success) {
            $playerBalance = $this->queryPlayerBalance($playerName);
            $afterBalance = null; // $playerBalance['balance'];
            if(isset($resultArr['balance'])){
                if ($this->drop_third_decimal_places) {
                    $afterBalance = round(intval($resultArr['balance'] * 100)/100,2);
                }else{
                    $afterBalance = @floatval($resultArr['balance']);
                }
            }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['after_balance']=$afterBalance;
            // $result["currentplayerbalance"] = $afterBalance;
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

            // if ($playerId) {
            //     //deposit
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeMainWalletToSubWallet());
            // } else {
            //     $this->CI->utils->debug_log('===== VR depositToGame error: '. $playerName);
            // }
            $result['didnot_insert_game_logs']=true;
        } else {
            if(isset($resultArr['state'])){
                $result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['state']);
                $result['transfer_status'] = $this->isDeclinedStatus($resultArr['state']) ? self::COMMON_TRANSACTION_STATUS_DECLINED : self::COMMON_TRANSACTION_STATUS_UNKNOWN;
            }
        }

        return array($success, $result);
    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $transfer_secure_id = empty($transfer_secure_id) ? 'T' . random_string('unique') : $transfer_secure_id;

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'external_transaction_id' => $transfer_secure_id,
        );

        $createTime = $this->serverTimeToGameTime(date("Y-m-d H:i:s"));

        $data = [
            "serialNumber" => $transfer_secure_id,
            "playerName" => $gameUsername,
            "type" => self::TYPE_WITHDRAW,
            "amount" => floatval($amount),
            "createTime" => $createTime
        ];

        $this->CI->utils->debug_log('===== VR withdrawFromGame data: ' . json_encode($data) . ' =====');

        $json_data = $this->CI->utils->encodeJson($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('===== VR withdrawFromGame params: ' . json_encode($params) . ' =====');

        return $this->callApi(self::API_withdrawFromGame, $params, $context);
    }

    public function processResultForWithdrawFromGame($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultArr = $this->CI->utils->decodeJson($this->apiDecode($resultText));
        $success = $this->processResultBoolean($responseResultId, $resultArr,$playerName, self::API_withdrawFromGame);
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        $this->CI->utils->debug_log('===== VR withdrawFromGame result: ' . json_encode($resultArr) . ' =====');

        if ($success) {
            //get current sub wallet balance
            // $playerBalance = $this->queryPlayerBalance($playerName);
            $afterBalance = null; // $playerBalance['balance'];
            if(isset($resultArr['balance'])){
                if ($this->drop_third_decimal_places) {
                    $afterBalance = round(intval($resultArr['balance'] * 100)/100,2);
                }else{
                    $afterBalance = @floatval($resultArr['balance']);
                }
            }
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
            $result['after_balance']=$afterBalance;
            // $result["currentplayerbalance"] = $afterBalance;

            // if ($playerId) {
            //     //withdraw
            //     $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId, $this->transTypeSubWalletToMainWallet());
            // } else {
            //     $this->CI->utils->debug_log('===== VR WithdrawFromGame error: '. $playerName);
            // }
            $result['didnot_insert_game_logs']=true;
        } else {
            if(isset($resultArr['state'])){
                $result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['state']);
                $result['transfer_status'] = $this->isDeclinedStatus($resultArr['state']) ? self::COMMON_TRANSACTION_STATUS_DECLINED : self::COMMON_TRANSACTION_STATUS_UNKNOWN;
            }
        }

        return array($success, $result);
    }

    public function isDeclinedStatus($errorCode){
        $status=self::COMMON_TRANSACTION_STATUS_UNKNOWN;
        if(!empty($errorCode)){
            $errorCode=(int) $errorCode;
            switch ($errorCode) {
                case 8:
                case 10:
                case 14:
                case 7:
                case 17:
                case 15:
                case 16:
                case 3:
                case 4:
                case 5:
                case 6:
                case 1:
                    $status=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
            }
        }

        return $status;
    }

    public function getTransferErrorReasonCode($apiErrorCode) {
        $reasonCode = self::REASON_UNKNOWN;

        switch ((int)$apiErrorCode) {
            case 8:
            $reasonCode = self::REASON_NOT_FOUND_PLAYER;                # User does not exist
                break;
            case 10:
                $reasonCode = self::REASON_NO_ENOUGH_BALANCE;           # not enough balance
                break;
            case 14:
                $reasonCode = self::REASON_DUPLICATE_TRANSFER;          # dupplicate transaction id
                break;
            case 2:
            case 7:
                $reasonCode = self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;           # invalid vendor id
                break;
            case 17:
                $reasonCode = self::REASON_INVALID_TRANSFER_AMOUNT;    # amount error
                break;
            case 15:
                $reasonCode = self::REASON_INVALID_TRANSACTION_ID;     # invalid transfer id
                break;
            case 16:
                $reasonCode = self::REASON_SESSION_TIMEOUT;             # invalid transfer date
                break;
            case 3:
            case 4:
                $reasonCode = self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                break;
            case 5:
            case 6:
                $reasonCode = self::REASON_INVALID_KEY;
                break;
            case 1:
                $reasonCode = self::REASON_INVALID_API_VERSION;
                break;
        }

        return $reasonCode;
    }

    public function generateGotoUri($playerName, $extra){

        return '/iframe_module/goto_vrgame/'.$extra['game_code'];

    }

    //===start queryForwardGame=====================================================================================
    function queryForwardGame($playerName, $extra) {
        $nextUrl=$this->generateGotoUri($playerName, $extra);
        $result=$this->forwardToWhiteDomain($playerName, $nextUrl);
        if($result['success']){
            return $result;
        }

        $gameUsername=$this->getGameUsernameByPlayerUsername($playerName);

        $loginTime = $this->serverTimeToGameTime(date("Y-m-d H:i:s"));
        // $playerOdds = 1960;
        // $channelId = 1;
        // $departureUrl = null;

        $data = "playerName=" . $gameUsername . "&loginTime=" . $loginTime. "&playerOdds=".$this->player_odds;

        if($extra['game_code']){
            $data = $data.'&channelId='.$extra['game_code'];
        }

        if(isset($extra['departureUrl']) && !empty($extra['departureUrl'])){
            $data .= '&departureUrl=' . $extra['departureUrl'];
        }

        $this->CI->utils->debug_log('===== VR login params=====', $data);

        $encrypt_data = $this->apiEncode($data);
        $url_encode_data = urlencode($encrypt_data);

        $url = $this->launcher_url . self::URI_MAP[self::API_login] . '?version=' . self::VR_VERSION . '&id=' . $this->vr_merchant_id .'&data=' . $url_encode_data;

        return array('success' => true, 'url' => $url, 'is_mobile'=>$extra['is_mobile']);

        // $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        // $loginTime = $this->serverTimeToGameTime(date("Y-m-d H:i:s"));
        // // $playerOdds = 1960;
        // // $channelId = 1;

        // $departureUrl = null;

        // if(isset($extra['is_mobile']) && $extra['is_mobile']){
        //     $departureUrl=$this->CI->utils->getSystemUrl('m');
        // }else{
        //     $departureUrl=$this->CI->utils->getSystemUrl('www');
        // }

        // $data = "playerName=" . $gameUsername . "&loginTime=" . $loginTime;

        // if(isset($extra['game_code'])){
        //     $data.='&channelId='.$extra['game_code'];
        // }
        // if(isset($this->player_odds) && !empty($this->player_odds)){
        //     $data.='&playerOdds='.$this->player_odds;
        // }

        // $encrypt_data = $this->apiEncode($data);
        // $url_encode_data = urlencode($encrypt_data);

        // // $this->CI->utils->debug_log('===== VR login params: ' . json_encode($params) . ' =====');

        // $url = $this->api_url . self::URI_MAP[self::API_login] . '?version=' . self::VR_VERSION . '&id=' . $this->vr_merchant_id . '&data=' . $url_encode_data;

        // return array('url' => $url, 'success' => true);
        // return $this->login($playerName);
    }


    function syncOriginalGameLogs($token = false) {
        // NOTE use the servertimetogametime on request
        // use the gametimetoservertime on response.
        // this doesnt use yet the server time

        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate->modify($this->getDatetimeAdjust());
        $startDate = $this->serverTimeToGameTime($startDate);
        $endDate = $this->serverTimeToGameTime($endDate);
        $take = $this->max_limit_of_page_on_sync_record;
        $page = 0;

        $this->ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

        $this->CI->utils->debug_log('---------take--------', $take);

        return $this->_continueSync( $startDate, $endDate, $take, $page );
    }

    public function _continueSync( $startDate, $endDate, $limit, $page) {
        $return = $this->syncVRGamelogs($startDate,$endDate,$limit,$page);

        $pages = 0;
        if ($return['success'] && !empty($return['totalRecords'])) {
            $pages = intval($return['totalRecords'] / $limit);
            $return['total_page']= $pages;
            $this->CI->utils->debug_log('===== VR syncOriginalGamelogs result: ' . json_encode($return) . ' =====');

            if( $pages > $page ){
                $page += 1;
                return $this->_continueSync( $startDate, $endDate, $limit, $page );
            }
        }

        $return['total_page']= $pages;

        $this->CI->utils->debug_log('===== VR syncOriginalGamelogs finish: ' . json_encode($return) . ' =====');
        return $return;
    }

    public function syncVRGamelogs($startDate,$endDate,$limit,$page) {

        $use_create_time=$this->ignore_public_sync;

        $data = [
            "startTime" => $startDate,
            "endTime" => $endDate,
            "channelId" => -1,
            "issueNumber" => null,
            "playerName" => null, // Remove this
            "serialNumber" => null,
            "state" => -1,
            "isUpdateTime" => !$use_create_time,
            "recordCountPerPage" => $limit,
            "recordPage" => $page
        ];

        $this->CI->utils->debug_log('===== VR syncOriginalGamelogs params: ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncGameRecords',
            'startDate' => $startDate,
            'endDate' => $endDate
        );

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );
        // $this->CI->utils->debug_log('===== VR syncOriginalGamelogs params: ' . json_encode($params) . ' =====');

        return $this->callApi(self::API_syncGameRecords, $params, $context);

    }

    public function processResultForSyncGameRecords($params) {
        $this->CI->load->model(array('vr_game_logs', 'player_model'));

        $resultText = $this->getResultTextFromParams($params);
        $resultArr = json_decode($this->apiDecode($resultText), true);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId,$resultArr,null,"syncOriginalGamelogs");
        $result = array();

        // $dataCount = 0;
        if ($success) {
            // $current_page = $resultArr['page_number'];
            // $total_page = ceil($resultArr['total_rows'] / $resultArr['rows_per_page']);
            $gameRecords = @$resultArr['betRecords'];

            $result['recordPage'] = @$resultArr['recordPage'];
            $result['totalRecords'] = @$resultArr['totalRecords'];
            $result['count'] =0;

            $this->CI->utils->debug_log('vr record', $resultArr['recordPage'], $resultArr['totalRecords']);

            if ($gameRecords) {
                list($availableRows, $diffStateRows) = $this->CI->vr_game_logs->getAvailableRows($gameRecords);
                #should allow update
                // $availableRows = $gameRecords;

                $this->CI->utils->debug_log('vr available rows', count($availableRows), count($gameRecords), count($diffStateRows));
                //free
                unset($gameRecords);

                if (!empty($availableRows)) {
                    $result['count'] += $this->processVrGameLogs($availableRows,$responseResultId,null, 'insert');
                }

                if (!empty($diffStateRows)) {
                    $result['count'] += $this->processVrGameLogs($diffStateRows,$responseResultId,null, 'update');
                }

                unset($availableRows);
                unset($diffStateRows);
            }
        }

        return array($success, $result);
    }

    private function processVrGameLogs($game_record, $response_result_id = null, $local_sync = null, $update_type='insert'){
        $this->CI->utils->debug_log('===== VR processVrGameLogs local_sync: ' . $local_sync);
        // $vr_game_record = array();
        // $list_of_game_id = array();
        $data_count = 0;
        $this->CI->load->model(["vr_game_logs","game_description_model"]);

        foreach ($game_record as $record) {

            if (empty($record['playerName'])) {
                $this->CI->utils->error_log('wrong record, lost playerName', $record);
                continue;
            }

            if ( ! empty($local_sync)) {
                #get channelId in game description table (provided excel file doesn't have:
                # channelId
                # prizeDetail
                # subState
                $where = "game_platform_id = " . $this->getPlatformCode() . " and game_name like '%" . $record['channelName'] . "%'";
                $game_detail = $this->CI->game_description_model->getGameByQuery("external_game_id",$where);
                $record['channelId'] = isset($game_detail[0]['external_game_id']) ? $game_detail[0]['external_game_id']: null;
            }

            // $playerID = $this->getPlayerIdInGameProviderAuth(strtolower($record['playerName']));
            // $playerUsername = $this->getGameUsernameByPlayerId($playerID);

            // if(empty($playerID)) continue;

            #if issue number exist add the current data to extra: checking with username and issueNumber

            // $currentIssueNumberData = $this->CI->vr_game_logs->isIssueNumberAlreadyExist($record);
            // if ( ! empty($currentIssueNumberData)) {
            //     if ($currentIssueNumberData['serialNumber'] == $record['serialNumber']) continue;

            //     if ( ! empty($currentIssueNumberData['extra'])) {
            //         $isSerialNumberExistOnExtra = strpos($currentIssueNumberData['extra'], $record['serialNumber']);
            //         if ( ! empty($isSerialNumberExistOnExtra)) continue;
            //     }

            //     if ($record['state'] == self::CANCELLED_BET) continue;

            //     $newGameRecord['extra'] = $this->prepareGameRecordExtra($currentIssueNumberData,$record);
            //     $newGameRecord['extra'] = empty($newGameRecord['extra']) ? null : json_encode($newGameRecord['extra']);
            //     $this->cCI->vr_game_logs->updateGameLogsExtra($newGameRecord,$record);

            //     continue;
            // }

            // $extra = $this->prepareGameRecordExtra(null,$record);

            $vrGameRecord = [
                // Data from VR
                'cost' => isset($record['cost']) ? $record['cost'] : NULL,
                'unit' => isset($record['unit']) ? $record['unit'] : NULL,
                'lossPrize' => isset($record['lossPrize']) ? $record['lossPrize'] : NULL,
                'playerPrize' => isset($record['playerPrize']) ? $record['playerPrize'] : (isset($record['prize']) ? $record['prize'] : NULL),
                'merchantPrize' => isset($record['merchantPrize']) ? $record['merchantPrize'] : NULL,
                'state' => isset($record['state']) ? $record['state'] : (isset($record['state']) == null ? 3 : NULL),
                'count' => isset($record['count']) ? $record['count'] : NULL,
                'multiple' => isset($record['multiple']) ? $record['multiple'] : NULL,
                'channelId' => isset($record['channelId']) ? $record['channelId'] : NULL,
                'subState' => isset($record['subState']) ? $record['subState'] : NULL,
                'prizeDetail' => isset($record['prizeDetail']) ? json_encode($record['prizeDetail']) : (isset($record['prize']) ? json_encode($record['prize']) : NULL),
                'updateTime' => isset($record['updateTime']) ? $this->gameTimeToServerTime($record['updateTime']) : NULL,
                'createTime' => isset($record['createTime']) ? $this->gameTimeToServerTime($record['createTime']) : NULL,
                'note' => isset($record['note']) ? $record['note'] : NULL,
                'winningNumber' => isset($record['winningNumber']) ? $record['winningNumber'] : NULL,
                'issueNumber' => isset($record['issueNumber']) ? $record['issueNumber'] : NULL,
                'betTypeName' => isset($record['betTypeName']) ? $record['betTypeName'] : NULL,
                'channelName' => isset($record['channelName']) ? $record['channelName'] : NULL,
                'position' => isset($record['position']) ? $record['position'] : NULL,
                'number' => isset($record['number']) ? $record['number'] : NULL,
                'odds' => isset($record['odds']) ? $record['odds'] : NULL,
                'playerName' => isset($record['playerName']) ? $record['playerName'] : NULL,
                'serialNumber' => isset($record['serialNumber']) ? $record['serialNumber'] : NULL,
                'merchantCode' => isset($record['merchantCode']) ? $record['merchantCode'] : NULL,

                'issue_key' =>  @$record['channelId'].'-'.@$record['issueNumber'].'-'.@$record['playerName'],
                //extra info from SBE
                // 'Username' => $playerUsername,
                // 'PlayerId' => $playerID,
                'external_uniqueid' => isset($record['serialNumber']) ? $record['serialNumber'] : NULL,
                'response_result_id' => $response_result_id,
                'extra' => null, // $this->CI->utils->encodeJson($extra),
            ];
            //record update or insert time
            $vrGameRecord['last_sync_time']=$this->CI->utils->getNowForMysql();

            // $isSerialNumberExist = $this->CI->vr_game_logs->checkSerialNumberAlreadyExist($vrGameRecord);
            if ( $update_type=='update') {
                $this->CI->vr_game_logs->updateVrGameLogs($vrGameRecord);
            }else{
                $this->CI->vr_game_logs->insertVrGameLogs($vrGameRecord);
            }
            $data_count++;
        }


        return $data_count;

    }

    public function prepareGameRecordExtra($currentRecord = null, $newRecord){

        $extra=[];

        // if(!empty($currentRecord['extra'])){
        //     $extra = json_decode($currentRecord['extra'],true);
        // }

        // if (!empty($newRecord['extra'])) {
        //     $newRecord_extra = json_decode($newRecord['extra'], true);

        //     #check if the serial number is not yet exist: if not add it to extra
        //     foreach ($newRecord_extra as $serial_number => $current_extra_details) {
        //         if(empty($extra[$serial_number])){
        //             $extra[$serial_number] = $current_extra_details;
        //         }
        //     }

        // }else{
            $extra[$newRecord['serialNumber']] = [
                'odds' => $newRecord['odds'],
                'bet_amount' => $newRecord["cost"],
                'win_amount' => $newRecord["playerPrize"],
                'place_of_bet' => isset($newRecord['number']) ? $newRecord['number']:null,
                'after_balance' => null,
                'winloss_amount' => $newRecord["playerPrize"] - $newRecord["cost"],
                'prize_details' => isset($newRecord["prizeDetail"]) ? $newRecord["prizeDetail"]:null,
                'state' => $newRecord["state"],
            ];
        // }

        return $extra;
    }

    #sync for electronic games record
    public function syncLostAndFound($token) {
        $startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $startDate->modify($this->getDatetimeAdjust());
        $startDate = $this->serverTimeToGameTime($startDate);
        $endDate = $this->serverTimeToGameTime($endDate);
        $take = $this->max_limit_of_page_on_sync_record;
        $page = 0;

        $this->ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

        $this->CI->utils->debug_log('---------take--------', $take);

        return $this->_continueVRGGLSync( $startDate, $endDate, $take, $page );
    }

    public function _continueVRGGLSync( $startDate, $endDate, $limit, $page) {
        $return = $this->syncVRGGLGamelogs($startDate,$endDate,$limit,$page);

        $pages = 0;
        if ($return['success'] && !empty($return['totalRecords'])) {
            $pages = intval($return['totalRecords'] / $limit);
            $return['total_page']= $pages;
            $this->CI->utils->debug_log('===== VR syncOriginalGamelogs result: ' . json_encode($return) . ' =====');

            if( $pages > $page ){
                $page += 1;
                return $this->_continueSync( $startDate, $endDate, $limit, $page );
            }
        }

        $return['total_page']= $pages;

        $this->CI->utils->debug_log('===== VR syncOriginalGamelogs finish: ' . json_encode($return) . ' =====');
        return $return;
    }

    public function syncVRGGLGamelogs($startDate,$endDate,$limit,$page) {

        $use_create_time=$this->ignore_public_sync;

        $data = [
            "startTime" => $startDate,
            "endTime" => $endDate,
            "type" => 0,
            "playerName" => null, // Remove this
            "serialNumber" => null,
            "recordCountPerPage" => $limit,
            "recordPage" => $page
        ];

        $this->CI->utils->debug_log('===== VR syncOriginalGamelogs params: ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForSyncVRGGLGamelogs',
            'startDate' => $startDate,
            'endDate' => $endDate
        );

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );
        // $this->CI->utils->debug_log('===== VR syncOriginalGamelogs params: ' . json_encode($params) . ' =====');

        return $this->callApi(self::API_syncLostAndFound, $params, $context);

    }

    public function processResultForSyncVRGGLGamelogs($params) {
        $this->CI->load->model(array('vr_game_logs', 'player_model'));

        $resultText = $this->getResultTextFromParams($params);
        $resultArr = json_decode($this->apiDecode($resultText), true);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = $this->processResultBoolean($responseResultId,$resultArr,null,"syncOriginalGamelogs");
        $result = array();

        // $dataCount = 0;
        if ($success) {
            // $current_page = $resultArr['page_number'];
            // $total_page = ceil($resultArr['total_rows'] / $resultArr['rows_per_page']);

            $gameRecords = @$resultArr['betRecords'];

            $this->CI->utils->debug_log('===== processResultForSyncVRGGLGamelogs =========================', $gameRecords);

            $result['recordPage'] = @$resultArr['recordPage'];
            $result['totalRecords'] = @$resultArr['totalRecords'];
            $result['count'] =0;


            $this->CI->utils->debug_log('vr record', $resultArr['recordPage'], $resultArr['totalRecords']);

            if ($gameRecords) {
                list($availableRows, $diffStateRows) = $this->CI->vr_game_logs->getAvailableRows($gameRecords);
                #should allow update
                // $availableRows = $gameRecords;

                $this->CI->utils->debug_log('vr available rows', count($availableRows), count($gameRecords), count($diffStateRows));
                //free
                unset($gameRecords);

                if (!empty($availableRows)) {
                    $result['count'] += $this->processVrGameLogs($availableRows,$responseResultId,null, 'insert');
                }

                if (!empty($diffStateRows)) {
                    $result['count'] += $this->processVrGameLogs($diffStateRows,$responseResultId,null, 'update');
                }

                unset($availableRows);
                unset($diffStateRows);
            }
        }

        return array($success, $result);
    }

    public function syncMergeToGameLogs($token) {
        $this->CI->load->model(array('game_logs', 'game_description_model', 'vr_game_logs'));

        $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

        $this->ignore_public_sync = $this->getValueFromSyncInfo($token, 'ignore_public_sync');

        // Observer the date format
        $dateTimeFrom->modify($this->getDatetimeAdjust());
        // $startDate = $this->serverTimeToGameTime($dateTimeFrom);
        // $endDate = $this->serverTimeToGameTime($dateTimeTo);

        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');

        $rlt = array('success' => true);
        //if it's manually sync
        $use_create_time=$this->ignore_public_sync;
        $result = $this->CI->vr_game_logs->getGameLogStatistics($startDate, $endDate, $use_create_time );

        $this->CI->utils->debug_log('===== syncMergeToGameLogs [dateTimeFrom: ' . $startDate . ', dateTimeTo: ' . $endDate . '] =====', count($result), 'use_create_time', $use_create_time);

        $where = "external_game_id not in ('unknown',".self::BACCARAT.",'') and game_platform_id = " . $this->getPlatformCode();
        $channelRows = $this->CI->game_description_model->getGameByQuery("external_game_id",$where);
        $currentChannelIds = array_column($channelRows, "external_game_id");

        $cnt = 0;
        $this->CI->utils->debug_log('===== syncMergeToGameLogs ======================================', $result);
        if (!empty($result)) {
            $unknownGame = $this->getUnknownGame();

            //process bet details
            //merge_same_issue_number_to_bet_details

            $this->batchProcessBetDetails($result);


            foreach ($result as $vrData) {

                $playerId = $vrData["player_id"];

                // if (!$playerId) {
                //     continue;
                // }

                $cnt++;

                $game_description_id = $vrData["game_description_id"];
                $game_type_id = $vrData["game_type_id"];

                if (empty($game_description_id)) {
                    list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($vrData, $unknownGame);
                }

                $betAmount = $vrData["bet_amount"];
                $validBetAmount = $vrData["bet_amount"];
                $winAmount = $vrData["win_amount"];
                // if ($vrData['response_result_id']) {
                $resultAmount = $winAmount - $betAmount;
                // }else{
                //     $resultAmount = $winAmount;
                // }

                #set default value to calculated opposite bet
                // if (in_array($vrData['serialNumber'], $this->opposite_bets_serial_numbers)) {
                //     $validBetAmount = 0;
                //     $resultAmount = 0;
                // }else{
                    $this->processValidBetamount($vrData, $betAmount, $resultAmount, $validBetAmount,$currentChannelIds);
                // }

                $extra = array(
                    'trans_amount' => $betAmount,
                    'bet_details'  => $vrData['bet_details'],
                    'table'        => $vrData['serialNumber'],
                    'bet_type'     => $vrData['multibet'] ? 'Multi Bet':'Single Bet',
                    'sync_index' => $vrData['id'],
                );

                $this->syncGameLogs(
                    $game_type_id,
                    $game_description_id,
                    $vrData["game_code"],
                    $vrData["game_type"],
                    $vrData["game"],
                    $playerId,
                    $vrData["playerName"],
                    $validBetAmount, # bet_amount
                    $resultAmount, # result_amount
                    null, # win_amount
                    null, # loss_amount
                    null, # after_balance
                    0, # has_both_side
                    $vrData["external_uniqueid"],
                    $vrData["create_time"], //start
                    // $vrData["update_time"], //end
                    $vrData["create_time"], //end
                    $vrData["response_result_id"],
                    1,
                    $extra
                );
            }

        }

        $this->CI->utils->debug_log('===== syncMergeToGameLogs monitor count: ' . $cnt . ' =====');

        return $rlt;
    }

    /**
     * process valid bet amount
     * @param  array $row
     * @param  double &$betAmount      [description]
     * @param  double &$resultAmount   [description]
     * @param  double &$validBetAmount [description]
     * @return null
     */
    private function processValidBetamount($row, &$betAmount, &$resultAmount, &$validBetAmount,$currentChannelIds){

        return null;

        $betDetails = $this->CI->utils->decodeJson($row['bet_details']);

        if (!empty($betDetails['bet_details'])) {

            $currentGameType = "lottery";

            $issueNumber = $row['issueNumber'];
            $channels = [
                "lottery" => $currentChannelIds,
                "baccarat" => [self::BACCARAT],
            ];

            foreach ($channels as $key => $channelIds) {
                if(in_array($row['channel_id'], $channelIds)){
                    $currentGameType = $key;
                }
            }

            #map opposite bets
            $mapOppositeBet = [];
            foreach ($betDetails['bet_details'] as $serialNumber => $betDetail) {
                if (in_array($betDetail['bet_placed'], $this->uncombined_opposite_bets[$currentGameType])) {
                    $mapOppositeBet[$betDetail['bet_placed']] = [
                        'bet_amount'=>$betDetail['bet_amount'],
                        'win_amount'=>$betDetail['win_amount'],
                        'result_amount'=>$betDetail['winloss_amount'],
                    ];
                }
            }

            #merge opposite bet
            $mapMergeOppositeBet = [];
            foreach ($this->combined_opposite_bets[$currentGameType] as $oppositeBetName => $oppositeBetsList) {
                foreach ($mapOppositeBet as $betPlaced => $oppositeBetDetails) {
                    if(in_array($betPlaced, $oppositeBetsList, true)){
                        $mapMergeOppositeBet[$oppositeBetName][$betPlaced] = $oppositeBetDetails;
                    }
                }
            }

            #only opposite bet can be recalculated
            if ( ! empty($mapMergeOppositeBet)) {
                // foreach ($betDetails['bet_details'] as $serialNumber => $betDetail) {
                //     array_push($this->opposite_bets_serial_numbers, $serialNumber);
                // }

                $finalizedMap = $calculatedBetPerOppositeBet = [];
                #calculate opposite bets
                foreach ($mapMergeOppositeBet as $oppositeBetName => $oppositeBets) {

                    $betMap = [];
                    foreach ($oppositeBets as $betPlaced => $betDetails) {
                        if(!empty($betMap['bet_amount'])){
                            #use result amount when bet amount are equal
                            if($betMap['bet_amount'] == $betDetails['bet_amount']){
                                $calculatedBetPerOppositeBet['validBetAmount'] = abs($betMap['result_amount']+$betDetails['result_amount']);
                            } else {
                                $calculatedBetPerOppositeBet['validBetAmount'] = abs($betMap['bet_amount']-$betDetails['bet_amount']);
                            }

                            #replace initialized the values
                            $calculatedBetPerOppositeBet['totalBetAmount'] += $betDetails['bet_amount'];
                            $calculatedBetPerOppositeBet['resultAmount'] += $betDetails['result_amount'];
                        }else{

                            #for single row only
                            $betMap = [
                                'bet_amount' => $betDetails['bet_amount'],
                                'result_amount' => $betDetails['result_amount'],
                            ];

                            $calculatedBetPerOppositeBet['totalBetAmount'] = $betDetails['bet_amount'];
                            $calculatedBetPerOppositeBet['resultAmount'] = $betDetails['result_amount'];
                        }
                    }
                    $finalizedMap[$oppositeBetName] = $calculatedBetPerOppositeBet;
                }

                $validBetAmount = array_sum(array_column($finalizedMap, 'validBetAmount'));
                // $resultAmount = array_sum(array_column($finalizedMap, 'resultAmount'));
            }
        }
    }

    /**
     * batch process bet details, convert same issue number to bet details, and process valid bet amount
     * @param  array &$rows
     */
    private function batchProcessBetDetails(&$rows){

        if(empty($rows)){
            return true;
        }

        $this->CI->load->model(['vr_game_logs']);
        //get all issue number
        // $map=[];
        $issueKeys=[];
        $mapsByIssue=[];
        $settledStates=[Vr_game_logs::STATE_WIN, Vr_game_logs::STATE_NOT_WINNING];
        //create issue keys
        foreach ($rows as $row) {
            if(!in_array($row['state'], $settledStates)){
                continue;
            }
            if (isset($row['issueNumber']) && (strpos( $row['issueNumber'], '-' ) !== false))
            {
                continue;
            }
            $issueKeys[]=$row['issue_key'];
        }
        //get multiple bet
        if(!empty($issueKeys)){
            $mapsByIssue=$this->CI->vr_game_logs->getMultipleBetRowsByIssueKeys($issueKeys, $settledStates);
        }

        $this->CI->utils->debug_log('batchProcessBetDetails issueKeys:'.count($issueKeys).', mapsByIssue:'.count($mapsByIssue).', rows:'.count($rows));

        //update $row
        foreach ($rows as &$row) {
            $is_multibet = false;
            $bet_details = [];

            if(isset($mapsByIssue[$row['issue_key']]) && !empty($mapsByIssue[$row['issue_key']]) ){
                //exists multiple
                $is_multibet=true;
                $issueRows=$mapsByIssue[$row['issue_key']];
                foreach ($issueRows as $issueRow) {
                    $this->insertSingleBetDetail($issueRow, $bet_details);
                }
            }else{
                $this->insertSingleBetDetail($row, $bet_details);
            }

            $row['bet_details']=$this->CI->utils->encodeJson(['bet_details'=>$bet_details]);
            $row['multibet']=$is_multibet;
        }

    }

    public function checkOdds($oddStr){
        $odds = implode(", ",explode('/',$oddStr)) ;
        return $odds;
    }

    private function insertSingleBetDetail($row, &$bet_details){

        $betAmount = $row["bet_amount"];
        $winAmount = $row["win_amount"];
        if ($row['response_result_id']) {
            $resultAmount = $winAmount - $betAmount;
        }else{
            $resultAmount = $winAmount;
        }

        //single bet
        $bet_details[$row['serialNumber']] = [
            "odds" => $this->checkOdds($row['odds']),
            "bet_amount" =>  $betAmount,
            "bet_placed" => $row['number'],
            "win_amount" => $winAmount,
            "won_side" => $row['winningNumber'],
            "winloss_amount" => $resultAmount,
            'issue_number' => $row['issueNumber'],
            'bet_type_name' => $row['betTypeName'],
        ];

        return $bet_details;
    }

    private function prepareValidBetAmount($game_record,$current_result_amount,$extra,$current_channel_ids){
        $issueNumber = $game_record['issueNumber'];
        $channels = [
            "lottery" => $current_channel_ids,
            "baccarat" => [self::BACCARAT],
        ];

        $current_game_type = "";
        $current_bet_type = $game_record['betPlaced'];
        $current_bet_amount = $game_record['bet_amount'];

        $list_of_opposite_bet_types_per_game_type = [
            "lottery" => [
                "small_big_1" => ['01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24',
                                '25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49'],
                "small_big_2" => ['01234,01234,01234,01234,01234', '56789,56789,56789,56789,56789'],
                "small_big_3" => ['01 02 03 04 05,01 02 03 04 05,01 02 03 04 05', '06 07 08 09 10 11,06 07 08 09 10 11,06 07 08 09 10 11'],
                "small_big_4" => ['01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05',
                                 '06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10'],
                "small_big_5" => ['0,1,2,3,4,5,6,7,8,9,10,11,12,13', '14,15,16,17,18,19,20,21,22,23,24,25,26,27'],
                "small_big_6" => ['小,,,,', '大,,,,'],
                "odd_even_1"  => ['01,03,05,07,09,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49',
                                '02,04,06,08,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48'],
                "odd_even_2"  => ['13579,13579,13579,13579,13579', '02468,02468,02468,02468,02468'],
                "odd_even_3"  => ['01 03 05 07 09 11,01 03 05 07 09 11,01 03 05 07 09 11', '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10'],
                "odd_even_4"  => ['01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09',
                                '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10'],
                "odd_even_5"  => ['1,3,5,7,9,11,13,15,17,19,21,23,25,27', '0,2,4,6,8,10,12,14,16,18,20,22,24,26'],
            ],
            "baccarat" => [
                'player_banker' =>[self::PLAYER,self::BANKER],
            ]
        ];

        $list_of_opposite_bet_type_keys = [
            'baccarat' => [self::PLAYER,self::BANKER],
            "lottery" => [
                '01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24', '25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49',
                '01,03,05,07,09,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49', '02,04,06,08,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48',
                '01234,01234,01234,01234,01234', '56789,56789,56789,56789,56789','01 02 03 04 05,01 02 03 04 05,01 02 03 04 05', '06 07 08 09 10 11,06 07 08 09 10 11,06 07 08 09 10 11',
                '13579,13579,13579,13579,13579', '02468,02468,02468,02468,02468', '01 03 05 07 09 11,01 03 05 07 09 11,01 03 05 07 09 11', '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10',
                '01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09,01 03 05 07 09',
                '02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10,02 04 06 08 10',
                '01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05,01 02 03 04 05',
                '06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10,06 07 08 09 10',
                '0,1,2,3,4,5,6,7,8,9,10,11,12,13', '14,15,16,17,18,19,20,21,22,23,24,25,26,27','1,3,5,7,9,11,13,15,17,19,21,23,25,27', '0,2,4,6,8,10,12,14,16,18,20,22,24,26','小,,,,', '大,,,,'
            ],
        ];

        $list_of_current_bet_type = [
            'baccarat' => [],
            'lottery' => [],
        ];

        $bet_type_map=[
            'baccarat' => [],
            'lottery' => [],
        ];

        $list_of_valid_bet_amount = [];
        $valid_bet_amount = 0;

        $current_game_type = 'lottery';//set default lottery to avoid error in game

        foreach ($channels as $key => $channel_ids) {
            if(in_array($game_record['channel_id'], $channel_ids)){
                $current_game_type = $key;
            }
        }

        $extra[$game_record['external_uniqueid']] = [
            'bet_amount' => $game_record['bet_amount'],
            'win_amount' => $game_record['win_amount'],
            'winloss_amount' => $current_result_amount,
            'place_of_bet' =>$game_record['winningNumber'],
            'state' =>$game_record['state'],
        ];


        if(is_array($extra)){
            foreach ($extra as $key => $game_record_extra) {
                if ($game_record_extra['state'] == self::CANCELLED_BET) continue;

                if (empty($game_record['response_result_id'])) {
                    $game_record_extra['winloss_amount'] = $game_record_extra['win_amount'];
                }

                #insert bet type from extra collumn to list_of_current_bet_type[$current_game_type]
                if( ! in_array($game_record_extra['place_of_bet'], $list_of_current_bet_type[$current_game_type])){

                    #check if bet occured 2 times for one bet type only
                    if(isset($list_of_current_bet_type[$current_game_type][$game_record_extra['place_of_bet']])){

                        $bet_amount = $list_of_current_bet_type[$current_game_type][$game_record_extra['place_of_bet']]['bet_amount'] + $game_record_extra['bet_amount'];
                        $result_amount = $list_of_current_bet_type[$current_game_type][$game_record_extra['place_of_bet']]['result_amount'] + $game_record_extra['winloss_amount'];

                        $list_of_current_bet_type[$current_game_type][$game_record_extra['place_of_bet']] = [
                            "bet_amount" => $bet_amount,
                            "result_amount" => $result_amount,
                        ];

                    }else{
                         $list_of_current_bet_type[$current_game_type][$game_record_extra['place_of_bet']] = [
                            "bet_amount" => $game_record_extra['bet_amount'],
                            "result_amount" => $game_record_extra['winloss_amount'],
                        ];
                    }

                }

            }
        }


        #list all available bet
        foreach ($list_of_opposite_bet_types_per_game_type[$current_game_type] as $key => $opposite_bets) {

            foreach ($list_of_current_bet_type[$current_game_type] as $bet_type_key => $bet_details) {

                #calculate the real result amount by subtracting the bet amount to result amount
                // $bet_details['result_amount'] = ($bet_details['result_amount']>0) ? $bet_details['result_amount'] - $bet_details['bet_amount']: -$bet_details['bet_amount'];

                #put the bet details in bet type map per opposite bet type
                if(in_array($bet_type_key, $opposite_bets, true)){
                    $bet_type_map[$current_game_type][$key][$bet_type_key]= $bet_details;
                }

                #list the not opposite bet type for the seperate calcualtion of valid bet amount
                if( ! in_array($bet_type_key, $list_of_opposite_bet_type_keys[$current_game_type], true)){
                    $bet_type_map[$current_game_type]['not_opposite_bet'][$bet_type_key]= $bet_details;
                }

            }

        }

        #prepare the valid bet amount per opposite bet type
        foreach ($bet_type_map[$current_game_type] as $opposite_bet_name => $current_bet_map) {

            #opposite bets have different computation
            if ($opposite_bet_name == 'not_opposite_bet') continue;

            #always clear the data
            $bet_map = [];

            foreach ($current_bet_map as $key => $current_map) {
                if(!empty($bet_map['bet_amount'])){

                    #use result amount when bet amount are equal
                    if($bet_map['bet_amount'] == $current_map['bet_amount']){
                        $current_valid_bet_amount = abs($bet_map['result_amount']+$current_map['result_amount']);
                    } else {
                        $current_valid_bet_amount = abs($bet_map['bet_amount']-$current_map['bet_amount']);
                    }

                    if ($bet_map['bet_amount'] == 0) {
                        $current_valid_bet_amount = $current_map['bet_amount'];
                    }

                    #replace initialized the values
                    $current_total_bet_amount = $bet_map['bet_amount'] + $current_map['bet_amount'];
                    $current_result_amount = $bet_map['result_amount'] + $current_map['result_amount'];
                }else{

                    #for single row only
                    $bet_map = [
                        'bet_amount' => $current_map['bet_amount'],
                        'result_amount' => $current_map['result_amount'],
                    ];

                    #initialize the values
                    $current_total_bet_amount = $current_map['bet_amount'];
                    $current_result_amount = $current_map['result_amount'];
                    $current_valid_bet_amount  = $current_map['bet_amount'];
                }

                $list_of_valid_bet_amount[$opposite_bet_name]['result_amount'] = $current_result_amount;
                $list_of_valid_bet_amount[$opposite_bet_name]['valid_bet_amount'] = $current_valid_bet_amount;
                $list_of_valid_bet_amount[$opposite_bet_name]['total_bet_amount'] = $current_total_bet_amount;
            }
        }

        #initialize value
        $result_amount = 0;
        $total_bet_amount = 0;
        #finalize the result for valid bet amount
        if( ! empty($list_of_valid_bet_amount)){
            foreach ($list_of_valid_bet_amount as $key => $value) {
                $valid_bet_amount+=$value['valid_bet_amount'];
                $result_amount+=$value['result_amount'];
                $total_bet_amount+=$value['total_bet_amount'];
            }
        }

        #calculate data per non opposite bets
        if( ! empty($bet_type_map[$current_game_type]['not_opposite_bet'])){
            foreach ($bet_type_map[$current_game_type]['not_opposite_bet'] as $key => $value) {
                $valid_bet_amount+=$value['bet_amount'];
                $result_amount+=$value['result_amount'];
                $total_bet_amount+=$value['bet_amount'];
            }
        }
        #end

        $finalized_result = array($result_amount, $valid_bet_amount, $total_bet_amount);

        return $finalized_result;

    }

    public function isPlayerExist($playerName) {

        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName
        );

        $data = ["playerName" => $gameUsername];

        $this->CI->utils->debug_log('===== VR isPlayerExist data: ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('===== VR isPlayerExist params: ' . json_encode($params) . ' =====');

        $request_token = hash ("sha256", $this->api_salt . http_build_query($params));
        $params['request_token'] = $request_token;

        return $this->callApi(self::API_isPlayerExist, $params, $context);

    //     $playerId=$this->getPlayerIdFromUsername($userName);
    //     $userName = $this->getGameUsernameByPlayerUsername($userName);

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForIsPlayerExist',
    //         'playerName' => $userName,
    //         'playerId'=>$playerId,
    //     );

    //     $data = ["playerName" => $userName];

    //     $this->CI->utils->debug_log('===== VR isPlayerExist data: ' . json_encode($data) . ' =====');

    //     $json_data = json_encode($data);
    //     $json_encrypt_data = $this->apiEncode($json_data);

    //     $params = array(
    //         'version' => self::VR_VERSION,
    //         'id' => $this->vr_merchant_id,
    //         'data' => $json_encrypt_data
    //     );

    //     $this->CI->utils->debug_log('===== VR isPlayerExist params: ' . json_encode($params) . ' =====');

    //     // $request_token = hash ("sha256", $this->api_salt . http_build_query($params));
    //     // $params['request_token'] = $request_token;

    //     return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params) {

        $playerName = $this->getVariableFromContext($params, 'playerName');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $resultArr = json_decode($this->apiDecode($resultText), true);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');

        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername, self::API_isPlayerExist);
        $this->CI->utils->debug_log('================================= VR processResultForIsPlayerExist result: ' . json_encode($resultArr) . ' =====');

        $result=['response_result_id'=>$responseResultId];

        if (!empty($success) ) {
            // $result['balance'] = @floatval($resultArr['balance']);
            $result['exists'] = isset($resultArr['balance']) && $resultArr['balance']>=0;
            if($result['exists']){

                $this->CI->utils->debug_log('============================================= VR processResultForIsPlayerExist: ' . $resultArr['balance'] . ' =====');

                if ($playerId = $this->getPlayerIdInGameProviderAuth($gameUsername)) {

                    $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
                    $this->CI->utils->debug_log('============================================= VR processResultForIsPlayerExist [playerIdasd: ' . $playerId . ', playerName: ' . $playerName . ', balance: ' . $resultArr['balance'] . ']');
                } else {
                    $this->CI->utils->debug_log('===== VR processResultForIsPlayerExist cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
                }

            }
        }else{
            $result['exists'] = null;
        }

        return array($success, $result);

        // $playerName = $this->getVariableFromContext($params, 'playerName');
        // $playerId = $this->getVariableFromContext($params, 'playerId');
        // $responseResultId = $this->getResponseResultIdFromParams($params);
        // $resultText = $this->getResultTextFromParams($params);
        // $statusCode = $this->getStatusCodeFromParams($params);
        // $resultArr = json_decode($this->apiDecode($resultText), true);
        // $success = false;
        // $result = array();

        // $this->CI->utils->debug_log('===== VR isPlayerExist result: ' . json_encode($resultArr) . ' =====');

        // if ($this->processResultBoolean($responseResultId, $resultArr, $playerName, self::API_isPlayerExist)) {
        //     // if ($playerId = $this->getPlayerIdInGameProviderAuth($playerName)) {
        //     // if (! $this->getPlayerIdInGameProviderAuth($playerName)) {
        //         //sync game provider auth
        //     // }

        //     $success = true;
        //     $result['exists'] = $statusCode!=404;
        //     $this->CI->utils->debug_log('===== VR isPlayerExist [ playerName: ' . $playerName . ', exists: ' . $result['exists'] . ']');

        //     $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
        //     // } else {
        //     //     $success = false;
        //     //     $result['exists'] = false;
        //     //     $this->CI->utils->debug_log('===== VR isPlayerExist cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
        //     // }
        // } else {
        //     $success = false;

        //     if ($statusCode==404 || @$resultArr['errorCode'] == self::ERROR_PLAYER_NOT_FOUND) {
        //         $result['exists'] = false;
        //         $success = true;
        //     } else {
        //         //means unknown
        //         $result['exists'] = null;
        //     }
        // }

        // return array($success, $result);
    }

    public function apiEncode($data) {
        //Encrypt data
        $encData = openssl_encrypt($data, $this->encrpytion, $this->merchant_key, OPENSSL_RAW_DATA);

        return base64_encode($encData);
    }

    public function apiDecode($base64_data) {
        $data = base64_decode($base64_data);

        //Decrypt data
        $plain_data = openssl_decrypt($data, $this->encrpytion, $this->merchant_key, OPENSSL_RAW_DATA);

        //Remove US-ASCII control character
        $plain_data = trim($plain_data, "\x00..\x1F");

        return $plain_data;
    }

    public function gameTimeToServerTime($dateTimeStr) {
        if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
            $dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
        }

        $modify = $this->getGameTimeToServerTime();
        $dateTimeStr =  $this->utils->modifyDateTime($dateTimeStr, $modify);
        return date('Y-m-d H:i:s', strtotime($dateTimeStr));
    }

    public function serverTimeToGameTime($dateTimeStr) {
        if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
            $dateTimeStr = $dateTimeStr->format('Y-m-d\TH:i:s\Z');
        }

        $modify = $this->getServerTimeToGameTime();
        $dateTimeStr =  $this->utils->modifyDateTime($dateTimeStr, $modify);
        return date('Y-m-d\TH:i:s\Z', strtotime($dateTimeStr));
    }

    /**
     * overview : get game time to server time
     *
     * @return string
     */
    public function getGameTimeToServerTime() {
        return $this->getSystemInfo('gameTimeToServerTime');
    }

    /**
     * overview : get server time to game time
     *
     * @return string
     */
    public function getServerTimeToGameTime() {
        return $this->getSystemInfo('serverTimeToGameTime');
    }

    // Below Are Not Yet Use
    // function isPlayerExist($userName){
    //     $playerName = $this->getGameUsernameByPlayerUsername($userName);

    //     $this->login($userName);
    //     $ip_address = $this->CI->input->ip_address();

    //     $context = array(
    //         'callback_obj' => $this,
    //         'callback_method' => 'processResultForIsPlayerExist',
    //         'playerName' => $userName
    //     );

    //     $params = array(
    //         'agent_id' => $this->agent_id,
    //         'username' => $this->operator_username,
    //         'session_ip' => $ip_address,
    //         'session_token' => $this->session_token,
    //     );

    //     $this->CI->utils->debug_log('===== VR isPlayerExist ' . json_encode($params) . ' =====');

    //     $request_token = hash ("sha256", $this->api_salt . http_build_query($params));
    //     $params['request_token'] = $request_token;

    //     return $this->callApi(self::API_isPlayerExist, $params, $context);
    // }

    // function processResultForIsPlayerExist($params){
    //     $responseResultId = $this->getResponseResultIdFromParams($params);
    //     $resultText = $this->getResultTextFromParams($params);
    //     $resultJsonArr = json_decode($resultText, TRUE);
    //     $playerName = $this->getVariableFromContext($params, 'playerName');
    //     $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

    //     $this->CI->utils->debug_log('===== VR isPlayerExist result ' . json_encode($resultText) . ' =====');

    //     if ($success) {
    //         $result = array('exists' => true);
    //     }else{
    //         $result = array('exists' => false); # Player not found
    //     }

    //     return array($success, $result);
    // }

    function changePassword($playerName, $oldPassword, $newPassword) {
        $playerName = $this->getGameUsernameByPlayerUsername($playerName);
        return array("success" => true);
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


    public function processBetDetails($game_record = null){

        $is_multibet = false;
        $bet_detail = array();
        $bet_type_unique_id = array();
        $filtered_bet_detail = array('bet_details' => null);
        $current_bet_placed = $game_record['number'];
        $current_result_amount = $game_record['win_amount'] - $game_record['bet_amount'];

        if( ! empty($game_record['extra'])){
            $extra = json_decode($game_record['extra'],true);
            $is_multibet = true;

            if(is_array($extra)){
                foreach ($extra as $key => $data) {
                    if ($data['state'] == self::CANCELLED_BET) continue;
                    #for in extra data
                    $bet_detail['bet_details'][$key] = [
                        "odds" => $data['odds'],
                        "bet_amount" =>  $data['bet_amount'],
                        "bet_placed" =>  $data['place_of_bet'],
                        "won_side" => $game_record['winningNumber'],
                    ];

                    list($tmp_win_amount,$tmp_result_amount) = $this->calculate_result_amount($game_record,$data['winloss_amount'],$data);
                    $bet_detail['bet_details'][$key]["win_amount"] = $tmp_win_amount;
                    $bet_detail['bet_details'][$key]["winloss_amount"] = $tmp_result_amount;

                    $bet_type_unique_id[$data['place_of_bet']] = isset($bet_type_unique_id[$data['place_of_bet']]) ? $bet_type_unique_id[$data['place_of_bet']] .', <br>'. $key:$key;
                }
            }

            #for current data
            $bet_detail['bet_details'][$game_record['external_uniqueid']] = [
                "odds" => $game_record['odds'],
                "bet_amount" =>  $game_record['bet_amount'],
                "bet_placed" => $current_bet_placed,
                "won_side" => $game_record['winningNumber'],
            ];

            list($tmp_win_amount,$tmp_result_amount) = $this->calculate_result_amount($game_record,$current_result_amount);
            $bet_detail['bet_details'][$game_record['external_uniqueid']]["win_amount"] = $tmp_win_amount;
            $bet_detail['bet_details'][$game_record['external_uniqueid']]["winloss_amount"] = $tmp_result_amount;

            $bet_type_unique_id[$current_bet_placed] = isset($bet_type_unique_id[$current_bet_placed]) ? $bet_type_unique_id[$current_bet_placed] .', <br>'. $game_record['external_uniqueid']: $game_record['external_uniqueid'];

        }else{
            $bet_detail['bet_details'][$game_record['external_uniqueid']] = [
                "odds" => $game_record['odds'],
                "bet_amount" =>  $game_record['bet_amount'],
                "bet_placed" => $current_bet_placed,
                "won_side" => $game_record['winningNumber'],
                "winloss_amount" => $current_result_amount,
            ];

            list($tmp_win_amount,$tmp_result_amount) = $this->calculate_result_amount($game_record,$current_result_amount);
            $bet_detail['bet_details'][$game_record['external_uniqueid']]["win_amount"] = $tmp_win_amount;
            $bet_detail['bet_details'][$game_record['external_uniqueid']]["winloss_amount"] = $tmp_result_amount;

        }

        $bet_details = array(
            "bet_details" => json_encode($bet_detail),
            "multibet" => $is_multibet,
        );

        return $bet_details;

    }

    function calculate_result_amount($game_record,$current_result_amount,$extra = null){

        $tmp_win_amount = ( ! empty($extra)) ? $extra['win_amount']:$game_record['win_amount'];
        $tmp_bet_amount = ( ! empty($extra)) ? $extra['bet_amount']:$game_record['bet_amount'];

        if ($game_record['response_result_id']) {
            $win_amount = ($tmp_win_amount > 0 && $current_result_amount > 0) ? $tmp_win_amount-$tmp_bet_amount:0;
            $winloss_amount = $current_result_amount;
        }else{
            $win_amount = ($tmp_win_amount > 0 && $current_result_amount > 0) ? $tmp_win_amount:0;
            $winloss_amount = $tmp_win_amount;
        }
        return [$win_amount,$winloss_amount];
    }

    function queryTransaction($transactionId, $extra) {
        $this->CI->utils->debug_log('===== VR queryTransaction extra: ' . json_encode($extra));

        $playerName   = $extra['playerName'];
        $playerId     = $extra['playerId'];
        list($startTime, $endTime) = $this->getFromToTransferTime($extra['transfer_time']);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'playerId' => $playerId,
            'external_transaction_id' => $transactionId,
        );

        $data = [
            "startTime" => $startTime,
            "endTime" => $endTime,
            "serialNumber" => $transactionId,
            // "playerName" => $gameUsername,
            "recordPage" => 0,
            "recordCountPerPage" => 1,
        ];

        $this->CI->utils->debug_log('===== VR queryTransaction data: ' . json_encode($data) . ' =====');

        $json_data = json_encode($data);
        $json_encrypt_data = $this->apiEncode($json_data);

        $params = array(
            'version' => self::VR_VERSION,
            'id' => $this->vr_merchant_id,
            'data' => $json_encrypt_data
        );

        $this->CI->utils->debug_log('===== VR QueryTransaction params: ' . json_encode($params) . ' =====');

        $request_token = hash ("sha256", $this->api_salt . http_build_query($params));
        $params['request_token'] = $request_token;

        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params) {
        $resultText                 = $this->getResultTextFromParams($params);
        $resultArr                  = $this->CI->utils->decodeJson($this->apiDecode($resultText));
        $responseResultId           = $this->getResponseResultIdFromParams($params);
        $external_transaction_id    = $this->getVariableFromContext($params, 'external_transaction_id');
        $success                    = $this->processResultBoolean($responseResultId,$resultArr, null, self::API_queryTransaction);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'reason_id'=>self::REASON_UNKNOWN,
        );

        $this->CI->utils->debug_log('vr query transaction result', $resultArr);

        if ($success) {
            # check if transfer exist
            if (!empty($resultArr['totalRecords'])) {
                $transferStatus = array_column($resultArr['records'], 'state')[0];
                # if transfer is approve
                if (empty($transferStatus)) {
                    $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
                } else {
                    # Other transfer status
                    $result['reason_id'] = $this->getTransferErrorReasonCode($transferStatus);
                    $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
                }
            } else {
                # means transaction not found.
                $result['reason_id']=self::REASON_INVALID_TRANSACTION_ID;
                $result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
            }
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

    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        if($apiName==self::API_isPlayerExist){
            return $errCode || (intval($statusCode, 10) >= 400 && intval($statusCode, 10)!=404);
        }
        // $statusCode = intval($statusCode, 10);
        return $errCode || intval($statusCode, 10) >= 400;
    }

    private function getGameDescriptionInfo($row, $unknownGame) {
        $game_description_id = null;
        $game_name = $row['channelName'];
        $external_game_id = $row['channel_id'];
        $extra = array('game_code' => $external_game_id);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

        return $this->processUnknownGame(
            $game_description_id, $game_type_id,
            $game_name, $game_type, $external_game_id, $extra,
            $unknownGame);
    }

    public function getDecodedResultText($resultText, $apiName, $params, $statusCode){
        if($statusCode<400 && !empty($resultText)){
            return $this->apiDecode($resultText);
        }

        return null;
    }

    private function getFromToTransferTime($transfer_time){
        $fromTime=(new DateTime($transfer_time))->modify('-10 minutes')->format('Y-m-d\TH:i:s');
        $toTime=(new DateTime($transfer_time))->modify('+10 minutes')->format('Y-m-d\TH:i:s');
        return [$fromTime, $toTime];
    }

    public function syncGameLogsThroughExcel($date){
        set_time_limit(0);
        $this->CI->load->model(['vr_game_logs']);

        require_once dirname(__FILE__) . '/../../../admin/application/libraries/phpexcel/PHPExcel.php';
        $this->CI->utils->debug_log('===== VR syncGameLogsThroughExcel Start: ' . $date);

        if (count(explode("-", $date)) > 3 ) return "Invalid Date";

        // $this->CI->load->library('phpexcel');
        $game_logs_path = $this->getSystemInfo('vr_game_records_path');

        $current_date = new DateTime($date);
        $year_months = $current_date->format("Ym");

        $month_day = null;
        if (count(explode("-", $date)) > 2 ) {
            $month_day = $current_date->format("md");
        }

        $directory = $game_logs_path . "/" . $year_months;
        $vr_game_logs_excel = array_diff(scandir($directory), array('..', '.'));

        $header = [
            'A' => 'serialNumber',
            'B' => 'merchantCode',
            'C' => 'playerName',
            'D' => 'betTypeName',
            'E' => 'channelName',
            'F' => 'issueNumber',
            'G' => 'position',
            'H' => '位置',
            'I' => 'unit',
            'J' => 'multiple',
            'K' => 'count',
            'L' => 'number',
            'M' => 'cost',
            'N' => 'state',
            'O' => 'merchantWinPrize',
            'P' => 'playerWinPrize',
            'Q' => 'lossPrize',
            'R' => 'merchantPrize',
            'S' => 'playerPrize',
            'T' => 'odds',
            'U' => 'player_odds',
            'V' => 'merchant_odds',
            'W' => 'createTime',
            'X' => '操作状态',
            'Y' => '追号单号',
            'Z' => 'updateTime',
            'AA' => 'winningNumber',
            'AB' => 'note',
        ];

        $count = [];
        $excel_data = [];
        $this->CI->utils->debug_log("file_list =========", $vr_game_logs_excel);
        foreach ($vr_game_logs_excel as $file_name) {

            #sync specific day only
            $file = explode(".", $file_name);

            if ( ! empty($month_day)) {
                if ($file[0] != $month_day) continue;
            }
            $this->CI->utils->debug_log("file_name =========", $file_name);

            $obj_php_excel = PHPExcel_IOFactory::load($directory . "/" . $file_name);
            $cell_collection = $obj_php_excel->getActiveSheet()->getCellCollection();
            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                ini_set('memory_limit', '-1');
                $column = $obj_php_excel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $obj_php_excel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $obj_php_excel->getActiveSheet()->getCell($cell)->getValue();

                //The header will/should be in row 1 only. of course, this can be modified to suit your need.
                if ($row == 1) continue;

                #remove merchant prefix of game username
                if ($column == "C") {
                    $game_username = explode("@", $data_value);
                    if (empty($game_username[1])) continue;
                    $data_value = $game_username[1];
                }
                // $data_value = $column == "C" ? explode("@", $data_value)[1]:$data_value;

                #modify dates
                if (in_array($column, ["W","Z"])) {
                    if ($file[1] != "csv") {
                        $data_value = $this->vrModifyDate($data_value,false);
                    }else{
                        $data_value = $this->vrModifyDate($data_value,true);
                    }
                }

                #set bet status
                if ($column=="N") {
                    if($data_value == self::STATE_CANCELED_CHINESE){
                        $data_value = Vr_game_logs::STATE_CANCELED;
                    }
                    if($data_value == self::STATE_NOT_WINNING_CHINESE){
                        $data_value = Vr_game_logs::STATE_NOT_WINNING;
                    }
                    if($data_value == self::STATE_WIN_CHINESE){
                        $data_value = Vr_game_logs::STATE_WIN;
                    }
                }

                #add space to channel name
                if ($column=="E") {
                    $data_value = str_replace("VR", "VR ", $data_value);
                }

                $excel_data[$row][$header[$column]] = $data_value;
            }

            $availableRows = $this->CI->vr_game_logs->getAvailableRows($excel_data);

            if(!empty($availableRows)){

                foreach ($availableRows as &$rows) {
                    $row['playerPrize']=$row['playerPrize']+$row['cost'];
                }
            }
            // echo "<pre>";print_r($availableRows);
            $count[$file_name] = $this->processVrGameLogs($availableRows,null,true);
            $this->CI->utils->debug_log("count =========", $count[$file_name]);
        }
        return $count;
    }

    private function vrModifyDate($date,$csv_file){
        $date = empty($csv_file) ? date('m/d/Y H:i:s',PHPExcel_Shared_Date::ExcelToPHP($date)): $date;
        $date = new DateTime($date);
        $date->modify($this->excel_default_modify_hours);

        if ($this->getServerTimeToGameTime() && empty($csv_file)) {
            $date->modify($this->getServerTimeToGameTime());
        }

        return $date = $date->format('m/d/Y H:i:s');
    }

}
