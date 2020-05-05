<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }

class Withdrawal_condition_checker extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model(array('daily_balance', 'player', 'operatorglobalsettings', 'withdraw_condition'));
	}

	function index() {
		$this->checkPlayerDailyBalanceIsBelowOperatorSetting();
	}

	function checkPlayerDailyBalanceIsBelowOperatorSetting() {
		$players = $this->player->getAllActivePlayer();
		$operatorSetting = $this->operatorglobalsettings->getOperatorGlobalSetting('previous_balance_set_amount');
		foreach ($players as $key) {
			$data = $this->daily_balance->queryPlayerDailyBalance($key['playerId']);
			if ($data) {
				if ($data['balance'] <= $operatorSetting[0]['value']) {
					$this->withdraw_condition->disablePlayerWithdrawalCondition($data['player_id']);
					// $this->player_promo->disablePlayerPromo($data['player_id']);
				}
			}
		}
	}
}
