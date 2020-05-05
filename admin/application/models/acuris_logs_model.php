<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get acuris c6 Status
 * * Get player acuris c6 Status
 * * Get/insert/update/ player acuris c6 status
 *
 * @category Player Acuris c6 Status
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class acuris_logs_model extends BaseModel {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('risk_score_model','player_model'));
		$this->load->library(array('acuris_api','authentication'));
	}

	protected $tableName = 'acuris_logs';

	public function generate_acuris_authentication($playerId,$isAdmin = false){

		$playerDetails = $this->player_model->getPlayerInfoById($playerId);
		$c6_data = $this->risk_score_model->getRiskScoreInfo(self::R8);
		$acurisResponse = $this->acuris_api->auth_sp($playerDetails);
		$response = array();

		$this->utils->debug_log('acuris response API: -------------------------->>> ', $acurisResponse);

		if (!empty($acurisResponse)) {
			if(isset($acurisResponse['message']) && !isset($acurisResponse['recordsFound'])){
				if(!empty($acurisResponse['message'])){
					$response = array(
						'status' => 'error',
						'msg' => $acurisResponse['message'],
						'msg_type' => BaseModel::MESSAGE_TYPE_ERROR
					);
				}
			} else {
				if(!empty($acurisResponse['matches'])){
					$valid_matches = array();

					foreach ($acurisResponse['matches'] as $key => $value) {
						$person = isset($value['person']) ? $value['person'] : null;


						if ( !empty($person) ) {

							if( empty($valid_matches) ) {
								$valid_matches = $value;
							} else {
								if(isset($valid_matches['score']) && isset($value['score'])){
									if( $value['score'] > $valid_matches['score'] ){
										$valid_matches = $value;
									}
								}
							}
							
							$data = array(
								'player_id' => $playerId,
								'score' => $value['score'],
								'person_id' => isset($person['id']) ? $person['id'] : null,
								'title' => isset($person['title']) ? json_encode($person['title']) : null,
								'alternative_title' => isset($person['alternativeTitle']) ? json_encode($person['alternativeTitle']) : null,
								'forename' => isset($person['forename']) ? $person['forename'] : null,
								'middlename' => isset($person['middlename']) ? $person['middlename'] : null,
								'surname' => isset($person['surname']) ? $person['surname'] : null,
								'date_of_birth' => isset($person['dateOfBirth']) ? $person['dateOfBirth'] : null,
								'year_of_birth' => isset($person['yearOfBirth']) ? $person['yearOfBirth'] : null,
								'date_of_death' => isset($person['dateOfDeath']) ? $person['dateOfDeath'] : null,
								'year_of_death' => isset($person['yearOfDeath']) ? $person['yearOfDeath'] : null,
								'is_deceased' => isset($person['isDeceased']) ? $person['isDeceased'] : null,
								'gender' => isset($person['gender']) ? $person['gender'] : null,
								'nationality' => isset($person['nationality']) ? json_encode($person['nationality']) : null,
								'image_url' => isset($person['imageURL']) ? $person['imageURL'] : null,
								'telephone_number' => isset($person['telephoneNumber']) ? $person['telephoneNumber'] : null,
								'fax_number' => isset($person['faxNumber']) ? $person['faxNumber'] : null,
								'mobile_number' => isset($person['mobileNumber']) ? $person['mobileNumber'] : null,
								'email' => isset($person['email']) ? $person['email'] : null,
								'pep_level' => isset($person['pepLevel']) ? $person['pepLevel'] : null,
								'is_pep' => isset($person['isPEP']) ? $person['isPEP'] : null,
								'is_sanctions_current' => isset($person['isSanctionsCurrent']) ? $person['isSanctionsCurrent'] : null,
								'is_sanctions_previous' => isset($person['isSanctionsPrevious']) ? $person['isSanctionsPrevious'] : null,
								'is_law_enforcement' => isset($person['isLawEnforcement']) ? $person['isLawEnforcement'] : null,
								'is_financial_regulator' => isset($person['isFinancialregulator']) ? $person['isFinancialregulator'] : null,
								'is_disqualified_director' => isset($person['isDisqualifiedDirector']) ? $person['isDisqualifiedDirector'] : null,
								'is_insolvent' => isset($person['isInsolvent']) ? $person['isInsolvent'] : null,
								'is_adverse_media' => isset($person['isAdverseMedia']) ? $person['isAdverseMedia'] : null,
								'addresses' => isset($person['addresses']) ? json_encode($person['addresses']) : null,
								'aliases' => isset($person['aliases']) ? json_encode($person['aliases']) : null,
								'articles' => isset($person['articles']) ? json_encode($person['articles']) : null,
								'sanctions' => isset($person['sanctions']) ? json_encode($person['sanctions']) : null,
								'notes' => isset($person['notes']) ? json_encode($person['notes']) : null,
								'linked_businesses' => isset($person['linkedBusinesses']) ? json_encode($person['linkedBusinesses']) : null,
								'linked_person' => isset($person['linkedPersons']) ? json_encode($person['linkedPersons']) : null,
								'political_positions' => isset($person['politicalPositions']) ? json_encode($person['politicalPositions']) : null,
								'generated_by' => ($isAdmin)? $this->authentication->getUserId() : 0,
								'created_at' => $this->utils->getCurrentDatetime()
							);

							$insertResponse = $this->addAcurislogs($data);
						}
					}
				} else {
					$data = array(
								'player_id' => $playerId,
								'generated_by' => ($isAdmin)? $this->authentication->getUserId() : 0,
								'created_at' => $this->utils->getCurrentDatetime()
							);
					$insertResponse = $this->addAcurislogs($data);
				}

				if( !empty($valid_matches) ) {
					$person = isset($valid_matches['person']) ? $valid_matches['person'] : null;
					if(!empty($person)){
						$acuris_c6_config = isset($this->utils->getConfig('acuris_c6_config')['sanctions']) ? $this->CI->utils->getConfig('acuris_c6_config')['sanctions'] : null;
						if(!empty($acuris_c6_config)){
							foreach ($acuris_c6_config as $key => $value) {
								if($value['enable']){
									if( isset($person[$value['reponse_var']]) ) {
										if( $person[$value['reponse_var']] ){
											$this->risk_score_model->update_c6_status($playerId,self::C6_True);
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
										if(!empty($c6_data)){
											if(isset($c6_data['rules'])){
												$rules = json_decode($c6_data['rules'],true);
												if(!empty($rules)){

												
													foreach ($rules as $key => $val) {
														if(isset($val['rule_name'])){
															if($val['rule_name'] == self::C6_True) {
																//var_dump("test");die();
																if(isset($val['change_status_to'])){
																	if(!empty($val['change_status_to'])){
																		$this->utils->debug_log('C6 player auto update status passed and change status to: ',$val['change_status_to']);
																		$this->player_model->updatePlayer($playerId,
																				array(                    
																					'blocked' => $val['change_status_to'],
																				));
																	}
																}

																if(isset($val['tag_id'])){
																	if(!empty($val['tag_id'])){
																		$this->utils->debug_log('C6 player auto update tag to: ',$val['tag_id']);
																		$data = array(
															                'playerId' => $playerId,
															                'tagId' => $val['tag_id'],
															                'status' => self::DB_TRUE,
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
											
											break;
										}
									}
								}
							}
						}
					}
				} else {
					$this->risk_score_model->update_c6_status($playerId,self::C6_False);
				}

				$response = array(
					'status' => 'success',
					'msg' => lang('Player authentication successful.'),
					'msg_type' => BaseModel::MESSAGE_TYPE_SUCCESS
				);
			}
		}
		
		//print("<pre>".print_r($acurisResponse,true)."</pre>");
		//print("<pre>".print_r($response,true)."</pre>");
		return $response;

		

		
		
	}

	public function addAcurislogs($data){
		$response = false;
		if(!empty($data)){
			return $this->insertData($this->tableName, $data);
		}

		return $response;
	}

	public function getAcurisInfoByPlayer($playerId){
		$response = false;
		if(!empty($playerId)) {
			$this->db->where('player_id', $playerId);
			$query = $this->db->get($this->tableName);
			$response = $this->getMultipleRowArray($query);
		}

		return $response;
	}

	public function getAcurisInfoByID($acurisLogId){
		$response = false;
		if(!empty($acurisLogId)) {
			$this->db->where('id', $acurisLogId);
			$query = $this->db->get($this->tableName);
			$response = $this->getOneRowArray($query);
		}

		return $response;
	}

	public function getAcurisLatestInfoByPlayer($playerId){
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
}