<?php
/**
 * Lanhai API wrapper
 * OGP-6107
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';

class Lanhai extends T1t_ac_tmpl {

	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = [
		'apiEcho' ,
		'apiPostEcho' ,
		'login' ,
		'logout' ,
		'isPlayerExist' ,
		'createPlayer' ,
		'manualDeposit' ,
		'transfer' ,
		'queryPlayerBalance' ,
		'queryDepositBank' ,
		'queryBankTypeList' ,
		'queryDepositWithdrawalAvailableBank' ,
		'updatePlayerPassword' ,		// OGP-6504
		'updatePlayerProfile' ,			// OGP-6710
		'listPlayerWithdrawAccounts' ,	// OGP-6741
		'manualWithdraw' ,				// OGP-6741
		'listGamePlatforms' ,			// OGP-6822
		'listGamesByPlatform' ,			// OGP-6822
		'listGamesByPlatformGameType' ,	// OGP-6822
		'listGame_settings' ,			// OGP-6822 (internal use only)
		'updatePlayerWithdrawalPassword' ,	// OGP-6908
		'playerLoginGetToken_CT',		// OGP-6932
		'playerLoginGetToken_FINANCE' 	// OGP-6985
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
			return true;
		}

		if ( empty($api_keys) ) {
            $this->log('isValidApiKey',  'api_key list not configured');
			return false;
		}

		foreach ($api_keys as $api_key => $ip_list) {
			// if ($this->utils->validate_password_md5($key_str, $api_key) &&
			// 	( $skip_ip_check == true || in_array($request_ip, $ip_list) ) ) {
			// 	return true;
			// }
			if ($this->utils->validate_password_md5($key_str, $api_key)) {
				if ($skip_ip_check == true) {
					return true;
				}
				else if (in_array($request_ip, $ip_list)) {
					return true;
				}
				else {
					return 128;
				}
			}
		}

        $this->log('isValidApiKey', 'api_key invalid', ['getIP' => $request_ip, 'given key' => $key_str, 'api key list' => $api_keys]);

		return false;
	}

}