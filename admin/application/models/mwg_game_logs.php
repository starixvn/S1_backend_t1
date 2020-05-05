<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class mwg_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "mwg_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('gameNum')->from($this->tableName)->where_in('gameNum', array_column($rows, 'gameNum'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'gameNum');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['gameNum'];
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
              mwg_game_logs.uid,
              mwg_game_logs.gameId,
              mwg_game_logs.gameName,
              mwg_game_logs.gameNum,
              mwg_game_logs.gameType,
              mwg_game_logs.playMoney AS bet_amount,
              mwg_game_logs.winMoney AS result_amount,
              mwg_game_logs.response_result_id,
              mwg_game_logs.gameNum AS round_id,
              mwg_game_logs.logDate,
              mwg_game_logs.external_uniqueid,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              mwg_game_logs
              LEFT JOIN game_description
                ON (
                  mwg_game_logs.gameId = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `mwg_game_logs`.`uid` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                mwg_game_logs.logDate >= ?
                AND mwg_game_logs.logDate <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            MWG_API,
            MWG_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////