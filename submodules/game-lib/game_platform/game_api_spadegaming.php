<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';
set_include_path(dirname(__FILE__) . '/../unencrypt/phpseclib');
include_once 'Crypt/RSA.php';

class Game_api_spadegaming extends Abstract_game_api {

	private $api_url;
	private $currency;
	private $language;
	private $merchantCode;
	private $currentProcess;
	private $game_url;

	const API_deposit = 'deposit';
	const API_withraw = 'withdraw';
	const API_getAcctIinfo = 'getAcctInfo';
	const API_AUTHORIZE = '/auth/';
	const API_getBetHistory = 'getBetHistory';
	const ORIGINAL_LOGS_TABLE_NAME = "spadegaming_game_logs";
	const MD5_FIELDS_FOR_ORIGINAL = ['ticketId','acctId','categoryId','gameCode','ticketTime','betAmount','winLoss','result','roundId'];
	const MD5_FLOAT_AMOUNT_FIELDS = ['betAmount','winLoss','balance'];
	const MD5_FIELDS_FOR_MERGE = ['start_at','end_at','bet_at','game_code','result_amount','bet_amount','after_balance','result'];
	const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE = ['result_amount','bet_amount','after_balance'];

	const DEFAULT_CODE_FOR_SUCCESS_CALLBACK = 0;


	const URI_MAP = array(
		self::API_createPlayer		 => 'deposit',
		self::API_depositToGame 	 => 'deposit',
		self::API_withdrawFromGame 	 => 'withdraw',
		self::API_queryPlayerInfo	 => 'getAcctInfo',
		self::API_isPlayerExist		 => 'getAcctInfo',
		self::API_syncGameRecords	 => 'getBetHistory',
		self::API_queryForwardGame	 => '/auth/'
	);

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->currency = $this->getSystemInfo('currency');
		$this->language = $this->getSystemInfo('language');
		$this->merchantCode = $this->getSystemInfo('merchantCode');
		$this->game_url = $this->getSystemInfo('game_url');

