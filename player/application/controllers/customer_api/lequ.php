<?php
/**
 * Lequ API wrapper
 * OGP-7475
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';

class Lequ extends T1t_ac_tmpl {

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
		'updatePlayerPassword' ,			// OGP-6504
		'updatePlayerProfile' ,				// OGP-6710
		'listPlayerWithdrawAccounts' ,		// OGP-6741
		'manualWithdraw' ,					// OGP-6741
		'listGamePlatforms' ,				// OGP-6822
		'listGamesByPlatform' ,				// OGP-6822
		'listGamesByPlatformGameType' ,		// OGP-6822
		'listGame_settings' ,				// OGP-6822 (internal use only)
		'updatePlayerWithdrawalPassword' ,	// OGP-6908
		'playerLoginGetToken_CT' ,			// OGP-6932
		// 'adjustPlayerBalance' ,				// OGP-6952
		'gameReport' ,						// OGP-6953

	];

	function __construct() {
		parent::__construct();

		// Leave adjustPlayerBalance unallowed
		// $this->enable_method_access('adjustPlayerBalance');
	}

}