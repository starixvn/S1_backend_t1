<?php
trait t1t_comapi_module_player_password {
    /**
     * Max permitted difference between server time and timestamp provided in request, in seconds
     * @var integer
     */
    protected $max_time_diff = 60;

	/**
     * Retrieves player's password plaintext.
     * Demanded by Yongji. OGP-7661.
     * NOTE: Restricted use, for Yongji only
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: username	Player username
     *
     * @return	JSON	General JSON return object, with password plaintext in result field
     */
	public function getPlayerPasswordPlain() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

		try {
			$this->load->model([ 'player_model' ]);

            // Determine max time diff (request timestamp-server time) allowed
            // Use config value if possible, or use value set in trait by default
            $max_time_diff_config = (int) $this->config->item('comapi_getPlayerPasswordPlain_max_time_diff_allowed');
            $max_time_diff_local = $max_time_diff_config > 0 ? $max_time_diff_config : $this->max_time_diff;

    		// Read arguments
    		$username	= $this->input->post('username');
    		$timestamp	= intval($this->input->post('timestamp'));
    		$secure		= strtolower($this->input->post('secure'));
    		$creds 		= [ 'api_key' => $api_key, 'username' => $username, 'timestamp' => $timestamp , 'secure' => $secure  ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $creds);

    		// Check API access
			if ($this->is_current_method_access_disabled()) {
				throw new Exception('Not found', self::CODE_API_METHOD_NOT_FOUND);
			}

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_GPWP_PLAYER_USERNAME_INVALID);
    		}

            // Verify secure string
            if (!$this->verify_secure($secure, 'getPlayerPasswordPlain', $api_key, $username, $timestamp)) {
                throw new Exception('Secure string invalid', self::CODE_GPWP_SECURE_STRING_INVALID);
            }

    		// Reject timestamp too far ahead or behind
    		$time_diff = abs(time() - $timestamp);
    		if ($time_diff > $max_time_diff_local) {
                $this->utils->debug_log(__FUNCTION__, 'time diff too large', [ 'allowed' => $max_time_diff_local, 'time_diff' => $time_diff, 'server' => time(), 'request' => $timestamp ]);
    			throw new Exception('Time difference too large', self::CODE_GPWP_TIME_DIFFERENCE_TOO_LARGE);
    		}

    		// Retrieve player password
    		$passwd_plaintext = $this->player_model->getPasswordById($player_id);

    		$res = [ 'password' => $passwd_plaintext ];

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Player password retrieved successfully',
            	'result'	=> $res
            ];
            $this->utils->debug_log(__FUNCTION__, 'Success', [ $ret, $creds ]);
	    }
	    catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function getPlayerPasswordPlain

} // End trait t1t_comapi_module_player_password