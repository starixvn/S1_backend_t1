<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebet_opus_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebet_opus_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('ebet_opus_id')->from($this->tableName)->where_in('ebet_opus_id', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ebet_opus_id');
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
              ebet_opus_game_logs.member_id,
              ebet_opus_game_logs.external_uniqueid,
              ebet_opus_game_logs.transaction_time as start_datetime,
              ebet_opus_game_logs.last_update as end_datetime,
              ebet_opus_game_logs.winlost_amount AS result_amount,
              ebet_opus_game_logs.stake AS bet_amount,
              ebet_opus_game_logs.response_result_id,
              ebet_opus_game_logs.odds,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              ebet_opus_game_logs
              LEFT JOIN game_description
                ON (
                  ebet_opus_game_logs.sports_type COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                ebet_opus_game_logs.transaction_time >= ?
                AND ebet_opus_game_logs.transaction_time <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            EBET_OPUS_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////