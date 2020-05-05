<?php
trait player_withdraw_module {

	public function iframe_viewWithdraw(){
		redirect('player_center/withdraw');
	}

	/**
	 * view withdrawal form
	 *
	 *
	 * @return rendered template
	 */
	public function withdraw( $new_bank = '' ) {
        redirect('player_center2/withdraw'); // disable legacy function.
	}

	/**
	 * verify withdrawal
	 *
	 *
	 * @return rendered template
	 */
	public function verifyWithdrawal($is_api_call = false) {
		$this->load->model('playerbankdetails');

		# OG-712 Update withdrawal functions for enabled_withdrawal
		$playerId = $this->authentication->getPlayerId();
		$player = $this->player_functions->getPlayerById($playerId);
		if ($player['enabled_withdrawal'] == self::WITHDRAWAL_DISABLED) {
			//$this->iframe_viewWithdraw();
            if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_DISABLED_WITHDRAWAL, 'message' => lang('Withdrawal disabled')]);
                return;
            }elseif ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => ''));
                return;
            }

			$this->withdraw();
			return;
		}

		if(!$this->verifyAndResetDoubleSubmit($playerId)){

			$message = lang('Please refresh and try, and donot allow double submit');
			if( $this->input->is_ajax_request() ){
				// $this->utils->debug_log($message);
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('/player_center/withdraw');
			return;

		}

		$withdrawVerificationMethod = $this->utils->getConfig('withdraw_verification');
		$contactNumberShow = $this->utils->getConfig('contact_number_show_withdrawal');

		$accountType = ($this->input->post('account_type') == "na") ? "1":"";
		$bankTypeId = $this->input->post('bank_name');
		$bank_account_number = $this->input->post('bank_account_number');
		$this->utils->debug_log('withdrawVerificationMethod', $withdrawVerificationMethod, $contactNumberShow, $accountType, $bankTypeId, $bank_account_number);
		// $isUniqueBankAccountNumber = $this->playerbankdetails->checkUniqueBankAccountNumber($bank_account_number, $accountType, $playerId,$bankTypeId);
		//only check new bank
		if($accountType=='1'){

			$bank_type=Playerbankdetails::WITHDRAWAL_BANK;
			$validate_success = $this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
					$bank_type, null);

			if(!$validate_success){
				$message = lang('bank account already exist') . "!";

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_BANK_ACCOUNT_EXIST, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

		}

		if ('password' == $withdrawVerificationMethod) {
			$userPassword = $this->input->post('password');
			if (!$this->authentication->validateOnly($userPassword)) {
				$msg = lang('Incorrect password');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INCORRECT_PASSWORD, 'message' => $msg]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($msg);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $msg));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $msg);
				$this->withdraw();
				return;
			}
		}

		if ('sms' == $withdrawVerificationMethod && !$this->utils->getConfig('disabled_sms')) {
			$this->load->model('sms_verification');
			// $this->load->library(array('authentication', 'player_functions'));

			$session_id = $this->session->userdata('session_id');
			$sms_verification_code = $this->input->post('sms_verification_code');
			// $player = $this->player_functions->getPlayerById($player_id);
			$mobileNumber = $player['contactNumber']; # we can assume this mobile number exist at this step

			if (!$this->sms_verification->validateVerificationCode($playerId, $session_id, $mobileNumber, $sms_verification_code)) {
				$message = lang('Invalid SMS verification code');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INVALID_SMS_VERIFICATION, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}
		}

		if( $withdrawVerificationMethod == 'withdrawal_password' && $this->utils->isEnabledFeature('enabled_withdrawal_password')){
			$withdrawal_password = $this->input->post('withdrawal_password');
			$checkWithdrawPassword=$this->player_model->validateWithdrawalPassword($playerId, $withdrawal_password);
			// $checkWithdrawPassword = $this->player_functions->isValidWithdrawPassword($player_id, $withdrawal_password);
			$this->utils->debug_log('withdrawal password : ' . $withdrawal_password . ', checkWithdrawPassword' . $checkWithdrawPassword);

			if( ! $checkWithdrawPassword ){
				$message = lang('Invalid Withdrawal Password');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INVALID_WITHDRAWAL_PASSWORD, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}
		}

		if($contactNumberShow){
			$contactNumber = $this->input->post('contact_number');
			$checkifPlayerContactNumberExist = $this->player_model->checkPhoneNumberIfCorrect($playerId, $contactNumber);

			if($checkifPlayerContactNumberExist == 0){
				$message = lang('Your Contact Number is incorrect!');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INCORRECT_CONTACT_NUMBER, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}
		}


		#check  withdrawal conditions
		if ($this->utils->isEnabledFeature('check_withdrawal_conditions')) {
            if(FALSE !== $un_finished = $this->withdraw_condition->getPlayerUnfinishedWithdrawCondition($playerId)){
                //un_finished
                $message = lang('notify.withdrawal.condition');
                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INVALID_WITHDRAWAL_PASSWORD, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }else{
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    $this->withdraw();
                    return;
                }
            }
		}

		#check  withdrawal conditions -- for each
		if ($this->utils->isEnabledFeature('check_withdrawal_conditions_foreach')) {
            if(FALSE !== $withdraw_data = $this->withdraw_condition->getPlayerWithdrawalCondition($playerId)){
                $message = lang('notify.withdrawal.condition');
                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INVALID_WITHDRAWAL_PASSWORD, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }else{
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    $this->withdraw();
                    return;
                }
            }
		}

		$this->utils->debug_log('withdrawVerificationMethod', $withdrawVerificationMethod, $contactNumberShow, $accountType, $bankTypeId, $bank_account_number);

		//transfer back balance by feature
		$rlt= $this->_transferBackToMainWallet($playerId, $player['username']);
		$this->utils->debug_log('after _transferBackToMainWallet, result', $rlt);
		if(!$rlt){

			$msg=lang('Transfer Failed From Sub-wallet');
            $this->utils->debug_log($msg);
            if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_WITHDRAWAL_FAILED, 'message' => $msg]);
                return;
            }elseif ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => $msg));
                return;
            }

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $msg);
			return redirect($redirectPage);
		}

		$success=false;
		$lockedKey=null;
		$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
		$this->utils->debug_log('lock withdrawal', $playerId, $lock_it);
		if (!$lock_it) {
			//withdrawl failed
			$message = lang('error.withdrawal_failed');

            if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_WITHDRAWAL_FAILED, 'message' => $message]);
                return;
            }elseif ($this->input->is_ajax_request()) {
                $this->utils->debug_log($message);
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			//$this->iframe_viewWithdraw();
			$this->withdraw();
			return;
		}
		//lock success
		try {
			$this->form_validation->set_rules('amount', 'Withdrawal Amount', 'trim|required|xss_clean|is_numeric');

			# Password Validation
			// $this->form_validation->set_rules('password', 'lang:password', 'trim|required|xss_clean');

			$withdrawalAmount = $this->input->post('amount');
			$bankName = $this->input->post('bank_name');
			$preferredAccount = $this->input->post('preferred_account'); // id of playerbankdetails

			$player = $this->player_model->getPlayerDetailsById($playerId);
			$player = get_object_vars($player);
			//should check language, because it should add blank if english
			$realname = $player['lastName'] . $player['firstName'];
			$bankAccountFullname = $realname; // full name   $this->input->post('bank_account_fullname');
			$bankAccountNumber = $this->input->post('bank_account_number');
			$bankAddress = $this->input->post('bank_address');
			$province = $this->input->post('bank_province');
			$city = $this->input->post('bank_city');
			$branch = $this->input->post('branch');
			$dwIp = $this->utils->getIP(); //$this->input->post('dw_ip');

			$rememberMyAccount = $this->input->post('rememberMyAccount_cb');
			$account_type = $this->input->post('account_type');

			$this->session->set_userdata('wf_bank_name', $bankName);
			$this->session->set_userdata('wf_bank_account_fullname', $bankAccountFullname);
			$this->session->set_userdata('wf_bank_account_number', $bankAccountNumber);
			$this->session->set_userdata('wf_bank_address', $bankAddress);
			// $this->session->set_userdata('wf_province', $province);
			// $this->session->set_userdata('wf_city', $city);
			$this->session->set_userdata('wf_branch', $branch);
			$this->session->set_userdata('wf_rememberMyAccount', $rememberMyAccount);

			$today = $this->utils->getNowForMysql();// date("Y-m-d H:i:s");

			$this->load->model(array('player_model', 'group_level', 'wallet_model', 'transactions','daily_player_trans', 'operatorglobalsettings','group_level', 'withdraw_condition'));

			//get player's data
			// $playerId = $this->authentication->getPlayerId();

			//get player's main wallet balance
			// $playerMainWalletBalance = $this->wallet_model->getMainWalletBalance($playerId);
			$mainWalletId = 0;
			$message = null;
			$playerBankDetailsId = null;
			$isAvailBalance = $this->utils->checkTargetBalance($playerId, $mainWalletId, $withdrawalAmount, $message);

			//player's account details
			$playerAccount = $this->player_model->getPlayerAccountByPlayerIdOnly($playerId);

			// $playerCurrentTotalWithdrawalToday = $this->transactions->get_today_withdraw($playerId);
			// $trans_amount = 0;
			// $trans_count = 0;
			// if ($playerCurrentTotalWithdrawalToday) {
			// 	$trans_amount = @$playerCurrentTotalWithdrawalToday->trans_amount;
			// 	$trans_count = @$playerCurrentTotalWithdrawalToday->trans_count;
			// }

			//check if player has existing active promo
			// $existingActivePromoFlag = $this->player_functions->checkPlayerPromoActive($playerId);

			//check if player has existing active promo
			// $existingRequestPromoFlag = $this->player_functions->checkPlayerPromoRequest($playerId);

			// $withdrawRule = $this->group_level->getPlayerMaxWithdrawalPerTransaction($playerId);
			// $maxWithdrawPerTransaction = @$withdrawRule[0]['max_withdraw_per_transaction'];

			$show_warning_for_one_withdraw = false;
			$one_withdraw_only = $this->group_level->isOneWithdrawOnly($playerId);
			if ($one_withdraw_only) {
				$cnt = $this->player_model->countWithdrawByStatusList($playerId);
				if ($cnt >= 1) {
					$show_warning_for_one_withdraw = true;
				}
			}

			$username = $this->authentication->getUsername();
			$password = $this->input->post('password');
			$withdrawal_password = $this->input->post('withdrawal_password');
			if(empty($password)){
				$password=$withdrawal_password;
			}

			$withdraw_verification=$this->utils->getConfig('withdraw_verification');

			if($withdraw_verification=='withdrawal_password' && $this->utils->isEnabledFeature('enabled_withdrawal_password')){
				$validatedPassword=$this->player_model->validateWithdrawalPassword($playerId, $password);
			}elseif($withdraw_verification=='password'){
				$validatedPassword=$this->authentication->validateOnly($password);
			}else{
				$validatedPassword=true;
			}

			if (!$validatedPassword) {
				if($this->utils->isEnabledFeature('enabled_withdrawal_password')){
					$message = lang('Wrong withdraw password');
				}else{
					$message = $this->authentication->get_error_message()['password'];
				}

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INCORRECT_WITHDRAWAL_PASSWORD, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($this->form_validation->run() == false) {
				$message = lang("Some fields are filled incorrectly");

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_FORM_VALIDATION_FAILED, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($show_warning_for_one_withdraw) {
				$message = lang('Sorry, your last withdraw request is not done, so you can\'t start new request');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_LAST_WITHDRAWAL_NOT_DONE, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			// } elseif ($withdrawalAmount > $maxWithdrawPerTransaction) {
			// 	$message = lang('Max Withdrawal Per Transaction').' is : '.$maxWithdrawPerTransaction;
			// 	$arr = array('status' => 'error', 'msg' => $message);
			}

			if (!$isAvailBalance) {
				//$message = "Withdrawal amount should not be greater than your current main wallet balance. Please try again!";
				$message = lang('notify.55');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INSUFFICIENT_BALANCE, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

            //get player's withdrawal rule
            $playerWithdrawalRule = $this->utils->getWithdrawMinMax($playerId);
            list($trans_count, $trans_amount) = $this->transactions->count_today_withdraw($playerId);
            if (!$playerWithdrawalRule || is_null($playerWithdrawalRule['max_withdraw_per_transaction'])) {
                $message = lang('Undefined withdraw rule');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_MW_UNDEFINED_WITHDRAW_RULE, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                $this->withdraw();
                return;
            }
			if ($withdrawalAmount > $playerWithdrawalRule['max_withdraw_per_transaction']) {
				$message = lang('Max Withdrawal Per Transaction is') . $playerWithdrawalRule['max_withdraw_per_transaction'];

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_OVER_MAX_WITHDRAWAL, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($withdrawalAmount + $trans_amount > $playerWithdrawalRule['dailyMaxWithdrawalAmount']) {
				$message = lang('notify.56');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_OVER_MAX_DAILY_WITHDRAWAL, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($trans_count >= $playerWithdrawalRule['withdraw_times_limit']) {
				$message = lang('notify.106');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_OVER_WITHDRAWAL_TIMES_LIMITS, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($withdrawalAmount <= 0) {
				$message = lang('notify.57');

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_INVALID_WITHDRAWAL_AMOUNT, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}

			if ($withdrawalAmount < $playerWithdrawalRule['min_withdraw_rule']) {
				$message = lang('notify.102') . $playerWithdrawalRule['min_withdraw_rule'];

                if($is_api_call){
                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_LESS_MIN_WITHDRAWAL, 'message' => $message]);
                    return;
                }elseif ($this->input->is_ajax_request()) {
                    $this->utils->debug_log($message);
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    return;
                }

				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				$this->withdraw();
				return;
			}


			if ($this->utils->isEnabledFeature('check_withdrawal_conditions')) {

				$playerId = $this->authentication->getPlayerId();
	    		$withdraw_data = $this->withdraw_condition->getPlayerWithdrawalCondition($playerId);
	    		$totalRequiredBet =$this->multi_array_sum($withdraw_data,'conditionAmount');
	    		$totalPlayerBet =$this->multi_array_sum($withdraw_data,'currentBet');
	    		$un_finished = $totalRequiredBet - $totalPlayerBet ;

	    		//check if withdrawal conditions
				if ($un_finished > 0){
					$message = lang('notify.withdrawal.condition');

	                if($is_api_call){
	                    $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_LESS_MIN_WITHDRAWAL, 'message' => $message]);
	                    return;
	                }elseif ($this->input->is_ajax_request()) {
	                    $this->utils->debug_log($message);
	                    $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
	                    return;
	                }
				}
			}

			//check if new account or old account
			if (!empty($account_type) && $account_type == 'na') {
				$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('bank_account_fullname', 'Bank Account Full Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|required|xss_clean|is_numeric|callback_check_new_withdrawal_bank_account_number');
				$this->form_validation->set_rules('bank_address', 'Bank Address', 'trim|xss_clean');
				// $this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
				// $this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
				$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean');
				//check if remember this account add to record
				$rememberMyAccount == TRUE ? $rememberMyAccount = '1' : $rememberMyAccount = '0';

				//add bank account details and get id
				$data = array(
					'playerId' => $playerId,
					'bankTypeId' => $bankName, //bank type id
					'bankAccountNumber' => $bankAccountNumber,
					'bankAccountFullName' => $bankAccountFullname,
					'bankAddress' => $bankAddress,
					'city' => $city,
					'province' => $province,
					'branch' => $branch,
					'isRemember' => $rememberMyAccount, //0 is false
					'dwBank' => '1', //0 is withdrawal
					'status' => '0', //0 is active
				);

				$playerBankDetailsId = $this->player_functions->addBankDetailsByWithdrawal($data);
			} else {
				$playerBankDetailsId = $preferredAccount;

				$this->utils->debug_log("Updating city and province for stored bank [$playerBankDetailsId]...", $city, $province);
				if(!empty($city) && !empty($province)) {
					$data = array(
						'city' => $city,
						'province' => $province,
					);
					$this->player_functions->updateBankDetails($playerBankDetailsId, $data);
				}
			}
			$geolocation = $this->utils->getGeoplugin($dwIp);

			//add to player frozen amount
			// $this->freezeAmount($playerMainWalletBalance['totalBalanceAmount'], $withdrawalAmount);

			$this->player_model->startTrans();

			$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);
			$playerBankDetails = $this->playerbankdetails->getBankList(array('playerBankDetailsId' => $playerBankDetailsId))[0];
			$bankName = $this->banktype->getBankTypeById($playerBankDetails['bankTypeId'])->bankName;

			//wallet account details
			$walletAccountData = array(
				'playerAccountId' => $playerAccount['playerAccountId'],
				'walletType' => 'Main',
				'amount' => $withdrawalAmount,
				'dwMethod' => 1,
				'dwStatus' => 'request',
				'dwDateTime' => $today,
				'transactionType' => 'withdrawal',
				'dwIp' => $dwIp,
				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
				'transactionCode' => $this->wallet_model->getRandomTransactionCode(),
				'status' => '0',
				'before_balance' => $beforeBalance,
				'after_balance' => $beforeBalance - $withdrawalAmount,
				'playerId' => $playerId,
				'player_bank_details_id'=>$playerBankDetailsId,
				'bankAccountFullName' => $playerBankDetails['bankAccountFullName'],
				'bankAccountNumber' => $playerBankDetails['bankAccountNumber'],
				'bankName' => $bankName,
				'bankAddress' => $playerBankDetails['bankAddress'],
				'bankCity' => $playerBankDetails['city'],
				'bankProvince' => $playerBankDetails['province'],
				'bankBranch' => $playerBankDetails['branch'],
			);

			//save record to withdrawal request
			$localBankWithdrawalDetails = array(
				'withdrawalAmount' => $withdrawalAmount,
				'playerBankDetailsId' => $playerBankDetailsId,
				'depositDateTime' => $today,
				'status' => 'active',
			);
			//$this->player_functions->createAccountDetails($data);
			$walletAccountId = $this->wallet_model->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);

			// save http_request (cookies, referer, user-agent)
			$this->saveHttpRequest($this->authentication->getPlayerId(), Http_request::TYPE_WITHDRAWAL);

			// $this->withdraw_condition->checkAndCleanWithdrawCondition($playerId);
			if($walletAccountId === false) {
				$this->utils->error_log("Wallet model newWithdrawal fails, rolling back transaction. Player ID: ", $playerId);
				$this->player_model->rollbackTrans();
				$success = false;
			} else {
				$this->utils->debug_log("Wallet model newWithdrawal success, walletAccountId: ", $walletAccountId);
				$success = $this->player_model->endTransWithSucc();
			}
		} finally {
			// release it
			$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
			// $rlt = $this->player_model->transReleaseLock($trans_key);
			$this->utils->debug_log('release withdrawal lock', $playerId, $rlt);
		}

		if($success){
			$redirect_url = $this->input->post('redirect_url');
			//$message = $this->session->userdata('promoMessage') . " Your withdrawal request has been sent!";
			$message = lang('notify.58');

            if($is_api_call){
                $this->returnJsonResult(['success' => true, 'code' => Api_common::CODE_SUCCESS, 'message' => $message]);
                return;
            }elseif ($this->input->is_ajax_request()) {
                $this->utils->debug_log($message);
                $this->returnJsonResult(array('status' => 'success', 'msg' => $message, 'playerBankDetailsId' => $playerBankDetailsId));
                return;
            }

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

            if(empty($redirect_url)){
                $this->withdraw();
            }else{
                redirect(site_url($redirect_url));
                //$this->goViewCashier();
            }

        }else{

			//withdrawl failed
			$message = lang('error.withdrawal_failed');

            if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_WITHDRAWAL_FAILED, 'message' => $message]);
                return;
            }elseif ($this->input->is_ajax_request()) {
                $this->utils->debug_log($message);
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			//$this->iframe_viewWithdraw();
			$this->withdraw();
		}


		// }
	}

	//sum
    public function multi_array_sum($arr,$key) {
		if ($arr) {
			$sum_no = 0;
			foreach($arr as $v){
			   $sum_no +=  $v[$key];
			 }
			return $sum_no;
		} else {
			return 0;
		}
	}

	public function valWidthdrawalAmtThruAjax() {

		// show_error('bad request', 400);

		$playerId = $this->authentication->getPlayerId();
		$player = $this->player_functions->getPlayerById($playerId);
		if ($player['enabled_withdrawal'] == self::WITHDRAWAL_DISABLED) {
			//return 400
			show_error('bad request', 400);
			// $this->iframe_viewWithdraw();
			return;
		}
		$withdrawalAmount = $this->input->post('amount');
		//get player's main wallet balance
		$playerMainWalletBalance = $this->player_model->getMainWalletBalance($playerId);
		//get player's withdrawal rule
		$playerWithdrawalRule = $this->player_functions->getPlayerWithdrawalRule($playerId);
		//player's account details
		$playerAccount = $this->player_functions->getPlayerAccountByPlayerIdOnly($playerId);
		//get player's withdrawal rule
		//$playerCurrentTotalWithdrawalToday = $this->player_functions->getPlayerCurrentTotalWithdrawalToday($playerAccount['playerAccountId']);
		// $this->load->model('daily_player_trans');
		// $playerCurrentTotalWithdrawalToday = $this->daily_player_trans->get_today_withdraw($playerId);
		$playerCurrentTotalWithdrawalToday = $this->transactions->get_today_withdraw($playerId);
		$trans_amount = 0;
		$trans_count = 0;
		if (!empty($playerCurrentTotalWithdrawalToday)) {
			$trans_amount = $playerCurrentTotalWithdrawalToday->trans_amount;
			$trans_count = $playerCurrentTotalWithdrawalToday->trans_count;
		}

		//get min withdrawal rule
		$this->load->model(array('operatorglobalsettings','group_level'));
		$min_withdraw_rule = $this->operatorglobalsettings->getSettingDoubleValue('min_withdraw');

		//check if player has existing active promo
		// $existingActivePromoFlag = $this->player_functions->checkPlayerPromoActive($playerId);

		//check if player has existing active promo
		// $existingRequestPromoFlag = $this->player_functions->checkPlayerPromoRequest($playerId);
		$withdrawRule = $this->group_level->getPlayerMaxWithdrawalPerTransaction($playerId);
		$nonDepositor = $this->transactions->getTransactionCount(array(
							'to_id' => $playerId,
							'to_type' => Transactions::PLAYER,
							'transaction_type' => Transactions::DEPOSIT,
							'status' => Transactions::APPROVED,
						));

		$maxWithdrawPerTransaction = ($nonDepositor == 0) ? @$withdrawRule[0]['max_withdrawal_non_deposit_player'] : @$withdrawRule[0]['max_withdraw_per_transaction'];
		//$maxWithdrawPerTransaction = @$withdrawRule[0]['max_withdraw_per_transaction'];

		if ($withdrawalAmount > $maxWithdrawPerTransaction) {
			$message = lang('Max Withdrawal Per Transaction is').': '.$maxWithdrawPerTransaction;
			$arr = array('status' => 'error', 'msg' => $message);
		} elseif ($withdrawalAmount > $playerMainWalletBalance) {
			//$message = "Withdrawal amount should not be greater than your current main wallet balance. Please try again!";
			$message = lang('notify.55');
			$arr = array('status' => 'error', 'msg' => $message);
			// echo json_encode($arr);
		} elseif ($withdrawalAmount + $trans_amount > $playerWithdrawalRule['dailyMaxWithdrawal']) {
			//$message = "Your amount request should not greater than your daily max withdrawal limit.";
			$message = lang('notify.56');
			$arr = array('status' => 'error', 'msg' => $message);
			// echo json_encode($arr);
		} elseif ($trans_count >= $playerWithdrawalRule['withdraw_times_limit']) {
			//$message = "Your amount request should not greater than your daily max withdrawal limit.";
			$message = lang('Exceed max withdrawal times');
			$arr = array('status' => 'error', 'msg' => $message);
			// echo json_encode($arr);
		} elseif ($withdrawalAmount <= 0) {
			//$message = "Your withdrawal amount request should not be less than or equal to zero.";
			$message = lang('notify.57');
			$arr = array('status' => 'error', 'msg' => $message);
			// echo json_encode($arr);
		} elseif ($withdrawalAmount < $min_withdraw_rule) {
			//$message = "Your withdrawal amount request shuld not less than <$min_withdraw_rule> ";
			$message = lang('notify.102') . $min_withdraw_rule;
			$arr = array('status' => 'error', 'msg' => $message);
			// echo json_encode($arr);
		} else {
			$arr = array('status' => 'success', 'msg' => "");
			// echo json_encode($arr);
		}

		$this->returnJsonResult($arr);

	}

	// public function check_unique_bank_number() {
	// 	$this->load->model(['playerbankdetails']);

	// 	$playerId = $this->authentication->getPlayerId();

	// 	$accountType = $this->input->post('account_type');

	// 	$bank_account_number = $this->input->post('bank_account_number');
	// 	$newBankAcctNo = $this->input->post('bank_account_number');
	// 	$prevBankAcctNo = $this->input->post('bank_account_number_prev');
	// 	$bankTypeId = $this->input->post('bankTypeId');

	// 	if (!empty($newBankAcctNo)) {
	// 		if ($newBankAcctNo == $prevBankAcctNo) {
	// 			$success = true;
	// 		} else {
	// 			$success = !$this->playerbankdetails->checkUniqueBankAccountNumber($newBankAcctNo, $accountType, $playerId,$bankTypeId);
	// 		}
	// 	} else {
	// 		$success = !$this->playerbankdetails->checkUniqueBankAccountNumber($bank_account_number, $accountType, $playerId,$bankTypeId);
	// 	}
	// 	if (!$success) {
	// 		$this->form_validation->set_message('check_unique_bank_number', lang('Bank Account Number already exist'));
	// 	}

	// 	return $success;

	// }

	public function check_new_withdrawal_bank_account_number($bank_account_number){
		$playerId = $this->authentication->getPlayerId();
		if(empty($playerId)){
			$this->form_validation->set_message('check_new_withdrawal_bank_account_number', lang('Please login again'));
			return false;
		}

		// $success=true;
		// $account_type = $this->input->post('account_type');
		// if($account_type=='na'){

			$bank_details_id=null;

			$this->load->model(['playerbankdetails']);
		// 	$bank_type=Playerbankdetails::WITHDRAWAL_BANK;
		// 	$success=$this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
		// 		$bank_type, $bank_details_id);

		// }

		// if(!$success){
		// 	$this->form_validation->set_message('check_new_withdrawal_bank_account_number', lang('Bank Account Number already exist'));
		// }

		// return $success;

		$bank_type=Playerbankdetails::WITHDRAWAL_BANK;
		return $this->common_validate_bank_account_number('check_new_withdrawal_bank_account_number',
			$playerId, $bank_account_number, $bank_type, $bank_details_id);

	}


}
