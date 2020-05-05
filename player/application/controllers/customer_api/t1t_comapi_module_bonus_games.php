<?php
/**
 * Bonus mini game API module
 * 2018/01: Built (OGP-3381)
 * 2019/01: Migrated to player center API code space (OGP-10906)
 *
 * @author		Rupert Chen <rupert.php.tw@tripleonetech.net>
 * @copyright	Tripleonetech 2018-2019
 */
trait t1t_comapi_module_bonus_games {

	protected $t1t_ret;
	protected $t1t_args;
	protected $t1t_arg_mode = 'devel'; // post than get for devel; otherwise post only
	protected $t1t_default_promo_id = 'bg_only';
	protected $t1t_ident = 't1t-bgapi';
	protected $t1t_player_deposit_bet_times = 2;

	/**
	 * The dispatcher for bonus game API support, OGP-3381 / OGP-3555
	 * @param	string		$method		API method
	 * @param	post/get	$token		The common token, acquired at login-time
	 * @param	post/get	various
	 *
	 * @return	JSON	mixed content in JSON format
	 */
	public function bgame($method = null) {
		$this->load->model(['common_token']);
		$this->t1t_args = [];

		$this->t1t_game__set_default_return();

		$token = $this->t1t_game_arg('token');

		$bonus_games_cf = $this->utils->getConfig('bonus_games');

		try {
			if (empty($token)) {
				throw new Exception('token_empty');
			}

			$player_id = $this->common_token->getPlayerIdByToken($token);
			if (empty($player_id)) {
				throw new Exception('token_invalid');
			}
			$this->t1t_args['player_id'] = $player_id;

			switch ($method) {
				case 'request_play_game_list' :
					$this->t1t_game_request_play_game_list(); break;
				case 'request_bonus' :
					$this->t1t_game_request_bonus(); break;
				case 'release_bonus' :
					$this->t1t_game_release_bonus(); break;
				case 'get_bonus_history_list' :
					$this->t1t_game_get_bonus_history_list(); break;
				case 'get_reward_history' :
					$this->t1t_game_get_reward_history(); break;
				default :
					throw new Exception('method_unknown');
			}
		}
		catch (Exception $ex) {
			$this->t1t_game__set_return_failure($ex->getMessage());
		}
		finally {
			$this->returnJsonResult($this->t1t_ret);
		}
	}

	// t1t utilities

	protected function t1t_game_arg($name, $default = null) {
		if ($this->t1t_arg_mode == 'devel') {
			$val = $this->input->post($name) ?: $this->input->get($name);
		}
		else {
			$val = $this->input->post($name);
		}

		if (empty($val)) {
			$val = $default;
		}

		return $val;
	}

	protected function t1t_game__set_return_failure($mesg = null, $ret = null) {
		$this->t1t_ret['success'] = false;
		$this->t1t_ret['resultdata'] = $ret;
		if (!empty($mesg)) {
			$this->t1t_ret['message'] = $mesg;
		}
	}

	protected function t1t_game__set_return_success($resultdata = null) {
		$this->t1t_ret['success'] = true;
		$this->t1t_ret['message'] = null;
		$this->t1t_ret['resultdata'] = null;
		if (!empty($resultdata)) {
			$this->t1t_ret['resultdata'] = $resultdata;
		}
	}

	protected function t1t_game__set_default_return() {
		$this->t1t_ret = [
			'success'	=> false ,
			'message'	=> 'Execution incomplete' ,
			'resultdata' => null
		];
	}

	// Workers for real API methods

	/**
	 * Worker for API method request_play_game_list
	 * request_play_game_list: returns games available to player for playing
	 * api args:
	 * 		token		(converted to player_id in t1t_games)
	 *
	 * @uses	arg: player_id	(provided by t1t_args['player_id'] by t1t_games())
	 * @uses	model: Promo_games::get_avail_games_for_player()
	 *
	 * @return	array
	 */
	protected function t1t_game_request_play_game_list() {
		$this->load->model(['promo_games']);
		$format = $this->t1t_game_arg('format', 'default');
		try {
			$avail_games = $this->promo_games->get_avail_games_for_player($this->t1t_args['player_id'], $format);
			if (empty($avail_games)) {
				throw new Exception('No_game_available');
			}
			$this->t1t_game__set_return_success([ 'game_list' => $avail_games ]);
		}
		catch (Exception $ex) {
			$this->utils->debug_log($this->t1t_ident, 'request_play_game_list', 'Exception', $ex->getMessage());
			$this->t1t_game__set_return_failure($ex->getMessage());
		}

		return;
	}

