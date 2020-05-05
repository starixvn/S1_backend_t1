<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class One88_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "one88_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertOne88GameLogs($data) {
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

	function getOne88GameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT gl.user_code username, gl.wagers_no external_id, gl.date_created, gl.sport_name game_code, gl.winloss_amount result_amount, gl.total_stakef as bet_amount,
gl.response_result_id,
gd.id as game_description_id, gd.game_name as game, gd.game_code as game_code, gd.game_type_id,
gd.void_bet as void_bet
FROM one88_game_logs as gl
left JOIN game_description as gd ON gl.sport_name = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id=?
JOIN game_provider_auth ON gl.user_code = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE
date_created >= ? AND date_created <= ?
EOD;

		// $this->utils->debug_log($sql);

		$query = $this->db->query($sql, array(
			ONE88_API,
			ONE88_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();

		foreach ($rows as $row) {
			$arr[] = $row['WagerNo'];
		}
		$this->db->select('wagers_no')->from($this->tableName)->where_in('wagers_no', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->wagers_no;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['WagerNo'] > $maxRowId) {
					$maxRowId = $row['WagerNo'];
				}

				if (!in_array($row['WagerNo'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['WagerNo'] > $maxRowId) {
					$maxRowId = $row['WagerNo'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

}

///END OF FILE///////
