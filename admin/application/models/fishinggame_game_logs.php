<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Fishinggame_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "fishinggame_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertFishinggameGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('detail_autoid' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function updateFishinggameGameLogs($UPDATE,$WHERE) {

		$this->db->where('detail_autoid', $WHERE['detail_autoid']);
		return $this->db->update($this->tableName, $UPDATE);
	}

	function updateGameLogs($data) {
		// $this->db->where('row_id', $data['row_id']);
		// return $this->db->update($this->tableName, $data);
	}


	public function getAvailableRows($rows) {
		$maxRowId = null;
		$arr = array();
		foreach ($rows as $row) {
			$arr[] = $row['detail_autoid'];
		}
		$this->db->select('detail_autoid')->from($this->tableName)->where_in('detail_autoid', $arr);
		$existsRow = $this->runMultipleRow();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();

			foreach ($existsRow as $row) {
				$existsId[] = $row->detail_autoid;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				if ($maxRowId == null || $row['detail_autoid'] > $maxRowId) {
					$maxRowId = $row['detail_autoid'];
				}

				if (!in_array($row['detail_autoid'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['detail_autoid'] > $maxRowId) {
					$maxRowId = $row['detail_autoid'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}


	function getFishinggameGameLogStatistics($dateFrom, $dateTo) { 
		$sql = <<<EOD
			SELECT
			  `fishinggame_game_logs`.`id` as id,
			  `game_provider_auth`.`player_id` as PlayerId,
			  `fishinggame_game_logs`.`Username`,
			  `fishinggame_game_logs`.`external_uniqueid`,
			  `fishinggame_game_logs`.`bettimeStr` AS date_created,
			  `fishinggame_game_logs`.`gameId` AS game_code,
			  `fishinggame_game_logs`.`profit` AS result_amount,
			  `fishinggame_game_logs`.`bet` AS bet_amount,
			  `fishinggame_game_logs`.`response_result_id`,
			  `game_description`.`id`  AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code`,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type`
			FROM
			  `fishinggame_game_logs`
			  LEFT JOIN `game_description`
			    ON (
			      `fishinggame_game_logs`.`gameId` = `game_description`.`game_code`
			      AND `game_description`.`game_platform_id` = ?
			      AND `game_description`.`void_bet` != 1
			    )
			  LEFT JOIN `game_type`
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    )
			  LEFT JOIN `game_provider_auth`
			    ON (
			      `fishinggame_game_logs`.`Username` = `game_provider_auth`.`login_name`
			      AND `game_provider_auth`.`game_provider_id` = ?
			    )
			WHERE (
			    `fishinggame_game_logs`.`bettimeStr` >= ?
			    AND `fishinggame_game_logs`.`bettimeStr` <= ?
			    )
EOD;

		$data = array(
			FISHINGGAME_API,
			FISHINGGAME_API,
			$dateFrom,
			$dateTo
		);

		$query = $this->db->query($sql,$data);
	 	 return $this->getMultipleRow($query);
	}

}

///END OF FILE///////

