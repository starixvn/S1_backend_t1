<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_sync_ag extends BaseTesting {

	private $platformCode = AG_API;

	public function init() {
		// $this->load->model('game_provider_auth');
		// $this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		// // $this->test($this->game_platform_manager == null, false, 'init game platform manager');
		// $api = $this->game_platform_manager->initApi($this->platformCode);
		// // $this->test($api == null, false, 'init api');
		// $this->test($api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
	}

	public function testAll() {
		// $this->init();
		// $this->game_platform_manager->syncGameRecords();

		//check response_results
		//check ag_game_logs
		//check game_logs
	}

}

/// END OF FILE//////