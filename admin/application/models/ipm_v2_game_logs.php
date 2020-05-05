<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ipm_v2_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ipm_v2_game_logs";

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['BetId'];
			$arr[] = $uniqueId;			
		}

		$this->db->select('BetId')->from($this->tableName)->where_in('BetId', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->BetId;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				$uniqueId = $row['BetId'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
		return null; 
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
		
		$select = 'ipm_v2_game_logs.PlayerId,
				  ipm_v2_game_logs.external_uniqueid,
				  ipm_v2_game_logs.WagerCreationDateTime  AS game_date,
				  ipm_v2_game_logs.GameID AS game_code,
				  ipm_v2_game_logs.response_result_id,
				  ipm_v2_game_logs.PayoutAmount AS payout_amount,
				  ipm_v2_game_logs.StakeAmount AS bet_amount,
				  ipm_v2_game_logs.WinLoss AS result_amount,
				  ipm_v2_game_logs.Platform AS platform,
				  ipm_v2_game_logs.isSettled AS is_settled,
				  ipm_v2_game_logs.isConfirmed AS is_confirmed,
				  ipm_v2_game_logs.isCancelled AS is_cancelled,
				  ipm_v2_game_logs.BetId AS RoundID,
				  ipm_v2_game_logs.SportsName AS sports_name,
				  ipm_v2_game_logs.DetailItems AS bet_details,
				  game_description.id  AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select);
		$this->db->from('ipm_v2_game_logs');
		$this->db->join('game_description', 'ipm_v2_game_logs.SportsName = game_description.external_game_id AND game_description.game_platform_id = "'.IPM_V2_SPORTS_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->where('`ipm_v2_game_logs`.`WagerCreationDateTime` >= "' . $dateFrom . '" AND `ipm_v2_game_logs`.`WagerCreationDateTime` <= "' . $dateTo . '"');
		//$qobj = $this->db->get();

		//echo $this->db->last_query();exit();
		return $this->runMultipleRowArray();

	}

	/**
	 * overview : check if idx already exist
	 *
	 * @param  int		$betId
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($betId) {
		$qry = $this->db->get_where($this->tableName, array('BetId' => $betId));
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
		$this->db->where('BetId', $data['BetId']);
		return $this->runUpdate($data);
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
			->where_in('isSettled',0);
		if($startDate) {
			$this->db->where("WagerCreationDateTime >=", $startDate)->where("WagerCreationDateTime <=", $endDate);
		}

		return $this->runMultipleRowArray();
	}

}

///END OF FILE///////