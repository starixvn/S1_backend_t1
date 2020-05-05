<?php

require_once dirname(__FILE__).'/abstract_game_api.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Create player
 * * Checks if the player is exist
 * * Deposit to Game
 * * Withdraw from Game
 * * Prepare Transfer Credit
 * * Confirm transfer credit
 * * Check Player Balance
 * * Check Forward Game
 * * Get Game Record Path
 * * Get Sub Directories
 * * Check if the directory is empty
 * * Computes Total Betting Amount
 * * Check Game Records
 * * Check Player Daily Balance
 * * Check Transaction
 * * Block/Unblock
 * * Get Attribute value from XML
 * * Get Game Time to Server Time
 * * Get Server Time to Game Time
 * * Throws Callback
 * * Generate XML
 * * Generate URL
 * * All the function bellow is still not working
 * * Getting the constant variable of the AG
 * * Login/Logout
 * * Update Player Info
 * * Check Login Status
 * * Check Player info
 * *
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 *
 *
 * @see Redirect redirect to game page
 *
 * @category Game_platform
 *
 * @version 1.8.10
 *
 * @copyright 2013-2016 TripleOneTech
 */
/**
 * AG includes some other game providers.
 *
 * like BBIN, HG, PT, SHABA
 *
 * platform type: 'AGIN', 'AG', 'DSP', 'AGHH', 'IPM', 'BBIN', 'MG', 'SABAH', 'HG', 'PT',
 * 'OG', 'UGS', 'HUNTER', 'AGTEX', 'HB', 'XTD', 'PNG', 'NYX', 'ENDO', 'BG',
 * 'XIN', 'YOPLAY', 'TTG'
 */
abstract class Game_api_common_ag extends Abstract_game_api
{

    public $currency;
    public $cagent;
    public $actype; # TODO: actype is a per-account property, shouldn't be an API property
    public $ignore_type_array;
    public $egame_platformtypes;
    // public $is_update_original_row;
    public $transfer_retry_times;
    public $currentProtocol;
    public $disable_mh5_for_gametype;

    const DEFAULT_ODD_TYPE='A';
    const DEFAULT_CURRENCY = 'CNY';
    const REAL_ACCOUNT = 1;
    const DEMO_ACCOUNT = 0;
    const API_prepareTransferCredit = 'prepareTransferCredit';
    const API_transferCreditConfirm = 'transferCreditConfirm';
    const STATUS_CODE = array(
        'Successful' => 0,
        'Failure, account error' => 1,
        'Failure, password error' => 2,
        'Failure, system internal error' => -2,
        'Failure, not official invoke, reject' => -3,
        );

    const AG_PLATFORM_TYPE = 'AGIN';
    const AG_FISHING_PLATFORM_TYPE = 'HUNTER';
    const AG_SPORTS_PLATFORM_TYPE = 'SBTA';
    const AGBBIN_PLATFORM_TYPE = 'BBIN';
    const AGSHABA_PLATFORM_TYPE = 'SABAH';
    const AG_SLOTS_PLATFORM_TYPE = 'XIN';
    const CALL_PREPARE_TRANSFER_SUCCESS=1;
    const RED_POCKET = 'RED_POCKET';
    const HUNTER_GAME_CODE= 'hunter';
    const BUY_FISH_RECORD = 4;
    const BUY_FISH_PAYOUT = 12;

    const DATA_TYPE_EBR = "EBR";

    const AGIN_TRANSFER_RULE_ARR=[AGIN_API, AG_API];

    public function __construct()
    {
        parent::__construct();

        $this->currency = $this->getSystemInfo('currency');
        // if (empty($this->currency)) {
        //     $this->currency = self::DEFAULT_CURRENCY;
        // }

        if(!is_null($this->getCurrencyCode())){
            $this->currency = $this->getCurrencyCode();
        }elseif(empty($this->currency)){
            $this->currency = self::DEFAULT_CURRENCY;
        }

        $this->cagent = $this->getSystemInfo('CAGENT_AG');

        $this->actype = $this->getSystemInfo('actype', self::REAL_ACCOUNT);

        $this->oddtype = $this->getSystemInfo('oddtype');
        if ($this->oddtype == '') {
            $this->oddtype = self::DEFAULT_ODD_TYPE;
        }
        $this->disabled_oddtype=$this->getSystemInfo('disabled_oddtype', false);

        $this->md5key_ag = $this->getSystemInfo('MD5KEY_AG');

        $this->ignore_type_array = $this->getSystemInfo('ignore_type_array', ['TR', 'HTR', 'GR', 'TEXGR', 'LGR']);

        $this->allowed_transfer_type= $this->getSystemInfo('allowed_transfer_type', [self::RED_POCKET]);

        $this->egame_platformtypes = $this->getSystemInfo('egame_platformtypes', ['XIN', 'NYX', 'BG', 'PT', 'TTG']);

        $this->transfer_retry_times=$this->getSystemInfo('transfer_retry_times', self::DEFAULT_TRANSFER_RETRY_TIMES);

        $this->disable_mh5_for_gametype = $this->getSystemInfo('disable_mh5_for_gametype');

        $this->round_transfer_amount =$this->getSystemInfo('round_transfer_amount', true); // use default decimal_places_count

        $this->merge_game_logs = $this->getSystemInfo('merge_game_logs', false);

        $this->oddtype_for_prefix = $this->getSystemInfo('oddtype_for_prefix');
        // if(empty($this->transfer_retry_times)){
        //     $this->transfer_retry_times=self::DEFAULT_TRANSFER_RETRY_TIMES;
        // }

        #checking $protocol in HTTP or HTTPS
        if ($this->CI->utils->isHttps()) {
             $this->currentProtocol = "https";
        } else {
             $this->currentProtocol = "http";
        }
        $this->demo_suffix = $this->getSystemInfo('demo_username_suffix');
        $this->pid = $this->getSystemInfo('PID');
        //format: { 'G': ['username1', 'usernam2', 'username3'], 'C':['user1', 'user2'] }
        $this->special_odd_type_list=$this->getSystemInfo('special_odd_type_list');

    }

    // protected function convertStatus($status){

    //     if($status=='0'){ //approved
    //         return self::COMMON_TRANSACTION_STATUS_APPROVED;
    //     }elseif($status=='2'){ //declined
    //         return self::COMMON_TRANSACTION_STATUS_DECLINED;
    //     }else{
    //         return self::COMMON_TRANSACTION_STATUS_UNKNOWN;
    //     }

    // }

    protected function customHttpCall($ch, $params) {
        curl_setopt($ch, CURLOPT_USERAGENT, 'WEB_LIB_GI_'.$this->cagent);
    }

    public function processResultBoolean($responseResultId, $resultXml, $errArr, $info_must_be_0=false)
    {
        $success = true;

        $info = $this->getAttrValueFromXml($resultXml, 'info');
        if (in_array($info, $errArr)) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('AG got error', $responseResultId, 'result', $resultXml);
            $success = false;
        }elseif($info_must_be_0){
            $success= $info=='0';
        }

