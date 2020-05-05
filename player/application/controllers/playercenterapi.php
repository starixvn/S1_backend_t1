<?php

require_once dirname(__FILE__) . '/customer_api/t1t_ac_tmpl.php';

/**
 *
 * @see  T1t_ac_tmpl (player/application/controllers/customer_api/t1t_ac_tmpl.php)
 *
 */
class Playercenterapi extends T1t_ac_tmpl {

    protected $black_list_enabled = true;
    protected $black_list = [];
    protected $white_list_enabled = true;

    protected $white_list = ['ping_auth', 'ping', 'test_sign', 'login_by_username', 'logout_from_token', 'register_player',
        'player_exists' ,
        'deposit_create_and_approve' ,
    ];

    protected $comapi_req_id;

    //excepted from auth
    protected $except=['login_by_username', 'register_player', 'ping'];
    //excepted from sign
    protected $except_sign=['ping', 'login_by_username', 'register_player', 'test_sign'];
    /**
     * API methods excluded from db logging
     * @see returnError(), returnSuccess(), savePlayercenterResponseResult()
     */
    protected $except_log = [
        'ping_auth', 'ping', 'test_sign'
    ];

    const VERSION='2.02';

    const CODE_SUCCESS='0';

    //new api code
    const CODE_INVALID_SIGN='8001';
    const CODE_INVALID_MERCHANT_CODE='8002';
    const CODE_INVALID_SECURE_KEY='8003';
    const CODE_INVALID_AUTH_TOKEN='8004';
    const CODE_INVALID_USERNAME='8005';
    const CODE_INVALID_PASSWORD='8006';
    const CODE_PLAYER_USERNAME_PASSWORD_DONOT_MATCH='8007';
    const CODE_PLAYER_IS_BLOCKED='8008';
    const CODE_PLAYER_IS_DELETED='8009';
    const CODE_TEST_FIELDS_IS_REQUIRED='8010';
    const CODE_INVALID_CURRENCY='8011';

    const CODE_DEPOSIT1_AMOUNT_INVALID                      = '8012';
    const CODE_DEPOSIT1_AMOUNT_TOO_LONG                     = '8013';
    const CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE  = '8014';
    const CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN                = '8015';
    const CODE_DEPOSIT1_AMOUNT_BEYOND_MAX                   = '8016';
    const CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT        = '8017';
    const CODE_DEPOSIT1_DEPOSIT_FAILED                      = '8018';
    const CODE_DEPOSIT1_APPROVAL_FAILED                     = '8019';

    //system
    const CODE_LOCK_FAILED='9998';
    const CODE_INTERNAL_ERROR='9999';

    protected $codes=[
        self::CODE_SUCCESS=>'success',
        self::CODE_INVALID_SIGN=>'invalid signature',
        self::CODE_INVALID_MERCHANT_CODE=>'invalid merchant_code',
        self::CODE_INVALID_SECURE_KEY=>'invalid secure_key',
        self::CODE_INVALID_AUTH_TOKEN=>'invalid auth_token',
        self::CODE_INVALID_USERNAME=>'invalid username',
        self::CODE_INVALID_PASSWORD=>'invalid password',
        self::CODE_PLAYER_USERNAME_PASSWORD_DONOT_MATCH=>'username password donot match',
        self::CODE_PLAYER_IS_BLOCKED=>'player is blocked',
        self::CODE_PLAYER_IS_DELETED=>'player is deleted',
        self::CODE_TEST_FIELDS_IS_REQUIRED=>'test_fields is required',
        self::CODE_INVALID_CURRENCY=>'invalid currency',
        self::CODE_LOCK_FAILED=>'lock failed',
        self::CODE_INTERNAL_ERROR=>'internal error',

        // deposit_create_and_approve()
        self::CODE_DEPOSIT1_AMOUNT_INVALID          => 'Amount invalid' ,
        self::CODE_DEPOSIT1_AMOUNT_TOO_LONG         => 'Amount too long, 10 places max' ,
        self::CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE => 'Payment account ID invalid or unavailable' ,
        self::CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN    => 'Amount less than min' ,
        self::CODE_DEPOSIT1_AMOUNT_BEYOND_MAX       => 'Amount greater than max' ,
        self::CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT => 'Max daily deposit amount reached' ,
        self::CODE_DEPOSIT1_DEPOSIT_FAILED          => 'Deposit failed' ,
        self::CODE_DEPOSIT1_APPROVAL_FAILED         => 'Failure when approving deposit'
    ];

