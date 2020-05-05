<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetimpt_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ebetimpt_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('betId')->from($this->tableName)->where_in('betId', array_column($rows, 'id'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'betId');
			$availableRows = array();
			foreach ($rows as $row) {
				$betId = $row['id'];
				if (!in_array($betId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}
	public function getPayOutByRoundId($roundId,$type = "WIN") {
		// $this->db->select('*');
		// $this->db->from($this->tableName);
		// $this->db->where('trnType', $type);
		// $this->db->where('roundId', $roundId);
		// $this->db->distinct();
		// $query = $this->db->get();
		// if (!$query->row_array()) {
		// 	return false;
		// } else {
		// 	return $query->row_array();
		// }
	}

	public function getGameLogStatistics($dateFrom, $dateTo,$type = "BET") {

		$select = 'ebetimpt_game_logs.PlayerId,
				  ebetimpt_game_logs.UserName,
				  ebetimpt_game_logs.external_uniqueid,
				  ebetimpt_game_logs.gameDate AS game_date,
				  ebetimpt_game_logs.gameCode1 AS game_code,
				  ebetimpt_game_logs.response_result_id,
				  ebetimpt_game_logs.bet,
				  ebetimpt_game_logs.betId,
				  ebetimpt_game_logs.endAmount AS after_balance,
				  ebetimpt_game_logs.win,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.external_game_id,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('ebetimpt_game_logs');
		$this->db->join('game_description', 'ebetimpt_game_logs.gameCode1 = game_description.external_game_id AND game_description.game_platform_id = "'.EBET_IMPT_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('ebetimpt_game_logs.gameDate >= "'.$dateFrom.'" AND ebetimpt_game_logs.gameDate <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////