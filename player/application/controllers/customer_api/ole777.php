<?php
/**
 * OLE777 exclusive API endpoint
 * OGP-9073
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_ole777_reward_sys.php';

class Ole777 extends T1t_ac_tmpl {

	use t1t_comapi_module_ole777_reward_sys;

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
		'gameReport' ,
		'createAffiliate' ,				// OGP-6459
		'getPlayerProfile', 			// OGP-7624
		'ole777_login' ,				// OGP-9073
		'ole777_auth_check' ,			// OGP-11087
		'ole777_auth_check_local' ,		// OGP-11087
	];

	function __construct() {
		parent::__construct();

		// Enable adjustPlayerBalance (disabled by default)
		// $this->enable_method_access('adjustPlayerBalance');
		// $this->enable_method_access('getPlayerPasswordPlain');
	}


}