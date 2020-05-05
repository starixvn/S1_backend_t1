<?php
/**
 * Generic player_center API wrapper
 * OGP-9053
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/t1t_ac_tmpl.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_mobile_reg2.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_mobile_reg1.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_third_party_deposit.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_bonus_games.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_player_game_log.php';
require_once dirname(__FILE__) . '/t1t_comapi_module_get_player_risk_info.php';

class B9b extends T1t_ac_tmpl {

	// use t1t_comapi_module_mobile_reg2;
	// use t1t_comapi_module_mobile_reg1;
	use t1t_comapi_module_third_party_deposit;
	use t1t_comapi_module_bonus_games;
	use t1t_comapi_module_player_game_log;
	use t1t_comapi_module_get_player_risk_info;

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
		// 'queryBankTypeList' ,
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
		// 'adjustPlayerBalance' ,			// OGP-6952
		'gameReport' ,
		'createAffiliate' ,				// OGP-6459
		'getPlayerProfile', 			// OGP-7624
		// 'getPlayerPasswordPlain'	,	// OGP-7661
		// // mobile reg series
		// 'mobileRegSendSms' ,
		// 'mobileRegCreatePlayer' ,
		// // mobile reg series (earlier version)
		// 'mobileCreatePlayer' ,
		// 'mobilePlayerValidationStatus' ,
		// 'mobileRevalidatePlayer' ,
		// 'mobileActivatePlayer' ,
		// OGP-9568: third party payment series
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
		// OGP-9571
		'getSysFeatures' ,
		'getRegSettings' ,
		// OGP-10251
		'listDepositMethods' ,
		// OGP-9570
		'listPromos' ,
		'applyPromo' ,
		// OGP-10906
		'bgame' ,
		// OGP-12133
		'getPlayerGameLogs' ,
		// OGP-12222
		'getPlayerRiskInfo' ,
		// OGP-12220
		'getPlayerDepositHistory' ,
		// OGP-12221
		'getPlayerWithdrawHistory' ,
	];

	function __construct() {
		parent::__construct();

		// Enable adjustPlayerBalance (disabled by default)
		// $this->enable_method_access('adjustPlayerBalance');
		// $this->enable_method_access('getPlayerPasswordPlain');
	}


}
