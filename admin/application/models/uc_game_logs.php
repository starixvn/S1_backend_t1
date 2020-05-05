<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Uc_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "uc_game_logs";

	public function getAvailableRows($rows) {
		$this->db->select('TicketId')->from($this->tableName)->where_in('TicketId', array_column($rows, 'TicketId'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'TicketId');
			$availableRows = array();
			foreach ($rows as $row) {
				$TicketId = $row['TicketId'];
				if (!in_array($TicketId, $existsId)) {
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
			  `uc_game_logs`.`PlayerId`,
			  `uc_game_logs`.`Username`,
			  `uc_game_logs`.`external_uniqueid`,
			  `uc_game_logs`.`TimeStamp` AS date_created,
			  `uc_game_logs`.`GameCode` AS game_code,
			  `uc_game_logs`.`WinAmount` AS result_amount,
			  `uc_game_logs`.`BetAmount` AS bet_amount,
			  `uc_game_logs`.`response_result_id`,
			  `uc_game_logs`.`RoundId`,
			  `game_description`.`id`  AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code`,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type`
		FROM
			  `uc_game_logs`
			  LEFT JOIN `game_description`
			    ON (
			      `uc_game_logs`.`GameCode` COLLATE utf8_unicode_ci = `game_description`.`game_code`
			      AND `game_description`.`game_platform_id` = ?
			      AND `game_description`.`void_bet` != 1
			    )
			  LEFT JOIN `game_type`
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    )
			  LEFT JOIN `game_provider_auth`
			    ON (
			      `uc_game_logs`.`Username` COLLATE utf8_unicode_ci = `game_provider_auth`.`login_name`
			      AND `game_provider_auth`.`game_provider_id` = ?
			    )
		WHERE (
			    `uc_game_logs`.`TimeStamp` >= ?
			    AND `uc_game_logs`.`TimeStamp` <= ?
			    )
EOD;

		$query = $this->db->query($sql, array(
			UC_API,
			UC_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

}

///END OF FILE///////