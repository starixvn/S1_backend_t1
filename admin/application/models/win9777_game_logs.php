<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Win9777_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "win9777_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertWin9777GameLogs($data) {
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

	function getWin9777GameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT bbin.username, bbin.external_uniqueid, bbin.wagers_date, bbin.game_type, bbin.result, bbin.bet_amount, bbin.payoff, bbin.currency, bbin.commisionable,
gd.id as game_description_id, gd.game_name as game, gd.game_code as game_code, gd.game_type_id,
gd.void_bet as void_bet
FROM win9777_game_logs as bbin
left JOIN game_description as gd ON bbin.game_type = gd.external_game_id and gd.void_bet!=1 and gd.game_platform_id=?
JOIN game_provider_auth ON bbin.username = game_provider_auth.login_name and game_provider_auth.game_provider_id=?
WHERE
wagers_date >= ? AND wagers_date <= ?
EOD;

		// $this->utils->debug_log($sql);

		$query = $this->db->query($sql, array(
			WIN9777_API,
			WIN9777_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();

		foreach ($rows as $row) {
			$arr[] = $row['WagersID'];
		}
		$this->db->select('wagers_id')->from($this->tableName)->where_in('wagers_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->wagers_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['WagersID'] > $maxRowId) {
					$maxRowId = $row['WagersID'];
				}

				if (!in_array($row['WagersID'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['WagersID'] > $maxRowId) {
					$maxRowId = $row['WagersID'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

}

///END OF FILE///////
