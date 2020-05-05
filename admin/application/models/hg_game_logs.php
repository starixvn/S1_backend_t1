<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Hg_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "hg_game_logs";

    public function getAvailableRows($rows) {
        if(!empty($rows)){
            $this->db->select('bet_id')->from($this->tableName)->where_in('bet_id', array_column($rows, 'BetId'));
            $existsRow = $this->runMultipleRowArray();
            $availableRows = null;
            if (!empty($existsRow)) {
                $existsId = array_column($existsRow, 'bet_id');
                $availableRows = array();
                foreach ($rows as $row) {
                    $ticketId = $row['BetId'];
                    if (!in_array($ticketId, $existsId)) {
                        $availableRows[] = $row;
                    }
                }
            } else {
                $availableRows = $rows;
            }
        }else {
            $availableRows = $rows;
        }
    
        return $availableRows;
    }

    public function getGameLogStatistics($dateFrom, $dateTo) {

        $sql = <<<EOD
            SELECT
              hg_game_logs.account_id as gameUsername,
              hg_game_logs.game_type as originalGameTypeName,
              hg_game_logs.game_type as originalGameName,
              hg_game_logs.bet_amount,
              hg_game_logs.payout AS result_amount,
              hg_game_logs.game_id AS round_id,
              hg_game_logs.external_uniqueid,
              hg_game_logs.bet_start_date,
              hg_game_logs.bet_end_date,
              hg_game_logs.response_result_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              hg_game_logs
              LEFT JOIN game_description
                ON (
                  hg_game_logs.game_type = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `hg_game_logs`.`account_id` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                hg_game_logs.bet_start_date >= ?
                AND hg_game_logs.bet_end_date <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            HG_API,
            HG_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }
}