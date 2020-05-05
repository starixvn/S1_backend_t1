<?php
/**
 * Comapi module: Simplified registration
 * Register player account without form checking by SBE/Registration Settings
 * Requested by TaiLai, for their white-listed users
 *
 * @copyright   Tripleonetech Aug 2018
 * @author      Rupert Chen <rupert.php.tw@tripleonetech.net>
 */
trait t1t_comapi_module_createPlayerSimple {

	/**
	 * Register player account without form checking by SBE/Registration Settings
	 * For trusted users only
	 * @uses	string	POST:username	Player username (REQUIRED)
	 * @uses	string	POST:password	Password (REQUIRED)
	 *
	 * @return	JSON	general JSON return structure
	 */
	public function createPlayerSimple() {
		$input = $this->input->post();
		$api_key = $this->input->post('api_key');
		unset($input['api_key']);

		if (!$this->__checkKey($api_key)) { return; }

		try {
			$this->load->library([ 'session', 'user_agent' ]);
			$this->load->model([ 'registration_setting', 'player_model' ]);

			$post_short = $this->comapi_lib->post_short();
			$this->log(__FUNCTION__, 'request', ['post_short' => $post_short]);

			if ($this->is_current_method_access_disabled()) {
				throw new Exception('Not found', self::CODE_API_METHOD_NOT_FOUND);
			}

			// Checking sername/password
			$username	= strtolower(trim($this->input->post('username')));
			$password	= $this->input->post('password');
			$cpassword	= $this->input->post('cpassword');
			$terms		= !empty(trim($this->input->post('terms')));

			$username_len_min = $this->utils->getConfig('default_min_size_username');
			$username_len_max = $this->utils->getConfig('default_max_size_username');
			$password_len_min = $this->utils->getConfig('default_min_size_password');
			$password_len_max = $this->utils->getConfig('default_max_size_password');
			$regex_username = $this->utils->getUsernameReg();
			$regex_password = $this->utils->getPasswordReg();

			// Empty check
			if (empty($username)) {
				throw new Exception('Username missing', self::CODE_CPS_USERNAME_MISSING);
			}

			if (empty($password)) {
				throw new Exception('Password missing', self::CODE_CPS_PASSWORD_MISSING);
			}

			// Regex check
			if (!preg_match($regex_username, $username)) {
				throw new Exception('Username does not satisfy validation regex', self::CODE_CPS_USERNAME_DOES_NOT_MATCH_REGEX);
			}

			if (!preg_match($regex_password, $password)) {
				throw new Exception('Password does not satisfy validation regex', self::CODE_CPS_PASSWORD_DOES_NOT_MATCH_REGEX);
			}

			// Length check
			if (strlen($username) < $username_len_min || strlen($username) > $username_len_max) {
				throw new Exception("Username length must between [ $username_len_min, $username_len_max ]", self::CODE_CPS_USERNAME_LEN_INVALID);
			}

			if (strlen($password) < $password_len_min || strlen($password) > $password_len_max) {
				throw new Exception("Password length must between [ $password_len_min, $password_len_max ]", self::CODE_CPS_PASSWORD_LEN_INVALID);
			}

			// password/cpassword
			if ($cpassword != $password) {
				throw new Exception("cpassword must be identical with password", self::CODE_CPS_CPASSWORD_DOES_NOT_MATCH);
			}

			// Reserved names
			$reserved_usernames = [ 'admin', 'moderator', 'hoster', 'administrator', 'mod' ];
			if (in_array($username, $reserved_usernames)) {
				throw new Exception("This username is not allowed", self::CODE_CPS_USERNAME_NOT_ALLOWED);
			}

			// Username Already in use
			if ($this->player_model->checkUsernameExist($username)) {
				throw new Exception("This username is taken", self::CODE_CPS_USERNAME_EXISTS);
			}

			// terms
			if (!$terms) {
				throw new Exception("terms must be checked", self::CODE_CPS_TERMS_NOT_CHECKED);
			}

			$httpHeadrInfo		= $this->session->userdata('httpHeaderInfo') ? : $this->utils->getHttpOnRequest();
			$header_referrer	= preg_replace('/\s+/', '', $httpHeadrInfo['referrer']);
			// OGP-8289 fix: when $_SERVER['HTTP_REFERER'] is not set, Agent::referrer() will
			// also be null, and Utils::getHttpOnRequest() has considered the value in
			// 'referrer' entry of return.  So no need for $_SERVER['HTTP_REFERER'] here.
			$referrer			= $header_referrer;
			// $referrer			= $header_referrer ?: $_SERVER['HTTP_REFERER'];

			// REGISTER
			$playerId = $this->player_model->register([
				// Player
				'username'              => $username,
				'gameName'              => $username,
				'password'              => $password,
				'email'                 => $this->input->post('email'),
				'secretQuestion'        => '' ,
				'secretAnswer'          => '' ,
				'verify'                => $this->player_functions->getRandomVerificationCode(),
				// Player Details
				'firstName'             => $this->input->post('firstName') ,
				'lastName'              => $this->input->post('lastName'),
				'language'              => $this->input->post('language'),
				'gender'                => $this->input->post('gender'),
				'birthdate'             => $this->input->post('birthdate'),
				'contactNumber'         => $this->input->post('contactNumber'),
				'citizenship'           => '' ,
				'imAccount'             => $this->input->post('im_account'),
				'imAccountType'         => $this->input->post('im_type'),
				'imAccount2'            => $this->input->post('im_account2'),
				'imAccountType2'        => $this->input->post('im_type2'),
				'imAccount3'            => $this->input->post('im_account3'),
				'imAccountType3'        => $this->input->post('im_type3'),
				'birthplace'            => '' ,
				'registrationIp'        => $this->utils->getIP(),
				'registrationWebsite'   => $referrer,
				'residentCountry'       => '' ,
				'city'                  => '' ,
				'address'               => '' ,
				'address2'              => '' ,
				'address3'              => '' ,
				'zipcode'               => '' ,
				'dialing_code'          => '' ,
				'id_card_number'        => '' ,
				// Codes
				'referral_code'         => $this->input->post('referral_code'),
				'affiliate_code'        => $this->input->post('affiliate_code'),
				'tracking_code'         => $this->input->post('tracking_code'),
				'agent_tracking_code'   => $this->input->post('agent_tracking_code'),
				// SMS verification
				// 'verified_phone'        => ! empty($sms_verification_code),
				'verified_phone'        => false,
				'newsletter_subscription'	=> null ,
				'communication_preference'	=> null
			]);
            //sync
            $this->load->model(['multiple_db_model']);
            $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($playerId, true);
            $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB', $rlt);

			$this->log(__FUNCTION__, 'playerId (register returned)', $playerId);

			if (!$playerId || !is_scalar($playerId)) {
				throw new Exception("Registration failed", self::CODE_CPS_REG_FAILED);
			}

			// LOGIN
			$this->authentication->login($username, $password);

			$playerId = $this->authentication->getPlayerId();

			if (!$playerId) {
				throw new Exception("Auto-login failed", self::CODE_CPS_AUTO_LOGIN_FAILED);
			}

			$result['username'] = $username;
			$result['playerId'] = $playerId;

			// Add token
			$result['token'] = $this->authentication->getPlayerToken();

			// Mark as new user
			$this->session->set_userdata('new_user', true);

			// Save http_request (cookies, referer, user-agent)
			$this->saveHttpRequest($playerId, Http_request::TYPE_REGISTRATION);

			$this->session->unset_userdata('httpHeaderInfo');

			// Send message to player email for account verification
			// player_auth_module::sendEmail()
			$this->sendEmail_wrapper($playerId);

			// $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Create user successfully'), $result);

			// POINT OF EXECUTION SUCCESS
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Player created successfully',
            	'result'	=> $result
            ];

            $this->log(__FUNCTION__, 'response', ['post_short' => $post_short]);
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['post_short' => $post_short]);

			$ret = [
				'success'   => false,
				'code'      => $ex->getCode(),
				'mesg'      => $ex->getMessage(),
				'result'    => null
			];
		}
		finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function createPlayerSimple()

} // End trait t1t_comapi_module_createPlayerSimple