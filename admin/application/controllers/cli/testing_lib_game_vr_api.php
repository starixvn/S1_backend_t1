<?php
    require_once dirname(__FILE__) . '/base_testing.php';

    class Testing_lib_game_vr_api extends BaseTesting {
        private $platformCode = VR_API;
        private $platformName = 'VR_API';
        private $api = null;

        public function init() {
            $this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);
        }

        public function testAll() {
            $this->init();
            // $this->testCreatePlayer(); // Working
            // $this->testLogin(); // Working - Just run /goto_vrgame at player center
            // $this->testPlayerForceLogOff(); // Working
            // $this->testQueryPlayerBalance(); // Working
             // $this->testDeposit(); // Working
            // $this->testWithdraw(); // Working
            // $this->testQueryForwardGame(); // Working - Just run /goto_vrgame at player center
            // $this->testSyncOriginalGamelogs(); // Working
            $this->testSyncGameLogsThroughExcel(); // Working
            //$this->testSyncMergeToGameLogs(); // Working
             // $this->testisPlayerExist(); // Ongoing

            // $this->testdecrypt('NSl30cxdXy0unHCVZcSeA0XF1qvIXn1+PYQi+Wxm8wY='); // Decode
        }

        public function syncFtpLogs($date) {
            echo "syncFtpLogs";
            $this->init();
            $this->testSyncGameLogsThroughExcel($date);
        }

        private function testdecrypt($value) {
            $rlt = $this->api->apiDecode($value);
            echo "<pre>";print_r($rlt);
        }

        private function testCreatePlayer() {
            $username = 'testgray';
            $password = '123456';
            $player_id = '57045';

            $rlt = $this->api->createPlayer($username, $player_id, $password);
        }

        private function testSyncGameLogsThroughExcel($date) {
            $rlt = $this->api->syncGameLogsThroughExcel($date);
        }

        private function testLogin() {
            $username = 'testjerb17';
            $rlt = $this->api->login($username);
             echo "<pre>";print_r($rlt);exit();
        }

        private function testPlayerForceLogOff() {
            $username = 'testmarcke';
            $rlt = $this->api->playerForceLogOff($username);
        }

        private function testQueryPlayerBalance() {
            $playerName = 'testgray';
            $rlt = $this->api->queryPlayerBalance($playerName);
            echo "<pre>";
            print_r($rlt);
        }

        private function testdeposit() {
            $playerName = 'testshu';
            $depositAmount = 1;
            $rlt = $this->api->depositToGame($playerName, $depositAmount);
            echo "<pre>";print_r($rlt);exit();
        }

        private function testWithdraw() {
            $playerName = 'testmarcke';
            $withdrawAmount = 1;
            $rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
        }

        private function testQueryForwardGame(){
            $username = 'testjerb17';
            $rlt = $this->api->queryForwardGame($username,null,0);
            echo "<pre>";print_r($rlt);exit();
        }

        private function testSyncOriginalGamelogs() {
            $token = 'abc123d';
            $dateTimeFrom = new DateTime('2017-07-26 13:00:00');
            $dateTimeTo = new DateTime('2017-07-26 13:05:00');

            $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
            $rlt = $this->api->syncOriginalGamelogs($token);
        }

        private function testSyncMergeToGameLogs() {
            $token = 'abc123d';
            $dateTimeFrom = new DateTime('2017-07-26 13:00:00');
            $dateTimeTo = new DateTime('2017-07-26 13:05:00');

            $this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
            $rlt = $this->api->syncMergeToGameLogs($token);
        }

        private function testIsPlayerExist() {
            $username = 'testjerb17111';
            $rlt = $this->api->isPlayerExist($username);
            echo "<pre>";print_r($rlt);exit();
        }

        // Below Are Not Yet Use
        private function testChangePassword() {
            $username = 'testezugin2';
            //$oldPassword = $this->password;
            $oldPassword = 'password1';
            $newPassword = 'password';

            $rlt = $this->api->changePassword($username, $oldPassword, $newPassword);
            echo "<pre>";print_r($rlt);exit;
        }

        private function testBlockPlayer(){
            $username =  'testkeir';
            $rlt = $this->api->blockPlayer($username);
            echo "<pre>";print_r($rlt);exit;
        }

        private function testUnblockPlayer(){
            $username =  'testkeir';
            $rlt = $this->api->unblockPlayer($username);
            echo "<pre>";print_r($rlt);exit;
        }
    }
?>