<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_model_wallet_model extends BaseTesting {

	private $subWalletId;
	private $bigWallet;

	public function init() {
		$this->load->model(array('wallet_model','transactions','sale_order', 'playerbankdetails', 'promorules'));
		$this->buildMockBigWallet();
	}

	public function testAll() {
		$this->init();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	public function testCode(){
		$code=$this->wallet_model->getRandomTransactionCode();
		$this->utils->debug_log('code',$code);

		$code=$this->wallet_model->getRandomTransactionCode();
		$this->utils->debug_log('code',$code);

		$code=$this->wallet_model->getRandomTransactionCode();
		$this->utils->debug_log('code',$code);

		$code=$this->wallet_model->getRandomTransactionCode();
		$this->utils->debug_log('code',$code);

	}

	private function buildMockBigWallet(){

		$this->bigWallet=$this->wallet_model->getEmptyBigWallet();

		$this->subWalletId=NT_API;
		//init deposit 100, bonus 50
		// $this->bigWallet['sub'][$this->subWalletId]['real_limit']=100;
		// $this->bigWallet['sub'][$this->subWalletId]['bonus_limit']=50;

		$this->bigWallet['sub'][$this->subWalletId]['real']=100;
		$this->bigWallet['sub'][$this->subWalletId]['win_real']=0;
		$this->bigWallet['sub'][$this->subWalletId]['bonus']=50;
		$this->bigWallet['sub'][$this->subWalletId]['win_bonus']=0;

		$this->wallet_model->totalBigWallet($this->bigWallet);

		$this->utils->debug_log('big wallet', $this->bigWallet, 'subWalletId', $this->subWalletId);
	}

	private function buildWinRealMockBigWallet(){
		$this->bigWallet=$this->wallet_model->getEmptyBigWallet();

		$this->subWalletId=NT_API;
		//init deposit 100, bonus 50
		// $this->bigWallet['sub'][$this->subWalletId]['real_limit']=100;
		// $this->bigWallet['sub'][$this->subWalletId]['bonus_limit']=50;

		$this->bigWallet['sub'][$this->subWalletId]['real']=100;
		$this->bigWallet['sub'][$this->subWalletId]['win_real']=20;
		$this->bigWallet['sub'][$this->subWalletId]['bonus']=50;
		$this->bigWallet['sub'][$this->subWalletId]['win_bonus']=0;

		$this->wallet_model->totalBigWallet($this->bigWallet);

		$this->utils->debug_log('big wallet', $this->bigWallet, 'subWalletId', $this->subWalletId);
	}

	private function buildBonusRealMockBigWallet(){
		$this->bigWallet=$this->wallet_model->getEmptyBigWallet();

		// $this->subWalletId=NT_API;
		//init deposit 100, bonus 50
		// $this->bigWallet['sub'][$this->subWalletId]['real_limit']=100;
		// $this->bigWallet['sub'][$this->subWalletId]['bonus_limit']=50;

		$this->bigWallet['main']['real']=100;
		$this->bigWallet['main']['win_real']=0;
		$this->bigWallet['main']['bonus']=50;
		$this->bigWallet['main']['win_bonus']=0;

		$this->wallet_model->totalBigWallet($this->bigWallet);

		$this->utils->debug_log('big wallet', $this->bigWallet, 'subWalletId', $this->subWalletId);
	}

	private function buildWinBonusMockBigWallet(){
		$this->bigWallet=$this->wallet_model->getEmptyBigWallet();

		$this->subWalletId=NT_API;
		//init deposit 100, bonus 50
		// $this->bigWallet['sub'][$this->subWalletId]['real_limit']=100;
		// $this->bigWallet['sub'][$this->subWalletId]['bonus_limit']=50;

		$this->bigWallet['sub'][$this->subWalletId]['real']=0;
		$this->bigWallet['sub'][$this->subWalletId]['win_real']=0;
		$this->bigWallet['sub'][$this->subWalletId]['bonus']=50;
		$this->bigWallet['sub'][$this->subWalletId]['win_bonus']=100;

		$this->wallet_model->totalBigWallet($this->bigWallet);

		$this->utils->debug_log('big wallet', $this->bigWallet, 'subWalletId', $this->subWalletId);
	}

	private function testDecManuallyBigWallet(){
		$this->buildWinRealMockBigWallet();

		$bigWallet=$this->bigWallet;
		$subWalletId=$this->subWalletId;

		$amount=170.0;
		$transfer_from=$subWalletId;
		$transfer_to=0;
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);

		$amount=80.0;
		$changed=$this->wallet_model->decMainManuallyByBigWallet($bigWallet, $amount);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 20.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 20.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 90.0, 'test total_nofrozen '.$amount);

		$amount=30.0;
		$changed=$this->wallet_model->decMainManuallyByBigWallet($bigWallet, $amount);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 10.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 60.0, 'test total_nofrozen '.$amount);

		$amount=20.0;
		$changed=$this->wallet_model->decMainManuallyByBigWallet($bigWallet, $amount);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 40.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 40.0, 'test total_nofrozen '.$amount);

		$amount=40.0;
		$changed=$this->wallet_model->decMainManuallyByBigWallet($bigWallet, $amount);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 0.0, 'test total_nofrozen '.$amount);

		$amount=10.0;
		$changed=$this->wallet_model->decMainManuallyByBigWallet($bigWallet, $amount);

		$this->test($changed, false, 'test failed '.$amount);

	}

	private function testTransferBigWallet(){
		$this->buildWinRealMockBigWallet();

		$bigWallet=$this->bigWallet;
		$subWalletId=$this->subWalletId;

		$amount=170.0;
		$transfer_from=$subWalletId;
		$transfer_to=0;
		// sub to main 170
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 170.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 170.0, 'test total_nofrozen '.$amount);

		$this->buildBonusRealMockBigWallet();
		$bigWallet=$this->bigWallet;

		$amount=150.0;
		$transfer_from=0;
		$transfer_to=$subWalletId;
		// main to sub 150
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 100.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 150.0, 'test total_nofrozen '.$amount);

		$this->buildWinRealMockBigWallet();
		$bigWallet=$this->bigWallet;

		$amount=100.0;
		$transfer_from=$subWalletId;
		$transfer_to=0;
		// sub to main 100
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 100.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 20.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 170.0, 'test total_nofrozen '.$amount);

		$amount=100.0;
		$transfer_from=0;
		$transfer_to=$subWalletId;
		// main to sub 100
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 100.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 20.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 170.0, 'test total_nofrozen '.$amount);

		$amount=110.0;
		$transfer_from=$subWalletId;
		$transfer_to=0;
		// sub to main 100
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 110.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 10.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 170.0, 'test total_nofrozen '.$amount);

		$amount=80.0;
		$transfer_from=0;
		$transfer_to=$subWalletId;
		// main to sub 100
		$changed=$this->wallet_model->transferByBigWallet($bigWallet, $amount, $transfer_to, $transfer_from);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['main']['real']), 30.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['main']['win_real']), 0.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['main']['bonus']), 0.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['main']['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 80.0, 'test real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 10.0, 'test win_real '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$amount);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$amount);
		$this->test(doubleval($bigWallet['total_nofrozen']), 170.0, 'test total_nofrozen '.$amount);

	}

	/**
	 *
	 * first non-0
	 *
	 * R limit: 100, B limit: 50
	 *
	 * R:100, W:0, B:50, WB:0
	 * bal= 170 = win 20 => WB
	 * R:100, W:0, B:50, WB:20
	 * bal= 140 = loss 30 => R
	 * R:70, W:0, B:50, WB:20
	 * bal= 190 = win 50 => WB
	 * R:70, W:0, B:50, WB:70
	 * bal= 40 = loss 150=> R, W, WB
	 * R:0, W:0, B:40, WB:0
	 * bal= 140 = win 100 => WB (first non-0)
	 * R:0, W:0, B:40, WB:100
	 * bal= 60 = loss 80 => WB
	 * R:0, W:0, B:40, WB:20
	 * bal= 5 = loss 55 => WB, B
	 * R:0, W:0, B:5, WB:0
	 *
	 *
	 * @return
	 */
	private function testWinLossBigWallet(){
		//build mock big wallet

		$bigWallet=$this->bigWallet;
		$subWalletId=$this->subWalletId;

		//($actually, $expectd, $testName, $notes = null)
		$balance=170.0;
		$changed=$this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
	 	// * bal= 170 , win 20 => W
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 100.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 20.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

		// * bal= 140 = loss 30 => R
		$balance=140.0;
		$changed=$this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 70.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 20.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

	 	// * bal= 190 = win 50 => WB
		$balance=190.0;
		$changed= $this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $this->subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 70.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 50.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 70.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

		//bal= 40 = loss 150=> R, BW, B
		$balance=40.0;
		$changed= $this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $this->subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 40.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

		//bal= 140 = win 100 => WB (first non-0)
		$balance=140.0;
		$changed= $this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $this->subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 40.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 100.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

		//bal= 60 = loss 80 => WB
		$balance=60.0;
		$changed= $this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $this->subWalletId, $balance);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 40.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 20.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

		//bal= 5 = loss 55 => WB, B
		$balance=5.0;
		$changed= $this->wallet_model->refreshSubWalletByBigWallet($bigWallet, $this->subWalletId, 5);
		$this->wallet_model->totalBigWallet($bigWallet);
		$this->utils->debug_log('return big wallet', $bigWallet, 'subWalletId', $subWalletId, 'changed',$changed);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['real']), 0.0, 'test real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_real']), 0.0, 'test win_real '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['bonus']), 5.0, 'test bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['win_bonus']), 0.0, 'test win_bonus '.$balance);
		$this->test(doubleval($bigWallet['sub'][$subWalletId]['total_nofrozen']), $balance, 'test total_nofrozen '.$balance);

	}


	private function testWinPlayerBigWallet(){
		$playerId=112;
		$subWalletId=7;

		$this->wallet_model->refreshSubWalletOnBigWallet($playerId, $subWalletId, 100);

	}

	private function testDepositOnly(){
		$result=false;

		$player=$this->getFirstPlayer();
		$paymentAccount=$this->getFirstCollectionAccount();

		$user_id=1;
		$payment_account_id=$paymentAccount->id;
		$systemId=$paymentAccount->external_system_id;
		$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;
		$currency=$this->utils->getDefaultCurrency();
		$player_promo_id=null;
		$playerId=$player->playerId;
		$playerName=$player->username;
		$date=$this->utils->getNowForMysql();
		$subwallet=null;
		$notes='testing deposit only';
		$reason='for testing';
		$show_reason=true;
		$amount=100;
		$ipAddress = '127.0.0.1';
		$dwLocation='local';
		$transactionCode = $this->wallet_model->getRandomTransactionCode();
		$playerAccount = $this->wallet_model->getMainWalletBy($playerId);
		$playerAccountId = $playerAccount->playerAccountId;
		$enabled_withdrawal = $player->enabled_withdrawal;
		$playerMainWalletBalance = $playerAccount->totalBalanceAmount;
		$reason='test withdraw';
		$bankList=$this->playerbankdetails->getPlayerWithdrawalBankList($playerId);
		$playerBankDetailsId=$bankList[0]['playerBankDetailsId'];

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);

		//deposit
		$saleOrderId = $this->sale_order->createSaleOrder($systemId, $playerId, $amount, $paymentKind,
			Sale_order::STATUS_PROCESSING, $notes, $player_promo_id, $currency, $payment_account_id, $date, null,
			$subwallet);
		$this->utils->debug_log('saleOrderId', $saleOrderId);
		$success = !empty($saleOrderId);

		$this->test($success, true, 'create deposit '.$saleOrderId, 'create deposit sale order and transaction '.$amount, $result);
		// if(!$result){ return $this->returnText('failed');}

		$success = $this->sale_order->approveSaleOrder($saleOrderId, $reason, $show_reason);

		$this->test($success, true, 'create deposit', 'create deposit sale order and transaction'.$amount, $result);
		// if(!$result){ return $this->returnText('failed');}

		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']+$amount , 'compare big wallet', 'compare big wallet '.$amount, $result);

		//transfer to subwallet
		// $transfer_to = $subwallet;
		// if ($this->utils->existsSubWallet($transfer_to)) {
		// 	$transfer_from = Wallet_model::MAIN_WALLET_ID;
		// 	$rlt = $this->utils->transferWallet($playerId, $username, $transfer_from, $transfer_to, $amount, $userId);

		// 	$this->utils->debug_log('transfer to subwallet failed', $playerId);
		// }

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		//transfer main to sub
		$transfer_from=Wallet_model::MAIN_WALLET_ID;
		$transfer_to=$this->subWalletId;
		$success=$this->utils->transferWallet($playerId, $playerName, $transfer_from, $transfer_to, $amount, $user_id);
		$this->test($success, true, 'transfer main to sub', 'transfer main to sub '.$amount, $result);

		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']-$amount , 'compare big wallet for real', 'compare big wallet for real '.$amount, $result);
		$this->test($afterBigWallet['sub'][$this->subWalletId]['real'], $beforeBigWallet['sub'][$this->subWalletId]['real']+$amount , 'compare big wallet for sub real', 'compare big wallet for  sub real '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		//sub to main
		$transfer_from=$this->subWalletId;
		$transfer_to=Wallet_model::MAIN_WALLET_ID;
		$success=$this->utils->transferWallet($playerId, $playerName, $transfer_from, $transfer_to, $amount, $user_id);
		$this->test($success, true, 'transfer sub to main', 'transfer sub to main '.$amount, $result);

		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']+$amount , 'compare big wallet for real', 'compare big wallet for real '.$amount, $result);
		$this->test($afterBigWallet['sub'][$this->subWalletId]['real'], $beforeBigWallet['sub'][$this->subWalletId]['real']-$amount , 'compare big wallet for sub real', 'compare big wallet for  sub real '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//withdraw
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$walletAccountData = array(
			'playerAccountId' => $playerAccountId,
			'walletType' => Wallet_model::TYPE_MAINWALLET,
			'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $date,
			'transactionType' => 'withdrawal',
			'dwIp' => $ipAddress,
			'dwLocation' => $dwLocation,
			'transactionCode' => $transactionCode,
			'status' => '0',
			'before_balance' => $playerMainWalletBalance,
			'after_balance' => $playerMainWalletBalance - $amount,
			'playerId' => $playerId,
			'notes' => $reason,
		);

		$localBankWithdrawalDetails = array(
			'withdrawalAmount' => $amount,
			'playerBankDetailsId' => $playerBankDetailsId,
			'depositDateTime' => $date,
			'status' => 'active',
		);

		$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
		$success= !!$walletAccountId;
		$this->test($success, true, 'withdraw', 'withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']+$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//decline
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$reason='test decline';
		$showDeclinedReason=true;
		$success = $this->wallet_model->declineWithdrawalRequest($user_id, $walletAccountId, $reason, $showDeclinedReason);
		$this->test($success, true, 'decline withdraw', 'decline withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']-$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//withdraw again
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$walletAccountData = array(
			'playerAccountId' => $playerAccountId,
			'walletType' => Wallet_model::TYPE_MAINWALLET,
			'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $date,
			'transactionType' => 'withdrawal',
			'dwIp' => $ipAddress,
			'dwLocation' => $dwLocation,
			'transactionCode' => $transactionCode,
			'status' => '0',
			'before_balance' => $playerMainWalletBalance,
			'after_balance' => $playerMainWalletBalance - $amount,
			'playerId' => $playerId,
			'notes' => $reason,
		);

		$localBankWithdrawalDetails = array(
			'withdrawalAmount' => $amount,
			'playerBankDetailsId' => $playerBankDetailsId,
			'depositDateTime' => $date,
			'status' => 'active',
		);

		$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
		$success= !!$walletAccountId;
		$this->test($success, true, 'withdraw', 'withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']+$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//approve withdraw
		$reason='test approve withdraw';
		$transaction_fee=null;
		$showRemarksToPlayerForPaid=true;

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$force=true;
		$success = $this->wallet_model->paidWithdrawal($user_id, $walletAccountId, $reason, $transaction_fee, $showRemarksToPlayerForPaid, $force);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen'] - $amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] - $amount , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

	}

	private function testWithdraw(){
		$result=false;

		$player=$this->getFirstPlayer();
		$paymentAccount=$this->getFirstCollectionAccount();

		$user_id=1;
		$payment_account_id=$paymentAccount->id;
		$systemId=$paymentAccount->external_system_id;
		$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;
		$currency=$this->utils->getDefaultCurrency();
		$player_promo_id=null;
		$playerId=$player->playerId;

		$date=$this->utils->getNowForMysql();
		$player=$this->getFirstPlayer();
		$paymentAccount=$this->getFirstCollectionAccount();
		$ipAddress = '127.0.0.1';
		$dwLocation='local';
		$transactionCode = $this->wallet_model->getRandomTransactionCode();
		$playerAccount = $this->wallet_model->getMainWalletBy($playerId);
		$playerAccountId = $playerAccount->playerAccountId;
		$enabled_withdrawal = $player->enabled_withdrawal;
		$playerMainWalletBalance = $playerAccount->totalBalanceAmount;
		$reason='test withdraw';
		$bankList=$this->playerbankdetails->getPlayerWithdrawalBankList($playerId);
		$playerBankDetailsId=$bankList[0]['playerBankDetailsId'];

		$amount=120;
		//withdraw
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$walletAccountData = array(
			'playerAccountId' => $playerAccountId,
			'walletType' => Wallet_model::TYPE_MAINWALLET,
			'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $date,
			'transactionType' => 'withdrawal',
			'dwIp' => $ipAddress,
			'dwLocation' => $dwLocation,
			'transactionCode' => $transactionCode,
			'status' => '0',
			'before_balance' => $playerMainWalletBalance,
			'after_balance' => $playerMainWalletBalance - $amount,
			'playerId' => $playerId,
			'notes' => $reason,
		);

		$localBankWithdrawalDetails = array(
			'withdrawalAmount' => $amount,
			'playerBankDetailsId' => $playerBankDetailsId,
			'depositDateTime' => $date,
			'status' => 'active',
		);

		$this->promorules->startTrans();
		$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
		$success= !!$walletAccountId;
		if($success){
			$success=$this->promorules->endTransWithSucc();
		}else{
			$this->promorules->rollbackTrans();
		}

		$this->test($success, true, 'withdraw', 'withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);

		$this->utils->debug_log('before',$beforeBigWallet, 'after', $afterBigWallet);

		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']+$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		// $this->test($afterBigWallet['main']['frozen_detail']['real'], 100 , 'compare big wallet for frozen', 'compare big wallet for frozen 100', $result);
		// $this->test($afterBigWallet['main']['frozen_detail']['real_for_bonus'], 20 , 'compare big wallet for frozen', 'compare big wallet for frozen 20', $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);


	}

	private function testAutoDepositPromotion(){

		$result=false;

		$player=$this->getFirstPlayer();
		$paymentAccount=$this->getFirstCollectionAccount();

		$user_id=1;
		$payment_account_id=$paymentAccount->id;
		$systemId=$paymentAccount->external_system_id;
		$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;
		$currency=$this->utils->getDefaultCurrency();
		$player_promo_id=null;
		$playerId=$player->playerId;
		$playerName=$player->username;
		$date=$this->utils->getNowForMysql();
		$subwallet=null;
		$notes='testing deposit promotion';
		$reason='for testing';
		$show_reason=true;
		$amount=100;
		$ipAddress = '127.0.0.1';
		$dwLocation='local';
		$transactionCode = $this->wallet_model->getRandomTransactionCode();
		$playerAccount = $this->wallet_model->getMainWalletBy($playerId);
		$playerAccountId = $playerAccount->playerAccountId;
		$enabled_withdrawal = $player->enabled_withdrawal;
		$playerMainWalletBalance = $playerAccount->totalBalanceAmount;
		$reason='test withdraw';
		$bankList=$this->playerbankdetails->getPlayerWithdrawalBankList($playerId);
		$playerBankDetailsId=$bankList[0]['playerBankDetailsId'];

		$deposit_enabled=true;

		//deposit
		if($deposit_enabled){
			$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
			$saleOrderId = $this->sale_order->createSaleOrder($systemId, $playerId, $amount, $paymentKind,
				Sale_order::STATUS_PROCESSING, $notes, $player_promo_id, $currency, $payment_account_id, $date, null,
				$subwallet);
			$this->utils->debug_log('saleOrderId', $saleOrderId);
			$success = !empty($saleOrderId);
			$this->test($success, true, 'create deposit '.$saleOrderId, 'create deposit sale order and transaction '.$amount, $result);
			$success = $this->sale_order->approveSaleOrder($saleOrderId, $reason, $show_reason);
			$this->test($success, true, 'create deposit', 'create deposit sale order and transaction'.$amount, $result);

			$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
			$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']+$amount , 'compare big wallet', 'compare big wallet '.$amount, $result);
		}

		//add promotion
		$promoCmsSettingId=26;
		$promorule = $this->promorules->getPromoruleByPromoCms($promoCmsSettingId);
		$promorulesId = $promorule['promorulesId'];
		$approveProcessPendingFlag = true;
		$bonus_amount=25;

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->promorules->startTrans();
		list($success, $message) = $this->promorules->checkAndProcessPromotion(
			$playerId, $promorule, $promoCmsSettingId, $approveProcessPendingFlag);
		if($success){
			$success=$this->promorules->endTransWithSucc();
		}else{
			$this->promorules->rollbackTrans();
		}
		$this->test($success, true, 'process promotion '.$promorulesId, 'process promotion '.$message, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		//should move real to real_bonus
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real'] - $amount , 'compare big wallet', 'compare big wallet '.$amount, $result);
		$this->test($afterBigWallet['main']['real_for_bonus'], $beforeBigWallet['main']['real_for_bonus'] + $amount , 'compare big wallet', 'compare big wallet '.$amount, $result);
		// $this->test($afterBigWallet['main']['bonus'], $beforeBigWallet['main']['bonus'] - $bonus_amount , 'compare big wallet', 'compare big wallet '.$bonus_amount, $result);
		$this->test($afterBigWallet['sub'][$this->subWalletId]['bonus'], $beforeBigWallet['sub'][$this->subWalletId]['bonus'] + $bonus_amount , 'compare big wallet bonus', 'compare big wallet bonus '.$bonus_amount, $result);

		return;

		//transfer to/from subwallet
		//transfer main to sub
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$transfer_from=Wallet_model::MAIN_WALLET_ID;
		$transfer_to=$this->subWalletId;
		$success=$this->utils->transferWallet($playerId, $playerName, $transfer_from, $transfer_to, $amount, $user_id);
		$this->test($success, true, 'transfer main to sub', 'transfer main to sub '.$amount, $result);

		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']-$amount , 'compare big wallet for real', 'compare big wallet for real '.$amount, $result);
		$this->test($afterBigWallet['sub'][$this->subWalletId]['real'], $beforeBigWallet['sub'][$this->subWalletId]['real']+$amount , 'compare big wallet for sub real', 'compare big wallet for  sub real '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		//sub to main
		$transfer_from=$this->subWalletId;
		$transfer_to=Wallet_model::MAIN_WALLET_ID;
		$success=$this->utils->transferWallet($playerId, $playerName, $transfer_from, $transfer_to, $amount, $user_id);
		$this->test($success, true, 'transfer sub to main', 'transfer sub to main '.$amount, $result);

		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['real'], $beforeBigWallet['main']['real']+$amount , 'compare big wallet for real', 'compare big wallet for real '.$amount, $result);
		$this->test($afterBigWallet['sub'][$this->subWalletId]['real'], $beforeBigWallet['sub'][$this->subWalletId]['real']-$amount , 'compare big wallet for sub real', 'compare big wallet for  sub real '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		return;

		//withdraw
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$walletAccountData = array(
			'playerAccountId' => $playerAccountId,
			'walletType' => Wallet_model::TYPE_MAINWALLET,
			'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $date,
			'transactionType' => 'withdrawal',
			'dwIp' => $ipAddress,
			'dwLocation' => $dwLocation,
			'transactionCode' => $transactionCode,
			'status' => '0',
			'before_balance' => $playerMainWalletBalance,
			'after_balance' => $playerMainWalletBalance - $amount,
			'playerId' => $playerId,
			'notes' => $reason,
		);

		$localBankWithdrawalDetails = array(
			'withdrawalAmount' => $amount,
			'playerBankDetailsId' => $playerBankDetailsId,
			'depositDateTime' => $date,
			'status' => 'active',
		);

		$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
		$success= !!$walletAccountId;
		$this->test($success, true, 'withdraw', 'withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']+$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//decline
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$reason='test decline';
		$showDeclinedReason=true;
		$success = $this->wallet_model->declineWithdrawalRequest($user_id, $walletAccountId, $reason, $showDeclinedReason);
		$this->test($success, true, 'decline withdraw', 'decline withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']-$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//withdraw again
		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$walletAccountData = array(
			'playerAccountId' => $playerAccountId,
			'walletType' => Wallet_model::TYPE_MAINWALLET,
			'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $date,
			'transactionType' => 'withdrawal',
			'dwIp' => $ipAddress,
			'dwLocation' => $dwLocation,
			'transactionCode' => $transactionCode,
			'status' => '0',
			'before_balance' => $playerMainWalletBalance,
			'after_balance' => $playerMainWalletBalance - $amount,
			'playerId' => $playerId,
			'notes' => $reason,
		);

		$localBankWithdrawalDetails = array(
			'withdrawalAmount' => $amount,
			'playerBankDetailsId' => $playerBankDetailsId,
			'depositDateTime' => $date,
			'status' => 'active',
		);

		$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
		$success= !!$walletAccountId;
		$this->test($success, true, 'withdraw', 'withdraw '.$amount, $result);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen']+$amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

		//approve withdraw
		$reason='test approve withdraw';
		$transaction_fee=null;
		$showRemarksToPlayerForPaid=true;

		$beforeBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$force=true;
		$success = $this->wallet_model->paidWithdrawal($user_id, $walletAccountId, $reason, $transaction_fee, $showRemarksToPlayerForPaid, $force);
		$afterBigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);
		$this->test($afterBigWallet['main']['frozen'], $beforeBigWallet['main']['frozen'] - $amount , 'compare big wallet for frozen', 'compare big wallet for frozen '.$amount, $result);
		$this->test($afterBigWallet['total'], $beforeBigWallet['total'] - $amount , 'compare big wallet for total', 'compare big wallet for total '.$amount, $result);

	}

}
