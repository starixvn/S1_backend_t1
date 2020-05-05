<?php
trait player_bank_module{
    protected function _load_player_bank_details(){
        $playerId = $this->authentication->getPlayerId();

        $bank_details = $this->playerbankdetails->getBankDetails($playerId);
        $this->utils->debug_log('bank_details', $bank_details);

        $banks = $this->banktype->getBanktypeList();

        $this->load->vars('bank_details', $bank_details);
        $this->load->vars('banks', $banks);
    }

    public function iframe_bankDetails() {
        $this->load->model(['player_model', 'playerbankdetails','banktype']);
        $playerId = $this->authentication->getPlayerId();

        $this->_load_player_bank_details();

        $data['currentLang'] = $this->language_function->getCurrentLanguage();

        $this->loadTemplate(lang('cashier.89'), '', '', 'settings');

        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/bank_details', $data);
        $this->template->render();
    }

	/**
	 * set default bank
	 *
	 * @return void
	 */
	public function setDefaultBankDetails($bank_details_id, $dw_bank) {
        $this->load->model(['playerbankdetails']);
        $playerId = $this->authentication->getPlayerId();
        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_details_id);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->alertMessage(2, $message);
            $this->goBankDetails();
        }

        $this->playerbankdetails->setPlayerDefaultBank($playerId, $playerBankDetail->dwBank, $bank_details_id);

        $message = lang($bank['bankName']) . ' ' . lang('notify.28');
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
			return;
		}

		$this->alertMessage(1, $message);
		$this->goBankDetails();
	}

	/**
	 * view addedit bank
	 *
	 * @return void
	 */
	public function addEditBank($dw_bank, $bank_details_id = '') {
		$this->load->model(['banktype', 'playerbankdetails']);
		$bank = $this->playerbankdetails->getBankDetailsById($bank_details_id);

		$data['dw_bank'] = $dw_bank == 'deposit' ? "0" : "1";
		$data['bank'] = $bank;
		$data['banks'] = $this->banktype->getBanktypeList($dw_bank);
		$data['bank_details_id'] = $bank_details_id;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$this->loadTemplate(!empty($bank['playerBankDetailsId']) ? lang('cashier.104') : lang('cashier.103'), '', '', 'settings');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/add_edit_bank', $data);
		$this->template->render();
	}

	/**
	 * post bank details
	 *
	 * @return void
	 */
	public function postBankDetails() {
		$this->load->model(['banktype', 'playerbankdetails']);
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean|callback_check_unique_bank_number');
		$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean');

		$isEdit = $this->input->post('player_bank_details_id');
		if ($this->form_validation->run() == false) {
			$message = lang('notify.30');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}

			$this->alertMessage(2, $message);

			if (empty($isEdit)) {
				$this->addEditBank($this->input->post('dw_bank'));
			} else {
				$this->addEditBank($this->input->post('dw_bank'), $isEdit);
			}
		} else {
			$player_id = $this->authentication->getPlayerId();
			$all_banks = $this->playerbankdetails->getDepositBankDetail($player_id);
			$default_bank = array_filter((empty($all_banks)) ? [] : $all_banks, function($bank){
				return ($bank['isDefault']) ? TRUE : FALSE;
			});
			if (empty($isEdit)) {
				$data = array(
					'playerId' => $player_id,
					'bankTypeId' => $this->input->post('bank_name'),
					'bankAccountNumber' => $this->input->post('bank_account_number'),
					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
					'bankAddress' => $this->input->post('bank_address'),
					'branch' => $this->input->post('bank_branch'),
					'dwBank' => $this->input->post('dw_bank'),
					'isDefault' => (empty($default_bank)) ? '1' : '0',
					'isRemember' => '1',
					'status' => '0',
				);

				$this->player_functions->addBankDetails($data);

				$message = lang('notify.31');
				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
					return;
				}
				$this->alertMessage(1, $message);
			} else {
				$this->load->model('playerbankdetails');
				$data = array(
					'bankTypeId' => $this->input->post('bank_name'),
					'bankAccountNumber' => $this->input->post('bank_account_number'),
					'bankAccountFullName' => $this->input->post('bank_account_fullname'),
					'bankAddress' => $this->input->post('bank_address'),
					'branch' => $this->input->post('bank_branch'),
					'dwBank' => $this->input->post('dw_bank'),
				);

				//save bank changes
				$origbank = $this->player_functions->getBankDetailsById($isEdit);
				$change = $this->checkBankChanges($origbank, $data);
				$changes = array(
					'playerBankDetailsId' => $isEdit,
					'changes' => lang('lang.edit') . ' ' . lang('player.ui07') . ' (' . $change . ')',
					'createdOn' => date("Y-m-d H:i:s"),
					'operator' => $this->authentication->getUsername(),
				);
				$this->player_functions->saveBankChanges($changes);
				$this->player_functions->editBankDetails($isEdit, $data);


				$message = lang('notify.32');
				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
					return;
				}
				$this->alertMessage(1, $message);

			}
			$this->goBankDetails();
		}
	}

	/**
	 *
	 * @return void
	 */
	public function changeBankStatus($status, $bank_details_id) {
		// if (!$this->authentication->isLoggedIn()) {
		// 	$this->goPlayerLogin();
		// } else {
		$data = array(
			'status' => $status,
		);
		$this->player_functions->updateBankDetails($bank_details_id, $data);

		//save bank changes
		$bankdetails = $this->player_functions->getBankDetailsById($bank_details_id);
		$changes = array(
			'playerBankDetailsId' => $bank_details_id,
			'changes' => ($status == 0) ? $bankdetails[0]['bankName'] . ' Status: Active' : 'Status: Inactive',
			'createdOn' => date("Y-m-d H:i:s"),
			'operator' => $this->authentication->getUsername(),
		);
		$this->player_functions->saveBankChanges($changes);

		$bank = $this->player_functions->getBankDetailsById($bank_details_id);

		$message = lang($bank['bankName']) . ' ' . lang('notify.28');

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
			return;
		}

		$this->alertMessage(1, $message);
		$this->goBankDetails();
		// }
	}

	public function getBankDetailsById($playerBankDetailsId = null){
		$this->load->model('player');

		$data = $this->player->getBankDetailsById($playerBankDetailsId);

		return $this->returnJsonResult($data);
	}

	public function isPlayerBankAccountNumberExists($bankAccountId, $dw_bank) {
		return $this->player_functions->isPlayerBankAccountNumberExists($bankAccountId, $dw_bank);
	}

	/**
	 * view add edit withdrawal bank
	 *
	 * @return void
	 */
	public function addEditWithdrawalBank($dw_bank, $bank_details_id = '') {
		$this->load->model(['banktype', 'playerbankdetails']);

        $this->_load_player_bank_details();
		$bank = $this->playerbankdetails->getBankDetailsById($bank_details_id);


		$data['dw_bank'] = $dw_bank == 'deposit' ? "0" : "1";
		$data['bank'] = $bank;
		$data['banks'] = $this->banktype->getBanktypeList($dw_bank);
		$data['bank_details_id'] = $bank_details_id;
		$playerId = $this->authentication->getPlayerId();
		$this->load->model('player_model');
		$player = $this->player_model->getPlayerDetailsById($playerId);
		$player = get_object_vars($player);
		$data['realname'] = $player['lastName'] . $player['firstName'];
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$player_info = $this->player_model->getPlayerInfoDetailById($playerId);
		$data['withdraw_password'] = $player_info['withdraw_password'];

		$this->loadTemplate(!empty($bank['playerBankDetailsId']) ? lang('cashier.104') : lang('cashier.103'), '', '', 'settings');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/add_edit_withdrawal_bank', $data);
		$this->template->render();
	}

	/**
	 * post bank details
	 *
	 * @return void
	 */
	public function postWithdrawalBankDetails() {
		$this->load->model(['banktype', 'playerbankdetails']);
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean|integer');
		$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean|callback_check_new_withdrawal_bank_account_number|max_length[20]|min_length[6]|integer');
		$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean|');
		$this->form_validation->set_rules('bank_address', 'Bank Province', 'trim|xss_clean|callback_remove_puncts');
		$this->form_validation->set_rules('bank_branch', 'Bank Branch', 'trim|xss_clean|callback_remove_puncts');

		$isEdit = $this->input->post('player_bank_details_id');

		if ($this->form_validation->run() == false) {
			$message = strip_tags(validation_errors());
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			if (empty($isEdit)) {
				$this->addEditWithdrawalBank($this->input->post('dw_bank'));
			} else {
				$this->addEditWithdrawalBank($this->input->post('dw_bank'), $isEdit);
			}
		} else {
			$player_id = $this->authentication->getPlayerId();
			$all_banks = $this->playerbankdetails->getWithdrawBankDetail($player_id);
			$default_bank = array_filter((empty($all_banks)) ? [] : $all_banks, function($bank){
				return ($bank['isDefault']) ? TRUE : FALSE;
			});
			if (empty($isEdit)) {
				$data = array(
					'playerId'			=> $player_id,
					'bankTypeId'		=> $this->input->post('bank_name'),
					'bankAccountNumber' => $this->input->post('bank_account_number'),
					'bankAccountFullName'	=> filter_var($this->input->post('bank_account_fullname'), FILTER_SANITIZE_SPECIAL_CHARS),
					'bankAddress'			=> filter_var($this->input->post('bank_address'), FILTER_SANITIZE_SPECIAL_CHARS),
					'city' 				=> filter_var($this->input->post("bank_city"), FILTER_SANITIZE_SPECIAL_CHARS),
					'province' 			=> filter_var($this->input->post("bank_province"), FILTER_SANITIZE_SPECIAL_CHARS),
					'branch' 			=> filter_var($this->input->post('bank_branch'), FILTER_SANITIZE_SPECIAL_CHARS),
					'dwBank' => $this->input->post('dw_bank'),
					'isDefault' => (empty($default_bank)) ? '1' : '0',
					'isRemember' => '1',
					'status' => '0',
				);

				$res = $this->player_functions->addBankDetails($data);

				$message = lang('notify.31');
				$success = 'success';
				if (!$res) {
					$success = 'error';
					$message = lang('notify.67');
				}

				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => $success, 'msg' => $message, 'action' => 'add', 'bankdetails_id' => $res));
					return;
				}

				$message_type = $success == 'success' ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR;
				$this->alertMessage($message_type, $message);

			} else {
				$data = array(
					'bankTypeId' => $this->input->post('bank_name'),
					'bankAccountNumber' => $this->input->post('bank_account_number'),
					'bankAccountFullName'	=> filter_var($this->input->post('bank_account_fullname'), FILTER_SANITIZE_SPECIAL_CHARS),
					'bankAddress'	=> filter_var($this->input->post('bank_address'), FILTER_SANITIZE_SPECIAL_CHARS),
					'city'			=> filter_var($this->input->post("bank_city"), FILTER_SANITIZE_SPECIAL_CHARS),
					'province'		=> filter_var($this->input->post("bank_province"), FILTER_SANITIZE_SPECIAL_CHARS),
					'branch'		=> filter_var($this->input->post('bank_branch'), FILTER_SANITIZE_SPECIAL_CHARS),
					'dwBank' => $this->input->post('dw_bank'),
				);

				//save changes to bank history
				$origbank = $this->player_functions->getBankDetailsById($isEdit);
				$change = $this->checkBankChanges($origbank, $data);
				$changes = array(
					'playerBankDetailsId' => $isEdit,
					'changes' => lang('lang.edit') . ' ' . lang('player.ui07') . ' (' . $change . ')',
					'createdOn' => date("Y-m-d H:i:s"),
					'operator' => $this->authentication->getUsername(),
				);

				$this->player_functions->saveBankChanges($changes);
				$this->player_functions->editBankDetails($isEdit, $data);

				$message = lang('notify.32');
				if ($this->input->is_ajax_request()) {
					$this->returnJsonResult(array('status' => 'success', 'msg' => $message, 'action' => 'edit'));
					return;
				}
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			}
			$this->goBankDetails();
		}
	}

	/**
	 * Validation through ajax
	 *
	 *
	 */
	public function validateThruAjax() {
		//print_r($this->input->post());

		if ($this->input->post('bank_account_number')) {
			$this->form_validation->set_rules('bank_account_number', lang('cashier.69'), 'trim|required|callback_check_common_bank_account_number');
		}
		if ($this->input->post('new_bank_account_number')) {
			$this->form_validation->set_rules('new_bank_account_number', lang('cashier.69'), 'trim|required|callback_check_common_bank_account_number');
		}

		if ($this->form_validation->run() == false) {
			$arr = array('status' => 'error', 'msg' => validation_errors());
			// echo json_encode($arr);
		} else {

			$arr = array('status' => 'success', 'msg' => "");
			// echo json_encode($arr);
		}

		$this->returnJsonResult($arr);
	}

	public function common_validate_bank_account_number($fldname, $playerId, $bank_account_number,
			$bank_type, $bank_details_id=null){

		if(empty($playerId)){
			$this->form_validation->set_message($fldname, lang('Please login again'));
			return false;
		}

		if(empty($bank_account_number)){
			//ignore empty
			return true;
		}

		if($bank_type===null){

			$this->utils->error_log('bank type is null', $account_type, $referer);
			return true;

		}

		//check number
		// if(!is_numeric($bank_account_number)){
		// 	$this->form_validation->set_message($fldname, lang('only_numeric'));
		// 	return false;
		// }

		$success=true;

		$success=$this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
			$bank_type, $bank_details_id);


		if(!$success){
			$this->form_validation->set_message($fldname, lang('Bank Account Number already exist'));
		}

		return $success;

	}


	public function check_common_bank_account_number($bank_account_number){
		$playerId = $this->authentication->getPlayerId();
		if(empty($playerId)){
			$this->form_validation->set_message('check_common_bank_account_number', lang('Please login again'));
			return false;
		}

		// //check number
		// if(!is_numeric($bank_account_number)){
		// 	$this->form_validation->set_message('check_common_bank_account_number', lang('only_numeric'));
		// 	return false;
		// }

		// $success=true;
		$this->load->model(['playerbankdetails']);

		$bank_type=null;
		//get type from refer or input
		$account_type=$this->input->post('account_type');
		$referer=null;
		if(empty($account_type)){
			$referer=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' ;
			// $account_type=;
			if(!empty($referer)){
				$path=parse_url($referer, PHP_URL_PATH);
				$arr=explode('/', $path);

				log_message('debug', 'search path', ['arr'=>$arr]);

				//search withdraw or deposit
				foreach ($arr as $val) {
					if(strpos($val, 'withdraw')!==FALSE){
						$bank_type=Playerbankdetails::WITHDRAWAL_BANK;
						break;
					}
					if(strpos($val, 'deposit')!==FALSE || strpos($val, 'manual_payment')!==FALSE
							|| strpos($val, 'auto_payment')!==FALSE){
						$bank_type=Playerbankdetails::DEPOSIT_BANK;
						break;
					}
				}

			}
		}else{

			if($account_type==Playerbankdetails::WITHDRAWAL_BANK){

				$bank_type=Playerbankdetails::WITHDRAWAL_BANK;

			}else{

				$bank_type=Playerbankdetails::DEPOSIT_BANK;

			}

		}

		if($bank_type===null){

			$this->utils->error_log('bank type is null', $account_type, $referer);
			return true;

		}

		$bank_details_id=null;
		//get bank_details_id from bank_account_number_prev
		$bank_account_number_prev=$this->input->post('bank_account_number_prev');
		if(!empty($bank_account_number_prev)){
			$bank_details_id=$this->playerbankdetails->search_id_by_bank_number($playerId, $bank_type ,$bank_account_number_prev);
		}

		// $success=$this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
		// 	$bank_type, $bank_details_id);


		// if(!$success){
		// 	$this->form_validation->set_message('check_common_bank_account_number', lang('Bank Account Number already exist'));
		// }

		// return $success;

		return $this->common_validate_bank_account_number('check_common_bank_account_number',
			$playerId, $bank_account_number, $bank_type, $bank_details_id);
	}

	public function deleteBankDetails($bank_details_id) {
		$bank = $this->player_functions->getBankDetailsById($bank_details_id);
		$this->player_functions->deleteBankDetails($bank_details_id);

		//save bank changes
		$changes = array(
			'playerBankDetailsId' => $bank_details_id,
			'changes' => lang('Delete') . ' ' . lang($bank['bankName']) . ', ' . $bank['bankAccountNumber'] . ', ' . $bank['bankAccountFullName'] . ', ' . $bank['dwBank'] . ', ' . $bank['playerId'],
			'createdOn' => date("Y-m-d H:i:s"),
			'operator' => $this->authentication->getUsername(),
		);
		$this->player_functions->saveBankChanges($changes);

		$message = lang($bank['bankName']) . ' ' . lang('notify.29');
		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
			return;
		}
		$this->alertMessage(1, $message);
		$this->goBankDetails();

	}

	/**
	 * callback check duplicate bank acct for editing
	 *
	 * @return	int
	 */
	public function checkBankAcctIsExistOnEdit() {
		$this->load->model('playerbankdetails');
		$newBankAcctNo = $this->input->post('new_bank_account_number');
		$prevBankAcctNo = $this->input->post('bank_account_number_prev');

		if ($this->playerbankdetails->checkBankAcctIsExistOnEdit($newBankAcctNo, $prevBankAcctNo)) {
			$this->form_validation->set_message('checkBankAcctIsExistOnEdit', 'Bank Account No already exist');
			return FALSE;
		} else {
			return TRUE;
		}

	}

	/**
	 * Checks fields modified on player bank info
	 *
	 * @param 	array
	 * @param 	array
	 * @return	string
	 */
	public function checkBankChanges($origbank, $data) {
		$array = null;

		$array .= $origbank['bankTypeId'] != $data['bankTypeId'] ? lang('player.ui35') . ', ' : '';
		$array .= $origbank['bankAccountNumber'] != $data['bankAccountNumber'] ? lang('cashier.69') . ', ' : '';
		$array .= $origbank['bankAccountFullName'] != $data['bankAccountFullName'] ? lang('cashier.68') . ', ' : '';
		//$array .= $origbank['province'] != $data['province'] ? lang('cashier.70') . ', ' : '';
		//$array .= $origbank['city'] != $data['city'] ? lang('cashier.71') . ', ' : '';
		$array .= $origbank['branch'] != $data['branch'] ? lang('cashier.72') . ', ' : '';

		return $modifiedField = empty($array) ? '' : substr($array, 0, -2);
	}
}

