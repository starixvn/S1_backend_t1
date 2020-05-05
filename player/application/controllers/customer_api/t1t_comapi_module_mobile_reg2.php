<?php

/**
 * Mobile registration API suite, version 2
 * OGP-7983, built on xcyl's request
 * Reengineered from OGP-7822
 *
 * @see		OGP-7822
 * @see		t1t_comapi_module_mobile_reg1
 */
trait t1t_comapi_module_mobile_reg2 {

	/**
	 * Send validation code to given mobile number (contactNumber)
	 *
	 * @uses	POST:api_key		api key given by system
	 * @uses	POST:contactNumber	Player's mobile number
	 *
	 * @return	JSON	Standard JSON return structure
	 */
	public function mobileRegSendSms() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->library([ 'session', 'comapi_lib' ]);

			$contact_number = trim($this->input->post('contactNumber'));
			$session_id 	= $this->session->userdata('session_id');
			$creds = [ 'api_key' => $api_key, 'contactNumber' => $contact_number, 'session_id' => $session_id  ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			if (empty($contact_number)) {
				throw new Exception('contactNumber missing', self::CODE_MPC_CONTACT_NUMBER_MISSING);
			}

			if (!preg_match('/^\d+$/', $contact_number)) {
				throw new Exception('contactNumber contains invalid chars', self::CODE_MREG_MOBILE_NUMBER_INVALID);
			}

			$send_res = $this->comapi_lib->mobileCreatePlayer_send_sms(NULL, $contact_number, $session_id);

			$this->utils->debug_log(__METHOD__, 'mobileCreatePlayer_send_sms return', $send_res);

			if ($send_res['code'] != 0) {
				$res = [ 'mesg' => $send_res['mesg'], 'mesg_debug' => $send_res['mesg_debug'] ];
				throw new Exception($send_res['mesg'], $send_res['code'] + 0x1a0);
			}

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => 'Validation SMS sent successfully',
			    'result'    => $send_res
			];

			$this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function mobileRegSendSms()

