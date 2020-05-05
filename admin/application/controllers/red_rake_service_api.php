<?php

if(! defined('BASEPATH')){
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

class Red_rake_service_api extends BaseController{
    
    # Actions of API
    const ACTION_ACCOUNT = "account";
    const ACTION_BALANCE = "balance";
    const ACTION_BET = "bet";
    const ACTION_WIN = "win";
    const ACTION_REFUND = "refund";
    const ACTION_BONUS_WIN = "bonusWin";
    const API_ACTIONS = [
        self::ACTION_ACCOUNT,
        self::ACTION_BALANCE,
        self::ACTION_BET,
        self::ACTION_WIN,
        self::ACTION_REFUND,
        self::ACTION_BONUS_WIN,
    ];
    # error codes constant
    const CODE_SUCCESS = 0;
    const CODE_ERROR_PARSING_DOCUMENT = 1;
    const CODE_ERROR_INVALID_CREDENTIALS = 2;
    const CODE_ERROR_TOKEN_INVALID = 3;
    const CODE_ERROR_USER_ACCOUNT = 4;
    const CODE_ERROR_USER_NO_MONEY_FOR_OPERATION = 5;
    const CODE_ERROR_TRANSACTION_ERROR = 6;
    const CODE_ERROR_METHOD_NOT_EXIST = 7;
    const CODE_ERROR_JSON_FORMAT = 8;
    const CODE_ERROR_METHOD_NOT_ALLOWED = 9;
    const CODE_ERROR_GETTING_PLAYER_BALANCE = 10;
    const CODE_ERROR_DUPLICATE_TRANSACTION_ID = 11;
    const CODE_ERROR_PLAYER_IS_BLOCKED = 12;
    const CODE_ERROR_REFUND_TRANSACTION_ID_NOT_EXIST = 13;
    const CODE_ERROR_CANNOT_REFUND_NON_BET_TRANSACTION = 14;
    const CODE_ERROR_TRANSACTION_IS_ALREADY_REFUNDED = 15;

    protected $red_rake_game_api; # the red rake game api class
    protected $username; # the username we provide to provider in order to access our API
    protected $password; # the password we provide to provider in order to access our API
    protected $current_method; # the current method executing
    protected $response_result_id;

    public function __construct()
    {
        parent::__construct();

        # load model
        $this->load->model([
            "wallet_model",
            "game_provider_auth",
            "common_token",
            "player_model",
            "red_rake_game_transactions_model",
            "red_rake_model"
        ]);

        $this->red_rake_game_api = $this->utils->loadExternalSystemLibObject(REDRAKE_GAMING_API);
        $this->username = $this->red_rake_game_api->getSystemInfo("username","");
        $this->password = $this->red_rake_game_api->getSystemInfo("password","");
        $this->current_method = "index";
    }

    /** 
     * This is the first Entry of all Request
    */
    public function index()
    {
        # check if method is POST in the request
        if(! $this->isPostMethod()){
            return $this->showError(self::CODE_ERROR_METHOD_NOT_ALLOWED,$this->getMessageFromStatusCode(self::CODE_ERROR_METHOD_NOT_ALLOWED));
        }

        $data = $this->request();
        if($this->isProviderAuthorize($data)){
            $method = $this->petitionMethod($data);
            $this->CI->utils->debug_log("REDRAKE_GAMING_API provider trying to access method: ", $method);
            
            # save response result
            $response_result_id = $this->saveToResponseResult($method,$data);

            $this->response_result_id = $response_result_id;

            if(in_array((string) $method,self::API_ACTIONS)){

                $this->CI->utils->debug_log("REDRAKE_GAMING_API provider access method: ", $method);

                try{
                    # set current method executing
                    $this->current_method = $method;

                    return $this->$method($data);
                }catch(\Exception $e){
                    return $this->showError($e->getCode(),$e->getMessage());
                }
                
            }else{
                # method is not exist
                return $this->showError(self::CODE_ERROR_METHOD_NOT_EXIST,$this->getMessageFromStatusCode(self::CODE_ERROR_METHOD_NOT_EXIST));
            }
        }

        return $this->showError(self::CODE_ERROR_INVALID_CREDENTIALS,$this->getMessageFromStatusCode(self::CODE_ERROR_INVALID_CREDENTIALS));
    }

    /**
     * This petition will be sent when the user starts a game session. Expected
     *  response from the wallet platform contains the unique user account ID and the
     *  currency used
     * 
     * @param array the payload request
     * 
     * @return json
     */
    private function account($data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];

            if($this->isTokenValid($token)){

                $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
                $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
                $username = isset($playerInfo["username"]) ? $playerInfo["username"] : null;
                $gameName = $this->red_rake_game_api->getGameUsernameByPlayerUsername($username) ?: "";
                $isPlayerExist = $this->isPlayerExistInProvider($playerId, $this->get_platform_code());
                $balance = $this->getPlayerSubWalletBalance($playerId);

                if($isPlayerExist){
                    $returnData["status"] = [
                        "code" => self::CODE_SUCCESS,
                        "msg" => $this->getMessageFromStatusCode(self::CODE_SUCCESS),
                    ];

                    $returnData["response"] = [
                        "accountid" => $playerId,
                        "username" => $gameName,
                        "currency" => $this->red_rake_game_api->currency_type,
                        "balance" => number_format($balance,2,".","")
                    ];
                    
                }else{
                    $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in account method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                    return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
               }
                

               $this->CI->utils->debug_log("REDRAKE_GAMING_API account method with data: ",$returnData);

               return $this->outputData($returnData);
            }
            
            return $this->showError(self::CODE_ERROR_TOKEN_INVALID,$this->getMessageFromStatusCode(self::CODE_ERROR_TOKEN_INVALID));
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /** 
     * This action will be invoked by the game server to get an update of the player's balance
     * 
     * @param array $data the payload request
     * 
     * @return json
    */
    private function balance($data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];

            return $this->getBalanceOnly($token,$this->current_method);
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /** 
     * This petition will be invoked by the game server every time the player makes a bet
     * 
     * @param array $data the payload request
     * 
     * @return json
    */
    private function bet($data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];
            $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo,$data); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());
            
            if($isPlayerExist){
                
                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["betAmount"],$tData["playerName"],$tData["playerId"],$tData["transaction_id"],true);

                if(! is_null($isPlayerAllowedToBet)){
                    return $this->showError($isPlayerAllowedToBet,$this->getMessageFromStatusCode($isPlayerAllowedToBet));
                }

                # do the deduct transaction
                $is_success_trans = $this->doDeduct($tData["playerId"],$tData["betAmount"],$data);

                $this->CI->utils->debug_log("REDRAKE_GAMING_API bet transaction is:",$is_success_trans);
                
                if($is_success_trans){
                    return $this->getBalanceOnly($token,$this->current_method);
                }

                return $this->showError(self::CODE_ERROR_TRANSACTION_ERROR,$this->getMessageFromStatusCode(self::CODE_ERROR_TRANSACTION_ERROR));

            }else{
                $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in bet method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
            }
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /**
     * This petition will be invoked by the game server every time it has determined to award the player a winning
     * 
     * @param array $data the payload request
     * 
     * @return json
     */
    private function win(&$data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];
            $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo,$data); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){
                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["betAmount"],$tData["playerName"],$tData["playerId"],$tData["transaction_id"],false);

                if(! is_null($isPlayerAllowedToBet)){
                    return $this->showError($isPlayerAllowedToBet,$this->getMessageFromStatusCode($isPlayerAllowedToBet));
                }

                # if amount is < 1 meaning, it is a bet but loss
                if($tData["betAmount"] < 1){
                    # we update is_bet_loss column to 1, means it is a bet but loss
                    $data["is_bet_loss"] = 1;
                }

                # do the increment transaction
                $is_success_trans = $this->doIncrement($tData["playerId"],$tData["betAmount"],$data);

                $this->CI->utils->debug_log("REDRAKE_GAMING_API win transaction is:",$is_success_trans);
                
                if($is_success_trans){
                    return $this->getBalanceOnly($token,$this->current_method);
                }

                return $this->showError(self::CODE_ERROR_TRANSACTION_ERROR,$this->getMessageFromStatusCode(self::CODE_ERROR_TRANSACTION_ERROR));
            }else{
                $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in win method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
            }
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /** 
     * This petition will be invoked by the game server to invalidate a bet. Only those
     * rounds that still don't have accepted winnings are able to be refunded
     * 
     * @param array $data the payload request
     * 
     * @return json
    */
    private function refund($data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];
            $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo,$data); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){
                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["betAmount"],$tData["playerName"],$tData["playerId"],$tData["transaction_id"],false,false);

                if(! is_null($isPlayerAllowedToBet)){
                    return $this->showError($isPlayerAllowedToBet,$this->getMessageFromStatusCode($isPlayerAllowedToBet));
                }

                # check if action is not bet
                $action = $this->red_rake_game_transactions_model->getActionType($tData["refunded_transaction_id"]);
                if($action != "bet"){
                return $this->showError(self::CODE_ERROR_CANNOT_REFUND_NON_BET_TRANSACTION,$this->getMessageFromStatusCode(self::CODE_ERROR_CANNOT_REFUND_NON_BET_TRANSACTION));
                }
                
                #check if transaction is alredy refunded
                $isAlreadyRefunded = $this->red_rake_game_transactions_model->isAlreadyRefunded($tData["refunded_transaction_id"]);
                if($isAlreadyRefunded){
                return $this->showError(self::CODE_ERROR_TRANSACTION_IS_ALREADY_REFUNDED,$this->getMessageFromStatusCode(self::CODE_ERROR_TRANSACTION_IS_ALREADY_REFUNDED));
                }
                
                $updatedRows = $this->red_rake_game_transactions_model->updateRefundedTransaction($tData["refunded_transaction_id"]);

                $this->CI->utils->debug_log("REDRAKE_GAMING_API refund updated rows is: ",$updatedRows);

                if($updatedRows == 0){
                    return $this->showError(self::CODE_ERROR_REFUND_TRANSACTION_ID_NOT_EXIST,$this->getMessageFromStatusCode(self::CODE_ERROR_REFUND_TRANSACTION_ID_NOT_EXIST));
                }

                if($updatedRows > 0){

                     # include refunded transaction id
                     $data ["refunded_transaction_id"] = $tData["refunded_transaction_id"];
                    
                     # do the increment transaction
                    $is_success_trans = $this->doIncrement($tData["playerId"],$tData["betAmount"],$data);

                    $this->CI->utils->debug_log("REDRAKE_GAMING_API refund transaction is:",$is_success_trans);
                    
                    if($is_success_trans){
                        return $this->getBalanceOnly($token,$this->current_method);
                    }
                }

                return $this->showError(self::CODE_ERROR_TRANSACTION_ERROR,$this->getMessageFromStatusCode(self::CODE_ERROR_TRANSACTION_ERROR));

            }else{
                $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in bet method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
            }
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /**
     * This petition will be invoked by the game server once for each win got during a
     * Free Rounds Bonus
     * 
     * @param array $data the payload request
     * 
     * @return json
     */
    private function bonusWin(&$data)
    {
        if($this->isParamExist($data,["token"])){

            $token = $data["request"]["token"];
            $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
            $tData = $this->getDataForTransaction($playerInfo,$data); # the transaction Data
            $isPlayerExist = $this->isPlayerExistInProvider($tData["playerId"], $this->get_platform_code());

            if($isPlayerExist){
                # player checkpoint for validity
                $isPlayerAllowedToBet = $this->isPlayerAllowedToBet($tData["before_balance"],$tData["betAmount"],$tData["playerName"],$tData["playerId"],$tData["transaction_id"],false);

                if(! is_null($isPlayerAllowedToBet)){
                    return $this->showError($isPlayerAllowedToBet,$this->getMessageFromStatusCode($isPlayerAllowedToBet));
                }

                # add bonus_id field
                if(! is_null($tData["bonusid"])){
                    $data["bonus_id"] = $tData["bonusid"];
                }

                # if amount is < 1 meaning, it is a bonus win but loss
                if($tData["betAmount"] < 1){
                    # we update is_bonus_win column to 1, means it is a bonus win but loss
                    $data["is_bonus_win"] = 1;
                }

                # do the increment transaction
                $is_success_trans = $this->doIncrement($tData["playerId"],$tData["betAmount"],$data);

                $this->CI->utils->debug_log("REDRAKE_GAMING_API bonusWin transaction is:",$is_success_trans);
                
                if($is_success_trans){
                    return $this->getBalanceOnly($token,$this->current_method);
                }

                return $this->showError(self::CODE_ERROR_TRANSACTION_ERROR,$this->getMessageFromStatusCode(self::CODE_ERROR_TRANSACTION_ERROR));
            }else{
                $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in bonusWin method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
            }
        }

        return $this->showError(self::CODE_ERROR_PARSING_DOCUMENT,$this->getMessageFromStatusCode(self::CODE_ERROR_PARSING_DOCUMENT));
    }

    /** 
     * Get Needed Data for the Transaction
     * 
     * @param array $playerInfo
     * @param array $data
     * 
     * @return array
    */
    private function getDataForTransaction($playerInfo,$data)
    {

        $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
        $before_balance = (! empty($this->getPlayerSubWalletBalance($playerId))) ? $this->getPlayerSubWalletBalance($playerId) : null;
        $playerName = isset($playerInfo["username"]) ? $playerInfo["username"] : null;
        $paramsData = isset($data["params"]) ? $data["params"] : null;
        $betAmount = (! is_null($paramsData) && isset($paramsData["amount"])) ? $paramsData["amount"] : null;
        $action = (! empty($this->current_method)) ? $this->current_method : null;
        $bonus_id = isset($data["params"]["bonusid"]) ? $data["params"]["bonusid"] : null;
        $transaction_id = isset($paramsData["transactionid"]) ? $paramsData["transactionid"] : null;
        $refunded_transaction_id = isset($paramsData["refundedtransactionid"]) ? $paramsData["refundedtransactionid"] : null;

        $returnData = [
            "playerId" => $playerId,
            "before_balance" => $before_balance,
            "playerName" => $playerName,
            "paramsData" => $paramsData,
            "betAmount" => $betAmount,
            "action" => $action,
            "bonusid" => $bonus_id,
            "transaction_id" => $transaction_id,
            "refunded_transaction_id" => $refunded_transaction_id
        ];

        return $returnData;
    }

    /** 
     * Do Deduct to player sub wallet balance
     * 
     * @param int $playerId
     * @param  $controller
     * @param int $betAmount
     * @param array $data
     * @param int $before_balance
     * 
     * @return boolean
    */
    private function doDeduct($playerId,$betAmount,$data){

        $controller = $this;

        $is_success_trans = $this->lockAndTransForPlayerBalance($playerId,function() use($controller,$playerId,$betAmount,$data){

            $before_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

            $isDeduct = $controller->wallet_model->decSubWallet($playerId, $controller->get_platform_code(),$betAmount);

            $controller->CI->utils->debug_log("REDRAKE_GAMING_API deduct to subwallet is: ",$isDeduct);

            if($isDeduct){

                $after_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;
                
                # insert in red_rake_game_transactions table
                $controller->doInsertToGameTransactions($data,$before_balance,$after_balance);
            }

            return $isDeduct;

        });

        return $is_success_trans;
    }

    /** 
     * Do Increment to player sub wallet balance
     * 
     * @param int $playerId
     * @param  $controller
     * @param int $betAmount
     * @param array $data
     * @param int $before_balance
     * 
     * @return boolean
    */
    private function doIncrement($playerId,$betAmount,$data){
        
        $controller = $this;
        
        $is_success_trans = $this->lockAndTransForPlayerBalance($playerId,function() use($controller,$playerId,$betAmount,$data){

            $before_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;

            if($betAmount > 0){
                $isIncrement = $controller->wallet_model->incSubWallet($playerId, $controller->get_platform_code(),$betAmount);
            }else{
                $isIncrement = true;
            }

            $controller->CI->utils->debug_log("REDRAKE_GAMING_API increment to subwallet is: ",$isIncrement);

            if($isIncrement){

                $after_balance = (! empty($controller->getPlayerSubWalletBalance($playerId))) ? $controller->getPlayerSubWalletBalance($playerId) : null;
                
                # insert in red_rake_game_transactions table
                $controller->doInsertToGameTransactions($data,$before_balance,$after_balance);
            }

            return $isIncrement;

        });

        return $is_success_trans;
    }

    /** 
     * Insert data to red_rake_game_transactions table
     * 
     * @param array $data
     * @param int|null $before_balance
     * @param int|null $after_balance
     * 
     * @return void
     * 
    */
    public function doInsertToGameTransactions($data,$before_balance=null,$after_balance=null)
    {

        $paramsData = isset($data["params"]) ? $data["params"] : null;
        $requestData = isset($data["request"]) ? $data["request"] : null;
        $token = isset($requestData["token"]) ? $requestData["token"] : null;
        $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
        $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
        $action = (! empty($this->current_method)) ? $this->current_method : null;
        $game_id = isset($requestData["gameid"]) ? $requestData["gameid"] : null;
        $currency = (! empty($this->red_rake_game_api->currency_type)) ? $this->red_rake_game_api->currency_type : null;
        $round_id = isset($paramsData["roundid"]) ? $paramsData["roundid"] : null;
        $transaction_id = isset($paramsData["transactionid"]) ? $paramsData["transactionid"] : null;
        $refunded_transaction_id = isset($data["refunded_transaction_id"]) ? $data["refunded_transaction_id"] : null;
        $response_result_id = (! empty($this->response_result_id)) ? $this->response_result_id : null;
        $betAmount = isset($paramsData["amount"]) ? $paramsData["amount"] : null;
        $refunded_in = isset($data["refunded_in"]) ? $data["refunded_in"] : null;
        $before_balance = (! empty($before_balance)) ? $before_balance : null;
        $after_balance = (! empty($after_balance)) ? $after_balance : null;
        $is_bet_loss = isset($data["is_bet_loss"]) ? $data["is_bet_loss"] : null;
        $bonus_id = isset($data["bonus_id"]) ? $data["bonus_id"] : null;
        $is_bonus_win = isset($data["is_bonus_win"]) ? $data["is_bonus_win"] : null;

        $insertData = [
            "action" => $action,
            "token" => $token,
            "game_id" => $game_id,
            "player_id" => $playerId,
            "currency" => $currency,
            "round_id" => $round_id,
            "transaction_id" => $transaction_id,
            "refunded_transaction_id" => $refunded_transaction_id,
            "amount" => $betAmount,
            "timestamp" => $this->utils->getNowForMysql(),
            "refunded_in" => $refunded_in,
            "before_balance" => $before_balance,
            "after_balance" => $after_balance,
            "response_result_id" => $response_result_id,
            "external_uniqueid" => $transaction_id . $round_id,
            "is_bet_loss" => $is_bet_loss,
            "bonus_id" => $bonus_id,
            "is_bonus_win" => $is_bonus_win
        ];

        $this->insertTransactionWithLog($insertData);
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
            return self::CODE_ERROR_USER_NO_MONEY_FOR_OPERATION;
        }

        $isPlayerBlockedAll = $this->player_model->isBlocked($playerId); # block in player table

        if($this->red_rake_game_api->isBlocked($playerName) || $isPlayerBlockedAll){
            return self::CODE_ERROR_PLAYER_IS_BLOCKED;
        }

        if($this->red_rake_game_transactions_model->isTransactionAlreadyExist($transactionId) && $is_not_refund){
            return self::CODE_ERROR_DUPLICATE_TRANSACTION_ID;
        }
        
        return null;
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
        $affected_rows = $this->red_rake_game_transactions_model->insertTransaction($data);

        $this->CI->utils->debug_log("REDRAKE_GAMING_API insert transaction count is: ",$affected_rows);

        return $affected_rows;
    }

    /**
     * Status Code of Petition from the game server
     * 
     * @param int $code the status code
     * 
     * @return string $message the message based in status code
     */
    protected function getMessageFromStatusCode($code)
    {
        switch($code){
            case 0:
                return "The operation has been properly executed";
                break;
            case 1:
                return "Error found when parsing the message document";
                break;
            case 2:
                return "Invalid service access credentials";
                break;
            case 3:
                return "Supplied token is no longer valid or incorrect";
                break;
            case 4:
                return "Invalid user account";
                break;
            case 5:
                return "User has not enough money to execute the operation";
                break;
            case 6:
                return "Transaction error";
                break;
            case 7:
                return "Petition Method not exist";
                break;
            case 8:
                return "Request is not in JSON format";
                break;
            case 9:
                return "Method not Allowed";
                break;
            case 10:
                return "Cannot Get the Balance of Player";
                break;
            case 11:
                return "Duplicate Transaction ID";
                break;
            case 12:
                return "Player is Blocked";
                break;
            case 13:
                return "Refund Transaction ID not found";
                break;
            case 14:
                return "Cannot Refund non Bet Transaction";
                break;
            case 15:
                return "Transaction is already refunded";
                break;
            default:
                return "Unknown Code";
        }
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
     * Check if provider's username and password is correct
     * 
     * @param string $username the username we provide to game provider to access our API endpoint fir their game server petition
     * @param string $password the password we provide to game provider to access our API endpoint for their game server petition
     * 
     * @return boolean it's true when authenticated otherwise false
     */
    private function isProviderAuthorize($request)
    {
       if($this->isParamExist($request,["user","password"])){
            if($this->username ==  $request["request"]["user"] && $this->password == $request["request"]["password"]){
                return true;
            }
        return false;
       }
    }

    /** 
     * Check if parameter exist in the payload request, if params to be check is multiple, it will return false if one param is not exist
     * 
     * @param array $request the request payload
     * @param array $params the params to be check
     * @param string|request the key in the array where to check
     * 
     * @return boolean
    */
    public function isParamExist($request,$params,$key="request")
    {
        if(isset($request[$key])){
            if(is_array($params) && count($params) == 0){

                $is_exist = array_key_exists($params[0],$request["request"]);

                return $is_exist;

            }else{

                foreach($params as $value){
                    if(! array_key_exists($value,$request["request"])){
                       return false;
                    }
                }

                return true;
            }

        }

        return false;
    }

    /** 
     * Return error, with error code and message
     * 
     * @param int $code the code error
     * @param string $message the error message based in code error
     * 
     * @return json
    */
    public function showError($code,$message)
    {
        # update content to error code and error message, field when we received error
        $errorData = [
            "code" => $code,
            "message" => $message
        ];

        if(! empty($this->response_result_id)){
            $this->db->where("id",$this->response_result_id)
                    ->update("response_results",["content" => json_encode($errorData),"flag"=>2]);
        }
       
        return $this->outputData([
            "status" => [
                "code" => $code,
                "msg" => $message
            ]
        ]);
    }

    /**
     * Return the method of game server petition of game provider
     * 
     * @param json the request where we can extract action field as petition method
     * 
     * @return string the method petition of game server
     */
    private function petitionMethod($request)
    {    
        if($this->isParamExist($request,["action"])){
            $action = $request["request"]["action"];

            return $action;
        }
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
     * Get Request Data
     * 
     * @return array
     */
    public function request()
    {
        $request = file_get_contents("php://input");

        if(! $this->isJson($request)){
            return $this->getMessageFromStatusCode(8);
        }

        $request_array = json_decode($request,true);

        return $request_array;
    }

    /** 
     * Save Request to Response result
     * 
     * @return int the last insert id
    */
    private function saveToResponseResult($request_method, $request_params = NULL)
    {

		$this->db->insert('response_results', array(
			'system_type_id' => $this->get_platform_code(),
			'request_api' => $request_method,
			'request_params' => is_array($request_params) ? json_encode($request_params) : $request_params,
			'created_at' => date('Y-m-d H:i:s'),
		));

		return $this->db->insert_id();
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
        $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);

        if(empty($playerInfo)){
            return false;
        }

        return true;
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
            return $this->showError(self::CODE_ERROR_GETTING_PLAYER_BALANCE,$this->getMessageFromStatusCode(self::CODE_ERROR_GETTING_PLAYER_BALANCE));
        }

        $balance = $this->CI->player_model->getPlayerSubWalletBalance($playerId,$this->get_platform_code());

        return $balance;
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
     * Get game provider id
     * 
     * @return int the game provider id
    */
    public function get_platform_code(){

        return $this->red_rake_game_api->getPlatformCode();
    }

    /** 
     * Get only the current balance of player based in token
     * 
     * @param string $token the current token of player
     * @param string|index $method the current method executing
     * 
     * @return json
    */
    protected function  getBalanceOnly($token,$method="index")
    {
        if($this->isTokenValid($token)){

            $playerInfo = $this->red_rake_game_api->getPlayerInfoBytoken($token);
            $playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
            $isPlayerExist = $this->isPlayerExistInProvider($playerId, $this->get_platform_code());
            $balance = $this->getPlayerSubWalletBalance($playerId);

            if($isPlayerExist){
                $returnData["status"] = [
                    "code" => self::CODE_SUCCESS,
                    "msg" => $this->getMessageFromStatusCode(self::CODE_SUCCESS),
                ];

                $returnData["response"] = [
                    "balance" => number_format($balance,2,".","")
                ];
                
            }else{
                $this->CI->utils->debug_log("REDRAKE_GAMING_API ERROR in {$method} method with status message of: ",$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));

                return $this->showError(self::CODE_ERROR_USER_ACCOUNT,$this->getMessageFromStatusCode(self::CODE_ERROR_USER_ACCOUNT));
           }
            

           $this->CI->utils->debug_log("REDRAKE_GAMING_API {$method} method with data: ",$returnData);

           return $this->outputData($returnData);
        }

        return $this->showError(self::CODE_ERROR_TOKEN_INVALID,$this->getMessageFromStatusCode(self::CODE_ERROR_TOKEN_INVALID));
    }


}
/** END OF FILE */