<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetbbtech_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ebetbbtech_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('remoteTranId')->from($this->tableName)->where_in('remoteTranId', array_column($rows, 'remoteTranId'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'remoteTranId');
			$availableRows = array();
			foreach ($rows as $row) {
				$remoteTranId = $row['remoteTranId'];
				if (!in_array($remoteTranId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}
	public function getPayOutByRoundId($roundId,$type = "WIN") {
		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->where('trnType', $type);
		$this->db->where('roundId', $roundId);
		// $this->db->distinct();
		$query = $this->db->get();

		if (!$query->row_array()) {
			return false;
		} else {

			// return $query->row_array();

			$payout_amount = 0;
			$gameLogs =  $query->result_array();
			if (!empty($gameLogs)) {
				foreach($gameLogs as $log) {
					$payout_amount = $payout_amount + $log['amount'];
				}
			}
			$result['amount'] = $payout_amount;
			return $result;
		}
	}

	public function getGameLogStatistics($dateFrom, $dateTo,$type = "BET") {

		$select = 'ebetbbtech_game_logs.PlayerId,
				  ebetbbtech_game_logs.UserName,
				  ebetbbtech_game_logs.external_uniqueid,
				  ebetbbtech_game_logs.transactionDate AS game_date,
				  ebetbbtech_game_logs.gameId AS game_code,
				  ebetbbtech_game_logs.response_result_id,
				  ebetbbtech_game_logs.amount,
				  ebetbbtech_game_logs.roundId,
				  ebetbbtech_game_logs.trnType,
                  ebetbbtech_game_logs.remoteTranId,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('ebetbbtech_game_logs');
		$this->db->join('game_description', 'ebetbbtech_game_logs.gameId = game_description.game_code AND game_description.game_platform_id = "'.EBET_BBTECH_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
	#	$this->db->where('ebetbbtech_game_logs.transactionDate >= "'.$dateFrom.'" AND ebetbbtech_game_logs.transactionDate <= "' . $dateTo . '"');
		$this->db->where('ebetbbtech_game_logs.trnType = "'.$type.'" ');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////