	/**
	 * Verifies SMS validation code and create player if success
	 *
	 * @uses	POST:api_key		api key given by system
	 * @uses	POST:contactNumber	Player's mobile number
	 * @uses	POST:val_code		The SMS validation code
	 *
	 * @return [type] [description]
	 */
	public function mobileRegCreatePlayer() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'sms_verification', 'player_model' ]);
			$this->load->library([ 'session' ]);

			$contact_number = trim($this->input->post('contactNumber'));
			$val_code		= trim($this->input->post('val_code'));
			$referral_code	= trim($this->input->post('referral_code'));
			$tracking_code	= trim($this->input->post('tracking_code'));
			$levels_options	= trim($this->input->post('levels_options'));
			$session_id 	= $this->session->userdata('session_id');
			$creds = [ 'api_key' => $api_key, 'contactNumber' => $contact_number, 'val_code' => $val_code, 'session_id' => $session_id ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			// Check contact_number
			if (empty($contact_number)) {
				throw new Exception('contactNumber missing', self::CODE_MPC_CONTACT_NUMBER_MISSING);
			}

			if (!preg_match('/^\d+$/', $contact_number)) {
				throw new Exception('contactNumber contains invalid chars', self::CODE_MREG_MOBILE_NUMBER_INVALID);
			}

			if (!$this->utils->isEnabledFeature('allow_player_same_number') && $this->player_model->checkContactExist($contact_number)) {
				throw new Exception('contactNumber is already in use', self::CODE_MPC_MOBILE_NUMBER_IN_USE);
			}

			// Check validation code
			if (empty($val_code)) {
				throw new Exception('val_code missing', self::CODE_MREG_VALIDATION_CODE_MISSING);
			}

			if (!preg_match('/^\w+$/', $val_code)) {
				throw new Exception('val_code contains invalid chars', self::CODE_MREG_VALIDATION_CODE_INVALID);
			}

			$val_res = $this->sms_verification->validateVerificationCode(null, $session_id, $contact_number, $val_code, Sms_verification::USAGE_COMAPI_MOBILE_REG);

			if (!$val_res) {
				throw new Exception('Code validation failed', self::CODE_MPC_VALIDATION_FAILED);
			}

			// ------ BEGIN PLAYER REGISTRATION -----

			// Determine username
			if($this->utils->isEnabledFeature('use_mobile_number_as_username')){
				$username=$contact_number;
			}else{
				$username=$this->player_model->generateUsernameByMobileNumber($contact_number,
					$this->utils->getConfig('mobile_random_username_size'));
			}

			// Determine password
			$password = $this->utils->generateRandomCode(8);

			// User history, referrer, etc
			$visit_record_id = $this->session->userdata('visit_record_id');
			$httpHeadrInfo = $this->session->userdata('httpHeaderInfo') ?: $this->utils->getHttpOnRequest();
			$referrer = $httpHeadrInfo['referrer'] ?: $_SERVER['HTTP_HOST'];

			// $refereePlayerId = null;
			// if( $referral_code ){
			// 	$refereePlayerId = $this->player_model->getPlayerIdByReferralCode($referral_code);
			// }

			if(empty($tracking_code) && !empty($referrer)){
				$host=parse_url($referrer, PHP_URL_HOST);
				if(!empty($host)){
					$tracking_code=$this->affiliatemodel->getTrackingCodeFromAffDomain($host);
					if(empty($tracking_code)){
						$tracking_code='';
					}
				}
			}

			$this->log(__FUNCTION__, 'save tracking code to new player', [ 'tracking_code' => $tracking_code, 'username' => $username ]);

			$player_id_arr = $this->player_model->register(
				[
					// Player
					'username' 				=> $username,
					'gameName' 				=> $username,
					'password' 				=> $password,
					'email' 				=> $username.$this->utils->getConfig('mobile_fake_email') ,
					'secretQuestion' 		=> '' ,
					'secretAnswer' 			=> '' ,
					'newsletter_subscription' => null ,
					'verify' 				=> $this->player_model->getRandomVerificationCode(),

					// Player Details
					'firstName' 			=> $this->input->post('firstName'),
					'lastName' 				=> $this->input->post('lastName'),
					'language' 				=> $this->input->post('language'),
					'gender' 				=> $this->input->post('gender'),
					'birthdate' 			=> $this->input->post('birthdate'),
					'contactNumber' 		=> $contact_number,
					'citizenship' 			=> $this->input->post('citizenship'),
					'imAccount' 			=> $this->input->post('im_account'),
					'imAccountType' 		=> $this->input->post('im_type'),
					'imAccount2' 			=> $this->input->post('im_account2'),
					'imAccountType2' 		=> $this->input->post('im_type2'),
					'imAccount3'			=> $this->input->post('im_account3') ,
					'imAccountType3'		=> $this->input->post('im_type3') ,
					'birthplace' 			=> $this->input->post('birthplace'),
					'registrationIp' 		=> $this->utils->getIP(),
					'registrationWebsite' 	=> $referrer,
					'residentCountry' 		=> $this->input->post('resident_country'),
					'city'					=> $this->input->post('city') ,
					'address'				=> $this->input->post('address') ,
					'address2'				=> $this->input->post('address2') ,
					'address3'				=> $this->input->post('address3') ,
					'zipcode'				=> $this->input->post('zipcode') ,
					'dialing_code'			=> $this->input->post('dialing_code') ,
					'id_card_number'		=> $this->input->post('id_card_number') ,

					// Codes
					'referral_code' 		=> $this->input->post('referral_code'),
					'affiliate_code' 		=> $this->input->post('affiliate_code'),
					'tracking_code' 		=> $tracking_code ,
					'agent_tracking_code'	=> $this->input->post('agent_tracking_code'),

					// SMS verification
					'verified_phone' 		=> true ,
					'visit_record_id'		=> $visit_record_id ,
					// 'refereePlayerId'		=> $refereePlayerId
				] ,
				false, true, false, "api/{$this->router->method}"
			);

			if (count($player_id_arr) != 1) {
				throw new Exception('Registration failure', self::CODE_MPC_REG_FAILURE);
			}
			$player_id = $player_id_arr[0];

            //sync
            $this->load->model(['multiple_db_model']);
            $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($player_id, true);
            $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB', $rlt);

			$this->authentication->login($username, $password);
			$token = $this->authentication->getPlayerToken();
			if (empty($token)) {
				throw new Exception('Registration failure, player not created', self::CODE_MREG_REG_FAILURE_PLAYER_NOT_CREATED);
			}

			$this->session->set_userdata('new_user', true);
			$this->clearTrackingCode();
			$this->session->unset_userdata('httpHeaderInfo');
			$this->session->unset_userdata('passed_captcha');

			if($levels_options){ // if select level options
				$this->group_level->startTrans();
				$this->group_level->adjustPlayerLevel($player_id, $levels_options);
				$this->group_level->endTrans();
			}

			$reg_res = [
				'username'	=> $username ,
				'player_id'	=> $player_id ,
				'token'		=> $token
			];

			// ------ POINT OF EXECUTION SUCCESS ------

			$ret = [
			    'success'   => true,
			    'code'      => self::CODE_SUCCESS,
			    'mesg'      => 'Player created sucessfully',
			    'result'    => $reg_res
			];

			$this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

			$ret = [
			    'success'   => false,
			    'code'      => $ex->getCode(),
			    'mesg'      => $ex->getMessage(),
			    'result'    => $res
			];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function mobileRegCreatePlayer()

} // End of trait t1t_comapi_module_mobile_reg2