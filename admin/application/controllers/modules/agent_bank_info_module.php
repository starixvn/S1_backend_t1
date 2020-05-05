<?php
/**
 *   filename:   agent_bank_info_module.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-03
 *   @brief:     bank info for agent info page
 */

trait agent_bank_info_module {

	/* ****** Agent Information Bank Info ****** */

	/** addBankInfo {{{2
	 * add new payment account
	 *
	 * @return	bool
	 */
	public function addBankInfo($agent_id) {

        if ($this->hasPermission('edit_agent_bank_account')) {
            $accounts = $this->agency_model->get_payment_by_agent_id($agent_id);
            if(count($accounts) >= 1 && !$this->utils->isEnabledFeature('agent_can_have_multiple_bank_accounts')) {
                $message = lang('Cannot add bank account! Please edit the old one.');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info", "refresh");
            } else {
                $this->load_template(lang('Agency Management'), '', '', 'agency');

                $data['agent_id'] = $agent_id;
                $data['controller_name'] = $this->controller_name;
                /*
                $this->load->model('banktype');
                $banks = (array) $this->banktype->getBankTypes();
                $data['banks'] = array();
                foreach ($banks as $key => $row) {
                $data['banks'][] = array(
                "bankTypeId" => $row->bankTypeId,
                "bankName" => $row->bankName,
                'enabled_withdrawal' => $row->enabled_withdrawal,
                );
                }
                $this->utils->debug_log('addBankInfo: DATA ', $data);
                 */
                $this->template->write_view('main_content', 'includes/agent_add_bank_info', $data);
                $this->template->render();
            }
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }

	}

