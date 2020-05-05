<?php

/**
 * Class promo_module
 *
 * General behaviors include :
 *
 * * Request and show promo details
 * * Promo history
 * * Pickup bonus
 * * Player bonus
 * * Transfer promotion
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait promo_module {

	/**
	 * overview : pre application
	 *
	 * @param string	$promoCode
	 */
	public function preapplication($promoCode){
		$this->load->model(array('promorules'));
		$data['promorulesCmsDetails'] = $this->promorules->getPromoCmsDetailsByPromoCode($promoCode);
		$promoCmsSettingId=@$data['promorulesCmsDetails'][0]['promoCmsSettingId'];
		return $this->request_promo($promoCmsSettingId, 0, 'true');
	}

	public function addtopromo($promoCode){
		$this->load->model(array('promorules'));
		$data['promorulesCmsDetails'] = $this->promorules->getPromoCmsDetailsByPromoCode($promoCode);
		$promoCmsSettingId=@$data['promorulesCmsDetails'][0]['promoCmsSettingId'];
		return $this->request_promo($promoCmsSettingId);
	}

	/**
	 * overview : pre application promo
	 *
	 * @param int	$promoCmsSettingId
	 */
	public function preapplicationPromo($promoCmsSettingId){

		$this->request_promo($promoCmsSettingId, 0, 'true');

	}

	/**
	 * overview : request promo
	 *
	 * @param int	$promoCmsSettingId
	 * @param int 	$action
	 * @param null $preapplication
	 */
	public function request_promo($promoCmsSettingId, $action = 0, $preapplication=null, $is_api_call = false, $ret_to_api = false, $playerId = null) {

		$this->load->model(array('promorules', 'player_promo', 'player_model'));

		if (empty($playerId)) {
			$playerId = $this->authentication->getPlayerId();
		}
		$player=$this->player_model->getPlayerArrayById($playerId);
		if($playerId && $player['disabled_promotion']==1){
			// $message = 'error.default.message';
			$message = 'Promo disabled for player';
			if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_DISABLED_PROMOTION, 'message' => lang($message)]);
                return;
            }
            else if (!empty($ret_to_api)) {
            	return [ 'success' => false, 'code' => Api_common::CODE_DISABLED_PROMOTION, 'message' => lang($message) ];
            }
            elseif ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => lang($message)));
                return;
            }
            else {
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang($message));
                return $this->goPlayerPromotions();
            }
		}

		// $isProcessPendingPromoRequestFlag = false;
		// if ($playerId && $action == Player_promo::TRANS_STATUS_REQUEST) {
		// 	$isProcessPendingPromoRequestFlag = true;
		// }
		//get playerid
		$promorule = $this->promorules->getPromoruleByPromoCms($promoCmsSettingId);

		$promorulesId = $promorule['promorulesId'];

        $preapplication = ($preapplication === NULL) ? ((1 != (int)$promorule['disabled_pre_application']) ? TRUE : FALSE) : FALSE;


		// $promorule = $this->promorules->getPromoRuleRow($promorulesId);
		// $promorule = $promoDetails[0];
		$success = false;
		$message = 'error.default.message';
		$this->utils->debug_log('is action: ', $action, ' promorulesId: ', $promorulesId, ' promoCmsSettingId: ', $promoCmsSettingId);

		if ($action == Player_promo::TRANS_STATUS_DECLINED_FOREVER) {

			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
			// $lock_it = $this->lockPlayerBalance($playerId);
			$success = $lock_it;
			if ($lock_it) {
				try{
					$this->startTrans();
					$success = $this->player_promo->declinedForeverPromoToPlayer($playerId, $promorulesId, 0, $promoCmsSettingId,
						null, null, null, Player_promo::TRANS_STATUS_DECLINED_FOREVER);
					$message = 'Promo has been declined forever!';
					$transSucc = $this->endTransWithSucc();
					$success = $success && $transSucc;
				} finally {
					// release it
					// $rlt = $this->releaseActionById($playerId, 'promo');
					// $rlt = $this->releasePlayerBalance($playerId);
					$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
					// $rlt = $this->player_model->transReleaseLock($trans_key);
					$this->utils->debug_log('release promo lock', $playerId, 'ruleId', $promorulesId, $rlt);
				}
			}

		} else {
			if (!empty($playerId) && !empty($promorule)) {
				$this->utils->debug_log('playerId', $playerId, 'promorule', $promorule['promorulesId'], 'promoCmsSettingId', $promoCmsSettingId);

				//should check declined forever
				if ($this->player_promo->isDeclinedForever($playerId, $promorulesId)) {
					$success = false;
					$message = 'Sorry, promo application has been declined';

				}
				else {

					$extra_info=null;
					$ruleId = $promorulesId;
					//lock it
					// $lock_it = $this->lockActionById($playerId, 'promo');
					// $lock_it = $this->lockPlayerBalance($playerId);
					$lockedKey=null;
					$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
					$this->utils->debug_log('lock promo', $playerId, 'ruleId', $ruleId, $lock_it);
					$success = $lock_it;

					if ($lock_it) {
						//lock success
						try {
							$this->startTrans();

							list($success, $message) = $this->promorules->triggerPromotionFromManualPlayer(
								$playerId, $promorule, $promoCmsSettingId, $preapplication=='true',
								null, $extra_info);

							$transSucc = $this->endTransWithSucc();
							$success = $success && $transSucc;
							// if ($promorule['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
							// 	list($success, $message) = $this->processDepositPromo($playerId, $promorule, $promoCmsSettingId);
							// } else {
							// 	list($success, $message) = $this->processNonDepositPromo($playerId, $promorule, $promoCmsSettingId);
							// }
						} finally {
							// release it
							// $rlt = $this->releaseActionById($playerId, 'promo');
							// $rlt = $this->releasePlayerBalance($playerId);
							$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
							// $rlt = $this->player_model->transReleaseLock($trans_key);
							$this->utils->debug_log('release promo lock', $playerId, 'ruleId', $ruleId, $rlt);
						}
					}

					// if (!$success) {
						//DB transaction failed
						// $success = false;
						// $message = $message; //'error.default.message';
						// $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
					// }

					// Directly release to sub-wallet
					if($success && isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])) {
						$releaseToSubWallet=$extra_info['releaseToSubWallet'];

						$ignore_promotion_check=true;
						$this->utils->debug_log('releaseToSubWallet player:'.$playerId, $releaseToSubWallet);
						//transfer to subwallet
						$bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
							$releaseToSubWallet['playerName'],
							$releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
							$releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
							$releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount'],
							$ignore_promotion_check);

						//move transfer amount to real_for_bonus

						if(!$bonusResult['success']){
							$this->utils->error_log('transfer bonus failed', $bonusResult );
						}elseif(isset($extra_info['player_promo_request_id'])){
                            $this->promorules->updatePromoId($promorule, $bonusResult['transferTransId'], $extra_info['player_promo_request_id']);
                        }
						// if(isset($bonusResult['success'])){
						// 	$result['message'].=' '. $message;
						// }else{
						// 	$result['message'].=' '. $message;
						// }
					}
				}
			}
		}

		$this->utils->debug_log(__METHOD__, 'promo request results', [ 'success' => $success, 'message' => $message ] );

		if ($success) {
            if($is_api_call){
                $this->returnJsonResult(['success' => true, 'code' => Api_common::CODE_SUCCESS, 'message' => lang($message)]);
                return;
            }
            else if (!empty($ret_to_api)) {
            	return ['success' => true, 'code' => Api_common::CODE_SUCCESS, 'message' => lang($message) ];
            }
            elseif ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'success', 'msg' => lang($message)));
                return;
            }

		} else {

            if($is_api_call){
                $this->returnJsonResult(['success' => false, 'code' => Api_common::CODE_REQUEST_PROMOTION_FAIL, 'message' => lang($message)]);
                return;
            }
            else if (!empty($ret_to_api)) {
            	return ['success' => false, 'code' => Api_common::CODE_REQUEST_PROMOTION_FAIL, 'message' => lang($message) ];
            }
            elseif ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => lang($message)));
                return;
            }

		}

		$this->alertMessage($success ? self::MESSAGE_TYPE_SUCCESS : self::MESSAGE_TYPE_ERROR, lang($message));

		// $next_url=$this->input->get('next_url');

		// Personal promotion list is suspended
		$this->goPlayerPromotions();
		// (!$success) ? $this->goPlayerPromotions() : $this->goPlayerMyPromotions() ;
	}

	/**
	 * overview : show promo
	 *
	 * @param $promoCode
	 */
	public function show_promo($promoCode) {
		$this->load->model(array('promorules', 'player_promo'));
		$data['promorulesCmsDetails'] = $this->promorules->getPromoCmsDetailsByPromoCode($promoCode);
		$data['promoruleInfo'] = $this->promorules->getPromoruleById($data['promorulesCmsDetails'][0]['promoId']);
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['playerId'] = $this->authentication->getPlayerId();

		$data['request_count'] = $this->player_promo->getDailyPromoRequestByPlayerId( $data['playerId'], $data['promorulesCmsDetails'][0]['promoCmsSettingId']);

		$this->loadTemplate(lang('cashier.promotion'), '', '', '');
		$url = $this->utils->getPlayerCenterTemplate() . '/promotion/promo_cms';
		$this->template->write_view('main_content', $url, $data);
		$this->template->render();
	}

	/**
	 * overview :  promo application from site	to track where it came from
	 *
	 * @param string	$promoCode
	 */
	public function show_promo_from_site($promoCode) {

		// if(!$this->authentication->isLoggedIn()){
		// 	redirect('player_center/iframe_register');
		// }

		$this->load->model(array('promorules'));
		$data['promorulesCmsDetails'] = $this->promorules->getPromoCmsDetailsByPromoCode($promoCode);
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['callFromSite'] = true;
		$data['isPlayerIsLogged'] = $this->authentication->isLoggedIn();
		$this->loadTemplate(lang('cashier.promotion'), '', '', '');
		$url = $this->utils->getPlayerCenterTemplate() . '/promotion/promo_cms_from_site';
		$this->template->write_view('main_content', $url, $data);
		$this->template->render();
	}

	/**
	 * overview : iframe promos
     *
     * @deprecated
     * @see Promotion::index()
	 *
	 * @param int $offset
	 * @param int $limit
	 */
	public function iframe_promos($offset = 0, $limit = 100) {
		$this->load->model(array('promorules', 'player_model'));

		if (!$this->authentication->isLoggedIn()) {
			redirect($this->utils->getSystemUrl('m') . '/promotions.html');
			// return $this->goPlayerRegister();
		}

        redirect('/player_center2/promotion');

		$player_id = $this->authentication->getPlayerId();
		$player = $this->player_model->getPlayerById($player_id);

        $allpromo = $this->utils->getPlayerPromo("allpromo", $player_id);
        //unset the promo that not equivalent to promo rules language
        if(!empty($allpromo)){
            if(isset($allpromo['promo_list'])) {
                foreach ($allpromo['promo_list'] as $allpromokey => $allpromovalue) {
                    if(isset($allpromovalue['promorule'])){
                        if(!empty($allpromovalue['promorule'])){
                            if(isset($allpromovalue['promorule']['language'])){
                                if($allpromovalue['promorule']['language']){
                                    //var_dump($this->language_function->getCurrentLanguage());die();
                                    if($allpromovalue['promorule']['language'] != $this->language_function->getCurrentLanguage()){

                                        unset($allpromo['promo_list'][$allpromokey]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

		$data['player'] = $player;
		$data['promo_list'] = (isset($allpromo['promo_list'])) ? $allpromo['promo_list'] : [];
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$promoCategoryList = $this->utils->getAllPromoType();

        if($this->utils->isEnabledFeature('show_promotion_view_all')){
            $view_all_category_entry = [
                'id' => 0,
                'name' => lang('View All')
            ];
            array_unshift($promoCategoryList, $view_all_category_entry);

            @reset($promoCategoryList);
            $data['default_show_category_id'] = 0;
        }else{
            @reset($promoCategoryList);
            $data['default_show_category_id'] = (empty($promoCategoryList)) ? 0 : current($promoCategoryList)['id'];
        }
        $data['promoCategoryList'] = $promoCategoryList;

		$this->loadTemplate(lang('cms.promoReqAppList'), '', '', '');
        $url = $this->utils->getPlayerCenterTemplate() . '/promotion/player_promotion_list';
        $this->template->add_js('/common/js/player_center/promotions.js');
		$this->template->write_view('main_content', $url, $data);
		$this->template->render();
	}

	public function api_getPromotions($player_id, $offset = 0, $limit = 15) {
        $this->load->model(array('promorules', 'player_model'));

        $player = $this->player_model->getPlayerById($player_id);

        if($player->disabled_promotion==1){
            $promo_list=[];
        }else{
            $promo_list = $this->promorules->getAllPromo($limit, $offset);
        }
        if (!empty($promo_list)) {

            // $isVerifiedEmail = $this->player_model->isVerifiedEmail($player_id);
            // $this->utils->debug_log('isVerifiedEmail', $isVerifiedEmail);

            $available_list=[];

            foreach ($promo_list as &$promo_item) {

                if($promo_item['hide_on_player'] > 0){
                    //ignore
                    continue;
                }

                $promorulesId = $promo_item['promoId'];

                // add resend
                $promorule = $this->promorules->getPromorule($promorulesId);

                // $allowedPlayerLevels 	= array_column($this->promorules->getAllowedPlayerLevels($promorulesId), 'vipsettingcashbackruleId');
                // $allowedAffiliates 	 	= array_column($this->promorules->getAllowedAffiliates($promorulesId), 'affiliateId');
                // $allowedPlayers 	 	= array_column($this->promorules->getAllowedPlayers($promorulesId), 'playerId');

                // $playerIsAllowed		= ($allowedPlayerLevels && in_array($player->levelId, $allowedPlayerLevels)) ||
                // 		        		  ($allowedAffiliates && in_array($player->affiliateId, $allowedAffiliates)) ||
                // 		        		  ($allowedPlayers && in_array($player->playerId, $allowedPlayers));
                $hide=false;
                $playerIsAllowed = $this->promorules->isAllowedPlayerBy($promorulesId, $promorule, $player->levelId, $player->playerId, $player->affiliateId, $hide);

                if($hide){
                    $this->utils->debug_log('ingore promotion', $promorulesId, 'player id', $player->playerId);
                    continue;
                }

                if ($playerIsAllowed) {
                    $promo_item['promorule'] = $promorule;
                    $status['enable_resend'] = false; // ! $isVerifiedEmail && $this->promorules->isEmailPromo($promorule);
                    $promo_item['status'] = $promorule['status'];
                    $promo_item['disabled_pre_application'] = $promorule['disabled_pre_application']=='1';
                }else{
                    $promo_item['disabled_pre_application'] = true;
                }

                $promo_item['promoType'] = $this->promorules->getPromoCmsDetails($promo_item['promoCmsSettingId'])[0]['promoType'];
                $promo_item['disabled'] = !$playerIsAllowed;

                $available_list[]=$promo_item;
            }

            $promo_list=$available_list;
        }

        return $promo_list;
    }

	public function my_promo() {

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		if( $this->utils->getConfig('enabled_promo_pagination') ) return $this->myPromo();

		$this->load->model(array('player_promo', 'promorules'));
		$data['playerpromo'] = $this->player_promo->getPlayerActivePromoDetails($player_id);
		$data['playerId'] = $player_id;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$this->loadTemplate(lang('cashier.myPromo'), '', '', '');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_mypromotion', $data);
		$this->template->render();
	}

	public function myPromo(){

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);
		$data['playerpromo'] = $this->player_promo->getPlayerActivePromoDetails($player_id);
		$data['useDataTable'] = true;
		$this->loadTemplate(lang('cashier.myPromo'), '', '', '');
		$this->template->add_js('resources/js/datatables.min.js');
		$this->template->add_css('resources/css/datatables.min.css');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_mypromotion', $data);
		$this->template->render();

	}

	public function myPromo_search(){

		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/view_mypromo_search', $data);
	}
	/**
	 * overview : view my promotion
	 *
	 * @return rendered template
	 */
	public function iframe_myPromo() {
		return $this->my_promo();
	}

	/**
	 * overview : promo history
	 *
	 * @param $from
	 * @param $to
	 */
	public function promoHistory($from, $to) {
		$player_id = $this->authentication->getPlayerId();

		$search = array(
			'from' => urldecode($from),
			'to' => urldecode($to),
		);
		$this->load->model(array('player_promo'));
		$data['promoHistory'] = $this->player_promo->getPlayerPromoHistoryWLimit($player_id, $search);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_promo_history', $data);
	}

	/**
	 * overview : Promo History
	 * add by Spencer.kuo
	 */
	public function iframe_promo_history() {
		$player_id = $this->authentication->getPlayerId();

		$search = $this->input->post();
		if (empty($search)) {
			$search = Array(
				"dateRangeValueStart" => date('Y-m-d 00:00:00'),
				"dateRangeValueEnd" => date('Y-m-d 23:59:59'),
				"flag" => ""
				);
		}
		$data["dateRangeValueStart"] = $search["dateRangeValueStart"];
		$data["dateRangeValueEnd"] = $search["dateRangeValueEnd"];
		$data["flag"] = $search["flag"];
		$data["promoHistory"] = $this->player_promo->getPlayerPromoHistoryWInput($player_id, $search);
		$this->loadTemplate(lang('cashier.myPromo'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/promo_history', $data);
		$this->template->render();
	}

	/**
	 * overview : my promo
	 * @return  view
	 * add by spencer.kuo 2017.04.26
	 */
	public function iframe_my_promo() {
		$this->load->model(array('player_promo', 'promorules'));
		$playerId = $this->authentication->getPlayerId();

		$record_count = $this->player_promo->getPlayerActivePromoDetails($playerId, null, null, 1);

		//setting up the pagination
		$config['base_url'] = site_url() . '/iframe_module/iframe_my_promo';
		$config['total_rows'] = $record_count;
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<div id="pageNav" class="pagejump" pagenum="1" pagesize="10">';
		$config['full_tag_close'] = '</div>';
		$config['cur_tag_open'] = '<span id="page_num" class="pageNavEle">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span id="page_num" class="pageNavEle">';
		$config['num_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span id="prev_button" class="pageNavEle">';
		$config['prev_tag_close'] = '</span>';
		$config['next_tag_open'] = '<span id="next_button" class="pageNavEle">';
		$config['next_tag_close'] = '</span>';
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 0;

		if( $this->uri->segment(3) ) {
			$page = $this->uri->segment(3);
		}

		$data['playerpromo'] = $this->player_promo->getPlayerActivePromoDetails($playerId, $config['per_page'], $page * $config['per_page']);
		$data['playerId'] = $playerId;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$this->loadTemplate(lang('cashier.myPromo'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/view_mypromotion', $data);
		$this->template->render();
	}

	/**
	 * overview : iframe report
	 *
	 * @return rendered template
	 */
	public function iframe_promotion($segment = "") {
		$this->load->model(array('promorules'));
		// if (!$this->authentication->isLoggedIn()) {
		// 	$this->goPlayerLogin();
		// } else {
		$data['promo'] = $this->promorules->getAllFeaturedPromo(null, null);
		$this->loadTemplate('Promotions', '', '', 'promotions');
		$this->template->write_view('main_content', 'iframe/promotion/view_featured_promotions');
		$this->template->render();
		// }
	}

	/**
	 * overview : preview promo cms
	 *
	 * @param   int	$promocmsId
	 */
	public function viewPromoDetails($promocmsId) {
		$this->load->model(array('promorules'));
		// if (!$this->authentication->isLoggedIn()) {
		// 	$this->goPlayerLogin();
		// } else {
		$data['promocms'] = $this->promorules->getPromoCmsDetails($promocmsId);
		$this->loadTemplate('Promotions', '', '', 'promotions');
		$this->template->add_js('resources/js/online/promotions.js');
		$this->template->write_view('main_content', 'iframe/promotion/view_promotion_details', $data);
		$this->template->render();
		// }
	}

	/**
	 * overview : pickup bonus for bonus mode counting
	 *
	 * @param int $randomResultType
	 * @param null $promoCategoryId
	 *
	 * Not used at 19-07-30. ("pick_up_bonus_for_bonus_mode_counting" only defined at function name)
	 */
	public function pick_up_bonus_for_bonus_mode_counting($randomResultType = 1, $promoCategoryId = null) {
		$this->load->model(array('transactions', 'wallet_model', 'withdraw_condition', 'random_bonus_history', 'promorules'));

		$random_bonus_mode = Promorules::RANDOM_BONUS_MODE_COUNTING;
		$playerId = $this->authentication->getPlayerId();

		if (!$this->utils->getConfig("random_bonus_mode_counting_sandbox_mode")) {
			$bonusExistsToday = $this->random_bonus_history->isPlayerBonusExistsTodayForBonusModeCounting($playerId);
			if ($bonusExistsToday) {
				// $msg = lang('random.bonus.message6');
				redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode . '/' . self::MESSAGE_CODE_NOT_AVAILABLE);
			}

			//get available bonus
			$onlyOne=1;
			$availableDepositForPickUpBonus = $this->transactions->getAnyDepositForPickupBonus($playerId, $onlyOne);
			$depositAmount = null;
			$depositTransactionId = null;
			if (!empty($availableDepositForPickUpBonus)) {
				$depositAmount = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT]['amount'];
				$depositTransactionId = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT]['deposit_transaction_id'];
			} else {
				// $msg = lang('random.bonus.message5');
				redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode . '/' . self::MESSAGE_CODE_NO_DEPOSIT);
			}
		}

		// $lock_it = $this->lockActionById($playerId, 'random_bonus');
		// $lock_it = $this->lockPlayerBalance($playerId);
		$lockedKey=null;
		$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
		// $this->utils->debug_log('lock random_bonus', $playerId, $lock_it);
		if ($lock_it) {
			//lock success
			try {
				if (!$this->utils->getConfig("random_bonus_mode_counting_sandbox_mode")) {
					$this->startTrans();

					$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);

					$depositForPickUpBonus = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT];
					//calc random bonus amount
					list($randomBonusAmount, $randomRate) = $this->promorules->calcRandomBonus($playerId, $random_bonus_mode,
						$depositForPickUpBonus);

					//add to main wallet, move to transaction
					// $this->wallet_model->incMainWallet($playerId, $randomBonusAmount);

					//write to transactions
					$adminUserId = 1;
					$bonusTransactionId = $this->transactions->createBonusTransaction($adminUserId, $playerId, $randomBonusAmount, $beforeBalance,
						null, null, Transactions::MANUAL, null, Transactions::RANDOM_BONUS,
						'deposit amount is ' . $depositAmount, $promoCategoryId);

					//write to withdrawal condition
					$random_bonus_withdraw_condition_times = $this->utils->getConfig('random_bonus_withdraw_condition_times');
					$withdrawalConditionAmount = $random_bonus_withdraw_condition_times * $randomBonusAmount;
					$this->withdraw_condition->createWithdrawConditionForRandomBonus($playerId, $bonusTransactionId, $withdrawalConditionAmount,
						$randomBonusAmount, $random_bonus_withdraw_condition_times);

					//write to random history
					$this->random_bonus_history->addToRandomBonusHistory($playerId, $depositTransactionId, $bonusTransactionId,
						$depositAmount, $randomBonusAmount, null, Promorules::RANDOM_BONUS_MODE_COUNTING);

					$success = $this->endTransWithSucc();

					if ($success) {

						$countAvailableDepositForPickUpBonus = $this->transactions->countAvailableDepositForPickupBonus($playerId);

						$data = array(
							'deposit_amount' => $depositAmount,
							'bonus_amount' => $randomBonusAmount,
							'username' => $this->authentication->getUsername(),
							'bonusChance' => $countAvailableDepositForPickUpBonus,
							'nopanel' => true,
						);

						$this->loadTemplate('Pick up bonus', '', '', 'bonus');
						$this->template->write_view('main_content', 'iframe/player/pick_up_bonus_for_bonus_mode_counting', $data);
						$this->template->render();
						return;
						// } else {
					}
				} else {
					//calc random bonus amount
					if ($randomResultType == Promorules::RANDOM_BONUS_MODE_COUNTING_RESULT_TYPE_1) {
						$bonusAmount = 1;
					} else {
						$bonusAmount = 50;
					}
					$data = array(
						'bonus_amount' => $bonusAmount,
					);
					$this->loadTemplate('Pick up bonus', '', '', 'bonus');
					$this->template->write_view('main_content', 'iframe/player/pick_up_bonus_for_bonus_mode_counting', $data);
					$this->template->render();
					return;
				}
			} finally {
				// release it
				// $rlt = $this->releaseActionById($playerId, 'random_bonus');
				// $rlt = $this->releasePlayerBalance($playerId);
				$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
				// $rlt = $this->player_model->transReleaseLock($trans_key);
				// $this->utils->debug_log('release random_bonus lock', $playerId, $rlt);
			}
		}

		$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		$this->goPlayerHome();
	}

	// const RANDOM_BONUS_MODE_PERCENT_DEPOSIT = 1;
	// const RANDOM_BONUS_MODE_COUNTING = 2;
	// const RANDOM_BONUS_MODE_FIXED_ITEM = 3;

	/**
	 * overview : pick up bonus
	 *
	 * @param int $random_bonus_mode
	 * @param int $promoCategoryId
	 */
	public function pick_up_bonus($random_bonus_mode = 1, $promoCategoryId = null) {
		$this->load->model(array('promorules', 'transactions', 'wallet_model'));

		$playerId = $this->authentication->getPlayerId();

		if (empty($playerId)) {
			redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode);
		}

		list($available, $depositForPickUpBonus) = $this->promorules->isAvailableRandomBonus($playerId, $random_bonus_mode, $promoCategoryId);

		if (!$available) {
			if ($random_bonus_mode == Promorules::RANDOM_BONUS_MODE_COUNTING) {
				if (empty($depositForPickUpBonus)) {
					//no deposit
					$msgcode = self::MESSAGE_CODE_NO_DEPOSIT;
				} else {
					$msgcode = self::MESSAGE_CODE_NOT_AVAILABLE;
				}
				redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode . '/' . $msgcode);
			} else {
				redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode);
			}
		}

		// //get available bonus
		// $availableDepositForPickUpBonus = $this->transactions->getAvailableDepositForPickupBonus($playerId);

		// // $depositAmount = null;
		// // $depositTransactionId = null;
		// $depositForPickUpBonus = null;
		// if (!empty($availableDepositForPickUpBonus)) {
		// 	$depositForPickUpBonus = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT];
		// 	// $depositAmount = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT]['amount'];
		// 	// $depositTransactionId = $availableDepositForPickUpBonus[self::FIRST_AVAILABLE_DEPOSIT]['deposit_transaction_id'];
		// } else {
		// 	redirect('iframe_module/noAvailableRandomBonus');
		// }

		//calc random bonus amount
		// $random_bonus_withdraw_condition_times = $this->utils->getConfig('random_bonus_withdraw_condition_times');

		// $min = $this->utils->getConfig('min_random_bonus_rate');
		// $max = $this->utils->getConfig('max_random_bonus_rate');
		// $randomRate = rand($min, $max);
		// $randomBonusPercentage = $randomRate / 100;
		// $randomBonusAmount = $randomBonusPercentage * $depositAmount;

		// $lock_it = $this->lockActionById($playerId, 'random_bonus');
		// $lock_it = $this->lockPlayerBalance($playerId);
		$lockedKey=null;
		$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);
		// $this->utils->debug_log('lock random_bonus', $playerId, $lock_it);
		if ($lock_it) {
			//lock success
			try {

				$this->startTrans();

				// $beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);

				$depositAmount = $depositForPickUpBonus['amount'];
				$depositTransactionId = $depositForPickUpBonus['deposit_transaction_id'];

				list($randomBonusAmount, $randomRate) = $this->promorules->calcRandomBonus($playerId, $random_bonus_mode,
					$depositForPickUpBonus);

				if ($randomBonusAmount > 0) {
					$this->promorules->releaseRandomBonus($playerId, $randomBonusAmount,
						$depositForPickUpBonus, $promoCategoryId, $randomRate);
				}

				$success = $this->endTransWithSucc();
				if ($success) {

					$countAvailableDepositForPickUpBonus = $this->transactions->countAvailableDepositForPickupBonus($playerId);

					$data = array(
						'deposit_amount' => $depositAmount,
						'bonus_amount' => $randomBonusAmount,
						'username' => $this->authentication->getUsername(),
						'bonusChance' => $countAvailableDepositForPickUpBonus,
						'nopanel' => true,
						'random_bonus_mode' => $random_bonus_mode,
						'promoCategoryId' => $promoCategoryId,
					);

					$this->loadTemplate('Pick up bonus', '', '', 'bonus');
					$this->template->write_view('main_content', 'iframe/player/pick_up_bonus', $data);
					$this->template->render();
					return;
					// } else {
				}
			} finally {
				// release it
				// $rlt = $this->releaseActionById($playerId, 'random_bonus');
				$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
				// $rlt = $this->releasePlayerBalance($playerId);
				// $rlt = $this->player_model->transReleaseLock($trans_key);
				// $this->utils->debug_log('release random_bonus lock', $playerId, $rlt);
			}
		}

		$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		$this->goPlayerHome();

	}

	/**
	 * overview : merge player pickup bonus and pickup bonus for bonus mode counting
	 *
	 * @param int $random_bonus_mode
	 * @param int $promoCategoryId
	 * @param int $randomResultType
	 */
	public function player_pick_up_bonus($random_bonus_mode = 1, $promoCategoryId = 0, $randomResultType = 1) {
		$random_bonus_intro_url = $this->utils->getConfig('random_bonus_intro_url') ?: site_url();
		$random_bonus_result_url = $this->utils->getConfig('random_bonus_result_url') ?: site_url();
		$random_bonus_not_available_url = $this->utils->getConfig('random_bonus_not_available_url') ?: site_url();

		$this->load->model(array('promorules', 'transactions', 'wallet_model'));
		$playerId = $this->authentication->getPlayerId();

		if (empty($playerId)) {
			// redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode);
			$msgcode = lang('promo.loginToJoin');

			redirect($random_bonus_not_available_url . '?random_bonus_mode=' . $random_bonus_mode . '&promo_category_id=' . $promoCategoryId . '&msgcode=' . $msgcode);
		}

		if (!$this->utils->getConfig("random_bonus_mode_counting_sandbox_mode")) {
			list($available, $depositForPickUpBonus) = $this->promorules->isAvailableRandomBonus($playerId, $random_bonus_mode, $promoCategoryId);

			if (!$available) {
				if ($random_bonus_mode == Promorules::RANDOM_BONUS_MODE_COUNTING) {
					if (empty($depositForPickUpBonus)) {
						//no deposit
						// $msgcode = self::MESSAGE_CODE_NO_DEPOSIT;
						$msgcode = lang('random.bonus.message3');
					} else {
						$msgcode = self::MESSAGE_CODE_NOT_AVAILABLE;
					}

					// redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode . '/' . $msgcode);
					redirect($random_bonus_not_available_url . '?random_bonus_mode=' . $random_bonus_mode . '&promo_category_id=' . $promoCategoryId . '&msgcode=' . $msgcode);
				} else {
					// redirect('iframe_module/noAvailableRandomBonus/' . $random_bonus_mode);
					redirect($random_bonus_not_available_url . '?random_bonus_mode=' . $random_bonus_mode . '&promo_category_id=' . $promoCategoryId . '&msgcode=0');
				}
			}

			// $lock_it = $this->lockActionById($playerId, 'random_bonus');
			// $lock_it = $this->lockPlayerBalance($playerId);
			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($playerId, $lockedKey);

			if ($lock_it) {
				//lock success
				$success=false;
				try {
					$this->startTrans();

					$depositAmount = $depositForPickUpBonus['amount'];
					$depositTransactionId = $depositForPickUpBonus['deposit_transaction_id'];

					list($randomBonusAmount, $randomRate) = $this->promorules->calcRandomBonus($playerId, $random_bonus_mode,
						$depositForPickUpBonus);

					if ($randomBonusAmount > 0) {
						$this->promorules->releaseRandomBonus($playerId, $randomBonusAmount,
							$depositForPickUpBonus, $promoCategoryId, $randomRate);
					}

					$success = $this->endTransWithSucc();
				} finally {
					// release it
					// $rlt = $this->releaseActionById($playerId, 'random_bonus');
					// $rlt = $this->releasePlayerBalance($playerId);
					$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
				}
				if ($success) {

					$countAvailableDepositForPickUpBonus = $this->transactions->countAvailableDepositForPickupBonus($playerId);
					$msgcode = $this->authentication->getUsername() . ' ' . lang('random.bonus.message2') . ' ' . $randomBonusAmount . ', '
					. lang('random.bonus.message1') . " " . $countAvailableDepositForPickUpBonus . " " . lang('random.bonus.message4');

					redirect($random_bonus_result_url . '?random_bonus_mode=' . $random_bonus_mode . '&promo_category_id=' . $promoCategoryId . '&msgcode=' . $msgcode);
					return;
				}
			}
		} else {
			//calc random bonus amount
			if ($randomResultType == Promorules::RANDOM_BONUS_MODE_COUNTING_RESULT_TYPE_1) {
				$randomBonusAmount = 1;
			} else {
				$randomBonusAmount = 50;
			}

			redirect($random_bonus_intro_url . '?random_bonus_mode=' . $random_bonus_mode . '&promo_category_id=' . $promoCategoryId . '&msgcode=0&bonus_amount=' . $randomBonusAmount);
			return;
		}

		$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
		$this->goPlayerHome();

	}

	/**
	 * overview : player pickup bonus into
	 *
	 * @param int $random_bonus_mode
	 * @param int $promoCategoryId
	 */
	public function player_pick_up_bonus_intro($random_bonus_mode = 1, $promoCategoryId = 0) {
		$this->load->model(array('transactions'));
		$playerId = $this->authentication->getPlayerId();
		$countAvailableDepositForPickUpBonus = $this->transactions->countAvailableDepositForPickupBonus($playerId);
		$msgcode = $countAvailableDepositForPickUpBonus;
		if ($countAvailableDepositForPickUpBonus) {
			$msgcode = lang('random.bonus.message1') . " " . $countAvailableDepositForPickUpBonus . " " . lang('random.bonus.message4');
		} else {
			$msgcode = lang('random.bonus.message3');
		}
		$random_bonus_intro_url = $this->utils->getConfig('random_bonus_intro_url') ?: site_url();
		redirect($random_bonus_intro_url . '?random_bonus_mode=' . $random_bonus_mode . '&msgcode=' . $msgcode . '&chance=' . $countAvailableDepositForPickUpBonus . '&promo_category_id=' . $promoCategoryId);
		return;
	}

	/**
	 * overview : pickup bonus intro
	 *
	 * @param int $random_bonus_mode
	 * @param null $promoCategoryId
	 */
	public function pick_up_bonus_intro($random_bonus_mode = 1, $promoCategoryId = null) {
		$this->load->model(array('transactions'));
		$playerId = $this->authentication->getPlayerId();
		$countAvailableDepositForPickUpBonus = $this->transactions->countAvailableDepositForPickupBonus($playerId);
		$data['bonusChance'] = $countAvailableDepositForPickUpBonus;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['nopanel'] = true;
		$data['random_bonus_mode'] = $random_bonus_mode;
		$data['promoCategoryId'] = $promoCategoryId;
		/// Patch for A PHP Error was encountered | Severity: Notice | Message:  Undefined variable: playerStatus | Filename: widgets/sidebar.php:3
		// 客戶網站：https://player.laba360.t1t.in/iframe_module/pick_up_bonus_intro/3/5
		// 沒出錯
		// 開發站有出錯
		// A PHP Error was encountered | Severity: Notice | Message:  Undefined variable: playerStatus | Filename: widgets/sidebar.php:3
		// A PHP Error was encountered | Severity: Notice | Message:  Undefined variable: playerStatus | Filename: widgets/sidebar.php:11
		$data['playerStatus'] = $this->utils->getPlayerStatus($playerId);

		// if ($countAvailableDepositForPickUpBonus > 0) {
		$this->loadTemplate('Pick up bonus', '', '', 'bonus');
		$this->template->write_view('main_content', 'iframe/player/pick_up_bonus_intro', $data);
		$this->template->render();
		// } else {
		// 	redirect('iframe_module/noAvailableRandomBonus');
		// }
	}

	/**
	 * overview : no available random bonus
	 *
	 * @param $bonus_mode
	 * @param $msg_code
	 */
	public function noAvailableRandomBonus($bonus_mode = null, $msg_code = null) {
		$this->load->model(array('promorules'));
		$this->loadTemplate('Pick up bonus', '', '', 'bonus');

		if ($bonus_mode == Promorules::RANDOM_BONUS_MODE_COUNTING) {
			if ($msg_code == self::MESSAGE_CODE_NO_DEPOSIT) {
				$msg = lang('random.bonus.message5');
			} else {
				$msg = lang('random.bonus.message6');
			}
			$this->load->model(array('static_site'));
			$logo_url = $this->static_site->getDefaultLogoUrl();
			$data = array('msg' => $msg,
				'nopanel' => true,
				'logo_url' => $logo_url,
			);
			$this->template->write_view('main_content', 'iframe/player/no_available_bonus', $data);
		} else {
			$data = array('bonusChance' => 0,
				'nopanel' => true);
			$this->template->write_view('main_content', 'iframe/player/pick_up_bonus_intro', $data);
		}

		$this->template->render();
	}

	/**
	 * overview : trigger transfer promotion
	 *
	 * @param $player_id
	 * @param $amount
	 * @param $transfer_from
	 * @param $transfer_to
	 * @param $transferTransId
	 * @param $result
	 * @param int $playerPromoId
	 */
	protected function triggerTransferPromotion($player_id, $amount, $transfer_from, $transfer_to, $transferTransId, &$result, $playerPromoId=null){

		$this->load->model(['promorules', 'player_promo']);

		if ($transfer_to != Wallet_model::MAIN_WALLET_ID) {
			$gamePlatformId = $transfer_to;
			$transactionType = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
			// 	$lock_type = 'main_to_sub';
		} else {
			$gamePlatformId = $transfer_from;
			$transactionType = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
			// 	$lock_type = 'sub_to_main';
		}

		$existsUnfinishedPromoAndDonotAllowOthers=$this->player_promo->existsUnfinishedPromoAndDonotAllowOthers($player_id);
		$this->utils->debug_log('existsUnfinishedPromoAndDonotAllowOthers', $existsUnfinishedPromoAndDonotAllowOthers, 'player_id', $player_id);
		//try apply promotion if transfer
		if ($transactionType == Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET && !$existsUnfinishedPromoAndDonotAllowOthers) {
			// $promoCmsSettingId=null;
			$promorule=null;

			//check playerPromoId
			if($playerPromoId){
				//load promorules
				$promorule=$this->player_promo->getPromoruleBy($playerPromoId);
				$this->utils->debug_log('try apply promo from playerPromoId', $playerPromoId, $promorule);
			}else{
				list($promoList, $playerPromoMap)=$this->promorules->getAvailTriggerOnTransferSubWallet($player_id, $transfer_to);
				if(!empty($promoList)){
					//only first
					$promorule=$promoList[0];
					$this->utils->debug_log('try apply first promo', $promorule);
					if($promorule){
						$playerPromoId= isset($playerPromoMap[$promorule['promorulesId']]) ? $playerPromoMap[$promorule['promorulesId']] : null;
						// $promoCmsSettingId=$promorule['promoCmsSettingId'];
					}
				}

			}

			if($promorule){
				$preapplication=false;
				// $playerPromoId= isset($playerPromoMap[$promorule['promorulesId']]) ? $playerPromoMap[$promorule['promorulesId']] : null;
				$promoCmsSettingId=$promorule['promoCmsSettingId'];
				$extra_info=['transferAmount'=>$amount, 'transferSubwalletId'=>$transfer_to,
				'transferTransId'=>$transferTransId, 'subWalletId'=>$gamePlatformId ];
				$controller=$this;
				$success=$this->lockAndTransForPlayerBalance($player_id, function() use ($controller, $player_id, $promorule,
					$promoCmsSettingId, $preapplication, $playerPromoId, &$extra_info, &$message){

					//load promorule
					list($success, $message)=$controller->promorules->triggerPromotionFromTransfer($player_id, $promorule,
						$promoCmsSettingId, $preapplication, $playerPromoId, $extra_info);

					return $success;
				});

				if($success){
					$result['message'].=' '.lang($message);

					if(isset($extra_info['releaseToSubWallet']) && !empty($extra_info['releaseToSubWallet'])){
						$releaseToSubWallet=$extra_info['releaseToSubWallet'];

						$this->utils->debug_log('releaseToSubWallet player:'.$player_id, $releaseToSubWallet);
						//transfer to subwallet
						$bonusResult=$this->utils->transferWallet($releaseToSubWallet['playerId'],
							$releaseToSubWallet['playerName'],
							$releaseToSubWallet['transfer_from'], $releaseToSubWallet['transfer_to'],
							$releaseToSubWallet['playerBonusAmount'], $releaseToSubWallet['adminId'],
							$releaseToSubWallet['walletType'], $releaseToSubWallet['originTransferAmount']);

						//move transfer amount to real_for_bonus

						if(!$bonusResult['success']){
							$this->utils->error_log('transfer bonus failed', $bonusResult );
						}
						// if(isset($bonusResult['success'])){
						// 	$result['message'].=' '. $message;
						// }else{
						// 	$result['message'].=' '. $message;
						// }
					}
				}else{
					$result['success']=false;
					$result['message'].=' '.lang($message);
					//don't show error, only show success
					$this->utils->debug_log('apply promotion failed', $message, $player_id, $promoCmsSettingId, $promorule);
					// $message=null;
				}
			}else{
				//try update manual only but not approved
				list($promoList, $playerPromoMap)=$this->promorules->getRequestTriggerOnTransferSubWallet($player_id, $transfer_to);
				if(!empty($promoList)){
					//only first
					$promorule=$promoList[0];
					$this->utils->debug_log('try apply first promo', $promorule);
					if($promorule){
						$playerPromoId= isset($playerPromoMap[$promorule['promorulesId']]) ? $playerPromoMap[$promorule['promorulesId']] : null;
						// $promoCmsSettingId=$promorule['promoCmsSettingId'];
						//
					}
				}
				if(!empty($promorule)){
					//only update number
					$promoCmsSettingId=$promorule['promoCmsSettingId'];
					$extra_info=['transferAmount'=>$amount, 'transferSubwalletId'=>$transfer_to,
					'transferTransId'=>$transferTransId, 'subWalletId'=>$gamePlatformId ];
					$controller=$this;
					$success=$this->lockAndTransForPlayerBalance($player_id, function() use ($controller, $player_id, $promorule,
						$promoCmsSettingId, $playerPromoId, $amount, $transferTransId, &$extra_info, &$message){

						// $preapplication=false;
						$userId=null;
						//load promorule
						list($success, $message)=$controller->promorules->updateOnlyNumberWithoutRelease($player_id, $promorule,
							$promoCmsSettingId, $userId , $amount, $transferTransId, $playerPromoId, $extra_info);

						return $success;
					});

					if($success){
						$result['message'].=' '.lang($message);

					}else{
						$result['success']=false;
						$result['message'].=' '.lang($message);
						//don't show error, only show success
						$this->utils->debug_log('updateOnlyNumberWithoutRelease promotion failed', $message, $player_id, $promoCmsSettingId, $promorule);
					}

				}else{
					$this->utils->debug_log('no promorule, ignore trigger');
				}
			}
		}else{
			$this->utils->debug_log('ignore trigger, transactionType:'.$transactionType.', existsUnfinishedPromoAndDonotAllowOthers:'.$existsUnfinishedPromoAndDonotAllowOthers);
		}
	}

	/**
	 * overview : promo history
	 *
	 * @param $from
	 * @param $to
	 */
	public function promoHistoryV2($segment, $from = null , $to = null) {
		$player_id = $this->authentication->getPlayerId();
		$input = $this->input->post();
		$this->load->model(array('player_promo'));

		$search = null;
		if ($from && $to) {
			$search = array(
				'from' => urldecode($from),
				'to' => urldecode($to),
			);
		}

		$data['count_all'] = $this->player_promo->getPlayerPromoHistoryWLimit($player_id, $search, null, null, true);

		$config['total_rows'] = $data['count_all'];
		$config['per_page'] = '5';
		$config['num_links'] = '1';
	    $config['base_url'] = "javascript:promoHistoryV2('%s')";
		$config['callback_link'] = true;

		$config['first_tag_open'] = '<li class="page_first">';
		$config['last_tag_open'] = '<li class="page_last">';
		$config['next_tag_open'] = '<li class="page_next">';
		$config['prev_tag_open'] = '<li class="page_preview">';
		$config['num_tag_open'] = '<li class="page_number">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_close'] = '</li>';
		$config['num_tag_close'] = '</li>';
		$config['anchor_class'] = 'class="my-pagination" ';

		$config['cur_tag_open'] = "<li><span><b>";
		$config['cur_tag_close'] = "</b></span></li>";

		$config['next_link'] = lang('Next Page');
		$config['prev_link'] = lang('Prev Page');
		$config['last_link'] = lang('Last Page');
		$config['first_link'] = lang('First Page');

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$data['total_pages'] = ceil($data['count_all'] / $config['per_page']);

		$data['promoHistory'] = $this->player_promo->getPlayerPromoHistoryWLimit($player_id, $search, $config['per_page'], $segment);

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_promo_history', $data);
	}


	// public function rescue_promo() {
	// 	if ($this->utils->getConfig('enabled_rescue_promotion')) {
	// 		$player_id = $this->authentication->getPlayerId();
	// 		$rescue_promotion_amount = $this->utils->getConfig('rescue_promotion_amount');
	// 		$totalBalance = $this->wallet_model->getTotalBalance();
	// 		if ($totalBalance <= $rescue_promotion_amount) {
	// 			//send request
	// 			return;
	// 		}
	// 	}

	// 	//decliend
	// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('promo.request_rescue.decliend'));
	// }
}
////END OF FILE/////////