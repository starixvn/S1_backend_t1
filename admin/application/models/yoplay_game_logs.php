<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Yoplay_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "yoplay_game_logs";

	public function getAvailableRows($rows) {
		$arr = array();

		foreach ($rows as $row) {
			$uniqueId = $row['@attributes']['billno'];
			$arr[] = $uniqueId;			
		}

		$this->db->select('billno')->from($this->tableName)->where_in('billno', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->billno;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				$uniqueId = $row['@attributes']['billno'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

	public function syncGameLogs($data) {
		$id=$this->getIdByUniqueid($data['uniqueid']);
		if(!empty($id)){
			$settledDate = $this->getSettledDateByUniqueid($data['uniqueid']);
			if($settledDate == null){
				return $this->updateGameLog($id, $data);
			}
		}else{
			return $this->insertGameLogs($data);
		}
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		
		$select = 'yoplay_game_logs.playerid,
				  yoplay_game_logs.username,
				  yoplay_game_logs.external_uniqueid,
				  yoplay_game_logs.billtime AS game_date,
				  yoplay_game_logs.gametype AS game_code,
				  yoplay_game_logs.response_result_id,
				  yoplay_game_logs.account AS bet_amount,
				  yoplay_game_logs.valid_account AS real_bet,
				  yoplay_game_logs.cus_account AS result_amount,
				  yoplay_game_logs.billno AS RoundID,
				  game_description.id  AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select);
		$this->db->from('yoplay_game_logs');
		$this->db->join('game_description', 'yoplay_game_logs.gametype = game_description.game_code AND game_description.game_platform_id = "'.YOPLAY_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('`yoplay_game_logs`.`billtime` >= "'.$dateFrom.'" AND `yoplay_game_logs`.`billtime` <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();

	}

}

///END OF FILE///////