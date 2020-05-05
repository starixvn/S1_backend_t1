<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Sbobet_service_api extends BaseController {

    const APICALL_GETBALANCE = "GetBalance";
    const APICALL_DEDUCT = "Deduct";
    const APICALL_SETTLE = "Settle";
    const APICALL_ROLLBACK = "Rollback";
    const APICALL_CANCEL = "Cancel";
    const APICALL_TIP = "Tip";
    const APICALL_BONUS = "Bonus";
    const APICALL_GETBETSTATUS = "GetBetStatus";

    const TRANSACTION_TYPE_DEDUCT = "DeductDeduct";
    const TRANSACTION_TYPE_ADDITIONAL_DEDUCT = "AdditionalDeduct";
    const TRANSACTION_TYPE_ADDITIONAL_INCREASE = "AdditionalAdd";
    const TRANSACTION_TYPE_SETTLE_ADD = "SettleAdd";
    const TRANSACTION_TYPE_SETTLE_DEDUCT = "SettleDeduct";
    const TRANSACTION_TYPE_ROLLBACK_DEDUCT = "RollbackDeduct";
    const TRANSACTION_TYPE_CANCEL_DEDUCT = "CancelDeduct";
    const TRANSACTION_TYPE_TIP_DEDUCT = "TipDeduct";
    const TRANSACTION_TYPE_BONUS_ADD = "BonusAdd";
    const TRANSACTION_TYPE_CANCEL_ADD = "CancelAdd";
    
    const BET_STATUS_SETTLED = "settled";
    const BET_STATUS_CANCELLED = "void";
    const BET_STATUS_RUNNING = "running";

	# error codes constant
    const CODE_SUCCESS = 0;
    const CODE_ERROR_PLAYER_DOES_NOT_EXISTS = 1;
    const CODE_ERROR_METHOD_NOT_ALLOWED = 3;
    const CODE_ERROR_WRONG_COMPANY_KEY = 4;
    const CODE_ERROR_USER_NOT_ENOUGH_BALANCE = 5;
    const CODE_ERROR_BET_DOES_NOT_EXISTS = 6;
    const CODE_ERROR_INTERNAL_ERROR = 7;
    const CODE_ERROR_DUPLICATE_TRANSFER_CODE = 8;
    const CODE_ROLLBACK_EXISTS = 9;
    const CODE_ERROR_WRONG_JSON_FORMAT = 10;
    const CODE_ERROR_PLAYER_IS_BLOCKED = 11;
    const CODE_ERROR_BET_IS_CANCELLED_ALREADY = 12;
    const CODE_ERROR_TIP_ALREADY_EXISTS = 13;
    const CODE_ERROR_BONUS_ALREADY_EXISTS = 14;

    const API_MSG = [
						self::CODE_SUCCESS => "No Error!",
						self::CODE_ERROR_WRONG_COMPANY_KEY => "Wrong Company Key!",
						self::CODE_ERROR_WRONG_JSON_FORMAT => "Wrong JSON Format!",
						self::CODE_ERROR_PLAYER_DOES_NOT_EXISTS => "Player Does Not Exists!",
						self::CODE_ERROR_USER_NOT_ENOUGH_BALANCE => "Not Enough Balance!",
						self::CODE_ERROR_DUPLICATE_TRANSFER_CODE => "Duplicate Transfer Code!",
						self::CODE_ROLLBACK_EXISTS => "Rollback Already Exists!",
						self::CODE_ERROR_PLAYER_IS_BLOCKED => "Player is blocked!",
						self::CODE_ERROR_BET_DOES_NOT_EXISTS => "Bet Does not exists!",
						self::CODE_ERROR_METHOD_NOT_ALLOWED => "Method Does Not Allowed!",
						self::CODE_ERROR_INTERNAL_ERROR => "Internal Error!",
						self::CODE_ERROR_BET_IS_CANCELLED_ALREADY => "Bet is cancelled already!",
						self::CODE_ERROR_TIP_ALREADY_EXISTS => "Tip exists already!",
						self::CODE_ERROR_BONUS_ALREADY_EXISTS => "Bonus exists already!",
				    ];

	const PRODUCT_TYPE_SPORTSBOOK = 1;
	const PRODUCT_TYPE_SBOGAMES = 3;
	const PRODUCT_TYPE_LIVECASINO = 7;
	const PRODUCT_TYPE_SLOTS = 9;

	const GAME_TYPE_SPORTSBOOK = [1];
	const GAME_TYPE_RNGLIVECASINO = [201,203,204,205,207,208,511,513,514,515,517,518];
	const GAME_TYPE_VIRTUAL_SPORTS = [200000,202601,201601,202602,201604];
	const GAME_TYPE_LIVECASINO = [1,3,4,5,7,9];
	const GAME_TYPE_WANMEI = [1000101,1000102,1000103,1000104,1000105,
							  1000106,1000107,1000108,1000109,1000110,
							  1000111];

	const RESULT_TYPE_WON = 0;
	const RESULT_TYPE_LOSS = 1;
	const RESULT_TYPE_TIE = 2;
	
	function __construct() {
		parent::__construct();
		$this->load->model(['wallet_model',
							'game_provider_auth',
							'common_token',
							'player_model',
							'sbobet_seamless_game_transactions_model',
							'sbobet_seamless_game_logs_model',
						  ]);
		$gamePlatformId = SBOBET_SEAMLESS_GAME_API;
		$this->game_api_sbo_seamless = $this->utils->loadExternalSystemLibObject($gamePlatformId);
		$this->gamePlatformId = $gamePlatformId;
	}

	/*
	 *	We will use this API to get member's balance.
	 */
	public function getBalance()
	{
		$result = $this->processRequest(self::APICALL_GETBALANCE);
		$gameUsername = $result['gameUsername'];
		$apiResult = $this->game_api_sbo_seamless->queryPlayerBalance($gameUsername);
		$balance = $result ? floatval($apiResult['balance']) : 0;

		$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $balance]));
		return;
	}

	/* 
	 * In Sports, the same transferCode can't Deduct twice.
	 * In VirtualSports, the same transferCode can't Deduct twice.
	 *
	 * In Casino, the same transferCode can Deduct twice, 
	 * but 2nd Deduct amount must be greater than 1st Deduct.
	 * 
	 * In 3rd Wan Mei, the same transferCode and same transactionId can't Deduct twice, 
	 * but same transferCode can Deduct another transactionId.
	 */ 
	public function deduct()
	{
		$result = $this->processRequest(self::APICALL_DEDUCT);
		$playerId = $result['playerId'];
		$transferCode = $result['transferCode'];
		$transactionId = $result['transactionId'];
		$gameUsername = $result['gameUsername'];
		$productType = $result['productType'];
    	$gameType = $result['gameType'];
    	$betAmount = $result['amount'];

    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

		$beforeBalance = $this->getPlayerWalletBalance($playerId);
        $hasEnoughBalance = $this->utils->compareResultFloat($beforeBalance,">=",$betAmount);
        if(!$hasEnoughBalance){
            $this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_USER_NOT_ENOUGH_BALANCE,["beforeBalance"=>$beforeBalance,"betAmount"=>$betAmount]));
            return;
        }

        # block in player table
        $isPlayerBlockedAll = $this->player_model->isBlocked($playerId);
        if($isPlayerBlockedAll){
            $this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_PLAYER_IS_BLOCKED));
            return;
        }

        if($this->sbobet_seamless_game_logs_model->isExternalUniqueIdAlreadyExist($externalUniqueId))
        {
        	# Rule for existing transfer code:
        	# -if product type and game type is sports or if game type is virtual sports, 
        	#  do not allow duplicate transfer code
        	if($productType == self::PRODUCT_TYPE_SPORTSBOOK || in_array($gameType,self::GAME_TYPE_VIRTUAL_SPORTS)){
        		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_DUPLICATE_TRANSFER_CODE));
            	return;
        	}

        	# -In Casino, the same transferCode can Deduct twice, 
        	#  but 2nd Deduct amount must be greater than 1st Deduct.
        	if(($productType == self::PRODUCT_TYPE_LIVECASINO && in_array($gameType,self::GAME_TYPE_LIVECASINO)) || ($productType == self::PRODUCT_TYPE_SBOGAMES && in_array($gameType,self::GAME_TYPE_RNGLIVECASINO))){

        		$previousBetAmount = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);

        		# do only if bet amount is greater than previuous
        		if($betAmount > $previousBetAmount)
        		{
        			$additionalDeductAmt = $betAmount - $previousBetAmount;
        			$isAdditionalDeduct = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$additionalDeductAmt);
        			$afterBalance = $this->getPlayerWalletBalance($playerId);

        			if($isAdditionalDeduct){
        				# Update the previuos bet amount in game logs, 
        				# before balance and after balance
	        			$updateData = [
	        							"bet_amount" => $betAmount,
	        							"updated_at" => $this->utils->getNowForMysql(),
	        						  ];
	        			$this->sbobet_seamless_game_logs_model->updateBetAmountByExternalUniqueId($externalUniqueId,$updateData);

	        			$insertData = [
				            "transaction_type" => self::TRANSACTION_TYPE_ADDITIONAL_DEDUCT,
				            "gameusername" => $gameUsername,
				            "game_type" => $gameType,
				            "product_type" => $productType,
				            "transfer_code" => $transferCode,
				            "transaction_id" => $transactionId,
				            "amount" => $additionalDeductAmt,
				            "before_balance" => $beforeBalance,
				            "after_balance" => $afterBalance,
				            "response_result_id" => $this->response_result_id,
				            "unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
				        ];

				        $this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_ADDITIONAL_DEDUCT-CASINO insertData: ",$insertData);

				        if($this->doInsertToGameTransactions($insertData))
				        {
					        $this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $afterBalance,"betAmount" => $betAmount]));
					        return;
					    }
        			}
        		}
        	}

    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_DUPLICATE_TRANSFER_CODE));
        	return;
        }else
        {
        	$isDeduct = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$betAmount);	
        	
        	if($isDeduct)
        	{
	        	$afterBalance = $this->getPlayerWalletBalance($playerId);
	            
	            $insertData = [
		            "transaction_type" => self::TRANSACTION_TYPE_DEDUCT,
		            "gameusername" => $gameUsername,
		            "game_type" => $gameType,
		            "product_type" => $productType,
		            "transfer_code" => $transferCode,
		            "transaction_id" => $transactionId,
		            "amount" => $betAmount,
		            "before_balance" => $beforeBalance,
		            "after_balance" => $afterBalance,
		            "response_result_id" => $this->response_result_id,
		            "unique_transaction_id" => $externalUniqueId,
		        ];

		        $this->utils->debug_log("SBOBET_SEAMLESS_API ACTION_DEDUCT-NORMAL_GAME insertData: ",$insertData);

		        if($this->doInsertToGameTransactions($insertData))
		        {
		        	$gameLogsData = [
		        					 "gameusername" => $gameUsername,
		        					 "bet_amount" => $betAmount,
		        					 "product_type" => $betAmount,
		        					 "game_type" => $gameType,
		        					 "product_type" => $productType,
		        					 "external_uniqueid" => $externalUniqueId,
		        					 "status" => self::BET_STATUS_RUNNING,
		        					];

		        	if($this->doInsertToGameLogs($gameLogsData))
		        	{
		        		$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $afterBalance,"betAmount" => $betAmount]));
		        		return;
		        	}
		        }
	        }
        }
	}

	private function generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId=null){
		if(($productType != self::PRODUCT_TYPE_SLOTS) || ($productType == self::PRODUCT_TYPE_SLOTS && in_array($gameType,self::GAME_TYPE_WANMEI))){
    		return $transferCode."-".$transactionId;
    	}else{
    		return $transferCode;
    	}
	}

    /*
     * We will use this API to send the result of bet for you to settle the bet. 
     * This API may be requested by us many times under the same bet, 
     * which means we resettle the bet.
     */
    public function settle()
    {
    	$result = $this->processRequest(self::APICALL_SETTLE);
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$resultType = isset($result['resultType']) ? $result['resultType'] : null;
    	$resultAmount = isset($result['winloss']) ? $result['winloss'] : 0;
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$productType = isset($result['ProductType']) ? $result['ProductType'] : $productType;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$gameType = isset($result['GameType']) ? $result['GameType'] : $gameType;

    	$transactionId = $this->sbobet_seamless_game_transactions_model->getTransactionIdByTransferCode($transferCode,self::TRANSACTION_TYPE_DEDUCT);

    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

    	# Checks if bet exists
    	$isBetExists = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);

    	if(!$isBetExists){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BET_DOES_NOT_EXISTS));
        	return;
    	}

    	$isBetStatusCancelledAlready = $this->sbobet_seamless_game_logs_model->checkIfBetIsCancelledAlready($externalUniqueId);

    	if($isBetStatusCancelledAlready){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_INTERNAL_ERROR,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
        	return;
    	}
    	
    	$betAmount = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);
    	
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);
    	$resultTime = isset($result['resultTime']) ? $result['resultTime'] : null;
    	if($resultTime){
    		$resultTime = new DateTime($resultTime);
    		$resultTime = $resultTime->format("Y-m-d H:i:s");
    	}

    	$transactionData = [
    				"transaction_type" => self::TRANSACTION_TYPE_SETTLE_ADD,
    				"gameusername" => $gameUsername,
    				"transfer_code" => $transferCode,
    				"transaction_id" => $transactionId,
    				"amount" => $resultAmount,
    				"before_balance" => $beforeBalance,
    				"result_type" => $resultType,
    				"result_time" => $resultTime,
    				"product_type" => $productType,
    				"game_type" => $gameType,
    				"unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
    			];
    	
    	if($this->sbobet_seamless_game_logs_model->isSettleBetAlready($externalUniqueId))
    	{
    		$transactionData['updated_at'] = $this->utils->getNowForMysql();
    		
			# Check if existing result amount is greater than current request result amount, increase balance
			$beforeResultAmount = $this->sbobet_seamless_game_logs_model->getResultAmountByExternalUniqueId($externalUniqueId) + $betAmount;

			$this->utils->debug_log("SBOBET_SEAMLESS_API SETTLE-EXIST: ",$resultAmount,$beforeResultAmount);

			if($resultAmount > $beforeResultAmount)
			{
				$increaseBalanceAmt = $resultAmount - $beforeResultAmount;
				$isIncreaseBalance = $this->wallet_model->incSubWallet($playerId, $this->getGamePlatformCode(),$increaseBalanceAmt);
				$afterBalance = $this->getPlayerWalletBalance($playerId);

				$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_SETTLE_ADD: ",$increaseBalanceAmt,$isIncreaseBalance);

				if($isIncreaseBalance)
				{
					$transactionData['transaction_type'] = self::TRANSACTION_TYPE_SETTLE_ADD;
					$transactionData['after_balance'] = $afterBalance;
					$transactionData['amount'] = $increaseBalanceAmt;

					# Inserts to transaction then update the game logs 
					# and insert for reference as well
					if($this->doInsertToGameTransactions($transactionData))
			    	{
			    		# Update bet result
			    		$betResultData = [
			    							"result_amount" => $resultAmount-$betAmount,
			    							"result_type" => $resultType,
			    							"result_time" => $resultTime,
			    							"updated_at" => $this->utils->getNowForMysql(),
			    							"status" => self::BET_STATUS_SETTLED,
			    						 ];

			    		$isBetResultUpdated = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betResultData);

			    		if($isBetResultUpdated)
			    		{
			    			# Also inserts result in the table, can be use as reference
				    		$betResultData["response_result_id"] = $this->response_result_id;
				    		$betResultData["external_uniqueid"] = $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique();
				    		$betResultData["currency"] = $this->game_api_sbo_seamless->getGameCurrency();
				    		$betResultData["product_type"] = $productType;
				    		$betResultData["game_type"] = $gameType;
				    		$betResultData["gameusername"] = $gameUsername;
				    		$betResultData["bet_amount"] = $betAmount;
				    		$betResultData["remarks"] = "Additional Win Amount";
				    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

			    			if($isUpdateBetResultInserted){
				    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
					        	return;
				    		}
			    		}
				    }
				}
			}

			# Check if existing result amount is less than current request result amount, decrease balance
			if($resultAmount < $beforeResultAmount)
			{
				$decreaseBalanceAmt = $beforeResultAmount - $resultAmount;
				$isDecreaseBalance = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$decreaseBalanceAmt);
				$afterBalance = $this->getPlayerWalletBalance($playerId);

				$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_SETTLE_DEDUCT: ",$decreaseBalanceAmt,$isDecreaseBalance);

				if($isDecreaseBalance)
				{
					$transactionData['transaction_type'] = self::TRANSACTION_TYPE_SETTLE_DEDUCT;
					$transactionData['after_balance'] = $afterBalance;
					$transactionData['amount'] = $decreaseBalanceAmt;

					# Inserts to transaction then update the game logs 
					# and insert for reference as well
					if($this->doInsertToGameTransactions($transactionData))
			    	{
						# Update bet result
			    		$betResultData = [
			    							"result_amount" => $resultAmount-$betAmount,
			    							"result_type" => $resultType,
			    							"result_time" => $resultTime,
			    							"updated_at" => $this->utils->getNowForMysql(),
			    							"status" => self::BET_STATUS_SETTLED
			    						 ];

			    		$isBetResultUpdated = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betResultData);

						if($isBetResultUpdated)
			    		{
			    			# Also inserts result for reference
				    		$betResultData["response_result_id"] = $this->response_result_id;
				    		$betResultData["external_uniqueid"] = $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique();
				    		$betResultData["currency"] = $this->game_api_sbo_seamless->getGameCurrency();
				    		$betResultData["product_type"] = $productType;
				    		$betResultData["game_type"] = $gameType;
				    		$betResultData["gameusername"] = $gameUsername;
				    		$betResultData["bet_amount"] = $betAmount;
				    		$betResultData["remarks"] = "Deduct to Win Amount";
				    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

			    			if($isUpdateBetResultInserted){
				    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
					        	return;
				    		}
			    		}
			    	}
				}
			}
			
			$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_INTERNAL_ERROR,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
        	return;
    	}else
    	{
    		$this->utils->debug_log("SBOBET_SEAMLESS_API SETTLE insertData: ",$transactionData);
    		
    		$isIncreaseBalance = false;
    		if($resultAmount){
    			$isIncreaseBalance = $this->wallet_model->incSubWallet($playerId, $this->getGamePlatformCode(),$resultAmount);
    		}
	    	
	    	if($isIncreaseBalance || $resultAmount < 1){
	    		$transactionData['after_balance'] = $this->getPlayerWalletBalance($playerId);
	    		if($this->doInsertToGameTransactions($transactionData))
		    	{
		    		# Update bet result
		    		$betResultData = [
		    							"result_amount" => $resultAmount-$betAmount,
		    							"result_type" => $resultType,
		    							"result_time" => $resultTime,
		    							"status" => self::BET_STATUS_SETTLED,
		    						 ];
		    		$isBetResultUpdated = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betResultData);

		    		if($isBetResultUpdated)
		    		{
		    			# Also inserts result for reference
			    		$betResultData["response_result_id"] = $this->response_result_id;
			    		$betResultData["external_uniqueid"] = $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique();
			    		$betResultData["currency"] = $this->game_api_sbo_seamless->getGameCurrency();
			    		$betResultData["product_type"] = $productType;
			    		$betResultData["game_type"] = $gameType;
			    		$betResultData["gameusername"] = $gameUsername;
			    		$betResultData["bet_amount"] = $betAmount;
			    		$isBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);
			    		
			    		if($isBetResultInserted){
			    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
				        	return;
			    		}
		    		}
			    }
	    	}
    	}
    }

    /*
     * If any situation force us to rollback the settlement, 
     * after we've rollback the bet, we will send this API request to inform you.
     * Rollback means the settled bet in a game will go back to running state, 
     * and will have to be settled again.
     */
    public function rollback()
    {
    	$result = $this->processRequest(self::APICALL_ROLLBACK);
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);

    	$transactionId = $this->sbobet_seamless_game_transactions_model->getTransactionIdByTransferCode($transferCode,self::TRANSACTION_TYPE_DEDUCT);
    	
    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

    	$betAmount = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);

    	if(!$betAmount){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BET_DOES_NOT_EXISTS));
        	return;
    	}

    	# Check if bet is settled already by getting result amount
    	$resultAmount = $this->sbobet_seamless_game_logs_model->getResultAmountByExternalUniqueId($externalUniqueId) ?: 0;

    	# Check rollback time if already rolled back
    	$isRolledBack = $this->sbobet_seamless_game_logs_model->isRolledBackAlready($externalUniqueId);
    
    	# Do rollback if bet is settled already and rollback hasn't made yet
    	if(!$isRolledBack)
    	{
    		$adjustmentAmt = $betAmount + $resultAmount;
	    	if($productType == self::PRODUCT_TYPE_SLOTS && in_array($gameType,self::GAME_TYPE_WANMEI)){
				$isBalanceAdjusted = $this->wallet_model->incSubWallet($playerId, $this->getGamePlatformCode(),$adjustmentAmt);
	    	}else{
				$isBalanceAdjusted = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$adjustmentAmt);
	    	}
    		
			$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_ROLLBACK: ",$adjustmentAmt,$isBalanceAdjusted);

			if($isBalanceAdjusted)
			{
				$afterBalance = $this->getPlayerWalletBalance($playerId);
				$transactionData = [
	    				"transaction_type" => self::TRANSACTION_TYPE_ROLLBACK_DEDUCT,
	    				"gameusername" => $gameUsername,
	    				"transfer_code" => $transferCode,
	    				"transaction_id" => $transactionId,
	    				"amount" => $adjustmentAmt,
	    				"before_balance" => $beforeBalance,
	    				"after_balance" => $afterBalance,
	    				"product_type" => $productType,
	    				"game_type" => $gameType,
	    				"unique_transaction_id" => $externalUniqueId."-RB-".$this->game_api_sbo_seamless->generateUnique(),
	    		];

	    		# Inserts to transaction then update the game logs 
				# and insert for reference as well
				if($this->doInsertToGameTransactions($transactionData))
		    	{
		    		$now = $this->utils->getNowForMysql();
		    		# Update bet result
		    		$betResultData = [
		    							"result_amount" => null,
		    							"result_type" => null,
		    							"result_time" => null,
		    							"cancel_time" => null,
		    							"rollback_time" => $now,
		    							"updated_at" => $now,
		    							"status" => self::BET_STATUS_RUNNING,
		    							"remarks" => "Rolled Back Record",
		    						 ];

		    		$isBetResultUpdated = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betResultData);

					if($isBetResultUpdated)
		    		{
		    			# Also inserts result for reference
			    		$betResultData["response_result_id"] = $this->response_result_id;
			    		$betResultData["external_uniqueid"] = $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique();
			    		$betResultData["currency"] = $this->game_api_sbo_seamless->getGameCurrency();
			    		$betResultData["product_type"] = $productType;
			    		$betResultData["game_type"] = $gameType;
			    		$betResultData["gameusername"] = $gameUsername;
			    		$betResultData["bet_amount"] = $betAmount;
			    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

		    			if($isUpdateBetResultInserted)
		    			{
			    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
				        	return;
			    		}
		    		}
		    	}
			}
    	}else{
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ROLLBACK_EXISTS));
        	return;
    	}
    }

    /*
     * If any situation force us to cancel the bet, 
     * after we've canceled the bet, we will send this API request to inform you.
     * Cancel means the running or settled bet in a game will be void, 
     * and will not be accepted anymore.
     */
    public function cancel()
    {
    	$result = $this->processRequest(self::APICALL_CANCEL);
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);
    	$transactionId = isset($result['transactionId']) ? $result['transactionId'] : null;
    	if(!$transactionId){
    		$transactionId = $this->sbobet_seamless_game_transactions_model->getTransactionIdByTransferCode($transferCode,self::TRANSACTION_TYPE_DEDUCT);
    	}

    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

    	# Checks if bet exists
    	$betAmount = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);

    	$resultAmount = $this->sbobet_seamless_game_logs_model->getResultAmountByExternalUniqueId($externalUniqueId) ?: 0;

    	if(!$betAmount){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BET_DOES_NOT_EXISTS));
        	return;
    	}else{
    		# Check cancel time if already cancelled
    		$isCancelled = $this->sbobet_seamless_game_logs_model->isBetCancelledAlready($externalUniqueId);

    		# Update bet details in records set status to cancelled
	    	if(!$isCancelled)
	    	{
	    		$isBetRunningStatus = $this->sbobet_seamless_game_logs_model->checkIfBetIsRunningAlready($externalUniqueId);
	    		if($isBetRunningStatus)
	    		{
	    			$increaseBalanceAmt = $betAmount;
					$increasedBalance = $this->wallet_model->incSubWallet($playerId, $this->getGamePlatformCode(),$increaseBalanceAmt);
					
					$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_CANCEL_ADD: ",$increaseBalanceAmt,$increasedBalance);

					if($increasedBalance)
					{
						$afterBalance = $this->getPlayerWalletBalance($playerId);
						$transactionData = [
			    				"transaction_type" => self::TRANSACTION_TYPE_CANCEL_ADD,
			    				"gameusername" => $gameUsername,
			    				"transfer_code" => $transferCode,
			    				"transaction_id" => $transactionId,
			    				"amount" => $betAmount,
			    				"before_balance" => $beforeBalance,
			    				"after_balance" => $afterBalance,
			    				"product_type" => $productType,
			    				"game_type" => $gameType,
			    				"unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
			    		];

			    		# Inserts to transaction then update the game logs 
						# and insert for reference as well
						if($this->doInsertToGameTransactions($transactionData))
				    	{
				    		$now = $this->utils->getNowForMysql();
				    		# Update bet result
				    		$betCancelData = [
				    							"result_amount" => null,
				    							"result_type" => null,
				    							"result_time" => null,
				    							"cancel_time" => $now,
				    							"updated_at" => $now,
				    							"status" => self::BET_STATUS_CANCELLED,
				    							"remarks" => "Cancelled Record"
				    						 ];

				    		$isBetCancelled = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betCancelData);

							if($isBetCancelled)
				    		{
				    			# Also inserts result for reference
					    		$betResultData = [
					    							"response_result_id" => $this->response_result_id,
					    							"external_uniqueid" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
					    							"currency" => $this->game_api_sbo_seamless->getGameCurrency(),
					    							"product_type" => $productType,
					    							"game_type" => $gameType,
					    							"gameusername" => $gameUsername,
					    							"bet_amount" => $betAmount,
					    							"cancel_time" => $now,
					    							"status" => self::BET_STATUS_CANCELLED,
					    							"remarks" => "Cancelled Record"
					    						];

					    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

				    			if($isUpdateBetResultInserted)
				    			{
					    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
						        	return;
					    		}
				    		}
				    	}
					}
	    		}else{
		    		$decreaseBalanceAmt = $resultAmount;
					$decreasedBalance = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$decreaseBalanceAmt);
					
					$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_CANCEL_DEDUCT: ",$decreaseBalanceAmt,$decreasedBalance);

					if($decreasedBalance)
					{
						$afterBalance = $this->getPlayerWalletBalance($playerId);
						$transactionData = [
			    				"transaction_type" => self::TRANSACTION_TYPE_CANCEL_DEDUCT,
			    				"gameusername" => $gameUsername,
			    				"transfer_code" => $transferCode,
			    				"transaction_id" => $transactionId,
			    				"amount" => $resultAmount,
			    				"before_balance" => $beforeBalance,
			    				"after_balance" => $afterBalance,
			    				"product_type" => $productType,
			    				"game_type" => $gameType,
			    				"unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
			    		];

			    		# Inserts to transaction then update the game logs 
						# and insert for reference as well
						if($this->doInsertToGameTransactions($transactionData))
				    	{
				    		$now = $this->utils->getNowForMysql();
				    		# Update bet result
				    		$betCancelData = [
				    							"result_amount" => null,
				    							"result_type" => null,
				    							"result_time" => null,
				    							"cancel_time" => $now,
				    							"updated_at" => $now,
				    							"status" => self::BET_STATUS_CANCELLED,
				    							"remarks" => "Cancelled Record"
				    						 ];

				    		$isBetCancelled = $this->sbobet_seamless_game_logs_model->updateBetResultByExternalUniqueId($externalUniqueId,$betCancelData);

							if($isBetCancelled)
				    		{
				    			# Also inserts result for reference
					    		$betResultData = [
					    							"response_result_id" => $this->response_result_id,
					    							"external_uniqueid" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
					    							"currency" => $this->game_api_sbo_seamless->getGameCurrency(),
					    							"product_type" => $productType,
					    							"game_type" => $gameType,
					    							"gameusername" => $gameUsername,
					    							"bet_amount" => $betAmount,
					    							"cancel_time" => $now,
					    							"status" => self::BET_STATUS_CANCELLED,
					    							"remarks" => "Cancelled Record"
					    						];

					    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

				    			if($isUpdateBetResultInserted)
				    			{
					    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
						        	return;
					    		}
				    		}
				    	}
					}
				}
	    	}else{
	    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BET_IS_CANCELLED_ALREADY));
        		return;
	    	}
    	}
    }

    /*
     *	When player giving some tips in a game, 
     *	we will use this api to send request to deduct 
     *	player's balance in their wallet directly.
     */ 
    public function tip()
    {
    	$result = $this->processRequest(self::APICALL_TIP);
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$amount = isset($result['amount']) ? $result['amount'] : null;
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);

    	$externalUniqueId = $transferCode."-".$productType."-".$gameType;

    	# Checks if tip exists
    	$isTipExist = $this->sbobet_seamless_game_logs_model->isPlayerTipExists($externalUniqueId);

    	if($isTipExist){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_TIP_ALREADY_EXISTS));
        	return;
    	}else
    	{	
    		$decreaseBalanceAmt = $amount;
			$isDecreaseBalance = $this->wallet_model->decSubWallet($playerId, $this->getGamePlatformCode(),$decreaseBalanceAmt);
			
			$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_TIP_DEDUCT: ",$decreaseBalanceAmt,$isDecreaseBalance);

			if($isDecreaseBalance)
			{
				$afterBalance = $this->getPlayerWalletBalance($playerId);
				$transactionData = [
	    				"transaction_type" => self::TRANSACTION_TYPE_TIP_DEDUCT,
	    				"gameusername" => $gameUsername,
	    				"transfer_code" => $transferCode,
	    				"amount" => $decreaseBalanceAmt,
	    				"before_balance" => $beforeBalance,
	    				"after_balance" => $afterBalance,
	    				"product_type" => isset($result['productType']) ? $result['productType'] : null,
	    				"game_type" => isset($result['gameType']) ? $result['gameType'] : null,
	    				"unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
	    		];

	    		# Inserts to transaction then update the game logs 
				# and insert for reference as well
				if($this->doInsertToGameTransactions($transactionData))
		    	{
		    		# Also inserts result for reference
		    		$betResultData = [
		    							"response_result_id" => $this->response_result_id,
		    							"external_uniqueid" => $externalUniqueId,
		    							"currency" => $this->game_api_sbo_seamless->getGameCurrency(),
		    							"product_type" => $productType,
		    							"game_type" => $gameType,
		    							"gameusername" => $gameUsername,
		    							"bet_amount" => $decreaseBalanceAmt,
		    							"tip_time" => $this->utils->getNowForMysql(),
		    							"remarks" => "Player Tip"
		    						];

		    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

	    			if($isUpdateBetResultInserted)
	    			{
		    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
			        	return;
		    		}
		    	}
	    	}
    	}
    }

    /*
     *	When player having or getting some bonus/jackpot/promotion in game, 
     *	we will use this api to send request to increase 
     *	player's balance in their wallet directly.
     */ 
    public function bonus()
    {
    	$result = $this->processRequest(self::APICALL_BONUS);
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$transactionId = isset($result['transactionId']) ? $result['transactionId'] : null;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$amount = isset($result['amount']) ? $result['amount'] : null;
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);

    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

    	# Checks if bonus exists
    	$isBonusExist = $this->sbobet_seamless_game_logs_model->isPlayerBonusExists($externalUniqueId);

    	if($isBonusExist){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BONUS_ALREADY_EXISTS));
        	return;
    	}else
    	{	
    		$increaseBalanceAmt = $amount;
			$isIncreaseBalance = $this->wallet_model->incSubWallet($playerId, $this->getGamePlatformCode(),$increaseBalanceAmt);
			
			$this->utils->debug_log("SBOBET_SEAMLESS_API TRANSACTION_TYPE_BONUS_ADD: ",$increaseBalanceAmt,$isIncreaseBalance);

			if($isIncreaseBalance)
			{
				$afterBalance = $this->getPlayerWalletBalance($playerId);
				$transactionData = [
	    				"transaction_type" => self::TRANSACTION_TYPE_BONUS_ADD,
	    				"gameusername" => $gameUsername,
	    				"transfer_code" => $transferCode,
	    				"amount" => $increaseBalanceAmt,
	    				"before_balance" => $beforeBalance,
	    				"after_balance" => $afterBalance,
	    				"product_type" => isset($result['productType']) ? $result['productType'] : null,
	    				"game_type" => isset($result['gameType']) ? $result['gameType'] : null,
	    				"unique_transaction_id" => $externalUniqueId."-".$this->game_api_sbo_seamless->generateUnique(),
	    				// "bonus_time" => $this->utils->getNowForMysql(),
	    		];

	    		# Inserts to transaction then update the game logs 
				# and insert for reference as well
				if($this->doInsertToGameTransactions($transactionData))
		    	{
		    		# Also inserts result for reference
		    		$betResultData = [
		    							"response_result_id" => $this->response_result_id,
		    							"external_uniqueid" => $externalUniqueId,
		    							"currency" => $this->game_api_sbo_seamless->getGameCurrency(),
		    							"product_type" => $productType,
		    							"game_type" => $gameType,
		    							"gameusername" => $gameUsername,
		    							"bet_amount" => $increaseBalanceAmt,
		    							// "bonus_time" => $this->utils->getNowForMysql(),
		    							"status" => self::BET_STATUS_SETTLED,
		    							"remarks" => "Player Bonus"
		    						];

		    		$isUpdateBetResultInserted = $this->sbobet_seamless_game_logs_model->insertGameLogs($betResultData);

	    			if($isUpdateBetResultInserted)
	    			{
		    			$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,["accountName" => $gameUsername,"balance" => $this->getPlayerWalletBalance($playerId)]));
			        	return;
		    		}
		    	}
	    	}
    	}
    }

    /*
     *	We will use this API request to sync up bet status between us, 
     *	normally for checking and debugging purpose. 
     *	For example, if a bet in 568win system is settled, 
     *	but the wallet status in your system is still running, 
     *	then we can find out the bet has problem.
     */
    public function getBetStatus()
    {
    	$result = array_filter($this->processRequest(self::APICALL_GETBETSTATUS));
    	$playerId = isset($result['playerId']) ? $result['playerId'] : null;
    	$gameUsername = isset($result['gameUsername']) ? $result['gameUsername'] : null;
    	$transferCode = isset($result['transferCode']) ? $result['transferCode'] : null;
    	$transactionId = isset($result['transactionId']) ? $result['transactionId'] : null;
    	$gameType = isset($result['gameType']) ? $result['gameType'] : null;
    	$productType = isset($result['productType']) ? $result['productType'] : null;
    	$amount = isset($result['amount']) ? $result['amount'] : null;
    	$beforeBalance = $this->getPlayerWalletBalance($playerId);

    	$externalUniqueId = $this->generateExternalUniqueId($productType,$gameType,$transferCode,$transactionId);

    	# Checks if bet exists
    	$isBetExists = $this->sbobet_seamless_game_logs_model->getBetAmountByExternalUniqueId($externalUniqueId);

    	if(!$isBetExists){
    		$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_BET_DOES_NOT_EXISTS));
        	return;
    	}else
    	{
	    	$betDetails = $this->sbobet_seamless_game_logs_model->getBetDetails($externalUniqueId);

	    	if($betDetails){
	    		$betDetails['transferCode'] = $transferCode;
	    		$betDetails['transactionId'] = $transactionId;
	    		$betDetails['winloss'] = $betDetails['winloss'] ?: 0;
	    	}

	    	$this->returnJsonResult($this->getResultResponse(self::CODE_SUCCESS,$betDetails));
			return;
	    }
    }

	private function processRequest($requestType)
	{
		# check if method is POST in the request
        if(!$this->isPostMethod()){
            $this->getResultResponse(self::CODE_ERROR_METHOD_NOT_ALLOWED);
            return;
        }

		$request = file_get_contents("php://input");
		$this->utils->debug_log($requestType." Request Data =>>>>>>> ",$request);
        if(!$this->isJson($request)){
            $this->getResultResponse(self::CODE_ERROR_WRONG_JSON_FORMAT);
            return;
        }

        $requestArr = json_decode($request,true);

        # save response result
        $response_result_id = $this->saveToResponseResult($requestType,$requestArr);
        $this->response_result_id = $response_result_id;
        
        if(isset($requestArr['companyKey'])){
			if(!$this->validateCompanyKey($requestArr['companyKey'])){
				$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_WRONG_COMPANY_KEY));
				return;
			}
		}
		$playerId = null;
		if(isset($requestArr['userName'])){
			$username = $requestArr['userName'];
			$playerId = $this->game_provider_auth->getPlayerIdByPlayerName($username, $this->gamePlatformId);
			if(!$playerId){
				$this->returnJsonResult($this->getResultResponse(self::CODE_ERROR_PLAYER_DOES_NOT_EXISTS));
				return;
			}
			$gameUsername = $this->game_provider_auth->getPlayerUsernameByGameUsername($username, $this->gamePlatformId);
			if(!$gameUsername){
				$this->getResultResponse(self::CODE_ERROR_PLAYER_DOES_NOT_EXISTS);
				return;
			}
		}

		$requestArr["gameUsername"] = $gameUsername;
		$requestArr["playerId"] = $playerId;
		$this->utils->debug_log($requestType." Request Arr =>>>>>>> ",$requestArr);
        return $requestArr;
	}

	private function validateCompanyKey($companyKey)
	{
		if($companyKey == $this->game_api_sbo_seamless->getCompanyKey()){
			return true;
		}
		return false;
	}

	private function getResultResponse($responseCode,$extra=null)
	{
		$result = [
				"errorMessage" => self::API_MSG[$responseCode],
				"errorCode" => $responseCode
			   ];
		if(!empty($extra) && is_array($extra)){
			$result = array_merge($result,$extra);
		}

		if($responseCode != self::CODE_SUCCESS){
			$customErrorMsg = [
								self::CODE_ERROR_DUPLICATE_TRANSFER_CODE,
								self::CODE_ROLLBACK_EXISTS,
								self::CODE_ERROR_WRONG_JSON_FORMAT,
								self::CODE_ERROR_PLAYER_IS_BLOCKED,
								self::CODE_ERROR_BET_IS_CANCELLED_ALREADY,
								self::CODE_ERROR_TIP_ALREADY_EXISTS,
								self::CODE_ERROR_BONUS_ALREADY_EXISTS,
							  ];

			if(in_array($responseCode,$customErrorMsg)){
				$result['errorCode'] = self::CODE_ERROR_INTERNAL_ERROR;
			}

			if(!empty($this->response_result_id)){
	            $this->db->where("id",$this->response_result_id)
	                    ->update("response_results",["content" => json_encode($result),
	                    							 "flag"=>self::CODE_ERROR_INTERNAL_ERROR]);
	        }
		}
		return $result;
	}

	private function isJson($data = null)
	{
        $rlt = true;
        if(null === @json_decode($data)){
            $rlt = false;
        }
        return $rlt;
    }

    private function saveToResponseResult($request_method, $request_params = NULL)
    {
		$this->db->insert('response_results', array(
			'system_type_id' => $this->getGamePlatformCode(),
			'request_api' => $request_method,
			'request_params' => is_array($request_params) ? json_encode($request_params) : $request_params,
			'created_at' => date('Y-m-d H:i:s'),
		));

		return $this->db->insert_id();
    }

    public function getGamePlatformCode()
    {
        return $this->game_api_sbo_seamless->getPlatformCode();
    }

    private function getPlayerWalletBalance($playerId)
    {
    	$balance = $this->CI->player_model->getPlayerSubWalletBalance($playerId,$this->getGamePlatformCode());
    	return $balance;
    }

    private function doInsertToGameLogs($data)
    {
    	$gameUsername = isset($data["gameusername"]) ? $data["gameusername"] : null;
    	$betAmount = isset($data["bet_amount"]) ? $data["bet_amount"] : null;
    	$resultAmount = isset($data["result_amount"]) ? $data["result_amount"] : null;
    	$resultType = isset($data["result_type"]) ? $data["result_type"] : null;
    	$resultTime = isset($data["result_time"]) ? $data["result_time"] : null;
    	$gameType = isset($data["game_type"]) ? $data["game_type"] : null;
        $productType = isset($data["product_type"]) ? $data["product_type"] : null;
        $status = isset($data["status"]) ? $data["status"] : null;
        $responseResultId = (!empty($this->response_result_id)) ? $this->response_result_id : null;
        $externalUniqueId = isset($data["external_uniqueid"]) ? $data["external_uniqueid"] : null;

        $data = [
            "gameusername" => $gameUsername,
            "product_type" => $productType,
            "game_type" => $gameType,
            "currency" => $this->game_api_sbo_seamless->getGameCurrency(),
            "bet_amount" => $betAmount,
            "result_amount" => $resultAmount,
            "result_type" => $resultType,
            "result_time" => $resultTime,
            "response_result_id" => $responseResultId,
            "status" => $status,
            "external_uniqueid" => $externalUniqueId,
        ];

        $insertRecordsCnt = $this->sbobet_seamless_game_logs_model->insertGameLogs($data);
        $this->CI->utils->debug_log("SBOBET_SEAMLESS_API insert records count is: ",$insertRecordsCnt);
        return $insertRecordsCnt;
    }

	private function doInsertToGameTransactions($data)
    {
        $token = isset($data["token"]) ? $data["token"] : null;
        if($token){
        	$playerInfo = $this->game_api_sbo_seamless->getPlayerInfoBytoken($token);	
        	$playerId = isset($playerInfo["playerId"]) ? $playerInfo["playerId"] : null;
        }
        
        $playerId = isset($data["player_id"]) ? $data["player_id"] : null;
        $transactionType = isset($data["transaction_type"]) ? $data["transaction_type"] : null;
        $productType = isset($data["product_type"]) ? $data["product_type"] : null;
        $gameType = isset($data["game_type"]) ? $data["game_type"] : null;
        $gameUsername = isset($data["gameusername"]) ? $data["gameusername"] : null;
        $beforeBalance = isset($data["before_balance"]) ? $data["before_balance"] : null;
        $afterBalance = isset($data["after_balance"]) ? $data["after_balance"] : null;
        $transactionId = isset($data["transaction_id"]) ? $data["transaction_id"] : null;
        $transferCode = isset($data["transfer_code"]) ? $data["transfer_code"] : null;
        $resultTime = isset($data["result_time"]) ? $data["result_time"] : null;
        $resultType = isset($data["result_type"]) ? $data["result_type"] : null;
        $responseResultId = (!empty($this->response_result_id)) ? $this->response_result_id : null;
        $amount = isset($data["amount"]) ? $data["amount"] : null;
        $uniqueTransactionId = isset($data["unique_transaction_id"]) ? $data["unique_transaction_id"] : null;

        $data = [
            "transaction_type" => $transactionType,
            "gameusername" => $gameUsername,
            "product_type" => $productType,
            "game_type" => $gameType,
            "currency" => $this->game_api_sbo_seamless->getGameCurrency(),
            "transfer_code" => $transferCode,
            "transaction_id" => $transactionId,
            "amount" => $amount,
            "before_balance" => $beforeBalance,
            "after_balance" => $afterBalance,
            "result_time" => $resultTime,
            "result_type" => $resultType,
            "response_result_id" => $responseResultId,
            "unique_transaction_id" => $uniqueTransactionId,
        ];

        $insertRecordsCnt = $this->sbobet_seamless_game_transactions_model->insertTransaction($data);
        $this->CI->utils->debug_log("SBOBET_SEAMLESS_API insert records count is: ",$insertRecordsCnt);
        return $insertRecordsCnt;
    }
}