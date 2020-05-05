<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Gamesos_game_logs
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
class Gamesos_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
		ini_set('display_errors', 1);


			error_reporting(E_ALL);
	}

	protected $tableName = "gamesos_game_logs";

	/**
	 * overview : insert gamesos game logs
	 *
	 * @param  array	$data
	 * @return boolean
	 */
	public function insertGamesosGameLogs($data) {
		//var_dump($data);
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : check if row already exist
	 *
	 * @param  int		$rowId
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		// $qry = $this->db->get_where($this->tableName, array('row_id' => $rowId));
		// if ($this->getOneRow($qry) == null) {
		// 	return false;
		// } else {
		// 	return true;
		// }
	}

	/**
	 * @param 	array	$data
	 *
	 * @return boolean
	 */
	function updateGameLogs($data) {
// 		$this->db->where('row_id', $data['row_id']);
// 		return $this->db->update($this->tableName, $data);
// 	}

// 	function getOnesgameGameLogStatistics($dateFrom, $dateTo) {
// 		$sql = <<<EOD
// SELECT onesg.player_id, onesg.player_currency, onesg.game_id, onesg.game_name, onesg.game_category, onesg.tran_id,onesg.total_amount,onesg.bet_amount,onesg.jackpot_contribution,
// onesg.win_amount, onesg.game_date, onesg.platform, onesg.external_uniqueid,onesg.response_result_id,
// gd.id as game_description_id, gd.game_name as game, gd.game_code as game_code, gd.game_type_id,
// gd.void_bet as void_bet
// FROM onesgame_game_logs as onesg
// LEFT JOIN game_description as gd ON onesg.game_id = gd.external_game_id
// JOIN game_provider_auth ON onesg.player_id = game_provider_auth.login_name and game_provider_id=?
// WHERE
// game_date >= ? AND game_date <= ?
// EOD;

// 		// $this->utils->debug_log($sql);

// 		$query = $this->db->query($sql, array(
// 			ONESGAME_API,
// 			$dateFrom,
// 			$dateTo,
// 		));

// 		return $this->getMultipleRow($query);
	}


	/**
	 * overview : get available rows
	 *
	 * @param  array		$rows
	 * @return array
	 */
	public function getAvailableRows($rows) { //var_dump($rows);
		$maxRowId = null;
		$arr = array();

		foreach ($rows as $row) {
			$arr[] = $row->TransactionId;
		}
		$this->db->select('transaction_id')->from($this->tableName)->where_in('transaction_id', $arr);
		$existsRow = $this->runMultipleRow();

		// $this->utils->printLastSQL();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array();
			foreach ($existsRow as $row) {
				$existsId[] = $row->transaction_id;
			}
			$availableRows = array();
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->TransactionId > $maxRowId) {
					$maxRowId = $row->TransactionId;
				}

				if (!in_array($row->TransactionId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
			foreach ($rows as $row) {
				if ($maxRowId == null || $row->TransactionId > $maxRowId) {
					$maxRowId = $row->TransactionId;
				}
			}
		}

		return array($availableRows, $maxRowId);
	}

	/**
	 * overview : get gamesos game log statistics
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @return array|null
	 */
	function getGamesosGameLogStatistics($dateFrom, $dateTo) {


		$sql = <<<EOD
SELECT
	gamesos.id,
	gamesos.transaction_date_time,
	gamesos.external_uniqueid,
	gamesos.gameshortcode,
	gamesos.response_result_id,
	gamesos.account_id as gameUsername,
	gamesos.category_name as game_type_str,
	gamesos.game_name_zh,
	gamesos.game_name_en,
	SUM(CASE gamesos.type WHEN 'DEBIT' THEN gamesos.amount ELSE 0 END) as bet_amount, 
	SUM(CASE gamesos.type WHEN 'CREDIT' THEN gamesos.amount ELSE 0 END) as result_amount,
 	SUM(CASE gamesos.type WHEN 'CREDIT' THEN gamesos.end_balance ELSE 0 END) as end_balance,
	gd.id as game_description_id,
	gd.game_name ,
	gd.game_code,
	gd.game_type_id

FROM
	gamesos_game_logs as gamesos
LEFT JOIN
	game_description as gd ON gd.game_code = gamesos.game_code COLLATE utf8_unicode_ci and gd.void_bet !=1 and gd.game_platform_id = ?
WHERE
   	gamesos.transaction_date_time >= ? AND gamesos.transaction_date_time <= ?

GROUP BY paired_transaction_id

EOD;

	$data = array(
			GAMESOS_API,
			$dateFrom,
			$dateTo
		);

	$query = $this->db->query($sql,$data);
  	 return $this->getMultipleRow($query);

	}



}

///END OF FILE///////
