<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Habanero_service_api extends BaseController {	
	
	const SUCCESS = 200;

	const UNKNOWN = 100;
	const INVALID_TOKEN = 101;
	const INVALID_PASSKEY = 102;
	const CANNOT_FIND_PLAYER = 103;
	const INSUFFICIENT_FUNDS = 104;
	const ERROR_CREDIT = 105;
	const ERROR_DEBIT = 106;
	const ERROR_REFUND = 107;
	const ERROR_RECREDIT = 108;
	const INVALID_TRANSFERID = 109;
	const INVALID_TRANSACTION = 110;
	
	const DONE_CREDIT = 301; 
	const DONE_DEBIT = 302; 
	const ALREADY_CREDITED = 303;
	const DONE_RECREDIT = 304;	

	public $request;
	public $passkey;
	public $trans_ids;
	public $host_name;
	public $game_api;
	public $game_platform_id;
	public $player_id;

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','habanero_transactions','player_model'));
		$this->host_name =  $_SERVER['HTTP_HOST'];
		
		$this->getValidPlatformId();
		$this->parseRequest();

		$this->game_api = $this->utils->loadExternalSystemLibObject($this->game_platform_id);
		$this->passkey = $this->request->auth->passkey;
		$this->trans_ids = [];
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (__construct)", $this->request);
	}

	public function testBermar(){
		/*$strtotime = strtotime('2019-12-11T05:17:22.3315271Z');
		echo "<br>".$strtotime;
		$strtotime = date('Y-m-d H:i:s', $strtotime);
		echo "<br>".$strtotime;*/

		$result = $this->game_api->queryGameInstanceIdsByDate('2019-12-12 00:00:00', '2019-12-12 23:59:59');
		var_dump($result);
		$result = $this->game_api->queryBetTransactionsByGameInstanceIds($result);
		var_dump($result);
		exit;
	}

	public function auth(){
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (auth)");		

		//initialize response
		$externalResponse['playerdetailresponse'] = $this->playerResponse();	

		try {
						
			if(!$this->isValidPassKey($this->passkey)){					
				throw new Exception(self::INVALID_PASSKEY);
			}
			
			list($player_status, $player_info, $game_username, $playerName) = $this->getPlayerByToken($this->request->playerdetailrequest->token);

			if(!$player_status){			
				$externalResponse['playerdetailresponse']['status']['message'] 	= $this->getErrorSuccessMessage(self::INVALID_TOKEN);			
				return $this->handleExternalResponse(false, 'auth', $this->request, $this->trans_ids, $externalResponse);						
			}		
			
			$externalResponse['playerdetailresponse']['status']['autherror'] 	= false;
			$externalResponse['playerdetailresponse']['status']['success'] 		= true;
			$externalResponse['playerdetailresponse']['accountid'] 				= $game_username;
			$externalResponse['playerdetailresponse']['accountname'] 			= $game_username;
			$externalResponse['playerdetailresponse']['balance'] 				= $this->game_api->queryPlayerBalance($playerName)['balance'];
			return $this->handleExternalResponse($externalResponse['playerdetailresponse']['status']['success'], 'auth', $this->request, $this->trans_ids, $externalResponse);		

		} catch (Exception $error) {
			$error_code = $error->getMessage();
			$externalResponse['playerdetailresponse']['status']['message'] = $this->getErrorSuccessMessage($error_code);
			return $this->handleExternalResponse(false, 'transaction', $this->request, $this->trans_ids, $externalResponse);
		}		
	}

	public function query(){		
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (query)");	
		$externalResponse['fundtransferresponse'] = $this->queryResponse();
		
		try {
						
			if(!$this->isValidPassKey($this->passkey)){					
				throw new Exception(self::INVALID_PASSKEY);
			}	

			//PROCESS HERE
			$transfer_id = isset($this->request->queryrequest->transferid)?$this->request->queryrequest->transferid:'';
			if(!$transfer_id){
				throw new Exception(self::INVALID_TRANSFERID);
			}

			$transaction = $this->habanero_transactions->getTransation($this->request->queryrequest->transferid);

			if(!$transaction){
				throw new Exception(self::INVALID_TRANSFERID);
			}else{
				if($transaction->fundinfo_amount<>$this->request->queryrequest->queryamount){
					throw new Exception(self::INVALID_TRANSACTION);
				}
			}

			$externalResponse['fundtransferresponse']['status']['success'] = true;

			return $this->handleExternalResponse($externalResponse['fundtransferresponse']['status']['success'], 'query', $this->request, $this->trans_ids, $externalResponse);		

		} catch (Exception $error) {
			$error_code = $error->getMessage();
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage($error_code);
			return $this->handleExternalResponse(false, 'query', $this->request, $this->trans_ids, $externalResponse);
		}
	}

	public function config(){
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (config)");		

		//initialize response
		$externalResponse['configdetailresponse'] = $this->configResponse();
		$defaultConfig = $this->configResponse();
		unset($defaultConfig['status']);	

		try {
						
			if(!$this->isValidPassKey($this->passkey)){					
				throw new Exception(self::INVALID_PASSKEY);
			}
			list($player_status, $player_info, $game_username, $playerName) = $this->getPlayerByToken($this->request->configdetailrequest->token);

			if(!$player_status){			
				$externalResponse['configdetailresponse']['status']['message'] 		= $this->getErrorSuccessMessage(self::INVALID_TOKEN);	
				return $this->handleExternalResponse(false, 'auth', $this->request, $this->trans_ids, $externalResponse);						
			}		
			
			$externalResponse['configdetailresponse']['status']['autherror'] 		= false;
			$externalResponse['configdetailresponse']['status']['success'] 		= true;

			foreach($defaultConfig as $key => $defaultConfigItem){
				$externalResponse['configdetailresponse'][$key] = isset($this->game_api->config[$key])?$this->game_api->config[$key]:$defaultConfigItem;
			}			

			return $this->handleExternalResponse($externalResponse['configdetailresponse']['status']['success'], 'config', $this->request, $this->trans_ids, $externalResponse);		

		} catch (Exception $error) {
			$error_code = $error->getMessage();
			$externalResponse['configdetailresponse']['status']['message'] = $this->getErrorSuccessMessage($error_code);
			return $this->handleExternalResponse(false, 'transaction', $this->request, $this->trans_ids, $externalResponse);
		}
	}

	public function endsession(){
		$externalResponse = [];
		return $this->handleExternalResponse(false, 'endsession', $this->request, $this->trans_ids, $externalResponse);
	}

	public function transaction(){
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (transaction)");	
		//initialize response	
		$fundsReponse = $this->fundsResponse();				
		$externalResponse['fundtransferresponse'] 		= $fundsReponse;

		try {

			if(!$this->isValidPassKey($this->passkey)){					
				throw new Exception(self::INVALID_PASSKEY);
			}

			//get player info
			list($player_status, $player, $game_username, $player_name) = $this->getPlayerByToken($this->request->fundtransferrequest->token);

			//check player info
			if(!$player_status){			
				throw new Exception(self::INVALID_TOKEN);
			}	

			$externalResponse['fundtransferresponse']['status']['autherror'] = false;
			$fundtransferrequest = $this->request->fundtransferrequest;

			$params = [
				'player_id'		=> $player['playerId'],
				'player_name'	=> $player_name,				
			];

			$this->player_id = $player['playerId'];
			
			if($fundtransferrequest->funds->debitandcredit == true){
				$this->handleDebitCredit($externalResponse, $fundtransferrequest, $params);				
			}else{						
				if($fundtransferrequest->isrefund){
					$this->handleRefund($externalResponse, $fundtransferrequest, $params);				
				}elseif($fundtransferrequest->isrecredit){
					$this->handleReCredit($externalResponse, $fundtransferrequest, $params);
				}else{
					$this->handleSingleTransaction($externalResponse, $fundtransferrequest, $params);
				}
			}

			return $this->handleExternalResponse($externalResponse['fundtransferresponse']['status']['success'], 'transaction', $this->request, $this->trans_ids, $externalResponse);

		} catch (Exception $error) {
			$error_code = $error->getMessage();
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage($error_code);	
			return $this->handleExternalResponse(false, 'transaction', $this->request, $this->trans_ids, $externalResponse);
		}
	}

	/**
     * Trasfer mode handler
     */
	public function handleDebitCredit(&$externalResponse, $fundtransferrequest, $params){		
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (handleDebitCredit)", $params);		
		$debit = $fundtransferrequest->funds->fundinfo[0];//credit
		$credit = $fundtransferrequest->funds->fundinfo[1];//debit

		$params['amount'] 		= $debit->amount;
		$params['transfer_id'] 	= $debit->transferid;

		$previous_balance 	= $this->game_api->queryPlayerBalance($params['player_name'])['balance'];
		$externalResponse['fundtransferresponse']['balance'] = $previous_balance;

		//check if transferid already exist - Debit
		$originalTransfer = $this->isTransferIdExist($debit->transferid);
		if($originalTransfer != null){
			$externalResponse['fundtransferresponse']['status']['success'] = false;
			return;
		}

		//check if transferid already exist - credit
		$originalTransfer = $this->isTransferIdExist($credit->transferid);
		if($originalTransfer != null){
			$externalResponse['fundtransferresponse']['status']['success'] = false;
			return;
		}

		list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'debit');

		if($trans_success == true){

			$this->trans_ids[] = $this->insertTransactionRecord($this->request, $debit->transferid, $previous_balance, $after_balance);

			$externalResponse['fundtransferresponse']['status']['successdebit'] = true;			
			//now do the credit
			$params['amount'] 		= $credit->amount;
			$params['transfer_id'] 	= $credit->transferid;
			list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'credit');
					
			if ($trans_success){
				$this->trans_ids[] = $this->insertTransactionRecord($this->request, $credit->transferid, $previous_balance, $after_balance);
				$externalResponse['fundtransferresponse']['status']['successcredit'] = true;
				$externalResponse['fundtransferresponse']['status']['success'] = true;
			}else{
				$externalResponse['fundtransferresponse']['status']['success'] = false;
				$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::ERROR_CREDIT);
			}
			$externalResponse['fundtransferresponse']['balance'] = $after_balance; //set back the current balance after the credit

		}else{
			//if the debit failed, then return...
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::INSUFFICIENT_FUNDS);
			$externalResponse['fundtransferresponse']['balance'] = $after_balance; //set back the current balance after the credit
		}
		return;
	}

	public function handleRefund(&$externalResponse, $fundtransferrequest, $params){		
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (handleRefund)", $params);		
		$refund = $fundtransferrequest->funds->refund;
		
		//check if transferid already exist
		$previousRefundTransfer = $this->isTransferIdExist($refund->transferid);

		$previous_balance 	= $this->game_api->queryPlayerBalance($params['player_name'])['balance'];
		$externalResponse['fundtransferresponse']['balance'] = $previous_balance;

		if($previousRefundTransfer){
			//frag transaction refunded
			//$this->habanero_transactions->flagTrasactionRefunded($refund->originaltransferid);

			//flag all game instance id as refunded
			$this->habanero_transactions->flagGameinstanceRefunded($fundtransferrequest->gameinstanceid);

			$externalResponse['fundtransferresponse']['status']['success'] = true;
			$externalResponse['fundtransferresponse']['status']['refundstatus'] = "1";
			return;
		}

		$originalTransfer = $this->habanero_transactions->getTransation($refund->originaltransferid);

		if($originalTransfer != null){

			$params['amount'] 		= $refund->amount;
			$params['transfer_id'] 	= $refund->transferid;
	
			list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'credit');
			
			if($trans_success == true){
				//flag all game instance id as refunded
				$this->habanero_transactions->flagGameinstanceRefunded($fundtransferrequest->gameinstanceid);

				$this->trans_ids[] = $this->insertTransactionRecord($this->request, $refund->transferid, $previous_balance, $after_balance);

				$externalResponse['fundtransferresponse']['status']['success'] = true;
				$externalResponse['fundtransferresponse']['status']['refundstatus'] = "1";
				$externalResponse['fundtransferresponse']['balance'] = $after_balance;	
			}else{
				$externalResponse['fundtransferresponse']['status']['success'] = false;
				$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::ERROR_REFUND);
			}
		}else{
			$externalResponse['fundtransferresponse']['status']['success'] = true;
			$externalResponse['fundtransferresponse']['status']['refundstatus'] = "2";
			$externalResponse['fundtransferresponse']['status']['message'] = "Original request never debited. So closing record.";
		}
		return;
	}

	public function handleReCredit(&$externalResponse, $fundtransferrequest, $params){		
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (handleReCredit)", $params);		
		$recredit = $fundtransferrequest->funds->fundinfo[0];

		$params['amount'] 		= $recredit->amount;
		$params['transfer_id'] 	= $recredit->transferid;

		//check if transferid already exist
		$originalTransfer = $this->isTransferIdExist($recredit->originaltransferid);
		if($originalTransfer != null){
			$previous_balance 	= $this->game_api->queryPlayerBalance($params['player_name'])['balance'];
			$externalResponse['fundtransferresponse']['balance'] = $previous_balance;
			$externalResponse['fundtransferresponse']['status']['success'] = true;
			return;
		}

		list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'credit');
			
		if($trans_success == true){
			$this->trans_ids[] = $this->insertTransactionRecord($this->request, $recredit->transferid, $previous_balance, $after_balance);
			$externalResponse['fundtransferresponse']['status']['success'] = true;
			//$externalResponse['fundtransferresponse']['balance'] = "0";
			$externalResponse['fundtransferresponse']['status']['message'] = self::DONE_RECREDIT;
		}else{
			$externalResponse['fundtransferresponse']['status']['success'] = false;
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::ERROR_RECREDIT);
		}

		$externalResponse['fundtransferresponse']['balance'] = $after_balance;

		return;
	}

	public function handleSingleTransaction(&$externalResponse, $fundtransferrequest, $params){	
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (handleSingleTransaction)", $params);		
		
		$single = $fundtransferrequest->funds->fundinfo[0];

		$params['amount'] 		= $single->amount;
		$params['transfer_id'] 	= $single->transferid;

		//check if transferid already exist
		$originalTransfer = $this->isTransferIdExist($single->transferid);
		if($originalTransfer != null){
			$previous_balance 	= $this->game_api->queryPlayerBalance($params['player_name'])['balance'];
			$externalResponse['fundtransferresponse']['balance'] = $previous_balance;
			$externalResponse['fundtransferresponse']['status']['success'] = true;
			return;
		}
		
		if($single->amount<0){
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::ERROR_DEBIT);	
			list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'debit');
		}else{
			$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::ERROR_CREDIT);
			list($trans_success, $previous_balance, $after_balance, $insufficient_balance) = $this->debitCreditAmount($params, 'credit');
		}

		if($trans_success == true){
			$this->trans_ids[] = $this->insertTransactionRecord($this->request, $single->transferid, $previous_balance, $after_balance);

			$externalResponse['fundtransferresponse']['status']['success'] = true;
			$externalResponse['fundtransferresponse']['status']['message'] = "";	
		}else{
			$externalResponse['fundtransferresponse']['status']['success'] = false;
			$externalResponse['fundtransferresponse']['status']['nofunds'] = $insufficient_balance;
			if($insufficient_balance){
				$externalResponse['fundtransferresponse']['status']['message'] = $this->getErrorSuccessMessage(self::INSUFFICIENT_FUNDS);
			}			
		}

		$externalResponse['fundtransferresponse']['balance'] = $after_balance; //set back the current balance after the credit
		return;
	}

    /**
     * Response template
     */
    public function playerResponse(){
        return array(
			"status" =>
				array(
					"success" => false,
					"nofunds" => false,
					"successdebit" => false,
					"successcredit" => false,
					"message" => "",
					"autherror" => true,
					"refundstatus" => 0
				),
			"accountid" => "",
			"accountname" => "",
			"balance" => 0,
			"currencycode" => $this->game_api->currency,
			"country" => null,
			"fname" => null,
			"lname" => null,
			"email" => null,
			"tel" => null,
			"telalt" => null
		);
    }

    public function fundsResponse(){
		return array("status" =>
			array(
				"success" => false,
				"nofunds" => false,
				"successdebit" => false,
				"successcredit" => false,
				"message" => null,
				"autherror" => true,
				"refundstatus" => 0
			),
			"balance" => "",
			"currencycode" => $this->game_api->currency,
			"remoteid" => null,
			"remotetransferid" => ""
		);
	}

    public function configResponse(){
		return array("status" =>
			array(
				"success" => false,
				"message" => null,
				"autherror" => true,
			),
			"minstake" => 1.0,
			"maxstake" => 5.0,
			"stakeincrement" => "0.10|0.50|1|5|10",
			"levelincrement" => "1|5|10",
			"defaultstake" => 0.50,
			"maxpaylimit" => 0.0,
			"mininsidestake" => 0.0,
			"maxinsidestake" => 0.0,
			"minoutsidestake" => 0.0,
			"maxoutsidestake" => 0.0
		);
	}

    public function queryResponse(){
		return array("status" =>
			array(
				"success" => false,
				"message" => null,
			)			
		);
	}

	public function externalQueryResponse(){
		return array(
			"fundtransferresponse" => array(
				"status" => array(
					"success" => false
				),
				"remotetransferid" => null
			)
		);
	}

	/**
     * Helper Functions
     */
	private function saveResponseResult($success, $callMethod, $params, $response){
        $flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        return $this->CI->response_result->saveResponseResult(
        	$this->game_platform_id, 
        	$flag, 
        	$callMethod, 
        	json_encode($params), 
        	json_encode($response), 
        	200, 
        	null, 
        	null
        );
	}
	
	public function handleExternalResponse($status, $type, $data, $trans_ids, $response){
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (handleExternalResponse)", $response, 'trans_ids',$trans_ids);	
		$response_result_id = $this->saveResponseResult($status, $type, $data, $response);	
		foreach($trans_ids as $trans_id){
			$this->habanero_transactions->updateResponseResultId($trans_id, $response_result_id);
		}

		return $this->output->set_content_type('application/json')->set_output(json_encode($response));
	}

	public function getPlayerByToken($token){
		$success = false;

		$player = $this->game_api->getPlayerInfoByToken($token);		
		$game_username = $this->game_api->getGameUsernameByPlayerId($player['playerId']);
		$player_name = $this->game_provider_auth->getPlayerUsernameByGameUsername($game_username, $this->game_platform_id);   
		if(!$player){		
			return [false, null, null, null];
		}

		return [true, $player, $game_username, $player_name];
	}

	public function insertTransactionRecord($request, $transferId, $balance_before = 0, $balance_after = 0){
		$trans_id = '';

		$trans_records = $this->makeTransactionRecord($request);	
		foreach($trans_records as $trans_record){
			$isTransactionExist = $this->habanero_transactions->isTransactionExist($transferId);
			if($trans_record['external_uniqueid']==$transferId && !$isTransactionExist){
				$trans_record['balance_after'] = $balance_after;
				$trans_record['balance_before'] = $balance_before;
				$trans_record['player_id'] = $this->player_id;
				//$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (insertTransactionRecord)", $trans_record);
				$trans_id = $this->CI->habanero_transactions->insertRow($trans_record);
			}			
		}

		return $trans_id;
	}

	public function makeTransactionRecord($request){
		$request = $this->objectToArray($request);
		$record = [];

		$isRetry = false;

		if(isset($request['fundtransferrequest']['funds']['refund'])){
			$isRetry = true;
		}

		if($isRetry){
			$recordsets[] = isset($request['fundtransferrequest']['funds']['refund'])?$request['fundtransferrequest']['funds']['refund']:[];
		}else{
			$recordsets = isset($request['fundtransferrequest']['funds']['fundinfo'])?$request['fundtransferrequest']['funds']['fundinfo']:[];
		}

		foreach($recordsets as $key => $row){
			$fundtransferrequest = @$request['fundtransferrequest'];			
			$fundtransferrequest_game = @$request['fundtransferrequest']['gamedetails'];
			$temp = array(							
						//'id' 									=> null,		
						'type' 									=> isset($request['type'])?$request['type']:null,
						'dtsent' 								=> isset($request['dtsent'])?$request['dtsent']:null,
						'brandgameid' 							=> isset($request['basegame']['brandgameid'])?$request['basegame']['brandgameid']:null,
						'keyname' 								=> isset($request['basegame']['keyname'])?$request['basegame']['keyname']:null,
						'auth_brandid' 							=> isset($request['auth']['brandid'])?$request['auth']['brandid']:null,
						'auth_locale' 							=> isset($request['auth']['locale'])?$request['auth']['locale']:null,
						'auth_machinename' 						=> isset($request['auth']['machinename'])?$request['auth']['machinename']:null,
						'auth_passkey' 							=> isset($request['auth']['passkey'])?$request['auth']['passkey']:null,
						'auth_username' 						=> isset($request['auth']['username'])?$request['auth']['username']:null,
						'gameinstanceid' 						=> isset($fundtransferrequest['gameinstanceid'])?$fundtransferrequest['gameinstanceid']:null,
						'isrecredit' 							=> isset($fundtransferrequest['isrecredit'])?$fundtransferrequest['isrecredit']:null,
						'isrefund' 								=> isset($fundtransferrequest['isrefund'])?$fundtransferrequest['isrefund']:null,
						'isretry' 								=> isset($fundtransferrequest['isretry'])?$fundtransferrequest['isretry']:null,
						'retrycount' 							=> isset($fundtransferrequest['retrycount'])?$fundtransferrequest['retrycount']:null,
						'token' 								=> isset($fundtransferrequest['token'])?$fundtransferrequest['token']:null,
						'accountid' 							=> isset($fundtransferrequest['accountid'])?$fundtransferrequest['accountid']:null,
						'customplayertype' 						=> isset($fundtransferrequest['customplayertype'])?$fundtransferrequest['customplayertype']:null,
						'friendlygameinstanceid' 				=> isset($fundtransferrequest['friendlygameinstanceid'])?$fundtransferrequest['friendlygameinstanceid']:null,
						'funds_debitandcredit' 					=> isset($fundtransferrequest['funds']['debitandcredit'])?$fundtransferrequest['funds']['debitandcredit']:null,
						'fundinfo_amount' 						=> isset($row['amount'])?$row['amount']:null,
						'fundinfo_currencycode' 				=> isset($row['currencycode'])?$row['currencycode']:null,
						'fundinfo_dtevent' 						=> isset($row['dtevent'])?$row['dtevent']:null,
						'fundinfo_gamestatemode' 				=> isset($row['gamestatemode'])?$row['gamestatemode']:null,
						'fundinfo_initialdebittransferid' 		=> isset($row['initialdebittransferid'])?$row['initialdebittransferid']:null,
						'fundinfo_isbonus' 						=> isset($row['isbonus'])?$row['isbonus']:null,
						'fundinfo_jpcont' 						=> isset($row['jpcont'])?$row['jpcont']:null,
						'fundinfo_jpwin' 						=> isset($row['jpwin'])?$row['jpwin']:null,
						'fundinfo_jpid' 						=> isset($row['jpid'])?$row['jpid']:null,
						'fundinfo_transferid' 					=> isset($row['transferid'])?$row['transferid']:null,
						'fundinfo_originaltransferid' 			=> isset($row['originaltransferid'])?$row['originaltransferid']:null,
						'gamedetails_brandgameid' 				=> isset($fundtransferrequest_game['brandgameid'])?$fundtransferrequest_game['brandgameid']:null,
						'gamedetails_browser' 					=> isset($fundtransferrequest_game['browser'])?$fundtransferrequest_game['browser']:null,
						'gamedetails_channel' 					=> isset($fundtransferrequest_game['channel'])?$fundtransferrequest_game['channel']:null,
						'gamedetails_device'					=> isset($fundtransferrequest_game['device'])?$fundtransferrequest_game['device']:null,
						'gamedetails_friendlygameinstanceid' 	=> isset($fundtransferrequest_game['friendlygameinstanceid'])?$fundtransferrequest_game['brandgameid']:null,
						'gamedetails_gameinstanceid' 			=> isset($fundtransferrequest_game['gameinstanceid'])?$fundtransferrequest_game['gameinstanceid']:null,
						'gamedetails_gamesessionid' 			=> isset($fundtransferrequest_game['gamesessionid'])?$fundtransferrequest_game['gamesessionid']:null,
						'gamedetails_gametypeid' 				=> isset($fundtransferrequest_game['gametypeid'])?$fundtransferrequest_game['gametypeid']:null,
						'gamedetails_gametypename' 				=> isset($fundtransferrequest_game['gametypename'])?$fundtransferrequest_game['gametypename']:null,
						'gamedetails_keyname' 					=> isset($fundtransferrequest_game['keyname'])?$fundtransferrequest_game['keyname']:null,
						'gamedetails_name' 						=> isset($fundtransferrequest_game['name'])?$fundtransferrequest_game['name']:null,
						'bonusdetails_bonusbalanceid'			=> isset($fundtransferrequest['bonusdetails']['bonusbalanceid'])?$fundtransferrequest['bonusdetails']['bonusbalanceid']:null,
						'bonusdetails_couponid'					=> isset($fundtransferrequest['bonusdetails']['couponid'])?$fundtransferrequest['bonusdetails']['couponid']:null,
						'bonusdetails_coupontypeid'				=> isset($fundtransferrequest['bonusdetails']['coupontypeid'])?$fundtransferrequest['bonusdetails']['coupontypeid']:null,
						'bonusdetails_couponcode'				=> isset($fundtransferrequest['bonusdetails']['couponcode'])?$fundtransferrequest['bonusdetails']['couponcode']:null,
						'balance_before' 						=> 0,
						'balance_after' 						=> 0,
						'raw_data' 								=> json_encode($request),
						'is_valid_transaction' 					=> 1,
						'is_refunded' 							=> 0,
						'response_result_id' 					=> null,
						'player_id' 							=> 0,
						
					);
					
			$temp['external_uniqueid'] = $temp['fundinfo_transferid'];

			$timestamp = strtotime($temp['fundinfo_dtevent']);			
			$timestamp = date('Y-m-d H:i:s', $timestamp);
			$temp['fundinfo_dtevent_parsed'] = $timestamp;
			
			$timestamp = strtotime($temp['dtsent']);			
			$timestamp = date('Y-m-d H:i:s', $timestamp);
			$temp['dtsent_parsed'] = $timestamp;

			$record[] = $temp;
		}
		
		return $record;
	}

	public $soapClient;

	public function getGames(){
		$this->soapClient = new SoapClient(Habanero_service_api_config::WebServiceEndpoint . "?wsdl", array('trace' => 1));

		$gamesrequest = new Habanero_service_api_game_request();
		$response = $this->soapClient->GetGames(array(
				"req" => $gamesrequest
			));
			echo "<pre>";
			$response = $this->objectToArray($response);
			print_r($response);
		return;
	}

	public function getErrorSuccessMessage($code){
		$message = '';

		switch ($code) {
			case self::INVALID_TOKEN:
				$message = lang('Invalid Auth Token.');
				break;
			case self::INVALID_PASSKEY:
				$message = lang('Invalid Passkey.');
				break;
			case self::CANNOT_FIND_PLAYER:
				$message = lang('Cannot find player.');
				break;
			case self::INSUFFICIENT_FUNDS:
				$message = lang('Insufficient funds.');
				break;
			case self::ERROR_CREDIT:
				$message = lang('Cannot credit amount.');
				break;
			case self::ERROR_DEBIT:
				$message = lang('Cannot debit amount.');
				break;
			case self::ERROR_REFUND:
				$message = lang('Could Not Refund.');
				break;
			case self::ERROR_RECREDIT:
				$message = lang('Could Not Recredit.');
				break;
			case self::ALREADY_CREDITED:
				$message = lang("Orignal Credit was done - so no need to do it again.");
				break;
			case self::DONE_RECREDIT:
				$message = lang("Original request never credited, so we did the re-credit");
				break;
			case self::DONE_CREDIT:
				$message = lang("Successfully credited an amount.");
				break;
			case self::DONE_DEBIT:
				$message = lang("Successfully debit an amount.");
				break;
			case self::INVALID_TRANSFERID:
				$message = lang("Invalid TransferID.");
				break;
			case self::INVALID_TRANSACTION:
				$message = lang("Invalid Transaction.");
				break;

				
				
			default:
				$message = "Unknown";
				break;
		}
		
		return $message;
	}

	public function parseRequest(){
		$request_json = file_get_contents('php://input');
		$this->request = json_decode($request_json);
		return;
	}

	public function getValidPlatformId(){
		$this->game_platform_id = HABANERO_SEAMLESS_GAMING_API;
		$multiple_currency_domain_mapping = (array)@$this->utils->getConfig('habanero_multiple_currency_domain_mapping');
		if (array_key_exists($this->host_name,$multiple_currency_domain_mapping) && !empty($multiple_currency_domain_mapping)) {
		    $this->game_platform_id  = $multiple_currency_domain_mapping[$this->host_name];
		}

		return;
	}

	public function isValidPassKey($key){
		if($this->game_api->getPassKey() <> $key){	
			return false;
		}
		return true;
	}
	
	public function objectToArray($obj) {
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = $this->objectToArray($val);
			}
		}
		else $new = $obj;
		return $new;       
	}

	public function isTransferIdExist($transfer_id){
		$transaction = $this->habanero_transactions->getTransation($transfer_id);

		if ($transaction == null) {
			return false;
		} else {
			return true;
		}
	}

	public function isTransferIdRetry($transfer_id){		
		return $this->habanero_transactions->isTransferIdRetry($transfer_id);
	}

	public function debitCreditAmount($params, $mode = 'credit'){
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (debitCreditAmount)", $params, $mode);

		$player_id			= $params['player_id'];
		$player_name 		= $params['player_name'];
		$transfer_id 		= $params['transfer_id'];
		$amount 			= abs($params['amount']);		
		$controller 		= $this;
		$previous_balance 	= $this->game_api->queryPlayerBalance($player_name)['balance'];
		$success = false;

		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (debitCreditAmount) getValidPlatformId: " . $this->game_platform_id);
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (debitCreditAmount) previous_balance: " . $previous_balance);
		$insufficient_balance = false;
		
		if($amount<>0){
			if($mode=='debit'){
				//check if fund is sufficient
				if($previous_balance >= $amount){
					$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
						return $controller->wallet_model->decSubWallet($player_id, $controller->game_platform_id, $amount);			
					});
				}else{
					$insufficient_balance = true;
				}
			}else{
				$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
					return $controller->wallet_model->incSubWallet($player_id, $controller->game_platform_id, $amount);			
				});	
			}
		}else{
			$success = true;
		}				

		$after_balance = $this->game_api->queryPlayerBalance($player_name)['balance'];
		$this->utils->debug_log("HABANERO SEAMLESS SERVICE: (debitCreditAmount) after_balance: " . $after_balance);
		return array($success, $previous_balance, $after_balance, $insufficient_balance);
	}

}

///END OF FILE////////////