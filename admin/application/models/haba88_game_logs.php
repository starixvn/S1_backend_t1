<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Haba88_game_logs
 *
 * General behaviors include :
 *
 * * Add API game logs
 * * Check if row id already exist
 * * Get last version key
 * * Update game logs
 * * Get game logs statistics
 * * Get available rows
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Haba88_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "haba88_game_logs";

	/**
	 * overview : insert haba88 game logs
	 *
	 * @param  array	$data
	 * @return boolean
	 */
	public function insertHaba88GameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : check if row id already exist
	 *
	 * @param  int		$rowId
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
	 * overview : update game logs
	 *
	 * @param  array		$data
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('row_id', $data['row_id']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get haba88 game log statistics
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @return null
	 */
	function getHaba88GameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT
  `haba88_game_logs`.`id` as id,
  `game_provider_auth`.`player_id` as player_id,
  `haba88_game_logs`.`Username`,
  `haba88_game_logs`.`external_uniqueid`,
  `haba88_game_logs`.`DtCompleted` AS date_created,
  `haba88_game_logs`.`GameKeyName` AS game_code,
  `haba88_game_logs`.`Payout` AS result_amount,
  IFNULL(`haba88_game_logs`.`BonusToReal`,0) AS bonus_amount,
  `haba88_game_logs`.`Stake` AS bet_amount,
  `haba88_game_logs`.`response_result_id`,
  `haba88_game_logs`.`BalanceAfter` as after_balance,
  `game_description`.`id`  AS game_description_id,
  `game_description`.`game_name` AS game,
  `game_description`.`game_type_id`,
  `game_description`.`void_bet`,
  `game_type`.`game_type`
FROM
  `haba88_game_logs`
  LEFT JOIN `game_description`
    ON (
      `haba88_game_logs`.`GameKeyName` = `game_description`.`game_code`
      AND `game_description`.`game_platform_id` = ?
    )
  LEFT JOIN `game_type`
    ON (
      `game_description`.`game_type_id` = `game_type`.`id`
    )
   JOIN `game_provider_auth`
    ON (
      `haba88_game_logs`.`Username` = `game_provider_auth`.`login_name`
      AND `game_provider_auth`.`game_provider_id` = ?
    )
WHERE (
    `haba88_game_logs`.`DtCompleted` >= ?
    AND `haba88_game_logs`.`DtCompleted` <= ? )
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			HB_API,
			HB_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	/**
	 * overview : get available rows
	 *
	 * @param  array	$rows
	 * @return array|null
	 */
	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();
		$rows = json_decode(json_encode($rows), true); //convert to array()

		foreach ($rows as $row) {
			$arr[] = $row['FriendlyGameInstanceId'];
		}
		$this->db->select('FriendlyGameInstanceId')->from($this->tableName)->where_in('FriendlyGameInstanceId', $arr);
		$existsRow = $this->runMultipleRow();
		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {

				$existsId[] = $row->FriendlyGameInstanceId;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				if ($maxRowId == null || $row['FriendlyGameInstanceId'] > $maxRowId) {
					$maxRowId = $row['FriendlyGameInstanceId'];
				}

				if (!in_array($row['FriendlyGameInstanceId'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['FriendlyGameInstanceId'] > $maxRowId) {
					$maxRowId = $row['FriendlyGameInstanceId'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

	/**
	 * overview : get haba game name game code
	 *
	 * @param  string	$game_code
	 * @return array
	 */
	public function getHabaGameName($game_code) {
		$this->db->select(array('game_description.id','game_description.game_name','game_description.game_type_id'))->from('game_description')->where('game_code',$game_code)->where('game_platform_id','38')->group_by('game_code');
		$query = $this->db->get();
		return $query->result_array();
	}

}

///END OF FILE///////