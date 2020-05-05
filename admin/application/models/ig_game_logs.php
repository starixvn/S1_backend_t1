<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Ig_game_logs extends Base_game_logs_model {

    public function __construct() {
        parent::__construct();
    }

    protected $tableName = "ig_game_logs";

    public function getAvailableRows($rows) {

        $this->db->select('bet_id')->from($this->tableName)->where_in('bet_id', array_column($rows, 'betId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'bet_id');
            $availableRows = array();
            foreach ($rows as $row) {
                $ticketId = $row['betId'];
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
    ig_game_logs.id as id,
    ig_game_logs.external_uniqueid,
    ig_game_logs.response_result_id,
    ig_game_logs.username,
    ig_game_logs.bet_time,
    ig_game_logs.result_time,
    ig_game_logs.stake_amount,
    ig_game_logs.win_loss,
    ig_game_logs.player_id,
    ig_game_logs.bet_on,
    ig_game_logs.bet_type,
    ig_game_logs.bet_details,
    ig_game_logs.odds,
    ig_game_logs.ig_id,
    ig_game_logs.bet_id,
    game_description.id  AS game_description_id,
    game_description.game_name AS game,
    game_description.game_code,
    game_description.game_type_id,
    game_description.void_bet,
    game_type.game_type
FROM
  ig_game_logs
  LEFT JOIN game_description
    ON (
      ig_game_logs.game_info_id = game_description.external_game_id
      AND game_description.game_platform_id = ?
      AND game_description.void_bet != 1
    )
  LEFT JOIN game_type
    ON (
      game_description.game_type_id = game_type.id
    )
   JOIN game_provider_auth
    ON (
      ig_game_logs.username = game_provider_auth.login_name
      AND game_provider_auth.game_provider_id = ?
    )
WHERE (
    ig_game_logs.result_time >= ?
    AND ig_game_logs.result_time <= ? )
EOD;
        $query = $this->db->query($sql, array(
            IG_API,
            IG_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRowArray($query);
    }

    public function getIGGame() {
        $this->db->select('game_description.id');
        $this->db->select('game_description.game_name');
        $this->db->select('game_description.game_code');
        $this->db->select('game_description.game_type_id');
        $this->db->select('game_description.void_bet');
        $this->db->select('game_type.game_type');
        $this->db->from('game_type');
        $this->db->join('game_description', 'game_description.game_type_id = game_type.id', 'LEFT');
        $this->db->where('game_type.game_type_code', 'ig_lottery_game');
        $this->db->where('game_description.game_code', 'ig_lottery');
        $this->db->where('game_description.void_bet !=', 1 );
        $this->db->where('game_description.game_platform_id', IG_API);
        return $this->runOneRowArray();
    }

    public function syncGameLogs($data) {
        $this->insertGameLogs($data);
    }
}