<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Class Entwine_game_logs
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
class Entwine_game_logs_model extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "entwine_game_logs";

	/**
	 * overview : insert entwine game logs
	 *
	 * @param  array	$data
	 * @return boolean
	 */
	public function insertEntwineGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * overview : check if row id already exist
	 *
	 * @param  int		$rowId
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {

		$qry = $this->db->get_where($this->tableName, array('uniqueid' => $rowId));
		if ($this->getOneRow($qry) == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * overview : update game logs
	 *
	 * @param  array		$data
	 * @return boolean
	 */
	function updateGameLogs($data) {
		$this->db->where('uniqueid', $data['uniqueid']);
		return $this->db->update($this->tableName, $data);
	}

	/**
	 * overview : get entwine game log statistics
	 *
	 * @param datetime	$dateFrom
	 * @param datetime	$dateTo
	 * @return null
	 */
	function getEntwineGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT entwine.id,
entwine.uniqueid,
entwine.game_name as username,
entwine.deal_id,
entwine.bet_amount,
entwine.hold as result_amount,
entwine.game_code as gameshortcode,
entwine.deal_enddate,
entwine.deal_startdate,
entwine.response_result_id,
entwine.external_uniqueid,
entwine.deal_details,
gd.id as game_description_id,
entwine.game_code as game,
entwine.game_code as game_code,
entwine.game_code as game_type,
gd.game_type_id,
gp.player_id
FROM entwine_game_logs as entwine
LEFT JOIN game_description as gd ON entwine.game_code = gd.external_game_id and gd.game_platform_id=?
JOIN game_provider_auth as gp ON entwine.game_name = gp.login_name and game_provider_id=?
WHERE
entwine.deal_enddate >= ? AND entwine.deal_enddate <= ?
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			ENTWINE_API,
			ENTWINE_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	/**
	 * overview : get available rows
	 *
	 * @param  array	$rows
	 * @return array|null
	 */
	public function getAvailableRows($rows) {
		
		if (!empty($rows)) {
			$arr = array();
			foreach ($rows as $row) {
				$deal_id = (isset($row['deal_id'])) ? $row['deal_id']: isset($row['id']);
				$uniqueId = $deal_id;
				$arr[] = $uniqueId;
			}
			$this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->deal_id;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					$uniqueId = $row['deal_id'];
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

}

///END OF FILE///////