		# init RSA
		$this->rsa = new Crypt_RSA();
		$this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$this->rsa->setHash('md5');
	}

	public function getPlatformCode() {
		return SPADE_GAMING_API;
	}

	public function generateUrl($apiName, $params) {
		return $this->api_url;
	}


	public function getHttpHeaders($params){
		return array(	"API" => $params['method'],
						"DataType" => "JSON");
	}

	/*protected function customHttpCall($ch, $params) {
		//unset($params["method"]); //unset action not need on params
		//curl_setopt($ch, CURLOPT_POST, TRUE);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params,true));
	}*/

	

	protected function customHttpCall($ch, $params) {
		unset($params["method"]); //unset action not need on params
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true ); 
  		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
  		//curl_setopt( $ch, CURLOPT_TIMEOUT, 60 ); 
	}


	public function callback($result = null, $platform = 'web') {
		$success = false;
		$this->CI->load->model(array('common_token', 'player_model', 'affiliatemodel', 'users','game_provider_auth'));
		
		
		
		if($platform == 'web'){
			$id = $this->CI->common_token->getPlayerIdByToken($result['token']);
			$this->CI->utils->debug_log('Check id ====================================>', $id);
			$this->CI->utils->debug_log('Check token ====================================>', $result['token']);
			if (!empty($id)) {
				$success = true;
				$playerInfo = $this->CI->player_model->getPlayerInfoById($id);
				$this->CI->utils->debug_log('Check infoooooooo ====================================>', $playerInfo);
			}
			if ($success) {
				$this->CI->utils->debug_log('Check SPADE_GAMING_API REQUEST (Callback) ====================================>', $result);
				$balance = $this->queryPlayerBalance($playerInfo['username']);
				$this->CI->utils->debug_log('Check SPADE_GAMING_API balance (Callback) ====================================>', $balance);
				$params = array(
					'acctInfo' => [
						"acctId" => $result['acctId'],
						"balance" => $balance['balance'],
						"userName" => $result['acctId'],
						"currency" => $this->currency,
					],
					'merchantCode' => $this->merchantCode,
					'msg' => "success",
					'code' => self::DEFAULT_CODE_FOR_SUCCESS_CALLBACK,
					'serialNo' => $result['serialNo'],
				);
				$this->CI->utils->debug_log('Check SPADE_GAMING_API RESPONSE (Callback) ====================================>', $params);
				return $params;
			}
			else{
				return "Acct Not Found (50100)";
			}
		}
		else{
			$this->CI->utils->debug_log('Check SPADE_GAMING_API REQUEST (Callback) ====================================>', $result);
			$playerInfo = (array)$this->CI->game_provider_auth->getPlayerInfoByGameUsername($result['acctId'],SPADE_GAMING_API);
			$this->CI->utils->debug_log('Check infoooooooo ====================================>', $playerInfo);
			if(!empty($playerInfo)){
				$balance = $this->queryPlayerBalance($playerInfo['username']);
				$this->CI->utils->debug_log('Check SPADE_GAMING_API balance (Callback) ====================================>', $balance);
				$params = array(
					'acctInfo' => [
						"acctId" => $result['acctId'],
						"balance" => $balance['balance'],
						"userName" => $result['acctId'],
						"currency" => $this->currency,
					],
					'merchantCode' => $this->merchantCode,
					'msg' => "success",
					'code' => self::DEFAULT_CODE_FOR_SUCCESS_CALLBACK,
					'serialNo' => $result['serialNo'],
				);
				$this->CI->utils->debug_log('Check SPADE_GAMING_API RESPONSE (Callback) ====================================>', $params);
				return $params;
			}
			else{
				return "Acct Not Found (50100)";
			}
		}
	}

	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = false;

        $success = ($resultArr['code']==0) ? true : '';

        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('Spade got error ======================================>', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
            $success = false;
        }
        //echo $success ? 'true' : 'false';exit();
        return $success;
	}

	public function createPlayer($userName, $playerId, $password, $email = null, $extra = null) {

			parent::createPlayer($userName, $playerId, $password, $email, $extra);
			$playerName = $this->getGameUsernameByPlayerUsername($userName);
			$serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');
	        $context = array(
	            'callback_obj' => $this,
	            'callback_method' => 'processResultForCreatePlayer',
	            'playerName' => $playerName,
	            'sbe_playerName' => $userName,
	            'amount' => 0.1,
	            'serialNo'=>$serialNo,
	        );
	        
	        $params = array(
	            "acctId" 		=> $playerName,
				"amount" 		=> 0.1,
				"currency" 		=> $this->currency,
				"merchantCode" 	=> $this->merchantCode,
				"serialNo" 		=> $serialNo,
				"method" 		=> self::URI_MAP[self::API_createPlayer]
	        );
	        $this->utils->debug_log("CreatePlayer params ============================>", $params);
	        return $this->callApi(self::URI_MAP[self::API_createPlayer], $params, $context);     
	}

	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log('processResultForCreatePlayer ==========================>', $resultJsonArr);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
		$success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);

		#withdraw deposit amount  on create player
		$this->withdrawFromGame($sbe_playerName, 0.1, null,true); 

		return array($success, $resultJsonArr);
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		return $this->returnUnimplemented();
	}

	public function processResultForChangePassword($params) {
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$newPassword = $this->getVariableFromContext($params, 'newPassword');
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$resultXml = (array) $this->getResultXmlFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultXml, $playerName);

		if ($success) {
			//sync password to game_provider_auth
			$this->updatePasswordForPlayer($playerId, $newPassword);
		}

		return array($success, $resultXml);
	}

	public function isPlayerExist($userName) {
		$playerName = $this->getGameUsernameByPlayerUsername($userName);
        $serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForIsPlayerExist',
            'playerName' => $playerName,
            'sbe_playerName' => $userName,
            'serialNo'=>$serialNo,
        );
        
        $params = array(
            "acctId" 		=> $playerName,
			"pageIndex" 	=> 0,
			"merchantCode" 	=> $this->merchantCode,
			"serialNo" 		=> $serialNo,
			"method" 		=> self::URI_MAP[self::API_isPlayerExist]
        );

        $this->utils->debug_log("Query params ============================>", $params);
        return $this->callApi(self::URI_MAP[self::API_isPlayerExist], $params, $context);
	}

	public function processResultForIsPlayerExist($params) {
        $resultArr = $this->getResultJsonFromParams($params);

		// if not exist set success to true then exists' to false
		// if exist set success to false then 'exists' to true
		$success = true;
		$exist = false;
		if (isset($resultArr['list'])) {
			# $success = count($resultArr['list']) == 0 ? false : true;
			if (count($resultArr['list']) > 0) {
				$success = false;
				$exist = true;
			}
		}
        $this->utils->debug_log("Check player if exist ============================>", $success);
        $result['exists'] = $exist;

		return array($success, $result);
	}

	public function queryPlayerBalance($userName) {
		$playerName = $this->getGameUsernameByPlayerUsername($userName);
        $serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForQueryPlayerBalance',
            'playerName' => $playerName,
            'sbe_playerName' => $userName,
            'serialNo'=>$serialNo,
        );
        
        $params = array(
            "acctId" 		=> $playerName,
			"pageIndex" 	=> 0,
			"merchantCode" 	=> $this->merchantCode,
			"serialNo" 		=> $serialNo,
			"method" 		=> self::URI_MAP[self::API_queryPlayerInfo]
        );

        //print_r($params);exit();
        $this->utils->debug_log("Query params ============================>", $params);
        return $this->callApi(self::URI_MAP[self::API_queryPlayerInfo], $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {

		$playerName = $this->getVariableFromContext($params, 'playerName');
        $sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        // $this->utils->debug_log("check Query params ============================>", $resultArr['list'][0]['balance']);
        $success = false;
       	$result['balance'] = @floatval($resultArr['list'][0]['balance']);
        if ($this->processResultBoolean($responseResultId, $resultArr,$playerName)) {
        	$success = true;
        	if ($playerId = $this->getPlayerIdInGameProviderAuth($playerName)) {
				$this->CI->utils->debug_log('SPADE GAME API query balance playerId', $playerId, 'playerName', $playerName, 'balance', $result['balance']);
			} else {
				$this->CI->utils->debug_log('SPADE GAME API cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			}
        }
       	$result['exists'] = true;
		return array($success, $result);
	}

	public function depositToGame($userName, $amount, $transfer_secure_id=null){
		
        $playerName = $this->getGameUsernameByPlayerUsername($userName);
        $serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForDepositToGame',
            'playerName' => $playerName,
            'sbe_playerName' => $userName,
            'amount' => $amount,
            'external_transaction_id'=>$serialNo,
        );
        
        $params = array(
        	//"method" => self::API_deposit,
            "acctId" 		=> $playerName,
			"amount" 		=> $amount,
			"currency" 		=> $this->currency,
			"merchantCode" 	=> $this->merchantCode,
			"serialNo" 		=> $serialNo,
			"method" 		=> self::URI_MAP[self::API_depositToGame]
        );
        $this->utils->debug_log("Deposit params ============================>", $params);
        return $this->callApi(self::URI_MAP[self::API_depositToGame], $params, $context);

	}

	public function processResultForDepositToGame($params) {
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        $this->utils->debug_log("check Deposit params ============================>", $params);
        if ($success) {
			//get current sub wallet balance
			// $playerBalance = $this->queryPlayerBalance($sbe_playerName);
			//for sub wallet
			// $afterBalance = @$playerBalance['balance'];

			// if(!empty($afterBalance)){
			// 	$result["currentplayerbalance"] = $afterBalance;
			// }

			//update
			// $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			// if ($playerId) {
			// 	//deposit
			// 	$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,$this->transTypeMainWalletToSubWallet());
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			// }
			// 
			$result["external_transaction_id"] = $resultArr['serialNo'];
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
		} else {
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['code']);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}
		
		return array($success, $result);
    }

    function withdrawFromGame($userName, $amount, $transfer_secure_id=null,$notRecordTransaction=false) {
        $playerName = $this->getGameUsernameByPlayerUsername($userName);
        $serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForWithdrawFromGame',
            'playerName' => $playerName,
            'sbe_playerName' => $userName,
            'amount' => $amount,
            'external_transaction_id'=>$serialNo,
            'recordTransaction'=>$notRecordTransaction,
        );
        
        $params = array(
        	//"method" => self::API_deposit,
            "acctId" 		=> $playerName,
			"amount" 		=> $amount,
			"currency" 		=> $this->currency,
			"merchantCode" 	=> $this->merchantCode,
			"serialNo" 		=> $serialNo,
			"method" 		=> self::URI_MAP[self::API_withdrawFromGame]
        );
        $this->utils->debug_log("Withraw params ============================>", $params);
        return $this->callApi(self::URI_MAP[self::API_withdrawFromGame], $params, $context);
    }

    function processResultForWithdrawFromGame($params) {
       	$playerName = $this->getVariableFromContext($params, 'playerName');
        $sbe_playerName = $this->getVariableFromContext($params, 'sbe_playerName');
        $amount = $this->getVariableFromContext($params, 'amount');
        $recordTransaction = $this->getVariableFromContext($params, 'recordTransaction');
        $external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultArr = $this->getResultJsonFromParams($params);
        $success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

        $result = array(
            'response_result_id' => $responseResultId,
            'external_transaction_id'=>$external_transaction_id,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN
        );

        $this->utils->debug_log("check Withraw params ============================>", $params);
        if ($success) {
			//get current sub wallet balance
			// $playerBalance = $this->queryPlayerBalance($sbe_playerName);
			//for sub wallet
			// $afterBalance = @$playerBalance['balance'];

			// if(!empty($afterBalance)){
			// 	$result["currentplayerbalance"] = $afterBalance;
			// }
			// //update
			// $playerId = $this->getPlayerIdInGameProviderAuth($playerName);
			// if ($playerId) {
			// 	//withdraw
			// 	if(!$recordTransaction){
			// 		$this->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,$this->transTypeSubWalletToMainWallet());
			// 	}
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $playerName . ' getPlayerIdInGameProviderAuth');
			// }
			$result['didnot_insert_game_logs']=true;
			$result["external_transaction_id"] = $resultArr['serialNo'];
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['code']);
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}
		
		return array($success, $result);
    }

    public function getTransferErrorReasonCode($apiErrorCode) {
		$reasonCode = self::COMMON_TRANSACTION_STATUS_APPROVED;

		switch ((int)$apiErrorCode) {
			case 2: # System Error
				$reasonCode = self::REASON_FAILED_FROM_API;
				break;
			case 3:	# Api Down
				$reasonCode = self::REASON_API_MAINTAINING;
				break;
			case 105:	# Missing parameters
			case 106:	# Invalid parameters
				$reasonCode = self::REASON_INCOMPLETE_INFORMATION;
				break;
			case 107:	# Duplicated Serial No.
				$reasonCode = self::REASON_DUPLICATE_TRANSFER;
				break;
			case 108:	# Merchant Key error
			case 10113:	# Merchant not found
				$reasonCode = self::REASON_AGENT_NOT_EXISTED;
				break;
			case 50100:	# Accnt not found
				$reasonCode = self::REASON_NOT_FOUND_PLAYER;
				break;
			case 50110:	# Insufficient balance
				$reasonCode = self::REASON_NO_ENOUGH_BALANCE;
				break;
			case 50110:	# Accnt not found
			case 50111:	# Exceed max amount
				$reasonCode = self::REASON_NO_ENOUGH_BALANCE;
				break;
			case 50112:	# Currency Invalid
				$reasonCode = self::REASON_CURRENCY_ERROR;
				break;
			case 50113:	# Amount invalid
				$reasonCode = self::REASON_INVALID_TRANSFER_AMOUNT;
				break;
			case 50113:	# Amount invalid
				$reasonCode = self::REASON_INVALID_TRANSFER_AMOUNT;
				break;
		}

		return $reasonCode;
	}


	

	public function queryForwardGame($userName, $extra = null) {
		//$url 			= "http://api.egame.staging.sgplay.net/okada/auth/?acctId=xxxx&language=en_US&token=xxxx&game=xxxx";
		# For gamegateway
		$extra['game'] = isset($extra['game_code'])?$extra['game_code']:$extra['game'];

		$this->CI->load->model('common_token');
		$playerName 	= $this->getGameUsernameByPlayerUsername($userName);
		$playerId 		= $this->getPlayerIdFromUsername($userName);
		$token 			= $this->CI->common_token->createTokenBy($playerId, 'player_id');
		$merchantCode 	= strtolower($this->merchantCode);
		$game_url 		= $this->game_url;
		$auth 			= self::API_AUTHORIZE."?";
		$game 			= "S-DG03";
		
		$params = array(
			"acctId" 	=> $playerName,
			"language" 	=> $this->language,
            "token"		=> $token,
			"menumode" => "off"
		);
		# added param for mobile
		if(@$extra['is_mobile']){
			$params["mobile"] = "true";
		}
		$url_params = http_build_query(array_merge($params,$extra));

		$generateUrl 	= $game_url.'/'.$merchantCode.$auth.$url_params;
		$data = [
            'url' => $generateUrl,
            'success' => true
        ];
        $this->utils->debug_log(' Spade generateUrl - =================================================> ' . $generateUrl);
        return $data;
		/*echo "<pre>";
		print_r($data);
		exit();*/

	}

	public function syncOriginalGameLogs($token = false) {
		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
		$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
		$startDate->modify($this->getDatetimeAdjust());
		//observer the date format
		$startDate = $startDate->format('Y-m-d H:i:s');
		$endDate   = $endDate->format('Y-m-d H:i:s');
		$result = array();

		$result[] = $this->CI->utils->loopDateTimeStartEnd($startDate, $endDate, '+24 hours', function($startDate, $endDate)  {
			$startDate = $startDate->format('Ymd\THis');
			$endDate = $endDate->format('Ymd\THis');
			$serialNo = substr(date('YmdHis'), 2) . random_string('alnum', 5);//'S' . random_string('unique');
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForSyncGameRecords',
				'startDate' => $startDate,
				'endDate' => $endDate,
			);

			$params = array(
				'beginDate' 	=> $startDate,
				'endDate' 		=> $endDate,
				'pageIndex' 	=> 1,
				'merchantCode'	=> $this->merchantCode,
				'serialNo'		=> $serialNo,
				'method'		=> self::URI_MAP[self::API_syncGameRecords]
			);


			$rlt =  $this->callApi(self::URI_MAP[self::API_syncGameRecords], $params, $context);
			$currentPage = 1;
			$totalPage = $rlt['page_count'];
			while($currentPage < $totalPage) {
				$params['pageIndex'] = $currentPage + 1;
			    $callApiByPage = $this->callApi(self::API_syncGameRecords, $params, $context);

			    if($callApiByPage['success']){
			    	$currentPage = $currentPage + 1;
			    } else{
			    	$currentPage = $totalPage;
			    }
			}
			return true;
		});

		return array(true, $result);

	}

	public function processGameRecords(&$gameRecords, $responseResultId) {
		if(!empty($gameRecords)){
			foreach($gameRecords as $index => $record) {
				$insertRecord = array();
				//Data from Spade API
				$insertRecord['ticketId'] 		= isset($record['ticketId']) ? $record['ticketId'] : NULL;
				$insertRecord['acctId'] 		= isset($record['acctId']) ? $record['acctId'] : NULL;
				$insertRecord['categoryId'] 	= isset($record['categoryId']) ? $record['categoryId'] : NULL;
				$insertRecord['gameCode'] 		= isset($record['gameCode']) ? $record['gameCode'] : NULL;
				$insertRecord['ticketTime'] 	= isset($record['ticketTime']) ? $this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['ticketTime']))) : NULL;
				$insertRecord['betIp'] 			= isset($record['betIp']) ? $record['betIp'] : NULL;
				$insertRecord['betAmount'] 		= isset($record['betAmount']) ? $record['betAmount'] : NULL;
				$insertRecord['winLoss'] 		= isset($record['winLoss']) ? $record['winLoss'] : NULL;
				$insertRecord['currency'] 		= isset($record['currency']) ? $record['currency'] : NULL;
				$insertRecord['result'] 		= isset($record['result']) ? $record['result'] : NULL;
				$insertRecord['jackpotAmount'] 	= isset($record['jackpotAmount']) ? $record['jackpotAmount'] : NULL;
				$insertRecord['luckyDrawId'] 	= isset($record['luckyDrawId']) ? $record['luckyDrawId'] : NULL;
				$insertRecord['completed'] 		= isset($record['completed']) ? $record['completed'] : NULL;
				$insertRecord['roundId'] 		= isset($record['roundId']) ? $record['roundId'] : NULL;
				$insertRecord['sequence'] 		= isset($record['sequence']) ? $record['sequence'] : NULL;
				$insertRecord['channel'] 		= isset($record['channel']) ? $record['channel'] : NULL;
				$insertRecord['balance'] 		= isset($record['balance']) ? $record['balance'] : NULL;
				$insertRecord['jpWin'] 			= isset($record['jpWin']) ? $record['jpWin'] : NULL;

				//extra info from SBE
				$insertRecord['Username'] = isset($record['acctId']) ? strtolower($record['acctId']) : NULL;
				$insertRecord['PlayerId'] = NULL;
				$insertRecord['external_uniqueid'] = $insertRecord['ticketId']; //add external_uniueid for og purposes
				$insertRecord['response_result_id'] = $responseResultId;
				$gameRecords[$index] = $insertRecord;
				unset($insertRecord);
			}
		}
	}

	public function processResultForSyncGameRecords($params) {
		$this->CI->load->model(array('spadegaming_game_logs', 'player_model','original_game_logs_model'));
        $resultArr = $this->getResultJsonFromParams($params);
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $success = isset($resultArr['list']) && count($resultArr['list']);
        $result = array(
			'data_count'=> 0,
			'page_count'=> 1
		);
		if ($success) {
			$result['page_count'] = $resultArr['pageCount'];
			$gameRecords = $resultArr['list'];
			// echo "<pre>";
			// print_r($resultArr);exit();
			if (!empty($gameRecords)) {
				$this->processGameRecords($gameRecords, $responseResultId);
				list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
	                self::ORIGINAL_LOGS_TABLE_NAME,
	                $gameRecords,
	                'external_uniqueid',
	                'external_uniqueid',
	                self::MD5_FIELDS_FOR_ORIGINAL,
	                'md5_sum',
	                'id',
	                self::MD5_FLOAT_AMOUNT_FIELDS
	            );

				$this->CI->utils->debug_log('after process available rows', 'gamerecords ->',count($gameRecords), 'insertrows->',count($insertRows), 'updaterows->',count($updateRows));

				if (!empty($insertRows)) {
					$result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert');
				}
				unset($insertRows);

				if (!empty($updateRows)) {
					$result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update');
				}
				unset($updateRows);		
			}
		}
		return array($success, $result);
	}

	private function updateOrInsertOriginalGameLogs($data, $queryType){
        $dataCount=0;
        if(!empty($data)){
            foreach ($data as $record) {
                if ($queryType == 'update') {
                	$record['updated_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->updateRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
                } else {
                    unset($record['id']);
                    $record['created_at'] = $this->utils->getNowForMysql();
                    $this->CI->original_game_logs_model->insertRowsToOriginal(self::ORIGINAL_LOGS_TABLE_NAME, $record);
                }
                $dataCount++;
                unset($record);
            }
        }

        return $dataCount;
    }

    /**
	 * overview : syncMergeTogameLogs
	 *
	 * @param $token
	 * @return array
	 */
	public function syncMergeToGameLogs($token) {

        $enabled_game_logs_unsettle=false;
        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            $enabled_game_logs_unsettle);

	}

	/**
     * queryOriginalGameLogs
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  bool   $use_bet_time
     * @return array
     */
    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){

        $sqlTime='sg.ticketTime >= ? and sg.ticketTime <= ?';
		$sql = <<<EOD
SELECT sg.id as sync_index,
sg.UserName as player_username,
sg.external_uniqueid,
sg.ticketTime AS start_at,
sg.ticketTime AS end_at,
sg.ticketTime AS bet_at,
sg.gameCode AS game_code,
sg.gameCode AS game,
sg.response_result_id,
sg.winLoss AS result_amount,
sg.betAmount AS bet_amount,
sg.betAmount AS real_bet_amount,
sg.balance AS after_balance,
sg.updated_at,
sg.md5_sum,
sg.result,
sg.ticketId,
sg.roundId,
sg.ticketId as round_number,

game_provider_auth.player_id,

gd.id as game_description_id,
gd.game_name as game_description_name,
gd.game_type_id

FROM spadegaming_game_logs as sg
LEFT JOIN game_description as gd ON sg.gameCode = gd.game_code AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON sg.UserName = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE

{$sqlTime}

EOD;

        $params=[$this->getPlatformCode(), $this->getPlatformCode(),
        $dateFrom,$dateTo];

        $result = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
       	return $result;
    }

	/**
     * it will be used on processUnsettleGameLogs and commonUpdateOrInsertGameLogs
     *
     * @param  array $row
     * @return array $params
     */
    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
    	$extra_info=['trans_amount'=>$row['bet_amount']];
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
        $row['bet_details']= $row['result'];
        $row['status'] = Game_logs::STATUS_SETTLED;
    }

    /**
	 * overview : get game description information
	 *
	 * @param $row
	 * @param $unknownGame
	 * @param $gameDescIdMap
	 * @return array
	 */
	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;
		$game_type_id = null;
		if (isset($row['game_description_id'])) {
			$game_description_id = $row['game_description_id'];
			$game_type_id = $row['game_type_id'];
		}

		if(empty($game_description_id)){
			$gameDescId=$this->CI->game_description_model->processUnknownGame($this->getPlatformCode(),
				$unknownGame->game_type_id, $row['game'], $row['game_code']);
		}

		return [$game_description_id, $game_type_id];
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

	/**
	 * overview : get game time to server time
	 *
	 * @return string
	 */
	/*public function getGameTimeToServerTime() {
		//return '+8 hours';
	}*/

	/**
	 * overview : get server time to game time
	 *
	 * @return string
	 */
	/*public function getServerTimeToGameTime() {
		//return '-8 hours';
	}*/

	public function queryTransaction($transactionId, $extra) {
		return $this->returnUnimplemented();
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

	
}

/*end of file*/