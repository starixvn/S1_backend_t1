<?php

/**
 * Class player_auth_module
 *
 * General behaviors include :
 *
 * * Login player
 * * Logout player
 * * Register
 * * Check if exist (email, username, contact, referral )
 * * Check sms verification
 * *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 *
 * @property Player_library $player_library
 */
trait player_auth_module {

	/**
	 * overview : login user from iframe
	 *
	 * @return postMessage to parent
	 *
	 */
	public function iframe_login() {
		//check login
		//set player info
		$origin = '*';
		$result = array('act' => $this->input->post('act'));

		if ($this->authentication->isLoggedIn()) {
			$result['success'] = true;
		}else{
            $this->load->model('operatorglobalsettings', 'player');
            $this->load->library(['player_library']);

            $username = $this->input->post('login');
            $isAutoMachineUser = $this->player->isAutoMachineUser($username);

            if ($this->operatorglobalsettings->getSettingJson('login_captcha_enabled') && !$isAutoMachineUser && $this->input->post('captcha')) {
                $this->form_validation->set_rules('captcha', lang('label.captcha'), 'callback_check_captcha[static_site]');
            }

            $this->form_validation->set_rules('login', 'lang:username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'lang:password', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $captcha_err = form_error('captcha');
                $errors =  validation_errors();
                $result['success'] = false;
                if (!empty($captcha_err)) {
                    $result['message'] = lang('error.captcha');
                } else {
                    $result['message'] = (is_array($errors) && !empty($errors)) ? end($errors) : $errors;
                }

                $data = array('result' => str_replace("'", "\\'", json_encode($result)), 'origin' => $origin);
                //NOTICE: don't callback with big text
                //callback
                $httpHeaderInfo = $this->utils->getHttpOnRequest();

                return $this->load->view('player/iframe_callback', $data);
            }

            $username = $this->form_validation->set_value('login');
            $password = $this->form_validation->set_value('password');

            $login_result = $this->player_library->login_by_password($username, $password, FALSE);

            if($login_result['success']){
                $result['success'] = TRUE;
                $result['message'] = NULL;
            }else{
                $result['success'] = FALSE;
                $result['message'] = (is_array($login_result['errors']) && !empty($login_result['errors'])) ? end($login_result['errors']) : $login_result['errors'];
            }
        }

		if ($result['success']) {
			$playerId = $this->authentication->getPlayerId();
			//load player info
			$player = $this->player_functions->getPlayerById($playerId);
			$result['playerName'] = $player['username'];
			$result['playerId'] = $playerId;
			//add token
			$result['token'] = $this->authentication->getPlayerToken();

			$this->load->model(array('wallet_model'));
			$big_wallet = $this->utils->getSimpleBigWallet($playerId);;

			$result['VIP_group'] = $this->authentication->getPlayerMembership();
			$result['total_balance'] = $big_wallet['total_balance']['balance'];
			$result['walletInfo'] = $big_wallet;
		}

		$data = array('result' => str_replace("'", "\\'", json_encode($result)), 'origin' => $origin);
		//NOTICE: don't callback with big text
		//callback
		$httpHeaderInfo = $this->utils->getHttpOnRequest();
		$this->utils->debug_log('<----------------- referrer origin ----------->', $httpHeaderInfo['referrer']);
		$this->utils->debug_log('<----------------- referrer player ----------->', $this->utils->getSystemUrl('www'));

		$this->load->view('player/iframe_callback', $data);
	}

	/**
	 * overview : iframe register
	 * @param string $tracking_code
	 * @param string $tracking_source_code
	 */
	public function iframe_register($tracking_code = null, $tracking_source_code = null, $agent_tracking_code = null, $agent_tracking_source_code = null) {
		$this->output->set_header('Access-Control-Allow-Origin:*');
		$this->load->model(array('operatorglobalsettings', 'registration_setting','group_level', 'affiliatemodel', 'agency_model', 'http_request'));

        if($this->utils->getConfig('disable_player_register_and_redirect_to_login')){
            return redirect('/iframe/auth/login');
        }

		if($tracking_code=='_null'){
			$tracking_code=null;
		}
		if($tracking_source_code=='_null'){
			$tracking_source_code=null;
		}
		if($agent_tracking_code=='_null'){
			$agent_tracking_code=null;
		}
		if($agent_tracking_source_code=='_null'){
			$agent_tracking_source_code=null;
		}

		if($this->authentication->isLoggedIn()){
            $this->goPlayerHome();
		}

        $this->clearSmsTime();

		$username = '';
		$data['hiddenPassword'] = $this->player_functions->randomizer($username);

		$tc = null;
		if (!empty($tracking_code)) {
			$tc = $tracking_code;
		}

		if (empty($tracking_source_code)) {
			$tracking_source_code = $this->input->get('source_code');
		}
		if (empty($tracking_source_code)) {
			$tracking_source_code = $this->session->userdata('tracking_source_code');
		}

		if(empty($tc)){
			//load from order
			$tc=$this->getTrackingCode();
		}

		if(empty($agent_tracking_code)){
			$agent_tracking_code=$this->getAgentTrackingCode();
		}
		if(empty($agent_tracking_source_code)){
			$agent_tracking_source_code=$this->getAgentTrackingSourceCode();
		}

		//setup header
        $httpHeaderInfo = $this->utils->getHttpOnRequest();
        //ignore referrer is register
        if(strpos($httpHeaderInfo['referrer'], 'register')===FALSE){
	        $this->session->set_userdata('httpHeaderInfo', $httpHeaderInfo);
        }

		if(empty($tc) && !empty($httpHeaderInfo['referrer'])){
			//try from referer
			$referrer=$httpHeaderInfo['referrer'];
			$host=parse_url($referrer, PHP_URL_HOST);
			if(!empty($host)){
				$tc=$this->affiliatemodel->getTrackingCodeFromAffDomain($host);
				if(empty($tc)){
					$tc='';
				}
			}
		}

		$this->utils->debug_log('trackingCodeSessiontRegisterPage ', $tc);

		$this->session->set_userdata('httpHeaderInfo', $httpHeaderInfo);

        $referralCode = $this->input->get('referralcode') ?: $this->utils->getReferralCodeCookie();

        // -- BTAG FOR INCOME ACCESS
        $btag = '';
		if($this->utils->isEnabledFeature('enable_income_access')){
			$btag = $this->input->get('btag') ? : $this->utils->getBtagCookie();
		}

		$this->_process_register($data, $tc, $tracking_source_code, $agent_tracking_code, $agent_tracking_source_code, $referralCode, $btag);

		$prefixOfPlayer=$this->getPrefixUsernameOfPlayer($tc);
		$data['prefixOfPlayer'] =$prefixOfPlayer;
		$data['default_lang'] = $this->config->item('default_lang');

		$this->setTrackingCodeToSession($data['tracking_code']);

		#if there aff tracking code, insert without player first and then update it when player registered  with player id
		if(!empty($tc) || !empty($tracking_source_code)){
			$visit_record_id = $this->http_request->recordPlayerRegistration(null,$tc,$tracking_source_code);
	     	$this->session->set_userdata('visit_record_id',$visit_record_id);
		}

		$data['snackbar'] = isset($snackbar) ? $snackbar : [];
		$common_country_list = unserialize(COMMON_COUNTRY_LIST);
		$country_list = unserialize(COUNTRY_LIST);
		$block_country_list = $this->utils->getConfig('block_country_list');
		if (!empty($block_country_list))
		foreach($block_country_list as $block) {
			if (in_array($block, $common_country_list)) {
				unset($common_country_list[$block]);
			}
			if (in_array($block, $country_list)) {
				unset($country_list[$block]);
			}
		}
		$password_min_max_enabled = $this->utils->isPasswordMinMaxEnabled();
		$data['playerId'] = NULL;
		$data['common_country_list'] = $common_country_list;
		$data['country_list'] = $country_list;
		$data['currency_list'] = $this->config->item('currency_list');
		$data['forbidden_names'] = $this->utils->getConfig('default_forbidden_names');
		$data['min_username_length'] = $this->utils->getConfig('default_min_size_username');
		$data['max_username_length'] = $this->utils->getConfig('default_max_size_username');
		$data['min_password_length'] = !empty($password_min_max_enabled) ? $password_min_max_enabled['min'] : $this->utils->getConfig('default_min_size_password');
		$data['max_password_length'] = !empty($password_min_max_enabled) ? $password_min_max_enabled['max'] : $this->utils->getConfig('default_max_size_password');
		$data['regex_username'] = $this->utils->getUsernameReg();
		$data['regex_password'] = $this->utils->getPasswordReg();
		$data['min_security_answer_length'] = $this->utils->getConfig('default_min_size_security_answer');
		$data['max_security_answer_length'] = $this->utils->getConfig('default_max_size_security_answer');
		$data['min_first_name_length'] = $this->utils->getConfig('default_min_size_first_name');
        $data['max_first_name_length'] = $this->utils->getConfig('default_max_size_first_name');
		$data['max_last_name_length'] = $this->utils->getConfig('default_max_size_last_name');

		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['registration_fields'] = $this->registration_setting->getRegistrationFields();

		$data['default_prefix_for_username'] = $this->config->item('default_prefix_for_username');
		$data['captcha_registration'] = $this->operatorglobalsettings->getSettingJson('registration_captcha_enabled');
        $data['full_address_in_one_row'] = $this->operatorglobalsettings->getSettingJson('full_address_in_one_row');
		$data['showSMSField'] = !$this->utils->getConfig('disabled_sms') && $this->registration_setting->isRegistrationFieldVisible('SMS Verification Code');
		$data['showWithdrawalPasswordField'] = ($this->utils->getConfig('withdraw_verification') == 'withdrawal_password') && $this->registration_setting->isRegistrationFieldVisible('Withdrawal Password');
		$data['captcha_length'] = $this->config->item($this->config->item('si_active'))['code_length'];
		$data['getAllCanJoinIn'] = $this->group_level->getAllCanJoinInGroup();

		$this->language_function->setCurrentLanguage($data['currentLang']);

		$bankTypes = $this->player_functions->getAllBankType();
		$data['banks'] =  $bankTypes;
		$new_banktypes_order = [];

		//Note: for arranging banktypes order in dropdown, for example in korea OGP-1017
		$target_banktypes_order = $this->utils->getConfig('target_banktypes_order');
		if(count($target_banktypes_order) > 0 ){

			foreach ($target_banktypes_order  as $value) {
				foreach ($bankTypes as $k => $v) {
					if($v['bankTypeId'] == $value){
						array_push($new_banktypes_order, $v);
						unset($bankTypes[$k]);
					}
				}
			}

			$data['banks'] = array_merge($new_banktypes_order,$bankTypes);
		}

		$this->loadTemplate(lang('Register'), '', '', 'player');
		# add bootstrap-select css, js for dialing_code
		$base_path = base_url() . $this->utils->getPlayerCenterTemplate(false);
		$this->template->add_js($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.js'));
		$this->template->add_css($this->utils->thirdpartyUrl('bootstrap-select/1.12.4/bootstrap-select.min.css'));

        $regPathDir = realpath(rtrim(dirname(__FILE__).'/../../../../player/application/views/resources/common/auth/', '/'));
		$embedded_register_template_form = '';
		if($this->utils->is_mobile()){
            $embedded_register_template_form = 'register_mobile.php';
        }else{
            $embedded_register_template_form = 'register_recommended.php';

            $getRegistrationTemplate = $this->utils->getPlayerCenterRegistration();
            if(!empty($getRegistrationTemplate)){
                if(file_exists($regPathDir . '/register_' . $getRegistrationTemplate . '.php')){
                    $embedded_register_template_form = 'register_' . $getRegistrationTemplate . '.php';
                }
            }
        }
        $data['embedded_register_template_form'] = $regPathDir . '/' . $embedded_register_template_form;
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate(FALSE) . '/auth/register' , $data);
		$this->template->render();
	}

