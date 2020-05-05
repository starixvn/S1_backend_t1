<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_agshaba_api extends BaseTesting {

	private $platformCode = AGSHABA_API;

	public function init() {
		$this->load->model('game_provider_auth');
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		// $this->test($this->game_platform_manager == null, false, 'init game platform manager');
		$api = $this->game_platform_manager->initApi($this->platformCode);
		$this->api=$api;
		// $this->test($api == null, false, 'init api');
		$this->test($api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);

	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	public function testAll() {
		$this->init();
		 //$this->testCreatePlayer();
		//$this->testDepositToAGHABA();
		//$this->testPlayerBalanceAGHABA();
		//$this->testWithdrawalToAGHABA();
		//$this->blockPlayer();
		//$this->unblockPlayer();
		//$this->queryGameRecords();
	    // $this->testSyncGameLogs();
		$this->testSyncMergeToGameLogs();
		//$this->testIsPlayerExist();
		// $this->testQueryForwardGame();
	}

	private function testCreatePlayer() {
		$username = 'test_' . random_string('alnum');
		$password = '123456';
		$playerId = 1;
		// $player = array('id' => 1, 'username' => $username,
		// 	'password' => '123456', 'source' => Game_provider_auth::SOURCE_REGISTER);
		// $this->game_provider_auth->savePasswordForPlayer($player, $this->platformCode);

		$result = $this->game_platform_manager->createPlayer($username, $playerId, $password);
		$this->test($result['success'], true, 'test create player for AGHABA');
		// log_message('error', 'get password:' . $password);
		// $this->test($password, $player['password'], 'test create player for AGHABA');
	}

	private function testDepositToAGHABA() {
		$playerName = 'asriinew2';
		$balResult = $this->game_platform_manager->depositToGame($playerName, 5);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['success'], true, 'Test Player Deposit to AGHABA');
	}

	private function testWithdrawalToAGHABA() {
		$playerName = 'asriinew2';
		$balResult = $this->game_platform_manager->withdrawFromGame($playerName, 5);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['success'], true, 'Test Player Withdrawal to AGHABA');
	}

	private function testPlayerBalanceAGHABA() {
		$playerName = 'asriinew2';
		$balResult = $this->game_platform_manager->queryPlayerBalance($playerName);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['balance'] > 0, true, 'Test Player Balance');
	}

	private function blockPlayer() {
		$playerName = 'asriinew2';
		$balResult = $this->game_platform_manager->blockPlayer($playerName);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['success'], true, 'Test Block Player');
	}

	private function unblockPlayer() {
		$playerName = 'asriinew2';
		$balResult = $this->game_platform_manager->unblockPlayer($playerName);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['success'], true, 'Test Unblock Player');
	}

	private function queryGameRecords() {
		$playerName = 'asriinew2';
		$dateFrom = date_create('2015-07-09 00:00:00');
		$dateTo = date_create('2015-07-10 00:00:00');
		$balResult = $this->game_platform_manager->queryGameRecords($dateFrom, $dateTo, $playerName);
		log_message("error", var_export($balResult, true));
		$this->test($balResult['success'], true, 'Test Game Records Results');
		$this->test(is_array($balResult['gameRecords']), true, 'Test Game Records');
	}

	private function testSyncGameLogs() { 
		// echo 1;
		$api = $this->game_platform_manager->initApi($this->platformCode);

		$token = 'abc123';
		// $dateTimeFrom = new DateTime('2017-11-03 00:00:00');
  //       $dateTimeTo = new DateTime('2017-11-04 23:59:59');
        $dateTimeFrom = new DateTime('2017-12-18 00:00:00');
		$dateTimeTo = new DateTime('2017-12-18 23:59:59');
		//$playerName = 'asriinew2';
		$playerName = null;
		$gameName = '';

		$api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo, "playerName" => $playerName, "gameName" => $gameName);
		$rlt = $this->api->syncOriginalGameLogs($token);
		echo"<pre>";
		print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {
		$api = $this->game_platform_manager->initApi($this->platformCode);

		$token = 'abc123';
		$dateTimeFrom = new DateTime('2017-12-18 00:00:00');
		$dateTimeTo = new DateTime('2017-12-18 23:59:59');

		$playerName = 'asriinew2';
		$gameName = '';

		$api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo, "playerName" => $playerName, "gameName" => $gameName);
		// $api->syncMergeToGameLogs($token);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo"<pre>";
		print_r($rlt);
	}
	private function testIsPlayerExist() {
		$rlt = $this->game_platform_manager->isPlayerExist("johann");
		$this->test($rlt['success'], true, 'testIsPlayerExist for AGHABA');
		$this->test($rlt['exists'], true, 'exist!');
		//try a palyer not exist
		// $rlt = $this->game_platform_manager->isPlayerExist("lalalala");
		// log_message('error', 'rlt:' . var_export($rlt, true));
		// $this->test($rlt['success'], true, 'testIsPlayerExist for AG');
		// $this->test($rlt['exists'], false, 'lalalala not exist');
	}

	private function testCallback(){
		$xml=<<<EOD
<?xml version="1.0" encoding="utf-8"?><request action="userverf"><element id="20160817204619256"><properties name="pcode">L14</properties><properties name="gcode">L14300</properties><properties name="userid">wbttestj</properties><properties name="password">qwer123456qwerty</properties><properties name="token">14abfd2bdf7546e11e2854725823e7e8</properties><properties name="cagent">1199A2C43F58C9ACB00E896DAB4CAC11</properties></element></request>
EOD;

		$api=$this->api;

		$this->utils->debug_log($api->callback($xml));
	}


	private function testQueryForwardGame(){
     $username =  'g90testv8';

   //$extra =  array('game_lang' =>'en-us' , 'game_code' => 'RNG4583');
	$rlt = $this->api->queryForwardGame($username, $extra);
	// var_dump($rlt);
	//redirect($rlt[1]['url']);
	}


}
