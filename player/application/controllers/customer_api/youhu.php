<?php
/**
 * Youhu API wrapper
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_game.php';

class Youhu extends T1t_ac_tmpl {

	use t1t_comapi_module_game;

	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = [
		'isPlayerExist' ,
		'login' ,
		'logout' ,
		'queryPlayerBalance' ,
		'apiEcho' ,
		'apiPostEcho' ,
		'game_signin_check' ,
		'game_store_results_with_bonus' ,
		'game_list_results' ,
		'game_settings_reset_defaults' ,
		'game_settings_review'
	];

    /**
     * Check api_key against configured list in system config files
     * Switchable IP matching; overrides Api_common::isValidApiKey()
     *
     * @uses	config item
     *
     * @param  string	$key_str	The api_key to be verified
     * @return boolean
     */
	public function isValidApiKey($key_str) {
		$api_keys = $this->config->item('api_key_player_center');
		$request_ip = $this->_getRequestIp();
		$skip_ip_check = false;

		if (!$this->config->item('api_key_player_center_required')) {
			$this->log('isValidApiKey', 'Not enabled');
			return false;
		}

		if ( empty($api_keys) ) {
            $this->log('isValidApiKey',  'api_key list not configured');
			return false;
		}

		foreach ($api_keys as $api_key => $ip_list) {
			if ($this->utils->validate_password_md5($key_str, $api_key) &&
				( $skip_ip_check == true || in_array($request_ip, $ip_list) ) ) {
				return true;
			}
		}

        $this->log('isValidApiKey', 'api_key invalid', ['getIP' => $request_ip, 'given key' => $key_str, 'api key list' => $api_keys]);

		return false;
	}

	// public function game_reset_defaults_5841() {
	// 	$api_key = $this->input->post('api_key');
	// 	$site = $this->input->post('site') ?: $this->utils->getConfig('comapi_site');

	// 	if (!$this->__checkKey($api_key)) { return; }

	// 	$this->load->model(['comapi_games']);

	// 	$settings = $this->comapi_games->game_get_all($site);

	// 	$set_lookup = [];
	// 	foreach ($settings as $row) {
	// 		$ident = "{$row['site']}-{$row['game_code']}-{$row['key']}";
	// 		$set_lookup[$ident] = $row;
	// 	}

	// 	$to_set = [
	// 		Comapi_games::SETTING_PLAYER_DAILY_SIGNIN_LIMIT => 1 ,
	// 		Comapi_games::SETTING_GAME_SESS_TIMEOUT_SEC		=> 600 ,
	// 		Comapi_games::SETTING_GAME_WITHDRAW_BET_TIMES	=> 5.0
	// 	];

	// 	$game_code = 1;
	// 	foreach ($to_set as $key => $val) {
	// 		$ident = "{$site}-{$game_code}-{$key}";
	// 		if (!isset($set_lookup[$ident])) {
	// 			$this->comapi_games->settings_set($site, $game_code, $key, $val);
	// 		}
	// 	}

	// } // End of function game_insert_defaults()
}