<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Oneworks_game_logs
 *
 * General behaviors include :
 *
 * * Add API game logs
 * * Check if row id already exist
 * * Get last version key
 * * Update game logs
 * * Get game logs statistics
 * * Get available rows
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Oneworks_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "oneworks_game_logs";
	protected $oneworks_game_result_table = "oneworks_game_result";

	/**
	 * overview: insert oneworks game logs
	 *
	 * @param array		$data
	 *
	 * @return boolean
	 */
	public function insertOneworksGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : check if rowId already exist
	 *
	 * @param  int		$rowId
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('trans_id' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : get last version key
	 *
	 * @return array
	 */
	public function getLastVersionKey() {
		$this->db->select('last_version_key,sync_datetime')->from($this->tableName);
		$this->db->order_by('sync_datetime', 'desc');
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array	$data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('trans_id', $data['trans_id']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get oneworks game log statistics
	 *
	 * @param 	datetime	$dateFrom
	 * @param 	datetime	$dateTo
	 * @return array
	 */
	function getOneworksGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT oneworks.id,
oneworks.trans_id,
oneworks.response_result_id,
oneworks.vendor_member_id as player_name,
oneworks.stake as bet_amount,
oneworks.winlost_amount as result_amount,
oneworks.sport_type as gameshortcode,
oneworks.transaction_time as start_at,
oneworks.transaction_time as end_at,
oneworks.transaction_time as transaction_time,
oneworks.odds,
oneworks.odds_type,
oneworks.hdp,
oneworks.league_id,
oneworks.home_id,
oneworks.away_id,
oneworks.bet_team,
oneworks.parlay_data,
oneworks.isLive,
oneworks.ticket_status,
oneworks.bet_type,
oneworks.match_id,
oneworks.sport_type,
oneworks.home_score,
oneworks.away_score,
oneworks.parlay_ref_no,
oneworks.parlay_type,
oneworks.combo_type,
oneworks.cash_out_data,
oneworks.home_hdp,
oneworks.away_hdp,
oneworks.original_stake,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gt.game_type
FROM oneworks_game_logs as oneworks
LEFT JOIN game_description as gd ON oneworks.sport_type = gd.game_code AND gd.game_platform_id = ?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON oneworks.vendor_member_id = gp.login_name and game_provider_id=?
WHERE
oneworks.transaction_time >= ? AND oneworks.transaction_time <= ?
EOD;
#AND oneworks.ticket_status IN ('WON','LOSE','DRAW')
		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			ONEWORKS_API,
			ONEWORKS_API,
			$dateFrom,
			$dateTo,
		));
		// echo $this->db->last_query();exit();
		return $this->getMultipleRow($query);
	}

	/**
	 * overview : get available rows
	 *
	 * @param $rows
	 * @return array|null
	 */
	public function getAvailableRows($rows) {
		if (!empty($rows)) {
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['trans_id'];
				$arr[] = $uniqueId;
			}

			$this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->trans_id;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					$uniqueId = $row['trans_id'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				//add all
				$availableRows = $rows;
			}
			return $availableRows;
		} else {
			return null;
		}

	}

	public function getOneworksGameMatchIds($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT oneworks.match_id
FROM oneworks_game_logs as oneworks
WHERE
oneworks.transaction_time >= ? AND oneworks.transaction_time <= ?
EOD;
		$query = $this->db->query($sql, array(
			$dateFrom,
			$dateTo,
		));
		return array_column($query->result_array(), "match_id");
	}

	public function insertOneworksGameResult($data) {
		return $this->db->insert($this->oneworks_game_result_table, $data);
	}

	public function isMatchIdAlreadyExists($matchId) {
		$qry = $this->db->get_where($this->oneworks_game_result_table, array('match_id' => $matchId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	public function updateOneworksGameResult($data) {
		$this->db->where('match_id', $data['match_id']);
		return $this->db->update($this->oneworks_game_result_table, $data);
	}

	public function getMatchDetail($matchId)
	{
		$qry = $this->db->get_where($this->oneworks_game_result_table, array('match_id' => $matchId));
		return $this->getOneRow($qry);
	}

	/**
	 * getMatchDetailWithCache
	 * @param  int $matchId
	 * @return array $row
	 */
	public function getMatchDetailWithCache($matchId){

		$key=$this->oneworks_game_result_table.'-'.$matchId;
		$row=$this->utils->getJsonFromCache($key);
		if(!empty($row)){
			return $row;
		}

		$this->db->from($this->oneworks_game_result_table)->where('match_id', $matchId);
		$row=$this->runOneRowArray();

		return $row;
	}

}

///END OF FILE///////
