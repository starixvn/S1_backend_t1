<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Kenogame_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "kenogame_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertKenoGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		// $qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
		// if ($this->getOneRow($qry) == null) {
		// 	return false;
		// } else {
		// 	return true;
		// }
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
// 		$this->db->where('row_id', $data['row_id']);
		// 		return $this->db->update($this->tableName, $data);
		// 	}

// 	function getOnesgameGameLogStatistics($dateFrom, $dateTo) {
		// 		$sql = <<<EOD
		// SELECT onesg.player_id, onesg.player_currency, onesg.game_id, onesg.game_name, onesg.game_category, onesg.tran_id,onesg.total_amount,onesg.bet_amount,onesg.jackpot_contribution,
		// onesg.win_amount, onesg.game_date, onesg.platform, onesg.external_uniqueid,onesg.response_result_id,
		// gd.id as game_description_id, gd.game_name as game, gd.game_code as game_code, gd.game_type_id,
		// gd.void_bet as void_bet
		// FROM onesgame_game_logs as onesg
		// LEFT JOIN game_description as gd ON onesg.game_id = gd.external_game_id
		// JOIN game_provider_auth ON onesg.player_id = game_provider_auth.login_name and game_provider_id=?
		// WHERE
		// game_date >= ? AND game_date <= ?
		// EOD;

// 		// $this->utils->debug_log($sql);

// 		$query = $this->db->query($sql, array(
		// 			ONESGAME_API,
		// 			$dateFrom,
		// 			$dateTo,
		// 		));

// 		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();

		foreach ($rows as $row) {
			$arr[] = $row->BetId;
		}

		$this->db->select('BetId')->from($this->tableName)->where_in('BetId', $arr);
		$existsRow = $this->runMultipleRow();
//var_dump($existsRow); exit();
		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->BetId;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->BetId > $maxRowId) {
					$maxRowId = $row->BetId;
				}

				if (!in_array($row->BetId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->BetId > $maxRowId) {
					$maxRowId = $row->BetId;
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

	function getKenogameGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
	kenogame.PlayerId as player_name,
	kenogame.external_uniqueid,
	kenogame.UpdateTime as game_date,
	kenogame.GameCode as game_name,
	kenogame.RegionId,
	kenogame.BetType game_type,
	kenogame.StakeAccurate,
	kenogame.Payout as result_amount,
	kenogame.Credit as after_balance,
	game_provider_auth.player_id,
	kenogame.response_result_id
FROM
	kenogame_game_logs as kenogame
INNER JOIN
	 game_provider_auth ON game_provider_auth.login_name = kenogame.PlayerId AND  game_provider_auth.game_provider_id = ?
WHERE
	kenogame.createdAt >= ? AND kenogame.createdAt <= ?

EOD;
//echo $sql;
		$query = $this->db->query($sql, array(
			KENOGAME_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

}

///END OF FILE///////