        return $success;
    }

    public function checkPassword($playerId, $password, $platformCode) {
        if($platformCode == AGBBIN_API) {
            $passLength = strlen($password);
            $requiredLength = 12;
            if(!(preg_match("/^[a-zA-Z0-9]+$/", $password)) || ($passLength > $requiredLength)){
                $password = uniqid();
                $password = substr($password,0,$requiredLength);
                $this->updatePasswordForPlayer($playerId, $password);
            }
        }
        return $password;
    }

    public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null)
    {
        $this->CI->utils->debug_log('=====================CREATE PLAYER=====================================');
        parent::createPlayer($playerName, $playerId, $password, $email, $extra);

        $password = $this->getPasswordString($playerName);
        $password = $this->checkPassword($playerId, $password, $this->getPlatformCode());
        $this->CI->utils->debug_log('playerName', $playerName, 'password', $password);

        //$playerId = $this->getPlayerIdInPlayer($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForCreatePlayer',
            'playerId' => $playerId,
            'gameUsername' => $gameUsername,
            );



        // $oddtype = $this->getSystemInfo('oddtype');
        // if (empty($oddtype)) {
        //     $oddtype = 'A';
        // }

        if (isset($extra['is_demo_flag']) && @$extra['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        $isDemo = false;
        if(!empty($extra)){
            if (array_key_exists('game_mode', $extra)) {
                $isDemo = $extra['game_mode']=='demo' || $extra['game_mode']=='trial';
            }
        }

        $params = array(
            'cagent' => $this->cagent,
            'loginname' => ($isDemo) ? $gameUsername .= $this->demo_suffix : $gameUsername,
            'method' => 'lg',
            'actype' => ($isDemo) ? self::DEMO_ACCOUNT : $this->actype,
            'password' => $password,
            'oddtype' => $this->oddtype,
            'cur' => $this->currency,
            );

        $this->CI->utils->debug_log('game_api_common_ag create player', $params);

        return $this->callApi(self::API_createPlayer, $params, $context);
    }

    public function processResultForCreatePlayer($params)
    {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        $playerId = $this->getVariableFromContext($params, 'playerId');

        $success = $this->processResultBoolean($responseResultId, $resultXml, array('key_error', 'network_error', 'account_add_fail', 'error'), true);

        return array($success, null);
    }

    public function isPlayerExist($playerName,$extra = null)
    {
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $password = $this->getPasswordString($gameUsername);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerId' => $playerId,
            'gameUsername' => $gameUsername,
            'game_platform' => isset($extra['game_platform']) ? $extra['game_platform']:false,
            );

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        $isDemo = false;
        if(!empty($extra)){
            if (array_key_exists('game_mode', $extra)) {
                $isDemo = $extra['game_mode']=='demo' || $extra['game_mode']=='trial';
            }
        }

        $params = array(
            'cagent' => $this->cagent,
            'loginname' => ($isDemo) ? $gameUsername .= $this->demo_suffix : $gameUsername,
            'method' => 'gb',
            'actype' => ($isDemo) ? self::DEMO_ACCOUNT : $this->actype,
            'password' => $password,
            'cur' => $this->currency,
            );

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params)
    {
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $game_platform = $this->getVariableFromContext($params, 'game_platform');

        $success = false;
        $result = array('exists' => true);
        if ($this->processResultBoolean($responseResultId, $resultXml, array('key_error', 'network_error', 'account_not_exist', 'error'))) {
            $success = true;
        } elseif ($resultXml['info'] == 'account_not_exist') {
            $success = true;
            $result['exists'] = false;
        } elseif ($resultXml['info'] == 'error' && (strstr($resultXml['msg'], 'error:60001,')||strstr($resultXml['msg'], 'exist:22002,'))) {
            $success = true;
            $result['exists'] = false;
        } else {
            $success = false;
            $result['exists'] = null;
        }

        if(isset($game_platform) && $game_platform == AGSHABA_API){
            $resultArr = (array) json_decode(json_encode($resultXml));

            if ($resultArr['@attributes']->info == 'error' && strstr($resultArr['@attributes']->msg,'Account not exist')!==FALSE ){
                $success = true;
                $result['exists'] = false;
            }
        }


        return array($success, $result);
    }

    public function queryTransaction($external_transaction_id, $extra)
    {
        $currency = isset($extra['currency']) ? $extra['currency'] : $this->currency;
        $playerId=$extra['playerId'];

        $createAt = $extra['transfer_time'];

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryTransaction',
            'playerId'=>$playerId,
            'createat' => $createAt,
            'external_transaction_id' => $external_transaction_id,
        );

        $params = array(
            'cagent' => $this->cagent,
            'billno' => $external_transaction_id,
            'method' => 'qos',
            'actype' => $this->actype,
            'cur' => $currency,
        );
        $this->CI->utils->debug_log('=========extra', $extra);
        return $this->callApi(self::API_queryTransaction, $params, $context);
    }

    public function processResultForQueryTransaction($params)
    {
        $dateRange = date('Y-m-d H:i:s',(strtotime ( '-1 day' , strtotime ( $this->utils->getDatetimeNow()) ) ));
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        // $resultText = $this->getResultTextFromParams($params);
        $this->CI->utils->debug_log('=========resultXml', $resultXml);
        // $success = $this->processResultBoolean($responseResultId, $resultXml, array('1','key_error', 'network_error', 'account_not_exist', 'error'), true);

        $result = array('response_result_id' => $responseResultId, 'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN);
        $result['external_transaction_id'] = $this->getVariableFromContext($params, 'external_transaction_id');
        $createAt = $this->getVariableFromContext($params, 'createat');
        $this->CI->utils->debug_log('=========createAt', $createAt);
        $info = $this->getAttrValueFromXml($resultXml, 'info');

        //means call api successful
        $success=!empty($resultXml) && $info!==null;

        $result['error_code']=$info;
        $result['error_message']=$this->getAttrValueFromXml($resultXml, 'msg');

        switch ($info) {
            case '0':
                $result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
                break;
            case '1':
                $result['status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                if ($createAt < $dateRange) {
                    $result['status']=self::COMMON_TRANSACTION_STATUS_UNKNOWN;
                }
                break;
            case '2':
            case 'error':
                $result['status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                break;
            case 'network_error':
            case 'key_error':
                //means call api failed
                $success=false;
                break;
        }

        return array($success, $result);
    }

    public function depositToGame($playerName, $amount, $transfer_secure_id = null)
    {

        $type='IN';
        return $this->transfer($type, $playerName, $amount, $transfer_secure_id);

    }

    public function withdrawFromGame($playerName, $amount, $transfer_secure_id = null)
    {

        $type='OUT';
        return $this->transfer($type, $playerName, $amount, $transfer_secure_id);

    }

    public function transfer($type, $playerName, $amount, $transfer_secure_id = null){
        $this->CI->load->model(array('wallet_model'));
        $usernameWithoutPrefix = $playerName;

        $password = $this->getPasswordString($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

        if(empty($transfer_secure_id)){
            $transfer_secure_id=random_string('numeric', 13);
        }

        $external_transaction_id=$this->cagent.$transfer_secure_id;
        //BBIN , 19 number
        //MG , 8 number
        if($this->getPlatformCode()==AGBBIN_API){
            $external_transaction_id=random_string('numeric', 19);
        }
        //generate external_transaction_id

        //always query balance first
        // $result=$this->queryPlayerBalance($usernameWithoutPrefix);
        // $result['external_transaction_id']=$billno;

        // $this->utils->debug_log('============= query player balance for '.$type.' billno', $billno, 'result', $result);

        // if(!$result['success']){
        //     return $result;
        // }

        //response_result_id, external_transaction_id, transfer_status, reason_id
        $result = $this->prepareTransferCredit($gameUsername, $password, $amount, $external_transaction_id, $type);
        $this->utils->debug_log('============= prepareTransferCredit '.$type.' external_transaction_id:'. $external_transaction_id, 'result', $result);
        //no longer continue , no matter what error
        if ($result['success']) {
            $result = $this->transferCreditConfirm($gameUsername, $password, $amount, $external_transaction_id, $type);
            $this->CI->utils->debug_log('============= AG transfer '.$type.' result ######### ', $result);
        }else{
            //return failed , don't try
            return $result;
        }

        //only if transferCreditConfirm is failed
        if(!$result['success'] || $result['error_code']=='network_error'){

            //try query order status
            $qryRlt=$this->queryTransaction($external_transaction_id, null);
            $this->CI->utils->debug_log('============= get error when '.$type.' try queryTransaction', $gameUsername, $amount, $external_transaction_id, $qryRlt);

            // $cnt=1;
            //3 times
            // while(!$result['success'] && @$result['status']== && $cnt<=$this->transfer_retry_times){
            // if(!$qryRlt['success'] || ($qryRlt['success'] && $qryRlt['error_code']=='network_error')){

            //     //try again
            //     $qryRlt=$this->queryTransaction($billno, null);

            // }

                // $cnt++;

                // $this->CI->utils->debug_log('============= get error when '.$type.' try queryTransaction', $playerName, $amount, $billno, $result);
            // }
            //only for transfer to sub
            if($type == 'IN' && @$qryRlt['error_code']=='network_error'){

                $this->CI->utils->debug_log('============= convert success to true if still network error when '.$type, $playerName, $amount, $external_transaction_id, $result);

                //convert to success if still network error, just assume it successful
                $result['success']=true;
                $result['reason_id']=self::REASON_GAME_PROVIDER_NETWORK_ERROR;
                $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_UNKNOWN;
            }
        }

        //assume it successful
        if ($result['success']) {
            //update
            $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            if ($playerId) {
                // $playerBalance = $this->queryPlayerBalance($usernameWithoutPrefix);
                // $this->CI->utils->debug_log('============= AG QUERY_PLAYER_BALANCE '.$type.' ######### ', $playerBalance);

                $afterBalance = null;
                // if ($playerBalance && $playerBalance['success']) {
                //     $afterBalance = $playerBalance['balance'];
                //     $this->CI->utils->debug_log('============= AG AFTER BALANCE FROM API '.$type.' ######### ', $afterBalance);
                // } else {
                //     //IF GET PLAYER BALANCE FAILED
                //     $rlt = $this->CI->wallet_model->getSubWalletBy($playerId, $this->getPlatformCode());
                //     $afterBalance = $rlt->totalBalanceAmount;
                //     $this->CI->utils->debug_log('============= AG AFTER BALANCE FROM WALLET '.$type.' ######### ', $afterBalance);
                // }
                $responseResultId = $result['response_result_id'];
                $result['didnot_insert_game_logs']=true;
                //history
                // $this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
                //     $type == 'IN' ? $this->transTypeMainWalletToSubWallet() : $this->transTypeSubWalletToMainWallet());

            } else {
                $this->CI->utils->debug_log('error', '=============== cannot get player id from '.$gameUsername.' getPlayerIdInGameProviderAuth');
            }
        }

        return $result;
    }

    public function prepareTransferCredit($gameUsername, $password, $amount, $external_transaction_id, $transaction)
    {
        $this->CI->load->helper('string');
        // $randStr = random_string('alnum', 16);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForPrepareTransferCredit',
            'gameUsername' => $gameUsername,
            'external_transaction_id' => $external_transaction_id,
            //no need to guess successful
            // 'transfer_type'=> $transaction=='IN' ? self::API_depositToGame : self::API_withdrawFromGame,
        );

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        $params = array(
            'cagent' => $this->cagent,
            'method' => 'tc',
            'loginname' => $gameUsername,
            'billno' => $external_transaction_id,
            'type' => $transaction,
            'credit' => $amount,
            'actype' => $this->actype,
            'password' => $password,
            'cur' => $this->currency,
        );

        $this->CI->utils->debug_log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>trans-params>>> ', $params);

        return $this->callApi(self::API_prepareTransferCredit, $params, $context);
    }

    public function processResultForPrepareTransferCredit($params)
    {
        $this->CI->utils->debug_log('####### AG PREPARE TRANSFER ######### ', $params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        $external_transaction_id=$this->getVariableFromContext($params, 'external_transaction_id');

        $success = $this->processResultBoolean($responseResultId, $resultXml, array('1','2','key_error', 'network_error', 'account_not_exist', 'error'), true);

        $result = array('response_result_id' => $responseResultId, 'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN, 'reason_id'=>self::REASON_UNKNOWN);
        $info = $this->getAttrValueFromXml($resultXml, 'info');
        $msg = $this->getAttrValueFromXml($resultXml, 'msg');
        $result['error_code']=$info;

        $success=$result['error_code']==='0';

        //check error code
        if(!$success){
            switch ($result['error_code']) {
                case '1':
                    $result['reason_id']=self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case '2':
                    $result['reason_id']=self::REASON_INVALID_TRANSFER_AMOUNT;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'key_error':
                    $result['reason_id']=self::REASON_INVALID_KEY;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    $success=false;
                    break;
                case 'duplicate_transfer':
                    $result['reason_id']=self::REASON_DUPLICATE_TRANSFER;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'network_error':
                    $result['reason_id']=self::REASON_GAME_PROVIDER_NETWORK_ERROR;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'account_not_exist':
                    $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'error':
                    if(strpos($msg, 'not enough credit')!==FALSE){
                        //found
                        $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
                    }else{
                        $result['reason_id']=self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    }
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
            }
        }else{
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }

        return array($success, $result);
    }

    public function transferCreditConfirm($gameUsername, $password, $amount, $external_transaction_id, $transaction)
    {
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForTransferCreditConfirm',
            'gameUsername' => $gameUsername,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_type'=> $transaction=='IN' ? self::API_depositToGame : self::API_withdrawFromGame,
            //mock testing
            // 'is_timeout_mock' => $this->getSystemInfo('is_timeout_mock', false),
            //for this api
            // 'enabled_guess_success_for_curl_errno_on_this_api' => $this->enabled_guess_success_for_curl_errno_on_this_api,
        );

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        //assume is success if timeout
        if($transaction=='IN'){
            $context['enabled_guess_success_for_curl_errno_on_this_api']= true; //$this->enabled_guess_success_for_curl_errno_on_this_api;
            // $context['is_timeout_mock']=$this->getSystemInfo('is_timeout_mock', false);
        }

        $params = array(
            'cagent' => $this->cagent,
            'method' => 'tcc',
            'loginname' => $gameUsername,
            'billno' => $external_transaction_id,
            'type' => $transaction,
            'credit' => $amount,
            'actype' => $this->actype,
            'flag' => self::CALL_PREPARE_TRANSFER_SUCCESS,
            'password' => $password,
            'cur' => $this->currency,
            );

        return $this->callApi(self::API_transferCreditConfirm, $params, $context);
    }

    public function processResultForTransferCreditConfirm($params)
    {

        $responseResultId = $this->getResponseResultIdFromParams($params);
        $external_transaction_id=$this->getVariableFromContext($params, 'external_transaction_id');
        $result = array('response_result_id' => $responseResultId, 'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN, 'reason_id'=>self::REASON_UNKNOWN);
        $success=false;
        //for timeout
        if(parent::processGuessSuccess($params, $success, $result)){
            return [$success, $result];
        }

        $resultXml = $this->getResultXmlFromParams($params);

        // $resultText = $this->getResultTextFromParams($params);
        $this->CI->utils->debug_log('=========resultXml', $resultXml);

        // $success = $this->processResultBoolean($responseResultId, $resultXml,
        //     array('1','2','key_error', 'network_error', 'account_not_exist', 'error'), true);
        $info = $this->getAttrValueFromXml($resultXml, 'info');
        $msg = $this->getAttrValueFromXml($resultXml, 'msg');
        $result['error_code']=$info;

        $success=$result['error_code']==='0';

        // $external_transaction_id=$this->getVariableFromContext($params, 'external_transaction_id');
        // $result['external_transaction_id'] = $external_transaction_id;
        //check error code
        if(!$success){
            switch ($result['error_code']) {
                case '1':
                    $result['reason_id']=self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case '2':
                    $result['reason_id']=self::REASON_INVALID_TRANSFER_AMOUNT;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'key_error':
                    $result['reason_id']=self::REASON_INVALID_KEY;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'duplicate_transfer':
                    $result['reason_id']=self::REASON_DUPLICATE_TRANSFER;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'network_error':
                    $result['reason_id']=self::REASON_GAME_PROVIDER_NETWORK_ERROR;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'account_not_exist':
                    $result['reason_id']=self::REASON_NOT_FOUND_PLAYER;
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
                case 'error':
                    if(strpos($msg, 'not enough credit')!==FALSE){
                        //found
                        $result['reason_id']=self::REASON_NO_ENOUGH_BALANCE;
                    }else{
                        $result['reason_id']=self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
                    }
                    $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_DECLINED;
                    break;
            }
        }else{
            $result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
        }

        return array($success, $result);
    }

    public function queryPlayerBalance($playerName)
    {
        $playerId = $this->getPlayerIdInPlayer($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $password = $this->getPasswordString($gameUsername);

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerId' => $playerId,
            'playerName' => $playerName,
            );

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        } else {
            $this->actype = self::REAL_ACCOUNT;
        }

        $params = array(
            'cagent' => $this->cagent,
            'loginname' => $gameUsername,
            'method' => 'gb',
            'actype' => $this->actype,
            'password' => $password,
            'cur' => $this->currency,
            );
        $this->CI->utils->debug_log('##########  QUERY PLAYER PARAMS         #####################', $params);

        return $this->callApi(self::API_queryPlayerBalance, $params, $context);
    }

    public function processResultForQueryPlayerBalance($params)
    {
        $this->CI->utils->debug_log('##########  QUERY PLAYER BALANCE         #####################', $params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultXml = $this->getResultXmlFromParams($params);
        $playerId = $this->getVariableFromContext($params, 'playerId');
        $playerName = $this->getVariableFromContext($params, 'playerName');

        $success = $this->processResultBoolean($responseResultId, $resultXml, array('key_error', 'network_error', 'account_not_exist', 'error'));
        $this->CI->utils->debug_log($resultXml);

        $result = array();

        $info = $this->getAttrValueFromXml($resultXml, 'info'); //['info'];

        $this->CI->utils->debug_log('info', $info);
        if ($success && isset($info) && $info !== null) {
            $result['balance'] = floatval($info);

            $this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName', $playerName, 'balance', $result['balance']);
            if ($playerId) {
                //should update database
                // $this->updatePlayerSubwalletBalance($playerId, $result["balance"]);
            } else {
                log_message('error', 'cannot get player id from '.$playerName.' getPlayerIdInGameProviderAuth');
            }
        } else {
            $success = false;
        }

        return array($success, $result);
    }

    public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
            case 1:
            case 'en-us':
                $lang = '3'; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = '1'; // chinese
                break;
            case 3:
            case 'id-id':
                $lang = '11'; // indo
                break;
            case 4:
            case 'vi-vn':
                $lang = '8'; // Vietnamese
                break;
            case 5:
            case 'ko-kr':
                $lang = '5'; // korean
                break;
            default:
                $lang = '3'; // default as english
                break;
        }
        return $lang;
    }

    public function queryForwardGame($playerName, $extra){
        $this->CI->utils->debug_log('common_ag queryForwardGame extra ========> ', $extra);

        $password = $this->getPasswordString($playerName);
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $sid = $this->cagent.$this->getRandomSequence();
        $game_type = $extra['game_type'];
        $lang = $this->getLauncherLanguage($extra['language']);
        $currency = $this->currency;
        $is_mobile = isset($extra['is_mobile']) ? $extra['is_mobile'] : false;

        if(array_key_exists('app', $extra) &&  $extra['app']){
            $this->CI->load->library(array('salt'));
            $des_password = $this->CI->salt->encrypt($password, $this->getSystemInfo('DESKEY_AG'));
            // $md5Key = md5($des_password);
            $app_url = "aggaming://login?";
            $app_data = array(
                "u" =>$gameUsername,
                "p" =>$des_password,
                "pid" => $this->pid
            );
            // $app_param = http_build_query($app_data);
            // $url =  $app_url.$app_param;
            $url =  $app_url . "u=" . $gameUsername . "&p=" . $des_password . "&pid=" . $this->pid;
            // print_r($url);exit();
            $result=['url' => $url,'data'=>$app_data, 'is_mobile'=> true, 'success'=>true];
            return $result;
        }

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        $isDemo = false;
        if(!empty($extra)){
            if (array_key_exists('game_mode', $extra)) {
                $isDemo = $extra['game_mode']=='demo' || $extra['game_mode']=='trial';
            }
        }

        $dynamicDomain=$this->getCurrentProtocol().'://'.$this->getCurrentDomain();

        # redirect to clietn url not gamegateway
        if (isset($extra['extra']['t1_ag_dm']) && !empty($extra['extra']['t1_ag_dm'])) {
            $dynamicDomain = $extra['extra']['t1_ag_dm'];
        }

        $params = array(
            'cagent' => $this->cagent,
            'loginname' => ($isDemo) ? $gameUsername .= $this->demo_suffix : $gameUsername,
            'actype' => ($isDemo) ? self::DEMO_ACCOUNT : $this->actype,
            'password' => $password,
            'dm' => $dynamicDomain,
            'sid' => $sid,
            'lang' => $lang,
            'gameType' => $game_type,
            'cur' => $currency,
        );

        $this->CI->utils->debug_log('common_ag queryForwardGame params ========> ', $params, $this->special_odd_type_list);

        $this->CI->utils->debug_log('special_odd_type_list', $this->special_odd_type_list);

        if(!$this->disabled_oddtype){
            if ($this->oddtype_for_prefix && preg_match("#^(?P<prefix>" . implode('|', array_keys($this->oddtype_for_prefix)) . ")#", $gameUsername, $matches)) {
                if (isset($this->oddtype_for_prefix[$matches['prefix']])) {
                    $oddtype = $this->oddtype_for_prefix[$matches['prefix']];
                    $params['oddtype']=$oddtype;
                }
            } else {
                $params['oddtype']=$this->oddtype;
            }
        }

        $this->CI->utils->debug_log("queryForwardGame is_mobile", $is_mobile);

        if($is_mobile){
            if(empty($this->disable_mh5_for_gametype) || !in_array($game_type, $this->disable_mh5_for_gametype)){
                $params['mh5']='y';
            }
        }

        $this->CI->load->library(array('salt'));
        $params = $this->CI->salt->encrypt($this->convertArrayToParamString($params), $this->getSystemInfo('DESKEY_AG'));
        $md5Key = md5($params.$this->md5key_ag);

        // $mobile_launch_game_url = $this->getSystemInfo('mobile_launch_game_url');
        // if($this->currentProtocol=='https' && !empty($this->getSystemInfo('mobile_launch_game_url_https'))){
        //     $mobile_launch_game_url = $this->getSystemInfo('mobile_launch_game_url_https') ;
        // }

        // if ($this->CI->utils->is_mobile() && !empty($mobile_launch_game_url)) {
        //     //url
        //     return rtrim($mobile_launch_game_url,'/').'forwardGame.do?params='.$params.'&key='.$md5Key.'&mh5=y';

        // }
        //use http and https
        // $launcher_url = $this->getSystemInfo('GCIURL_AG') ;
        $launcher_url = $this->getSystemInfo('GCIURL_HTTP');
        if(empty($launcher_url)){
            $launcher_url = $this->getSystemInfo('GCIURL_AG');
        }

        if($this->currentProtocol=='https' && !empty($this->getSystemInfo('GCIURL_HTTPS'))){
            $launcher_url = $this->getSystemInfo('GCIURL_HTTPS') ;
        }

        $param_string = 'forwardGame.do?params='.$params.'&key='.$md5Key;

        $url= rtrim($launcher_url,'/').'/'.$param_string;

        $success=!empty($url);
        $result=['url'=>$url, 'is_mobile'=>$is_mobile, 'success'=>$success];

        return $result;
    }

    public function syncOriginalGameLogs($token)
    {

        $gameLogDirectoryAG = $this->getGameRecordPath();

        if(!is_array($gameLogDirectoryAG)){
          $gameLogDirectoryAG = (array)$gameLogDirectoryAG;
        }

        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $syncId = parent::getValueFromSyncInfo($token, 'syncId');

        $dateTimeFrom=new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $dateTimeTo=new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));

        $dateTimeFrom->modify($this->getDatetimeAdjust());

        $this->CI->utils->debug_log('[syncOriginalGameLogs] after adjust ag dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

        foreach ($gameLogDirectoryAG as $logDirectoryAG) {

            $startDate = new DateTime($dateTimeFrom->format('Y-m-d H:i:s'));
            $endDate = new DateTime($dateTimeTo->format('Y-m-d H:i:s'));
            $day_diff = $endDate->diff($startDate)->format("%a");

            if ($day_diff > 0) {
                for ($i = 0; $i < $day_diff; $i++) {
                    $this->utils->debug_log('########  AG GAME DATES INPUT #################', $startDate , $endDate);
                    if ($i == 0) {
                        $directory = $logDirectoryAG . $startDate->format('Ymd');
                        $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
                    }
                    $startDate->modify('+1 day');
                    $directory = $logDirectoryAG . $startDate->format('Ymd');

                    $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);

                }
            } else {
                $directory = $logDirectoryAG . $startDate->format('Ymd');
                $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);

                $startDate->modify('+1 day');
                $directory = $logDirectoryAG . $startDate->format('Ymd');

                $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
            }
        }

        return array('success' => true);
    }

    public function syncOriginalGameResult($token){
        $game_platform = $this->getPlatformCode();

        if ($game_platform != AGIN_API) {
            return;
        }

        $resultDirectoryAG = $this->getGameResultRecordPath();

        if(!is_array($resultDirectoryAG)){
          $resultDirectoryAG = (array)$resultDirectoryAG;
        }
        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $syncId = parent::getValueFromSyncInfo($token, 'syncId');
        $dateTimeFrom=new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $dateTimeTo=new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));
        $dateTimeFrom->modify($this->getDatetimeAdjust());
        foreach ($resultDirectoryAG as $resultLogDirectoryAG) {
            $startDate = new DateTime($dateTimeFrom->format('Y-m-d H:i:s'));
            $endDate = new DateTime($dateTimeTo->format('Y-m-d H:i:s'));
            $day_diff = $endDate->diff($startDate)->format("%a");

            if ($day_diff > 0) {
                for ($i = 0; $i < $day_diff; $i++) {
                    $this->utils->debug_log('########  AG GAME RESULT DATES INPUT #################', $startDate , $endDate);
                    if ($i == 0) {
                        $directory = $resultLogDirectoryAG . $startDate->format('Ymd');
                        $this->retrieveResultXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo);
                    }
                    $startDate->modify('+1 day');
                    $directory = $resultLogDirectoryAG . $startDate->format('Ymd');

                    $this->retrieveResultXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo);

                }
            } else {
                $directory = $resultLogDirectoryAG . $startDate->format('Ymd');
                $this->retrieveResultXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo);

                //try tomorrow again
                $startDate->modify('+1 day');
                $directory = $resultLogDirectoryAG . $startDate->format('Ymd');

                $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
            }
        }
    }

    public function retrieveResultXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo){
        $this->CI->utils->debug_log('RESULT XML CURRENT DIRECTORY------',$directory);
        if (is_dir($directory)) {
            $agGameResultLogsXml = array_diff(scandir($directory), array('..', '.'));

            //from filename , to filename
            $fromFile = $dateTimeFrom->format('YmdH');
            $toFile = $dateTimeTo->format('YmdH');

            foreach ($agGameResultLogsXml as $xml) {
                //should ignore by time
                $xmlname = substr($xml, 0, 10); //YmdH
                // $this->CI->utils->debug_log('process xmlname on '.$directory, $xml, $xmlname, $fromFile, $toFile);
                if ($xmlname >= $fromFile && $xmlname <= $toFile) {
                    $filepath = $directory.'/'.$xml;

                    $this->CI->utils->debug_log($this->getPlatformCode().' ag process', $filepath);

                    $this->extractResultXMLRecord($filepath);
                } else {
                    //ignore
                }
            }
        }
    }

    public function extractResultXMLRecord($xml){
        $this->CI->load->model(array('agin_game_logs_result'));
        $source = $xml;

        $xmlData = '<rows>'.file_get_contents($source, true).'</rows>';
        $reportData = simplexml_load_string($xmlData);
        if(!empty($reportData)){
            foreach ($reportData as $key => $value) {
                $result = array();
                $result['data_type']    = isset($value['dataType']) ? (string)$value['dataType'] : NULL;
                $result['game_code']    = isset($value['gmcode']) ? (string)$value['gmcode'] : NULL;
                $result['table_code']   = isset($value['tablecode']) ? (string)$value['tablecode'] : NULL;
                $result['begin_time']   = isset($value['begintime']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['begintime']))) : NULL;
                $result['close_time']   = isset($value['closetime']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['closetime']))) : NULL;
                $result['dealer']       = isset($value['dealer']) ? (string)$value['dealer'] : NULL;
                $result['shoe_code']    = isset($value['shoecode']) ? (int)$value['shoecode'] : NULL;
                $result['flag']         = isset($value['flag']) ? (int)$value['flag'] : NULL;
                $result['banker_point'] = isset($value['bankerPoint']) ? (int)$value['bankerPoint'] : NULL;
                $result['player_point'] = isset($value['playerPoint']) ? (int)$value['playerPoint'] : NULL;
                $result['card_num']     = isset($value['cardnum']) ? (int)$value['cardnum'] : NULL;
                $result['pair']         = isset($value['pair']) ? (int)$value['pair'] : NULL;
                $result['game_type']    = isset($value['gametype']) ? (string)$value['gametype'] : NULL;
                $result['dragon_point'] = isset($value['dragonpoint']) ? (int)$value['dragonpoint'] : NULL;
                $result['tiger_point']  = isset($value['tigerpoint']) ? (int)$value['tigerpoint'] : NULL;
                $result['card_list']    = isset($value['cardlist']) ? (string)$value['cardlist'] : NULL;
                $result['vid']          = isset($value['vid']) ? (string)$value['vid'] : NULL;
                $result['platform_type'] = isset($value['platformtype']) ? (string)$value['platformtype'] : NULL;
                $isExists = $this->CI->agin_game_logs_result->isRowIdAlreadyExists($result['game_code']);
                if ($isExists) {
                    $result['updated_at'] = date('Y-m-d H:i:s');
                    $this->CI->agin_game_logs_result->updateGameResultLogs($result);
                } else {
                    $result['created_at'] = date('Y-m-d H:i:s');
                    $this->CI->agin_game_logs_result->insertGameResultLogs($result);
                }
            }
        }
        return array("success" => true);
    }

    public function getGameResultRecordPath(){
        return $this->getSystemInfo('ag_game_result_records_path');
    }

    public function getCurrencyCode() {
        return $this->currency;
    }

    public function getStringValueFromXml($xml, $key)
    {
        $value = (string) $xml[$key];
        if (empty($value) || $value == 'null') {
            $value = '';
        }

        return $value;
    }

    public function getGameRecordPath()
    {
        return $this->getSystemInfo('ag_game_records_path');
    }

    public function getIngorePlatformTypes()
    {
        return [];
    }

    public function getSubDirectories($directory)
    {
        $glob = glob($directory.'/*');
        if ($glob === false) {
            return array();
        }

        return array_filter($glob, function ($dir) {
            if (!$this->isDirectoryEmpty($dir) && is_dir($dir)) {
                return is_dir($dir);
            }
        });
    }

    public function isDirectoryEmpty($dir)
    {
        if (!is_readable($dir)) {
            return null;
        }

        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                return false;
            }
        }

        return true;
    }

    public function retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId)
    {
         $this->CI->utils->debug_log('XML CURRENT DIRECTORY------',$directory);

        if (is_dir($directory)) {
            $agGameLogsXml = array_diff(scandir($directory), array('..', '.'));

            //from filename , to filename
            $fromFile = $dateTimeFrom->format('YmdH');
            $toFile = $dateTimeTo->format('YmdH');

            //from filename , to filename
            $fromFileYmd = $dateTimeFrom->format('Ymd');
            $toFileYmd = $dateTimeTo->format('Ymd');

            # GET DIRECTORY NAME
            $directoryArr = explode("/",$directory);
            $dirName = $directoryArr[count($directoryArr)-2]; // directory type name
            # if lostAndfound directory
            if(in_array("lostAndfound",$directoryArr)){
                $dirName = $directoryArr[count($directoryArr)-3];
            }

            foreach ($agGameLogsXml as $xml) {
                //should ignore by time
                $xmlname = substr($xml, 0, 10); //YmdH
                $xmlYmd = substr($xml, 0, 8); //Ymd

                if (($xmlname >= $fromFile && $xmlname <= $toFile)||($xmlYmd!=$fromFileYmd && $xmlYmd!=$toFileYmd)||$dirName== self::AG_SPORTS_PLATFORM_TYPE) {
                    $filepath = $directory.'/'.$xml;
                    if(file_exists($filepath)){
                        $responseResultId = $this->saveResponseResultForFile(true, self::API_syncGameRecords, $this->getPlatformCode(), $filepath, array('sync_id' => $syncId));

                        $this->CI->utils->debug_log($this->getPlatformCode().' ag process', $filepath);

                        $this->extractXMLRecord($filepath, $playerName, $responseResultId);
                    }else{
                        $this->CI->utils->debug_log('not found '.$filepath);
                    }
                } else {
                    //ignore
                }
            }
        }
    }

    public function extractXMLRecord($xml, $playerName = null, $responseResultId = null)
    {

        // $this->CI->load->model('agin_game_logs');

        $source = $xml;

        $xmlData = '<rows>'.file_get_contents($source, true).'</rows>';
        $reportData = simplexml_load_string($xmlData);
        $cnt = 0;
        $dataResult = array();
        $uniqueIds = array();
        $ingorePlatformTypes = $this->getIngorePlatformTypes();

        foreach ($reportData as $key => $value) {
            $result = array();

            $dataType=(string)$value['dataType'];
            $transferType=(string)$value['transferType'];
            $platformType=(string) $value['platformType'];
            $rowPlayerName=(string) $value['rowPlayerName'];

            if (in_array($dataType, $this->ignore_type_array)) {
                if($dataType == 'TR' && in_array($transferType, $this->allowed_transfer_type)){
                    //allowed transfer type
                }else{
                    continue; //ignore
                }
            }
            if (!empty($playerName) && $rowPlayerName) {
                continue; //ignore
            }
            if (!empty($ingorePlatformTypes) && in_array($platformType, $ingorePlatformTypes)) {
                continue;
            }

            ++$cnt;

            if ($platformType == self::AG_FISHING_PLATFORM_TYPE) {

                //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
                //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
                //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

                // $result['playerId'] = $this->getPlayerIdInGameProviderAuth($value['playerName']);
                //debug save type to playerId
                // $result['playerId'] = (string) $value['type'];
                $result['datatype'] = (string) $value['dataType'];
                $result['logs_ID'] = (string) $value['ID'];
                $result['tradeNo'] = (string) $value['tradeNo']; //new
                $result['sceneId'] = (string) $value['sceneId']; //new
                $result['playtype'] = (string) $value['type'];
                $result['SceneStartTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['SceneStartTime'])));
                $result['SceneEndTime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['SceneEndTime'])));
                $result['Roomid'] = (string) $value['Roomid'];
                $result['Roombet'] = (string) $value['Roombet'];
                $result['Cost'] = (string) $value['Cost'];
                $result['Earn'] = (string) $value['Earn'];
                $result['Jackpotcomm'] = (string) $value['Jackpotcomm'];
                $result['transferAmount'] = (string) $value['transferAmount'];
                $result['previousAmount'] = (string) $value['previousAmount'];
                $result['currentAmount'] = (string) $value['currentAmount'];
                $result['currency'] = (string) $value['currency'];
                $result['exchangeRate'] = (string) $value['exchangeRate'];
                $result['loginip'] = (string) $value['IP'];
                $result['creationtime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['creationTime'])));
                //shared fields
                $result['flag'] = (string) $value['flag'];
                $result['uniqueid'] = (string) $value['ID'];
                $result['external_uniqueid'] = (string) $value['ID'];
                $result['response_result_id'] = $responseResultId;
                $result['billno']=(string) $value['ID'];
                $result['gamecode'] = (string) $value['ID']; //it's unique id
                $result['gametype'] = self::HUNTER_GAME_CODE;
                $result['platformtype'] = (string) $value['platformType'];
                $result['playername'] = (string) $value['playerName'];
                $result['beforecredit'] = (string) $value['previousAmount'];
                $result['betamount'] = (float) $value['Cost'];
                $result['validbetamount'] = (float) $value['Cost'];
                $result['netamount'] = (float)$value['transferAmount'];
                $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['SceneStartTime'])));
                $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['SceneEndTime'])));

                $result['transferType'] = (int) $value['transferType'];
                $result['fishIdStart'] = (int) $value['fishIdStart'];
                $result['fishIdEnd'] = (int)$value['fishIdEnd'];

                if( ! empty($value['creationTime']) && empty($value['SceneStartTime'])){
                    $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['creationTime'])));
                    $result['remark'] = (string) $value['remark'];
                    $result['recalcutime'] = null;
                    $result['SceneStartTime'] = null;
                    $result['SceneEndTime'] = null;
                }

                switch ($result['playtype']) {
                    case 1:
                        $result['jackpotsettlement'] = $result['Jackpotcomm'];
                        break;
                    case 2:
                    case 7:
                        $result['jackpotsettlement'] = -$result['transferAmount'];
                        break;
                    default:
                        $result['jackpotsettlement'] = 0;
                        break;
                }

                $result['updated_at'] = $this->CI->utils->getNowForMysql();

                //FILTER DUPLICATE ROWS IN XML
                if (!in_array($result['uniqueid'], $uniqueIds)) {
                    array_push($dataResult, $result);
                    array_push($uniqueIds, $result['uniqueid']);
                }

            // }elseif($value['platformType']==self::AG_PLATFORM_TYPE || in_array($value['platformType'], $this->egame_platformtypes)){
            } else {
                
                $result['datatype'] = (string) $value['dataType'];
                $result['playername'] = (string) $value['playerName'];
                $result['agentcode'] = (string) $value['agentCode'];


                $result['billno'] = (string) $value['billNo'];
                $result['uniqueid'] = (string) $value['billNo'];
                $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['betTime'])));
                $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['recalcuTime'])));
                $result['creationtime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['betTime'])));
                $result['betamount'] = (float) $value['betAmount'];
                $result['beforecredit'] = (string) $value['beforeCredit'];
                $result['netamount'] = (float) $value['netAmount'];
                $result['gametype'] = (string) $value['gameType'];
                $result['gamecode'] = (string) $value['gameCode'];

                $result['playtype'] = (string) $value['playType'];

                $transferType=(string)$value['transferType'];
                if($transferType == self::RED_POCKET ){
                    $result['billno'] = (string) $value['ID'];
                    $result['uniqueid'] = (string) $value['ID'];
                    $result['bettime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['creationTime'])));
                    $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['creationTime'])));
                    $result['creationtime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['creationTime'])));
                    $result['transferAmount'] = (float) $value['transferAmount'];
                    $result['previousAmount'] = (float) $value['previousAmount'];
                    $result['currentAmount'] = (float) $value['currentAmount'];
                    $result['beforecredit'] = (float) $value['previousAmount'];
                    $result['netamount'] = (float) $value['transferAmount'];
                    $result['betamount'] =  0;
                    $result['gametype'] = $transferType;
                    $result['gamecode'] = $transferType;
                }

                $result['subbillno'] = (string) $value['subbillno'];
                $result['validbetamount'] = (float) $value['validBetAmount'];
                $result['flag'] = (string) $value['flag'];
                $result['currency'] = (string) $value['currency'];
                $result['tablecode'] = $this->getStringValueFromXml($value, 'tableCode');
                $result['loginip'] = (string) $value['loginIP'];
                $result['platformtype'] = $platformType;
                $result['remark'] = $this->getStringValueFromXml($value, 'remark');
                $result['round'] = (string) $value['round'];
                $result['result'] = (string) $value['result'];
                $result['response_result_id'] = $responseResultId;
                $result['external_uniqueid'] = $result['uniqueid'];

                $result['updated_at'] = $this->CI->utils->getNowForMysql();

                //for AGSHABA
                $remarkJsonString = isset($value['remark']) ? (string) $value['remark'] : null;
                if (!empty($remarkJsonString)) {
                    //overwrite sport type to game type
                    $remark = $this->CI->utils->decodeJson($remarkJsonString);
                    if (isset($remark['after_amount'])) {
                        $result['after_amount'] = (float) $remark['after_amount'];
                    }
                    if (isset($remark['sport_type'])) {
                        $result['gametype'] = (string) $remark['sport_type'];
                    }
                }

                if ($value['flag'] != '0' ||  ((string)$value['transferType']  && (string)$value['transferType'] == self::RED_POCKET )) {
                    //FILTER DUPLICATE ROWS IN XML
                    if (!in_array($result['uniqueid'], $uniqueIds)) {
                        array_push($dataResult, $result);
                        array_push($uniqueIds, $result['uniqueid']);
                    }
                }

                # AGSPORTS include unsettled gamelogs
                if($value['platformType'] == self::AG_SPORTS_PLATFORM_TYPE){
                    if (strtotime($value['recalcuTime']) == false){
                       $result['recalcutime'] = $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($value['betTime'])));
                    }
                    $result['cancelReason'] =(string)$value['cancel_reason'];
                    //FILTER DUPLICATE ROWS IN XML
                    // echo "<pre>";print_r($uniqueIds);exit;
                    // if (!in_array($result['uniqueid'], $uniqueIds)) {
                        array_push($dataResult, $result);
                        // array_push($uniqueIds, $result['uniqueid']);
                    // }
                }

            }
        }

        $this->CI->utils->debug_log('dataResults', count($dataResult), 'isUpdateOriginalRow', $this->isUpdateOriginalRow());

        if (count($dataResult) > 0) {
            if ($this->isUpdateOriginalRow()) {
                # get data array for data merging
                if ($platformType == self::AG_PLATFORM_TYPE) {
                    $this->syncGameLogsToDB($dataResult);
                } else {
                    foreach ($dataResult as $dataRow) {
                       $this->syncGameLogsToDB($dataRow);
                    }
                }
            } else {
                $availableResult = $this->getAvailableRows($dataResult); //$this->CI->agin_game_logs->getAvailableRows($dataResult);
                if (count($availableResult) > 0) {
                    $this->CI->utils->debug_log('insert ag game logs', count($availableResult));

                    $ids = $this->insertBatchToGameLogs($availableResult); //$this->CI->agin_game_logs->insertBatchToAGINGameLogs($availableResult);
                    $this->syncMergeToGameLogsByIds($ids);
                }
                // return $cnt;
            }
        }

        $this->CI->utils->debug_log('count game record', $cnt, 'filepath', $xml);

        return $cnt;
    }

    public function isUpdateOriginalRow()
    {
        return $this->is_update_original_row;
    }
    abstract public function syncGameLogsToDB($dataResult);
    abstract public function getAvailableRows($dataResult);
    abstract public function insertBatchToGameLogs($availableResult);

    public function syncLostAndFound($token)
    {
        // $gameLogLostAndFoundDirectoryAG = $this->getGameRecordPath();
        // $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        // $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        // $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        // $syncId = parent::getValueFromSyncInfo($token, 'syncId');

        // $dateTimeFrom->modify('-1 days');
        // if (is_array($gameLogLostAndFoundDirectoryAG)) {
        //     foreach ($gameLogLostAndFoundDirectoryAG as $logDirectoryAG) {
        //         $this->retrieveXMLFromLocal($logDirectoryAG.'/lostAndfound/', $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
        //     }
        // } else {
        //     $this->retrieveXMLFromLocal($gameLogLostAndFoundDirectoryAG, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
        // }

        // return array('success' => true);

        $gameLogDirectoryAG = $this->getGameRecordPath();

        if(!is_array($gameLogDirectoryAG)){
          $gameLogDirectoryAG = (array)$gameLogDirectoryAG;
        }

        $playerName = $this->getValueFromSyncInfo($token, 'playerName');
        $dateTimeFrom = clone $this->getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone $this->getValueFromSyncInfo($token, 'dateTimeTo');
        $syncId = parent::getValueFromSyncInfo($token, 'syncId');

        $dateTimeFrom=new DateTime($this->serverTimeToGameTime($dateTimeFrom->format('Y-m-d H:i:s')));
        $dateTimeTo=new DateTime($this->serverTimeToGameTime($dateTimeTo->format('Y-m-d H:i:s')));

        $dateTimeFrom->modify($this->getDatetimeAdjust());

        $this->CI->utils->debug_log('[syncLostAndFound] after adjust ag dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

        foreach ($gameLogDirectoryAG as $logDirectoryAG) {

            $startDate = new DateTime($dateTimeFrom->format('Y-m-d H:i:s'));
            $endDate = new DateTime($dateTimeTo->format('Y-m-d H:i:s'));
            $day_diff = $endDate->diff($startDate)->format("%a");

            if ($day_diff > 0) {
                for ($i = 0; $i < $day_diff; $i++) {
                    $this->utils->debug_log('########  AG GAME DATES INPUT #################', $startDate , $endDate);
                    if ($i == 0) {
                        $directory = $logDirectoryAG.'lostAndfound/'.$startDate->format('Ymd');
                        $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
                    }
                    $startDate->modify('+1 day');
                    $directory = $logDirectoryAG.'lostAndfound/'.$startDate->format('Ymd');

                    $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);

                }
            } else {
                $directory = $logDirectoryAG .'lostAndfound/'.$startDate->format('Ymd');
                $this->retrieveXMLFromLocal($directory, $dateTimeFrom, $dateTimeTo, $playerName, $syncId);
            }
        }

        return array('success' => true);

    }

    public function getDefaultAdjustDatetimeMinutes()
    {
        return 60;
    }

    public function getRandomSequence()
    {
        return random_string('numeric', 16);
        // $seed = str_split('0123456789123456'); // and any other characters
        // shuffle($seed); // probably optional since array_is randomized; this may be redundant
        // $randomNum = '';
        // foreach (array_rand($seed, 16) as $k) {
        //     $randomNum .= $seed[$k];
        // }

        // return $randomNum;
    }

    public function queryGameRecords($dateFrom, $dateTo, $playerName = null)
    {
        $gameRecords = parent::getGameRecords($dateFrom, $dateTo, $playerName, $this->getPlatformCode());

        return array('success' => true, 'gameRecords' => $gameRecords);
    }

    public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null)
    {
        $daily_balance = parent::getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);

        $result = array();

        if ($daily_balance != null) {
            foreach ($daily_balance as $key => $value) {
                $result[$value['updated_at']] = $value['balance'];
            }
        }

        return array_merge(array('success' => true, 'balanceList' => $result));
    }

    public function blockPlayer($playerName)
    {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->blockUsernameInDB($gameUsername);

        return array('success' => true);
    }

    public function unblockPlayer($playerName)
    {
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->unblockUsernameInDB($gameUsername);

        return array('success' => true);
    }

    public function getAttrValueFromXml($resultXml, $attrName)
    {
        $info = null;
        if (!empty($resultXml)) {
            $result = $resultXml->xpath('/result');
            if (isset($result[0])) {
                $attr = $result[0]->attributes();
                if (!empty($attr)) {
                    foreach ($attr as $key => $value) {
                        if ($key == $attrName) {
                            $info = ''.$value;
                        }
                        $this->CI->utils->debug_log('key', $key, 'value', ''.$value);
                    }
                } else {
                    $this->CI->utils->debug_log('empty attr');
                }
            } else {
                $this->CI->utils->debug_log('empty /result');
            }
        } else {
            $this->CI->utils->debug_log('empty xml');
        }

        return $info;
    }

    public function callback($params, $platform = 'web', $method = "validatemember")
    {
        if($platform == 'web' || ($method == "login" && $platform == "mobile")){
            try {
                $params = new SimpleXMLElement($params);
                $params = json_encode($params);
                $params = json_decode($params, true);

                $this->CI->utils->debug_log('=============> AG callback params', $params);

                if (isset($params['@attributes']['action'])) {
                    switch ($params['@attributes']['action']) {

                        case 'userverf':
                        return $this->userverf($params, $platform, $method);

                        default:
                        throw new Exception('Unknown Action');

                    }
                } else {
                    throw new Exception('Unknown Action');
                }
            } catch (Exception $e) {
                $errdesc = $e->getMessage();
                $errcode = array_key_exists($errdesc, self::STATUS_CODE) ? self::STATUS_CODE[$errdesc] : self::STATUS_CODE['Failure, system internal error'];

                $this->CI->utils->error_log('callback_error', $errdesc);

                return $this->generateXml($errcode, $errdesc, $params);
            }
        } else {
            $loginname = filter_input(INPUT_GET,"loginname",FILTER_SANITIZE_STRING);
            $key = filter_input(INPUT_GET,"key",FILTER_SANITIZE_STRING);
            $gkey = md5($loginname."mobile#!@AG");

            $message= "Keys not match";
            if($gkey == $key) {
                $message= "User not exist.";
                $player_username = $this->getPlayerUsernameByGameUsername($loginname);
                $player_token = $this->getPlayerTokenByUsername($player_username);

                $next = $this->utils->getSystemUrl('player','/iframe_module/iframe_viewMiniCashier/'. $this->getPlatformCode() );
                $player_url = $this->utils->getSystemUrl('player','/iframe/auth/login_with_token/' . $player_token ."?next=".$next);
                if(!empty($player_username)){
                    redirect($player_url, 'refresh');
                }
            }
           $response = array('response' => array(
                'value' => array_merge(array(
                    array(
                            '_value' => $message,
                    ),
                )),
             ));

            return $this->utils->arrayToXml($response);
        }
    }

    public function userverf($params, $platform = null, $method = null)
    {
        $pcode = isset($params['element']['properties'][0]) ? $params['element']['properties'][0] : null;
        $gcode = isset($params['element']['properties'][1]) ? $params['element']['properties'][1] : null;
        $gameUsername = isset($params['element']['properties'][2]) ? $params['element']['properties'][2] : null;
        $obfuscated_password = isset($params['element']['properties'][3]) ? $params['element']['properties'][3] : null;
        $token = isset($params['element']['properties'][4]) ? $params['element']['properties'][4] : null;

        $username = null;
        $userid = null;
        $actype = null;
        $pwd = null;
        $gamelevel = null; // Optional parameter
        $vip = null; // Optional parameter

        $domain = $this->getSystemInfo('DM_AG');
        $ip = $this->getSystemInfo('DOWNLOAD_VERSION_IP');

        $this->CI->utils->debug_log('=============> userverf DM_AG', $domain);

        // $username = NULL;
        // $userid = NULL;
        // $actype = NULL;
        // $pwd = NULL;
        // $gamelevel = NULL; # Optional parameter
        // $vip = NULL; # Optional parameter

        // $domain = $this->getSystemInfo('DM_AG');
        // $ip = $this->CI->input->ip_address();
        // $gameUsername = $this->getGameUsernameByPlayerUsername($playerUsername);

        // VALIDATE TOKEN
        $md5Key = md5($pcode.$gcode.$gameUsername.$obfuscated_password.$this->md5key_ag);
        if ($token != $md5Key) {
            throw new Exception('Failure, not official invoke, reject');
        }

        // VALIDATE ACCOUNT
        $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
        //as username
        // $playerId=$this->getPlayerIdFromUsername($playerUsername);
        if (empty($playerId)) {
            throw new Exception('Failure, account error');
        }

        // VALIDATE PASSWORD
        $password = substr($obfuscated_password, 4, -6);
        // $passwordDB = $this->getPasswordFromPlayer($playerUsername);
        $this->CI->load->model('player_model');
        $passwordDB = $this->CI->player_model->getPasswordById($playerId);

        if ($password != $passwordDB) {
            // $this->getPasswordByGameUsername($gameUsername)) {
            throw new Exception('Failure, password error');
        }

        $player = $this->getPlayerInfo($playerId);
        $username = $player->username; //$playerUsername; # User's nickname, not more than 20 characters
        $userid = $gameUsername; // User's account, not more than 20 characters

        $isGameAccountDemoAccount = $this->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
        if ($isGameAccountDemoAccount['success'] && $isGameAccountDemoAccount['is_demo_flag']) {
            $this->actype = self::DEMO_ACCOUNT;
        }else{
            $this->actype = self::REAL_ACCOUNT;
        }

        $actype = $this->actype;
        //use game account password

        $pwd = $this->getPasswordString($username);
        // $pwd = //$password;

        // $player  = $this->getPlayerInfo($playerId);
        // $username    = $gameUsername; //$player->username; # User's nickname, not more than 20 characters
        // $userid      = $gameUsername; # User's account, not more than 20 characters
        // $actype      = self::REAL_ACCOUNT;
        // $pwd     = $password;

        $properties = array(
            array(
                'name_attr' => 'username',
                '_value' => $username,
                ),
            array(
                'name_attr' => 'userid',
                '_value' => $userid,
                ),
            array(
                'name_attr' => 'actype',
                '_value' => $actype,
                ),
            array(
                'name_attr' => 'pwd',
                '_value' => $pwd,
                ),
            array(
                'name_attr' => 'gamelevel',
                '_value' => $gamelevel,
                ),
            array(
                'name_attr' => 'vip',
                '_value' => $vip,
                ),
            array(
                'name_attr' => 'domain',
                '_value' => $domain,
                ),
            array(
                'name_attr' => 'ip',
                '_value' => $ip,
                ),
            );
        if($method == "login" && $platform == "mobile"){
            $this->utils->transferAllWallet($playerId, $player->username, $this->getPlatformCode());
            // $this->_transferAllWallet($playerId, $playerName, $game_platform_id);
        }
        return $this->generateXml(self::STATUS_CODE['Successful'], '', $params, $properties);
    }

    public function generateXml($status, $errdesc, $params, $properties = array())
    {
        $properties = !empty($properties) ? $properties : array();

        $element_id = isset($params['element']['@attributes']['id']) ? $params['element']['@attributes']['id'] : null;
        $pcode = isset($params['element']['properties'][0]) ? $params['element']['properties'][0] : null;
        $gcode = isset($params['element']['properties'][1]) ? $params['element']['properties'][1] : null;

        $response = array('response' => array(
            'action_attr' => 'userverf',
            'element' => array(
                'id_attr' => $element_id,
                'properties' => array_merge(array(
                    array(
                        'name_attr' => 'pcode',
                        '_value' => $pcode,
                        ),
                    array(
                        'name_attr' => 'gcode',
                        '_value' => $gcode,
                        ),
                    array(
                        'name_attr' => 'status',
                        '_value' => $status,
                        ),
                    array(
                        'name_attr' => 'errdesc',
                        '_value' => $errdesc,
                        ),
                    ), $properties),
                ),
            ));

        return $this->utils->arrayToXml($response);
    }

    public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null)
    {
        $this->CI->utils->debug_log($resultText);

        $resultXml = new SimpleXMLElement($resultText);
        if ($apiName == self::API_queryPlayerBalance) {
            // return $this->processResultForQueryPlayerBalance($apiName, $params, $responseResultId, $resultXml);
        } elseif ($apiName == self::API_createPlayer) {
            // return $this->processResultForCreatePlayer($apiName, $params, $responseResultId, $resultXml);
            // } else if ($apiName == self::API_depositToGame) {
            //  return $this->processResultForDepositToGame($apiName, $params, $responseResultId, $resultXml);
        } elseif ($apiName == self::API_prepareTransferCredit) {
            // return $this->processResultForPrepareTransferCredit($apiName, $params, $responseResultId, $resultXml);
        } elseif ($apiName == self::API_transferCreditConfirm) {
            // return $this->processResultForTransferCreditConfirm($apiName, $params, $responseResultId, $resultXml);
            // } else if ($apiName == self::API_queryForwardGame) {
            //  return $this->processResultForQueryForwardGame($apiName, $params, $responseResultId, $resultXml);
        } elseif ($apiName == self::API_queryTransaction) {
            return $this->processResultForQueryTransaction($apiName, $params, $responseResultId, $resultXml);
        } elseif ($apiName == self::API_isPlayerExist) {
            // return $this->processResultForIsPlayerExist($apiName, $params, $responseResultId, $resultXml);
        }
        // else if ($apiName == self::API_totalBettingAmount) {
        //  return $this->processResultForTotalBettingAmount($apiName, $params, $responseResultId, $resultXml);
        // }

        return array(false, null);
    }

    public function convertArrayToParamString($arr)
    {
        $paramString = '';
        if (!empty($arr)) {
            $rlt = array();
            foreach ($arr as $name => $value) {
                $rlt[] = $name.'='.$value;
            }
            $paramString = implode('/\\\\/', $rlt);
        }

        return $paramString;
    }

    public function generateUrl($apiName, $params)
    {
        $this->CI->load->library(array('salt'));

        $this->CI->utils->debug_log('apiName', $apiName, 'params', $params);

        $params = $this->CI->salt->encrypt($this->convertArrayToParamString($params), $this->getSystemInfo('DESKEY_AG'));
        $md5Key = md5($params.$this->md5key_ag);
        $url = $this->getSystemInfo('url');

        $url = rtrim($url, '/').'/doBusiness.do?params='.$params.'&key='.$md5Key;

        return $url;
    }

    public function getPlatformCode()
    {
        return $this->returnUnimplemented();
    }

    public function changePassword($playerName, $oldPassword, $newPassword)
    {
        return $this->returnUnimplemented();
    }

    public function login($playerName, $password = null)
    {
        return $this->returnUnimplemented();
    }

    public function logout($playerName, $password = null)
    {
        return $this->returnUnimplemented();
    }

    public function updatePlayerInfo($playerName, $infos)
    {
        return $this->returnUnimplemented();
    }

    public function checkLoginStatus($playerName)
    {
        return $this->returnUnimplemented();
    }

    public function queryPlayerInfo($playerName)
    {
        return $this->returnUnimplemented();
    }

    abstract public function getOriginalGameLogsByIds($ids);
    abstract public function getOriginalGameLogsByDate($startDate, $endDate);

    public function syncMergeToGameLogsByIds($ids)
    {
        $this->CI->load->model(array('game_logs'));

        $result = $this->getOriginalGameLogsByIds($ids); // $this->CI->agin_game_logs->getAGINGameLogStatisticsByIds($ids);
        if ($result) {
            $this->mergeResultGameLogs($result);
        }
    }

    public function syncMergeToGameLogs($token)
    {
        $this->CI->load->model(array('game_logs'));

        $dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
        $dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
        //$dateTimeFrom->modify("-1 hours");

        $dateTimeFrom->modify($this->getDatetimeAdjust());

        $this->CI->utils->debug_log('[syncMergeToGameLogs_ag] after adjust', 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);
        $rlt = array('success' => true);
        //observer the date format
        $startDate = $dateTimeFrom->format('Y-m-d H:i:s');
        $endDate = $dateTimeTo->format('Y-m-d H:i:s');
        // $result = $this->getOriginalGameLogsByDate($startDate, $endDate);

        //$this->CI->agin_game_logs->getAGINGameLogStatistics($startDate,$endDate);
        $result = $this->getOriginalGameLogsByDate($startDate, $endDate); //$this->CI->agin_game_logs->getAGINGameLogStatistics($startDate,$endDate);

       //  print_r($result); //exit();
       // var_dump($result); exit;
        if ($result) {
            $this->mergeResultGameLogs($result);
        }

        return $rlt;
    }

    public function processGameBetDetail($rowArray){
        // {"bet": "Banker", "rate": 0.95, "bet_detail": ""}

        $playtype=intval(@$rowArray['playtype']);

        $bet=null;
        $rate=null;
        $bet_detail=null;

        switch (@$rowArray['gametype']) {
            case 'BAC':
            case 'CBAC':
            case 'LBAC':
            case 'SBAC':
            case 'LINK':
                if($playtype==1){
                    $bet='banker';
                    $rate=0.95;
                }elseif($playtype==2){
                    $bet='player';
                    $rate=1;
                }elseif($playtype==3){
                    $bet='tie';
                    $rate=8;
                }elseif($playtype==4){
                    $bet='bankerPair';
                    $rate=11;
                }elseif($playtype==5){
                    $bet='playerPair';
                    $rate=11;
                }elseif($playtype==6){
                    $bet='big';
                    $rate=0.5;
                }elseif($playtype==7){
                    $bet='small';
                    $rate=1.5;
                }elseif($playtype==8){
                    $bet='bankerinsurance';
                }elseif($playtype==9){
                    $bet='playerinsurance';
                }elseif($playtype==11){
                    $bet='bankernofee';
                }elseif($playtype==12){
                    $bet='bankerlongbao';
                }elseif($playtype==13){
                    $bet='playerlongbao';
                }elseif($playtype==14){
                    $bet='Super Six';
                }elseif($playtype==15){
                    $bet='Any Pair';
                }elseif($playtype==16){
                    $bet='Perfect Pair';
                }elseif($playtype==17){
                    $bet='Banker Natural';
                    $rate=4.00;
                }elseif($playtype==18){
                    $bet='Player Natural';
                    $rate=4.00;
                }elseif($playtype==30){
                    $bet='Super Tie 0';
                    $rate=150.00;
                }elseif($playtype==31){
                    $bet='Super Tie 1';
                    $rate=215.00;
                }elseif($playtype==32){
                    $bet='Super Tie 2';
                    $rate=225.00;
                }elseif($playtype==33){
                    $bet='Super Tie 3';
                    $rate=200.00;
                }elseif($playtype==34){
                    $bet='Super Tie 4';
                    $rate=120.00;
                }elseif($playtype==35){
                    $bet='Super Tie 5';
                    $rate=110.00;
                }elseif($playtype==36){
                    $bet='Super Tie 6';
                    $rate=40.00;
                }elseif($playtype==37){
                    $bet='Super Tie 7';
                    $rate=40.00;
                }elseif($playtype==38){
                    $bet='Super Tie 8';
                    $rate=80.00;
                }elseif($playtype==39){
                    $bet='Super Tie 9';
                    $rate=80.00;
                }
                break;
            case 'DT':
                if($playtype==21){
                    $bet='dragon';
                    $rate=1;
                }elseif($playtype==22){
                    $bet='tiger';
                    $rate=1;
                }elseif($playtype==23){
                    $bet='tie';
                    $rate=8;
                }
                break;
            case 'SHB':
                if($playtype==41){
                    $bet='big';
                    $rate=1;
                }elseif($playtype==42){
                    $bet='small';
                    $rate=1;
                }elseif($playtype==43){
                    $bet='odd';
                    $rate=1;
                }elseif($playtype==44){
                    $bet='even';
                    $rate=1;
                }elseif($playtype==45){
                    $bet='allTriple';
                    $rate=24;
                }elseif($playtype==46){
                    $bet='betMap-sicbo-110';
                    $rate=150;
                }elseif($playtype==47){
                    $bet='betMap-sicbo-111';
                    $rate=150;
                }elseif($playtype==48){
                    $bet='betMap-sicbo-112';
                    $rate=150;
                }elseif($playtype==49){
                    $bet='betMap-sicbo-113';
                    $rate=150;
                }elseif($playtype==50){
                    $bet='betMap-sicbo-114';
                    $rate=150;
                }elseif($playtype==51){
                    $bet='betMap-sicbo-115';
                    $rate=150;
                }elseif($playtype==52){
                    $bet='betMap-sicbo-134';
                }elseif($playtype==53){
                    $bet='betMap-sicbo-135';
                }elseif($playtype==54){
                    $bet='betMap-sicbo-136';
                }elseif($playtype==55){
                    $bet='betMap-sicbo-137';
                }elseif($playtype==56){
                    $bet='betMap-sicbo-138';
                }elseif($playtype==57){
                    $bet='betMap-sicbo-139';
                }elseif($playtype==58){
                    $bet='betMap-sicbo-104';
                    $rate=8;
                }elseif($playtype==59){
                    $bet='betMap-sicbo-105';
                    $rate=8;
                }elseif($playtype==60){
                    $bet='betMap-sicbo-106';
                    $rate=8;
                }elseif($playtype==61){
                    $bet='betMap-sicbo-107';
                    $rate=8;
                }elseif($playtype==62){
                    $bet='betMap-sicbo-108';
                    $rate=8;
                }elseif($playtype==63){
                    $bet='betMap-sicbo-109';
                    $rate=8;
                }elseif($playtype==64){
                    $bet='betMap-sicbo-140';
                    $rate=5;
                }elseif($playtype==65){
                    $bet='betMap-sicbo-141';
                    $rate=5;
                }elseif($playtype==66){
                    $bet='betMap-sicbo-142';
                    $rate=5;
                }elseif($playtype==67){
                    $bet='betMap-sicbo-143';
                    $rate=5;
                }elseif($playtype==68){
                    $bet='betMap-sicbo-144';
                    $rate=5;
                }elseif($playtype==69){
                    $bet='betMap-sicbo-145';
                    $rate=5;
                }elseif($playtype==70){
                    $bet='betMap-sicbo-146';
                    $rate=5;
                }elseif($playtype==71){
                    $bet='betMap-sicbo-147';
                    $rate=5;
                }elseif($playtype==72){
                    $bet='betMap-sicbo-148';
                    $rate=5;
                }elseif($playtype==73){
                    $bet='betMap-sicbo-149';
                    $rate=5;
                }elseif($playtype==74){
                    $bet='betMap-sicbo-150';
                    $rate=5;
                }elseif($playtype==75){
                    $bet='betMap-sicbo-151';
                    $rate=5;
                }elseif($playtype==76){
                    $bet='betMap-sicbo-152';
                    $rate=5;
                }elseif($playtype==77){
                    $bet='betMap-sicbo-153';
                    $rate=5;
                }elseif($playtype==78){
                    $bet='betMap-sicbo-154';
                    $rate=5;
                }elseif($playtype==79){
                    $bet='betMap-sicbo-117';
                    $rate=50;
                }elseif($playtype==80){
                    $bet='betMap-sicbo-118';
                    $rate=18;
                }elseif($playtype==81){
                    $bet='betMap-sicbo-119';
                    $rate=14;
                }elseif($playtype==82){
                    $bet='betMap-sicbo-120';
                    $rate=12;
                }elseif($playtype==83){
                    $bet='betMap-sicbo-121';
                    $rate=8;
                }elseif($playtype==84){
                    $bet='betMap-sicbo-125';
                    $rate=6;
                }elseif($playtype==85){
                    $bet='betMap-sicbo-126';
                    $rate=6;
                }elseif($playtype==86){
                    $bet='betMap-sicbo-127';
                    $rate=6;
                }elseif($playtype==87){
                    $bet='betMap-sicbo-128';
                    $rate=6;
                }elseif($playtype==88){
                    $bet='betMap-sicbo-129';
                    $rate=8;
                }elseif($playtype==89){
                    $bet='betMap-sicbo-130';
                    $rate=12;
                }elseif($playtype==90){
                    $bet='betMap-sicbo-131';
                    $rate=14;
                }elseif($playtype==91){
                    $bet='betMap-sicbo-132';
                    $rate=18;
                }elseif($playtype==92){
                    $bet='betMap-sicbo-133';
                    $rate=50;
                }
                break;
            case 'ROU':
                if($playtype==101){
                    $bet='judgeResult-rouletteWheel-200';
                    $rate=35;
                }elseif($playtype==102){
                    $bet='judgeResult-rouletteWheel-201';
                    $rate=17;
                }elseif($playtype==103){
                    $bet='judgeResult-rouletteWheel-202';
                    $rate=11;
                }elseif($playtype==104){
                    $bet='judgeResult-rouletteWheel-204';
                    $rate=11;
                }elseif($playtype==105){
                    $bet='judgeResult-rouletteWheel-205';
                    $rate=8;
                }elseif($playtype==106){
                    $bet='judgeResult-rouletteWheel-203';
                    $rate=8;
                }elseif($playtype==107){
                    $bet='judgeResult-rouletteWheel-2071';
                    $rate=2;
                }elseif($playtype==108){
                    $bet='judgeResult-rouletteWheel-2072';
                    $rate=2;
                }elseif($playtype==109){
                    $bet='judgeResult-rouletteWheel-2073';
                    $rate=2;
                }elseif($playtype==110){
                    $bet='judgeResult-rouletteWheel-206';
                    $rate=5;
                }elseif($playtype==111){
                    $bet='judgeResult-rouletteWheel-2081';
                    $rate=2;
                }elseif($playtype==112){
                    $bet='judgeResult-rouletteWheel-2082';
                    $rate=2;
                }elseif($playtype==113){
                    $bet='judgeResult-rouletteWheel-2083';
                    $rate=2;
                }elseif($playtype==114){
                    $bet='judgeResult-rouletteWheel-209';
                    $rate=1;
                }elseif($playtype==115){
                    $bet='judgeResult-rouletteWheel-210';
                    $rate=1;
                }elseif($playtype==116){
                    $bet='judgeResult-rouletteWheel-213';
                    $rate=1;
                }elseif($playtype==117){
                    $bet='judgeResult-rouletteWheel-214';
                    $rate=1;
                }elseif($playtype==118){
                    $bet='judgeResult-rouletteWheel-211';
                    $rate=1;
                }elseif($playtype==119){
                    $bet='judgeResult-rouletteWheel-212';
                    $rate=1;
                }
                break;
            case 'BJ':
                if($playtype==220){
                    $bet='judgeResult-blackjack-220';
                }elseif($playtype==221){
                    $bet='judgeResult-blackjack-221';
                }elseif($playtype==222){
                    $bet='judgeResult-blackjack-222';
                }elseif($playtype==223){
                    $bet='judgeResult-blackjack-223';
                }elseif($playtype==224){
                    $bet='judgeResult-blackjack-224';
                }elseif($playtype==225){
                    $bet='judgeResult-blackjack-225';
                }elseif($playtype==226){
                    $bet='judgeResult-blackjack-226';
                }elseif($playtype==227){
                    $bet='judgeResult-blackjack-227';
                }elseif($playtype==228){
                    $bet='judgeResult-blackjack-228';
                }elseif($playtype==229){
                    $bet='judgeResult-blackjack-229';
                }elseif($playtype==230){
                    $bet='judgeResult-blackjack-230';
                }elseif($playtype==231){
                    $bet='judgeResult-blackjack-231';
                }elseif($playtype==232){
                    $bet='judgeResult-blackjack-232';
                }elseif($playtype==233){
                    $bet='judgeResult-blackjack-233';
                }
                break;
            case 'NN':
                if($playtype==207){
                    $bet='judgeResult-nn-207';
                }elseif($playtype==208){
                    $bet='judgeResult-nn-208';
                }elseif($playtype==209){
                    $bet='judgeResult-nn-209';
                }elseif($playtype==210){
                    $bet='judgeResult-nn-210';
                }elseif($playtype==211){
                    $bet='judgeResult-nn-211';
                }elseif($playtype==212){
                    $bet='judgeResult-nn-212';
                }elseif($playtype==213){
                    $bet='judgeResult-nn-213';
                }elseif($playtype==214){
                    $bet='judgeResult-nn-214';
                }elseif($playtype==215){
                    $bet='judgeResult-nn-215';
                }elseif($playtype==216){
                    $bet='judgeResult-nn-216';
                }elseif($playtype==217){
                    $bet='judgeResult-nn-217';
                }elseif($playtype==218){
                    $bet='judgeResult-nn-218';
                }
                break;

            case 'ULPK':
                if($playtype==180){
                    $bet='judgeResult-holdem-180';
                }elseif($playtype==181){
                    $bet='judgeResult-holdem-181';
                }elseif($playtype==182){
                    $bet='judgeResult-holdem-182';
                }elseif($playtype==183){
                    $bet='judgeResult-holdem-183';
                }elseif($playtype==184){
                    $bet='judgeResult-holdem-184';
                }
                break;

            case 'FT':
                if($playtype>=130 && $playtype<=167){
                    $bet='judgeResult-ft-'.$playtype;
                }
                break;
            case '27':
            case '24':
            case '13':
            case '25':
            case '26':
            case '29':
            case '23':
                //keno,lottery
                break;

            case 'ZJH':
                if($playtype==260){
                    $bet='dragon';
                }elseif($playtype==261){
                    $bet='Phoenix';
                }elseif($playtype==262){
                    $bet='judgeResult-winThreeCards-262';
                }elseif($playtype==263){
                    $bet='judgeResult-winThreeCards-263';
                }elseif($playtype==264){
                    $bet='judgeResult-winThreeCards-264';
                }elseif($playtype==265){
                    $bet='judgeResult-winThreeCards-265';
                }elseif($playtype==266){
                    $bet='judgeResult-winThreeCards-266';
                }
                break;

        }

        switch ($rowArray['platformtype']) {
            case self::AGSHABA_PLATFORM_TYPE:
                #replace &qout; to ""
                $json_remark = str_replace("&quot;", '"', $rowArray['remark']);
                $remark = json_decode($json_remark,true);
                $isParlay = !empty($remark['ParlayData']) ? true : false;
                $betDetails =  $this->utils->encodeJson(array_merge(
                        $this->processBetDetatails($remark),
                        array('sports_bet' => $this->setBetDetails($remark))
                    )
                );
                // $betDetails = array(
                //     "Event" => $remark['events'],
                //     "Status" => $remark['ticket_status'],
                // );
                return ['bet'=>$remark['stake'], 'rate'=>$remark['odds'], 'bet_detail'=>$betDetails ,'isParlay' => $isParlay];
                break;

            case self::AG_SPORTS_PLATFORM_TYPE:
                $json_remark = str_replace("&quot;", '"', $rowArray['remark']);
                $ArrRemark = json_decode($rowArray['remark'],true);
                $isParlay = count($ArrRemark['detail'])>1?true:false;
                $betDetails['sports_bet'] =  $this->processAGSportsBetDetatails($ArrRemark,$isParlay);
                $betDetails['isParlay'] =  $isParlay;
                if(!empty($rowArray['cancelReason'])){
                    $betDetails['Cancel Reason'] =  $rowArray['cancelReason'];
                }
                return $betDetails;
                break;

            default:
                return ['bet'=>$bet, 'rate'=>$rate, 'bet_detail'=>$bet_detail];
                break;
        }

    }

    public function processAGSportsBetDetatails($field,$isParlay){
        $data = $field['detail'];
        $set = array();
        if($isParlay){
            foreach ($data as $key => $game) {
                $set[$key] = array(
                    'yourBet' => $game['selection'][0]['name'],
                    'isLive' => "N/A",
                    'odd' => $game['odds'],
                    'hdp'=> "N/A",
                    'htScore'=> "N/A" ,
                    'eventName' => $game['event'][0]['name'],
                    'league' => $game['competition'][0]['name'],
                );
            }
        }else{
            $set[] = array(
                'yourBet' => @$data[0]['selection'][0]['name'],
                'isLive' => "N/A",
                'odd' => @$data[0]['odds'],
                'hdp'=> "N/A",
                'htScore'=> "N/A" ,
                'eventName' => @$data[0]['event'][0]['name'],
                'league' => @$data[0]['competition'][0]['name'],
            );
        }
        return $set;
    }

    public function processBetDetatails($field) {
        $details = array(
            "League" => $field['league_id'],
            "Match ID" => @$field['match_id'],
            "Odds"  => $field['odds'],
            "Status" => $field['ticket_status'],
            "Bet Team" =>$field['bet_team']
        );
        return  $details;
    }

    public function setBetDetails($field){
        $data = $field['ParlayData'];
        $set = array();
        if(!empty($data)){
            foreach ($data as $key => $game) {
                $set[$key] = array(
                    'yourBet' => $game['bet_team'],
                    'isLive' => $game['islive'] > 0,
                    'odd' => $game['odds'],
                    'hdp'=> $game['hdp'],
                    'htScore'=> "N/A" ,
                    'eventName' => $game['match_id'],
                    'league' => $game['league_id'],
                );
            }
        }else{
            $set[] = array(
                'yourBet' => $field['bet_team'],
                'isLive' => $field['islive'] > 0,
                'odd' => $field['odds'],
                'hdp'=> $field['hdp'],
                'htScore'=> "N/A" ,
                'eventName' => $field['match_id'],
                'league' => $field['league_id'],
            );
        }
        return $set;
    }

    public function mergeResultGameLogs($result)
    {//var_dump($result);
        $this->CI->load->model(array('agin_game_logs_result'));
        $unknownGame = $this->getUnknownGame();

        $this->CI->utils->debug_log('[mergeResultGameLogs] merge game logs '.$this->getPlatformCode().' count', count($result));
        foreach ($result as $key) {

            //use fields: player_id, playername, start_at, end_at, sceneId, gamecode, billno,
            //result, bet_amount, result_amount, after_balance, external_uniqueid, response_result_id
            //game_code, game_type, game, game_description_id, game_type_id, platformtype, flag

            //check if platform is hunter and transfer type is 4 which is BUY FISH RECORD, will disregard
            // if($key->platformtype ==self::AG_FISHING_PLATFORM_TYPE && $key->transferType == self::BUY_FISH_RECORD){
            //     continue;
            // }
            $player_id = $key->player_id;
            $username = $key->playername;
            list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($key, $unknownGame);

            // $gameDate = new DateTime($key->bettime);
            // $gameDateStr = $this->CI->utils->formatDateTimeForMysql($gameDate);

            //end_at = recalcutime for Sports, sceneendtime for HUNTER, bettime for other type games
            $start_at = $key->start_at;
            $end_at = $key->end_at;
            $note = null;
            $match_details = null;
            $after_balance=0;
            switch ($key->platformtype) {
                case self::AG_FISHING_PLATFORM_TYPE:
                    $end_at = $key->SceneEndTime;
                    $roundNumber = $key->sceneId;
                    break;

                case self::AG_PLATFORM_TYPE:
                    $end_at = $key->start_at;
                    $after_balance = $key->after_balance;
                    $roundNumber = $key->gamecode;
                    if(empty($roundNumber)){
                        $roundNumber = $key->billno;
                    }
                    $note = $key->billno;
                    $game_result =  $this->CI->agin_game_logs_result->getGameResultByGameCode($roundNumber);
                    $match_details = (!empty($game_result)) ? $game_result->card_list : null ;
                    break;

                case self::AG_SLOTS_PLATFORM_TYPE:
                    $end_at = $key->start_at;
                    $note = $key->billno."\n-\n ".$key->subbillno;
                    $roundNumber = $key->billno;
                    break; 
                case self::AG_SPORTS_PLATFORM_TYPE:
                    $end_at = $key->recalcutime;
                    $after_balance = $key->after_balance;
                    $roundNumber = $key->billno;
                    break;
                default:
                    $end_at = $key->start_at;
                    $roundNumber = $key->billno;
                    break;
            }

            $real_bet_amount= isset($key->real_bet_amount) ? $key->real_bet_amount : 0;

            $betDetail= $this->processGameBetDetail((array)$key);

            $extra = array(
                'table'       => $roundNumber,
                'note'        => $note,
                'trans_amount'=> $real_bet_amount,
                'match_details' => $match_details,
                'bet_details' => $betDetail,
            );

            if(isset($key->id)) {
                $extra['sync_index'] = $key->id;
            }

            #check if AGSHABA is the current platform type
            if($key->platformtype == self::AGSHABA_PLATFORM_TYPE && $key->flag == '0'){
                $extra['status'] = Game_logs::STATUS_PENDING;
                $extra['odds']   = $betDetail['rate'];
            }elseif($key->platformtype == self::AGSHABA_PLATFORM_TYPE && $key->flag == '1'){
                $extra['status'] = Game_logs::STATUS_SETTLED;
                $extra['bet_type'] = $betDetail['isParlay'] ? 'Mix Parlay' : 'Single Bet';
                $extra['bet_details'] = $betDetail['bet_detail'];
            }

            if ($key->platformtype == self::AG_PLATFORM_TYPE && isset($key->extra) && !empty($key->extra)){
                # destroy extra value then create new one
                unset($extra);
                $extraData = json_decode($key->extra, true);
                $betType = $extraData['isMultiBet'] ? 'Combo Bet':'Single Bet';
                unset($extraData['isMultiBet']);

                // print_r($extraData);

                $extra = array(
                    'trans_amount' => $real_bet_amount,
                    'table' => $roundNumber,
                    'bet_details'  => json_encode($extraData),
                    'bet_type'     => $betType,
                    'match_details' => $match_details,
                    'note'        => $note,
                    'sync_index'  => $key->id,
                );
            }

            if($key->platformtype == self::AG_SPORTS_PLATFORM_TYPE){
                $betType = $betDetail['isParlay']?'Combo Bet':'Single Bet';
                $extra = array(
                    'table'       => $roundNumber,
                    'note'        => $note,
                    'trans_amount'=> $real_bet_amount,
                    'match_details' => $match_details,
                    'bet_details' => $betDetail,
                    'bet_type'     => $betType,
                    'sync_index'  => $key->id,
                    'updated_at'  => $key->updated_at,
                );

                switch ($key->flag) {
                    case 0: // unsettled
                        $extra['status'] = Game_logs::STATUS_PENDING;
                        break;
                    case 1: // Settled
                        $extra['status'] = Game_logs::STATUS_SETTLED;
                        break;
                    case -8: // Cancel particular round’s Bill
                        $extra['status'] = Game_logs::STATUS_CANCELLED;
                        break;
                    case -9: // Cancel particular Bill No.
                        $extra['status'] = Game_logs::STATUS_CANCELLED;
                        break;
                    default: // by default Pending
                        $extra['status'] = Game_logs::STATUS_PENDING;
                        break;
                }
            }

            $bet_amount=$key->bet_amount;
            $result_amount = $key->result_amount;
            $after_balance = $key->after_balance;
            $flag = Game_logs::FLAG_GAME;

            if (self::DATA_TYPE_EBR == $key->datatype) {
               $after_balance = null;
            }

            //check if platform is hunter and transfer type is 12  which is BUY FISH PAYOUT,
            if($key->platformtype ==self::AG_FISHING_PLATFORM_TYPE && $key->transferType == self::BUY_FISH_PAYOUT){
                $fish_data = $this->CI->agin_game_logs->getBuyFishData($key->fishIdStart,$key->fishIdEnd);
                $fish_cost = (float)$fish_data->transferAmount;
                if($fish_data->fishIdStart != $fish_data->fishIdEnd){
                    $fish_count = (float)(($fish_data->fishIdEnd - $fish_data->fishIdStart)+1);
                    $fish_cost = (float) $fish_data->transferAmount / $fish_count;
                }

                $bet_amount = abs($fish_cost);
                $extra['trans_amount'] = $bet_amount;
                // echo $fish_data->transferAmount;echo"<br>";
                // echo "<pre>";
                // echo($fish_data->fishIdStart."-". $bet_amount);
                // print_r($bet_amount);

            }

            // $this->CI->utils->debug_log('AG sync merge extra info ===========>',$extra);

            $this->syncGameLogs(
                $game_type_id,
                $game_description_id,
                $key->game_code,
                $key->game_type,
                $key->game,
                $player_id,
                $username,
                $bet_amount,
                $result_amount,
                null,
                null,
                $after_balance,
                null,
                $key->external_uniqueid,
                $start_at,
                $end_at,
                $key->response_result_id,
                $flag,
                $extra
            );
            // }
        }
    }

    public function getGameDescriptionInfo($row, $unknownGame)
    {
        $externalGameId = $row->game_code;
        $extra = array('game_code' => $row->game_code);

        return $this->processUnknownGame(
            $row->game_description_id, $row->game_type_id,
            $row->game, $row->game_type, $externalGameId, $extra,
            $unknownGame);
    }

    protected function getHttpHeaders($params)
    {
        return array('Content-type' => 'text/xml');
    }

    public function getTrialAccounts()
    {
        $trial_account = $this->getSystemInfo('trial_account');
        if(empty($trial_account)){
            $trial_account = 'demoagplayer';

            return [$trial_account];
        }

        if(!is_array($trial_account)){
            $trial_account = [$trial_account];
        }

        return $trial_account;
    }

    public function getTrialAccount()
    {
        $trial_account = $this->getTrialAccounts();

        return $trial_account[array_rand($trial_account, 1)];
    }

    public function getTrialPlayer()
    {
        global $CI;

        $CI->load->model('player_model');

        $player_name = $this->getTrialAccount();

        $player_info = $CI->player_model->getPlayerByUsername($player_name);

        return $player_info;
    }

    // public function getGameTimeToServerTime() {
    //     return $this->getSystemInfo('gameTimeToServerTime','+12 hours');
    // }

    // public function getServerTimeToGameTime() {
    //     return $this->getSystemInfo('serverTimeToGameTime','-12 hours');
    // }
}

/*end of file*/
