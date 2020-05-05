<?php

/**
 * Class promorules_management_module
 *
 * General behaviors include
 *
 * * Add/update/delete promo rules
 * * Activate/deactivate promo rules
 * * Export report to excel
 *
 * @category Marketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
*/
trait promorules_management_module {

	/**
	 * overview : check if promo code exist
	 *
	 * @param $promoCode
	 * @return	rendered template
	 */
	public function isPromoCodeExists($promoCode) {
		// echo json_encode($this->depositpromo_manager->isPromoCodeExists($promoCode));
		$this->returnJsonResult($this->depositpromo_manager->isPromoCodeExists($promoCode));
	}

	/**
	 * overview : check if promo name exist
	 *
	 * @param $promoName
	 * @return	rendered template
	 */
	public function isPromoNameExists($promoName) {
		// echo json_encode($this->depositpromo_manager->isPromoNameExists($promoName));
		$this->returnJsonResult($this->depositpromo_manager->isPromoNameExists($promoName));
	}

	/**
	 * overview : add new promo
	 *
	 *  @return	rendered template
	 */
	public function addNewPromo() {

		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->add_css('resources/css/collapse-style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-javascript.js');
        $this->template->add_js('resources/js/ace/theme-tomorrow.js');
        $this->template->add_js('resources/js/ace-helper.js');
		$this->template->add_js('resources/js/jquery-checktree.js');
		$this->template->add_js('resources/js/select2.min.js');
		$this->template->add_css('resources/css/select2.min.css');

		$this->load->model(array('promorules', 'group_level', 'affiliatemodel', 'agency_model'));

		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$data['subWallets']= $this->utils->getActiveGameSystemList();

		// $data['gameProvider'] = $this->depositpromo_manager->getGameProvider();
		// $data['ptGameType'] = $this->depositpromo_manager->getGameType(PT_API);
		// $data['agGameType'] = $this->depositpromo_manager->getGameType(AG_API);
		// $data['mgGameType'] = $this->depositpromo_manager->getGameType(MG_API);
		// $data['ntGameType'] = $this->depositpromo_manager->getGameType(NT_API);
		// $data['ptGames'] = $this->depositpromo_manager->getPlatformGames(PT_API);
		// $data['agGames'] = $this->depositpromo_manager->getPlatformGames(AG_API);
		// $data['mgGames'] = $this->depositpromo_manager->getPlatformGames(MG_API);
		// $data['ntGames'] = $this->depositpromo_manager->getPlatformGames(NT_API);
		$data['playerLvl'] = $this->depositpromo_manager->getAllPlayerLevels();
		$affiliates=$this->affiliatemodel->getAllActivtedAffiliates(false, true);
		$data['affiliates'] =$affiliates ? array_column($affiliates, 'username', 'affiliateId') : null;
		$agents=$this->agency_model->get_active_agents(false, true);
		$data['agents'] = $agents ? array_column($agents, 'agent_name', 'agent_id') : null;
		$data['promoType'] = $this->promorules->getPromoType();
		// $data['showGameTree'] = $this->config->item('show_particular_game_in_tree');
		$data['promoRuleDetails'] = array(
			'promorulesId' => "",
			'promoName' => "",
			'promoCategory' => "",
			'applicationPeriodStart' => "",
			'applicationPeriodEnd' => "",
			'promoCode' => "",
			'promoDesc' => "",
			'promoType' => 0,
			// 'depositConditionType' => "",
			// 'depositConditionDepositAmount' => "",
			'depositConditionNonFixedDepositAmount' => "0", //default is AMOUNT
			// 'nonfixedDepositAmtCondition' => "",
			// 'nonfixedDepositAmtConditionRequiredDepositAmount' => "",
			'nonfixedDepositMinAmount' => '',
			'nonfixedDepositMaxAmount' => '',

			// 'bonusApplication' => "",
			'depositSuccesionType' => "",
			'depositSuccesionCnt' => "",
			'depositSuccesionPeriod' => "",
			// 'bonusApplicationRule' => "",
			'bonusApplicationLimitRule' => "1",
			'bonusApplicationLimitRuleCnt' => "1", // default once
			'bonusApplicationLimitDateType' => "0",
			// 'repeatConditionBetCnt' => "",
			'bonusReleaseRule' => "",
			'bonusReleaseToPlayer' => "",
			'releaseToSubWallet' => 0, //default is main wallet
			'bonusAmount' => "",
			'depositPercentage' => "",
			'maxBonusAmount' => "",
			'max_bonus_by_limit_date_type'=> '0',
			// 'withdrawRequirementRule' => "",
			'withdrawRequirementConditionType' => "1",
			'withdrawRequirementBetAmount' => "",
			'withdrawRequirementBetCntCondition' => "",
			'withdrawShouldMinusDeposit' => "0",
            'withdrawRequirementDepositConditionType' => '0', //default value
            'withdrawRequirementDepositAmount' => '0',
            'transferRequirementConditionType' => "0",
            'transferRequirementBetAmount' => "",
            'transferRequirementBetCntCondition' => "",
            'transferShouldMinusDeposit' => "0",
			'nonDepositPromoType' => "",
			'gameRequiredBet' => "",
			'gameRecordStartDate' => "",
			'gameRecordEndDate' => "",
			'hide_date' => "",
			'trigger_wallets'=>'',
			'release_to_same_sub_wallet'=>'0',
			'add_withdraw_condition_as_bonus_condition'=>'0',
			'donot_allow_other_promotion'=>'0',
			'donot_allow_any_withdrawals_after_deposit'=>'1',
            'donot_allow_any_despoits_after_deposit'=>'1',
            'donot_allow_any_available_bet_after_deposit'=>'0',
            'donot_allow_any_transfer_after_deposit'=>'0',
            'donot_allow_any_transfer_in_after_transfer'=>'1',
            'donot_allow_any_transfer_out_after_transfer'=>'1',
            //'expire_days'=>'0',
			'disabled_pre_application'=>'1',
            //'show_on_active_available'=>'1',
			'disable_cashback_if_not_finish_withdraw_condition'=>'1',
			// 'disabled_pre_application'=>'1',
			'hide_if_not_allow'=>'0',
            'allowed_scope_condition'=>'0',
			'always_join_promotion'=>'0',
			'affiliates' => array(),
			'agents' => array(),
			'players' => array(),
			'formula' => '{"bonus_release":"","withdraw_condition":"","transfer_condition":"", "bonus_condition":""}',
			'enable_edit' => '0',
			//'withdrawal_max_limit'=>null,
			//'ignore_withdrawal_max_limit_after_first_deposit'=>'0',
			//'always_apply_withdrawal_max_limit_when_first_deposit'=>'0',
            'approved_limit' => 0,
            'total_approved_limit' => 0,
			'dont_allow_request_promo_from_same_ips'=>'0',
		);

		// $data['promoRuleGames'] = $this->depositpromo_manager->getAllGames();
		// $data['promoRuleGamesType'] = $this->depositpromo_manager->getAllGameType();
		// $data['promoRuleGamesProvider'] = $this->depositpromo_manager->getAllGameProvider();
		$data['promoRuleLevels'] = $this->depositpromo_manager->getAllPromoRuleLevels();
		$this->template->write_view('main_content', 'marketing_management/promorules/view_promo_form', $data);
		$this->template->render();
	}

