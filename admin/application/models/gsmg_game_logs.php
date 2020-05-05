<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Gsmg_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "gsmg_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isTransIdAlreadyExists($game_tran_id) {
		$qry = $this->db->get_where($this->tableName, array('game_tran_id' => $game_tran_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isWinAmountExists($game_tran_id) {
		$qry = $this->db->get_where($this->tableName, array('game_tran_id' => $game_tran_id, 'win_flag' => true));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function getResultAmount($game_tran_id) {
		$this->db->select('result_amount,amount as bet_amount')->from($this->tableName);
		$this->db->where('game_tran_id', $game_tran_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('game_tran_id', $data['game_tran_id']);
		return $this->db->update($this->tableName, $data);
	}


	function getGameLogStatistics($dateFrom, $dateTo, $auto_prefix) {
// JOIN game_provider_auth ON gsmg.account_number = concat(?,game_provider_auth.login_name) AND game_provider_auth.game_provider_id = ?
// game_provider_auth.player_id,
		$sql = <<<EOD
SELECT gsmg.id,
gsmg.game_account_without_prefix,
game_provider_auth.player_id,
gsmg.row_id,
gsmg.session_id,
gsmg.total_wager as bet_amount,
gsmg.total_payout,
gsmg.result_amount,
gsmg.display_name as gameshortcode,
gsmg.external_uniqueid,
gsmg.response_result_id,
gsmg.game_end_time,
gsmg.account_number,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gt.game_type
FROM gsmg_game_logs as gsmg
LEFT JOIN game_description as gd ON gsmg.display_name = gd.english_name AND gd.game_platform_id =  ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth ON gsmg.game_account_without_prefix = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
WHERE
gsmg.game_end_time >= ? AND gsmg.game_end_time <= ?
EOD;
		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			GSMG_API,
			// $auto_prefix,
			GSMG_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows, &$maxRowId=null) {
		if (!empty($rows)) {
			$maxRowId = null;
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['RowId'];
				$arr[] = $uniqueId;
			}

			$this->db->select('row_id')->from($this->tableName)->where_in('row_id', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->row_id;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					if ($maxRowId == null || $row['RowId'] > $maxRowId || strlen($row['RowId']) > strlen($maxRowId) ) {
						$maxRowId = $row['RowId'];
					}
					$uniqueId = $row['RowId'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				//add all
				$availableRows = $rows;
				foreach ($rows as $row) {
					if ($maxRowId == null || $row['RowId'] > $maxRowId || strlen($row['RowId']) > strlen($maxRowId) ) {
						$maxRowId = $row['RowId'];
					}
				}
			}
			return $availableRows;
		} else {
			return null;
		}

	}

}

///END OF FILE///////
