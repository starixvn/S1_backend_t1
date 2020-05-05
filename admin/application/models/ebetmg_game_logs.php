<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetmg_game_logs extends Base_game_logs_model {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "ebetmg_game_logs";

	public function getAvailableRows($rows) {
		$this->db->select('row_id')->from($this->tableName)->where_in('row_id', array_column($rows, 'rowId'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'row_id');
			$availableRows = array();
			foreach ($rows as $row) {
				$rowId = $row['rowId'];
				if (!in_array($rowId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	function getGameLogStatistics($dateFrom, $dateTo) {
		//if can't find player id , just ignore
		$sql = <<<EOD
SELECT row_id,
game_end_time,
account_number,
total_wager as bet_amount,
total_payout-total_wager as result_amount,
display_name as game,
display_game_category as game_type,
game_description.id as game_description_id,
game_description.game_type_id as game_type_id,
external_uniqueid,
ebetmg_game_logs.response_result_id,
game_provider_auth.player_id,
game_provider_auth.login_name as playername,
game_description.void_bet as void_bet,
ebetmg_game_logs.module_id,
ebetmg_game_logs.client_id,
ebetmg_game_logs.transaction_id
FROM ebetmg_game_logs
JOIN game_provider_auth ON ebetmg_game_logs.account_number = game_provider_auth.login_name and game_provider_auth.game_provider_id = ?
left join game_description on game_description.external_game_id=ifnull(ebetmg_game_logs.display_name,'unknown') and game_description.game_platform_id=? and game_description.void_bet!=1
WHERE
game_end_time >= ?
AND game_end_time <= ?
EOD;

		$query = $this->db->query($sql, array(EBET_MG_API, EBET_MG_API, $dateFrom, $dateTo));
		return $this->getMultipleRow($query);
	}

}
///END OF FILE///////
