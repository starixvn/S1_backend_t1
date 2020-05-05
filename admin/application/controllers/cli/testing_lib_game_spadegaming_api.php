<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_spadegaming_api extends BaseTesting {

	private $platformCode = SPADE_GAMING_API;
	private $platformName = 'SPADE_GAMING_API';

	private $api = null;


	public function init() {

		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);


	}

	public function testAll() {

	 	$this->init();
	    //$this->testCreatePlayer();
	    //$this->testQueryPlayerBalance();

	    // $this->testLogin();
	    // $this->testIsPlayerExist();
	    //$this->testDeposit();
	  //   $this->testWithdraw();
	    // $this->testLogout();
	 	// $this->testChangePassword();
	    //$this->testIsPlayerExist();

	 	$this->testCallBack();
	    //$this->testQueryForwardGame();
	 	//$this->testBlockPlayer();
	 	//this->testUnblockPlayer();
	    //$this->testSyncGameLogs();
	  // $this->testSyncMergeToGameLogs();
	    // $this->testCreateMobilePlayer();

	
	}

    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

	private function testIsPlayerExist() {
	
		$username =  'testjerb06';
		$rlt = $this->api->isPlayerExist($username);
		echo "<pre>";print_r($rlt);exit;

	}

	private function testCreatePlayer() { 
		//$username = $this->existPlayer; 
		$username = 'testzai'; 
		$password = 'pass123'; 
		$player_id = '1';
		
		$rlt = $this->api->createPlayer($username,$player_id,$password);
		print_r($rlt);
		
	}

	private function testCreateMobilePlayer() { 
	
		//$username = $this->existPlayer; 
		$username = 'sbetestshu'; 
		
		$rlt = $this->api->createMobilePlayer($username);
		echo "<pre>";print_r($rlt);exit;
		
	}

	private function testLogin() {

		$username = 'testshu123213123';
		//$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->login($username);
		
		echo "<pre>";print_r($rlt);exit;
	}

	private function testLogout() {

		$username = 'testshu';

		$rlt = $this->api->logout($username);
		
		echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncGameLogs() {

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-06-08 01:00:00');
		$dateTimeTo = new DateTime('2017-06-08 23:59:59');
		
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		
		// echo "<pre>";print_r($rlt);exit;
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-06-08 01:00:00');
		$dateTimeTo = new DateTime('2017-06-08 23:59:59');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		// echo "<pre>";print_r($rlt);exit;
	}

	private function testChangePassword() {
		$username = 'testshu'; 
		//$oldPassword = $this->password;
		$oldPassword = 'garry1';
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
     	$username =  'testjerb06';
     	$fun  = 'true';
     	$game_code = 'S-DG03';
     	$extra = array(
				'game' => $game_code,
				'fun' => $fun,
		);
		$rlt = $this->api->queryForwardGame($username,$extra);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testBlockPlayer(){
		$username =  'testkeir';
		$rlt = $this->api->blockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testUnblockPlayer(){
		$username =  'testkeir';
		$rlt = $this->api->unblockPlayer($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testCallBack(){
		$result = array(
			"acctId" => "T1testjerb06",
			"language"	=> "en_US",
			"merchantCode" => "OKADA",
			"token"			=> "7518fd7cc7c891ed186638e2964d5eda",
			"serialNo"		=> "20120722224255982841",
		);
		$rlt = $this->api->callback($result,'mobile');
		echo "<pre>";print_r($rlt);exit;
	}


}