<?php

/**
 * Api_common core module: game list and tokens
 * Separated 7/01/2019
 * @see		api_common.php
 */
trait comapi_core_game_list_tokens {

	/**
	 * If true, listGame* series would require username and token.  Defaults to false.
	 * @var boolean
	 */
	protected $lg_login_req = false;

	/**
	 * Lists game platforms configured on system
	 * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses	int		POST:refresh		non-zero or nonblank to force refresh
	 *
	 * @return	JSON
	 */
    public function listGamePlatforms() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$refresh		= !empty($this->input->post('refresh', 1));
    		$request 		= [ 'api_key' => $api_key, 'lg_login_requirement' => $this->lg_login_req ];
    		if ($this->lg_login_req) {
				$token			= trim($this->input->post('token', 1));
	    		$username		= trim($this->input->post('username', 1));
	    		$request = array_merge($request, [ 'username' => $username, 'token' => $token ]);
    		}
    		$this->comapi_log(__METHOD__, 'request', $request);

    		if ($this->lg_login_req) {
	    		$playerId = $this->player_model->getPlayerIdByUsername($username);
	    		if (empty($playerId)) {
	    			throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
	    		}

	    		// Check player token
	    		if (!$this->__isLoggedIn($playerId, $token)) {
	    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
	    		}
    		}

			// Exclude providers listed in GAME_PROVIDERS_CONCEAL
   			$this->load->model([ 'external_system', 'comapi_settings_cache' ]);

