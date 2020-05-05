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
// require_once dirname(__FILE__) . '/t1t_comapi_module_third_party_deposit.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_bonus_games.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_player_game_log.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_get_player_risk_info.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_sms_verification.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_sms_registration.php';
// require_once dirname(__FILE__) . '/t1t_comapi_module_mail_verification.php';

class Aff extends T1t_ac_tmpl {

	// use t1t_comapi_module_third_party_deposit;
	// use t1t_comapi_module_bonus_games;
	// use t1t_comapi_module_player_game_log;
	// use t1t_comapi_module_sms_verification;
	// use t1t_comapi_module_sms_registration;
	// use t1t_comapi_module_mail_verification;

	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = [
		'apiEcho' ,
		'apiPostEcho' ,
		// 'login' ,
		// 'logout' ,
		// 'isPlayerExist' ,
		// 'getRequestIP' ,
		// 'getClientIP' ,
		// 'createPlayer' ,
		// 'manualDeposit' ,
		// 'transfer' ,
		// 'transfer0' ,
		// 'queryPlayerBalance' ,
		// 'queryDepositBank' ,
		// // 'queryBankTypeList' ,
		// 'queryDepositWithdrawalAvailableBank' ,
		// 'updatePlayerPassword' ,		// OGP-6504
		// 'updatePlayerProfile' ,			// OGP-6710
		// 'updatePlayerProfile2' ,		// OGP-13290
		// 'listPlayerWithdrawAccounts' ,	// OGP-6741
		// 'manualWithdraw' ,				// OGP-6741
		// 'listGamePlatforms' ,			// OGP-6822
		// 'listGamePlatforms2' ,			// OGP-13359 WIP
		// 'listGamesByPlatform' ,			// OGP-6822
		// 'listGamesByPlatformGameType' ,	// OGP-6822
		// 'listGame_settings' ,			// OGP-6822 (internal use only)
		// 'updatePlayerWithdrawalPassword' ,	// OGP-6908
		// 'playerLoginGetToken_CT' ,		// OGP-6932
		// // 'adjustPlayerBalance' ,			// OGP-6952
		// 'gameReport' ,
		// 'createAffiliate' ,				// OGP-6459
		// 'getPlayerProfile', 			// OGP-7624
		// // 'getPlayerPasswordPlain'	,	// OGP-7661
		// // OGP-9568: third party payment series
		// 'listThirdPartyPayments' ,
		// 'thirdPartyDepositForm' ,
		// 'thirdPartyDepositRequest' ,
		// 'message' ,
		// 'addMessage' ,
		// // OGP-13596
		// 'messageSetRead' ,
		// // OGP-9493
		// 'addPlayerDepositAccount' ,
		// 'addPlayerWithdrawAccount' ,
		// // OGP-9815
		// 'getPlayerReports' ,
		// // OGP-9571
		// 'getSysFeatures' ,
		// // 'getRegSettings' ,
		// // OGP-10251
		// 'listDepositMethods' ,
		// // OGP-9570
		// 'listPromos' ,
		// 'applyPromo' ,
		// // OGP-13277
		// 'listPromos2' ,
		// // OGP-14625 ,
		// 'applyPromo2' ,
		// // OGP-10906
		// 'bgame' ,
		// // OGP-13250, 13251, 13326
		// 'depositPaymentCategories' ,
		// 'manualDepositForm' ,
		// 'manualDepositRequest' ,
		// 'announcements' ,
		// // OGP-13284
		// 'manualDepositLastResult' ,
		// // OGP-12133
		// 'getPlayerGameLogs' ,
		// // OGP-12844
		// 'smsVerifyStatus' ,
		// 'smsVerifySend' ,
		// 'smsVerify' ,
		// // OGP-13669
		// 'mailVerifyStatus' ,
		// 'mailVerifySend' ,
		// 'mailVerify' ,
		// // OGP-12843, 13030, 13033
		// 'passwordRecovMailSend' ,
		// 'passwordRecovMailRecv' ,
		// 'passwordRecovSmsSend' ,
		// 'passwordRecovSmsRecv' ,
		// 'playerKycImageUpload' ,
  //       'playerKycImageList' ,
		// // OGP-13076
		// 'smsRegSendSms' ,
		// 'smsRegCreatePlayer' ,
		// 'depositPaymentCategories' ,
		// // OGP-13106
		// 'setPlayerWithdrawAccountDefault' ,
		// // OGP-14401
		// 'isIpBlocked' ,
		// // OGP-14487
		// 'getAffTrackingCodeByAdditionalDomain' ,
		// // OGP-15868
		// 'getPlayerVipStatus' ,
		// // OGP-16295
		// 'transferTo' ,
		// 'transferAllTo' ,
		// 'queryPlayerBalance2' ,
		// 'playerWalletMappings' ,
		// // OGP-16511
		// 'updatePlayerBalance' ,
		// // OGP-16730
		// 'getRegSettings' ,
		// 'getReg0Settings' ,
		// // OGP-16735
		// 'manualDepositAttUpload' ,
		// OGP-17093
		'affLogin' ,
		// OGP-17094
		'affLogout' ,
		// OGP-17088
		'reportSubAffs' ,
	];

	function __construct() {
		parent::__construct();

	}


}
