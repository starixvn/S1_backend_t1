<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class kingrich_scheduler_logs extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'kingrich_scheduler_logs';


	public function insertRecord($data){
		$response = false;
		if(!empty($data)){
			$response = $this->insertData($this->tableName, $data);
		}
		return $response;
	}

	public function getAllRecords(){
		$this->db->select('*');
		$this->db->from($this->tableName);
		return $this->runMultipleRowArray();
	}

	public function getRecordsBySchedulerId($scheduler_id){
		$this->db->select('sl.*, al.status as status');
		$this->db->from('kingrich_scheduler_logs sl');
		$this->db->join('kingrich_api_logs al', 'sl.batch_transaction_id = al.batch_transaction_id', 'left');
		$this->db->where('sl.scheduler_id', $scheduler_id);
		return $this->runMultipleRowArray();
	}

	public function getGrandTotalBySchedulerId($scheduler_id){
		$this->db->select_sum('total');
		$this->db->from($this->tableName);
		$this->db->where('scheduler_id', $scheduler_id);

		return floatval($this->runOneRowOneField('total'));
	}

}