<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class t1games_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    protected $tableName = "t1games_game_logs";

    /**
     * overview : check if rowId already exist
     *
     * @param  int    $rowId
     *
     * @return boolean
     */

    public function syncGameLogs($data) {
      $id=$this->getIdByExternalUniqueid($data['external_uniqueid']);
      if(!empty($id)){
        return $this->updateGameLog($id, $data);
        // return $this->db->update($this->tableName, $data);
      }else{
        return $this->insertGameLogs($data);
        // return $this->db->insert($this->tableName, $data);
      }
    }

    public function getIdByExternalUniqueid($uniqueid){
      $this->db->select('id')->from($this->tableName)->where('external_uniqueid', $uniqueid);
      return $this->runOneRowOneField('id');
    }

    public function getAvailableRows($rows,$game_platform_id = null) {
        $this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', array_column($rows, 'external_uniqueid'))->where('game_platform_id =',$game_platform_id );
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'external_uniqueid');
            $availableRows = array();
            foreach ($rows as $row) {
                $external_uniqueid = $row['external_uniqueid'];
                if (!in_array($external_uniqueid, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }

        return $availableRows;
    }

    public function getGameLogStatistics($dateFrom, $dateTo, $game_platform_id=null,$original_game_platform_id = null) {
        if(in_array($original_game_platform_id, $this->utils->getConfig('t1games_using_external_game_id'))){
          $sql = <<<EOD
              SELECT
                t1games_game_logs.username,
                t1games_game_logs.external_uniqueid,
                t1games_game_logs.bet_time,
                t1games_game_logs.game_finish_time,
                t1games_game_logs.result_amount,
                t1games_game_logs.effective_bet_amount AS bet_amount,
                t1games_game_logs.real_bet_amount,
                t1games_game_logs.after_balance,
                t1games_game_logs.response_result_id,
                t1games_game_logs.game_details,
                t1games_game_logs.game_status,
                t1games_game_logs.bet_details,
                game_description.id AS game_description_id,
                game_description.game_name AS game,
                game_description.game_code,
                game_description.game_type_id,
                game_description.void_bet,
                game_type.game_type
              FROM
                t1games_game_logs
                LEFT JOIN game_description
                  ON (
                    t1games_game_logs.game_code COLLATE utf8_unicode_ci = game_description.external_game_id
                    AND game_description.game_platform_id = ?
                    AND game_description.void_bet != 1
                  )
                LEFT JOIN game_type
                  ON (
                    game_description.game_type_id = game_type.id
                  )
              WHERE (
                  ((t1games_game_logs.bet_time >= ?
                  AND t1games_game_logs.game_finish_time <= ?) 
                  OR (t1games_game_logs.bet_time >= ?
                  AND t1games_game_logs.updated_at <= ?))
                  AND t1games_game_logs.game_platform_id = ?
                )
EOD;
        }else{
          $sql = <<<EOD
              SELECT
                t1games_game_logs.username,
                t1games_game_logs.external_uniqueid,
                t1games_game_logs.bet_time,
                t1games_game_logs.game_finish_time,
                t1games_game_logs.result_amount,
                t1games_game_logs.effective_bet_amount AS bet_amount,
                t1games_game_logs.real_bet_amount,
                t1games_game_logs.after_balance,
                t1games_game_logs.response_result_id,
                t1games_game_logs.game_details,
                t1games_game_logs.game_status,
                t1games_game_logs.bet_details,
                game_description.id AS game_description_id,
                game_description.game_name AS game,
                game_description.game_code,
                game_description.game_type_id,
                game_description.void_bet,
                game_type.game_type
              FROM
                t1games_game_logs
                LEFT JOIN game_description
                  ON (
                    t1games_game_logs.game_code COLLATE utf8_unicode_ci = game_description.game_code
                    AND game_description.game_platform_id = ?
                    AND game_description.void_bet != 1
                  )
                LEFT JOIN game_type
                  ON (
                    game_description.game_type_id = game_type.id
                  )
              WHERE (
                  ((t1games_game_logs.bet_time >= ?
                  AND t1games_game_logs.game_finish_time <= ?) 
                  OR (t1games_game_logs.bet_time >= ?
                  AND t1games_game_logs.updated_at <= ?))
                  AND t1games_game_logs.game_platform_id = ?
                )
EOD;
}
        $query = $this->db->query($sql, array(
            $game_platform_id,
            $dateFrom,
            $dateTo,
            $dateFrom,
            $dateTo,
            $original_game_platform_id,
        ));
        // echo $this->db->last_query();exit;
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////