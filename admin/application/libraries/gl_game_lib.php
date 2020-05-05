<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class gl_game_lib {

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model([ 'player_model', 'gl_game_tokens' ]);
		$this->ci->load->library([ 'utils' ]);
	}

	/**
	 * Login, insert a token into token table
	 * @param	int		$player_id		== player.playerId
	 * @return	array 	[ code(int), mesg(string), result(mixed) ]
	 * 		Player not logged in		code =  0x0	result = [ token(string) ]
	 * 		Player already logged in	code = -0x1	result = [ token(string) ]
	 * 		Player invalid				code = 0x11	result = null
	 */
	public function player_login($player_id, $demo_mode = false) {
		try{
			$res = null;

			if ($demo_mode == true) {
				$res = $this->ci->gl_game_tokens->create_token_login($player_id, 'demo');
				throw new Exception('Demo mode, returning demo token', -0x02);
			}

			if (!$this->is_player_id_valid($player_id)) {
				throw new Exception('player_id invalid', 0x11);
			}

			$act_token_rows = $this->ci->gl_game_tokens->get_active_tokens_by_player_id($player_id);
			if (empty($act_token_rows)) {
				$res = $this->ci->gl_game_tokens->create_token_login($player_id);
				throw new Exception('Player logging in', 0x0);
			}
			else {
				$token = $this->extract_latest_token($act_token_rows);
				$res = [ 'token' => $token ];
				throw new Exception('Player already logged in, returning active token', -0x01);
			}
		}
		catch (Exception $ex) {
			$ret = $this->lib_return($ex->getCode(), $ex->getMessage(), $res);
		}
		finally {
			return $ret;
		}
	} // End function player_login()

	public function create_token_recharge($player_id, $secure_id, $amount) {
		return $this->create_token_tx('recharge', $player_id, $secure_id, $amount);
	}

	public function create_token_withdraw($player_id, $secure_id, $amount) {
		return $this->create_token_tx('withdraw', $player_id, $secure_id, $amount);
	}

	protected function create_token_tx($tx, $player_id, $secure_id, $amount) {
		try {
			$res = null;

			if (!$this->is_player_id_valid($player_id)) {
				throw new Exception('player_id invalid', 0x11);
			}

			switch ($tx) {
				case 'recharge' :
					$res = $this->ci->gl_game_tokens->create_token_recharge($player_id, $secure_id, $amount);
					break;
				case 'withdraw' :
					$res = $this->ci->gl_game_tokens->create_token_withdraw($player_id, $secure_id, $amount);
					break;
				default :
					throw new Exception("tx '{$tx}' not supported", 0x12);
					break;
			}

			$ret = $this->lib_return(0, 'Tx token created', $res);
		}
		catch (Exception $ex) {
			$ret = $this->lib_return($ex->getCode(), $ex->getMessage(), $res);
		}
		finally {
			return $ret;
		}
	} // End function create_token_tx()

	public function deactivate_token($token) {
		try {
			$res = null;

			if (!$this->ci->gl_game_tokens->token_exists($token)) {
				throw new Exception('Token not found',  0x31);
			}

			$token_row = $this->ci->gl_game_tokens->get_by_token($token);

			$res = [ 'token_row' => $token_row ];

			if (!$this->ci->gl_game_tokens->is_token_active($token)) {
				throw new Exception('Already deactivated',  0x32);
			}

			$deact_res = $this->ci->gl_game_tokens->deactivate_token($token);
			$res = array_merge($res, $deact_res);

			$ret = $this->lib_return(0, 'Deactivated successfully', $res);
		}
		catch (Exception $ex) {
			$ret = $this->lib_return($ex->getCode(), $ex->getMessage(), $res);
		}
		finally {
			return $ret;
		}
	} // End function deactivate_token()

	public function mock_secure_id($prefix) {
		$this->ci->load->helper('string');
		$part_digits  = random_string('numeric', 8);
		$secure_id = $prefix . $part_digits . 'TEST';

		return $secure_id;
	}

	public function is_player_logged_in($player_id) {
		try {
			$res = null;

			if (!$this->is_player_id_valid($player_id)) {
				throw new Exception('player_id invalid', 0x21);
			}

			$act_token_rows = $this->ci->gl_game_tokens->get_active_tokens_by_player_id($player_id);
			$logged_in = !empty($act_token_rows);
			$res = [ 'logged_in' => $logged_in ];
			if ($logged_in) {
				$token = $this->extract_latest_token($act_token_rows);
				$res['token'] = $token;
			}
			throw new Exception('Player login status retrieved', 0x0);
		}
		catch (Exception $ex) {
			$ret = $this->lib_return($ex->getCode(), $ex->getMessage(), $res);
		}
		finally {
			return $ret;
		}
	} // End function is_player_logged_in()

	public function player_logout($player_id) {
		$act_token_rows = $this->ci->gl_game_tokens->get_active_tokens_by_player_id($player_id);
		if (!empty($act_token_rows)) {
			foreach ($act_token_rows as $row) {
				$id = $row['id'];
				$this->ci->gl_game_tokens->set_token_inactive($id);
			}
			$ret = $this->lib_return(0, 'Player token rendered inactive, log out successful');
		}

		if (empty($act_token_rows)) {
			$ret = $this->lib_return(-0x03, 'Player not logged in');
		}

		return $ret;
	} // End function player_logout()

	public function get_login_creds_by_token($token, $demo_mode, $demo_username) {
		$creds = $this->ci->gl_game_tokens->get_login_creds_by_token($token, $demo_mode, $demo_username);

		if (empty($creds)) {
			$retval = $this->lib_return(101, 'Token invalid', [ 'data' => null ]);
		}
		else {
			$retval = $this->lib_return(0, 'Player creds retrieved', [ 'data' => $creds ]);
		}

		return $retval;
	} // End function get_login_creds_by_token()

	public function get_recharge_creds_by_token($token) {
		return $this->get_tx_creds_by_token('recharge', $token);
	}

	public function get_withdraw_creds_by_token($token) {
		return $this->get_tx_creds_by_token('withdraw', $token);
	}

	protected function get_tx_creds_by_token($tx, $token) {
		try {
			$res = null;

			$tx_map = [ 'recharge' => Gl_game_tokens::TOKEN_TYPE_RECHARGE, 'withdraw' => Gl_game_tokens::TOKEN_TYPE_WITHDRAW ];
			$type = $tx_map[$tx];

			$creds = $this->ci->gl_game_tokens->get_tx_creds_by_redis($type, $token);

			$ret = $this->lib_return(0, 'Transaction verified', [ 'data' => $creds ]);

			if (empty($creds)) {

				if (!$this->ci->gl_game_tokens->token_exists($token, $type)) {
					$this->ci->utils->debug_log(__METHOD__, 'Token does not exist', [ 'token' => $token, 'tx' => $tx, 'type' => $type ]);
					throw new Exception('Token does not exist', 102);
				}

				if (!$this->ci->gl_game_tokens->is_token_active($token, $type)) {
					$this->ci->utils->debug_log(__METHOD__, 'Token deactivated', [ 'token' => $token ]);
					throw new Exception('Token does not exist', 102);
				}

				$creds = $this->ci->gl_game_tokens->get_tx_creds_by_token($type, $token);

				if (empty($creds)) {
					throw new Exception('Token invalid', 101);
				}

				$ret = $this->lib_return(0, 'Transaction verified', [ 'data' => $creds ]);
			}
		}
		catch (Exception $ex) {
			$ret = $this->lib_return($ex->getCode(), $ex->getMessage(), $res);
		}
		finally {
			return $ret;
		}
	}

	public function list_tokens($player_id = null, $limit = 10) {
		return $this->ci->gl_game_tokens->list_tokens($player_id, $limit);
	}

	public function find_player_id_by_username($username) {
		$player_id = $this->ci->player_model->getPlayerIdByUsername($username);

		return $player_id;
	}

	protected function is_player_id_valid($player_id) {
		$username = $this->ci->player_model->getUsernameById($player_id);
		// $this->ci->utils->debug_log(__METHOD__, [ 'username' => $username ]);
		return !empty($username);
	}

	protected function extract_latest_token($token_rows) {
		$latest_row = reset($token_rows);
		$token = $latest_row['token'];

		return $token;
	}

	protected function lib_return($code, $mesg, $result = null) {
		return [ 'code' => $code, 'mesg' => $mesg, 'result' => $result ];
	}

	public function get_login_token_by_player_id($player_id) {
		$lt_res = $this->ci->gl_game_tokens->get_active_tokens_by_player_id($player_id);

		if (empty($lt_res)) {
			return null;
		}
		else {
			$row = reset($lt_res);
			return $row['token'];
		}
	}

} // End of class gl_game_lib