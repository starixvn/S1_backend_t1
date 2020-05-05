<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

class Game_api_pt_v2 extends Abstract_game_api {

	const ORIGINAL_TABLE  = "pt_v2_game_logs";

    const ORIGINAL_TABLE_FIELDS = [
		'id',
        'reference_no',
        'entity_name',
        'kiosk_name',
        'game_server',
		'gamzo_player_name',
		'game_name',
        'game_shortcode',
        'game_type',
        'currency',
        'is_win',
        'bet',
        'win',
        'progressive_bet',
		'progressive_win',
		'exit_type',
		'bonus_type',
        'game_server_session_id',
        'game_server_reference_1',
        'bet_timestamp',
        'bet_datetime',
        'game_snapshot_token',
        'game_snapshot',
        'balance_after',
        'hash',
        'created_at',
        'updated_at',
        'is_valid_game_logs',
	];

	const MD5_FIELDS_FOR_ORIGINAL=[
        'reference_no',
        'entity_name',
        'kiosk_name',
        'game_server',
		'gamzo_player_name',
		'game_name',
        'game_shortcode',
        'game_type',
        'currency',
        'is_win',
        'bet',
        'win',
        'progressive_bet',
        'progressive_win',
        'game_server_session_id',
        'game_server_reference_1',
        'bet_timestamp',
        'bet_datetime',
        'game_snapshot_token',
        'game_snapshot',
        'balance_after',
        'hash',
    ];

    const MD5_FLOAT_AMOUNT_FIELDS = [
        'bet',
        'win',
        'balance_after',
        'progressive_bet',
        'progressive_win'
    ];

    const MD5_FIELDS_FOR_MERGE=['player_id', 'currency', 'result_amount', 'bet_amount'];
    const MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE=['result_amount', 'bet_amount'];

	const START_PAGE = 1;
	const ITEM_PER_PAGE = 3000;


	const OFFLINE_STATUS = 0;
	const ONLINE_STATUS = 0;


	protected $transaction_status_declined;
	protected $transaction_status_approved;

	const DEFAULT_TRANSACTION_STATUS_APPROVED='approved';
	const DEFAULT_TRANSACTION_STATUS_DECLINED='declined';

	const DEFAULT_COUNTRYCODE = 'CN';

	const SUCCESS_CODES=['200']; /** OGP-17130 */

	protected $launch_game_on_player;

	private $method;

	const URI_MAP = array(
		//POST
		self::API_createPlayer => '/backoffice/player/create',
		self::API_updatePlayerInfo => '/backoffice/player/update',
		self::API_depositToGame => '/backoffice/transfer/player/deposit',
		self::API_withdrawFromGame => '/backoffice/transfer/player/withdraw',
		self::API_syncGameRecords => '/backoffice/reports/gameTransactions',
		self::API_changePassword => '/backoffice/player/update',
		self::API_isPlayerExist => '/backoffice/player/info',

		//GET
		self::API_queryPlayerInfo => '/backoffice/player/info',
		self::API_queryPlayerBalance => '/backoffice/player/serverBalance',
		self::API_queryTransaction => '/backoffice/transfer/player/status',
		self::API_checkLoginStatus => '/backoffice/player/isOnline',
		self::API_resetPlayer => '/backoffice/player/resetFailedLogin',
		self::API_logout => '/backoffice/player/logout',
	);

	public function __construct() {
		parent::__construct();

		$this->key = $this->getSystemInfo('key');
		$this->admin_user_name = $this->getSystemInfo('admin_user_name');
		$this->kiosk_name = $this->getSystemInfo('kiosk_name');
		$this->main_server_name = $this->getSystemInfo('main_server_name', 'main');
		$this->game_server_name = $this->getSystemInfo('game_server_name');
		$this->currency = $this->getSystemInfo('currency');
		$this->prefix = $this->getSystemInfo('prefix_for_username');

		//true if api auto add prefix
		$this->is_auto_add_prefix = $this->getSystemInfo('is_auto_add_prefix', false);

		$this->perPageSize = $this->getSystemInfo('per_page_size', self::ITEM_PER_PAGE);
		$this->sync_time_interval = $this->getSystemInfo('sync_time_interval', '+10 minutes');
		$this->launch_game_on_player = $this->getSystemInfo('launch_game_on_player', true);//need to study
		$this->country_code = $this->getSystemInfo('country_code', self::DEFAULT_COUNTRYCODE);

		$this->add_progressive_bet = $this->getSystemInfo('add_progressive_bet', false);
		$this->add_progressive_win = $this->getSystemInfo('add_progressive_win', true);

		$this->api_play_pt_js = $this->getSystemInfo('API_PLAY_PT_JS','https://login.winforfun88.com/jswrapper/integration.js.php?casino=winforfun88'); # for flash games
		$this->api_play_pt_js_h5 = $this->getSystemInfo('API_PLAY_PT_JS_H5','https://login.ld176988.com/jswrapper/integration.js.php?casino=winforfun88'); # for HTML5 games
		$this->api_play_pt = $this->getSystemInfo('API_PLAY_PT');
		$this->mobile_systemId = $this->getSystemInfo('mobile_systemId');
		$this->mobile_js_url = $this->getSystemInfo('mobile_js_url');
		$this->mobile_launcher = $this->getSystemInfo('mobile_launcher');

		$this->transaction_status_approved= $this->getSystemInfo('transaction_status_approved', self::DEFAULT_TRANSACTION_STATUS_APPROVED);
		$this->transaction_status_declined= $this->getSystemInfo('transaction_status_declined', self::DEFAULT_TRANSACTION_STATUS_DECLINED);

		$this->status_map=[
			$this->transaction_status_approved => self::COMMON_TRANSACTION_STATUS_APPROVED,
			$this->transaction_status_declined => self::COMMON_TRANSACTION_STATUS_DECLINED,
		];

        $this->load_pt_js_from_our_server=$this->getSystemInfo('load_pt_js_from_our_server', false);
        $this->mobile_lobby = $this->getSystemInfo('mobile_lobby', '/ptGame.html');
        $this->mobile_logout_url = $this->getSystemInfo('mobile_logout_url', '');
        $this->support_url = $this->getSystemInfo('support_url');
        $this->client_support_url = $this->getSystemInfo('client_support_url');
        //don't support
        $this->is_enabled_direct_launcher_url=$this->getSystemInfo('is_enabled_direct_launcher_url', false);

		$this->drop_third_decimal_places=$this->getSystemInfo('drop_third_decimal_places', true);

		$this->sleep = $this->getSystemInfo('sleep', 5);

		$this->decimal_place = $this->getSystemInfo('decimal_place', 2);

        $this->is_forced_withdraw=$this->getSystemInfo('is_forced_withdraw', 1);

        $this->password_prefix=$this->getSystemInfo('password_prefix', "");
   	}

