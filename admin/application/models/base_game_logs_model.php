<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * syncGameLogs, getAvailableRows, insertBatchGameLogs
 *
 * getGameLogStatistics, getGameLogStatisticsByIds
 *
 */
abstract class Base_game_logs_model extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	public function insertGameLogs($data) {
		// return $this->db->insert($this->tableName, $data);
		return $this->insertData($this->tableName, $data);
	}

	public function insertBatchGameLogs($data) {
		return $this->db->insert_batch($this->tableName, $data);
	}

	public function insertBatchGameLogsReturnIds($datas) {
        $ids = array();
        foreach ($datas as $data) {
            $this->db->insert($this->tableName, $data);
            array_push($ids, $this->db->insert_id());
        }

        return $ids;
	}

	public function getGameRecord($id) {
		return $this->db->get_where($this->tableName, array('id' => $id))->row_array();
	}

	public function updateBatchGameLogs($data, $key, $escape = TRUE) {
		if ($escape) {
			return $this->db->update_batch($this->tableName, $data, $key);
		} else {
			$this->db->set_update_batch($data, $key, FALSE);
			return $this->db->update_batch($this->tableName, NULL, $key);
		}
	}

	public function updateGameLog($id, $data){
		$this->db->where('id', $id);
		$this->db->set($data);
		return $this->runAnyUpdate($this->tableName);
	}

	public function getIdByUniqueid($uniqueid){
		$this->db->select('id')->from($this->tableName)->where('uniqueid', $uniqueid);
		return $this->runOneRowOneField('id');
	}

	public function syncGameLogs($data) {
		$id=$this->getIdByUniqueid($data['uniqueid']);
		if(!empty($id)){
			return $this->updateGameLog($id, $data);
			// return $this->db->update($this->tableName, $data);
		}else{
			return $this->insertGameLogs($data);
			// return $this->db->insert($this->tableName, $data);
		}
	}

	abstract function getGameLogStatistics($dateFrom, $dateTo);
	function getGameLogStatisticsByIds($ids){
		return null;
	}

	public function getAvailableRows($rows) {
		$this->db->select('uniqueid')->from($this->tableName)->where_in('uniqueid', array_column($rows, 'uniqueid'));
		$existsRow = $this->runMultipleRowArray();
		$availableRows = null;
		if (!empty($existsRow)) {
			$existsId = array_column($existsRow, 'uniqueid');
			$availableRows = array();
			foreach ($rows as $row) {
				$uniqueId = $row['uniqueid'];
				if (!in_array($uniqueId, $existsId)) {
					$availableRows[] = $row;
				}
			}
		} else {
			$availableRows = $rows;
		}
		return $availableRows;
	}

}

///END OF FILE///////