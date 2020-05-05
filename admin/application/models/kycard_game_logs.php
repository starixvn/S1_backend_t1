<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class kycard_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "kycard_game_logs";

    public function getAvailableRows($gameids) {
        $this->db->select('gameid')->from($this->tableName)->where_in('gameid', $gameids);
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'gameid');
            $availableRows = array();
            foreach ($gameids as $key => $row) {
                $uniqueId = $row;
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $key;
                }
            }
        } else {
            foreach ($gameids as $key => $row) {
                $availableRows[] = $key;
            }
        }

        return $availableRows;
    }
	
    public function getGameLogStatistics($dateFrom, $dateTo) {

        $sql = <<<EOD
            SELECT
              kycard_game_logs.username,
              CONCAT(kycard_game_logs.kindid,kycard_game_logs.serverid) as game_id,
              kycard_game_logs.allbet as real_bet_amount,
              kycard_game_logs.kindid as game_type,
              kycard_game_logs.cellscore as bet_amount,
              kycard_game_logs.profit AS result_amount,
              kycard_game_logs.gameid AS round_id,
              kycard_game_logs.gamestarttime as start_datetime,
              kycard_game_logs.gameendtime as end_datetime,
              kycard_game_logs.external_uniqueid,
              kycard_game_logs.response_result_id,
              kycard_game_logs.revenue as rake,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              kycard_game_logs
              LEFT JOIN game_description
                ON (
                  kycard_game_logs.external_game_id = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `kycard_game_logs`.`username` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                kycard_game_logs.gameendtime >= ?
                AND kycard_game_logs.gameendtime <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            KYCARD_API,
            KYCARD_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////