	/**
	 * overview : update promo rule
	 *
	 * @param $promoruleId
	 *  @return	rendered template
	 */
	public function editPromoRule($promoruleId) {
		$this->loadTemplate('Marketing Management', '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->add_css('resources/css/collapse-style.css');
		$this->template->add_css('resources/css/jquery-checktree.css');
		// $this->template->add_js('resources/js/highlight.pack.js');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-php.js');
		$this->template->add_js('resources/js/ace/theme-tomorrow.js');
        $this->template->add_js('resources/js/ace-helper.js');
		$this->template->add_js('resources/js/jquery-checktree.js');
		// $this->template->add_css('resources/css/hljs.tomorrow.css');
		$this->template->add_js('resources/js/select2.min.js');
		$this->template->add_css('resources/css/select2.min.css');

		if (!$this->permissions->checkPermissions('export_report')) {
			$data['export_report_permission'] = FALSE;
		} else {
			$data['export_report_permission'] = TRUE;
		}

		$this->load->model(array('promorules', 'group_level', 'affiliatemodel', 'agency_model', 'promo_games'));

		$data['subWallets']= $this->utils->getActiveGameSystemList();

		$data['promoRuleDetails'] = $this->promorules->viewPromoRuleDetails($promoruleId);

		$data['selectedGameDescs'] = $this->promorules->getPromoRuleGames($promoruleId);
		$data['selectedGamesTypes'] = $this->promorules->getPromoRuleGamesType($promoruleId);
		$data['selectedGamePlatforms'] = $this->promorules->getPromoRuleGamesProvider($promoruleId);

		// $data['promoRuleGames'] = $this->depositpromo_manager->getPromoRuleGames($promoruleId);
		// $data['promoRuleGamesType'] = $this->depositpromo_manager->getPromoRuleGamesType($promoruleId);
		// $data['promoRuleGamesProvider'] = $this->depositpromo_manager->getPromoRuleGamesProvider($promoruleId);
		$data['promoRuleSelectedGroup'] = $this->group_level->getSelectedPromoRuleGroup($promoruleId);
		$data['promoRuleSelectedPlayerLevels'] = $this->promorules->getPromoRuleLevels($promoruleId);
		// $data['gameProvider'] = $this->depositpromo_manager->getGameProvider();

		$data['playerLvl'] = $this->group_level->getAllPlayerLevelsForSelect();
		$affiliates=$this->affiliatemodel->getAllActivtedAffiliates(false, true);
		$data['affiliates'] = $affiliates ? array_column($affiliates, 'username', 'affiliateId') : null;
		$agents=$this->agency_model->get_active_agents(false, true);
		$data['agents'] = $agents ? array_column($agents, 'agent_name', 'agent_id') : null;
		$data['promoType'] = $this->promorules->getPromoType();
		$data['promo_game'] = $this->promo_games->get_promorule_game($promoruleId);

		// $data['showGameTree'] = $this->config->item('show_particular_game_in_tree');

		$this->template->write_view('main_content', 'marketing_management/promorules/view_promo_form', $data);
		$this->template->render();
	}

	/**
	 * overview : update promo rule
	 *
	 *  @return	rendered template
	 */
	public function preparePromo() {
		$this->utils->debug_log('===========preparePromo input post', $this->input->post());
		//check promo period end date
		// if ($this->input->post("promoPeriodEndCbxVal") == 'true') {
		// 	$promoPeriodEndCbx = self::STATUS_ACTIVE;
		// } else {
		// 	$promoPeriodEndCbx = self::STATUS_INACTIVE;
		// }
		$this->load->model(array('affiliate', 'player_model', 'promo_games'));

		$promorulesId = $this->input->post("promorulesId");
		$playerLevels = $this->input->post('player_lvl') ?: array();

		$affiliates = $this->input->post('affiliates') ?: array();
		$agents = $this->input->post('agents') ?: array();
		$players = $this->input->post('players') ?: array();

		$csv_affiliates = $csv_agents = $csv_players = array();

		//get the content of the csv file for affiliate
		if( $_FILES['csv_affiliates']['name'] != "" ){

			$tmpName = $_FILES['csv_affiliates']['tmp_name'];
			$csv_affiliates = array_map('str_getcsv', file($tmpName));

			$csvAffiliateIds = array();

			foreach ($csv_affiliates as $key => $value) {

				$username = $value[0];
				$rec = $this->affiliate->checkUsernameIfExist($username);

				if( empty( $rec ) ) continue;

				$affiliateId = $rec[0]->id;

				if( in_array($affiliateId, $affiliates) ) continue;

				array_push($affiliates, $affiliateId);

			}

		}
		//end

		//get the content of the csv file for agent
		if( $_FILES['csv_agents']['name'] != "" ){

			$tmpName = $_FILES['csv_agents']['tmp_name'];
			$csv_agents = array_map('str_getcsv', file($tmpName));

			$csvAgentIds = array();

			foreach ($csv_agents as $key => $value) {

				$username = $value[0];
				$rec = $this->agency_model->get_agent_by_name($username);

				if( empty( $rec ) ) continue;

				$agentId = $rec['agent_id'];

				if( in_array($agentId, $agents) ) continue;

				array_push($agents, $agentId);

			}

		}
		//end

		//get the content of the csv file for players
		if( $_FILES['csv_players']['name'] != "" ){

			$tmpName = $_FILES['csv_players']['tmp_name'];
			$csv_players = array_map('str_getcsv', file($tmpName));

			$csvPlayerIds = array();

			foreach ($csv_players as $key => $value) {

				$username = $value[0];
				$playerId = $this->player_model->getPlayerIdByUsername($username);

				if( empty( $playerId ) ) continue;

				if( in_array($playerId, $players) ) continue;

				array_push($players, $playerId);

			}

		}
		//end

		// $this->utils->debug_log('player_lvl', $playerLevels, $this->input->post("depositPromoGameType"));
		if (!$playerLevels) {
			//$message = lang('con.d15');
			//$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			if (!empty($promorulesId)) {
				//redirect('marketing_management/editPromoRule/' . $promorulesId);
			} else {
				//redirect('marketing_management/addNewPromo');
			}
			// redirect('marketing_management/promoRuleManager');
		}

		$percentage = 0;
		if ($this->input->post("depositPercentage")) {
			$percentage = $this->input->post("depositPercentage");
			if(!is_numeric($percentage)){
                $message = 'Bonus Release Deposit Percentage 格式错误，必須為数字';
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, $message);
				redirect('marketing_management/editPromoRule/' . $promorulesId);
			}else {
				$percentage = round($percentage ,2);
				if($percentage == '0.00') {
					$percentage = '0.01';
					$message = 'Bonus Release Deposit Percentage 数字必須大于 0.00';
					$this->alertMessage(self::MESSAGE_TYPE_WARNING, $message);
					redirect('marketing_management/editPromoRule/' . $promorulesId);
				}
			}
		}

