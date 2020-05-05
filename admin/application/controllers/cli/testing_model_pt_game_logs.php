<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_pt_game_logs extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('pt_game_logs');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->testModel();
	}

	//it's your real test function
	private function testModel() {
		$this->test($this->pt_game_logs != null, true, 'test model pt_game_logs');
	}

}

///end of file/////////////