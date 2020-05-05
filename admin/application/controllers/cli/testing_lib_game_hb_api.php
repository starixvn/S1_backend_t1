<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_hb_api extends BaseTesting {

	
	private $platformCode = HB_API;
	private $playerName = 'testtest';
	private $password = 'password';
	private $api;
	
	public function init() {
		$this->load->model('game_provider_auth');
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);

		$this->test($this->api == null, false, 'init api');
		$this->test($this->api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
	}

	public function testAll() {
		$this->init();
		$this->testGetJackpots();
		//$this->testCreatePlayer();
		//$this->testChangePassword();
		//$this->testQueryBalance();
		//$this->testDepositPlayerMoney();
		//$this->testWithdraw())
		//$this->testqueryTransaction();
		//$this->logout();
		//$this->queryGameRecords();
		//$this->queryForwardGame();
		// $this->testSyncOriginalGameLogs();
		//$this->testSyncMergeToGameLogs();
		//$this->testQueryPlayerInfo();
	}

	private function testGetJackpots(){
		return $this->api->getJackpots();
	}

	private function testCreatePlayer(){
		$username = $this->playerName;
		$this->utils->debug_log("Login player", $username);
		$password = $this->password;
		$player = $this->getFirstPlayer($username);
		$rlt = $this->api->createPlayer($username, $player->playerId, $password, null);
		return $rlt;
	}

	private function testDepositPlayerMoney() {
		$username = $this->playerName;
		$depositAmount = 1;
		$resultBal = $this->testQueryBalance();
		$curr = $resultBal['balance'];
		$this->api->depositToGame($username, $depositAmount);
		$this->test($rlt['success'], true, 'Test Player Deposit to HB');
		$this->test($rlt['currentplayerbalance'], $depositAmount, 'Current Balance after deposit is '.$curr);
		return $rlt;
	}

	private function testWithdraw() {
		$playerName = $this->playerName;
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		$resultBal = $this->testQueryBalance();
		$curr = $resultBal['balance'];
		$this->test($rlt['success'], true, 'Test Player Withdrawal to HB');
		$this->test($rlt['currentplayerbalance'], $amount, 'Current Balance after withdrawal is '.$curr);
		return $rlt;
	}

	private function testChangePassword() {
		$playerName = $this->playerName;
		//$oldPassword = $this->password;
		$oldPassword = 'password';
		$newPassword = 'password';

		$rlt = $this->api->changePassword($playerName, $oldPassword, $newPassword);
		$this->test($rlt['success'], true, 'Player '.$playerName.' successfully changed password!');
		return $rlt;
	}

	private function testQueryBalance() {

		$rlt = $this->api->queryPlayerBalance($this->playerName);
		$this->utils->debug_log('query balance', $rlt);
		return $rlt;
	}

	private function testqueryTransaction() {
		$transactionId ="asd";
		$rlt = $this->api->queryTransaction($transactionId,'');
		$this->test($rlt['success'], true, 'balance');
		return $rlt;
	}

	private function logout() {
		$username = $this->playerName;
		$password = $this->password;
		$rlt = $this->api->logout($username,$password);
		return $rlt;
	}

	private function queryGameRecords() {
		$username = $this->playerName;
		$from=strtotime("12:00am April 01 2016");
		$to=strtotime("11:59pm April 18 2016");
		$dateTimeFrom = date("Ymdhis", $from);
		$dateTimeTo = date("Ymdhis",$to);

		$rlt = $this->api->queryGameRecords($dateTimeFrom, $dateTimeTo, $username);
		return $rlt;
	}

	private function queryForwardGame(){
		$username = $this->playerName;
		$rlt = $this->api->queryForwardGame($username, null);
	}

	private function testSyncOriginalGameLogs() {

		$token = 'testlocaldan1';
        $dateTimeFrom = new DateTime('2018-03-13 00:00:00');
        $dateTimeTo = new DateTime('2018-03-13 23:59:00');

        $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncOriginalGameLogs($token);
        echo "<pre>";print_r($rlt);
	}

	private function testSyncMergeToGameLogs() {

		/*$token 			= 'abc123';
		$from 	= strtotime("12:00am April 01 2016");
		$to 	= strtotime("11:59pm April 26 2016");
		$dateTimeFrom = date("Y-m-d H:i:s", $from); //follow this format
		$dateTimeTo = date("Y-m-d H:i:s",$to); //follow this format*/
        $token = 'testlocaldan1';
        $dateTimeFrom = new DateTime('2018-03-13 00:00:00');
        $dateTimeTo = new DateTime('2018-03-13 23:59:00');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		$this->utils->debug_log('syncMergeToGameLogs', $rlt);
		$this->test($rlt['success'], true, 'Test Sync Merge Game Logs');
		return $rlt;
	}

	public function testQueryPlayerInfo() {
		$rlt = $this->api->queryPlayerInfo($this->playerName);
		$this->utils->debug_log('query player info', $rlt);
		return $rlt;
	}
}