    function __construct() {
        parent::__construct();
        $this->load->model(['common_token', 'external_system', 'player_model', 'wallet_model']);
        // $this->generate_comapi_req_id();
    }

    private $start_time;
    private $player_id;
    private $auth_token;
    private $sign_key;
    private $params;
    private $currency;
    private $api_name;

    /**
     * init api first
     * @return boolean init result
     */
    protected function initApi(){
        $this->start_time=time();

        $this->processParameters();
        //example: /playercenterapi/ping
        $this->currency=$this->getParam('currency');
        if(empty($this->currency)){
            //try get it from __OG_TARGET_DB
            $this->currency=$this->input->get(Multiple_db::__OG_TARGET_DB);
            if(empty($this->currency)){
                //still empty
                $this->currency=null;
            }
        }

        if($this->validateCurrencyAndSwitchDB()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_CURRENCY);
            return false;
        }

        //validate token and get sign key
        if($this->validateAuthToken()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_AUTH_TOKEN);
            return false;
        }

        if($this->validateSign()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_SIGN);
            return false;
        }

        return true;
    }

    /**
     * process parameter
     * decode json parameter
     * copy json parameter to $_POST
     */
    protected function processParameters(){
        //read json
        $json = file_get_contents('php://input');
        $this->params=$this->utils->decodeJson($json);
        $this->input->copyParametersToInput($this->params);

        //record raw call
        $this->utils->debug_log('-------- get json from input on game api', $json, $this->params);
        $this->utils->debug_log('====print $_POST', $_POST);
        unset($json);

        $this->load->library(['uri']);
        $this->api_name=$this->uri->segment(2);
    }

    /**
     * copy internal api key to $_POST
     */
    protected function fillInternalAPIKey(){
        $internal_player_center_api_key=$this->getInternalAPIKey();
        $_POST['api_key']=$internal_player_center_api_key;
    }

    /**
     * get internal api key from config
     * @return string
     */
    protected function getInternalAPIKey(){
        return $this->utils->getConfig('internal_player_center_api_key');
    }

    /**
     * validate auth_token and set player id and sign key
     *
     * @return boolean auth_token is valid or not
     */
    protected function validateAuthToken(){
        $success=false;
        if(in_array($this->api_name, $this->except)){
            //ignore
            $success=true;
        }else{
            //check token
            list($playerId,$signKey)=$this->common_token->getPlayerIdAndSignKeyByPlayerAuthToken($this->getParam('auth_token'));
            $this->player_id=$playerId;
            $this->sign_key=$signKey;
            if(!empty($this->player_id) && !empty($this->sign_key)){
                $this->auth_token=$this->getParam('auth_token');
                //pass
                $success=true;
            }else{
                $success=false;
            }
        }

        return $success;
    }

    /**
     * only fo mdb, check currency option and change db to target currency
     * @return boolean
     */
    protected function validateCurrencyAndSwitchDB(){
        if(!$this->utils->isEnabledMDB()){
            return true;
        }
        if(empty($this->currency)){
            return false;
        }else{
            //validate currency name
            if(!$this->utils->isAvailableCurrencyKey($this->currency)){
                //invalid currency name
                return false;
            }else{
                //switch to target db
                $_multiple_db=Multiple_db::getSingletonInstance();
                $_multiple_db->switchCIDatabase($this->currency);
                return true;
            }
        }
    }

    /**
     * validateSign
     * @return boolean
     */
    protected function validateSign(){
        //no need to check sign
        if(in_array($this->api_name, $this->except_sign)){
            //ignore
            $success=true;
            return $success;
        }

        //check debug api key
        $debugKey=$this->utils->getConfig('player_center_api_X-DEBUG-SIGN-KEY');
        $inputDebugKey=$this->input->get('X-DEBUG-SIGN-KEY');
        if(empty($inputDebugKey)){
            //try get from header
            if(isset($_SERVER['HTTP_X_DEBUG_SIGN_KEY'])){
                $inputDebugKey=$_SERVER['HTTP_X_DEBUG_SIGN_KEY'];
            }
        }
        if(!empty($debugKey) && !empty($inputDebugKey)){
            $this->utils->debug_log('checking debug key', $inputDebugKey, $debugKey);
            if($debugKey==$inputDebugKey){
                $this->utils->debug_log('debug key is right, permission is granted');
                $success=true;
                return $success;
            }
        }

        $signKey=$this->sign_key;
        if(empty($signKey)){
            $this->utils->error_log('empty sign key in player_center_api_sign_key');
            return false;
        }

        list($sign, $signString)=$this->common_token->generateSign($this->params, $signKey, ['sign']);

        $requestSign=strtolower($this->getParam('sign'));

        $this->utils->debug_log('sign string:'.$signString.', sign:'.$sign.', request sign:'.$requestSign);

        return $sign===$requestSign;
    }

    /**
     * get parameter
     * @param  string $key
     * @param  mixin $default
     * @return mixin value
     */
    protected function getParam($key, $default=null){
        if(isset($this->params[$key])){
            return $this->params[$key];
        }

        return $default;
    }

    /**
     * reset all params
     *
     */
    protected function resetAllParams(){
        unset($this->params);
        $this->params=[];
    }

    /**
     * get int param
     * @param  string $key
     * @param  int $default
     * @return int value
     */
    protected function getIntParam($key, $default=null){

        return intval($this->getParam($key, $default));

    }

    /**
     * get bool param
     * @param  string $key
     * @param  boolean $default
     * @return boolean value
     */
    protected function getBoolParam($key, $default=null){
        $val=$this->getParam($key, $default);
        if(is_bool($val)){
            return $val;
        }
        if(is_string($val)){
            //string true
            return strtolower($val)=='true';
        }
        if(is_int($val)){
            //!=0 is true
            return $val!=0;
        }

        return boolval($val);
    }

    /**
     * return error
     * @param  string $code
     * @param  string $customized_message
     * @param  string $detail
     *
     */
    protected function returnError($code, $customized_message=null, $detail=null){
        //if error is does not belong and it's is_player_account_exist, don't write to response result
        $ignoreWriteLog=false;

        $message=$customized_message;
        if(empty($message)){
            $message=$this->codes[$code];
        }

        $result=['success'=>false, 'version'=>self::VERSION, 'code'=>$code, 'message'=> $message,
            'request_id'=>$this->utils->getRequestId(), 'server_time'=>$this->utils->getNowForMysql(),
            'cost'=>time()-$this->start_time,
            'detail'=>$detail];
        if (!$ignoreWriteLog && !in_array($this->api_name, $this->except_log)) {
            //get function name
            $requstApi=$this->api_name;
            $returnJson=$result;
            $is_error=true;
            $this->savePlayercenterResponseResult($requstApi, $returnJson, $is_error);
        }

        return $this->returnJsonResult($result);
    }

    /**
     * return success
     * @param  string $detail
     * @param  string $customized_message
     *
     */
    protected function returnSuccess($detail, $customized_message=null){
        $ignoreWriteLog = false;
        $code=self::CODE_SUCCESS;
        $message=$customized_message;
        if(empty($message)){
            $message=$this->codes[$code];
        }

        $result=['success'=>true, 'version'=>self::VERSION, 'code'=>$code, 'message'=> $message,
            'request_id'=>$this->utils->getRequestId(), 'server_time'=>$this->utils->getNowForMysql(),
            'cost'=>time()-$this->start_time,
            'detail'=>$detail];
        if (!$ignoreWriteLog && !in_array($this->api_name, $this->except_log)) {
            //get function name
            $requstApi  = $this->api_name;
            $returnJson = $result;
            $is_error   = false;
            $this->savePlayercenterResponseResult($requstApi, $returnJson, $is_error);
        }

        return $this->returnJsonResult($result);
    }

    protected function returnResultFromInternal(){
        $apiResult=$this->getInternalJsonResult();
        //['success'=>, 'code'=>, 'message'=>, ]
        if(!empty($apiResult) && is_array($apiResult)){
            if($apiResult['success']){
                return $this->returnSuccess($detail);
            }else{
                return $this->returnError($apiResult['code']);
            }
        }
        return $this->returnError(self::CODE_INTERNAL_ERROR);
    }

    /**
     * save response result
     * @param  string  $requstApi
     * @param  array  $returnJson
     * @param  boolean $is_error
     * @param  array  $extra
     * @param  integer $statusCode
     * @param  string  $statusText
     * @return int id
     */
    protected function savePlayercenterResponseResult($requstApi, $returnJson,
            $is_error=false, $extra=null, $statusCode=200, $statusText=null){
        $this->load->model(['response_result']);
        $systemId=GAMEGATEWAY_API;
        $flag= $is_error ? Response_result::FLAG_ERROR : Response_result::FLAG_NORMAL;
        $requestParams=json_encode($this->params);
        if(empty($returnJson)){
            $returnJson=[];
        }
        if(!is_array($returnJson)){
            $returnJson=[$returnJson];
        }
        $returnJson['cost']=time()-$this->start_time;
        $resultText=json_encode($returnJson);
        return $this->response_result->saveResponseResult($systemId, $flag, $requstApi,
            $requestParams, $resultText, $statusCode, $statusText, $extra);
    }

    /**
     * return unimplemented
     * @param  string $customized_message
     *
     */
    protected function returnUnimplemented($customized_message=null){

        $code=self::CODE_SUCCESS;
        $message=$customized_message;
        if(empty($message)){
            $message=$this->codes[$code];
        }

        $result=['success'=>true, 'code'=>$code, 'message'=> $message, 'request_id'=>$this->utils->getRequestId(),
            'detail'=>['unimplemented'=>true]];

        return $this->returnJsonResult($result);

    }

    protected function stockException($code) {
        return new Exception(lang($this->codes[$code]), $code);
    }

    protected function generate_comapi_req_id() {
        $comapi_req_id = sprintf('%08x_%08x', time(), mt_rand(0x10000000, 0xffff0000));
        $this->comapi_req_id = $comapi_req_id;
    }

    protected function comapi_log() {
        $args_in = func_get_args();
        // $args_pass = array_merge([ $this->utils->getRequestId() ], $args_in);
        $args_pass = $args_in;
        call_user_func_array([ $this->utils, 'debug_log' ], $args_pass);
    }

    //===API start==============================================
    /**
     * login by username
     *
     */
    public function login_by_username(){
        if(!$this->initApi()){
            return false;
        }

        $username=$this->getParam('username');
        $password=$this->getParam('password');
        if(empty($username)){
            return $this->returnError(self::CODE_INVALID_USERNAME);
        }
        if(empty($password)){
            return $this->returnError(self::CODE_INVALID_PASSWORD);
        }

        list($player_id, $auth_token, $signKey, $timeout_datetime, $errorCode)=
            $this->common_token->generateAuthKeyByPlayerUsernamePassword(
                $username, $password, $this->getParam('force_new', false));

        if(empty($auth_token)){
            $returnCode=self::CODE_PLAYER_USERNAME_PASSWORD_DONOT_MATCH;
            switch ($errorCode) {
                case Player_model::ERROR_USERNAME_PASSWORD_DOESNOT_MATCH:
                    $returnCode=self::CODE_PLAYER_USERNAME_PASSWORD_DONOT_MATCH;
                    break;
                case Player_model::ERROR_USERNAME_BLOCKED:
                    $returnCode=self::CODE_PLAYER_IS_BLOCKED;
                    break;
                case Player_model::ERROR_USERNAME_DELETED:
                    $returnCode=self::CODE_PLAYER_IS_DELETED;
                    break;
            }
            return $this->returnError($returnCode);
        }else{
            $detail=['auth_token'=> $auth_token, 'sign_key'=>$signKey, 'timeout_datetime'=> $timeout_datetime];
            return $this->returnSuccess($detail);
        }
    }

    /**
     * ping before login
     * @return array 'pong'=>true
     */
    public function ping(){
        if(!$this->initApi()){
            return false;
        }

        $detail=['pong'=>true];

        return $this->returnSuccess($detail);
    }

    /**
     * ping after login with token
     * @return array 'pong'=>true
     */
    public function ping_auth(){
        if(!$this->initApi()){
            return false;
        }

        $this->enableInternalJsonResult();
        $this->apiEcho($this->getInternalAPIKey());
        $this->disableInternalJsonResult();
        $apiResult=$this->getInternalJsonResult();

        $this->utils->debug_log('api echo result', $apiResult);

        if(!$apiResult['success']){
            return $this->returnError($apiResult['code']);
        }else{
            $this->utils->debug_log('got player id', $this->player_id);
            $detail=['pong'=>true, 'logged'=>!empty($this->player_id)];

            return $this->returnSuccess($detail);
        }
    }

    /**
     * validate sign for any field
     * @return array signed=>true
     */
    public function test_sign(){
        if(!$this->initApi()){
            return false;
        }
        $signKey=$this->sign_key;
        $testFields=$this->getParam('test_fields');
        if(empty($testFields)){
            return $this->returnError(self::CODE_TEST_FIELDS_IS_REQUIRED);
        }
        list($sign, $signString)=$this->common_token->generateSign($testFields, $signKey, ['sign']);

        $requestSign=strtolower($this->getParam('test_sign'));

        $this->utils->debug_log('sign string:'.$signString.', sign:'.$sign.', request sign:'.$requestSign);

        $detail=['signed'=>$requestSign==$sign];

        return $this->returnSuccess($detail);

    }

    /**
     * logout from token
     * @return array $logged=>true
     */
    public function logout_from_token(){
        if(!$this->initApi()){
            return false;
        }

        $detail=['logged'=>$this->common_token->deleteToken($this->auth_token)];
        return $this->returnSuccess($detail);
    }

    /**
     * register player
     * @return array
     */
    public function register_player(){
        if(!$this->initApi()){
            return false;
        }

        $this->enableInternalJsonResult();
        $this->fillInternalAPIKey();
        //call register
        $this->createPlayer();
        $this->disableInternalJsonResult();

        return $this->returnResultFromInternal();
    }

    //===API end==============================================


    public function player_exists() {
        if (!$this->initApi()) { return false; }

        try {
            $username   = $this->getParam('username');


            $request = [
                'username'          => $username   ,
            ];
            $this->comapi_log(__METHOD__, [ 'request' => $request ]);

            if (empty($username)) {
                throw $this->stockException(self::CODE_INVALID_USERNAME);
            }

            $player_existence = $this->player_model->checkUsernameExist($username);

            $player_status_res = [
                'username'  => $username ,
                'existence' => $player_existence
            ];

            // Point of success --------

            $this->comapi_log(__METHOD__, 'Success', $player_status_res);

            $this->returnSuccess($player_status_res);
        }
        catch (Exception $ex) {
            $this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
            $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * Creates deposit by client's deposit request, then auto-approve it
     * OGP-15167
     */
    public function deposit_create_and_approve() {

        if (!$this->initApi()) { return false; }

        try {
            $this->load->model([ 'payment_account', 'transactions', 'sale_orders_notes' ]);
            $this->load->library([ 'comapi_lib' ]);

            $username       = $this->getParam('username');
            $amount         = floatval($this->getParam('amount'));
            $pay_acc_id     = $this->getIntParam('payment_account_id');
            $int_notes      = $this->getParam('notes_internal');
            $ext_notes      = $this->getParam('notes_external');
            $req_time       = $this->getParam('request_time');

            $request = [
                'username'          => $username   ,
                'amount'            => $amount     ,
                'payment_account_id'=> $pay_acc_id ,
                'notes_internal'    => $int_notes  ,
                'notes_external'    => $ext_notes  ,
                'request_time'      => $req_time
            ];
            $this->comapi_log(__METHOD__, [ 'request' => $request ]);

            // 1: User exists and legal
            //  (covered by login and token verifications)
            $player_id = $this->player_model->getPlayerIdByUsername($username);

            if (empty($player_id) || $this->player_id != $player_id) {
                $this->comapi_log(__METHOD__, 'player_id void or mismatch', [ 'player_id_from_token' => $this->player_id, 'player_id_from_username' => $player_id ]);
                throw($this->stockException(self::CODE_INVALID_USERNAME));
            }

            // 2: Amount format
            if ($amount <= 0) {
                throw($this->stockException(self::CODE_DEPOSIT1_AMOUNT_INVALID));
            }

            $amount_clear = number_format($amount, 2, '.', '');

            if (strlen($amount_clear) > 10) {
                throw($this->stockException(self::CODE_DEPOSIT1_AMOUNT_TOO_LONG));
            }

            // 3: Check for payment account ID
            $payment_account = $this->payment_account->getPaymentAccountWithVIPRule($pay_acc_id, $player_id);
            if (empty($payment_account)) {
                throw($this->stockException(self::CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE));
            }

            // 4: Check deposit amount against min/max in vip group rule
            $deposit_min = $this->comapi_lib->depcat_fix_deposit_max($payment_account->vip_rule_min_deposit_trans);
            $deposit_max = $this->comapi_lib->depcat_fix_deposit_min($payment_account->vip_rule_max_deposit_trans);

            // 4.1 Check for min deposit
            if ($amount < $deposit_min) {
                $this->comapi_log(__METHOD__, 'amount not within [min, max]', [ 'amount' => $amount, 'min' => $deposit_min, 'max' => $deposit_max ]);
                throw($this->stockException(self::CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN));
            }

            // 4.2 Check for max deposit
            if ($deposit_max > 0 && $amount > $deposit_max) {
                $this->comapi_log(__METHOD__, 'amount not within [min, max]', [ 'amount' => $amount, 'min' => $deposit_min, 'max' => $deposit_max ]);
                throw($this->stockException(self::CODE_DEPOSIT1_AMOUNT_BEYOND_MAX));
            }

            // 4.3 Check for daily deposit max (if set)
            $deposit_daily_max = $this->utils->getConfig('defaultMaxDepositDaily');

            if ($deposit_daily_max > 0) {
                $current_total = $this->transactions->sumDepositAmountToday($player_id);
                if (($current_total + $amount) >= $deposit_daily_max) {
                    $this->comapi_log(__METHOD__, 'Total deposit today exceeds limit', [ 'current_total' => $current_total, 'this_amount' => $amount, 'limit' => $deposit_daily_max ]);
                    throw($this->stockException(self::CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT));
                }
            }

            // 5: all set: create the order
            $deposit_dataset = [
                'username'              => $username ,
                'player_id'             => $player_id ,
                'amount'                => $amount_clear ,
                'payment_account_id'    => $pay_acc_id ,
                'deposit_notes'         => $int_notes ,
                'external_notes'        => $ext_notes ,
                'deposit_time'          => $req_time ,
            ];

            $deposit_res = $this->comapi_lib->comapi_deposit_bare($deposit_dataset);

            $this->utils->debug_log(__METHOD__, 'deposit_res', $deposit_res);

            if ($deposit_res['success'] == false) {
                throw($this->stockException(self::CODE_DEPOSIT1_DEPOSIT_FAILED));
            }

            $order = $deposit_res['order'];

            // 6: append int/ext notes
            $this->sale_orders_notes->add($int_notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::INTERNAL_NOTE, $order['id']);
            $this->sale_orders_notes->add($ext_notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::EXTERNAL_NOTE, $order['id']);

            // 7: approve the order
            $approval_res = $this->sale_order->approveSaleOrder($order['id']);

            if (!$approval_res) {
                throw($this->stockException(self::CODE_DEPOSIT1_APPROVAL_FAILED));
            }

            // point of success --------
            $deposit_return = [
                'username'  => $username ,
                'amount'    => $amount_clear ,
                'secure_id' => $order['secure_id'] ,
            ];

            $this->comapi_log(__METHOD__, 'Success', $deposit_return);

            $this->returnSuccess($deposit_return);
        }
        catch (Exception $ex) {
            $this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
            $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

} // End class Playercenterapi