		if ($this->input->post("bonusReleaseBonusPercentage")) {
			$percentage = $this->input->post("bonusReleaseBonusPercentage");
		}

		$this->load->model(array('promorules'));

		$bonusReleaseToPlayerOption = $this->input->post("bonusReleaseToPlayerOption");
		if (empty($bonusReleaseToPlayerOption)) {
			$bonusReleaseToPlayerOption = Promorules::BONUS_RELEASE_TO_PLAYER_AUTO;
		}
		$releaseToSubWallet= $this->input->post('releaseToSubWallet');
		if (empty($releaseToSubWallet)) {
			$releaseToSubWallet = 0; // default main wallet
		}

		$formula = array('bonus_release' => null, 'withdraw_condition' => null, 'bonus_condition' => $this->input->post('formula_bonus_condition'));
		$bonusReleaseTypeOption = $this->input->post("bonusReleaseTypeOption");
		//get formula
		if ($bonusReleaseTypeOption == Promorules::BONUS_RELEASE_RULE_CUSTOM) {
			$formula['bonus_release'] = $this->input->post('formula_bonus_release');
		}
		$withdrawRequirementBettingConditionOption = $this->input->post("withdrawRequirementBettingConditionOption");
		if ($withdrawRequirementBettingConditionOption == Promorules::WITHDRAW_CONDITION_TYPE_CUSTOM) {
			$formula['withdraw_condition'] = $this->input->post('formula_withdraw_condition');
		}
        $transferRequirementBettingConditionOption = 0;
        $transferRequirementBettingConditionOption = $this->input->post("transferRequirementBettingConditionOption");
        if ($transferRequirementBettingConditionOption == Promorules::TRANSFER_CONDITION_TYPE_CUSTOM) {
            $formula['transfer_condition'] = $this->input->post('formula_transfer_condition');
        }

