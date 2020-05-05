<?php if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class suncity_game_logs extends Base_game_logs_model {

  function __construct() {
    parent::__construct();
  }

  protected $tableName = "suncity_game_logs";
  

    public function getAvailableRows($rows) {
        $this->db->select('ugsbetid')->from($this->tableName)->where_in('ugsbetid', array_column($rows, 'ugsbetid'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ugsbetid');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['ugsbetid'];
                if (!in_array($uniqueId, $existsId)) {
                    $availableRows[] = $row;
                }
            }
        } else {
            $availableRows = $rows;
        }

        return $availableRows;
    }

    public function getExistingRows($rows) {
        $this->db->select('ugsbetid')->from($this->tableName)->where_in('ugsbetid', array_column($rows, 'ugsbetid'));
        $existsRow = $this->runMultipleRowArray();
        $existingRows = array();
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'ugsbetid');
            foreach ($rows as $row) {
                $uniqueId = $row['ugsbetid'];
                if (in_array($uniqueId, $existsId)) {
                    $existingRows[] = $row;
                }
            }
        }
        return $existingRows;
    }

    function updateGameLogs($data) {
        $this->db->where('ugsbetid', $data['ugsbetid']);
        return $this->db->update($this->tableName, $data);
    }

    public function isRowIdAlreadyExists($ugsbetid) {
        $qry = $this->db->get_where($this->tableName, array('ugsbetid' => $ugsbetid));
        if ($this->getOneRow($qry) == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getGameLogStatistics($dateFrom, $dateTo) {
        $sql = <<<EOD
            SELECT
              suncity_game_logs.userid,
              suncity_game_logs.gameid,
              suncity_game_logs.gamename,
              suncity_game_logs.external_uniqueid,
              suncity_game_logs.betid,
              suncity_game_logs.beton,
              suncity_game_logs.betclosedon,
              suncity_game_logs.winloss AS result_amount,
              suncity_game_logs.validbet AS valid_bet_amount,
              suncity_game_logs.bettype,
              suncity_game_logs.playtype,
              suncity_game_logs.match_detail,
              ABS(suncity_game_logs.riskamt) AS bet_amount,
              suncity_game_logs.response_result_id,
              suncity_game_logs.postbal AS after_balance,
              suncity_game_logs.roundid AS round_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              suncity_game_logs
              LEFT JOIN game_description
                ON (
                  suncity_game_logs.gameid = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_description.game_type_id = game_type.id
                )
              JOIN `game_provider_auth`
                ON (
                  `suncity_game_logs`.`userid` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                suncity_game_logs.betclosedon >= ?
                AND suncity_game_logs.betclosedon <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            SUNCITY_API,
            SUNCITY_API,
            $dateFrom,
            $dateTo
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////