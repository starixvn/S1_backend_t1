<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base_model.php';

class Betmaster_game_logs extends BaseModel {

	const TABLE = "betmaster_game_logs";
//	const KEY 	= 'billNo';

	public function insertBetmasterGameLogs($data) {
		return $this->db->insert(self::TABLE, $data);
	}

	public function syncToBetmasterGameLogs($data) {
		return $this->db->insert(self::TABLE, $data);
	}

	public function isUniqueIdAlreadyExists($uniqueId) {
		$qry = $this->db->get_where(self::TABLE, array('uniqueid' => $uniqueId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * update betmaster Game Logs
	 *
	 * @param $data
	 * @return mixed
	 */
	function updateBetmasterGameLogs($data) {
		$this->db->where('uniqueid', $data['uniqueid']);
		return $this->db->update(self::TABLE, $data);
	}

	public function getBetmasterGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT
	betmaster.uniqueid as external_uniqueid,
	betmaster.username,
	betmaster.game_name as game,
	betmaster.game_code,
	betmaster.game_details_id as response_result_id,
	betmaster.bet_time as start_at,
	betmaster.bet_time as end_at,
	betmaster.effective_bet_amount as bet_amount,
	betmaster.result_amount,
	betmaster.real_bet_amount,
	betmaster.payout_amount,
	betmaster.outcome_id,
	betmaster.special_odds,
	betmaster.is_live,
	betmaster.type_id,
	betmaster.team_home_id,
	betmaster.team_away_id,
	betmaster.game_platform_id,
	betmaster.odds,
	betmaster.after_balance,
	game_provider_auth.player_id,
	gd.id as game_description_id,
	gd.game_type_id,
	gd.void_bet as void_bet
FROM
	betmaster_game_logs as betmaster
LEFT JOIN
	game_description as gd ON gd.game_code = betmaster.game_code and gd.void_bet!=1 and gd.game_platform_id = ?
JOIN
	game_provider_auth ON betmaster.username = game_provider_auth.login_name AND game_provider_auth.game_provider_id = ?
WHERE
	betmaster.bet_time >= ? AND betmaster.bet_time <= ?
EOD;

//		$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics->sql', $sql);
//		$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics', $dateFrom);
//		$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics', $dateTo);


		$query = $this->db->query($sql, array(
			BETMASTER_API,
			BETMASTER_API,
			$dateFrom,
			$dateTo,
		));

//		$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics', $query);

		return $this->getMultipleRow($query);

	}

	public function getAvailableRows($rows) {
		$this->db->select('uniqueid,payout_time')->from(self::TABLE)->where_in('uniqueid', array_column($rows, 'uniqueid'));

		$existsRows = $this->runMultipleRowArray();

		$existsItems = array();

		foreach ($existsRows as $existsRow){
			$existsItems[$existsRow['uniqueid']] = $existsRow['payout_time'];
		}

		$availableRows = null;

		if (!empty($existsRows)) {
			$existsId = array_column($existsRows, 'uniqueid');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['uniqueid'];
				if (!in_array($uniqueId, $existsId)) {
					$row['isUniqueIdAlreadyExists'] = false;
					$availableRows[] = $row;
				}else if(empty($existsItems[$uniqueId]) && !empty($row['payout_time'])){
//					$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics->payout_time', $row['payout_time']);
//					$this->CI->utils->debug_log('========================getBetmasterGameLogStatistics->$existsItems[$uniqueId]', $existsItems[$uniqueId]);

					$row['isUniqueIdAlreadyExists'] = true;
					$availableRows[] = $row;
				}

//				$this->CI->utils->debug_log('########################getBetmasterGameLogStatistics->payout_time', $row['payout_time']);
//				$this->CI->utils->debug_log('########################getBetmasterGameLogStatistics->$existsItems[$uniqueId]', $existsItems[$uniqueId]);
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

}

///END OF FILE///////