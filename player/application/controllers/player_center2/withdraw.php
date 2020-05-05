<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Provides withdrawal function in player center dashboard
 */
class Withdraw extends PlayerCenterBaseController {
	const WITHDRAWAL_ENABLED = 1;
	const WITHDRAWAL_DISABLED = 0;
	const NUM_CHAR_DISPLAY = 5;
	const BANK_TYPE_ARR = ['bank','alipay','wechat'];

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->preloadCashierVars();
	}

	public function index() {
		return $this->bank();
	}

	public function bank() {
		$this->load->vars('type', 'bank');
		$this->loadBanks('bank');
		return $this->display();
	}

	public function wechat() {
		$this->load->vars('type', 'wechat');
		$this->loadBanks('wechat');
		return $this->display();
	}

	public function alipay() {
		$this->load->vars('type', 'alipay');
		$this->loadBanks('alipay');
		return $this->display();
	}

	private function loadBanks($type) {
		$this->load->model(['banktype', 'playerbankdetails']);
		$playerId = $this->load->get_var('playerId');
		$bankList = $this->utils->getAllBankTypes();

		$this->load->vars('bankList', $bankList);

		$this->load->vars(['isAlipayEnabled' => false]);
		$this->load->vars(['isWeChatEnabled' => false]);

		$bank_type_alipay = $this->playerbankdetails->BANK_TYPE_ALIPAY;
		$bank_type_wechat = $this->playerbankdetails->BANK_TYPE_WECHAT;

		# Record down whether alipay or wechat is enabled for withdrawal
		foreach($bankList as $bank) {
			if($bank['bankTypeId'] == $bank_type_alipay) {
				$this->load->vars(['isAlipayEnabled' => $bank['enabled_withdrawal']]);
			} elseif ($bank['bankTypeId'] == $bank_type_wechat) {
				$this->load->vars(['isWeChatEnabled' => $bank['enabled_withdrawal']]);
			}
		}

		# Player's saved bank accounts
		$playerBankDetails = array();
		if('bank' == $type) {
			$playerBankDetails = $this->playerbankdetails->getWithdrawBankOnlyDetail($playerId);
			// OGP-2513: generate bank icon url for each bank entry
			foreach ($playerBankDetails as $key => $bank) {
				$playerBankDetails[$key]['bank_icon_url'] = '';
				$playerBankDetails[$key]['is_crypto'] = 0;
				if (!empty($bank['bankIcon'])) {
					$playerBankDetails[$key]['bank_icon_url'] = Banktype::getBankIcon($bank['bankIcon']);
				}
				$banktype = $this->banktype->getBankTypeById($playerBankDetails[$key]['bankTypeId']);
				if($this->utils->isCryptoCurrency($banktype)) {
					$playerBankDetails[$key]['is_crypto'] = 1;
				}
			}
		} elseif ('wechat' == $type) {
			$playerBankDetails = $this->playerbankdetails->getWithdrawWeChatAccountDetail($playerId);
			foreach ($playerBankDetails as $key => $bank) {
				$playerBankDetails[$key]['is_crypto'] = 0;
			}
		} elseif ('alipay' == $type) {
			$playerBankDetails = $this->playerbankdetails->getWithdrawAlipayAccountDetail($playerId);
			foreach ($playerBankDetails as $key => $bank) {
				$playerBankDetails[$key]['is_crypto'] = 0;
			}
		}

		# Fill accounts with displayName, modifying the array in foreach
		foreach($playerBankDetails as $bankIndex => $bank) {
            $playerBankDetails[$bankIndex]['bankAccountNumber'] = Playerbankdetails::getDisplayAccNum($bank['bankAccountNumber']);
            $playerBankDetails[$bankIndex]['displayName'] = lang($bank['bankName']).' ('.$playerBankDetails[$bankIndex]['bankAccountNumber'].')';
		}

		# Player's default bank account / last used bank account
		$lastUsedBankDetailsId = $this->session->flashdata('bankDetailsId');
		foreach($playerBankDetails as $playerBankDetail) {
			if(empty($lastUsedBankDetailsId) && $playerBankDetail['isDefault'] == 1
				|| $lastUsedBankDetailsId == $playerBankDetail['playerBankDetailsId']) {
				$playerDefaultBankDetail = $playerBankDetail;
				break;
			}
		}
		if(empty($playerDefaultBankDetail) && !empty($playerBankDetails)) {
			# No default bank set, use the first stored bank
			$playerDefaultBankDetail = $playerBankDetails[0];
		}

		$this->load->vars('playerBankDetails', $playerBankDetails);
		if(!empty($playerDefaultBankDetail)) {
			$this->load->vars('playerDefaultBankDetail', $playerDefaultBankDetail);
		}
	}

	private function getDisplayAccNum($accNum) {
		if(strlen($accNum) <= self::NUM_CHAR_DISPLAY){
			return $accNum;
		}
		return '*'.substr($accNum, -self::NUM_CHAR_DISPLAY);
	}

	private function display(){

		$this->load->model(['system_feature','risk_score_model', 'financial_account_setting']);

		$result = $this->session->flashdata('result');

		$playerId = $this->load->get_var('playerId');
		$player = $this->load->get_var('player');

		# Withdraw settings
		$data['withdrawSetting'] = $playerWithdrawalRule = $this->utils->getWithdrawMinMax($playerId);

		# Player's name, needed when creating new deposit account
		$data['realname'] = Player::getPlayerFullName($player['firstName'], $player['lastName'], $player['language']);

		$data['has_withdraw_password'] = (!empty($player['withdraw_password']));

		# Message about the last data submission
		$data['result'] = $result;

		if($player['enabled_withdrawal'] == self::WITHDRAWAL_DISABLED) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('withdrawal_disabled_message'));
			return redirect('/');
		}

		if ($this->utils->isEnabledFeature("show_allowed_withdrawal_status") && $this->utils->isEnabledFeature("show_risk_score") && $this->utils->isEnabledFeature("show_kyc_status")) {
			$data['allowed_withdrawal_status_kyc_risk_score'] = $this->risk_score_model->generate_allowed_withdrawal_status($playerId);
		} else {
			$data['allowed_withdrawal_status_kyc_risk_score'] = true;
		}

		$data['current_controller'] = 'player_center2';

		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        if($this->config->item('cryptocurrencies')){
            $cryptocurrencies = $this->config->item('cryptocurrencies');
            $defaultCurrency  = $this->utils->getCurrentCurrency()['currency_code'];
            foreach($cryptocurrencies as $cryptocurrency) {
                list($crypto, $rate) = $this->utils->convertCryptoCurrency(1, $cryptocurrency, $defaultCurrency);
                $custom_withdrawal_rate = $this->config->item('custom_withdrawal_rate') ? $this->config->item('custom_withdrawal_rate') : 1;
                $player_rate = number_format($rate * $custom_withdrawal_rate, 4, '.', '');
                $data['cryptocurrencies'][$cryptocurrency] = $player_rate;
            }
	    }

        $data['showMaxWithdrawalPerTransaction'] = 1;
        $data['showDailyMaxWithdrawalAmount'] = 1;
        $withdrawal_page_setting = $this->operatorglobalsettings->getPlayerCenterWithdrawalPageSetting();
        if(isset($withdrawal_page_setting)){
            $data['showMaxWithdrawalPerTransaction'] = $withdrawal_page_setting['showMaxWithdrawalPerTransaction'];
            $data['showDailyMaxWithdrawalAmount'] = $withdrawal_page_setting['showDailyMaxWithdrawalAmount'];
        }

	    $data['withdraw_amount_step_limit'] = $this->CI->config->item('withdraw_amount_step_limit');
		# Templates
		$this->loadTemplate();

		# Custom
		$this->template->add_js('/common/js/player_center/player-cashier.js');
		$this->template->add_js('/common/js/player_center/withdraw.js');
		//$this->template->add_js('/common/js/player_center/player-bank-account.js');
		$this->template->add_js('/common/js/plugins/province_city_select.js');
		$this->template->add_js('/resources/js/validator.js');
		$this->template->add_js('/resources/third_party/clipboard/clipboard.min.js');

		# Template-related variables
		$data['sub_nav_active'] = 'withdraw';

		# Render
        $this->template->append_function_title(lang('Withdrawal'));
		$this->template->write_view('main_content', $this->templateName . '/cashier/withdraw/withdraw', $data);
		$this->template->render();
	}

	//sum withdraw_data (totalRequiredBet / totalPlayerBet)
    private function multi_array_sum($arr,$key) {
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

	# Withdrawal form submits to this action
	public function verify() {
		$this->load->model(['playerbankdetails','risk_score_model', 'banktype', 'player_promo', 'transactions', 'users']);

		$playerId = $this->load->get_var('playerId');
		$player = $this->load->get_var('player');
		$this->utils->debug_log('playerId', $playerId, 'player', $player, 'input post', $this->input->post());
		$type = $this->input->post('type');

		if (!in_array($type, self::BANK_TYPE_ARR)) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('withdrawal_bank_type_not_exist')
			));

			return redirect('player_center2/withdraw');
		}

		$redirectPage = 'player_center2/withdraw'.'/'.$type;

		if ($player['enabled_withdrawal'] == self::WITHDRAWAL_DISABLED) {
			$this->utils->debug_log("Withdrawal attempted when withdrawal is disabled.");
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('withdrawal_disabled_message')
			));
			return redirect($redirectPage);
		}

		if ($this->utils->isEnabledFeature("show_allowed_withdrawal_status") && $this->utils->isEnabledFeature("show_risk_score") && $this->utils->isEnabledFeature("show_kyc_status")) {
			if(!$this->risk_score_model->generate_allowed_withdrawal_status($playerId)){
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('not_allowed_kyc_risk_score_message')
				));
				return redirect($redirectPage);
			}
		}

		if(!$this->verifyAndResetDoubleSubmit($playerId)){

			$message = lang('Please refresh and try, and donot allow double submit');
			if( $this->input->is_ajax_request() ){
				// $this->utils->debug_log($message);
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => $message
			));
			redirect($redirectPage);
			return;

		}

		$bankDetailsId = $this->input->post('bankDetailsId');

		if(empty($bankDetailsId)) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('notify.verify_player_bank_details_empty'));
			$this->utils->debug_log("=====================bankDetailsId", $bankDetailsId);
			redirect($redirectPage);
		}else {
			# Record down which id is used, display back the same id
			$this->session->set_flashdata('bankDetailsId', $bankDetailsId);
		}

		# SMS and Password verification is not yet present in new player center. Verify withdraw password only
		$withdrawVerificationMethod = $this->utils->getConfig('withdraw_verification');
		if($withdrawVerificationMethod == 'withdrawal_password'){
			$withdrawal_password = $this->input->post('withdrawal_password');
			$checkWithdrawPassword=$this->player_model->validateWithdrawalPassword($playerId, $withdrawal_password);
			if(!$checkWithdrawPassword){
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('Invalid Withdrawal Password')
				));
				redirect($redirectPage);
			}
		}

		# Validate withdraw limits
		$amount = $this->input->post('amount');
		$this->utils->debug_log("Withdrawal submitted by player [$playerId], amount [$amount], bankDetailsId [$bankDetailsId]");

		// Verify if the submittng bankDetailsId belong to thie player
		$check_if_bank_detail_belnog_to_player = false;
		$available_banks = $this->playerbankdetails->getAvailableWithdrawBankDetail($playerId);
		$this->utils->debug_log("=====================available_banks", $available_banks);
		if($available_banks == NULL) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('notify.verify_player_bank_details_null'));
			redirect($redirectPage);
		}
		foreach ($available_banks as $each_bank) {
			if($each_bank['playerBankDetailsId'] == $bankDetailsId) {
				$check_if_bank_detail_belnog_to_player = true;
				break;
			}
		}

		if(!$check_if_bank_detail_belnog_to_player) {
			$this->utils->debug_log("=====================Verify player bank details error. The bankDetailsId: [$bankDetailsId] is not belong to the playerId: [$playerId].");
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('notify.verify_player_bank_details_binding_error'));
			redirect($redirectPage);
		}

		## Check whether withdrawal satisfies withdrawal rule for this player
        $playerWithdrawalRule = $this->utils->getWithdrawMinMax($playerId);
		list($withdrawalProcessingCount, $processingAmount) = $this->wallet_model->countTodayProcessingWithdraw($playerId);
		list($withdrawalPaidCount, $paidAmount) = $this->transactions->count_today_withdraw($playerId);
		$amount_used = $processingAmount + $paidAmount;
		$time_used = $withdrawalProcessingCount + $withdrawalPaidCount;

		if (!$playerWithdrawalRule || is_null($playerWithdrawalRule['max_withdraw_per_transaction'])) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('Undefined withdraw rule')
			));
			redirect($redirectPage);
		}
		else if ($amount > $playerWithdrawalRule['max_withdraw_per_transaction']) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('Max Withdrawal Per Transaction is').$playerWithdrawalRule['max_withdraw_per_transaction']
			));
			redirect($redirectPage);
		}
		if ($amount + $amount_used > $playerWithdrawalRule['dailyMaxWithdrawalAmount']) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('notify.56')
			));
			redirect($redirectPage);
		}
		if ($time_used >= $playerWithdrawalRule['withdraw_times_limit']) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('notify.106')
			));
			redirect($redirectPage);
		}

		$minWithdraw = $this->operatorglobalsettings->getSettingDoubleValue('min_withdraw');
		if ($amount < $minWithdraw) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('notify.102') . $minWithdraw
			));
			redirect($redirectPage);
		}

		#check  withdrawal conditions
		if ($this->utils->isEnabledFeature('check_withdrawal_conditions')) {
            if(FALSE !== $un_finished = $this->withdraw_condition->getPlayerUnfinishedWithdrawCondition($playerId)){
                //un_finished_withdrawal
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('notify.withdrawal.condition')
				));
                redirect($redirectPage);
            }
		}

		#check  withdrawal conditions -- for each
		if ($this->utils->isEnabledFeature('check_withdrawal_conditions_foreach')) {
			if( FALSE !== $withdraw_data = $this->withdraw_condition->getPlayerUnfinishedWithdrawConditionForeach($playerId)){
                //un_finished
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('notify.withdrawal.condition')
				));
                redirect($redirectPage);
            }


		}

		##check deposit conditions in withdrawal conditions -- for each
        if($this->utils->isEnabledFeature('check_deposit_conditions_foreach_in_withdrawal_conditions')){
            if(FALSE !== $un_finished_deposit = $this->withdraw_condition->getPlayerUnfinishedDepositConditionForeach($playerId)){
                //un_finished_deposit
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('notify.withdrawal.condition')
				));
                redirect($redirectPage);
            }
        }

		## Check whether multiple pending withdraws are allowed
		$this->load->model('group_level');
		if ($this->group_level->isOneWithdrawOnly($playerId)) {
			$this->load->model('player_model');
			if ($this->player_model->countWithdrawByStatusList($playerId) >= 1) {
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('Sorry, your last withdraw request is not done, so you can\'t start new request')
				));
				redirect($redirectPage);
			}
		}

		# All checks done, we can now proceed to submit withdrawal request
		## Save bank info if it's new (no bankDetailsId given)
		if(!$bankDetailsId) {
			$data = array(
				'playerId' => $playerId,
				'bankTypeId' => $this->input->post('bankTypeId'),
				'bankAccountNumber' => $this->input->post('bankAccNum'),
				'bankAccountFullName' => $this->input->post('bankAccName'),
				'bankAddress' => $this->input->post('bankAddress'),
				'province' => $this->input->post('province'),
				'city' => $this->input->post('city'),
				'branch' => $this->input->post('branch'),
				'isRemember' => Playerbankdetails::REMEMBERED,
				'dwBank' => Playerbankdetails::WITHDRAWAL_BANK,
            	'verified' => '1',
				'status' => Playerbankdetails::STATUS_ACTIVE,
				'phone' => $this->input->post('phone'),
			);

	        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_withdraw_account_default_unverified')){
	            $data['verified'] = '0';
	        }

			if(empty($data['bankTypeId'])) {
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('Sorry, please specify receiving account information')
				));
				redirect($redirectPage);
			}
			$this->load->library('player_functions');
			$bankDetailsId = $this->player_functions->addBankDetailsByWithdrawal($data);
			# Again, record down which id is used, display back the same id
			$this->session->set_flashdata('bankDetailsId', $bankDetailsId);
		}

		$this->load->model('wallet_model');
		$playerAccount = $this->player_model->getPlayerAccountByPlayerIdOnly($playerId);
		// $beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);
		$dwIp = $this->utils->getIP();
		$geolocation = $this->utils->getGeoplugin($dwIp);

		# Get bank details
		$playerBankDetails = $this->playerbankdetails->getBankList(array('playerBankDetailsId' => $bankDetailsId))[0];

        $banktype = $this->banktype->getBankTypeById($playerBankDetails['bankTypeId']);
		$bankName = $banktype->bankName;

		//wallet account details
		$walletAccountData = array(
			'playerAccountId' => $playerAccount['playerAccountId'],
			'walletType' => 'Main',
			// 'amount' => $amount,
			'dwMethod' => 1,
			'dwStatus' => 'request',
			'dwDateTime' => $this->utils->getNowForMysql(),
			'transactionType' => 'withdrawal',
			'dwIp' => $dwIp,
			'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
			'transactionCode' => $this->wallet_model->getRandomTransactionCode(),
			'status' => '0',
			//after transfer back balance
			// 'before_balance' => $beforeBalance,
			// 'after_balance' => $beforeBalance - $amount,
			'playerId' => $playerId,
			'player_bank_details_id'=>$bankDetailsId,
			'bankAccountFullName' => $playerBankDetails['bankAccountFullName'],
			'bankAccountNumber' => $playerBankDetails['bankAccountNumber'],
			'bankName' => $bankName,
			'bankAddress' => $playerBankDetails['bankAddress'],
			'bankCity' => $playerBankDetails['city'],
			'bankProvince' => $playerBankDetails['province'],
			'bankBranch' => $playerBankDetails['branch'],
		);

		# OGP-3531
		if($this->utils->isEnabledFeature('enable_withdrawal_pending_review')){
			if($this->checkPlayerIfTagIsUnderPendingWithdrawTag($playerId)){
				$walletAccountData['dwStatus'] = Wallet_model::PENDING_REVIEW_STATUS;
			}

			# OGP-3996 // add withdrawal_pending_review to risk score chart and set 1 = true unset if false in System settings->Risk score setting
			if($this->utils->isEnabledFeature('enable_withdrawal_pending_review_in_risk_score') && $this->utils->isEnabledFeature('show_risk_score')) {
				$risk_score_levels = $this->risk_score_model->getRiskScoreInfo(risk_score_model::RC);
				$this->utils->debug_log("risk_score_levels enable_withdrawal_pending_review_in_risk_score", "enabled");
				if(!empty($risk_score_levels)){
					$this->utils->debug_log("risk_score_levels not empty", true);
					if(isset($risk_score_levels['rules'])){
						$this->utils->debug_log("risk_score_levels rules set", true);
						$rules = json_decode($risk_score_levels['rules'],true);
						if(!empty($rules)) {
							$this->utils->debug_log("risk_score_levels rules not empty", true);
							foreach ($rules as $key => $value) {
								if(isset($value['withdrawal_pending_review'])){
									$this->utils->debug_log("risk_score_levels withdrawal_pending_review set", true);
									if($value['withdrawal_pending_review']){
										$this->utils->debug_log("risk_score_levels withdrawal_pending_review enabled", true);
										$player_risk_score_level = $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
										$this->utils->debug_log("risk_score_levels player_risk_score_level value", $player_risk_score_level);
										$this->utils->debug_log("risk_score_levels risk_score value", $value['risk_score']);
										if($player_risk_score_level == $value['risk_score']){
											$this->utils->debug_log("risk_score_levels withdrawal_pending_review set", true);
											$walletAccountData['dwStatus'] = Wallet_model::PENDING_REVIEW_STATUS;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$this->utils->debug_log("risk_score_levels walletAccountData", $walletAccountData['dwStatus']);

		//transfer back balance by feature
		$rlt= $this->_transferBackToMainWallet($playerId, $player['username']);
		$this->utils->debug_log('after _transferBackToMainWallet, result', $rlt);
		if(!$rlt){

			$msg=lang('Transfer Failed From Sub-wallet');
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => $msg,
			));
			return redirect($redirectPage);
		}

		/*  OGP-13007 Deprecate Promo Rule 7. Withdraw Requirement
		//check promotion to get max limit of withdrawal
		$maxLimit=$this->player_promo->getMaxLimitWithdrawalFromAppliedPromoOnWithdrawal($playerId);
		$changedAmount=false;
		if($maxLimit!=null && $maxLimit>0){
			$adminUserId=Users::SUPER_ADMIN_ID;
			$success = $this->lockAndTransForPlayerBalance($playerId, function ()
				use ($playerId, $maxLimit, $adminUserId, &$changedAmount) {
				$success=true;
				//check balance
				$targetBalance = $this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
				if($this->utils->compareResultFloat($targetBalance, '>', $maxLimit)){
					//try to minus balance
					$minus_balance= $this->utils->roundCurrencyForShow($targetBalance-$maxLimit);
					$note='minus balance to '.$maxLimit.' for promotion';
					$tran=$this->transactions->createDecTransaction($playerId, $minus_balance, $adminUserId, $note);
					$success=!empty($tran);
					if($success){
						$changedAmount=true;
					}
				}

				return $success;
			});

			$this->utils->debug_log('check withdrawal max limit', $success);

			if(!$success){
				$this->session->set_flashdata('result', array(
					'success' => false,
					'message' => lang('Try Apply Max Limit Withdrawal Rule of Promotion is Failed')
				));
				return redirect($redirectPage);
			}
		}
		if($changedAmount){
			//reset back
			$amount = $this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
		}
		*/

        $walletAccountData['amount']=$amount;
		$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);//$this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
		$walletAccountData['before_balance']=$beforeBalance;
		$walletAccountData['after_balance']=$beforeBalance - $amount;

        #cryptocurrency withdrawal
        if($this->utils->isCryptoCurrency($banktype)){
        	$cryptocurrency  = $this->utils->getCryptoCurrency($banktype);
        	$defaultCurrency = $this->utils->getCurrentCurrency()['currency_code'];

            $custom_withdrawal_fee = $this->config->item('custom_withdrawal_fee') ? $this->config->item('custom_withdrawal_fee') : 0;
            $fee = $amount * $custom_withdrawal_fee;
            $withdraw_amount = $amount - $fee;
            list($crypto, $rate) = $this->utils->convertCryptoCurrency($withdraw_amount, $defaultCurrency, $cryptocurrency);

            $custom_withdrawal_rate = $this->config->item('custom_withdrawal_rate') ? $this->config->item('custom_withdrawal_rate') : 1;
            $crypto      = number_format($crypto * $custom_withdrawal_rate, 8, '.', '');
            $player_rate = number_format($rate   * $custom_withdrawal_rate, 8, '.', '');

            $withdrawal_notes = 'Fee: $'.$fee.' | '.$cryptocurrency.': '.$crypto.' | Crypto Real Rate: '.$rate.' | Custom Withdrawal Rate: '.$custom_withdrawal_rate;
            $walletAccountData['notes'] = $withdrawal_notes;
            $walletAccountData['transaction_fee'] = $fee;
            $walletAccountData['extra_info'] = $crypto;
            $this->utils->debug_log('=======================cryptocurrency withdrawal_notes', $withdrawal_notes);
        }

		$this->load->model(array('wallet_model', 'transaction_notes', 'users', 'walletaccount_timelog', 'walletaccount_notes'));
		$walletModel = $this->wallet_model;
		$hasSufficientBalance = false;
		$errorMsg = '';
		$cryptoSet = isset($crypto) ? $crypto : false;
		$playerRateSet = isset($player_rate) ? $player_rate : false;
		$fee = isset($fee) ? $fee : false;
		if(($cryptoSet != false) && ($playerRateSet != false)) {
			$walletAccountData['showNotesFlag'] = '1';
		}

		$success = $this->lockAndTransForPlayerBalance($playerId, function () use ($walletModel, $playerId, $walletAccountData, $fee, $cryptoSet, $playerRateSet, &$walletAccountId, &$hasSufficientBalance, &$errorMsg) {
			## Check main wallet (id = 0) balance
			$hasSufficientBalance = $this->utils->checkTargetBalance($playerId, 0, $walletAccountData['amount'], $errorMsg);
			if($hasSufficientBalance) {
				$localBankWithdrawalDetails = array(
					'withdrawalAmount' => $walletAccountData['amount'],
					'playerBankDetailsId' => $walletAccountData['player_bank_details_id'],
					'depositDateTime' => $walletAccountData['dwDateTime'],
					'status' => 'active',
				);
				$walletAccountId = $walletModel->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
				$withdrawalActionLogByPlayer = sprintf("Withdrawal is success processing, status => request");
				$walletModel->addWalletaccountNotes($walletAccountId, $playerId, $withdrawalActionLogByPlayer, 'request', null, Walletaccount_timelog::PLAYER_USER);

				if(!empty($walletAccountId) && ($cryptoSet != false) && ($playerRateSet != false) && ($fee != false)) {
					$adminUserId = $this->users->getSuperAdminId();
					$note = sprintf(lang('Fee is: $%s. Will transfer: %s (Rate: %s)'), $fee, $cryptoSet, $playerRateSet);
					$lastTransactionNotesId = $this->walletaccount_notes->add($note, $adminUserId, Walletaccount_notes::ACTION_LOG, $walletAccountId);
				}
			}

			return !empty($walletAccountId);
		});

		$this->utils->debug_log("Withdrawal submitted by player [$playerId] evaluated, amount [$amount], hasSufficientBalance [$hasSufficientBalance], walletAccountId [$walletAccountId], errorMsg [$errorMsg]");

		if(!$hasSufficientBalance) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => empty($errorMsg) ? lang('notify.55') : $errorMsg
			));
			redirect($redirectPage);
		}

		$this->saveHttpRequest($playerId, Http_request::TYPE_WITHDRAWAL);
		if(!$success) {
			$this->session->set_flashdata('result', array(
				'success' => false,
				'message' => lang('error.withdrawal_failed')
			));
			redirect($redirectPage);
		}

		$withdrawSuccessMsg = lang($this->config->item('playercenter.withdrawMsg.success') ?: 'notify.58');

		if(isset($crypto)){
			$withdrawSuccessMsg = $withdrawSuccessMsg.' ['.sprintf(lang('Fee is: $%s. Will transfer: %s (Rate: %s)'), $fee, $crypto, $player_rate).']';
		}

		$this->session->set_flashdata('result', array(
			'success' => true,
			'message' => $withdrawSuccessMsg." <!-- walletAccountId: [$walletAccountId] -->"
		));

		# send prompt message when withdrawal request is successful
		if ($this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_request')) {

			$this->load->model(['cms_model', 'queue_result']);
			$this->load->library(["lib_queue", "sms/sms_sender"]);

			$mobileNum  = $player['contactNumber'];
			$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_REQUEST);
			$mobileNumIsVeridied = $player['verified_phone'];
			$isUseQueueToSend    = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
			$callerType = Queue_result::CALLER_TYPE_ADMIN;
			$caller = $playerId;
			$state  = null;

			if ($mobileNumIsVeridied && $isUseQueueToSend) {
				$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
			} else if ($mobileNumIsVeridied) {
				$this->sms_sender->send($mobileNum, $smsContent);
			}
		}

		redirect($redirectPage);
	}
}
