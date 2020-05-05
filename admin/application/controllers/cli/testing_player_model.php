<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_player_model extends BaseTesting {
//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('player_model');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		// $this->testTotalCashbackDaily();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testLock() {
		$key = 'test_lock';
		$rlt = $this->player_model->transGetLock($key);
		$this->utils->debug_log('lock', $key, $rlt);
		try {
			$this->utils->debug_log('do something');
		} finally {
			$rlt = $this->player_model->transReleaseLock($key);
			$this->utils->debug_log('release lock', $key, $rlt);
		}
	}

	//it's your real test function
	// private function testTotalCashbackDaily() {
	// 	$this->group_level->totalCashbackDaily();
	// }

	private function testActivePlayer() {
		$player = $this->getFirstPlayer();
		$playerId = $player->playerId;
		$rlt = $this->player_model->isActivePlayer($playerId);
		$this->test($rlt, true, 'isActivePlayer:' . $playerId);
	}
}
///end of file/////////////
