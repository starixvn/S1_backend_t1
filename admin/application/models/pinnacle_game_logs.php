<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Pinnacle_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "pinnacle_game_logs";

	public function getSettledDateByUniqueid($uniqueid){
		$this->db->select('settledDate')->from($this->tableName)->where('uniqueid', $uniqueid);
		return $this->runOneRowOneField('settledDate');
	}

	public function syncGameLogs($data) {
		$id=$this->getIdByUniqueid($data['uniqueid']);

		if(!empty($id)){
			$settledDate = $this->getSettledDateByUniqueid($data['uniqueid']);
			// fix settled game date
			// only update SETTLED, CANCELLED or DELETED (not include open bets)
			if(!empty($data['settledDate'])){
				return $this->updateGameLog($id, $data);
			}
		}else{
			return $this->insertGameLogs($data);
		}
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		
		$select = 'pinnacle_game_logs.id as id,
				  pinnacle_game_logs.playerId,
				  pinnacle_game_logs.userName,
				  pinnacle_game_logs.external_uniqueid,
				  pinnacle_game_logs.wagerDateFm AS game_date,
				  IFNULL(pinnacle_game_logs.settledDate,pinnacle_game_logs.wagerDateFm ) AS settled_date,
				  pinnacle_game_logs.betType,
				  pinnacle_game_logs.sport AS game_code,
				  pinnacle_game_logs.response_result_id,
				  pinnacle_game_logs.stake AS bet_amount,
				  pinnacle_game_logs.winLoss AS result_amount,
				  pinnacle_game_logs.wagerId AS RoundID,
				  pinnacle_game_logs.status,
				  pinnacle_game_logs.odds,
				  pinnacle_game_logs.eventName,
				  pinnacle_game_logs.inPlay,
				  pinnacle_game_logs.handicap,
				  pinnacle_game_logs.selection,
				  pinnacle_game_logs.inplayScore,
				  pinnacle_game_logs.parlaySelections,
				  pinnacle_game_logs.league,
				  pinnacle_game_logs.oddsFormat,
				  pinnacle_game_logs.result,
				  game_description.id  AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select);
		$this->db->from('pinnacle_game_logs');
		$this->db->join('game_description', 'pinnacle_game_logs.sport = game_description.game_code AND game_description.game_platform_id = "'.PINNACLE_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('IFNULL(`pinnacle_game_logs`.`settledDate`,`pinnacle_game_logs`.`wagerDateFm`) >= "'.$dateFrom.'" AND IFNULL(`pinnacle_game_logs`.`settledDate`,`pinnacle_game_logs`.`wagerDateFm`) <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();

	}

}

///END OF FILE///////