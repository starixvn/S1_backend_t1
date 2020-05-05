<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * Getting platform code
 * * Generate URL
 * * Generate Soap Method
 * * Prepares Data below
 * * * Currency for Create Account
 * * * Profile List Id
 * * * Currency for Deposit
 * * * My Balance
 * * Create Player
 * * Login/Logout
 * * Deposit To game
 * * Withdraw from Game
 * * Change Password
 * * Check Player Balance
 * * Check Transaction
 * * Check Game records
 * * Check Forward Game
 * * Synchronize Original Game Logs
 * * Authenticate Soap
 * * Make Soap Options
 * * Check if Player Exist
 * * Check Player Information
 * * Block/Unblock Player
 * * Check Player Daily Balance
 * * Check login Status
 * * Check Total Betting Amount

 * Behaviors not implemented
 * * Check Fund Transfer
 * * Reset Player
 * * Update Player Information
 * * Batch Check Player Balance
 * * Check Player's Available Balance
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 *
 *
 *
 * @see Redirect redirect to game page
 *
 * @category Game API
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Game_api_hb extends Abstract_game_api {

	private $api_domain;
	private $brandId;
	private $APIKey;
	private $PlayerHostAddress;
	private $CurrencyCode;
	private $userAgent;

	const API_getJackpots = 'GetJackpots';

	const URI_MAP = [
        self::API_changePassword => 'UpdatePlayerPassword'
	];

	const JSON_API=[
		self::API_changePassword,
	];

	//FIXME
	// const ERROR_CODE_NOT_FOUND_PLAYER='';

	public function __construct() {
		parent::__construct();
		$this->PlayerHostAddress = ''; //$_SERVER['REMOTE_ADDR'];
		$this->userAgent = ''; //$_SERVER['HTTP_USER_AGENT'];
		$this->api_domain = $this->getSystemInfo('url');
		$this->APIKey = $this->getSystemInfo('live_secret');
		$this->brandId = $this->getSystemInfo('live_key');
        $this->CurrencyCode = $this->getSystemInfo('currency', "CNY");
        $this->lobby_url = $this->getSystemInfo('lobby_url', null);
		$this->use_local_wsdl = $this->getSystemInfo('use_local_wsdl',false);
		$this->use_local_staging_wsdl = $this->getSystemInfo('use_local_staging_wsdl', false);
		$this->allow_fullscreen = $this->getSystemInfo('allow_fullscreen', true);
		$this->use_referrer_lobby_url = $this->getSystemInfo('use_referrer_lobby_url', false);
		$this->language = $this->getSystemInfo('language');

        //don't support
        $this->is_enabled_direct_launcher_url=$this->getSystemInfo('is_enabled_direct_launcher_url', false);
        $this->is_hide_home_button_in_mobile = $this->getSystemInfo('is_hide_home_button_in_mobile', true);
		$this->is_hide_home_button_in_desktop = $this->getSystemInfo('is_hide_home_button_in_desktop', true);

		//extra info if needed to check player info during creation
		$this->check_player_info = $this->getSystemInfo('check_player_info', true);
		$this->json_api_url=$this->getSystemInfo('json_api_url', 'https://ws-a.insvr.com/jsonapi');

		$this->sync_sleep=$this->getSystemInfo('sync_sleep', 0);
	}

	/**
	 * overview : get platform code
	 * @return int
	 */
	public function getPlatformCode() {
		return HB_API;
	}

	/**
	 * overview : get call type
	 *
	 * @param $apiName
	 * @param $params
	 * @return int
	 */
	protected function getCallType($apiName, $params) {

		if(in_array($apiName, self::JSON_API)){
			return self::CALL_TYPE_HTTP;
		}

		//overwrite in sub-class
		return self::CALL_TYPE_SOAP;
	}

	/**
	 * overview : generate url
	 *
	 * @param $apiName
	 * @param $params
	 * @return string
	 */
	public function generateUrl($apiName, $params) {
		if($this->use_local_staging_wsdl) {
			return realpath(dirname(__FILE__)).'/wsdl/38/'.$this->getSystemInfo('wsdl_filename', 'hb_staging_server.wsdl');
		}

		if(in_array($apiName, self::JSON_API)){
			$methodName=self::URI_MAP[$apiName];
			return rtrim($this->json_api_url,'/').'/'.$methodName;
		}

		if($this->use_local_wsdl){
			return realpath(dirname(__FILE__)).'/wsdl/38/'.$this->getSystemInfo('wsdl_filename', 'hb_live_server.wsdl');
		}else{
			return $this->getSystemInfo('url');
		}
	}

    protected function getHttpHeaders($params) {
        return [
            'Content-Type'=>'application/json'
        ];
    }

	public function customHttpCall($ch, $params){
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
	}

	protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        $statusCode = intval($statusCode, 10);
        // echo $statusCode;exit;
        return $errCode || intval($statusCode, 10) >= 501;
    }

	/**
	 * overview : generate soap method
	 *
	 * @param $apiName
	 * @param $params
	 * @return array
	 */
	protected function generateSoapMethod($apiName, $params) {
		switch ($apiName) {
		case self::API_getJackpots:
			return array('GetJackpots', $params);
			break;
		case self::API_createPlayer:
			return array('LoginOrCreatePlayer', $params);
			break;

		case self::API_queryPlayerInfo:
		case self::API_isPlayerExist:
		case self::API_queryPlayerCurrency:
			return array('QueryPlayer', $params);
			break;

		case self::API_depositToGame:
			return array('DepositPlayerMoney', $params);
			break;

		case self::API_withdrawFromGame:
			return array('WithdrawPlayerMoney', $params);
			break;

		case self::API_queryPlayerBalance:
			return array('QueryPlayer', $params);
			break;

		case self::API_logout:
			return array('LogOutPlayer', $params);
			break;

		case self::API_queryGameRecords:
			return array('GetPlayerGameTransactions', $params);
			break;

		case self::API_syncGameRecords:
			// return array('GetBrandCompletedGameResults', $params);
			return array('GetBrandCompletedGameResultsTemp', $params);
			break;

		case self::API_queryTransaction:
			return array('QueryTransfer', $params);
			break;

		}
		return parent::generateSoapMethod($apiName, $params);
	}

	/**
	 * overview : result after process
	 *
	 * @param $apiName
	 * @param $params
	 * @param $responseResultId
	 * @param $resultText
	 * @param $statusCode
	 * @param null $statusText
	 * @param null $extra
	 * @param null $resultObj
	 * @return array
	 */
	public function afterProcessResult($apiName, $params, $responseResultId, $resultText, $statusCode, $statusText = null, $extra = null, $resultObj = null) {
		return array(false, null);

	}

	/**
	 * overview : get game time to server time
	 *
	 * @return string
	 */
	// public function getGameTimeToServerTime() {
	// 	return '+8 hours';
	// }

	/**
	 * overview : get server time to game time
	 *
	 * @return string
	 */
	// public function getServerTimeToGameTime() {
	// 	return '-8 hours';
	// }

	/**
	 * overview : get prepare data
	 *
	 * @return array
	 */
	public function getPrepareData() {
		$currencyList = $this->getCurrencyForCreateAccount();
		$profileList = $this->getProfileListId();
		$currencyForDepositList = $this->getCurrencyForDeposit();
		$myBalanceList = $this->getMyBalance();
		return array('currencyList' => $currencyList, 'profileList' => $profileList,
			'currencyForDepositList' => $currencyForDepositList,
			'myBalanceList' => $myBalanceList);
	}

	/**
	 * overview : get currency for creating account
	 *
	 * @return array
	 */
	private function getCurrencyForCreateAccount() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetCurrencyForCreateAccount',
		);

		return $this->callApi(self::API_getCurrencyForCreateAccount, array(), $context);
	}

	/**
	 * overview : process result for getCurrencyForCreateAccount
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForGetCurrencyForCreateAccount($params) {
		$responseResultId = $params['responseResultId'];
		$resultObj = $params['resultObj'];

		$success = $this->processResultBoolean($responseResultId, $resultObj->GetCurrenciesForAddAccountResult);
		$this->CI->utils->debug_log('processResultForGetCurrencyForCreateAccount', $resultObj);
		$result = array();
		if ($success) {
			$result['currencyList'] = (array) $resultObj->GetCurrenciesForAddAccountResult;
		}

		return array($success, $result);
	}

	/**
	 * overview : get profile list
	 * @return array
	 */
	private function getProfileListId() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetBettingProfileList',
		);

		return $this->callApi(self::API_getBettingProfileList, array(), $context);
	}

	/**
	 * overview : processing result for get betting profile list
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForGetBettingProfileList($params) {
		$responseResultId = $params['responseResultId'];
		$resultObj = $params['resultObj'];
		// $playerId = $params['context']['playerId'];

		$success = $this->processResultBoolean($responseResultId, $resultObj->GetBettingProfileListResult);
		$this->CI->utils->debug_log($resultObj);

		$result = array();
		if ($success) {
			foreach ($resultObj->GetBettingProfileListResult as $rlt) {
				$result["profileList"][] = $rlt->Id;
				break;
			}
		}

		return array($success, $result);
	}

	/**
	 * overview : get currency for deposit
	 *
	 * @return array
	 */
	private function getCurrencyForDeposit() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetCurrencyForDeposit',
		);

		return $this->callApi(self::API_getCurrencyForDeposit, array(), $context);
	}

	/**
	 * overview : process result for getCurrencyForDeposit
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForGetCurrencyForDeposit($params) {
		$responseResultId = $params['responseResultId'];
		$resultObj = $params['resultObj'];

		$success = $this->processResultBoolean($responseResultId, $resultObj->GetCurrenciesForDepositResult);
		$result = array();
		if ($success) {
			$result['currencyList'] = (array) $resultObj->GetCurrenciesForDepositResult;
		}

		return array($success, $result);
	}

	/**
	 * overview : get my balance
	 *
	 * @return array
	 */
	private function getMyBalance() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetMyBalance',
		);

		return $this->callApi(self::API_getMyBalance, array(), $context);

	}

	/**
	 * overview : process result for getMyBalance
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForGetMyBalance($params) {
		$responseResultId = $params['responseResultId'];
		$resultObj = $params['resultObj'];

		$success = $this->processResultBoolean($responseResultId, $resultObj->GetMyBalanceResult);
		//$this->CI->utils->debug_log($resultObj);
		$result = array();
		if ($success) {
			foreach ($resultObj->GetMyBalanceResult->MemberBalances as $rlt) {
				$result["myBalanceList"][] = (array) $rlt;
				break;
			}
		}

		return array($success, $result);
	}


	public function getJackpots() {
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForGetJackpots',
		);

		$params = array(
			"BrandId" => $this->brandId,
			"APIKey" => $this->APIKey
		);
		$params = array('req' => $params);

		return $this->callApi(self::API_getJackpots, $params, $context);
	}

	public function processResultForGetJackpots($params){
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = json_decode(json_encode($params['resultObj']),true);
		$jackpots = isset($resultObj['GetJackpotsResult']['JackpotInfoDTO'])?$resultObj['GetJackpotsResult']['JackpotInfoDTO']:array();
		$success = isset($resultObj['GetJackpotsResult']['JackpotInfoDTO'])?true:false;
		return array($success, array('jackpots'=>$jackpots,'response_result_id'=>$responseResultId));
	}

	/**
	 * overview : create player game
	 *
	 * @param $playerName
	 * @param $playerId
	 * @param $password
	 * @param null $email
	 * @param null $extra
	 * @return array
	 */
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		if($this->check_player_info){
			$currency_result = $this->queryPlayerCurrency($playerName);
			if($currency_result['success']){
				return array(true,array("message"=>"already exist"));
			}
		}
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->CI->load->helper('string');
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerId' => $playerId,
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'playerPassword' => $password,
		);
		$params = array(
			"Username" => $gameUsername,
			"BrandId" => $this->brandId,
			"PlayerHostAddress" => $this->PlayerHostAddress,
			'UserAgent' => $this->userAgent,
			'KeepExistingToken' => true,
			"APIKey" => $this->APIKey,
			"Password" => $password,
			"CurrencyCode" => $this->CurrencyCode,
		);

		$params = array('req' => $params);
		$results = $this->callApi(self::API_createPlayer, $params, $context);
		return $results;
	}

	/**
	 * overview process result for create player
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForCreatePlayer($params) {
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = isset($params['resultObj']->LoginOrCreatePlayerResult) ? $params['resultObj']->LoginOrCreatePlayerResult : null;
		$playerId = $this->getVariableFromContext((array) $params, 'playerId');
		$playerName = $this->getVariableFromContext((array) $params, 'playerName');
		$playerPassword = $this->getVariableFromContext((array) $params, 'playerPassword');
		$success = $this->processResultBoolean($responseResultId, $resultObj, $playerName);
		$this->CI->utils->debug_log('processResultForCreatePlayer', $resultObj);
		$token = null;
		if(!empty($resultObj)){

			$externalAccountId = isset($resultObj->PlayerId)? $resultObj->PlayerId : null;
			if(!empty($externalAccountId)){
				$this->updateExternalAccountIdForPlayer($playerId, $externalAccountId);
			}
			$resultObj->Message = 'success!';
			$token = isset($resultObj->Token)? $resultObj->Token: null;
		}
		
		$this->CI->utils->debug_log('processResultForCreatePlayer resultObj', $resultObj , 'success',  $success);

		//update register
		if ($success) {
			$this->CI->utils->debug_log('changePass', $playerName ,  $playerPassword);

			// update password
			parent::changePasswordInDB($playerName, $playerPassword);
			
			$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}

		return array($success, array('token' => $token, 'response_result_id'=>$responseResultId));
	}

	/**
	 * @param $playerName
	 * @param null $password
	 * @return array
	 */
	public function login($playerName, $password = null) {
		$password = $this->getPassword($playerName);
		$playerId = $this->getPlayerIdInPlayer($playerName);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->CI->load->helper('string');
		$playerInfo = $this->queryPlayerInfo($playerName);
		$this->CurrencyCode = isset($playerInfo[0]['CurrencyCode']) ? $playerInfo[0]['CurrencyCode'] : $this->CurrencyCode;

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		$params = array(
			"Username" => $gameUsername,
			"BrandId" => $this->brandId,
			"PlayerHostAddress" => $this->PlayerHostAddress,
			"APIKey" => $this->APIKey,
			"Password" => $password['password'],
			"CurrencyCode" => $this->CurrencyCode,
		);
		$params = array('req' => $params);

		$resultsArr = $this->callApi(self::API_createPlayer, $params, $context);
		return $resultsArr;
	}

	/**
	 * overview : process result for login
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForLogin($params) {
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = isset($params['resultObj']->LoginOrCreatePlayerResult)?$params['resultObj']->LoginOrCreatePlayerResult: null;
		$playerId = $this->getVariableFromContext((array) $params, 'playerId');
		$playerName = $this->getVariableFromContext((array) $params, 'playerName');
		$success = $this->processResultBoolean($responseResultId, $resultObj, $playerName);
		$this->CI->utils->debug_log('processResultForLogin', $resultObj);
		$token = null;
		if(!empty($resultObj)){
			$externalAccountId = isset($resultObj->PlayerId)? $resultObj->PlayerId : null;
			if(!empty($externalAccountId)){
				$this->updateExternalAccountIdForPlayer($playerId, $externalAccountId);
			}
			$token = isset($resultObj->Token)? $resultObj->Token: null;
		}
		return array($success, array('token' => $token));
	}

	/**
	 * overview : deposit to game
	 *
	 * @param $playerName
	 * @param $amount
	 * @param null $transfer_secure_id
	 * @return array
	 */
	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
		$password = $this->getPassword($playerName);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$transactionReferenceNumber = 'DEP' . random_string('alnum');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
			'external_transaction_id' => $transactionReferenceNumber,
		);
		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			'Username' => $gameUsername,
			'Password' => $password['password'],
			'CurrencyCode' => $this->CurrencyCode,
			'Amount' => $this->dBtoGameAmount($amount),
			'RequestId' => $transactionReferenceNumber,
		);

		$params = array('req' => $params);

		return $this->callApi(self::API_depositToGame, $params, $context);

	}

	/**
	 * overview : process result for deposit to game
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForDepositToGame($params) {
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$gameUsername = $this->getVariableFromContext((array) $params, 'gameUsername');
		$amount = $this->getVariableFromContext((array) $params, 'amount');
		$rlt = $this->getFirstResultFromObject($resultObj);
		$rltSuccess = (is_object($rlt) && property_exists($rlt,'Success')) ? $rlt->Success : false;
		$success = $this->processResultBoolean($responseResultId, $rltSuccess, $gameUsername);

		if(strpos(@$rlt->Message,'NO change to balance') !== false||strpos(@$rlt->Message,'repeated deposit requestid') !== false){
			$success = false;
		}

        $result = array(
            'response_result_id' => $responseResultId,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN,
            'external_transaction_id'=>$external_transaction_id
        );

		if ($success) {
			$afterBalance = $this->gameAmountToDB(floatval($rlt->RealBalance));
			//$result['external_transaction_id'] = $rlt->TransactionId;
			$result["currentplayerbalance"] = $rlt->RealBalance;
			$result["balance"] = $afterBalance;
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);

			// if ($playerId) {
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeMainWalletToSubWallet());
			// 	$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
   //              $result['reason_id'] = self::REASON_NOT_FOUND_PLAYER;
   //              $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			// }
			$result['didnot_insert_game_logs']=true;
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result["userNotFound"] = false;
		} else {
			$error_msg = $this->getResultTextFromParams($params);
			$result['reason_id'] = $this->getReasons($error_msg);
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	private function getReasons($error_msg){
        $error_msg = strtolower($error_msg);
        switch ($error_msg) {
            case strpos($error_msg,'username not provided') !== false:
                return self::REASON_NOT_FOUND_PLAYER;
                break;
            case strpos($error_msg,'parameter error') !== false:
                return self::REASON_INCOMPLETE_INFORMATION;
                break;
            case strpos($error_msg,'unrecognized guid format') !== false:
                return self::REASON_GAME_PROVIDER_ACCOUNT_PROBLEM;
                break;
            case strpos($error_msg,'repeated withdrawal requestid') !== false:
            case strpos($error_msg,'repeated deposit requestid') !== false:
                return self::REASON_DUPLICATE_TRANSFER;
                break;
            case strpos($error_msg,'insufficient funds') !== false:
                return self::REASON_NO_ENOUGH_BALANCE;
                break;
            case strpos($error_msg,'maintenance') !== false:
                return self::REASON_API_MAINTAINING;
                break;
            default:
                return self::REASON_UNKNOWN;
                break;
        }
	}

	/**
	 * overview : withdraw from game
	 *
	 * @param $playerName
	 * @param $amount
	 * @param null $transfer_secure_id
	 * @return array
	 */
	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		$playerUsername = $playerName;
		$password = $this->getPassword($playerName);
		$transactionReferenceNumber = 'WIT' . random_string('alnum');
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$resultPlayerBalance = $this->queryPlayerBalance($playerUsername);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawFromGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
			'external_transaction_id' => $transactionReferenceNumber,
		);

		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			'Username' => $gameUsername,
			'Password' => $password['password'],
			'CurrencyCode' => $this->CurrencyCode,
			'Amount' => '-' . $this->dBtoGameAmount($amount),
			'WithdrawAll' => '',
			'RequestId' => $transactionReferenceNumber,
		);
		$params = array('req' => $params);

		$this->CI->utils->debug_log('withdrawFromGame params', $params);

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	/**
	 * overview : processing for withdraw from game
	 * @param $params
	 * @return array
	 */
	public function processResultForWithdrawFromGame($params) {
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$playerName = $this->getVariableFromContext((array) $params, 'playerName');
		$gameUsername = $this->getVariableFromContext((array) $params, 'gameUsername');
		$amount = $this->getVariableFromContext((array) $params, 'amount');
		$rlt = $this->getFirstResultFromObject($resultObj);
		$rltSuccess = (is_object($rlt) && property_exists($rlt,'Success')) ? $rlt->Success : false;
		$success = $this->processResultBoolean($responseResultId, $rltSuccess, $gameUsername);
		if(strpos(@$rlt->Message,'NO change to balance') !== false || strpos(@$rlt->Message,'repeated withdrawal requestid') !== false || strpos(@$rlt->Message,'Insufficient funds')!== false){
			$success = false;
		}

        $result = array(
            'response_result_id' => $responseResultId,
            'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
            'reason_id'=>self::REASON_UNKNOWN,
			'external_transaction_id'=>$external_transaction_id
        );

		if ($success) {
			$afterBalance = $this->gameAmountToDB(floatval($rlt->RealBalance));
			//$result['external_transaction_id'] = $rlt->TransactionId;
			$result["currentplayerbalance"] = $afterBalance; //The account’s balance in the player’s currency
			// $result["balance"] = $afterBalance;
			// //update
			// $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
			// if ($playerId) {
			// 	//withdrawal
			// 	$this->insertTransactionToGameLogs($playerId, $gameUsername, $afterBalance, $amount, $responseResultId,
			// 		$this->transTypeSubWalletToMainWallet());
			// 	$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			// } else {
			// 	$this->CI->utils->debug_log('error', 'cannot get player id from ' . $gameUsername . ' getPlayerIdInGameProviderAuth');
   //              $result['reason_id'] = self::REASON_NOT_FOUND_PLAYER;
   //              $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			// }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
			$result["userNotFound"] = false;
		} else {
			$error_msg = $this->getResultTextFromParams($params);
			$result['reason_id'] = $this->getReasons($error_msg);
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	/**
	 * overview : change player password
	 *
	 * @param $playerName
	 * @param $oldPassword
	 * @param $newPassword
	 * @return array
	 */
	public function changePassword($playerName, $oldPassword, $newPassword) {
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
		if (!empty($accountNumber)) {
			//EditAccount
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForChangePassword',
				'playerName' => $playerName,
				'newPassword' => $newPassword,
			);

			$params = array(
				'BrandId' => $this->brandId,
				'APIKey' => $this->APIKey,
				'Username' => $accountNumber,
				'NewPassword' => $newPassword,
			);

			$this->CI->utils->debug_log(__METHOD__. ' params >>>>>>>>',$params);

			return $this->callApi(self::API_changePassword, $params, $context);
		}
		return $this->returnFailed('Not found ' . $playerName);
	}

	/**
	 * overview : process result for change password
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForChangePassword($params) {
        $responseResultId = $this->getResponseResultIdFromParams((array) $params);
        $resultJson = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext((array) $params, 'playerName');
        $newPassword = $this->getVariableFromContext((array) $params, 'newPassword');
        $success = false;
        $resultJsonSuccess = isset($resultJson['Success']) ? $resultJson['Success'] : false;

        $result = [
            'player' => $playerName,
			'newPassword' => $newPassword,
			'message' => "Update password Failed!"
        ];

        $success = $this->processResultBoolean($responseResultId, $resultJsonSuccess, $playerName);

        $this->CI->utils->debug_log(__METHOD__.' processResultForChangePassword', $resultJson , 'success', $success);

        if ($success) {
           $playerId = $this->getPlayerIdInPlayer($playerName);
           //sync password to game_provider_auth
		   $this->updatePasswordForPlayer($playerId, $newPassword);
		   $result['message'] = "Update password success!";
        }

        return array($success, $result);
	}

	/**
	 * overview : query player balance
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerBalance($playerName) {
		// $password = $this->getPasswordString($playerName);
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
		$password = $this->getPassword($playerName);
		$playerId = $this->getPlayerIdByExternalAccountId($accountNumber);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerId' => $playerId,
			'accountNumber' => $accountNumber,
		);
		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			"Username" => $accountNumber,
			"Password" => $password['password'],
		);
		$params = array('req' => $params);
		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	/**
	 * overview : processing result for query player balance
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForQueryPlayerBalance($params) {

		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$accountNumber = $this->getVariableFromContext((array) $params, 'accountNumber');
		$playerName = $accountNumber;

		if ($resultObj == null) {
			$this->CI->utils->debug_log('processResultForQueryPlayerBalance returned null');
			return array(false, null);
		}

		$balance = $this->gameAmountToDB($resultObj->QueryPlayerResult->RealBalance);
		$status = $resultObj->QueryPlayerResult->Found ? 1 : 0;
		$success = $this->processResultBoolean($responseResultId, $resultObj, $playerName);
		$result = array();
		// if ($success) {
		$rltObj = (array) $resultObj->QueryPlayerResult;
		$playerName = $params['params']['req']['Username'];
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
		$playerId = $this->getPlayerIdByExternalAccountId($accountNumber);

		if ($success) {
			$result["balance"] = $this->convertAmountToDB(floatval($rltObj['RealBalance']));
			// $result['success'] = true;
			//$this->CI->utils->debug_log('query balance playerId', $playerId, 'playerName',$playerName, 'balance', @$result['RealBalance']);
		} else {
			// $success = false;
			// $result['success'] = false;
		}

		if (!$success) {
			$this->CI->utils->debug_log('HB got error', $responseResultId, 'playerName', $accountNumber, 'result', $resultObj);
		}
		return array($success, $result);
	}

	/**
	 * overview : query transaction
	 *
	 * @param $transactionId
	 * @param $extra
	 * @return array
	 */
	public function queryTransaction($transactionId, $extra) {
        $playerName=$extra['playerName'];
        $playerId=$extra['playerId'];
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'playerId' => $playerId,
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'external_transaction_id' => $transactionId,
		);

		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			"RequestId" => $transactionId,
		);
		$params = array('req' => $params);

		return $this->callApi(self::API_queryTransaction, $params, $context);

	}

	/**
	 * overview : processing result for query transaction
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForQueryTransaction($params) {
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$gameUsername = $this->getVariableFromContext((array) $params, 'gameUsername');
		$rlt = $this->getFirstResultFromObject($resultObj);

		$this->CI->utils->debug_log('result obj ',$resultObj);
		$this->CI->utils->debug_log('result hb ',$rlt);

		$success = $this->processResultBoolean($responseResultId, $rlt->Success, $gameUsername);

		$result = array(
			'response_result_id' => $responseResultId,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN,
			'external_transaction_id'=>$external_transaction_id
		);

		if($success) {
			$result['status'] = self::COMMON_TRANSACTION_STATUS_APPROVED;
		} else {
			$error_msg = $this->getResultTextFromParams($params);
			$result['reason_id'] = $this->getReasons($error_msg);
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	/**
	 * overview : logout player
	 *
	 * @param $playerName
	 * @param null $password
	 * @return array
	 */
	public function logout($playerName, $password = null) {
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
        $password = $this->getPassword($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogout',
			'playerName' => $playerName,
			'gameUsername' => $accountNumber,
		);

		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			'Username' => $accountNumber,
			'Password' => $password['password'],
		);
		$params = array('req' => $params);

		return $this->callApi(self::API_logout, $params, $context);
	}

	/**
	 * overview : process result for logout
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForLogout($params) {
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = @$params['resultObj']->LogoutPlayerResult;
		$playerId = $this->getVariableFromContext((array) $params, 'playerId');
		$gameUsername = $this->getVariableFromContext((array) $params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultObj, $gameUsername);
		$result =[];

		if($success){
			$result['message'] = "player logout!";
		}

		return array($success, $result);
	}

	/**
	 * overview : query game records
	 *
	 * @param $dateFrom
	 * @param $dateTo
	 * @param null $playerName
	 * @return array
	 */
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processQueryGameRecords',
		//	'playerId' => $playerId,
		//	'accountNumber' => $accountNumber,
		);
		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			'Username' => $playerName,
			'DtStartUTC' => $dateFrom,
			'DtEndUTC' => $dateTo,
		);
		$params = array('req' => $params);

		$result = $this->callApi(self::API_queryGameRecords, $params, $context);
		return $result;
	}

	/**
	 * overview : process query game records
	 *
	 * @param $params
	 * @return array
	 */
	public function processQueryGameRecords($params) {

		$responseResultId = $this->getResponseResultIdFromParams((array) $params);

		$resultObj = $this->getResultObjFromParams($params);
		$accountNumber = $params['params']['req']['Username'];
		if (count((array) $resultObj->GetPlayerGameTransactionsResult) == 0) {
			$this->CI->utils->debug_log('processResultForQueryGameRecords returned null');
			return array(false, array());
		}

		$result = $return = $resultObj->GetPlayerGameTransactionsResult->PlayerGameTransactionsDTO;
		$success = count($result) > 0 ? TRUE : FALSE;
		return array($success, (array) $result);
	}


	public function getLauncherLanguage($language){
		$lang='';
        switch ($language) {
            case 1:
            case 'en-us':
                $lang = 'en'; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 'zh-CN'; // chinese
                break;
            case 3:
            case 'id-id':
                $lang = 'id'; // chinese
                break;
            case 4:
            case 'vi-vn':
                $lang = 'vi'; // chinese
                break;
            case 5:
            case 'ko-kr':
                $lang = 'ko'; // korean
                break;
            case 'th-th':
                $lang = 'th'; // korean
                break;
            default:
                $lang = 'en'; // default as english
                break;
        }
        return $lang;
	}

	/**
	 * overview : query forward game
	 *
	 * @param $playerName
	 * @param null $extra
	 * @return mixed
	 */
	public function queryForwardGame($playerName, $extra = null) {
		$mode = strtolower($extra['game_mode'])!='real'?'fun':'real';
		$lobbyurl = !empty($this->lobby_url) ? $this->lobby_url : @$extra['lobby_url'];
		if ($this->use_referrer_lobby_url) {
			$lobbyurl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $lobbyurl ;
		}

		if (!empty($this->language)) {
			$language = $this->getLauncherLanguage($this->language);
		} else {
			$language = $this->getLauncherLanguage(@$extra['language']);
		}

		$newParams = array(
			'mode' => $mode,
			'keyname' => @$extra['game_code'],
			'locale' => $language,
			'brandid' => $this->brandId,
			'mobile' => $extra['is_mobile'],
			'lobbyurl' => $lobbyurl,
		);

		if($this->is_hide_home_button_in_mobile || $this->is_hide_home_button_in_desktop){
			unset($newParams['lobbyurl']);
		}

		$this->CI->utils->debug_log('HB LOBBY URL ====> ', $lobbyurl);

		if(strtolower(@$extra['redirection']) == 'iframe'){
			$newParams['ifrm']=1;
		}
		$success=true;

		if($mode=='real'){
			$playerReturn = $this->login($playerName);
			if(isset($playerReturn['token'])){
				$newParams['token'] = $playerReturn['token'];
			}
			if(empty($newParams['token'])){
				$success=false;
			}
		}

		$result['success'] = $success;
		$result['url'] = $this->getSystemInfo('second_url'). '?' . http_build_query($newParams);

		return $result;
	}

	/**
	 * overview : sync original game logs
	 *
	 * @param $token
	 * @return array
	 */
	public function syncOriginalGameLogs($token) {

		$this->CI->utils->debug_log('HB sync original dateTimeFrom', parent::getValueFromSyncInfo($token, 'dateTimeFrom'),'dateTimeTo', parent::getValueFromSyncInfo($token, 'dateTimeTo'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$syncId = parent::getValueFromSyncInfo($token, 'syncId');

		$startDate = new DateTime($this->serverTimeToGameTime($dateTimeFrom));
		$endDate = new DateTime($this->serverTimeToGameTime($dateTimeTo));

		$startDate->modify($this->getDatetimeAdjust());

		$startDate = $startDate->format("YmdHis");
		$endDate = $endDate->format("YmdHis");
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncGameRecords',
			'syncId' => $syncId,
		);

		$this->CI->utils->debug_log('real dateTimeFrom', $startDate, 'dateTimeTo', $endDate);

		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			'DtStartUTC' => $startDate,
			'DtEndUTC' => $endDate,
		);
		$params = array('req' => $params);

		if ($this->sync_sleep > 0) {
	        $this->CI->utils->debug_log('SYNC ORIGINAL PARAMS ===> ', $params, "SLEEP TIME ===> ", $this->sync_sleep);
			sleep($this->sync_sleep);
		}
		$rlt = $this->callApi(self::API_syncGameRecords, $params, $context);
		return $rlt;
	}

	/**
	 * overview : process result for sync game records
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForSyncGameRecords($params) {

		$this->CI->load->model(array('haba88_game_logs', 'player_model'));
		$responseResultId = $this->getResponseResultIdFromParams($params);
		// $resultObj = $this->getResultObjFromParams($params);
        $resultText = $this->getResultTextFromParams($params);
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $resultText);
        $resultObj = simplexml_load_string($clean_xml);
        $result = $resultObj->Body->GetBrandCompletedGameResultsTempResponse->GetBrandCompletedGameResultsTempResult;
        //$this->CI->utils->debug_log('the result again ---->', $result);
		$success = true;
		if ( ! empty($result)) {
            $resultRows = json_decode(json_encode($result),true);

        	$records = @$resultRows['PlayerCompletedGamesExtendedDTO'] ? : [];

        	if ( ! isset($records[0]) || ! is_array($records[0])) {
        	    if (is_array($records)) {
        	        $records = [$records];
        	    }
        	}

            if( ! empty($records)) {

                $this->CI->utils->debug_log('resultRows', count($resultRows));

                $success = $this->processResultBoolean($responseResultId, $resultRows, NULL);
                $processedResultRows = [];
                if ($success) {
                    foreach ($records as $key => $rest) {
                        if (empty($rest['FriendlyGameInstanceId'])) {
                            $success = false;
                            $this->CI->utils->debug_log('lost FriendlyGameInstanceId', $records);
                            return array($success);
                        }
                        //convert date time back to server time

                        $processedResultRows[$key] = $rest;
                        $processedResultRows[$key]['DtStarted'] = $this->gameTimeToServerTime($rest['DtStarted']);
                        $processedResultRows[$key]['DtCompleted'] = $this->gameTimeToServerTime($rest['DtCompleted']);
                        $processedResultRows[$key]['external_uniqueid'] = $rest['FriendlyGameInstanceId']; //add external_uniueid for og purposes
                        $processedResultRows[$key]['response_result_id'] = $responseResultId; //add response_result_id for og purposes
                    }

                    $gameRecords = $processedResultRows;

                    if ($gameRecords) {
                        $availableRows = $this->CI->haba88_game_logs->getAvailableRows($gameRecords);
                        // print_r($availableRows);exit;

                        $this->CI->utils->debug_log('availableRows', count($availableRows), 'gameRecords', count($gameRecords));
                        if (isset($availableRows[0])) {
                            foreach ($availableRows[0] as $record) {
                                $insertData = array();
                                $playerId = $this->getPlayerIdInGameProviderAuth($record['Username']);

                                if (empty($playerId)) {
                                	continue;
                                }

                                $record['PlayerId'] = $playerId;

                                $insertData['PlayerId'] = $record['PlayerId'];
                                $insertData['BrandId'] = $record['BrandId'];
                                $insertData['Username'] = $record['Username'];
                                $insertData['BrandGameId'] = $record['BrandGameId'];
                                $insertData['GameKeyName'] = $record['GameKeyName'];
                                $insertData['GameTypeId'] = $record['GameTypeId'];
                                $insertData['DtStarted'] = $record['DtStarted'];
                                $insertData['DtCompleted'] = $record['DtCompleted'];
                                $insertData['FriendlyGameInstanceId'] = $record['FriendlyGameInstanceId'];
                                $insertData['GameInstanceId'] = $record['GameInstanceId'];
                                $insertData['Stake'] = $record['Stake'];
                                $insertData['Payout'] = $record['Payout'];
                                $insertData['JackpotWin'] = $record['JackpotWin'];
                                $insertData['JackpotContribution'] = $record['JackpotContribution'];
                                $insertData['CurrencyCode'] = $record['CurrencyCode'];
                                $insertData['ChannelTypeId'] = $record['ChannelTypeId'];
                                $insertData['BalanceAfter'] = $record['BalanceAfter'];
                                $insertData['external_uniqueid'] = $record['external_uniqueid'];
                                $insertData['response_result_id'] = $record['response_result_id'];
                                $insertData['BonusToReal'] = isset($record['BonusToReal']) && !empty($record['BonusToReal']) ? $record['BonusToReal']:null;
                                $insertData['BonusToRealCoupon'] = isset($record['BonusToRealCoupon']) && !empty($record['BonusToRealCoupon']) ? $record['BonusToRealCoupon']:null;

                                $this->CI->haba88_game_logs->insertHaba88GameLogs($insertData);
                            }
                        }
                    }
                }
            }
			// } else {
			// 	$success = false;
			// 	$this->CI->utils->error_log('sync false HABA88 game logs', $resultRows);
			// 	//debug
			// 	throw new Exception('stopped');
		}
		return array($success);
	}

	/**
	 * overview : sync merge to game logs
	 *
	 * @param $token
	 * @return array
	 */
	public function syncMergeToGameLogs($token) {
		$this->CI->load->model(array('game_logs', 'player_model', 'haba88_game_logs'));
		//$dateTimeFrom = (array)$this->syncInfo[$token]['dateTimeFrom'];
		//$dateTimeTo = (array)$this->syncInfo[$token]['dateTimeTo'];

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		// $dateTimeFrom = (array) new DateTime($this->serverTimeToGameTime(date("Y-m-d H:i:s", strtotime($dateTimeFrom['date']))));
		// $dateTimeTo = (array) new DateTime($this->serverTimeToGameTime(date("Y-m-d H:i:s", strtotime($dateTimeTo['date']))));

		// $dateTimeFrom = date("YmdHis", strtotime($dateTimeFrom['date']));
		// $dateTimeTo = date("YmdHis", strtotime($dateTimeTo['date']));

		$this->CI->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);
		$result = $this->CI->haba88_game_logs->getHaba88GameLogStatistics(
			$this->CI->utils->formatDateTimeForMysql($dateTimeFrom),
			$this->CI->utils->formatDateTimeForMysql($dateTimeTo));
		$cnt = 0;

        $this->utils->debug_log('the result ----->', $result);

		if ($result) {

			$unknownGame = $this->getUnknownGame();
			foreach ($result as $haba_data) {

		        $this->utils->debug_log('HABA DATA =====>', $haba_data);

				$player_id = $haba_data->player_id;
				if (!$player_id) {
					continue;
				}

				$cnt++;

				$bet_amount = $haba_data->bet_amount;
				$result_amount = ($haba_data->result_amount+$haba_data->bonus_amount) - $bet_amount;

				$game_description_id = $haba_data->game_description_id;
				$game_type_id = $haba_data->game_type_id;

				if (empty($game_description_id)) {
                    list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($haba_data, $unknownGame);
				}

                $extra_info=['trans_amount'=>$this->convertAmountToDB($bet_amount), 'table'=>$haba_data->external_uniqueid,
						'sync_index' => $haba_data->id];

				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$haba_data->game_code,
					$haba_data->game_type,
					$haba_data->game,
					$player_id,
					$haba_data->Username,
					$this->convertAmountToDB($bet_amount),
					$this->convertAmountToDB($result_amount),
					null, # win_amount
					null, # loss_amount
					$this->convertAmountToDB($haba_data->after_balance), # after_balance
					0, # has_both_side
					$haba_data->external_uniqueid,
					$haba_data->date_created,
					$haba_data->date_created,
					$haba_data->response_result_id,# response_result_id
                    Game_logs::FLAG_GAME,
                    $extra_info
				);

			}
		}
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return array('success' => true);
	}

    private function getGameDescriptionInfo($row, $unknownGame) {
        if(empty($row->game_type_id)){
            $game_type_id = $unknownGame->game_type_id;
            $row->game = $row->game_code;
            $row->game_type = $unknownGame->game_name;
        }

        $externalGameId = $row->game_code;
        $extra = array('game_code' => $row->game_code);
        return $this->processUnknownGame(
            $row->game_description_id, $row->game_type_id,
            $row->game, $row->game_type, $externalGameId, $extra,
            $unknownGame);
    }

	/**
	 * overview : check funds transfer
	 *
	 * @param $playerName
	 * @return array
	 */
	public function checkFundTransfer($playerName) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : processing result for checkFundTransfer
	 *
	 * @param $params
	 * @return array
	 */
	public function processResultForcheckFundTransfer($params) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : processing result
	 *
	 * @param $responseResultId
	 * @param $result
	 * @param null $playerName
	 * @return bool
	 */
	protected function processResultBoolean($responseResultId, $result, $playerName = null) {
		$success = false;
		if ($result) {
			$success = true;
		}
		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('HB got error', $responseResultId, 'playerName', $playerName, 'result', $result);
		}

		return $success;
	}

	/**
	 * overview : auth soap
	 *
	 * @param $client
	 * @return bool
	 */
	protected function authSoap($client) {
		return true;
	}

	/**
	 * overview : make soap options
	 *
	 * @param $options
	 * @return mixed
	 */
	protected function makeSoapOptions($options) {
		//overwrite in sub-class
		if($this->use_local_wsdl){
			$options['ignore_ssl_verify'] = $this->getSystemInfo('ignore_ssl_verify', true);
		}
		return $options;
	}

	/**
	 * overview : check if player exist
	 *
	 * @param $playerName
	 * @return array
	 */
	public function isPlayerExist($playerName) {
		//debug
		// $accountNumber = $playerName;
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
		if (!empty($accountNumber)) {
			$playerId=$this->getPlayerIdFromUsername($playerName);
			// $accountNumber = $this->getGameUsernameByPlayerUsername($playerName);
			$password = $this->getPassword($playerName);
			// $playerId = $this->getPlayerIdByExternalAccountId($accountNumber);
			$context = array(
				'callback_obj' => $this,
				'callback_method' => 'processResultForIsPlayerExist',
				'playerId' => $playerId,
				'accountNumber' => $accountNumber,
			);
			$params = array(
				'BrandId' => $this->brandId,
				'APIKey' => $this->APIKey,
				"Username" => $accountNumber,
				"Password" => $password['password'],
			);
			$params = array('req' => $params);
			return $this->callApi(self::API_isPlayerExist, $params, $context);
		}
		return $this->returnFailed('Not found ' . $playerName);
	}

	/**
	 * overview : process result for isPlayerExist
	 *
	 * @param $params
	 * @return array
	 */
	protected function processResultForIsPlayerExist($params) {
		$exists = false;
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$gameUsername = $this->getVariableFromContext((array) $params, 'gameUsername');
        $playerId = $this->getVariableFromContext($params, 'playerId');

        $success = false;
        if(!empty($resultObj) && isset($resultObj->QueryPlayerResult)){
        	if(isset($resultObj->QueryPlayerResult->Found)){
        		$exists = $resultObj->QueryPlayerResult->Found ? true : false;
        		$success = true;
        	}
        } else {
			$this->CI->utils->debug_log('processResultForIsPlayerExist returned null');
        }

		if (!$success) {
			$exists=null;
		}

		if($exists){
	        $this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}

		$result = ['exists' => $exists,'response_result_id'=>$responseResultId];

		return array($success, $result);
	}

	/**
	 * overview : query player currency
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerCurrency($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$password = $this->getPassword($playerName);
		$playerId = $this->getPlayerIdByExternalAccountId($gameUsername);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerCurrency',
			'playerId' => $playerId,
			'gameUsername' => $gameUsername,
		);
		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			"Username" => $gameUsername,
			"Password" => $password['password'],
		);
		$params = array('req' => $params);
		return $this->callApi(self::API_queryPlayerCurrency, $params, $context);
	}

	/**
	 * overview : processing result for queryPlayerCurrency
	 * @param $params
	 * @return array
	 */
	public function processResultForQueryPlayerCurrency($params) {
		$resultObj = $this->getResultObjFromParams((array) $params);
		$success = false;
		$result = array();
		if($resultObj != null){
			if(isset($resultObj->QueryPlayerResult)){
				$exist = $resultObj->QueryPlayerResult->Found ? 1 : 0;
				if($exist){
					$success = true;
					$result['currency_code'] = $resultObj->QueryPlayerResult->CurrencyCode;
					$result['currency_symbol'] = $resultObj->QueryPlayerResult->CurrencySymbol;
				}
			}
		}
		return array($success, $result);
	}

	/**
	 * overview : query player information
	 *
	 * @param $playerName
	 * @return array
	 */
	public function queryPlayerInfo($playerName) {
		$accountNumber = $this->getGameUsernameByPlayerUsername($playerName);

		$password = $this->getPassword($playerName);

		$playerId = $this->getPlayerIdByExternalAccountId($accountNumber);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerInfo',
			'playerId' => $playerId,
			'accountNumber' => $accountNumber,
		);
		$params = array(
			'BrandId' => $this->brandId,
			'APIKey' => $this->APIKey,
			"Username" => $accountNumber,
			"Password" => $password['password'],
		);
		$params = array('req' => $params);
		return $this->callApi(self::API_queryPlayerInfo, $params, $context);
	}

	/**
	 * overview : processing result for queryPlayerInfo
	 * @param $params
	 * @return array
	 */
	public function processResultForQueryPlayerInfo($params) {
		$responseResultId = $this->getResponseResultIdFromParams((array) $params);
		$resultObj = $this->getResultObjFromParams((array) $params);
		$accountNumber = $this->getVariableFromContext((array) $params, 'accountNumber');
		$playerId = $this->getVariableFromContext((array) $params, 'playerId');

		$playerName = $accountNumber;

		if ($resultObj == null) {
			$this->CI->utils->debug_log('processResultForQueryPlayerBalance returned null');
			return array(false, null);
		}

		$status = $resultObj->QueryPlayerResult->Found ? 1 : 0;
		$success = $this->processResultBoolean($responseResultId, $status, $playerName);
		$result = array();
		$result = (array) $resultObj->QueryPlayerResult;

		if ($success) {
			$this->CI->utils->debug_log('query playerinfo playerId', $playerId, 'playerName',
				$playerName, 'balance', @$result['balance']);
		} else {
			$success = false;
		}

		if (!$success) {
			$this->CI->utils->debug_log('HB got error', $responseResultId, 'playerName', $accountNumber, 'result', $result);
		}
		return array('success' => $success, $result);
	}

	/**
	 * overview : block player
	 * @param $playerName
	 * @return array
	 */
	public function blockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->blockUsernameInDB($playerName);
		return array("success" => true);
	}

	/**
	 * overview : unblock player
	 *
	 * @param $playerName
	 * @return array
	 */
	public function unblockPlayer($playerName) {
		$playerName = $this->getGameUsernameByPlayerUsername($playerName);
		$success = $this->unblockUsernameInDB($playerName);
		return array("success" => true);
	}

	/**
	 * overview : reset player
	 * @param $playerName
	 * @return array
	 */
	public function resetPlayer($playerName) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : update player information
	 *
	 * @param $playerName
	 * @param $infos
	 * @return array
	 */
	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

	/**
	 * overview : query player daily balance
	 *
	 * @param $playerName
	 * @param $playerId
	 * @param null $dateFrom
	 * @param null $dateTo
	 * @return array
	 */
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		$daily_balance = parent::getPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null);

		$result = array();

		if ($daily_balance != null) {
			foreach ($daily_balance as $key => $value) {
				$result[$value['updated_at']] = $value['balance'];
			}
		}

		return array_merge(array('success' => true, "balanceList" => $result));
	}

	/**
	 * overview : check login status
	 *
	 * @param $playerName
	 * @return array
	 */
	public function checkLoginStatus($playerName) {
		return array("success" => true, "loginStatus" => true);
	}

	/**
	 * overview : get total betting game amount
	 *
	 * @param $playerName
	 * @param $dateFrom
	 * @param $dateTo
	 * @return array
	 */
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {
		$gameBettingRecord = parent::getGameTotalBettingAmount($playerName, $dateFrom, $dateTo);
		if ($gameBettingRecord != null) {
			$result['bettingAmount'] = $gameBettingRecord['bettingAmount'];
		}
		return array("success" => true, "bettingAmount" => $result['bettingAmount']);
	}

	/**
	 * overview : get timeout second
	 *
	 * @return int
	 */
	protected function getTimeoutSecond() {
		return 3600;
	}

	/**
	 * overview : batch query player balance
	 *
	 * @param $playerNames
	 * @param null $syncId
	 * @return array
	 */
	public function batchQueryPlayerBalance($playerNames, $syncId = null) {
		return $this->returnUnimplemented();
	}


	/**
	 * overview : query available player balance
	 *
	 * @return array
	 */
	public function useQueryAvailableBalance() {
		return $this->returnUnimplemented();
	}

}
