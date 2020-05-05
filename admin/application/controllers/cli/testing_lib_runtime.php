<?php

require_once dirname(__FILE__) . '/base_testing.php';
require_once dirname(__FILE__) . '/../../libraries/runtime.php';

class Testing_lib_runtime extends BaseTesting {

	private $runtime = null;
	private $playerId = 112;
	private $promoruleId = 49;
	private $promorule = null;
	private $playerBonusAmount = 200;

	public function init() {
		$this->load->model(array('promorules'));
		$this->promorule = $this->promorules->getPromorule($this->promoruleId);
		$this->runtime = Runtime::getRuntime($this->playerId, $this->promorule, $this->playerBonusAmount);

		// $this->load->library('utils');
		$this->test($this->runtime != null, true, 'init runtime');
	}

	public function testAll() {
		$this->init();
		// $this->testGetIpCity();
		// $this->testGetIpCityAndCountry();
		// $this->testLoadSystem();
		// $this->testTimezone();
		// $this->testSyncCurrentExternalSystem();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testBelongToAff() {
		$js = <<<EOD
var from_datetime=PHP.runtime.belong_to_aff('');
from_datetime;
EOD;
		$from_datetime = $this->runtime->runjs($js);

	}

	private function testPrepare() {
		$js = <<<EOD
var from_datetime=PHP.runtime.get_from_datetime(['last_withdraw','last_same_promo','player_reg_date']);
from_datetime;
EOD;
		$from_datetime = $this->runtime->runjs($js);
		$this->utils->debug_log('from_datetime', $from_datetime);

		$js = <<<EOD
var to_datetime=PHP.runtime.get_to_datetime(['now']);
to_datetime;
EOD;

		$to_datetime = $this->runtime->runjs($js);
		$this->utils->debug_log('to_datetime', $to_datetime);

		$js = <<<EOD
from_datetime='2015-06-01 00:00:00';
to_datetime='2015-12-31 00:00:00';
var result_amount=PHP.runtime.get_game_result_amount(from_datetime,to_datetime);
result_amount;
EOD;
		$result_amount = $this->runtime->runjs($js);
		$this->utils->debug_log('result_amount', $result_amount);

		$js = <<<EOD
from_datetime='2015-06-01 00:00:00';
to_datetime='2015-12-31 00:00:00';
var betting_amount=PHP.runtime.get_game_betting_amount(from_datetime,to_datetime);
betting_amount;
EOD;
		$betting_amount = $this->runtime->runjs($js);
		$this->utils->debug_log('betting_amount', $betting_amount);

		$js = <<<EOD
var total_balance=PHP.runtime.current_player_total_balance();
total_balance;
EOD;
		$total_balance = $this->runtime->runjs($js);
		$this->utils->debug_log('total_balance', $total_balance);

		$js = <<<EOD
from_datetime='2015-06-01 00:00:00';
to_datetime='2016-12-31 00:00:00';
var deposit=PHP.runtime.sum_deposit_amount(from_datetime,to_datetime,100);
deposit;
EOD;
		$deposit = $this->runtime->runjs($js);
		$this->utils->debug_log('deposit', $deposit);

		$js = <<<EOD
var today_bonus_amount=PHP.runtime.sum_bonus_amount_today();
today_bonus_amount;
EOD;
		$today_bonus_amount = $this->runtime->runjs($js);
		$this->utils->debug_log('today_bonus_amount', $today_bonus_amount);

		$js = <<<EOD
var playerBonusAmount=PHP.runtime.playerBonusAmount;
playerBonusAmount;
EOD;
		$playerBonusAmount = $this->runtime->runjs($js);
		$this->utils->debug_log('playerBonusAmount', $playerBonusAmount);

	}

	public function testRecuse() {

		$js = <<<EOD
//resuce
var bonus_amount=0;
var from_datetime=PHP.runtime.get_from_datetime(['last_withdraw','last_same_promo','player_reg_date']);
var to_datetime=PHP.runtime.get_to_datetime(['now']);
var result_amount=PHP.runtime.get_game_result_amount(from_datetime,to_datetime);
var total_balance=PHP.runtime.current_player_total_balance();

var max_bonus_today=3000;
var min_balance=5;

PHP.runtime.debug_log(from_datetime+' to '+to_datetime+', result_amount:'+result_amount+', total_balance:'+total_balance);

if(total_balance<min_balance && result_amount<0){
	// result_amount=Math.abs(result_amount);

	var deposit_amount=PHP.runtime.sum_deposit_amount(from_datetime,to_datetime,200);

PHP.runtime.debug_log('deposit_amount:'+deposit_amount);

	var rate=0;
	if(deposit_amount>=200 && deposit_amount<1000){
		rate=0.1;
	}else if(deposit_amount>=1000 && deposit_amount<5000){
		rate=0.11;
	}else if(deposit_amount>=5000){
		rate=0.12;
	}

PHP.runtime.debug_log('rate:'+rate);

	if(rate>0){
		bonus_amount=deposit_amount*rate;

		var today_bonus_amount=PHP.runtime.sum_bonus_amount_today();
		if(today_bonus_amount+bonus_amount>max_bonus_today){
			bonus_amount=max_bonus_today-today_bonus_amount;
			if(bonus_amount<=0){
				bonus_amount=0;
			}
		}

	}
}

PHP.runtime.debug_log('bonus_amount:'+bonus_amount);

bonus_amount;
EOD;

		$bonus_amount = $this->runtime->runjs($js);
		$this->utils->debug_log('bonus_amount', $bonus_amount);

	}

	public function sampleFunc($param1) {
		return $param1 . ' get';
	}

	public function testV8js() {
		$v8 = new V8Js();
		$v8->prop1 = array('prop1' => 'val');
		$v8->obj = $this;
		$JS = <<< EOT
len = 'Hello' + ' ' + 'World!' +PHP.prop1['prop1']+ PHP.obj.sampleFunc('xxx');
len;
EOT;

		$rlt = 'Hello World!valxxx get';
		try {
			$str = $v8->executeString($JS, 'basic.js');
			$this->utils->debug_log($str);
			$this->test($str, $rlt, 'test v8js');
		} catch (V8JsException $e) {
			$this->utils->debug_log($e);
		}

	}
}
