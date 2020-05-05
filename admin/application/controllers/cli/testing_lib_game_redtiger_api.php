<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_redtiger_api extends BaseTesting {

	private $platformCode = REDTIGER_API;
	private $platformName = 'REDTIGER_API';

	private $api = null;


	public function init() {

		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {

	 	$this->init();
	   	// $this->testGetAvailableApiToken();
	   	// $this->testCreatePlayer();
	    // $this->testIsPlayerExist();
	    // $this->testDeposit();
	    // $this->testWithdraw();
	    // $this->testQueryPlayerBalance();
	    // $this->testDeposit();
	    // $this->testQueryForwardgame();
	    // $this->testGetGameList();
	    // $this->testSyncGameLogs();
	    $this->testSyncMergeToGameLogs();
	 	// $this->testBlockPlayer();
	 	// $this->testUnblockPlayer();
	}

	public function testGetGameList() {
		$username = 'osdemo1';
		$rlt = $this->api->getGameList();
		echo "<pre>";print_r($rlt);exit;

	}

	public function testLogin() {
		$username = 'osdemo';
		$rlt = $this->api->createPlayer($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() {
		$username = 'osdemo1'; 
		$password = '123456';
		$playerId = '1005';
		$rlt = $this->api->createPlayer($username,$playerId,$password);
		echo "<pre>";print_r($rlt);
	}

	public function testQueryPlayerBalance() {
		$username = 'osdemo1'; 
		$rlt = $this->api->queryPlayerBalance($username);
		echo "<pre>";print_r($rlt);
	}

	public function testQueryForwardgame() {
		$username = 'osdemo1';
		$extra['game_code'] = "Lion_Dance";
		$extra['game_type'] = "TGP";
		// $extra['game_mode'] = "demo";
		$extra['language'] = "en-US";
		$extra['is_mobile'] = true;
		$extra['extra'] = "3";
		$rlt = $this->api->queryForwardgame($username,$extra);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testGetAvailableApiToken() {
		$rlt = $this->api->getAvailableApiToken();
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncMergeToGameLogs() {
		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2019-06-16 00:00:00');
		$dateTimeTo = new DateTime('2019-06-18 23:59:59');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncGameLogs() {
		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2019-06-17 19:00:00');
		$dateTimeTo = new DateTime('2019-06-17 19:59:59');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);

		echo "<pre>";print_r($rlt);exit;
	}

	private function testDeposit() {
	    $playerName = 'osdemo1';
	    $depositAmount = 100;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		echo "<pre>";print_r($rlt);
	}

	private function testWithdraw() {
		$playerName = 'osdemo1';
		$withdrawAmount = 500000;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		echo "<pre>";print_r($rlt);

	}
}