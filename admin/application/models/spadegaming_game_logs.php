<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Spadegaming_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "spadegaming_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('ticketId')->from($this->tableName)->where_in('ticketId', array_column($rows, 'ticketId'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'ticketId');
			$availableRows = array();
			foreach ($rows as $row) {
				$ticketId = $row['ticketId'];
				if (!in_array($ticketId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = 'spadegaming_game_logs.PlayerId,
				  spadegaming_game_logs.UserName,
				  spadegaming_game_logs.external_uniqueid,
				  spadegaming_game_logs.ticketTime AS game_date,
				  spadegaming_game_logs.gameCode AS game_code1,
				  spadegaming_game_logs.response_result_id,
				  spadegaming_game_logs.winLoss AS result_amount,
				  spadegaming_game_logs.betAmount AS BetAmount,
				  spadegaming_game_logs.balance AS after_balance,
				  spadegaming_game_logs.result,
                  spadegaming_game_logs.ticketId,
                  spadegaming_game_logs.roundId as RoundNo,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('spadegaming_game_logs');
		$this->db->join('game_description', 'spadegaming_game_logs.gameCode = game_description.game_code AND game_description.game_platform_id = "'.SPADE_GAMING_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('spadegaming_game_logs.ticketTime >= "'.$dateFrom.'" AND spadegaming_game_logs.ticketTime <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////