	/** verifyaddNewAccount {{{2
	 * verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyaddNewAccount() {
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('branch_address', 'Branch Address', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean|required');
        $this->form_validation->set_rules('account_number', 'Account Number',
            'trim|xss_clean|required|numeric|is_unique[agent_payment.account_number]');

		$agent_id = $this->input->post('agent_id');
		$agentDetails = $this->agency_model->get_agent_by_id($agent_id);

		if ($this->form_validation->run() == false) {
			$this->addBankInfo($agent_id);
		} else {
			$data = array(
				'agent_id' => $agent_id,
				'payment_method' => 'Wire Transfer',
				'bank_name' => $this->input->post('bank_name'),
				'branch_address' => $this->input->post('branch_address'),
				'account_name' => $this->input->post('account_name'),
                'account_number' => $this->input->post('account_number'),
				'created_on' => date('Y-m-d H:i:s'),
				'updated_on' => date('Y-m-d H:i:s'),
            );
            foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->agency_model->insert_payment($data);
			$this->utils->debug_log('verifyaddNewAccount: DATA ', $data);

            $this->saveAction('Add Agent Bank Information',
                "User " . $this->getUsername() . " has add agent bank info to " . $agentDetails['agent_name']);

			$message = lang('con.agen01');
			$this->alertMessage(1, $message); //will set and send message to the user
			$redirectUrl = $this->controller_name . "/agent_information/" . $agent_id . "#bank_info";
			redirect($redirectUrl, "refresh");
		}
	}

	/** editPayment {{{2
	 * edit payment account
	 *
	 * @return	void
	 */
	public function editPayment($agent_payment_id) {
        if ($this->hasPermission('edit_agent_bank_account')) {
			$this->load_template('Agency Management', '', '', 'agency');

			$data['payment'] = $this->agency_model->get_payment_by_id($agent_payment_id);
            $data['controller_name'] = $this->controller_name;

			$this->template->write_view('main_content', 'includes/agent_edit_bank_info', $data);
			$this->template->render();
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	}

	/** verifyEditPayment {{{2
	 * verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyEditPayment($agent_payment_id) {
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('branch_address', 'Branch Address', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean|required|numeric|callback_checkAccountNumber');

		$agent_id = $this->input->post('agent_id');
		$agentDetails = $this->agency_model->get_agent_by_id($agent_id);

		if ($this->form_validation->run() == false) {
			$this->editPayment($agent_payment_id);
		} else {
			$data = array(
				'agent_id' => $agent_id,
				'payment_method' => 'Wire Transfer',
				'bank_name' => $this->input->post('bank_name'),
				'branch_address' => $this->input->post('branch_address'),
				'account_name' => $this->input->post('account_name'),
				'account_number' => $this->input->post('account_number'),
				'updated_on' => date('Y-m-d H:i:s'),
            );
            foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

            $this->utils->debug_log('Edit Payment verify:', $data);
            $this->agency_model->update_payment($agent_payment_id, $data);

			$this->saveAction('Edit Agent Bank Information', "User " . $this->getUsername() . " has edit agent bank info to " . $agentDetails['agent_name']);

			$message = lang('con.aff56');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info", "refresh");
		}
	}

    // checkAccountNumber {{{2
	public function checkAccountNumber() {
		$agent_payment_id = $this->input->post('agent_payment_id');
		$payment = $this->agency_model->get_payment_by_id($agent_payment_id);
		$account_number = $this->input->post('account_number');

		if ($payment['account_number'] != $account_number) {
			$this->form_validation->set_rules('account_number', 'Account Number', 'is_unique[agent_payment.account_number]');

			if ($this->form_validation->run() == false) {
				$this->form_validation->set_message('checkAccountNumber', "The Account Number field must contain a unique value.");
				return false;
			}
		}

		return true;
	}

	/** activate_payment {{{2
	 * activate payment
	 *
	 * @return	void
	 */
	public function activate_payment($payment_id, $bank_name, $agent_id) {
        if ($this->hasPermission('edit_agent_bank_account')) {
			$agentDetails = $this->agency_model->get_agent_by_id($agent_id);

			$data = array(
				'status' => '0',
				'updated_on' => date('Y-m-d H:i:s'),
			);
			$this->agency_model->update_payment($payment_id, $data);

			$this->saveAction('Activate Agent Bank Information', "User " . $this->getUsername() . " has activate agent bank info to " . $agentDetails['agent_name']);

			$message = lang('con.aff57') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info", "refresh");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	}

	/** deactivate_payment {{{2
	 * deactivate payment
	 *
	 * @return	void
	 */
	public function deactivate_payment($payment_id, $bank_name, $agent_id) {
        if ($this->hasPermission('edit_agent_bank_account')) {
			$agentDetails = $this->agency_model->get_agent_by_id($agent_id);
			$this->utils->debug_log('Deactivate Payment agentDetails: ', $agentDetails);

			$data = array(
				'status' => '1',
				'updated_on' => date('Y-m-d H:i:s'),
			);
            $this->agency_model->update_payment($payment_id, $data);

			$this->saveAction('Deactivate Agent Bank Information', "User " . $this->getUsername() . " has deactivate agent bank info to " . $agentDetails['agent_name']);

			$message = lang('con.aff58') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info", "refresh");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	}

	/** delete_payment {{{2
	 * delete payment
	 *
	 * @return	void
	 */
	public function delete_payment($payment_id, $bank_name, $agent_id) {
        if ($this->hasPermission('edit_agent_bank_account')) {
			$this->agency_model->remove_payment($payment_id);

			$agentDetails = $this->agency_model->get_agent_by_id($agent_id);
			$this->saveAction('Delete Agent Bank Information', "User " . $this->getUsername() . " has delete agent bank info to " . $agentDetails['agent_name']);

			$message = lang('con.aff59') . ": " . str_replace("%20", " ", $bank_name);
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info", "refresh");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	} // delete_payment }}}2

	/** agent_deposit {{{2
	 * overview : agent deposit
	 *
	 * @param null $agent_id
	 */
	public function agent_deposit($agent_id = null) {
        if ($this->hasPermission('agent_deposit')) {
            $this->load->model(array('agency_model', 'transactions'));

            $this->form_validation->set_rules('username',
                lang('Agent Username'), 'trim|xss_clean|required|callback_is_exist[agency_agents.agent_name]');
            $this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
            // $this->form_validation->set_rules('account', lang('Account'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('date', lang('Date'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');
            // $this->form_validation->set_rules('status', lang('Status'), 'trim|xss_clean|required');

            $username = null;
            if ($agent_id) {
                $agent = $this->agency_model->get_agent_by_id($agent_id);
                $username = $agent['agent_name'];
            }
            $data['agent_id'] = $agent_id;
            $data['username'] = $username;
            $data['controller_name'] = $this->controller_name;

            if ($this->form_validation->run()) {

                $username = $this->input->post('username');
                $agent_id = $this->agency_model->get_agent_id_by_agent_name($username);
                $amount = $this->input->post('amount');

                $date = $this->input->post('date');
                $reason = $this->input->post('reason');
                $adminUserId = $this->authentication->getUserId();

                $lock_type = Utils::LOCK_ACTION_AGENCY_BALANCE;
                $lock_it = $this->utils->lockResourceBy($agent_id, $lock_type, $locked_key);
                $this->utils->debug_log('lock agent', $agent_id, 'amount', $amount);
                try {
                    if ($lock_it) {
                        $this->transactions->startTrans();
                        $success = $this->transactions->depositToAgent($agent_id, $amount, $reason, $adminUserId, Transactions::MANUAL, $date);
                        $success = $this->transactions->endTransWithSucc() && $success;
                    } else {
                        $this->utils->error_log('lock agent failed', $agent_id, $amount);
                        $success = false;
                    }
                } finally {
                    $rlt = $this->utils->releaseResourceBy($agent_id, $lock_type, $locked_key);
                }

                if ($success) {
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New deposit has been successfully added'));
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
                }

                redirect($this->controller_name . '/agent_deposit/' . $agent_id);

                return;
            }

            $this->load_template(lang('Agency Management'), '', '', 'agency');
            // $this->template->write_view('sidebar', 'payment_management/sidebar');
            $this->template->write_view('main_content', 'includes/agent_new_deposit', $data);
            $this->template->render();
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	} // agent_deposit }}}2
	/** agent_withdraw {{{2
	 * overview : agent withdraw
	 *
	 * @param null $agent_id
	 * @param string $walletType
	 */
	public function agent_withdraw($agent_id = null, $walletType='main') {
        if (!$this->isAdmin()) {
            redirect('agency/withdrawRequest/' . $walletType);

        } else if ($this->hasPermission('agent_withdraw')) {
            $this->load->model(array('transactions'));

            $this->form_validation->set_rules('username', lang('Agent Username'),
                'trim|xss_clean|required|callback_is_exist[agency_agents.agent_name]');
            $this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required');
            // $this->form_validation->set_rules('account', lang('Account'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('date', lang('Date'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');
            // $this->form_validation->set_rules('status', lang('Status'), 'trim|xss_clean|required');

            if ($agent_id) {
                $agent = $this->agency_model->get_agent_by_id($agent_id);
                $username = $agent['agent_name'];
            } else if ($username = $this->input->post('username')) {
                $agent_id = $this->agency_model->get_agent_id_by_agent_name($username);
            }

            $data['agent_id'] = $agent_id;
            $data['username'] = $username;
            $data['walletType'] = $walletType;
            $data['controller_name'] = $this->controller_name;

            if ($this->form_validation->run()) {

                $amount = $this->input->post('amount');

                $date = $this->input->post('date');
                $reason = $this->input->post('reason');
                $adminUserId = $this->authentication->getUserId();

                $message=null;

                $lock_type = Utils::LOCK_ACTION_AGENCY_BALANCE;
                $lock_it = $this->utils->lockResourceBy($agent_id, $lock_type, $locked_key);
                $this->utils->debug_log('lock agent', $agent_id, 'amount', $amount);
                try {
                    if ($lock_it) {
                        $this->transactions->startTrans();
                        //check enough balance
                        if($walletType=='main'){
                            $bal = $this->agency_model->getMainWallet($agent_id);
                        }else{
                            $bal = $this->agency_model->getBalanceWallet($agent_id);
                        }
                        if ($this->utils->compareResultFloat($bal, '>=', $amount)) {
                            $success = $this->transactions->withdrawFromAgent($agent_id, $amount, $reason, $adminUserId, Transactions::MANUAL, $date, $walletType);
                        } else {
                            $this->utils->error_log('do not have enough balance', $agent_id, $amount, 'wallet balance', $bal);
                            $message=lang('Do not have enough balance');
                            $success = false;
                        }
                        $success = $this->transactions->endTransWithSucc() && $success;
                    } else {
                        $this->utils->error_log('lock agent failed', $agent_id, $amount);
                        $success = false;
                    }
                } finally {
                    $rlt = $this->utils->releaseResourceBy($agent_id, $lock_type, $locked_key);
                }

                if ($success) {
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New withdrawal has been successfully added'));
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message ? $message : lang('error.default.db.message'));
                }

                if($agent_id){
                    redirect($this->controller_name . '/agent_withdraw/'.$agent_id.'/'.$walletType);
                }else{
                    redirect($this->controller_name . '/agent_withdraw');
                }

                return;
            }

            $this->load_template(lang('Agency Management'), '', '', 'agency');
            // $this->template->write_view('sidebar', 'payment_management/sidebar');
            $this->template->write_view('main_content', 'includes/agent_new_withdrawal', $data);
            $this->template->render();
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	} // agent_withdraw }}}2
	/** agent_manual_add_balance {{{2
	 * overview : agent manual add balance
	 *
	 * @param null $agent_id
	 */
	public function agent_manual_add_balance($agent_id = null) {
        if ($this->hasPermission('agent_deposit')) {
            $this->load->model(array('transactions'));

            $this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required|greater_than[0]');
            $this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');

            $username = null;
            if ($agent_id) {
                $agent = $this->agency_model->get_agent_by_id($agent_id);
                $username = $agent['agent_name'];
            }
            $data['title'] = lang('Manually Add Balance to Agent Main Wallet');
            $data['agent_id'] = $agent_id;
            $data['username'] = $username;
            $data['balance'] = $this->agency_model->getMainWallet($agent_id);
            $data['controller_name'] = $this->controller_name;

            if ($this->form_validation->run()) {

                $amount = $this->input->post('amount');
                $reason = $this->input->post('reason');
                $date = $this->input->post('date');
                $adminUserId = $this->authentication->getUserId();

                $lock_type = Utils::LOCK_ACTION_AGENCY_BALANCE;
                $lock_it = $this->utils->lockResourceBy($agent_id, $lock_type, $locked_key);
                $this->utils->debug_log('lock agent', $agent_id, 'amount', $amount);
                try {
                    if ($lock_it) {
                        $this->transactions->startTrans();
                        $success = $this->transactions->manualAddBalanceAgent($agent_id, $amount, $reason, $adminUserId, Transactions::MANUAL, $date);
                        $success = $this->transactions->endTransWithSucc() && $success;
                    } else {
                        $this->utils->error_log('lock agent failed', $agent_id, $amount);
                        $success = false;
                    }
                } finally {
                    $rlt = $this->utils->releaseResourceBy($agent_id, $lock_type, $locked_key);
                }

                if ($success) {
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Balance has been successfully added to agent main wallet'));
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
                }

                redirect($this->controller_name . '/agent_manual_add_balance/'.$agent_id);

                return;
            }

            $this->load_template(lang('Agency Management'), '', '', 'agency');
            $this->template->write_view('main_content', 'includes/agent_manual_add_subtract_balance', $data);
            $this->template->render();
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
	} // agent_manual_add_balance }}}2
	/** agent_manual_subtract_balance {{{2
	 * overview : agent manual subtract balance
	 *
	 * @param null $agent_id
	 */
    public function agent_manual_subtract_balance($agent_id = null) {
        if ($this->hasPermission('agent_withdraw')) {
            $this->load->model(array('agency_model', 'transactions'));

            $this->form_validation->set_rules('amount', lang('Amount'), 'trim|xss_clean|required|greater_than[0]');
            $this->form_validation->set_rules('reason', lang('Reason'), 'trim|xss_clean|required');

            $username = null;
            if ($agent_id) {
                $agent = $this->agency_model->get_agent_by_id($agent_id);
                $username = $agent['agent_name'];
            }
            $data['title'] = lang('Manually Subtract Balance to Agent Main Wallet');
            $data['agent_id'] = $agent_id;
            $data['username'] = $username;
            $data['balance'] = $this->agency_model->getMainWallet($agent_id);
            $data['controller_name'] = $this->controller_name;

            if ($this->form_validation->run()) {

                $amount = $this->input->post('amount');
                $reason = $this->input->post('reason');
                $date = $this->input->post('date');
                $adminUserId = $this->authentication->getUserId();

                $lock_type = Utils::LOCK_ACTION_AGENCY_BALANCE;
                $lock_it = $this->utils->lockResourceBy($agent_id, $lock_type, $locked_key);
                $this->utils->debug_log('lock agent', $agent_id, 'amount', $amount);
                try {
                    if ($lock_it) {
                        $this->transactions->startTrans();
                        $success = $this->transactions->manualSubtractBalanceAgent($agent_id, $amount, $reason, $adminUserId, Transactions::MANUAL, $date);
                        $success = $this->transactions->endTransWithSucc() && $success;
                    } else {
                        $this->utils->error_log('lock agent failed', $agent_id, $amount);
                        $success = false;
                    }
                } finally {
                    $rlt = $this->utils->releaseResourceBy($agent_id, $lock_type, $locked_key);
                }

                if ($success) {
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Balance has been successfully subtracted to agent main wallet'));
                } else {
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
                }

                redirect($this->controller_name . '/agent_manual_subtract_balance/'.$agent_id);

                return;
            }

            $this->load_template(lang('Agency Management'), '', '', 'agency');
            $this->template->write_view('main_content', 'includes/agent_manual_add_subtract_balance', $data);
            $this->template->render();
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
    } // agent_manual_subtract_balance }}}2
	/** agent_transfer_bal_to_main {{{2
	 * overview : agent transfer balance to main
	 *
	 * @param $agent_id
	 * @param null $amount
	 */

    public function agent_transfer_bal_to_main($agent_id, $amount = null) {
        if($this->hasPermission('agent_balance_transfer')){
            $this->load->model(array('transactions'));

            $self = $this;

            $opUserId = $this->getUserId();
            if($this->isAdmin()){
                $opType = 'admin';
            } else {
                $opType = 'agent';
                // only the agent itself can do this
                if ($opUserId != $agent_id) {
                    return;
                }
            }

            // $lock_type = Utils::LOCK_ACTION_AGENCY_BALANCE;

            $success = $this->lockAndTransForAgencyBalance($agent_id, function () use ($self, $agent_id, $amount, $opUserId, $opType) {
                $success = $self->transactions->agentTransferFromBalanceToMain($agent_id, $amount, $opUserId);
                return $success;
            });

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
            }

            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
    } // agent_transfer_bal_from_main }}}2
    /** agent_transfer_bal_from_main {{{2
     * overview : agent transfer balance from main
     *
     * @param $agent_id
     * @param null $amount
     */
    public function agent_transfer_bal_from_main($agent_id, $amount = null) {

        if($this->hasPermission('agent_balance_transfer')){
            $this->load->model(array('transactions'));

            $self = $this;
            $opUserId = $this->getUserId();
            if($this->isAdmin()){
                $opType = 'admin';
            } else {
                $opType = 'agent';
                // only the agent itself can do this
                if ($opUserId != $agent_id) {
                    return;
                }
            }

            $success = $this->lockAndTransForAgencyBalance($agent_id,
                function () use ($self, $agent_id, $amount, $opUserId, $opType) {
                    $success = $self->transactions->agentTransferToBalanceFromMain($agent_id, $amount, $opUserId, $opType);
                    return $success;
                });

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'), true);
            } else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Transfer Amount is too low'), true);
            }

            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
    } // agent_transfer_bal_from_main }}}2
    // agent_transfer_balance_to_binding_player {{{2
    /**
     *  Transfer balance in main wallet to binding player's main wallet
     *
     *  @param  INT agent_id
     *  @return
     */
    public function agent_transfer_balance_to_binding_player($agent_id, $wallet_type = 'main', $amount = null, $redirect_url = '') {

        if($this->hasPermission('agent_balance_transfer')){
            $this->load->model(array('transactions'));

            $self = $this;
            $agent_details = $this->agency_model->get_agent_by_id($agent_id);
            $player_id = $agent_details['binding_player_id'];
            $message=null;
            $success = $this->lockAndTransForPlayerBalanceAndAgencyCredit($player_id, $agent_id,
                function () use ($self, $agent_id, $agent_details, $player_id, $amount, $wallet_type, &$message) {
                    if ($amount === null) {
                        if ($wallet_type == 'main') {
                            $wallet_name = 'wallet_balance';
                        } else {
                            $wallet_name = 'wallet_hold';
                        }
                        $amount = $agent_details[$wallet_name];
                    }
                    if($amount <= 0) {
                        $message=lang('Transfer amount CAN NOT <= 0!');
                        return false;
                    }
                    $success=$self->transactions->createDepositTransactionByAgent($player_id, $amount, $agent_id, null);
                    if(!$success){
                        $message=lang('Deposit to player failed');
                        return $success;
                    }

                    $success = $self->transactions->agentTransferBalanceToBindingPlayer($agent_id, $player_id, $amount, $wallet_type);
                    if(!$success){
                        $message=lang('Deduct from agent wallet failed');
                        return $success;
                    }

                    $agent_name = $agent_details['agent_name'];
                    $player_username = $self->player_model->getUsernameById($player_id);
                    $log_params = array(
                        'action' => 'transfer_balance_to_binding_player',
                        'link_url' => site_url($self->controller_name . '/agent_transfer_balance_to_binding_player/' . $agent_id. '/'. $wallet_type) ,
                        'done_by' => $agent_name,
                        'done_to' => $player_username,
                        'details' => 'player ' . $player_username . ' deposit. amount = '. $amount . '. parent agent is '. $agent_name,
                    );
                    $self->agency_library->save_action($log_params);
                    return $success;
                });

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
            } else {
                if(empty($message)){
                    $message = lang('Transaction Failed. Please check your account!');
                }
                //$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }

        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
        }

        if(empty($redirect_url)) {
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        } else {
            redirect($redirect_url);
        }
    } // agent_transfer_balance_to_binding_player  }}}2
    // agent_transfer_balance_from_binding_player {{{2
    /**
     *  Transfer balance in main wallet to binding player's main wallet
     *
     *  @param  INT agent_id
     *  @return
     */
    public function agent_transfer_balance_from_binding_player($agent_id, $wallet_type = 'main', $amount = null) {

        if($this->hasPermission('agent_balance_transfer')){
            $this->load->model(array('transactions'));

            $self = $this;
            $agent_details = $this->agency_model->get_agent_by_id($agent_id);
            $player_id = $agent_details['binding_player_id'];
            $message=null;
            $success = $this->lockAndTransForPlayerBalanceAndAgencyCredit($player_id, $agent_id,
                function () use ($self, $agent_id, $agent_details, $player_id, $amount, $wallet_type, &$message) {
                    $self->load->model('wallet_model');
                    $balance = $self->wallet_model->getMainWalletBalance($player_id);
                    if ($amount === null) {
                        $amount = $balance;
                    }
                    if ($self->utils->compareResultFloat($balance, '<', $amount)){
                        $message=lang('No enough balance!');
                        return false;
                    }
                    if ($amount <= 0){
                        $message=lang('Transfer amount CAN NOT <= 0!');
                        return false;
                    }
                    $success=$self->transactions->createWithdrawTransactionByAgent($player_id, $amount, $agent_id, null);
                    if(!$success){
                        $message=lang('Withdraw from player failed');
                        return $success;
                    }

                    $success = $self->transactions->agentTransferBalanceFromBindingPlayer($agent_id, $player_id, $amount, $wallet_type);
                    if(!$success){
                        $message=lang('Deposit to agent wallet failed');
                        return $success;
                    }

                    $agent_name = $agent_details['agent_name'];
                    $player_username = $self->player_model->getUsernameById($player_id);
                    $log_params = array(
                        'action' => 'transfer_balance_from_binding_player',
                        'link_url' => site_url($self->controller_name . '/agent_transfer_balance_from_binding_player/' . $agent_id. '/'. $wallet_type) ,
                        'done_by' => $agent_name,
                        'done_to' => $player_username,
                        'details' => 'transfer from binding player ' . $player_username . '. amount = '. $amount . '. parent agent is '. $agent_name,
                    );
                    $self->agency_library->save_action($log_params);
                    return $success;
                });

            if ($success) {
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Transfer successfully'));
            } else {
                if(empty($message)){
                    $message = lang('Transaction Failed. Please check your account!');
                }
                //$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            }

            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        } else {
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Sorry! You have no permission on this operation!'));
            redirect($this->controller_name . "/agent_information/" . $agent_id . "#bank_info");
        }
    } // agent_transfer_balance_from_binding_player  }}}2
}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_bank_info_module.php
