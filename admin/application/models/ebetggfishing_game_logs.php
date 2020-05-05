<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetggfishing_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebetggfishing_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('ebetgg_id')->from($this->tableName)->where_in('ebetgg_id', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ebetgg_id');
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
              ebetggfishing_game_logs.playerId,
              ebetggfishing_game_logs.external_uniqueid,
              ebetggfishing_game_logs.gameDate,
              ebetggfishing_game_logs.winLoss AS result_amount,
              ebetggfishing_game_logs.betAmount AS bet_amount,
              ebetggfishing_game_logs.response_result_id,
              ebetggfishing_game_logs.providerRoundId,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              ebetggfishing_game_logs
              LEFT JOIN game_description
                ON (
                  ebetggfishing_game_logs.gameId COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                ebetggfishing_game_logs.gameDate >= ?
                AND ebetggfishing_game_logs.gameDate <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            EBET_GGFISHING_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////