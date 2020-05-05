<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Opus_keno_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "opus_keno_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertOpusGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
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
		$this->db->where('row_id', $data['row_id']);
		return $this->db->update($this->tableName, $data);
	}

	function getGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
			SELECT
			  opuskeno.member_id AS player_name,
			  IFNULL (game_description.game_name,'OPUS KENO') AS game_name,
			  opuskeno.external_uniqueid,
			  opuskeno.match_area,
			  opuskeno.trans_time AS game_date,
			  opuskeno.bet_money,
			  opuskeno.bet_winning,
			  opuskeno.player_id,
			  opuskeno.response_result_id
			FROM
			  opus_keno_game_logs AS opuskeno
			LEFT JOIN
				 game_description ON game_description.game_platform_id = ? AND game_code <> 'unknown'
			WHERE
				opuskeno.trans_time >= ?
			  AND opuskeno.trans_time <= ?

EOD;
//echo $sql;
		$query = $this->db->query($sql, array(
			OPUS_KENO_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}


	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = isset($row['@attributes'])?$row['@attributes']['bet_id']:$row['bet_id'];
			$arr[] = $uniqueId;
		}

		$this->db->select('bet_id')->from($this->tableName)->where_in('bet_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->bet_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = isset($row['@attributes'])?$row['@attributes']['bet_id']:$row['bet_id'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = isset($row['@attributes'])?$row['@attributes']:$row;
				}
			}
		} else {
			//add all
			$availableRows = $rows;
		}

		return $availableRows;
	}

	public function getKenoGameName($game_code) {
		$this->db->select(array('game_description.id','game_description.game_name','game_description.game_type_id'))->from('game_description')->where('game_code',$game_code)->where('game_platform_id',OPUS_KENO_API)->group_by('game_code');
		$query = $this->db->get();
		return $query->result_array();
	}

}

///END OF FILE///////
