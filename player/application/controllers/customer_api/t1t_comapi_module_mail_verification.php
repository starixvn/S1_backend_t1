<?php

/**
 * Mail address verification API suite
 * OGP-13669
 */
trait t1t_comapi_module_mail_verification {

	public function mailVerifySend() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model' ]);
			$this->load->library([ 'email_manager' ]);

			$username	= trim($this->input->post('username', true));
			$token      = trim($this->input->post('token', true));

			$request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
			$this->comapi_log(__METHOD__, 'request', $request);

			// Check username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception(lang('Username invalid'), self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
            }

			// Check template status
			$template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $player_id));
        	$template_enabled = $template->getIsEnableByTemplateName(true);
        	if (!$template_enabled['enable']) {
        		throw new Exception(lang('Mail template disabled by system'), self::CODE_MVAL_TEMPLATE_DISABLED);
        	}

			$player = $this->player_model->getPlayerById($player_id);

			// Check player's mail address
			$player_email = $player->email;
			if (empty(filter_var($player_email, FILTER_VALIDATE_EMAIL))) {
				throw new Exception(lang('Player email invalid or not set'), self::CODE_MVAL_PLAYER_EMAIL_INVALID);
			}

			// Check player's mail verification status
			$mail_verified = $player->verified_email;
			if ($mail_verified) {
				throw new Exception(lang('Player email is already verified'), self::CODE_MVAL_PLAYER_EMAIL_ALREADY_VERIFIED);
			}

            $template->sendingEmail($player_email, Queue_result::CALLER_TYPE_PLAYER, $player_id);

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> lang('Validation mail sent successfully.'),
	    		'result'	=> null
			];
		}
		catch (Exception $ex) {
	    	$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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
	} // End function mailVerifySend()

	public function mailVerifyStatus() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model' ]);
			$this->load->library([ 'email_manager' ]);

			$username		= trim($this->input->post('username'));

			$request = [ 'api_key' => $api_key, 'username' => $username ];
			$this->comapi_log(__METHOD__, 'request', $request);

			// Check username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception(lang('Username invalid'), self::CODE_COMMON_INVALID_USERNAME);
			}


			// Check template status
			$template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $player_id));
        	$template_enabled = $template->getIsEnableByTemplateName(true);
        	if (!$template_enabled['enable']) {
        		throw new Exception(lang('Mail template disabled by system'), self::CODE_MVAL_TEMPLATE_DISABLED);
        	}

			// Check player's mail verification status
			$player = $this->player_model->getPlayerById($player_id);
			$mail_verified = $player->verified_email;

			if (!$mail_verified) {
				throw new Exception(lang('Player email is not verified yet'), self::CODE_MVAL_PLAYER_EMAIL_NOT_VERIFIED);
			}

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> lang('Player email address is verified.'),
	    		'result'	=> null
			];
		}
		catch (Exception $ex) {
	    	$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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
	} // End function mailVerifyStatus()

	/**
	 * Serves as alternate mail verification endpoint other than /iframe_module/verify
	 *
	 * @param  [type] $vtoken     [description]
	 * @param  [type] $promo_code [description]
	 * @see		player_auth_module::verify()
	 * @see
	 * @return [type]             [description]
	 */
	public function mailVerify($vtoken, $promo_code = null) {
		$this->load->model(array('player_model','email_template_model'));
		$this->load->library(array('session'));
		$this->comapi_log(__METHOD__, [ 'vtoken' => $vtoken, 'promo_code' => $promo_code ]);

		try {
			// Logout first
			if($this->authentication->isLoggedIn()) {
				$this->authentication->logout();
				$this->session->sess_create();
			}

			// Check for vtoken
			if (empty($vtoken)) {
				throw new Exception(lang('Verification token empty'), self::CODE_MVAL_VERIFY_TOKEN_EMPTY);
			}

			// Extract parts from verify token
			// 0-32:	md5'd player_id
			// Last 32:	md5'd expiry time
			// between:	player_id
			// See Email_template_player_verify_email::getVerifyLink()
			$player_id_md5	= substr($vtoken, 0, 32);
			$player_id		= substr($vtoken, 32, -32);
			$expiry_md5		= substr($vtoken, -32);

			// check for player_id
			if (empty($player_id)) {
				throw new Exception(lang('Player ID empty'), self::CODE_MVAL_PLAYER_ID_EMPTY);
			}

			// Check for player_id md5
			if (md5($player_id) != $player_id_md5) {
				throw new Exception(lang('player_id_md5 does not match'), self::CODE_MVAL_PLAYER_ID_MD5_NOT_MATCH);
			}

			// Check for expiry time md5
			$player = $this->player_model->getPlayerById($player_id);
			if (md5($player->email_verify_exptime) != $expiry_md5) {
				throw new Exception(lang('expiry_time_md5 does not match'), self::CODE_MVAL_EXPIRY_MD5_NOT_MATCH);
			}

			// Check if token has expired
			$now = time();
			if ($now > strtotime($player->email_verify_exptime)) {
				throw new Exception(lang('Verify token has expired'), self::CODE_MVAL_TOKEN_EXPIRED);
			}

			// Check player's current verification status
			if ($player->verified_email == true) {
				throw new Exception(lang('Player email is already verified'), self::CODE_MVAL_PLAYER_EMAIL_ALREADY_VERIFIED);
			}

			// The main event
			$verify_res = $this->player_model->verifyEmail($player_id);
			if (!$verify_res) {
				throw new Exception(lang('Verification failed'), self::CODE_MVAL_VERIFICATION_FAILED);
			}

			$this->comapi_log(__METHOD__, 'Mail validation successful', [ 'player_id' => $player_id, 'username' => $player->username ]);

			// Save update log
			$username = $player->username;
			$this->player_model->savePlayerUpdateLog($player_id,
				sprintf('%s %s', lang('Email verified by player: '), $player->username),
				"{$player->username}; {$this->api_common_ident()}"
			);

			// Update to MDB
			$this->syncPlayerCurrentToMDBWithLock($player_id, $username, false);

			// Send congratulation email
			$this->load->library([ 'email_manager' ]);
	        $template = $this->email_manager->template('player', 'player_verify_email_success', array('player_id' => $player_id));
	        $template_enabled = $template->getIsEnableByTemplateName(true);
	        if ($template_enabled['enable']) {
	        	$email = $player->email;
	        	$template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $player_id);
	        }

	        // Obtain redirect url, set alert
	        $redirect_target = $this->utils->getConfig('mail_verify_redirect_success');
	        $alert_type = self::MESSAGE_TYPE_SUCCESS;
	        $alert_mesg = lang('notify.11');
		}
		catch (Exception $ex) {
			$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

			// Obtain redirect url, set alert
			$redirect_target = $this->utils->getConfig('mail_verify_redirect_failure');
			$alert_type = self::MESSAGE_TYPE_WARNING;
	        $alert_mesg = lang('notify.113');
		}
		finally {
			// If redirect url not empty
	        if (!empty($redirect_target)) {
	        	redirect($redirect_target);
	        }
	        else {
		        $this->alertMessage($alert_type, $alert_mesg);
		        $this->goPlayerHome();
		    }
		}

	} // End function mailVerify()


} // End of trait t1t_comapi_module_mail_verification