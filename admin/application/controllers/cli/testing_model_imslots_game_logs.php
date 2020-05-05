<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_imslots_game_logs extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('imslots_game_logs');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->getdata();
	}

	//it's your real test function
	private function testModel() {
		
	}

	private function syncTtg() {
		$this->imslots_game_logs->syncPrg(); // games for PRG
		$this->imslots_game_logs->syncGos(); // games for GOS
		$this->imslots_game_logs->syncTtg(); // games for TTG
	}

	private function getdata() {
		//echo "<pre>";print_r($this->imslots_game_logs->getdatabasedata());exit;
		$games = $this->imslots_game_logs->getdatabasedata();
		$games = json_decode(json_encode($games), true);

		$pulledGames = array();
		foreach ($games as $gameType) {
			if (!isset($pulledGames[$gameType['game_type']])) {
				$pulledGames[$gameType['game_type']] = array(
					'game_type' => 'IMSLOTS PRG ' . $gameType['game_type'],
					'game_type_lang' => 'IMSLOTS PRG ' . $gameType['game_type'],
					'status' => true,
					'flag_show_in_site' => true,
					'game_description_list' => array(),
				);
			}
			$pgame = array(
				'game_name' => $gameType['chinese_name'],
				'english_name' => $gameType['game_name'],
				'external_game_id' => $gameType['game_code'],
				'game_code' => $gameType['game_code'],
				'flash_enabled' => strstr($gameType['technology'], "Flash") ? true : false,
				'html_five_enabled' => strstr($gameType['technology'], "HTML5") ? true : false,
			);
			array_push($pulledGames[$gameType['game_type']]['game_description_list'], $pgame);
		}
		//echo "<pre>";print_r($pulledGames);exit;
		echo json_encode($pulledGames);exit;
	}

}

///end of file/////////////