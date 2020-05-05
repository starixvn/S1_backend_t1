<?php

/**
 * Mobile registration API suite, version 1
 * OGP-7822, built on xcyl's request
 * Reengineered as OGP-7983
 *
 * @see		OGP-7983
 * @see		tit_comapi_module_mobile_reg2
 */
trait t1t_comapi_module_mobile_reg1 {

	/**
	 * OGP-7822, rewritten from createPlayer()
	 * Register player by mobile number.  Sends a validation code by SMS on registration.
	 * The new player is not valid until activated using mobileActivatePlayer().
	 *
	 * @return	JSON	Standard return of {"success" (bool), "code" (int), "mesg" (string), "result" (mixed)}
	 */
	public function mobileCreatePlayer() {
		$input = $this->input->post();
		$api_key = $this->input->post('api_key');
		unset($input['api_key']);

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$post_short =  array_slice($this->input->post(), 0, 36, 'preserve_keys');
			$this->utils->debug_log(__FUNCTION__, 'request', $post_short);

			$this->load->library([ 'session', 'comapi_lib' ]);
			$this->load->model([ 'registration_setting', 'player_model' ]);

			// OGP-5172 workaround
			$validation_result = $this->validate_registration('skip_captcha');

			if (!$validation_result) {
				$validation_errors = trim(strip_tags(validation_errors()));
				$res = explode("\n", $validation_errors);

				throw new Exception('Registration validation error', self::CODE_MPC_REG_VALIDATION_ERROR);
			}

			$contact_number = $this->input->post('contactNumber');
			if (empty($contact_number)) {
				throw new Exception('Contact number (mobile number) missing', self::CODE_MPC_CONTACT_NUMBER_MISSING);
			}

			// REQUEST PARAMETERS
			$username 			= strtolower($this->input->post('username'));
			$password 			= $this->input->post('password');

			$httpHeadrInfo 		= $this->session->userdata('httpHeaderInfo') ? : $this->utils->getHttpOnRequest();
            $header_referrer	= preg_replace('/\s+/', '', $httpHeadrInfo['referrer']);
            $referrer			= $header_referrer ?: $_SERVER['HTTP_REFERER'];

            // Workaround - use before finding a better approach
			// error_reporting(0);

			// REGISTER
			// 7/16: Following fields are added to address API ajax return issue on staging:
			// newsletter_subscription, imAccount3, imAccountType3, city,
			// address, address2, address3, zipcode, dialing_code, id_card_number
			$player_id_arr = $this->player_model->register(
				[
					// Player
					'username' 				=> $username,
					'gameName' 				=> $username,
					'password' 				=> $password,
					'email' 				=> $this->input->post('email'),
					'secretQuestion' 		=> $this->input->post('security_question'),
					'secretAnswer' 			=> $this->input->post('security_answer'),
					'newsletter_subscription' => null ,
					'verify' 				=> $this->player_functions->getRandomVerificationCode(),

					// Player Details
					'firstName' 			=> $this->input->post('firstName'),
					'lastName' 				=> $this->input->post('lastName'),
					'language' 				=> $this->input->post('language'),
					'gender' 				=> $this->input->post('gender'),
					'birthdate' 			=> $this->input->post('birthdate'),
					'contactNumber' 		=> $this->input->post('contactNumber'),
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
					'tracking_code' 		=> $this->input->post('tracking_code'),
					'agent_tracking_code'	=> $this->input->post('agent_tracking_code'),

					// SMS verification
					'verified_phone' 		=> ! empty($sms_verification_code),
				] ,
				false, true, false, "api/{$this->router->method}"
			);

			if ((is_array($player_id_arr) && count($player_id_arr) != 1) || (!is_array($player_id_arr) && empty($player_id_arr))) {
				throw new Exception('Registration failure', self::CODE_MPC_REG_FAILURE);
			}

			if (is_array($player_id_arr)) {
				$player_id = $player_id_arr[0];
			}
			else {
				$player_id = $player_id_arr;
			}

            //sync
            $this->load->model(['multiple_db_model']);
            $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($player_id, true);
            $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB', $rlt);

			$this->utils->debug_log(__FUNCTION__, [ 'player_id_arr' => $player_id_arr, 'player_id' => $player_id ]);

			// Block player after successful registration
			$this->player_model->blockPlayerWithGame($player_id);

			// $this->comapi_sms_validate->set_code();
			$contactNumber = $this->input->post('contactNumber');
			$send_res = $this->comapi_lib->mobileCreatePlayer_send_sms($player_id, $contactNumber);

			$this->utils->debug_log(__METHOD__, 'mobileCreatePlayer_send_sms return', $send_res);

			if ($send_res['code'] != 0) {
				$res = [ 'mesg' => $send_res['mesg'], 'mesg_debug' => $send_res['mesg_debug'] ];
				throw new Exception($send_res['mesg'], $send_res['code'] + 0x1a0);
			}

			// save http_request (cookies, referer, user-agent)
			$this->saveHttpRequest($player_id, Http_request::TYPE_REGISTRATION);
			// $this->session->unset_userdata('httpHeaderInfo');

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> 'Registration successful, please activate your account',
	    		'result'	=> null
			];

