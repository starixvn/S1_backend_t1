<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ezugi_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ezugi_game_logs";

	public function getAvailableRows($rows) {
		$this->db->select('ezugiID')->from($this->tableName)->where_in('ezugiID', array_column($rows, 'ID'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'ezugiID');
			$availableRows = array();
			foreach ($rows as $row) {
				$ezugiID = $row['ID'];
				if (!in_array($ezugiID, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		
		$select = 'ezugi_game_logs.PlayerId,
				  ezugi_game_logs.Username,
				  ezugi_game_logs.external_uniqueid,
				  ezugi_game_logs.RoundDateTime AS game_date,
				  ezugi_game_logs.GameTypeID AS game_code,
				  ezugi_game_logs`.`response_result_id`,
				  ezugi_game_logs`.`Win` AS result_amount,
				  ezugi_game_logs`.`RoundID` AS RoundID,
				  ezugi_game_logs`.`GameString`,
				  game_description`.`id`  AS game_description_id,
				  game_description.game_name AS game,
				  game_description.game_code,
				  game_description.game_type_id,
				  game_description.void_bet,
				  game_type.game_type';

		$this->db->select($select);
		$this->db->from('ezugi_game_logs');
		$this->db->join('game_description', 'ezugi_game_logs.GameTypeID = game_description.game_code AND game_description.game_platform_id = "'.EZUGI_API.'" AND game_description.void_bet != 1', 'LEFT');
		$this->db->join('game_type', 'game_description.game_type_id = game_type.id', 'LEFT');
		$this->db->join('game_provider_auth', 'ezugi_game_logs.Username = game_provider_auth.login_name AND game_provider_auth.game_provider_id = "'.EZUGI_API.'"', 'LEFT');
		$this->db->where('ezugi_game_logs.RoundDateTime >= "'.$dateFrom.'" AND ezugi_game_logs.RoundDateTime <= "' . $dateTo . '"');
		$qobj = $this->db->get();

		return $qobj->result_array();

// 		$sql = <<<EOD

// 		SELECT
// 			  `ezugi_game_logs`.`PlayerId`,
// 			  `ezugi_game_logs`.`Username`,
// 			  `ezugi_game_logs`.`external_uniqueid`,
// 			  `ezugi_game_logs`.`RoundDateTime` AS game_date,
// 			  `ezugi_game_logs`.`GameTypeID` AS game_code,
// 			  `ezugi_game_logs`.`response_result_id`,
// 			  `ezugi_game_logs`.`Win` AS result_amount,
// 			  `ezugi_game_logs`.`RoundID` AS RoundID,
// 			  `ezugi_game_logs`.`GameString`,
// 			  `game_description`.`id`  AS game_description_id,
// 			  `game_description`.`game_name` AS game,
// 			  `game_description`.`game_code`,
// 			  `game_description`.`game_type_id`,
// 			  `game_description`.`void_bet`,
// 			  `game_type`.`game_type`
// 		FROM
// 			  `ezugi_game_logs`
// 			  LEFT JOIN `game_description`
// 			    ON (
// 			      `ezugi_game_logs`.`GameTypeID` = `game_description`.`game_code`
// 			      AND `game_description`.`game_platform_id` = ?
// 			      AND `game_description`.`void_bet` != 1
// 			    )
// 			  LEFT JOIN `game_type`
// 			    ON (
// 			      `game_description`.`game_type_id` = `game_type`.`id`
// 			    )
// 			  LEFT JOIN `game_provider_auth`
// 			    ON (
// 			      `ezugi_game_logs`.`Username` = `game_provider_auth`.`login_name`
// 			      AND `game_provider_auth`.`game_provider_id` = ?
// 			    )
// 		WHERE (
// 			    `ezugi_game_logs`.`RoundDateTime` >= ?
// 			    AND `ezugi_game_logs`.`RoundDateTime` <= ?
// 			    )
// EOD;

// 		$query = $this->db->query($sql, array(
// 			EZUGI_API,
// 			EZUGI_API,
// 			$dateFrom,
// 			$dateTo,
// 		));

	}

}

///END OF FILE///////