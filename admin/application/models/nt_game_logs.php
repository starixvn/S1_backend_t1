<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class NT_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "nt_game_logs";

	// sync to nt_game_logs

	public function insertNTGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function syncToNTGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param string $uniqueId
	 *
	 * @return boolean
	 */
	function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where($this->tableName, array('log_info_id' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function getNTGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT nt.id as id, nt.time, nt.betting_amount, nt.after_balance, nt.win_amount, nt.multiplier, nt.lines, nt.game_id, nt.username, nt.log_info_id,
nt.response_result_id, nt.type as gameType, nt.game_id as gameshortcode, nt.game_id as gamename, nt.type as gametype,
gd.id as game_description_id, gd.game_name as game, gd.game_code as game_code, gd.game_type_id,
gd.void_bet as void_bet
FROM nt_game_logs as nt
left JOIN game_description as gd ON nt.game_id = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id=?
JOIN game_provider_auth ON nt.username = game_provider_auth.login_name
and game_provider_auth.game_provider_id=?
WHERE
time >= ? AND time <= ?
EOD;

		// $this->utils->debug_log($sql);

		$query = $this->db->query($sql, array(
			NT_API,
			NT_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row[1];
			$arr[] = $uniqueId;
		}

		$this->db->select('log_info_id')->from($this->tableName)->where_in('log_info_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->log_info_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row[1];
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