<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_game_logs_model.php';

class Og_game_logs extends Base_game_logs_model {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "og_game_logs";

	public function getAvailableRows($rows) {

		if(!isset($rows)){
			$rows = array($rows);
		}

		$ids =  array();
		foreach($rows as $nrow){
			array_push($ids,$nrow['ProductID']);
		}

		$existsRow = array();
		if(!empty($ids)){
			$this->db->select('ProductID')->from($this->tableName)->where_in('ProductID', $ids);
			$existsRow = $this->runMultipleRowArray();
		}

		$availableRows = null;

		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'ProductID');

			$availableRows = array();

			foreach ($rows as $row) {
				$transNo = $row['ProductID'];
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
              game_provider_auth.player_id AS PlayerId,
			  og_game_logs.UserName,
			  og_game_logs.external_uniqueid,
			  og_game_logs.AddTime AS game_date,
			  og_game_logs.GameNameID AS game_code,
			  og_game_logs.response_result_id,
			  og_game_logs.WinLoseAmount AS result_amount,
			  og_game_logs.BettingAmount AS BetAmount,
			  og_game_logs.ValidAmount,
			  og_game_logs.GameRecordId,
              og_game_logs.GameBettingContent,
			  game_description.id AS game_description_id,
			  game_description.game_name AS game,
			  game_description.game_type_id,
			  game_description.void_bet,
			  game_type.game_type
            FROM
              og_game_logs
              LEFT JOIN game_description
                ON (
                  og_game_logs.GameNameID = game_description.game_code
                  AND game_description.game_platform_id = ?
                  AND game_description.void_bet != 1
                )
              LEFT JOIN game_type
                ON (
                  game_type.id = game_description.game_type_id
                )
              JOIN `game_provider_auth`
                ON (
                  `og_game_logs`.`UserName` = `game_provider_auth`.`login_name`
                  AND `game_provider_auth`.`game_provider_id` = ?
                )
            WHERE (
                og_game_logs.addTime >= ?
                AND og_game_logs.addTime <= ?
              )
EOD;

        $query = $this->db->query($sql, array(
            OG_API,
            OG_API,
            $dateFrom,
            $dateTo,
        ));
        return $this->getMultipleRow($query);
	}

}

///END OF FILE///////