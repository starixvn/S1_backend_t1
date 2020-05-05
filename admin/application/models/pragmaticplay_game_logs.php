<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Pragmaticplay_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "pragmaticplay_game_logs";

	public function getAvailableRows($rows) {

		if(!isset($rows)){
			$rows = array($rows);
		}

		$ids =  array();
		foreach($rows as $nrow){
			array_push($ids,$nrow[0].$nrow[2].$nrow[3].$nrow[6]);
		}
		$existsRow = array();
		if(!empty($ids)){
			$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $ids);
			$existsRow = $this->runMultipleRowArray();
		}

		$availableRows = null;

		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'external_uniqueid');

			$availableRows = array();

			foreach ($rows as $row) {
				$ext_id = $row[0].$row[2].$row[3].$row[6];
				if (!in_array($ext_id, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = '
				  pragmaticplay_game_logs.id as id,
				  pragmaticplay_game_logs.SbePlayerId,
				  pragmaticplay_game_logs.UserName,
				  pragmaticplay_game_logs.related_uniqueid as external_uniqueid,
				  pragmaticplay_game_logs.timestamp AS game_date,
				  pragmaticplay_game_logs.gameID AS game_code,
                  pragmaticplay_game_logs.response_result_id,
                  pragmaticplay_game_logs.amount,
				  pragmaticplay_game_logs.type,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';
		$this->db->select($select,false);
		$this->db->from('pragmaticplay_game_logs');
		$this->db->join('game_description', 'pragmaticplay_game_logs.gameID = game_description.game_code AND game_description.game_platform_id = "'.PRAGMATICPLAY_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('pragmaticplay_game_logs.timestamp >= "'.$dateFrom.'" AND pragmaticplay_game_logs.timestamp <= "' . $dateTo . '"');
		// $this->db->group_by('pragmaticplay_game_logs.related_uniqueid');
		$qobj = $this->db->get();

		return $qobj->result_array();

	}

}

///END OF FILE///////