<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_tcg_api extends BaseTesting {

    private $platformCode = TCG_API;
    private $api = null;

    private $player_name = "huanghaishan23";
    private $password = "123456";
    private $player_id = 57015;
    private $amount = 29;

    public function init() {
        $this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
    }

    public function testAll() {
        $this->init();
    }

    public function testTarget($methodName) {
        $this->init();
        $this->$methodName();
    }

    private function testCreatePlayer() {
        $rlt = $this->api->createPlayer($this->player_name,$this->player_id, $this->password, null);
        print_r($rlt);
    }

    private function testAccessToken() {
        $rlt = $this->api->accessToken();
        print_r($rlt);
    }

    private function testRefreshToken() {
        $rlt = $this->api->refreshToken();
        print_r($rlt);
    }


    private function testChangePassword() {
        $oldPassword = $this->password;
        $newPassword = 'pass123';

        $res = $this->api->changePassword($this->player_name, $oldPassword, $newPassword);
        print_r($res);
    }


    private function testQueryPlayerBalance() {
        $rlt = $this->api->queryPlayerBalance($this->player_name);

        print_r($rlt);
    }

    private function testQueryBatchPlayerBalance() {
        $rlt = $this->api->batchQueryPlayerBalance('',random_string('numeric'));

        print_r($rlt);
    }

    private function testLogin() {
        $extra = array();
        $rlt = $this->api->login($this->player_name,$extra);

        print_r($rlt);
    }

    private function testDeposit() {

        $rlt = $this->api->depositToGame($this->player_name, $this->amount);

        print_r($rlt);
    }

    private function testWithdraw() {
        $rlt = $this->api->withdrawFromGame($this->player_name, $this->amount);

        print_r($rlt);
    }

    private function testQueryForwardGame(){
        $extra = array();
        $rlt = $this->api->queryForwardGame($this->player_name,$extra);

        print_r($rlt);
    }

    private function testSyncGameLogs() {
        $token = 'abc123';

        # UNSETTLED DATE 13:52:36
        $dateTimeFrom = new DateTime('2018-08-20 13:52:00');
        $dateTimeTo = new DateTime('2018-08-20 14:20:00');

        #SETTLED NA DATE 13:52:36
        #$dateTimeFrom = new DateTime('2018-08-20 17:00:00');
        #$dateTimeTo = new DateTime('2018-08-20 17:50:00');

        # SETTLED DATE 11:44:36
        # $dateTimeFrom = new DateTime('2018-08-20 11:45:00');
        # $dateTimeTo = new DateTime('2018-08-20 13:45:00');

        $this->api->syncInfo[$token] = array("playerName" => $this->player_name, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncOriginalGameLogs($token);

        print_r($rlt);
    }

    private function testSyncMergeToGameLogs() {
        $token = 'abc123';
        $dateTimeFrom = new DateTime('2018-07-29 13:00:00');
        $dateTimeTo = new DateTime('2018-08-22 19:00:00');

        $this->api->syncInfo[$token] = array("playerName" => $this->player_name, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
        $rlt = $this->api->syncMergeToGameLogs($token);

        print_r($rlt);
    }

    // too slow, ask game provider to update
    private function testQueryTransaction() {
        $transaction_id = 'T671967300430';
        $extra = array(
            'playerName' => $this->player_name,
            'playerId' => $this->player_id
        );
        $rlt = $this->api->queryTransaction($transaction_id, $extra);
        print_r($rlt);
        exit;
    }

    private function testIsPlayerExist() {
        $rlt = $this->api->isPlayerExist($this->player_name);

        print_r($rlt);
        exit;
    }

    private function testGetGameList() {
        $rlt = $this->api->getGameList();

        print_r($rlt);
        exit;
    }
}