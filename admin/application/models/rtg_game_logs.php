<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class rtg_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "rtg_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('game_number')->from($this->tableName)->where_in('game_number', array_column($rows, 'game_number'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'game_number');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['game_number'];
                if (!in_array($uniqueId, $existsId)) {
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
              rtg_game_logs.login,
              rtg_game_logs.gameid_machineid as game_id,
              rtg_game_logs.game_name as game_type,
              rtg_game_logs.machine_name as game_name,
              rtg_game_logs.bet_amount ,
              rtg_game_logs.payout AS result_amount,
              rtg_game_logs.game_number AS round_id,
              rtg_game_logs.balance_after AS after_balance,
              rtg_game_logs.date_started,
              rtg_game_logs.date_finished,
              rtg_game_logs.external_uniqueid,
              rtg_game_logs.response_result_id,
              rtg_game_logs.bonus_bet_amount,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              rtg_game_logs
              LEFT JOIN game_description
                ON (
                  rtg_game_logs.gameid_machineid = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `rtg_game_logs`.`login` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                rtg_game_logs.date_finished >= ?
                AND rtg_game_logs.date_finished <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            RTG_API,
            RTG_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////