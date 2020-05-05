<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_sync_player_statistics extends BaseTesting {

	private $platformCode = PT_API;

	public function init() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		// $this->test($this->game_platform_manager == null, false, 'init game platform manager');
		$api = $this->game_platform_manager->initApi($this->platformCode);
		// $this->test($api == null, false, 'init api');
		$this->test($api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
	}

	public function testAll() {
		$this->init();
		// $this->syncTotalStats();
		// $this->testSyncBalance();
		$this->testSyncPlayerInfo();
	}

	private function testSyncPlayerInfo() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		$api = $this->game_platform_manager->initApi($this->platformCode);

		$token = 'abc123';
		$dateTimeFrom = new DateTime('2015-04-01');
		$dateTimeTo = new DateTime('2015-07-30');
		$api->syncInfo[$token] = array("playerName" => null, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$api->syncPlayerInfo($token);
	}

	private function syncTotalStats() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		$api = $this->game_platform_manager->initApi($this->platformCode);

		$token = 'abc123';
		$dateTimeFrom = new DateTime('2015-04-01');
		$dateTimeTo = new DateTime('2015-07-30');
		$player = $this->getFirstPlayer();
		$api->syncInfo[$token] = array("playerName" => null, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$api->syncTotalStats($token);
	}

	// private function testSyncBalance() {
	// 	$api = $this->game_platform_manager->initApi($this->platformCode);
	// 	$dateTimeFrom = new DateTime('2015-07-01 00:00:00');
	// 	$dateTimeTo = new DateTime('2015-07-30 23:59:59');
	// 	$playerName = null;

	// 	$token = random_string('unique');
	// 	$api->clearSyncInfo($token);
	// 	$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName);
	// 	// $api->syncInfo[$token] = array("playerName" => null, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
	// 	$api->syncDailyBalance($token);
	// }

}
