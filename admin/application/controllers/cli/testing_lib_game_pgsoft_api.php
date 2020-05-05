<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_pgsoft_api extends BaseTesting {

	private $platformCode = PGSOFT_API;
	private $platformName = 'PGSOFT_API';

	private $api = null;


	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {
	 	$this->init();
	   	// $this->testCreatePlayer();
	    // $this->testQueryPlayerBalance();
	    // $this->testQueryTransaction();
	    // $this->testLogout();
        // $this->testIsPlayerExist();
        // $this->testBlockPlayer();
        // $this->testUnBlockPlayer();
        // $this->testChangePassword();
	    // $this->testDeposit();
        // $this->testWithdraw();
	 	// $this->testLogin();
	    // $this->testSyncGameLogs();
	    // $this->testSyncMergeToGameLogs();

	}

	private function testLogin() {
	
		$username =  'testshu';
		$rlt = $this->api->login($username,null,array('language'=>'en-US'));
		echo "<pre>";print_r($rlt);exit;

	}

	private function testBlock() {
	
		$playerName = 'testlocal';
		$rlt = $this->api->login($username,null,array('language'=>'en-US'));
		echo "<pre>";print_r($rlt);exit;

	}

	private function testIsPlayerExist() {
	
		$username =  'testlocal';
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testLogout() {
	
		$playerName = 'testlocal';
		$rlt = $this->api->logout($playerName);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() { 
		
		//$username = $this->existPlayer; 
		$playerName = 'testlocal'; 
		$password = 'password';
		$playerId = '56962';
		$rlt = $this->api->createPlayer($username,$playerId,$password);
		echo "<pre>";print_r($rlt);exit;
	}

	public function testQueryPlayerBalance() {
		$playerName = 'testlocal';
		$rlt = $this->api->queryPlayerBalance($playerName);
		echo "<pre>";print_r($rlt);exit;
	}

	public function testQueryTransaction() {
		$externalId = 'T122454529565';
		$extra = ['playerName'=>'testlocal','playerId'=>57024];
		$rlt = $this->api->queryTransaction($externalId,$extra);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncGameLogs() {

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2018-03-15 00:00:00');
		$dateTimeTo = new DateTime('2018-03-15 23:30:00');
		
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		
		echo "<pre>";print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2018-07-18 00:00:00');
		$dateTimeTo = new DateTime('2018-07-19 23:30:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);
	}

	private function testChangePassword() {
		$playerName = 'testlocal'; 
		//$oldPassword = $this->password;
		$oldPassword = 'password';
		$newPassword = 'password';

		$rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
		echo "<pre>";print_r($rlt);exit;
	}


	private function testDeposit() { 

	    $playerName = 'testlocal';
	    $depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testWithdraw() {
		$playerName = 'testlocal';
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		echo "<pre>";print_r($rlt);exit;
		
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



}