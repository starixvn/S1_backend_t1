<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class isbseamless_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "isbseamless_game_logs";

	public function isRowByTransactionId($transId) {
		$qry = $this->db->get_where($this->tableName, array('transaction_id' => $transId))->row_array();
		return $qry;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

	}

}

///END OF FILE///////