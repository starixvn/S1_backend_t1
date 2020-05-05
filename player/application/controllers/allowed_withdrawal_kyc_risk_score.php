<?php
trait allowed_withdrawal_kyc_risk_score {

	public function generate_allowed_withdrawal_status($playerId) {

		$this->load->model(array('player_kyc','risk_score_model'));

		$this->load->model(array('player_kyc','risk_score_model'));
		return $this->risk_score_model->generate_allowed_withdrawal_status($playerId);
	}

	public function allowed_withdrawal_status_chart(){
		$this->load->model(array('risk_score_model','kyc_status_model'));
		$kycInfo = $this->kyc_status_model->getAllKycStatus();
		$riskScoreInfo = $this->risk_score_model->getRiskScoreInfo(7);
		$kyc_lvl = array();
		$risk_lvl = array();

		foreach ($kycInfo as $key => $value) {
			$kyc_lvl[] = $value['kyc_lvl'];
		}

		foreach (json_decode($riskScoreInfo->rules,true) as $key => $value) {
			$risk_lvl[] = $value['risk_score'];
		}

		$data = array(
			'kyc_lvl' => array(
						lang('Not Verified') => array(
								lang('Low') => self::FALSE,
								lang('Medium') => self::FALSE,
								lang('High') => self::FALSE,
								lang('Very High') => self::FALSE,
							),
						lang('Low') => array(
								lang('Low') => self::TRUE,
								lang('Medium') => self::TRUE,
								lang('High') => self::FALSE,
								lang('Very High') => self::FALSE,
							),
						lang('Medium') => array(
								lang('Low') => self::TRUE,
								lang('Medium') => self::TRUE,
								lang('High') => self::TRUE,
								lang('Very High') => self::FALSE,
							),
						lang('High') => array(
								lang('Low') => self::TRUE,
								lang('Medium') => self::TRUE,
								lang('High') => self::TRUE,
								lang('Very High') => self::TRUE,
							),
					),
		);

		if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest' && $this->input->server('REQUEST_METHOD') == 'POST') {
			echo json_encode($data);
		} else {
			return $data;
		}
	}
	//============================= KYC =====================================================

	/**
	 * @author Jhunel L. Ebero
	 * overview : Summitted documment validation
	 *
	 * details : Check if the uploaded documents match the information to the system, such as: register name and proof of address
	 *
	 * @param int $player_id	player_id
	 */
	public function player_valid_identity_and_proof_of_address($playerId) {
		$verified = $this->get_verification_info($playerId)['valid_identity_and_address']['status'];
		return ($verified) ? TRUE : FALSE ;
	}

	public function get_verification_info($playerId){
		$this->load->model(array('player_model'));
		$this->set_default_value_proof_filename($playerId);
		$verified = json_decode($this->player_model->getPlayerInfoById($playerId)['proof_filename'],true)['verification'];
		$data = array(
				"identity_verification" => $verified['identity_verification'],
				"provide_proof_valid_docs" => $verified['provide_proof_valid_docs'],
				"valid_identity_and_address" => $verified['valid_identity_and_address'],
			);
		return $data;
	}

	public function set_default_value_proof_filename($playerId) {
		$this->load->model('kyc_status_model');
		return $this->kyc_status_model->set_default_value_proof_filename($playerId);
	}

	//=============================== Risk score ==========================================
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
		//var_dump($val);die();
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
				if($val >= $numFrom && $val <= $numTo) {
					$score = $value['risk_score'];
					break;
				}
			} elseif($formula == 0 && $val == 0) {
				$score = $value['risk_score'];
				break;
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
	public function generate_player_risk_score($playerId) {
		$this->load->model('risk_score_model');

		$risk_scores = $this->risk_score_model->getAllRiskScore();
		$total_score = 0;

		foreach ($risk_scores as $key => $value) {
			$score = 0;
			$highest_method = "";
			$lastest_method = "";
			switch ($value['id']) {
				case self::R1:
						$total_deposit = $this->check_risk_total_deposit($playerId);
						$score = $this->calc_risk_score_with_formula($total_deposit,$value['rules']);
						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => '<a href="javascript:void(0)" onclick="showDepositList('.$playerId.');">'. number_format($total_deposit,2,'.',',') .'</a>',
								'score' => $score,
							);
					break;
				case self::R2:
						$total_withdrawal = $this->check_risk_total_withdrawal($playerId);
						$score = $this->calc_risk_score_with_formula($total_withdrawal,$value['rules']);
						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => '<a href="javascript:void(0)" onclick="showWithdrawList('.$playerId.');">'. number_format($total_withdrawal,2,'.',',') .'</a>',
								'score' => $score,
							);
					break;
				case self::R3:
						$deposit_method = $this->check_risk_deposit_method($playerId);
						if(!empty($deposit_method)) {
							foreach (json_decode($value['rules'],true) as $key => $valuei) {
								if(in_array($valuei['rule_name'], $deposit_method)) {
									if($score < $valuei['risk_score']){
										$score = $valuei['risk_score'];
										$highest_method = $valuei['rule_name'];
									}
									$lastest_method = $valuei['rule_name'];
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

						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $deposit_details,
								'score' => $score,
							);
					break;
				case self::R4:
						$country = $this->check_risk_player_country($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $country) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => (empty($country)? lang('lang.norecord') : $country),
								'score' => $score,
							);
					break;
				case self::R5:
						$pep = $this->check_risk_player_PEP($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $pep) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $pep,
								'score' => $score,
							);
					break;
				case self::R6:
						$proof_of_identity = $this->check_risk_player_proof_of_identity($playerId);
						foreach (json_decode($value['rules'],true) as $key => $valuei) {
							if($valuei['rule_name'] == $proof_of_identity) {
									$score = $valuei['risk_score'];
							}
						}
						$data[$value['id']] = array(
								'risk_id' => $value['id'],
								'rule_name' => '',
								'risk_score' => '',
								'generated_result' => $proof_of_identity,
								'score' => $score,
							);
					break;
				default:
					# code...
					break;
			}
			$total_score += $score;
		}

		$response['data'] = $data;
		$response['total_score'] = $total_score;

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
	public function check_risk_total_deposit($playerId,$number_of_days = 30) {
		$this->load->model(array('transactions'));
		$date_from = date("Y-m-d 00:00:00",strtotime('-'. $number_of_days .' days'));
		$date_to = date("Y-m-d H:i:s");
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
		$this->load->model(array('transactions'));
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
		$this->load->model(array('transactions'));

		$data = $this->transactions->getPlayerDepositsTrasactionList($playerId);

		if(!empty($data['payment_method'])){
			$data = array_unique($data['payment_method']);
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
		$this->load->model(array('transactions'));

		$this->load->model(array('player_model'));
		$player_country = $this->player_model->getPlayerInfoById($playerId)['country'];
		return $player_country;
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
		$this->load->model(array('risk_score_model'));
		$current_pep_status = $this->player_model->getPlayerInfoById($playerId)['pep_status'];
		$pep_data = $this->risk_score_model->getRiskScoreInfo(self::R5);
		$rules = json_decode($pep_data['rules'],true);
		if(!empty($rules)){
			if (empty($current_pep_status)) {		
				foreach ($rules as $key => $value) {
					if($value['risk_score'] == 0){
						$this->update_pep_status($playerId, $value['rule_name']);
						$current_pep_status = $value['rule_name'];
					}
				}
			} else {
				$isExist = false;
				foreach ($rules as $key => $value) {
					if($value['rule_name'] == $current_pep_status){
						$isExist = true;
					}
				}

				if(!$isExist){
					foreach ($rules as $key => $value) {
						if($value['risk_score'] == 0){
							$this->update_pep_status($playerId, $value['rule_name']);
							$current_pep_status = $value['rule_name'];
						}
					}
				}
			}
		}
		
		return $current_pep_status;
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
		$poi = ($this->player_valid_identity_and_proof_of_address($playerId))? 'Consistent': 'Inconsistent';
		return $poi;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Verify proof of identy of the particular player
	 *
	 * details : check if player is consistent or inconsistent base on there submitted documents
	 *
	 * @param int $player_id	player_id
	 *
	 * @return json
	 */
	public function get_risk_score_info($riskId) {
		$this->load->model(array('risk_score_model'));
		$data = $this->risk_score_model->getRiskScoreInfo($riskId);
		echo json_encode($data);
		//return $data;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : get deposit history list by player
	 *
	 * details : get deposit history list by player
	 *
	 * @param int $playerId	player_id
	 *
	 * @return json
	 */
	public function getDepositHistoryList($playerId){
		$this->load->model(array('sale_order'));
		$data = $this->sale_order->getSaleOrderByPlayerId($playerId);
		$response = array();

		foreach ($data as $key => $value) {
			if($value['status'] == self::Settled){
				$response[] = array(
						'player_submit_datetime' => $value['player_submit_datetime'],
						'amount' => number_format($value['amount'],2,'.',','),
						'secure_id' => $value['secure_id'],
					);
			}
		}

		echo json_encode($response);
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : get withdrawal history list by player
	 *
	 * details : get withdrawal history list by player
	 *
	 * @param int $playerId	player_id
	 *
	 * @return json
	 */
	public function getWithdrawalHistoryList($playerId){
		$this->load->model(array('player'));
		$data = $this->player->getPlayerWithdrawalHistory($playerId, $limit, $offset);
		$response = array();

		foreach ($data as $key => $value) {
			if($value['dwStatus'] == "paid"){
				$response[] = array(
						'dwDateTime' => $value['dwDateTime'],
						'transactionType' => $value['transactionType'],
						'amount' => number_format($value['amount'],2,'.',','),
						'transactionCode' => $value['transactionCode'],
					);
			}
		}

		echo json_encode($response);
	}
}