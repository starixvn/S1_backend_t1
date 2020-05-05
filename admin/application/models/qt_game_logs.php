<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class  Qt_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "qt_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertQtGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	function isRowIdAlreadyExists($rowId) {
		$qry = $this->db->get_where($this->tableName, array('result_id' => $rowId));
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
		$this->db->where('result_id', $data['result_id']);
		return $this->db->update($this->tableName, $data);
	}

	function getQtGameLogStatistics($dateFrom, $dateTo) {
		$sql = <<<EOD
SELECT qt.id as id,
       qt.playerId as username, 
	   qt.transId,
       qt.totalBet as bet_amount, 
       qt.totalPayout as result_amount, 
       qt.completed, 
       qt.gameId as game_code,
       qt.external_uniqueid,
       qt.response_result_id,
       qt.gameId,
	   gd.id as game_description_id, 
	   gd.game_name as game, 
	   gd.game_code as game_code, 
	   gd.game_type_id,
	   gd.void_bet as void_bet,
	   gp.player_id,
	   gt.game_type
FROM qt_game_logs as qt
LEFT JOIN game_description as gd ON qt.gameId = gd.external_game_id and gd.game_platform_id=?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
JOIN game_provider_auth as gp ON qt.playerId = gp.login_name and game_provider_id=?
WHERE
qt.completed >= ? AND qt.completed <= ?
EOD;

		// $this->utils->debug_log($sql);
		$query = $this->db->query($sql, array(
			QT_API,
			QT_API,
			$dateFrom,
			$dateTo,
		));
		return $this->getMultipleRow($query);
	}

	public function getAvailableRows($rows) {
		if(!empty($rows)){
			$arr = array();
			foreach ($rows as $row) {
				$uniqueId = $row['id'];
				$arr[] = $uniqueId;
			}
			
			$this->db->select('transId')->from($this->tableName)->where_in('transId', $arr);
			$existsRow = $this->runMultipleRow();
			// $this->utils->printLastSQL();
			$availableRows = null;
			if (!empty($existsRow)) {
				$existsId = array();
				foreach ($existsRow as $row) {
					$existsId[] = $row->transId;
				}
				$availableRows = array();
				foreach ($rows as $row) {
					$uniqueId = $row['id'];
					if (!in_array($uniqueId, $existsId)) {
						$availableRows[] = $row;
					}
				}
			} else {
				//add all
				$availableRows = $rows;
			}
			return $availableRows;
		}else{
			return null;
		}
		
	}

}

///END OF FILE///////
