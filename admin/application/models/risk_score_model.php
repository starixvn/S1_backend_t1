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
class risk_score_model extends BaseModel {
	const CACHE_TTL = 600; # 10 minutes
	const CACHE_TTL_LONG = 3600; # 1 hour

	const zero_total = 0;
	const R1 = 'R1';
	const R2 = 'R2';
	const R3 = 'R3';
	const R4 = 'R4';
	const R5 = 'R5';
	const R6 = 'R6';
	const R7 = 'R7';
	const R8 = 'R8';
	const RC = 'RC';//risk score
	const player_no_attached_document = "player_no_attached_document";
	const player_depositor = "player_depositor";
	const player_identity_verification = "player_identity_verification";
	const player_valid_documents = "player_valid_documents";
	const player_valid_identity_and_proof_of_address = "player_valid_identity_and_proof_of_address";

	const RISK_SCORE_ALL = 'all';

	public function __construct() {
		parent::__construct();
		$this->load->model(array('player_kyc','player_model','kyc_status_model','riskscore_kyc_chart_management_model','player','system_feature','transactions'));
	}

	protected $tableName = 'risk_score';

    private function getCacheKey($name) {
        return PRODUCTION_VERSION."|$this->tableName|$name";
    }

	public function getAllRiskScore() {
		$query = $this->db->query("SELECT * FROM risk_score where category_name != 'RC' and status != 0");
		return $query->result_array();
	}

	public function getRiskScoreInfo($riskId) {
		$result = $this->utils->getJsonFromCache($this->getCacheKey('risk_score_info:'.$riskId));
		if(!empty($result)) {
			return $result;
		}

		$this->db->where('category_name', $riskId);
		$query = $this->db->get($this->tableName);
		$result = $query->row_array();
		$this->utils->saveJsonToCache($this->getCacheKey('risk_score_info:'.$riskId), $result, self::CACHE_TTL_LONG);
		return $result;
	}

	public function getRiskScoreCategory(){
		$select = "*";

		$this->db->select($select);
		$this->db->from($this->tableName);
		$this->db->where('status',self::TRUE);
		return $this->runMultipleRowArray();
	}

	public function updateRiksScoreByCategoryName($key,$data){
		$this->utils->deleteCache();
		$this->db->where('category_name', $key);
		$this->db->where('status',self::TRUE);
		return $this->runUpdate($data);
	}

	public function getRiskScoreInfoByCategoryName($name)
	{
		$this->db->where('category_name', $name);
		$query = $this->db->get($this->tableName);
		return $this->runOneRow();
	}

