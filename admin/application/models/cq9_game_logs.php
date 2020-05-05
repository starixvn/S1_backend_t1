<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class cq9_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "cq9_game_logs";

  public function getAvailableRows($rows) {
    $columns = array(
      'gamehall',
      'round'
    );
    $filter = $this->utils->array_column_concat_multi($rows,$columns);
    $this->db->select('external_uniqueid')->from($this->tableName)->where_in('external_uniqueid', $filter);
    $existsRow = $this->runMultipleRowArray();
    $availableRows = null;
    if (!empty($existsRow)) {
        $existsId = array_column($existsRow, 'external_uniqueid');
        $availableRows = array();
        foreach ($rows as $row) {
            $uniqueId = $row['gamehall'].$row['round'];
            if (!in_array($uniqueId, $existsId)) {
                $availableRows[] = $row;
            }
        }
    }else{
      $availableRows = $rows;
    }

    return $availableRows;
  }
	
  public function getGameLogStatistics($dateFrom, $dateTo) {
    $sql = <<<EOD
        SELECT
          cq9_game_logs.account as username,
          cq9_game_logs.gamecode as game_id,
          cq9_game_logs.gametype as game_type,
          cq9_game_logs.bet as bet_amount,
          cq9_game_logs.win AS win_amount,
          cq9_game_logs.balance as after_balance,
          cq9_game_logs.round AS round_id,
          cq9_game_logs.bettime as start_datetime,
          cq9_game_logs.endroundtime as end_datetime,
          cq9_game_logs.rake,
          cq9_game_logs.external_uniqueid,
          cq9_game_logs.response_result_id,
          game_provider_auth.player_id,
          game_description.id AS game_description_id,
          game_description.game_name AS game,
          game_description.game_code,
          game_description.game_type_id,
          game_description.void_bet
        FROM
          cq9_game_logs
          LEFT JOIN game_description
            ON (
              cq9_game_logs.gamecode = game_description.external_game_id
              AND game_description.game_platform_id = ?
              AND game_description.void_bet != 1
            )
          JOIN `game_provider_auth`
            ON (
              `cq9_game_logs`.`account` = `game_provider_auth`.`login_name`
              AND `game_provider_auth`.`game_provider_id` = ?
            )
        WHERE (
            cq9_game_logs.endroundtime >= ?
            AND cq9_game_logs.endroundtime <= ?
          )
EOD;
  
    $query = $this->db->query($sql, array(
        CQ9_API,
        CQ9_API,
        $dateFrom,
        $dateTo,
    ));
    return $this->getMultipleRow($query);
  }

}

///END OF FILE///////