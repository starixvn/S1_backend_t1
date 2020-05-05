<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_sync_lost_and_found extends BaseTesting {

	private $platformCode = AG_API;

	public function init() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		// $this->test($this->game_platform_manager == null, false, 'init game platform manager');
		$api = $this->game_platform_manager->initApi($this->platformCode);
		// $this->test($api == null, false, 'init api');
		$this->test($api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
	}

	public function testAll() {
		$this->testSyncLostAndFound();
	}

	private function testSyncLostAndFound() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		$api = $this->game_platform_manager->initApi($this->platformCode);

		$token = 'abc123';
		$dateTimeFrom = new DateTime('2015-07-09 00:00:00');
		$dateTimeTo = new DateTime('2015-07-11 00:00:00');
		//$dateTimeTo = null;
		$player = $this->getFirstPlayer();

		$api->syncInfo[$token] = array("playerName" => $player->username, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$api->syncLostAndFound($token);
	}

}