	/**
	 * Worker for API method request_bonus
	 * request_bonus: On app call,
	 * 		(1) determines game result
	 * 		(2) stores game_config to promo_game_player_game_history
	 * 		(3) issues combined game details to appgame_config
	 *
	 * api args:
	 * 		token				(converted to player_id in t1t_games)
	 * 		player_game_id		int		corresponds to promo_game_player_to_games.id
	 * 		external_request_id	string	app-provided ID, stored for identification
	 *
	 * @uses	arg: player_id	(provided by t1t_args['player_id'] by t1t_games())
	 * @uses	model: Promo_games
	 *       is_external_request_id_present()
	 *       get_player_game_by_id()
	 *       get_bonus_game_by_id()
	 *       game_type_to_string()
	 *       get_game_resources_by_gametype_and_theme()
	 *       fair_draw()
	 *       generate_request_promotion_id()
	 *       create_history_entry()
	 *
	 * @return	set in class property t1t_ret
	 */
	protected function t1t_game_request_bonus() {
		$this->load->model(['promo_games']);
		try {
			// $game_type = $this->t1t_game_arg('game_type');
			$player_game_id = $this->t1t_game_arg('player_game_id');
			$external_request_id = $this->t1t_game_arg('external_request_id');

			$ret = [
				'is_allowed'			=> false ,
				'game_config'			=> null ,
				'background_skin_url'	=> null ,
				'request_promotion_id'	=> null ,
				'background_animation_type' => 0
			];

			// Check external_request_id
			if (empty($external_request_id)) {
				throw new Exception('external_request_id_missing');
			}

			if ($this->promo_games->is_external_request_id_present($external_request_id)) {
				throw new Exception('external_request_id_duplicate');
			}

			// Check player_game_id
			if (empty($player_game_id)) {
				throw new Exception("player_game_id_missing;($player_game_id)");
			}

			// Find record from promo_game_player_to_games
			$player_game = $this->promo_games->get_player_game_by_id($player_game_id);
			if (empty($player_game)) {
				throw new Exception("player_game_record_not_found;($player_game_id)");
			}

			if ($player_game['play_rounds'] <= 0) {
				throw new Exception("no_more_rounds_to_play_for_this_player_game_id");
			}

			$game_id = $player_game['game_id'];
			$game = $this->promo_games->get_bonus_game_by_id($game_id);
			if (empty($game)) {
				throw new Exception("game_not_found;(game_id=$game_id)");
			}
			$game_type = $this->promo_games->game_type_to_string($game['gametype_id']);

			$gameconfig = [];

			// Determine gameconfig
			switch ($game_type) {
				case 'scratchcard' :
					// Default values
					$gameconfig = [
						'maskingurl'=> null ,
						'list'		=> [ 'id' => 0, 'imgurl' => null, 'reward_msg' => null ]
					];

					// Read resources
					$resources = $this->promo_games->get_game_resources_by_gametype_and_theme($game['gametype_id'], $game['theme_id']);

					// $this->t1t_jbd($resources);

					// Determine reward
					$reward = $this->promo_games->fair_draw($game['prizes']);
					if (empty($reward)) {
						throw new Exception('Error_while_determining_reward');
					}

					// Construct list
					$prize_type_map = [ 'cash' => 0, 'vip_exp' => 1, 'nothing' => 2 ];
					$prize_type_id = $prize_type_map[$reward['prize_type']];
					$prize_item = [
						'id'  		=> 0 ,
						'imgurl' 	=> $resources['prize'][$prize_type_id] ,
						'reward_msg'=> $reward['message']
					];
					$list = [ $prize_item ];

					// Construct gameconfig
					$gameconfig = [
						'list'			=> $list ,
						'maskingurl'	=> $resources['masking']
					];
					break;

				case 'luckywheel' :
					// Default values;
					$gameconfig = [
						'list'			=> [] ,
						'skinurl'		=> null ,
						'arrowurl'		=> null ,
						'buttonurl'		=> null ,
						'reward_id'		=> -1 ,
						'reward_msg'	=> null ,
					];

					$resources = $this->promo_games->get_game_resources_by_gametype_and_theme($game['gametype_id'], $game['theme_id']);

					// Determine reward
					$reward = $this->promo_games->fair_draw($game['prizes']);
					if (empty($reward)) {
						throw new Exception('Error_while_determining_reward');
					}

					// Construct list
					$list = [];
					foreach ($game['prizes'] as $prize) {
						$list[] = [ 'id' => $prize['sort'] - 1 , 'name' => $prize['title'] ];
					}

					// Construct gameconfig
					$gameconfig = [
						'list'			=> $list ,
						'skinurl'		=> $resources['skin'] ,
						'arrowurl'		=> $resources['arrow'] ,
						'buttonurl'		=> $resources['button'] ,
						'reward_id'		=> $reward['sort'] - 1 ,
						'reward_msg'	=> $reward['message'] ,
					];

					break;

				case 'puzzlebox' :
				case 'redenvelope' :
					throw new Exception('game_type_not_supported_yet');
					break;
				default :
					throw new Exception('unknown_game_type');
					break;
			}

			if (empty($gameconfig) || empty($resources)) {
			}

			$request_promotion_id = $this->promo_games->generate_request_promotion_id();
			$ret = [
				'count'			=> $player_game['play_rounds'] ,
				'is_allowed'	=> true ,
				'game_config'	=> $gameconfig ,
				'background_skin_url'	=> $resources['bg'] ,
				'request_promotion_id'	=> $request_promotion_id ,
				'background_animation_type' => ($game['theme_id'] == 4 ? 1 : 0)
			];

			// Record game_config, request_promotion_id, external_request_id to history
			$history_entry = [
				'player_id'		=> $this->t1t_args['player_id'] ,
				'game_id'		=> $game_id ,
				'promorule_id'	=> $player_game['promorule_id'] ,
				'bonus_type'	=> $reward['prize_type'] ,
				'bonus_amount'	=> $reward['amount'] ,
				'game_config'	=> json_encode($ret) ,
				'player_to_game_id'		=> $player_game_id ,
				'external_request_id'	=> $external_request_id ,
				'request_promotion_id'	=> $request_promotion_id ,
				'created_at'	=> $this->utils->getNowForMysql()
			];

			$this->promo_games->create_history_entry($history_entry);

			$this->t1t_game__set_return_success($ret);
		}
		catch (Exception $ex) {
			$this->utils->debug_log($this->t1t_ident, 'request_bonus', 'Exception', $ex->getMessage());
			$this->t1t_game__set_return_failure($ex->getMessage(), $ret);
		}

		return;
	}

