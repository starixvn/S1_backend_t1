<?php
trait player_promo_management_module {

	/**
	 * promoApplicationList
	 *
	 *
	 * @return	redered template
	 */
	public function promoApplicationList($status=Player_promo::TRANS_STATUS_APPROVED, $transactionDateType=Player_promo::TRANSACTION_DATE_TYPE_PROCESSED_TIME, $player_promo_status=Player_promo::TRANS_STATUS_APPROVED) {
		if (!$this->permissions->checkPermissions('promoapp_list')) {
			$this->error_access();
		} else {
			$this->load->model(array('promorules', 'player_promo','users'));

			$this->loadTemplate(lang('role.59'), '', '', 'marketing');
			$this->template->write_view('sidebar', 'marketing_management/sidebar');

			if (!$this->permissions->checkPermissions('export_report')) {
				$data['export_report_permission'] = FALSE;
			} else {
				$data['export_report_permission'] = TRUE;
			}

            $sort = 'promoName';
            $data['promoList'] = $this->promorules->getPromoSettingList($sort, null, null);
			$data['countAllStatus'] = $this->player_promo->countAllStatusOfPromoApplication();
			$data['allLevels'] = $this->player_manager->getAllPlayerLevels();
            $data['allPlayerPromoStatus'] = [
                'all' => lang('All'),
                Player_promo::TRANS_STATUS_REQUEST => lang('promo.request_list.search_status.pending'),
                Player_promo::TRANS_STATUS_APPROVED => lang('promo.request_list.search_status.approved'),
                Player_promo::TRANS_STATUS_DECLINED => lang('promo.request_list.search_status.declined'),
                Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION => lang('promo.request_list.search_status.finished')
            ];
			//$data['users'] = $this->users->getAllUsernames();
            $data['date_from'] = $date_from = $this->utils->getFirstDateOfCurrentMonth() . ' 00:00:00';
            $data['date_to'] = $date_to = date("Y-m-d") . ' 23:59:59';

            $conditions = array(
                'status'=>$status,
                'player_promo_status'=>$player_promo_status,
                'request_date_from' => $date_from,
                'request_date_to' => $date_to,
                'promorulesId' => '',
                'promoCmsSettingId' => '',
                'username' => '',
                'vipsettingcashbackruleId' => '',
                //'processed_by' => '',
                'transactionDateType' => $transactionDateType,
                'search_by_date' => '1',
                'isClickSearch' => '0'
            );

            $data['conditions'] = $this->safeLoadParams($conditions);

            $this->template->add_css('resources/css/dashboard.css');
			$this->template->add_js('resources/third_party/datatables/datatables.min.js');
			$this->template->add_css('resources/third_party/datatables/datatables.min.css');
			$this->template->write_view('main_content', 'marketing_management/promorules/promoapplication_list', $data);
			$this->template->render();
		}
	}

