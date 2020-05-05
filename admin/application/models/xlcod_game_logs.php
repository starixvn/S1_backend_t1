<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class  Xlcod_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "xlcod_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertXlcodGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('result_id' => $rowId));
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
		$this->db->where('result_id', $data['result_id']);
		return $this->db->update($this->tableName, $data);
	}

	function getXlcodGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT xlcod.user_id, 
	   xlcod.result_id,
       xlcod.bet_amount, 
       xlcod.result_amount, 
       xlcod.timestamp, 
       xlcod.game_code,
       xlcod.external_uniqueid,
       xlcod.response_result_id,
	   gd.id as game_description_id, 
	   gd.game_name as game, 
	   gd.game_code as game_code, 
	   gd.game_type_id,
	   gd.void_bet as void_bet
FROM xlcod_game_logs as xlcod
LEFT JOIN game_description as gd ON xlcod.game_code = gd.external_game_id
JOIN game_provider_auth ON xlcod.user_id = game_provider_auth.login_name and game_provider_id=?
WHERE
xlcod.timestamp >= ? AND xlcod.timestamp <= ?
EOD;
		
		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			XHTDLOTTERY_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['external_uniqueid'];
			$arr[] = $uniqueId;
		}
		$this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->external_uniqueid;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['external_uniqueid'];
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

}

///END OF FILE///////
