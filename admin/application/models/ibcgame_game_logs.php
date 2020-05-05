<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class  Ibcgame_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ibc_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertIbcGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('trans_id' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('trans_id', $data['trans_id']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get running logs
	 *
	 * @param  string	$startDate,$endDate
	 *
	 * @return array
	 */
	function getRunningLogs($startDate = false,$endDate = false) {
		// return $startDate;
		$this->db->select('*')
			->from($this->tableName)
			->where_in('ticket_status', array('running','refund'));
		if($startDate) {
			$this->db->where("transaction_time >=", $startDate)->where("transaction_time <=", $endDate);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$select = 'ibc_game_logs.id as id,
				  ibc_game_logs.player_name,
				  ibc_game_logs.trans_id,
				  ibc_game_logs.league_id,
				  ibc_game_logs.league_name,
				  ibc_game_logs.sport_type,
				  ibc_game_logs.balance,
				  ibc_game_logs.bet_team,
				  ibc_game_logs.transaction_time,
				  ibc_game_logs.stake,
				  ibc_game_logs.winlose_amount,
                  ibc_game_logs.winlost_datetime,
                  ibc_game_logs.game_platform,
                  ibc_game_logs.external_uniqueid,
                  ibc_game_logs.response_result_id,
                  ibc_game_logs.ticket_status,
                  ibc_game_logs.odds,
                  ibc_game_logs.odds_type,
                  ibc_game_logs.away_id_name,
                  ibc_game_logs.home_id_name,
                  game_provider_auth.player_id,
				  game_description.id AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select,false);
		$this->db->from('ibc_game_logs');
		$this->db->join('game_description', 'ibc_game_logs.sport_type  = game_description.game_code AND game_description.game_platform_id = "'.IBC_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->join('game_provider_auth','game_provider_auth.login_name=ibc_game_logs.player_name AND game_provider_auth.game_provider_id = "'.IBC_API.'"'  , 'LEFT');
		$this->db->where('ibc_game_logs.transaction_time >= "'.$dateFrom.'" AND ibc_game_logs.transaction_time <= "' . $dateTo . '"');
		// $qobj = $this->db->get();
		// echo($this->db->last_query());exit();
		// return $qobj->result_array();

		return $this->runMultipleRowArray();
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['TransId'];
			$arr[] = $uniqueId;
		}
		$this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->trans_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['TransId'];
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

	function getExistsTransIdArr($transIdArr) {
		$this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $transIdArr);

		$rows= $this->runMultipleRowArray();

		$map=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$map[]=$row['trans_id'];
			}
		}

		return $map;

		// $qry = $this->db->get_where($this->tableName, array('trans_id' => $rowId));
		// if ($this->getOneRow($qry) == null) {
		// 	return false;
		// } else {
		// 	return true;
		// }
	}

	public function getExistsNotSettledTransIdArr($transIdArr) {
		$this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $transIdArr)
			->where_not_in('ticket_status', ['won', 'half won', 'draw', 'lose', 'half lose']);

		$rows= $this->runMultipleRowArray();

		$map=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$map[]=$row['trans_id'];
			}
		}

		return $map;

		// $qry = $this->db->get_where($this->tableName, array('trans_id' => $rowId));
		// if ($this->getOneRow($qry) == null) {
		// 	return false;
		// } else {
		// 	return true;
		// }
	}

}

