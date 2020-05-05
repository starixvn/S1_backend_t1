<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class kingrich_summary_report extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'kingrich_summary_reports';


	public function insertRecord($data){
		$response = false;
		if(!empty($data)){
			$dataExist = $this->dataExist($data);
			if( empty($dataExist) ){
				$data["created_at"] = $this->utils->getNowForMysql();
				$response = $this->insertData($this->tableName, $data);
			} else {
				$data["id"] = (int)$dataExist["id"];
				$data["updated_at"] = $this->utils->getNowForMysql();
				$dataExist = $this->dataUpdate($data);
			}
		}
		return $response;
	}

	public function dataExist($data = null){
		if( !empty($data) ) {
			$this->db->select('id');
			$this->db->from($this->tableName);
	        $this->db->where("settlement_date", $data["settlement_date"]);
	        $this->db->where("kingrich_game_type", $data["kingrich_game_type"]);
	        $this->db->where("currency", isset($data["currency"]) ? $data["currency"] : '');
	        $qry = $this->db->get();
	        return $this->getOneRowArray($qry);
		}
	}

	public function dataUpdate($data = null){
		if( !empty($data) ) {
			$this->db->where('id', $data["id"]);
			$this->db->set($data);
			$success = $this->runAnyUpdate($this->tableName);
		}
	}

}