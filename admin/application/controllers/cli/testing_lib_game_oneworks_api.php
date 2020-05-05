<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_oneworks_api extends BaseTesting {

	private $platformCode = ONEWORKS_API;
	private $platformName = 'ONEWORKS';
	private $api = null;

	public function init() {
		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
		$this->test($this->api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
	}

	public function testAll() {
		$this->init();
		// $this->testCreatePlayer();
		// $this->testSetMemberSettings();
		// $this->testQueryPlayerBalance();
		// $this->testDeposit();
		// $this->testWithdraw();
		// $this->testPlayGame();
		// $this->testSyncGameLogs();
		$this->testSyncMergeToGameLogs();
		// $this->testBase();
		// $this->testQueryForwardGame();
		// $this->testCallback();
		// $this->testUpdateMemberSetting();
        // $this->testUpdateAllMemberSetting();
		// $this->testIsPlayerExist();
        // $this->testGetLeagueName();
        // $this->testGetTeamName();
        // $this->testQueryTransaction();
        // $this->testSyncOriginalGameResult();
	}

	private function testSyncOriginalGameResult() {
		$token = 'abc123';
        $dateTimeFrom = new DateTime('2018-05-03 20:50:01');
        $dateTimeTo = new DateTime('2018-05-15 23:59:40');
		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameResult($token);
		echo "<pre>";print_r($rlt);exit;
	}


	private function testBase() {
		$username = 'ow' . random_string('alnum') . "_test";
		$this->utils->debug_log("create player", $username);
		$password = $username;

		$rlt = $this->api->createPlayer($username, 1, $password, null);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'Create Player: ' . $username);

		$depositAmount = 10;
		$rlt = $this->api->depositToGame($username, $depositAmount);
		$this->utils->debug_log('depositToGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Deposit to Oneworks');

		$rlt = $this->api->queryPlayerBalance($username);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'balance for ' . $username);
		$this->test($rlt['balance'], 30, 'balance amount ' . $rlt['balance'] . ' for ' . $username);

		$withdrawAmount = 10;
		$rlt = $this->api->withdrawFromGame($username, $withdrawAmount);
		$this->utils->debug_log('withdrawToGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Withdraw to Oneworks');

		$rlt = $this->api->queryPlayerBalance($username);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'balance for ' . $username);
		$this->test($rlt['balance'], 30, 'balance amount ' . $rlt['balance'] . ' for ' . $username);
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	public function testCallback() {
		$rlt = $this->api->callback();
	}

    public function testIsPlayerExist(){
        $playerName = 'wintestgray';
        $rlt = $this->api->isPlayerExist($playerName);
        $this->utils->debug_log($rlt);
        $this->test($rlt['success'], true, ' testBatchQueryPlayerBalance for ' . $this->platformName);
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
		// $username = 'testb188';
		$username = 'testdarkred';
		$password = $username;
		$playerId = 1;
		$rlt = $this->api->createPlayer($username, $playerId, $password, null);
		$this->test($rlt['success'], true, $username . ' testCreatePlayer for ' . $this->platformName);
		$this->utils->debug_log($rlt);
	}

	private function testSetMemberSettings() {
		// $username = 'testb188';
		// $rlt = $this->api->setMemberSetting($username);
		// $this->test($rlt['success'], true, $username . ' setMemberSetting for ' . $this->platformName);
		// $this->utils->debug_log($rlt);

		$username =  'testjerb06';
		$rlt = $this->api->setMemberSetting($username);
		echo "<pre>";print_r($rlt);exit;
	}

	private function testUpdateMemberSetting($username = null) {
		if (!$username) {
			$username = 'p233513057827';
		}

		$rlt = $this->api->updateMemberSetting($username);
		$this->test($rlt['success'], true, $username . ' updateMemberSetting for ' . $this->platformName);
		$this->utils->debug_log($rlt);
	}

	private function testUpdateAllMemberSetting() {
		$this->load->model(array('player_model'));
		$players = $this->player_model->getAllUsernames();
		foreach ($players as $key) {
			$rlt = $this->api->updateMemberSetting($key['username']);
			$this->test($rlt['success'], true, $key['username'] . ' updateMemberSetting for ' . $this->platformName);
			$this->utils->debug_log($rlt);
		}
	}

	private function testDeposit() {
		$playerName = 'teststaginggray';
		$depositAmount = 1;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);
		$this->utils->debug_log('depositToGame', $rlt);
		$this->test($rlt['success'], true, 'Test ' . $playerName . ' Player Deposit to Oneworks');
		$this->test($rlt['currentplayerbalance'], $rlt['currentplayerbalance'], ' Current Balance after deposit: ' . $rlt['currentplayerbalance']);
	}

	private function testWithdraw() {
		$playerName = 'teststaginggray';
		$withdrawAmount = 1;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		$this->utils->debug_log('withdrawToGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Withdraw to Oneworks');
		$this->test($rlt['currentplayerbalance'], $rlt['currentplayerbalance'], ' Current Balance after deposit: ' . $rlt['currentplayerbalance']);
	}

	public function testQueryPlayerBalance() {
		$playerName = 'testwXi7Lic3_test';
		$rlt = $this->api->queryPlayerBalance($playerName);
		$this->utils->debug_log($rlt);
		$this->test($rlt['success'], true, 'balance for ' . $playerName);
		$this->test($rlt['balance'], 0, 'balance amount ' . $rlt['balance'] . ' for ' . $playerName);
	}

	private function testSyncGameLogs() {
		$token = 'abc123';
		$dateTimeFrom = new DateTime('2017-11-29 00:00:00');
		$dateTimeTo = new DateTime('2017-11-29 23:59:59');

		$playerName = 'testibc24164105';

		$this->api->syncInfo[$token] = array("playerName" => $playerName, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		$this->test($rlt['success'], true, 'Test syncOriginalGameLogs to Oneworks');
	}

	private function testSyncMergeToGameLogs() {
		// echo 1;exit();
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
         $dateTimeFrom = new DateTime('2018-06-16 18:45:45');
        $dateTimeTo = new DateTime('2018-06-16 18:45:45');

		$playerName = 'wiplixt';

		$this->api->syncInfo[$token] = array("playerName" => $playerName, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		echo "<pre>";print_r($rlt);exit;
		// $this->test($rlt['success'], true, 'Test syncMergeToGameLogs to Oneworks');
	}

	public function testPlayGame() {
		$playerName = 'testibc24164105';
		$password = 'pass123';
		$rlt = $this->api->login($playerName, $password);
		$this->test($rlt['success'], true, 'Login');
		$sessionToken = $rlt["sessionToken"];
		$this->load->helper('cookie');
		$this->input->set_cookie('g', $sessionToken, 3600); //3600 = 1hr
	}

	private function testQueryForwardGame() {
		$playerName = 'testwXi7Lic3_test';
		$param = array(
			"language" => "en",
			"gameType" => "1",
		);
		$rlt = $this->api->queryForwardGame($playerName, $param);
		var_dump($rlt);exit();
		$this->test($rlt['success'], true, 'Test syncOriginalGameLogs to LAPIS');
	}

	public function testRaw($post_data = null, $url = null) {
		// $url = "http://api.mid.ib.gsoft88.net/api/CreatePlayer?SecurityToken=587C960551C91AC1275EBB073FC13CEB&OpCode=LBBCN&PlayerName=testibc57663977&OddsType=1&MaxTransfer=1000&MinTransfer=100";
		// $url = "http://api.prod.ib.gsoft88.net/api/CreateMember?SecurityToken=587C960551C91AC1275EBB073FC13CEB&OpCode=LBBCN&PlayerName=testibc57663977&OddsType=1&MaxTransfer=1000&MinTransfer=100";
		// $url = "http://api.prod.ib.gsoft88.net/api/CreateMember?SecurityToken=C8E7E4F11F731D10D25E2DE5274C6CC5&OpCode=LBBCN&PlayerName=testibc35675120&OddsType=1&MaxTransfer=1000&MinTransfer=100";
		$url = "http://api.prod.ib.gsoft88.net/api/FundTrasfer?OpCode=LBBCN&PlayerName=testibc24164105&TransId=7pzdY2l9&Amount=10&Direction=1&SecurityToken=15FD12D78377D8A4EA81A50CB5446AEF";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_POST, 1);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		$result = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		var_dump($result, $errno, $error);
		$this->utils->debug_log('res:' . $result . 'errno:' . $errno . 'error:' . $error);
		//close connection
		curl_close($ch);
	}

    public function testGetLeagueName(){
        $league_id = 99;
        $result = $this->api->getLeagueName($league_id);
        var_dump($result);exit();

    }
    public function testQueryTransaction(){
        $result = $this->api->queryTransaction(71827772,null);
        var_dump($result);exit();

    }


    public function testGetTeamName(){
        $league_id = 6;
        $result = $this->api->getTeamName($league_id);
        // var_dump($result);exit();
    }

}