		//$withdrawRequirementBetCntCondition
        $withdrawRequirementBetCntCondition = 0;
		if ($withdrawRequirementBettingConditionOption == Promorules::WITHDRAW_CONDITION_TYPE_BONUS_TIMES) {
			$withdrawRequirementBetCntCondition = $this->input->post("withdrawReqBonusTimes");
		}
		if ($withdrawRequirementBettingConditionOption == Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES) {
			$withdrawRequirementBetCntCondition = $this->input->post('withdrawReqBettingTimes');
		}
        if ($withdrawRequirementBettingConditionOption == Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES_CHECK_WITH_MAX_BONUS) {
            $withdrawRequirementBetCntCondition = $this->input->post('withdrawReqBettingTimesCheckWithMaxBonus');
        }

		$withdrawRequirementDepositConditionOption = $this->input->post('withdrawRequirementDepositConditionOption');
        $withdrawRequirementDepositAmount = 0;
		if($withdrawRequirementDepositConditionOption == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT){
            $withdrawRequirementDepositAmount = $this->input->post('withdrawReqDepMinLimit');
        }
        if($withdrawRequirementDepositConditionOption == Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION){
            $withdrawRequirementDepositAmount = $this->input->post('withdrawReqDepMinLimitSinceRegistration');
        }

        $transferConditionDisallowTransferInWallet = null;
        $transferConditionDisallowTransferOutWallet = null;
        $transferRequirementWalletsInfo = null;

        if($this->input->post('transfer_condition_disallow_transfer_in_wallet')){
            $transferConditionDisallowTransferInWallet = ['wallet_id' => $this->input->post('transfer_condition_disallow_transfer_in_wallet')];
        }

        if($this->input->post('transfer_condition_disallow_transfer_out_wallet')){
            $transferConditionDisallowTransferOutWallet = ['wallet_id' => $this->input->post('transfer_condition_disallow_transfer_out_wallet')];
        }

        $transferRequirementWalletsInfo = [
            'disallow_transfer_in_wallets' => $transferConditionDisallowTransferInWallet,
            'disallow_transfer_out_wallets' => $transferConditionDisallowTransferOutWallet
        ];



