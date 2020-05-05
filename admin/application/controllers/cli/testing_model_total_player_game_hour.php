<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_total_player_game_hour extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model(array('total_player_game_hour', 'total_player_game_day'));
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		// $this->testModel();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testSyncHour() {
		$from = new \DateTime('2015-09-01 10:00:00');
		$to = new \DateTime('2015-09-01 23:00:00');
		$playerId = null;

		$cnt = $this->total_player_game_hour->sync($from, $to, $playerId);

		$this->utils->debug_log('cnt', $cnt);

		$this->test($cnt > 0, true, 'sync total_player_game_hour');
	}

	private function testSyncDay() {
		$from = new \DateTime('2015-09-01 10:00:00');
		$to = new \DateTime('2015-09-01 23:00:00');
		$playerId = null;

		$cnt = $this->total_player_game_day->sync($from, $to, $playerId);

		$this->utils->debug_log('cnt', $cnt);

		$this->test($cnt > 0, true, 'sync total_player_game_hour');
	}

	//it's your real test function
	// private function testModel() {
	// 	$this->test($this->pt_game_logs != null, true, 'test model pt_game_logs');
	// }

}

///end of file/////////////