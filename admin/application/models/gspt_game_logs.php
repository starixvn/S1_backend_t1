<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Gspt_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "gspt_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertGsptGameLogs($data) {
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
			$arr[] = $row['GAMECODE'];
		}
		$this->db->select('game_code')->from($this->tableName)->where_in('game_code', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->game_code;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['GAMECODE'] > $maxRowId) {
					$maxRowId = $row['GAMECODE'];
				}

				if (!in_array($row['GAMECODE'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['GAMECODE'] > $maxRowId) {
					$maxRowId = $row['GAMECODE'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

	// SELECT
	// gspt.player_name,
	// gspt.external_uniqueid,
	// gspt.game_date,
	// gspt.game_name,
	// gspt.bet+gspt.progressive_bet AS void_bet,
	// gspt.win+gspt.progressive_win AS result_amount,
	// gspt.response_result_id, gd.id AS game_description_id,
	// gd.game_name AS game,
	// gd.game_code AS game_code,
	// gd.game_type_id FROM gspt_game_logs AS gspt
	// LEFT JOIN game_description AS gd ON gd.game_code = REPLACE(SUBSTRING_INDEX(gspt.game_name, '(', -1), ')', '') COLLATE utf8_unicode_ci
	// AND gd.void_bet !=1 AND gd.game_platform_id = 22
	// JOIN game_provider_auth ON gspt.player_name = CONCAT(UPPER(game_provider_auth.login_name), '@V8K')
	// COLLATE utf8_unicode_ci AND game_provider_auth.game_provider_id = 22
	// WHERE gspt.game_date >= '2016-11-03 00:00:00'
	// AND gspt.game_date <= '2016-11-03 23:59:59'

function getGsptGameLogStatistics($dateFrom, $dateTo,$gspt_suffix) {

		$sql = <<<EOD
SELECT

gspt.player_name,
gspt.external_uniqueid,
gspt.game_date,
gspt.game_name,
gspt.game_type,
gspt.gameshortcode,
gspt.bet,
gspt.progressive_bet,
gspt.win as win_amount,
gspt.progressive_win,
gspt.response_result_id,
gspt.game_code as round_number,
gspt.info,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id

FROM
	gspt_game_logs as gspt
LEFT JOIN
	game_description as gd ON gd.game_code =  gspt.gameshortcode and gd.game_platform_id = ?
JOIN
	game_provider_auth
	ON
		gspt.player_name =  CONCAT(UPPER(game_provider_auth.login_name), ?) AND
		game_provider_auth.game_provider_id = ?
WHERE
	gspt.game_date >= ? AND gspt.game_date <= ?

EOD;

	$data = array(
			GSPT_API,
			$gspt_suffix,
			GSPT_API,
			$dateFrom,
			$dateTo
		);

	$query = $this->db->query($sql,$data);
	//echo $this->db->last_query();
 	 return $this->getMultipleRow($query);

	}









}

///END OF FILE///////