        $transferRequirementBetCntCondition = 0;
        if ($transferRequirementBettingConditionOption == Promorules::TRANSFER_CONDITION_TYPE_BONUS_TIMES) {
            $transferRequirementBetCntCondition = $this->input->post('transferReqBonusTimes');
        }
        if ($transferRequirementBettingConditionOption == Promorules::TRANSFER_CONDITION_TYPE_BETTING_TIMES) {
            $transferRequirementBetCntCondition = $this->input->post('transferReqBettingTimes');
        }

		/*
		 * Git Issue: #156 (start)
		 * Modified by: Asrii
		 * Modification details: change "trigger on transfer to subwallet" to a multiselect
		 * Modified Datetime: 2016-10-25 02:25:00
		 */
		$triggerWallets = null;
		if($this->input->post("trigger_on_transfer_to_subwallet")){
			$triggerGameIds = $this->input->post("trigger_on_transfer_to_subwallet");
			$triggerWallets = implode(',', $triggerGameIds);
		}

		/*
		 * Git Issue: #156 (end)
		 */

		$promoruledata = array(
			'promoName' => $this->input->post("promoName"),
			'promoCategory' => $this->input->post("promoCategory"),
			'applicationPeriodStart' => $this->input->post("appStartDate"),
			'applicationPeriodEnd' => $this->input->post("appEndDate"),
			'hide_date' => $this->input->post("hideDate"),
			'noEndDateFlag' => Promorules::NO_END_DATE_FLAG_TRUE,
			'promoCode' => $this->input->post("promoCode"),
			'promoDesc' => $this->input->post("promoDesc"),

			'promoType' => $this->input->post("promoType"),
			// 'depositConditionType' => $this->input->post("depositConditionType"), //"0=FIXED DEPOSIT AMOUNT,1=NON-FIXED DEPOSIT AMOUNT"
			// 'depositConditionDepositAmount' => $this->input->post("fixedDepositAmt"),
			'depositConditionNonFixedDepositAmount' => $this->input->post("depositConditionTypeOption"), //"0=DEPOSIT <= OR >= AMOUNT,1=ANY AMOUNT"
			// 'nonfixedDepositAmtCondition' => $this->input->post("nonfixedDepositAmtCondition"), //"0=LESSTHAN UQUAL DEPOSIT AMOUNT,1=GREATERTHAN EQUAL DEPOSIT AMOUNT"
			// 'nonfixedDepositAmtConditionRequiredDepositAmount' => $this->input->post("fixedDepositAmtConditionAmount"), //"IF NON FIXED DEP AMOUNT SELECTED, MUST ENTER REQUIRED AMOUNT"
			'nonfixedDepositMinAmount' => $this->input->post("nonfixedDepositMinAmount"),
			'nonfixedDepositMaxAmount' => $this->input->post("nonfixedDepositMaxAmount"),

			// 'bonusApplication' => $this->input->post("depositSuccessionOption"), //DEPOSIT SUCCESSION AND APPLICATION
			'depositSuccesionType' => $this->input->post("bonusReleaseTypeOptionBySuccession"), //1ST,2ND,3RD OR OTHERS
			'depositSuccesionCnt' => $this->input->post("depositCnt"), //1ST,2ND,3RD OR OTHERS
			'depositSuccesionPeriod' => $this->input->post("depositSuccessionPeriodOption"), //1-STARTING FROM REG, 4-BONUS EXPIRE

			// 'bonusApplicationRule' => $this->input->post("bonusReleaseTypeByNonSuccessionRepeatOption"), //REPEAT OR NOREPEAT
			'bonusApplicationLimitRule' => $this->input->post("bonusReleaseTypeOptionByNonSuccessionLimitOption"), //NO LIMIT OR WITH LIMIT
			'bonusApplicationLimitRuleCnt' => $this->input->post("limitCnt"), //LIMIT OF APPICATION
			// 'repeatConditionBetCnt' => $this->input->post("bettingCnt"), //IN ORDER TO REPEAT YOU MUST ENTER BET CNT
			'bonusApplicationLimitDateType' => $this->input->post("bonusApplicationLimitDateType"),
			'trigger_wallets' => $triggerWallets,
			'release_to_same_sub_wallet'=> $this->input->post("release_to_same_sub_wallet")=='true' ? '1' : '0',
			'add_withdraw_condition_as_bonus_condition' => $this->input->post("add_withdraw_condition_as_bonus_condition")=='true',
			'donot_allow_other_promotion'=>$this->input->post("donot_allow_other_promotion")=='true' ? '1' : '0',
            'donot_allow_any_withdrawals_after_deposit'=>$this->input->post("donot_allow_any_withdrawals_after_deposit")=='true' ? '1' : '0',
            'donot_allow_any_despoits_after_deposit'=>$this->input->post("donot_allow_any_despoits_after_deposit")=='true' ? '1' : '0',
            'donot_allow_any_available_bet_after_deposit'=>$this->input->post("donot_allow_any_available_bet_after_deposit")=='true' ? '1' : '0',
            'donot_allow_any_transfer_after_deposit'=>$this->input->post("donot_allow_any_transfer_after_deposit")=='true' ? '1' : '0',
            'donot_allow_any_transfer_in_after_transfer'=>$this->input->post("donot_allow_any_transfer_in_after_transfer")=='true' ? '1' : '0',
            'donot_allow_any_transfer_out_after_transfer'=>$this->input->post("donot_allow_any_transfer_out_after_transfer")=='true' ? '1' : '0',
            //'expire_days'=>$this->input->post("expire_days"),
			'disabled_pre_application'=>$this->input->post("disabled_pre_application")=='true' ? '1' : '0',
            //'show_on_active_available'=>$this->input->post("show_on_active_available")=='true' ? '1' : '0',
			'disable_cashback_if_not_finish_withdraw_condition'=>$this->input->post("disable_cashback_if_not_finish_withdraw_condition")=='true' ? '1' : '0',
			// 'disabled_pre_application'=>$this->input->post("disabled_pre_application")=='true' ? '1' : '0',
			'hide_if_not_allow'=>$this->input->post("hide_if_not_allow")=='true' ? '1' : '0',
			'allowed_scope_condition'=>$this->input->post("allowed_scope_condition")=='true' ? '1' : '0',
			'always_join_promotion'=>$this->input->post("always_join_promotion")=='true' ? '1' : '0',

			'bonusReleaseRule' => $bonusReleaseTypeOption, //"0=BY FIXED BONUS AMOUNT,1=BY DEPOSIT PERCENTAGE,2=BY BET PERCENTAGE", 3=custom
			'bonusReleaseToPlayer' => $bonusReleaseToPlayerOption, //"0=AUTO,1=MANUAL"
			'releaseToSubWallet' => $releaseToSubWallet, //0=main
			'bonusAmount' => $this->input->post("bonusReleaseBonusAmount"),
			'depositPercentage' => $percentage, // can be by deposit percentage or bet percentage
			'maxBonusAmount' => $this->input->post("bonusReleaseMaxBonusAmount"),
			'max_bonus_by_limit_date_type' => $this->input->post('max_bonus_by_limit_date_type')=='true' ? '1' : '0',

			// 'withdrawRequirementRule' => $this->input->post("withdrawRequirementTypeOption"), //"0=BY BETTING AMOUNT,1=NON BETTING AMOUNT"
			'withdrawRequirementConditionType' => $withdrawRequirementBettingConditionOption, //"0=(>=) OF BET AMOUNT,1=(DEPOSIT AMOUNT + BONUS) X NUMBER OF BETTING TIMES"
			'withdrawRequirementBetAmount' => $this->input->post("withdrawReqBetAmount"),
			'withdrawRequirementBetCntCondition' => $withdrawRequirementBetCntCondition, //"DEFINE IF WITHDRAW CONDITION TYPE IS (DEPOSIT AMOUNT + BONUS) X NUMBER OF BETTING TIMES"
			'withdrawShouldMinusDeposit' => $this->input->post("withdrawShouldMinusDeposit")=='true' ? '1' : '0',

            'withdrawRequirementDepositConditionType' => $withdrawRequirementDepositConditionOption,
            'withdrawRequirementDepositAmount' => $withdrawRequirementDepositAmount,

            'transferRequirementWalletsInfo' => json_encode($transferRequirementWalletsInfo),
            'transferRequirementConditionType' => $transferRequirementBettingConditionOption,
            'transferRequirementBetCntCondition' => $transferRequirementBetCntCondition,
            'transferRequirementBetAmount' => $this->input->post("transferReqBetAmount"),
            'transferShouldMinusDeposit' => $this->input->post("transferShouldMinusDeposit")=='true' ? '1' : '0',

            //'withdrawal_max_limit'=>$this->input->post('withdrawal_max_limit'),
            //'ignore_withdrawal_max_limit_after_first_deposit'=>$this->input->post("ignore_withdrawal_max_limit_after_first_deposit")=='true' ? '1' : '0',
            //'always_apply_withdrawal_max_limit_when_first_deposit'=>$this->input->post("always_apply_withdrawal_max_limit_when_first_deposit")=='true' ? '1' : '0',

            'formula' => json_encode($formula),
            'nonDepositPromoType' => $this->input->post("nonDepositOption"), //"DEFINE IF NON-DEPOSIT CONDITION IS SELECTED, 0=BY EMAIL,1=BY MOBILE,2=BY REGISTRATION ACCT,3=BY COMPLETE REGISTRATION,4=BY BETTING,5=BY LOSS,6=BY WINNING"
            'gameRequiredBet' => $this->input->post("gameRequiredBet"),
            'gameRecordStartDate' => $this->input->post("gameRecordStartDate"),
            'gameRecordEndDate' => $this->input->post("gameRecordEndDate"),
            'createdBy' => $this->authentication->getUserId(),
            'status' => Promorules::OLD_STATUS_ACTIVE,
            'request_limit' => $this->input->post('request_limit'),
            'approved_limit' => $this->input->post('approved_limit'),
            'total_approved_limit' => $this->input->post('total_approved_limit'),
            'language' => $this->input->post('language'),

            'dont_allow_request_promo_from_same_ips' => $this->input->post("dont_allow_request_promo_from_same_ips")=='true' ? '1' : '0',
        );

