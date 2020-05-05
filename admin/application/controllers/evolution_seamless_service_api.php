<?php

if(! defined("BASEPATH")){
    exit("No direct script access allowed");
}

require_once dirname(__FILE__) . "/BaseController.php";

class Evolution_seamless_service_api extends BaseController
{

    private $auth_token; # the authentication token of game provider when accessing our endpoint
    protected $red_rake_game_api; # the evolution game api class
    protected $current_method; # the current method executing
    protected $extraInfoAuthtoken; # the token in extra info
    protected $response_result_id;
    protected $evoRequestData; # this is the request data of evolution to us

    # API actions
    const ACTION_CHECK = "check";
    const ACTION_BALANCE = "balance";
    const ACTION_DEBIT = "debit";
    const ACTION_CREDIT = "credit";
    const ACTION_CANCEL = "cancel";
    const ACTION_SID = "sid"; # for testing purpose only
    const API_ACTIONS =[
        self::ACTION_CHECK,
        self::ACTION_BALANCE,
        self::ACTION_DEBIT,
        self::ACTION_CREDIT,
        self::ACTION_CANCEL
    ];
    # error codes constant
    const CODE_ERROR_METHOD_NOT_ALLOWED = 1;


    public function __construct()
    {
        parent::__construct();

        # load model
        $this->load->model(array(
            "wallet_model",
            "game_provider_auth",
            "common_token",
            "player_model",
            "evolution_seamless_thb1_wallet_transactions_model"
        ));

        $this->auth_token = $this->input->get("authToken") ?: null;
        $this->evolution_game_api = $this->utils->loadExternalSystemLibObject(EVOLUTION_SEAMLESS_THB1_API);
        $this->current_method = "index";
        $this->evoRequestData = new stdClass();

    }

    /**
     * This is the first Entry of all Request
     * 
     * @param string $method the request method of game provider
     */
    public function index($method)
    {
        # check if method is POST in the request
        if(! $this->isPostMethod()){
            return $this->showError("TEMPORARY_ERROR");
        }

        $data = $this->request();

        $extraInfoAuthtoken = $this->evolution_game_api->getSystemInfo("auth_token");
        
        if($this->isProviderAuthorize($extraInfoAuthtoken)){

            $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API provider trying to access method: ", $method);

            if(in_array((string) $method,self::API_ACTIONS)){

                $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API provider access method: ", $method);

                try{
                    # set current method executing
                    $this->current_method = $method;

                    # save response result
                    $response_result_id = $this->saveToResponseResult($this->current_method,$data);

                    $this->response_result_id = $response_result_id;

                    # for testing purposes only
                    if(self::ACTION_SID == $method){

                        return $this->check();
                    }

                    return $this->$method();

                }catch(\Exception $e){
                    return $this->showError($e->getMessage());
                }
                
            }else{
                # method is not exist
                return $this->showError("INVALID_PARAMETER");
            }
        }

        return $this->showError("INVALID_PARAMETER");
    }

    /** 
     * 
    */
    protected function check()
    {
        $dataObject = $this->request(false);
        $eObj = $this->evoRequestData->params = $dataObject;
        $token = $eObj->sid;

        $returnData = [
            "sid" => $token,
            "uuid" => $eObj->uuid
        ];

        if(property_exists($eObj,"sid")){

            if($this->istokenValid($token)){
                $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);
                $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
                $isPlayerExist = $this->isPlayerExistInProvider($playerId, $this->get_platform_code());

                if($isPlayerExist){

                    $returnData["status"] = "OK";

                }else{
                   $returnData["status"] = "INVALID_TOKEN_ID";
                }

                $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API ".__FUNCTION__." method with data: ",$returnData);

               return $this->outputData($returnData);
            }

            $returnData["status"] = "INVALID_SID";

            return $this->outputData($returnData);
        }

        $returnData["status"] = "INVALID_PARAMETER";

