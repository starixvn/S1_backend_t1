<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Kuma_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "kuma_game_logs";

	public function getAvailableRows($rows) {
		$this->db->select('BillNo')->from($this->tableName)->where_in('BillNo', array_column($rows, 'BillNo'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'BillNo');
			$availableRows = array();
			foreach ($rows as $row) {
				$BillNo = $row['BillNo'];
				if (!in_array($BillNo, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
		SELECT
			  `kuma_game_logs`.`PlayerId`,
			  `kuma_game_logs`.`Username`,
			  `kuma_game_logs`.`external_uniqueid`,
			  `kuma_game_logs`.`SettleTime` AS date_created,
			  `kuma_game_logs`.`GameID` AS game_code,
			  `kuma_game_logs`.`NetAmount` AS result_amount,
			  `kuma_game_logs`.`BetValue` AS bet_amount,
			  `kuma_game_logs`.`response_result_id`,
			  `game_description`.`id`  AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code`,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type`
		FROM
			  `kuma_game_logs`
			  LEFT JOIN `game_description`
			    ON (
			      `kuma_game_logs`.`GameID` = `game_description`.`game_code`
			      AND `game_description`.`game_platform_id` = ?
			      AND `game_description`.`void_bet` != 1
			    )
			  LEFT JOIN `game_type`
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    )
			  LEFT JOIN `game_provider_auth`
			    ON (
			      `kuma_game_logs`.`Username` = `game_provider_auth`.`login_name`
			      AND `game_provider_auth`.`game_provider_id` = ?
			    )
		WHERE (
			    `kuma_game_logs`.`SettleTime` >= ?
			    AND `kuma_game_logs`.`SettleTime` <= ?
			    )
EOD;

		$query = $this->db->query($sql, array(
			KUMA_API,
			KUMA_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

}

///END OF FILE///////