	protected function _process_register(&$data, $_tracking_code, $_tracking_source_code, $_agent_tracking_code, $_agent_tracking_source_code, $_referral_code, $_btag = NULL){
        $tracking_code = NULL;
        $tracking_source_code = NULL;
        $displayAffilateCode = TRUE;

        $agent_tracking_code = NULL;
        $agent_tracking_source_code = NULL;
        $displayAgencyCode = TRUE;

        $referral_code = NULL;
        $displayReferralCode = TRUE;

        if(!empty($_tracking_code) || !empty($_tracking_source_code)){ // from affilate
            $tracking_code = $_tracking_code;
            $tracking_source_code = $_tracking_source_code;

            $displayAgencyCode = FALSE;
            $this->utils->clearAgentTrackingCodeFromSession();
            $this->utils->clearAgentTrackingSourceCodeFromSession();

            if($this->utils->isEnabledFeature('disable_display_affiliate_code_on_player_center_affiliate_register_page')){
                $displayReferralCode = FALSE;
                $this->utils->removeReferralCodeCookie();
            }
        }else if(!empty($_agent_tracking_code) || !empty($_agent_tracking_source_code)){ // from agency
            $displayAffilateCode = FALSE;
            $this->utils->clearTrackingCode();

            $agent_tracking_code = $_agent_tracking_code;
            $agent_tracking_source_code = $_agent_tracking_source_code;

            if($this->utils->isEnabledFeature('disable_display_agent_code_on_player_center_agent_register_page')){
                $displayReferralCode = FALSE;
                $this->utils->removeReferralCodeCookie();
            }
        }else{ // from player center
            if(!empty($_referral_code)){
                if($this->utils->isEnabledFeature('hidden_affiliate_code_on_player_center_when_exists_referral_code')){
                    $displayAffilateCode = FALSE;
                    $this->utils->clearTrackingCode();
                }

                if($this->utils->isEnabledFeature('hidden_agent_code_on_player_center_when_exists_referral_code')){
                    $displayAgencyCode = FALSE;
                    $this->utils->clearAgentTrackingCodeFromSession();
                    $this->utils->clearAgentTrackingSourceCodeFromSession();
                }

                $referral_code = $_referral_code;
                $this->utils->setReferralCodeCookie($_referral_code);
            }
        }

        // -- BTAG FOR INCOME ACCESS
		if($this->utils->isEnabledFeature('enable_income_access')){
			if (!empty($_btag)) $this->utils->setBtagCookie($_btag);
		}

		$data['btag'] = $_btag;

        $data['tracking_code'] = $tracking_code;
        $data['tracking_source_code'] = $tracking_source_code;
        $data['displayAffilateCode'] = (!empty($tracking_code) || !empty($tracking_source_code)) ? FALSE : $displayAffilateCode;

        $data['agent_tracking_code'] = $agent_tracking_code;
        $data['agent_tracking_source_code'] = $agent_tracking_source_code;
        $data['displayAgencyCode'] = (!empty($agent_tracking_code) || !empty($agent_tracking_source_code)) ? FALSE : $displayAgencyCode;

        $data['referral_code'] = $referral_code;
        $data['displayReferralCode'] = (!empty($referral_code)) ? FALSE : $displayReferralCode;
    }

