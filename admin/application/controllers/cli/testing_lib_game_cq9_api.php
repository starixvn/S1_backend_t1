<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_cq9_api extends BaseTesting {

	private $platformCode = CQ9_API;
	private $platformName = 'CQ9_API';

	private $api = null;


	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {
	 	$this->init();
	   	// $this->testCreatePlayer();
	    // $this->testQueryPlayerBalance();
        // $this->testIsPlayerExist();
	    // $this->testDeposit();
        // $this->testWithdraw();
        // $this->testQueryTransaction();
        // $this->testChangePassword();
	 	// $this->testLogin();
	 	// $this->testLogout();
	 	// $this->testGetGameProviderList();
	 	// $this->testGetGameList();
        // $this->testBlockPlayer();
        // $this->testUnBlockPlayer();
	    // $this->testSyncGameLogs();
	    $this->testSyncMergeToGameLogs();
	}

	private function testGetGameList(){
		$gameProviders = $this->api->getGameProviderList();
		foreach ($gameProviders["providers"] as $value) {
			$rlt[$value['gamehall']] = $this->api->getCq9GameList($value['gamehall']);
		}
		
		echo "<pre>";print_r($rlt);exit;
	}

	private function testGetGameProviderList(){
		$rlt = $this->api->getGameProviderList();
		echo "<pre>";print_r($rlt);exit;
	}

	private function testQueryTransaction() {
	
		$transactionId =  't1testshu201806041900365b151bd4c7293';
		$username =  'testshu';
		$playerId = '56962';

		$extra = array(
			'playerName' => $username,
			'playerId' => $playerId,
		);

		$rlt = $this->api->queryTransaction($transactionId,$extra);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testLogin() {
	
		$username =  'testshu';
		$password =  'password';
		$rlt = $this->api->login($username,$password);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testBlock() {
	
		$username =  'testshu';
		$rlt = $this->api->login($username,null,array('language'=>'en-US'));
		echo "<pre>";print_r($rlt);exit;

	}

	private function testIsPlayerExist() {
	
		$username =  'testshu';
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testLogout() {
	
		$username =  'testshu';
		$rlt = $this->api->logout($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() { 
		$username = 'testshu'; 
		$password = 'password';
		$playerId = '56962';
		$rlt = $this->api->createPlayer($username,$playerId,$password);
		echo "<pre>";print_r($rlt);exit;
	}


	public function testQueryPlayerBalance() {

		$playerName = 'testshu';
		$rlt = $this->api->queryPlayerBalance($playerName);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testSyncGameLogs() {

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2018-06-04 23:00:00');
		$dateTimeTo = new DateTime('2018-06-05 18:00:00');
		
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		
		echo "<pre>";print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2018-06-04 23:00:00');
		$dateTimeTo = new DateTime('2018-06-05 18:00:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);
	}

	private function testChangePassword() {
		$username = 'testshu'; 
		$oldPassword = 'password';
		$newPassword = 'password';

		$rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
		echo "<pre>";print_r($rlt);exit;
	}


	private function testDeposit() { 

	    $playerName = 'testshu';
	    $depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testWithdraw() {
		$playerName = 'testshu';
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