	public function getPlatformCode() {
		return PT_V2_API;
	}

	protected function convertStatus($status){

		if(isset($this->status_map[$status])){
			return $$this->status_map[$status];
		}else{
			return self::COMMON_TRANSACTION_STATUS_PROCESSING;
		}

	}

	public function gameAmountBalance($amount) {
		$conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
		$value = floatval($amount / $conversion_rate);
		return $this->roundDown($value, 3);
	}

	private function roundDown($number, $precision = 2){
		$fig = (int) str_pad('1', $precision, '0');
		return (floor($number * $fig) / $fig);
	}

	protected function convertToDBAmount($amount){
		$conversion_rate = floatval($this->getSystemInfo('conversion_rate', 1));
		$decimal_place = floatval($this->getSystemInfo('decimal_place', 2));
        $value = floatval($amount / $conversion_rate);
        return round($value, $decimal_place);
	}

    protected function processResultBoolean($responseResultId, $resultArr, $playerName = null,$is_querytransaction= false) {
        $success = false;
        if(isset($resultArr['code']) && (in_array(trim($resultArr['code']),self::SUCCESS_CODES))){
            $success = true;
        }

        if (!$success) {
            $this->setResponseResultToError($responseResultId);
            $this->CI->utils->debug_log('PT_V2 (processResultBoolean) got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
        }
        return $success;
	}

    public function generateUrl($apiName, $params) {
		$url = $this->getSystemInfo('url').self::URI_MAP[$apiName];

		switch ($this->method){
			case 'POST':

				break;
			case 'GET':
				$params_string = http_build_query($params);
				$url.='?'.$params_string;
				break;
		}

        $this->utils->debug_log("PT_V2: (generateUrl) url: $url");
        return $url;
    }

	protected function customHttpCall($ch, $params) {
		$this->utils->debug_log("PT_V2: (customHttpCall) method: ", $this->method, 'params:', json_encode($params));
		$headers = array('X-Auth-Api-Key: '.$this->key);
		switch ($this->method){
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				break;
			case 'GET':

			break;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	public function getHttpHeaders($params) {
		return array("Accept" => "application/json",
			"Content-Type" => "application/json",
			"X-Auth-Api-Key" => $this->getSystemInfo('key'));
	}

	public function removePrefixFromUsername($gameUsername, $prefix){
		$gameUsername = ltrim($gameUsername, $prefix);
		return $gameUsername;
	}

	/**
	 * Note: When creating a player send the non prefix playername, the BO will add the prefix on their end
	 */
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		$this->utils->debug_log("PT_V2: (createPlayer) playerName: $playerName");
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$this->utils->debug_log("PT_V2: (createPlayer) gameUsername: $gameUsername");

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'playerId' => $playerId,
		);

		$params = array(
			"name" => $gameUsername,
			"username" => $gameUsername,
			"password" => $this->password_prefix.$password,
			"kiosk_name" => $this->kiosk_name,
		);

		if($this->is_auto_add_prefix){
			$params['username'] = $playerName;
		}

        $this->method = 'POST';

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params) {
		$this->utils->debug_log("PT_V2: (processResultForCreatePlayer) params:", $params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$playerId = $this->getVariableFromContext($params, 'playerId');

		$success = $this->processResultBoolean($responseResultId, $resultJson, $playerName);

		if ($success) {
			$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);
		}

		return array($success, null);
	}

	public function updatePlayerInfo($playerName, $infos) {
		$this->utils->debug_log("PT_V2: (updatePlayerInfo) playerName:", $playerName, "infos:", $infos);
		return $this->returnUnimplemented();
	}

	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
		$this->utils->debug_log("PT_V2: (depositToGame) playerName: $playerName", "amount: $amount");

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForDepositToGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'amount' => $amount,
            'external_transaction_id'=>$transfer_secure_id,
		);