	protected function safe_array($ar, $key, $default = '') {
		if (isset($ar[$key])) {
			return $ar[$key];
		}
		else {
			return $default;
		}
	}

	/**
	 * Worker for API method get_bonus_history_list
	 * get_bonus_history_list: queries player's game play records.
	 *
	 * api args:
	 * 		token					(converted to player_id in t1t_games)
	 * 		external_request_id		hexstr	app-provided external ID
	 * 		request_promotion_id	hexstr	internally generated ID for reference
	 * 		game_para				mixed	reserved
	 *
	 * @uses	arg: player_id	(provided by t1t_args['player_id'] by t1t_games())
	 * @uses	model: Promo_games
	 *        get_player_history_by_req_id()
	 *        player_game_decrease_play_rounds()
	 *        get_promorule_game()
	 *        get_sum_bonus_by_game_and_promorule()
	 *        player_history_close_entry()
	 * @uses 	model: Promorules
	 *        getByCmsPromoCodeOrId()
	 *        manuallyApplyPromo()
	 *
	 * @return	set in class property t1t_ret
	 */
	protected function t1t_game_release_bonus() {
		$this->load->model(['promo_games']);
		$ret = [ 'is_allowed' => false, 'reward_config' => null ];
		try {

			$external_request_id = $this->t1t_game_arg('external_request_id');
			$request_promotion_id = $this->t1t_game_arg('request_promotion_id');
			if (empty($external_request_id)) {
				throw new Exception('external_request_id_missing');
			}
			if (empty($request_promotion_id)) {
				throw new Exception('request_promotion_id_missing');
			}

			$history_entry = $this->promo_games->get_player_history_by_req_id($request_promotion_id);

			if (empty($history_entry)) {
				throw new Exception("game_not_found_by_given_request_promotion_id($request_promotion_id)");
			}
			if ($history_entry['external_request_id'] != $external_request_id) {
				throw new Exception("external_request_id_mismatch($external_request_id)");
			}
			if ($history_entry['status'] == 'closed') {
				throw new Exception('this_game_is_already_closed');
			}

			$player_id = $this->t1t_args['player_id'];
			$history_id = $history_entry['id'];
			$game_id = $history_entry['game_id'];
			$promorule_id = $history_entry['promorule_id'];
			$player_bonus = $history_entry['bonus_amount'];
			$player_game_id = $history_entry['player_to_game_id'];
			$bonus_type = $history_entry['bonus_type'];

			// Decrease play_rounds by 1 on player_to_games entry
			$dpres = $this->promo_games->player_game_decrease_play_rounds($player_game_id);

			if ($dpres == -1) {
				throw new Exception('no_more_rounds_to_play_for_this_player_game_entry');
			}

			// Find promorule-to-game mapping; 1-1 mapping
			$game_promorule = $this->promo_games->get_promorule_game($promorule_id);

			if (empty($game_promorule)) {
				throw new Exception('promorule_to_games_record_not_found');
			}

			$issue_bonus = false;
			$close_notes = null;
			switch ($bonus_type) {
				case 'nothing' :
					$ret['is_allowed'] = true;
					$close_notes = 'Closing with no reward.';
					break;

				case 'cash' :
					$limit = $game_promorule['budget_cash'];
					$bonus_so_far = $this->promo_games->get_sum_bonus_by_game_and_promorule('cash', $promorule_id, $game_id);
					$expected_total_bonus = $bonus_so_far + $player_bonus;
					$this->utils->debug_log($this->t1t_ident, 'budget', $limit, 'bonus sum', $bonus_so_far, 'this bonus', $player_bonus, 'expected sum', $expected_total_bonus);

					if ($limit > 0 && $expected_total_bonus > $limit) {
						$this->promo_games->player_history_close_entry($history_id, false, "Budget hit, cannot issue cash bonus ($bonus_so_far + $player_bonus > $limit).");
						$this->utils->debug_log($this->t1t_ident, 'cash budget hit');
						// $this->promo_games->player_history_reset_reward_to_nothing($history_id);
						throw new Exception('no_prize_rewarded_promorule_cash_budget_hit');

					}
					else {
						$close_notes = "Closing with cash reward $player_bonus.  Total bonus under promorule $promorule_id = $expected_total_bonus/$limit.";
						$issue_bonus = true;
					}
					break;

				case 'vip_exp' :
					$limit = $game_promorule['budget_vipexp'];
					$bonus_so_far = $this->promo_games->get_sum_bonus_by_game_and_promorule('vip_exp', $promorule_id, $game_id);
					$expected_total_bonus = $bonus_so_far + $player_bonus;
					$this->utils->debug_log($this->t1t_ident, 'budget', $limit, 'bonus sum', $bonus_so_far, 'this bonus', $player_bonus, 'expected sum', $expected_total_bonus);

					if ($limit > 0 && $expected_total_bonus > $limit) {
						$this->promo_games->player_history_close_entry($history_id, false, "Budget hit, cannot issue vipexp bonus ($bonus_so_far + $player_bonus > $limit).");
						$this->utils->debug_log($this->t1t_ident, 'vipexp budget hit');
						// $this->promo_games->player_history_reset_reward_to_nothing($history_id);
						throw new Exception('no_prize_rewarded_promorule_vipexp_budget_hit');

					}
					else {
						$close_notes = "Closing with vipexp reward $player_bonus.  Total bonus under promorule $promorule_id = $expected_total_bonus/$limit.";
						$issue_bonus = true;
					}
					break;
			}

			if ($issue_bonus) {
				$this->load->model(['promorules']);

				list($promorule, $promoCmsSettingId) = $this->promorules->getByCmsPromoCodeOrId($this->t1t_default_promo_id);

				$bonusAmount = $bonus_type == 'cash' ? $player_bonus : 1;

				$this->utils->debug_log($this->t1t_ident, 'promo application', 'promoCmsSettingId', $promoCmsSettingId, 'bonusAmount', $bonusAmount, 'promorulesId', is_array($promorule) ? $promorule['promorulesId'] : $promorule);

				$this->promorules->manuallyApplyPromo($player_id, $promorule, $promoCmsSettingId, $bonusAmount, $this->t1t_player_deposit_bet_times);

			}

			// Close this history entry
			$this->promo_games->player_history_close_entry($history_entry['id'], 'realize', $close_notes);

			$ret['is_allowed'] = true;

			$this->t1t_game__set_return_success($ret);
		}
		catch (Exception $ex) {
			$this->utils->debug_log($this->t1t_ident, 'release_bonus', 'Exception', $ex->getMessage());
			$this->t1t_game__set_return_failure($ex->getMessage(), $ret);
		}

		return;
	}

