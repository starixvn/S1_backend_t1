<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class pgsoft_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "pgsoft_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('betid')->from($this->tableName)->where_in('betid', array_column($rows, 'betId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'betid');
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
              pgsoft_game_logs.playername,
              pgsoft_game_logs.gameid as gameid,
              pgsoft_game_logs.gameid as game_type,
              pgsoft_game_logs.betamount as bet_amount,
              pgsoft_game_logs.betamount as real_bet_amount,
              pgsoft_game_logs.winamount AS result_amount,
              pgsoft_game_logs.balanceafter AS after_balance,
              pgsoft_game_logs.betid AS round_id,
              pgsoft_game_logs.bettime as start_datetime,
              pgsoft_game_logs.bettime as end_datetime,
              pgsoft_game_logs.external_uniqueid,
              pgsoft_game_logs.response_result_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              pgsoft_game_logs
              LEFT JOIN game_description
                ON (
                  pgsoft_game_logs.gameid = game_description.external_game_id
                  AND game_description.game_platform_id = ?
                )
              JOIN `game_provider_auth`
                ON (
                  `pgsoft_game_logs`.`playername` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                pgsoft_game_logs.bettime >= ?
                AND pgsoft_game_logs.bettime <= ?
              )
EOD;
      
        $query = $this->db->query($sql, array(
            PGSOFT_API,
            PGSOFT_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

}

///END OF FILE///////