		if ($promorulesId) {
			// $promorulesId = $this->input->post("promorulesId");
			$promoruledata['promorulesId'] = $promorulesId;
			$promoruledata['updatedBy'] = $this->authentication->getUserId();
			$promoruledata['updatedOn'] = $this->utils->getNowForMysql();

			$this->promorules->editPromoRules($promoruledata);

			//clear promorule items
			$this->promorules->clearPromoItems($promorulesId);

			//promo rules has successfully updated
			$message = lang('con.d14');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			$this->saveAction(self::MANAGEMENT_TITLE, 'Edit Promo Rule', "User " . $this->authentication->getUsername() . " has successfully edit promo rule with id: " . $promorulesId);
		} else {
			$promoruledata['createdOn'] = $this->utils->getNowForMysql();
			$promorulesId = $this->promorules->addPromoRules($promoruledata);

			if($promorulesId){
                $message = lang('con.d13');
                $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
            }else{
                $message = lang('con.d16');
                $this->alertMessage(self::MESSAGE_TYPE_WARNING, $message);
            }
			$this->saveAction(self::MANAGEMENT_TITLE, 'Added Promo Rule', "User " . $this->authentication->getUsername() . " has successfully added promo rule with id: " . $promorulesId);
		}

		$this->utils->debug_log('preparePromo', 'bonusReleaseTypeOption', $bonusReleaseTypeOption, 'is bonus game?', $bonusReleaseTypeOption == Promorules::BONUS_RELEASE_RULE_BONUS_GAME);

