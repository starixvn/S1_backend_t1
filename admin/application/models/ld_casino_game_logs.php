<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class ld_casino_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "ld_casino_game_logs";

    public function getAvailableRows($rows) {
        if (empty($rows)) {
          return $rows;
        }

        $this->db->select('bet_id')->from($this->tableName)->where_in('bet_id', array_column($rows, 'betId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'bet_id');
            $availableRows = array();

            foreach ($rows as $row) {
                $uniqueId = $row['betId'];
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
              a.external_uniqueid,
              a.round_id,
              a.bet_time as betTime,
              a.calcu_time as endTime,
              a.win_lose AS result_amount,
              a.amount AS bet_amount,
              a.valid_bet AS real_bet_amount,
              a.response_result_id,
              a.player_id,
              a.username, 
              a.extra, 
              b.id AS game_description_id,
              b.game_name AS game_name,
              b.game_code,
              b.game_type_id,
              b.void_bet,
              c.game_type
            FROM
              ld_casino_game_logs a
            LEFT JOIN game_description b
              ON a.game_type = b.game_code
                AND b.game_platform_id = ?
                AND b.void_bet != 1
            LEFT JOIN game_type c
              ON b.game_type_id = c.id
            WHERE a.bet_time >= ? AND a.calcu_time <= ?;
EOD;
        
        $query = $this->db->query($sql, array(
            LD_CASINO_API,
            $dateFrom,
            $dateTo,
        ));

        return $this->getMultipleRow($query);
    }

    public function getExistingBetIds($betIds) {
        $this->db->select('id, bet_id')->from($this->tableName)->where_in('bet_id', $betIds);
        return $this->runMultipleRowArray();
    }

}

///END OF FILE///////