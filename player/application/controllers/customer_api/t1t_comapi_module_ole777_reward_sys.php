<?php
trait t1t_comapi_module_ole777_reward_sys {


	protected $errors = [
		'SUCCESS'                 => 0 ,
		'ERR_INVALID_SECURE'      => 101 ,
		'ERR_INVALID_MEMBER_CODE' => 102 ,
		'ERR_INVALID_TOKEN'       => 103 ,
		'ERR_FAILURE'             => 190
	];

	protected $auth_errors = [
		'SUCCESS'			=> 0 ,
		'INVALID_USERNAME'	=> 102 ,
		'INVALID_PASSWORD'	=> 103 ,
		'INVALID_PARAMS'	=> 104 ,
		'GENERAL_ERROR'		=> 199
	];

	protected $status = [
		'ACTIVE'	=> 200 ,
		'BLOCKED'	=> 201 ,
		'INACTIVE'	=> 208 ,
		'UNKNOWN'   => 209
	];

	/**
	 * Demanded by ole777. OGP-9073.
	 * NOTE: Restricted use, for ole777 only
	 *
	 * @uses	string	POST: api_key	api key given by system
	 * @uses	string	POST: username	Player username
	 *
	 * @return	JSON	General JSON return object, with password plaintext in result field
	 */
	public function ole777_login() {
		$success = [
			'success'	=> false ,
			'code'      => $this->errors['ERR_FAILURE'],
			'status'    => $this->status['UNKNOWN']
		];
		try {
			$this->load->model([ 'player_model', 'common_token' ]);
			$username    = trim($this->input->post('username', true));
			$token       = trim($this->input->post('token', true));
			$secure      = trim($this->input->post('secure', true));

			$creds = [ 'username' => $username, 'token' => $token, 'secure' => $secure ];

			$this->utils->debug_log(__METHOD__, 'request', $creds);

			if (!$this->ole777_check_secure($secure, $username, $token)) {
				throw new Exception('Invalid value for secure', $this->errors['ERR_INVALID_SECURE']);
			}

			$player_id = $this->player_model->getPlayerIdByUsername($username);

			if (empty($player_id)) {
				throw new Exception('Invalid value for username', $this->errors['ERR_INVALID_MEMBER_CODE']);
			}

			$valid_token = $this->common_token->getPlayerToken($player_id);

			if ($token != $valid_token) {
				throw new Exception('Invalid value for token', $this->errors['ERR_INVALID_TOKEN']);
			}

			$ret = [
				'success'	=> true ,
				'code'      => $this->errors['SUCCESS'],
				'mesg'      => 'Verification complete',
				'result'    => [
					'status'   	=> $this->status['ACTIVE'] ,
                    'username'	=> $username
            	]
            ];

            $this->utils->debug_log(__METHOD__, 'response', $ret, $creds);

		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

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
	} // End function ole777_login()

	/**
	 * Check given secure string against username and token
	 *
	 * @param   string  $secure      The secure hash
	 *                               = sha1("{$ole777_key}{$username}{$token}")
	 * @param   string  $username    Player username
	 * @param   string  $token       The token
	 *
	 * @return  bool    true of secure matches; otherwise false.
	 *
	 *      */
	protected function ole777_check_secure($secure, $username, $token) {
		// Fetch login key from config file
		$conf = $this->utils->getConfig('ole777_reward_conf');
		if (isset($conf['login_verify_key'])) {
			$this->ole777_key = $conf['login_verify_key'];
		}

		// Compute valid login
		$plaintext = $this->ole777_key . $username . $token;
		$valid_secure = sha1($plaintext);

		$match = $secure == $valid_secure;

		$this->utils->debug_log(__METHOD__, [ 'match' => $match, 'secure' => $secure, 'valid_secure' => $valid_secure, 'plaintext' => $plaintext ]);

		return $match;
	} // End function ole777_check_secure()

	/**
	 * Generate list of host URLs to query from ole777_reward_conf.hosts in config
	 *
	 * @uses	$config['ole777_reward_conf']['hosts']['siblings']
	 * @uses	$config['ole777_reward_conf']['hosts']['self']
	 * @see		config_secret_local.php
	 * @return	array 	Associative array of host URLs to query
	 */
	protected function ole777_find_query_hosts() {
		$orconf = $this->utils->getConfig('ole777_reward_conf');
		if (empty($orconf)) {
			$this->utils->debug_log(__METHOD__, 'ole777_reward_conf not set in config', [ 'ole777_reward_conf' => $orconf ]);
			return [];
		}

		$hosts = $this->utils->safeGetArray($orconf, 'hosts');
		if (empty($hosts)) {
			$this->utils->debug_log(__METHOD__, 'hosts not set in ole777_reward_conf',
				[ 'ole777_reward_conf' => $orconf ]);
			return [];
		}

		$siblings = $this->utils->safeGetArray($hosts, 'siblings');
		$self = $this->utils->safeGetArray($hosts, 'self');
		if (empty($siblings) || empty($self)) {
			$this->utils->debug_log(__METHOD__, 'ole777_reward_conf.hosts malformed',
				[ 'ole777_reward_conf' => $orconf ]);
			return [];
		}

		// Remove self from list of sibling hosts
		unset($siblings[$self]);

		// $this->utils->debug_log(__METHOD__, 'query hosts return', $siblings);

		return $siblings;

	} // End function ole777_find_query_hosts()

	public function ole777_auth_check() {

		// $httpcall_config_default = [ 'is_post' => 1, 'timeout_second' => 5, 'connect_timeout' => 5 ];
		$httpcall_config_default = [ CURLOPT_TIMEOUT => 5, CURLOPT_CONNECTTIMEOUT => 5 ];

		$ole777_conf = $this->utils->getConfig('ole777_reward_conf');
		$testmode_auth_check = !empty($this->utils->safeGetArray($ole777_conf, 'test_mode_auth_check'));

		try {
			$username	= trim($this->input->post('username', true));
			$password	= trim($this->input->post('password', true));
			$secure		= trim($this->input->post('secure', true));

			$creds = [ 'username' => $username, 'password' => $password, 'secure' => $secure ];

			$this->utils->debug_log(__METHOD__, 'request', $creds);

			if (!$this->ole777_check_secure($secure, $username, $password)) {
				throw new Exception('Invalid value for secure', $this->errors['ERR_INVALID_SECURE']);
			}

			$login_res = $this->comapi_lib->login_priv($username, $password);

			$this->utils->debug_log(__METHOD__, 'local login results', $login_res);

			$qhosts = $this->ole777_find_query_hosts();

			if (!$testmode_auth_check) {
				// Not test mode
				if ($login_res['code'] == 0) {
					$from_host = $this->utils->getSystemUrl('player', '/');
					// Throw minus error code, successful exception
					throw new Exception('Player authorized locally', -0x100);
				}

			}
			else {
				// Test mode
				$qhosts = [ 'ole777thb' => 'player.staging.ole777thb.t1t.games', 'ole777idn' => 'player.staging.ole777idn.t1t.games' ];
			}

			$this->utils->debug_log(__METHOD__, 'player not found locally, querying sibling hosts', [ 'query_hosts' => $qhosts ]);

			foreach ($qhosts as $qh) {
				// Send query
				$qurl = "{$qh}/api/ole777/ole777_auth_check_local/";
				list($rqheader, $rqresp_raw, $rqstatus, $rqstatus_text, $rqerr, $rqerr_text) =
				// $this->utils->httpCall($qurl, [ 'username' => $username, 'password' => $password ], $httpcall_config_default);
				$this->utils->callHttp($qurl, 'POST', [ 'username' => $username, 'password' => $password ], $httpcall_config_default);

				// Status != 200, problem while connecting
				if ($rqstatus != '200') {
					$this->utils->debug_log(__METHOD__, 'problem while connecting', [ 'qurl' => $qurl, 'status' => $rqstatus ]);
					continue;
				}

				// Or status == 200, successful
				$rqresp = json_decode($rqresp_raw, 'as array');
				$this->utils->debug_log(__METHOD__, 'successfully connected', [ 'qurl' => $qurl, 'status' => $rqstatus, 'result' => $rqresp ]);

				if ($rqresp['success'] == true) {
					$from_host = $rqresp['result']['from_host'];
					$this->utils->debug_log(__METHOD__, 'Player authorized remotely', [ 'from_host' => $from_host ]);
					throw new Exception('Player authorized', -0x101);
				}

				$this->utils->debug_log(__METHOD__, 'Player not found remotely', [ 'query_host' => $qh ]);

			}

			throw new Exception('Player not found in all hosts', $this->auth_errors['INVALID_USERNAME']);

		}
		catch (Exception $ex) {
			$code = $ex->getCode();
			if ($code > 0) {
				$ret = [
					'success'	=> false ,
					'code'		=> $ex->getCode() ,
					'mesg'		=> $ex->getMessage() ,
					'result'	=> null
				];
				$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);
			}

			else {
				$ret = [
					'success'	=> true ,
					'code'		=> $this->errors['SUCCESS'] ,
					'mesg'		=> 'Player authorized' ,
					'result'	=> [
						'from_host' => $from_host ,
						'status'	=> $this->status['ACTIVE']
					]
				];
				$this->utils->debug_log(__METHOD__, 'response', $ret, $creds);
			}
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function ole777_auth_check()


	public function ole777_auth_check_local() {
		try {
			$username	= trim($this->input->post('username', true));
			$password	= trim($this->input->post('password', true));

			$creds = [ 'username' => $username, 'password' => $password ];

			$this->utils->debug_log(__METHOD__, 'request', $creds);

			$chk = $this->comapi_lib->login_priv($username, $password);
			$from_host = $this->utils->getSystemUrl('player', '/');

			$this->utils->debug_log(__METHOD__, 'chk', $chk);


			switch ($chk['code']) {
				case self::CODE_INVALID_USERNAME :
					throw new Exception('Username empty or invalid', $this->auth_errors['INVALID_PARAMS']);
					break;
				case self::CODE_INVALID_PASSWORD :
					throw new Exception('Invalid password', $this->auth_errors['INVALID_PASSWORD']);
					break;
				case self::CODE_INVALID_USER :
					throw new Exception('Invalid username', $this->auth_errors['INVALID_USERNAME']);
					break;
				case self::CODE_USER_IS_BLOCKED :
				case self::CODE_USER_UNDER_SELF_EXCLUSION :
					throw new Exception('User blocked', $this->status['BLOCKED']);
					break;
				case 0 :
					break;
				case self::CODE_LOGIN_FAIL : default :
					throw new Exception('Login failed', $this->auth_errors['GENERAL_ERROR']);
					break;
			}

			// Or successful
			$ret = [
				'success'	=> true ,
				'code'		=> 0 ,
				'mesg'		=> 'Player authorized' ,
				'result'	=> [
					'from_host' => $from_host
				]
			];
			$this->utils->debug_log(__METHOD__, 'response', $ret, $creds);
		}
		catch (Exception $ex) {
			$ret = [
				'success'	=> false ,
				'code'		=> $ex->getCode() ,
				'mesg'		=> $ex->getMessage() ,
				'result'	=> null
			];
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function ole777_auth_check_local()

} // End trait t1t_comapi_module_player_password