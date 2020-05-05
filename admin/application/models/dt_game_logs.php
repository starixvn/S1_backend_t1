<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Dt_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "dt_game_logs";

	public function getAvailableRows($rows) {
		if(!isset($rows[0])){
			$rows = array($rows);
		}

		$this->db->select('dt_id')->from($this->tableName)->where_in('dt_id', array_column($rows, 'id'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'dt_id');
			$availableRows = array();
			foreach ($rows as $row) {
				$transNo = $row['id'];
				if (!in_array($transNo, $existsId)) {
					$availableRows[] = $row;
				}								
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		
		$select = 'dt_game_logs.playerId,
				  dt_game_logs.username,
				  dt_game_logs.external_uniqueid,
				  dt_game_logs.createTime AS game_date,
				  dt_game_logs.gameCode AS game_code,
				  dt_game_logs.response_result_id,
				  dt_game_logs.betWins AS result_amount,
				  dt_game_logs.betPrice AS BetAmount,
				  dt_game_logs.creditAfter,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('dt_game_logs');
		$this->db->join('game_description', 'dt_game_logs.gameCode = game_description.game_code AND game_description.game_platform_id = "'.DT_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->join('game_provider_auth', 'dt_game_logs.username = game_provider_auth.login_name AND game_provider_auth.game_provider_id = "'.DT_API.'"', 'LEFT');
		$this->db->where('dt_game_logs.createTime >= "'.$dateFrom.'" AND dt_game_logs.createTime <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();
	}

}

///END OF FILE///////