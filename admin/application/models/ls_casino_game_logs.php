<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ls_casino_game_logs extends Base_game_logs_model {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "ls_casino_game_logs";

	public function getGameLogStatistics($dateFrom, $dateTo) {}

	public function checkGameRecordIsExist($transId, $roundId, $userid, $action) {
		$this->db->select("id");
		$this->db->where("trans_id", $transId);
		$this->db->where("round_id", $roundId);
		$this->db->where("user_id", $userid);
		$this->db->where("action", $action);
		return $this->db->get($this->tableName)->row_array();
	}

	public function getDebitRoundKey($roundKey) {
		$this->db->where("round_key", $roundKey);
		$this->db->where("action", 'debitUser');
		return $this->db->get($this->tableName)->row_array();
	}



}
///END OF FILE///////
