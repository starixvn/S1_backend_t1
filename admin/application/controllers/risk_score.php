<?php
trait risk_score {


	/**
	 * @author Jhunel L. Ebero
	 * overview : Display the current Risk score by player
	 *
	 * details : display current Risk score by player with detailed information per Risk Score Category
	 *
	 * @param int $playerId	player_id
	 */
	public function player_risk_score($playerId) {
		/*if (!$this->permissions->checkPermissions('show_risk_score')) {
			$this->error_access();
		} else {*/
			//$this->getDepositHistoryList($playerId);
			$total_score = 0;
			$this->load->model('risk_score_model');

			$player_risk_score = $this->generate_player_risk_score($playerId);
			$lang = $this->language_function->getCurrentLanguage();
			$this->loadTemplate('Player Management', '', '', 'player');

			$risk_scores = $this->risk_score_model->getAllRiskScore();
			foreach ($risk_scores as $key => $value) {
				$data[$key]['id']= json_decode($value['id'],true);
				$data[$key]['rules']= json_decode($value['rules'],true);
				$data[$key]['category']= $value['category_name'];
				$data[$key]['description']= $value['category_description'];
				$data[$key]['generated_result']= $player_risk_score['data'][$value['category_name']]['generated_result'];
				$data[$key]['score']= $player_risk_score['data'][$value['category_name']]['score'];
				$total_score += $player_risk_score['data'][$value['category_name']]['score'];
			}
			$response['total_score'] = $total_score;
			$response['data'] = $data;
			$response['risk_level'] = $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
			$response['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId)) ? lang('Yes') : lang('No');
			$this->load->view('player_management/player_risk_score', $response);
		//}
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
		return $this->risk_score_model->generate_player_risk_score($playerId);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->calc_risk_score_with_formula($val,$rules);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_total_deposit($playerId,$number_of_days);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_total_withdrawal($playerId,$number_of_days);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_deposit_method($playerId);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_player_country($playerId);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_player_PEP($playerId);
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
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_player_proof_of_identity($playerId);
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

		$number_of_days = 29;
		$date_from = date("Y-m-d 00:00:00",strtotime('-'. $number_of_days .' days'));
		$date_to = date("Y-m-d 23:59:59");
		$data = $this->sale_order->getSaleOrderByPlayerId($playerId, $date_from, $date_to);
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
		$data = $this->player->getPlayerWithdrawalHistory($playerId, null, null, 30);
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

	public function generate_gbg_authentication($playerId,$isAdmin = false){
		$this->load->model(array('gbg_logs_model'));
		$response = $this->gbg_logs_model->generate_gbg_authentication($playerId,$isAdmin);
		return $response;
	}

	public function player_pep($playerId) {
		$this->load->model('risk_score_model');
		$lang = $this->language_function->getCurrentLanguage();
		$this->loadTemplate('Player Management', '', '', 'player');
		$total_score = 0;

		$player_risk_score = $this->generate_player_risk_score($playerId);
		$risk_scores = $this->risk_score_model->getAllRiskScore();
		foreach ($risk_scores as $key => $value) {
			$data[$key]['id']= json_decode($value['category_name'],true);
			$data[$key]['rules']= json_decode($value['rules'],true);
			$data[$key]['category']= $value['category_name'];
			$data[$key]['description']= $value['category_description'];
			$data[$key]['generated_result']= $player_risk_score['data'][$value['category_name']]['generated_result'];
			$data[$key]['score']= $player_risk_score['data'][$value['category_name']]['score'];
			$total_score += $player_risk_score['data'][$value['category_name']]['score'];
		}

		$data['total_score'] = $total_score;

		$pep_data = $this->risk_score_model->getRiskScoreInfo(self::R5);
		$data['playerId'] = $playerId;
		$data['rules'] = json_decode($pep_data['rules'],true);
		$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId)) ? lang('Yes') : lang('No');
		$data['risk_level']	= $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
		$data['current_pep_status'] = $this->check_risk_player_PEP($playerId);
		$this->load->view('player_management/player_pep', $data);
	}

