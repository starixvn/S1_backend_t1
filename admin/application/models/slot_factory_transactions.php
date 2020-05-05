<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Slot_factory_transactions extends Base_game_logs_model {
	
	function __construct() {
		parent::__construct();
	}

	protected $tableName = "slot_factory_transactions";

	public function isTransactionIdAlreadyExists($transaction_id) {

		$this->db->from($this->tableName)
			->where('transactionId', $transaction_id);

		return $this->runExistsResult();

	}

	public function insertTransaction($data) {
    	// print_r($data);exit();
       $this->db->insert($this->tableName,$data);

       return $this->db->affected_rows();
    }

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return null;
	}


}