	public function release_promo($playerpromoId, $bonusAmount=null){
		$this->load->model(array('promorules', 'player_promo'));
		$success=false;
        $promorule = NULL;
		if($playerpromoId){
			$playerPromo = $this->player_promo->getPlayerPromo($playerpromoId);
			$promoCmsSettingId = $playerPromo->promoCmsSettingId;
			$playerId = $playerPromo->playerId;

			if(empty($playerPromo)){
                $message = lang('error.default.db.message');
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
                    return;
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('marketing_management/promoApplicationList');
            }

	        $lockedKey=null;
			$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);

            $promorulesId = $playerPromo->promorulesId;
            $promorule = $this->promorules->getPromorule($promorulesId);

            if(empty($promorule)){
                $message = lang('error.default.db.message');
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
                    return;
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                return redirect('marketing_management/promoApplicationList');
            }

			// $lock_it = $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
			$this->utils->debug_log('lock admin promo', $playerId, $lock_it);

			$success = $lock_it;
			$extra_info=['release_to_real'=>true, 'promorule' => $promorule, 'player_promo_request_id' => $playerpromoId];
			$result=null;

            $playerPromoStatus = $this->player_promo->getPlayerPromoStatusById($playerpromoId);
            $allow_release_promo = in_array($playerPromoStatus, [Player_promo::TRANS_STATUS_REQUEST, Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS]);
			if ($lock_it) {
				if($allow_release_promo){
					//lock success
					try {
						//check status again


						$this->startTrans();
						$adminUserId = $this->authentication->getUserId();

						// $playerPromo = $this->player_promo->getPlayerPromo($playerpromoId);
						// $promoCmsSettingId = $playerPromo->promoCmsSettingId;
						// $playerId = $playerPromo->playerId;

						// $promotion_rules=$this->utils->getConfig('promotion_rules');
						//default is false
						// $release_on_admin_approve=$promotion_rules['release_on_admin_approve'];

						// if($release_on_admin_approve){
						$tranId=null;
						$reason=null;
						$extra_info['bonusAmount']=$bonusAmount;
						$success=!!$this->promorules->approvePromo($playerId, $promorule, $promoCmsSettingId,
								$adminUserId, $playerPromo->depositAmount, $tranId, $playerpromoId, $extra_info, $reason);

						// $success=$result['success'];
						// }else{

							// $this->promorules->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
								// $adminUserId, $playerPromo->depositAmount, null, $playerpromoId);

							//should checkfirst
							// $result=$this->promorules->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId);

						// }


						//log admin action
						$this->saveAction(self::MANAGEMENT_TITLE, 'Release Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully approved player promo :".$playerpromoId);

						// $this->group_level->endTrans();
						// if ($this->group_level->isErrorInTrans()) {
						// 	//show error
						// 	$this->alertMessage(1, lang('save.failed'));
						// }
						if($success){
							$success = $this->endTransWithSucc();
						}else{
							//false rollback
							$this->rollbackTrans();
							$this->utils->error_log('rollback release_promo playerpromoId:', $playerpromoId);
						}
						// $success = $this->endTransWithSucc() && $success;
					} finally {
						$rlt = $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
						// release it
						// $rlt = $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
						// $rlt = $this->player_model->transReleaseLock($trans_key);
						$this->utils->debug_log('release admin promo lock', $playerId, $rlt);
					}
				}else{
					$success = false;
				}
			}
		}

		if($success){
			// $result['message'].=' '.lang($message);

			$bonusResult=$this->promorules->releaseToAfterApplyPromoV2($promorule, $extra_info);
		}else{
			// $result['success']=false;
			// $result['message'].=' '.lang($message);
			//don't show error, only show success
			$this->utils->debug_log('apply promotion failed', $success, $playerId, $playerpromoId);
			// $message=null;
		}

		if ($success && $lock_it) {
			//Promo application has been approved!
			$message = lang('Released bonus to player');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => true, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');

	}

	/**
     * @deprecated marked by curtis
	 * approvePromoApplication
	 *
	 *
	 * @return	redered template
	 */
	public function approve_player_promo($playerpromoId) {
		// $this->group_level->startTrans();

		$this->load->model(array('promorules', 'player_promo'));
		$checkPromoIfUpdated = $this->player_promo->checkPromoIfUpdated($playerpromoId);
		$success=false;
		if($playerpromoId && !$checkPromoIfUpdated){

			$playerPromo = $this->player_promo->getPlayerPromo($playerpromoId);
			$promoCmsSettingId = $playerPromo->promoCmsSettingId;
			$playerId = $playerPromo->playerId;

	        $lockedKey=null;
			$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
			// $lock_it = $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
			$this->utils->debug_log('lock admin promo', $playerId, $lock_it);

			$success = $lock_it;
			if ($lock_it) {
				//lock success
				try {

					$this->startTrans();
					$adminUserId = $this->authentication->getUserId();

					// $playerPromo = $this->player_promo->getPlayerPromo($playerpromoId);
					// $promoCmsSettingId = $playerPromo->promoCmsSettingId;
					// $playerId = $playerPromo->playerId;
					$promorulesId = $playerPromo->promorulesId;
					$promorule = $this->promorules->getPromorule($promorulesId);

					$promotion_rules=$this->utils->getConfig('promotion_rules');
					//default is false
					// $release_on_admin_approve=$promotion_rules['release_on_admin_approve'];

					$reason=null;
					$extra_info=null;
					$success=!!$this->promorules->approvePrePromo($playerId, $promorule, $promoCmsSettingId,
						$adminUserId, $playerPromo->depositAmount, null, $playerpromoId, $extra_info, $reason);

					// if($promorule['add_withdraw_condition_as_bonus_condition']=='1'){

						// $this->promorules->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
						// 	$adminUserId, $playerPromo->depositAmount, null, $playerpromoId);

					// }else{

						// $this->promorules->approvePromo($playerId, $promorule, $promoCmsSettingId,
						// 	$adminUserId, $playerPromo->depositAmount, null, $playerpromoId);

						//should checkfirst
						// $result=$this->promorules->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId);

					// }

					//log admin action
					$this->saveAction(self::MANAGEMENT_TITLE, 'Approve Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully approved player promo :".$playerpromoId);

					if($success){
						$success = $this->endTransWithSucc();
					}else{
						//false rollback
						$this->rollbackTrans();
						$this->utils->error_log('rollback approvePrePromo playerpromoId:', $playerpromoId);
					}

					// $this->group_level->endTrans();
					// if ($this->group_level->isErrorInTrans()) {
					// 	//show error
					// 	$this->alertMessage(1, lang('save.failed'));
					// }
					// $success = $this->endTransWithSucc();
				} finally {
					// release it
					$rlt = $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
					// $rlt = $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
					// $rlt = $this->player_model->transReleaseLock($trans_key);
					$this->utils->debug_log('release admin promo lock', $playerId, $rlt);
				}
			}
		}

		if ($success && $lock_it) {
			//Promo application has been approved!
			$message = lang('cms.promoApproved');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');
	}

	//Deprecated by curtis
	public function expire_promo(){
    /*
		$this->load->model(array('player_promo', 'promorules'));

		$playerPromoId = $this->input->post('expirePlayerPromoId');
		$adminUserId = $this->authentication->getUserId();
		$reason = $this->input->post('reasonToExpire');
		// $playerpromodata = array(
		// 	'playerpromoId' => $this->input->post('declinePlayerPromoId'),
		// 	'declinedApplicationReason' => $this->input->post('reasonToCancel'),
		// 	'dateApplicationDeclined' => date('Y-m-d H:i:s'),
		// 	'processedBy' => $this->authentication->getUserId(),
		// 	'transactionStatus' => Player_promo::TRANS_STATUS_DECLINED,
		// 	'dateProcessed' => $this->utils->getNowForMysql(),
		// );
		// $this->player_promo->processPromoApplication($playerpromodata);
		$success=false;
		if(!empty($playerPromoId)){

			$this->startTrans();

			$playerPromo=$this->player_promo->getPlayerPromo($playerPromoId);
			$promorule=$this->promorules->getPromoRuleRow($playerPromo->promorulesId);
			$promoCmsSettingId=$playerPromo->promoCmsSettingId;
			$playerId=$playerPromo->playerId;
			$bonusAmount=null;
			$depositAmount = null;
			$withdrawConditionAmount=null;
			$betTimes=null;
			// $reason = null;

			$success=!!$this->promorules->expirePromo($playerId, $promorule, $promoCmsSettingId, $adminUserId,
				$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes, $reason, $playerPromoId);

			// $this->promorules->expirePromo(null, null, null, null,
			// 	$adminUserId, $reason, $playerPromoId);

			$this->saveAction(self::MANAGEMENT_TITLE, 'Expire Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully expire player promo application request.");

			if($success){
				$success = $this->endTransWithSucc();
			}else{
				//false rollback
				$this->rollbackTrans();
				$this->utils->error_log('rollback expire_promo playerpromoId:', $playerpromoId);
			}
			// $success=$this->endTransWithSucc();
		}

		if ($success) {
			//Promo application has been approved!
			$message = lang('Set this promotion to expired');
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');
    */
	}

	public function finish_promo($playerpromoId){

		$this->load->model(array('promorules', 'player_promo'));

		$success=false;
		if($playerpromoId){
			$playerPromo = $this->player_promo->getPlayerPromo($playerpromoId);
			$promoCmsSettingId = $playerPromo->promoCmsSettingId;
			$playerId = $playerPromo->playerId;

	        $lockedKey=null;
			$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
			// $lock_it = $this->lockActionById($playerId, Utils::LOCK_ACTION_BALANCE);
			$this->utils->debug_log('lock admin promo', $playerId, $lock_it);

			$success = $lock_it;
			$extra_info=['release_to_real'=>true];
			$result=null;
			if ($lock_it) {
				//lock success
				try {

					$this->startTrans();
					$adminUserId = $this->authentication->getUserId();

					$reason=null;
					// $playerPromo=$this->player_promo->getPlayerPromo($playerpromoId);
					$promorule=$this->promorules->getPromoRuleRow($playerPromo->promorulesId);
					// $promoCmsSettingId=$playerPromo->promoCmsSettingId;
					// $playerId=$playerPromo->playerId;
					$bonusAmount=null;
					$depositAmount = null;
					$withdrawConditionAmount=null;
					$betTimes=null;

					// $promotion_rules=$this->utils->getConfig('promotion_rules');
					//default is false
					// $release_on_admin_approve=$promotion_rules['release_on_admin_approve'];

					// if($release_on_admin_approve){

					$success=!!$this->promorules->finishPromo($playerId, $promorule, $promoCmsSettingId,
							$adminUserId, $bonusAmount, $depositAmount,$withdrawConditionAmount, $betTimes,
							$playerpromoId, $extra_info, $reason);

					// $success=$result['success'];
					// }else{

						// $this->promorules->approvePromoWithoutRelease($playerId, $promorule, $promoCmsSettingId,
							// $adminUserId, $playerPromo->depositAmount, null, $playerpromoId);

						//should checkfirst
						// $result=$this->promorules->checkAndProcessPromotion($playerId, $promorule, $promoCmsSettingId);

					// }


					//log admin action
					$this->saveAction(self::MANAGEMENT_TITLE, 'Finish Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully approved player promo :".$playerpromoId);

					if($success){
						$success = $this->endTransWithSucc();
					}else{
						//false rollback
						$this->rollbackTrans();
						$this->utils->error_log('rollback finishPromo playerpromoId:', $playerpromoId);
					}

					// $this->group_level->endTrans();
					// if ($this->group_level->isErrorInTrans()) {
					// 	//show error
					// 	$this->alertMessage(1, lang('save.failed'));
					// }
					// $success = $this->endTransWithSucc();
				} finally {
					// release it
					$rlt = $this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
					// $rlt = $this->releaseActionById($playerId, Utils::LOCK_ACTION_BALANCE);
					// $rlt = $this->player_model->transReleaseLock($trans_key);
					$this->utils->debug_log('release admin promo lock', $playerId, $rlt);
				}
			}
		}

		if($success){
			// $result['message'].=' '.lang($message);
		}else{
			// $result['success']=false;
			// $result['message'].=' '.lang($message);
			//don't show error, only show success
			$this->utils->debug_log('apply promotion failed', $success, $playerId, $playerpromoId);
			// $message=null;
		}

		if ($success && $lock_it) {
			//Promo application has been approved!
			$message = lang('Set to Finished');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => true, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerpromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');

	}

	/**
	 *
	 *
	 *
	 * @return	redered template
	 */
	public function decline_player_promo() {
		$this->load->model(array('player_promo', 'promorules'));

		$playerPromoId = $this->input->post('declinePlayerPromoId');
        $playerPromoStatus = $this->player_promo->getPlayerPromoStatusById($playerPromoId);
        $allow_decline_player_promo = in_array($playerPromoStatus, [PLAYER_PROMO::TRANS_STATUS_REQUEST,
                                                                    Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
                                                                    Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS]);

		$success=false;
		if(!empty($playerPromoId) && $allow_decline_player_promo){
			$this->startTrans();

			$adminId = $this->authentication->getUserId();
			$reason = $this->input->post('reasonToCancel');

			// $this->promorules->declinePromo(null, null, null, null,
			// 	$adminUserId, $reason, $playerPromoId);

			$playerPromo=$this->player_promo->getPlayerPromo($playerPromoId);
			$promorule=$this->promorules->getPromoRuleRow($playerPromo->promorulesId);
			$promoCmsSettingId=$playerPromo->promoCmsSettingId;
			$playerId=$playerPromo->playerId;
			$bonusAmount=null;
			$depositAmount = null;
			$withdrawConditionAmount=null;
			$betTimes=null;
			// $reason = null;

			$this->promorules->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
				$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
				$reason, $playerPromoId);

			$this->saveAction(self::MANAGEMENT_TITLE, 'Decline Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully declined player promo application request.");

			$success=$this->endTransWithSucc();

		}
		if ($success) {
			//Promo application has been approved!
			$message = lang('Declined this promotion');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => true, 'message'=>$message, 'playerPromoId'=>$playerPromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerPromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');

	}

	//Deprecated by curtis
	public function decline_forever_player_promo(){
    /*
		if(!$this->permissions->checkPermissions('allow_to_delete_declined_promotion')){
			return $this->error_access();
		}


		$this->load->model(array('player_promo', 'promorules'));
		$this->startTrans();

		$adminUserId = $this->authentication->getUserId();
		$playerPromoId = $this->input->post('declineForeverPlayerPromoId');
		$reason = $this->input->post('reasonToDeclineForever');
        $playerPromoStatus = $this->player_promo->getPlayerPromoStatusById($playerPromoId);
        $allow_decline_forever_promo = in_array($playerPromoStatus, [PLAYER_PROMO::TRANS_STATUS_DECLINED]);

		// $playerPromoId = $this->input->post('declinePlayerPromoId');
		// $reason = $this->input->post('reasonToCancel');
		// $playerpromodata = array(
		// 	'playerpromoId' => $this->input->post('declinePlayerPromoId'),
		// 	'declinedApplicationReason' => $this->input->post('reasonToCancel'),
		// 	'dateApplicationDeclined' => date('Y-m-d H:i:s'),
		// 	'processedBy' => $this->authentication->getUserId(),
		// 	'transactionStatus' => Player_promo::TRANS_STATUS_DECLINED,
		// 	'dateProcessed' => $this->utils->getNowForMysql(),
		// );
		// $this->player_promo->processPromoApplication($playerpromodata);
		$success = false;
		if($allow_decline_forever_promo){
			$playerPromo=$this->player_promo->getPlayerPromo($playerPromoId);
			$promorule=$this->promorules->getPromoRuleRow($playerPromo->promorulesId);
			$promoCmsSettingId=$playerPromo->promoCmsSettingId;
			$playerId=$playerPromo->playerId;
			$bonusAmount=null;
			$depositAmount = null;
			$withdrawConditionAmount=null;
			$betTimes=null;
			// $reason = null;

			$this->promorules->declineForeverPromo($playerId, $promorule, $promoCmsSettingId, $adminUserId,
				$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes, $reason, $playerPromoId);

			// $this->player_promo->declinedForeverPlayerPromo($playerPromoId, $adminUserId);

			$this->saveAction(self::MANAGEMENT_TITLE, 'Declined forever Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully deleted player promo application request. on ".$playerPromoId);

			$success=$this->endTransWithSucc();
		}


		if ($success) {
			//Promo application has been approved!
			$message = lang('Declined forever this promotion');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => true, 'message'=>$message, 'playerPromoId'=>$playerPromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
		} else {
			$message = lang('error.default.db.message');
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('success' => false, 'message'=>$message, 'playerPromoId'=>$playerPromoId));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		}

		redirect('marketing_management/promoApplicationList');
    */
	}


	public function manually_add_bonus($playerId=null){
		if (!$this->permissions->checkPermissions('manually_add_bonus')) {
			return $this->error_access();
		}

		if(empty($playerId)){
			return $this->error_access();
		}
		$userId=$this->authentication->getUserId();

		$this->load->model(array('promorules', 'external_system', 'player_model', 'transactions'));
		// $platform_name = $platform_id == '0' ? lang('pay.mainwallt') : $this->external_system->getNameById($platform_id) . ' ' . lang('cashier.42');

		$username=$this->player_model->getUsernameById($playerId);
		$data = array(
			// 'platform_id' => $platform_id,
			// 'platform_name' => $platform_name,
			// 'transaction_type' => $transaction_type,
			'player_id' => $playerId,
			'username' => $username,
		);
		// $data['promoCategory'] = $this->promorules->getAllPromoCategory();
		// $data['promoRules'] = $this->promorules->getAvailablePromoruleList();
		if($this->utils->isEnabledFeature('only_manually_add_active_promotion')){
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();
		}else{
			$data['promoCms'] = $this->promorules->getAllPromoCMSList();
		}
		$dt=new DateTime('-3 months');

		//load all deposit transaction without applying promo
		$data['transaction_list']=$this->transactions->getDepositWithoutApplyingPromoList($playerId, $this->utils->formatDateTimeForMysql($dt), $this->utils->getNowForMysql());

		$data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenFieldForAdmin($userId);

		// $this->session->set_flashdata('prevent_refresh', true);
		// $is_own_page= $is_own_page=='true';

		// $data['return_url']=site_url('/payment_management/manually_add_bonus/'.$playerId);

		// $data['is_own_page'] = $is_own_page ;
		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/view_add_bonus', $data);
		$this->template->render();

	}

	public function post_manually_add_bonus(){
		if (!$this->permissions->checkPermissions('manually_add_bonus')) {
			return $this->error_access();
		}

		$userId=$this->authentication->getUserId();
		$player_id=$this->input->post('player_id');

		if(!$this->verifyAndResetDoubleSubmitForAdmin($userId)){

			$message = lang('Please refresh and try, and donot allow double submit');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('/marketing_management/manually_add_bonus/'.$player_id);
			return;

		}

		$this->load->model(array('transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules','withdraw_condition', 'wallet_model'));

		$player_name=$this->input->post('username');
		// $deposit_amt_condition = $this->input->post('depositAmtCondition') ? $this->input->post('depositAmtCondition') : null;
		if(empty($player_id)){
			$player_id=$this->player_model->getPlayerIdByUsername($player_name);
		}else{
			$player_name=$this->player_model->getUsernameById($player_id);
		}

		$promoCmsSettingId = $this->input->post('promo_cms_id');
		$promoRuleId  = $this->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
		$promorule=$this->promorules->getPromoRuleRow($promoRuleId);
		if(!empty($promoCmsSettingId) && !empty($promorule)){

			$controller=$this;
			$message=lang('Add Bonus Failed');
			$success=$this->lockAndTransForPlayerBalance($player_id, function()
					use($controller, $player_id, $player_name, &$message) {

			$current_timestamp = $controller->utils->getNowForMysql();

			//get logged user
			$adminUserId=$controller->authentication->getUserId();
			$adminUsername=$controller->authentication->getUsername();

			$promoCmsSettingId = $controller->input->post('promo_cms_id');
			$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
			$release_date=$controller->input->post('release_date');

			//set promo category
			$promo_category = null;
			if (!empty($promoRuleId)) {
				$promorule = $controller->promorules->getPromoRuleRow($promoRuleId);
				$promo_category = $promorule['promoCategory'];
			}

			$action_name = 'Add';
			$adjustment_type = Transactions::ADD_BONUS;

			$status = $controller->input->post('status');
			$amount = $controller->input->post('amount');
			$betTimes = $controller->input->post('betTimes');
			$deposit_amt_condition=$controller->input->post('depositAmtCondition');
			$reason=$controller->input->post('reason');
			$show_in_front_end=$controller->input->post('show_in_front_end');
			// $withdraw_condition_amount=$deposit_amount * $betTimes;
			$deductDeposit = $controller->input->post('deductDeposit');
			if ($deductDeposit) {
				$condition = (($amount + $deposit_amt_condition) * $betTimes) - $deposit_amt_condition;
				$condition_desc='(bonus '.$amount.' + deposit '.$deposit_amt_condition.') x '.$betTimes.' - depost '.$deposit_amt_condition;
			} else {
				$condition = ($amount + $deposit_amt_condition) * $betTimes;
				$condition_desc='(bonus '.$amount.' + deposit '.$deposit_amt_condition.') x '.$betTimes;
			}

			if($condition <= 0){
                $condition = 0;
            }

			$transaction_id=$this->input->post('transaction_id');

			$note = 'add bonus '.number_format($amount, 2).' to '.$player_name.' by '.$adminUsername.', with deposit condition of ' . $condition;

			#if want pending, don't create transaction, only create player promo
			if($status == Player_promo::TRANS_STATUS_REQUEST ){
				// request promo
				$player_promo_id=$controller->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, null, $deposit_amt_condition,
					$condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );

				if(!empty($player_promo_id) && !empty($transaction_id)){
					$controller->transactions->updatePlayerPromoId($transaction_id, $player_promo_id, $promo_category);
				}

				$success=!empty($player_promo_id);

				if(!$success){
					$message=lang('Request Promotion Failed');
				}else{
					$message=lang('Request Promotion Successfully');
				}
			}else{
				$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
					$adminUserId, $player_id, $amount, null, $note, null,
					$promo_category, $show_in_front_end, $reason, $promoRuleId, null, Transactions::MANUALLY_ADJUSTED);

				$success=!!$transaction;
				if($success){
					$this->payment_manager->addPlayerBalAdjustmentHistory(array(
						'playerId' => $transaction['to_id'],
						'adjustmentType' => $transaction['transaction_type'],
						'walletType' => 0, # 0 - MAIN WALLET
						'amountChanged' => $transaction['amount'],
						'oldBalance' => $transaction['before_balance'],
						'newBalance' => $transaction['after_balance'],
						'reason' => $reason,
						'adjustedOn' => $transaction['created_at'],
						'adjustedBy' => $transaction['from_id'],
						// 'show_flag' => $show_in_front_end == '1',
					));

					//save to player promo
					$playerBonusAmount = $amount;
					$extra_info=[];
					$player_promo_id = $controller->player_promo->approvePromoToPlayer($player_id, $promoRuleId, $playerBonusAmount,
						$promoCmsSettingId, $adminUserId, null, $condition, $extra_info, $deposit_amt_condition, $betTimes,$reason );

					//move to withdraw_condition
					$promorule=$this->promorules->getPromoruleById($promoRuleId);
					$bonusTransId=$transaction['id'];
					$controller->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
							$condition, $deposit_amt_condition, $amount, $betTimes, $promorule,$reason, $player_promo_id);

					//update player promo id of transaction
					$controller->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);
					$controller->transactions->updatePlayerPromoId($transaction_id, $player_promo_id, $promo_category);
					// }
					$success=true;
					$message=lang('Add Bonus Successfully');
				}else{
					$message=lang('Add Bonus Failed');
				}
			}

			if(!empty($player_promo_id)){
                //add requestAdmin
                $this->player_promo->addPlayerPromoRequestBy($player_promo_id, $adminUserId, null);
            }

			return $success;

			});
		}else{
			$success=false;
			$message=lang('lost promo id');
		}

		$this->saveAction(self::MANAGEMENT_TITLE, 'Add Bonus', $message);

		if($success){
			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
			redirect('/player_management/userInformation/'.$player_id);
		}else{
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('/marketing_management/manually_add_bonus/'.$player_id);
		}

	}

	public function manually_batch_bonus(){
		if (!$this->permissions->checkPermissions('manually_batch_bonus')) {
			return $this->error_access();
		}

		$this->load->model(array('promorules', 'external_system', 'player_model'));
		// $platform_name = $platform_id == '0' ? lang('pay.mainwallt') : $this->external_system->getNameById($platform_id) . ' ' . lang('cashier.42');

		// $username=$this->player_model->getUsernameById($playerId);
		$data = array(
			// 'platform_id' => $platform_id,
			// 'platform_name' => $platform_name,
			// 'transaction_type' => $transaction_type,
			// 'player_id' => $playerId,
			// 'username' => $username,
		);
		// $data['promoCategory'] = $this->promorules->getAllPromoCategory();
		// $data['promoRules'] = $this->promorules->getAvailablePromoruleList();
		if($this->utils->isEnabledFeature('only_manually_add_active_promotion')){
			$data['promoCms'] = $this->promorules->getAvailablePromoCMSList();
		}else{
			$data['promoCms'] = $this->promorules->getAllPromoCMSList();
		}

		// $this->session->set_flashdata('prevent_refresh', true);
		// $is_own_page= $is_own_page=='true';

		// $data['return_url']=site_url('/payment_management/manually_add_bonus/'.$playerId);

		// $data['is_own_page'] = $is_own_page ;
		$allowed_csv_max_size = ' <= 10mb';
		$upload_promo_csv_max_row = $this->utils->getConfig('upload_promo_csv_max_row');
		$data['csv_note'] = sprintf(lang("%s size of csv could be uploaded."), $allowed_csv_max_size);
		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
		$this->template->write_view('sidebar', 'marketing_management/sidebar');
		$this->template->write_view('main_content', 'marketing_management/view_batch_add_bonus', $data);
		$this->template->render();

	}

	// public function post_manually_batch_bonus(){
	// 	if (!$this->permissions->checkPermissions('manually_batch_bonus')) {
	// 		return $this->error_access();
	// 	}

	// 	$this->load->model(array('transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules','withdraw_condition', 'wallet_model'));

	// 	$path='/tmp';
	// 	$random_csv=random_string('unique').'.csv';

	// 	$config['upload_path'] = $path;
	// 	$config['allowed_types'] = '*';
	// 	$config['max_size'] = $this->utils->getMaxUploadSizeByte();
	// 	$config['remove_spaces'] = true;
	// 	$config['overwrite'] = true;
	// 	$config['file_name'] = $random_csv;
	// 	$config['max_width'] = '';
	// 	$config['max_height'] = '';
	// 	$this->load->library('upload', $config);
	// 	$this->upload->initialize($config);

	// 	if ($this->upload->do_upload('csv_file')) {
	// 		$success=true;

	// 		$csv_file_data = $this->upload->data();

	// 		//get the highest line
	// 		$file = new SplFileObject($csv_file_data['full_path'], 'r');
	// 		$file->seek(PHP_INT_MAX);
	// 		$row_count =  $file->key() - 1; //remove the first line
	// 		$upload_promo_csv_max_row = $this->utils->getConfig('upload_promo_csv_max_row');
	// 		$file = null;//close file
	// 		if($row_count > $upload_promo_csv_max_row){
	// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, sprintf(lang("Only %s rows allowed. Please check the csv file."), $upload_promo_csv_max_row));
	// 			redirect('/marketing_management/manually_batch_bonus');
	// 		}
	// 		//process cvs file
	// 		$this->utils->debug_log('upload csv_file_data', $csv_file_data);


	// 		$message=lang('Add Bonus Failed');
	// 		$ignore_first_row = true;
	// 		$cnt = 0;
	// 		$success_amount = 0;
	// 		$success_usernames = [];
	// 		$failed_usernames = [];
	// 		$controller=$this;

	// 		$this->utils->loopCSV($csv_file_data['full_path'], $ignore_first_row, $cnt, $message,
	// 		function($cnt, $tmpData, $stop_flag) use($controller, &$message, &$success_amount, &$success_usernames, &$failed_usernames){
	// 			//process one row
	// 			$success=false;

	// 			$this->utils->debug_log('==============post_manually_batch_bonus->tmpData', $tmpData);

	// 			$player_name = $tmpData[0];
	// 			$amount = $tmpData[1];
	// 			$betTimes = $tmpData[2];
	// 			$condition = $tmpData[3];
	// 			$deposit_amt_condition = $tmpData[4];

	// 			$player_id=$this->player_model->getPlayerIdByUsername($player_name);

	// 			if(empty($player_id)){
	// 				$this->utils->debug_log('==============player not exist', $player_name);
	// 				$failed_usernames[$player_name] = sprintf(lang('gen.error.not_exist'), $player_name);
	// 				return false;
	// 			}

	// 			$success=$this->lockAndTransForPlayerBalance($player_id, function()
	// 			use($controller, $player_id, $player_name, $amount, $betTimes, $condition, $deposit_amt_condition, &$message) {

	// 				$current_timestamp = $controller->utils->getNowForMysql();

	// 				//get logged user
	// 				$adminUserId=$controller->authentication->getUserId();
	// 				$adminUsername=$controller->authentication->getUsername();

	// 				$promoCmsSettingId = $controller->input->post('promo_cms_id');
	// 				$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
	// 				$release_date=$controller->input->post('release_date');

	// 				//set promo category
	// 				$promo_category = null;
	// 				if (!empty($promoRuleId)) {
	// 					$promorule = $controller->promorules->getPromoRuleRow($promoRuleId);
	// 					$promo_category = $promorule['promoCategory'];
	// 				}

	// 				$action_name = 'Add';
	// 				$adjustment_type = Transactions::ADD_BONUS;

	// 				$status = $controller->input->post('status');
	// 				$reason=$controller->input->post('reason');
	// 				$show_in_front_end=$controller->input->post('show_in_front_end');

	// 				$note = 'add bonus '.number_format($amount, 2).' to '.$player_name.' by '.$adminUsername.', with deposit condition of ' . $condition;

	// 				#if want pending, don't create transaction, only create player promo
	// 				if($status == Player_promo::TRANS_STATUS_REQUEST ){
	// 					// request promo
	// 					$success=!!$controller->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $adminUserId, $deposit_amt_condition,
	// 						$condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );
	// 					if(!$success){
	// 						$message=lang('Request Promotion Failed');
	// 					}else{
	// 						$message=lang('Request Promotion Successfully');
	// 					}
	// 				}else{

	// 					$this->utils->debug_log('==============post_manually_batch_bonus->before_transaction', 'test');

	// 					$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
	// 						$adminUserId, $player_id, $amount, null, $note, null,
	// 						$promo_category, $show_in_front_end, $reason, $promoRuleId, null, Transactions::MANUALLY_ADJUSTED);

	// 					$this->utils->debug_log('==============post_manually_batch_bonus->transaction', $transaction);

	// 					$success=!!$transaction;
	// 					if($success){
	// 						$this->payment_manager->addPlayerBalAdjustmentHistory(array(
	// 							'playerId' => $transaction['to_id'],
	// 							'adjustmentType' => $transaction['transaction_type'],
	// 							'walletType' => 0, # 0 - MAIN WALLET
	// 							'amountChanged' => $transaction['amount'],
	// 							'oldBalance' => $transaction['before_balance'],
	// 							'newBalance' => $transaction['after_balance'],
	// 							'reason' => $reason,
	// 							'adjustedOn' => $transaction['created_at'],
	// 							'adjustedBy' => $transaction['from_id'],
	// 							// 'show_flag' => $show_in_front_end == '1',
	// 						));

	// 						//move to withdraw_condition
	// 						$promorule=$this->promorules->getPromoruleById($promoRuleId);
	// 						$bonusTransId=$transaction['id'];
	// 						$withdrawalConditionId = $controller->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
	// 							$condition, $deposit_amt_condition, $amount, $betTimes, $promorule,$reason);

	// 						//save to player promo
	// 						$playerBonusAmount = $amount;
	// 						$extra_info=[];
	// 						$player_promo_id = $controller->player_promo->approvePromoToPlayer($player_id, $promoRuleId, $playerBonusAmount,
	// 							$promoCmsSettingId, $adminUserId, null, $condition, $extra_info, $deposit_amt_condition, $betTimes,$reason );

 //                            //add requestAdmin
 //                            $controller->player_promo->addPlayerPromoRequestBy($player_promo_id, $adminUserId, null);

	// 						//update player promo id of transaction
	// 						$controller->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);

	// 						//update player promo id of withdraw_condition
	// 						$controller->withdraw_condition->updatePlayerPromoId($withdrawalConditionId, $player_promo_id);

	// 						// }
	// 						$success=true;
	// 						$message=lang('Add Bonus Successfully');
	// 					}else{
	// 						$message=lang('Add Bonus Failed');
	// 					}
	// 				}

	// 				return $success;

	// 			});

	// 			if ($success) {
	// 				$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($controller->input->post('promo_cms_id'));
	// 				if (!empty($promoRuleId)) {
	// 					$gameAPI = $controller->promorules->getPromoRuleRow($promoRuleId)['releaseToSubWallet'];
	// 					$main_wallet_id = 0;

	// 					/** check if promo rule release bonus to sub-wallet */
	// 					if (!empty($gameAPI)) {
	// 						if (!$this->external_system->isGameApiActive($gameAPI)) {
	// 							$this->utils->debug_log('============== API is not active. Cant transfer to subwallet', 'warning');
	// 						} else {
	// 							/** Transfer bonus from main wallet sub wallet */
	// 							$transferResult = $this->utils->transferWallet($player_id, $player_name, $main_wallet_id, $gameAPI, $amount);
	// 							$this->utils->debug_log('result of transfer wallet', $transferResult);
	// 						}
	// 					}
	// 				}
	// 			}

	// 			if($success){
	// 				$success_amount += $amount;
	// 				$success_usernames[$player_name] = lang('Transfer Success');
	// 			}else{
	// 				$failed_usernames[$player_name] = lang('Transfer Failed');
	// 			}

	// 			return $success;
	// 		});

	// 	}else{
	// 		//failed
	// 		$success=false;
	// 		$message=lang('Upload CSV File Failed')."\n".$this->upload->display_errors();
	// 	}

	// 	// $this->saveAction(self::MANAGEMENT_TITLE, 'Add Bonus', $message);

	// 	if($success){

	// 		$this->utils->debug_log('==============post_manually_batch_bonus->success');

	// 		$data['success_amount'] = $success_amount;
	// 		$data['success_count'] = count($success_usernames);
	// 		$data['failed_count'] = count($failed_usernames);
	// 		$data['failed_usernames'] = $failed_usernames;
	// 		$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
	// 		$this->template->write_view('sidebar', 'marketing_management/sidebar');
	// 		$this->template->write_view('main_content', 'marketing_management/promorules/post_manually_batch_bonus_result', $data);
	// 		$this->template->render();

	// 		$this->utils->debug_log('==============post_manually_batch_bonus->render');
	// 	}else{
	// 		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
	// 		redirect('/marketing_management/manually_batch_bonus');
	// 	}

	// }

	public function post_manually_batch_bonus(){
		if (!$this->permissions->checkPermissions('manually_batch_bonus')) {
			return $this->error_access();
		}

		$this->load->model(array('transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules','withdraw_condition', 'wallet_model'));

		$path='/tmp';
		$random_csv=random_string('unique').'.csv';

		$config['upload_path'] = $path;
		$config['allowed_types'] = '*';
		$config['max_size'] = $this->utils->getMaxUploadSizeByte();
		$config['remove_spaces'] = true;
		$config['overwrite'] = true;
		$config['file_name'] = $random_csv;
		$config['max_width'] = '';
		$config['max_height'] = '';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		$do_run = $this->upload->do_upload('csv_file');

		if ($do_run) {

			$csv_file_data = $this->upload->data();

			//process cvs file
			$this->utils->debug_log('upload csv_file_data', $csv_file_data);

			//not allow excel
			$file_ext=$csv_file_data['file_ext'];
			$not_allow_types=['xsl', 'xslx'];

			if(in_array($file_ext, $not_allow_types)){
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Note: Upload file format must be CSV'));
				return redirect('/marketing_management/manually_batch_bonus');
			}
		}

		if($do_run){

			//get logged user
	 		$admin_user_id=$this->authentication->getUserId();

			$csv_fullpath=$csv_file_data['full_path'];
			// $path_parts=pathinfo($csv_fullpath);
			$csv_filename=$csv_file_data['client_name'];
			$exists=false;

			//write to history, lock all
			$success=$this->lockAndTrans(Utils::LOCK_ACTION_BATCH_ADD_BONUS, 0, function()
				use($admin_user_id, $csv_fullpath, $csv_filename, &$exists){

					$success=$this->transactions->checkAndAddUploadCSVHistory($admin_user_id,$csv_filename, $csv_fullpath,
						Transactions::CSV_TYPE_BATCH_ADD_BONUS, $exists);

					return $success;

				});

			if($exists){
				$message=lang('Cannot upload duplicate csv file').' '.$csv_filename;
				//block it
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('/marketing_management/manually_batch_bonus');
			}

			if(!$success){
				$message=lang('Write upload history failed');
				//block it
				$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
				redirect('/marketing_management/manually_batch_bonus');
			}

			$this->load->library(['lib_queue']);
            //add it to queue job
			$callerType=Queue_result::CALLER_TYPE_ADMIN;
			$caller=$this->authentication->getUserId();
			$state='';

			//$charset_code = intval($this->input->post('charset_code'));
			$charset_code = 2;

            //copy file to sharing private
			$success=$this->utils->copyFileToSharingPrivate($csv_file_data['full_path'], $target_file_path, $charset_code);

			$this->utils->debug_log($csv_file_data['full_path'].' to '.$target_file_path, $success);

			if($success){

				$promoCmsSettingId = intval($this->input->post('promo_cms_id'));
				$release_date=$this->input->post('release_date');// not used
				$status = intval($this->input->post('status'));
				$reason=$this->input->post('reason');
                $show_in_front_end=false; //$this->input->post('show_in_front_end')=='1'; //not used

                $token=$this->lib_queue->addRemoteBatchAddBonusJob(basename($target_file_path),$promoCmsSettingId,
                	$release_date, $status, $reason, $show_in_front_end, $callerType, $caller, $state);

                $success=!empty($token);
                if(!$success){
                	$message=lang('Create batch job failed');
                }else{
                    //redirect to queue
                	redirect('/marketing_management/manually_batch_add_bonus_result/'.$token);
                }
            }else{
            	$message=lang('Copy file failed');
            }

            if(!$success){
            	$message=lang('Add Bonus Failed');
            	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            	redirect('/marketing_management/manually_batch_bonus');
            }

        }else{
            //failed
        	$success=false;
        	$message=lang('Upload CSV Failed')."\n".$this->upload->display_errors();
        }

        if($success){

        	$this->utils->debug_log('==============post_manually_batch_bonus->success');

        	$data['success_amount'] = $success_amount;
        	$data['success_count'] = count($success_usernames);
        	$data['failed_count'] = count($failed_usernames);
        	$data['failed_usernames'] = $failed_usernames;
        	$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
        	$this->template->write_view('sidebar', 'marketing_management/sidebar');
        	$this->template->write_view('main_content', 'marketing_management/promorules/post_manually_batch_bonus_result', $data);
        	$this->template->render();

        	$this->utils->debug_log('==============post_manually_batch_bonus->render');
        }else{
        	$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        	redirect('/marketing_management/manually_batch_bonus');
        }

    }

    public function manually_batch_add_bonus_result($token){
    	$data['result_token']=$token;
    	$this->loadTemplate(lang('Marketing Management'), '', '', 'marketing');
    	$this->template->write_view('sidebar', 'marketing_management/sidebar');
    	$this->template->write_view('main_content', 'marketing_management/manually_batch_bonus_result', $data);
    	$this->template->render();
    }

	public function addBonusToPlayer($player_id, $player_name){
		 $controller=$this;

		$success=$this->lockAndTransForPlayerBalance($player_id, function()
		use($controller, $player_id, $player_name, &$message) {

			$current_timestamp = $controller->utils->getNowForMysql();

			//get logged user
			$adminUserId=$controller->authentication->getUserId();
			$adminUsername=$controller->authentication->getUsername();

			$promoCmsSettingId = $controller->input->post('promo_cms_id');
			$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
			$release_date=$controller->input->post('release_date');

			//set promo category
			$promo_category = null;
			if (!empty($promoRuleId)) {
				$promorule = $controller->promorules->getPromoRuleRow($promoRuleId);
				$promo_category = $promorule['promoCategory'];
			}

			$action_name = 'Add';
			$adjustment_type = Transactions::ADD_BONUS;

			$status = $controller->input->post('status');
			$amount = $controller->input->post('amount');
			$betTimes = $controller->input->post('betTimes');
			$deposit_amt_condition=$controller->input->post('depositAmtCondition');
			$reason=$controller->input->post('reason');
			$show_in_front_end=$controller->input->post('show_in_front_end');
			// $withdraw_condition_amount=$deposit_amount * $betTimes;
			$deductDeposit = $controller->input->post('deductDeposit');
			if ($deductDeposit) {
				$condition = (($amount + $deposit_amt_condition) * $betTimes) - $deposit_amt_condition;
				$condition_desc='(bonus '.$amount.' + deposit '.$deposit_amt_condition.') x '.$betTimes.' - depost '.$deposit_amt_condition;
			} else {
				$condition = ($amount + $deposit_amt_condition) * $betTimes;
				$condition_desc='(bonus '.$amount.' + deposit '.$deposit_amt_condition.') x '.$betTimes;
			}

			$note = 'add bonus '.number_format($amount, 2).' to '.$player_name.' by '.$adminUsername.', with deposit condition of ' . $condition;

			#if want pending, don't create transaction, only create player promo
			if($status == Player_promo::TRANS_STATUS_REQUEST ){
				// request promo
				$success=!!$controller->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $adminUserId, $deposit_amt_condition,
					$condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );
				if(!$success){
					$message=lang('Request Promotion Failed');
				}else{
					$message=lang('Request Promotion Successfully');
				}
			}else{
				$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
					$adminUserId, $player_id, $amount, null, $note, null,
					$promo_category, $show_in_front_end, $reason, $promoRuleId);

				$success=!!$transaction;
				if($success){
					$this->payment_manager->addPlayerBalAdjustmentHistory(array(
						'playerId' => $transaction['to_id'],
						'adjustmentType' => $transaction['transaction_type'],
						'walletType' => 0, # 0 - MAIN WALLET
						'amountChanged' => $transaction['amount'],
						'oldBalance' => $transaction['before_balance'],
						'newBalance' => $transaction['after_balance'],
						'reason' => $reason,
						'adjustedOn' => $transaction['created_at'],
						'adjustedBy' => $transaction['from_id'],
						// 'show_flag' => $show_in_front_end == '1',
					));

					//move to withdraw_condition
					$promorule=$this->promorules->getPromoruleById($promoRuleId);
					$bonusTransId=$transaction['id'];
					$controller->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
						$condition, $deposit_amt_condition, $amount, $betTimes, $promorule,$reason);

					//save to player promo
					$playerBonusAmount = $amount;
					$extra_info=[];
					$player_promo_id = $controller->player_promo->approvePromoToPlayer($player_id, $promoRuleId, $playerBonusAmount,
						$promoCmsSettingId, $adminUserId, null, $condition, $extra_info, $deposit_amt_condition, $betTimes,$reason );
					//update player promo id of transaction
					$controller->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);
					// }
					$success=true;
					$message=lang('Add Bonus Successfully');
				}else{
					$message=lang('Add Bonus Failed');
				}
			}

			return $success;

		});
	}


}
////END OF FILE/////////