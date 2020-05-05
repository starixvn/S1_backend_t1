<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Beteast_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "beteast_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertBeteastGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('gameid' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function updateBeteastGameLogs($UPDATE,$WHERE) {

		$this->db->where('gameid', $WHERE['gameid']);
		return $this->db->update($this->tableName, $UPDATE);
	}

	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();
		foreach ($rows as $row) {
			$arr[] = $row['gameid'];
		}
		$this->db->select('gameid')->from($this->tableName)->where_in('gameid', $arr);
		$existsRow = $this->runMultipleRow();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();

			foreach ($existsRow as $row) {
				$existsId[] = $row->gameid;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				if ($maxRowId == null || $row['gameid'] > $maxRowId) {
					$maxRowId = $row['gameid'];
				}

				if (!in_array($row['gameid'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['gameid'] > $maxRowId) {
					$maxRowId = $row['gameid'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}


	function getBeteastLogStatistics($dateFrom, $dateTo) { 
		$sql = <<<EOD
			SELECT
			  `beteast_game_logs`.`PlayerId`,
			  `beteast_game_logs`.`Username`,
			  `beteast_game_logs`.`external_uniqueid`,
			  `beteast_game_logs`.`start_dt` AS date_start,
			  `beteast_game_logs`.`end_dt` AS date_end,
			  `beteast_game_logs`.`gameId` AS game_code,
			  `beteast_game_logs`.`win_cash` AS result_amount,
			  `beteast_game_logs`.`bet_cash` AS bet_amount,
			  `beteast_game_logs`.`response_result_id`,
			  `beteast_game_logs`.`gameid`,
			  `game_description`.`id`  AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code`,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type`
			FROM
			  `beteast_game_logs`
			  LEFT JOIN `game_description`
			    ON (
			      CONCAT_WS('-', `beteast_game_logs`.`game_type`, `beteast_game_logs`.`type`, `beteast_game_logs`.`game_name`) COLLATE utf8_unicode_ci = `game_description`.`game_code`
			      AND `game_description`.`game_platform_id` = ?
			      AND `game_description`.`void_bet` != 1
			    )
			  LEFT JOIN `game_type`
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    )
			  LEFT JOIN `game_provider_auth`
			    ON (
			      `beteast_game_logs`.`Username` COLLATE utf8_unicode_ci = `game_provider_auth`.`login_name`
			      AND `game_provider_auth`.`game_provider_id` = ?
			    )
			WHERE (
			    `beteast_game_logs`.`start_dt` >= ?
			    AND `beteast_game_logs`.`end_dt` <= ?
			    )
EOD;

		$data = array(
			BETEAST_API,
			BETEAST_API,
			$dateFrom,
			$dateTo
		);

		$query = $this->db->query($sql,$data);
	 	 return $this->getMultipleRow($query);
	}

}

///END OF FILE///////

