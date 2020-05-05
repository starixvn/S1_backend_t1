<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Lb_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "lb_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertLBGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('row_id', $data['row_id']);
		return $this->db->update($this->tableName, $data);
	}

	function getLBGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT lb.member_id, lb.bet_id, lb.bet_money, lb.bet_odds, lb.bet_winning, lb.bet_win,lb.bet_time,lb.trans_time,lb.external_uniqueid,
lb.response_result_id, lb.bet_type
FROM lb_game_logs as lb
JOIN game_provider_auth ON lb.member_id = game_provider_auth.login_name and game_provider_id=?
WHERE
bet_time >= ? AND bet_time <= ?
and bet_status=?
EOD;

		// $this->utils->debug_log($sql);

		$query = $this->db->query($sql, array(
			LB_API,
			$dateFrom,
			$dateTo,
			self::BET_STATUS_SETTLED,
		));

		return $this->getMultipleRow($query);
	}

	const BET_STATUS_SETTLED = 'settled';
	const BET_STATUS_UNSETTLED = 'unsettled';

	public function getAvailableRows($resultArr) {
		$maxRowId = null;
		$arr = array_column($resultArr, 'bet_id');

		$this->db->select('bet_id')->from($this->tableName)
			->where_in('bet_id', $arr);

		$existsRow = $this->runMultipleRow();

		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->bet_id;
			}

			$availableRows = array();
			foreach ($resultArr as $value) {
				$bet_id = $value['bet_id'];
				if (!in_array($bet_id, $existsId)) {
					$availableRows[] = $value;
				}
			}
		} else {
			$availableRows = $resultArr;
		}

		return $availableRows;
	}

	public function getLogStatistics($dateFrom, $dateTo) { 
		$sql = <<<EOD
			SELECT 
			  `lb_game_logs`.`PlayerId`,
			  `lb_game_logs`.`Username`,
			  `lb_game_logs`.`external_uniqueid`,
			  `lb_game_logs`.`bet_time` AS date_start,
			  `lb_game_logs`.`bet_time` AS date_end,
			  `lb_game_logs`.`bet_winning` AS result_amount,
			  `lb_game_logs`.`bet_money` AS bet_amount,
			  `lb_game_logs`.`bet_type`,
			  `lb_game_logs`.`bet_content`,
			  `lb_game_logs`.`bet_winning`,
			  `lb_game_logs`.`bet_odds`,
			  `lb_game_logs`.`bet_win`,
			  `lb_game_logs`.`response_result_id`,
			  `lb_game_logs`.`bet_no` AS round_id,
			  `game_description`.`id` AS game_description_id,
			  `game_description`.`game_name` AS game,
			  `game_description`.`game_code` AS game_code,
			  `game_description`.`game_type_id`,
			  `game_description`.`void_bet`,
			  `game_type`.`game_type` 
			FROM
			  `lb_game_logs` 
			  LEFT JOIN `game_description`
			   ON (`game_description`.`game_platform_id` = ? 
			   AND `lb_game_logs`.`match_area` = `game_description`.`external_game_id`
			    AND `game_description`.`void_bet` != 1) 
			  LEFT JOIN `game_type` 
			    ON (
			      `game_description`.`game_type_id` = `game_type`.`id`
			    ) 
			  LEFT JOIN `game_provider_auth` 
			    ON (
			      `lb_game_logs`.`Username` = `game_provider_auth`.`login_name` 
			      AND `game_provider_auth`.`game_provider_id` = ?
			    ) 
			WHERE (
			    `lb_game_logs`.`bet_time` >= ? 
			    AND `lb_game_logs`.`bet_time` <= ?
			  )
EOD;

		$data = array(
			LB_API,
			LB_API,
			$dateFrom,
			$dateTo
		);

		$query = $this->db->query($sql,$data);
	 	 return $this->getMultipleRow($query);
	}


}

///END OF FILE///////