	/**
	 * overview : iframe register send sms verification
	 *
	 * @param string $mobileNumber
	 */
	public function iframe_register_send_sms_verification($mobileNumber = null, $restrictArea = null) {

		$this->load->library(array('session', 'sms/sms_sender'));
		$this->load->model(array('sms_verification', 'player_model'));

		if ($this->utils->getConfig('disabled_sms')) {
			return $this->returnJsonResult(array('success' => false, 'message' => lang('Disabled SMS')));
		}

		$player_id = $this->authentication->getPlayerId();
		$player = $this->player_functions->getPlayerById($player_id);

		if (!isset($mobileNumber)) {
			# Try to get mobile number from player profile if not supplied
			$this->load->library(array('authentication', 'player_functions'));

			$mobileNumber = $player['contactNumber'];
			if (!$mobileNumber) {
				$this->returnJsonResult(array('success' => false, 'message' => lang('No contact number available')));
				return;
			}
		}

		if (!$this->check_sms_captcha()) {
			$this->returnJsonResult(array('success' => false, 'message' => lang('error.captcha'), 'isDisplay' => true, 'field' => 'captcha'));
			return;
		}

		$sessionId = $this->session->userdata('session_id');
		$lastSmsTime = $this->session->userdata('last_sms_time');
		$smsCooldownTime = $this->config->item('sms_cooldown_time');

		if(empty($lastSmsTime)){
			//load from redis
			$lastSmsTime=$this->utils->readRedis($mobileNumber.'_last_sms_time');
		}

		# Should not send SMS without valid session ID
		if(!$sessionId) {
			$this->returnJsonResult(array('success' => false, 'message' => lang('Unknown error')));
			return;
		}

		# Check the send count with ip or mobile on cooldown period
		if ($this->sms_verification->checkIPAndMobileLastTIme($smsCooldownTime, $mobileNumber)) {
			$this->returnJsonResult(array('success' => false, 'message' => sprintf(lang('It is not allowed to send twice within %s seconds if same IP/Phone no, please try again later.'), $smsCooldownTime), 'isDisplay' => true));
			return;
		}

		# This check ensures for a given session (i.e. session ID), SMS cannot be sent again within the cooldown period
		if ($lastSmsTime && time() - $lastSmsTime <= $smsCooldownTime) {
			$this->returnJsonResult(array('success' => false, 'message' => lang('You are sending SMS too frequently. Please wait.')));
			return;
		}

		$codeCount = $this->sms_verification->getVerificationCodeCountPastMinute();
		if($codeCount > $this->config->item('sms_global_max_per_minute')) {
			$this->utils->error_log("Sent [$codeCount] SMS in the past minute, exceeded config max [".$this->config->item('sms_global_max_per_minute')."]");
			$this->returnJsonResult(array('success' => false, 'message' => lang('SMS process is currently busy. Please wait.')));
			return;
		}

		$numCount = $this->sms_verification->getTodaySMSCountFor($mobileNumber);
		if($numCount >= $this->config->item('sms_max_per_num_per_day')) {
			$this->utils->error_log("Sent maximum [$numCount] SMS to this number today.");
			$this->returnJsonResult(array('success' => false, 'message' => sprintf(lang('One username is only allowed to send %s texts per day, please try again tomorrow.'), $this->config->item('sms_max_per_num_per_day')), 'isDisplay' => true));
			return;
		}

		// OGP-12 : Check for duplicate mobile number
		$playerFromMobile = $this->player_model->getPlayerLoginInfoByNumber($mobileNumber);
		if (is_array($playerFromMobile) && isset($playerFromMobile['playerId']) > 0 && $player_id != $playerFromMobile['playerId']) {
			$this->returnJsonResult(array('success' => false, 'message' => lang('The number is in use'), 'isDisplay' => true));
			return;
		}

		# Topic : Restrict send num -- By Bryson
		# 1. Use system_feature to control
		# 2. Setting restrict num in config must more than zero
		# 3. Note : Get request params is restrict_num to control (Currently only the player center's phone verification settings)
		$restrictSmsSendNum = (int) $this->utils->getConfig('sms_restrict_send_num');
		$enableRestrictSmsSendNum = $this->utils->isEnabledFeature('enable_restrict_sms_send_num_in_player_center_phone_verification');
		$isRestrictArea = $this->sms_verification->isRestrictArea($restrictArea);
		if ($isRestrictArea && $player_id && $enableRestrictSmsSendNum && $restrictSmsSendNum > 0) {
			$numDaily = $this->sms_verification->sendNumDaily($player_id, $restrictArea);
			if ($numDaily >= $restrictSmsSendNum) {
				$this->returnJsonResult(array('success' => false, 'message' => lang('Today has exceeded the sending limit')));
				return;
			}
		}
		if($restrictArea == NULL) {
			$restrictArea = sms_verification::USAGE_DEFAULT;
		}
		$code = $this->sms_verification->getVerificationCode($player_id, $sessionId, $mobileNumber, $restrictArea);
        $useSmsApi = $this->sms_sender->getSmsApiName();
        $msg = $this->utils->createSmsContent($code, $useSmsApi);

		if ($this->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
			$this->load->model('queue_result');
			$this->load->library('lib_queue');

			$mobileNum = $mobileNumber;
			$content = $msg;
			$callerType = Queue_result::CALLER_TYPE_PLAYER;
			$caller = $player_id;
			$state = null;

			$this->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);

			$this->session->set_userdata('last_sms_time', time());
			$this->utils->writeRedis($mobileNumber.'_last_sms_time', time());
			$this->returnJsonResult(array('success' => true));

		} else {

			if ($this->sms_sender->send($mobileNumber, $msg, $useSmsApi)) {
				$this->session->set_userdata('last_sms_time', time());
				$this->utils->writeRedis($mobileNumber.'_last_sms_time', time());
				$this->returnJsonResult(array('success' => true));
			} else {
				$this->returnJsonResult(array('success' => false, 'message' => $this->sms_sender->getLastError()));
			}

		}
	}

	/**
	 * overview : validate registration
	 *
	 * @param string $from_comapi
	 * @return array
	 */
	public function validate_registration($from_comapi = false) {

		$min_username_length = $this->utils->getConfig('default_min_size_username');
		$max_username_length = $this->utils->getConfig('default_max_size_username');
		$min_password_length = $this->utils->getConfig('default_min_size_password');
		$max_password_length = $this->utils->getConfig('default_max_size_password');

		$this->load->library(array('form_validation'));
		$this->load->model(array('operatorglobalsettings', 'registration_setting'));
		$regex_username = $this->utils->getUsernameReg();
		$regex_password = $this->utils->getPasswordReg();
		$this->form_validation->set_rules('username', lang('Username'), 'trim|xss_clean|required|alpha_numeric|min_length['.$min_username_length.']|max_length['.$max_username_length.']|regex_match['.$regex_username.']|callback_check_username');
		$this->form_validation->set_rules('password', lang('Password'), 'trim|xss_clean|required|min_length['.$min_password_length.']|max_length['.$max_password_length.']|regex_match['.$regex_password.']');
		$this->form_validation->set_rules('cpassword', lang('Confirm Password'), 'trim|xss_clean|required|min_length['.$min_password_length.']|max_length['.$max_password_length.']|matches[password]');

        if ($this->registration_setting->isRegistrationFieldRequired('Email')) {
            $this->form_validation->set_rules('email', lang('Email Address'), 'trim|xss_clean|required|valid_email|callback_check_email');
        } else {
            $this->form_validation->set_rules('email', lang('Email Address'), 'trim|xss_clean|valid_email|callback_check_email');
        }

		# NOTE: Currently, only using CNY
		# $this->form_validation->set_rules('currency', 'Currency', 'trim|xss_clean|required');

		# NOTE: Please arrange by registrationFieldId

		# regId.1
		if ($this->registration_setting->isRegistrationFieldRequired('First Name')) {
			$this->form_validation->set_rules('firstName', lang('First Name'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('firstName', lang('First Name'), 'trim|xss_clean');
		}

		# regId.2
		if ($this->registration_setting->isRegistrationFieldRequired('Last Name')) {
			$this->form_validation->set_rules('lastName', lang('Last Name'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('lastName', lang('Last Name'), 'trim|xss_clean');
		}

		# regId.3
		if ($this->registration_setting->isRegistrationFieldRequired('Birthday')) {
			$this->form_validation->set_rules('birthdate', lang('reg.fields.birthdate'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('birthdate', lang('reg.fields.birthdate'), 'trim|xss_clean');
		}

		# regId.4
		if ($this->registration_setting->isRegistrationFieldRequired('Gender')) {
			$this->form_validation->set_rules('gender', lang('reg.fields.gender'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('gender', lang('reg.fields.gender'), 'trim|xss_clean');
		}

		# regId.5
		if ($this->registration_setting->isRegistrationFieldRequired('Nationality')) {
			$this->form_validation->set_rules('citizenship', lang('reg.fields.citizenship'), 'trim|xss_clean|required|callback_check_country');
		} else {
			$this->form_validation->set_rules('citizenship', lang('reg.fields.citizenship'), 'trim|xss_clean|callback_check_country');
		}

		# regId.6
		if ($this->registration_setting->isRegistrationFieldRequired('BirthPlace')) {
			$this->form_validation->set_rules('birthplace', lang('reg.fields.birthplace'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('birthplace', lang('reg.fields.birthplace'), 'trim|xss_clean');
		}

		# regId.7
		if ($this->registration_setting->isRegistrationFieldRequired('Language')) {
			$this->form_validation->set_rules('language', lang('reg.fields.language'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('language', lang('reg.fields.language'), 'trim|xss_clean');
		}

		$playerValidate = $this->utils->getConfig('player_validator');
		$contactRule = isset($playerValidate['contact_number']) ? $playerValidate['contact_number'] : [];
		$contactMin  = isset($contactRule['min']) ? $contactRule['min'] : "";
		$contactMax  = isset($contactRule['max']) ? $contactRule['max'] : "";
		$contactLenRule = "";
		if (isset($contactMin, $contactMan) && $contactMin == $contactMax) {
			$contactLenRule .= "|exact_length[$contactMin]";
		} else {
			if (is_int($contactMin)) {
				$contactLenRule .= "|min_length[$contactMin]";
			}
			if (is_int($contactMax)) {
				$contactLenRule .= "|max_length[$contactMax]";
			}
		}

		# regId.8
		if ($this->registration_setting->isRegistrationFieldRequired('Contact Number')) {
			if ($this->utils->isEnabledFeature('allow_player_same_number')) {
				$this->form_validation->set_rules('contactNumber', lang('reg.fields.contactNumber'), 'trim|xss_clean' . $contactLenRule . '|required');
			} else {
				$this->form_validation->set_rules('contactNumber', lang('reg.fields.contactNumber'), 'trim|xss_clean' . $contactLenRule . '|required|callback_check_contact');
			}
		} else {
			if ($this->utils->isEnabledFeature('allow_player_same_number')) {
				$this->form_validation->set_rules('contactNumber', lang('reg.fields.contactNumber'), 'trim|xss_clean'. $contactLenRule);
			} else {
				$this->form_validation->set_rules('contactNumber', lang('reg.fields.contactNumber'), 'trim|xss_clean' . $contactLenRule . '|callback_check_contact');
			}
		}

		# regId.9
		if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 1')) {
			$this->form_validation->set_rules('imAccount', lang('reg.fields.imAccount'), 'trim|xss_clean|required|callback_differs_im[imAccount]');
			// $this->form_validation->set_rules('im_type', 'IM 1', 'trim|xss_clean|required|callback_check_im[imAccount]');
		} else {
			$this->form_validation->set_rules('imAccount', lang('reg.fields.imAccount'), 'trim|xss_clean|callback_differs_im[imAccount]');
			$this->form_validation->set_rules('im_type', 'IM 1', 'trim|xss_clean|callback_check_im[imAccount]');
		}

		# regId.10
		if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 2')) {
			$this->form_validation->set_rules('imAccount2', lang('reg.fields.imAccount2'), 'trim|xss_clean|required|callback_differs_im[imAccount2]');
			// $this->form_validation->set_rules('im_type2', 'IM 2', 'trim|xss_clean|required|callback_check_im[imAccount2]');
		} else {
			$this->form_validation->set_rules('imAccount2', lang('reg.fields.imAccount2'), 'trim|xss_clean|callback_differs_im[imAccount2]');
			$this->form_validation->set_rules('im_type2', 'IM 2', 'trim|xss_clean|callback_check_im[imAccount2]');
		}

		if ($this->registration_setting->isRegistrationFieldRequired('residentCountry')) {
			$this->form_validation->set_rules('residentCountry', lang('a_reg.33'), 'trim|xss_clean|required|callback_check_country');
		} else {
			$this->form_validation->set_rules('residentCountry', lang('a_reg.33'), 'trim|xss_clean|callback_check_country');
		}

		# regId.47
		if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 3')) {
			$this->form_validation->set_rules('imAccount3', lang('reg.fields.imAccount3'), 'trim|xss_clean|required|callback_differs_im[imAccount3]');
			// $this->form_validation->set_rules('im_type3', 'IM 3', 'trim|xss_clean|required|callback_check_im[imAccount3]');
		} else {
			$this->form_validation->set_rules('imAccount3', lang('reg.fields.imAccount3'), 'trim|xss_clean|callback_differs_im[imAccount3]');
			$this->form_validation->set_rules('im_type3', 'IM 3', 'trim|xss_clean|callback_check_im[imAccount3]');
		}

		# regId.11
		if ($this->registration_setting->isRegistrationFieldRequired('Security Question')) {
			$this->form_validation->set_rules('secretQuestion', lang('Security Question'), 'trim|xss_clean|required');
			$this->form_validation->set_rules('secretAnswer', lang('reg.42'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('secretQuestion', lang('Security Question'), 'trim|xss_clean');
			$this->form_validation->set_rules('secretAnswer', lang('reg.42'), 'trim|xss_clean');
		}

		# regId.13
		$this->form_validation->set_rules('invitationCode', lang('Referral code'), 'trim|xss_clean|callback_check_referral');

		# regId.14
		$this->form_validation->set_rules('tracking_code', lang('Affiliate code'), 'trim|xss_clean');

		# regId.31
		if ($this->registration_setting->isRegistrationFieldRequired('At Least 18 Yrs. Old and Accept Terms and Conditions')) {
			$this->form_validation->set_rules('terms', lang('xpj.footer_bar.links.terms'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('terms', lang('xpj.footer_bar.links.terms'), 'trim|xss_clean');
		}

		# regId.35
		if ($this->registration_setting->isRegistrationFieldRequired('Withdrawal Password')) {
			$this->form_validation->set_rules('withdrawPassword', lang('Withdraw Password'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('withdrawPassword', lang('Withdraw Password'), 'trim|xss_clean');
		}

		# regId.40
		if ($this->registration_setting->isRegistrationFieldRequired('Bank Name')) {
			$this->form_validation->set_rules('bankName', lang('Bank Name'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('bankName', lang('Bank Name'), 'trim|xss_clean');
		}

		# regId.41
		if ($this->registration_setting->isRegistrationFieldRequired('Bank Account Number')) {
			$this->form_validation->set_rules('bankAccountNumber', lang('a_reg.41'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('bankAccountNumber', lang('a_reg.41'), 'trim|xss_clean');
		}

		# regId.42
		if ($this->registration_setting->isRegistrationFieldRequired('Bank Account Name')) {
			$this->form_validation->set_rules('bankAccountName', lang('a_reg.42'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('bankAccountName', lang('a_reg.42'), 'trim|xss_clean');
		}

        # regId.37
        if ($this->registration_setting->isRegistrationFieldRequired('Region')) {
            $this->form_validation->set_rules('region', lang('a_reg.37.placeholder'), 'trim|xss_clean|max_length[120]|required');
        } else {
            $this->form_validation->set_rules('region', lang('a_reg.37.placeholder'), 'trim|xss_clean|max_length[120]');
        }

        # regId.36
        if ($this->registration_setting->isRegistrationFieldRequired('City')) {
            $this->form_validation->set_rules('city', lang('a_reg.36.placeholder'), 'trim|xss_clean|max_length[120]|required');
        } else {
            $this->form_validation->set_rules('city', lang('a_reg.36.placeholder'), 'trim|xss_clean|max_length[120]');
        }

		# regId.43
		if ($this->registration_setting->isRegistrationFieldRequired('Address')) {
			$this->form_validation->set_rules('address', lang('a_reg.43.placeholder'), 'trim|xss_clean|max_length[120]|required');
		} else {
			$this->form_validation->set_rules('address', lang('a_reg.43.placeholder'), 'trim|xss_clean|max_length[120]');
		}

		# regId.44
		if ($this->registration_setting->isRegistrationFieldRequired('Address2')) {
			$this->form_validation->set_rules('address2', lang('a_reg.44.placeholder'), 'trim|xss_clean|max_length[120]|required');
		} else {
			$this->form_validation->set_rules('address2', lang('a_reg.44.placeholder'), 'trim|xss_clean|max_length[120]');
		}

		# regId.48
		if ($this->registration_setting->isRegistrationFieldRequired('Zip Code')) {
			$this->form_validation->set_rules('zipcode', lang('a_reg.48'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('zipcode', lang('a_reg.48'), 'trim|xss_clean');
		}

		# regId.49
		if ($this->registration_setting->isRegistrationFieldRequired('ID Card Number')) {
			$this->form_validation->set_rules('id_card_number', lang('a_reg.49'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('id_card_number', lang('a_reg.49'), 'trim|xss_clean');
		}

		# regId.50
		if ($this->registration_setting->isRegistrationFieldRequired('Dialing Code')) {
			$this->form_validation->set_rules('dialing_code', lang('a_reg.50'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('dialing_code', lang('a_reg.50'), 'trim|xss_clean');
		}

		# regId.51
		if ($this->registration_setting->isRegistrationFieldRequired('ID Card Type')) {
			$this->form_validation->set_rules('id_card_type', lang('a_reg.51'), 'trim|xss_clean|required');
		} else {
			$this->form_validation->set_rules('id_card_type', lang('a_reg.51'), 'trim|xss_clean');
		}

		// Skip SMS Verification code if invoked from Api_common - OGP-14163
		if (empty($from_comapi)) {
			if (!$this->utils->getConfig('disabled_sms')) {
				if ($this->registration_setting->isRegistrationFieldRequired('SMS Verification Code') && $this->registration_setting->isRegistrationFieldVisible('Contact Number')) {
					$this->form_validation->set_rules('sms_verification_code', lang('SMS Verification Code'), 'trim|xss_clean|required|callback_check_sms_verification[contactNumber]');
				} else {
					$this->form_validation->set_rules('sms_verification_code', lang('SMS Verification Code'), 'trim|xss_clean|callback_check_sms_verification[contactNumber]');
				}
			} else {
				$this->form_validation->set_rules('sms_verification_code', lang('SMS Verification Code'), 'trim|xss_clean');
			}
		}

		// if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled')) {
        if ($this->utils->getConfig('enabled_registration_captcha') && $this->operatorglobalsettings->getSettingJson('registration_captcha_enabled') && empty($from_comapi)){
			$this->form_validation->set_rules('captcha', lang('label.captcha'), 'trim|xss_clean|required|callback_check_captcha');
		}

        if ($this->registration_setting->isRegistrationFieldRequired('Newsletter Subscriptions')) {
            $this->form_validation->set_rules('newsletter_subscription', lang('a_reg.52'), 'trim|xss_clean|required');
        } else {
            $this->form_validation->set_rules('newsletter_subscription', lang('a_reg.52'), 'trim|xss_clean');
        }

		//set form validation language
		$this->form_validation->set_message('matches', lang('formvalidation.matches'));
		$this->form_validation->set_message('min_length', lang('formvalidation.min_length'));
		$this->form_validation->set_message('max_length', lang('formvalidation.max_length'));
		$this->form_validation->set_message('required', lang('formvalidation.required'));
		$this->form_validation->set_message('isset', lang('formvalidation.isset'));
		$this->form_validation->set_message('valid_email', lang('formvalidation.valid_email'));
		$this->form_validation->set_message('valid_emails', lang('formvalidation.valid_emails'));
		$this->form_validation->set_message('exact_length', lang('formvalidation.exact_length'));
		$this->form_validation->set_message('alpha', lang('formvalidation.alpha'));
		$this->form_validation->set_message('alpha_numeric', lang('formvalidation.alpha_numeric'));
		$this->form_validation->set_message('alpha_dash', lang('formvalidation.alpha_dash'));
		$this->form_validation->set_message('numeric', lang('formvalidation.numeric'));
		$this->form_validation->set_message('is_numeric', lang('formvalidation.is_numeric'));
		$this->form_validation->set_message('regex_match', lang('formvalidation.regex_match'));
		$this->form_validation->set_message('is_unique', lang('formvalidation.is_unique'));
		$this->form_validation->set_message('less_than', lang('formvalidation.is_unique'));
		$this->form_validation->set_message('greater_than', lang('formvalidation.is_unique'));

		$result = $this->form_validation->run();

		return $result;
	}

	/**
	 * overview : check username
	 *
	 * @param string	$username
	 * @return bool
	 */
	public function check_username($username) {
		# TODO: MOVE TO CONFIG OR OPERATOR SETTINGS OR REGISTRATION SETTINGS?
		$forbidden_names = array('admin', 'moderator', 'hoster', 'administrator', 'mod');
		if (in_array($username, $forbidden_names)) {
			$this->form_validation->set_message('check_username', "<b>" . $username . "</b> " . lang('notify.4'));
			return false;
		}
		$this->load->model(['player_model']);
		$result = !$this->player_model->checkUsernameExist($username);
		if (!$result) {
			$this->form_validation->set_message('check_username', "<b>" . $username . "</b> " . lang('notify.3'));
		}
		return $result;

	}

	/**
	 * overview : check email
	 *
	 * @param string	$email
	 * @return bool
	 */
	public function check_email($email) {
		$this->load->model(['player_model']);
		$result = empty($email) ? : !$this->player_model->checkEmailExist($email);
		if (!$result) {
			$this->form_validation->set_message('check_email', "" . $email . " " . lang('notify.5'));
		}
		return $result;
	}

	/**
	 * overview : check contact
	 *
	 * @param $contact_number
	 * @return bool
	 */
	public function check_contact($contact_number) {
		if (!empty($contact_number)) {
			$this->load->model(['player_model']);
			$result = !$this->player_model->checkContactExist($contact_number);
			if (!$result) {
				$this->form_validation->set_message('check_contact', lang('The contact number has been used'));
			}
			return $result;
		} else {
			return true;
		}
	}

	/**
	 * overview : check country
	 *
	 * @param $country
	 * @return bool
	 */

	public function check_country($country) {
		if (!empty($country)) {
			$result = in_array($country,$this->utils->getCountryList());
			if (!$result) {
				$this->form_validation->set_message('check_country',"<b>" . $country . "</b> ".lang('this country is not on the list'));
			}
			return $result;
		} else {
			return true;
		}
	}

	/**
	 * overview : check referral
	 *
	 * @param string 	$referral_code
	 * @return bool
	 */
	public function check_referral($referral_code) {
		if (!empty($referral_code)) {
			$this->load->library('player_functions');
			$result = $this->player_functions->checkIfReferralCodeExist($referral_code);
			if (!$result) {
				$this->form_validation->set_message('check_referral', "<b>" . $referral_code . "</b> " . lang('notify.6'));
			}
			return !!$result;
		} else {
			return true;
		}
	}

	/**
	 * overview : check captcha
	 *
	 * @param string	$val
	 * @return bool
	 */
	public function check_captcha($val, $namespace = 'default') {
		$this->utils->debug_log('check_captcha', $val, $this->session->all_userdata());

		$this->load->model('operatorglobalsettings');
		$rlt = true;
		if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled') || $this->operatorglobalsettings->getSettingJson('login_captcha_enabled')) {

			$active = $this->config->item('si_active');
			$allsettings = array_merge($this->config->item($active), $this->config->item('si_general'), ['namespace' => $namespace]);

			$this->load->library('captcha/securimage');
			$securimage = new Securimage($allsettings);

			$this->utils->debug_log('check captcha session',$this->session->userdata('auth_flag'), $this->session->all_userdata(), $this->input->post('captcha'));

			$rlt = $securimage->check($this->input->post('captcha'));

			$this->utils->debug_log('result', $rlt);
			if(!$rlt){
				$this->form_validation->set_message('check_captcha', lang('error.captcha'));
			}
		}
		return $rlt;
	}

	/**
	 * overview : check captcha
	 *
	 * @param string	$val
	 * @return bool
	 */
	public function check_sms_captcha() {

		$this->load->model('operatorglobalsettings');

		$this->load->library('captcha/sms_securimage');

		$securimage = new Sms_securimage();

		$this->utils->debug_log('check captcha session',$this->session->userdata('auth_flag'), $this->session->all_userdata(), $this->input->post('sms_captcha'));

		$rlt = $securimage->check($this->input->post('sms_captcha'));

		$this->utils->debug_log('result', $rlt);

		return $rlt;
	}

	/**
	 * overview : check im
	 *
	 * @param $im_type
	 * @param $field
	 * @return bool
	 */
	public function check_im($im_type, $field) {

		$im_account = $this->input->post($field);

		if (isset($im_account)) {
			switch ($im_type) {
			case 'QQ':
				$this->form_validation->set_message('check_im', lang('formvalidation.numeric'));
				return $this->form_validation->numeric($im_account);

			case 'MSN':
				$this->form_validation->set_message('check_im', lang('valid_email'));
				return $this->form_validation->valid_email($im_account);
			}
		}

		return true;
	}

	/**
	 * callback method for verifying IM account duplication, used in validate_registration
	 * rewritten to support 3 instant messengers (OGP-2594)
	 *
	 * @param	string	$str	default arg provided by CI validator
	 * @param	string	$field	what field is this
	 *                      (NOTE: different with old differs_im())
	 * @see		player_auth_module::validate_registration()
	 * @return	bool	true if validation OK (no duplication); otherwise false
	 */
	public function differs_im($str, $field) {
		$im_acc_fields = [ 'imAccount', 'imAccount2', 'imAccount3' ];
		$im_acc_vals = [];

		// Collect imAccount* values
		foreach ($im_acc_fields as $key => $im_acc_field) {
			// excluding current $field
			if ($im_acc_field == $field) {
				unset($im_acc_fields[$key]);
				continue;
			}
			$im_acc_vals[$im_acc_field] = $this->input->post($im_acc_field);
		}
		$this->utils->debug_log('DIFFERS_IM', 'im_acc_vals', $im_acc_vals);

		// Match $str against collected imAccount* values
		$dup = false;
		if (empty($str) || count($im_acc_fields) == 0) {
			// If $str empty, assert false directly
		}
		else {
			foreach ($im_acc_vals as $im_acc_field => $im_acc_val) {
				if (empty($im_acc_val)) {
					// Target empty => no need to check
					continue;
				}
				else if ($im_acc_val == $str) {
					$this->form_validation->set_message('differs_im', lang('notify.7'));
					$dup = true;
					break;
				}
			}
		}

		$success = !$dup;
		$this->utils->debug_log('DIFFERS_IM', 'success', $success);
		return $success;
	}

	/**
	 * overview : check sms verification
	 *
	 * @param string	$sms_verification_code
	 * @param $field
	 * @return bool
	 */
	public function check_sms_verification($sms_verification_code, $field) {

		$this->load->library('session');
		$this->load->model('sms_verification');

		$playerId = $this->authentication->getPlayerId();
		$session_id = $this->session->userdata('session_id');
		$contact_number = $this->input->post($field);
		$result = !isset($sms_verification_code) || $this->sms_verification->validateVerificationCode($playerId, $session_id, $contact_number, $sms_verification_code);
		if (!$result) {
			$this->form_validation->set_message('check_sms_verification', lang('Invalid SMS verification code'));
		}
		return $result;

	}

	/**
	 * overview : post registere player
	 */
	public function postRegisterPlayer() {
		$this->output->set_header('Access-Control-Allow-Origin:*');
		$this->output->set_header("Access-Control-Allow-Headers: X-Requested-With");
		$this->utils->debug_log('POSTREGISTERPLAYER post', $this->utils->filterPasswordLogs($this->input->post()));

        if($this->authentication->isLoggedIn()){
            return $this->goPlayerHome();
        }

		if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($this->input->post())) {
		    return $this->returnJsonResult(false);
		}

        //check ip limit
        $ip=$this->utils->getIP();
        $type='register';
        $this->load->model(['player_model']);
        $err=null;
        $reached=$this->player_model->reachedIpLimitHourlyBy($ip, $type, $err);
        if($reached===null){
            return show_error($err, 500);
        }
        if($reached===true){
            $this->utils->error_log('reached ip limit, blocked', $ip, $type);
            //block
            return show_error('Reached limit, try later', 403);
        }

		# LIBRARIES
		$this->load->library(array('session', 'sms/sms_sender'));
		$this->load->model(array('group_level', 'agency_model','communication_preference_model', 'gbg_logs_model', 'email_template_model', 'acuris_logs_model'));


		$tracking_code = $this->input->post('tracking_code');
		if(empty($tracking_code)){
			$tracking_code=$this->getTrackingCode();
		}
		$tracking_source_code = $this->input->post('tracking_source_code');
		if(empty($tracking_source_code)){
			$tracking_source_code=$this->getTrackingSourceCode();
		}

		$agent_tracking_code = $this->input->post('agent_tracking_code');
		if(empty($agent_tracking_code)){
			$agent_tracking_code=$this->getAgentTrackingCode();
		}
		$agent_tracking_source_code = $this->input->post('agent_tracking_source_code');
		if(empty($agent_tracking_source_code)){
			$agent_tracking_source_code=$this->getAgentTrackingSourceCode();
		}

		# VALIDATE
		$validation_result = $this->validate_registration();

        if ($this->utils->isEnabledFeature('enable_username_cross_site_checking')) {
            $username = $this->input->post('username', true);
            if ($this->player_model->checkCrossSiteByUsername($username, true)) {
                return $this->iframe_register($tracking_code, $tracking_source_code, $agent_tracking_code, $agent_tracking_source_code);
            }
        }

        if (!$validation_result) {
			$message = validation_errors();
			$this->utils->debug_log('POSTREGISTERPLAYER error', $message);

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}

			if( empty( $message ) ) $this->session->unset_userdata('result');

            return $this->iframe_register($tracking_code, $tracking_source_code, $agent_tracking_code, $agent_tracking_source_code);
        }

        if($this->player_model->isReachDailyIPAllowedRegistrationLimit($this->utils->getIp())){
            $message = lang('reg.reach_limit_of_single_ip_registrations_per_day');
            $this->utils->debug_log('POSTREGISTERPLAYER error', $message);

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

            if ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

            if( empty( $message ) ) $this->session->unset_userdata('result');

            return $this->iframe_register($tracking_code, $tracking_source_code, $agent_tracking_code, $agent_tracking_source_code);
        }

        # REQUEST PARAMETERS
        $username = strtolower($this->input->post('username'));

        $this->utils->debug_log('player_auth_module::postRegisterPlayer()', 'tracking_code', $tracking_code, 'tracking_source_code', $tracking_source_code,
            'agent_tracking_code', $agent_tracking_code, 'agent_tracking_source_code', $agent_tracking_source_code);

        $prefixOfPlayer=$this->getPrefixUsernameOfPlayer($tracking_code);
        //add to username
        if(!empty($prefixOfPlayer)){
            $username=$prefixOfPlayer.$username;
        }

        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $contact_number = $this->input->post('contactNumber');
        $im_type = $this->input->post('im_type');
        $im_type2 = $this->input->post('im_type2');
        $im_account = $this->input->post('imAccount');
        $im_account2 = $this->input->post('imAccount2');
        $im_account3 = $this->input->post('imAccount3');
        $referral_code = $this->input->post('invitationCode');
        $sms_verification_code = $this->input->post('sms_verification_code');
        $levels_options = $this->input->post('levels_options');
        $withdraw_password = $this->input->post('withdrawPassword');

        // bank info
        $bank_name = $this->input->post('bankName');
        $bank_account_num = $this->input->post('bankAccountNumber');
        $bank_account_name = $this->input->post('firstName');


        //add tracking banner
        $visit_record_id = $this->session->userdata('visit_record_id');

        $httpHeadrInfo = $this->session->userdata('httpHeaderInfo') ?: $this->utils->getHttpOnRequest();
        $header_referrer = preg_replace('/\s+/', '', $httpHeadrInfo['referrer']);
        $referrer = $header_referrer ?: $_SERVER['HTTP_REFERER'];

        // -- Process Communication Preference
        $config_prefs = $this->utils->getConfig('communication_preferences');
        $communication_preference = null;

        if($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($config_prefs))
            $communication_preference = $this->communication_preference_model->getCommunicationPreferenceChanges($this->input->post());

        $player_data = array(
            # Player
            'username' => $username,
            'gameName' => $username,
            'email' => $email,
            'password' => $password,
            'secretQuestion' => $this->input->post('secretQuestion'),
            'secretAnswer' => str_replace('%20', ' ', $this->input->post('secretAnswer')),
            'verify' => $this->player_functions->getRandomVerificationCode(),
            'withdraw_password' => $withdraw_password,


            # Player Details
            'firstName' => $this->input->post('firstName'),
            'lastName' => $this->input->post('lastName'),
            'language' => $this->input->post('language'),
            'gender' => $this->input->post('gender'),
            'birthdate' => $this->input->post('birthdate'),
            'contactNumber' => $this->input->post('contactNumber'),
            'citizenship' => $this->input->post('citizenship'),
            'imAccount' => $this->input->post('imAccount'),
            'imAccountType' => $this->input->post('im_type'),
            'imAccount2' => $this->input->post('imAccount2'),
            'imAccountType2' => $this->input->post('im_type2'),
            'imAccount3' => $this->input->post('imAccount3'),
            'imAccountType3' => $this->input->post('im_type3'),
            'birthplace' => $this->input->post('birthplace'),
            'registrationIp' => $this->utils->getIP(),
            'registrationWebsite' => $referrer,
            'residentCountry' => $this->input->post('residentCountry'),
            'city' => $this->input->post('city'),
            'address' => $this->input->post('address'),
            'address2' => $this->input->post('address2'),
            'address3' => $this->input->post('address3'),
            'zipcode' => $this->input->post('zipcode'),
            'dialing_code' => $this->input->post('dialing_code'),
            'id_card_number' => $this->input->post('id_card_number'),
            # Codes
            'tracking_code' => $tracking_code,
            'tracking_source_code' => $tracking_source_code,
            'agent_tracking_code' => $agent_tracking_code,
            'agent_tracking_source_code' => $agent_tracking_source_code,

            # SMS verification
            'verified_phone' => !empty($sms_verification_code),

            'visit_record_id' => $visit_record_id,
            'newsletter_subscription' => $this->input->post('newsletter_subscription') == 'on' ? 1 : 0,
            'communication_preference' => $communication_preference
        );

        $this->utils->removeReferralCodeCookie();
        $this->utils->removeBtagCookie();

        $refereePlayerId = null;

        if (!empty($referral_code)){
            $refereePlayerId = $this->player_model->getPlayerIdByReferralCode($referral_code);
        }
        if (!empty($refereePlayerId)){
            $player_data['refereePlayerId'] = $refereePlayerId;
            $player_data['referral_code'] = $referral_code;
        }

        if($this->utils->isEnabledFeature('enable_income_access') && $this->input->post('btag')){
            $player_data['btag'] = $this->input->post('btag');
        }

        # REGISTER
        $playerId = null;

        if($this->utils->isEnabledFeature('enable_username_cross_site_checking')){
            //global lock
            $add_prefix = false;
            $anyid = 0;
        } else {
            //not global lock
            $add_prefix = true;
            $anyid = random_string('numeric', 5);
        }

        $controller = $this;
        $this->lockAndTrans(Utils::GLOBAL_LOCK_ACTION_REGISTRATION, $anyid, function () use ($controller, $player_data,&$playerId) {
            $playerId = $controller->player_model->register($player_data, false, true, false, false);
            return (!empty($playerId)) ? true : false;
        },$add_prefix);

        if(!empty($playerId)){
			//sync
			$this->syncPlayerCurrentToMDBWithLock($playerId, $username, true);

			// -- Save HTTP Request after lock/transaction
			$this->load->model(['http_request','player_attached_proof_file_model']);
			$this->utils->saveHttpRequest($playerId, Http_request::TYPE_REGISTRATION);

			// -- save default attached file status history
			$this->player_attached_proof_file_model->saveAttachedFileStatusHistory($playerId);
		}

        if(empty($playerId)){
            $message = lang('Register failed, try it later');
            $this->utils->debug_log('POSTREGISTERPLAYER error', $message);

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

            if ($this->input->is_ajax_request()) {
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

            if( empty( $message ) ) $this->session->unset_userdata('result');

            return $this->iframe_register($tracking_code, $tracking_source_code, $agent_tracking_code, $agent_tracking_source_code);
        }

        if($this->utils->isEnabledFeature('enable_player_registered_send_msg')) {

            $this->load->model(['cms_model', 'player_model', 'queue_result']);
            $this->load->library(["lib_queue"]);
            $sms_template = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_REGISTERED);

            $searchArr=['{player_username}', '{player_center_url}', '{mobile_number}'];
            $replaceArr=[ $username, $this->utils->getSystemUrl('player'), $contact_number];
            $content = str_replace($searchArr, $replaceArr, $sms_template);

            if ($this->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
                $isVerifiedPhone = $this->player_model->isVerifiedPhone($playerId);
                if ($content && $isVerifiedPhone) {
                    $mobileNum = $contact_number;
                    $callerType = Queue_result::CALLER_TYPE_PLAYER;
                    $caller = $playerId;
                    $state = null;

                    $this->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);
                }
            } else {
                $this->sms_sender->send($contact_number, $content);
            }
        }

        if(!empty($bank_account_num) && !empty($bank_account_name)) {
            $banks = array( 0 => 'Deposit', 1 => 'Withdraw' );
            foreach( $banks as $dwBank => $bank) {
                $data = array(
                    'playerId' => $playerId,
                    'bankTypeId' => $bank_name,
                    'bankAccountNumber' => $bank_account_num,
                    'bankAccountFullName' => $bank_account_name,
                    'bankAddress' => '',
                    'city' => '',
                    'province' => '',
                    'branch' => '',
                    'isRemember' => 1, // 0 is false
                    'dwBank' => "$dwBank", // 1 withdraw , 0 deposit
                    'status' => '0', // 0 is active
                );
                $this->player_functions->addBankDetailsByWithdrawal($data);
            }
        }

        if ($this->operatorglobalsettings->getSettingJson('login_after_registration_enabled')) {

            # LOGIN
            $this->authentication->login($username, $password);
            # MARK AS NEW USER
            $this->session->set_userdata('new_user', true);

            # PROCESS PROMO IF THERE'S ANY (CODE DOES NOT MAKE SENSE)
            if (!empty($this->session->userdata('promoCode')) && $this->session->userdata('promoCode') == 'depAFae') {
                $message = lang('notify.8'); # UNUSED VARIABLE
            }

        }

        $this->utils->clearTrackingCode();
        $this->utils->clearAgentTrackingCodeFromSession();
        $this->utils->clearAgentTrackingSourceCodeFromSession();

        $this->session->unset_userdata('httpHeaderInfo');

        // SEND MESSAGE TO PLAYER EMAIL FOR ACCOUNT VERIFICATION
        $email_sent_token = null;
        if (!empty($email)) {
			#sending email
			$this->load->library(['email_manager']);
	        $template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $playerId));
	        $template_enabled = $template->getIsEnableByTemplateName(true);
	        if($template_enabled['enable']){
	        	$email = $this->player->getPlayerById($playerId)['email'];
	        	$email_sent_token = $template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $playerId);
	        }
        }


        if ($levels_options){ // if select level options
            $this->group_level->startTrans();
            $this->group_level->adjustPlayerLevel($playerId, $levels_options);
            $this->group_level->endTrans();
        }

        if ($this->input->is_ajax_request()) {
            $this->returnJsonResult(array('status' => 'success', 'msg' => lang('notify.9')));
            return;
        }

        if (!$this->utils->isEnabledFeature('enable_registered_show_success_popup')) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('notify.9'));
        }

        if ($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->operatorglobalsettings->getSettingJson('generate_pep_gbg_auth_after_registration_enabled') && $this->utils->isEnabledFeature('show_pep_authentication')) {
            $gbg_response = $this->gbg_logs_model->generate_gbg_authentication($playerId);
            $this->utils->debug_log('PEP Authentication response', $gbg_response);
        }

        if ($this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->operatorglobalsettings->getSettingJson('generate_c6_acuris_auth_after_registration_enabled') && $this->utils->isEnabledFeature('show_c6_authentication')) {
            $gbg_response = $this->acuris_logs_model->generate_acuris_authentication($playerId);
            $this->utils->debug_log('C6 Authentication response', $gbg_response);
        }

        $this->utils->debug_log('============================triggerRegisterEvent');
        $this->triggerRegisterEvent($playerId);

        if (!$this->operatorglobalsettings->getSettingJson('login_after_registration_enabled')) {
            $this->goPlayerLogin();
            return;
        }

        if ($this->input->post('goto_url') ){
            redirect($this->input->post('goto_url'));
        }

        if ($email_sent_token) {
            $this->goActiveAccount($playerId);
        } else {
            $this->goPlayerHome();
        }
	}

	/**
	 * overview : activate email
	 *
	 * @return  page
	 */
	public function iframe_activate() {
		$this->load->model(['wallet_model']);
		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_functions->getPlayerById($player_id);
		$origin = '*';
		$result = array('act' => 'iframe_login');
		$result['success'] = true;
		$result['keepOpen'] = true;
		$player = $this->player_functions->getPlayerById($player_id);
		$result['playerName'] = $player['username'];
		$result['playerId'] = $player_id;
		//add token
        $result['token'] = $this->authentication->getPlayerToken();
		$data['result'] = str_replace("'", "\\'", json_encode($result));
		$data['origin'] = $origin;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['playerStatus']=null;
		$this->loadTemplate(lang('title.activate_account'), '', '', 'player');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/auth/activate_account', $data);
		$this->template->render();
	}

	/**
	 * Change status on active field player table
	 *
	 * @return  page
	 */
	public function resendEmail($from_security_setting = false) {
		$this->load->model('email_template_model');

		$playerId = $this->getLoggedPlayerId();
		#sending email
		$this->load->library(['email_manager']);
        $template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $playerId));
        $template_enabled = $template->getIsEnableByTemplateName(true);
        if ($template_enabled['enable']) {
        	$email = $this->player->getPlayerById($playerId)['email'];
        	$template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $playerId);
        	$message = lang('notify.96');
	        if($from_security_setting){
				$result = ['success' => TRUE, 'message' => $message];
				$this->returnJsonResult($result);
				return;
	        }
	        else{
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
	        }
        } else {
        	$message = lang('Security') . ' - ' . lang('Email Verification') .'<br>'. $template_enabled['message'];
        	$this->alertMessage(self::MESSAGE_TYPE_WARNING, $message);

	        if($from_security_setting){
				$result = ['success' => FALSE, 'message' => $message];
				$this->returnJsonResult($result);
				return;
	        }
        }

		$this->goPlayerHome();
	}

	/**
	 * overview : verify Email for registration or promo application
	 *
	 * @param 	int  $id  player id
	 * @param 	string  $promoCode  player promo id
	 * @return  rendered Template
	 */
	public function verify($id, $promoCode = '') {
		$this->load->model(array('player_model','email_template_model'));
		$this->load->library(array('session'));
		$this->utils->debug_log("player verify email processing id [$id]");

		# logout current player first
		if($this->authentication->isLoggedIn()) {
			$this->authentication->logout();
			$this->session->sess_create();
		}

		if(empty($id)){
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.113'));
			$this->goPlayerHome();
		}

		# Refer to: Email_template_player_verify_email::getVerifyLink
		# first 32 digits are md5 encoded playerId
		# last 32 digits are md5 encoded expired_time
		# in the middle is playerId
		$encoded_playerId     = substr($id, 0, 32);
		$encoded_expired_time = substr($id, -32);
		$playerId             = substr($id, 32, -32);

		if(empty($playerId)) {
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.113'));
			$this->goPlayerHome();
		}

		$this->utils->debug_log("verifying playerId [$playerId]");

		if(md5($playerId) != $encoded_playerId){
			$this->utils->debug_log("verifying playerId [$id], encoded_playerId is wrong");
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.113'));
			$this->goPlayerHome();
		}

		$player = $this->player_model->getPlayerById($playerId);
		if(md5($player->email_verify_exptime) != $encoded_expired_time){
			$this->utils->debug_log("verifying playerId [$id], encoded_expired_time is wrong");
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.113'));
			$this->goPlayerHome();
		}

		$now = new DateTime();
		$expired_time = new DateTime($player->email_verify_exptime);
		if($now > $expired_time){
			$this->utils->debug_log("verifying playerId [$id], link expired");
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.112'));
			$this->goPlayerHome();
		}

		$verified = $this->player_model->isVerifiedEmail($playerId);
		if($verified){
			$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.12'));
		} else {
			# set to verified
			$success = $this->player_model->verifyEmail($playerId);
			if ($success) {
				// -- add player update history
				$username = !$this->authentication->getUsername() ? $this->player_model->getUsernameById($playerId) : $this->authentication->getUsername();
				$this->savePlayerUpdateLog($playerId, lang('Email verified by player: ') . ' ' . $username, $username);
				$this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);

				# sending email
				$this->load->library(['email_manager']);
		        $template = $this->email_manager->template('player', 'player_verify_email_success', array('player_id' => $playerId));
		        $template_enabled = $template->getIsEnableByTemplateName(true);
		        if ($template_enabled['enable']) {
		        	$email = $this->player->getPlayerById($playerId)['email'];
		        	$template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $playerId);
		        }

				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('notify.11'));
			} else {
				$this->alertMessage(self::MESSAGE_TYPE_WARNING, lang('notify.113'));
			}
		}

		$this->goPlayerHome();
	}

	/**
	 * overview : logout
	 */
	public function iframe_logout() {
        $logout_result = $this->authentication->logout();

        $origin = '*';

        $result = [
            'act' => 'iframe_logout',
            'success' => true,
            'message' => lang('logout_successfully'),
            'redirect_url' => $logout_result['redirect_url']
        ];

        $data = array('result' => str_replace("'", "\\'", json_encode($result)), 'origin' => $origin);
		$this->load->view('player/iframe_callback', $data);
	}

	/**
	 * update phone verification flag
	 *
	 * @param  string $contact_number
	 * @param  string $sms_verification_code
	 * @return json
	 */
	public function update_sms_verification($contact_number, $sms_verification_code, $restrict_area = null) {

		$playerId = $this->authentication->getPlayerId();
		$this->load->library('session');
		$this->load->model(['sms_verification','player_model']);

		$session_id = $this->session->userdata('session_id');

		$success = false;
		$message=lang('Verify SMS Code Failed');
		if(!empty($playerId) && !empty($session_id) && !empty($contact_number) && !empty($sms_verification_code)){

			$success = !isset($sms_verification_code) || $this->sms_verification->validateVerificationCode($playerId, $session_id, $contact_number, $sms_verification_code, $restrict_area);

			if(!$success) {
				$this->utils->debug_log('========== validate sms_verification_code from back office =====', $success);
				// validte verification code from back office
				$success = $this->sms_verification->validateVerificationCode($playerId, null, $contact_number, $sms_verification_code);
			}

			$this->utils->debug_log('========== sms_verification_code result =====', $success);
			if($success){
				$success=$this->player_model->updateAndVerifyContactNumber($playerId, $contact_number);
				if(!$success){
					$message=lang('Verify SMS Code Failed');
				}
			}else{
				$message=lang('Verify SMS Code Failed');
			}

			if($success){
				$username= $this->authentication->getUsername();
				$this->savePlayerUpdateLog($playerId, lang('Phone verified by player: ') . ' ' . $username, $username);
				$message=lang('Verify SMS Code Successfully');

				$this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);
			}
		}

		$result = ['success'=>$success, 'message'=>$message];

		$this->returnJsonResult($result);
	}

	/**
     * @deprecated should be replaced by player_center/profile
	 * view playere settings or details
	 *
	 *
	 * @return rendered template
	 */
	public function iframe_playerSettings() {
		$player_id = $this->authentication->getPlayerId();
		$data['current_promo'] = "";
		$data['player'] = $this->player_functions->getPlayerById($player_id);
		$data['bank_details'] = $this->player_functions->getBankDetails($player_id);
		$data['currentLang'] = $this->language_function->getCurrentLanguage();
		$data['default_prefix_for_username'] = $this->config->item('default_prefix_for_username');
		$data['fields'] = $this->getVisibleFielsForPlayer();
		if (!empty($this->session->userdata('promoCode'))) {
			$promo = $this->player_functions->checkPromoCodeExist($this->session->userdata('promoCode'));
			$data['current_promo'] = $this->player_functions->checkIfAlreadyGetPromo($player_id, $promo['promoId']);
		}

		$this->loadTemplate(lang('pi.16'), '', '', 'settings');
		$this->template->write_view('main_content',  $this->utils->getPlayerCenterTemplate() . '/player/view_player_settings', $data);
		$this->template->render();
	}

	/**
	 * Detect inputs has XSS ?
	 * @param array $inputs $_GET or $_POST, usually apply into the return of $this->getInputGetAndPost().
	 * @return array $return The formats,
	 * - $return['result'] boolean Return true while XSS detected else false.
	 * - $return['inupts'] array  depend by param, $inputs, ex:
	 * $return['inupts'][foo]['orig'] = 'boo'
	 * $return['inupts'][foo]['clean'] = 'boo'
	 * $return['inupts'][foo]['hasXSS'] = false
	 * $return['inupts'][foo1]['orig'] = '<script> alert(document.domain) </script>'
	 * $return['inupts'][foo1]['clean'] = '[removed] alert&#40;document.domain&#41; [removed]'
	 * $return['inupts'][foo1]['hasXSS'] = true
	 * ...
	 *
	 */
	public function detectXSSWithParams($inputs){
		$return = array();
		$returnBool = false;
		$returnInupts = array();
		reset ($inputs);

		foreach ($inputs as $key => $value) {
			$xssClean = $this->security->xss_clean($value);
			$returnInupts[$key]['orig'] = $inputs[$key];
			$returnInupts[$key]['clean'] = $xssClean;

			if( $xssClean == $value){ // without xss
				$returnInupts[$key]['hasXSS'] = false;
				$returnBool = $returnBool || false;
			}else{ // has xss
				$returnInupts[$key]['hasXSS'] = true;
				$returnBool = $returnBool || true;
				break;
			}
		} // EOF while ( list($key, $value) = each ($inputs) )
			$return['result'] = $returnBool;
			$return['inupts'] = $returnInupts;
			return $return;

	}// EOF detectXSSWithParams

	public function postEditPlayer() {

		$this->utils->debug_log('enter postEditPlayer');

		if (empty($_POST) && empty($_FILES)) {
			return $this->_saveProfileSuccess();
		}

        $this->formRulesEditPlayer();
		$inputs = $_REQUEST; // orig inputs.
		$hasXSS = $this->detectXSSWithParams($inputs);
		/// Ref. to
		// https://3v4l.org/SWdT0#v500
		// https://blag.kazeno.net/development/access-private-protected-properties
		//
		// suppert for PHP 5.0.0 - 5.6.40, hhvm-3.10.1 - 3.22.0, 7.0.0 - 7.4.0alpha1
		$fv = (array)$this->form_validation;
		$fieldData = $fv["\0*\0" .'_field_data']; /// get protected attr.

		if( $hasXSS['result'] ) {

			// Get Field Name has XSS.
			$hasXSSInputKey = '';
			reset ($hasXSS['inupts']);
			while ( list($key, $value) = each ($hasXSS['inupts']) ) {
				if( $value['hasXSS'] ){
					$hasXSSInputKey = $key;
					break;
				}
			}
			$label = 'Inputs';
			// Get label of the field.
			if( ! empty($fieldData[$hasXSSInputKey]) ){
				$label = $fieldData[$hasXSSInputKey]['label'];
			}

			$this->utils->debug_log('profile form validation has XSS: ', $hasXSS); // 可以提示輸入的狀況，跟有問題的狀況。
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			$message = '<ul class="validation_errors"><li class="error_entry">'.$label.' is unacceptable.</li></ul>'; //@todo 提示什麼欄位，系統無法接受（私下是 XSS 的疑慮不用告訴玩家）
			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			$this->profile();
		}else if ($this->form_validation->run() == false) {
			$this->utils->debug_log('profile form validation', $this->form_validation->error_string());

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $message = '<ul class="validation_errors">' . validation_errors('<li class="error_entry">', '</li>') . '</ul>';

			if ($this->input->is_ajax_request()) {
				$this->returnJsonResult(array('status' => 'error', 'msg' => $message));
				return;
			}

			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

			$this->profile();
		} else {
			$player_id = $this->authentication->getPlayerId();

			$address 			= $this->input->post('address');
			$address2 			= $this->input->post('address2');
			$address3 			= $this->input->post('address3');
			$zipcode 			= $this->input->post('zipcode');
			$city 				= $this->input->post('city');
			$language 			= $this->input->post('language');
			$citizenship 		= $this->input->post('citizenship');
            $dialing_code    	= $this->input->post('dialing_code');
			$contact_number 	= $this->input->post('contact_number');
			$im_account 		= $this->input->post('im_account');
			$im_account2 		= $this->input->post('im_account2');
			$im_account3 		= $this->input->post('im_account3');
			$birthplace 		= $this->input->post('birthplace');
			$today 				= date("Y-m-d H:i:s");

			# Special fields, only allow edit when original value is empty
			$firstName 			= $this->input->post('name');
			$lastName 			= $this->input->post('lastname');
			$gender 			= $this->input->post('gender');
			$birthdate 			= $this->input->post('birthdate');
			$email 				= $this->input->post('email');
			$id_card_number 	= $this->input->post('id_card_number');
            $id_card_type       = $this->input->post('id_card_type');
            $residentCountry    = $this->input->post('residentCountry');

			$data['currentLang'] = $this->language_function->getCurrentLanguage();
			$player = $this->player_functions->getPlayerById($this->authentication->getPlayerId());

			$data = ['updatedOn' => $today];

			$player_email = array();

			$this->player_functions->editPlayer($data, $this->authentication->getPlayerId());

			if (empty($language)) {
				if(!empty($player['language'])){
					$language = $player['language'];

				}else{
					$language = $this->utils->getConfig('default_player_language');
				}

			}

			$data = array();

            $this->load->model(array('registration_setting'));

			# Editing of special fields

			if (!empty($firstName) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'firstName')) {
				$data['firstName'] = $firstName;
			}


			if (!empty($lastName) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'lastName')) {
				$data['lastName'] = $lastName;
			}


			if (!empty($birthdate) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'birthdate')) {
				$data['birthdate'] = $birthdate;
			}


			if (!empty($gender) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'gender')) {
				$data['gender'] = $gender;
			}

            if (!empty($language) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'language')) {
                $data['language'] = $language;
            } else if (!empty($player['language'])) {
                $data['language'] = $language;
            }

            $disable_email_edit = $player['verified_email'];
            if (!empty($email) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'email', $disable_email_edit)) {
				$player_email['email'] = $email;
			}

            $disable_contact_number_edit = ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone') || $player['verified_phone']);
            if (!empty($contact_number) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'contactNumber', $disable_contact_number_edit)) {
                $data['contactNumber'] = $contact_number;
            }

            if (!empty($dialing_code) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'dialing_code')) {
                $data['dialing_code'] = $dialing_code;
            }

            $disable_im_edit = ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
			if (!empty($im_account) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'imAccount', $disable_im_edit)) {
			    $data['imAccount'] = $im_account;
			}

			if (!empty($im_account2) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'imAccount2', $disable_im_edit)) {
			    $data['imAccount2'] = $im_account2;
			}

			if (!empty($im_account3) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'imAccount3', $disable_im_edit)) {
			    $data['imAccount3'] = $im_account3;
			}

			if (!empty($address) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'address')) {
				$data['address'] = $address;
			}
			if (!empty($address2) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'address2')) {
				$data['address2'] = $address2;
			}

			if (!empty($address3) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'address3')) {
				$data['address3'] = $address3;
			}

			if (!empty($zipcode) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'zipcode')) {
				$data['zipcode'] = $zipcode;
			}

			if (!empty($city) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'city')) {
				$data['city'] = $city;
			}

			if (!empty($citizenship) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'citizenship')) {
				$data['citizenship'] = $citizenship;
			}

            if (!empty($birthplace) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'birthplace')) {
                $data['birthplace'] = $birthplace;
            }

            if (!empty($residentCountry) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'residentCountry')) {
                $data['residentCountry'] = $residentCountry;
            }

			if (!empty($id_card_number) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'id_card_number')) {
				$data['id_card_number'] = $id_card_number;
			}

            if (!empty($id_card_number) && $this->registration_setting->checkAccountInfoFieldAllowEdit($player,'id_card_type')) {
                $data['id_card_type'] = $id_card_type;
            }
			//set language
			$this->language_function->setCurrentLanguage($this->language_function->langStrToInt($language));

			//save changes to playerupdatehistory
			$modifiedFields = $this->checkModifiedFields($player_id, $data);
			$this->savePlayerUpdateLog($player_id, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername());

			// Update player preferences (OGP-2700 and on)
			$enable_auto_transfer_postval = $this->input->post('enable_auto_transfer');
			if (!empty($enable_auto_transfer_postval)) {
				$this->load->model('player_preference');
				$enable_auto_transfer_prefval = ($enable_auto_transfer_postval == 'yes');
				$this->player_preference->storePrefItem($player_id, 'auto_transfer', $enable_auto_transfer_prefval);
			}

			#OGP-13922 if xinyan status = 1 and edit player name or contactNumber, update status to enable the xinyan validation button
			if($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) {
				$this->load->model(array('player_model','player_api_verify_status'));

				$status           = $this->player_api_verify_status->getApiStatusByPlayerId($player_id);
				$getcontactNumber = $this->player_model->getPlayerContactNumber($player_id);
				$playerDetails 	  = $this->CI->player_model->getPlayerDetails($player_id);
				$getPlayerName    = $playerDetails[0]['firstName'];

				if($status == player_api_verify_status::API_RESPOSE_SUCCESS){
					if($contact_number && $contact_number != $getcontactNumber){
						$this->player_api_verify_status->updateApiStatusByPlayerId($player_id, player_api_verify_status::API_UNKNOWN);
						$this->CI->utils->debug_log('===========================contact_number update xinyan status success by player ');
					}
					if($firstName && $firstName != $getPlayerName){
						$this->player_api_verify_status->updateApiStatusByPlayerId($player_id, player_api_verify_status::API_UNKNOWN);
						$this->CI->utils->debug_log('===========================firstName update xinyan status success by player');
					}
				}
			}


			if (!empty($data)) {
				$this->player_functions->editPlayerDetails($data, $this->authentication->getPlayerId());
			}

			if (!empty($player_email)) {
				$this->player_functions->editPlayerEmail($player_email, $this->authentication->getPlayerId());
			}

			//sync
			$this->load->model(['player_model']);
			$username=$this->player_model->getUsernameById($player_id);
			$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

			$uploadProfile = $this->uploadProfilePicture();
			if ($uploadProfile['status'] == 'error') {
				$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $uploadProfile['msg']);
				$this->goPlayerSettings($this->authentication->getPlayerId());
				return;
			}

			return $this->_saveProfileSuccess();
		}
	}

	protected function _saveProfileSuccess(){
		$message = lang('notify.24');

		if ($this->input->is_ajax_request()) {
			$this->returnJsonResult(array('status' => 'success', 'msg' => $message));
			return;
		}

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

		$this->goPlayerSettings($this->authentication->getPlayerId());
	}

	public function registration_successful(){
		$player_id = $this->authentication->getPlayerId();
		$data['player'] = $this->player_model->getPlayerInfoDetailById($player_id);

		$this->loadTemplate(lang('Register'), '', '', '');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/register_success', $data);
		$this->template->render();
	}

	public function check_login_contact_number($contact_number){
		if (!empty($contact_number)) {
			//exist
			$this->load->model(['player_model']);
			$result = $this->player_model->checkContactExist($contact_number);
			if (!$result) {
				$this->form_validation->set_message('check_login_contact_number', $contact_number . " " . lang('gen.error.not_exist'));
			}
			return $result;
		} else {
			return true;
		}
	}

    public function view_mobile_login(){
        $this->output->set_header('Access-Control-Allow-Origin:*');
        $this->load->model(array('operatorglobalsettings', 'country_rules'));
        $this->load->library('user_agent');

        $data['playercenter_logo'] = $this->utils->getPlayerCenterLogoURL();

        $data['snackbar'] = isset($snackbar) ? $snackbar : [];
        $data['currentLang'] = $this->language_function->getCurrentLanguage();
        $data['forget_password_enabled'] = $this->operatorglobalsettings->getSettingJson('forget_password_enabled');
        $data['login_captcha_enabled'] = $this->operatorglobalsettings->getSettingJson('login_captcha_enabled');
        $data['remember_password_enabled'] = $this->operatorglobalsettings->getSettingJson('remember_password_enabled');
        $this->session->set_userdata('currentLanguage', $this->language_function->getCurrentLanguage());

        $template = $this->utils->getPlayerCenterTemplate();

        $title=lang('lang.login_title');
        $view=$template . '/auth/login_mobile_account';
        $activenav = '';
        $description = '';
        $keywords = '';

        $this->template->set_template($this->utils->getPlayerCenterTemplate(false));

        # scripts that was originally written in template
        $base_path = base_url() . $this->utils->getPlayerCenterTemplate();
        $this->template->add_js('/resources/third_party/jquery-wizard/0.0.7/jquery.wizard.min.js');
        $this->template->write('skin', 'template1.css');
        $this->template->write('title', $title);
        $this->template->write('description', $description);
        $this->template->write('keywords', $keywords);
        $this->template->write('activenav', $activenav);
        $this->template->write_view('main_content', $view, $data);
        $this->template->render();
    }

	public function mobile_login() {
        $this->load->model('operatorglobalsettings');
        $this->form_validation->set_rules('contact_number', lang('Mobile Number'), 'trim|required|xss_clean|callback_check_login_contact_number');
        $this->form_validation->set_rules('sms_verification_code', lang('SMS Code'), 'trim|required|xss_clean|callback_check_sms_verification[contact_number]');

        $result=[];

        if ($this->form_validation->run()) {
            $contact_number = $this->input->post('contact_number');

            $this->load->library(array('player_library'));

            $login_result = $this->player_library->login_by_contact_number($contact_number);

            if(!$login_result['success']){
                $result['success'] = FALSE;
                $result['message'] = (is_array($login_result['errors']) && !empty($login_result['errors'])) ? end($login_result['errors']) : $login_result['errors'];

                return $result;
            }

            $next_url = $this->input->post('goto_url');
            if(empty($next_url)){
                $next_url = $this->utils->getPlayerHomeUrl();
            }

            $result['success'] = TRUE;
            $result['message'] = lang('Login successfully');
            $result['next_url'] = site_url($next_url);
        } else {
            $result['success'] = false;
            $result['message'] = lang('Login failed, wrong mobile number or SMS code');
        }

        $this->utils->debug_log($result);

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, $result['message'], $result);
	}

	/**
	 * Sends an email with basic template
	 *
	 * @param  string $player_id Player's ID
	 * @param  string $email_template Chosen email template
	 * @param  string $subject Email subject
	 * @return boolean           Email sending status
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
	 */
	public function sendEmailWithBasicTemplate($player_id, $email_template, $subject) {
		$this->load->model(array('queue_result', 'player'));

		$player_info = $this->player->getPlayerById($player_id); // get player info

		// -- Email Template
		$emailTemplate = $this->email_setting->getEmailTemplatePromo($email_template);

		$replaced_variables = str_replace("[playername]", $player_info['lastName'] . ' ' . $player_info['firstName'], $emailTemplate);
		$replaced_variables = str_replace("[username]", $player_info['username'], $replaced_variables);

		$body = '<html><body>' . $replaced_variables['template'] . '</body></html>';

		$token = $this->utils->sendMail($player_info['email'], null, null, $subject, $body, Queue_result::CALLER_TYPE_PLAYER, $player_id);
		$this->utils->debug_log('email', $player_info['email'], 'query token', $token);

		return $token;
	}
}
////END OF FILE/////////