		// OGP-3381, relationships with promo games
		// Create/update
		if ($bonusReleaseTypeOption == Promorules::BONUS_RELEASE_RULE_BONUS_GAME) {
			$entry = [
				'promorule_id'	=> $promorulesId ,
				'game_id'		=> $this->input->post('bg_game_id') ,
				'play_rounds'	=> $this->input->post('bg_play_rounds') ,
				'budget_cash'	=> empty($this->input->post('bg_budget_cash_enable')) ? 0 : $this->input->post('bg_budget_cash') ,
				'budget_vipexp'	=> empty($this->input->post('bg_budget_vipexp_enable')) ? 0 : $this->input->post('bg_budget_vipexp')
			];
			$this->utils->debug_log('preparePromo', 'promo game entry', $entry);

			$this->promo_games->create_or_update_promorule_game($entry);
		}
		// Remove
		if ($bonusReleaseTypeOption != Promorules::BONUS_RELEASE_RULE_BONUS_GAME && !empty($promorulesId)) {
			$this->promo_games->remove_promorule_game($promorulesId);
		}

		$showGameTree = $this->config->item('show_particular_game_in_tree');
		$this->load->model('game_description_model');
		$promoType = $this->input->post("promoType");
		$nonDepositOption = $this->input->post("nonDepositOption");

		$this->load->model('promorules');
		$gamesAptList = $this->loadSubmitGameTreeWithNumber($showGameTree);
		if (!empty($gamesAptList)) {
			$this->promorules->batchAddAllowedGames($promorulesId, $gamesAptList);
		}


		$allowedPlayerLevelIds = $this->input->post('allowed_player_level');

		$idsArray = explode(',', $allowedPlayerLevelIds);
		foreach($idsArray as $item) {
			$arr = explode('_', $item);
			if(sizeof($arr) == 4) {
				$playerLevelIds[] = $arr['3'];
			}
		}
		if(!empty($playerLevelIds)) {
			$this->promorules->batchAddAllowedPlayer($promorulesId, $playerLevelIds);
		}

		//$this->promorules->replaceApplicablePlayerLevels($promorulesId, $playerLevels);
		$this->promorules->replaceApplicableAffiliates($promorulesId, $affiliates);
		$this->promorules->replaceApplicableAgents($promorulesId, $agents);
		$this->promorules->replaceApplicablePlayers($promorulesId, $players);

