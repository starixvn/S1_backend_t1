<?php
trait t1t_comapi_module_game {

	protected $site, $game_code, $player_id, $api_key;

	protected $CODE_GAME_MISSING_GAME_CODE				= 0x201;
	protected $CODE_GAME_MISSING_PLAYER_TOKEN			= 0x202;
	protected $CODE_GAME_MISSING_RESULT					= 0x203;
	protected $CODE_GAME_MISSING_GAME_SESS_ID			= 0x204;
	protected $CODE_GAME_INVALID_GAME_SESS_ID			= 0x205;
	protected $CODE_GAME_PLAYER_HITS_TODAY_SIGNIN_LIMIT	= 0x206;
	protected $CODE_GAME_COMAPI_PROMO_NOT_FOUND			= 0x207;

	protected function _module_game_cons() {
		$this->load->model(['common_token', 'comapi_games']);
		$this->api_key = $this->input->post('api_key');
		$this->site = $this->input->post('site') ?: $this->utils->getConfig('comapi_site');
		$this->game_code = $this->input->post('game_code');
		$this->game_sess_id = $this->input->post('game_sess_id');

		$this->token = $this->input->post('token');
		$this->player_id = $this->common_token->getPlayerIdByToken($this->token);
	}

	/**
	 * Check player login count and/or sign in player
	 *
	 * @uses	POST:api_key	string	The api_key
	 * @uses	POST:site		string	(optional) Site ident, string like 'youhu'; will use value from config file if absent.
	 * @uses	POST:game_code	int		Game code
	 * @uses	POST:token		string	Token obtained on player login
	 *
	 * @return On success: result = [ 'player_can_play' = true, 'game_sess_id' ]
	 *         On failure: result = [ 'player_can_play' = false ]
	 *         With standard JSON return set: [ success, code, mesg, result ]
	 */
	public function game_signin_check() {
		$this->_module_game_cons();

		if (!$this->__checkKey($this->api_key)) { return; }

		$extra_result = [];

		try {

			// Check player token
			if (empty($this->player_id)) {
				throw new Exception('Player token missing or invalid', $this->CODE_GAME_MISSING_PLAYER_TOKEN);
			}

			// Check game_code
			if (empty($this->game_code) || ! $this->comapi_games->game_is_valid($this->site, $this->game_code)) {
				throw new Exception('game_code missing or invalid', $this->CODE_GAME_MISSING_GAME_CODE);
			}

			// Check player's login count today
			$player_daily_signin_limit = $this->comapi_games->settings_get($this->site, $this->game_code, Comapi_games::SETTING_PLAYER_DAILY_SIGNIN_LIMIT);

			$player_signin_count_today = $this->comapi_games->events_count_today($this->site, $this->game_code, $this->player_id, Comapi_games::EVENT_GAME_START);

			$this->utils->debug_log('game_signin_check', 'player_daily_play_limit', [ 'player' => $this->player_id , 'count' => $player_signin_count_today , 'limit' => $player_daily_signin_limit ]);

			if ($player_signin_count_today >= $player_daily_signin_limit) {
				$extra_result = [ 'player_signin_count' => $player_signin_count_today, 'player_signin_limit' => $player_daily_signin_limit ];
				throw new Exception("Player has hit today's signin count limit", $this->CODE_GAME_PLAYER_HITS_TODAY_SIGNIN_LIMIT);
			}

			// Generate game_sess_id, log player_sign_in event
			$game_sess_id = $this->comapi_games->game_sess_id_generate($this->site, $this->game_code, $this->player_id);

			$this->comapi_games->events_log($this->site, $this->game_code, $this->player_id, Comapi_games::EVENT_GAME_START, $game_sess_id);

			$this->utils->debug_log('game_signin_check', 'game_started', [ 'player' => $this->player_id , 'game_sess_id' => $game_sess_id ]);

			// Successful return
	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'game started' ,
	    		'result'	=> [ 'player_can_play' => true, 'game_sess_id' => $game_sess_id ]
	    	];
		}
		catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$result = array_merge([ 'player_can_play' => false ], $extra_result);
	    	$ret = [
	    		'success'	=> false ,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> $result
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	}

