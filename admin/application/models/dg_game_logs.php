<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class dg_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "dg_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('dg_id')->from($this->tableName)->where_in('dg_id', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        $skipForManualSync = false;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'dg_id');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['id'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
            $skipForManualSync = true;
        } else {
            $availableRows = $rows;
        }

        return [$availableRows,$skipForManualSync];
    }

    public function getGameLogStatistics($dateFrom, $dateTo, $gamePlatformID = DG_API) {

        $sql = <<<EOD
            SELECT
              dg_game_logs.id as id,
              dg_game_logs.userName,
              dg_game_logs.external_uniqueid,
              dg_game_logs.betTime,
              dg_game_logs.calTime,
              dg_game_logs.winOrLoss AS result_amount,
              dg_game_logs.betPoints AS real_bet_amount,
              dg_game_logs.availableBet AS bet_amount,
              dg_game_logs.response_result_id,
              dg_game_logs.ext,
              dg_game_logs.dg_id,
              dg_game_logs.betDetail,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              dg_game_logs
              LEFT JOIN game_description
                ON (
                  CONCAT(dg_game_logs.gameType,dg_game_logs.gameId) COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                dg_game_logs.betTime >= ?
                AND dg_game_logs.betTime <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            $gamePlatformID,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////