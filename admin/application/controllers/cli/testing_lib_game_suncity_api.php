<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_suncity_api extends BaseTesting {

	private $platformCode = SUNCITY_API;
	private $platformName = 'SUNCITY_API';

	private $api = null;


	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {

	 	$this->init();
	   	//$this->testCreatePlayer();
	 	//$this->testLogin();
	    //$this->testQueryPlayerBalance();
        //$this->testIsPlayerExist();
	    //$this->testDeposit();
        //$this->testWithdraw();
        // $this->testQueryForwardGame();
	    // $this->testSyncGameLogs();
	    $this->testSyncMergeToGameLogs();
	    // $this->testLogout();
	}

	private function testLogin() {
	
		$username =  'testdan1';
		$rlt = $this->api->login($username,null,array('language'=>'en-US'));
		echo "<pre>";print_r($rlt);exit;

	}

	private function testIsPlayerExist() {
	
		$username =  'testdan1';
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testLogout() {
	
		$username =  'testshu';
		$rlt = $this->api->logout($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() { 
		
		//$username = $this->existPlayer; 
		$username = 'testshu'; 
		$password = 'password';
		$playerId = '56963';
		$rlt = $this->api->createPlayer($username,$playerId,$password);
		echo "<pre>";print_r($rlt);exit;
	}


	public function testQueryPlayerBalance() {

		$playerName = 'testdan1';
		$rlt = $this->api->queryPlayerBalance($playerName);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testSyncGameLogs() {

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2018-02-07 00:00:00');
		$dateTimeTo = new DateTime('2018-02-08 18:30:00');
		
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		
		// echo "<pre>";print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2018-02-07 00:00:00');
		$dateTimeTo = new DateTime('2018-02-08 18:30:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);
	}

	private function testChangePassword() {
		$username = 'testgar'; 
		//$oldPassword = $this->password;
		$oldPassword = 'password';
		$newPassword = 'password1';

		$rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
		echo "<pre>";print_r($rlt);exit;
	}


	private function testDeposit() { 

	    $playerName = 'testdan1';
	    $depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testWithdraw() {
		$playerName = 'testdan1';
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testQueryForwardGame(){ 
     	$username =  'testdan1';
		$rlt = $this->api->queryForwardGame($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testBlockPlayer(){
		$username =  'testgar';
		$rlt = $this->api->blockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testUnblockPlayer(){
		$username =  'testgar';
		$rlt = $this->api->unblockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}



}