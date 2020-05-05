<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Backendapi
 *
 * sbe backend api
 *
 */
class Backendapi extends BaseController {

    private $params;
    private $currency;
    private $start_time;
    private $api_name;
    private $testing_mode=false;
    private $backend_api_white_ip_list=null;
    private $admin_user_id=null;
    private $backend_api_keys=null;

    protected $except_auth=['generate_token', 'ping', 'test_sign'];
    protected $except_sign=['ping', 'test_sign'];
    protected $except_admin_user=['ping'];

    protected $except_log = [
        'ping', 'ping_auth', 'test_sign',
    ];

    const VERSION='1.01';

    const CODE_SUCCESS='0';
    const CODE_INVALID_SIGN='1';
    const CODE_INVALID_ADMIN_USER_ID='2';
    const CODE_INVALID_SECURE_KEY='3';
    const CODE_INVALID_AUTH_TOKEN='4';
    const CODE_TEST_FIELDS_IS_REQUIRED='5';
    const CODE_INVALID_CURRENCY='6';
    const CODE_INVALID_WHITE_IP='7';
    const CODE_NO_PERMISSION='8';

    //for deposit
    const CODE_DEPOSIT1_AMOUNT_INVALID                      = '8012';
    const CODE_DEPOSIT1_AMOUNT_TOO_LONG                     = '8013';
    const CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE  = '8014';
    const CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN                = '8015';
    const CODE_DEPOSIT1_AMOUNT_BEYOND_MAX                   = '8016';
    const CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT        = '8017';
    const CODE_DEPOSIT1_DEPOSIT_FAILED                      = '8018';
    const CODE_DEPOSIT1_APPROVAL_FAILED                     = '8019';

    //system
    const CODE_INVALID_REQUEST='9997';
    const CODE_LOCK_FAILED='9998';
    const CODE_INTERNAL_ERROR='9999';

    protected $codes=[
        self::CODE_SUCCESS=>'success',
        self::CODE_INVALID_SIGN=>'invalid signature',
        self::CODE_INVALID_WHITE_IP=>'no permission, not in white ip list',
        self::CODE_INVALID_SECURE_KEY=>'invalid secure_key',
        self::CODE_INVALID_AUTH_TOKEN=>'invalid auth_token',
        self::CODE_INVALID_ADMIN_USER_ID=>'invalid admin user id',
        self::CODE_TEST_FIELDS_IS_REQUIRED=>'test_fields is required',
        self::CODE_INVALID_CURRENCY=>'invalid currency',
        self::CODE_NO_PERMISSION=>'no permission on this function',

        // deposit_create_and_approve()
        self::CODE_DEPOSIT1_AMOUNT_INVALID          => 'Amount invalid' ,
        self::CODE_DEPOSIT1_AMOUNT_TOO_LONG         => 'Amount too long, 10 places max' ,
        self::CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE => 'Payment account ID invalid or unavailable' ,
        self::CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN    => 'Amount less than min' ,
        self::CODE_DEPOSIT1_AMOUNT_BEYOND_MAX       => 'Amount greater than max' ,
        self::CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT => 'Max daily deposit amount reached' ,
        self::CODE_DEPOSIT1_DEPOSIT_FAILED          => 'Deposit failed' ,
        self::CODE_DEPOSIT1_APPROVAL_FAILED         => 'Failure when approving deposit',

        self::CODE_INVALID_REQUEST=>'invalid request',
        self::CODE_LOCK_FAILED=>'lock failed',
        self::CODE_INTERNAL_ERROR=>'internal error',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->model(['common_token', 'external_system', 'player_model', 'wallet_model', 'users']);
        // if(!$this->initApi()){
        //     $this->utils->error_log('init failed');
        //     //quit controller
        //     exit(1);
        // }
    }

