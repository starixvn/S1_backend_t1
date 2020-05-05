<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Agin_game_logs_result extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "agin_game_logs_result";

	public function insertGameResultLogs($data) {
		return $this->insertData($this->tableName, $data);
	}

	/**
	 * overview : update game result logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	public function updateGameResultLogs($data) {
		$this->db->where('game_code', $data['game_code']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : check if game_code already exist
	 *
	 * @param  int		$game_code
	 *
	 * @return boolean
	 */
	public function isRowIdAlreadyExists($game_code) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $game_code));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get agin game logs result by game code
	 *
	 * @param  int		$game_code
	 *
	 * @return row
	 */
	public function getGameResultByGameCode($game_code) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $game_code));
		// return $this->runMultipleRowArray();
		return $this->getOneRow($qry);
	}
	public function getGameLogStatistics($dateFrom, $dateTo) {	
	}
}

///END OF FILE///////
