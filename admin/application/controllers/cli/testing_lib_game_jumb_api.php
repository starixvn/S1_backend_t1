<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_jumb_api extends BaseTesting {

    private $platformCode = JUMB_GAMING_API;
    private $platformName = 'JUMB';
    private $api = null;

    public function init() {
        $this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
        $this->test($this->api->getPlatformCode() == $this->platformCode, true, 'init api by ' . $this->platformCode);
    }

    public function testAll() {
        $this->init();
        // $this->testIsPlayerExist();
        // $this->testCreatePlayer();

        // $this->testPlayerLogin();
        // $this->testDeposit();
        // $this->testWithdraw();
        // $this->testQueryPlayerBalance();
        // $this->testQueryForwardGame();

        // $this->testSyncGameLogs();
        // $this->testSyncMergeToGameLogs();
        //$this->testCreateAndLogin();
        // $this->testSyncGameLogsFtp();
    }
    //parameter $date 20180606
    public function syncDailyGameLogsFtp($date = null){
        $this->init();
        $token = 'abc123';
        $date = new DateTime($date);
        $dateTimeFrom = new DateTime($date->format('Y-m-d 00:00:00'));
        $dateTimeTo = new DateTime($date->format('Y-m-d 23:59:59'));
        $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncJumbGamelogsFtp($token);
        echo "<pre>";print_r($rlt);exit();
    }
    private function testSyncGameLogsFtp() {
        $token = 'abc123';

        $dateTimeFrom = new DateTime('2018-06-06 02:00:00');
        $dateTimeTo = new DateTime('2018-06-10 02:59:59');

        $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncJumbGamelogsFtp($token);
        echo "<pre>";print_r($rlt);
        $this->test($rlt['success'], true, 'Test syncOriginalGameLogs to Jumb');
    }

    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

    private function testIsPlayerExist() {
        $username =  'testoink';
        $rlt = $this->api->isPlayerExist($username);
        echo "<pre>";print_r($rlt);exit;
    }

    private function testCreatePlayer() {
        $playerName = 'testoink';
        $password = 'pass123';
        $playerId = 1;
        $rlt = $this->api->createPlayer($playerName, $playerId, $password, null);
        echo "<pre>";print_r($rlt);exit;
    }

    private function testCreateAndLogin() {
        $username = 'test' . random_string('alnum');
        $password = '12344321';
        $depositAmount = 1.2;
        $player = $this->getFirstPlayer($username);
        $this->utils->debug_log("create player", $username, 'password', $password, 'amount', $depositAmount);

        $this->utils->debug_log('=====createPlayer: ' . $username . '======================================================');
        $rlt = $this->api->createPlayer($username, $player->playerId, $password);
        // $this->utils->debug_log('after createPlayer', $rlt);
        $this->test($rlt['success'], true, $username . ' createPlayer for ' . $this->platformName);
        $this->utils->debug_log('=====result of createPlayer: ' . $username . '======================================================', $rlt);

        $this->utils->debug_log('=====login: ' . $username . '======================================================');
        $rlt = $this->api->login($username, $password);
        // $this->utils->debug_log('after createPlayer', $rlt);
        $this->test($rlt['success'], true, $username . ' login for ' . $this->platformName);
        $this->utils->debug_log('=====result of login: ' . $username . '======================================================', $rlt);

    }

    private function testBatchQueryPlayerBalance() {
        $rlt = $this->api->batchQueryPlayerBalance(null);
        $this->utils->debug_log($rlt);
        $this->test($rlt['success'], true, ' testBatchQueryPlayerBalance for ' . $this->platformName);
    }

    private function testGameLogs() {
        $rowId = '16103653263';
        $rlt = $this->api->convertGameRecordsToFile($rowId);

    }

    private function testPlayerLogin() {
        $playerName = 'test123456789012l234561test';
        $password = 'pass123';
        $rlt = $this->api->login($playerName, $password);
        $this->test($rlt['success'], true, $playerName . ' login for ' . $this->platformName);
        $this->utils->debug_log('=====result of login: ' . $playerName . '======================================================', $rlt);
    }

    public function testQueryPlayerBalance() {
        $playerName = 'testoink';

        $rlt = $this->api->queryPlayerBalance($playerName);
        $this->utils->debug_log($rlt);
        $this->test($rlt['success'], true, 'balance for ' . $playerName);
        $this->test($rlt['balance'], 14, 'balance amount ' . $rlt['balance'] . ' for ' . $playerName);
    }

    private function testDeposit() {
        $playerName = 'testoink';
        $depositAmount = 1;
        $rlt = $this->api->depositToGame($playerName, $depositAmount);

        print_r($rlt);
        exit;

        $this->utils->debug_log('depositToGame', $rlt);
        echo "<pre>";print_r($rlt);exit;
        $this->test($rlt['success'], true, 'Test Player Deposit to JUMB');
        $this->test($rlt['currentplayerbalance'], 56, 'Current Balance after deposit');
    }

    private function testWithdraw() {
        $playerName = 'testjumbx6';
        $withdrawAmount = -1;
        $rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
        $this->utils->debug_log('withdrawToGame', $rlt);
        $this->test($rlt['success'], true, 'Test Player Withdraw to JUMB');
        $this->test($rlt['currentplayerbalance'], 10, 'Current Balance after withdrawal');
    }

    private function testSyncGameLogs() {
        $token = 'abc123';

        $dateTimeFrom = new DateTime('2017-10-04 16:40:00');
        $dateTimeTo = new DateTime('2017-10-04 16:45:00');

        $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncOriginalGameLogs($token);
        echo "<pre>";print_r($rlt);
        $this->test($rlt['success'], true, 'Test syncOriginalGameLogs to Jumb');
    }

    private function testSyncMergeToGameLogs() {
        //$api = $this->game_platform_manager->initApi($this->platformCode);
        $token = 'abc123';
        // $dateTimeFrom = new DateTime('2017-06-02 16:40:00');
        // $dateTimeTo = new DateTime('2017-06-02 14:50:00');

        // $dateTimeFrom = new DateTime('2017-06-07 16:57:00');
        // $dateTimeTo = new DateTime('2017-06-07 17:12:00');

        $dateTimeFrom = new DateTime('2017-06-22 18:00:00');
        $dateTimeTo = new DateTime('2017-06-22 18:15:00');

        $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncMergeToGameLogs($token);
        echo "<pre>";print_r($rlt);exit;
        $this->test($rlt['success'], true, 'Test syncMergeToGameLogs to Jumb');
    }

    private function testQueryForwardGame() {
        $playerName = 'test123456789012l23test';
        $param = array(
            "game_code" => "123",
            "game_mode" => "true",
            "is_mobile_flag" => "false",
        );
        $rlt = $this->api->queryForwardGame($playerName, $param);
        var_dump($rlt);exit();
        $this->test($rlt['success'], true, 'Test syncOriginalGameLogs to Jumb');
    }
}