<?php
trait allowed_withdrawal_status {

	public function player_allowed_withdrawal_status($playerId) {
		/*if (!$this->permissions->checkPermissions('show_allowed_withdrawal_status')) {
			$this->error_access();
		} else {*/
			
			
			$this->loadTemplate('Player Management', '', '', 'player');
			$this->load->model(array('player_kyc','risk_score_model'));
			
			$data['kyc_lvl'] = $this->player_kyc->getPlayerCurrentKycLevel($playerId);
			
			$data['risk_lvl'] = $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
			$data['verified_bank_account_name'] = $this->player_identity_verification($playerId);
			$data['risk_kyc_result'] = $this->generate_allowed_withdrawal_status($playerId);
			//$data['allowed_withdrawal_status'] = ($this->generate_allowed_withdrawal_status($playerId) && $data['verified_bank_account_name']);

			$this->load->view('player_management/player_allowed_withdrawal_status', $data);
		//}
	}

	public function generate_allowed_withdrawal_status($playerId) {
		$this->load->model(array('player_kyc','risk_score_model'));
		return $this->risk_score_model->generate_allowed_withdrawal_status($playerId);
	}

	public function allowed_withdrawal_status_chart(){
		$this->load->model(array('risk_score_model','kyc_status_model'));
		$kyc_list = $this->kyc_status_model->getAllKycStatus();
		$risk_score_info = $this->risk_score_model->getRiskScoreInfo(self::RC);
		$risk_score_list = json_decode($risk_score_info['rules'],true);
		
		$data['kyc_list'] = $kyc_list;
		$data['risk_score_list'] = $risk_score_list;

		$data['renderChart'] = $this->render_kyc_riskscore_chart();
		if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest' && $this->input->server('REQUEST_METHOD') == 'POST') {
			echo json_encode($data);
		} else {
			return $data;
		}

	}

	public function render_kyc_riskscore_chart(){
		$this->load->model('risk_score_model');
		return $this->risk_score_model->render_kyc_riskscore_chart();
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : update kyc / riskscore chart
	 *
	 * details : get withdrawal history list by player
	 *
	 *
	 * @return json
	 */
	public function updateKycRiskScoreChart(){
		$response = array();
		$input = $this->input->post();
		if(!empty($input)){
			if(!empty($input['kycLvl']) && !empty($input['riskLvl']) && !empty($input['tag'])){
				$this->load->model(array('riskscore_kyc_chart_management_model'));
				$updateResponse = $this->riskscore_kyc_chart_management_model->addUpdateChart($input['kycLvl'],$input['riskLvl'],$input['tag']);
				if($updateResponse){
					$response = array('status' => 'success', 'msg' => lang("Saved"));
				} else {
					$response = array('status' => 'error', 'msg' => lang("save.failed"));
				}
				
			} else {
				$response = array('status' => 'error', 'msg' => lang("Some fields are filled incorrectly"));
			}
		} else {
			$response = array('status' => 'error', 'msg' => lang("Some fields are filled incorrectly"));
		}
		
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult($response);
			return;
		} else {
			return $response;
		}
	}
}