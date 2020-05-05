<?php

/**
 * SMS verification API suite
 * OGP-12844, RFE-2902
 * converted from xcyl mobile_reg1 API suite
 *
 * @see		OGP-7983
 * @see		tit_comapi_module_mobile_reg1
 */
trait t1t_comapi_module_sms_verification {

	protected $debug_level_sms = 10;
	protected $debug_sms_service_enabled = 100;

	public function smsVerifySend() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model', 'sms_verification' ]);

			$username	= trim($this->input->post('username', 1));
			$token		= trim($this->input->post('token', 1));

			$request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
			$this->comapi_log(__FUNCTION__, 'request', $request);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception(lang('Username invalid'), self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
    			throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
    		}

    		if ($this->utils->isSMSEnabled() == false && $this->debug_level_sms < $this->debug_sms_service_enabled) {
				throw new Exception(lang('SMS service is disabled'), self::CODE_SMSVAL_SMS_SERVICE_DISABLED);
			}

			if ($this->player_model->isVerifiedPhone($player_id) == true) {
				throw new Exception(lang('Player phone number has been already verified'), self::CODE_SMSVAL_PLAYER_PHONE_ALREADY_VERIFIED);
			}

			$contact_number = $this->player_model->getPlayerContactNumber($player_id);

			if (empty($contact_number)) {
				throw new Exception(lang('Player phone number not set yet'), self::CODE_SMSVAL_PLAYER_PHONE_NUMBER_NOT_SET);
			}

			$this->comapi_log(__METHOD__, 'contact_number', $contact_number);

			$send_res = $this->comapi_lib->comapi_send_sms($player_id, $contact_number, Sms_verification::USAGE_COMAPI_SMS_VALIDATE);

			if ($send_res['code'] != 0) {
				$res = [ 'mesg' => $send_res['mesg'], 'mesg_debug' => $send_res['mesg_debug'] ];
				throw new Exception($send_res['mesg'], $send_res['code']);
			}

			$ret = $send_res;
			$ret['success'] = true;
		}
		catch (Exception $ex) {
			$ex_log = [ 'code' => $ex->getCode(), 'message' => isset($res['mesg_debug']) ? $res['mesg_debug'] : $ex->getMessage() ];
	    	$this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

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
	} // End function smsVerifySend()

	public function smsVerify() {
		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$this->load->model([ 'player_model', 'sms_verification' ]);

		$res = null;

		try {
			$this->load->model([ 'player_model', 'sms_verification' ]);
			$this->load->library([ 'player_library' ]);

			$username		= trim($this->input->post('username', 1));
			$token			= trim($this->input->post('token', 1));
			$verify_code	= trim($this->input->post('verify_code', 1));

			$request = [ 'api_key' => $api_key, 'username' => $username , 'token' => $token, 'verify_code' => $verify_code ];
			$this->comapi_log(__FUNCTION__, 'request', $request);

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Player unknown', self::CODE_MPC_PLAYER_UNKNOWN);
			}

   			if ($this->utils->isSMSEnabled() == false && $this->debug_level_sms < $this->debug_sms_service_enabled) {
				throw new Exception(lang('SMS service is disabled'), self::CODE_SMSVAL_SMS_SERVICE_DISABLED);
			}

			if ($this->player_model->isVerifiedPhone($player_id) == true) {
				throw new Exception(lang('Player phone number has been already verified'), self::CODE_SMSVAL_PLAYER_PHONE_ALREADY_VERIFIED);
			}

			$contact_number = $this->player_model->getPlayerContactNumber($player_id);

			if (empty($contact_number)) {
				throw new Exception(lang('Player phone number not set yet'), self::CODE_SMSVAL_PLAYER_PHONE_NUMBER_NOT_SET);
			}

			if (empty($verify_code)) {
				throw new Exception(lang('Verify code empty'), self::CODE_SMSVAL_VERIFY_CODE_EMPTY);
			}

			// $verify_result = $this->update_sms_verification($contact_number, $verify_code, 1, $player_id, Sms_verification::USAGE_COMAPI_SMS_VALIDATE, 'from_api');

			$code_verify_res = $this->sms_verification->validateVerificationCode($player_id, Sms_verification::SESSION_ID_DEFAULT, $contact_number, $verify_code, Sms_verification::USAGE_COMAPI_SMS_VALIDATE);

			if (!$code_verify_res) {
				$this->comapi_log(__METHOD__, 'Code verify failed in',  'Sms_verification::validateVerificationCode()');
				throw new Exception(lang('Code verification failed').' (-1)', self::CODE_SMSVAL_CODE_VERIFY_FAILED);
			}

			$cn_update_res = $this->player_model->updateAndVerifyContactNumber($player_id, $contact_number);

			if (!$cn_update_res) {
				$this->comapi_log(__METHOD__, 'Code verify failed in',  'Player_model::updateAndVerifyContactNumber');
				throw new Exception(lang('Code verification failed').' (-2)', self::CODE_SMSVAL_CODE_VERIFY_FAILED);
			}

			$this->player_library->savePlayerUpdateLog($player_id, 'Phone verified by pcAPI smsVerify', $username);

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> 'SMS verification successful',
	    		'result'	=> $res
			];

		}
		catch (Exception $ex) {
	    	$this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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
	} // End function smsVerify()

	public function smsVerifyStatus() {

		$api_key = $this->input->post('api_key');

		if (!$this->__checkKey($api_key)) { return; }

		$res = null;

		try {
			$this->load->model([ 'player_model' ]);

			$username		= trim($this->input->post('username'));

			$request = [ 'api_key' => $api_key, 'username' => $username ];
			$this->comapi_log(__FUNCTION__, 'request', $request);

			// Check username
			$player_id = $this->player_model->getPlayerIdByUsername($username);
			if (empty($player_id)) {
				throw new Exception(lang('Username invalid'), self::CODE_COMMON_INVALID_USERNAME);
			}

			// Check if SMS has been disabled
			if ($this->utils->isSMSEnabled() == false && $this->debug_level_sms < $this->debug_sms_service_enabled) {
				throw new Exception(lang('SMS service is disabled'), self::CODE_SMSVAL_SMS_SERVICE_DISABLED);
			}

			// Check if player's phone is verified
			if ($this->player_model->isVerifiedPhone($player_id) == false) {
				throw new Exception(lang('Player phone number is not verified yet'), self::CODE_SMSVAL_PLAYER_PHONE_NOT_VERIFIED);
			}

			$ret = [
				'success'	=> true,
	    		'code'		=> 0,
	    		'mesg'		=> lang('Player phone number is verified.'),
	    		'result'	=> null
			];
		}
		catch (Exception $ex) {
	    	$this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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

	} // End function smsVerifyStatus()


} // End of trait t1t_comapi_module_sms_validation