<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_pragmaticplay_api extends BaseTesting {

	private $platformCode = PRAGMATICPLAY_API;
	private $platformName = 'PRAGMATICPLAY_API';

	private $api = null;


	public function init() {

		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
		

	}

	public function testAll() {

	 	$this->init();
	   	// $this->testCreatePlayer();
	    // $this->testQueryPlayerBalance();
	    // $this->testDeposit();
	    // $this->testWithdraw();
	    //$this->testIsPlayerExist();
	    //$this->testQueryForwardGame();
	 	// $this->testChangePassword();
	 	//$this->testBlockPlayer();
	 	//$this->testUnblockPlayer();
	    // $this->testIsPlayerExist(); 
	    //$this->testSyncGameLogs();
	    //$this->testSyncMergeToGameLogs();
	     // $this->testLogout();
	 	$this->testGetGamelist();
	
	
	}

	public function testTarget($methodName)
	{
		$this->init();
		$this->$methodName();
	}

	private function testIsPlayerExist() {
	
		$username =  'testshu';
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testLogout() {
	
		$username =  'testshu1';
		$rlt = $this->api->logout($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() { 
		$username = 'testzai';
		$password = 'pass123';
		$playerId = 1;

		$rlt = $this->api->createPlayer($username,$playerId,$password);
		print_r($rlt);
	}

	private function testSyncGameLogs() {

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-06-15 13:04:45');
		$dateTimeTo = new DateTime('2017-06-15 14:00:00');
		
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		
		echo "<pre>";print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2017-06-15 13:04:45');
		$dateTimeTo = new DateTime('2017-06-15 14:00:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);
	}

	private function testChangePassword() {
		$username = 'testshu1'; 
		//$oldPassword = $this->password;
		$oldPassword = 'password1';
		$newPassword = 'password';

		$rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
		echo "<pre>";print_r($rlt);exit;
	}


	private function testDeposit() { 

	    $playerName = 'testzai';
	    $depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		print_r($rlt);
		
	}

	public function testQueryPlayerBalance() {

		$playerName = 'testzai';
		$rlt = $this->api->queryPlayerBalance($playerName);
		print_r($rlt);
		
	}

	private function testWithdraw() {
		$playerName = 'testzai';
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		print_r($rlt);
		
	}

	private function testQueryForwardGame(){ 
     	$username =  'testshu';
		$rlt = $this->api->queryForwardGame($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testBlockPlayer(){
		$username =  'testshu';
		$rlt = $this->api->blockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testUnblockPlayer(){
		$username =  'testshu';
		$rlt = $this->api->unblockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testGetGamelist() {
		$rlt = $this->api->getGameProviderGameList();
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, ' testBatchQueryPlayerBalance for ' . $this->platformName);
	}
	
}