	/**
	 * Worker for API method get_bonus_history_list
	 * get_bonus_history_list: queries player's game play records.
	 *
	 * api args:
	 * 		token		(converted to player_id in t1t_games)
	 * 		datatype	int		1 for is_done == true, 2 for is_done == false
	 * 		count		int		number of records to return.  Default: 10.
	 *
	 * @uses	arg: player_id	(provided by t1t_args['player_id'] by t1t_games())
	 * @uses	model: Promo_games::get_player_history_list()
	 *
	 * @return	set in class property t1t_ret
	 */
	protected function t1t_game_get_bonus_history_list() {
		$this->load->model(['promo_games']);
		try {
			$datatype = intval($this->t1t_game_arg('datatype', 0));
			$count = intval($this->t1t_game_arg('count', 10));
			$hist_list = $this->promo_games->get_player_history_list($this->t1t_args['player_id'], $datatype, $count);

			$this->t1t_game__set_return_success($hist_list);
		}
		catch (Exception $ex) {
			$this->utils->debug_log($this->t1t_ident, 'get_bonus_history_list', 'Exception', $ex->getMessage());
			$this->t1t_game__set_return_failure($ex->getMessage());
		}

		return;
	}

	/**
	 * Worker for API method get_reward_history
	 * get_reward_history: return winner/reward banner messages
	 *
	 * api args:
	 * 		token		(converted to player_id in t1t_games)
	 * 		count		int		number of records to return.  Default: 50.
	 *
	 * @uses	arg: player_id	(provided by t1t_args['player_id'] by t1t_games())
	 * @uses 	model: Promo_games::player_history_get_recent_winnings()
	 *
	 * @return [type] [description]
	 */
	protected function t1t_game_get_reward_history() {
		$this->load->model(['promo_games']);
		try {
			$count = intval($this->t1t_game_arg('count', 50));
			$ranking_list = $this->promo_games->player_history_get_recent_winnings($count);

			$this->t1t_game__set_return_success($ranking_list);
		}
		catch (Exception $ex) {
			$this->utils->debug_log($this->t1t_ident, 'get_reward_history', 'Exception', $ex->getMessage());
			$this->t1t_game__set_return_failure($ex->getMessage());
		}

		return;
	}

