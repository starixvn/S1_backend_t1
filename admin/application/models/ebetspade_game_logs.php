<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebetspade_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebetspade_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('ticketId')->from($this->tableName)->where_in('ticketId', array_column($rows, 'ticketId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ticketId');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['ticketId'];
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
              ebetspade_game_logs.playerId,
              ebetspade_game_logs.acctId as username,
              ebetspade_game_logs.external_uniqueid,
              ebetspade_game_logs.ticketTime AS date_created,
              ebetspade_game_logs.winLoss AS result_amount,
              ebetspade_game_logs.betAmount AS bet_amount,
              ebetspade_game_logs.response_result_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              ebetspade_game_logs
              LEFT JOIN game_description
                ON (
                  ebetspade_game_logs.gameCode COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                ebetspade_game_logs.ticketTime >= ?
                AND ebetspade_game_logs.ticketTime <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            EBET_SPADE_GAMING_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////