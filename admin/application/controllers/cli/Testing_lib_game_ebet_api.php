<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_ebet_api extends BaseTesting {

	private $platformCode = EBET_API;
	private $platformName = 'EBET';
	private $api = null;

	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
	}

	public function testAll() {
		$this->init();
		$this->testQueryForwardGame();
		// $this->testCreatePlayer();
		// $this->testQueryPlayerBalance();
		// $this->testDeposit();
		// $this->testWithdraw();
		// $this->testPlayGame();
		// $this->testSyncGameLogs();
		// $this->testSyncMergeToGameLogs();
	}

	private function testBase() {
		$username = 'test1sgame' . random_string('numeric');
		$this->utils->debug_log("create player", $username);
		$password = 'pass123';
		$player = $this->getFirstPlayer($username);

		$rlt = $this->api->createPlayer($username, $player->playerId, $password, null);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'Create Player: ' . $username);

		$depositAmount = 10;
		$rlt = $this->api->depositToGame($username, $depositAmount);
		$this->utils->debug_log('depositToGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Deposit to 1sGames');

		$rlt = $this->api->queryPlayerBalance($username);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'balance for ' . $username);
		$this->test($rlt['balance'], 30, 'balance amount ' . $rlt['balance'] . ' for ' . $username);

		$withdrawAmount = 10;
		$rlt = $this->api->withdrawFromGame($username, $withdrawAmount);
		$this->utils->debug_log('withdrawToGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Withdraw to 1sGames');

		$rlt = $this->api->queryPlayerBalance($username);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'balance for ' . $username);
		$this->test($rlt['balance'], 30, 'balance amount ' . $rlt['balance'] . ' for ' . $username);
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testBatchQueryPlayerBalance() {
		$rlt = $this->api->batchQueryPlayerBalance(null);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, ' testBatchQueryPlayerBalance for ' . $this->platformName);
	}

	public function testPrefixUsername() {
		$api = $this->utils->loadExternalSystemLibObject($this->platformCode);
		$this->test($api->convertUsernameToGame('testuser'), 'ogtestuser', 'convert username');
	}

	private function testCreatePlayer() {
		$username = 'testopus' . random_string('numeric');
		$password = '123123';
		$this->player = $this->getFirstPlayer($username);
		$rlt = $this->api->createPlayer($this->player->username, $this->player->playerId, $password, null);
		var_dump($rlt);
	}

	public function testQueryPlayerBalance() {
		$rlt = $this->api->queryPlayerBalance($this->player->username);
		var_dump($rlt);
	}

	private function testDeposit() {
		$depositAmount = 1;
		$rlt = $this->api->depositToGame($this->player->username, $depositAmount);
		var_dump($rlt);
	}

	private function testWithdraw() {
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($this->player->username, $withdrawAmount);
		var_dump($rlt);
	}

	private function testSyncGameLogs() {
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2016-10-01 00:00:00');
		$dateTimeTo = new DateTime('2016-11-02 23:59:59');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		var_dump($rlt);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2016-03-22 00:00:00');
		$dateTimeTo = new DateTime('2016-03-26 23:59:59');

		$playerName = 'wiplixt';

		$this->api->syncInfo[$token] = array("playerName" => $playerName, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		$this->test($rlt['success'], true, 'Test syncMergeToGameLogs to Opus');
	}

	private function testQueryForwardGame() {
		$playerName = 'testshu';
		$password = '123123';
		$extra = array('password' => $password, 'gameType' => $gameType, 'gameName' => $gameName);
		$rlt = $this->api->queryForwardGame($playerName, $extra);
		var_dump($rlt);
	}

	public function testPlayGame() {
		$extra = array();
		$rlt = $this->api->queryForwardGame($this->player->username, $extra);
		var_dump($rlt);
	}

	public function testRaw($post_data = null, $url = null) {
		// $url = "https://club8api.bet8uat.com/op/createuser?merch_id=GSOFT05&merch_pwd=53AAAC24-CFE5-4842-B43E-BFEEA057C675&cust_id=testrtt01&cust_name=testrtt01&currency=CNY";
		// $url = "http://110.92.26.200/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=RTT&product_code=mcasino&member_id=ogtestopus12295222&language=zh-cn&currency=rmb";
		// $url = "http://mcasino.rtt1889.com/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=RTT&product_code=mcasino&member_id=ogtestopus12295222";
		//$url = "http://api.mansion888.net/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=RTT&product_code=mcasino&member_id=ogtestopus12295222";

		// $url = "http://api.uat01.mansion888.com/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=rtt&product_code=mcasino&member_id=opusp1_rtt&language=en-us&currency=rmb";
		// $url = "http://api.mansion888.com/CreateMember.API?secret_key=862B05FA0F62&operator_id=a&site_code=rtt&product_code=mcasino&member_id=opusp1_rtt&language=en-US&currency=RMB";
		// $url = "http://api.mansion888.net/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=rtt&product_code=mcasino&member_id=opusp1_rtt&language=en-us&currency=rmb";
		// $url = "http://api.mansion888.net/CreateMember.API?secret_key=862B05FA0F62&operator_id=CA8F608C-A278-4846-8193-6A5195DAD1C6&site_code=RTT&product_code=mcasino&member_id=97_opusp1_rtt&language=en-US&currency=RMB";
		// var_dump($url);
		// $ch = curl_init($url);

		// curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// // curl_setopt($ch, CURLOPT_POST, 1);
		// // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		// $result = curl_exec($ch);
		// $errno = curl_errno($ch);
		// $error = curl_error($ch);
		// var_dump($result, $errno, $error);
		// $this->utils->debug_log('res:' . $result . 'errno:' . $errno . 'error:' . $error);
		// curl_close($ch);
	}
}