	public function generate_allowed_withdrawal_status($playerId) {
		$response = false;
		if($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')){
			$response = $this->utils->getJsonFromCache($this->getCacheKey('allowed_withdrawal_status:'.$playerId));
			if(!empty($response)) {
				$this->utils->debug_log("allowed_withdrawal_status getJsonFromCache response===== ", $response);
				$data = array(
					'allowed_withdrawal_status' => ($response) ? 1 : 0,
				);
				$this->utils->debug_log("allowed_withdrawal_status getJsonFromCache===== ", $data);
				$this->player->editPlayer($data, $playerId);
				return $response;
			}

			$this->kyc_status_model->getPlayerCurrentStatus($playerId);
			$risk_scores_chart = $this->getRiskScoreInfo(self::RC);
			$kyc_lvl = $this->player_kyc->getPlayerCurrentKycStatus($playerId);
			$risk_lvl = $this->calc_risk_score_with_formula($this->generate_player_risk_score($playerId)['total_score'],$risk_scores_chart['rules']);
			$response = false;
			$renderChart = $this->render_kyc_riskscore_chart();

			if(!empty($renderChart)){
				foreach ($renderChart as $key => $value) {
					if($key == $risk_lvl){
						if(!empty($value)){
							foreach ($value as $resultkey => $resultvalue) {
								if(isset($resultvalue['kyc_level']) && isset($kyc_lvl['rate_code'])){
									if($resultvalue['kyc_level'] == $kyc_lvl['rate_code']){
										$response = ($resultvalue['tag'] == 'Y') ;
										break;
									}
								}
							}
						}
					}
				}
			}

			if(!empty($response)) {
				$this->utils->saveJsonToCache($this->getCacheKey('allowed_withdrawal_status:'.$playerId), $response, self::CACHE_TTL);
			}
		}
		$this->utils->debug_log("allowed_withdrawal_status default response===== ", $response);
		$data = array(
			'allowed_withdrawal_status' => ($response) ? 1 : 0,
		);
		$this->utils->debug_log("allowed_withdrawal_status default===== ", $data);
		$this->player->editPlayer($data, $playerId);
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get the score of particular player by given rules
	 *
	 * details : return total score
	 *
	 * @param int $val	score the need to evaluate
	 * @param array $rules	set of rules to determine the correspond score
	 *
	 * @return string
	 */
	public function calc_risk_score_with_formula($val,$rules){
		$rules = json_decode($rules,true);
		$val = floatval($val);
		$score = 0;
		if(!empty($rules)){
			foreach ($rules as $key => $value) {
				$formula = $value['rule_name'];
				if(strpos($formula, '>=') !== false) {
					if ($val >= intval(str_replace(">=","",$formula))) {
						$score = $value['risk_score'];
						break;
					}
				} elseif(strpos($formula, '>') !== false) {
					if ($val > intval(str_replace(">","",$formula))) {
						$score = $value['risk_score'];
						break;
					}
				} elseif(strpos($formula, '<=') !== false) {
					if ($val <= intval(str_replace("<=","",$formula)) && $val != 0) {
						$score = $value['risk_score'];
						break;
					}
				} elseif(strpos($formula, '<') !== false) {
					if ($val < intval(str_replace("<","",$formula)) && $val != 0) {
						$score = $value['risk_score'];
						break;
					}
				} elseif(strpos($formula, '==') !== false) {
					$num1 = ($val == intval(str_replace(">=","",$formula)));
					$score = $value['risk_score'];
				} elseif(strpos($formula, '~') !== false) {
					$exploded = explode("~", $formula);
					$numFrom = intval($exploded[0]);
					$numTo = intval($exploded[1]);

					if ( $numFrom < 0 && $numTo < 0 && $val < 0 )
					{
					   if($val <= $numFrom && $val >= $numTo) {
							$score = $value['risk_score'];
							break;
						}
					} else {
						if($val >= $numFrom && $val <= $numTo) {
							$score = $value['risk_score'];
							break;
						}
					}
				} elseif($formula == 0 && $val == 0) {
					$score = $value['risk_score'];
					break;
				}
			}
		}

		return $score;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Generate latest Risk Score by player
	 *
	 * details : Generate latest Risk Score by checking the rules by category of risk action.
	 *
	 * @param int $playerId	player_id
	 *
	 * @return array
	 */
	public function generate_player_risk_score($playerId, $use_cache = true) {
		$response = $this->utils->getJsonFromCache($this->getCacheKey('player_risk_score:'.$playerId));
		if(!empty($response) && $use_cache) {

			// -- Save info to player details
			$this->saveRiskScoreToPlayerDetails($playerId, $response['total_score']);
			return $response;
		}

		$risk_scores = $this->getAllRiskScore();
		$total_score = 0;
		foreach ($risk_scores as $key => $value) {
			$score = 0;
			$highest_method = "";
			$lastest_method = "";
			switch ($value['category_name']) {
				case self::R1:
						$total_deposit = $this->check_risk_total_deposit($playerId);
						$score = $this->calc_risk_score_with_formula($total_deposit,$value['rules']);
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => '<a href="javascript:void(0)" onclick="showDepositList('.$playerId.');">'. number_format($total_deposit,2,'.',',') .'</a>',
								'score' => $score,
							);
						$data_logs[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => number_format($total_deposit,2,'.',','),
								'score' => $score,
							);
					break;
				case self::R2:
						$total_withdrawal = $this->check_risk_total_withdrawal($playerId);
						$score = $this->calc_risk_score_with_formula($total_withdrawal,$value['rules']);
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => '<a href="javascript:void(0)" onclick="showWithdrawList('.$playerId.');">'. number_format($total_withdrawal,2,'.',',') .'</a>',
								'score' => $score,
							);
						$data_logs[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => number_format($total_withdrawal,2,'.',','),
								'score' => $score,
							);
					break;
				case self::R3:
						$deposit_method = $this->check_risk_deposit_method($playerId);
						if(!empty($deposit_method)) {
							foreach (json_decode($value['rules'],true) as $key => $valuei) {
								if(isset($valuei['flag_key'])){
									if(in_array($valuei['flag_key'], $deposit_method)) {
										if($score < $valuei['risk_score']){
											$score = $valuei['risk_score'];
											$highest_method = $valuei['rule_name'];
										}
										$lastest_method = $valuei['rule_name'];
									}
								}
							}
							/*$deposit_info = array(
										'highest_method' => $highest_method,
										'latest_method' => $lastest_method,
								);*/
							$deposit_details = lang('Highest Deposit Method'). ' :'. '<p class="text-danger">'. $highest_method .'</p>'.lang('Latest Deposit Method').' :'. '<p class="text-success">'.$lastest_method .'</p>';
						} else {
							$deposit_details = lang('No deposit yet');
						}

						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $deposit_details,
								'score' => $score,
							);
						$data_logs[$value['category_name']] = $data[$value['category_name']];
					break;
				case self::R4:
						$country = $this->check_risk_player_country($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $country) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => (empty($country)? lang('lang.norecord') : $country),
								'score' => $score,
							);
						$data_logs[$value['category_name']] = $data[$value['category_name']];
					break;
				case self::R5:
						$pep = $this->check_risk_player_PEP($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $pep) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $pep,
								'score' => $score,
							);
						$data_logs[$value['category_name']] = $data[$value['category_name']];
					break;
				case self::R6:
						$proof_of_identity = $this->check_risk_player_proof_of_identity($playerId);
							foreach (json_decode($value['rules'],true) as $key => $valuei) {
								if($valuei['rule_name'] == $proof_of_identity) {
										$score = $valuei['risk_score'];
								}
							}
							$data[$value['category_name']] = array(
									'risk_id' => $value['category_name'],
									'rule_name' => '',
									'risk_score' => '',
									'generated_result' => $proof_of_identity,
									'score' => $score,
								);
							$data_logs[$value['category_name']] = $data[$value['category_name']];
					break;
				case self::R7:
						$total_deposit = $this->transactions->getPlayerTotalDeposits($playerId);
						$total_withdrawal = $this->transactions->getPlayerTotalWithdrawals($playerId);
						$total = $total_withdrawal - $total_deposit;
						$score = $this->calc_risk_score_with_formula($total,$value['rules']);
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => '<a href="javascript:void(0)" onclick="showDepositList('.$playerId.');">'. number_format($total,2,'.',',') .'</a>',
								'score' => $score,
							);
						$data_logs[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => number_format($total,2,'.',','),
								'score' => $score,
							);
						break;
				case self::R8:
						$c6 = $this->check_risk_player_c6($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $c6) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['category_name']] = array(
								'risk_id' => $value['category_name'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $c6,
								'score' => $score,
							);
						$data_logs[$value['category_name']] = $data[$value['category_name']];
						break;
				default:
					# code...
					break;
			}
			$total_score += $score;
			$this->update_risk_score_logs($playerId,$value['category_name'],$data_logs[$value['category_name']],$total_score);
		}
			$this->update_last_risk_score_total_score($playerId,$total_score);
		$response['data'] = $data;
		$response['total_score'] = $total_score;

		$this->utils->saveJsonToCache($this->getCacheKey('player_risk_score:'.$playerId), $response, self::CACHE_TTL);


		// -- Save info to player details
		$this->saveRiskScoreToPlayerDetails($playerId, $response['total_score']);

		return $response;
	}

	/**
	 * Update player risk score under playerdetails
	 *
	 * @param  int  $player_id
	 * @param  integer $total
	 * @return boolean
	 */
	public function saveRiskScoreToPlayerDetails($player_id, $total = 0)
	{
		// -- Save info to player details

		$risk_final_info = array(
			'risk_score' => $total,
			'risk_level' => 'Low',
		);

		$risk_scores_chart = $this->getRiskScoreInfo(self::RC);

		if(!empty($risk_scores_chart) && isset($risk_scores_chart['rules']))
			$risk_final_info['risk_level'] = $this->calc_risk_score_with_formula($risk_final_info['risk_score'], $risk_scores_chart['rules']);

		$this->load->model('player_model');

		return $this->player_model->editPlayerDetails(array('risk_score_level' => json_encode($risk_final_info)), $player_id);
	}

	public function generate_total_risk_score($playerId){
		$response = 0;

		if($this->system_feature->isEnabledFeature('show_risk_score')) {
			$risk_score_info = $this->generate_player_risk_score($playerId);

			if(!empty($risk_score_info)){
				if(isset($risk_score_info['total_score'])){
					$response = $risk_score_info['total_score'];
				}
			}
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get total deposit of particular player
	 *
	 * details : return total deposit from current date back to the given number of days
	 *
	 * @param int $player_id	player_id
	 * @param int $number_of_days	player_id
	 *
	 * @return int
	 */
	public function check_risk_total_deposit($playerId,$number_of_days = 29) {
		$date_from = date("Y-m-d 00:00:00",strtotime('-'. $number_of_days .' days'));
		$date_to = date("Y-m-d 23:59:59");
		$total = $this->transactions->getPlayerTotalDeposits($playerId, $date_from, $date_to);
		return ($total) ? $total : self::zero_total;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get total withdrawal of particular player
	 *
	 * details : return total withdrawal from current date back to the given number of days
	 *
	 * @param int $player_id	player_id
	 * @param int $number_of_days	player_id
	 *
	 * @return int
	 */
	public function check_risk_total_withdrawal($playerId,$number_of_days = 30) {
		$date_from = date("Y-m-d 00:00:00",strtotime('-'. $number_of_days .' days'));
		$date_to = date("Y-m-d H:i:s");
		$total = $this->transactions->getPlayerTotalWithdrawals($playerId, FALSE , $date_from, $date_to);
		return ($total) ? $total : self::zero_total;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get all deposit method of particular player
	 *
	 * details : return tdeposti method and number of transaction
	 *
	 * @param int $player_id	player_id
	 *
	 * @return array
	 */
	public function check_risk_deposit_method($playerId) {
		$data = $this->transactions->getPlayerDepositsTrasactionList($playerId);

		$this->utils->debug_log('------------------------------------------check_risk_deposit_method decSQL : ',$data);

		if(!empty($data['payment_flag_key'])){
			$data = array_unique($data['payment_flag_key']);
		}

		return $data;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current country of particular player
	 *
	 * details : get current country of player
	 *
	 * @param int $player_id	player_id
	 *
	 * @return string
	 */
	public function check_risk_player_country($playerId) {
		$response = $this->utils->getJsonFromCache($this->getCacheKey('risk_player_country:'.$playerId));
		if(!empty($response)) {
			return $response;
		}
		$player_country = $this->player_model->getPlayerInfoById($playerId);
		$response = null;
		if(!empty($player_country)){
			if(!empty($player_country['residentCountry']) && isset($player_country['residentCountry'])){
				$response = $player_country['residentCountry'];
			}
		}

		$this->utils->saveJsonToCache($this->getCacheKey('risk_player_country:'.$playerId), $response, self::CACHE_TTL);

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current country of particular player
	 *
	 * details : get current country of player
	 *
	 * @param int $player_id	player_id
	 *
	 * @return string
	 */
	public function check_risk_player_PEP($playerId) {
		$response = $this->utils->getJsonFromCache($this->getCacheKey('risk_player_PEP:'.$playerId));
		if(!empty($response)) {
			return $response;
		}

		$this->load->model(array('gbg_logs_model'));

		if($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication')){
			if($this->kyc_status_model->player_depositor($playerId)){
				$getGbgInfoByPlayer = $this->gbg_logs_model->getGbgInfoByPlayer($playerId);
				if(empty($getGbgInfoByPlayer)){
					$this->gbg_logs_model->generate_gbg_authentication($playerId);
				}
			}
		}

		$current_pep_status = $this->player_model->getPlayerInfoById($playerId);
		$pep_data = $this->getRiskScoreInfo(self::R5);

		$response = null;
		$default_score = null;

		if(!empty($current_pep_status) && !empty($pep_data)) {
			if(isset($pep_data['rules'])){
				$rules = json_decode($pep_data['rules'],true);
				if(!empty($rules)){
					if (empty($current_pep_status['pep_status'])) {
						foreach ($rules as $key => $value) {
							if(isset($value['default_score'])){
								if($value['default_score']){
									$default_score = $value['rule_name'];
								}
							}
							if(isset($value['risk_score'])){
								if($value['risk_score'] == 0){
									$response = $value['rule_name'];
								}
							}
						}
						if(!empty($default_score)){
							$response = $default_score;
						}
						$this->update_pep_status($playerId, $response);
					} else {
						$response = $current_pep_status['pep_status'];

						$isExist = false;
						foreach ($rules as $key => $value) {
							if($value['rule_name'] == $response){
								$isExist = true;
							}
						}

						if($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && !$this->utils->getConfig('enable_change_player_pep_status_when_binding_ID3')){
							$getGbgInfoByPlayer = $this->gbg_logs_model->getGbgLatestInfoByPlayer($playerId);
							if(empty($getGbgInfoByPlayer)){
								$isExist = false;
							} else {
								$isExist = true;
								if($response != $getGbgInfoByPlayer['band_text']){
									$this->update_pep_status($playerId, $getGbgInfoByPlayer['band_text']);
									$response = $getGbgInfoByPlayer['band_text'];
								}
							}
						}

						if(!$isExist){
							foreach ($rules as $key => $value) {
								if(isset($value['default_score'])){
									if($value['default_score']){
										$default_score = $value['rule_name'];
									}
								}
								if(isset($value['risk_score'])){
									if($value['risk_score'] == 0){
										$response = $value['rule_name'];
									}
								}
							}
							if(!empty($default_score)){
								$response = $default_score;
							}
							$this->update_pep_status($playerId, $response);
						}
					}
				}
			}
		}

		$this->utils->saveJsonToCache($this->getCacheKey('risk_player_PEP:'.$playerId), $response, self::CACHE_TTL);

		return $response;
	}

		/**
	 * @author Jhunel L. Ebero
	 * overview : Verify proof of identy of the particular player
	 *
	 * details : check if player is consistent or inconsistent base on there submitted documents
	 *
	 * @param int $player_id	player_id
	 *
	 * @return string
	 */
	public function check_risk_player_proof_of_identity($playerId) {
		//$this->load->model(array('risk_score_model','player_kyc','kyc_status_model'));
		$player_kyc_status = $this->player_kyc->getPlayerKycStatus($playerId);
		$poi = ($this->kyc_status_model->player_valid_documents($playerId))? 'Consistent': 'Inconsistent';
		if(isset($player_kyc_status[0]['kyc_status'])){
			$player_current_kyc_status = json_decode($player_kyc_status[0]['kyc_status'],true);
			//echo "<pre>";print_r($player_current_kyc_status);die();
			$risk_item_info = array();
			$r6Info = $this->risk_score_model->getRiskScoreInfo(self::R6);
			if(!empty($r6Info)){
				$r6Info = json_decode($r6Info['rules'],true);
				if(!empty($r6Info)){
					foreach ($r6Info as $key => $value) {
						$status = false;
						if(isset($value['function_ref'])){
							if(!empty($value['function_ref'])){
								if($value['function_ref'] == 'kyc'){
									if(isset($value['target_function'])){
										if(!empty($player_current_kyc_status)){
											foreach ($player_current_kyc_status as $kyckey => $kycvalue) {
												$kyc_info = $this->kyc_status_model->getKycStatusInfo($kycvalue['kyc_id']);
												if(!empty($kyc_info)){
													if($kyc_info['rate_code'] == $value['target_function']){
														$status = $kycvalue['current_kyc_status'];
														break;
													}
												}
											}
										}
									}
								} else {
									if(isset($value['target_function'])){
										switch ($value['target_function']) {
											case self::player_no_attached_document:
												$status = !$this->kyc_status_model->checkNoAttachement($playerId);
												break;
											case self::player_depositor:
												$status = $this->kyc_status_model->player_depositor($playerId);
												break;
											case self::player_identity_verification:
												$status = $this->kyc_status_model->player_identity_verification($playerId);
												break;
											case self::player_valid_documents:
												$status = $this->kyc_status_model->player_valid_documents($playerId);
												break;
											case self::player_valid_identity_and_proof_of_address:
												$status = $this->kyc_status_model->player_valid_identity_and_proof_of_address($playerId);
												break;
											default:
												break;
										}
									}
								}

							}
						}
						$default_score = null;
						if(isset($value['default_score'])){
							$default_score = $value['default_score'];
						}
						$risk_item_info[] = array(
							'risk_score' => $value['risk_score'],
							'status' => $status,
							'rule_name' => $value['rule_name'],
							'default_score' => $default_score
						);
					}
				}

				if(!empty($risk_item_info)){
					$temp_score = 0;
					$temp_name = null;
					$default_risk_name = null;

					foreach ($risk_item_info as $key => $value) {

						if($value['status']){
							//if($value['risk_score'] >= $temp_score){
								$temp_name = $value['rule_name'];
								$temp_score = $value['risk_score'];
							//}
						}

						if($value['default_score']){

							$default_risk_name = $value['rule_name'];
						}
					}
					if(!empty($temp_name)){
						$poi = $temp_name;
					} else {
						$poi = $default_risk_name;
					}
					//echo "<pre>";print_r($default_risk_name);die();
				}

			}
		}
		return $poi;
	}

	public function update_pep_status($playerId,$pep_status){
		if(!empty($playerId) && !empty($pep_status)) {
			$this->utils->deleteCache($this->getCacheKey('risk_player_PEP:'.$playerId));
			$data = array(
				'pep_status' => str_replace('%20', ' ', $pep_status),
			);
			$this->player->editPlayer($data, $playerId);
			$response = array( 'status' => "success",'message' => lang("Update Successful"));// : array( 'status' => "fail",'message' => lang("Update
		}

		return $response;
	}

	public function render_kyc_riskscore_chart(){

		$kyc_list = $this->kyc_status_model->getAllKycStatus();
		$risk_score_info = $this->getRiskScoreInfo(self::RC);
		$risk_score_list = json_decode($risk_score_info['rules'],true);

		$renderChart = array();
		if(!empty($risk_score_list) && !empty($kyc_list)){
			foreach ($risk_score_list as $key => $value) {
				foreach ($kyc_list as $kyc_key => $kyc_value) {
					$chartDetail = $this->riskscore_kyc_chart_management_model->getChartCoordinateTag($kyc_value['rate_code'],$value['risk_score']);
					if(empty($chartDetail)){
						$this->riskscore_kyc_chart_management_model->addUpdateChart($kyc_value['rate_code'],$value['risk_score']);
						$chartDetail = $this->riskscore_kyc_chart_management_model->getChartCoordinateTag($kyc_value['rate_code'],$value['risk_score']);
					}

					$renderChart[$value['risk_score']][] = $chartDetail;
				}
			}
		}

		return $renderChart;
	}

	public function getPlayerCurrentRiskLevel($playerId){
		$response = null;

		if($this->system_feature->isEnabledFeature('show_risk_score')) {
			$generate_player_risk_score = $this->generate_player_risk_score($playerId);
			$risk_scores_chart = $this->getRiskScoreInfo(self::RC);
			if(!empty($generate_player_risk_score) && !empty($risk_scores_chart)){
				if(isset($generate_player_risk_score['total_score']) && isset($risk_scores_chart['rules'])){
					$response = $this->calc_risk_score_with_formula($generate_player_risk_score['total_score'],$risk_scores_chart['rules']);
				}
			}
		}

		return $response;
	}

	public function getRiskScoreLevels(){
		$risk_scores_chart = $this->getRiskScoreInfo(self::RC);
		if(!empty($risk_scores_chart['rules'])){
			$rules = json_decode($risk_scores_chart['rules'],true);
			if(!empty($rules)){
				$risk_score_levels = array_column($rules, 'risk_score');
				return $risk_score_levels;
			}
		}
		return null;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Get current c6 status from acuris particular player
	 *
	 * details : get current c6 status of player
	 *
	 * @param int $playerId	player_id
	 *
	 * @return string
	 */
	public function check_risk_player_c6($playerId) {
		$response = $this->utils->getJsonFromCache($this->getCacheKey('risk_player_c6:'.$playerId));
		if(!empty($response)) {
			return $response;
		}

		$this->load->model(array('acuris_logs_model'));

		if($this->utils->isEnabledFeature('enable_c6_acuris_api_authentication')){
			if($this->kyc_status_model->player_depositor($playerId)){
				$getC6InfoByPlayer = $this->acuris_logs_model->getAcurisInfoByPlayer($playerId);
				if(empty($getC6InfoByPlayer)){
					$this->acuris_logs_model->generate_acuris_authentication($playerId);
				}
			}
		}

		$current_c6_status = $this->player_model->getPlayerInfoById($playerId);
		$c6_data = $this->getRiskScoreInfo(self::R8);

		$response = null;
		$default_score = null;

		if(!empty($current_c6_status) && !empty($c6_data)) {
			if(isset($c6_data['rules'])){
				$rules = json_decode($c6_data['rules'],true);
				if(!empty($rules)){
					if (empty($current_c6_status['c6_status'])) {
						foreach ($rules as $key => $value) {
							if(isset($value['default_score'])){
								if($value['default_score']){
									$default_score = $value['rule_name'];
								}
							}
							if(isset($value['risk_score'])){
								if($value['risk_score'] == 0){
									$response = $value['rule_name'];
								}
							}
						}
						if(!empty($default_score)){
							$response = $default_score;
						}
						$this->update_c6_status($playerId, $response);
					} else {
						$response = $current_c6_status['c6_status'];

						$isExist = false;
						foreach ($rules as $key => $value) {
							if($value['rule_name'] == $response){
								$isExist = true;
							}
						}

						if(!$isExist){
							foreach ($rules as $key => $value) {
								if(isset($value['default_score'])){
									if($value['default_score']){
										$default_score = $value['rule_name'];
									}
								}
								if(isset($value['risk_score'])){
									if($value['risk_score'] == 0){
										$response = $value['rule_name'];
									}
								}
							}
							if(!empty($default_score)){
								$response = $default_score;
							}
							$this->update_c6_status($playerId, $response);
						}
					}
				}
			}
		}

		$this->utils->saveJsonToCache($this->getCacheKey('risk_player_c6:'.$playerId), $response, self::CACHE_TTL);

		return $response;
	}

	public function update_c6_status($playerId,$c6_status){
		if(!empty($playerId) && !empty($c6_status)) {
			$this->utils->deleteCache($this->getCacheKey('risk_player_c6:'.$playerId));
			$data = array(
				'c6_status' => str_replace('%20', ' ', $c6_status),
			);
			$this->player->editPlayer($data, $playerId);
			$response = array( 'status' => "success",'message' => lang("Update Successful"));// : array( 'status' => "fail",'message' => lang("Update
		}

		return $response;
	}

	public function update_risk_score_logs($playerId,$risk_score_category,$data,$total_score = null) {
		$this->load->model(array('risk_score_history_logs'));
		return $this->risk_score_history_logs->update_risk_score_logs($playerId,$risk_score_category,$data,$total_score);
	}

	public function update_last_risk_score_total_score($playerId,$total_score){
		$this->load->model(array('risk_score_history_logs'));
		return $this->risk_score_history_logs->update_last_data_total_score($playerId,$total_score);
	}

}