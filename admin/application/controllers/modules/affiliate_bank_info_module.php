<?php
trait affiliate_bank_info_module {

	/* ****** Affiliate User Information Bank Info ****** */

	/**
	 * add new payment account
	 *
	 * @return	bool
	 */
	public function addBankInfo($affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['affiliate_id'] = $affiliate_id;

			$this->template->write_view('main_content', 'affiliate_management/affiliates/add_bank_info', $data);
			$this->template->render();
		}
	}

	/**
	 * verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyaddNewAccount() {
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_info', 'Account Info', 'trim|xss_clean|required');
        if($this->utils->isEnabledFeature('disable_account_name_letter_format')){
            $this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean|required');
        }else{
            $this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean|required|regex_match[/^[a-zA-Z\p{Han}]+$/u]');
        }
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean|required|numeric|is_unique[affiliatepayment.accountNumber]');

		$this->form_validation->set_message('required', lang('formvalidation.required'));
		$this->form_validation->set_message('alpha', lang('formvalidation.alpha'));
		$this->form_validation->set_message('regex_match', lang('formvalidation.alpha'));
		$this->form_validation->set_message('numeric', lang('formvalidation.numeric'));
		$this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));

		$affiliate_id = $this->input->post('affiliate_id');
		$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

		if ($this->form_validation->run() == false) {
			$this->addBankInfo($affiliate_id);
		} else {
			$data = array(
				'affiliateId' => $affiliate_id,
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $this->input->post('bank_name'),
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'createdOn' => date('Y-m-d H:i:s'),
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->affiliate_manager->addPayment($data);

			$this->saveAction('Add Affiliate Bank Information', "User " . $this->authentication->getUsername() . " has add affiliate bank info to " . $affiliateDetails['username']);

			$message = lang('con.aff55');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	/**
	 * edit payment account
	 *
	 * @return	void
	 */
	public function editPayment($affiliate_payment_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			$data['payment'] = $this->affiliate_manager->getPaymentByPaymentId($affiliate_payment_id);

			$this->template->write_view('main_content', 'affiliate_management/affiliates/edit_bank_info', $data);
			$this->template->render();
		}
	}

	/**
	 * verify add new payment account
	 *
	 * @return	void
	 */
	public function verifyEditPayment($affiliate_payment_id) {
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_info', 'Account Info', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean|required|numeric|callback_checkAccountNumber');

		$affiliate_id = $this->input->post('affiliate_id');
		$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

		if ($this->form_validation->run() == false) {
			$this->editPayment($affiliate_payment_id);
		} else {
			$data = array(
				'affiliateId' => $affiliate_id,
				'paymentMethod' => 'Wire Transfer',
				'bankName' => $this->input->post('bank_name'),
				'accountInfo' => $this->input->post('account_info'),
				'accountName' => $this->input->post('account_name'),
				'accountNumber' => $this->input->post('account_number'),
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			foreach ($data as $key => $value) {
                $data[$key] = $this->stripHTMLtags($value);
            }

			$this->affiliate_manager->editPaymentInfo($data, $affiliate_payment_id);

			$this->saveAction('Edit Affiliate Bank Information', "User " . $this->authentication->getUsername() . " has edit affiliate bank info to " . $affiliateDetails['username']);

			$message = lang('con.aff56');
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	public function checkAccountNumber() {
		$affiliate_payment_id = $this->input->post('affiliate_payment_id');
		$payment = $this->affiliate_manager->getPaymentByPaymentId($affiliate_payment_id);
		$account_number = $this->input->post('account_number');

		if ($payment['accountNumber'] != $account_number) {
			$this->form_validation->set_rules('account_number', 'Account Number', 'is_unique[affiliatepayment.accountNumber]');

			if ($this->form_validation->run() == false) {
				$this->form_validation->set_message('checkAccountNumber', "The Account Number field must contain a unique value.");
				return false;
			}
		}

		return true;
	}

	/**
	 * activate payment
	 *
	 * @return	void
	 */
	public function activatePayment($payment_id, $affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$data = array(
				'status' => '0',
				'updatedOn' => date('Y-m-d H:i:s'),
			);

			$this->affiliate_manager->editPaymentInfo($data, $payment_id);

			$paymentInfo = $this->affiliate_manager->getPaymentByPaymentId($payment_id);

			$this->saveAction('Activate Affiliate Bank Information', "User " . $this->authentication->getUsername() . " has activate affiliate bank info to " . $affiliateDetails['username']);

			$message = lang('con.aff57') . ": " . lang(str_replace("%20", " ", $paymentInfo['bankName']));
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	/**
	 * deactivate payment
	 *
	 * @return	void
	 */
	public function deactivatePayment($payment_id, $affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$data = array(
				'status' => '1',
				'updatedOn' => date('Y-m-d H:i:s'),
			);
			$this->affiliate_manager->editPaymentInfo($data, $payment_id);

			$paymentInfo = $this->affiliate_manager->getPaymentByPaymentId($payment_id);

			$this->saveAction('Deactivate Affiliate Bank Information', "User " . $this->authentication->getUsername() . " has deactivate affiliate bank info to " . $affiliateDetails['username']);

			$message = lang('con.aff58') . ": " . lang(str_replace("%20", " ", $paymentInfo['bankName']));
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	/**
	 * delete payment
	 *
	 * @return	void
	 */
	public function deletePayment($payment_id, $affiliate_id) {
		if (!$this->permissions->checkPermissions('view_affiliates')) {
			$this->error_access();
		} else {
			$paymentInfo = $this->affiliate_manager->getPaymentByPaymentId($payment_id);
			$affiliateDetails = $this->affiliate_manager->getAffiliateById($affiliate_id);

			$this->affiliate_manager->deletePaymentInfo($payment_id);
			$this->saveAction('Delete Affiliate Bank Information', "User " . $this->authentication->getUsername() . " has delete affiliate bank info to " . $affiliateDetails['username']);

			$message = lang('con.aff59') . ": " . lang(str_replace("%20", " ", $paymentInfo['bankName']));
			$this->alertMessage(1, $message); //will set and send message to the user
			redirect("affiliate_management/userInformation/" . $affiliate_id, "refresh");
		}
	}

	public function player_info($player_id) {
		$this->load->library(['player_manager', 'payment_manager', 'game_functions']);

		$data['player'] = $this->player_manager->getPlayerById($player_id);
		$data['playeraccount'] = $this->player_manager->getPlayerAccount($player_id);
		$data['affiliate'] = $this->player_manager->getAffiliateOfPlayer($player_id);

		$referrer = $this->player_manager->getCodeofReferrer($player_id);
		$data['referred_by_code'] = empty($referrer) ? null : $referrer['referrer_code'];
		$data['referred_by_id'] = empty($referrer) ? null : $referrer['referrer_id'];

		$referrals = $this->player_manager->getAllReferralByPlayerId($player_id);
		$data['referral_count'] = ($referrals == false) ? 0 : count($referrals);

		$data['first_last_deposit'] = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::DEPOSIT);
		$data['first_last_withdraw'] = $this->player_manager->getPlayerFirstLastApprovedTransaction($player_id, Transactions::WITHDRAWAL);
		$data['total_deposits'] = $this->player_manager->getPlayerTotalDeposits($data['playeraccount']['playerAccountId']);
		$data['total_withdrawal'] = $this->player_manager->getPlayerTotalWithdrawal($data['playeraccount']['playerAccountId']);
		$data['mainwallet'] = $this->player_manager->getMainWallet($player_id);
		$data['subwallet'] = $this->payment_manager->getAllPlayerAccountByPlayerId($player_id);
		$data['total_deposit_bonus'] = $this->player->getTotalBonus($player_id);
		$data['total_cashback_bonus'] = $this->player->getTotalCashbackBonus($player_id);
		$data['total_referral_bonus'] = $this->player->getTotalReferralBonus($player_id);

		$average = "";
		if (!empty($data['total_deposits']['totalDeposit']) && !empty($data['total_deposits']['totalNumberOfDeposit'])) {
			$average = ($data['total_deposits']['totalDeposit'] / $data['total_deposits']['totalNumberOfDeposit']);
		}
		$data['average_deposits'] = $average ? $average : '0';

		$average = "";
		if (!empty($data['total_withdrawal']['totalWithdrawal']) && !empty($data['total_withdrawal']['totalNumberOfWithdrawal'])) {
			$average = ($data['total_withdrawal']['totalWithdrawal'] / $data['total_withdrawal']['totalNumberOfWithdrawal']);
		}
		$data['average_withdrawals'] = $average ? $average : '0';

		$data['games'] = $this->game_functions->getGameProviders($player_id);
		$data['blocked_games'] = $this->player_manager->getPlayerBlockedGames($player_id);
		$data['api_details'] = $this->player_manager->getAPIDetails($player_id);

		$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
		$this->template->add_js('resources/js/player_management/player_management.js');
		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'affiliate_management/view_user_information', $data);
		$this->template->render();
	}

	public function getAffiliates($id = NULL) {
		$this->load->model(array('affiliate'));
		$affiliates = $this->affiliate->getAffiliates();
		$data['affiliates'] = json_decode(json_encode($affiliates), true);
		$arr = array('status' => 'success', 'data' => $data);
		echo json_encode($arr);
	}

	/* ****** End of Affiliate User Information Bank Info ****** */
}