		$next_action=$this->input->post('next_action');

		if($next_action=='back'){

			redirect('marketing_management/promoRuleManager');

		}else{

			redirect('marketing_management/editPromoRule/'.$promorulesId);

		}


	}

	public function setPromoAllowedGame($ajax_request=true) {
		$promorulesId = $this->input->post("promorulesId");
		$this->utils->debug_log('==========================setPromoAllowedGame promorulesId', $promorulesId);

		$this->load->model('promorules');

		$rlt = false;
		$showGameTree = $this->config->item('show_particular_game_in_tree');
		$gamesAptList = $this->loadSubmitGameTreeWithNumber($showGameTree);
		$this->utils->debug_log('================setPromoAllowedGame gameDescList', $gamesAptList);

		if (!empty($gamesAptList)) {
			$rlt = $this->promorules->batchAddAllowedGames($promorulesId, $gamesAptList);
		}

		if(!$ajax_request) {
			return $rlt;
		}
		if($rlt) {
			echo json_encode(array("success"=> true, "message"=> "Already successfully updated selected games."));
			// $message = "Already successfully updated selected games and their cashback percentage.";
			// $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		}
		else {
			echo json_encode(array("success"=> false, "message"=> "Failed to update selected games."));
			// $messsage = "Failed to update selected games and their cashback percentage.";
			// $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
		}
	}

	/**
	 * overview : add new promo by template
	 */
	public function add_new_promo_by_template() {
		//add promo from template
		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->add_css('resources/css/collapse-style.css');
		// $this->template->add_css('resources/css/jquery-checktree.css');
		$this->template->add_js('resources/js/ace/ace.js');
		$this->template->add_js('resources/js/ace/mode-javascript.js');
		$this->template->add_js('resources/js/ace/theme-tomorrow.js');
        $this->template->add_js('resources/js/ace-helper.js');
		// $this->template->add_js('resources/js/jquery-checktree.js');

		$this->load->model(array('promorules', 'promo_rule_templates'));
		$data['template_list'] = $this->promo_rule_templates->getTemplateList();
		$data['promoCategoryList'] = $this->promorules->getPromoCategoryListKV();

		$this->template->write_view('main_content', 'marketing_management/promorules/add_new_by_template', $data);
		$this->template->render();
	}

	/**
	 * overview : create promo from template
	 */
	public function create_promo_from_template() {
		$template_id = $this->input->post('template_id');

		$success = false;
		//load template
		$this->load->model(array('promo_rule_templates', 'promorules'));
		$tmpl = $this->promo_rule_templates->getTemplateById($template_id);
		if ($tmpl) {
			$template_parameters = json_decode($tmpl['template_parameters'], true);
			if (!empty($template_parameters)) {
				$template_content = json_decode($tmpl['template_content'], true);

				foreach ($template_parameters as $param) {
					$val = $this->input->post($param['name'] . '_' . $template_id);
					// $this->utils->debug_log($param['name'], $val);
					// if (empty($val)) {
					// $val = isset($param['value']) ? $param['value'] : '';
					// }
					if ($param['type'] == 'checkbox') {
						$val = $val == 'true';
					} else if ($param['type'] == 'float_amount') {
						$val = floatval($val);
					}
					// $this->utils->debug_log($param['name'], $val);
					$template_content['json_info'][$param['name']] = $val;
				}

				//print
				$this->utils->debug_log('template_content', $template_content, 'template_id', $template_id);

				$adminUserId = $this->authentication->getUserId();
				$promoruleName = $tmpl['template_name'] . ' ' . $this->utils->getNowForMysql();
				$promoCategory = $this->input->post('promoCategory');
				//save to
				$success = $this->promorules->saveTemplateToPromoRule($promoCategory, $promoruleName, $template_content, $adminUserId);
			}

		}

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('New promotion rule is added'));
			redirect('/marketing_management/promoRuleManager');
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Add new promotion rule is failed'));
			redirect('/marketing_management/add_new_promo_by_template');
		}

	}

	/**
	 * overview : promo rule manager
	 *
	 *  @return	rendered template
	 */
	public function promoRuleManager() {
		if (!$this->permissions->checkPermissions('promo_rules_setting')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Marketing Management', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');

			$this->load->model(array('promorules'));
			$data['playerLvl'] = $this->depositpromo_manager->getAllPlayerLevels();
			$data['promorules'] = $this->promorules->getAllPromoRule();

			$this->template->write_view('main_content', 'marketing_management/promorules/view_promo_manager', $data);
			$this->template->render();
		}
	}

}
////END OF FILE/////////
