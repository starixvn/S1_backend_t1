<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Golden_race_transactions extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}
	const IS_FAILED = 1;
	const CLOSED = 1;
	protected $tableName = "golden_race_transactions";

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
	public function isTransactionIdAlreadyExists($transaction_id, $action) {
		$this->db->from($this->tableName)
			->where('transactionId', $transaction_id)
				->where('action', $action);
		return $this->runExistsResult();
	}

	/**
	 * overview : check if game cylce already exist on bet
	 *
	 * @param  string		$game_cycle
	 *
	 * @return boolean
	 */
	public function isGameCycleIdAlreadyExists($game_cycle) {
		$this->db->from($this->tableName)
			->where('gameCycle', $game_cycle)
				->where('action', 'debit');
		return $this->runExistsResult();
	}

	/**
	 * overview : check if game cylce already closed
	 *
	 * @param  string		$game_cycle
	 *
	 * @return boolean
	 */
	public function isGameCycleClosed($game_cycle) {
		$this->db->from($this->tableName)
			->where('gameCycle', $game_cycle)
				->where('gameCycleClosed', self::CLOSED);
		return $this->runExistsResult();
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