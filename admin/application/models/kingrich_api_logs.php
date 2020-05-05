<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class kingrich_api_logs extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'kingrich_api_logs';


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

	public function getAllData($where=null, $values=null){
		$this->db->select('kal.batch_transaction_id, kal.created_at, kal.status');
		$this->db->from('kingrich_api_logs kal');
        $this->db->where($where['0'], $values['0']);
        $this->db->where($where['1'], $values['1']);
        $query = $this->db->get();
        $result = $query->result_array();
        $result = json_decode(json_encode($result), true);
        return $result;
	}

}