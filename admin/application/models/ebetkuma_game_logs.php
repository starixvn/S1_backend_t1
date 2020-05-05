<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetkuma_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebetkuma_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('billNo')->from($this->tableName)->where_in('billNo', array_column($rows, 'billNo'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'billNo');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['billNo'];
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
              ebetkuma_game_logs.playerId,
              ebetkuma_game_logs.username,
              ebetkuma_game_logs.external_uniqueid,
              ebetkuma_game_logs.settleTime,
              ebetkuma_game_logs.netAmount AS result_amount,
              ebetkuma_game_logs.betValue AS bet_amount,
              ebetkuma_game_logs.response_result_id,
              ebetkuma_game_logs.billNo,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              ebetkuma_game_logs
              LEFT JOIN game_description
                ON (
                  ebetkuma_game_logs.gameId COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                ebetkuma_game_logs.settleTime >= ?
                AND ebetkuma_game_logs.settleTime <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            EBET_KUMA_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////