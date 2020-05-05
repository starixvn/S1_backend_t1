<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Vivo_live_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "vivo_live_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertViVoLiveGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('TransactionID' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	function updateViVoLiveGameLogs($UPDATE,$WHERE) {

		$this->db->where('TableRoundID', $WHERE['TableRoundID']);
		$this->db->where('TransactionType', $WHERE['TransactionType']);
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
			$arr[] = $row['TransactionID'];
		}
		$this->db->select('TransactionID')->from($this->tableName)->where_in('TransactionID', $arr);
		$existsRow = $this->runMultipleRow();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->TransactionID;
			}
			$availableRows = array();
			foreach ($rows as $row) {

				if ($maxRowId == null || $row['TransactionID'] > $maxRowId) {
					$maxRowId = $row['TransactionID'];
				}

				if (!in_array($row['TransactionID'], $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row['TransactionID'] > $maxRowId) {
					$maxRowId = $row['TransactionID'];
				}
			}
		}

		return array($availableRows, $maxRowId);
	}


	function getViVoLiveGameLogStatistics($dateFrom, $dateTo) { 
		$sql = <<<EOD
			SELECT
			  `vivo_live_game_logs`.`PlayerId`,
			  `vivo_live_game_logs`.`Username`,
			  `vivo_live_game_logs`.`external_uniqueid`,
			  `vivo_live_game_logs`.`TransacTionDate` AS date_created,
			  `vivo_live_game_logs`.`GameName` AS game_code,
			  `vivo_live_game_logs`.`CreditAmount` AS result_amount,
			  `vivo_live_game_logs`.`DebitAmount` AS bet_amount,
			  `vivo_live_game_logs`.`response_result_id`,
			  `vivo_live_game_logs`.`BalanceAfter`,
			  `game_description`.`id`  AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code`,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type`
			FROM
			  `vivo_live_game_logs`
			  LEFT JOIN `game_description`
			    ON (
			      `vivo_live_game_logs`.`GameName` = `game_description`.`game_code`
			      AND `game_description`.`game_platform_id` = ?
			      AND `game_description`.`void_bet` != 1
			    )
			  LEFT JOIN `game_type`
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    )
			  LEFT JOIN `game_provider_auth`
			    ON (
			      `vivo_live_game_logs`.`Username` = `game_provider_auth`.`login_name`
			      AND `game_provider_auth`.`game_provider_id` = ?
			    )
			WHERE (
			    `vivo_live_game_logs`.`TransactionDate` >= ?
			    AND `vivo_live_game_logs`.`TransactionDate` <= ?
			    AND `vivo_live_game_logs`.`TransactionType` LIKE '%BET%'  )
EOD;

		$data = array(
			VIVO_API,
			VIVO_API,
			$dateFrom,
			$dateTo
		);

		$query = $this->db->query($sql,$data);
	 	 return $this->getMultipleRow($query);
	}

}

///END OF FILE///////

