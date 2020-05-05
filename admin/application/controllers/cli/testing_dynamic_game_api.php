<?php

require_once dirname(__FILE__) . '/base_testing.php';
/**
 * Defines general behavior of game API classes.
 *
 * General behaviors include:
 * * XML extraction of logs
 * * sync and merge games logs for AGBBIN
 *
 * The functions implemented by child class:
 * * Populating game form parameters
 * * Handling callbacks
 *
 *
 *
 * @see Return testing response for specific game api
 *
 * @category Game_platform
 * @version 1.0.1
 * @copyright 2013-2017 TripleOneTech
 */

class Testing_dynamic_game_api extends BaseTesting {

	private $platformCode = null;

	private $api = null;

	private $username = null;
	private $usernames = [];
	private $limit = null;
	private $password = null;
	private $player_id = null;
	private $new_password = null;
	private $old_password = null;
    private $amount = null;
	private $external_transaction_id = null;
	private $token = null;
	private $date_time_from = null;
	private $date_time_to = null;
	private $email = null;
	private $extra = null;

	const TEST_CREATE_PLAYER = "001";
	const TEST_IS_PLAYER_EXIST = "002";
	const TEST_CHANGE_PASSWORD = "003";
	const TEST_QUERY_PLAYER_BALANCE = "004";
	const TEST_DEPOSIT = "005";
	const TEST_WITHDRAWAL = "006";
	const TEST_QUERY_FORWARD_GAME = "007";
	const TEST_BLOCK_PLAYER = "008";
	const TEST_UNBLOCK_PLAYER = "009";
	const TEST_SYNC_GAME_LOGS = "010";
	const TEST_SYNC_MERGE_TO_GAME_LOGS = "011";
    const TEST_LOGOUT = "012";
	const TEST_QUERY_PLAYER_TRANSACTION = "013";
	const TEST_QUERY_PLAYER_INFO = "014";
	const TEST_BATCH_QUERY_PLAYER_BALANCE = "015";
	const TEST_GET_BET_LIMIT= "016";
	const TEST_UPDATE_BET_LIMIT= "017";
	const DOCS = "docs";
	const TEST_METHOD = "999";

	public function init(){

		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);

		$testing_dynamic_secret_key = $this->api->getSystemInfo('testing_dynamic_secret_key','secret_unique');

		$testing_dynamic_config_key = $this->utils->getConfig('testing_dynamic_config_key');

