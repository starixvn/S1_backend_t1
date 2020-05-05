<?php
/**
 * Yongji Common API wrapper
 * Rebuilt to restrict access of getPlayerPasswordPlain, OGP-7661
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_player_password.php';

class Yongji extends T1t_ac_tmpl {

	use t1t_comapi_module_player_password;

	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = [
		'apiEcho' ,
		'apiPostEcho' ,
		'getPlayerReferralCode' ,
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
		'updatePlayerPassword' ,			// OGP-6504
		'updatePlayerProfile' ,				// OGP-6710
		'listPlayerWithdrawAccounts' ,		// OGP-6741
		'manualWithdraw' ,					// OGP-6741
		'updatePlayerWithdrawalPassword' ,	// OGP-6908
		'getPlayerPasswordPlain'			// OGP-7661, wrapped in t1t_comapi_module_player_password
	];
	function __construct() {
		parent::__construct();

		// Enable getPlayerPasswordPlain (disabled by default)
		$this->enable_method_access('getPlayerPasswordPlain');
	}

}