   			$res = $this->comapi_settings_cache->new_real_list_game_platforms();

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Successfully got the list of game platforms' ,
	    		'result'	=> $res
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
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
	} // End of function listGamePlatforms()

	/**
	 * Lists game lobby URL, or game types for platforms without lobby
	 * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int		POST:platform_id	platform_id as id reported by listGamePlatforms
     * @uses	int		POST:refresh		non-zero or nonblank to force refresh
     * @uses	int		POST:force_lang		force language, overriding SBE set preference.  Defaults to 2 (chinese).
	 *
	 * @return	JSON
	 */
	public function listGamesByPlatform() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$platform_id	= intval($this->input->post('platform_id', 1));
    		$refresh		= !empty($this->input->post('refresh', 1));
    		$force_lang		= intval($this->input->post('force_lang', 1));
    		$request 		= [ 'api_key' => $api_key, 'platform_id' => $platform_id, 'refresh' => $refresh, 'force_lang' => $force_lang, 'lg_login_requirement' => $this->lg_login_req ];
    		if ($this->lg_login_req) {
				$token			= trim($this->input->post('token', 1));
	    		$username		= trim($this->input->post('username', 1));
	    		$request = array_merge($request, [ 'username' => $username, 'token' => $token ]);
    		}
    		$this->comapi_log(__METHOD__, 'request', $request);

    		if ($this->lg_login_req) {
	    		$playerId = $this->player_model->getPlayerIdByUsername($username);
	    		if (empty($playerId)) {
	    			throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
	    		}

	    		// Check player token
	    		if (!$this->__isLoggedIn($playerId, $token)) {
	    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
	    		}
	    	}

    		if (empty($platform_id)) {
    			throw new Exception("Platform ID invalid", self::CODE_LG_PLATFORM_ID_INVALID);
    		}

    		$force_lang = $force_lang ?: null;

    		if ($force_lang > 6 || $force_lang < 1) {
    			$force_lang = null;
    		}

    		$this->load->model('comapi_settings_cache');
    		$gp_conceal = $this->comapi_settings_cache->settings_get_list_game('GAME_PROVIDERS_CONCEAL');
    		if (isset($gp_conceal[$platform_id])) {
    			throw new Exception("Platform ID '$platform_id' not supported", self::CODE_LG_PLATFORM_ID_NOT_SUPPORTED);
    		}

			$this->load->model('comapi_settings_cache');

			$res = $this->comapi_settings_cache->new_fgapi_list_games_by_platform($platform_id, $refresh, $force_lang);

			if ($res['success'] != true) {
				throw new Exception($res['mesg'], $res['code']);
			}

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> isset($res['mesg']) ? $res['mesg'] : 'Successfully got the list of game types' ,
	    		'result'	=> $res['result']
	    	];

	    	$this->comapi_log(__METHOD__, 'Response', $ret);
    	}
    	catch (Exception $ex) {
	    	$this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
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
	} // End of function listGameTypesByPlatform()

	/**
	 * Lists game details and launch URL by platform and game type
	 * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int		POST:platform_id	platform_id as id reported by listGamePlatforms
     * @uses    string	POST:game_type		game_type as game_types reported by listGamesByPlatform
     * @uses	int		POST:limit			count limit of result rows
     * @uses	int		POST:offset			offset of result
     * @uses	int		POST:refresh		non-zero or nonblank to force refresh
	 *
	 * @return	JSON
	 */
	public function listGamesByPlatformGameType() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	$debug = false;

    	try {
    		$platform_id	= intval($this->input->post('platform_id', 1));
    		$game_type		= trim($this->input->post('game_type', 1));
    		$refresh		= !empty($this->input->post('refresh', 1));
    		$limit			= intval($this->input->post('limit', 1));
    		$offset			= intval($this->input->post('offset', 1));
    		$request 		= [ 'api_key' => $api_key, 'platform_id' => $platform_id, 'game_type' => $game_type, 'refresh' => $refresh, 'limit' => $limit, 'offset' => $offset, 'lg_login_requirement' => $this->lg_login_req ];
    		if ($this->lg_login_req) {
				$token			= trim($this->input->post('token', 1));
	    		$username		= trim($this->input->post('username', 1));
	    		$request = array_merge($request, [ 'username' => $username, 'token' => $token ]);
    		}
			$this->comapi_log(__METHOD__, 'request', $request);

			if ($this->lg_login_req) {
	    		$playerId = $this->player_model->getPlayerIdByUsername($username);
	    		if (empty($playerId)) {
	    			throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
	    		}

	   			// Check player token
	    		if (!$this->__isLoggedIn($playerId, $token)) {
	    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
	    		}
	    	}

    		if (empty($platform_id)) {
    			throw new Exception("Platform ID invalid", self::CODE_LG_PLATFORM_ID_INVALID);
    		}

    		$this->load->model('comapi_settings_cache');
    		$gp_conceal = $this->comapi_settings_cache->settings_get_list_game(Comapi_settings_cache::SETTINGS_GAME_PROVIDERS_CONCEAL);
    		if (isset($gp_conceal[$platform_id])) {
    			throw new Exception("Platform ID '$platform_id' not supported", self::CODE_LG_PLATFORM_ID_NOT_SUPPORTED);
    		}

    		if (empty($limit) || $limit > 50) { $limit = 50; }

    		if (empty($offset)) { $offset = 0; }

    		$cf_res = $this->comapi_settings_cache->new_fgapi_list_games_by_platform_gametype($platform_id, $game_type, $refresh, $limit, $offset);

			if ($cf_res['success'] != true) {
				throw new Exception($cf_res['mesg'], $cf_res['code']);
			}

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		// 'mesg'		=> "Successfully got game list for platform={$platform_id},type={$game_type}" ,
	    		'mesg'		=> $cf_res['mesg'] ,
	    		'result'	=> $cf_res['result']
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
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
	} // End of function listGameTypesByPlatform()

	/**
	 * (INTERNAL USE ONLY) Show or import settings for listGame_* series API methods
	 * @param	string	$action			The action; URL arg.  'review' to show current settings, 'import' to import settings from config to db table.  Default is 'review'.
	 * @uses	string	POST:api_key	api_key as security measure.
	 * @return	JSON	General JSON return object, with res =
	 *                  [ to_set, current_set ] 			if action == review;
	 *                  [ before_set, to_Set, after_set] 	if action == import.
	 */
	public function listGame_settings($action = 'review') {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

		try {
			$this->load->model('comapi_settings_cache');
			$settings_prefix = Comapi_settings_cache::SETTINGS_PREFIX_LISTGAME;
			$config_key = strtolower("comapi_{$settings_prefix}");

			switch ($action) {

				case 'import' :
					$to_set = $this->utils->getConfig($config_key);
					if (empty($to_set)) {
						$res = null;
						$mesg = "Config item '$config_key' not found, nothing to import";
						break;
					}

					$before_set = $this->comapi_settings_cache->settings_group_get($settings_prefix);

					foreach ($to_set as $settings_key => $value) {
						$settings_key_upper = strtoupper($settings_key);
						$this->comapi_settings_cache->settings_set(
							"{$settings_prefix}:{$settings_key_upper}",
							null,
							is_scalar($value) ? $value : json_encode($value)
						);
					}

					$after_set = $this->comapi_settings_cache->settings_group_get($settings_prefix);

					$res = [ 'before_set' => $before_set, 'to_set' => $to_set, 'after_set' => $after_set ];
					$mesg = 'Imported in-config settings';

					break;

				case 'review' : default :
					$current_set = $this->comapi_settings_cache->settings_group_get($settings_prefix);
					$to_set = $this->utils->getConfig($config_key);

					$res = [ 'current_set' => $current_set , 'to_set' => $to_set ];

					$mesg = 'Reviewing current/in-config settings.';
					if (empty($to_set)) { $mesg .= "  Config item '$config_key' not found."; }
					break;
			}

			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> $mesg ,
	    		'result'	=> $res
	    	];

		}
		catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false ,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> [ $extra_results ]
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function listGame_settings()

} // End of trait t1t_comapi_module_player_game_log