		// if($testing_dynamic_secret_key != $testing_dynamic_config_key){
		// 	throw new Exception("Unauthorized Access");
		// }
	}


	public function run($platformCodes = null) {
		header('Content-Type: application/json');

		if(empty($platformCodes)){
			 $this->docs();exit();
		}else{
			$this->platformCode = $platformCodes;
			$this->init();
		}

		$request = file_get_contents('php://input');
		$params = array_merge(json_decode($request, true) ?: array(), ($this->input->post() ?: array()));

		foreach ($params as $key => $value) {
			$this->$key = $value;
		}

		switch ($params["request_code"]) {
		    case self::TEST_CREATE_PLAYER:
		        $this->testCreatePlayer();
		        break;
		    case self::TEST_IS_PLAYER_EXIST:
		        $this->testIsPlayerExist();
		        break;
		    case self::TEST_CHANGE_PASSWORD:
		        $this->testChangePassword();
		        break;
		    case self::TEST_QUERY_PLAYER_BALANCE:
		        $this->testQueryPlayerBalance();
		        break;
		    case self::TEST_DEPOSIT:
		        $this->testDeposit();
		        break;
		    case self::TEST_WITHDRAWAL:
		        $this->testWithdraw();
		        break;
		    case self::TEST_QUERY_FORWARD_GAME:
		        $this->testQueryForwardGame();
		        break;
		    case self::TEST_BLOCK_PLAYER:
		        $this->testBlockPlayer();
		        break;
		    case self::TEST_UNBLOCK_PLAYER:
		        $this->testUnblockPlayer();
		        break;
		    case self::TEST_SYNC_GAME_LOGS:
		        $this->testSyncGameLogs();
		        break;
		    case self::TEST_SYNC_MERGE_TO_GAME_LOGS:
		        $this->testSyncMergeToGameLogs();
		        break;
            case self::TEST_LOGOUT:
                $this->testLogout();
                break;
		    case self::TEST_QUERY_PLAYER_TRANSACTION:
		        $this->testQueryTransaction();
				break;
			case self::TEST_QUERY_PLAYER_INFO:
				$this->testQueryPlayerInfo();
				break;
			case self::TEST_BATCH_QUERY_PLAYER_BALANCE:
				$this->testBatchQueryPlayerBalance();
				break;
			case self::TEST_GET_BET_LIMIT:
				$this->testGetBetLimit();
				break;
			case self::TEST_UPDATE_BET_LIMIT:
				$this->testUpdateBetLimit();
				break;
		    case self::DOCS:
		        $this->docs();exit();
		        break;
		    case self::TEST_METHOD:
		        $this->testMethod();exit();
		        break;
		    default:
		        $this->docs();exit();
		}

	}

	public function testMethod() {
		$method = $this->method;

		$response = array();

		if( empty($method) ) {
			$response = array(
					"Success" => False,
					"Information" => "method is required"
				);
		} else {
			$rlt = $this->api->$method();
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	public function docs() {
		$response = array();
		$response["test_game_api" ] = array(
								"url" => $this->utils->getSystemHost("admin")."/cli/Testing_dynamic_game_api/run/{param}",
								"param" => "Game API ID only"
					);

		$response["data_params" ] = array(
								"request_code" => "001",
								"username" => "testuser",
								"usernames"=>['testt1dev1','testt1dev2'],
								"limit" => json_encode([
									[
										'game_type'=>1,
										'playerMin'=>200
									],
									[
										'game_type'=>2,
										'playerMin'=>300
									]
								]),
								"password" => "pass1234",
								"player_id" => "12345",
								"amount" => "1",
								"token" => "sample token",
								"date_time_from" => "2017-06-15 13:04:4",
								"date_time_to" => "2017-06-15 14:00:00",
					);

		$response["request_code" ] = array(
						"TEST_CREATE_PLAYER" => array(
									"request_code" => "001",
									"required_fields" => "username, password, playerId"
								),
						"TEST_IS_PLAYER_EXIST" => array(
									"request_code" => "002",
									"required_fields" => "username"
								),
						"TEST_CHANGE_PASSWORD" => array(
									"request_code" => "003",
									"required_fields" => "username, new_password, old_password"
								),
						"TEST_QUERY_PLAYER_BALANCE" => array(
									"request_code" => "004",
									"required_fields" => "username"
								),
						"TEST_DEPOSIT" => array(
									"request_code" => "005",
									"required_fields" => "username, amount"
								),
						"TEST_WITHDRAWAL" => array(
									"request_code" => "006",
									"required_fields" => "username, amount"
								),
						"TEST_QUERY_FORWARD_GAME" => array(
									"request_code" => "007",
									"required_fields" => "username"
								),
						"TEST_BLOCK_PLAYER" => array(
									"request_code" => "008",
									"required_fields" => "username"
								),
						"TEST_UNBLOCK_PLAYER" => array(
									"request_code" => "009",
									"required_fields" => "username"
								),
						"TEST_SYNC_GAME_LOGS" => array(
									"request_code" => "010",
									"required_fields" => "token, date_time_from, date_time_to"
								),
						"TEST_SYNC_MERGE_TO_GAME_LOGS" => array(
									"request_code" => "011",
									"required_fields" => "token, date_time_from, date_time_to"
								),
						"TEST_LOGOUT" => array(
									"request_code" => "012",
									"required_fields" => "username"
								),
						"TEST_QUERY_PLAYER_INFO" => array(
									"request_code" => "014",
									"required_fields" => "username"
								),
						'TEST_BATCH_QUERY_PLAYER_BALANCE' => array(
									"request_code" => "015",
									"required_fields" => "usernames"
								),
						'TEST_GET_BET_LIMIT' => array(
									"request_code" => "016",
									"required_fields" => "username"
								),
						'TEST_UPDATE_BET_LIMIT' => array(
									"request_code" => "017",
									"required_fields" => "username,limit"
								),
						"API_Guidelines" => array(
									"request_code" => "docs",
									"required_fields" => "none"
								),
					);
		echo json_encode($response);
	}

	public function testAll() {

	 	/*$this->init();
	 	$this->testCreatePlayer();
	 	$this->testIsPlayerExist();
	   	$this->testChangePassword();
	    $this->testQueryPlayerBalance();
	    $this->testDeposit();
	   	$this->testWithdraw();
	 	$this->testQueryForwardGame();
	    $this->testBlockPlayer();
	 	$this->testUnblockPlayer();
	 	$this->testUnblockPlayer();
	    $this->testIsPlayerExist();
	    $this->testSyncGameLogs();
	    $this->testSyncMergeToGameLogs();
	    $this->testLogout();*/
	}

	/**
	 * Test Query Player Info
	 *  
	 * @return json $response
	*/
	private function testQueryPlayerInfo(){
		$username = $this->username;
		$response = array();
		
		if(empty($username)){
			$response = array(
				"Success"=>false,
				"Information"=>"Username is required"
			);
		}else{
			$api_response = $this->api->queryPlayerInfo($username);
			$response = array(
				"Success" => true,
				"Information"=>$api_response

			);
		}

		echo json_encode($response);
	}

	/**
	 * Test Multiple Player Balance
	 * testted in EBET API
	 * 
	 * @return json $response
	*/
	private function testBatchQueryPlayerBalance(){
		$usernames = $this->usernames;
		$response = array();

		if(! is_array($usernames) && empty($usernames)){
			$response = array(
				"Success" => false,
				"Information" => "Usernames is required and must be an array"
			);
		}else{
			$api_response = $this->api->batchQueryPlayerBalance($usernames);
			$response = array(
				"Success" => true,
				"Information"=>$api_response

			);
		}

		echo json_encode($response);
	}

	/**
	 * Test Get Bet Limit
	 * tested in  EBET API
	 * 
	 * @return json $response
	*/
	private function testGetBetLimit(){
		$username = $this->username;
		$response = array();

		if(empty($username)){
			$response = array(
				"Success" => false,
				"Information" => "Username is required"
			);
		}else{
			$api_response = $this->api->getBetLimit($username);
			$response = array(
				"Success" => true,
				"Information"=>$api_response

			);
		}

		echo json_encode($response);
	}

	/**
	 * Test Update Bet Limit
	 * tested in  EBET API
	 * 
	 * @return json $response
	*/
	private function testUpdateBetLimit(){
		$username = $this->username;
		$limit = $this->limit;
		$response = array();
		$params['limit'] = $limit;

		if(! is_array($limit) && empty($username)){
			$response = array(
				"Success" => false,
				"Information" => "Username is required and limit must an array"
			);
		}else{
			$api_response = $this->api->updateBetLimit($username,$params);
			$response = array(
				"Success" => true,
				'limit' => $limit,
				'params'=>$params,
				"Information"=>$api_response

			);
		}

		echo json_encode($response);
	}

	/**
	* Test Is Player Exist
	*
	* @return  json response
	*/
	private function testIsPlayerExist() {
		$username = $this->username;
		$extra = $this->extra;

		$response = array();

		if( empty($username) ) {
			$response = array(
					"Success" => False,
					"Information" => "Username is required"
				);
		} else {
			$rlt = $this->api->isPlayerExist($username, $extra);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	/**
	* Test Logout
	*
	* @return  json response
	*/
	private function testLogout() {

		$username = $this->username;

		if( empty($username) ) {
			$response = array(
					"Success" => False,
					"Information" => "Username is required"
				);
		} else {
			$rlt = $this->api->logout($username);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);

	}

	/**
	* Test Create Player
	*
	* @return  json response
	*/
	private function testCreatePlayer() {
		$username = $this->username;
		$password = $this->password;
		$playerId = $this->player_id;
		$extra = $this->extra;
		$email = $this->email;

		$response = array();

		if( empty($username) || empty($password) || empty($playerId)) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->createPlayer($username,$playerId,$password, $email, $extra);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	/**
	* Test Sync Game Logs
	*
	* @return  json response
	*/
	private function testSyncGameLogs() {
		$token = $this->token;
		$date_time_from = $this->date_time_from;
		$date_time_to = $this->date_time_to;

		/*$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-06-15 13:04:45');
		$dateTimeTo = new DateTime('2017-06-15 14:00:00');*/

		$response = array();

		// if( empty($token) || empty($date_time_from) || empty($date_time_to)) {
		if(empty($date_time_from) || empty($date_time_to)) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$dateTimeFrom = new DateTime($date_time_from);
			$dateTimeTo = new DateTime($date_time_to);
			$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
			$rlt = $this->api->syncOriginalGameLogs($token);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	private function testSyncMergeToGameLogs() {
		$token = $this->token;
		$date_time_from = $this->date_time_from;
		$date_time_to = $this->date_time_to;

		/*$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-06-15 13:04:45');
		$dateTimeTo = new DateTime('2017-06-15 14:00:00');*/

		$response = array();

		if( empty($token) || empty($date_time_from) || empty($date_time_to)) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$dateTimeFrom = new DateTime($date_time_from);
			$dateTimeTo = new DateTime($date_time_to);
			$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
			$rlt = $this->api->syncMergeToGameLogs($token);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	private function testChangePassword() {
		$username = $this->username;
		$newPassword = $this->new_password;
		$oldPassword = $this->old_password;

		$response = array();

		if( empty($username) || empty($newPassword) || empty($oldPassword) ){
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	/**
	* Test Deposit
	*
	* @return  json response
	*/
	private function testDeposit() {

	    $username = $this->username;
	    $depositAmount = $this->amount;

	    $response = array();

		if( empty($username) || empty($depositAmount) ) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->depositToGame($username, $depositAmount);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);

	}

	/**
	* Test Query Player Balance
	*
	* @return  json response
	*/
	public function testQueryPlayerBalance() {

		$username = $this->username;

		$response = array();

		if( empty($username)) {
			$response = array(
					"Success" => False,
					"Information" => "Username is required"
				);
		} else {
			$rlt = $this->api->queryPlayerBalance($username);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);

	}

	/**
	* Test Withdrawal
	*
	* @return  json response
	*/
	private function testWithdraw() {
		$username = $this->username;
	    $withdrawAmount = $this->amount;

		$response = array();

		if( empty($username) || empty($withdrawAmount)) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->withdrawFromGame($username, $withdrawAmount);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);

	}

	/**
	* Test Query Forward Game
	*
	* @return  json response
	*/
	private function testQueryForwardGame(){
     	$username = $this->username;
     	$extra = $this->extra;

     	$response = array();

		if( empty($username) ) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->queryForwardGame($username, $extra);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	/**
	* Test Block Player
	*
	* @return  json response
	*/
	private function testBlockPlayer(){
		$username = $this->username;

     	$response = array();

		if( empty($username) ) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {

			$rlt = $this->api->blockPlayer($username);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}

	/**
    * Test Unblock Player
    *
    * @return  json response
    */
    private function testUnblockPlayer(){
        $username = $this->username;

        $response = array();

        if( empty($username) ) {
            $response = array(
                    "Success" => False,
                    "Information" => "All Fields are required"
                );
        } else {
            $rlt = $this->api->unblockPlayer($username);
            $response = array(
                    "Success" => True,
                    "Information" => $rlt
                );
        }

        echo json_encode($response);
    }

    /**
	* Test Unblock Player
	*
	* @return  json response
	*/
	private function testQueryTransaction(){
        $username = $this->username;
		$external_transaction_id = $this->external_transaction_id;

     	$response = array();

		if( empty($username) ) {
			$response = array(
					"Success" => False,
					"Information" => "All Fields are required"
				);
		} else {
			$rlt = $this->api->queryTransaction($external_transaction_id,['player_username' => $username]);
			$response = array(
					"Success" => True,
					"Information" => $rlt
				);
		}

		echo json_encode($response);
	}



}