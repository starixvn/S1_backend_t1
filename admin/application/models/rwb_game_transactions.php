<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Rwb_game_transactions extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}
	const IS_FAILED = 1;
	protected $tableName = "rwb_game_transactions";

	/**
	 * overview : check if request_id already exist
	 *
	 * @param  int		$request_id
	 *
	 * @return boolean
	 */
	public function isRowIdAlreadyExists($request_id) {
		$qry = $this->db->get_where($this->tableName, array('request_id' => $request_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : check if transaction_id already exist
	 *
	 * @param  int		$transaction_id
	 *
	 * @return boolean
	 */
	public function isTransactionIdAlreadyExists($transaction_id) {
		$qry = $this->db->get_where($this->tableName, array('transaction_id' => $transaction_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get failed transaction
	 *
	 * @param  int		$bet_id
	 *
	 * @return boolean
	 */
	public function getFailedTransaction($bet_id) {
		$qry = $this->db->get_where($this->tableName, array('bet_id' => $bet_id, 'is_failed' => self::IS_FAILED));
		return $this->getOneRow($qry);
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		return null;
	}

}

///END OF FILE///////