	/**
	 * Log player game results and/or issue bonus
	 *
	 * @uses	POST:api_key		string	The api_key
	 * @uses	POST:site			string	(optional) Site ident, string like 'youhu'; will use value from config file if absent.
	 * @uses	POST:game_code		int		Game code
	 * @uses	POST:token			string	Token obtained on player login
	 * @uses	POST:game_sess_id	string	Obtained from game_signin_check().  To identify specific game session.
	 * @uses	POST:result_cash	float	(Optional) Result cash, if >0 the system will issue same amount to player by promotion.  Defaults to 0.
	 * @uses	POST:result			mixed	(Optional) Other results.  May use scalar or array.
	 *
	 * @return	On success: result = [ 'player_can_play' = true, 'game_sess_id' ]
	 *         On failure: result = [ 'player_can_play' = false ]
	 *         With standard return set: [ success, code, mesg, result ]
	 */
	public function game_store_results_with_bonus() {
		$this->_module_game_cons();

		if (!$this->__checkKey($this->api_key)) { return; }

		$extra_result = [];

		try {

			// Check player token
			if (empty($this->player_id)) {
				throw new Exception('Player token missing or invalid', $this->CODE_GAME_MISSING_PLAYER_TOKEN);
			}

			// Check game_code
			if (empty($this->game_code) || ! $this->comapi_games->game_is_valid($this->site, $this->game_code)) {
				throw new Exception('game_code missing or invalid', $this->CODE_GAME_MISSING_GAME_CODE);
			}

			// Check game_sess_id
			if (empty($this->game_sess_id)) {
				throw new Exception('game_sess_id missing', $this->CODE_GAME_MISSING_GAME_SESS_ID);
			}
			$game_sess_check = $this->comapi_games->game_sess_valid_and_open($this->game_sess_id, $this->site, $this->game_code, $this->player_id);
			if ($game_sess_check['code'] != 0) {
				throw new Exception ("{$game_sess_check['mesg']} ({$game_sess_check['code']})", $this->CODE_GAME_INVALID_GAME_SESS_ID);
			}

			// Check result_number, result_cash
			$result_cash = floatval($this->input->post('result_cash')) ?: 0;
			$result = $this->input->post('result');
			// if (empty($result)) {
			// 	throw new Exception('result missing', $this->CODE_GAME_MISSING_RESULT);
			// }



			if ($result_cash > 0) {
				$this->load->model(['promorules']);

				list($promorule, $promoCmsSettingId) = $this->promorules->getByCmsPromoCodeOrId(Comapi_games::GAME_DEFAULT_PROMO_ID);

				if (empty($promorule) || empty($promoCmsSettingId)) {
					throw new Exception("Promo 'comapi_games' not found", $this->CODE_GAME_COMAPI_PROMO_NOT_FOUND);
				}

				$bonusAmount = $result_cash;

				$this->utils->debug_log('t1t_comapi_module_games', 'promo-apply-issue-bonus',
					[ 'site-game-player' => "{$this->site}-{$this->game_code}-{$this->player_id}", 'game_sess_id' => $this->game_sess_id, 'bonusAmount' => $bonusAmount ,
					'promoCmsSettingId' => $promoCmsSettingId, 'promorulesId' => is_array($promorule) ? $promorule['promorulesId'] : $promorule ]);

				$withdraw_bet_times = floatval($this->comapi_games->settings_get($this->site, $this->game_code, Comapi_games::SETTING_GAME_WITHDRAW_BET_TIMES) ?: Comapi_games::GAME_DEFAULT_WITHDRAW_BET_TIMES);

				$game_title = $this->comapi_games->game_get_title($this->site, $this->game_code);
				$notes = "Comapi game: {$this->site}-{$this->game_code} ($game_title)";

				$this->promorules->manuallyApplyPromo($this->player_id, $promorule, $promoCmsSettingId, $bonusAmount, $withdraw_bet_times, $notes);

			}

			// All correct
			$dataset = [ 'result' => $result, 'cash' => $result_cash ];
			$this->comapi_games->events_log($this->site, $this->game_code, $this->player_id, Comapi_games::EVENT_GAME_RESULT, $this->game_sess_id, $dataset);

			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Game results stored successfully' ,
	    		'result'	=> [ 'game_sess_id' => $this->game_sess_id, 'issued_cash' => $result_cash ]
	    	];

	    	// TO-DO: mechanism for issuing bonus
		}
		catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false ,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> [ ]
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	}

	/**
	 * Return a list of player game results
	 *
	 * @uses	POST:api_key		string	The api_key
	 * @uses	POST:site			string	(optional) Site ident, string like 'youhu'; will use value from config file if absent.
	 * @uses	POST:game_code		int		Game code
	 * @uses	POST:token			string	Token obtained on player login
	 * @uses	POST:game_sess_id	string	(optional) Obtained from game_signin_check().  To identify specific game session.
	 * @uses	POST:offset			int		Starting index of returned data
	 * @uses	POST:limit			int		Number of rows of returned data
	 * @uses	POST:date_from		string	Start date of interval of returned data
	 * @uses	POST:date_to		string	End date of interval of returned data
	 *
	 * @return	JSON	Standard set: [ success, code, mesg, result ]
	 */
	public function game_list_results() {
		$this->_module_game_cons();

		if (!$this->__checkKey($this->api_key)) { return; }

		$extra_result = [];

		try {

			// Check player token
			if (empty($this->player_id)) {
				throw new Exception('Player token missing or invalid', $this->CODE_GAME_MISSING_PLAYER_TOKEN);
			}

			// Check game_code
			if (empty($this->game_code) || ! $this->comapi_games->game_is_valid($this->site, $this->game_code)) {
				throw new Exception('game_code missing or invalid', $this->CODE_GAME_MISSING_GAME_CODE);
			}

			if (!empty($this->game_sess_id)) {
				$game_sess_check = $this->comapi_games->game_sess_valid_and_open($this->game_sess_id, $this->site, $this->game_code, $this->player_id);
				if ($game_sess_check['code'] != 0) {
					throw new Exception ("{$game_sess_check['mesg']} ({$game_sess_check['code']})", $this->CODE_GAME_INVALID_GAME_SESS_ID);
				}
			}

			$offset		= intval($this->input->post('offset'));
			$limit		= intval($this->input->post('limit'));
			$date_from	= $this->input->post('date_from');
			$date_to	= $this->input->post('date_to');

			$game_results = $this->comapi_games->events_get($this->site, $this->game_code, $this->player_id, Comapi_games::EVENT_GAME_RESULT, null, [ 'data', 'created_at' ], $offset, $limit, $date_from, $date_to);

			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Game results read successfully' ,
	    		'result'	=> [ 'results' => $game_results ]
	    	];

		}
		catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false ,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> [ ]
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	} // End of function game_list_results()

	public function game_settings_reset_defaults() {
		$this->_module_game_cons();

		if (!$this->__checkKey($this->api_key)) { return; }


		try {
			$this->load->model(['comapi_games']);

			$live = $this->input->post('live');

			// Read default game cf in config files
			$to_set = $this->utils->getConfig('comapi_game_default_settings');
			if (empty($to_set)) {
				throw new Exception('No settings found in config', 0x220);
			}

			if (empty($live)) {
				$ret = [
		    		'success'	=> true ,
		    		'code'		=> 0 ,
		    		'mesg'		=> "Not live, show found settings and exit peacefully" ,
		    		'result'	=> [ 'default_cf_in_config' => $to_set ]
		    	];

		    	$this->returnApiResponseByArray($ret);
		    	return;
			}

			// Remove all old settings
			$this->comapi_games->settings_remove($this->site);
			$this->comapi_games->game_remove_all($this->site);

			// Re-insert settings game by game
			foreach ($to_set as $game_code => $game) {
				$this->comapi_games->game_set($this->site, $game_code, $game['title'], $game['notes']);
				foreach ($game['settings'] as $key => $val) {
					$this->utils->debug_log('game_settings_reset_defaults', 'setting defaults', [ $this->site, $game_code, $key, $val ]);
					$this->comapi_games->settings_set($this->site, $game_code, $key, $val);
				}
			}

			// Auto-review after setting
			$games = $this->comapi_games->game_get_all($this->site);
			$settings = $this->comapi_games->settings_get_all($this->site);

			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> "Game settings for {$this->site} are reset to default" ,
	    		'result'	=> [ 'games' => $games , 'settings' => $settings ]
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


	} // End of function game_insert_defaults()

    public function game_settings_review() {
		$this->_module_game_cons();

		if (!$this->__checkKey($this->api_key)) { return; }

		try {
			$games = $this->comapi_games->game_get_all($this->site);

			$settings = $this->comapi_games->settings_get_all($this->site);


			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> "Game settings for {$this->site}" ,
	    		'result'	=> [ 'games' => $games , 'settings' => $settings ]
	    	];
		}
		catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false ,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> [ ]
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	} // End of function game_settings_review()


} // End of trait t1t_comapi_module_game