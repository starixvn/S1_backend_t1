<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_all_game_api extends BaseTesting {

	// private $platformCode = MG_API;
	private $api = null;

	// private $existPlayer = 'testmg18317385';

	public function init() {
		// $this->load->model('game_provider_auth');
		// $this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		// $this->test($this->game_platform_manager == null, false, 'init game platform manager');
		// $this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
		// $this->test($api == null, false, 'init api');
		//var_dump($this->platformCode);
		// $this->test($this->api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
		// load all api

	}

	public function testAll() {
		$this->init();
		// $this->testQueryPlayerBalance();
		// $this->testCreatePlayer();
		// $this->testQueryPlayerInfo();
		// $this->testDeposit();
		// $this->testWithdraw();
		// $this->testIsPlayerExist();
		//$this->testSyncGameLogs();
		// $this->testSyncMergeToGameLogs();
		// $this->testPrefixUsername();
		// $this->testGetPrepareData();
	}

	// public function testGetPrepareData() {
	// 	$rlt = $this->api->getPrepareData();
	// 	$this->utils->debug_log($rlt);
	// 	var_export($rlt);

	// }

	private function testRequiredAPI() {
		$username = 'testmg' . random_string('numeric');
		$this->utils->debug_log("create player", $username);
		$password = 'pass123';
		$player = $this->getFirstPlayer($username);

		$rlt = $this->api->createPlayer($username, $player->playerId, $password, null);

		$this->utils->debug_log($rlt);

		$this->test($rlt['success'], true, 'create player: ' . $username);

		$rlt = $this->api->isPlayerExist($username);
		log_message('error', 'query player if exists: ' . var_export($rlt, true));

		$this->test($rlt['success'], true, 'player ' . $username . ' already exists');
		$this->test($rlt['exists'], true, 'player ' . $username . ' already exists');
	}

}