<?php
trait transfer_request_module {

	/**
	 *
	 * detail: Display lists for transfer request
	 *
	 * @return load template
	 */
	public function transfer_request($from = 'payment') {
		if ($this->permissions->checkPermissions(['transfer_request', 'report_transfer_request'])) {
			$this->load->model(['wallet_model', 'external_system']);
			// $transfer_request_cnt = $this->wallet_model->transfer_request_count();

			// $data['transfer_request_cnt'] = @$transfer_request_cnt[Wallet_model::STATUS_TRANSFER_REQUEST] ?: 0;
			// $data['transfer_success_cnt'] = @$transfer_request_cnt[Wallet_model::STATUS_TRANSFER_SUCCESS] ?: 0;
			// $data['transfer_failed_cnt'] = @$transfer_request_cnt[Wallet_model::STATUS_TRANSFER_FAILED] ?: 0;
			//to load abstract_game_api
			$this->utils->loadAnyGameApiObject();

			$data=['game_platforms' => $this->external_system->getAllActiveSytemGameApi()];

			$data['conditions']=$this->safeLoadParams([
				'timezone'=>8,
				'status'=>'', //Wallet_model::STATUS_TRANSFER_SUCCESS,
				'by_game_platform_id'=>'',
				'secure_id'=>'',
				'search_reg_date'=>'on',
				'result_id'=>'',
				'suspicious_trans'=>'',
			]);

			if(!empty($data['conditions']['secure_id'])){
				$data['conditions']['search_reg_date']=false;
			}else{
				if($this->input->get_post('search_reg_date')===FALSE){
					//set default value
					$data['conditions']['search_reg_date']='on';
				}
			}

			// $data['status'] = Wallet_model::STATUS_TRANSFER_REQUEST;

			if ($from == 'report') {
				$activenav = 'report';
			} else {
				$activenav = 'payment';
			}

			$this->loadTemplate(lang('Transfer Request'), '', '', $activenav);
			// $this->template->add_css('resources/css/dashboard.css');

			if ($from == 'report') {
				$this->template->write_view('sidebar', 'report_management/sidebar', ['active' => 'transfer_request']);
			} else {
				$this->template->write_view('sidebar', 'payment_management/sidebar', ['active' => 'transfer_request']);
			}

			$this->template->write_view('main_content', 'payment_management/transfer_request', $data);
			$this->template->render();
		} else {
			$this->error_access($from);
		}
	}

	public function query_transfer_status($id){

		if (!$this->permissions->checkAnyPermissions(['transfer_request', 'report_transfer_request']) || empty($id)) {
			return $this->returnErrorStatus('403');
		}

		$this->load->model(['wallet_model', 'player_model']);
		//load transfer request record
		$transferRequest=$this->wallet_model->getTransferRequestById($id);
		if(empty($transferRequest)){
			return $this->returnErrorStatus();
		}
		//validate data

		list($success, $status, $error_message, $status_message)=$this->utils->queryAndUpdateTransferRequestStatus($transferRequest);

		$this->returnJsonResult(['success'=>$success, 'error_message'=>$error_message, 'status'=>$status, 'status_message'=>$status_message]);
	}

	public function auto_fix_transfer($id){

		if (!$this->permissions->checkAnyPermissions(['make_up_transfer_record']) || empty($id)) {
			return $this->returnErrorStatus('403');
		}

		$this->load->model(['wallet_model', 'external_system', 'transactions']);
		//load transfer request record
		$transferRequest=$this->wallet_model->getTransferRequestById($id);
		if(empty($transferRequest)){
			return $this->returnErrorStatus();
		}
		//validate data
		// $external_system_id=$transferRequest['external_system_id'];

		// $success=true;
		// $error_message=null;
		// $status=null;
		// $message=null;

		list($success, $status, $error_message, $message)=$this->utils->fixTransferRequest($transferRequest);

		$this->returnJsonResult(['success'=>$success, 'error_message'=>$error_message, 'status'=>$status, 'message'=>$message]);

	}

}
