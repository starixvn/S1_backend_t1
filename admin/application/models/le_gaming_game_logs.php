<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Le_gaming_game_logs extends Base_game_logs_model {

    function __construct() {
        parent::__construct();
    }

    const GAME_FOLD = 0;
    protected $tableName = "le_gaming_game_logs";

    public function getAvailableRows($rows) {

        if(!isset($rows)){
            $rows = array($rows);
        }

        $ids =  array();
        foreach($rows as $nrow){
            array_push($ids,$nrow['GameID']);
        }

        $existsRow = array();
        if(!empty($ids)){
            $this->db->select('GameID')->from($this->tableName)->where_in('GameID', $ids);
            $existsRow = $this->runMultipleRowArray();
        }

        $availableRows = null;

        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'GameID');

            $availableRows = array();

            foreach ($rows as $row) {
                $transNo = $row['GameID'];
                if (!in_array($transNo, $existsId)) {
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
              game_provider_auth.player_id AS player_id,
              le_gaming_game_logs.player_username as username,
              le_gaming_game_logs.external_uniqueid,
              le_gaming_game_logs.GameStartTime AS start_date,
              le_gaming_game_logs.GameEndTime AS end_date,
              le_gaming_game_logs.KindID AS game_code,
              le_gaming_game_logs.response_result_id,
              le_gaming_game_logs.Profit AS result_amount,
              le_gaming_game_logs.AllBet AS bet_amount,
              le_gaming_game_logs.Revenue AS revenue,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_type_id,
              game_description.void_bet,
              game_type.game_type
            FROM
              le_gaming_game_logs
              LEFT JOIN game_description
                ON (
                  le_gaming_game_logs.KindID = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_type.id = game_description.game_type_id
                )
              JOIN `game_provider_auth`
                ON (
                  `le_gaming_game_logs`.`player_username` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                le_gaming_game_logs.GameStartTime >= ?
                AND le_gaming_game_logs.GameStartTime <= ?
                AND le_gaming_game_logs.CellScore != ?
              )
EOD;

        $query = $this->db->query($sql, array(
            LE_GAMING_API,
            LE_GAMING_API,
            $dateFrom,
            $dateTo,
            self::GAME_FOLD,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////