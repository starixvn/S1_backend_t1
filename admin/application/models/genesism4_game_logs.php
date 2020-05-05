<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Genesism4_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "genesism4_game_logs";

	public function getAvailableRows($rows) {

		$this->db->select('causality')->from($this->tableName)->where_in('causality', array_column($rows, 'causality'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'causality');
			$availableRows = array();
			foreach ($rows as $row) {
				$causality = $row['causality'];
				if (!in_array($causality, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

	public function getGameLogStatistics($dateFrom, $dateTo) {
		$this->CI->load->model(array('original_game_logs_model'));
		$sqlTime='genesism4_game_logs.timestamp >= ? and genesism4_game_logs.timestamp <= ?';
		$sql = <<<EOD
SELECT
genesism4_game_logs.id AS sync_index,
genesism4_game_logs.external_uniqueid,
genesism4_game_logs.md5_sum,
genesism4_game_logs.timestamp AS game_date,
genesism4_game_logs.game_id AS game_code,
genesism4_game_logs.response_result_id,
genesism4_game_logs.total_won AS won_amount,
genesism4_game_logs.total_bet AS bet_amount,
genesism4_game_logs.balance AS after_balance,
genesism4_game_logs.partner_data,
genesism4_game_logs.user_id,
genesism4_game_logs.causality,
genesism4_game_logs.currency,
genesism4_game_logs.device,
game_provider_auth.player_id as player_id,
game_provider_auth.login_name as login_name,
game_description.id AS game_description_id,
game_description.game_name AS game,
game_description.game_type_id,
game_description.void_bet
FROM
  genesism4_game_logs
  join game_provider_auth on game_provider_auth.login_name=genesism4_game_logs.user_id and game_provider_auth.game_provider_id=?
  LEFT JOIN game_description
    ON (
      genesism4_game_logs.game_id = game_description.external_game_id
      AND game_description.game_platform_id = ?
      AND game_description.void_bet != 1
    )
WHERE

  {$sqlTime}

EOD;

  $params=[GENESISM4_GAME_API, GENESISM4_GAME_API,
          $dateFrom,$dateTo];

        return $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql, $params);
	}

}

///END OF FILE///////