			$this->returnApiResponseByArray($ret);
		}
		catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $post_short);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> $res
	    	];

	    	$this->returnApiResponseByArray($ret);
	    }

	} // End function mobileCreatePlayer()

	public function mobileActivatePlayer() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$this->load->model([ 'player_model', 'sms_verification' ]);

		$res = null;

		try {
			$username	= trim($this->input->post('username'));
			$val_code	= trim($this->input->post('val_code'));

			$creds = [ 'api_key' => $api_key, 'username' => $username , 'val_code' => $val_code ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_MPC_PLAYER_UNKNOWN);
			}

			$player_blocked = $this->player_model->isBlocked($player_id);
			if (!$player_blocked) {
				throw new Exception('Player seems already activated', self::CODE_MPC_PLAYER_ALREADY_ACTIVATED);
			}

			$player_details = $this->player_model->getAllPlayerDetailsById($player_id);
			$contact_number = $player_details['contactNumber'];
			$session_id = '';
			// $session_id = $this->session->userdata('session_id');

			$this->utils->debug_log(__METHOD__, 'contact_number', $contact_number, 'session_id', $session_id);

			$val_res = $this->sms_verification->validateVerificationCode($player_id, $session_id, $contact_number, $val_code, Sms_verification::USAGE_COMAPI_MOBILE_REG);

			if (!$val_res) {
				throw new Exception('Validation failed', self::CODE_MPC_VALIDATION_FAILED);
			}

			$this->player_model->unblockPlayerWithGame($player_id);

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> 'SMS validation complete, welcome to the site',
	    		'result'	=> $res
			];

		}
		catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> $res
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function mobileActivatePlayer()

	public function mobileRevalidatePlayer() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model', 'sms_verification' ]);

			$username				= trim($this->input->post('username'));
			$contact_number_post	= trim($this->input->post('contactNumber'));

			$creds = [ 'api_key' => $api_key, 'username' => $username , 'contact_number_post' => $contact_number_post ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_MPC_PLAYER_UNKNOWN);
			}

			$player_blocked = $this->player_model->isBlocked($player_id);
			if (!$player_blocked) {
				throw new Exception('Player seems already activated', self::CODE_MPC_PLAYER_ALREADY_ACTIVATED);
			}

			$player_details = $this->player_model->getAllPlayerDetailsById($player_id);
			$contactNumber = $player_details['contactNumber'];
			if ($contactNumber != $contact_number_post) {
				throw new Exception('Wrong contact number', self::CODE_MPC_CONTACT_NUMBER_WRONG);
			}

			$player_details = $this->player_model->getAllPlayerDetailsById($player_id);
			$contactNumber = $player_details['contactNumber'];

			$send_res = $this->comapi_lib->mobileCreatePlayer_send_sms($player_id, $contactNumber);

			if ($send_res['code'] != 0) {
				$res = [ 'mesg' => $send_res['mesg'], 'mesg_debug' => $send_res['mesg_debug'] ];
				throw new Exception($send_res['mesg'], $send_res['code'] + 0x1a0);
			}

			$ret = $send_res;
		}
		catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> $res
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function mobileRevalidatePlayer()

	public function mobilePlayerValidationStatus() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model', 'sms_verification' ]);

			$username		= trim($this->input->post('username'));

			$creds = [ 'api_key' => $api_key, 'username' => $username ];
			$this->utils->debug_log(__FUNCTION__, 'request', $creds);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_MPC_PLAYER_UNKNOWN);
			}

			// Whether player can login
			$player_blocked = $this->player_model->isBlocked($player_id);
			$text_player_can_login = !$player_blocked ? 'can' : 'can not';

			// We use no session id now, but still leave it blank
			// See Comapi_lib::mobileCreatePlayer_send_sms()
			$session_id = '';
			// $session_id = $this->session->userdata('session_id');

			$player_details = $this->player_model->getAllPlayerDetailsById($player_id);
			$contact_number = $player_details['contactNumber'];

			// Read the status
			$code_count = (int) $this->sms_verification->playerValidationStatus($session_id, $contact_number, $player_id,Sms_verification::USAGE_COMAPI_MOBILE_REG);
			$text_code_count = $code_count > 0 ? 'one' : 'no';

			$ret_mesg = "Player {$text_player_can_login} login." .
				"  Player has {$text_code_count} validation code pending";

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> $ret_mesg,
	    		'result'	=> [ 'player_can_login' => !$player_blocked, 'validation_code_pending' => $code_count > 0  ]
			];
		}
		catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> $res
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	} // End function mobileRevalidatePlayer()


} // End of trait t1t_comapi_module_mobile_reg1