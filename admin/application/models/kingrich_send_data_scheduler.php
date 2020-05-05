<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

class kingrich_send_data_scheduler extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = 'kingrich_send_data_scheduler';


	public function insertUpdateRecord($data){
		$response = false;
		if(!empty($data)){
			if(!empty($data['id'])){
				$this->db->where('id', $data['id']);
				$this->db->set($data);
				return $this->runAnyUpdate($this->tableName);
			} else {
				return $this->insertData($this->tableName, $data);
			}
		}
		return $response;
	}

	public function getAllRecords(){
		$this->db->select('*');
		$this->db->from($this->tableName);
		return $this->runMultipleRowArray();
	}

	public function getTotalActiveSchedule() {
		$included_status = [self::PENDING,self::ONGOING,self::PAUSED];
		$this->db->select('status as label, COUNT(*) as value');
		$this->db->where_in('status', $included_status);
		$this->db->from($this->tableName);
		$this->db->group_by('status');
		$response = $this->runMultipleRowArray();

		if (empty($response)) {
			return [
				'morris' => [[ 'label' => lang('No Schedule'), 'value' => 0 ]] ,
				'cat_cues' => 'others'
			];
		} else {
			$cat_cues = [];
			$kingrich_scheduler_status = $this->utils->getConfig('kingrich_scheduler_status');

			foreach ($response as $key => $value) {
				$this->utils->debug_log(__METHOD__, $key, $value);
				$response[$key] = [
						'label' => $kingrich_scheduler_status[$value['label']]['label'] ,
				 		'value' => $value['value']
					];
				$cat_cues[] = $kingrich_scheduler_status[$value['label']]['label'];
			}

			return [ 'morris' => $response, 'cat_cues' => $cat_cues ];
		}
	}

	public function getScheduler_data($data_id){
		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->where('id', $data_id);
		return $this->runOneRowArray();
	}

	public function updateStatus($data){
		$response = false;
		if(!empty($data)){
			if(!empty($data['id'])){
				$this->db->where('id', $data['id']);
				$this->db->set($data);
				return $this->runAnyUpdate($this->tableName);
			}
		}
		return $response;
	}

	public function getOngoingSchedule(){
		$this->db->select('id as scheduler_id,date_from, date_to, currency');
		$this->db->where('status', self::ONGOING);
		$this->db->from($this->tableName);
		return $this->runMultipleRowArray();
	}

}