	protected function t1t_jbd($v, $dont_halt=null) {
		print_r($v);
		if (empty($dont_halt)) {
			die();
		}
	}

	public function t1t_test() {
		$this->load->model(['promo_games']);
		$this->t1t_te("test for get_promorule_game");
		$tres = $this->promo_games->get_promorule_game(63, 'with_game_entry');
		$this->t1t_tear($tres);
		// $this->t1t_tear(false);
		// $this->t1t_tear(null);
		// $this->t1t_tear('');
	}

	protected function t1t_te($s, $tag = 'p') {
		$tag_open = "<{$tag}>";
		$tag_close = "</{$tag}>";
		echo "{$tag_open}{$s}{$tag_close}";
	}

	protected function t1t_tear($v) {
		if ($v === false)
			{ $out = "(bool false)"; }
		else if (is_null($v))
			{ $out = '(null)'; }
		else if (empty($v))
			{ $out = "('' or 0)"; }
		else
			{ $out = print_r($v, 1); }
		$this->t1t_te($out, 'pre');
	}

	public function t1t_resource_dump() {
		$query = $this->db->from('promo_game_resources')
			->get();
		$res = $query->result_array();

		$out = [];
		foreach ($res as $row) {
			$item = [];
			foreach ($row as $key => $val) {
				if (in_array($key, ['id', 'updated_at'])) {
					continue;
				}
				else if (empty($val) && $key=='theme_id') {
					$item[$key] = null;
				}
				else {
					$item[$key] = $val;
				}
				// $row_str .= "\t";
			}
			// $out .= "[ $row_str ] , \n";
			$out[] = $item;
		}
		$this->returnJsonResult($out);
	}

} // end trait t1t_comapi_module_bonus_games