    protected function initApi(){
        $this->start_time=time();
        if($this->processParameters()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_REQUEST, null, null, true);
            return false;
        }
        if($this->discoverCurrency()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_CURRENCY);
            return false;
        }

        if($this->validateCurrencyAndSwitchDB()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_CURRENCY);
            return false;
        }

        // $this->backend_api_keys=$this->utils->getConfig('backend_api_keys');
        // $this->backend_api_keys_testing=$this->utils->getConfig('backend_api_keys_testing');

        if($this->validateWhiteIP()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_WHITE_IP);
            return false;
        }

        if($this->validateAdminUserId()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_ADMIN_USER_ID);
            return false;
        }

        if($this->validateSign()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_SIGN);
            return false;
        }

        if($this->validateAuthToken()){
            //safe
        }else{
            $this->returnError(self::CODE_INVALID_AUTH_TOKEN);
            return false;
        }

        return true;
    }

    /**
     * process params, decode json
     */
    protected function processParameters(){
        $success=true;
        if($this->utils->isOptionsRequest()){
            $success=false;
        }else{
            //read json
            $json = file_get_contents('php://input');
            if(empty($json)){
                $success=false;
            }else{
                $this->params=$this->utils->decodeJson($json);
                //record raw call
                $this->utils->debug_log('-------- get json from input on game api', $json, $this->params);
                if(empty($this->params)){
                    $success=false;
                    $this->utils->error_log('empty params');
                }
            }
            unset($json);
        }

        $this->load->library(['uri']);
        //example: /backendapi/generate_token
        $this->api_name=$this->uri->segment(2);

        return $success;
    }

    protected function getParam($key, $default=null){
        if(isset($this->params[$key])){
            return $this->params[$key];
        }

        return $default;
    }

    protected function resetAllParam(){
        unset($this->params);
        $this->params=[];
    }

    protected function getIntParam($key, $default=null){
        return intval($this->getParam($key, $default));
    }

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
     * search currency from param or input get
     */
    protected function discoverCurrency(){
        $success=true;
        $this->currency=$this->getParam('currency');
        if(empty($this->currency)){
            //try get it from __OG_TARGET_DB
            $this->currency=$this->input->get(Multiple_db::__OG_TARGET_DB);
            if(empty($this->currency)){
                $this->currency=null;
            }
        }
        return $success;
    }

    protected function validateAdminUserId(){
        $success=false;
        if(in_array($this->api_name, $this->except_admin_user)){
            //ignore
            $success=true;
            return $success;
        }

        //try load admin user and key
        if($this->api_name=='generate_token'){
            $this->admin_user_id=$this->getParam('admin_user_id');
        }else{
            $auth_token=$this->getParam('auth_token');
            $arr=explode('-', $auth_token);
            if(!empty($arr) && count($arr)==2){
                $this->admin_user_id=$arr[0];
            }
        }
        if(!empty($this->admin_user_id)){
            $this->load->model(['users']);
            $this->backend_api_keys=$this->users->getKeysByUserId($this->admin_user_id);
            if(!empty($this->backend_api_keys)
                && !empty($this->backend_api_keys['secure_key'])
                && !empty($this->backend_api_keys['sign_key'])
                ){
                $success=true;
            }
        }

        return $success;
    }

    /**
     * for white ip
     * @return boolean $success
     */
    protected function validateWhiteIP(){
        $success=false;

        $this->backend_api_white_ip_list=$this->utils->getConfig('backend_api_white_ip_list');

        //init white ip info
        $this->load->model(['ip']);
        // if(isset($this->backend_api_keys['testing']) &&
        //         $this->backend_api_keys['testing']['enabled']){
        //     //set testing mode
        //     $this->testing_mode=true;
        //     $this->backend_keyname='testing';
        //     //testing mode
        //     $success=true;
        // }else{

        $success=$this->ip->checkWhiteIpListForAdmin(function ($ip, &$payload){
            $this->utils->debug_log('search ip', $ip);
            foreach ($this->backend_api_white_ip_list as $whiteIp) {
                if($this->utils->compareIP($ip, $whiteIp)){
                    $this->utils->debug_log('found white ip', $whiteIp, $ip);
                    //found
                    return true;
                }
            }
            //not found
            return false;
        }, $payload);
        //found
        // if(!empty($keyInfo) && $success){
        //     $this->backend_keyname=$keyInfo;
        // }

        // }
        // if(empty($this->backend_keyname)){
        //     //failed
        //     $success=false;
        // }

        $this->utils->debug_log('get key info', $success);
        return $success;
    }

    /**
     * check sign
     * @return boolean
     */
    protected function validateSign(){

        if(in_array($this->api_name, $this->except_sign)){
            //ignore
            $success=true;
            return $success;
        }

        //check debug api key
        $debugKey=$this->utils->getConfig('backend_api_X-DEBUG-SIGN-KEY');
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

        $signKey=$this->backend_api_keys['sign_key'];
        if(empty($signKey)){
            $this->utils->debug_log('empty sign key for :'. $this->getParam('merchant_code'));
            return false;
        }

        list($sign, $signString)=$this->common_token->generateSign($this->params, $signKey, ['sign']);

        $requestSign=strtolower($this->getParam('sign'));

        $this->utils->debug_log('sign string:'.$signString.', sign:'.$sign.', request sign:'.$requestSign);

        return $sign===$requestSign;
    }

    protected function validateAuthToken(){
        $success=false;
        if(in_array($this->api_name, $this->except_auth)){
            //ignore
            $success=true;
        }else{
            //check token
            if($this->common_token->isValidBackendAuthToken($this->getParam('auth_token'))){
                //pass
                $success=true;
            }else{
                $success=false;
            }
        }

        return $success;
    }

    protected function validateCurrencyAndSwitchDB(){
        if(!$this->utils->isEnabledMDB()){
            return true;
        }
        //mdb only
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
     * returnError
     * @param  string  $code
     * @param  string  $customized_message
     * @param  string  $detail
     * @param  boolean $ignoreWriteLog
     * @return boolean
     */
    protected function returnError($code, $customized_message=null, $detail=null, $ignoreWriteLog=false){
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
            $this->saveBackendResponseResult($requstApi, $returnJson, $is_error);
        }

        return $this->returnJsonResult($result);
    }

    protected function returnSuccess($detail, $customized_message=null){
        $ignoreWriteLog=false;
        $code=self::CODE_SUCCESS;
        $message=$customized_message;
        if(empty($message)){
            $message=$this->codes[$code];
        }

        $result=['success'=>true, 'version'=>self::VERSION, 'code'=>$code, 'message'=> $message,
            'request_id'=>$this->utils->getRequestId(), 'server_time'=>$this->utils->getNowForMysql(),
            'cost'=>time()-$this->start_time, 'testing_mode'=>$this->testing_mode,
            'detail'=>$detail];
        if (!$ignoreWriteLog && !in_array($this->api_name, $this->except_log)) {
            //get function name
            $requstApi=$this->api_name;
            $returnJson=$result;
            $is_error=false;
            $this->saveBackendResponseResult($requstApi, $returnJson, $is_error);
        }

        return $this->returnJsonResult($result);
    }

    protected function saveBackendResponseResult($requstApi, $returnJson,
            $is_error=false, $extra=null, $statusCode=200, $statusText=null){
        $this->load->model(['response_result']);
        $systemId=SBE_BACKEND_API;
        $flag= $is_error ? Response_result::FLAG_ERROR : Response_result::FLAG_NORMAL;
        $requestParams=json_encode($this->params);
        if(empty($returnJson)){
            $returnJson=[];
        }
        if(!is_array($returnJson)){
            $returnJson=[$returnJson];
        }
        if(empty($extra)){
            $extra=json_encode(getallheaders());
        }
        $returnJson['cost']=time()-$this->start_time;
        $resultText=json_encode($returnJson);
        $this->utils->debug_log('write to response result', $returnJson);
        return $this->response_result->saveResponseResult($systemId, $flag, $requstApi,
            $requestParams, $resultText, $statusCode, $statusText, $extra);
    }

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

    /**
     *
     * @return bool|void
     * @internal param string $merchant_code
     * @internal param string $secure_key
     * @internal param string $sign
     *
     */
    public function generate_token(){
        if(!$this->initApi()){
            return false;
        }

        //validate secure key
        if($this->getParam('secure_key')!=$this->backend_api_keys['secure_key']){
            return $this->returnError(self::CODE_INVALID_SECURE_KEY);
        }

        list($auth_token, $timeout_datetime)=$this->common_token->generateBackendAuthKeyByUserId(
            $this->admin_user_id, $this->getParam('force_new', false));

        if(empty($auth_token)){
            return $this->returnError(self::CODE_INTERNAL_ERROR);
        }else{
            $detail=['auth_token'=> $this->admin_user_id.'-'.$auth_token, 'timeout_datetime'=> $timeout_datetime];
            return $this->returnSuccess($detail);
        }
    }

    /**
     * logout from token
     * @return array $logged=>true
     */
    public function logout_from_token(){
        if(!$this->initApi()){
            return false;
        }

        $auth_token=$this->getParam('auth_token');
        $detail=['logout'=>$this->common_token->deleteTokenForAdminUser($auth_token)];
        return $this->returnSuccess($detail);
    }

    /**
     * Creates deposit by client's deposit request, then auto-approve it
     * OGP-15167
     */
    public function deposit_create_and_approve() {
        if(!$this->initApi()){
            return false;
        }

        //check permission of admin user
        if(!$this->users->checkAllPermissions($this->admin_user_id, ['approve_decline_deposit'])){
            return $this->returnError(self::CODE_NO_PERMISSION, 'no permission on deposit');
        }


        // try {
        $this->load->model([ 'payment_account', 'transactions', 'sale_orders_notes' ]);
        $this->load->library([ 'comapi_lib' ]);

        $username       = $this->getParam('username');
        $amount         = floatval($this->getParam('amount'));
        $pay_acc_id     = $this->getIntParam('payment_account_id');
        $int_notes      = $this->getParam('notes_internal');
        $ext_notes      = $this->getParam('notes_external');
        $req_time       = $this->getParam('request_time');

        $this->utils->debug_log(__METHOD__, [
            'username'          => $username   ,
            'amount'            => $amount     ,
            'payment_account_id'=> $pay_acc_id ,
            'notes_internal'    => $int_notes  ,
            'notes_external'    => $ext_notes  ,
            'request_time'      => $req_time
        ]);

        // 1: User exists and legal
        //  (covered by login and token verifications)
        $player_id = $this->player_model->getPlayerIdByUsername($username);
        if (empty($player_id)) {
            $this->utils->debug_log(__METHOD__, 'player_id void, wrong username');
            return $this->returnError(self::CODE_INVALID_USERNAME);
            // throw($this->stockException(self::CODE_INVALID_USERNAME));
        }

        // 2: Amount format
        if ($amount <= 0) {
            return $this->returnError(self::CODE_DEPOSIT1_AMOUNT_INVALID);
            // throw($this->stockException(self::CODE_DEPOSIT1_AMOUNT_INVALID));
        }

        $amount_clear = number_format($amount, 2, '.', '');

        if (strlen($amount_clear) > 10) {
            return $this->returnError(self::CODE_DEPOSIT1_AMOUNT_TOO_LONG);
        }

        // 3: Check for payment account ID
        $payment_account = $this->payment_account->getPaymentAccountWithVIPRule($pay_acc_id, $player_id);
        if (empty($payment_account)) {
            return $this->returnError(self::CODE_DEPOSIT1_PAY_ACCOUNT_INVALID_OR_UNAVAILABLE);
        }

        // 4: Check deposit amount against min/max in vip group rule
        $deposit_min = $this->comapi_lib->depcat_fix_deposit_max($payment_account->vip_rule_min_deposit_trans);
        $deposit_max = $this->comapi_lib->depcat_fix_deposit_min($payment_account->vip_rule_max_deposit_trans);

        // 4.1 Check for min deposit
        if ($amount < $deposit_min) {
            $this->utils->debug_log(__METHOD__, 'amount not within [min, max]', [ 'amount' => $amount, 'min' => $deposit_min, 'max' => $deposit_max ]);
            return $this->returnError(self::CODE_DEPOSIT1_AMOUNT_LESS_THAN_MIN);
        }

        // 4.2 Check for max deposit
        if ($deposit_max > 0 && $amount > $deposit_max) {
            $this->utils->debug_log(__METHOD__, 'amount not within [min, max]', [ 'amount' => $amount, 'min' => $deposit_min, 'max' => $deposit_max ]);
            return $this->returnError(self::CODE_DEPOSIT1_AMOUNT_BEYOND_MAX);
        }

        // 4.3 Check for daily deposit max (if set)
        $deposit_daily_max = $this->utils->getConfig('defaultMaxDepositDaily');

        if ($deposit_daily_max > 0) {
            $current_total = $this->transactions->sumDepositAmountToday($player_id);
            if (($current_total + $amount) >= $deposit_daily_max) {
                $this->utils->debug_log(__METHOD__, 'Total deposit today exceeds limit', [ 'current_total' => $current_total, 'this_amount' => $amount, 'limit' => $deposit_daily_max ]);
                return $this->returnError(self::CODE_DEPOSIT1_DAILY_DEPOSIT_AMOUNT_MAX_HIT);
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
            return $this->returnError(self::CODE_DEPOSIT1_DEPOSIT_FAILED);
        }

        $order = $deposit_res['order'];

        // 6: append int/ext notes
        $this->sale_orders_notes->add($int_notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::INTERNAL_NOTE, $order['id']);
        $this->sale_orders_notes->add($ext_notes, Users::SUPER_ADMIN_ID, Sale_orders_notes::EXTERNAL_NOTE, $order['id']);

        $add_prefix=true;
        $isLockFailed=false;
        $success=$this->player_model->lockAndTransForPlayerBalance($player_id, function ()
                use(&$approval_res, $order){
            // 7: approve the order
            $approval_res = $this->sale_order->approveSaleOrder($order['id']);
            return $approval_res;
        }, $add_prefix, $isLockFailed);

        if($isLockFailed){
            return $this->returnError(self::CODE_LOCK_FAILED);
        }

        if (!$approval_res || !$success) {
            return $this->returnError(self::CODE_DEPOSIT1_APPROVAL_FAILED);
        }

        // point of success --------
        $deposit_return = [
            'username'  => $username ,
            'amount'    => $amount_clear ,
            'secure_id' => $order['secure_id'] ,
        ];

        $this->utils->debug_log(__METHOD__, 'Success', $deposit_return);

        $this->returnSuccess($deposit_return);

        // }
        // catch (Exception $ex) {
        //     $this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
        //     $this->returnError($ex->getCode(), $ex->getMessage());
        // }
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

        $this->utils->debug_log('got user id', $this->admin_user_id);
        $detail=['pong'=>true, 'logged'=>!empty($this->admin_user_id)];

        return $this->returnSuccess($detail);
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

}