	public function update_pep_status($playerId,$pep_status = null){
		if(empty($pep_status)){
			$pep_status = $this->input->post('pep_status');
		}

		if(!$this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') || $this->utils->getConfig('enable_change_player_pep_status_when_binding_ID3')){
			$this->load->model('risk_score_model');
			$pep_status = str_replace('%20', ' ', $pep_status);
			$beforeAdjustmentStatus = $this->check_risk_player_PEP($playerId);
			$response = $this->risk_score_model->update_pep_status($playerId,$pep_status);
			if(!empty($response)){
				if($response['status'] == "success"){
					$playerInfo = $this->player_model->getPlayerInfoById($playerId);
					if(!empty($playerInfo)){
						$this->savePlayerUpdateLog($playerId, "User " . $this->authentication->getUsername() . " update manually the PEP Status of Account " . $playerInfo['username']. ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $beforeAdjustmentStatus . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $pep_status . ') ', $this->authentication->getUsername());

						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Manual Update PEP Status', "User " . $this->authentication->getUsername() . " update manually the PEP Status of Account " . $playerInfo['username']. ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $beforeAdjustmentStatus . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $pep_status . ') ');
					}
				}

				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult($response);
					return;
				} else {
					return $response;
				}
			}
		}
	}

	public function generate_total_risk_score($playerId){
		$this->load->model(array('risk_score_model'));
		return $this->risk_score_model->generate_total_risk_score($playerId);
	}

	/**
	 * overview : Player PEP Authentication Overview
	 *
	 * detail : load player PEP Authentication Details
	 *
	 * @param int $player_id
	 */
	public function player_pep_authentication($playerId) {
		$this->load->model(array('risk_score_model','gbg_logs_model'));
		$lang = $this->language_function->getCurrentLanguage();
		$this->loadTemplate('Player Management', '', '', 'player');

		$data['total_score'] = $this->generate_total_risk_score($playerId);

		$data['pep_auth_list'] = $this->gbg_logs_model->getGbgInfoByPlayer($playerId);


		if(!empty($data['pep_auth_list'])){
			foreach($data['pep_auth_list'] as $key => $value)
			{
				if(isset($value['generated_by'])){
					$generatedBy = $this->users->getUsernameById($value['generated_by']);
					$data['pep_auth_list'][$key]['generated_by'] = (!$value['generated_by']) ? lang('System Generated') :lang('Manually Generated By:').' '.$generatedBy;
				}

			}
		}


		$data['playerId'] = $playerId;
		$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId)) ? lang('Yes') : lang('No');

		$playerInfo = $this->player_model->getPlayerInfoById($playerId);

		if(!empty($playerInfo)){
			if(isset($playerInfo['disable_player_update_status_pep'])){
				$data['enable_auto_update_player_status_pep'] = $playerInfo['disable_player_update_status_pep'];
			}
		}

		//var_dump($data['allowed_withdrawal_status']);die();
		$data['current_pep_status'] = $this->check_risk_player_PEP($playerId);
		$this->load->view('player_management/player_pep_auth', $data);
	}

	public function generate_manual_pep_authentication($playerId,$isAdmin = false){
		$response = array();
		if($this->permissions->checkPermissions('manual_generate_pep_authentication')){
			$response = $this->generate_gbg_authentication($playerId,$isAdmin);
		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('You dont have permission to generate PEP Authentication'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}

	public function get_pep_auth_info($pepLogsId){
		$response = array();
		if($this->permissions->checkPermissions('show_pep_authentication')){
			$this->load->model(array('gbg_logs_model'));
			$pepInfo = $this->gbg_logs_model->getPepAuthInfo($pepLogsId);
			if(!empty($pepInfo)){
				$response = array(
					'status' => 'success',
					'msg' => lang('con.usm04'),
					'data' => $pepInfo,
					'msg_type' => self::MESSAGE_TYPE_ERROR
				);
			} else {
				$response = array(
					'status' => 'error',
					'msg' => lang('con.usm04'),
					'msg_type' => self::MESSAGE_TYPE_ERROR
				);
			}
		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('You dont have permission to view PEP Authentication details'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		}
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}

	public function edit_update_auto_update_pep($playerId,$status){
		$response = array();
		if($this->permissions->checkPermissions('manual_generate_pep_authentication')){
			$this->player_model->updatePlayer($playerId, array(
				'disable_player_update_status_pep' => ($status === 'true')? self::FALSE : self::TRUE,
			));

			$response = array(
				'status' => 'success',
				'msg' => lang('Auto Update Player Status successfully update.'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('You dont have permission to generate modified the status'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}

	public function generate_acuris_authentication($playerId,$isAdmin = false){
		$this->load->model(array('acuris_logs_model'));
		$response = $this->acuris_logs_model->generate_acuris_authentication($playerId,$isAdmin);
		return $response;
	}

	/**
	 * overview : Player C6 Authentication Overview
	 *
	 * detail : load player C6 Authentication Details
	 *
	 * @param int $player_id
	 */
	public function player_c6_authentication($playerId) {
		$this->load->model(array('risk_score_model','acuris_logs_model'));
		$lang = $this->language_function->getCurrentLanguage();
		$this->loadTemplate('Player Management', '', '', 'player');

		$data['total_score'] = $this->generate_total_risk_score($playerId);

		$data['c6_auth_list'] = $this->acuris_logs_model->getAcurisInfoByPlayer($playerId);


		if(!empty($data['c6_auth_list'])){
			foreach($data['c6_auth_list'] as $key => $value)
			{
				if(isset($value['generated_by'])){
					$generatedBy = $this->users->getUsernameById($value['generated_by']);
					$data['c6_auth_list'][$key]['generated_by'] = (!$value['generated_by']) ? lang('System Generated') :lang('Manually Generated By:').' '.$generatedBy;
				}

			}
		}


		$data['playerId'] = $playerId;
		$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId)) ? lang('Yes') : lang('No');

		$playerInfo = $this->player_model->getPlayerInfoById($playerId);

		if(!empty($playerInfo)){
			if(isset($playerInfo['disable_player_update_status_pep'])){
				$data['enable_auto_update_player_status_pep'] = $playerInfo['disable_player_update_status_pep'];
			}
		}

		//var_dump($data['allowed_withdrawal_status']);die();
		$data['current_pep_status'] = $this->check_risk_player_PEP($playerId);
		$data['current_c6_status'] = $this->check_risk_player_c6($playerId);
		$this->load->view('player_management/player_c6_auth', $data);
	}


	public function generate_manual_c6_authentication($playerId,$isAdmin = false){
		$response = array();
		if($this->permissions->checkPermissions('manual_generate_c6_authentication')){
			$response = $this->generate_acuris_authentication($playerId,$isAdmin);
		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('You dont have permission to generate C6 Authentication'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}

	public function get_acuris_c6_info_by_id($acurisLogId){
		$response = array();
		if($this->permissions->checkPermissions('show_c6_authentication')){
			$this->load->model(array('acuris_logs_model'));
			$response = $this->acuris_logs_model->getAcurisInfoByID($acurisLogId);
			if(!empty($response)){
				foreach ($response as $key => $value) {
					$data = json_decode($value,true);
					switch ($key) {
					    case "title":
					        if (is_array($data)){
					        	$response[$key] = implode (", ", $data);
					        }
					        break;
					    case "nationality":
					        if (is_array($data)){
					        	$response[$key] = implode (", ", $data);
					        }
					        break;
					    case "is_pep":
					    case "is_sanctions_previous":
					    case "is_sanctions_current":
					    case "is_law_enforcement":
					    case "is_financial_regulator":
					    case "is_insolvent":
					    case "is_disqualified_director":
					    case "is_adverse_media":
					        	$response[$key] = ($data) ? lang('True') : lang("False");
					        break;
					    default:
					   		// if (is_array($data)){
					     //    	$response[$key] = $data;
					     //    }
					    	if (is_array($data)){
					        	    $response[$key] = $this->associate_to_indexed_multi_array($data);
					        }
					        break;
					}
				}
			} else {
				$response = array(
					'status' => 'error',
					'msg' => lang('Acuris logs are invalid or empty records.'),
					'msg_type' => self::MESSAGE_TYPE_ERROR
				);
			}
		} else {
			$response = array(
				'status' => 'error',
				'msg' => lang('You dont have permission to view C6 Authentication'),
				'msg_type' => self::MESSAGE_TYPE_ERROR
			);
		}

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}

	public function associate_to_indexed_multi_array($arr) {
        // initialize destination indexed array
        $indArr = array();
        // loop through source
        foreach($arr as $val) {
            // if the element is array call the recursion
            if(is_array($val)) {
                $indArr[] = $this->associate_to_indexed_multi_array($val);
            // else add the value to destination array
            } else {
            	if (!is_bool($val)){
            		$indArr[] = !empty($val) ? $val : lang("N/A");
            	} else {
            		$indArr[] = $val;
				}
            }
        }
        return $indArr;
    }

    /**
	 * @author Jhunel L. Ebero
	 * overview : Get current c6 status
	 *
	 * details : get current country of player
	 *
	 * @param int $playerId	player_id
	 *
	 * @return string
	 */
	public function check_risk_player_c6($playerId) {
		$this->load->model('risk_score_model');
		return $this->risk_score_model->check_risk_player_c6($playerId);
	}

	public function player_c6($playerId) {
		$this->load->model('risk_score_model');
		$lang = $this->language_function->getCurrentLanguage();
		$this->loadTemplate('Player Management', '', '', 'player');
		$total_score = 0;

		$player_risk_score = $this->generate_player_risk_score($playerId);
		$risk_scores = $this->risk_score_model->getAllRiskScore();
		foreach ($risk_scores as $key => $value) {
			$data[$key]['id']= json_decode($value['category_name'],true);
			$data[$key]['rules']= json_decode($value['rules'],true);
			$data[$key]['category']= $value['category_name'];
			$data[$key]['description']= $value['category_description'];
			$data[$key]['generated_result']= $player_risk_score['data'][$value['category_name']]['generated_result'];
			$data[$key]['score']= $player_risk_score['data'][$value['category_name']]['score'];
			$total_score += $player_risk_score['data'][$value['category_name']]['score'];
		}

		$data['total_score'] = $total_score;

		$c6_data = $this->risk_score_model->getRiskScoreInfo(self::R8);
		$data['playerId'] = $playerId;
		$data['rules'] = json_decode($c6_data['rules'],true);
		$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId)) ? lang('Yes') : lang('No');
		$data['risk_level']	= $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
		$data['current_c6_status'] = $this->check_risk_player_c6($playerId);
		$this->load->view('player_management/risk_score/player_c6', $data);
	}

	public function update_c6_status($playerId,$c6_status = null){
		if(empty($c6_status)){
			$c6_status = $this->input->post('c6_status');
		}

		if(!$this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') || $this->utils->getConfig('enable_change_player_pep_status_when_binding_ID3')){
			$this->load->model('risk_score_model');
			$c6_status = str_replace('%20', ' ', $c6_status);
			$beforeAdjustmentStatus = $this->check_risk_player_c6($playerId);
			$response = $this->risk_score_model->update_c6_status($playerId,$c6_status);
			if(!empty($response)){
				if($response['status'] == "success"){
					$playerInfo = $this->player_model->getPlayerInfoById($playerId);
					if(!empty($playerInfo)){
						$this->savePlayerUpdateLog($playerId, "User " . $this->authentication->getUsername() . " update manually the C6 Status of Account " . $playerInfo['username']. ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $beforeAdjustmentStatus . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $c6_status . ') ', $this->authentication->getUsername());

						$this->saveAction(self::ACTION_MANAGEMENT_TITLE, 'Manual Update C6 Status', "User " . $this->authentication->getUsername() . " update manually the C6 Status of Account " . $playerInfo['username']. ' - ' .lang('adjustmenthistory.title.beforeadjustment') . ' (' . $beforeAdjustmentStatus . ') ' .lang('adjustmenthistory.title.afteradjustment') . ' (' . $c6_status . ') ');
					}
				}

				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult($response);
					return;
				} else {
					return $response;
				}
			}
		}
	}
}