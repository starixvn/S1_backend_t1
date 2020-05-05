<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_mgplus_api extends BaseTesting {

	private $platformCode = MGPLUS_API;
	private $platformName = 'MGPLUS_API';

	private $api = null;


	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {

	 	$this->init();
	   	// $this->testGetAvailableApiToken();
	   	// $this->testCreatePlayer();
	    // $this->testQueryPlayerBalance();
	    // $this->testIsPlayerExist();
	    // $this->testDeposit();
	    // $this->testWithdraw();
	    // $this->testQueryTransactions();
	    // $this->testSyncGameLogs();
	    // $this->testSyncMergeToGameLogs();
	 	// $this->testBlockPlayer();
	 	// $this->testUnblockPlayer();
	}

	private function testCreatePlayer() {
		$username = 'testdevlocal'; 
		$password = 'password';
		$playerId = '57000';
		$rlt = $this->api->createPlayer($username,$playerId,$password);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testGetAvailableApiToken() {
		$rlt = $this->api->getAvailableApiToken();
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2019-02-21 16:00:00');
		$dateTimeTo = new DateTime('2019-02-21 18:00:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncGameLogs() {
		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2019-02-21 16:00:00');
		$dateTimeTo = new DateTime('2019-02-21 18:00:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);

		echo "<pre>";print_r($rlt);exit;
	}
	
	private function testQueryTransactions(){
		$transactionId = '03E5121F01563F0B00010000000000BB79DF';
		$extra = array(
			'playerName' => 'testdevlocal',
		    'playerId' => '57000',
		);	

		$rlt = $this->api->queryTransaction($transactionId, $extra);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testIsPlayerExist() {
		$username = 'testdevlocal'; 
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;
	}

	public function testQueryPlayerBalance() {
		$username = 'testdevlocal'; 
		$rlt = $this->api->queryPlayerBalance($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testDeposit() {
	    $playerName = 'testdevlocal';
	    $depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testWithdraw() {
		$playerName = 'testdevlocal';
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testBlockPlayer(){
		$username =  'testdevlocal';
		$rlt = $this->api->blockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testUnblockPlayer(){
		$username =  'testdevlocal';
		$rlt = $this->api->unblockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}



}