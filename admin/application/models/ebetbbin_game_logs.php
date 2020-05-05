<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetbbin_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "ebetbbin_game_logs";

	public function getAvailableRows($rows) {
		$this->db->select('WagersID')->from($this->tableName)->where_in('WagersID', array_column($rows, 'WagersID'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'WagersID');
			$availableRows = array();
			foreach ($rows as $row) {
				$TicketId = $row['WagersID'];
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
			  ebetbbin_game_logs.PlayerId,
			  ebetbbin_game_logs.Username,
			  ebetbbin_game_logs.external_uniqueid,
			  ebetbbin_game_logs.WagersDate AS date_created,
			  ebetbbin_game_logs.GameType AS game_code,
			  ebetbbin_game_logs.Payoff AS result_amount,
			  ebetbbin_game_logs.BetAmount AS bet_amount,
			  ebetbbin_game_logs.response_result_id,
			  ebetbbin_game_logs.RoundNo AS RoundNo,
			  game_description.id AS game_description_id,
			  game_description.game_name AS game,
			  game_description.game_code,
			  game_description.game_type_id,
			  game_description.void_bet,
			  game_type.game_type 
			FROM
			  ebetbbin_game_logs 
			  LEFT JOIN game_description 
			    ON (
			      ebetbbin_game_logs.GameType COLLATE utf8_unicode_ci = game_description.game_code 
			      AND game_description.game_platform_id = ? 
			      AND game_description.void_bet != 1
			    ) 
			  LEFT JOIN game_type 
			    ON (
			      game_description.game_type_id = game_type.id
			    ) 
			WHERE (
			    ebetbbin_game_logs.WagersDate >= ? 
			    AND ebetbbin_game_logs.WagersDate <= ?
			  )
EOD;

		$query = $this->db->query($sql, array(
			EBET_BBIN_API,
			$dateFrom,
			$dateTo,
		));

		return $this->getMultipleRow($query);
	}

}

///END OF FILE///////