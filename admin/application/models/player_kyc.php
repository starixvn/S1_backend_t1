<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get kyc Status
 * * Get player kyc Status
 * * Get/insert/update/ player kyc status
 *
 * @category Player KYC Status
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class player_kyc extends BaseModel {
	const CACHE_TTL = 600; # 10 minutes

	public function __construct() {
		parent::__construct();
		$this->load->model(array('system_feature'));
	}

	protected $tableName = 'kyc_player';

	private function getCacheKey($name) {
        return PRODUCTION_VERSION."|$this->tableName|$name";
    }

	/**
	 * @author Jhunel L. Ebero
	 * overview : Save/update Player KYC Status
	 *
	 * details : It's applicable for auto generate by system and manual generate by user
	 *
	 * @param int $player_id	player_id
	 * @param array $kyc_status	player_id //json_encode
	 * @param int $auto_generated	true=1 / false=0
	 */
	public function savePlayerKycStatus($playerId,$kyc_status,$auto_generated = FALSE,$new_status,$criteria_refresh = FALSE) {
		if(!empty($playerId)) {
			$this->load->model(['kyc_status_model','player_model']);

			$from_status=null;
			$to_status=null;

			$kycStatus=$this->getPlayerCurrentKycStatus($playerId);
			if(!empty($kycStatus)){
				if(isset($kycStatus['rate_code'])){
					$from_status = $kycStatus['rate_code'];
				}
			}
			$kycStatusInfo=$this->kyc_status_model->getKycStatusInfo($new_status);
			if(!empty($kycStatusInfo)){
				if(isset($kycStatusInfo['rate_code'])){
					$to_status = $kycStatusInfo['rate_code'];
				}
			}

			$this->db->where('player_id', $playerId);
			$query = $this->db->get($this->tableName);
			$data_history = ($query->num_rows() == 0) ? array() : json_decode($query->result_array()[0]['generated_by'],true);
			if(!$criteria_refresh) {
				$adminUser = array(
					"ui" => ($auto_generated) ? '0' : $this->authentication->getUserId(),//userId
					"un" => ($auto_generated) ? "System" : $this->authentication->getUsername(),//userName
					"ac" => ($from_status) ? lang('From status').' ( '.$from_status.' ) '.lang('to status').' ( '.$to_status.' )' : lang('Newly Generated'),
					"dt" => date("Y-m-d H:i:s"),//datetime
				);
				array_push($data_history ,$adminUser);
				$data = array(
					'player_id' => $playerId,
					'auto_generated' => $auto_generated,
				);
			}

			$data['kyc_status'] = $kyc_status;
			//var_dump(json_decode($query->result_array()[0]['generated_by'],true)[0]);die();
			//echo "<pre>";print_r($data);die();
			if($query->num_rows() == 0){
				$data['created_at'] =  date("Y-m-d H:i:s");
				$data['generated_by'] = json_encode($data_history);
				$this->db->insert($this->tableName, $data);
			} else {
				if($kyc_status != $query->result_array()[0]['kyc_status']) {
					$this->db->where('player_id', $playerId);
					$data['updated_at'] =  date("Y-m-d H:i:s");
					$data['generated_by'] = json_encode($data_history);
					$this->db->update($this->tableName, $data);
				}
			}

			// -- Update KYC Level under playedetails
			$current_kyc_status_id = $this->getPlayerCurrentKycStatus($playerId, true);
			$this->player_model->editPlayerDetails(array('kyc_status_id' => $current_kyc_status_id), $playerId);

			$this->utils->deleteCache($this->getCacheKey('player_kyc_status:'.$playerId));
			return $this->db->trans_status();
		}
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current kyc status by details of category of particular player
	 *
	 * details : return all content of table
	 *
	 * @param int $player_id	player_id
	 */
	public function getPlayerKycStatus($playerId) {
		$result = $this->utils->getJsonFromCache($this->getCacheKey('player_kyc_status:'.$playerId));
		if(!empty($result)) {
			return $result;
		}
		$this->db->where('player_id', $playerId);
		$query = $this->db->get($this->tableName);
		$result = $query->result_array();

		$this->utils->saveJsonToCache($this->getCacheKey('player_kyc_status:'.$playerId), $result, self::CACHE_TTL);

		return $result;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current kyc history logs of particular player
	 *
	 * details : return all content of table
	 *
	 * @param int $player_id	player_id
	 * @return array
	 */
	public function getPlayerKycHistory($playerId, $where, $values) {
		$this->db->where('player_id', $playerId);

		/**
		 * OGP-10058, KYC History Logs issue
		 * I commented the two where clauses for the date range filtration
		 * because we only keep one row for the kyc history logs per player.
		 * The date range filter will be checked on the later part of the code.
		 * Please see the loop on the json value below.
		 */
		
		//$this->db->where($where['0'], $values['0']);
		//$this->db->where($where['1'], $values['1']);
		
		$query = $this->db->get($this->tableName);
		//$result = $query->result_array();
		if ($query->num_rows() > 0) {
			$result = json_decode($query->result_array()[0]['generated_by'],true);

			foreach ($result as $key => $value) {
				if(isset($value['dt']) && !empty($value['dt'])){
					// -- check the date range
					$datetime 	= date('Y-m-d H:i:s', strtotime($value['dt']));
					$from 		= date('Y-m-d H:i:s', strtotime($values['0']));
					$to 		= date('Y-m-d H:i:s', strtotime($values['1']));

					if($datetime < $from || $datetime > $to){
						unset($result[$key]);
						continue;
					}
				}

				$result[$key]['ui'] = ($value['ui']) ? lang('Manual Generated') : lang('System Generated') ;
			}

			return $result;

		} else {
			$result = 0;
			return $result;
		}
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current kyc status of particular player
	 *
	 * details : return only current status
	 *
	 * @param int $player_id	player_id
	 * @param boolean $return_id_only	return_id_only
	 * @return array
	 */
	public function getPlayerCurrentKycStatus($playerId, $return_id_only = false) {
		$player_kyc_rows = $this->getPlayerKycStatus($playerId);
		$result = null;
		if(!empty($player_kyc_rows)){
			$result = json_decode($player_kyc_rows[0]['kyc_status'], true);
			if(!empty($result)){

				$kyc_id=null;
				foreach ($result as $key => $value) {
					if($value['current_kyc_status']){
						$kyc_id = $value['kyc_id'];
						break;
					}
				}

				if($return_id_only == true) return $kyc_id;

				if(!empty($kyc_id)){
					$this->load->model(array('kyc_status_model'));
					$result = $this->kyc_status_model->getKycStatusInfo($kyc_id);
				}
			}
		}
		
		return $result;
	}

	public function getPlayerCurrentKycLevel($playerId){
		$response = null;

		if($this->system_feature->isEnabledFeature('show_kyc_status')) {
			$getPlayerCurrentKycStatus = $this->getPlayerCurrentKycStatus($playerId);
			if(!empty($getPlayerCurrentKycStatus)){
				if(isset($getPlayerCurrentKycStatus['kyc_lvl'])) {
					$response = $getPlayerCurrentKycStatus['kyc_lvl'];
				}
			}
		}
		
		return $response;
	}
}