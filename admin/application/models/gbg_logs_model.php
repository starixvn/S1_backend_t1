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
class gbg_logs_model extends BaseModel {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('risk_score_model','player_model'));
		$this->load->library(array('gbg_api','authentication'));
	}

	protected $tableName = 'gbg_logs';

	public function addGbglogs($data){
		$response = false;
		if(!empty($data)){
			return $this->insertData($this->tableName, $data);
		}

		return $response;
	}

	public function getGbgInfoByPlayer($playerId){
		$response = false;
		if(!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$query = $this->db->get($this->tableName);
			$response = $this->getMultipleRowArray($query);
		}

		return $response;
	}

	public function getGbgLatestInfoByPlayer($playerId){
		$response = false;
		if(!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$this->db->order_by('id', 'desc');
			$this->db->limit(1);
			$query = $this->db->get($this->tableName);
			$response = $this->getOneRowArray($query);
		}
		return $response;
	}

	public function getPepAuthInfo($id){
		$response = false;
		if(!empty($id)) {
			$this->db->select("auth_id,timestamp,profile_name,profile_version,profile_state,result_codes,score,band_text,country");
			$this->db->where('id', $id);
			$query = $this->db->get($this->tableName);
			$response = $this->getOneRowArray($query);
		}
		return $response;
	}
	public function generate_gbg_authentication($playerId,$isAdmin = false){

		$playerDetails = $this->player_model->getPlayerInfoById($playerId);
		$pep_data = $this->risk_score_model->getRiskScoreInfo(self::R5);
		$gbgResponse = $this->gbg_api->auth_sp($playerDetails);
		$response = array();
		$this->utils->debug_log('GBG response API: -------------------------->>> ', $gbgResponse);

		if (!empty($gbgResponse)) {
			if($gbgResponse['status'] == 'error'){
				$response = array(
					'status' => $gbgResponse['status'],
					'msg' => lang("GBG credential and API url not configure or invalid."),
					'msg_type' => lang("GBG credential and API url not configure or invalid.")
				);
			} else {
				$gbgDetails = json_decode(json_encode($gbgResponse['response']->AuthenticateSPResult), true);
			
				$data = array(
					'player_id' => $gbgDetails['CustomerRef'],
					'external_uniqueid' => $gbgDetails['AuthenticationID'],
					'auth_id' => $gbgDetails['AuthenticationID'],
					'timestamp' => date('Y/m/d H:i:s',strtotime($gbgDetails['Timestamp'])),
					'profile_id' => $gbgDetails['ProfileID'],
					'profile_name' => $gbgDetails['ProfileName'],
					'profile_version' => $gbgDetails['ProfileVersion'],
					'profile_revision' => $gbgDetails['ProfileRevision'],
					'profile_state' => $gbgDetails['ProfileState'],
					'result_codes' => json_encode($gbgDetails['ResultCodes']),
					'score' => $gbgDetails['Score'],
					'band_text' => $gbgDetails['BandText'],
					'country' => $gbgDetails['Country'],
					'generated_by' => ($isAdmin)? $this->authentication->getUserId() : 0, 
				);

				$insertResponse = $this->addGbglogs($data);

				if ($insertResponse) {
					$this->risk_score_model->update_pep_status($playerId,$gbgDetails['BandText']);
					$response = array(
						'status' => 'success',
						'msg' => lang('Player authentication successful.'),
						'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
					);


					//this part auto block/suspended account it's depend of pep auth result OGP-8430
					/*
						- required attributes are change_status_to and tag_id
						- change_status_to: 1 = blocked , 5 = suspended
						- tag_id: adjust or add it to tag management in Player Tab SBE and the value is the tag id.
					*/
					$enable_change_player_status_pep_authentication = $this->utils->getConfig('enable_change_player_status_pep_authentication');

					if(!empty($enable_change_player_status_pep_authentication)){
						if($enable_change_player_status_pep_authentication){
							if(!empty($playerDetails)){
								if(isset($playerDetails['disable_player_update_status_pep'])){
									if(!$playerDetails['disable_player_update_status_pep']){
										if(!empty($pep_data) && !empty($gbgDetails)){
											if(isset($pep_data['rules']) && isset($gbgDetails['BandText'])){
												$rules = json_decode($pep_data['rules'],true);
												$this->utils->debug_log('PEP player BandText : ', str_replace('%20', ' ', $gbgDetails['BandText']));
												if(!empty($rules)){
													foreach ($rules as $key => $value) {
														if(isset($value['rule_name'])){
															if($value['rule_name'] == str_replace('%20', ' ', $gbgDetails['BandText'])){
																//change_status_to: 1 = blocked , 5 = suspended
																if(isset($value['change_status_to']) && isset($value['tag_id'])){
																	if(!empty($value['change_status_to']) && !empty($value['tag_id'])){
																		$this->utils->debug_log('PEP player auto update status passed and change status to: ',$value['change_status_to']);
																		$this->player_model->updatePlayer($playerId,
																				array(                    
																					'blocked' => $value['change_status_to'],
																				));

																		$data = array(
															                'playerId' => $playerId,
															                'tagId' => $value['tag_id'],
															                'status' => 1,
															                'createdOn' => date("Y-m-d H:i:s"),
															                'updatedOn' => date("Y-m-d H:i:s"),
															            );

															            $this->player_model->insertAndGetPlayerTag($data);
																	}
																}
															}
														}
														
													}
												}
											}
										}
									}
								}
							}		
						}
					}

				} else {
					$response = array(
						'status' => 'error',
						'msg' => lang('Player authentication failed. Please try again.'),
						'msg_type' => BaseController::MESSAGE_TYPE_ERROR
					);
				}
			}

		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('gbg error'),
				'msg_type' => BaseController::MESSAGE_TYPE_ERROR
			);
		}

		return $response;
	}
}