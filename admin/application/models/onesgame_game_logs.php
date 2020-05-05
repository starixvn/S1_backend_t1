<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Onesgame_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "onesgame_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertOnesgameGameLogs($data) {
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

	function getOnesgameGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT onesg.player_id,
onesg.player_currency,
onesg.game_id,
onesg.game_name,
onesg.game_category,
onesg.tran_id,
onesg.total_amount,
onesg.bet_amount,
onesg.jackpot_contribution,
onesg.win_amount,
onesg.game_date,
onesg.platform,
onesg.external_uniqueid,
onesg.response_result_id,

gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gd.void_bet as void_bet

FROM onesgame_game_logs as onesg

LEFT JOIN game_description as gd ON onesg.game_id = gd.external_game_id and gd.game_platform_id = ?
JOIN game_provider_auth ON onesg.player_id = game_provider_auth.login_name and game_provider_id=?

WHERE
game_date >= ? AND game_date <= ?
EOD;

		// $this->utils->debug_log($sql);

		$query = $this->db->query($sql, array(
            ONESGAME_API,
			ONESGAME_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$arr = array();
		foreach ($rows as $row) {
			$uniqueId = $row['TranID'];
			$arr[] = $uniqueId;
		}

		$this->db->select('tran_id')->from($this->tableName)->where_in('tran_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->tran_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['TranID'];
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
