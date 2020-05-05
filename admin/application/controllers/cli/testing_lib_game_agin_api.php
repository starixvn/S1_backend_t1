<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_game_agin_api extends BaseTesting {

	private $platformCode = AGIN_API;
	private $platformName = 'AGIN';

	private $api = null;


	public function init() {

		$this->api = $this->utils->loadExternalSystemLibObject($this->platformCode);



	}

	public function testAll() {

	 	$this->init();
	  // $this->testCreatePlayer();
	  //   $this->testDeposit();
	    //$this->testWithdraw();
	//   $this->testQueryPlayerBalance();
	    //$this->testAddandUpdateGameList();
	 //  $this->testSyncGameLogs();
	    // $this->testIsPlayerExist();
	  // $this->testQueryForwardGame();
	   // $this->testSyncGameLogs();
	   // $this->testSyncMergeToGameLogs();
	   $this->syncOriginalGameResult();
	     //$this->testLogout();


	}

    public function testTarget($methodName,$from,$to) {
		$this->init();
		$this->$methodName($from,$to);
	}

	public function testBase() {


	}

	private function syncOriginalGameResult($from,$to) {
		$token = 'abc123d';
		$dateTimeFrom = new DateTime($from);
		$dateTimeTo = new DateTime($to);
		// print_r($to);exit();
		//$playerName = 'testwebet';

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameResult($token);
		var_dump($rlt);
	}

	private function testCreatePlayer() {


		 $this->utils->debug_log('hello');


		$username = 'chistie10';
		$this->utils->debug_log("create player", $username);
		$password = 'chistie10';
		//$playerId = $this->api->getPlayerIdInGameProviderAuth($username);
		$playerId =21;
		$this->utils->debug_log('=====createPlayer: ' . $username . '======================================================');

		$extra = array();
		 $rlt = $this->api->createPlayer($username, $playerId, $password, null, $extra );
		 // var_dump($rlt);
		 $this->test($rlt['success'], true, 'create player: ' . $username);
		 $this->utils->debug_log('=====result of createPlayer: ' . $username . '======================================================', $rlt);

	}


	private function testDeposit() {
	    $playerName = 'testwebet';
	    $depositAmount = 2.00;
		$rlt = $this->api->depositToGame($playerName, $depositAmount);

		var_dump($rlt);
		$this->utils->debug_log('depositToGame', $rlt);
		 $this->test($rlt['success'], true, 'Test Player Deposit to AGIN');

	}


	private function testWithdraw() {
		$playerName = 'gutom37';
	    $withdrawAmount = 10.00;
		$rlt = $this->api->withdrawFromGame($playerName, $withdrawAmount);
		$this->utils->debug_log('withdrawFromGame', $rlt);
		$this->test($rlt['success'], true, 'Test Player Withdrawal to AGIN');

	}

	public function testQueryPlayerBalance() {
		//$playerName = 'siopao36';
		$playerName = 'tstwebet3';

		$rlt = $this->api->queryPlayerBalance($playerName);
		var_dump($rlt);
		//$$this->utils->debug_log('queryPlayerBalance', $rlt);
		$this->test($rlt['success'], true, 'balance for ' . $playerName);

	}

	private function testSyncGameLogs() { 

		$token = 'abc123d';
		$dateTimeFrom = new DateTime('2017-01-26');
		$dateTimeTo = new DateTime('2017-01-28');

		//$playerName = 'testwebet';

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncOriginalGameLogs($token);
		//var_dump($rlt);
         $this->test($rlt['success'], true, 'Test syncOriginalGameLogs to AGIN');
		//$this->utils->readExcel();
	}


	private function testIsPlayerExist() {

		$username =  'gutom37';
		$rlt = $this->api->isPlayerExist($username);
		var_dump($rlt);
		//$this->test($rlt['success'], true, 'player ' . $username . ' already exists');

	}


	private function testQueryForwardGame(){
     $username = 'testwebet';


    // $extra =  array(
    // 	'language' =>'EN' ,
    // 	'game_code' => 'xc_polartaleslots',
    // 	'playmode'=>'real'
    // 	);
    // $extra =  array(
    // 	'language' =>'EN' ,
    // 	'code' => 'xc_mobilenonstoppartyslots',
    // 	'mode'=>'real',
    // 	'mobile' => 'true'
    // 	);



	$rlt = $this->api->queryForwardGame($username, $extra);
	var_dump($rlt);//redirect($rlt['url']);
	}

	private function testSyncMergeToGameLogs() {
		//$api = $this->game_platform_manager->initApi($this->platformCode);
		$token = 'abc123';
	 $dateTimeFrom = new DateTime('2017-01-26 00:00:00');
		$dateTimeTo = new DateTime('2017-01-28 23:59:59');

		$this->api->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);
		$rlt = $this->api->syncMergeToGameLogs($token);
		$this->test($rlt['success'], true, 'Test syncMergeToGameLogs to GAMESOS');
	}

	private function testLogout() {
		$playerName = 'siopao36';
		$rlt = $this->api->logout($playerName);
		$this->utils->debug_log('logout', $rlt);
		$this->test($rlt['success'], true, 'Test Player Logout to GSPT');

	}


	private function testAddandUpdateGameList(){

		$playerName = 'gutom37';

		$rlt = $this->api->AddandUpdateGameList($playerName);

	}

	private function depositToGame($playerName, $amount, $transfer_secure_id = null){

		$api=$this->api;

        $this->load->model(array('wallet_model'));
        $usernameWithoutPrefix = $playerName;

        $password = $api->getPasswordString($playerName);
        $playerName = $api->getGameUsernameByPlayerUsername($playerName);

        $billno=$api->cagent.$transfer_secure_id; //random_string('numeric', 13);
        //BBIN , 19 number
        //MG , 8 number
        if($api->getPlatformCode()==AGBBIN_API){
            $billno=random_string('numeric', 19);
        }

        //always query balance first
        $result=$api->queryPlayerBalance($usernameWithoutPrefix);
        $result['external_transaction_id']=$billno;

        $this->utils->debug_log('=============query player balance billno', $billno, 'result', $result);

        if(!$result['success']){
            return $result;
        }

        $result = $api->prepareTransferCredit($playerName, $password, $amount, $billno, 'IN');

        $this->utils->debug_log('=============prepareTransferCredit billno', $billno, 'result', $result);

        if ($result['success']) {
            $result = $api->transferCreditConfirm($playerName, $password, $amount, $result['external_transaction_id'], 'IN');
            $this->utils->debug_log('============= AG DEPOSIT_FROM_GAME_RESULT ######### ', $result);
        }else{
        	//return failed , don't try
        	return $result;
        }

        //DEBUG: set to false
        $result['success']=false;
        $result['error_code']='network_error';

        //only if transferCreditConfirm is failed
        if(!$result['success']){

            //try query order status
            $result=$api->queryTransaction($billno, null);

            $this->utils->debug_log('============= get error when deposit try queryTransaction', $playerName, $amount, $billno, $result);

            $cnt=1;
            //3 times
            while(!$result['success'] && @$result['error_code']=='network_error' && $cnt<=3){
                $result=$api->queryTransaction($billno, null);

                $cnt++;

                $this->utils->debug_log('============= get error when deposit', $playerName, $amount, $billno, $result);
            }

            if(!$result['success'] && @$result['error_code']=='network_error'){
                $this->utils->debug_log('============= convert success to true if still network error when deposit', $playerName, $amount, $billno, $result);
                //convert to success
                $result['success']=true;
            }
        }

        if ($result['success']) {
            //update
            $playerId = $api->getPlayerIdInGameProviderAuth($playerName);
            if ($playerId) {
                $playerBalance = $api->queryPlayerBalance($usernameWithoutPrefix);
                $this->utils->debug_log('============= AG QUERY_PLAYER_BALANCE D ######### ', $playerBalance);

                $afterBalance = 0;
                if ($playerBalance && $playerBalance['success']) {
                    $afterBalance = $playerBalance['balance'];
                    $this->utils->debug_log('============= AG AFTER BALANCE FROM API D ######### ', $afterBalance);
                } else {
                    //IF GET PLAYER BALANCE FAILED
                    $result = $this->wallet_model->getSubWalletBy($playerId, $api->getPlatformCode());
                    $afterBalance = $result->totalBalanceAmount - $amount;
                    $this->utils->debug_log('============= AG AFTER BALANCE FROM WALLET D ######### ', $afterBalance);
                }
                $responseResultId = $result['response_result_id'];
                //withdrawal
                $api->insertTransactionToGameLogs($playerId, $playerName, $afterBalance, $amount, $responseResultId,
                    $api->transTypeMainWalletToSubWallet());

                //should update database
                // $this->updatePlayerSubwalletBalance($playerId, $afterBalance);
            } else {
                $this->utils->debug_log('error', '============= cannot get player id from '.$playerName.' getPlayerIdInGameProviderAuth');
            }
        }

        return $result;
	}

	private function testTransfer(){

		$playerName='test002';
		$amount=2;
		$transfer_secure_id='T'.random_string();

		$rlt=$this->depositToGame($playerName, $amount, $transfer_secure_id);

		$this->utils->debug_log('======================================',$rlt);

	}

	private function testTimeout(){
		$api= $this->api;

		$playerName='test002';
		$amount=2;
		$transfer_secure_id='T'.random_string();

		$rlt=$api->depositToGame($playerName, $amount, $transfer_secure_id);

		$this->utils->debug_log('======================================',$rlt);
	}

}