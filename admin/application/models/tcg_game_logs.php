<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Tcg_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "tcg_game_logs";

    public function getAvailableRows($rows) {

        $this->db->select('mg_id')->from($this->tableName)->where_in('mg_id', array_column($rows, 'id'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'mg_id');
            $availableRows = array();
            foreach ($rows as $row) {
                $ticketId = $row['id'];
                if (!in_array($ticketId, $existsId)) {
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
              tcg_game_logs.username,
              tcg_game_logs.external_uniqueid,
              tcg_game_logs.bet_time,
              tcg_game_logs.settlement_time,
              tcg_game_logs.net_pnl AS result_amount,
              tcg_game_logs.bet_amount,
              tcg_game_logs.response_result_id,
              tcg_game_logs.username,
              tcg_game_logs.game_code,
              tcg_game_logs.bet_order_no,
              tcg_game_logs.bet_status AS status,
              tcg_game_logs.round_key,
              IFNULL(tcg_game_logs.settlement_time,tcg_game_logs.bet_time ) AS game_date,
              game_provider_auth.player_id,
              game_provider_auth.login_name AS player_name,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              tcg_game_logs
              LEFT JOIN game_description
                ON (
                  tcg_game_logs.game_code = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `tcg_game_logs`.`username` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                tcg_game_logs.bet_time >= ?
                AND IFNULL(tcg_game_logs.settlement_time,tcg_game_logs.bet_time ) <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            TCG_API,
            TCG_API,
            $dateFrom,
            $dateTo
        ));

        return $this->getMultipleRowArray($query);
    }

    public function getGameRecordsByRoundKey($roundKey) {
        $this->db->select('id')->from($this->tableName)->where('round_key', $roundKey);
        return $this->runOneRowOneField('id');
    }

    public function syncGameLogs($data, $isOpenBet=null) {
        $id=$this->getGameRecordsByRoundKey($data['round_key']);
        if (!empty($id)) {
            $this->updateGameLog($id, $data);
        } else {
            $this->insertGameLogs($data);
        }
    }
}