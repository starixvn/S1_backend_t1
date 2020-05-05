<?php

/**
 * Class cashback_module
 *
 * General behaviors include :
 *
 * * Create common cashback settings
 * * Update common cashback settings
 *
 * @category Merketing Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait cashback_module {

	/**
	 * overview : update common cashback rule
	 *
	 *  @return	rendered template
	 */
	public function saveCommonCashbackSetting($casbackRuleId = null, $promorulesId=null) {
		$this->load->model('cashback_settings');

		$commonCashbackRuledata = array(
			'min_bet_amount' => $this->input->post("min_bet_amount"),
			'max_bet_amount' => $this->input->post("max_bet_amount"),
			'default_percentage' => $this->input->post("default_percentage"),
		);

		if ($casbackRuleId) {
			$commonCashbackRuledata['id'] = $casbackRuleId;
			$commonCashbackRuledata['updated_by'] = $this->authentication->getUserId();
			$commonCashbackRuledata['updated_at'] = $this->utils->getNowForMysql();
			$this->cashback_settings->updateCommonCashbackRule($commonCashbackRuledata);

			$message = lang('Common cashback rule has been updated');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			$this->saveAction(self::MANAGEMENT_TITLE, 'Edit Common Cashback Rule', "User " . $this->authentication->getUsername() . " has successfully edit common cashback rule with id: " . $casbackRuleId);
		} else {
			$commonCashbackRuledata['created_by'] = $this->authentication->getUserId();
			$commonCashbackRuledata['created_at'] = $this->utils->getNowForMysql();
			$commonCashbackRuledata['status'] = Cashback_settings::ACTIVE;
			$casbackRuleId = $this->cashback_settings->addCommonCashbackRule($commonCashbackRuledata);

			$message = lang('Common cashback rule has been created');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			$this->saveAction(self::MANAGEMENT_TITLE, 'Added cashback rule', "User " . $this->authentication->getUsername() . " has successfully added cashback rule with id: " . $promorulesId);
		}

		$enabled_edit_game_tree=$this->input->post('enabled_edit_game_tree')=='true';
		if($enabled_edit_game_tree) {
			$adminUserId=$this->authentication->getUserId();
			$showGameTree = $this->config->item('show_particular_game_in_tree');
			$this->utils->debug_log('showGameTree', $showGameTree);

			$gamesAptList = $this->loadSubmitGameTreeWithNumber($showGameTree);
			list($gamePlatformList, $gameTypeList, $gameDescList) = $this->loadSubmitGameTree();

			$this->utils->debug_log('================saveCommonCashbackSetting gamePlatformList', $gamePlatformList);
			$this->utils->debug_log('================saveCommonCashbackSetting gameTypeList', $gameTypeList);
			$this->utils->debug_log('================saveCommonCashbackSetting gameDescList', $gameDescList);


			// if (!empty($gamesAptList)) {
			$this->utils->debug_log('casbackRuleId', $casbackRuleId, 'gamesAptList', count($gamesAptList));
			$cashback_percentage = $this->input->post("default_percentage");
			$selected_game_tree = $this->input->post('selected_game_tree');

			$diffList=[];
			$rlt = $this->cashback_settings->batchAddCashbackGameRules($casbackRuleId, $cashback_percentage, $gamesAptList, $adminUserId, $selected_game_tree, $gamePlatformList, $gameTypeList, $gameDescList, $diffList);
			$this->utils->debug_log('saveCommonCashbackSetting', $rlt, $diffList);

			if(!$rlt){
				$additional_message=lang('Saved cashback game tree failed');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('save.failed').' '.$additional_message);
				return $rlt;
			}
			$additional_message=lang('Saved cashback settings');
			//show diff list
			if(!empty($diffList)){
				if(!empty($diffList['deleted_game_platform'])){
					//search system code
					$this->load->model(['external_system']);
					$langList=$this->external_system->searchSystemCodeByList($diffList['deleted_game_platform']);
					sort($langList);
					// $additional_message.=' | '.lang('Deleted').': <br>'.implode(',<br>', $langList);

					// $message = lang('common_cashback_rules.successfully_updated');
					// $this->alertMessage(self::MESSAGE_TYPE_WARNING, $message.' '.$additional_message);
				}
				if(!empty($diffList['deleted_game_type'])){
					//search game type lang
					$this->load->model(['game_type_model']);
					$langList=$this->game_type_model->searchGameTypeByList($diffList['deleted_game_type']);
					sort($langList);
					$additional_message.=' | '.lang('Deleted').': <br>'.implode(',<br>', $langList);

					$message = lang('common_cashback_rules.successfully_updated');
					$this->alertMessage(self::MESSAGE_TYPE_WARNING, $message.' '.$additional_message);
				}
				// if(!empty($diffList['deleted_game_description'])){
				// 	//search game type lang
				// 	$this->load->model(['game_description_model']);
				// 	$langList=$this->game_description_model->searchGameDescriptionByList($diffList['deleted_game_description']);
				// 	sort($langList);
				// 	$additional_message.=' | '.lang('Deleted').': <br>'.implode(',<br>', $langList);

				// 	$message = lang('common_cashback_rules.successfully_updated');
				// 	$this->alertMessage(self::MESSAGE_TYPE_WARNING, $message.' '.$additional_message);
				// }

				$this->utils->debug_log('additional_message for batchAddCashbackPercentage', $additional_message);
			}
			// }
		}

		redirect('marketing_management/cashbackPayoutSetting');
	}

	public function updateStatusCashbackGameRuleSetting($status, $casbackRuleId) {
		$this->load->model('cashback_settings');
		$commonCashbackRuledata = array("status" => $status, "id" => $casbackRuleId, "updated_at" => $this->utils->getNowForMysql(), "updated_by" => $this->authentication->getUserId());

		$this->cashback_settings->updateStatusCashbackGameRuleSetting($commonCashbackRuledata);
		$status = $status ? 'Activate' : 'Deactivate';
		$message = lang('Common cashback rule has been updated');
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		$this->saveAction(self::MANAGEMENT_TITLE, $status . ' Common Cashback Rule', "User " . $this->authentication->getUsername() . " has successfully " . $status . " common cashback rule with id: " . $casbackRuleId);

		redirect('marketing_management/cashbackPayoutSetting');
	}

	/**
	 * overview : cashback game rule setting
	 *
	 * detail : view page for cashback game rule setting
	 */
	public function editCashbackGameRuleSetting($commonCasbackRuleId) {

		if (!$this->permissions->checkPermissions('cashback_setting')) {
			$this->error_access();
		} else {

			$this->load->model(['cashback_settings']);

			$data['common_cashback_rule'] = $this->cashback_settings->getCommonCashbackRule($commonCasbackRuleId);

			$this->loadTemplate('Cashback Game Rule Setting', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'player_management/vipsetting/view_cashback_game_rule_setting', $data);
			$this->template->render();
		}
	}

	/**
	 * overview : add cashback game rule setting
	 *
	 * detail : add cashback game rule setting
	 */
	public function addCashbackGameRuleSetting() {

		if (!$this->permissions->checkPermissions('cashback_setting')) {
			$this->error_access();
		} else {

			$this->load->model(['cashback_settings']);

			$this->loadTemplate('Cashback Game Rule Setting', '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');
			$this->template->write_view('main_content', 'player_management/vipsetting/view_cashback_game_rule_setting');
			$this->template->render();
		}
	}

	public function deleteCashbackGameRuleSetting($commonCasbackRuleId) {

		if (!$this->permissions->checkPermissions('cashback_setting')) {
			$this->error_access();
		} else {
			$current_hour = $this->utils->getThisHourForMysql();
			$this->load->model('group_level');
			$this->load->model('cashback_settings');
			$commonSettings=(array) $this->group_level->getCashbackSettings();

			$toHour = $commonSettings['toHour'] . ':59:59';
			$hour = $commonSettings['payTimeHour'];

			$maxhour = date("H:i", strtotime('+1 hour',strtotime($hour)));
			$minhour = date("H:i", strtotime('-1 hour',strtotime($hour)));

			$maxtoHour = date("H:i:s", strtotime('+1 hour',strtotime($toHour)));
			$mintoHour = date("H:i:s", strtotime('-1 hour',strtotime($toHour)));

			if( ($mintoHour <= $current_hour && $maxtoHour >= $current_hour) || ($minhour <= $current_hour && $maxhour >= $current_hour) ){
				$message = lang('cashbackrules_cant_delete');
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('marketing_management/cashbackPayoutSetting');

			}else{
				$this->cashback_settings->deleteCommonCashbackRule($commonCasbackRuleId);

				$this->saveAction(self::MANAGEMENT_TITLE, 'Deleted cashback rule', "User " . $this->authentication->getUsername() . " has successfully deleted cashback rule with id: " . $commonCasbackRuleId);

				$message = lang('cashbackrules_delete');
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
				redirect('marketing_management/cashbackPayoutSetting');
			}

		}
	}

		public function manually_batch_add_cashback_bonus(){
		if (!$this->permissions->checkPermissions('manually_batch_bonus')) {
			return $this->error_access();
		}

		$this->load->model(array('promorules', 'external_system', 'player_model'));
		
		$data = array(
		
		);
		
		if($this->utils->isEnabledFeature('only_manually_add_active_promotion')){
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();
		}else{
			$data['promoCms'] = $this->promorules->getAllPromoCMSList();
		}

		$allowed_csv_max_size = ' <= 10mb';
		$data['csv_note'] = sprintf(lang("%s size of csv could be uploaded."), $allowed_csv_max_size);
		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/view_batch_add_cashback_bonus.php', $data);
		$this->template->render();

	}

	public function post_manually_batch_add_cashback_bonus(){
		if (!$this->permissions->checkPermissions('manually_batch_bonus')) {
			return $this->error_access();
		}
		$uploadFieldName = 'manually_batch_add_cashback_bonus_csv_file';

		$filepath='';
		$msg='';
		if($this->existsUploadField($uploadFieldName)){
			//check file type
			if($this->saveUploadFileToRemote($uploadFieldName, ['csv'], $filepath, $msg)){
				//get $filepath
				//echo 'uploaded'; 
			}else{
				$message=lang('Upload csv file failed').', '.$msg;
				return false;
			}
		}
		
		$this->load->library(['lib_queue']);
		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=$this->authentication->getUserId();
		$state=null;
		$lang=$this->language_function->getCurrentLanguage();
		$file= empty($filepath) ? null : basename($filepath);
		//save csv file
		if(!empty($file)){	
			$this->load->model(['promorules']);
            #params
			$adminUserId=$this->authentication->getUserId();
			$adminUsername=$this->authentication->getUsername();
			$reason=$this->input->post('reason');
			
			$token=$this->lib_queue->addRemoteBatchAddCashbackBonus($file,$adminUserId,$adminUsername,$reason,$callerType, $caller, $state, $lang);

			if (!empty($token)) {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('Create importing job successfully'));
				return redirect('marketing_management/post_manually_batch_add_cashback_bonus_result/'.$token);

			} else {
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Create importing job failed'));
				redirect('/marketing_management/view_batch_add_cashback_bonus');
			}
		}else {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Upload csv file failed'));
			redirect('/marketing_management/view_batch_add_cashback_bonus');
		}		
	}

	public function post_manually_batch_add_cashback_bonus_result($token){
		$data['result_token']=$token;
		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/cashback/post_manually_batch_add_cashback_bonus_result', $data);
		$this->template->render();
	}




}
////END OF FILE/////////