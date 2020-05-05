<?php
//always include base testing
require_once dirname(__FILE__) . '/base_testing.php';

//always extends from BaseTesting
class Testing_model_game_provider_auth extends BaseTesting {

	//should overwrite init function
	public function init() {
		//init your model or lib
		$this->load->model('game_provider_auth');
	}
	//should overwrite testAll
	public function testAll() {
		//init first
		$this->init();
		//call your test function
		$this->testSavePassword();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	//it's your real test function
	private function testSavePassword() {
		$gamePlatformId = AG_API;
		//$username = 'test_' . random_string('alnum');
		$username = 'asriinew2';
		$player = array('id' => 1, 'username' => $username,
			'password' => '12344321', 'source' => Game_provider_auth::SOURCE_REGISTER);
		$this->game_provider_auth->savePasswordForPlayer($player, $gamePlatformId);

		$this->test($this->game_provider_auth->getPasswordByLoginName($username, $gamePlatformId), '123456', 'save password');
	}

	private function testGetPlayerCurrentBet() {
		$this->load->model(array('game_logs'));
		$playerId = 112;
		$this->game_logs->getPlayerCurrentBet($playerId, null);

	}

}

///end of file/////////////
