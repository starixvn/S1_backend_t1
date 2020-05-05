<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

/**
 * @deprecated because move to game_api_lottery_t1
 */
class t1lottery_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "t1lottery_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', array_column($rows, 'uniqueid'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'uniqueid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['uniqueid'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }

        return $availableRows;
    }

    /**
    * overview : check if uniqueid already exist
    *
    * @param  int    $uniqueid
    *
    * @return boolean
    */
    function isRowIdAlreadyExists($uniqueid) {
      $qry = $this->db->get_where($this->tableName, array('external_uniqueid' => $uniqueid));
      if ($this->getOneRow($qry) == null) {
        return false;
      } else {
        return true;
      }
    }

    /**
    * overview : update game logs
    *
    * @param  array  $data
    *
    * @return boolean
    */
    function updateGameLogs($data) {
      $this->db->where('external_uniqueid', $data['uniqueid']);
      return $this->db->update($this->tableName, $data);
    }

    public function getGameLogStatistics($dateFrom, $dateTo) {

        $sql = <<<EOD
            SELECT
              t1lottery_game_logs.username,
              t1lottery_game_logs.external_uniqueid,
              t1lottery_game_logs.bet_time,
              t1lottery_game_logs.game_finish_time,
              t1lottery_game_logs.result_amount,
              t1lottery_game_logs.effective_bet_amount AS bet_amount,
              t1lottery_game_logs.real_bet_amount,
              t1lottery_game_logs.after_balance,
              t1lottery_game_logs.response_result_id,
              t1lottery_game_logs.bet_details,
              t1lottery_game_logs.game_code,
              t1lottery_game_logs.game_name,
              t1lottery_game_logs.status,
              game_description.game_name AS game,
              game_description.id AS game_description_id,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              t1lottery_game_logs
              LEFT JOIN game_description
                ON (
                  t1lottery_game_logs.game_code COLLATE utf8_unicode_ci = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
            WHERE (
                t1lottery_game_logs.bet_time >= ?
                AND IFNULL(t1lottery_game_logs.game_finish_time,t1lottery_game_logs.bet_time ) <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            T1LOTTERY_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////