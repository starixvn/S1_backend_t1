<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ebet_dt_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ebet_dt_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('game_unique_id')->from($this->tableName)->where_in('game_unique_id', array_column($rows, 'gameUniqueId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'game_unique_id');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['gameUniqueId'];
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
              ebet_dt_game_logs.player_name,
              ebet_dt_game_logs.external_uniqueid,
              ebet_dt_game_logs.create_time as bet_datetime,              
              ebet_dt_game_logs.prize_wins AS result_amount,
              ebet_dt_game_logs.bet_price AS bet_amount,
              ebet_dt_game_logs.response_result_id,
              ebet_dt_game_logs.parent_id,
              ebet_dt_game_logs.credit_after as after_balance,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              ebet_dt_game_logs
              LEFT JOIN game_description
                ON (
                  ebet_dt_game_logs.game_code COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                ebet_dt_game_logs.create_time >= ?
                AND ebet_dt_game_logs.create_time <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            EBET_DT_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

    public function getEbetDtGameLogs($game_id) {
        $this->db->select('*')->from($this->tableName);
        $this->db->where('game_unique_id', $game_id);
        $query = $this->db->get();
        return $query->result_array();
    }

}

///END OF FILE///////