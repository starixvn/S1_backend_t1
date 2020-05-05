<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class rtg_master_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "rtg_master_game_logs";

    public function getAvailableRows($rows) {
      // echo "<pre>";print_r(array_column($rows, 'id'));exit;
        $this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();

        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'external_uniqueid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['id'];
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
              rtg_master_game_logs.playername as gameUsername,
              rtg_master_game_logs.gameid,
              rtg_master_game_logs.gamename as originalGameTypeName,
              rtg_master_game_logs.gamename as originalGameName,
              rtg_master_game_logs.bet AS bet_amount,
              rtg_master_game_logs.win AS result_amount,
              rtg_master_game_logs.gamenumber AS round_id,
              rtg_master_game_logs.external_uniqueid,
              rtg_master_game_logs.balanceend AS after_balance,
              rtg_master_game_logs.gamestartdate,
              rtg_master_game_logs.external_uniqueid,
              rtg_master_game_logs.response_result_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              rtg_master_game_logs
              LEFT JOIN game_description
                ON (
                  rtg_master_game_logs.gameid = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `rtg_master_game_logs`.`playername` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                rtg_master_game_logs.gamestartdate >= ?
                AND rtg_master_game_logs.gamestartdate <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            RTG_MASTER_API,
            RTG_MASTER_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////