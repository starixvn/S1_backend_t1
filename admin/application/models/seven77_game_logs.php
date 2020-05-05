<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Seven77_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "seven77_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertSeven77GameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	function getSeven77GameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT seven77.id,
seven77.username,
seven77.result_id as trans_id,
seven77.game_bet as bet_amount,
seven77.total_profit as result_amount,
seven77.game_id as gameshortcode,
seven77.external_uniqueid,
seven77.response_result_id,
seven77.end_time as transaction_time,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gp.player_id,
gt.game_type
FROM seven77_game_logs as seven77
LEFT JOIN game_description as gd ON seven77.game_id = gd.external_game_id AND gd.game_platform_id =?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON seven77.username = gp.login_name and game_provider_id=?
WHERE
seven77.end_time >= ? AND seven77.end_time <= ?
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			SEVEN77_API,
			SEVEN77_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		if (!empty($rows)) {
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['resultID'];
				$arr[] = $uniqueId;
			}

			$this->db->select('result_id')->from($this->tableName)->where_in('result_id', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->result_id;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					$uniqueId = $row['resultID'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				//add all
				$availableRows = $rows;
			}
			return $availableRows;
		} else {
			return null;
		}

	}

}

///END OF FILE///////