		$params = array(
			"from_admin" => $this->admin_user_name,
			"to_player" => $gameUsername,
			"currency" => $this->currency,
			"amount" => $this->dBtoGameAmount($amount),
			"server" => $this->game_server_name,
			"client_reference_no" => $transfer_secure_id,
		);

        $this->method = 'POST';

		return $this->callApi(self::API_depositToGame, $params, $context);
	}

	public function processResultForDepositToGame($params) {
		$this->utils->debug_log("PT_V2: (processResultForDepositToGame) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array('response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN);

        if($success) {
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
			$result['external_transaction_id']=@$resultJson['data']['reference_no'];
        }else{
            $result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['code']);
        }

        return array($success, $result);
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		$this->utils->debug_log("PT_V2: (withdrawFromGame) playerName: $playerName", "amount: $amount");

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForWithdrawFromGame',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
            'external_transaction_id'=>$transfer_secure_id,
			'amount' => -$amount,
		);

		$params = array(
			"from_player" => $gameUsername,
			"to_admin" => $this->admin_user_name,
			"currency" => $this->currency,
			"amount" => $this->dBtoGameAmount($amount),
			"is_forced" => $this->is_forced_withdraw,
			"server" => $this->game_server_name,
			"client_reference_no" => $transfer_secure_id,
		);

        $this->method = 'POST';

		return $this->callApi(self::API_withdrawFromGame, $params, $context);
	}

	public function processResultForWithdrawFromGame($params) {
		$this->utils->debug_log("PT_V2: (processResultForWithdrawFromGame) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$amount = $this->getVariableFromContext($params, 'amount');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array(
			'response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if($success) {
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
			$result['external_transaction_id']=@$resultJson['data']['reference_no'];
		}else{
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
			$result['reason_id'] = $this->getTransferErrorReasonCode($resultArr['code']);
		}

		return array($success, $result);
	}

	/**
	 * Error codes still in progress need to update once available
	 */
    public function getTransferErrorReasonCode($errorCode) {
        switch ($errorCode) {
            case 'T002-403':
                $reasonCode = self::REASON_NO_ENOUGH_CREDIT_IN_SYSTEM;
                break;
            default:
                $reasonCode = self::REASON_UNKNOWN;
        }

        return $reasonCode;
    }

	public function isPlayerExist($playerName) {
		$this->utils->debug_log("PT_V2: (isPlayerExist) params:", $playerName);

		$playerId=$this->getPlayerIdFromUsername($playerName);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist',
			'playerName' => $playerName,
			'playerId'=>$playerId,
		);

		$params = array(
			"player_name" => $gameUsername,
			"is_realtime" => 0
		);

        $this->method = 'GET';

		return $callResult = $this->callApi(self::API_isPlayerExist, $params, $context);
	}

	public function getPlayerGamePasswordPrefix(){
		return $this->password_prefix;
	}

	public function processResultForIsPlayerExist($params) {
		$this->utils->debug_log("PT_V2: (processResultForIsPlayerExist) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$playerId = $this->getVariableFromContext($params, 'playerId');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array('exists' => false, 'response_result_id'=>$responseResultId);
		$code = isset($resultJson['code']) ? $resultJson['code'] : null;

		if($success && in_array(trim($code),self::SUCCESS_CODES)) {
			$success = true;
			$result["exists"] = true;
		}else{
			if($code==400){
                $success = true;
                $result['exists'] = false;
            }
		}
		return array(true, $result);
	}

	public function queryPlayerInfo($playerName) {
		$this->utils->debug_log("PT_V2: (queryPlayerInfo) playerName:", $playerName);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerInfo',
			'playerName' => $playerName,
		);

		$params = array(
			"player_name" => $gameUsername,
			"is_realtime" => 1
		);

        $this->method = 'GET';

		return $callResult = $this->callApi(self::API_queryPlayerInfo, $params, $context);
	}

	public function processResultForQueryPlayerInfo($params) {
		$this->utils->debug_log("PT_V2: (processResultForQueryPlayerInfo) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array();
		if ($success) {
			$ptrlt = $resultJson['data'];
			$playerInfo = array(
				'playerName' => $ptrlt['username'],
				// 'kiosk' => $ptrlt['kiosk'],
				// 'name' => $ptrlt['name'],
				// 'email' => $ptrlt['email'],
				// 'phone' => $ptrlt['phone'],
				// 'country' => $ptrlt['country'],
				// 'gender' => $ptrlt['gender'],
				// 'servers' => $ptrlt['servers'],
				// 'main_wallet' => $ptrlt['main_wallet'],
				'blocked' => ($ptrlt['is_frozen'] == 1),
			);

			if ($ptrlt['is_frozen'] == 1) {
				$this->utils->debug_log("PT_V2: (processResultForQueryPlayerInfo:blockUsernameInDB) playerName: ".$playerName);

				$this->blockUsernameInDB($gameUsername);
			} else {
				$this->utils->debug_log("PT_V2: (processResultForQueryPlayerInfo:unblockUsernameInDB) playerName: ".$playerName);

				$this->unblockUsernameInDB($gameUsername);
			}
			$result["playerInfo"] = $playerInfo;
		}

		return array($success, $result);
	}

	public function changePassword($playerName, $oldPassword, $newPassword) {
		$this->utils->debug_log("PT_V2: (changePassword) playerName:", $playerName);

		$playerUsername = $playerName;
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForChangePassword',
			'playerName' => $playerUsername,
			'password' => $this->password_prefix.$newPassword,
		);

		$params = array(
			"player_name" => $gameUsername,
			"password" => $this->password_prefix.$newPassword
		);

        $this->method = 'POST';

		return $this->callApi(self::API_changePassword, $params, $context);
	}

	public function processResultForChangePassword($params) {
		$this->utils->debug_log("PT_V2: (processResultForChangePassword) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array();
		if ($success) {
			$result["password"] = $this->getVariableFromContext($params, 'password');
			$playerName = $this->getVariableFromContext($params, 'playerName');
			$playerId = $this->getPlayerIdInPlayer($playerName);
			if($playerId) {
				$this->utils->debug_log("PT_V2: (processResultForChangePassword:updatePasswordForPlayer) params:", $params);

				$this->updatePasswordForPlayer($playerId, $result["password"]);
			}else{
				$this->utils->debug_log("PT_V2: (processResultForChangePassword:cannotFindPlayer) playerName: ".$playerName);
			}
		}

		return array($success, $result);
	}

	public function login($playerName, $password = null) {
		$this->utils->debug_log("PT_V2: (login) playerName:", $playerName);

		return $this->returnUnimplemented();
	}

	public function logout($playerName, $password = null) {
		$this->utils->debug_log("PT_V2: (logout) playerName:", $playerName);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogout',
			'playerName' => $playerName,
		);

		$params = array(
			"player_name" => $gameUsername,
			"server_name" => $this->game_server_name
		);

        $this->method = 'GET';

		return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params) {
		$this->utils->debug_log("PT_V2: (processResultForLogout) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);

		$success = $this->processResultBoolean($responseResultId, $resultJson);

		return array($success, null);
	}

	public function queryPlayerBalance($playerName) {
		$this->utils->debug_log("PT_V2: (queryPlayerBalance) playerName:", $playerName);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
		);

		$params = array(
			"player_name" => $gameUsername,
			"server_name" => $this->game_server_name
		);

        $this->method = 'GET';

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$this->utils->debug_log("PT_V2: (processResultForQueryPlayerBalance) params:", $params);

		$responseResultId = $params['responseResultId'];
		$resultJson = $this->convertResultJsonFromParams($params);
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array();
		if ($success && isset($resultJson['data']['wallets'][$this->game_server_name])) {
			$result['balance'] = $this->gameAmountBalance(floatval($resultJson['data']['wallets'][$this->game_server_name]));
		} else {
			$success = false;
		}

		return array($success, $result);
	}

	public function checkLoginStatus($playerName) {
		$this->utils->debug_log("PT_V2: (checkLoginStatus) playerName:", $playerName);

		return $this->returnUnimplemented();
	}

	public function queryTransaction($transactionId, $extra) {
		$this->utils->debug_log("PT_V2: (queryTransaction) transactionId:", $transactionId);

		$playerId=$extra['playerId'];

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryTransaction',
			'playerId'=>$playerId,
			'external_transaction_id' => $transactionId,
		);

		$params = array(
			"reference_no" => $transactionId,
		);

        $this->method = 'GET';

		return $this->callApi(self::API_queryTransaction, $params, $context);
	}

	public function processResultForQueryTransaction($params) {
		$this->utils->debug_log("PT_V2: (processResultForQueryTransaction) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);
		$playerId = $this->getVariableFromContext($params, 'playerId');
		$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');

		$success = $this->processResultBoolean($responseResultId, $resultJson);
		$result = array('response_result_id' => $responseResultId,
			'external_transaction_id'=>$external_transaction_id,
			'status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN);

		$validation_data = @$resultJson['data'];
		if ($success && isset($validation_data['reference_no']) && $validation_data['reference_no']==$external_transaction_id) {
			$result['reason_id'] = self::REASON_UNKNOWN;
			$result['status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$this->utils->debug_log("PT_V2: (processResultForQueryTransaction) approved:", 'external_transaction_id', $external_transaction_id, 'validation_data', $validation_data);
		}else{
			$success  = false;
			$result['reason_id'] = self::REASON_UNKNOWN;
			$result['status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

		return array($success, $result);
	}

	public function resetPlayer($playerName, $password = null) {
		$this->utils->debug_log("PT_V2: (resetPlayer) playerName:", $playerName);

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultResetPlayer',
			'playerName' => $playerName,
		);

		$params = array(
			"player_name" => $gameUsername,
			"server_name" => $this->game_server_name
		);

        $this->method = 'GET';

		return $this->callApi(self::API_resetPlayer, $params, $context);
	}

	public function processResultResetPlayer($params) {
		$this->utils->debug_log("PT_V2: (processResultResetPlayer) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);

		$success = $this->processResultBoolean($responseResultId, $resultJson);

		return array($success, null);
	}

	public function syncOriginalGameLogs($token) {
		$this->utils->debug_log("PT_V2: (syncOriginalGameLogs)");

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');
		$playerName = parent::getValueFromSyncInfo($token, 'playerName');

        $this->utils->debug_log("PT_V2: (syncOriginalGameLogs) platformCode",$this->getPlatformCode(),"startDate:",$startDate," endData: ", $endDate," playerName: ", $playerName);

		$syncId = parent::getValueFromSyncInfo($token, 'syncId');

		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$startDate->modify($this->getDatetimeAdjust());

		$cnt = 0;
		$real_count = 0;
		$sum = 0;
		$success = true;

		$queryDateTimeStart = $startDate->format('Y-m-d H:i:s');
		$queryDateTimeEnd = $startDate->modify($this->sync_time_interval)->format('Y-m-d H:i:s');
		$queryDateTimeMax = $endDate->format('Y-m-d H:i:s');

        $this->utils->debug_log("PT_V2: (syncOriginalGameLogs) queryDateTimeMax:",$queryDateTimeMax," queryDateTimeStart: ", $queryDateTimeStart);

		while ($queryDateTimeMax  > $queryDateTimeStart) {

			$done = false;
			$currentPage = self::START_PAGE;

			while (!$done) {

				$dont_save_response_in_api = $this->getConfig('dont_save_response_in_api');

				$rlt = null;

				$context = array(
					'callback_obj' => $this,
					'callback_method' => 'processResultForSyncGameRecords',
					'playerName' => $playerName,
					'startDate' => $queryDateTimeStart,
					'endDate' => $queryDateTimeEnd
				);

				$this->utils->debug_log("############# PT_V2: (syncOriginalGameLogs) queryDateTimeStart:",$queryDateTimeStart," queryDateTimeEnd: ", $queryDateTimeEnd);


				$startDateParam=new DateTime($queryDateTimeStart);
				if($queryDateTimeEnd>$queryDateTimeMax){
					$endDateParam=new DateTime($queryDateTimeMax);
				}else{
					$endDateParam=new DateTime($queryDateTimeEnd);
				}

				$params = array(
					"game_server" => $this->game_server_name,
					"date_from" => $startDateParam->format('Y-m-d H:i:s'),
					"date_to" => $endDateParam->format('Y-m-d H:i:s'),
					'page' => $currentPage,
					'per_page' => $this->perPageSize,
					'player_name' => $gameUsername,
					'is_frozen' => -1,
					'kiosk_name' => $this->kiosk_name,
					'timezone'=>date_default_timezone_get()
				);

				$this->method = 'GET';

				$rlt = $this->callApi(self::API_syncGameRecords, $params, $context);

				$done = true;
				if ($rlt) {
					$success = $rlt['success'];
				}
				if ($rlt && $rlt['success']) {
					$currentPage = $rlt['currentPage'];
					$total_pages = $rlt['totalPages'];
					//next page
					$currentPage += 1;

					$done = $currentPage > $total_pages;
					$cnt += $rlt['totalCount'];
					$sum += $rlt['sum'];
					$this->CI->utils->debug_log('PT_V2: currentPage', $currentPage, 'total_pages', $total_pages, 'done', $done, 'result', $rlt);
				}
			}//end while for page

			$queryDateTimeStart = $endDateParam->format('Y-m-d H:i:s');
	    	$queryDateTimeEnd  = (new DateTime($queryDateTimeStart))->modify($this->sync_time_interval)->format('Y-m-d H:i:s');

			sleep($this->sleep);
		}//end while for date

		$this->CI->utils->debug_log('queryDateTimeStart', $queryDateTimeStart, 'queryDateTimeEnd', $queryDateTimeEnd,
			'queryDateTimeMax', $queryDateTimeMax);

		return array('success' => $success);

	}

    public function processResultForSyncGameRecords($params) {
		$this->utils->debug_log("PT_V2: (processResultForSyncGameRecords)");
        $this->CI->load->model(array('original_game_logs_model'));
        $resultArr = $this->getResultJsonFromParams($params);
		$this->utils->debug_log("PT_V2: (processResultForSyncGameRecords) resultArr", $resultArr);
        $responseResultId = $this->getResponseResultIdFromParams($params);

        //check if call success
        $success = $this->processResultBoolean($responseResultId, $resultArr);

        if(!$success) {
            return array(false);
        }

        $gameRecords = array();
        if(isset($resultArr['data']) && $resultArr['data']['data'] ) {
            $gameRecords = $resultArr['data']['data'];
        }

        $result = [
			'data_count' => 0,
			'totalPages' => 0,
			'currentPage' => 0,
			'itemsPerPage' => 0,
			'hasNextPage' => false,
			'totalCount' => 0,
			'sum' => 0
		];

        if(!empty($gameRecords) && is_array($gameRecords)){
            # add in columns not returned by API, and process username column to remove suffix
            foreach($gameRecords as $index => $record) {
				$this->preProcessOriginalGameLogs($gameRecords[$index]);

                $gameRecords[$index]['external_uniqueid'] = $gameRecords[$index]['hash'];
				$gameRecords[$index]['response_result_id'] = $responseResultId;
				$gameRecords[$index]['bet_datetime'] = $this->gameTimeToServerTime($gameRecords[$index]['bet_datetime']);

				// logs with exit_type == 'exit' should be tagged as invalid logs
				$gameRecords[$index]['is_valid_game_logs'] = 1;
				if($gameRecords[$index]['exit_type']=='exit'){
					$gameRecords[$index]['is_valid_game_logs'] = 0;
				}

            }

            list($insertRows, $updateRows)=$this->CI->original_game_logs_model->getInsertAndUpdateRowsForOriginal(
                self::ORIGINAL_TABLE,
                $gameRecords,
                'external_uniqueid',
                'external_uniqueid',
                self::MD5_FIELDS_FOR_ORIGINAL,
                'md5_sum',
                'id',
                self::MD5_FLOAT_AMOUNT_FIELDS
            );

            $this->CI->utils->debug_log('PT_V2: (processResultForSyncGameRecords) after process available rows', count($gameRecords), count($insertRows), count($updateRows));

            unset($gameRecords);

            if (!empty($insertRows)) {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($insertRows, 'insert',
                    ['responseResultId'=>$responseResultId]);
            }
            unset($insertRows);

            if (!empty($updateRows)) {
                $result['data_count'] += $this->updateOrInsertOriginalGameLogs($updateRows, 'update',
                    ['responseResultId'=>$responseResultId]);
            }
			unset($updateRows);

			$result['totalPages'] = $resultArr['data']['pagination']['last_page'];
			$result['currentPage'] = $resultArr['data']['pagination']['page'];
			$result['itemsPerPage'] = $resultArr['data']['pagination']['per_page'];
			$result['hasNextPage'] = $resultArr['data']['pagination']['has_next_page'];
			$result['totalCount'] = @count($resultArr['data']['data']);
			$result['sum'] = $result['totalCount'];
        }

        return array(true, $result);
	}

	/**
	 * Cleanup gamerecords from API to eliminate un-supported fields
	 */
	private function preProcessOriginalGameLogs(&$gameRecords){
		foreach($gameRecords as $key => $value){
			if(!in_array($key, self::ORIGINAL_TABLE_FIELDS)){
				unset($gameRecords[$key]);
				$this->CI->utils->debug_log('PT_V2: (preProcessOriginalGameLogs) unsetting unsupported field: '.$key);
			}
		}
	}

    private function updateOrInsertOriginalGameLogs($rows, $update_type, $additionalInfo=[]){
        $dataCount=0;
        if(!empty($rows)){
            $responseResultId=$additionalInfo['responseResultId'];
            foreach ($rows as $record) {

                if ($update_type=='update') {
                    $this->CI->original_game_logs_model->updateRowsToOriginal(self::ORIGINAL_TABLE, $record);
                } else {
                    unset($record['id']);
                    $this->CI->original_game_logs_model->insertRowsToOriginal(self::ORIGINAL_TABLE, $record);
                }
                $dataCount++;
                unset($data);
            }
        }

        return $dataCount;
    }

    public function syncMergeToGameLogs($token) {
        $this->CI->utils->debug_log('PT_V2 (syncMergeToGameLogs)');

        return $this->commonSyncMergeToGameLogs($token,
            $this,
            [$this, 'queryOriginalGameLogs'],
            [$this, 'makeParamsForInsertOrUpdateGameLogsRow'],
            [$this, 'preprocessOriginalRowForGameLogs'],
            false);
    }

    public function queryOriginalGameLogs($dateFrom, $dateTo, $use_bet_time){
        $this->CI->utils->debug_log('PT_V2 (queryOriginalGameLogs)');

        $sql = <<<EOD
SELECT
    gd.game_type_id,
    gd.id as game_description_id,
    gd.game_code,
    original.game_name as game_type,
    original.game_name as game_name,
    original.game_shortcode as game_code_unknown,

    game_provider_auth.player_id,
    original.gamzo_player_name as player_username,

    original.is_win as is_win,
    original.bet as bet_amount,
    original.win as win,
	original.bet as real_bet,
	original.balance_after as balance_after,

	original.progressive_bet as progressive_bet,
	original.progressive_win as progressive_win,

    original.bet_datetime as bet_at,

    original.game_server_reference_1 as round_number,

    original.external_uniqueid,
    original.md5_sum,
    original.response_result_id,
    original.id as sync_index,
    gd.game_name as game_description_name
FROM
    pt_v2_game_logs as original
    LEFT JOIN game_description AS gd ON gd.external_game_id = original.game_shortcode AND gd.game_platform_id = ?
    JOIN game_provider_auth ON original.gamzo_player_name = game_provider_auth.login_name and game_provider_auth.game_provider_id = ?
WHERE
	original.bet_datetime >= ? AND original.bet_datetime <= ?
	AND original.is_valid_game_logs=1;
EOD;

        $params=[
            $this->getPlatformCode(),
            $this->getPlatformCode(),
            $dateFrom,
            $dateTo
        ];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
    }

    public function makeParamsForInsertOrUpdateGameLogsRow(array $row){
		$this->CI->utils->debug_log('PT_V2 (makeParamsForInsertOrUpdateGameLogsRow)');

        if(empty($row['md5_sum'])){
            $this->CI->utils->debug_log('PT_V2 (makeParamsForInsertOrUpdateGameLogsRow=>generateMD5SumOneRow)');
            $row['md5_sum']=$this->CI->game_logs->generateMD5SumOneRow($row, self::MD5_FIELDS_FOR_MERGE,
                self::MD5_FLOAT_AMOUNT_FIELDS_FOR_MERGE);
		}

		$result_amount = (($row['win'] + $row['progressive_win']) - $row['bet_amount']);
		$result_amount = $this->convertToDBAmount($result_amount);
		$bet_amount = $this->convertToDBAmount($row['bet_amount']);
		$balance_after = $this->convertToDBAmount($row['balance_after']);

        return [
            'game_info' => [
                'game_type_id' => $row['game_type_id'],
                'game_description_id' => $row['game_description_id'],
                'game_code' => $row['game_code'],
                'game_type' => null,
                'game' => $row['game_code']
            ],
            'player_info' => [
                'player_id' => $row['player_id'],
                'player_username' => $row['player_username']
            ],
            'amount_info' => [
                'bet_amount' => $bet_amount,
                'result_amount' => $result_amount,
                'bet_for_cashback' => $bet_amount,
                'real_betting_amount' => $bet_amount,
                'win_amount' => null,
                'loss_amount' => null,
                'after_balance' => $balance_after
            ],
            'date_info' => [
                'start_at' => $row['bet_at'],
                'end_at' => $row['bet_at'],
                'bet_at' => $row['bet_at'],
                'updated_at' => $this->CI->utils->getNowForMysql(),
            ],
            'flag' => Game_logs::FLAG_GAME,
            'status' => Game_logs::STATUS_SETTLED,
            'additional_info' => [
                'has_both_side' => 0,
                'external_uniqueid' => $row['external_uniqueid'],
                'round_number' => $row['round_number'],
                'md5_sum' => $row['md5_sum'],
                'response_result_id' => $row['response_result_id'],
                'sync_index' => $row['sync_index'],
                'bet_type' => null
            ],
            'bet_details' => null,
            'extra' => [],
            'game_logs_id'=>isset($row['game_logs_id']) ? $row['game_logs_id'] : null,
            'game_logs_unsettle_id'=>isset($row['game_logs_unsettle_id']) ? $row['game_logs_unsettle_id'] : null,
        ];
    }

    public function preprocessOriginalRowForGameLogs(array &$row){
        if (empty($row['game_description_id'])) {
            $unknownGame = $this->getUnknownGame($this->getPlatformCode());
            list($game_description_id,$game_type_id) = $this->getGameDescriptionInfo($row,$unknownGame);
            $row['game_description_id']= $game_description_id;
            $row['game_type_id'] = $game_type_id;
        }
        $this->CI->utils->debug_log('PT_V2 (preprocessOriginalRowForGameLogs)');
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
			$game_description_id=$this->CI->game_description_model->processUnknownGame($this->getPlatformCode(),
				$unknownGame->game_type_id, $row['game_name'], $row['game_code_unknown']);
		}

		return [$game_description_id, $game_type_id];
	}

	public function generateGotoUri($playerName, $extra){

		return '/iframe_module/goto_ptv2game/default/'.$extra['game_code'].'/'.$extra['game_mode'].'/'.$extra['is_mobile'];

	}

	public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
        	case Language_function::INT_LANG_ENGLISH:
            case 'en-us':
                $lang = 'en'; // english
                break;
            case Language_function::INT_LANG_CHINESE:
			case 'cn':
			case 'zh-cn':
                $lang = 'zh-cn'; // chinese
                break;
            case Language_function::INT_LANG_INDONESIAN:
			case 'id':
			case 'id-id':
                $lang = 'en'; // indonesia
                break;
            case Language_function::INT_LANG_VIETNAMESE:
            case 'vi-vn':
                $lang = 'en'; // vietnamese
                break;
            case Language_function::INT_LANG_KOREAN:
            case 'ko-kr':
                $lang = 'ko'; // korean
                break;
			case Language_function::INT_LANG_THAI:
			case 'th':
            case 'th-th':
                $lang = 'th'; // thai
                break;
            default:
                $lang = 'en'; // default as english
                break;
        }
        return $lang;
	}

	public function queryForwardGame($playerName, $extra) {
		$nextUrl=$this->generateGotoUri($playerName, $extra);
		$result=$this->forwardToWhiteDomain($playerName, $nextUrl);
		if($result['success']){
			return $result;
		}

        $this->CI->load->model(['external_system', 'game_provider_auth', 'player_model']);

		$success=true;
		$platformName = $this->CI->external_system->getNameById($this->getPlatformCode());
		$gameCode=$extra['game_code'];
		$ptLang=$this->getLauncherLanguage($extra['language']);
		$api_play_pt = $this->getSystemInfo('API_PLAY_PT');
		$player_url = $this->CI->utils->getSystemUrl('player');

        $playerId=$this->CI->player_model->getPlayerIdByUsername($playerName);

        $loginInfo = $this->CI->game_provider_auth->getLoginInfoByPlayerId($playerId, $this->getPlatformCode());

        //for pt deposit mini cashier
		$player_token = $this->getPlayerTokenByUsername($playerName);
		$deposit_url = $this->CI->utils->getSystemUrl('player','/player_center2/deposit');
        $mobile_lobby = $this->CI->utils->getSystemUrl('m') . $this->mobile_lobby;
        $mobile_logout_url = $this->CI->utils->getSystemUrl('m') . $this->mobile_logout_url;
        $merchant_code = null;
        # for minicashier url for mobile to redirect to client player center not gamegateway player center
        if (isset($extra['extra']['t1_mini_cashier_url']) && !empty($extra['extra']['t1_mini_cashier_url'])) {
            $deposit_url = $extra['extra']['t1_mini_cashier_url'];
        }
        # for gamegateway logout url for mobile to redirect to client player center not gamegateway player center
        if (isset($extra['extra']['t1_mobile_logout_url']) && !empty($extra['extra']['t1_mobile_logout_url'])) {
            $mobile_logout_url = $extra['extra']['t1_mobile_logout_url'];
        }
        # for gamegateway lobby url for mobile to redirect to client player center not gamegateway player center
        if (isset($extra['extra']['t1_lobby_url']) && !empty($extra['extra']['t1_lobby_url'])) {
            $mobile_lobby = $extra['extra']['t1_lobby_url'];
        }
        # get merchant code when using game gateway
        if (isset($extra['extra']['t1_merchant_code']) && !empty($extra['extra']['t1_merchant_code'])) {
            $merchant_code = $extra['extra']['t1_merchant_code'];
            $this->support_url = !empty($merchant_code) ? $this->client_support_url[$merchant_code]['url'] : null;
        }

        $online_status = $this->checkIsOnlineStatus($playerName);
        $do_launch_multiple_pt = $this->getSystemInfo('do_launch_multiple_pt', false);
        $debug_javascript_response = $this->getSystemInfo('debug_javascript_response', false);

		$result = [
			'success'=>$success,
			'launch_game_on_player'		=> $this->launch_game_on_player,
			'platformName' 				=> $platformName,
			'game_code'					=> $gameCode,
			'lang' 						=> $ptLang,
			'api_play_pt' 				=> $this->api_play_pt,
			'api_play_pt_js' 			=> (isset($extra['is_mobile']) && $extra['is_mobile']) ? $this->api_play_pt_js_h5 : $this->api_play_pt_js,	# we will check here if game is HTML5(mobile) or Flash(WEB) by is_mobile
			'player_url' 				=> $player_url,
			'mobile_js_url' 			=> (isset($extra['is_mobile']) && $extra['is_mobile']) ? $this->mobile_js_url : '',
			'mobile'					=> $extra['is_mobile']?"mobile":"",
			'mobile_systemId'			=> $this->mobile_systemId,
			'mobile_launcher'			=> rtrim($this->mobile_launcher, '/').'/',
			'load_pt_js_from_our_server'=> $this->load_pt_js_from_our_server,
			'game_username'				=> $loginInfo->login_name,
			'game_secret'				=> base64_encode($loginInfo->password),
			'mobile_lobby'				=> $mobile_lobby,
			'mobile_logout_url'			=> $mobile_logout_url,
			'deposit_url'				=> $deposit_url,
			'support_url'				=> $this->support_url,
			'support_button_link' 		=> $this->CI->utils->getSystemUrl('player'). '/iframe_module/goto_ptv2game/default/default/real/mobile/1/'.$merchant_code,
			'v'							=> $this->CI->utils->getCmsVersion(),
			'game_platform_id'			=> $this->getPlatformCode(),
			'allow_fullscreen'			=> $this->getSystemInfo('allow_fullscreen', true),
			'is_online'					=> true,
			'do_launch_multiple_pt'		=> $do_launch_multiple_pt,
			'debug_javascript_response'	=> $debug_javascript_response,
		];
		
		$this->utils->debug_log("PT_V2: (queryForwardGame) launch method: ".(isset($extra['is_mobile']) && $extra['is_mobile']?"Mobile/HTML5":"Flash"));

		return $result;
	}

	public function checkIsOnlineStatus($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCheckIsOnlineStatus',
		);

		$params = array(
			"player_name" => $gameUsername,
			"server_name" => $this->game_server_name,
		);

        $this->method = 'GET';
		return $this->callApi(self::API_checkLoginStatus, $params, $context);
	}

	public function processResultForCheckIsOnlineStatus($params) {
		$this->utils->debug_log("PT_V2: (processResultForDepositToGame) params:", $params);

		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJson = $this->getResultJsonFromParams($params);

		$success = $this->processResultBoolean($responseResultId, $resultJson);

		$result['online'] = @$resultJson['data']['online'];
		return array($success, $result);
	}

    /**
     * overwrite it , if not http call
     *
     * @return boolean true=error, false=ok
     */
    protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
		// $statusCode = intval($statusCode, 10);
		if($apiName == self::API_isPlayerExist){
			return $errCode || intval($statusCode, 10) >= 401;
		}
        return parent::isErrorCode($apiName, $params, $statusCode, $errCode, $error);
	}

}/*END OF CLASS*/