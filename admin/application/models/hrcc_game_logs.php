<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Hrcc_game_logs extends BaseModel {
	const STATUS_ACTIVE = 1; #1(active)
	const STATUS_ACTIVE_SCR = 2; #scr
	const STATUS_ACTIVE_CANCELLED = 4; #cancelled
	const STATUS_ACTIVE_SETTLED = 9; #1(active) and 8(settle) can be combined to become 9 by BitOr.
	const STATUS_ACTIVE_SCR_SETTLED = 11; #1(active) and 2(scr) and 8(settle) can be combined to become 11 by BitOr.
	const STATUS_ACTIVE_CANCEL_SETTLED = 13; #1(active) and 4(cancel) and 8(settle) can be combined to become 13 by BitOr
	const STATUS_ACTIVE_RACE_VOID = 16; #Race void

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "hrcc_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param trans_id int
	 *
	 * @return boolean
	 */
	function isTransIdAlreadyExists($trans_id) {
		$qry = $this->db->get_where($this->tableName, array('trans_id' => $trans_id));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param trans_id int
	 *
	 * @return boolean
	 */
	function isWinAmountExists($trans_id) {
		$qry = $this->db->get_where($this->tableName, array('trans_id' => $trans_id, 'win_flag' => true));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param trans_id int
	 *
	 * @return boolean
	 */
	function getResultAmount($trans_id) {
		$this->db->select('result_amount,amount as bet_amount')->from($this->tableName);
		$this->db->where('trans_id', $trans_id);
		$query = $this->db->get();
		return $query->row_array();
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

	function getGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT hrcc.id,
	   hrcc.user_id as username,
	   hrcc.trans_id,
       hrcc.bet_amount,
       hrcc.result_amount,
       hrcc.race_type as gameshortcode,
       hrcc.external_uniqueid,
       hrcc.response_result_id,
       hrcc.transaction_date,
       hrcc.status,
	   gd.id as game_description_id,
	   gd.game_name as game,
	   gd.game_code as game_code,
	   gd.game_type_id,
	   gp.player_id,
	   gt.game_type
FROM hrcc_game_logs as hrcc
LEFT JOIN game_description as gd ON hrcc.race_type COLLATE utf8_unicode_ci = gd.external_game_id
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON hrcc.user_id = gp.login_name COLLATE utf8_unicode_ci and game_provider_id=?
WHERE
hrcc.transaction_date >= ? AND hrcc.transaction_date <= ?
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			HRCC_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		if (!empty($rows)) {
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['id'];
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
					$uniqueId = $row['id'];
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
