<?php

/**
 * Api_common core module: promos
 * Separated 10/14/2019
 * @see		api_common.php
 */
trait comapi_core_promos {

    /**
     * Lists promos for promo manager
     *
     * @uses    string  POST:api_key    api key given by system
     * @uses	string	POST:username	Player username
     * @uses	string	POST:token		Effective token for player
     * @uses	int		POST:limit		Limit (page length), for paging
     * @uses	int		POST:offset		Offset, for paging
     * @uses    int     POST:is_deposit return deposit promos if true.  Default: false
     *
     * @return	JSON	Standard JSON return structure [ success, code, mesg, result ]
     *                       With result = [ promos (array), categories (array) ] when successful
     */
	public function listPromos() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

		try {
			$this->load->model([ 'player_model' ]);

    		// Read arguments
    		$token		= $this->input->post('token', true);
    		$username	= $this->input->post('username', true);
    		$limit		= (int) $this->input->post('limit', true);
    		$offset		= (int) $this->input->post('offset', true);
            $is_deposit = !empty($this->input->post('is_deposit', true));
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'limit' => $limit, 'offset' => $offset ];
    		$this->comapi_log(__METHOD__, 'request', $std_creds);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_INVALID_USER);
    		}

    		// Check player token
    		$logcheck = $this->_isPlayerLoggedIn($player_id, $token);
    		if ($logcheck['code'] != 0) {
    			throw new Exception($logcheck['mesg'], $logcheck['code']);
    		}
    		// Set limit/offset to null if not specified, so they fall back to Comapi_lib::get_promos_enabled_for_promo_manager() default
    		if (empty($limit))	{ $limit = null; }
    		if (empty($offset))	{ $offset = null; }

    		$promo_res = $this->comapi_lib->get_promos_enabled_for_promo_manager($player_id, $offset, $limit, Comapi_lib::PROMO_SIMPLE, $is_deposit);

    		$mesg = 'Promo(s) for player are retrieved successfully';

    		if (count($promo_res) == 0) {
    			$player = $this->player_model->getPlayerArrayById($player_id);
    			if ($player['disabled_promotion'] == 1) {
    				throw new Exception(lang('Promo disabled for player'), self::CODE_DISABLED_PROMOTION);
    			}
    			$mesg = 'No applicable promo for player at this time';
    		}

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> $mesg,
            	'result'	=> $promo_res
            ];
	    }
	    catch (Exception $ex) {
	    	$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $std_creds);

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
	} // End function listPromos()

    /**
     * Lists all promos, requires no login, without any pre-checks
     *
     * @uses    string  POST:api_key    api key given by system
     * @uses    int     POST:limit      Limit (page length), for paging
     * @uses    int     POST:offset     Offset, for paging
     * @uses    int     POST:is_deposit return deposit promos if true.  Default: false
     *
     * @return  JSON    Standard JSON return structure [ success, code, mesg, result ]
     *                       With result = [ promos (array), categories (array) ] when successful
     */
    public function listPromos2() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }
        $res_mesg = '';

        try {
            $this->load->model([ 'player_model' ]);

            // Read arguments
            $limit      = (int) $this->input->post('limit', true);
            $offset     = (int) $this->input->post('offset', true);
            $is_deposit = !empty($this->input->post('is_deposit', true));
            $std_creds      = [ 'api_key' => $api_key, 'limit' => $limit, 'offset' => $offset ];
            $this->comapi_log(__METHOD__, 'request', $std_creds);

            // Set limit/offset to null if not specified
            if (empty($limit))  { $limit = null; }
            if (empty($offset)) { $offset = null; }

            $promo_res = $this->comapi_lib->get_promos_bare($offset, $limit, Comapi_lib::PROMO_SIMPLE, $is_deposit);

            $mesg = 'Promo(s) for player are retrieved successfully';

            if (count($promo_res) == 0) {
                $mesg = 'No applicable promo for player at this time';
            }

            // If everything goes alright
            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => $mesg,
                'result'    => $promo_res
            ];
        }
        catch (Exception $ex) {
            $this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

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
    } // End function listPromos2()

	/**
	 * Apply some promo available in promo manager
     *
     * @param   bool    $skip_vip_level_check   Skip player VIP level check if true
     *
     * @uses    string  POST:api_key        api key given by system
	 * @uses	string	POST:username		Player username
     * @uses	string	POST:token			Effective token for player
     * @uses	string	POST:promo_cms_id	promo_cms_id of target promo
     *
	 * @return	JSON	Standard JSON return structure [ success, code, mesg, result ]
	 */
	public function applyPromo($skip_vip_level_check = false) {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

		try {
			$this->load->model([ 'player_model' ]);

    		// Read arguments
    		$token			= $this->input->post('token', true);
    		$username		= $this->input->post('username', true);
    		$promo_cms_id	= (int) $this->input->post('promo_cms_id', true);

    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'promo_cms_id' => $promo_cms_id ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_INVALID_USER);
    		}

    		// Check player token
    		$logcheck = $this->_isPlayerLoggedIn($player_id, $token);
    		if ($logcheck['code'] != 0) {
    			throw new Exception($logcheck['mesg'], $logcheck['code']);
    		}

            if (!$skip_vip_level_check) {
        		// Check promo_cms_id
        		$promo_res = $this->comapi_lib->get_promos_enabled_for_promo_manager($player_id);
        		$pcid_legal = false;
        		if (!empty($promo_cms_id) && !empty($promo_res['promos'])) {
    	    		foreach ($promo_res['promos'] as $p) {
    	    			if ($p['promo_cms_id'] == $promo_cms_id) {
    	    				$pcid_legal = true;
    	    				break;
    	    			}
    	    		}
    	    	}

        		if (!$pcid_legal) {
        			throw new Exception(lang('Invalid promo_cms_id'), self::CODE_PMO_PROMO_CMS_ID_INVALID);
        		}
            }

    		$apply_res = $this->request_promo($promo_cms_id, 0, null, false, 'ret_to_api', $player_id);

    		if ($apply_res['success'] != true) {
    			throw new Exception($apply_res['message'], $apply_res['code']);
    		}

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> lang('Promo request successful'),
            	'result'	=> [ 'message' => $apply_res['message'] ]
            ];
	    }
	    catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $std_creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> lang('Promo request failed') ,
	    		'result'	=> [ 'message' => $ex->getMessage() ]
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function applyPromo()

    /**
     * Apply some promo available in promo manager
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    string  POST:promo_cms_id   promo_cms_id of target promo
     *
     * @return  JSON    Standard JSON return structure [ success, code, mesg, result ]
     */
    public function applyPromo2() {
        $this->applyPromo('skip_vip_level_check');
    }

} // End of trait comapi_core_promos
