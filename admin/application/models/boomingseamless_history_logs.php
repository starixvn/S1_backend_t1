<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Boomingseamless_history_logs extends Base_game_logs_model {

	const DO_WALLET_ADDITION_FROM_CALLBACK = 'DO_WALLET_ADDITION_FROM_CALLBACK';
	const DO_WALLET_DEDUCTION_FROM_CALLBACK = 'DO_WALLET_DEDUCTION_FROM_CALLBACK';
	const DO_WALLET_ADDITION_FROM_ROLLBACK = 'DO_WALLET_ADDITION_FROM_ROLLBACK';
	const DO_WALLET_DEDUCTION_FROM_ROLLBACK = 'DO_WALLET_DEDUCTION_FROM_ROLLBACK';
	const HISTORY_WALLET_DO_PLAYER_BET = 'HISTORY_WALLET_DO_PLAYER_BET';
	const HISTORY_WALLET_DO_PLAYER_CANCEL_BET = 'HISTORY_WALLET_DO_PLAYER_CANCEL_BET';

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "boomingseamless_history_logs";

	public function getGameLogStatistics($dateFrom, $dateTo) {
	}
	
	public function updateGameLogsByExternalUniqueId($external_uniqueid, $data) {
		$this->db->where('external_uniqueid', $external_uniqueid);
		$this->db->set($data);
		return $this->runAnyUpdate($this->tableName);
	}

}

///END OF FILE///////
