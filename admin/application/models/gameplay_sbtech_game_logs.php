<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Gameplay_sbtech_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "gameplay_sbtech_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('idx')->from($this->tableName)->where_in('idx', array_column($rows, 'idx'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'idx');
			$availableRows = array();
			foreach ($rows as $row) {
				$idx = $row['idx'];
				if (!in_array($idx, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	/**
	 * overview : check if idx already exist
	 *
	 * @param  int		$idx
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($idx) {
		$qry = $this->db->get_where($this->tableName, array('idx' => $idx));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('idx', $data['idx'])->where('doneTime', NULL);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get opened logs
	 *
	 * @param  string	$startDate,$endDate
	 *
	 * @return array
	 */
	function getOpenLogs($startDate = false,$endDate = false) {
		// return $startDate;
		$this->db->select('*')
			->from($this->tableName)
			->where_in('status',array('Open','Opened'));
		if($startDate) {
			$this->db->where("creationDate >=", $startDate)->where("creationDate <=", $endDate);
		}
		$query = $this->db->get();
		return $query->result_array();
	}


	public function getGameLogStatistics($dateFrom, $dateTo) {	
		$select = 'gameplay_sbtech_game_logs.PlayerId,
				  gameplay_sbtech_game_logs.UserName,
				  gameplay_sbtech_game_logs.external_uniqueid,
				  IFNULL(gameplay_sbtech_game_logs.doneTime,gameplay_sbtech_game_logs.creationDate ) AS game_date,
				  gameplay_sbtech_game_logs.branchName AS game_code,
				  gameplay_sbtech_game_logs.response_result_id,
				  gameplay_sbtech_game_logs.pl AS result_amount,
				  gameplay_sbtech_game_logs.stake AS BetAmount,
				  gameplay_sbtech_game_logs.status,
                  gameplay_sbtech_game_logs.idx,
                  gameplay_sbtech_game_logs.betId,
                  gameplay_sbtech_game_logs.yourBet,
                  gameplay_sbtech_game_logs.parlayChecked,
                  gameplay_sbtech_game_logs.parlays,
                  gameplay_sbtech_game_logs.doneTime,
                  gameplay_sbtech_game_logs.stake,
                  gameplay_sbtech_game_logs.odds,
                  gameplay_sbtech_game_logs.score,
                  gameplay_sbtech_game_logs.liveScore1,
                  gameplay_sbtech_game_logs.liveScore2,
                  gameplay_sbtech_game_logs.leagueName,
                  CONCAT(gameplay_sbtech_game_logs.homeTeam, " vs ", gameplay_sbtech_game_logs.awayTeam ) as match_details,
                  gameplay_sbtech_game_logs.eventTypeName,
                  gameplay_sbtech_game_logs.yourBet as bet_info,
                  gameplay_sbtech_game_logs.betTypeName,
                  gameplay_sbtech_game_logs.rowId,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('gameplay_sbtech_game_logs');
		$this->db->join('game_description', 'gameplay_sbtech_game_logs.branchName = game_description.game_code AND game_description.game_platform_id = "'.GAMEPLAY_SBTECH_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('IFNULL(`gameplay_sbtech_game_logs`.`doneTime`,`gameplay_sbtech_game_logs`.`creationDate`) >= "'.$dateFrom.'" AND IFNULL(`gameplay_sbtech_game_logs`.`doneTime`,`gameplay_sbtech_game_logs`.`creationDate`) <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////