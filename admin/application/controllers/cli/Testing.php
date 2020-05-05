<?php

class Testing extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library("unit_test");
		$this->load->helper('string');
	}

	public function index() {
		// $this->unit->run(1, 1, 'test sample', 'only a sample 1==1');

		// $this->testGamePlatformManager();
		$this->testGamePlatformAG();

		//$this->testGamePlatformPT();

		//$randStr = random_string('abcstw');
		//echo 'test: '.$randStr;

		//$this->testDepositToAG();

		$this->load->view('test_result');
	}

	public function aff($yearmonth = NULL) {
		$this->load->library('affiliate_commission');
		$this->affiliate_commission->test($yearmonth);
	}

	private function initGamePlatform($platformCode) {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $platformCode));
		$this->unit->run($this->game_platform_manager == null, false, 'init game platform manager');
		$api = $this->game_platform_manager->initApi($platformCode);
		//$this->unit->run($api == null, false, 'init api');
		//$this->unit->run($api->getPlatformCode() == $platformCode, true, 'init api by ' . $platformCode);
		return $this->game_platform_manager;
	}

	private function testGamePlatformManager() {
		//test AG
		$this->initGamePlatform(AG_API);
		$api = $this->game_platform_manager->getApi();

		log_message('error', var_export($api->queryPlayerDailyBalance('eyetiger', '111', '2015-07-09', '2015-07-09'), true));

		// log_message("error", $this->lang->line("doesn't exist"));
		// $this->game_platform_manager->createPlayer();
	}

	private function testGamePlatformAG() {
		$this->initGamePlatform(AG_API);

		$playerName = 'raicese08'; // 'agtest2';
		$infos = array('firstname' => 'Andy', 'lastname' => 'Radam');

		$dateFrom = '2015-03-01';
		$dateTo = '2015-06-17';

		// $balResult = $this->game_platform_manager->queryPlayerBalance($playerName);
		// $infoResult = $this->game_platform_manager->updatePlayerInfo($playerName, $infos);
		// $checkLoginStatusResult = $this->game_platform_manager->checkLoginStatus($playerName);
		$totalBettingAmountResult = $this->game_platform_manager->totalBettingAmount($playerName, $dateFrom, $dateTo);

		log_message("error", var_export($totalBettingAmountResult, true));

		// $this->unit->run($balResult['success'], true, 'queryPlayerBalance');
		// $this->unit->run($infoResult['success'], true, 'updatePlayerInfo');
		// $this->unit->run($balResult['balance'], 3, 'queryPlayerBalance');
		// $this->unit->run($checkLoginStatusResult['success'], true, 'checkLoginStatus');
		// $this->unit->run($checkLoginStatusResult['loginStatus'], true, 'checkLoginStatus');
		$this->unit->run($totalBettingAmountResult['bettingAmount'], 23990, 'totalBettingAmount');
	}

	private function testGamePlatformPT() {
		$this->initGamePlatform(PT_API);
		$playerName = 'hl1234';
		$password = 'newpass';
		$balResult = $this->game_platform_manager->queryPlayerBalance($playerName);
		log_message("error", var_export($balResult, true));
		// $this->unit->run($balResult['success'], true, 'queryPlayerBalance');
		// $this->unit->run($balResult['balance'], 3, 'queryPlayerBalance');

	}

	private function testDepositToAG() {
		$this->initGamePlatform(AG_API);
		$playerName = 'asriinew2'; // 'agtest2';
		$balResult = $this->game_platform_manager->depositToGame($playerName, 5);
		log_message("error", var_export($balResult, true));
		$this->unit->run($balResult['success'], true, 'queryPlayerBalance');
	}

	// private function testResponseResult() {
	// 	$this->load->model("response_result");
	// 	$systemTypeId = AG_API;
	// 	$apiName = 'CheckOrCreateGameAccout';
	// 	$params = json_encode(array());
	// 	$this->response_result->saveResponseResult($systemTypeId, $apiName, $params, $resultText, $statusCode, $statusText, $extra);
	// }

}