<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class MG_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "mg_game_logs";

	/**
	 * @param array data
	 *
	 * @return boolean
	 */
	function syncToMGGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param int rowId
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
	 * @param array data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('row_id', $data['row_id']);
		return $this->db->update($this->tableName, $data);
	}

//	function getMGGameLogStatisticsUnbuffered($dateFrom, $dateTo) {
//
//		$query=$this->getQueryOfMGGameLogStatistics($dateFrom, $dateTo);
//
//		return $this->getMultipleRowObjectUnbuffered($query);
//
//	}

	/**
	 * @param string $dateFrom
	 * @param string $dateTo
	 * @param bool $unffered
	 * @return query
	 *
	 */
	public function getQueryOfMGGameLogStatistics($dateFrom, $dateTo, $unffered){

		//if can't find player id , just ignore
		$sql = <<<EOD
SELECT mg_game_logs.id as id,
row_id,
game_end_time,
account_number,
total_wager as bet_amount,
total_payout-total_wager as result_amount,
display_name as game,
display_game_category as game_type,
game_description.id as game_description_id,
game_description.game_type_id as game_type_id,
external_uniqueid,
mg_game_logs.response_result_id,
game_provider_auth.player_id,
game_provider_auth.login_name as playername,
game_description.void_bet as void_bet,
mg_game_logs.external_game_id,
mg_game_logs.module_id,
mg_game_logs.client_id,
mg_game_logs.transaction_id
FROM mg_game_logs
JOIN game_provider_auth ON mg_game_logs.account_number = game_provider_auth.login_name and game_provider_auth.game_provider_id = ?
left join game_description on game_description.external_game_id=mg_game_logs.external_game_id and game_description.game_platform_id=? and game_description.void_bet!=1
WHERE
game_end_time >= ?
AND game_end_time <= ?
EOD;

		$query=null;
		if($unffered){
			$secondReadDB = $this->getSecondReadDB();

			// $this->utils->debug_log($sql, $dateFrom, $dateTo);
			$query = $secondReadDB->query_unbuffered_mysql($sql, array(MG_API, MG_API, $dateFrom, $dateTo));

		}else{

			$query = $this->db->query($sql, array(MG_API, MG_API, $dateFrom, $dateTo));

		}

		return $query;
	}

	public function getMGGameLogStatistics($dateFrom, $dateTo){

		$query=$this->getQueryOfMGGameLogStatistics($dateFrom, $dateTo, false);

		return $query ? $query->result() : null;
	}

	public function getAvailableRows($rows) {
		if(empty($rows)){
			return [[],null];
		}
		$maxRowId = null;
		$arr = array();
		foreach ($rows as $row) {
			$arr[] = $row->RowId;
		}

		$this->db->select('row_id')->from($this->tableName)->where_in('row_id', $arr);
		$existsRow = $this->runMultipleRowArrayUnbuffered();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->row_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->RowId > $maxRowId || strlen($row->RowId) > strlen($maxRowId)) {
					$maxRowId = $row->RowId;
				}

				if (!in_array($row->RowId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->RowId > $maxRowId || strlen($row->RowId) > strlen($maxRowId)) {
					$maxRowId = $row->RowId;
				}
			}
		}

		unset($existsRow);

		return array($availableRows, $maxRowId);
	}

}

///END OF FILE///////
