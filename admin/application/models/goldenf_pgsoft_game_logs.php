<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class goldenf_pgsoft_game_logs extends Base_game_logs_model {

  const Payoff = 'Payoff'; // result 

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "goldenf_pgsoft_game_logs";

    public function getAvailableRows($rows) {
        $this->db->select('traceId')->from($this->tableName)->where_in('traceId', array_column($rows, 'traceId'));
        $existsRow = $this->runMultipleRowArray();
        $availableRows = null;
        if (!empty($existsRow)) {
            $existsId = array_column($existsRow, 'traceId');
            $availableRows = array();
            foreach ($rows as $row) {
                $uniqueId = $row['traceId'];
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
              goldenf_pgsoft_game_logs.player_name AS gameUsername,
              goldenf_pgsoft_game_logs.game_id,
              goldenf_pgsoft_game_logs.game_code AS originalGameName,
              goldenf_pgsoft_game_logs.game_code AS originalGameTypeName,
              goldenf_pgsoft_game_logs.bet_amount AS bet_amount,
              goldenf_pgsoft_game_logs.win_amount AS result_amount,
              goldenf_pgsoft_game_logs.bet_id AS round_id,
              goldenf_pgsoft_game_logs.pgsoft_created_at AS start_datetime,
              goldenf_pgsoft_game_logs.pgsoft_created_at AS end_datetime,
              goldenf_pgsoft_game_logs.external_uniqueid,
              goldenf_pgsoft_game_logs.response_result_id,
              game_provider_auth.player_id,
              game_description.id AS game_description_id,
              game_description.game_name AS game,
              game_description.game_code,
              game_description.game_type_id,
              game_description.void_bet
            FROM
              goldenf_pgsoft_game_logs
              LEFT JOIN game_description
                ON (
                  goldenf_pgsoft_game_logs.game_code = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              JOIN `game_provider_auth`
                ON (
                  `goldenf_pgsoft_game_logs`.`player_name` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                goldenf_pgsoft_game_logs.pgsoft_created_at >= ?
                AND goldenf_pgsoft_game_logs.pgsoft_created_at <= ?
                AND trans_type = 'Stake'
              )
EOD;
      
        $query = $this->db->query($sql, array(
            GOLDENF_PGSOFT_API,
            GOLDENF_PGSOFT_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
    }

    public function getWinAmountByRoundKey($roundKey) {
        $this->db->from($this->tableName)->where('bet_id', $roundKey)->where('trans_type',self::Payoff);
        return $this->runOneRowArray();
    }

}

///END OF FILE///////