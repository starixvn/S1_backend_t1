<?php

/**
 * General behaviors include:
 * * Loads Template
 * * Displays Affiliate Commission Setting Page
 * * Manage Operator Setting
 * * Manage Affiliate Commission Setting
 * * Manage Sub Affiliate Commission Setting
 * * Saves new setting
 *
 * @see Redirect redirect to affiliate commission setting page
 *
 * @category Affiliate Modules
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait affiliate_terms_module {

	public function saveAffiliateCommissionTier() {

		$data = $this->input->post();

		$data['baseIncomeConfig'] 	= intval($data['baseIncomeConfig']);
		$data['minimumDeposit'] 	= floatval($data['minimumDeposit']);
		$data['admin_fee'] 			= floatval($data['admin_fee']);
		$data['transaction_fee'] 	= floatval($data['transaction_fee']);
		$data['bonus_fee'] 			= floatval($data['bonus_fee']);
		$data['cashback_fee'] 		= floatval($data['cashback_fee']);
		$data['minimumPayAmount'] 	= floatval($data['minimumPayAmount']);
		$data['paymentSchedule'] 	= is_numeric($data['paymentSchedule']) ? intval($data['paymentSchedule']) : $data['paymentSchedule'];
		$data['manual_open'] 		= $data['manual_open'] == 'true';
		$data['sub_link'] 			= $data['sub_link'] == 'true';

		array_walk($data['sub_levels'], function(&$str) {
			$str = floatval($str);
		});

		$tiers = array();
		foreach ($data['commission_percentage'] as $i => $commission_percentage) {
			$tiers[] = array(
				'active_players' 		=> $data['active_players'][$i],
				'net_revenue' 			=> $data['net_revenue'][$i],
				'commission_percentage' => $commission_percentage,
			);
		}

		unset($data['active_players'],$data['net_revenue'],$data['commission_percentage']);

		$data['tiers'] = $tiers;

		$success = $this->operatorglobalsettings->syncSettingJson('aff_commission_settings', $data, 'template');

		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/viewTermsSetup_2','refresh');
	}

	public function viewTermsSetup_2() {
		if (!$this->permissions->checkPermissions('affiliate_terms')) {
			$this->error_access();
		} else {

			$data['data'] = $this->operatorglobalsettings->getSettingJson('aff_commission_settings', 'template') ? : $this->utils->getConfig('aff_commission_settings');

			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');
			$this->template->write_view('main_content', 'affiliate_management/setup/view_setup_2', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : view terms setup
	 */
	public function viewTermsSetup() {
		if (!$this->permissions->checkPermissions('affiliate_terms')) {
			$this->error_access();
		} else {
            $this->template->add_css('resources/third_party/bootstrap-toggle-master/css/bootstrap-toggle.min.css');
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			// load game list
			$this->load->model(array('external_system', 'affiliatemodel'));
			$game = $this->external_system->getAllActiveSytemGameApi();

			$commonSettings = $this->affiliatemodel->getDefaultAffSettings();
			//print_r($commonSettings);
			// check if post not empty
			// if (!empty($_POST)) {
			// 	// -> then store to operator settings
			// 	// var_dump($_POST); die();

			// 	$this->setAsDefault();
			// }

			// else load default value

			// $affiliate_settings = $this->affiliate->getAffiliateSettings();
			// $affiliate_terms = $this->affiliate->getDefaultAffiliateTerms();
			// $sub_affiliate_terms = $this->affiliatemodel->getDefaultSubAffiliateTerms();

			// $aff_settings = "";
			// if (!empty($affiliate_settings)) {
			// 	$aff_settings = json_decode($affiliate_settings);
			// }

			// $affiliate_terms_type = "";
			// $totalactiveplayer = 0;
			// $minBetting = 0;
			// $minDeposit = 0;
			// $provider = [];

			// if (!empty($affiliate_terms) || $affiliate_terms != 0) {
			// 	$this->utils->debug_log('affiliate_terms', $affiliate_terms);
			// 	$default = json_decode($affiliate_terms); // comment on debugger mode
			// 	$affiliate = $default;
			// 	$affiliate_terms_type = $affiliate->terms->terms_type;
			// 	switch ($affiliate_terms_type) {
			// 	case 'option1':
			// 		$totalactiveplayer = $affiliate->terms->totalactiveplayer;
			// 		if (isSet($affiliate->terms->minimumBetting)) {
			// 			$minBetting = $affiliate->terms->minimumBetting;
			// 		}

			// 		if (isSet($affiliate->terms->minimumDeposit)) {
			// 			$minDeposit = $affiliate->terms->minimumDeposit;
			// 		}

			// 		$provider = $affiliate->terms->provider;
			// 		break;
			// 	}
			// }

			// $sub_affiliate_terms_type = "";
			// $sub_allowed = "";
			// $sub_level = "";
			// $sub_levels = [];
			// $sub_shares_percent = "";
			// $manual_open = false;
			// $sub_link = false;

			// if (!empty($sub_affiliate_terms) || $sub_affiliate_terms != 0) {
			// 	$sub_default = json_decode($sub_affiliate_terms); // comment on debugger mode
			// 	if ($sub_default != null) {
			// 		$sub_affiliate = $sub_default;

			// 		$sub_affiliate_terms_type = $sub_affiliate->terms->terms_type;

			// 		switch ($sub_affiliate_terms_type) {
			// 		case 'allow':
			// 			$sub_allowed = $sub_affiliate->terms->sub_allowed;
			// 			switch ($sub_allowed) {
			// 			case 'manual':
			// 				$sub_level = $sub_affiliate->terms->sub_level;
			// 				$sub_levels = $sub_affiliate->terms->sub_levels; // explode(',', $sub_affiliate->terms->sub_levels);
			// 				if (isSet($sub_affiliate->terms->manual_open)) {
			// 					$manual_open = true;
			// 				}

			// 				if (isSet($sub_affiliate->terms->sub_link)) {
			// 					$sub_link = true;
			// 				}

			// 				break;
			// 			}
			// 			break;
			// 		}
			// 	}
			// }

			$data = array(
				'commonSettings' => $commonSettings,
				// 'affiliate_terms_type' => $affiliate_terms_type,
				// 'totalactiveplayer' => $totalactiveplayer,
				// 'affiliate_settings' => $affiliate_settings,
				// 'affiliate_terms' => $affiliate_terms,
				// 'sub_affiliate_terms' => $sub_affiliate_terms,
				// 'manual_open' => $manual_open,
				// 'sub_link' => $sub_link,
				// 'sub_affiliate_terms_type' => $sub_affiliate_terms_type,
				// 'sub_allowed' => $sub_allowed,
				// 'sub_level' => $sub_level,
				// 'sub_levels' => $sub_levels,
				// 'sub_shares_percent' => $sub_shares_percent,
				// 'minBetting' => $minBetting,
				// 'minDeposit' => $minDeposit,
				// 'provider' => $provider,
				'game' => $game,
			);

			$this->addBoxDialogToTemplate();

			$this->addJsTreeToTemplate();
			$this->template->add_js('resources/third_party/jstree/jstree_plugin_grid.js');
            $this->template->add_js('resources/third_party/bootstrap-toggle-master/js/bootstrap-toggle.min.js');
            $this->template->add_js('resources/third_party/knockout/knockout.js');

			$this->template->write_view('main_content', 'affiliate_management/setup/view_setup', $data);
			$this->template->render();
		}
	}

    public function get_tier_settings(){
        $this->load->model('affiliatemodel');
        $res = $this->affiliatemodel->getAffCommissionTierSettings();
        $this->returnJsonResult($res);
    }

    public function save_tier_setting($id = null){
        $this->load->model('affiliatemodel');
        $res = $this->affiliatemodel->addEditAffCommTierSettings($this->input->post(), $id);
        $this->returnJsonResult($res);
    }

    public function delete_tier_setting($id){
        $this->load->model('affiliatemodel');
        $res = $this->affiliatemodel->deleteTierSettings($id);
        $this->returnJsonResult($res);
    }

	/**
 	* over view: viewVipTermsSetup
 	* add by spencer.kuo 2017.05.09
 	*/
	public function viewAffilliateLevelSetup() {
		if (!$this->permissions->checkPermissions('affiliate_terms')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			// load game list
			$this->load->model(array('external_system', 'affiliatemodel'));
			$data['setting'] = json_decode($this->operatorglobalsettings->getSettingJson('affilliate_level_settings', 'template'), true);

			$this->addBoxDialogToTemplate();

			$this->addJsTreeToTemplate();

			$this->template->write_view('main_content', 'affiliate_management/setup/view_affiliate_level', $data);
			$this->template->render();
		}
	}

	/**
	 * over view save_vip_common_setup
	 * add by spencer.kuo 2017.05.10
	 */
	public function saveAffilliateLevelSetup($vip_level) {
		$this->load->model(['operatorglobalsettings']);
		$affilliate_level_settings = $this->operatorglobalsettings->getSettingJson('affilliate_level_settings', 'template');
		$this->utils->debug_log('affilliate_level_settings', $affilliate_level_settings);
		$setting = Array();
		$input = $this->input->post();

		if (!empty($affilliate_level_settings)) {
			$setting = json_decode($affilliate_level_settings, true);
		} else {
			$this->operatorglobalsettings->insertSettingJson('affilliate_level_settings', '', 'template');
		}
		$setting[$vip_level] = Array();
		$setting[$vip_level]['vip_level'] = $vip_level;
		$setting[$vip_level]['min_profits'] = $input['min_profits'][$vip_level];
		$setting[$vip_level]['max_profits'] = $input['max_profits'][$vip_level];
		$setting[$vip_level]['min_valid_player'] = $input['min_valid_player'][$vip_level];
		$setting[$vip_level]['max_valid_player'] = $input['max_valid_player'][$vip_level];
		$selected_game = explode(',', $input['selected_game_tree'][$vip_level]);
		foreach ($selected_game as $game_id) {
			if (!empty($input['per_' . $game_id]))
				$setting[$vip_level]['selected_game_tree'][$game_id] = $input['per_' . $game_id];
		}
		$jsonSetting = json_encode($setting);
		$this->utils->debug_log('affilliate_level_settings jsonsetting', $jsonSetting);
		$success = $this->operatorglobalsettings->putSettingJson('affilliate_level_settings', $jsonSetting, 'template');
		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}
		redirect('/affiliate_management/viewAffilliateLevelSetup');
	}

	public function viewAffilliateSubLevelSetup() {
		if (!$this->permissions->checkPermissions('affiliate_terms')) {
			$this->error_access();
		} else {
			$this->loadTemplate('Affiliate Management', '', '', 'affiliate');

			// load game list
			$this->load->model(array('external_system', 'affiliatemodel'));
			$data['setting'] = json_decode($this->operatorglobalsettings->getSettingJson('affilliate_sub_level_settings', 'template'), true);

			$this->addBoxDialogToTemplate();

			$this->addJsTreeToTemplate();

			$this->template->write_view('main_content', 'affiliate_management/setup/view_affiliate_sub_level', $data);
			$this->template->render();
		}

	}

	public function saveAffilliateSubLevelSetup() {
		$this->load->model(['operatorglobalsettings']);
		$affilliate_sub_level_settings = $this->operatorglobalsettings->getSettingJson('affilliate_sub_level_settings', 'template');
		$this->utils->debug_log('affilliate_sub_level_settings', $affilliate_sub_level_settings);
		$setting = Array();
		$input = $this->input->post();

		if (!empty($affilliate_sub_level_settings)) {
			$setting = json_decode($affilliate_sub_level_settings, true);
		} else {
			$this->operatorglobalsettings->insertSettingJson('affilliate_sub_level_settings', '', 'template');
		}
		$setting['levelmaster'] = $input['levelmaster'];
		$setting['level1rate'] = $input['level1rate'];
		$setting['level2type'] = $input['level2type'];
		switch((int)$setting['level2type']) {
			case 1 :
				$selected_game = explode(',', $input['selected_game_tree']);
				foreach ($selected_game as $game_id) {
					if (!empty($input['per_' . $game_id]))
						$setting['selected_game_tree'][$game_id] = $input['per_' . $game_id];
				}
				break;
			case 2 :
				$setting['level2Rate'] = $input['level2Rate'];
				break;
		}
		$jsonSetting = json_encode($setting);
		$this->utils->debug_log('affilliate_sub_level_settings jsonsetting', $jsonSetting);
		$success = $this->operatorglobalsettings->putSettingJson('affilliate_sub_level_settings', $jsonSetting, 'template');
		if ($success) {
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}
		redirect('/affiliate_management/viewAffilliateSubLevelSetup');
	}

	/**
	 * overview : save common setup(operator_settings)
	 * Patch for OGP-12828 Affiliate "Operator Settings" can't save the set %
	 *
	 * @param integer $affId Ref. to affiliate_terms.affiliateId.
	 * @return redirect Redirect to "/affiliate_management/userInformation/{$affId}" .
	 */
	public function save_common_setup_with_operator_settings($affId){
		$this->load->model(array('affiliatemodel'));

		$request = $this->getInputGetAndPost();

		// laod operator_settings fields for update
		$affiliateSettings = $this->affiliatemodel->getAffTermsSettings($affId);

		// $selectedIds = $this->input->post('selected_game_tree'); // jstree removed.

		if( isset($request['level_master']) ){
			$affiliateSettings['level_master'] = (integer)$request['level_master'];
		}
		if( isset($request['platform_shares']) ){
			$platform_shares = $request['platform_shares'];
			reset($platform_shares);
			while (list($game_id, $value) = each ($platform_shares)) {
				$affiliateSettings['platform_shares'][$game_id] = $value;
			}
		}

		// $affiliateSettings
		// $this->utils->debug_log('mergeToAffiliateSettings : ' , $fldKV, $affId, $mode);
		// $success = $this->affiliatemodel->mergeToAffiliateSettings($fldKV, $affId, $mode);
		$success = $this->affiliatemodel->updateAffSettings($affId,$affiliateSettings);
		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);
			// $success = $this->operatorglobalsettings->putSettingJson('affiliate_common_settings',
			// 					$aff_setting, 'template');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		redirect('/affiliate_management/userInformation/' . $affId);
	}// EOF save_common_setup_with_operator_settings



	/**
	 * overview : save common setup
	 *
	 * @param $mode
	 * @param null $affId
	 * @return redirect
	 */
	public function save_common_setup($mode, $affId = null) {

		$this->load->model(array('affiliatemodel'));

		# CHECK PERMISSION
		if (empty($mode) || ! $this->permissions->checkPermissions('edit_affiliate_term')) {
			return $this->error_access();
		}

		if ($mode == 'sub_affiliate_settings' && ! $this->permissions->checkPermissions('affiliate_admin_action')) {
			return $this->error_access();
		}

		$fields = array(
			'operator_settings' => array(
				'baseIncomeConfig' => 'int',
				'level_master' => 'double',
				'admin_fee' => 'double',
				'transaction_fee' => 'double',
				'bonus_fee' => 'double',
				'cashback_fee' => 'double',
				'minimumPayAmount' => 'double',
				'paymentSchedule' => 'string',
				'paymentDay' => 'int',
				'autoTransferToWallet' => 'string',
				'allowed_fee' => 'array',
				'platform_shares' => 'array_double',
				'selected_game_tree' => 'array_double',
                'enable_commission_by_tier' => 'bool',
                'tier_provider' => 'array_int',
                'enable_transaction_fee' => 'bool',
                'split_transaction_fee' => 'bool',
                'transaction_deposit_fee' => 'double',
                'transaction_withdrawal_fee' => 'double',
            ),
			'commission_setup' =>array(
				'totalactiveplayer' => 'int',
				'minimumBetting' => 'double',
				'minimumDeposit' => 'double',
				'provider' => 'array_int',
			),
			'sub_affiliate_settings' => array(
				'manual_open' => 'bool',
				'sub_link' => 'bool',
				'sub_level' => 'int',
				'sub_levels' => 'array_double',
			),
		);

		$ignore_cost_settings = ! $this->utils->isEnabledFeature('individual_affiliate_term') && ! empty($affId);
		if ($ignore_cost_settings) {
			if ($mode == 'operator_settings') {
				// only allow master and game tree
				$fields['operator_settings'] = array('level_master' => 'double');
			}
		}
		if($this->utils->isEnabledFeature('switch_to_ibetg_commission')) {
			if ($mode == 'operator_settings') {
				$fields['operator_settings'] = array(
					'baseIncomeConfig' => 'int',
					'level_master' => 'double',
					'platform_shares' => 'array_int',
					'selected_game_tree' => 'array_int',
				);
			}
			if ($mode == 'commission_setup') {
				$fields['commission_setup']['minimumBettingTimes'] = 'int';
			}
		}
		$fldKV = $this->filterInput($fields[$mode]);

		//update affiliate share settings
		if ($mode == 'operator_settings') {
			$gameNumberList = array();
			$selectedIds = $this->input->post('selected_game_tree');
			$idArr = explode(',', $selectedIds);
			if ( ! empty($idArr)) {
				foreach ($idArr as $id) {
					if (!empty($id)) {
						$gameNumber = array();
						$arr = explode('_', $id);
						if (count($arr) >= 6) {
							$gamePlatformId = $arr[1];
							$gameTypeId = $arr[3];
							$gameDescId = $arr[5];
							$gameNumber['game_platform_id'] = $gamePlatformId;
							$gameNumber['game_platform_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId));
							$gameNumber['game_type_id'] = $gameTypeId;
							$gameNumber['game_type_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId.'_gt_'.$gameTypeId));
							$gameNumber['id'] = $gameDescId;
							$gameNumber['game_desc_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId.'_gt_'.$gameTypeId.'_gd_'.$gameDescId));
						}elseif (count($arr) >= 4){
							$gamePlatformId = $arr[1];
							$gameTypeId = $arr[3];
							$gameNumber['game_platform_id'] = $gamePlatformId;
							$gameNumber['game_platform_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId));
							$gameNumber['game_type_id'] = $gameTypeId;
							$gameNumber['game_type_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId.'_gt_'.$gameTypeId));
						}elseif (count($arr) >= 2){
							$gamePlatformId = $arr[1];
							$gameNumber['game_platform_id'] = $gamePlatformId;
							$gameNumber['game_platform_number'] = floatval($this->input->post('per_gp_' . $gamePlatformId));
						}
						$gameNumberList[] = $gameNumber;
					}
				}
				$this->affiliatemodel->batchAddAffiliateGames($gameNumberList, $affId);
			}
		}

        if(isset($fldKV['enable_commission_by_tier']) && $fldKV['enable_commission_by_tier'] == true){
            $tier = $this->affiliatemodel->getAffCommissionTierSettings();
            if(count($tier) == 0){
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Please fill up the commission computation by tier form'));
                $fldKV['enable_commission_by_tier'] = false;
                redirect('/affiliate_management/viewTermsSetup');
            }
        }

		if ($mode == 'operator_settings' && ! $ignore_cost_settings) {
			$allowed = array('admin_fee','transaction_fee','bonus_fee','cashback_fee');
			foreach ($allowed as $key) {
				if(!empty($fldKV['allowed_fee'])){
					//not allow
					if( ! in_array($key, $fldKV['allowed_fee'])){
						$fldKV[$key] = 0;
					}
				}
			}

			if ( ! isset($fldKV['autoTransferToWallet'])) {
				$fldKV['autoTransferToWallet'] = '';
			}
			if ($fldKV['autoTransferToWallet'] == 'main') {
				$fldKV['autoTransferToWallet'] = true;
				$fldKV['autoTransferToLockedWallet'] = false;
			}else if($fldKV['autoTransferToWallet'] == 'locked'){
				$fldKV['autoTransferToWallet'] = false;
				$fldKV['autoTransferToLockedWallet'] = true;
			}else{
				$fldKV['autoTransferToWallet'] = false;
				$fldKV['autoTransferToLockedWallet'] = false;
			}
		}

		if ($mode == 'commission_setup') {
			$provider=$this->input->post('provider');

			$this->load->model(['external_system']);
			// $provider_betting_amount=$emptySettings['provider_betting_amount'];
			//fill by game api
			$games = $this->external_system->getAllActiveSytemGameApi();
			foreach ($games as $g) {
				$game_id=$g['id'];
				$v=0;
				if(!empty($provider) && in_array($game_id, $provider)){
					$v=$this->input->post('provider_betting_amount_'.$game_id);
					if(!empty($v)){
						$v=doubleval($v);
					}else{
						$v=0;
					}
				}

				$fldKV['provider_betting_amount'][$game_id]=$v;
			}
		}

		if ($affId) {
			$this->utils->debug_log('mergeToAffiliateSettings : ' , $fldKV, $affId, $mode);
			$success = $this->affiliatemodel->mergeToAffiliateSettings($fldKV, $affId, $mode);
		} else {
			$success = $this->affiliatemodel->mergeToAffiliateCommonSettings($fldKV, $mode);
		}

		if ($success) {
			$username=$this->affiliatemodel->getUsernameById($affId);
			$this->syncAffCurrentToMDBWithLock($affId, $username, false);

			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Save settings successfully'));
		} else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save settings failed'));
		}

		if (empty($affId)) {
			redirect('/affiliate_management/viewTermsSetup');
		} else {
			redirect('/affiliate_management/userInformation/' . $affId);
		}

	}

	/**
	 * overview : get default sub affiliate terms
	 *
	 * @return json
	 */
	public function getDefaultSubAffiliateTermsAJAX() {

		$sub_affiliate_terms = json_decode($this->affiliate->getDefaultSubAffiliateTerms());

		$arr = array(
			'status' => 'success',
			'sub_affiliate_terms' => $sub_affiliate_terms,
		);

		echo json_encode($arr);
	}

	// public function getDefaultSubAffiliateTermsByIdAJAX($affiliate_id) {

	// 	$sub_affiliate_terms = $this->affiliate->getSubAffiliateTermsById($affiliate_id);

	// 	if (empty($sub_affiliate_terms)) {
	// 		$sub_affiliate_terms = json_decode($this->affiliate->getDefaultSubAffiliateTerms());
	// 	} else {
	// 		$sub_affiliate_terms = json_decode($sub_affiliate_terms);
	// 	}

	// 	$arr = array(
	// 		'status' => 'success',
	// 		'sub_affiliate_terms' => $sub_affiliate_terms,
	// 	);

	// 	echo json_encode($arr);
	// }

	/**
	 * edit Affiliate Terms Default Setup
	 *
	 * @param 	int
	 * @return	redirect
	 */
	// public function setAsDefault() {
	// 	if (!$this->permissions->checkPermissions('affiliate_terms')) {
	// 		$this->error_access();
	// 	} else {
	// 		$this->startTrans();

	// 		$row = "";
	// 		$terms = $this->input->post('terms');
	// 		$terms_type = $this->input->post('terms_type');
	// 		$data = "";

	// 		switch ($terms) {
	// 		case 'operator_terms':
	// 			$row = "affiliate_settings";

	// 			$data = '{';
	// 			$data = $data . '"baseIncomeConfig": "' . $_POST['baseIncomeConfig'] . '",';
	// 			$data = $data . '"level_master":"' . $_POST['level_master'] . '",';
	// 			$data = $data . '"minimumPayAmount": "' . $_POST['minimumPayAmount'] . '",';
	// 			$data = $data . '"autoTransferToWallet": ' . ($this->input->post('autoTransferToWallet') == 'true' ? 'true' : 'false') . ',';
	// 			$data = $data . '"paymentDay": "' . $_POST['paymentDay'] . '",';

	// 			$allowedFee = $_POST['allowed_fee'];
	// 			end($allowedFee);
	// 			$last_key = key($allowedFee);

	// 			if (!empty($allowedFee)) {
	// 				foreach ($allowedFee as $key => $value) {
	// 					if ($last_key != $key) {
	// 						$data = $data . '"' . $value . '": "' . $_POST[$value] . '",';
	// 					} else {
	// 						$data = $data . '"' . $value . '": "' . $_POST[$value] . '"';
	// 					}

	// 				}
	// 			}
	// 			$data = $data . '}';
	// 			break;
	// 		case 'affiliate_terms':
	// 			$row = "affiliate_default_terms";
	// 			$data = '{"terms": {"terms_type": "' . $terms_type . '",';
	// 			switch ($terms_type) {
	// 			case 'option1':
	// 				$providers = isset($_POST['provider']) ? @$_POST['provider'] : null;
	// 				if (empty($providers)) {
	// 					$providers = array();
	// 				}
	// 				$data = $data . '"totalactiveplayer": "' . $_POST['totalactiveplayer'] . '",';
	// 				$data = $data . '"minimumBetting": "' . $_POST['minimumBetting'] . '",';
	// 				$data = $data . '"minimumDeposit": "' . $_POST['minimumDeposit'] . '",';
	// 				$data = $data . '"provider": [' . implode(",", $providers) . ']';
	// 				break;
	// 			}
	// 			$data = $data . "}}";
	// 			break;
	// 		case 'sub_affiliate_terms':
	// 			$row = "sub_affiliate_default_terms";

	// 			$data = '{"terms": {"terms_type": "' . $terms_type . '",';
	// 			switch ($terms_type) {
	// 			case 'allow':
	// 				$sub_allowed = $_POST['sub_allowed'];
	// 				$data = $data . '"sub_allowed":"' . $sub_allowed . '",';
	// 				switch ($sub_allowed) {
	// 				case 'all':
	// 					$data = $data . '"sub_level":"' . $_POST['sub_level'] . '",';
	// 					$data = $data . '"sub_levels":[' . implode(",", $_POST['sub_levels']) . '],';
	// 					$data = $data . '"sub_shares_percent":"' . $_POST['sub_shares_percent'] . '"';
	// 					break;
	// 				case 'manual':
	// 					if (isSet($_POST['manualOpen'])) {
	// 						$data = $data . '"manual_open":"' . $_POST['manualOpen'] . '",';
	// 					}

	// 					if (isSet($_POST['subLink'])) {
	// 						$data = $data . '"sub_link":"' . $_POST['subLink'] . '",';
	// 					}

	// 					$rates_input = $_POST['sub_levels'];
	// 					$rates_value = [];

	// 					foreach ($rates_input as $key => $value) {
	// 						if ($value != "" || $value != null) {
	// 							$rates_value[] = $value;
	// 						} else {
	// 							$rates_value[] = 0;
	// 						}
	// 					}

	// 					$data = $data . '"sub_level":"' . $_POST['sub_level'] . '",';
	// 					$data = $data . '"sub_levels":[' . implode(',', $rates_value) . ']';
	// 					break;
	// 				case 'link':
	// 					$data = $data . '"sub_shares_percent":"' . $_POST['sub_shares_percent'] . '"';
	// 					break;
	// 				}
	// 				break;
	// 			case 'unallow':
	// 				$data = 0;
	// 				break;
	// 			}
	// 			if ($terms_type != 'unallow') {
	// 				$data = $data . "}}";
	// 			}

	// 			break;
	// 		}

	// 		$this->affiliate->updateDefaultTerms($row, $data);

	// 		$this->utils->debug_log('terms', $terms);
	// 		if ($terms == 'operator_terms') {
	// 			//save game info
	// 			$showGameTree = $this->config->item('show_particular_game_in_tree');
	// 			$gameNumberList = $this->loadSubmitGameTreeWithNumber($showGameTree);
	// 			$this->utils->debug_log('showGameTree', $showGameTree, 'gameNumberList', count($gameNumberList));
	// 			$this->affiliatemodel->batchAddAffiliateGames($gameNumberList);
	// 		}

	// 		$this->saveAction('Affiliate Terms Default Setup', "User " . $this->authentication->getUsername() . " change affiliate terms setup.");

	// 		$success = $this->endTransWithSucc();
	// 		if ($success) {
	// 			$message = lang('con.aff45');
	// 			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
	// 		} else {
	// 			$message = lang('Sorry, save affiliate terms failed');
	// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
	// 		}

	// 		redirect('affiliate_management/viewTermsSetup');
	// 		// }
	// 	}
	// }

	// public function setAsDefaultById() {
	// 	if (!$this->permissions->checkPermissions('affiliate_terms')) {
	// 		$this->error_access();
	// 	} else {
	// 		// var_dump($_POST); die();
	// 		$this->startTrans();

	// 		$row = "";
	// 		$affiliateId = $this->input->post('affiliateId');
	// 		$terms = $this->input->post('terms');
	// 		$terms_type = $this->input->post('terms_type');
	// 		$data = "";

	// 		switch ($terms) {
	// 		case 'operator_terms':
	// 			$row = "affiliate_settings";

	// 			$data = '{';
	// 			$data = $data . '"baseIncomeConfig": "' . $_POST['baseIncomeConfig'] . '",';
	// 			$data = $data . '"minimumPayAmount": "' . $_POST['minimumPayAmount'] . '",';
	// 			$data = $data . '"paymentDay": "' . $_POST['paymentDay'] . '",';

	// 			$allowedFee = $_POST['allowed_fee'];
	// 			end($allowedFee);
	// 			$last_key = key($allowedFee);

	// 			if (!empty($allowedFee)) {
	// 				foreach ($allowedFee as $key => $value) {
	// 					if ($last_key != $key) {
	// 						$data = $data . '"' . $value . '": "' . $_POST[$value] . '",';
	// 					} else {
	// 						$data = $data . '"' . $value . '": "' . $_POST[$value] . '"';
	// 					}

	// 				}
	// 			}
	// 			$data = $data . '}';
	// 			break;
	// 		case 'affiliate_terms':
	// 			$row = "affiliate_default_terms";
	// 			$data = '{"terms": {"terms_type": "' . $terms_type . '",';
	// 			switch ($terms_type) {
	// 			case 'option1':
	// 				$data = $data . '"totalactiveplayer": "' . $this->input->post('totalactiveplayer') . '",';
	// 				$data = $data . '"minimumBetting": "' . $this->input->post('minimumBetting') . '",';
	// 				$data = $data . '"minimumDeposit": "' . $this->input->post('minimumDeposit') . '",';
	// 				$data = $data . '"level_master": "' . $this->input->post('level_master') . '",';
	// 				$provider = $this->input->post('provider');
	// 				$providerStr = '';
	// 				if (!empty($provider)) {
	// 					$providerStr = implode(",", $provider);
	// 				}
	// 				$data = $data . '"provider": [' . $providerStr . ']';
	// 				break;
	// 			}
	// 			$data = $data . "}}";
	// 			break;
	// 		case 'sub_affiliate_terms':
	// 			$row = "sub_affiliate_default_terms";

	// 			$sub_levels = $this->input->post('sub_levels');
	// 			$dataJson = array('terms' => array(
	// 				'terms_type' => $terms_type,
	// 				'manual_open' => $this->input->post('manualOpen'),
	// 				'sub_link' => $this->input->post('subLink'),
	// 				'level_master' => $this->input->post('level_master'),
	// 				'sub_level' => count($sub_levels),
	// 				'sub_levels' => $sub_levels,
	// 			),
	// 			);

	// 			$data = json_encode($dataJson);

	// 			break;
	// 		}
	// 		// $this->utils->debug_log('data', $data, 'row', $row, 'affiliateId', $affiliateId);

	// 		// var_dump($data); die();

	// 		$this->affiliate->updateTermsById($affiliateId, $row, $data);

	// 		$this->utils->debug_log('terms', $terms);
	// 		if ($terms == 'affiliate_terms') {
	// 			//save game info
	// 			$showGameTree = $this->config->item('show_particular_game_in_tree');
	// 			$gameNumberList = $this->loadSubmitGameTreeWithNumber($showGameTree);
	// 			$this->utils->debug_log('showGameTree', $showGameTree, 'gameNumberList', count($gameNumberList));
	// 			$this->affiliatemodel->batchAddAffiliateGames($gameNumberList, $affiliateId);
	// 		}

	// 		$this->saveAction('Affiliate Terms Default Setup', "User " . $this->authentication->getUsername() . " change affiliate terms setup.");

	// 		$success = $this->endTransWithSucc();
	// 		if ($success) {
	// 			$message = lang('aff.ai31') . ' ' . lang('con.aff39');
	// 			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
	// 		} else {
	// 			$message = lang('Sorry, save affiliate terms failed');
	// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
	// 		}

	// 		redirect('affiliate_management/userInformation/' . $affiliateId);
	// 	}
	// }

	/**
	 * overview : callback for setAsDefault
	 *
	 * @return	redirect
	 */
	public function checkPercentage($percentage) {
		if ($percentage == 0) {
			$this->form_validation->set_message('checkPercentage', 'Percentage cannot be set to 0.');
			return false;
		}

		return true;
	}

	/**
	 * overview : callback for setAsDefault
	 *
	 * @return	redirect
	 */
	public function checkActive($active) {
		if ($active == 0) {
			$this->form_validation->set_message('checkActive', 'Active Players cannot be set to 0.');
			return false;
		}

		return true;
	}

	public function affiliate_formula() {
		$this->load->view('affiliate_management/earnings/view_affiliate_formula');
	}

	/* ****** End of Affiliate Terms Default Setup ****** */

}
