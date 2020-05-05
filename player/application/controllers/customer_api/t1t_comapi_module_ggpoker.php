<?php
/**
 * ggpoker custom API module
 * 2019/04: Built (OGP-12023)
 *
 * @author		Rupert Chen <rupert.php.tw@tripleonetech.net>
 * @copyright	Tripleonetech 2019
 */
trait t1t_comapi_module_ggpoker {
	protected $ggp_brand_id		= 'LABA360';
	protected $ggp_platform		= 'GGPOKER_GAME_API';
	protected $ggp_platform_id	= 394;
	protected $debug_mode		= true;

	protected $err = [
		'INTERNAL_ERROR'	=> 5,
		'ACCOUNT_SUSPENDED'	=> 4,
		'ACCOUNT_NOT_FOUND'	=> 3,
		'BRAND_NOT_FOUND'	=> 2,
		'INVALID_REQUEST'	=> 1,
	];

	/**
	 * ggpoker dispatcher, receives entire POST string as json request
	 * @uses	JSON-POST:requestType	API method
	 * @uses	JSON-POST:brandId		Brand ID issued by game provider
	 * @uses	JSON-POST:userId		Player login username provided by game
	 *
	 * @return	JSON	mixed content in JSON format
	 */
	public function ggpoker() {
		try {
			$raw_post_data = file_get_contents('php://input');

			$this->debug([__METHOD__, 'post_raw', $raw_post_data]);

			$post = json_decode($raw_post_data, 'as_array');

			if (empty($post)) {
				throw new Exception('Input empty', $this->err['INVALID_REQUEST']);
			}

			// Main dispatcher
			$req_type = $this->utils->safeGetArray($post, 'requestType');
			switch ($req_type) {
				case 'getUrlToken' :
					$brand_id	= $this->utils->safeGetArray($post, 'brandId');
					$gg_userid	= $this->utils->safeGetArray($post, 'userId');
					$gg_res = $this->ggp_get_url_token($brand_id, $gg_userid);
					break;

				default :
					throw new Exception("Unknown method: '{$req_type}'", $this->err['INVALID_REQUEST']);
					break;
			}

			// Result manipulation
			$this->debug([__METHOD__, 'gg_res', $gg_res]);
			if (!isset($gg_res['success']) || empty($gg_res['success'])) {
				throw new Exception($gg_res['message'], $gg_res['code']);
			}

			$ret = $gg_res['result'];
			$this->returnJsonResult($ret);
		}
		catch (Exception $ex) {
			// Translate error code to text 'code' (in adherence to ggpoker's demand)
			$err_code = $ex->getCode();
			$err_texts = array_flip($this->err);
			$err_code_text = $err_texts[$err_code];

			$err_ret = json_encode([ 'code' => $err_code_text, 'message' => $ex->getMessage() ]);
			$this->debug([__METHOD__, 'err_ret', $err_ret]);
			return $this->returnErrorStatus('400', true, '*', $err_ret);
		}
	} // End function ggpoker()

	/**
	 * getUrlToken worker method
	 * @param	string	$brand_id	Brand ID
	 * @param	string	$gg_userid	GGpoker game login username
	 * @return	array 	Standard return array
	 */
	protected function ggp_get_url_token($brand_id, $gg_userid) {
		// Default result
		$res = [ 'success' => false , 'message' => 'exec_interrupted', 'code' => $this->err['INTERNAL_ERROR'], 'result' => null ];

		try {
			$this->load->model([ 'game_provider_auth', 'common_token' ]);

			if (strtolower($brand_id) != strtolower($this->ggp_brand_id)) {
				throw new Exception('brandId empty or invaild', $this->err['BRAND_NOT_FOUND']);
			}

			if (empty($gg_userid)) {
				throw new Exception('userId empty', $this->err['ACCOUNT_NOT_FOUND']);
			}

			// Translate game username to SBE username
			$player_username = $this->game_provider_auth->getPlayerUsernameByGameUsername($gg_userid, $this->ggp_platform_id);
			$player_id = $this->player_model->getPlayerIdByUsername($player_username);

			if (empty($player_id)) {
				throw new Exception("userId '{$gg_userid}' invalid or not found", $this->err['ACCOUNT_NOT_FOUND']);
			}

			// If player is blocked
			$is_blocked = $this->game_provider_auth->isBlockedUsernameInDB($player_id, $this->ggp_platform_id);

			if (!empty($is_blocked)) {
				throw new Exception("Player '{$gg_userid}' is suspended", $this->err['ACCOUNT_SUSPENDED']);
			}

			// Get common token for player
			$token = $this->common_token->getPlayerToken($player_id);

			if (empty($token)) {
				throw new Exception('Cannot access token for player', $this->err['INTERNAL_ERROR']);
			}

			$res = [ 'success' => true , 'message' => '', 'code' => 0, 'result' => [ 'token' => $token ] ];
		}
		catch (Exception $ex) {
			$res = [ 'success' => false , 'message' => $ex->getMessage(), 'code' => $ex->getCode(), 'result' => null ];
		}
		finally {
			return $res;
		}
	} // End function ggp_get_url_token()

	protected function debug($v) {
		if ($this->debug_mode == true) {
			$this->utils->debug_log($v);
		}
	}

	protected function t1t_jbd($v, $dont_halt=null) {
		print_r($v);
		if (empty($dont_halt)) {
			die();
		}
	}

	// public function t1t_test() {
	// 	$this->load->model(['promo_games']);
	// 	$this->t1t_te("test for get_promorule_game");
	// 	$tres = $this->promo_games->get_promorule_game(63, 'with_game_entry');
	// 	$this->t1t_tear($tres);
	// 	// $this->t1t_tear(false);
	// 	// $this->t1t_tear(null);
	// 	// $this->t1t_tear('');
	// }

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

} // end trait t1t_comapi_module_bonus_games