        return $this->outputData($returnData);
    }

    /** 
     * 
    */
    protected function balance()
    {
        $dataObject = $this->request(false);
        $eObj = $this->evoRequestData->params = $dataObject;
        $token = $eObj->sid;
        $eObj->eCurrentMethod = __FUNCTION__;

        if(property_exists($eObj,"sid")){

            if($this->istokenValid($token)){
                $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);
                $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
                $isPlayerExist = $this->isPlayerExistInProvider($playerId, $this->get_platform_code());

                if($isPlayerExist){

                    $eObj->eResponseStatusCode = "OK";

                    return $this->getStandardResponse($eObj);

                }else{

                    $eObj->eResponseStatusCode = "INVALID_TOKEN_ID";

                    return $this->getStandardResponse($eObj);
                }
            }

            $eObj->eResponseStatusCode = "INVALID_SID";

            return $this->getStandardResponse($eObj);
        }
        
        $eObj->eResponseStatusCode = "INVALID_PARAMETER";

        return $this->getStandardResponse($eObj);
    }

    /** 
     * 
    */
    protected function debit()
    {
        $dataObject = $this->request(false);
        $eObj = $this->evoRequestData->params = $dataObject;
        $token = $eObj->sid;
        $eObj->eCurrentMethod = __FUNCTION__;

        if($this->isParamComplete($eObj)){

            if(! $this->isTokenValid($token)){

                $eObj->eResponseStatusCode = "INVALID_SID";

                return $this->getStandardResponse($eObj);
            }

            $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo, $eObj); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){

                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["amount"],$tData["playerName"],$tData["playerId"],$tData["transactionId"],true);

                if(! is_null($isPlayerAllowedToBet)){

                    $eObj->eResponseStatusCode = $isPlayerAllowedToBet;

                    return $this->getStandardResponse($eObj);
                }

                # do the deduct transaction
                $eObj->eAction = __FUNCTION__;
                $is_success_trans = $this->doDeduct($tData["playerId"],$tData["amount"],$eObj);

                if($is_success_trans){

                  $eObj->eResponseStatusCode = "OK";

                  return $this->getStandardResponse($eObj);
                }

                $eObj->eResponseStatusCode = "TEMPORARY_ERROR";

                return $this->getStandardResponse($eObj);
            }else{

                $eObj->eResponseStatusCode = "INVALID_TOKEN_ID";

                return $this->getStandardResponse($eObj);
            }
        }

        $eObj->eResponseStatusCode = "INVALID_PARAMETER";

        return $this->getStandardResponse($eObj);
    }

    /** 
     * 
    */
    protected function credit()
    {
        $dataObject = $this->request(false);
        $eObj = $this->evoRequestData->params = $dataObject;
        $token = $eObj->sid;
        $eObj->eCurrentMethod = __FUNCTION__;

        if($this->isParamComplete($eObj)){

            if(! $this->isTokenValid($token)){

                $eObj->eResponseStatusCode = "INVALID_SID";

                return $this->getStandardResponse($eObj);
            }

            $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo, $eObj); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){

                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["amount"],$tData["playerName"],$tData["playerId"],$tData["transactionId"],false);

                if(! is_null($isPlayerAllowedToBet)){

                    $eObj->eResponseStatusCode = $isPlayerAllowedToBet;

                    return $this->getStandardResponse($eObj);
                }

                # do the increment transaction
                $eObj->eAction = __FUNCTION__;
                $is_success_trans = $this->doIncrement($tData["playerId"],$tData["amount"],$eObj);

                $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API ".__FUNCTION__." transaction is:",$is_success_trans);

                if($is_success_trans){

                    $eObj->eResponseStatusCode = "OK";

                    return $this->getStandardResponse($eObj);
                }

                $eObj->eResponseStatusCode = "TEMPORARY_ERROR";

                return $this->getStandardResponse($eObj);

            }else{
                $eObj->eResponseStatusCode = "INVALID_TOKEN_ID";

                return $this->getStandardResponse($eObj);
            }
        }

        $eObj->eResponseStatusCode = "INVALID_PARAMETER";

        return $this->getStandardResponse($eObj);
    }

    /**
     * 
     */
    protected function cancel()
    {
        $dataObject = $this->request(false);
        $eObj = $this->evoRequestData->params = $dataObject;
        $token = $eObj->sid;
        $eObj->eCurrentMethod = __FUNCTION__;

        if($this->isParamComplete($eObj)){

            if(! $this->isTokenValid($token)){

                $eObj->eResponseStatusCode = "INVALID_SID";

                return $this->getStandardResponse($eObj);
            }

            $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo, $eObj); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){

                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["amount"],$tData["playerName"],$tData["playerId"],$tData["transactionId"],false,false);

                if(! is_null($isPlayerAllowedToBet)){

                    $eObj->eResponseStatusCode = $isPlayerAllowedToBet;

                    return $this->getStandardResponse($eObj);
                }

                #check if transaction is alredy refunded
                $isAlreadyRefunded = $this->evolution_seamless_thb1_wallet_transactions_model->isAlreadyRefunded($tData["transactionId"]);

                if($isAlreadyRefunded){

                     $eObj->eResponseStatusCode = "BET_ALREADY_EXIST";

                    return $this->getStandardResponse($eObj);
                }

                $updatedRows = $this->evolution_seamless_thb1_wallet_transactions_model->updateRefundedTransaction($tData["transactionId"]);

                $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API ".__FUNCTION__." updated rows is: ",$updatedRows);

                if($updatedRows == 0){

                    $eObj->eResponseStatusCode = "BET_DOES_NOT_EXIST";

                    return $this->getStandardResponse($eObj);
                }

                if($updatedRows > 0){

                    # do the increment transaction
                    $transactionAmount = $this->evolution_seamless_thb1_wallet_transactions_model->getTransactionAmount($tData["transactionId"]);
                    $eObj->eAction = __FUNCTION__;
                    # set refundedTransactionId in cancel action
                    $eObj->eRefundedTransactionId = $tData["transactionId"];
                    # we concat transactionId and  transactionRefId because transactionId is unique in table and that is the transactionId of bet to be refunded
                    $eObj->transaction->id = $tData["transactionId"] . "-". $eObj->transaction->refId;
                    $is_success_trans = $this->doIncrement($tData["playerId"],$transactionAmount,$eObj);

                    if($is_success_trans){

                        $eObj->eResponseStatusCode = "OK";

                        return $this->getStandardResponse($eObj);
                    }
                }

                $eObj->eResponseStatusCode = "TEMPORARY_ERROR";

                return $this->getStandardResponse($eObj);

            }else{

                $eObj->eResponseStatusCode = "INVALID_TOKEN_ID";

                return $this->getStandardResponse($eObj);
            }

        }

        $eObj->eResponseStatusCode = "INVALID_PARAMETER";

        return $this->getStandardResponse($eObj);
    }


    /** 
     * Return error, with error code
     * 
     * @param int $code the code error
     * 
     * @return json
    */
    public function showError($code)
    {
        # update content to error code and error message, field when we received error
        $errorData = [
            "code" => $code
        ];

        if(! empty($this->response_result_id)){
            $this->db->where("id",$this->response_result_id)
                    ->update("response_results",["content" => json_encode($errorData),"flag"=>2]);
        }
       
        return $this->outputData([
            "status" => $code
        ]);
    }

    /**
     * Output Data in json header format
     * 
     * @param array $data the data to be outputed
     * @param int $response_result_id the response result id of the request
     * @param int $player_id the player id
     */
    private function outputData($data=[],$response_result_id=null,$player_id=null)
    {
        $output = json_encode((array) $data);

        if($response_result_id){
            $this->db->update(
                "response_results",[
                    "content" => $output,
                    "player_id" => $player_id,
                ],[
                    "id" => $response_result_id
                ]
            );
        }

        return $this->output->set_content_type("application/json")
                            ->set_output($output);
    }

    /**
     * Check if provider's auth token is match with our auth token in extra info
     * 
     * @param string $authToken
     * 
     * @return boolean it's true when authenticated otherwise false
     */
    private function isProviderAuthorize($authToken)
    {
        if(! empty($authToken) && $this->auth_token == $authToken){
            
            return true;
        }

        return false;
    }

    /**
     * Get Request Data
     * 
     * @param boolean $is_array
     * 
     * @return array
     */
    public function request($is_array=true)
    {
        $request = file_get_contents("php://input");

        if(! $this->isJson($request)){
            // TODO
            //return $this->getMessageFromStatusCode(8);
        }

        $request_array = json_decode($request,$is_array);

        return $request_array;
    }

    /** 
     * Detect if parameter is json data type
     * 
     * @param mixed the data to check if json or not
     * 
     * @return boolean
    */
    public function isJson($data = null)
    {
        $rlt = true;
        
        if(null === @json_decode($data)){
            $rlt = false;
        }
        
        return $rlt;
    }

    /** 
     * Save Request to Response result
     * 
     * @return int the last insert id
    */
    private function saveToResponseResult($request_method, $request_params = null,$extra=null,$fields=[])
    {
        $flag = Response_result::FLAG_NORMAL; # default to 1
        $data = is_array($request_params) ? json_encode($request_params) : $request_params;

        $lastInsertId = $this->CI->response_result->saveResponseResult(
            $this->get_platform_code(), #1
            $flag,#2
            $request_method,#3
            $data,#4
            "",#5
            200,#6
            null,#7
            $extra,#8
            $fields #9
        );

        return $lastInsertId;
    }

    /** 
     * Get game provider id
     * 
     * @return int the game provider id
    */
    public function get_platform_code(){

        return $this->evolution_game_api->getPlatformCode();
    }

    /** 
     * Check if token is valid
     * 
     * @param string $token the token to validate
     * 
     * @return boolean
    */
    protected function isTokenValid($token)
    {
        $playerInfo = $this->evolution_game_api->getPlayerInfoBytoken($token);

        if(empty($playerInfo)){
            return false;
        }

        return true;
    }

    /**
     * Check if player is exist in game provider
     * 
     * @param int $playerId
     * @param int $game_platform_id
     * 
     * @return boolean 
     */
    protected function isPlayerExistInProvider($playerId,$game_platform_id)
    {
        $login_name = $this->db->select("login_name")
                               ->from("game_provider_auth")
                               ->where("game_provider_id",$game_platform_id)
                               ->where("player_id",$playerId)
                               ->get()
                               ->result_array();

        if(! empty($login_name)){
            return true;
        }

        return false;
    }

    /**
     * Get Player Balance
     * 
     * @param int $playerId the player id
     * @return int
     */
    protected function getPlayerSubWalletBalance($playerId)
    {

        if(empty($playerId)){
            return $this->showError("TEMPORARY_ERROR");
        }

        $balance = $this->CI->player_model->getPlayerSubWalletBalance($playerId,$this->get_platform_code());

        return $balance;
    }

    /** 
     * Get Needed Data for the Transaction
     * 
     * @param array $playerInfo
     * @param object $eObj
     * 
     * @return array
    */
    private function getDataForTransaction($playerInfo,$eObj)
    {

        $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
        $before_balance = (! empty($this->getPlayerSubWalletBalance($playerId))) ? $this->getPlayerSubWalletBalance($playerId) : null;
        $amount = $eObj->transaction->amount;
        $playerName = isset($playerInfo["username"]) ? $playerInfo["username"] : null;
        $transactionId = $eObj->transaction->id;

        $returnData = [
            "playerId" => $playerId,
            "before_balance" => $before_balance,
            "amount" => $amount,
            "playerName" => $playerName,
            "playerId" => $playerId,
            "transactionId" => $transactionId
        ];

        return $returnData;
    }

    /** 
     * Check if Player is Allowed to bet
     * - if bet is greater than player balance
     * - if player is blocked
     * - check duplicate transaction ID
     * 
     * @param int $before_balance
     * @param int $betAmount
     * @param string $playerName
     * @param int $playerId
     * @param int $transactionId
     * @param boolean|false $is_bet
     * @param boolean|true $is_not_refund
     * 
     * @return mixed
    */
    public function isPlayerAllowedToBet($before_balance,$betAmount,$playerName,$playerId,$transactionId,$is_bet=false,$is_not_refund=true)
    {
        $isAllowedToBet = $this->utils->compareResultFloat($before_balance,">=",$betAmount); # check if current balance is greater than bet

        if(! $isAllowedToBet && $is_bet){
            return "INSUFFICIENT_FUNDS";
        }

        $isPlayerBlockedAll = $this->player_model->isBlocked($playerId); # block in player table

        if($this->evolution_game_api->isBlocked($playerName) || $isPlayerBlockedAll){
             return "ACCOUNT_LOCKED";
        }

        if($this->evolution_seamless_thb1_wallet_transactions_model->isTransactionAlreadyExist($transactionId) &&$is_not_refund){
            return "BET_ALREADY_EXIST";
        }
        
        return null;
    }

    /** 
     * Do Deduct to player sub wallet balance
     * 
     * @param int $playerId
     * @param  double $betAmount
     * @param object $eObj
     * 
     * @return boolean
    */
    private function doDeduct($playerId,$betAmount,$eObj){

        $controller = $this;

        $is_success_trans = $this->lockAndTransForPlayerBalance($playerId,function() use($controller,$playerId,$betAmount,$eObj){

            $before_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

            $isDeduct = $controller->wallet_model->decSubWallet($playerId, $controller->get_platform_code(),$betAmount);

            $controller->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API deduct to subwallet is: ",$isDeduct);

            if($isDeduct){

                $after_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

                $eObj->eAfterBalance = $after_balance;
                
                # insert in evolution wallet transaction table
                $controller->doInsertToGameTransactions($eObj,$before_balance,$after_balance);
            }

            return $isDeduct;

        });

        return $is_success_trans;
    }

    /** 
     * Do Increment to player sub wallet balance
     * 
     * @param int $playerId
     * @param  double $betAmount
     * @param object $eObj
     * 
     * @return boolean
    */
    private function doIncrement($playerId,$betAmount,$eObj){
        
        $controller = $this;
        
        $is_success_trans = $this->lockAndTransForPlayerBalance($playerId,function() use($controller,$playerId,$betAmount,$eObj){

            $before_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

            if($betAmount > 0){
                $isIncrement = $controller->wallet_model->incSubWallet($playerId, $controller->get_platform_code(),$betAmount);
            }else{
                $isIncrement = true;
            }

            $controller->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API increment to subwallet is: ",$isIncrement);

            if($isIncrement){

                $after_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

                $eObj->eAfterBalance = $after_balance;
                
                # insert in evolution wallet transaction table
                $controller->doInsertToGameTransactions($eObj,$before_balance,$after_balance);
            }

            return $isIncrement;

        });

        return $is_success_trans;
    }

    /** 
     * Get The Standard Response of Request
     * 
     * @param object $eObj
     * 
     * @return array
    */
    private function getStandardResponse($eObj)
    {
        $this->CI->load->model('game_provider_auth');
        $status = $eObj->eResponseStatusCode;
        $playerId = $this->CI->game_provider_auth->getPlayerIdByPlayerName($eObj->userId,$this->get_platform_code());
        $balance = property_exists($eObj,"eAfterBalance") ? $eObj->eAfterBalance : $this->getPlayerSubWalletBalance($playerId);
        $bonus = property_exists($eObj,"eBonus") ? $eObj->eBonus : null;
        $retransmission = property_exists($eObj,"eRetransmission") ? $eObj->eRetransmission : false;
        $uuid = property_exists($eObj,"uuid") ? $eObj->uuid : null;

        $response = [
            "status" => $status,
            "balance" => number_format($balance,2,".",""),
            "bonus" => number_format($bonus,2,".",""),
            "retransmission" => $retransmission,
            "uuid" => $uuid
        ];
        
        $rResponse = is_array($response) ? json_encode($response) : $response;

        $this->saveToResponseResult($eObj->eCurrentMethod,null,null,["content"=>$rResponse]);

        $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API ".$eObj->eCurrentMethod." method with data: ",$eObj);

        return $this->outputData($response);
    }

    /**
     * Is Parameter Complete in Response
     * 
     * @param Object $eObj
     * 
     * @return boolean
     */
    private function isParamComplete($eObj)
    {
        $isComplete = isset($eObj,
        $eObj->sid,
        $eObj->userId,
        $eObj->currency,
        $eObj->game,
        $eObj->game->id,
        $eObj->game->type,
        $eObj->game->details,
        $eObj->game->details->table,
        $eObj->game->details->table->id,
        $eObj->transaction,
        $eObj->transaction->id,
        $eObj->transaction->refId,
        $eObj->transaction->amount,
        $eObj->uuid
        );

        return $isComplete;
    }

        /** 
     * Insert data to red_rake_game_transactions table
     * 
     * @param object $data
     * @param int|null $before_balance
     * @param int|null $after_balance
     * 
     * @return void
     * 
    */
    public function doInsertToGameTransactions($data,$before_balance=null,$after_balance=null)
    {

        $action = isset($data,$data->eAction) ? $data->eAction : null;
        $sid = isset($data,$data->sid) ? $data->sid : null;
        $userId = isset($data,$data->userId) ? $data->userId : null;
        $channelType = isset($data,$data->channel->type) ? $data->channel->type : null;
        $uuid = isset($data,$data->uuid) ? $data->uuid : null;
        $currency = isset($data,$data->currency) ? $data->currency : null;
        $gameId = isset($data,$data->game->id) ? $data->game->id : null;
        $gameType = isset($data,$data->game->type) ? $data->game->type : null;
        $gameDetailsTableId = isset($data,$data->game->details->table->id) ? $data->game->details->table->id : null;
        $gameDetailsTableVid = isset($data,$data->game->details->table->vid) ? $data->game->details->table->vid : null;
        $transactionId = isset($data,$data->transaction->id) ? $data->transaction->id : null;
        $transactionRefId = isset($data,$data->transaction->refId) ? $data->transaction->refId : null;
        $transactionAmount = isset($data,$data->transaction->amount) ? $data->transaction->amount : null;
        $refundedTransactionId = isset($data,$data->eRefundedTransactionId) ? $data->eRefundedTransactionId : null;
        $refundedIn = isset($data,$data->eRefundedIn) ? $data->eRefundedIn : null;
        $beforeBalance = (! empty($before_balance)) ? $before_balance : null;
        $afterBalance = (! empty($after_balance)) ? $after_balance : null;
        $isBonus =  isset($data,$data->eIsBonus) ? $data->eIsBonus : null;
        $response_result_id = (! empty($this->response_result_id)) ? $this->response_result_id : null;
        $external_uniqueid = $transactionId;

        $insertData = [
            "action" => $action,
            "sid" => $sid,
            "userId" => $userId,
            "channelType" => $channelType,
            "uuid" => $uuid,
            "currency" => $currency,
            "gameId" => $gameId,
            "gameType" => $gameType,
            "gameDetailsTableId" => $gameDetailsTableId,
            "gameDetailsTableVid" => $gameDetailsTableVid,
            "transactionId" => $transactionId,
            "transactionRefId" => $transactionRefId,
            "transactionAmount" => $transactionAmount,
            "refundedTransactionId" => $refundedTransactionId,
            "refundedIn" => $refundedIn,
            "beforeBalance" => $beforeBalance,
            "afterBalance" => $afterBalance,
            "isBonus" => $isBonus,
            "response_result_id" => $response_result_id,
            "external_uniqueid" => $external_uniqueid,
            
        ];

        $this->insertTransactionWithLog($insertData);
    }

    /** 
     * Insert Transaction with debug Log
     * 
     * @param array $data
     * 
     * @return int
    */
    private function insertTransactionWithLog($data)
    {
        $affected_rows = $this->evolution_seamless_thb1_wallet_transactions_model->insertTransaction($data);

        $this->CI->utils->debug_log("EVOLUTION_SEAMLESS_THB1_API insert transaction count is: ",$affected_rows);

        return $affected_rows;
    }
}