<?php
/**
 * Testground API wrapper
 * OGP-6952
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_mobile_reg2.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_mobile_reg1.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_third_party_deposit.php';

class Testground extends T1t_ac_tmpl {

	use t1t_comapi_module_mobile_reg2;
	use t1t_comapi_module_mobile_reg1;
	use t1t_comapi_module_third_party_deposit;

	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = [
		'apiEcho' ,
		'apiPostEcho' ,
		'login' ,
		'login_new' ,
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
		'playerLoginGetToken_CT' ,		// OGP-6932
		'adjustPlayerBalance' ,			// OGP-6952
		'createAffiliate' ,				// OGP-6459
		'getPlayerProfile', 			// OGP-7624
		// 'getPlayerPasswordPlain'	,	// OGP-7661
		// mobile reg series
		'mobileRegSendSms' ,
		'mobileRegCreatePlayer' ,
		// mobile reg series (earlier version)
		'mobileCreatePlayer' ,
		'mobilePlayerValidationStatus' ,
		'mobileRevalidatePlayer' ,
		'mobileActivatePlayer' ,
		// third party payment series
		'listThirdPartyPayments' ,
		'thirdPartyDepositForm' ,
		'thirdPartyDepositRequest' ,
		'message' ,
		'addMessage' ,
		// OGP-9493
		'addPlayerDepositAccount' ,
		'addPlayerWithdrawAccount' ,
		// OGP-9815
		'getPlayerReports' ,
	];

	function __construct() {
		parent::__construct();

		// Enable adjustPlayerBalance (disabled by default)
		// $this->enable_method_access('adjustPlayerBalance');
		// $this->enable_method_access('getPlayerPasswordPlain');
	}

    /**
     * Check api_key against configured list in system config files
     * Switchable IP matching; overrides Api_common::isValidApiKey()
     *
     * @uses	config item
     *
     * @param  string	$key_str	The api_key to be verified
     * @return boolean
     */
	// public function isValidApiKey($key_str) {
	// 	$api_keys = $this->config->item('api_key_player_center');
	// 	$request_ip = $this->_getRequestIp();
	// 	$skip_ip_check = false;

	// 	if (!$this->config->item('api_key_player_center_required')) {
	// 		$this->log('isValidApiKey', 'Not enabled');
	// 		return false;
	// 	}

	// 	if ( empty($api_keys) ) {
 //            $this->log('isValidApiKey',  'api_key list not configured');
	// 		return false;
	// 	}

	// 	foreach ($api_keys as $api_key => $ip_list) {
	// 		// if ($this->utils->validate_password_md5($key_str, $api_key) &&
	// 		// 	( $skip_ip_check == true || in_array($request_ip, $ip_list) ) ) {
	// 		// 	return true;
	// 		// }
	// 		if ($this->utils->validate_password_md5($key_str, $api_key)) {
	// 			$this->log('isValidApiKey', [ 'key_str' => $key_str, 'api_key' => $api_key , 'request_ip' => $request_ip, 'ip_list' => $ip_list ]);
	// 			if ($skip_ip_check == true) {
	// 				return true;
	// 			}
	// 			else if (in_array($request_ip, $ip_list)) {
	// 				return true;
	// 			}
	// 			else {
	// 				return 128;
	// 			}
	// 		}
	// 	}

 //        $this->log('isValidApiKey', 'api_key invalid', ['getIP' => $request_ip, 'given key' => $key_str, 'api key list' => $api_keys]);

	// 	return false;
	// }

}