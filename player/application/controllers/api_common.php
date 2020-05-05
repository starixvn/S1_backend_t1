<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

require_once dirname(__FILE__) . '/modules/player_auth_module.php';
require_once dirname(__FILE__) . '/modules/promo_module.php';
require_once dirname(__FILE__) . '/modules/player_withdraw_module.php';
require_once dirname(__FILE__) . '/modules/player_password_module.php';
require_once dirname(__FILE__) . '/modules/withdrawal_process_flow_module.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_game_list_tokens.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_manual_deposit_new.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_non_token_secured_methods.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_promos.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_aff.php';
require_once dirname(__FILE__) . '/modules_api_common/comapi_core_accounts.php';

/**
 * Api_common, the client-accessible common API implementation
 *
 * Converted from class api_3tbet, REST API For TTTbet
 *
 * @property Player_message_library $player_message_library
 */
class Api_common extends BaseController {

	use player_auth_module;
    use promo_module;
    use player_withdraw_module;
    use player_password_module;
    use withdrawal_process_flow_module;

    // Core modules, separation of already too long Api_common.php
    use comapi_core_game_list_tokens;
    use comapi_core_manual_deposit_new;
    use comapi_core_non_token_secured_methods;
    use comapi_core_promos;
    use comapi_core_aff;
    use comapi_core_accounts;

    const WITHDRAWAL_ENABLED = 1;
    const WITHDRAWAL_DISABLED = 0;

	//API common code
	const CODE_SUCCESS = 0;
	const CODE_INVALID_SIGNATURE = 1;
	const CODE_INVALID_TOKEN = 2;
	const CODE_INVALID_USER = 3;
	const CODE_IP_NOT_WHITELISTED = 0x80;

	const CODE_UNIMPLEMENTED = -1;

	//API isPlayerExist
	const CODE_USERNAME_NOT_EXIST = 4;

	//API createPlayer
	const CODE_INVALID_USERNAME = 4;
	const CODE_INVALID_PASSWORD = 5;
	const CODE_DUPLICATE_USERNAME = 6;

	//API login
    const CODE_LOGIN_INVALID_PLAYER             = 0x03;
    const CODE_LOGIN_INVALID_USERNAME           = 0x04;
    const CODE_LOGIN_INVALID_PASSWORD           = 0x05;
   	const CODE_LOGIN_USER_IS_BLOCKED            = 0x10;
	// const CODE_LOGIN_PLAYER_LOCKED_OR_DELETED   = 0x11;
	const CODE_LOGIN_USER_UNDER_SELF_EXCLUSION  = 0x12;
    const CODE_LOGIN_LOGIN_FAILED               = 0x13;

	//API transfer
	const CODE_SERVICE_NOT_AVAILABLE = 4;
	const CODE_TRANSFER_FAIL = 5;

	//API report
    const CODE_NOT_ALLOWED_TYPE = 4;

    //API withdrawal
    const CODE_DISABLED_WITHDRAWAL = 4;
    const CODE_BANK_ACCOUNT_EXIST = 5;
    const CODE_INCORRECT_PASSWORD = 6;
    const CODE_INVALID_SMS_VERIFICATION = 7;
    const CODE_INVALID_WITHDRAWAL_PASSWORD = 8;
    const CODE_INCORRECT_CONTACT_NUMBER = 9;
    const CODE_WITHDRAWAL_FAILED = 10;
    const CODE_INCORRECT_WITHDRAWAL_PASSWORD = 11;
    const CODE_FORM_VALIDATION_FAILED = 12;
    const CODE_LAST_WITHDRAWAL_NOT_DONE = 13;
    const CODE_INSUFFICIENT_BALANCE = 14;
    const CODE_OVER_MAX_WITHDRAWAL = 15;
    const CODE_OVER_MAX_DAILY_WITHDRAWAL = 16;
    const CODE_OVER_WITHDRAWAL_TIMES_LIMITS = 17;
    const CODE_INVALID_WITHDRAWAL_AMOUNT = 18;
    const CODE_LESS_MIN_WITHDRAWAL = 19;
     const CODE_QUERY_WITHDRAWALBANK_EMPTY = 20;
    //API deposit
    const CODE_DEPOSIT_DEFAULT_ERROR = 4;
    const CODE_DEPOSIT_VALIDATION_ERROR = 5;
    const CODE_DEPOSIT_BANKDETAILID = 6;
    const CODE_DEPOSIT_AMOUNT_ZERO = 7;
    const CODE_DEPOSIT_AMOUNT_LESS = 8;
    const CODE_DEPOSIT_AMOUNT_DAILY_OVER = 9;
    const CODE_REQUEST_ONLINE_DEPOSIT_ERROR = 10;
    const CODE_DEPOSIT_NO_PAYMENT_ID = 11;
    const CODE_DEPOSIT_BANKTYPE_INVALID = 12;
    const CODE_DEPOSIT_PLAYERBANK_INVALID = 13;

    // queryDepositBank, queryDepositWithdrawalAvailableBank
    const CODE_QUERY_DEPOSITBANK_EMPTY = 21;

    //API requestPromotion
    // const CODE_DISABLED_PROMOTION = 4;
    // const CODE_REQUEST_PROMOTION_FAIL = 5;

    //API add message
    const CODE_NOT_ALLOW_EMPTY_MESSAGE = 4;
    const CODE_ADD_MESSAGE_FAILED = 5;

	//API send sms
	const CODE_SMS_DISABLE_SMS = 1;
	const CODE_SMS_ERROR_CAPTCHA = 2;
	const CODE_SMS_NOT_SESSION_ID = 3;
	const CODE_SMS_SEND_FREQUENTLY = 4;
	const CODE_SMS_CURRENTLY_BUSY  = 5;
	const CODE_SMS_MORE_THAN_MAX_AMOUNT_OF_DAY = 6;
	const CODE_SMS_DUPLICATE_MOBILE = 7;
	const CODE_SMS_ERROR_WITH_PAIRTY_3 = 8;

	const CODE_API_METHOD_NOT_FOUND		= 0x80;

    const CODE_EXECUTION_INCOMPLETE		= 0x1ff;

    // getPlayerReferralCode()
    const CODE_PLAYER_USERNAME_EMPTY	= 0x101;
    const CODE_PLAYER_NOT_FOUND			= 0x102;
    const CODE_MALFORMED_PLAYER_RECORD	= 0x103;

 	// getPlayerProfile
    const CODE_GPP_PLAYER_USERNAME_INVALID	= 0x105;
    const CODE_GPP_PLAYER_TOKEN_INVALID		= 0x106;

    // updatePlayerToken()
    const CODE_TOKEN_UPDATE_FAILED		= 0x109;

    // messageSetRead()
    const CODE_INTMESG_MESSAGE_ID_INVALID   = 0x10b;

    // updatePlayerPassword()
    const CODE_OLD_PASSWORD_MISSING			= 0x111;
    const CODE_NEW_PASSWORD_MISSING			= 0x112;
    const CODE_PASSWORD_FORMAT_INVALID		= 0x113;
    const CODE_OLD_PASSWORD_DOES_NOT_MATCH	= 0x114;
    const CODE_PASSWORD_REPEATS_THE_OLD		= 0x115;

    // API echo
    // const CODE_MESG_EMPTY				= 0x116;

    // Agency methods
    const CODE_PLAYER_USERNAME_INVALID	= 0x117;
    const CODE_AGENT_USERNAME_INVALID	= 0x118;
    const CODE_PLAYER_ALREADY_UNDER_THIS_AGENT = 0x119;
    const CODE_AGENT_USERNAME_IN_USE	= 0x11a;
    const CODE_AGENT_REG_FAILURE		= 0x11b;

    // updatePlayerProfile
    const CODE_UPP_PLAYER_USERNAME_INVALID	= 0x120;
    const CODE_UPP_PLAYER_TOKEN_INVALID		= 0x121;
    const CODE_UPP_FIELDS_JSON_ILLEGAL		= 0x122;
    const CODE_UPP_FIELD_ILLEGAL			= 0x123;

    // listPlayerWithdrawAccounts
    const CODE_LPWA_PLAYER_USERNAME_INVALID	= 0x128;
    const CODE_LPWA_PLAYER_TOKEN_INVALID	= 0x129;
    const CODE_LPWA_NO_WITHDRAW_ACCOUNT		= 0x12a;

    // manualWithdraw
    const CODE_MW_PLAYER_USERNAME_INVALID			= 0x130;
    const CODE_MW_PLAYER_TOKEN_INVALID				= 0x131;
    const CODE_MW_ILLEGAL_KYC_STATUS				= 0x132;
    const CODE_MW_PLAYER_WITHDRAWAL_DISABLED		= 0x133;
    const CODE_MW_WRONG_WITHDRAWAL_PASSWORD		 	= 0x134;
    const CODE_MW_SINGLE_WITHDRAWAL_AMOUNT_MAX_HIT	= 0x135;
    const CODE_MW_SINGLE_WITHDRAWAL_AMOUNT_MIN_HIT	= 0x136;
    const CODE_MW_DAILY_WITHDRAWAL_LIMIT_HIT		= 0x137;
    const CODE_MW_DAILY_WITHDRAWAL_COUNT_LIMIT_HIT	= 0x138;
    const CODE_MW_BET_AMOUNT_NOT_SATISFIED			= 0x139;
    const CODE_MW_CONDITION_AMOUNT_NOT_SATISFIED	= 0x13a;
    const CODE_MW_GROUP_LIMITS_ONLY_ONE_WITHDRAWAL	= 0x13b;
    const CODE_MW_FAILED_XFER_FROM_SUBWALLETS		= 0x13c;
    const CODE_MW_FAILED_PROMO_MAX_LIMIT_WD_RULE	= 0x13d;
    const CODE_MW_INSUFFICIENT_BALANCE				= 0x13e;
    const CODE_MW_WITHDRAWAL_FAILED					= 0x13f;
    const CODE_MW_UNDEFINED_WITHDRAW_RULE           = 0x140;
    const CODE_MW_BANKDETAILSID_INVALID             = 0x141;

    // updatePlayerWithdrawalPassword
    const CODE_UPWP_PLAYER_USERNAME_INVALID			= 0x150;
    const CODE_UPWP_PLAYER_TOKEN_INVALID			= 0x151;
    const CODE_UPWP_OLD_PASSWORD_MISSING			= 0x152;
    const CODE_UPWP_OLD_PASSWORD_NOT_MATCH			= 0x153;
    const CODE_UPWP_NEW_PASSWORD_MISSING			= 0x154;
    const CODE_UPWP_NEW_PASSWORD_REPEATS_OLD		= 0x155;

    // playerLoginGetToken_CT
    const CODE_LTCT_PLAYER_USERNAME_INVALID			= 0x158;
    const CODE_LTCT_PLAYER_TOKEN_INVALID			= 0x159;
    const CODE_LTCT_PLAYER_BLOCKED					= 0x15a;
    const CODE_LTCT_GAME_UNDER_MAINTENANCE			= 0x15b;
    const CODE_LTCT_CANNOT_LOAD_GAME_API			= 0x15c;

    // adjustPlayerBalance
    const CODE_APB_PLAYER_USERNAME_INVALID			= 0x160;
    const CODE_APB_TRANSACTION_NOT_SUPPORTED		= 0x163;
    const CODE_APB_AMOUNT_INVALID					= 0x164;
    const CODE_APB_REASON_EMPTY						= 0x165;
    const CODE_APB_BALANCE_ADJUSTMENT_ERROR			= 0x167;

    // gameReport
    const CODE_GR_TIMEZONE_INVALID					= 0x170;
    const CODE_GR_GROUPBY_INVALID					= 0x171;

    // createAffiliate
    const CODE_CA_VALIDATION_ERROR					= 0x180;
    const CODE_CA_PASSWORD_CONF_NOT_MATCH			= 0x181;
    const CODE_CA_AGE_UNDER_18						= 0x182;
    const CODE_CA_MOC_INVALID						= 0x183;
    const CODE_CA_GENDER_INVALID					= 0x184;
    const CODE_CA_PARENT_AFF_NOT_FOUND				= 0x185;
    const CODE_CA_IM_INVALID						= 0x186;
    const CODE_CA_IMTYPE_INVALID					= 0x187;
    const CODE_CA_IM_ABSENT							= 0x188;
    const CODE_CA_AFF_REG_FAILED					= 0x189;

    // getPlayerPasswordPlain
    const CODE_GPWP_PLAYER_USERNAME_INVALID			= 0x190;
    const CODE_GPWP_TIME_DIFFERENCE_TOO_LARGE		= 0x191;
    const CODE_GPWP_SECURE_STRING_INVALID			= 0x192;

    // mobileCreatePlayer() series
    const CODE_MPC_REG_VALIDATION_ERROR				= 0x198;
    const CODE_MPC_CONTACT_NUMBER_MISSING			= 0x199;
    const CODE_MPC_REG_FAILURE						= 0x19a;
    const CODE_MPC_PLAYER_UNKNOWN					= 0x19b;
    const CODE_MPC_VALIDATION_FAILED				= 0x19c;
    const CODE_MPC_PLAYER_ALREADY_ACTIVATED			= 0x19d;
    const CODE_MPC_CONTACT_NUMBER_WRONG				= 0x19e;

    const CODE_MPC_SMS_DISABLED_GLOBALLY 			= 0x1a1;
	const CODE_MPC_SESSION_ID_OR_PLAYER_ID_MISSING	= 0x1a3;
	const CODE_MPC_WAIT_AFTER_SEND_AGAIN			= 0x1a4;
	const CODE_MPC_SMS_MINUTE_LIMIT_HIT				= 0x1a5;
	const CODE_MPC_SMS_DAILY_LIMIT_HIT				= 0x1a6;
	const CODE_MPC_MOBILE_NUMBER_IN_USE				= 0x1a7;
	const CODE_MPC_THIRD_PARTY_SERVICE_ERROR		= 0x1a8;
	const CODE_MPC_PLAYER_INVALID					= 0x1a9;

	// mobileReg series
	const CODE_MREG_MOBILE_NUMBER_INVALID			= 0x1aa;
	const CODE_MREG_VALIDATION_CODE_MISSING			= 0x1ab;
	const CODE_MREG_VALIDATION_CODE_INVALID			= 0x1ac;
	const CODE_MREG_REG_FAILURE_PLAYER_NOT_CREATED	= 0x1ad;

	// thirdPartyPayment series
	const CODE_TPD_PLAYER_UNKNOWN					= 0x1b0;
	const CODE_TPD_PAYMENT_UNAVAILABLE_FOR_PLAYER	= 0x1b1;
	const CODE_TPD_PLAYER_UNDER_AGENCY_CREDITS		= 0x1b2;
	const CODE_TPD_ERROR_LOADING_PAYMENT_API		= 0x1b3;
	const CODE_TPD_AMOUNT_HIT_TRANS_MIN				= 0x1b4;
	const CODE_TPD_AMOUNT_HIT_TRANS_MAX				= 0x1b5;
	const CODE_TPD_AMOUNT_HIT_DAILY_MAX				= 0x1b6;
	const CODE_TPD_OTHER_DEPOSIT_ERROR				= 0x1b7;
	const CODE_TPD_ORDER_NOT_FOUND					= 0x1b8;
	const CODE_TPD_SYSTEM_ID_DOES_NOT_MATCH_ORDER	= 0x1b9;
	const CODE_TPD_AMOUNT_DOES_NOT_MATCH_ORDER		= 0x1ba;
	const CODE_TPD_GENPAYURLFORM_RETURN_EMPTY		= 0x1bb;
	const CODE_TPD_PAYMENT_FAILED					= 0x1bc;
	const CODE_TPD_WRONG_VALUE_FOR_DEPOSIT_FROM		= 0x1bd;
	const CODE_TPD_WRONG_VALUE_FOR_MIN_MAX_DEPOSIT	= 0x1be;

    const CODE_CPS_USERNAME_MISSING					= 0x1c8;
    const CODE_CPS_USERNAME_DOES_NOT_MATCH_REGEX	= 0x1c9;
    const CODE_CPS_USERNAME_LEN_INVALID				= 0x1ca;
    const CODE_CPS_PASSWORD_MISSING					= 0x1cb;
    const CODE_CPS_PASSWORD_DOES_NOT_MATCH_REGEX	= 0x1cc;
    const CODE_CPS_PASSWORD_LEN_INVALID				= 0x1cd;
    const CODE_CPS_CPASSWORD_DOES_NOT_MATCH			= 0x1ce;
    const CODE_CPS_USERNAME_NOT_ALLOWED				= 0x1cf;
    const CODE_CPS_USERNAME_EXISTS					= 0x1d0;
    const CODE_CPS_TERMS_NOT_CHECKED				= 0x1d1;
    const CODE_CPS_REG_FAILED						= 0x1d2;
    const CODE_CPS_AUTO_LOGIN_FAILED				= 0x1d3;

    const CODE_PAO_BANKACC_FIELD_MISSING            = 0x1e6;
    const CODE_PAO_TOO_MANY_ACCOUNTS                = 0x1e7;
	const CODE_PAO_PLAYER_USERNAME_INVALID			= 0x1e8;
	const CODE_PAO_PLAYER_TOKEN_INVALID				= 0x1e9;
	const CODE_PAO_BANKTYPEID_INVALID				= 0x1ea;
	const CODE_PAO_BANKACCNUM_INVALID				= 0x1eb;
	const CODE_PAO_BANKACCNAME_INVALID				= 0x1ec;
	const CODE_PAO_BANKACC_ALREADY_EXISTS			= 0x1ed;
	const CODE_PAO_ERROR_ADDING_ACCOUNT				= 0x1ee;
    const CODE_PAO_BANKACC_IN_USE_BY_OTHER_PLAYERS  = 0x1ef;

	const CODE_PMO_PROMO_CMS_ID_INVALID				= 0x1f0;
	const CODE_DISABLED_PROMOTION					= 0x1f1;
    const CODE_REQUEST_PROMOTION_FAIL				= 0x1f2;

    const CODE_ADT_AFF_DOMAIN_EMPTY                 = 0x1f8;
    const CODE_ADT_AFFILIATE_NOT_FOUND              = 0x1f9;

    const CODE_COMMON_INVALID_USERNAME				= 0x0a0;
    const CODE_COMMON_INVALID_TOKEN					= 0x0a1;

    // listGame series: listGamePlatforms, listGameTypesForPlatform, listGamesForPlatformGameType
    // const CODE_LG_INVALID_PLATFORM_ID				= 0x201;
    // const CODE_LG_INVALID_GAME_TYPE					= 0x202;
    const CODE_LG_PLATFORM_ID_INVALID				= 0x203;
    const CODE_LG_PLATFORM_ID_NOT_SUPPORTED			= 0x204;
    const CODE_LG_GAMETYPE_QUERY_NOT_SUPPORTED		= 0x205;
    const CODE_LG_GAME_PLATFORM_USES_LOBBY			= 0x206;
    const CODE_LG_GAMETYPE_INVALID					= 0x207;

    // smsVerify* series
    const CODE_SMSVAL_SMS_SERVICE_DISABLED			= 0x210;
    const CODE_SMSVAL_PLAYER_PHONE_NOT_VERIFIED		= 0x211;
    const CODE_SMSVAL_PLAYER_PHONE_ALREADY_VERIFIED	= 0x212;
    const CODE_SMSVAL_PLAYER_PHONE_NUMBER_NOT_SET	= 0x213;
    const CODE_SMSVAL_VERIFY_CODE_EMPTY				= 0x214;
    const CODE_SMSVAL_CODE_VERIFY_FAILED			= 0x215;

    // Reserved for Comapi_lib::comapi_send_sms()
    const CODE_SMSCOM_IP_OR_NUMBER_COOLDOWN_PERIOD	= 0x220;
    const CODE_SMSCOM_SMS_LIMIT_PER_MINUTE_HIT		= 0x221;
    const CODE_SMSCOM_SMS_LIMIT_PER_DAY_HIT			= 0x222;
    const CODE_SMSCOM_3RD_PARTY_SMS_SERVICE_ERROR	= 0x223;

    // Password recovery series
    const CODE_PREC_MAIL_NOT_MATCH                  = 0x230;
    const CODE_PREC_ERROR_SENDING_MAIL              = 0x231;
    const CODE_PREC_MOBILE_NUMBER_NOT_MATCH         = 0x232;
    const CODE_PREC_ERROR_SENDING_SMS               = 0x233;
    const CODE_PREC_VERIFY_CODE_EMPTY               = 0x234;
    const CODE_PREC_VERIFY_CODE_NOT_MATCH           = 0x235;
    const CODE_PREC_PASSWORD_NOT_MATCH              = 0x236;
    const CODE_PREC_PASSWORD_FORMAT_INVALID         = 0x237;
    const CODE_PREC_ERROR_WHILE_RESET_PASSWORD      = 0x238;
    const CODE_PREC_MAIL_TOO_FREQUENT               = 0x239;
    const CODE_PREC_MAIL_TEMPLATE_DISABLED          = 0x23a;

    // KYC image upload
    const CODE_KIMU_TOO_MANY_IMAGES_ALREADY         = 0x240;
    const CODE_KIMU_IMAGE_SIZE_INVALID              = 0x241;
    const CODE_KIMU_IMAGE_DIMENSIONS_INVALID        = 0x242;
    const CODE_KIMU_IMAGE_FORMAT_INVALID            = 0x243;
    const CODE_KIMU_UPLOAD_ERROR                    = 0x244;
    const CODE_KIMU_IMAGE_VERIFIED                  = 0x245;
    const CODE_KIMU_ID_NUMBER_EMPTY                 = 0x246;
    const CODE_KIMU_IMAGE_UPLOAD_EMPTY              = 0x247;

    // smsReg* series (trait t1t_comapi_module_sms_registration)
    const CODE_SREG_SMS_SERVICE_DISABLED            = 0x250;
    const CODE_SREG_CONTACT_NUMBER_INVALID          = 0x251;
    const CODE_SREG_CONTACT_NUMBER_IN_USE           = 0x252;
    const CODE_SREG_TUID_INVALID                    = 0x253;
    const CODE_SREG_VERIFY_CODE_INVALID             = 0x254;
    const CODE_SREG_CODE_VERIFY_FAILED              = 0x255;

    // Player account series (setPlayerWithdrawAccountDefault, removePlayerWithdrawAccount, removePlayerDepositAccount)
    const CODE_PAO_NOT_PLAYERS_WD_ACCOUNT           = 0x248;
    const CODE_PAO_ERROR_SET_DEFAULT_WD_ACCOUNT     = 0x249;

    // New manual deposit series (trait comapi_core_manual_deposit_new)
    // (manualDepositForm, manualDepositRequest)
    const CODE_MDN_BANKTYPEID_NOT_VALID_MANU_PAY    = 0x257;
    const CODE_MDN_PAYMENT_ACCOUNT_NOT_ACCESSIBLE   = 0x258;
    const CODE_MDN_IN_COOL_DOWN_PERIOD              = 0x259;
    const CODE_MDN_DEPOSIT_LIMITS_IN_EFFECT         = 0x25a;
    const CODE_MDN_LAST_DEPOSIT_NOT_COMPLETE        = 0x25b;
    const CODE_MDN_AMOUNT_NOT_GREATER_THAN_ZERO     = 0x25c;
    const CODE_MDN_AMOUNT_LESS_THAN_MINIMUM         = 0x25d;
    const CODE_MDN_AMOUNT_EXCEEDS_MAXIMUM           = 0x25e;
    const CODE_MDN_DAILY_DEPOSIT_LIMIT_HIT          = 0x25f;
    const CODE_MDN_INVALID_SECURE_ID                = 0x260;
    const CODE_MDN_NO_MANUAL_DEPOSIT_FOR_PLAYER     = 0x261;
    const CODE_MDN_PLAYERBANK_INVALID               = 0x262;
    const CODE_MDN_DEPOSIT_METHOD_INVALID           = 0x263;
    const CODE_MDN_DEPOSIT_TIME_INVALID             = 0x264;

    const CODE_XFER_CANNOT_TRANSFER_TO_SAME_WALLET  = 0x178;
    const CODE_XFER_TRANSFER_FAILED                 = 0x179;
    const CODE_XFER_NOT_BETWEEN_SUBWALLETS          = 0x17a;
    const CODE_XFER_AMOUNT_INVALID                  = 0x17b;
    const CODE_XFER_INVALID_SUBWALLET_ID            = 0x17c;
    const CODE_XFER_INSUFFICIENT_BAL_IN_WALLET      = 0x17d;
    const CODE_XFER_SUBWALLET_NOT_AVAILABLE         = 0x17e;
    const CODE_XFER_ALL_SUBWALLETS_DISABLED         = 0x17f;
    const CODE_XFER_DISALLOWED_BY_XFER_COND         = 0x177;
    const CODE_XFER_DISALLOWED_BY_WAGERING_LIMITS   = 0x176;
    const CODE_XFER_DISALLOWED_BY_WITHDRAW_COND     = 0x175;
    const CODE_XFER_NOT_TO_AGENCY                   = 0x280;
    const CODE_XFER_PLAYER_XFER_DISABLED            = 0x281;
    const CODE_XFER_PLAYER_BLOCKED_IN_GAME          = 0x282;

    const CODE_XFER_SINGLE_WALLET_SWITCH_DISABLED   = 0x286;
    const CODE_XFER_XFERALL_FAILED                  = 0x287;
    const CODE_XFER_PLAYER_PREF_DISABLED_AUTO_XFER  = 0x288;
    const CODE_XFER_XFERALL_XFER_COND_ACTIVE        = 0x289;

    const CODE_MVAL_TEMPLATE_DISABLED               = 0x268;
    const CODE_MVAL_PLAYER_EMAIL_NOT_VERIFIED       = 0x269;
    const CODE_MVAL_PLAYER_EMAIL_INVALID            = 0x26a;
    const CODE_MVAL_PLAYER_EMAIL_ALREADY_VERIFIED   = 0x26b;
    ///
    const CODE_MVAL_VERIFY_TOKEN_EMPTY              = 0x270;
    const CODE_MVAL_PLAYER_ID_EMPTY                 = 0x271;
    const CODE_MVAL_PLAYER_ID_MD5_NOT_MATCH         = 0x272;
    const CODE_MVAL_EXPIRY_MD5_NOT_MATCH            = 0x273;
    const CODE_MVAL_TOKEN_EXPIRED                   = 0x274;
    const CODE_MVAL_VERIFICATION_FAILED             = 0x275;

    const CODE_VIPS_ERROR_READING_VIP_STATUS        = 0x278;

    const CODE_IPBLK_MALFORMED_IP                   = 0x0b0;

    const CODE_UPB_SUBWALLET_ID_INVALID             = 0x280;

    const CODE_MDU_INVALID_SECURE_ID                = 0x288;
    const CODE_MDU_UPLOAD_FILE_NOT_ACCESSIBLE       = 0x289;
    const CODE_MDU_ONLY_ONE_UPLOAD_AT_A_TIME        = 0x28a;
    const CODE_MDU_MAX_ATT_NUM_PER_ORDER_REACHED    = 0x28b;
    const CODE_MDU_ERROR_UPLOADING_FILE             = 0x28c;

    const CODE_PRO_BANKDETAILSID_INVALID            = 0x290;
    const CODE_PRO_ACC_REMOVAL_FAILED               = 0x291;

    const CODE_AFF_COMMON_INVALID_USERNAME          = 0x400;
    const CODE_AFF_COMMON_INVALID_TOKEN             = 0x401;

    const CODE_AFL_LOGIN_FAILURE                    = 0x410;
    const CODE_AFL_ACC_NOT_ACTIVATED                = 0x411;
    const CODE_AFL_ACC_FROZEN                       = 0x412;

    protected $api_response = null;
    protected $comapi_req_id;

    public static $record_api_action_excludes = [
		'apiEcho', 'apiPostEcho', 'getRegSettings', 'getSysFeatures', 'gameReport',
		'listGame_settings',
		'listGamesByPlatformGameType', 'listGamesByPlatform', 'listGamePlatforms',
		'queryDepositWithdrawalAvailableBank', 'queryDepositBank',
		'getPlayerReports' ,
        'listPromos' , 'listPromos2' ,
        'announcements'
	];

	/**
	 * @var	array 	$method_access_control
	 * Method access control array, bulit for OGP-6952 adjustPlayerBalance
	 * Array element: key (method name) => bool true (access disabled)
	 *
	 * TO ENABLE ACCESS TO A METHOD, EXTEND THIS CLASS
	 * THEN USE enable_method_access('<method>') IN __construct()
	 * DO NOT EDIT THIS ARRAY DIRECTLY
	 */
	protected $method_access_control = [
		'adjustPlayerBalance'		=> true ,
		'getPlayerPasswordPlain'	=> true ,
	];

	public function __construct() {
		parent::__construct();

        $this->load->library([ 'form_validation', 'comapi_lib' ]);
        $this->load->model([ 'http_request', 'player', 'player_model' ]);

        $this->generate_comapi_req_id();
	}

    /**
     * Generate common API request ID
     * Format: hex(8)1_hex(8)2 /[0-9a-f]{8}_[0-9a-f]{8}/
     *   1st 8-place hex: return value of time()
     *   2nd 8-place hex: random
     *
     * @return  string
     */
	protected function generate_comapi_req_id() {
		$comapi_req_id = sprintf('%08x_%08x', time(), mt_rand(0x10000000, 0xffff0000));

		$this->comapi_req_id = $comapi_req_id;
	}

	protected function comapi_log() {
		$args_in = func_get_args();
		// $args_pass = array_merge([ $this->comapi_req_id ], $args_in);
		$args_pass = $args_in;
        // $this->utils->debug_log($args_pass);
		call_user_func_array([ $this->utils, 'debug_log' ], $args_pass);
	}

	/**
	 * Method access control: Check if access to $method is disabled
	 * Check in methods under access control, not in constructor
	 * Usage:
	 * 		if ($this->is_method_access_disabled()) { throw new Exception(...); }
	 * 		if ($this->is_method_access_disabled()) { $this->__returnUnimplementedResponse(); }
	 *
	 * @return	bool	true if disabled; otherwise false
	 */
	protected function is_current_method_access_disabled() {
		$method = $this->router->method;

		if (!isset($this->method_access_control[$method])) {
			return true;
		}
		else {
			return $this->method_access_control[$method];
		}
	}

	/**
	 * Method access control: Enable a given method
	 * Use in __construct() of extended classes
	 * @param	string	$method		Name of the method
	 * @return	none
	 */
	protected function enable_method_access($method) {
		$this->method_access_control[$method] = false;
	}

	public function isValidApiKey($api_key) {
        $internal_player_center_api_key=$this->utils->getConfig('internal_player_center_api_key');
        if(!empty($internal_player_center_api_key) && $internal_player_center_api_key == $api_key){
            $this->comapi_log(__METHOD__, 'match internal_player_center_api_key');
            return true;
        }

		/*SAMPLE
			$config['api_key_player_center'] = [
			    "api_key" => ['192.168.2.3']
			];
		*/
		$apiList = $this->utils->getConfig('api_key_player_center');
		$getIP = $this->_getRequestIp();

		if ($this->utils->getConfig('api_key_player_center_required')) {
			if ( empty($apiList) ) {
                $this->comapi_log(__METHOD__, 'no api_key list defined.');
				return false;
			} else {
				$whiteIpIssue=false;
				foreach ($apiList as $key => $value) {
					if ($this->utils->validate_password_md5($api_key,$key)){
						$this->comapi_log(__METHOD__, [ 'key' => $key, 'value' => $value, 'method' => $this->router->method ]);
						//* means match any ip
						if(in_array('*', $value) || in_array('any', $value)){
							$this->comapi_log(__METHOD__, 'IP * matched', [ 'api_key' => $api_key, 'req_ip' => $getIP, 'allowed_ips' => $value ]);
							return true;
						}
						if (in_array($getIP, $value)){
							$this->comapi_log(__METHOD__, 'Exact IP matched', [ 'api_key' => $api_key, 'req_ip' => $getIP, 'allowed_ips' => $value ]);
							return true;
						}
						$whiteIpIssue=true;
						break;
					}
				}
				if($whiteIpIssue){
	                $this->comapi_log(__METHOD__, 'white ip issue', [ 'api_key' => $api_key, 'req_ip' => $getIP, 'api_key-list' => $apiList] );
					return self::CODE_IP_NOT_WHITELISTED;
				}

                $this->comapi_log(__METHOD__, 'invalid', [ 'api_key' => $api_key, 'req_ip' => $getIP, 'api_key-list' => $apiList] );

                return false;
			}
		}else{
            $this->utils->debug_log('isValidApiKey is not enabled.');
            // OGP-9059: returning true when config item 'api_key_player_center_required' null or false will leave Api_common accessible to any api_key/any IP when no api_key configured.  This is a serious security hole.  Reverting to false here.
            // return true;
            return false;
        }
	}

	protected function _getRequestIp(){
        if ($this->config->item('api_request_ip_prefer_remote_addr')) {
            return (isset($_SERVER['REMOTE_ADDR']) && $this->CI->input->valid_ip($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
	    return $this->utils->getIP();
    }

    protected function _getClientIp(){
        return $this->utils->getIP();
    }

	/**
	 * check if key is validate
	 *
	 * @param $api_key
	 * @return bool|void
	 */
    protected function __checkKey($api_key){
    	$validFlag = $this->isValidApiKey($api_key);
    	// $this->utils->debug_log('__checkKey', 'validFlag', $validFlag);
		if ($validFlag === false) {
			//return error
			$this->__returnApiResponse(false, self::CODE_INVALID_SIGNATURE, lang('Invalid signature').", your api_key {$api_key} is not listed");
			return false;
		}
		else if (intval($validFlag) == self::CODE_IP_NOT_WHITELISTED) {
			// rupert: Change _getClientIp() to _getRequestIp() in consistent with isValidApiKey()
			$this->__returnApiResponse(false, self::CODE_IP_NOT_WHITELISTED, lang('IP not whitelisted') . ", your ip: {$this->_getRequestIp()}");
			return false;
		}
		else {
			return true;
		}
	}

    protected function __isLoggedIn($player_id, $token){

		if (!$player_id){
			return $this->__returnApiResponse(false, self::CODE_INVALID_USER, lang('Invalid user'));
		}

		// OGP-8489: Common_token::isTokenValid() sometimes returns true when $token is empty
		if (empty($token)){
            // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
            // return $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
            return false;
		}

		$this->load->model(array('common_token'));
		$isTokenValid = $this->common_token->isTokenValid($player_id, $token);

		$this->comapi_log(__METHOD__, [ 'isTokenValid' => $isTokenValid, 'player_id' => $player_id, 'token' => $token ]);

		if (!$isTokenValid){
            // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
            // return $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
            return false;
		}

		return true;
	}

	/**
	 * Send back player token check result instead of directly return json
	 * Built in OGP-7690
	 * @param	int		$player_id	== player.playerId
	 * @param	string	$token		Player token returned by ::login()
	 *
	 * @return	array 	[ code(int), mesg(string) ]
	 */
	protected function _isPlayerLoggedIn($player_id, $token) {
		try {
			if (!$player_id) {
				throw new Exception(lang('Invalid user'), self::CODE_INVALID_USER);
			}

			// OGP-8489: Common_token::isTokenValid() sometimes returns true when $token is empty
			if (empty($token)) {
				//return error
				// throw new Exception(lang('Invalid token or user not logged in'), self::CODE_INVALID_TOKEN);
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception(lang('Invalid token or user not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$this->load->model(array('common_token'));
			$isTokenValid = $this->common_token->isTokenValid($player_id, $token);

			if (!$isTokenValid){
				// throw new Exception(lang('Invalid token or user not logged in'), self::CODE_INVALID_TOKEN);
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception(lang('Invalid token or user not logged in'), self::CODE_COMMON_INVALID_TOKEN);
			}

			$ret = [ 'code' => 0, 'mesg' => 'Player logged in' ];
		}
		catch (Exception $ex) {
			$ret = [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ];
		}
		finally {
			return $ret;
		}
	}

    // protected function __login($player){
    //     $password = $this->utils->decodePassword($player->password);

    //     $allow_clear_session=false;
    //     $this->authentication->login($player->username, $password, $allow_clear_session);
    // }

	/**
	 * Return api response with
	 *
	 * Standard result:
	 * {
	 *		success: <true/false>,
	 *		code: <result code>,
	 *		message: <error message or normal message>,
	 *		result: {<any data>}
	 * }
	 *
	 * @param bool $success
	 * @param int $code
	 * @param string $message
	 */
	protected function __returnApiResponse($success = true, $code = self::CODE_SUCCESS, $message = 'Success', $result = [], $requested_data = null){
		$response['success'] = $success;
		$response['code'] = $code;
		$response['message'] = $message;

		if ($result) $response['result'] = $result;
		if ($requested_data){
			unset($requested_data['password']);
			unset($requested_data['cpassword']);
			$response['requested_data'] = $requested_data;
		}

		// OGP-11265: Log API operation
		$this->api_response = $response;
		$req_ip = $this->_getRequestIp();
		$this->comapi_lib->record_api_action($this->api_response, $req_ip);

        // OGP-16626
		return $this->comapi_return_json($response);
	}

    protected function __returnUnimplementedResponse(){
		$response['success'] = true;
		$response['code'] = self::CODE_UNIMPLEMENTED;
		$response['message'] = 'Not implemented';

        // OGP-16626
		return $this->comapi_return_json($response);
	}

    /**
     * Imported from BaseController, with json_encode option JSON_PARTIAL_OUTPUT_ON_ERROR added
     * OGP-16626
     * @param   mixed   $result     The result to output
     * @param   boolean $addOrigin  use addOrigin header
     * @param   string  $origin     origin in header
     *
     * @uses    BaseController::addOriginHeader()
     * @return  bool
     */
    protected function comapi_return_json($result, $addOrigin = true, $origin = "*") {

        // $this->utils->sendDebugbarDataInHeaders();

        $txt = json_encode($result, JSON_PARTIAL_OUTPUT_ON_ERROR);

        $this->output->set_content_type('application/json')->set_output($txt);
        if ($addOrigin) {
            $this->addOriginHeader($origin);
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Expose-Headers: X-Requested-With, Access-Control-Allow-Origin");
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
            header("Access-Control-Allow-Credentials: true");
        }

        return true;
    }

	protected function verify_secure($secure, $method, $api_key, $username, $timestamp) {
		// Hexstring sanitation
		$secure = preg_replace('/[^0-9a-f]/', '', strtolower($secure));
		$secure_valid = md5( $timestamp . md5( $api_key . $method . $timestamp ) . $username );
		$res = $secure_valid == $secure;

		if (!$res) {
			$this->utils->debug_log(__FUNCTION__, "$method secure verify failed", [ 'expected' => $secure_valid, 'secure' => $secure, 'method' => $method, 'api_key' => $api_key, 'username' => $username, 'timestamp' => $timestamp ]);
		}

		return $res;
	}

	/**
	 * 0.1 檢查使用者是否存在
	 *
	 * Check Player Exist
	 *
	 * http://player.og.local/iframe/auth/validate/username?username=test
	 *
	 * @api {get} /isPlayerExist/:username
	 * @apiName isPlayerExist
	 *
	 * @apiParam {String} api_key API KEY.
	 * @apiParam {String} username User name.
	 *
	 */
	public function isPlayerExist($api_key = '', $username = ''){
		if (empty($username))	{ $username = trim($this->input->post('username')); }
		if (empty($api_key))	{ $api_key = trim($this->input->post('api_key')); }

		if (!$this->__checkKey($api_key)) { return; }

		$this->load->library(['player_functions']);

		$resquest_data = array();
		if (!empty($username)){
			$resquest_data = array('username'=>$username);
		}
		$username = $this->player_functions->checkUsernameExist($username);

		if ($username){
			$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('User exist'),$resquest_data);
		}else{
			$this->__returnApiResponse(false, self::CODE_USERNAME_NOT_EXIST, lang("User doesn't exist"),$resquest_data);
		}
	}

	/**
	 * 0.2 SMS 圖像驗證
	 *
	 * SMS captcha
	 *
	 * http://player.og.local/iframe/auth/smsCaptcha/90/40
	 *
	 * @api {get} /smsCaptcha/:width/:height
	 * @api {post} {api_key}
	 * @apiName smsCaptcha
	 *
	 * @apiParam {String} width width of captcha.
	 * @apiParam {String} height height of captcha.
	 *
	 */
	public function smsCaptcha($width=null, $height=null) {

		$api_key = $this->input->post('api_key');
		unset($input['api_key']);
		if (!$this->__checkKey($api_key)) { return; }

		$active = $this->config->item('si_active');
		$allsettings = array_merge($this->config->item($active), $this->config->item('si_general'));

		if ($width) {
			$allsettings['image_width'] = $width;
		}

		if ($height) {
			$allsettings['image_height'] = $height;
		}

		$this->load->library('captcha/sms_securimage');
		$img = new Sms_securimage($allsettings);

		$this->utils->verbose_log('generate captcha session');

		$img->show($this->config->item('si_background'));
	}

	/**
	 * 0.2 發送 SMS 簡訊
	 *
	 * Send sms message
	 *
	 * http://player.og.local/iframe_module/iframe_register_send_sms_verification/:mobileNumber
	 *
	 * @api {get} /registerSendSmsVerification/:mobileNumber
	 * @api {post} {api_key, sms_captcha}
	 * @apiName smsCaptcha
	 *
	 * @apiParam {String} mobileNumber mobile number.
	 *
	 */
	public function registerSendSmsVerification($mobileNumber=null) {

		$api_key = $this->input->post('api_key');
		unset($input['api_key']);
		if (!$this->__checkKey($api_key)) { return; }

		$player_id = null;

		$this->load->library(array('session', 'sms/sms_sender'));
		$this->load->model(array('sms_verification', 'player_model'));

		if ($this->utils->getConfig('disabled_sms')) {
			return $this->__returnApiResponse(false, self::CODE_SMS_DISABLE_SMS, lang('Disabled SMS'));
		}

		if (!$this->check_sms_captcha()) {
			return $this->__returnApiResponse(false, self::CODE_SMS_ERROR_CAPTCHA, lang('error.captcha'));
		}

		$sessionId = $this->session->userdata('session_id');
		$lastSmsTime = $this->session->userdata('last_sms_time');
		$smsCooldownTime = $this->config->item('sms_cooldown_time');

		if(empty($lastSmsTime)){
			//load from redis
			$lastSmsTime=$this->utils->readRedis($mobileNumber.'_last_sms_time');
		}

		// Should not send SMS without valid session ID
		if(!$sessionId) {
			return $this->__returnApiResponse(false, self::CODE_SMS_NOT_SESSION_ID, lang('Unknown error'));
		}

		// This check ensures for a given session (i.e. session ID), SMS cannot be sent again within the cooldown period
		if ($lastSmsTime && time() - $lastSmsTime <= $smsCooldownTime) {
			return $this->__returnApiResponse(false, self::CODE_SMS_SEND_FREQUENTLY, lang('You are sending SMS too frequently. Please wait.'));
		}

		$codeCount = $this->sms_verification->getVerificationCodeCountPastMinute();
		if($codeCount > $this->config->item('sms_global_max_per_minute')) {
			$this->utils->error_log("Sent [$codeCount] SMS in the past minute, exceeded config max [".$this->config->item('sms_global_max_per_minute')."]");
			return $this->__returnApiResponse(false, self::CODE_SMS_CURRENTLY_BUSY, lang('SMS process is currently busy. Please wait.'));
		}

		$numCount = $this->sms_verification->getTodaySMSCountFor($mobileNumber);
		if($numCount >= $this->config->item('sms_max_per_num_per_day')) {
			$this->utils->error_log("Sent maximum [$numCount] SMS to this number today.");
			return $this->__returnApiResponse(false, self::CODE_SMS_MORE_THAN_MAX_AMOUNT_OF_DAY, lang('SMS process is currently busy. Please wait.'));
		}

		// OGP-12 : Check for duplicate mobile number
		$playerFromMobile = $this->player_model->getPlayerLoginInfoByNumber($mobileNumber);
		if (is_array($playerFromMobile) && isset($playerFromMobile['playerId']) > 0) {
			return $this->__returnApiResponse(false, self::CODE_SMS_DUPLICATE_MOBILE, lang('The number is in use'));
		}

		$code = $this->sms_verification->getVerificationCode($player_id, $sessionId, $mobileNumber);
        $useSmsApi = $this->sms_sender->getSmsApiName();
        $msg = $this->utils->createSmsContent($code, $useSmsApi);

		if ($this->utils->isEnabledFeature('enabled_send_sms_use_queue_server')) {
			$this->load->model('queue_result');
			$this->load->library('lib_queue');

			$mobileNum = $mobileNumber;
			$content = $msg;
			$callerType = Queue_result::CALLER_TYPE_PLAYER;
			$caller = $player_id;
			$state = null;

			$this->lib_queue->addRemoteSMSJob($mobileNum, $content, $callerType, $caller, $state);

			$this->session->set_userdata('last_sms_time', time());
			$this->utils->writeRedis($mobileNumber.'_last_sms_time', time());
			return $this->__returnApiResponse(true, self::CODE_SUCCESS);

		} else {

			if ($this->sms_sender->send($mobileNumber, $msg, $useSmsApi)) {
				$this->session->set_userdata('last_sms_time', time());
				$this->utils->writeRedis($mobileNumber.'_last_sms_time', time());
				return $this->__returnApiResponse(true, self::CODE_SUCCESS);
			} else {
				return $this->__returnApiResponse(false, self::CODE_SMS_ERROR_WITH_PAIRTY_3, $this->sms_sender->getLastError());
			}

		}
	}

    /**
     * To convey registration options for createPlayer() internally, using no params
     * Do not modify initial value here
     * @see     t1t_comapi_module_sms_registration::smsRegCreatePlayer()
     * @var     array
     */
    protected $createPlayer_option = [];

	/**
	 * Player registration
	 *
	 * Following fields are required:
	 * @uses 	POST: username		string		Player username
	 * @uses 	POST: password		string		Password
	 * @uses 	POST: cpassword		string		Password confirmation
	 * @uses 	POST: firstName		string		First Name
	 * @uses 	POST: first_name	string		Alias of firstName
	 * @uses 	POST: email			string		Email address
	 * @uses 	POST: retyped_email	string		Email address confirmation
	 * @uses 	POST: contactNumber	numeric		Mobile number
	 * @uses 	POST: terms			int			Agreement to terms.  Always send 1 (or non-empty)
	 *
	 * Following fields are optional:
	 * @uses	POST: lastName				string
	 * @uses	POST: language				string
	 * @uses	POST: gender				string	[ Male, Female ]
	 * @uses	POST: birthdate				date
	 * @uses	POST: citizenship			string
	 * @uses	POST: imAccount				string
	 * @uses	POST: imAccountType			string
	 * @uses	POST: imAccount2			string
	 * @uses	POST: imAccountType2		string
	 * @uses	POST: imAccount3			string
	 * @uses	POST: imAccountType3		string
	 * @uses	POST: birthplace			string
	 * @uses	POST: residentCountry		string
	 * @uses	POST: city					string
	 * @uses	POST: address				string
	 * @uses	POST: address2				string
	 * @uses	POST: address3				string
	 * @uses	POST: zipcode				string
	 * @uses	POST: dialing_code			string
	 * @uses	POST: id_card_number		string
	 * @uses	POST: referral_code			string	Player referral code
	 * @uses	POST: tracking_code			string	Affiliate tracking code
	 * @uses	POST: agent_tracking_code	string	Agent tracking code
	 *
	 * @return	JSON	General JSON return object
	 */
	public function createPlayer() {
		$input = $this->input->post();
		$api_key = $this->input->post('api_key');
		unset($input['api_key']);

		if (!$this->__checkKey($api_key)) { return; }

		$this->load->library([ 'session' ]);
		$this->load->model([ 'registration_setting', 'player_model' ]);

        $req_fields = array_slice($this->input->post(), 0, 20, 'preserve_keys');
        $this->comapi_log(__METHOD__, 'request', $req_fields);

		// OGP-8266 workaround
		if (empty($this->input->post('firstName'))) {
			$_POST['firstName'] = $this->input->post('first_name');
		}

		// OGP-5172 workaround
		$validation_result = $this->validate_registration('from_api_common');

		if (!$validation_result) {
			$validation_errors = trim(strip_tags(validation_errors()));
			$errors = explode("\n", $validation_errors);

			$this->__returnApiResponse(false, self::CODE_INVALID_USERNAME, lang('Create user fail'), $errors);

			return;
		}

		if ($validation_result) {

			// REQUEST PARAMETERS
			$username 			= strtolower($this->input->post('username'));
			$password 			= $this->input->post('password');
			$httpHeadrInfo 		= $this->session->userdata('httpHeaderInfo') ? : $this->utils->getHttpOnRequest();
            $header_referrer    = preg_replace('/\s+/', '', $httpHeadrInfo['referrer']);
            // Remove $_SERVER['HTTP_REFERER'], as $httpHeadrInfo['referrer'] has already included this value
            // $referrer           = $header_referrer ?: ($_SERVER['HTTP_REFERER'] ?: '');
            $referrer           = $header_referrer;

            // OGP-8266 workaround
            $first_name = empty($this->input->post('firstName')) ? $this->input->post('first_name') : $this->input->post('firstName');

			$reg_res = $this->player_model->register([

				// Player
				'username' 				=> $username,
				'gameName' 				=> $username,
				'password' 				=> $password,
				'email' 				=> $this->input->post('email'),
				'secretQuestion' 		=> $this->input->post('security_question'),
				'secretAnswer' 			=> $this->input->post('security_answer'),
				'verify' 				=> $this->player_functions->getRandomVerificationCode(),

				// Player Details
				'firstName' 			=> $first_name ,
				'lastName' 				=> $this->input->post('last_name'),
				'language' 				=> $this->input->post('language'),
				'gender' 				=> $this->input->post('gender'),
				'birthdate' 			=> $this->input->post('birthdate'),
				'contactNumber' 		=> $this->input->post('contactNumber'),
				'citizenship' 			=> $this->input->post('citizenship'),
				'imAccount' 			=> $this->input->post('im_account'),
				'imAccountType' 		=> $this->input->post('im_type'),
				'imAccount2' 			=> $this->input->post('im_account2'),
				'imAccountType2' 		=> $this->input->post('im_type2'),
				'imAccount3' 			=> $this->input->post('im_account3'),
				'imAccountType3' 		=> $this->input->post('im_type3'),
				'birthplace' 			=> $this->input->post('birthplace'),
				'registrationIp' 		=> $this->utils->getIP(),
				'registrationWebsite' 	=> $referrer,
				'residentCountry' 		=> $this->input->post('resident_country'),
				'city'					=> $this->input->post('city'),
				'address'				=> $this->input->post('address'),
				'address2'				=> $this->input->post('address2'),
				'address3'				=> $this->input->post('address3'),
				'zipcode'				=> $this->input->post('zipcode'),
				'dialing_code'			=> $this->input->post('dialing_code'),
				'id_card_number' 		=> $this->input->post('id_card_number'),

				// Codes
				'referral_code' 		=> $this->input->post('referral_code'),
				// 'affiliate_code' 		=> $this->input->post('affiliate_code'),
				'tracking_code' 		=> $this->input->post('tracking_code'),
				'agent_tracking_code'	=> $this->input->post('agent_tracking_code'),

				// SMS verification
				'verified_phone' 		=> ! empty($sms_verification_code),
				'newsletter_subscription'	=> null ,
				'communication_preference'	=> null
			]);

			$this->comapi_log(__METHOD__, 'playerId (register returned)', $reg_res);

			// OGP-9059: remove authentication usage, disable auto login after createPlayer

			// //add token
			// $result['token'] = $this->authentication->getPlayerToken();
			$login_res = $this->comapi_lib->login_priv($username, $password);
			if ($login_res['code'] != self::CODE_SUCCESS) {
				$this->__returnApiResponse(false, $login_res['code'], $login_res['mesg'], null);
			}
			$playerId = $login_res['result']['playerId'];
			$result['playerName'] = $username;
			$result['playerId'] = $playerId;
			$result['token']	= $login_res['result']['token'];

            //sync
            $this->load->model(['multiple_db_model']);
            $rlt=$this->multiple_db_model->syncPlayerFromCurrentToOtherMDB($playerId, true);
            $this->utils->debug_log('syncPlayerFromCurrentToOtherMDB', $rlt);

            // Mark as new user: only works if accessed by a proper browser
			$this->session->set_userdata('new_user', true);

			// save http_request (cookies, referer, user-agent)
			$this->saveHttpRequest($playerId, Http_request::TYPE_REGISTRATION);

			$this->session->unset_userdata('httpHeaderInfo');

			// Send message to player email for account verification
			if (!empty($exempt_email)) {
                // OBSOLETE - BaseController::sendEmail() is removed in favor of new email_manager lib
                // OGP-13938 bugfix
				// $this->sendEmail($playerId);
                $this->load->library(['email_manager']);
                $template = $this->email_manager->template('player', 'player_verify_email', array('player_id' => $playerId));
                $template_enabled = $template->getIsEnableByTemplateName(true);
                if($template_enabled['enable']) {
                    $email = $this->player->getPlayerById($playerId)['email'];
                    $template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $playerId);
                    $this->comapi_log(__METHOD__, 'Verify mail sent to player');
                }
                else {
                    $this->comapi_log(__METHOD__, 'Verify mail not sent', 'verify mail template disabled');
                }
			}
            else {
                $this->comapi_log(__METHOD__, 'Verify mail not sent', 'email is disabled in register fields');
            }

            // OGP-14852: process post-registration option from smsRegCreatePlayer()
            if (isset($this->createPlayer_option['AFTER_REG_VALIDATE_PHONE'])) {
                $this->player_model->verifyPhone($playerId);
            }

            // Invoke register event
            $this->triggerRegisterEvent($playerId);
            $this->comapi_log(__METHOD__, 'Register event triggered');

            $this->comapi_log(__METHOD__, 'registration successful return', $result);

			$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Player created successfully'), $result);
		}
	}

	/**
	 * DISUSED
	 * @deprecated
	 * Kept as a fail-safe
	 * @see login() below
	 *
	 * 2. 登录接口
	 *
	 * @api {post} /login
	 * @apiName login
	 *
	 */
	// public function login_old(){
	// 	$input = $this->input->post();
	// 	$api_key = $this->input->post('api_key');
	// 	unset($input['api_key']);
	// 	$username = $this->input->post('username');
	// 	$password = $this->input->post('password');

	// 	if ( $this->__checkKey($api_key) ) {

	// 		$this->load->library(['player_functions', 'authentication']);
	// 		$this->load->model(array('player_model'));

	// 		$playerId = $this->player_model->getPlayerIdByUsername($username);
	// 		$isBlocked = $this->player_model->isBlocked($playerId);

	// 		if ($isBlocked) {
	// 			$this->__returnApiResponse(false, self::CODE_USER_IS_BLOCKED, lang("User is blocked"));
	// 		} else if (!$this->authentication->login($username, $password)) {
	// 			$errors = array_values($this->authentication->get_error_message());
	// 			$this->__returnApiResponse(false, self::CODE_LOGIN_FAIL, lang('error.login_failed_2'), $errors,$input);
	// 		} else {

	// 			$playerId = $this->authentication->getPlayerId();
	// 			$result['playerName'] = $username;
	// 			$result['playerId'] = $playerId;

	// 			//add token
	// 			$result['token'] = $this->authentication->getPlayerToken();

	// 			$this->__returnApiResponse(true, self::CODE_SUCCESS, NULL, $result,$input);
	// 		}
	// 	}

	// }

	/**
	 * The new login
	 * Built in OGP-9059
	 *
	 * @uses	string	POST:api_key	The api_key, as md5 sum. Required.
	 * @uses 	string	POST:username	Player username.  Required.
	 * @uses 	string	POST:password	Player password.  Required.
	 * @see		Comapi_lib::login_priv()
	 * @see		Api_common::login_old()
	 *
	 * @return	JSON	Standard JSON return [ success, code, messsage, result ]
	 *                  result = [ playerName, playerId, token ] on success
	 */
	public function login() {
		$api_key	= $this->input->post('api_key');
		if (!$this->__checkKey($api_key)) { return; }

		try {
			$username	= $this->input->post('username');
			$password	= $this->input->post('password');
			$request 		= [ 'api_key' => $api_key, 'username' => $username, 'password' => $password ];
    		$this->comapi_log(__FUNCTION__, 'request', $request);

    		$login_res = $this->comapi_lib->login_priv($username, $password);

    		if ($login_res['code'] != self::CODE_SUCCESS) {
    			throw new Exception($login_res['mesg'], $login_res['code']);
    		}

			$ret = $login_res;
			$ret['success'] = true;

            $this->comapi_log(__FUNCTION__, 'Response', $ret);
		}
		catch (Exception $ex) {
	    	$this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

	    	$ret = $login_res;
	    	$ret['success'] = false;
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	}

    public function logout(){
    	$input = $this->input->post();
        $api_key = $this->input->post('api_key');
        unset($input['api_key']);
        $username = $this->input->post('username');
        $token = $this->input->post('token');

        if ( $this->__checkKey($api_key) ) {

	        $this->load->model(['player_model']);
	        $player = $this->player_model->getPlayerByUsername($username);

	        if (empty($player)){
                $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
                return;
            }

            $player_id = $player->playerId;

            if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
                return;
            }

            $this->load->library([ 'authentication' ]);
            $this->authentication->logout($player_id, 'from_comapi');

            $this->load->model(array('common_token'));
            $result = $this->common_token->disableToken($token);

            $this->__returnApiResponse(true, self::CODE_SUCCESS, 'Logout successfully');

    	}

    }

	 /**
	   OBSOLETE
     * 3. 读取余额 (DISUSED)
     *
     * query Player Balance
     *
     * @api {get} /queryPlayerBalance
     * @apiName queryPlayerBalance
     *
     * @apiParam {String} api_key API KEY.
     * @apiParam {String} username User name.
     */
    // public function queryPlayerBalance($api_key = null, $username = null, $token = null, $request_refresh = '0'){
    //     $input['username'] = $username;
    //     $input['token'] = $token;
    //     $input['request_refresh'] = $request_refresh;
    //     if ($this->__checkKey($api_key)) {
    //         $this->load->library(['player_functions']);
    //         $this->load->model(['player_model', 'common_token']);
    //         $player = $this->player_model->getPlayerByUsername($username);
    //         $refresh = (empty($request_refresh)) ? (bool)$this->input->get_post('refresh') : (bool)$request_refresh;

    //         if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

    //             $playerUsername = $player->username;
    //             $playerId = $player->playerId;
    //             $this->utils->debug_log('queryPlayerBalance', 'player', ['username' => $playerUsername, 'id' => $playerId]);
    //             $manager = $this->utils->loadGameManager();
    //             $this->load->model(array('player_model', 'wallet_model'));

    // 			$frozen = $this->utils->floorCurrencyForShow($this->player_model->getPendingBalanceById($playerId));

    // 			$mainWallet = $this->utils->floorCurrencyForShow($this->wallet_model->getMainWalletBalance($playerId));

    //             $subwallets = array();
    //             if ($refresh){
    //                 //sync all balance
    //                 $balances = $manager->queryBalanceOnAllPlatforms($playerUsername);
    //                 $controller = $this;

    //                 $this->lockAndTransForPlayerBalance($playerId, function ()
    //                     use ($controller, $playerId, $balances,&$mainWallet, &$frozen, &$subwallets) {
    //                 // $this->lockAndTrans(Utils::LOCK_ACTION_BALANCE, $playerId, function () use ($controller, $playerId, $balances, &$subwallets) {

    //                     $this->utils->debug_log('get balances',$balances);

    //                     foreach ($balances as $apiId => $apiRlt) {

    //                         if ($apiRlt['success']) {
    //                             $balance = $apiRlt['balance'];
    //                             $subwallets[$apiId] = $controller->utils->floorCurrencyForShow($balance);
    //                             $api = $controller->utils->loadExternalSystemLibObject($apiId);
    //                             // $this->utils->debug_log('apiId', $apiId, 'balance', $balance);
    //                             $api->updatePlayerSubwalletBalanceWithoutLock($playerId, $balance);
    //                         }else{
    //                             $subwallets[$apiId] = 0;
    //                         }
    //                     }

    //                     //after balance
    //                     $controller->wallet_model->recordPlayerAfterActionWalletBalanceHistory(Wallet_model::BALANCE_ACTION_REFRESH,
    //                         $playerId, null, -1, 0, null, null, null, null, null);
    //                     return true;
    //                 });

    //                 $this->utils->debug_log('queryPlayerBalance', 'subwallets', $subwallets);
    //             }else{
    //                 $palyer_current_subwallets = $this->player_functions->getSubWalletBalance($playerId);
    //                 if (!empty($palyer_current_subwallets)) {
	   //                  foreach($palyer_current_subwallets as $subwallet){
	   //                      $subwallets[$subwallet['typeId']] = $this->utils->floorCurrencyForShow($subwallet['totalBalanceAmount']);
	   //                  }
	   //              }
    //             }

    //             $wallets_mapping = array_filter($this->player_model->getSubwalletsByPlayerId($playerId), function($game_platform_id) use ($subwallets){
    //                 return (isset($subwallets[$game_platform_id])) ? TRUE : FALSE;
    //             }, ARRAY_FILTER_USE_KEY);

    //             $result = [
    //                 'mainwallet'	    =>	$mainWallet,
    //                 'frozen'		    =>	$frozen,
    //                 'subwallets'	    =>	$subwallets,
    //                 'wallets_mapping'	=>	$wallets_mapping,
    //             ];

    //             return $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Query player balance successfully'), $result);
    //         }else{
    //             $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
    //             return;
    //         }
    //     }
    // }


	/**
	 * The all-new queryPlayerBalance, GET/POST accessible
	 * Ported from xcyl, built in OGP-7658
	 *
	 * @uses	string	GET/POST:api_key	api key given by system
	 * @uses	string	GET/POST:username	Player username
	 * @uses	string	GET/POST:token		Effective token for player
	 * @uses	int		GET/POST:refresh	1 to refresh wallets, 0 or blank otherwise
	 *
	 * @uses	comapi_lib::available_subwallet_list()
	 * @uses	comapi_lib::player_query_all_balance()
	 * @uses	  comapi_lib::player_query_balance_by_platform()
	 * @uses	  comapi_lib::isWalletUpdated()
	 * @uses	comapi_lib::get_mapping_for_subwallets()
	 *
	 * @return	JSON	wallet details in result as
	 *     [ mainwallet:float, frozen:float, subwallets:array, wallets_mapping:array ]
	 */
	public function queryPlayerBalance($api_key = null, $username = null, $token = null, $refresh = null) {
		if (empty($api_key)) { $api_key = $this->input->post('api_key'); }
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

		try {
			$this->load->model([ 'player_model', 'wallet_model' ]);

    		// Read arguments
    		if (empty($token))		{ $token	= $this->input->post('token'); }
    		if (empty($username))	{ $username	= $this->input->post('username'); }
    		if (empty($refresh))	{ $refresh	= !empty($this->input->post('refresh')); }
    		$request 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->comapi_log(__FUNCTION__, 'request', $request);

			// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_INVALID_USER);
    		}

    		// Check player token
    		$logchk = $this->_isPlayerLoggedIn($player_id, $token);
    		if ($logchk['code'] != 0) {
    			throw new Exception($logchk['mesg'], $logchk['code']);
    		}
    		// if (!$this->__isLoggedIn($player_id, $token)) {
    		// 	throw new Exception('Token invalid or player not logged in', self::CODE_QPB_TOKEN_INVALID);
    		// }

    		$subwallet_res = $this->comapi_lib->available_subwallet_list($player_id);

    		// $this->utils->debug_log(__METHOD__, 'subwallet_res', $subwallet_res);

    		if ($subwallet_res['success'] == false) {
    			throw new Exception($subwallet_res['mesg'], $subwallet_res['code'] + 0x1d7);
    		}

    		$result = $subwallet_res['result'];

    		// Add wallets_mapping to result
			$result['wallets_mapping'] = $this->comapi_lib->get_mapping_for_subwallets($result['subwallets']);

			// Refresh not requested
    		if (!$refresh) {
	            $this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], 'No refreshing.  Returning cached balances');
    		}
    		// Refresh requested, but nothing to refresh
    		else if ($refresh && empty($subwallet_res['result']['wallets'])) {
    			$this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], 'All subwallets have balance 0.  Returning cached balances');
    		}
    		// Refreshing
    		else if ($refresh && !empty($result['wallets'])) {
                $this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], 'refresh targets', [ 'wallets' => $result['wallets'] ]);
	    		$api_fails = [];
	    		$api_successes = [];
    			foreach ($result['wallets'] as $api_id) {
    				$pqab_res = $this->comapi_lib->player_query_all_balance($api_id, $player_id, $username);
    				$this->comapi_log(__FUNCTION__, ['pqab_res' => $pqab_res ]);
    				$api_res = $pqab_res['subject_api'];

    				if ($api_res['success']) {
    					$result['subwallets'][$api_res['api_id']] = $api_res['subwallet_balance'];
    					$this->comapi_log(__FUNCTION__, "Refresh: balance query successful", [ 'api_id' => $api_id, 'subwallet_balance' => $api_res['subwallet_balance'] ]);
    					$api_successes[] = $api_id;
    				}
    				else {
    					$this->comapi_log(__FUNCTION__, "Refresh: balance query failed", [ 'api_id' => $api_id, 'subwallet_balance' => $api_res['subwallet_balance'] ]);
    					$api_fails[] = $api_id;
    				}
    			}

    			// Summary of refresh operation
	    		if (empty($api_fails)) {
	    			$this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], "Refresh successful for all subwallets");
	    		}
	    		else if (empty($api_successes)) {
	    			$this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], "Refresh failed for all subwallets");
	    		}
	    		else {
	    			$this->comapi_log(__FUNCTION__, [ 'refresh' => $refresh ], "Refresh failed for some subwallets", $api_fails);
	    		}
	    	} // End if ($refresh && !empty($result['wallets']))

	    	$result['refresh'] = $refresh;
	    	unset($result['success']);
	    	unset($result['wallets']);

	    	$ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Player balance query complete' ,
            	'result'	=> $result
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);

	    }
	    catch (Exception $ex) {
	    	$this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];

	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    } // End function queryPlayerBalance

    /**
     * Updates balance in individual subwallet for player, one at a time
     * POST only
     * OGP-16511
     *
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    string  POST:subwallet_id   ID of the subwallet to refresh.
     *
     * @uses    comapi_lib::player_query_all_balance()
     * @uses      comapi_lib::player_query_balance_by_platform()
     * @uses      comapi_lib::isWalletUpdated()
     *
     * @return  JSON    wallet details in result as
     *     [ subwallet_id:int, balance:float ]
     */
    public function updatePlayerBalance() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }
        $exception_res = null;

        try {
            $this->load->model([ 'player_model', 'wallet_model' ]);

            // Read arguments
            $token          = trim($this->input->post('token', 1));
            $username       = trim($this->input->post('username', 1));
            $subwallet_id   = (int) $this->input->post('subwallet_id', 1);
            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'subwallet_id' => $subwallet_id ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
            }

            // Check player token
            $logchk = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logchk['code'] != 0) {
                throw new Exception($logchk['mesg'], $logchk['code']);
            }

            $all_subwallets = $this->external_system->getActivedGameApiList();

            if (!in_array($subwallet_id, $all_subwallets)) {
                // $exception_res = [ 'valid_subwallet_id' => $all_subwallets ];
                throw new Exception(lang('Invalid subwallet_id'), self::CODE_UPB_SUBWALLET_ID_INVALID);
            }

            $pqab_res = $this->comapi_lib->player_query_all_balance($subwallet_id, $player_id, $username);

            $result = [
                'subwallet_id'  => $pqab_res['subject_api']['api_id'] ,
                'balance'       => $pqab_res['subject_api']['subwallet_balance']
            ];

            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => 'Subwallet balance successfully refreshed' ,
                'result'    => $result
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);

        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $exception_res
            ];

        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function updatePlayerBalance


	/**
     * DISUSED; OBSOLETE
     * @deprecated
	 * 4 转账接口
	 *
	 * API_transfer
	 *
	 * @api {post} /transfer
	 * @apiName transfer
	 *
	 */
	// public function transfer0(){
	// 	$api_key = $this->input->post('api_key');
	// 	$username = $this->input->post('username');
	// 	$token = $this->input->post('token');
	// 	$transfer_to = $this->input->post('transfer_to');

	// 	// $this->__checkKey($api_key);
	// 	if (!$this->__checkKey($api_key)) { return; }

	// 	$this->load->model(['player_model']);
	// 	$player = $this->player_model->getPlayerByUsername($username);

	// 	$synopsis = [ 'api_key' => $api_key, 'token' => $token, 'username' => $username, 'transfer_to' => $transfer_to ];
 //        $this->utils->debug_log(__FUNCTION__, 'request', $synopsis);

	// 	if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

	// 		// OGP-9654: Remove sys feature limit
	// 		// if (!$this->utils->isAllowedAutoTransferOnFeature()) {
	// 		// 	$this->__returnApiResponse(false, self::CODE_SERVICE_NOT_AVAILABLE, lang('notify.61'));
	// 		// 	return;
	// 		// }

	// 		$transfer_result = $this->_transferAllWallet($player->playerId, $player->username, $transfer_to);
	// 		if (FALSE === $transfer_result || (!isset($transfer_result['success']) || (FALSE === $transfer_result['success']))) {
	// 			$this->__returnApiResponse(false, self::CODE_TRANSFER_FAIL, lang('notify.62'));
	// 			return;
	// 		} else {
	// 			$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('notify.63'));
	// 			return;
	// 		}

	// 		$this->returnJsonResult($result);
	// 	}else{
	// 		$this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
	// 		return;
	// 	}

	// } // End function transfer0()


    // -- The old transfer - commented out
    // public function transfer1() {
    //     $api_key = $this->input->post('api_key', 1);
    //     if (!$this->__checkKey($api_key)) { return; }

    //     try {
    //         $username       = $this->input->post('username', 1);
    //         $token          = $this->input->post('token', 1);
    //         $amount         = floatval($this->input->post('amount', 1));
    //         $transfer_to    = intval($this->input->post('transfer_to', 1));
    //         $transfer_from  = intval($this->input->post('transfer_from', 1));

    //         $request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'amount' => $amount, 'transfer_to' => $transfer_to, 'transfer_from' => $transfer_from ];
    //         $this->comapi_log(__METHOD__, 'request', $request);

    //         $res_mesg = '';
    //         $res_data = null;

    //         // 0a: Check player username
    //         $player_id  = $this->player_model->getPlayerIdByUsername($username);
    //         if (empty($player_id)) {
    //             throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
    //         }

    //         // 0b: Check player token
    //         if (!$this->__isLoggedIn($player_id, $token)) {
    //             throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
    //         }

    //         // Reject transfer to same wallet
    //         if (!empty($amount) && $transfer_to == $transfer_from) {
    //             throw new Exception(lang('Cannot transfer to same wallet'), self::CODE_XFER_CANNOT_TRANSFER_TO_SAME_WALLET);
    //         }

    //         // 1: Reject transfer between subwallets
    //         if ($transfer_to != 0 && $transfer_from != 0) {
    //             throw new Exception(lang('Cannot transfer between subwallets'), self::CODE_XFER_NOT_BETWEEN_SUBWALLETS);
    //         }

    //         // 2: Check for amount
    //         if (empty($amount) && !empty($transfer_from) && !empty($transfer_to)) {
    //             throw new Exception(lang('Amount required'), self::CODE_XFER_AMOUNT_INVALID);
    //         }

    //         $this->load->model([ 'external_system' ]);

    //         $game_platform_id = $transfer_to ?: $transfer_from;
    //         $bal = $this->comapi_lib->available_subwallet_list($player_id);
    //         $this->comapi_log(__METHOD__, 'balance', $bal);

    //         if (empty($game_platform_id)) {
    //             // 'transfer-all-back' operation
    //             $amount_to_xfer = 0.0; $amount_blocked = 0.0;
    //             $num_to_xfer = 0; $num_blocked = 0;
    //             $blocked_wallets = [];

    //             foreach ($bal['result']['subwallets'] as $game_platform_id => $gp_amount) {
    //                 if ($gp_amount > 0.0) {
    //                     $amount_to_xfer += $gp_amount;
    //                     ++$num_to_xfer;
    //                 }
    //                 if (!$this->external_system->isGameApiActive($game_platform_id) || $this->CI->external_system->isGameApiMaintenance($game_platform_id)) {
    //                     $amount_blocked += $gp_amount;
    //                     ++$num_blocked;
    //                     $blocked_wallets[$game_platform_id] = $gp_amount;
    //                 }
    //             }

    //             $this->comapi_log(__METHOD__, 'transfer-all-back', [
    //                 'to-xfer' => [ 'amount' => $amount_to_xfer, 'num' => $num_to_xfer ] ,
    //                 'blocked' => [ 'amount' => $amount_blocked, 'num' => $num_blocked, 'wallets' => $blocked_wallets ]
    //             ]);
    //             if ($num_to_xfer == 0) {
    //                 // Nothing to transfer back
    //                 if ($amount_blocked > 0) {
    //                     // With some blocked
    //                     throw new Exception(lang('Cannot transfer back to main wallet, all amount are in subwallets under maintenance or disabled'), self::CODE_XFER_ALL_SUBWALLETS_DISABLED);
    //                 }
    //                 else {
    //                     // Nothing blocked
    //                     $res_mesg = lang('Balance transferred back to main wallet successfully.');
    //                 }
    //             }
    //             else {
    //                 // Still something to transfer back
    //                 $res_mesg = lang('Balance transferred back to main wallet successfully.');
    //                 if ($amount_blocked > 0) {
    //                     $res_mesg .= sprintf(lang('Some amount (%.2f) cannot be transferred because subwallets are under maintenance or disabled.'), $amount_blocked);
    //                     $res_data = [ 'amount_blocked' => $amount_blocked ];
    //                 }

    //             }
    //         }
    //         else {
    //             // 3: Check if game platform (sub wallet) is available
    //             if ( !empty($game_platform_id) && ( !$this->external_system->isGameApiActive($game_platform_id) || $this->CI->external_system->isGameApiMaintenance($game_platform_id) ) ) {
    //                 throw new Exception(sprintf(lang('Cannot transfer, sub wallet %d not available'), $game_platform_id), self::CODE_XFER_SUBWALLET_NOT_AVAILABLE);
    //             }

    //             // 4a: Check for valid subwallet ID (from)
    //             if ($transfer_from != 0 && !isset($bal['result']['subwallets'][$transfer_from])) {
    //                 throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_from), self::CODE_XFER_INVALID_SUBWALLET_ID);
    //             }

    //             // 4b: Check for valid subwallet ID (to)
    //             if ($transfer_to != 0 && !isset($bal['result']['subwallets'][$transfer_to])) {
    //                 throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_to), self::CODE_XFER_INVALID_SUBWALLET_ID);
    //             }

    //             // 5: Check for enough balance
    //             $src_balance = -1;
    //             if ($transfer_from == 0) {
    //                 $src_balance = $bal['result']['mainwallet'];
    //             }
    //             else {
    //                 $src_balance = $bal['result']['subwallets'][$transfer_from];
    //             }

    //             if (!empty($amount) && $amount > $src_balance) {
    //                 throw new Exception(sprintf(lang('Insufficient balance in wallet %d'), $transfer_from), self::CODE_XFER_INSUFFICIENT_BAL_IN_WALLET);
    //             }
    //         }

    //         // The main event: run transfer operation
    //         if (empty($amount)) {
    //             $transfer_result = $this->_transferAllWallet($player_id, $username, $transfer_to);
    //         }
    //         else {
    //             $transfer_result = $this->utils->transferWallet($player_id, $username, $transfer_from, $transfer_to, $amount);
    //         }

    //         // 6: Aftermath
    //         if (FALSE === $transfer_result || (!isset($transfer_result['success']) || (FALSE === $transfer_result['success']))) {
    //             throw new Exception(lang('Transfer failed'), self::CODE_XFER_TRANSFER_FAILED);
    //         }

    //         $ret = [
    //             'success'   => true,
    //             'code'      => 0,
    //             'mesg'      => $res_mesg ?: lang('Transfer successful'),
    //             'result'    => $res_data
    //         ];
    //         $this->comapi_log(__METHOD__, 'Successful response', $ret);
    //     }
    //     catch (Exception $ex) {
    //         $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
    //         $this->comapi_log(__METHOD__, 'Exception', $ex_log);

    //         $ret = [
    //             'success'   => false,
    //             'code'      => $ex->getCode(),
    //             'mesg'      => $ex->getMessage(),
    //             'result'    => null
    //         ];
    //     }
    //     finally {
    //         $this->returnApiResponseByArray($ret);
    //     }
    // }

    // Reverted changes of OGP-16296 - 3/05/2020 - rupert
    // Restoring changes of OGP-16296, with withdrawal conditions removed - 3/06/2020 - rupert
    /**
     * Transfers balance between subwallets and main wallet
     * Revised; supersedes the old transfer
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int     POST:transfer_from  (optional) subwallet to transfer from
     * @uses    int     POST:transfer_to    (optional) subwallet to transfer to
     * @uses    float   POST:amount         (optional) Amount to transfer
     *
     * @return  JSON    Standard JSON return object
     */
    public function transfer() {
        $api_key = $this->input->post('api_key', 1);
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $username       = $this->input->post('username', 1);
            $token          = $this->input->post('token', 1);
            $amount         = floatval($this->input->post('amount', 1));
            $transfer_to    = intval($this->input->post('transfer_to', 1));
            $transfer_from  = intval($this->input->post('transfer_from', 1));

            $request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'amount' => $amount, 'transfer_to' => $transfer_to, 'transfer_from' => $transfer_from ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $res_mesg = '';
            $res_data = null;

            // 0a: Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
            }

            // 0b: Check player token
            if (!$this->__isLoggedIn($player_id, $token)) {
                throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
            }

            // Reject transfer to same wallet
            if (!empty($amount) && $transfer_to == $transfer_from) {
                throw new Exception(lang('Cannot transfer to same wallet'), self::CODE_XFER_CANNOT_TRANSFER_TO_SAME_WALLET);
            }

            // 1: Reject transfer between subwallets
            if ($transfer_to != 0 && $transfer_from != 0) {
                throw new Exception(lang('Cannot transfer between subwallets'), self::CODE_XFER_NOT_BETWEEN_SUBWALLETS);
            }

            // 2: Check for amount
            if (empty($amount) && !empty($transfer_from) && !empty($transfer_to)) {
                throw new Exception(lang('Amount required'), self::CODE_XFER_AMOUNT_INVALID);
            }

            $this->load->model([ 'external_system', 'transfer_condition', 'withdraw_condition' ]);

            $game_platform_id = $transfer_to ?: $transfer_from;
            $bal = $this->comapi_lib->available_subwallet_list($player_id);
            $this->comapi_log(__METHOD__, 'balance', $bal);

            if (empty($game_platform_id)) {
                // 'transfer-all-back' operation
                $amount_to_xfer = 0.0; $amount_blocked = 0.0;
                $num_to_xfer = 0; $num_blocked = 0;
                $blocked_wallets = [];

                foreach ($bal['result']['subwallets'] as $game_platform_id => $gp_amount) {
                    if ($gp_amount > 0.0) {
                        $amount_to_xfer += $gp_amount;
                        ++$num_to_xfer;
                    }

                    // a: transfer condition (from subwallets)
                    $xfer_cond = $this->transfer_condition->isPlayerTransferConditionExist($player_id, $transfer_from, $transfer_to);

                    // b: withdrawal conditions (from subwallets)
                    $wd_cond_clear = true;
                    // if ($this->utils->isEnabledFeature('enabled_auto_check_withdraw_condition')) {
                    //     $mesg = null;
                    //     $wd_cond_clear = $this->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($player_id, $mesg, $transfer_from, 'unfinished_is_false');
                    //     $this->comapi_log(__METHOD__, [ 'mesg_from_wd_cond_check' => $mesg ] );
                    // }

                    // c: inactive or maintenance
                    $inactive_or_maintenance = !$this->external_system->isGameApiActive($game_platform_id) || $this->CI->external_system->isGameApiMaintenance($game_platform_id);

                    // c + b + a
                    if ($inactive_or_maintenance || !empty($xfer_cond) || !$wd_cond_clear) {
                        $amount_blocked += $gp_amount;
                        ++$num_blocked;
                        $blocked_wallets[$game_platform_id] = $gp_amount;
                    }
                }

                $this->comapi_log(__METHOD__, 'transfer-all-back', [
                    'to-xfer' => [ 'amount' => $amount_to_xfer, 'num' => $num_to_xfer ] ,
                    'blocked' => [ 'amount' => $amount_blocked, 'num' => $num_blocked, 'wallets' => $blocked_wallets ]
                ]);
                if ($num_to_xfer == 0) {
                    // Nothing to transfer back
                    if ($amount_blocked > 0) {
                        // With some blocked
                        throw new Exception(lang('Cannot transfer back to main wallet, all amount are in subwallets under maintenance or disabled'), self::CODE_XFER_ALL_SUBWALLETS_DISABLED);
                    }
                    else {
                        // Nothing blocked
                        $res_mesg = lang('Balance transferred back to main wallet successfully.');
                    }
                }
                else {
                    // Still something to transfer back
                    $res_mesg = lang('Balance transferred back to main wallet successfully.');
                    if ($amount_blocked > 0) {
                        $res_mesg .= sprintf(lang('Some amount (%.2f) cannot be transferred because subwallets are under maintenance or disabled.'), $amount_blocked);
                        $res_data = [ 'amount_blocked' => $amount_blocked ];
                    }

                }
            }
            else {
                // 3: Check if game platform (sub wallet) is available
                if ( !empty($game_platform_id) && ( !$this->external_system->isGameApiActive($game_platform_id) || $this->CI->external_system->isGameApiMaintenance($game_platform_id) ) ) {
                    throw new Exception(sprintf(lang('Cannot transfer, sub wallet %d not available'), $game_platform_id), self::CODE_XFER_SUBWALLET_NOT_AVAILABLE);
                }

                // 4a: Check for valid subwallet ID (from)
                if ($transfer_from != 0 && !isset($bal['result']['subwallets'][$transfer_from])) {
                    throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_from), self::CODE_XFER_INVALID_SUBWALLET_ID);
                }

                // 4b: Check for valid subwallet ID (to)
                if ($transfer_to != 0 && !isset($bal['result']['subwallets'][$transfer_to])) {
                    throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_to), self::CODE_XFER_INVALID_SUBWALLET_ID);
                }

                // 5: Check for enough balance
                $src_balance = -1;
                if ($transfer_from == 0) {
                    $src_balance = $bal['result']['mainwallet'];
                }
                else {
                    $src_balance = $bal['result']['subwallets'][$transfer_from];
                }

                if (!empty($amount) && $amount > $src_balance) {
                    throw new Exception(sprintf(lang('Insufficient balance in wallet %d'), $transfer_from), self::CODE_XFER_INSUFFICIENT_BAL_IN_WALLET);
                }

                // 6: Check xfer condition (from-subwallets)
                $xfer_cond = $this->transfer_condition->isPlayerTransferConditionExist($player_id, $transfer_from, $transfer_to);
                if (!empty($xfer_cond)) {
                    throw new Exception(lang('Transfer failed because of transfer condition'), self::CODE_XFER_DISALLOWED_BY_XFER_COND);
                }

                $this->comapi_log(__METHOD__, 'interstep 6-7' );

                // 7: Check wagering limit (to-subwallets)
                if ($this->utils->isEnabledFeature('responsible_gaming')) {
                    $this->comapi_log(__METHOD__, 'checking wagering limits' );
                    $this->load->library([ 'player_responsible_gaming_library' ]);
                    if ($this->player_responsible_gaming_library->inWageringLimits($player_id, $amount) && $transfer_to != 0) {
                        throw new Exception(lang('Transfer failed, wagering limits is in effect for player'), self::CODE_XFER_DISALLOWED_BY_WAGERING_LIMITS);
                    }
                }

                // 8: Check withdrawal conditions (from-subwallets)
                // if ($this->utils->isEnabledFeature('enabled_auto_check_withdraw_condition')) {
                //     $mesg = null;
                //     $success = $this->withdraw_condition->autoCheckWithdrawConditionAndMoveBigWallet($player_id, $mesg, $transfer_from, 'unfinished_is_false');
                //     $this->comapi_log(__METHOD__, [ 'mesg_from_wd_cond_check' => $mesg ] );
                //     if (!$success) {
                //         throw new Exception(lang('Transfer failed, bonus in subwallet is under withdrawal condition'), self::CODE_XFER_DISALLOWED_BY_WITHDRAW_COND);
                //     }
                // }
            }

            // The main event: run transfer operation
            if (empty($amount)) {
                $transfer_result = $this->_transferAllWallet($player_id, $username, $transfer_to);
            }
            else {
                $transfer_result = $this->utils->transferWallet($player_id, $username, $transfer_from, $transfer_to, $amount);
            }

            // 9: Aftermath
            if (FALSE === $transfer_result || (!isset($transfer_result['success']) || (FALSE === $transfer_result['success']))) {
                throw new Exception(lang('Transfer failed'), self::CODE_XFER_TRANSFER_FAILED);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => $res_mesg ?: lang('Transfer successful'),
                'result'    => $res_data
            ];
            $this->comapi_log(__METHOD__, 'Successful response', $ret);
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__METHOD__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    }

	/**
	 * 4. 存款
	 */
	// public function depositToGame(){
	// 	$api_key = $this->input->post('api_key');
	// 	$username = $this->input->post('username');
	// 	$token = $this->input->post('token');

	// 	// $this->__checkKey($api_key);
	// 	if (!$this->__checkKey($api_key)) { return; }

	// 	$this->load->model(['player_model']);
	// 	$player = $this->player_model->getPlayerByUsername($username);

	// 	if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
	// 		$userId = $this->authentication->getUserId();
	// 		$username = $this->input->post('username');
	// 		$playerId = $this->player_model->getPlayerIdByUsername($username);
	// 		$amount = $this->input->post('amount');
	// 		$subwallet = $this->input->post('subwallet');
	// 		$promo_cms_id = $this->input->post('promo_cms_id');
	// 		$paymentKind = Sale_order::PAYMENT_KIND_DEPOSIT;
	// 		// $status = Sale_order::STATUS_PROCESSING;
	// 		$notes = sprintf("Manual Deposit %s to %s", $this->utils->formatCurrencyNoSym($amount), $username);
	// 		// $player_promo_id = null;
	// 		$player_promo_id = $this->process_player_promo($playerId, $promo_cms_id, $amount, $subwallet, $error);
	// 		$currency = $this->config->item('default_currency');
	// 		$payment_account_id = $this->input->post('account');
	// 		$paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
	// 		$systemId = $paymentAccount->external_system_id;
	// 		$date = $this->input->post('date');
	// 		$reason = $this->input->post('reason');
	// 		$status = $this->input->post('status');
	// 		$show_reason = false;

	// 		$success = false;
	// 		$this->payment_account->startTrans();

	// 		// $locked = $this->lockPlayerBalance($playerId);
	// 		$lockedKey=null;
	// 		$locked = $this->lockPlayerBalanceResource($playerId, $lockedKey);
	// 		try {
	// 			if ($locked) {

	// 				$saleOrderId = $this->sale_order->createSaleOrder($systemId, $playerId, $amount, $paymentKind,
	// 					Sale_order::STATUS_PROCESSING, $notes, $player_promo_id, $currency, $payment_account_id, $date, null,
	// 					$subwallet);
	// 				$success = !empty($saleOrderId);

	// 				if (empty($status)) {
	// 					$status == Sale_order::STATUS_PROCESSING;
	// 				}

	// 				if ($status == Sale_order::STATUS_PROCESSING) {
	// 					//pending
	// 					//update note
	// 					if (!empty($reason)) {
	// 						$this->sale_order->appendReason($saleOrderId, $reason);
	// 					}
	// 				} else if ($status == Sale_order::STATUS_SETTLED) {
	// 					$this->sale_order->approveSaleOrder($saleOrderId, $reason, $show_reason);
	// 					//transfer to subwallet
	// 					$transfer_to = $subwallet;
	// 					if ($this->utils->existsSubWallet($transfer_to)) {
	// 						$transfer_from = Wallet_model::MAIN_WALLET_ID;
	// 						$rlt = $this->utils->transferWallet($playerId, $username, $transfer_from, $transfer_to, $amount, $userId);

	// 						$this->utils->debug_log('transfer to subwallet failed', $playerId);
	// 					}

	// 				}

	// 			}
	// 		} finally {
	// 			// release it
	// 			// $rlt = $this->releasePlayerBalance($playerId);
	// 			$rlt = $this->releasePlayerBalanceResource($playerId, $lockedKey);
	// 			// $this->utils->debug_log('releasePlayerBalance', $player_id, $rlt);
	// 		}

	// 		$success = $this->payment_account->endTransWithSucc() && $success;
	// 		if ($success) {
	// 			$this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('new_deposit.success.add'));
	// 		} else {
	// 			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.db.message'));
	// 		}

	// 		if ($status == Sale_order::STATUS_PROCESSING) {
	// 			redirect('home/nav/deposit_all');
	// 		} else {
	// 			redirect('home/nav/deposit_approved_without_date');
	// 		}
	// 	}
	// }

	/**
	 * 5. 游戏链接
	 */
	// public function getGameUrls(){
 //        $api_key = $this->input->post('api_key');
 //        $username = $this->input->post('username');
 //        $token = $this->input->post('token');
 //        $game = $this->input->post('game');
 //        $limit = $this->input->post('limit');
 //        $offset = $this->input->post('offset');

 //        // $this->__checkKey($api_key);
 //        if (!$this->__checkKey($api_key)) { return; }

 //        $this->load->model(['player_model']);
 //        $player = $this->player_model->getPlayerByUsername($username);

 //        if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
 //            $result = $this->restful_model->api_getPlayerActivePromoDetails($player->playerId, $limit, $offset);

 //            $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Get promotion successfully'), $result);
 //            return;
 //        }else{
 //            $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
 //            return;
 //        }
	// }

	/**
	 * Returns player's reports as in player center
	 * Furbished in OGP-9569, OGP-9815
	 * Ported back from xcyl
	 *
	 * @uses	POST:api_key	string		The api_key, as md5 sum. Required.
	 * @uses	POST:token		string		Effective token for player.
	 * @uses	POST:username	string		Player username.  Required.
	 * @uses	POST:time_from	datetime	Start time of query
	 * @uses	POST:time_to    datetime	End time of query
	 * @uses	POST:trans_type	string		Type of transaction.  Use any one of
	 *      following: cashback, deposit, game, point, promo, transfer, withdrawal
	 * @uses	POST:limit		int			Paging.  Count of records to return.  Defaults to 10.
	 * @uses	POST:offset		int			Paging.  Starting point of records to return.
	 *
	 * @see		models/comapi_reports
	 *
	 * @return	JSON	Standard return object of [ success, code, mesg, result ]
	 *
	 */
	public function getPlayerReports() {
		$api_key 	 = $this->input->post('api_key'		, true);
		$username 	 = $this->input->post('username'	, true);
		$token 		 = $this->input->post('token'		, true);
		$time_start  = $this->input->post('time_from'	, true);
		$time_end 	 = $this->input->post('time_to'		, true);
		$trans_type  = $this->input->post('trans_type'	, true);
		$limit 		 = intval($this->input->post('limit'		, true));
		$offset 	 = intval($this->input->post('offset'		, true));
        $include_declined_promos = intval($this->input->post('include_declined_promos')) != -1;
        $reveal_unknown   = !empty($this->input->post('reveal_unknown'));
        $game_platform_id = intval($this->input->post('game_platform_id' , true));
        $return_type      = intval($this->input->post('return_type' , true));

		if (!$this->__checkKey($api_key)) { return; }

		$this->load->model(['player_model', 'comapi_reports', 'game_description_model']);
		$player = $this->player_model->getPlayerByUsername($username);

		if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

            $limit_max = $this->utils->getConfig('comapi_get_player_reports_query_limit');

			if (empty($limit) || $limit < 0 || $limit > $limit_max) {
				$limit = $limit_max;
			}

			$result = [];

			switch ($trans_type){
				case 'promo':
					$result = $this->comapi_reports->playerActivePromoDetails($player->playerId, $time_start, $time_end, $limit, $offset, $include_declined_promos);
					break;
				case 'transfer':
					$result = $this->comapi_reports->playerTransferRequests($player->playerId, $time_start, $time_end, $limit, $offset);
					break;
				case 'cashback':
					$result = $this->comapi_reports->playerCashbackRequestRecords($player->playerId, $time_start, $time_end, $limit, $offset);
					break;
				// case 'point':
				// 	$result = $this->comapi_reports->api_pointsHistory($player->playerId, $time_start, $time_end, $limit, $offset);
				// 	break;
				case 'game':
                    $game_res_count = $this->comapi_reports->playerGameHistory($player->playerId, $time_start, $time_end, $limit, $offset, $reveal_unknown, $game_platform_id, 'return_count');
					$game_res = $this->comapi_reports->playerGameHistory($player->playerId, $time_start, $time_end, $limit, $offset, $reveal_unknown, $game_platform_id);
					$ret_rows = [];
					if (!empty($game_res)) {
						foreach ($game_res as $grow) {
                            // OGP-16577: fix for game name
                            $game_desc = $this->game_description_model->getGameDetailsByGameCodeAndGamePlatform($grow['game_platform_id'], $grow['game_code']);
							// $game_desc = $this->game_description_model->getGameDescriptionByCode($grow['game_code']);
							$game_locale_name = lang(empty($game_desc) ? null : $game_desc->game_name);

							$row = [
								'date'				=> $grow['end_at'] ,
								'game_platform'		=> $grow['game'] ,
                                'game_platform_id'  => $grow['game_platform_id'] ,
								'game_type'			=> lang($grow['game_type']) ,
								'game_name'			=> $game_locale_name ,
								'real_bet_amount'	=> $grow['real_bet_amount'] ,
								'bet_amount'		=> $grow['bet_amount'] ,
								'result_amount'		=> $grow['result_amount'] ,
								'bet_plus_result'	=> $grow['bet_plus_result_amount'] ,
								'win_amount'		=> $grow['win_amount'] ,
								'loss_amount'		=> $grow['loss_amount'] ,
                                'round_no'          => $grow['round_no'] ,
                                'bet_details'       => $grow['bet_details'] ,
							];
							$ret_rows[] = $row;
						}
					}

                    $result = [ 'row_count_total' => $game_res_count, 'rows' => $ret_rows ];

					break;
				case 'withdrawal':
					$result = $this->comapi_reports->player_withdrawals($player->playerId, $time_start, $time_end, $limit, $offset);
					break;
				case 'deposit2':
					$result = $this->comapi_reports->player_deposits2($player->playerId, $time_start, $time_end, $limit, $offset);
					break;
                case 'deposit':
                default:
                    $result = $this->comapi_reports->player_deposits($player->playerId, $time_start, $time_end, $limit, $offset);
                    break;
			}

            $ret = ($return_type == 0) ? $result['rows'] : $result;

			$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Player report generated successfully'), $ret);

			return;
		}
        else{
            // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
            $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
			return;
		}
	} // End function getPlayerReports()

	/**
	 * 7. 提款接口
     *
     * iframe_module/verifyWithdrawal
	 */
	// public function withdrawFromGame(){
 //        $api_key = $this->input->post('api_key');
 //        $username = $this->input->post('username');
 //        $token = $this->input->post('token');

 //        // $this->__checkKey($api_key);
 //        if (!$this->__checkKey($api_key)) { return; }

 //        $player = $this->player_model->getPlayerByUsername($username);

 //        if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

 //            $this->__login($player);
 //            $this->verifyWithdrawal(true);
 //        }else{
 //            $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
 //            return;
 //        }
	// }

    // public function requestPromo(){

    //     $api_key = $this->input->post('api_key');
    //     $username = $this->input->post('username');
    //     $token = $this->input->post('token');
    //     $promoCmsSettingId = $this->input->post('promoCmsSettingId');
    //     $action = $this->input->post('action');
    //     $preapplication = $this->input->post('preapplication');

    //     if (!$action) $action=0;
    //     if (!$preapplication) $preapplication=null;

    //     // $this->__checkKey($api_key);
    //     if (!$this->__checkKey($api_key)) { return; }

    //     $this->load->model(['player_model', 'restful_model', 'promorules', 'player_promo']);
    //     $player = $this->player_model->getPlayerByUsername($username);

    //     if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

    //         $this->__login($player);
    //         $this->request_promo($promoCmsSettingId, $action, $preapplication, true);

    //     }else{
    //         $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
    //         return;
    //     }

    // }

	/**
	 * DISUSED
	 * @deprecated	Use promo mode of getPlayerReports instead.
	 * 8.2 我的优惠
	 */
	// public function queryPromo(){
 //        $api_key = $this->input->post('api_key');
 //        $username = $this->input->post('username');
 //        $token = $this->input->post('token');
 //        $limit = $this->input->post('limit');
 //        $offset = $this->input->post('offset');

 //        // $this->__checkKey($api_key);
 //        if (!$this->__checkKey($api_key)) { return; }

 //        $this->load->model(['player_model']);
 //        $player = $this->player_model->getPlayerByUsername($username);

 //        if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
 //            // $result = $this->restful_model->api_getPlayerActivePromoDetails($player->playerId, $limit, $offset);

 //            $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Get promotion successfully'), $result);
 //            return;
 //        }else{
 //            $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
 //            return;
 //        }
	// }

    /**
     * Lists all available promotion
     */
    // public function listAllPromos(){
    //     $api_key = $this->input->post('api_key');
    //     $username = $this->input->post('username');
    //     $token = $this->input->post('token');
    //     $limit = $this->input->post('limit');
    //     $offset = $this->input->post('offset');

    //     // $this->__checkKey($api_key);
    //     if (!$this->__checkKey($api_key)) { return; }

    //     $this->load->model(['player_model']);
    //     $player = $this->player_model->getPlayerByUsername($username);

    //     if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
    //         $result = $this->api_getPromotions($player->playerId, $limit, $offset);

    //         $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Get all promotion successfully'), $result);
    //         return;
    //     }else{
    //         $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
    //         return;
    //     }
    // }

	/**
	 * 9. 公告接口
	 *
	 * @api {get} /announcement
	 * @apiName announcement
	 */
	// public function announcement(){
 //        $api_key = $this->input->post('api_key');
	// 	if (!$this->__checkKey($api_key)) { return; }

	// 	// load cms model
	// 	$this->load->model('cms_model');
	// 	// get all announcements
	// 	$announcements = $this->cms_model->getAllNews(null, null, 'date desc');

	// 	$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('All news'), $announcements);
	// }

    /**
     * Returns all announcement items which are not out-of-date from table cmsnews, OGP-13201
     * Not out-of-date:
     *     - is_daterange == 0 , or
     *     - is_daterange == 1 and today within [ start_date, end_date ]
     * @uses    string  POST:api_key        api key given by system
     * @return  JSON    Standard JSON return structure, array of announcement objects in result
     */
    public function announcements() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'cms_model' ]);

            $ann = $this->cms_model->getAllNews(null, null, 'date desc');

            $ann = $this->utils->array_select_fields($ann, [ 'newsId', 'title', 'content', 'start_date', 'end_date', 'name' ]);

            foreach ($ann as & $aitem) {
                $aitem['category'] = $aitem['name'];
                unset($aitem['name']);
            }

            $cats = $this->cms_model->getAllNewsCategoriesSimple();

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Announcements retrieved successfully'),
                'result'    => [ 'cats' => $cats, 'list' => $ann ]
            ];
            $this->comapi_log(__FUNCTION__, 'Successful response', $ret);
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function announcements()

	/**
	 * 10. 站内信接口
	 * Returns message list, or thread contents
	 *
	 * @api		/(api_common_root)/message
	 *
	 * @uses	POST:api_key		string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username		tring	Player username.  Required.
	 * @uses 	POST:token			string	Effective token for player. Required.
	 * @uses	POST:messageType	string  Optional.  Any of [ 'unreadMessage', 'readMessage',
	 *       'allMessage' ].  Defaults to 'allMessage'.
	 * @uses 	POST:limit			int		Optional.  Len limit for message list, defaults to 15.
	 * @uses	POST:offset			int		Optional.  Offset of starting for message list, defaults to 0.
	 * @uses 	POST:messageId		int		Optional.  Returns thread contents of specified messageId if
	 *       present.  Limit and offset also apply.
	 *
	 * @return  JSON	{ success, code, message, result }
	 *                 Result holds the message list or thread contents when successful.
	 *
	 */
	public function message(){
        $api_key = $this->input->post('api_key');
        $username = $this->input->post('username');
        $token = $this->input->post('token');
        $messageType = $this->input->post('messageType');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');
        $messageId = $this->input->post('messageId');

        if (!$this->__checkKey($api_key)) { return; }

        $this->load->model(['player_model', 'internal_message']);
        $player = $this->player_model->getPlayerByUsername($username);

        if (empty($token) || empty($player) || !$this->__isLoggedIn($player->playerId, $token)){
            // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors//
            $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
            return;
        }

        // No messageId - Return message list
        if (empty($messageId)) {
        	// See: (model)	 Internal_message::getMessages()

            $where = null;

            switch($messageType) {
                case "unreadMessage":
                    $where = "c.status = " . Internal_message::STATUS_UNPROCESSED;
                    break;
                case "readMessage":
                    $where = "c.status = " . Internal_message::STATUS_READ;
                    break;
                case "allMessage":
                    break;
            }

            $rows = $this->internal_message->getMessages($player->playerId, $limit, $offset, $where);
            $count = (empty($rows)) ? 0 : count($rows);


            foreach ($rows as & $r) {
                $r['admin_unread'] = $r['player_unread_count'];
                $r['player_unread'] = $r['admin_unread_count'];
                unset($r['admin_unread_count'], $r['player_unread_count']);
            }

            $result = [ 'messages' => $rows, 'count' => $count ];
        	return $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Got message list successfully'), $result);

        }

        // messageId present - Return thread contents
    	$limit = $limit ?: 15;
    	$offset = $offset ?: 0;
    	$result = $this->internal_message->getMessagesHistoryByMessageId($messageId, $limit, $offset, 'desc');
    	return $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('Got thread contents successfully'), $result);
	}

    /**
     * 10.2 新增站内信
     * Write new message, or reply to some existing message
     *
     * @api		/(api_common_root)/addMessage
     *
	 * @uses	POST:api_key	string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username	tring	Player username.  Req
	 * @uses 	POST:token		string	Effective token for player
	 * @uses	POST:subject	string	Subject of the message
	 * @uses	POST:message	string	The message body.
	 * @uses 	POST:messageId	int		Optional.  If present, the new message would be a reply to some
	 *        existing message.
     */
    public function addMessage(){
        $api_key    = trim($this->input->post('api_key' , 1));
        $username   = trim($this->input->post('username', 1));
        $token      = trim($this->input->post('token'  , 1));
        $subject    = trim($this->input->post('subject', 1));
        $message    = trim($this->input->post('message', 1));
        $messageId  = intval($this->input->post('messageId', 1));

        // $this->__checkKey($api_key);
        if (!$this->__checkKey($api_key)) { return; }

        $this->load->model(['player_model']);
        $this->load->library(['player_message_library']);
        $player = $this->player_model->getPlayerByUsername($username);

        if (empty($token) || empty($player) || !$this->__isLoggedIn($player->playerId, $token)){
            // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
            $this->__returnApiResponse(FALSE, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
            return;
        }

        if($this->utils->isEnabledFeature('disabled_player_send_message')){
            return $this->__returnApiResponse(FALSE, self::CODE_NOT_ALLOW_EMPTY_MESSAGE, 'error', lang('mess.20'));
        }

        if (empty($messageId)){
            if(!$this->player_message_library->run_validation(Player_message_library::VALIDATION_TYPE_ADD)){
                return $this->__returnApiResponse(FALSE, self::CODE_NOT_ALLOW_EMPTY_MESSAGE, 'error', lang('mess.16'));
            }

            $result = $this->player_message_library->addMessage($player->playerId, $player->username, $subject, $message);
            if ($result) {
                $message = lang('mess.18');
                $success = TRUE;
                $code = Api_common::CODE_SUCCESS;
            } else {
                $message = lang('error.default.db.message');
                $success = FALSE;
                $code = Api_common::CODE_ADD_MESSAGE_FAILED;
            }
        }else{
            if(!$this->player_message_library->run_validation(Player_message_library::VALIDATION_TYPE_REPLY)){
                return $this->__returnApiResponse(FALSE, self::CODE_NOT_ALLOW_EMPTY_MESSAGE, 'error', lang('mess.16'));
            }

            $result = $this->player_message_library->replyMessage($messageId, $player->username, $message);

            if ($result['status']) {
                $message = lang('mess.18');
                $success = TRUE;
                $code = Api_common::CODE_SUCCESS;
            } else {
                $message = $result['message'];
                $success = FALSE;
                $code = Api_common::CODE_ADD_MESSAGE_FAILED;
            }
        }

        return $this->__returnApiResponse($success, $code, $message, []);
    }

    /**
     * Sets given message status read
     *
     * @uses    string  POST:api_key    api key given by system
     * @uses    string  POST:username   Player username
     * @uses    string  POST:token      Effective token for player
     * @uses    int     POST:messageId
     *
     * @return  JSON    Standard JSON return structure [ success, code, mesg, result ]
     *                       With result = [ promos (array), categories (array) ] when successful
     */
    public function messageSetRead() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }
        $res_mesg = '';

        try {
            $this->load->model([ 'player_model' ]);
            $this->load->library([ 'player_message_library' ]);

            // Read arguments
            $token      = trim($this->input->post('token', true));
            $username   = trim($this->input->post('username', true));
            $messageId  = intval($this->input->post('messageId', 1));

            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'messageId' => $messageId ];
            $this->comapi_log(__METHOD__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
            }

            $int_mesg = $this->player_message_library->getMessageByIdForPlayer($player_id, $messageId);
            if (empty($int_mesg)) {
                throw new Exception(lang('Message ID invalid'), self::CODE_INTMESG_MESSAGE_ID_INVALID);
            }

            // If everything goes alright
            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => lang('Message status set read'),
                'result'    => null
            ];
        }
        catch (Exception $ex) {
            $this->comapi_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function messageSetRead()

    // public function go(){
    //     $api_key = $this->input->post('api_key');
    //     $username = $this->input->post('username');
    //     $token = $this->input->post('token');
    //     $type = $this->input->post('type');

    //     // $this->__checkKey($api_key);
    //     if (!$this->__checkKey($api_key)) { return; }

    //     $player = $this->player_model->getPlayerByUsername($username);

    //     if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){

    //         $this->__login($player);
    //         $target_url = $this->__getUrlByType($type);

    //         if ($target_url){
    //             redirect($target_url);
    //         }else{
    //             redirect($this->utils->getSystemUrl('player'));
    //         }
    //     }else{
    //         $this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'));
    //         return;
    //     }
    // }

    // protected function __getUrlByType($type){
    //     $mapping = [
    //         'game'      =>  '',
    //         'deposit'   =>  $this->utils->getSystemUrl('player') . '/iframe_module/iframe_makeDeposit',
    //     ];

    //     return isset($mapping[$type]) ? $mapping[$type] : '';
    // }

    /**
     * unimplement common function
     */
    protected function __unimplementCommonFunction(){
		$this->__returnUnimplementedResponse();
		return;
	}


	public function manualDeposit() {
        $api_key    = $this->input->post('api_key'  , 1);
        $username   = $this->input->post('username' , 1);
        $token      = $this->input->post('token'    , 1);

        if ($this->__checkKey($api_key)) {
            $depositAmount      = (float) $this->input->post('depositAmount', 1);
            // $this->utils->debug_log('depositAmount : ' . $depositAmount);
            //flag of old or new
            $itemAccount        = $this->input->post('itemAccount', 1);
            //old
            $pa_bankname        = (int) $this->input->post('pa_bankname', 1);
            $playerBankDetailsId = $pa_bankname; //preferred player bank account id
            //new
            $fullName           = $this->input->post('fullName', 1);
            $na_bankname        = (int) $this->input->post('na_bankname', 1);
            $depositAccountNo   = $this->input->post('depositAccountNo', 1);
            $sub_wallet_id      = (int) $this->input->post('sub_wallet_id', 1);
            $group_level_id     = $this->input->post('group_level_id', 1);
            $bankSlipImageName  = $this->input->post('bank_slip', 1);
            $depositDatetime    = $this->input->post('deposit_datetime', 1);
            $depositReferenceNo = $this->input->post('reference_no', 1);
            $rememberMyAccount  = $this->input->post('rememberMyAccount', 1);
            $promo_cms_id       = (int) $this->input->post('promo_cms_id', 1);

            $this->load->model(['player_model']);
            $player = $this->player_model->getPlayerByUsername($username);

            if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
                $playerDetail = $this->player_model->getPlayerDetailsById($player->playerId);

                $fullName = $playerDetail->lastName . $playerDetail->firstName;
                $playerId = $player->playerId;
                $this->form_validation->set_rules('depositAmount', lang('cashier.53'), 'required');
                $this->form_validation->set_rules('pa_bankname', lang('cashier.65'), 'trim|xss_clean');
                $this->form_validation->set_rules('fullName', lang('cashier.88'), 'trim|xss_clean');
                $this->form_validation->set_rules('na_bankname', lang('cashier.67'), 'trim|xss_clean');
                // $this->form_validation->set_rules('depositAccountNo', lang('cashier.69'), 'trim|xss_clean|callback_check_new_deposit_bank_account_number');
                $this->form_validation->set_rules('depositAccountNo', lang('cashier.69'), "trim|xss_clean|callback_comapi_check_despoit_account_no[{$playerId}]");
                $this->form_validation->set_rules('bank_slip', lang('Bank slip'), 'trim|xss_clean');
                $this->form_validation->set_rules('deposit_datetime', lang('Deposit Date time'), 'trim|xss_clean');
                $this->form_validation->set_rules('reference_no', lang('Reference No'), 'trim|xss_clean');
                if ($this->form_validation->run() == false) {
                    $this->__returnApiResponse(false, self::CODE_DEPOSIT_VALIDATION_ERROR, validation_errors());
                    return;
                }
                $this->load->model(['group_level']);
                $depositRule = $this->group_level->getPlayerDepositRule($playerId);
                $minDeposit = $depositRule[0]['minDeposit']; # TODO: REMOVE INDEX 0
                $maxDeposit = $depositRule[0]['maxDeposit']; # TODO: REMOVE INDEX 0

                // Check deposit amount > 0
                if ((float)$depositAmount <= 0) {
                    $this->utils->debug_log(lang('notify.39'));
                    $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_ZERO, lang('notify.39'));
                    return;
                // check against minimum deposit
                } else if ((float)$depositAmount < (float)$minDeposit) {
                    $this->utils->debug_log(lang('notify.40') . " {$minDeposit}!");
                    $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_LESS, lang('notify.40') . " {$minDeposit}!");
                    return;
                // check against single deposit/daily deposit maximum
                } else if ((float)$maxDeposit) {
                    if ((float)$depositAmount > (float)$maxDeposit) {
                        $this->utils->debug_log(lang('notify.74') . " {$maxDeposit}!");
                        $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_DAILY_OVER, lang('notify.74') . " {$maxDeposit}!");
                        return;
                    }
                    $playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($playerId);
                    if (((float)$playerTotalDailyDeposit + (float)$depositAmount) > (float)$maxDeposit) {
                        $this->utils->debug_log(lang('notify.74') . " {$maxDeposit}!");
                        $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_DAILY_OVER, lang('notify.74') . " {$maxDeposit}!");
                        return;
                    }
                }


                $this->load->model(['payment_account','transactions', 'sale_order']);

                $flag = Payment_account::FLAG_MANUAL_ONLINE_PAYMENT;
                $paymentAccount = $this->payment_account->getAvailableAccount($playerId, $flag);

                if (!empty($paymentAccount)) {
                    $payment_account_id = $paymentAccount->payment_account_id;
                    $payment_type = $paymentAccount->payment_type;
                    $payment_branch_name = $paymentAccount->payment_branch_name;
                    $payment_account_name = $paymentAccount->payment_account_name;
                    $payment_account_number = $paymentAccount->payment_account_number;
                    $paymentAccount = $paymentAccount;
                    $payment_account_hide_bank_info = $this->payment_account->isHideBankInfo($payment_type);
                    $payment_account_hide_bank_type = $this->payment_account->isHideBankType($payment_type);
                } else {
                    // $this->__returnApiResponse(false, self::CODE_DEPOSIT_NO_PAYMENT_ID, lang('notify.61'));
                    $this->__returnApiResponse(false, self::CODE_DEPOSIT_NO_PAYMENT_ID, 'Collection account not ready, please try later');
                    return;
                }

                if ($itemAccount == 'old') {
                    if (!empty($playerBankDetailsId)) {
                        $playerBankDetail = $this->player_functions->getBankDetailsById($playerBankDetailsId);
                        if (empty($playerBankDetail) || $playerBankDetail['playerId'] != $playerId) {
                        	$this->__returnApiResponse(false, self::CODE_DEPOSIT_PLAYERBANK_INVALID, 'pa_bankname invalid, must be existing playerBankDetailsId and belongs to deposit player');
                        return;
                        }
                        $fullName = $playerBankDetail['bankAccountFullName'];
                        $depositAccountNo = $playerBankDetail['bankAccountNumber'];
                        $depositTo = $playerBankDetail['bankName'];
                    } else {
                        $this->__returnApiResponse(false, self::CODE_DEPOSIT_BANKDETAILID, 'Deposit BankDetailId can not be null');
                        return;
                    }
                } else {
                	$this->load->model('banktype');
                	$deposit_bank = $this->banktype->getBankTypeById($na_bankname);
                	if (empty($deposit_bank)) {
                		$this->__returnApiResponse(false, self::CODE_DEPOSIT_BANKTYPE_INVALID, 'na_bankname invalid, must be existing bankTypeId');
                        return;
                	}
                    $rememberMyAccount = $rememberMyAccount ? '1' : '0';
                    $data = array(
                        'playerId' => $playerId,
                        'bankTypeId' => $na_bankname,
                        'bankAccountNumber' => $depositAccountNo,
                        'bankAccountFullName' => $fullName,
                        'dwBank' => '0', //0 is deposit
                        'isRemember' => $rememberMyAccount, //1 is default
                        'status' => '0', //0 is active
                    );
                    $playerBankDetailsId = $this->player_functions->addBankDetailsByDeposit($data);
                }

                $dwIp = $this->input->ip_address();
                $geolocation = $this->utils->getGeoplugin($dwIp);

                // $depositPromoId = null;
                // $playerPromoId = null;

                $error = null;
                $player_promo_id = null;

                list($promo_cms_id, $promo_rules_id, $promorule) = $this->comapi_lib->process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error, $sub_wallet_id);
                $this->utils->debug_log(__METHOD__, 'process_promo_rules result', ['promo_cms_id' => $promo_cms_id, 'promo_rules_id' => $promo_rules_id, 'error' => $error, 'sub_wallet_id' => $sub_wallet_id ]);
                $promo_info = [
                    'promo_rules_id'    => $promo_rules_id ,
                    'promo_cms_id'      => $promo_cms_id
                ];

                // $this->load->model(array('sale_order'));
                $defaultCurrency = $this->config->item('default_currency');
                $depositSlipPath = $bankSlipImageName;

                // OGP-6107/6279: Added args beyond $depositReferenceNo to make createDepositOrder() call
                // work; the method was originally developed for 3TBet, and not compatible with live_stable
                // version of Sale_order::createDepositOrder.
                $saleOrder = $this->sale_order->createDepositOrder(
                	Sale_order::PAYMENT_KIND_DEPOSIT,	// paymentKind
                	$payment_account_id,	// payment_account_id
                	$playerId,				// playerId
                	$depositAmount,			// amount
                	$defaultCurrency,		// currency
                	$player_promo_id,		// player_promo_id
                	$dwIp,					// ip
					$geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'], // geo_location
					$playerBankDetailsId,	// playerBankDetailsId,
					null,					// depositTransactionCode
					$bankSlipImageName,		// depositSlipPath
					"API deposit; by {$this->api_common_ident('with_method')}",	// notes
					Sale_order::STATUS_PROCESSING,	// status
					$sub_wallet_id,			// sub_wallet_id
					$group_level_id,		// group_level_id
					$depositDatetime,		// depositDatetime
                	$depositReferenceNo ,	// depositReferenceNo
					null ,					// pendingDepositWalletType
		        	null ,					// depositMethod
		        	false ,					// is_mobile
		        	$this->utils->getNowForMysql() ,	// player_submit_datetime
		        	$promo_info ,			// promo_info
		        	// $fullName ,			// fullName
		        	null , 					// fullName
		        	$depositAccountNo ,		// depositAccountNo
		        	Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED	// playerDepositMethod
            	);


                $this->transferBankslipImage($playerId);
                $this->emptyPlayerTempUploadFolder($playerId);
                $this->saveHttpRequest($playerId, Http_request::TYPE_DEPOSIT);

                if (!$this->sale_order->endTransWithSucc()) {
                    $this->__returnApiResponse(false, self::CODE_DEPOSIT_DEFAULT_ERROR, lang('error.default.message'));
                    return;
                }

                $this->__returnApiResponse(true, self::CODE_SUCCESS, lang('notify.38'),
                	[ 'id' => $saleOrder['secure_id'] ]
            	);
                return;
            }else{
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
                return;
            }
        }
    }

    const TEMPORARY_DEPOSIT_FOLDER_NAME  = 'deposit_temp';
    const TEMP_PLAYER_UPLOAD_FOLDER_NAME = 'temp_player_upload_';
    const UPLOADED_DEPOSIT_SLIPS_FOLDER = 'deposit_slips';

    protected function transferBankslipImage($playerId){
        $upload_dir = $this->utils->getUploadPath();
        $depositTemp = self::TEMPORARY_DEPOSIT_FOLDER_NAME;
        $dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME.$playerId;

        $currentUploadImg = $this->session->userdata('player_deposit_image');

        if (!empty($currentUploadImg)){
        	$tempImgPath = $upload_dir. $depositTemp.'/'.$dirname.'/'.$currentUploadImg ;
            $depositSlipDir = self::UPLOADED_DEPOSIT_SLIPS_FOLDER;
            $this->utils->addSuffixOnMDB($depositSlipDir);
            if (!file_exists($upload_dir.'/'.$depositSlipDir)) {
                mkdir($upload_dir.'/'.$depositSlipDir, 0777, true);
            }

            $this->session->unset_userdata('player_deposit_image');

            if (!copy($tempImgPath,$upload_dir.$depositSlipDir.'/'.$currentUploadImg )){
            /// echo $upload_dir.$depositSlipDir.'/';
                throw new Exception(lang('Image not transferred successfully'));
            }

        }
    }

    protected function emptyPlayerTempUploadFolder($playerId){
        $upload_dir = $this->utils->getUploadPath();
        $depositTemp =self::TEMPORARY_DEPOSIT_FOLDER_NAME;
        $dirname = self::TEMP_PLAYER_UPLOAD_FOLDER_NAME.$playerId;

        foreach (glob($upload_dir.$depositTemp.'/'.$dirname."/*.*") as $filename) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }

        $dir=$upload_dir.$depositTemp.'/'.$dirname;

        if (file_exists($dir)){
            rmdir($dir);
        }

    }

    // public function queryDepositBank0() {
    //     $input = $this->input->post();
    //     $api_key = @$input['api_key'];
    //     unset($input['api_key']);
    //     $username = $input['username'];
    //     $token = $input['token'];
    //     if ($this->__checkKey($api_key, $input)) {
    //         $this->load->model(['player_model', 'playerbankdetails']);
    //         $player = $this->player_model->getPlayerByUsername($username);
    //         if (!empty($token) && !empty($player) && $this->__isLoggedIn($player->playerId, $token)){
    //             // $this->load->library(['language_function']);
    //             // $currentLanguage = $this->language_function->getCurrentLanguage();
    //             $playerBankDetails = $this->playerbankdetails->getDepositBankDetails($player->playerId);
    //             $result = array();
    //             foreach($playerBankDetails as $bankinfo) {
    //                 $BankDetail = $this->playerbankdetails->getBankDetailsById($bankinfo['playerBankDetailsId']);
    //                 // $bankName = $bankinfo['bankName'];
    //                 // if (substr($bankName, 0, strlen('_json:')) == '_json:')
    //                 // {
    //                 //     $bankName = json_decode(substr($bankName, strlen('_json:'), strlen($bankName) - strlen('_json:')), true);
    //                 // }
    //                 // $bankinfo['bankName'] = $bankName[$currentLanguage];
    //                 $bankinfo['bankName'] = lang($bankinfo['bankName']);
    //                 // $bankinfo['bankAccountNumber'] = $this->utils->keepOnlyString($BankDetail['bankAccountNumber'], -5);
    //                 $bankinfo['bankAccountNumber'] = $BankDetail['bankAccountNumber'];
    //                 // unset($bankinfo['bankAccountFullName']);
    //                 $result[] = $bankinfo;
    //             }
    //             if (!empty($result)) {
    //                 $this->__returnApiResponse(true, self::CODE_SUCCESS, 'Successfull get deposit bank details', $result);
    //             } else {
    //                 $this->__returnApiResponse(false, self::CODE_QUERY_DEPOSITBANK_EMPTY, 'Query deposit bank details empty');
    //             }
    //         }else{
    //             // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    //             $this->__returnApiResponse(false, self::CODE_COMMON_INVALID_TOKEN, lang('Invalid token or user not logged in'));
    //             return;
    //         }
    //     }
    // } // End function queryDepositBank0()

    public function queryDepositBank(){
        $api_key    = $this->input->post('api_key'  , 1);
        $username   = $this->input->post('username' , 1);
        $token      = $this->input->post('token'    , 1);

        if (!$this->__checkKey($api_key)) { return; }

        try {
            $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $this->load->model(['player_model', 'playerbankdetails']);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $playerBankDetails = $this->playerbankdetails->getDepositBankDetails($player_id);
            $result = array();

            foreach($playerBankDetails as $bankinfo) {
                $BankDetail = $this->playerbankdetails->getBankDetailsById($bankinfo['playerBankDetailsId']);
                $bankinfo['bankName'] = lang($bankinfo['bankName']);
                $bankinfo['bankAccountNumber'] = $BankDetail['bankAccountNumber'];
                $result[] = $bankinfo;
            }

            if (empty($result)) {
                throw new Exception(lang('Query deposit bank details empty'), self::CODE_QUERY_DEPOSITBANK_EMPTY);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => 'Successfully got deposit bank details',
                'result'    => $result
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            // $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }
    } // End function queryDepositBank()

    public function queryDepositWithdrawalAvailableBank(){
        $api_key    = $this->input->post('api_key'  , 1);
        $username   = $this->input->post('username' , 1);
        $token      = $this->input->post('token'    , 1);
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__METHOD__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $bank_res = $this->player_functions->getAllBankType();

            if (empty($bank_res)) {
                throw new Exception(lang('Query available deposit/withdraw bank details empty'), self::CODE_QUERY_DEPOSITBANK_EMPTY);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => 'Successfully got available deposit/withdraw bank details',
                'result'    => $bank_res
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            // $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }
    }

    // public function manualTransferBalance() {

    // 	$input = $this->input->post();
    // 	$api_key = @$input['api_key'];
    // 	unset($input['api_key']);
    // 	$username = $input['username'];
    // 	$token = $input['token'];
    // 	$depositAmount = $input['depositAmount'] ? $input['depositAmount'] : null;
    // 	$wallet_name = 'Main Wallet';
    // 	$current_timestamp = $this->utils->getNowForMysql();
    // 	$action_name = 'add';

    // 	if ($this->__checkKey($api_key)) {
    // 		$player = $this->player_model->getPlayerByUsername($username);
    // 		$player_id = !empty($player) ? $player->playerId : null;

    // 		if (!empty($token) && !empty($player) && $this->__isLoggedIn($player_id, $token)) {
    // 			$this->load->model(array('transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules', 'wallet_model', 'group_level'));

				// $adjustment_type = Transactions::MANUAL_ADD_BALANCE;

				// $this->startTrans();

    //             $depositRule = $this->group_level->getPlayerDepositRule($player_id);
    //             $minDeposit = $depositRule[0]['minDeposit']; # TODO: REMOVE INDEX 0
    //             $maxDeposit = $depositRule[0]['maxDeposit']; # TODO: REMOVE INDEX 0

    //             if ((float)$depositAmount <= 0) {
    //                 $this->utils->debug_log(lang('notify.39'));
    //                 $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_ZERO, lang('notify.39'),$input);
    //                 return;
    //             } else if ((float)$depositAmount < (float)$minDeposit) {
    //                 $this->utils->debug_log(lang('notify.40') . " {$minDeposit}!");
    //                 $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_LESS, sprintf( lang('notify.40') , $minDeposit )."!",$input);
    //                 return;
    //             } else if ((float)$maxDeposit) {

    //                 if ((float)$depositAmount > (float)$maxDeposit) {
    //                     $this->utils->debug_log(lang('notify.74') . " {$maxDeposit}!");
    //                     $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_DAILY_OVER, sprintf( lang('notify.74') , $maxDeposit)."!",$input);
    //                     return;
    //                 }

    //                 //today
    //                 $playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($player_id);
    //                 if (((float)$playerTotalDailyDeposit + (float)$depositAmount) > (float)$maxDeposit) {
    //                     $this->utils->debug_log(lang('notify.74') . " {$maxDeposit}!");
    //                     $this->__returnApiResponse(false, self::CODE_DEPOSIT_AMOUNT_DAILY_OVER, sprintf( lang('notify.74') , $maxDeposit)."!",$input);
    //                     return;
    //                 }
    //             }

				// $totalBeforeBalance = $this->wallet_model->getTotalBalance($player_id);
				// $this->utils->debug_log('player_id', $player_id, 'totalBeforeBalance', $totalBeforeBalance);

				// // $before_adjustment = $wallet_type ? $this->player_model->getPlayerSubWalletBalance($player_id, $wallet_type) : $this->player_model->getMainWalletBalance($player_id);
				// $before_adjustment = $this->player_model->getMainWalletBalance($player_id);
				// $after_adjustment = $before_adjustment + $amount;
				// $action_name = 'Add';

				// $this->utils->debug_log('player_id', $player_id, 'after_adjustment', $after_adjustment);

				// if ($after_adjustment < 0) {
				// 	$this->__returnApiResponse(false, self::CODE_DEPOSIT_DEFAULT_ERROR, lang('error.default.message'),$input);
    //     			return;
				// }

				// $note = sprintf('%s <b>%s</b> balance to <b>%s</b>\'s <b>%s</b>(<b>%s</b> to <b>%s</b>) by <b>%s</b>, <b>%s</b>',
				// 	$action_name, number_format($amount, 2), $player_name, $wallet_name,
				// 	number_format($before_adjustment, 2), number_format($after_adjustment, 2),
				// 	$username, $deposit_amount_note);

				// $this->utils->debug_log('player_id', $player_id, 'depositNote', $note);

				// /*$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
				// 	$user_id, $player_id, $depositAmount, $before_adjustment, $note, $totalBeforeBalance,
				// 	$promo_category, $show_in_front_end, $reason);*/

				// $transaction = $this->wallet_model->lockAndTransForPlayerBalance($playerId,function()
				// 	use ($adjustment_type,$player_id, $depositAmount, $before_adjustment, $note, $totalBeforeBalance, $promo_category, $show_in_front_end){

				// 	return $this->transactions->createAdjustmentTransaction($adjustment_type,
				// 	$player_id, $player_id, $depositAmount, $before_adjustment, $note, $totalBeforeBalance);
				// });

				// $this->utils->debug_log('player_id', $player_id, 'transaction', $transaction);

				// if (!$transaction) {
				// 	//rollback and quit;
				// 	$this->rollbackTrans();
				// 	$this->__returnApiResponse(false, self::CODE_DEPOSIT_DEFAULT_ERROR, lang('error.default.message'),$input);
    //     			return;
				// } else {
				// 	$this->endTransWithSucc();
				// 	$this->__returnApiResponse(true, self::CODE_SUCCESS, lang('notify.38'),$input);
    //             	return;
				// }

    // 		} else {
    // 			$this->__returnApiResponse(false, self::CODE_INVALID_TOKEN, lang('Invalid token or user not logged in'),$input);
    //             return;
    // 		}
    // 	}

    // }

	public function getRequestIP() {
		$this->__returnApiResponse(true, self::CODE_SUCCESS, $this->_getRequestIp());
	}

    public function getClientIP() {
        $this->__returnApiResponse(true, self::CODE_SUCCESS, $this->_getClientIp());
    }

	//SAMPLE CODE FOR PHP REQUEST API:
	/*public function test(){
		$api_key = md5("api_key");
		$url = "http://player.staging.beteast.t1t.games/api_player_center/registerPlayer";
		print_r($url);

		$postdata = array( "api_key" => $api_key , "username" => "username" , "password" => "1111" , "cpassword" => "1111" , "firstName" => "1111" , "email" => "1111" , "retyped_email" => "1111" , "contact_number" => "1111" , "im_type" => "" , "im_account" => "" , "im_type2" => "" , "im_account2" => "" , "birthdate" => "1970-01-01" , "bankName" => "1111" , "bankAccountNumber" => "1111" , "bankAccountName" => "1111" , "referral_code" => "" , "affiliate_code" => "" , "tracking_code" => "" , "terms" => "true" , "sms_verification_code" => "1111" );

		$postdata = http_build_query($postdata);

		$protocol = explode("/", $_SERVER["SERVER_PROTOCOL"]);
		$protocol = strtolower($protocol[0]);
		$headers = array("Content-Type:application/x-www-form-urlencoded\r\n");
		$opts = array($protocol =>array('method' => 'POST','header' => $headers,'content' => $postdata,'timeout' => 10));
		$context = stream_context_create($opts);

		$result = file_get_contents($url, false, $context);

		echo "<pre>";print_r(json_decode($result,true));
	}*/

	protected function returnApiResponseByArray($ret_ar = []) {
		$success	= isset($ret_ar['success'])	? $ret_ar['success']	: false;
		$code		= isset($ret_ar['code'])	? $ret_ar['code']		: self::CODE_EXECUTION_INCOMPLETE;
		$mesg		= isset($ret_ar['mesg'])	? $ret_ar['mesg']		: 'Execution incomplete';
		$result		= isset($ret_ar['result'])	? $ret_ar['result']		: null;

		$this->__returnApiResponse( $success, $code, lang($mesg), $result );
	}

	/**
	 * Returns referral code for given player, specified by username
	 * OGP-4670
	 *
	 * @uses	string	POST: api_key	api key given by system
	 * @uses 	string	POST: username	player username
	 *
	 * @return	JSON 	array [ success, message, result ]  in JSON
	 */
	public function getPlayerReferralCode() {
		// Default return
		$ret = [];

		try {
	        $api_key = $this->input->post('api_key');
	        $api_key = strtolower($api_key);
	        if (!$this->isValidApiKey($api_key)) {
	        	throw new Exception('Invalid signature', self::CODE_INVALID_SIGNATURE);
	        }

	        $username = $this->input->post('username');
	        if (empty($username)) {
	        	throw new Exception('Player username empty', self::CODE_PLAYER_USERNAME_EMPTY);
	        }

            $player = $this->player_model->getPlayerByUsername($username);
            if (empty($player)) {
            	throw new Exception('Player not found', self::CODE_PLAYER_NOT_FOUND);
            }

            $player = get_object_vars($player);
            if (!isset($player['invitationCode'])) {
            	throw new Exception('Malformed player record', self::CODE_MALFORMED_PLAYER_RECORD);
            }

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Success',
            	'result'	=> $player['invitationCode']
            ];
	    }
	    catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    /**
     * Forcibly update token for current player
     * OGP-5402
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: token		player token, acquired at login time
     *
     * @return	JSON	result would contain new token on success
     */
    public function updatePlayerToken() {
    	$ret = [];

    	$api_key = $this->input->post('api_key');
    	$api_key = strtolower($api_key);
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model(['common_token']);
    		$token = $this->input->post('token');

    		$player_id = $this->common_token->getPlayerIdByToken($token);

    		if (empty($player_id)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    			throw new Exception(lang('Invalid token or user not logged in'), self::CODE_COMMON_INVALID_TOKEN);
    		}

    		$new_token = $this->common_token->createTokenBy($player_id, 'player_id');

    		if (!$new_token) {
    			throw new Exception('Token update failed', self::CODE_TOKEN_UPDATE_FAILED);
    		}

    		// Render old token obsolete
    		$this->common_token->disableToken($token);

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Success',
            	'result'	=> $new_token
            ];
    	}
    	catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    /**
     * Update player's password
     *
     * @uses	string	POST: api_key		api key given by system
     * @uses	string	POST: token			player token, acquired at login time
     * @uses	string	POST: password_old	The old password
     * @uses	string	POST: password		New password
     *
     * @return	JSON	status true if success; otherwise false
     */
    public function updatePlayerPassword() {
    	$ret = [];

    	$api_key = $this->input->post('api_key');
    	$api_key = strtolower($api_key);
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model(['player_model']);
    		$this->load->library(['player_functions']);
    		$password_old = $this->input->post('password_old');
    		$password     = $this->input->post('password');
    		$token        = $this->input->post('token');
            $username     = $this->input->post('username');

    		if (empty($password_old)) {
    			throw new Exception('Old password missing', self::CODE_OLD_PASSWORD_MISSING);
    		}

    		if (empty($password)) {
    			throw new Exception('New password missing', self::CODE_NEW_PASSWORD_MISSING);
    		}

    		// $player_id = $this->common_token->getPlayerIdByToken($token);
    		// if (empty($player_id)) {
    		// 	throw new Exception('Invalid token', self::CODE_INVALID_TOKEN);
    		// }

            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                // OGP-14342
                throw new Exception('Invalid token', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Invalid token', self::CODE_INVALID_TOKEN);
            }

    		// Password validation
    		$this->form_validation->set_rules('password', 'New Password', 'trim|required|min_length[6]|max_length[12]|xss_clean|regex_match[/^[a-z0-9]+$/]');

    		$password_valid_format = $this->form_validation->run();

    		if (!$password_valid_format) {
    			$val_errors = trim(strip_tags(validation_errors()));
    			throw new Exception("Password format invalid: {$val_errors}", self::CODE_PASSWORD_FORMAT_INVALID);
    		}

    		$password_old_valid = $this->player_functions->isValidPassword($player_id, $password_old);

    		if (!$password_old_valid) {
    			throw new Exception("Old password does not match", self::CODE_OLD_PASSWORD_DOES_NOT_MATCH);
    		}

 			$passwd_repeats = $this->player_functions->isValidPassword($player_id, $password);

 			if ($passwd_repeats) {
 				throw new Exception("Can not use exactly the old password", self::CODE_PASSWORD_REPEATS_THE_OLD);
 			}

 			/**
 			 * Post-validation tasks: merged as method
 			 * player_password_module::__resetPasswordAfterValidation()
 			 */
 			$extra_log_info = $this->api_common_ident();

 			// $this->utils->debug_log('api_common', 'Begin exec __resetPassword', 'player_id', $player_id);

 			$rp_res = $this->__resetPasswordAfterValidation($player_id, $password, $extra_log_info, 'is_api_call');

            // $this->comapi_log(__METHOD__, 'passwd-change-results', $rp_res);

 			$this->comapi_log(__METHOD__, 'End exec __resetPassword', 'player_id', $player_id, 'results', $rp_res);

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Success',
            	'result'	=> [ 'game_password_results' => $rp_res['mesg'] ]
            ];
    	}
    	catch (Exception $ex) {
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
            $this->comapi_log(__METHOD__, 'Exception', $ret);
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    /**
     * Echo test
     * @param	string	$api_key	api key given by system
     * @param	string	$mesg    	echo message, optional
     * @return	JSON	input mesg in field 'result' if success
     */
    public function apiEcho($api_key = null, $mesg = null) {
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$mesg = urldecode($mesg);

    		if (empty($mesg)) {
    			$res_mesg = 'Api_key verified correctly.';
    		}
    		else {
    			$res_mesg = 'Echo mesg received.';
    		}

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> $res_mesg ,
	    		'result'	=> [ 'mesg' => $mesg ]
	    	];
    	}
    	catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    /**
     * Echo test by post
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: mesg    	echo message, optional
     * @return	JSON	input mesg in field 'result' if success
     */
    public function apiPostEcho() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$mesg = $this->input->post('message');
    		$mesg = urldecode($mesg);
    		// $std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		// $this->utils->debug_log(__FUNCTION__, 'request', $std_creds);
    		$this->utils->debug_log(__FUNCTION__, ['api_key' => $api_key, 'message' => $mesg ]);

    		if (empty($mesg)) {
    			$res_mesg = 'Api_key verified correctly.';
    		}
    		else {
    			$res_mesg = 'Echo mesg received.';
    		}

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> $res_mesg ,
	    		'result'	=> [ 'mesg' => $mesg , 'POST' => $this->input->post() ]
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    /**
     * Set agent for player
     *
     * @uses	string	POST: api_key			api key given by system
     * @uses	string	POST: agent_username	username of agent
     * @uses	string	POST: player_username	username of player
     *
     * @return	JSON
     */
  	public function setPlayerAgent() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'player_model', 'agency_model' ]);
    		$agent_username = $this->input->post('agent_username');
    		$player_username = $this->input->post('player_username');

    		// Check player username
    		$player_id = $this->player_model->getPlayerIdByUsername($player_username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_PLAYER_USERNAME_INVALID);
    		}

    		// Check agent username
    		$agent = $this->agency_model->get_agent_by_name($agent_username);
    		$agent_id = is_array($agent) && isset($agent['agent_id']) ? $agent['agent_id'] : null;
    		if (empty($agent_id)) {
    			throw new Exception('Agent username invalid', self::CODE_AGENT_USERNAME_INVALID);
    		}

    		// Check if old agent == new agent
    		$player = $this->player_model->getPlayerById($player_id);
    		$agent_id_current = $player->agent_id;

    		if ($agent_id_current == $agent_id) {
    			throw new Exception('Player is already under this agent', self::CODE_PLAYER_ALREADY_UNDER_THIS_AGENT);
    		}

    		// Clear affiliate ID when setting agent ID
    		$this->player_model->setPlayerAgent($player_id, $agent_id, null);

    		// Log player info change event
    		$notes = $this->api_common_ident();
    		$this->player_model->savePlayerUpdateLog($player_id, lang('Set Parent Agent') . " $notes", $notes);

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Successfully set agent for player' ,
	    		'result'	=> [
	    			'player'	=> [ 'id' => $player_id, 'username' => $player_username ] ,
	    			'agent'		=> [ 'id' => $agent_id , 'username' => $agent_username  ]
	    		]
	    	];
    	}
    	catch (Exception $ex) {
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    }

    protected function api_common_ident($with_method = false) {
    	$ident = "api_common|{$this->router->fetch_class()}" . ($with_method ? "|{$this->router->fetch_method()}" : '');
    	return "($ident)";
    }

    /**
     * Use player credentials to register an agent
     *
     * @uses	string	POST: api_key			api key given by system
     * @uses	string	POST: player_username	Player username
     * @uses	string	POST: agent_username	(optional) Agent username.  Defaults to player username
     * @return	JSON
     */
    public function convertPlayerToAgent() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'player_model', 'agency_model' ]);
    		$agent_username = $this->input->post('agent_username');
    		$player_username = $this->input->post('player_username');

    		// Check player username
    		$player_id = $this->player_model->getPlayerIdByUsername($player_username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_PLAYER_USERNAME_INVALID);
    		}

    		$result = $this->agency_model->convertPlayerToAgent($player_id, $agent_username, $this->input->post('parentId'));

    		if(ERROR_AGENT_ALREADY_EXISTS === $result){
                throw new Exception('Agent username already in use', self::CODE_AGENT_USERNAME_IN_USE);
            }

	        if (ERROR_AGENT_REGISTRATION_FAILED === $result) {
	            throw new Exception('Agent registration failure', self::CODE_AGENT_REG_FAILURE);
	        }

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Successfully converted player to agent' ,
	    		'result'	=> [
	    			'agent'		=> [ 'id' => $result['agent_id'], 'username' => $result['agent_id']['agent']['agent_name'] ]
	    		]
	    	];
    	}
		catch (Exception $ex) {
			$this->utils->debug_log('convertPlayerToAgent', $ex->getMessage());
	    	// Catch exception, set in ret and then go to finally block
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

    }

    /**
     * Updates player's profile information.  Only accepts fields in playerdetails
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: username	Player username
     * @uses	string	POST: token		Effective token
     * @uses	json	POST: fields	Update field/value as a json
     *
     * @return	JSON	General JSON return object
     */
    public function updatePlayerProfile() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

    	try {
    		$this->load->model([ 'player_model' ]);

    		// Read arguments
    		$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$fields_json	= $this->input->post('fields');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds, [ 'fields_json' => $fields_json ]);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_UPP_PLAYER_USERNAME_INVALID);
    		}

    		// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_UPP_PLAYER_TOKEN_INVALID);
    		}

    		// Check argument 'fields'
    		$fields = json_decode($fields_json, 'as_array');
    		if (!is_array($fields) || empty($fields)) {
    			throw new Exception('Argument "fields" must be legal non-empty json string', self::CODE_UPP_FIELDS_JSON_ILLEGAL);
    		}

    		// Check for illegal field in fields
    		$legal_columns_raw = $this->player_model->getColumnsForPlayerDetails();
    		$this->utils->debug_log('updatePlayerProfile', 'legal_columns', $legal_columns_raw);
    		$legal_columns = array_flip($legal_columns_raw);


            $fields_clean = [];
            foreach ($fields as $field_raw => $val) {
                $field = preg_replace('/\W/', '_', $field_raw);
                if ($field == 'email') {
                    $this->player_model->updatePlayerEmail($player_id, $val);
                }
                else if (!isset($legal_columns[$field])) {
                    throw new Exception("Field '{$field}' is illegal", self::CODE_UPP_FIELD_ILLEGAL);
                }
                else {
                    $fields_clean[$field] = $val;
                }
            }

            // All set
            if (!empty($fields_clean)) {
                $this->player_model->updatePlayerDetails($player_id, $fields_clean);
            }

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Player profile updated successfully' ,
	    		'result'	=> []
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // function updatePlayerProfile2()

     /**
     * Alternative to updatePlayerProfile; updates player's profile one field at a time
     * OGP-13290
     *
     * @uses    string  POST: api_key   api key given by system
     * @uses    string  POST: username  Player username
     * @uses    string  POST: token     Effective token
     * @uses    string  POST: field     Update field
     * @uses    string  POST: value     Update value
     *
     * @return  JSON    General JSON return object
     */
    public function updatePlayerProfile2() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }
        $res_mesg = '';

        try {
            $this->load->model([ 'player_model' ]);

            // Read arguments
            $token          = trim($this->input->post('token', 1));
            $username       = trim($this->input->post('username', 1));
            $field_raw      = trim($this->input->post('field', 1));
            $value          = trim($this->input->post('value', 1));
            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'field' => $field_raw, 'value' => $value ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_UPP_PLAYER_USERNAME_INVALID);
            }

            // Check player token
            if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_UPP_PLAYER_TOKEN_INVALID);
            }

            // Check for illegal field in fields
            $legal_columns_raw = $this->player_model->getColumnsForPlayerDetails();
            // $this->utils->debug_log('updatePlayerProfile', 'legal_columns', $legal_columns_raw);
            $legal_columns = array_flip($legal_columns_raw);

            $field = preg_replace('/\W/', '_', $field_raw);
            if ($field == 'email') {
                // Email comes first
                $this->player_model->updatePlayerEmail($player_id, $value);
            }
            else if (isset($legal_columns[$field])) {
                // Clear to update
                $this->player_model->updatePlayerDetails($player_id, [ $field => $value ]);
            }
            else {
                // Field illegal
                throw new Exception(sprintf(lang("Field '%s' is illegal"), $field), self::CODE_UPP_FIELD_ILLEGAL);
            }

            $ret = [
                'success'   => true ,
                'code'      => 0 ,
                'mesg'      => 'Player profile updated successfully' ,
                'result'    => []
            ];
        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);
            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function updatePlayerProfile2()

	public function listPlayerDepositAccounts() {
		$this->queryDepositBank();
	}

    /**
     * Lists player's existing withdrawal accounts.
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: username	Player username
     * @uses	string	POST: token		Effective token
     *
     * @return	JSON	General JSON return object, with result = [  withdrawal account details ]
     */
	public function listPlayerWithdrawAccounts() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'player_model', 'playerbankdetails' ]);

    		// Read arguments
    		$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_LPWA_PLAYER_USERNAME_INVALID);
    		}

    		// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
    			// throw new Exception('Token invalid or player not logged in', self::CODE_LPWA_PLAYER_TOKEN_INVALID);
    		}

            $wd_accounts = $this->comapi_lib->player_withdraw_accounts($player_id);

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Successfully got withdraw accounts for player' ,
	    		'result'	=> $wd_accounts
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function listPlayerWithdrawAccounts()

	/**
	 * Manual withdraw endpoint
	 * Demanded by Lanhai, OGP-6509, OGP-7109
	 *
	 * @uses	POST:api_key		string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username		string	Player username.  Required.
	 * @uses 	POST:token			string	Effective token for player. Required.
	 * @uses	POST:amount			decimal	Withdraw amount.
	 * @uses	POST:withdrawal_password	string	Player's withdrawal password.
	 * @uses	POST:bankDetailsId	int		bankDetailsId reported by API method listPlayerWithdrawAccounts.  Specify this item to use player's available account.
	 * @uses	POST:bankTypeId		int		bankTypeId reported by API method queryDepositWithdrawalAvailableBank.  Specify this item to add and use a new account.
	 * @uses	POST:bankAccName	string	New bank account holder name
	 * @uses	POST:bankAccNum		string	New bank account number
	 * @uses	POST:branch			string	New bank account branch
	 * @uses	POST:city			string	New bank account bank city
	 * @uses	POST:province		string	New bank account bank province
	 * @uses	POST:phone			string	New bank account bank phone
	 * @uses	POST:bankAddress	string	New bank account bank address
	 *
	 * @return	JSON	General JSON return object
	 */
	public function manualWithdraw() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
			$this->load->model([ 'playerbankdetails','risk_score_model', 'banktype', 'player_promo', 'transactions', 'users', 'player_model' ]);

			$token				= $this->input->post('token');
    		$username			= $this->input->post('username');
    		$bankDetailsId		= $this->input->post('bankDetailsId');
    		$withdrawal_password = $this->input->post('withdrawal_password');
    		$amount				= $this->input->post('amount');
    		$bankTypeId			= $this->input->post('bankTypeId');
    		$bankAccountNumber	= $this->input->post('bankAccountNumber');
    		$bankAccountFullName= $this->input->post('bankAccountFullName');

    		$std_creds = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$wd_creds = [ 'bankDetailsId' => $bankDetailsId, 'bankTypeId' => $bankTypeId, 'bankAccountNumber' => $bankAccountNumber, 'bankAccountFullName' => $bankAccountFullName, 'withdrawal_password' => $withdrawal_password, 'amount' => $amount ];

    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds, $wd_creds);

    		$playerId = $this->player_model->getPlayerIdByUsername($username);
    		if (empty($playerId)) {
    			throw new Exception('Player username invalid', self::CODE_MW_PLAYER_USERNAME_INVALID);
    		}

    		// Check player token
    		if (!$this->__isLoggedIn($playerId, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_MW_PLAYER_TOKEN_INVALID);
    		}

    		$player = $this->player_model->getPlayerArrayById($playerId);

			$this->utils->debug_log('playerId', $playerId, 'player', $player, 'input post', $this->input->post());

			if ($this->utils->isEnabledFeature("show_allowed_withdrawal_status") && $this->utils->isEnabledFeature("show_risk_score") && $this->utils->isEnabledFeature("show_kyc_status")) {
				if(!$this->risk_score_model->generate_allowed_withdrawal_status($playerId)){
					throw new Exception('Player withdraw is prohibited by KYC checking', self::CODE_MW_ILLEGAL_KYC_STATUS);
				}
			}

			// if(!$this->verifyAndResetDoubleSubmit($playerId)){
			// 	throw new Exception('Double submit detected', self::CODE_MW_DOUBLE_SUBMIT_DETECTED);
			// }

			if ($player['enabled_withdrawal'] == self::WITHDRAWAL_DISABLED) {
				throw new Exception('Player withdrawal is disabled.', self::CODE_MW_PLAYER_WITHDRAWAL_DISABLED);
			}

			// SMS and Password verification is not yet present in new player center. Verify withdraw password only
			// $withdrawVerificationMethod = $this->utils->getConfig('withdraw_verification');
			// if($withdrawVerificationMethod == 'withdrawal_password'){ }

			// OGP-9659: Use sys feature enabled_withdraw_password to control withdrawal password verification
			if ($this->utils->isEnabledFeature('enabled_withdrawal_password') &&
                $this->utils->getConfig('withdraw_verification') == 'withdrawal_password') {
				$checkWithdrawPassword=$this->player_model->validateWithdrawalPassword($playerId, $withdrawal_password);
				if(!$checkWithdrawPassword){
					throw new Exception('Withdrawal password does not match', self::CODE_MW_WRONG_WITHDRAWAL_PASSWORD);
				}
			}

            // OGP-15776: checks for bankDetailsId
            if (!empty($bankDetailsId)) {
                $this->utils->debug_log(__METHOD__, 'player acc check', [ 'playerId' => $playerId ]);
                if (!$this->comapi_lib->is_valid_withdraw_account_for_player($playerId, $bankDetailsId)) {
                    throw new Exception('bankDetailsId invalid', self::CODE_MW_BANKDETAILSID_INVALID);
                }
            }

			// Validate withdraw limits
			// $this->utils->debug_log(__METHOD__, "Withdrawal submitted by:", [ 'player' => $playerId, 'amount' => $amount, 'bankDetailsId' => $bankDetailsId ]);

			// Check whether withdrawal satisfies withdrawal rule for this player
	        $playerWithdrawalRule = $this->utils->getWithdrawMinMax($playerId);
			list($withdrawalCount, $accumulativeAmount) = $this->transactions->count_today_withdraw($playerId);
            if (!$playerWithdrawalRule || is_null($playerWithdrawalRule['max_withdraw_per_transaction'])) {
                throw new Exception("Undefined withdraw rule", self::CODE_MW_UNDEFINED_WITHDRAW_RULE);
            }
			if ($amount > $playerWithdrawalRule['max_withdraw_per_transaction']) {
				throw new Exception("Amount of a single withdrawal cannot exceed {$playerWithdrawalRule['max_withdraw_per_transaction']}", self::CODE_MW_SINGLE_WITHDRAWAL_AMOUNT_MAX_HIT);
			}
			if ($amount + $accumulativeAmount > $playerWithdrawalRule['dailyMaxWithdrawalAmount']) {
				throw new Exception("Amount of withdrawals of a single day cannot exceed {$playerWithdrawalRule['dailyMaxWithdrawalAmount']}", self::CODE_MW_DAILY_WITHDRAWAL_LIMIT_HIT);
			}
			if ($withdrawalCount >= $playerWithdrawalRule['withdraw_times_limit']) {
				throw new Exception("Count of withdrawals of a single day cannot exceed {$playerWithdrawalRule['withdraw_times_limit']}", self::CODE_MW_DAILY_WITHDRAWAL_COUNT_LIMIT_HIT);
			}

			$minWithdraw = $this->operatorglobalsettings->getSettingDoubleValue('min_withdraw');
			if ($amount < $minWithdraw) {
				throw new Exception("Amount of a single withdrawal must be at least {$minWithdraw}", self::CODE_MW_SINGLE_WITHDRAWAL_AMOUNT_MIN_HIT);
			}

			//check  withdrawal conditions
			if ($this->utils->isEnabledFeature('check_withdrawal_conditions')) {

	     		$withdraw_data = $this->withdraw_condition->getPlayerWithdrawalCondition($playerId);
	     		$totalRequiredBet =$this->comapi_lib->multi_array_sum($withdraw_data,'conditionAmount');
	     		$totalPlayerBet =$this->comapi_lib->multi_array_sum($withdraw_data,'currentBet');
	     		$un_finished = $totalRequiredBet - $totalPlayerBet ;
	    		$this->utils->debug_log(__FUNCTION__, 'check_withdrawal_conditions',  [ 'playerId' => $playerId, 'totalRequiredBet' => $totalRequiredBet, 'totalPlayerBet' => $totalPlayerBet, 'un_finished' => $un_finished ]);
	    		//un_finished
				if ($un_finished > 0){
					throw new Exception("Bet amount not met, required: {$totalRequiredBet} to go: {$un_finished}", self::CODE_MW_BET_AMOUNT_NOT_SATISFIED);
				}
			}

			//check  withdrawal conditions -- for each
			if ($this->utils->isEnabledFeature('check_withdrawal_conditions_foreach')) {
				$withdraw_data = $this->withdraw_condition->getPlayerWithdrawalCondition($playerId);
				if(!empty($withdraw_data)) {
					foreach($withdraw_data as $v){
						 $this->utils->debug_log("check_withdrawal_conditions_foreach===>", "player", [$playerId], "conditionAmount", $v['conditionAmount'], "currentBet", $v['currentBet'], "un_finished", $v['unfinished']);
						//un_finished
						if ($v['unfinished'] > 0){
							throw new Exception("Withdrawal condition not met, required: {$v['conditionAmount']} to go: {$v['unfinished']}", self::CODE_MW_CONDITION_AMOUNT_NOT_SATISFIED);
						}
					}
				}
			}

            ##check deposit conditions in withdrawal conditions -- for each
            if($this->utils->isEnabledFeature('check_deposit_conditions_foreach_in_withdrawal_conditions')){
                if(FALSE !== $un_finished_deposit = $this->withdraw_condition->getPlayerUnfinishedDepositConditionForeach($playerId)){
                    //un_finished_deposit
                    throw new Exception("Deposit condition not met", self::CODE_MW_CONDITION_AMOUNT_NOT_SATISFIED);
                }
            }

			// Check whether multiple pending withdraws are allowed
			$this->load->model('group_level');
			if ($this->group_level->isOneWithdrawOnly($playerId)) {
				$this->load->model('player_model');
				if ($this->player_model->countWithdrawByStatusList($playerId) >= 1) {
					throw new Exception("Group rules prohibit multiple withdrawals at a time", self::CODE_MW_GROUP_LIMITS_ONLY_ONE_WITHDRAWAL);
				}
			}

			// All checks done, we can now proceed to submit withdrawal request
			// Save bank info if it's new (no bankDetailsId given)
			if(!$bankDetailsId) {
				$data = array(
					'playerId' => $playerId,
					'bankTypeId' => $this->input->post('bankTypeId'),
					'bankAccountNumber' => $this->input->post('bankAccNum'),
					'bankAccountFullName' => $this->input->post('bankAccName'),
					'bankAddress' => $this->input->post('bankAddress'),
					'province' => $this->input->post('province'),
					'city' => $this->input->post('city'),
					'branch' => $this->input->post('branch'),
					'isRemember' => Playerbankdetails::REMEMBERED,
					'dwBank' => Playerbankdetails::WITHDRAWAL_BANK,
					'status' => Playerbankdetails::STATUS_ACTIVE,
					'phone' => $this->input->post('phone'),
				);

				if(empty($data['bankTypeId'])) {
					throw new Exception('bankTypeId missing', self::CODE_MW_MISSING_BANKTYPEID);
				}
				$this->load->library('player_functions');
				$bankDetailsId = $this->player_functions->addBankDetailsByWithdrawal($data);
			}

			$this->load->model('wallet_model');
			$playerAccount = $this->player_model->getPlayerAccountByPlayerIdOnly($playerId);
			// $beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);
			$dwIp = $this->utils->getIP();
			$geolocation = $this->utils->getGeoplugin($dwIp);

			// Get bank details
			$playerBankDetails = $this->playerbankdetails->getBankList(array('playerBankDetailsId' => $bankDetailsId))[0];
			$bankName = $this->banktype->getBankTypeById($playerBankDetails['bankTypeId'])->bankName;

			//wallet account details
			$walletAccountData = array(
				'playerAccountId' => $playerAccount['playerAccountId'],
				'walletType' => 'Main',
				// 'amount' => $amount,
				'dwMethod' => 1,
				'dwStatus' => 'request',
				'dwDateTime' => $this->utils->getNowForMysql(),
				'transactionType' => 'withdrawal',
				'dwIp' => $dwIp,
				'dwLocation' => $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
				'transactionCode' => $this->wallet_model->getRandomTransactionCode(),
				'status' => '0',
				//after transfer back balance
				// 'before_balance' => $beforeBalance,
				// 'after_balance' => $beforeBalance - $amount,
				'playerId' => $playerId,
				'player_bank_details_id'=>$bankDetailsId,
				'bankAccountFullName' => $playerBankDetails['bankAccountFullName'],
				'bankAccountNumber' => $playerBankDetails['bankAccountNumber'],
				'bankName' => $bankName,
				'bankAddress' => $playerBankDetails['bankAddress'],
				'bankCity' => $playerBankDetails['city'],
				'bankProvince' => $playerBankDetails['province'],
				'bankBranch' => $playerBankDetails['branch'],
			);

			// OGP-3531
			if($this->utils->isEnabledFeature('enable_withdrawal_pending_review')){
				if($this->checkPlayerIfTagIsUnderPendingWithdrawTag($playerId)){
					$walletAccountData['dwStatus'] = Wallet_model::PENDING_REVIEW_STATUS;
				}

				// OGP-3996 // add withdrawal_pending_review to risk score chart and set 1 = true unset if false in System settings->Risk score setting
				if($this->utils->isEnabledFeature('enable_withdrawal_pending_review_in_risk_score') && $this->utils->isEnabledFeature('show_risk_score')) {
					$risk_score_levels = $this->risk_score_model->getRiskScoreInfo(risk_score_model::RC);
					$this->utils->debug_log("risk_score_levels enable_withdrawal_pending_review_in_risk_score", "enabled");
					if(!empty($risk_score_levels)){
						$this->utils->debug_log("risk_score_levels not empty", true);
						if(isset($risk_score_levels['rules'])){
							$this->utils->debug_log("risk_score_levels rules set", true);
							$rules = json_decode($risk_score_levels['rules'],true);
							if(!empty($rules)) {
								$this->utils->debug_log("risk_score_levels rules not empty", true);
								foreach ($rules as $key => $value) {
									if(isset($value['withdrawal_pending_review'])){
										$this->utils->debug_log("risk_score_levels withdrawal_pending_review set", true);
										if($value['withdrawal_pending_review']){
											$this->utils->debug_log("risk_score_levels withdrawal_pending_review enabled", true);
											$player_risk_score_level = $this->risk_score_model->getPlayerCurrentRiskLevel($playerId);
											$this->utils->debug_log("risk_score_levels player_risk_score_level value", $player_risk_score_level);
											$this->utils->debug_log("risk_score_levels risk_score value", $value['risk_score']);
											if($player_risk_score_level == $value['risk_score']){
												$this->utils->debug_log("risk_score_levels withdrawal_pending_review set", true);
												$walletAccountData['dwStatus'] = Wallet_model::PENDING_REVIEW_STATUS;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$this->utils->debug_log("risk_score_levels walletAccountData", $walletAccountData['dwStatus']);

			//transfer back balance by feature
			$rlt= $this->_transferBackToMainWallet($playerId, $player['username']);
			$this->utils->debug_log('after _transferBackToMainWallet, result', $rlt);
			if(!$rlt){
				throw new Exception('Cannot transfer back from sub-wallets', self::CODE_MW_FAILED_XFER_FROM_SUBWALLETS);
			}

            /*  OGP-13007 Deprecate Promo Rule 7. Withdraw Requirement
            //check promotion to get max limit of withdrawal
            $maxLimit=$this->player_promo->getMaxLimitWithdrawalFromAppliedPromoOnWithdrawal($playerId);
            $changedAmount=false;
            if($maxLimit!=null && $maxLimit>0){
                $adminUserId=Users::SUPER_ADMIN_ID;
                $success = $this->lockAndTransForPlayerBalance($playerId, function ()
                    use ($playerId, $maxLimit, $adminUserId, &$changedAmount) {
                    $success=true;
                    //check balance
                    $targetBalance = $this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
                    if($this->utils->compareResultFloat($targetBalance, '>', $maxLimit)){
                        //try to minus balance
                        $minus_balance= $this->utils->roundCurrencyForShow($targetBalance-$maxLimit);
                        $note='minus balance to '.$maxLimit.' for promotion';
                        $tran=$this->transactions->createDecTransaction($playerId, $minus_balance, $adminUserId, $note);
                        $success=!empty($tran);
                        if($success){
                            $changedAmount=true;
                        }
                    }

                    return $success;
                });

                $this->utils->debug_log('check withdrawal max limit', $success);

                if(!$success){
                    throw new Exception('Failed to withdraw limit rule from promotions', self::CODE_MW_FAILED_PROMO_MAX_LIMIT_WD_RULE);
                }
            }
            if($changedAmount){
                //reset back
                $amount = $this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
            }
            */

            $walletAccountData['amount']=$amount;
			$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId); //$this->wallet_model->getTargetTransferBalanceOnMainWallet($playerId, Wallet_model::MAIN_WALLET_ID);
			$walletAccountData['before_balance']=$beforeBalance;
			$walletAccountData['after_balance']=$beforeBalance - $amount;

			$this->load->model(array('wallet_model','walletaccount_timelog'));
			$walletModel = $this->wallet_model;
			$hasSufficientBalance = false;
			$errorMsg = '';
			$success = $this->lockAndTransForPlayerBalance($playerId, function () use ($walletModel, $playerId, $walletAccountData, &$walletAccountId, &$hasSufficientBalance, &$errorMsg) {
				// Check main wallet (id = 0) balance
				$hasSufficientBalance = $this->utils->checkTargetBalance($playerId, 0, $walletAccountData['amount'], $errorMsg);
				if($hasSufficientBalance) {
					$localBankWithdrawalDetails = array(
						'withdrawalAmount' => $walletAccountData['amount'],
						'playerBankDetailsId' => $walletAccountData['player_bank_details_id'],
						'depositDateTime' => $walletAccountData['dwDateTime'],
						'status' => 'active',
					);
					$walletAccountId = $walletModel->newWithdrawal($walletAccountData, $localBankWithdrawalDetails, $playerId);
                    $withdrawalActionLogByPlayer = sprintf("Withdrawal is success processing, status => request");
                    $walletModel->addWalletaccountNotes($walletAccountId, $playerId, $withdrawalActionLogByPlayer, 'request', null, Walletaccount_timelog::PLAYER_USER);
				}

				return !empty($walletAccountId);
			});

			$this->utils->debug_log(__FUNCTION__, "Withdrawal evaluated",  [ 'playerId' => $playerId , 'amount' => $amount , 'hasSufficientBalance' => $hasSufficientBalance, 'walletAccountId' => $walletAccountId, 'errorMsg' => $errorMsg ], $std_creds);

			if(!$hasSufficientBalance) {
				throw new Exception('Insufficient balance', self::CODE_MW_INSUFFICIENT_BALANCE);
			}

			$this->saveHttpRequest($playerId, Http_request::TYPE_WITHDRAWAL);
			if(!$success) {
				throw new Exception('Withdrawal failed', self::CODE_MW_WITHDRAWAL_FAILED);
			}

			$withdrawSuccessMsg = lang($this->config->item('playercenter.withdrawMsg.success') ?: 'notify.58');
			// $this->utils->debug_log('manualWithdraw', 'withdrawSuccessMsg', $withdrawSuccessMsg);
//			if($changedAmount){
//				$withdrawSuccessMsg.=$withdrawSuccessMsg.' '.lang('Changed Automatically Amount because Promotion Limit');
//			}

			// send prompt message when withdrawal request is successful
			if ($this->utils->isEnabledFeature('enable_sms_withdrawal_prompt_action_request')) {

				$this->load->model(['cms_model', 'queue_result']);
				$this->load->library(["lib_queue", "sms/sms_sender"]);

                $dialingCode = $player['dialing_code'];
                $mobileNum = !empty($dialingCode)? $dialingCode.'|'.$player['contactNumber'] : $player['contactNumber'];
				$smsContent = $this->cms_model->getManagerContent(Cms_model::SMS_MSG_WITHDRAWAL_REQUEST);
				$mobileNumIsVeridied = $player['verified_phone'];
				$isUseQueueToSend    = $this->utils->isEnabledFeature('enabled_send_sms_use_queue_server');
				$callerType = Queue_result::CALLER_TYPE_ADMIN;
				$caller = $playerId;
				$state  = null;

				if ($mobileNumIsVeridied && $isUseQueueToSend) {
					$this->lib_queue->addRemoteSMSJob($mobileNum, $smsContent, $callerType, $caller, $state, null);
				} else if ($mobileNumIsVeridied) {
					$this->sms_sender->send($mobileNum, $smsContent);
				}
			}

			$walletAccount = $this->wallet_model->getWalletAccountBy($walletAccountId);
			// $this->utils->debug_log(__FUNCTION__, [ 'walletAccountId' => $walletAccountId , 'walletAccount' => $walletAccount ]);
			$res = [ 'withdraw_code' => $walletAccount->transactionCode ];

			$this->utils->debug_log(__FUNCTION__, "Withdrawal successful",  [ 'mesg' => $withdrawSuccessMsg ], $std_creds, $wd_creds, $res);

			// Successful return
	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> $withdrawSuccessMsg ,
	    		'result'	=> $res
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End funcion manualWithdraw()

	/**
	 * Update player's withdrawal password
	 *
	 * @uses	POST:api_key		string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username		string	Player username.  Required.
	 * @uses 	POST:token			string	Effective token for player.
	 * @uses 	POST:old_password	string	The current withdrawal password
	 * @uses 	POST:new_password	string	The new withdrawal password
	 * @uses 	POST:force_reset	int		1 to forcibly reset (requires no old password); leave empty or skip for the general way (only update if the old password matches)
	 *
	 * @return	JSON	General JSON return object
	 */
	public function updatePlayerWithdrawalPassword() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'player_model' ]);

			$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$old_password	= $this->input->post('old_password');
    		$new_password	= $this->input->post('new_password');
    		$force_reset	= $this->input->post('force_reset');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];

    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds, [ 'old_password' => $old_password, 'new_password' => $new_password, 'force_reset'=> $force_reset ]);

    		$player_id = $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_LG_PLAYER_USERNAME_INVALID);
    		}

   			// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_LG_PLAYER_TOKEN_INVALID);
    		}

    		$current_password_not_empty = !$this->player_model->isPlayerWithdrawalPasswordEmpty($player_id);

    		$not_force_reset = empty($force_reset);

    		// $this->utils->debug_log(__FUNCTION__, 'current_password_not_empty', $current_password_not_empty, 'not_force_reset', $not_force_reset , $std_creds);

    		// Not force reset nor old password empty => check old
    		if ($not_force_reset && $current_password_not_empty) {
	    		// Old password not empty
	    		if (empty($old_password)) {
	    			throw new Exception('Old withdrawal password required', self::CODE_UPWP_OLD_PASSWORD_MISSING);
	    		}

	    		// Old password must match
	    		if (!$this->player_model->validateWithdrawalPassword($player_id, $old_password)) {
	    			throw new Exception('Old withdrawal password does not match', self::CODE_UPWP_OLD_PASSWORD_NOT_MATCH);
	    		}

	    		// New password cannot repeat the old
	    		if ($new_password == $old_password) {
	    			throw new Exception('Do not use exactly the old password again', self::CODE_UPWP_NEW_PASSWORD_REPEATS_OLD);
	    		}
	    	}

    		// New password must not be empty
    		if (empty($new_password)) {
    			throw new Exception('New withdrawal password required', self::CODE_UPWP_NEW_PASSWORD_MISSING);
    		}

    		// All checks passed
    		$update_res = $this->player_model->updateWithdrawalPassword($player_id, $new_password);

    		$this->utils->debug_log(__FUNCTION__, 'updating wd password', [ 'username' => $username, 'force_reset' => !empty($force_reset), 'old_passwd'=> $old_password, 'new_passwd' => $new_password, 'result'=> $update_res ]);

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Player withdrawal password updated successfully' ,
	    		'result'	=> null
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    } // End function updatePlayerWithdrawalPassword()

	/**
	 * Call CT (LO_LOTTERY) login and return token
	 *
	 * @uses	POST:api_key		string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username		string	Player username.  Required.
	 * @uses 	POST:token			string	Effective token for player.
	 *
	 * @return	JSON	General JSON return object with result == {"token":<token>}
	 */
	public function playerLoginGetToken_CT() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'external_system' ]);
			$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		$game_api_id = LD_LOTTERY_API;

    		// Check username validity; game_api will use username to login later
    		$player_id = $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_LTCT_PLAYER_USERNAME_INVALID);
    		}

   			// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
    			throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_LTCT_PLAYER_TOKEN_INVALID);
    		}

			if($this->utils->blockLoginGame($player_id)){
			    throw new Exception('Player is blocked', self::CODE_LTCT_PLAYER_BLOCKED);
			}

			if(!$this->external_system->isGameApiActive($game_api_id)){
			    throw new Exception('Game platform under maintenance', self::CODE_LTCT_GAME_UNDER_MAINTENANCE);
			}

    		$ld_api = $this->utils->loadExternalSystemLibObject($game_api_id);

    		if (empty($ld_api)) {
    			throw new Exception('Cannot load game API', self::CODE_LTCT_CANNOT_LOAD_GAME_API);
    		}

    		$res = $ld_api->login($username);

    		$this->utils->debug_log(__FUNCTION__, 'LD_API login result', $res, $std_creds);

    		$token = $res['token'];

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Token retrieved successfully' ,
	    		'result'	=> [ 'token' => $token ]
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    } // End function playerLoginGetToken_CT()

    /**
	 * Call FINANCE login and return token
	 *
	 * @uses	POST:api_key		string	The api_key, as md5 sum. Required.
	 * @uses 	POST:username		string	Player username.  Required.
	 * @uses 	POST:token			string	Effective token for player.
	 *
	 * @return	JSON	General JSON return object with result == {"token":<token>}
	 */
	public function playerLoginGetToken_FINANCE() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model([ 'external_system' ]);
			$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		$game_api_id = FINANCE_API;

    		// Check username validity; game_api will use username to login later
    		$player_id = $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_LTCT_PLAYER_USERNAME_INVALID);
    		}

   			// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
    			// OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_LTCT_PLAYER_TOKEN_INVALID);
    		}

			if($this->utils->blockLoginGame($player_id)){
			    throw new Exception('Player is blocked', self::CODE_LTCT_PLAYER_BLOCKED);
			}

			if(!$this->external_system->isGameApiActive($game_api_id)){
			    throw new Exception('Game platform under maintenance', self::CODE_LTCT_GAME_UNDER_MAINTENANCE);
			}

    		$ld_api = $this->utils->loadExternalSystemLibObject($game_api_id);

    		if (empty($ld_api)) {
    			throw new Exception('Cannot load game API', self::CODE_LTCT_CANNOT_LOAD_GAME_API);
    		}

    		$res = $ld_api->login($username);

    		$this->utils->debug_log(__FUNCTION__, 'LD_API login result', $res, $std_creds);

    		$token = $res['token'];

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Token retrieved successfully' ,
	    		'result'	=> [ 'token' => $token ]
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username, 'token' => $token]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
    } // End function playerLoginGetToken_CT_FINANCE()

	/**
	 * API endpoint for manual balance adjustment
	 * OGP-6952
	 *
	 * @uses	POST:transaction_type	int	Type of balance adjustment, only
	 *       	7 (manual add balance) and 13 (add cashback) are supported now.
	 *       	Other types are not supported yet.
	 * @uses	POST:api_key	string		The api_key, as md5 sum. Required.
	 * @uses 	POST:username	string		Player username.  Required.
	 * @uses 	POST:token		string		Effective token for player.
	 * @uses 	POST:amount		decimal		Amount to add
	 * @uses 	POST:reason		decimal		Reason of this adjustment
	 *
	 * @return	JSON	General JSON return object
	 */
	public function adjustPlayerBalance() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

		try {
			$this->load->model([ 'transactions', 'wallet_model', 'response_result','player_model' ]);
			// $this->load->library([ 'permissions' ]);
			$transaction_type	= $this->input->post('transaction_type');
			// $token		= $this->input->post('token');
			$username	= $this->input->post('username');
			$amount		= $this->input->post('amount');
			$reason		= $this->input->post('reason');
			$status 	= $this->input->post('status');
			$betTimes	= $this->input->post('betTimes');
			$promoCmsSettingId	= $this->input->post('promo_cms_id') ;
			$gamePlatformId		= $this->input->post('gamePlatformId');
			$deductDeposit 			= $this->input->post('deductDeposit');
			$deposit_amt_condition	= $this->input->post('deposit_amt_condition');
			$manual_subtract_balance_tag_id = $this->input->post('manual_subtract_balance_tag_id');

			$std_creds 	= [ 'api_key' => $api_key, 'username' => $username ];
			$this->utils->debug_log(__FUNCTION__, 'request', $std_creds,
				[ 'transaction_type' => $transaction_type, 'amount' => $amount, 'reason' => $reason ] ,
				[ 'status' => $status, 'betTimes' => $betTimes, 'promoCmsSettingId' => $promoCmsSettingId, 'gamePlatformId' => $gamePlatformId, 'deposit_amt_condition' => $deposit_amt_condition, 'deductDeposit' => $deductDeposit, 'manual_subtract_balance_tag_id' => $manual_subtract_balance_tag_id ]
			);

			if ($this->is_current_method_access_disabled()) {
				throw new Exception('Not found', self::CODE_API_METHOD_NOT_FOUND);
			}

			$player_id = $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_APB_PLAYER_USERNAME_INVALID);
    		}

    		// if (!$this->__isLoggedIn($player_id, $token)) {
    		// 	throw new Exception('Token invalid or player not logged in', self::CODE_APB_PLAYER_TOKEN_INVALID);
    		// }

    		$allowed_transactions = [ 7, 13 ];
    		$transaction_type = intval($transaction_type);
    		if (!in_array($transaction_type, $allowed_transactions)) {
    			throw new Exception('Transaction not supported', self::CODE_APB_TRANSACTION_NOT_SUPPORTED);
    		}

    		$amount = floatval($amount);
    		if (empty($amount) || $amount <= 0) {
    			throw new Exception('Amount invalid', self::CODE_APB_AMOUNT_INVALID);
    		}

    		$reason = trim(strip_tags($reason));
    		if (empty($reason)) {
    			throw new Exception('Must support a reason', self::CODE_APB_REASON_EMPTY);
    		}

			// get parameters from system
			$current_timestamp = $this->utils->getNowForMysql();
			$user_id = 1; // Always as admin

			// $show_in_front_end = $this->input->post('show_in_front_end');
			$show_in_front_end = 1;
			$adjustment_category = null;
			// if($this->utils->isEnabledFeature('enable_adjustment_category')){
			// 	$adjustment_category = $this->input->post('adjustment_category_id');
			// }

			$promo_category	= null;
			$promoRuleId	= null;

			$promoCmsSettingId = intval($promoCmsSettingId);
			if(!empty($promoCmsSettingId)){
				$promoRuleId  = $this->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
		    	if (!empty($promoRuleId)) {
					$promorule = $this->promorules->getPromoRuleRow($promoRuleId);
					$promo_category = $promorule['promoCategory'];
				}
			}

			$extra_success_mesg = '';

			if (!empty($promoCmsSettingId) && empty($promoRuleId)) {
				$extra_success_mesg = "  Promo ID {$promoCmsSettingId} unknown.  Promo skipped.";
			}

			// $this->utils->debug_log(__FUNCTION__, [ 'promoCmsSettingId' => $promoCmsSettingId , 'promoRuleId' => $promoRuleId , 'promo_category' => $promo_category ], $std_creds);

			// $isTransfer = $transaction_type == Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET ||
			// $transaction_type == Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET;
			$isTransfer = false;
			// $make_up_only = $this->input->post('make_up_only');
			$make_up_only = false;

			$this->utils->debug_log(__FUNCTION__, [ 'isTransfer' => $isTransfer, 'transaction_type' => $transaction_type, 'make_up_only' => $make_up_only ]);

			$rlt = $this->_commonBalanceAdjusmentLogic($player_id, $gamePlatformId, $transaction_type, $amount, $user_id, $reason, $promo_category, $show_in_front_end, $promoRuleId, $promoCmsSettingId, $manual_subtract_balance_tag_id, $adjustment_category, $deposit_amt_condition, $status, $betTimes, $deductDeposit);

			if (!isset($rlt['response_result_id']) && $gamePlatformId != '0'){
				$this->utils->error_log(__FUNCTION__, 'lost response_result_id on game platform', $gamePlatformId);
			}

			if (isset($rlt['reason_id']) && !empty($rlt['reason_id'])) {
				$abstractApi=$this->utils->loadAnyGameApiObject();
				$message='API: '.$abstractApi->translateReasonId($rlt['reason_id']);
			}
			else {
				$responseResData = $this->response_result->getResponseResultById(@$rlt['response_result_id']);
				if (!empty($responseResData)) {
					$message = $responseResData->status_text;
				}
			}

			// $success = $this->endTransWithSucc();
			if (!$rlt['success']) {
				if (isset($rlt['message']) && !empty($rlt['message'])) {
					$message = $message ?: $rlt['message'];
				} else {
					$message = $message ?: lang('notify.61');
				}
				throw new Exception("Error in balance adjustment: $message", self::CODE_APB_BALANCE_ADJUSTMENT_ERROR);
			}

			$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> "Player balance adjustment successful.{$extra_success_mesg}" ,
	    		'result'	=> null
	    	];

	    	$this->utils->debug_log(__FUNCTION__, 'Balance adjustment successful', $std_creds);
		}
		catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], ['username' => $username]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }

	} // End function adjustPlayerBalance()

	/**
	 * Balance adjustment common logic; workhorse of adjustPlayerBalance()
	 * Copied from admin/application/controllers/payment_management.php
	 * PROTECTED FUNCTION; NEVER OPEN FOR DIRECT FOREIGN ACCESS
	 *
	 * @param	int		$player_id			== player.playerId
	 * @param	int		$wallet_type		ID of game wallet; == external_system.id
	 * @param	int		$adjustment_type	Or transaction_type.  Currently only
	 *                  7	(MANUAL_ADD_BALANCE) and
	 *                  13	(AUTO_ADD_CASHBACK_TO_BALANCE) are used.
	 * @param	decimal	$amount				Amount of adjustment
	 * @param	int		$user_id			SBE admin user ID
	 * @param	string	$reason				Reason of adjustment
	 * @param	int		$promo_category		promo category ID; == promotype.id
	 * @param	bool	$show_in_front_end	?show in player adjust history report?
	 * @param	int		$promoRuleId		promo rule ID; == promorules.id
	 * @param	int		$promoCmsSettingId	promo CMS ID; == promocmssetting.id
	 * @param	int		$manual_subtract_balance_tag_id		?unknown?
	 * @param	int		$adjustment_category				?unknown?
	 * @param	decimal	$deposit_amt_condition	amount, withdraw condition
	 * @param	int		$status				status of adjustment.
	 * @param	decimal	$betTimes			bet times, withdraw condition
	 * @param	bool	$deductDeposit		flag to deduct deposit, withdraw condition
	 * @return	array 	may contain following fields:
	 *                  success				bool	success status of adjustment
	 *                  message				string	extra message
	 *                  response_result_id	int		ID of API response
	 *                  rason_id			int		reason ID reported by game API
	 *
	 */
	protected function _commonBalanceAdjusmentLogic($player_id, $wallet_type, $adjustment_type,
		$amount, $user_id, $reason, $promo_category = null, $show_in_front_end = null, $promoRuleId = null, $promoCmsSettingId = null, $manual_subtract_balance_tag_id = null,$adjustment_category = null, $deposit_amt_condition = null, $status = null, $betTimes = null, $deductDeposit = null) {
		$this->load->model([ 'transactions', 'external_system', 'player_model', 'users', 'player_promo', 'promorules', 'wallet_model', 'transaction_notes', 'payment' ]);
		// $this->load->library([ 'payment_manager' ]);

		$current_timestamp = $this->utils->getNowForMysql();

		$wallet_name = $wallet_type ? $this->external_system->getNameById($wallet_type) . ' Subwallet' : 'Main Wallet';
		$player_name = $this->player_model->getUsernameById($player_id);
		$user_name = $this->users->selectUsersById($user_id)['username'];

		//set promo category
		$promo_category = null;
		if (!empty($promoRuleId)) {
			$promorule = $this->promorules->getPromoRuleRow($promoRuleId);
			$promo_category = $promorule['promoCategory'];
		}

		$result = array('success' => false);

		switch ($adjustment_type) {

			case Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET:
				$from_id = 0; # main wallet
				$to_id = $wallet_type; # wallet id
			case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
			case Transactions::MANUAL_ADD_BALANCE:
			case Transactions::ADD_BONUS:
				$action_name = 'Add';
				break;

			case Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET:
				$from_id = $wallet_type; # wallet id
				$to_id = 0; # main wallet
			case Transactions::SUBTRACT_BONUS:
			case Transactions::MANUAL_SUBTRACT_BALANCE:
			// case Transactions::SUBTRACT_BONUS: NO LOGIC YET SUCH AS DOES IT REQUIRE WITHDRAWAL CONDITION LIKE THE ADD BONUS
				// $after_adjustment = $before_adjustment - $amount;
				$action_name = 'Subtract';
				// $apiFunction = 'withdrawFromGame';
				// $walletFunction = 'decSubWallet';
				break;

			default:
				return array('success' => false);
				break;
		}


		if (!$deposit_amt_condition) {
			$deposit_amount_note = '';
		} else {
			$deposit_amount_note = 'with deposit condition of ' . $deposit_amt_condition;
		}

		$note = '';

		if ($wallet_type) {

			$result = $this->utils->transferWallet($player_id, $player_name, $from_id, $to_id, $amount, $user_id, null, null, false, $reason);

			if ($result['success']) {

				// $this->payment_manager->addPlayerBalAdjustmentHistory(array(
				$this->payment->addPlayerBalAdjustmentHistory(array(
					'playerId' => $player_id,
					'adjustmentType' => $adjustment_type,
					'walletType' => $wallet_type ?: 0, # 0 - MAIN WALLET
					'amountChanged' => $amount,
					'oldBalance' => 0,
					'newBalance' => 0,
					'reason' => $reason,
					'adjustedOn' => $current_timestamp,
					'adjustedBy' => $user_id,
					'show_flag' => $show_in_front_end == '1',
				));
			}

			$note = 'transfer wallet from ' . $from_id . ' to ' . $to_id . ', player:' . $player_name .
				', wallet_name:' . $wallet_name . ', amount:' . $amount;
		} else {
			//only main wallet
			//lock
			$lock_type = Utils::LOCK_ACTION_BALANCE;
			// $lock_it = $this->lockActionById($player_id, $lock_type);
			$lockedKey=null;
			$lock_it = $this->lockPlayerBalanceResource($player_id, $lockedKey);
			try {
				if ($lock_it) {

					$this->startTrans();

					$totalBeforeBalance = $this->wallet_model->getTotalBalance($player_id);
					$this->utils->debug_log('player_id', $player_id, 'totalBeforeBalance', $totalBeforeBalance);

					// $before_adjustment = $wallet_type ? $this->player_model->getPlayerSubWalletBalance($player_id, $wallet_type) : $this->player_model->getMainWalletBalance($player_id);
					$before_adjustment = $this->player_model->getMainWalletBalance($player_id);
					switch ($adjustment_type) {
					case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add manual cashback';
						break;
					case Transactions::MANUAL_ADD_BALANCE:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add';
						// $apiFunction = 'depositToGame';
						// $walletFunction = 'incSubWallet';
						break;
					case Transactions::ADD_BONUS:
						$after_adjustment = $before_adjustment + $amount;
						$action_name = 'Add';
						//create player promo
						//promoRuleId
						break;
					case Transactions::SUBTRACT_BONUS:
					case Transactions::MANUAL_SUBTRACT_BALANCE:
						# case Transactions::SUBTRACT_BONUS: NO LOGIC YET SUCH AS DOES IT REQUIRE WITHDRAWAL CONDITION LIKE THE ADD BONUS
						$after_adjustment = $before_adjustment - $amount;
						$action_name = 'Subtract';
						// $apiFunction = 'withdrawFromGame';
						// $walletFunction = 'decSubWallet';
						break;
					}

					if ($after_adjustment < 0) {
						return array('success' => false);
					}

					$note = sprintf('%s <b>%s</b> balance to <b>%s</b>\'s <b>%s</b>(<b>%s</b> to <b>%s</b>) by <b>%s</b>, <b>%s</b> (API operation, by customer_api/%s::%s() )',
						$action_name, number_format($amount, 2), $player_name, $wallet_name,
						number_format($before_adjustment, 2), number_format($after_adjustment, 2),
						$user_name, $deposit_amount_note, ucfirst($this->router->class), $this->router->method);
					$note_reason = sprintf('<i>Reason:</i> %s <br>',$reason);
					$note = (trim($reason) != '')  ? ($note_reason . sprintf('<i>Normal Note:</i> %s <br>',$note)) : $note;
					// $status = $this->input->post('status');
					// $betTimes = $this->input->post('betTimes');


					#if want pending, don't create transaction, only create player promo
					if($adjustment_type == Transactions::ADD_BONUS && $status == Player_promo::TRANS_STATUS_REQUEST ){

		             	// request promo
						$this->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $user_id, null, $deposit_amt_condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );

						 $result['success'] = $this->endTransWithSucc();
						 return $result;
					}


					$transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
						$user_id, $player_id, $amount, $before_adjustment, $note, $totalBeforeBalance,
						$promo_category, $show_in_front_end, $reason,null,$adjustment_category);


					if (!$transaction) {
						//rollback and quit;
						$this->rollbackTrans();
						return array('success' => false);
					}

					// $this->payment_manager->addPlayerBalAdjustmentHistory(array(
					$this->payment->addPlayerBalAdjustmentHistory(array(
						'playerId' => $transaction['to_id'],
						'adjustmentType' => $transaction['transaction_type'],
						'walletType' => 0, # 0 - MAIN WALLET
						'amountChanged' => $transaction['amount'],
						'oldBalance' => $transaction['before_balance'],
						'newBalance' => $transaction['after_balance'],
						'reason' => $reason,
						'adjustedOn' => $transaction['created_at'],
						'adjustedBy' => $transaction['from_id'],
						'show_flag' => $show_in_front_end == '1',
					));

					if ($adjustment_type == Transactions::ADD_BONUS) {
						// $deductDeposit = $this->input->post('deductDeposit');
						// $deposit_amt_condition = $this->input->post('depositAmtCondition') ? $this->input->post('depositAmtCondition') : null;


						if ($deductDeposit) {
							$condition = (($amount + $deposit_amt_condition) * $betTimes) - $deposit_amt_condition;
						} else {
							$condition = ($amount + $deposit_amt_condition) * $betTimes;
						}

						$promorulesId = empty($promoRuleId) ? $this->promorules->getSystemManualPromoRuleId() : $promoRuleId;

						// $this->payment_manager->savePlayerWithdrawalCondition([
						$this->payment->savePlayerWithdrawalCondition([
							'source_id' => $transaction['id'],
							'source_type' => 4, # manual
							'started_at' => $current_timestamp,
							'condition_amount' => $condition,
							'status' => 1, # enabled
							'player_id' => $player_id,
							'promotion_id' => $promorulesId,
							'bet_times' => $betTimes,
							'bonus_amount' => $amount,
							'deposit_amount' => $deposit_amt_condition,
						]);

						//save to player
						if (!empty($promorulesId)) {
							//load from
							$promorules = $this->promorules->getPromoruleById($promorulesId);
							$promo_category = $promorules['promoCategory'];
						}
						$promoCmsSettingId = $this->promorules->getSystemManualPromoCMSId();
						$playerBonusAmount = $amount;
						$player_promo_id = $this->player_promo->approvePromoToPlayer($player_id, $promorulesId, $playerBonusAmount,
							$promoCmsSettingId, $user_id);
						//update player promo id of transaction
						$this->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);
					}

					if ($adjustment_type == Transactions::MANUAL_SUBTRACT_BALANCE && !empty($manual_subtract_balance_tag_id)) {
						$tags = explode(',', $manual_subtract_balance_tag_id);
						foreach($tags as $tag) {
							$data = Array(
								'rtn_id' => $transaction['id'],
								'msbt_id' => $tag,
								'created_at' => $transaction['created_at'],
								'updated_at' => $transaction['created_at']
							);
							// $this->player_manager->insertTransactionsTag($data);
							$this->player_model->insertTransactionsTag($data);
						}
					}

					if (!empty($transaction['id']) && $adjustment_type == Transactions::AUTO_ADD_CASHBACK_TO_BALANCE) {
						$this->transaction_notes->add($reason, $user_id, $adjustment_type, $transaction['id']);
					}

					$result['success'] = $this->endTransWithSucc();
				}
			} finally {
				// release it
				// $rlt = $this->releaseActionById($player_id, $lock_type);
				$rlt = $this->releasePlayerBalanceResource($player_id, $lockedKey);
				// $rlt = $this->player_model->transReleaseLock($trans_key);
				// $this->debug_log('release change balance', $player_id, $lock_type, $rlt);
			}

		}

		if (!$result['success']) {
			$note = $note . ' failed';
		}
		$this->saveAction("customer_api/{$this->router->class}", 'Adjust Balance', $note);

		return $result;
	} // End function _commonBalanceAdjusmentLogic


	/**
	 * Game report end point, like SBE Game Reports
	 * (code was 'borrowed' from that feature)
	 * OGP-6953
	 *
	 * @uses 	datetime	POST:date_from		start date
	 * @uses 	datetime	POST:date_to		end date
	 * @uses 	int			POST:timezone		timezone, -12 .. 0 .. +12
	 * @uses 	string		POST:group_by		group_by option, any of
	 *        'game_platform_id', 'game_type_id', 'game_description_id',
	 *        'player_id', 'aff_id', 'agent_id', 'game_type_and_player',
	 *        'game_platform_and_player', 'game_description_and_player'
	 * @uses 	string		POST:username			player username
	 * @uses	decimal		POST:total_bet_min		min for total bet
	 * @uses	decimal		POST:total_bet_max		max for total bet
	 * @uses	decimal		POST:total_loss_min		min for total loss
	 * @uses	decimal		POST:total_loss_max		max for total loss
	 * @uses	decimal		POST:total_gain_min		min for total gain
	 * @uses	decimal		POST:total_gain_max		max for total gain
	 * @uses	string		POST:agent_username		agent username
	 * @uses	string		POST:affiliate_username	affiliate username
	 *
	 * @return	JSON	General JSON return format with res = report data.  See documentation for details.
	 */
	function gameReport() {
    	$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }

    	try {
    		$this->load->model('report_model');

    		$datetime_from	= $this->input->post('date_from');
    		$datetime_to	= $this->input->post('date_to');
			$timezone		= $this->input->post('timezone');
			$group_by		= $this->input->post('group_by');
			$username		= $this->input->post('username');
			$total_bet_min	= floatval($this->input->post('total_bet_min'));
			$total_bet_max	= floatval($this->input->post('total_bet_max'));
			$total_loss_min	= floatval($this->input->post('total_loss_min'));
			$total_loss_max	= floatval($this->input->post('total_loss_max'));
			$total_gain_min	= floatval($this->input->post('total_gain_min'));
			$total_gain_max	= floatval($this->input->post('total_gain_max'));
			$agent_username	= $this->input->post('agent_username');
			$affiliate_username		= $this->input->post('affiliate_username');
			$search_unsettled_games	= $this->input->post('search_unsettled_games');
			$include_all_downlines	= $this->input->post('include_all_downlines');
			$sqldebug		= $this->input->post('sqldebug');

			// Default timezone
			if (strval(trim($timezone)) == '') 		{ $timezone = '+8'; }
			// Default datetime
			if (empty($datetime_from))	{ $datetime_from = date('c', strtotime('yesterday')); }
			if (empty($datetime_to)) {
				if (empty($datetime_from)) {
					$datetime_to   = date('c', strtotime('yesterday'));
				}
				else {
					$datetime_to   = $datetime_from;
				}
			}
			// Strip time part from datetime
			$datetime_from	= date('Y-m-d', strtotime($datetime_from));
			$datetime_to	= date('Y-m-d', strtotime($datetime_to));

			$this->utils->debug_log(__FUNCTION__, 'datetime normalized', [ 'datetime_from' => $datetime_from , 'datetime_to' => $datetime_to ]);

			// if (!empty($datetime_from) && !empty($datetime_to) && strtotime($datetime_from) == strtotime($datetime_to))	{
			// 	$datetime_to = date('c', strtotime("{$datetime_from} +24 hours"));
			// }

			$search = ['datetime_from' => $datetime_from , 'datetime_to' => $datetime_to , 'timezone' => $timezone , 'group_by' => $group_by , 'username' => $username , 'total_bet_min' => $total_bet_min , 'total_bet_max' => $total_bet_max , 'total_loss_min' => $total_loss_min , 'total_loss_max' => $total_loss_max , 'total_gain_min' => $total_gain_min , 'total_gain_max' => $total_gain_max , 'agent_username' => $agent_username , 'affiliate_username' => $affiliate_username , 'search_unsettled_games' => $search_unsettled_games , 'include_all_downlines' => $include_all_downlines, 'sqldebug' => $sqldebug ];

    		$this->utils->debug_log(__FUNCTION__, 'request', $search);

    		if ($timezone > 12 || $timezone  < -12) {
				throw new Exception('Invalid value for timezone, please use integer between [ -12, +12 ] or leave it blank.  Default value is +8.', self::CODE_GR_TIMEZONE_INVALID);
			}

			$groupby_options = [ 'game_platform_id', 'game_type_id', 'game_description_id', 'player_id', 'aff_id', 'agent_id', 'game_type_and_player', 'game_platform_and_player', 'game_description_and_player' ];
			$groupby_options_str = implode(', ', $groupby_options);
			if (!empty($group_by) && !in_array($group_by, $groupby_options)) {
				throw new Exception("Invalid value for group_by, please specify any one of following: {$groupby_options_str} , or leave it blank.  Default value is game_platform_id.", self::CODE_GR_GROUPBY_INVALID);
			}


    		$res = $this->report_model->gameReports_api($search);

    		if (empty($sqldebug)) {
    			unset($res['sql']);
			}

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> 'Game report generated' ,
	    		'result'	=> $res
	    	];
    	}
    	catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], [ 'api_key' => $api_key ]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	}

	/**
	 * Affiliate registration
	 * Demanded by Yongji.  OGP-6459.
	 *
	 * @uses 	POST:username			string		Affiliate username
	 * @uses 	POST:password			string		Password
	 * @uses 	POST:password_conf		string		Password confirmation
	 * @uses 	POST:parent_username	string		Username of parent affiliate
	 * @uses 	POST:firstname			string		First Name
	 * @uses 	POST:lastname			string		Last Name
	 * @uses 	POST:birthday			date		Birth Date
	 * @uses 	POST:gender				string		Gender
	 *        										Valid values: Male, Female
	 * @uses 	POST:company			string		Company
	 * @uses 	POST:occupation			string		Occupation
	 * @uses 	POST:email				string		Email address
	 * @uses 	POST:city				string		City
	 * @uses 	POST:address			string		Address
	 * @uses 	POST:zip				string		zip
	 * @uses 	POST:state				string		State
	 * @uses 	POST:country			string		Country
	 * @uses 	POST:mobile				string		Mobile number
	 * @uses 	POST:phone				string		Phone number
	 * @uses 	POST:im1				string		IM1 address
	 * @uses 	POST:imtype1			string		IM1 type
	 * @uses 	POST:im2				string		IM2 address
	 * @uses 	POST:imtype2			string		IM2 type
	 * @uses 	POST:mode_of_contact	string		preferred contact
	 *        										Valid values: mobile,
	 * @uses 	POST:website			string		website address
	 * @uses 	POST:currency			string		Preferred currency
	 * @uses 	POST:language			string		Preferred language
	 * @uses 	POST:lang				int			Language for confirmation mail.
	 *        										1 for english; otherwise Chinese.
	 *
	 * @return	JSON	General JSON return object
	 */
	public function createAffiliate() {
		$api_key = $this->input->post('api_key');
		if ( !$this->__checkKey($api_key) ) { return; }

		try {
			$post_short = $this->comapi_lib->post_short();
			$this->utils->debug_log(__FUNCTION__, 'request', ['post_short' => $post_short]);

			// CI form validation
			$this->comapi_lib->aff_form_rules();
			if ($this->form_validation->run() == false) {
				$message = validation_errors();

				$extra = $this->comapi_lib->aff_form_valid_error_to_array($message);
				throw new Exception("Registration validation error", self::CODE_CA_VALIDATION_ERROR);
			}

			// Extra validation
			list($ex_mesg, $ex_code, $extra) = $this->comapi_lib->aff_reg_extra_checks();
			if ($ex_code != 0) {
				throw new Exception($ex_mesg, $ex_code);
			}


			// (--- START REAL REGISTRATION)
			$affiliate_payout_id = 0;
			$affiliate_id = $this->comapi_lib->aff_add_to_affiliates($affiliate_payout_id);
			if (empty($affiliate_id)) {
				throw new Exception('Affiliate registration failed', self::CODE_CA_AFF_REG_FAILED);
			}
			// (--- END REAL REGISTRATION)


			$aff_details = $this->affiliate->getAffiliateById($affiliate_id);

            #sending email
            $mail_res = '';
            if(!empty($aff_details['email'])){
                #sending email
                $this->load->library(['email_manager']);
                $template = $this->email_manager->template('affiliate', 'affiliate_registered_success', array('affiliate_id' => $affiliate_id));

                $template_enabled = $template->getIsEnableByTemplateName(true);
                if ($template_enabled['enable']) {
                    $email = $aff_details['email'];
                    $email_token = $template->sendingEmail($email, Queue_result::CALLER_TYPE_AFFILIATE, $affiliate_id);
                    $mail_res = ' Confirmation mail sent.';
                    $this->utils->debug_log(__FUNCTION__, 'send aff confirm email', [ 'email' => $aff_details['email'], 'token' => $email_token ]);
                }
            }

			// Prepare new aff details to return
			$act_message = $this->comapi_lib->aff_reg_activation_message();
			$selected_details = $this->utils->array_select_fields($aff_details, [ 'affiliateId', 'parentId', 'username', 'trackingCode', 'email' ]);
			$parent = $this->affiliatemodel->getUsernameById($selected_details['parentId']);
			$selected_details['parent'] = $parent;
			unset($selected_details['parentId']);

			// Return
			$res = [
				'activation_message' => $act_message ,
				'reg_results' => $selected_details
			];

			$mesg = "Affiliate user created successfully.{$mail_res}";
			$this->utils->debug_log(__FUNCTION__, 'Success', [ 'mesg' => $mesg , 'api_key' => $api_key , ['post_short' => $post_short] ]);

	    	$ret = [
	    		'success'	=> true ,
	    		'code'		=> 0 ,
	    		'mesg'		=> $mesg ,
	    		'result'	=> $res
	    	];
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], [ 'api_key' => $api_key , 'extra' => isset($extra) ? $extra : null ], ['post_short' => $post_short]);
	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> isset($extra) ? $extra : null
	    	];
		}
		finally {
			$this->returnApiResponseByArray($ret);
		}
	} // End function createAffiliate

	/**
     * Retrieves player's profile information.  Only returns an abridged set of profile fields.
     * OGP-7625.
     *
     * @uses	string	POST: api_key	api key given by system
     * @uses	string	POST: username	Player username
     *
     * @return	JSON	General JSON return object with profile object in result field
     *                  result: { address, city, contactNumber, email, firstName,
     *                  	gender, imAccount, imAccount2, language, lastName }
     */
	public function getPlayerProfile() {
		$api_key = $this->input->post('api_key');
    	if (!$this->__checkKey($api_key)) { return; }
    	$res_mesg = '';

		try {
			$this->load->model([ 'player_model' ]);

    		// Read arguments
    		$token			= $this->input->post('token');
    		$username		= $this->input->post('username');
    		$std_creds 		= [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
    		$this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

    		// Check player username
    		$player_id	= $this->player_model->getPlayerIdByUsername($username);
    		if (empty($player_id)) {
    			throw new Exception('Player username invalid', self::CODE_GPP_PLAYER_USERNAME_INVALID);
    		}

    		// Check player token
    		if (!$this->__isLoggedIn($player_id, $token)) {
                // OGP-14098: Use CODE_COMMON_INVALID_TOKEN for all token errors
                throw new Exception('Token invalid or player not logged in', self::CODE_COMMON_INVALID_TOKEN);
                // throw new Exception('Token invalid or player not logged in', self::CODE_GPP_PLAYER_TOKEN_INVALID);
    		}

    		// Retrieve fields from playerdetails
    		$res_details = $this->player_model->getAllPlayerDetailsById($player_id);
    		$abridged_fields = [ 'contactNumber' , 'firstName' , 'lastName', 'gender', 'language', 'birthdate', 'imAccount', 'imAccount2', 'city', 'address' ];
    		$res_details = $this->utils->array_select_fields($res_details, $abridged_fields);

    		// Retrieve player
    		$player = $this->player_model->getPlayerById($player_id);

    		$res = $res_details;
    		$res['email'] = $player->email;
    		$res['username'] = $player->username;
    		$res['withdraw_password_status'] = !empty($player->withdraw_password);
    		// OGP-9733: Add player_id in return.  Also port OGP-9139 changes from xcyl (vip_group/vip_level).
    		$res['vip_group'] = lang($player->groupName);
			$res['vip_level'] = lang($player->levelName);
			// $res['vip_level_display'] = "{$player->groupName} - {$player->levelName}";
            $res['vip_level_display'] = "{$res['vip_group']} - {$res['vip_level']}";
    		$res['player_id'] = $player_id;
    		// OGP-10047: player referral code
            $res['referral_code'] = $player->invitationCode;
			// OGP-16961: show player's affiliate
			$res['affiliate'] = $this->player_model->getAffiliateOfPlayer($player_id);
			$res['agent'] = $this->player_model->getAgentNameByPlayerId($player_id);

    		ksort($res);

            // If everything goes alright
            $ret = [
            	'success'	=> true ,
            	'code'		=> self::CODE_SUCCESS ,
            	'mesg'		=> 'Player profile retrieved successfully',
            	'result'	=> $res
            ];
	    }
	    catch (Exception $ex) {
	    	$this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $std_creds);

	    	$ret = [
	    		'success'	=> false,
	    		'code'		=> $ex->getCode(),
	    		'mesg'		=> $ex->getMessage(),
	    		'result'	=> null
	    	];
	    }
	    finally {
	    	$this->returnApiResponseByArray($ret);
	    }
	} // End function getPlayerProfile

	/**
	 * Wrapper to access player_auth_module::sendEmail()
	 * for classes extending Api_common (T1t_ac_tmpl, access points of each client)
	 *
	 * @var		int		$player_id		== player.playerId
	 * @var 	int		$playerPromoId	== playerpromo.playerpromoId
	 *
	 * @return	string	email token
	 */
	protected function sendEmail_wrapper($player_id, $playerPromoId = '') {
		return $this->sendEmail($player_id, $playerPromoId);
	}

	/**
	 * Callback function for CI validation library, verifying deposit account no for manualDeposit()
	 * Workaround for OGP-9059 manualDeposit issue
	 *
	 * @param	string	$bank_acc_no	Bank account number
	 * @param	int		$player_id		== player.playerId
	 * @see	player_deposit_module::check_new_deposit_bank_account_number()
	 * @see	player_bank_module::common_validate_bank_account_number()
	 *
	 * @return	bool	true if valid, false otherwise
	 */
	public function callback_comapi_check_despoit_account_no($bank_acc_no, $player_id) {
		$bank_details_id = null;
        $this->load->model(['playerbankdetails']);
        $bank_type = Playerbankdetails::DEPOSIT_BANK;

     	if (empty($playerId)) {
			$this->form_validation->set_message($fldname, lang('Please login again'));
			return false;
		}

		if (empty($bank_account_number)) {
			return true;
		}

		if ($bank_type === null) {
			$this->utils->error_log('bank type is null', $account_type, $referer);
			return true;
		}

		$success = $this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
			$bank_type, $bank_details_id);


		if (!$success) {
			$this->form_validation->set_message($fldname, lang('Bank Account Number already exist'));
		}

		return $success;
	}


    /**
     * Returns available payment methods in categories
     * like the deposit_category page (/player_center2/deposit/deposit_category)
     * OGP-13250
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     *
     * @see     /player_center2/deposit/deposit_category
     * @return  JSON
     */
    public function depositPaymentCategories() {
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'player_model' ]);

            // Read arguments
            $token          = $this->input->post('token', true);
            $username       = $this->input->post('username', true);
            $is_mobile      = !empty($this->input->post('is_mobile'));

            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $paycat_res = $this->comapi_lib->depcat_deposit_paycats($player_id, $is_mobile);

            // If everything goes alright
            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => lang('Payment categories fetched successfully'),
                'result'    => [ 'payment_cats' => $paycat_res ]
            ];

            $this->comapi_log(__FUNCTION__, 'Successful return', $ret);
        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage() ,
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function depositPaymentCategories()



    /**
     * Sends password recovery mail, OGP-13033
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:email          Player email
     *
     * @error   self::CODE_COMMON_INVALID_USERNAME
     * @error   self::CODE_PREC_MAIL_NOT_MATCH
     * @error   self::CODE_PREC_ERROR_SENDING_MAIL
     * @error   self::CODE_PREC_MAIL_TOO_FREQUENT
     * @error   self::CODE_PREC_MAIL_TEMPLATE_DISABLED
     *
     * @return  JSON
     */
    public function passwordRecovMailSend() {
        // Common api_key verification
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'player_model', 'player' ]);

            // Read arguments
            $username = $this->input->post('username', true);
            $email    = $this->input->post('email', true);

            $std_creds      = [ 'api_key' => $api_key, 'username' => $username, 'email' => $email ];
            $this->utils->debug_log(__FUNCTION__, 'request', $std_creds);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
            }

            $email = urldecode($email);
            $player = (array) $this->player->getPlayerByLogin($username);

            if(strcasecmp(trim($email), trim($player['email'])) !== 0) {
                throw new Exception('Email does not match', self::CODE_PREC_MAIL_NOT_MATCH);
            }

            $cooldown = $this->utils->getConfig('password_recovery_cooldown') ?: 0;
            $resetExpireTime = new DateTime($player['resetExpire']);
            $lastCallTime = $resetExpireTime->modify('-1 hour')->getTimestamp();
            $currentTime = time();

            $cooledTime = $currentTime - $lastCallTime;

            if($cooledTime <= $cooldown) {
                $wait = $cooldown - $cooledTime;
                $this->utils->error_log("Last call time [$lastCallTime], Current time [$currentTime], Cooldown of [$cooldown] sec not reached.");
                throw new Exception("Please wait at least ​$wait ​seconds before resending mail", self::CODE_PREC_MAIL_TOO_FREQUENT);
            }

            # Obtain the reset code
            # access player_password_module::generateResetCode()
            $resetCode = $this->generateResetCode($player_id, true);
            $this->utils->debug_log("Reset code: ", $resetCode);

            #sending email
            $this->load->library(['email_manager']);
            $template = $this->email_manager->template('player', 'player_forgot_login_password', array('player_id' => $player_id, 'verify_code' => $resetCode));
            $template_enabled = $template->getIsEnableByTemplateName(true);

            if ($template_enabled['enable']) {
                $email = $this->player->getPlayerById($player['playerId'])['email'];
                $template->sendingEmail($email, Queue_result::CALLER_TYPE_PLAYER, $player['playerId']);
            } else {
                throw new Exception('Mail template disabled by system', self::CODE_PREC_MAIL_TEMPLATE_DISABLED);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => lang('Password recovery mail sent successfully') ,
                'result'    => null
            ];
        }
        catch (Exception $ex) {
            $this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $std_creds);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => lang('Password recovery mail sent failed') ,
                'result'    => [ 'message' => $ex->getMessage() ]
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function passwordRecovMailSend()

    /**
     * Receives verification code in recovery mail and reset password, OGP-13033
     * Wrapper for passwordRecovRecvCommon()
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:verify_code    the verification code
     * @uses    string  POST:password       new password
     * @uses    string  POST:password_conf  confirm new password
     *
     * @error   self::CODE_PREC_VERIFY_CODE_EMPTY
     * @error   self::CODE_PREC_VERIFY_CODE_NOT_MATCH
     * @error   self::CODE_PREC_PASSWORD_NOT_MATCH
     * @error   self::CODE_PREC_PASSWORD_FORMAT_INVALID
     * @error   self::CODE_PREC_ERROR_WHILE_RESET_PASSWORD
     *
     * @return  JSON
     */
    public function passwordRecovMailRecv() {
        return $this->passwordRecovRecvCommon('mail');
    }

    /**
     * Sends password recovery SMS, OGP-13030
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:mobile_number  Player mobile number
     *
     * @error   self::CODE_COMMON_INVALID_USERNAME
     * @error   self::CODE_PREC_MOBILE_NUMBER_NOT_MATCH
     * @error   self::CODE_PREC_ERROR_SENDING_SMS
     *
     * @return  JSON
     */
    public function passwordRecovSmsSend() {
        // Common api_key verification
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'player_model', 'sms_verification' ]);
            $username       = $this->input->post('username', true);
            $mobile_number  = $this->input->post('mobile_number', true);

            $request = [ 'api_key' => $api_key, 'username' => $username, 'mobile_number' => $mobile_number ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
            }

            $mobile_number_stored = $this->player_model->getPlayerContactNumber($player_id);
            if ($mobile_number != $mobile_number_stored) {
                throw new Exception('Mobile number does not match', self::CODE_PREC_MOBILE_NUMBER_NOT_MATCH);
            }

            $this->comapi_log(__METHOD__, 'mobile_number', $mobile_number_stored);

            $send_res = $this->comapi_lib->comapi_send_sms($player_id, $mobile_number_stored, Sms_verification::USAGE_COMAPI_PASSWORD_RECOVERY);

            if ($send_res['code'] != 0) {
                $res = [ 'mesg' => $send_res['mesg'], 'mesg_debug' => $send_res['mesg_debug'] ];
                throw new Exception($send_res['mesg'], $send_res['code']);
            }

            $ret = $send_res;
            $ret['success'] = true;
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => isset($res['mesg_debug']) ? $res['mesg_debug'] : $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }

    } // End function passwordRecovSmsSend()

    /**
     * Receives verification code in recovery SMS and reset password, OGP-13030
     * Wrapper for passwordRecovRecvCommon()
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:verify_code    the verification code
     * @uses    string  POST:password       new password
     * @uses    string  POST:password_conf  confirm new password
     *
     * @error   self::CODE_PREC_VERIFY_CODE_EMPTY
     * @error   self::CODE_PREC_VERIFY_CODE_NOT_MATCH
     * @error   self::CODE_PREC_PASSWORD_NOT_MATCH
     * @error   self::CODE_PREC_PASSWORD_FORMAT_INVALID
     * @error   self::CODE_PREC_ERROR_WHILE_RESET_PASSWORD
     *
     * @return  JSON
     */
    public function passwordRecovSmsRecv() {
        return $this->passwordRecovRecvCommon('sms');
    }

    /**
     * Common recovery code receiver for passwordRecovMailRecv, passwordRecovSmsRecv
     * @see     Api_common::passwordRecovMailRecv()
     * @see     Api_common::passwordRecovSmsRecv()
     * @param   string  $mode   'sms' or 'mail'
     * @return  JSON    Standard return [ success, code, mesg, result ]
     */
    protected function passwordRecovRecvCommon($mode) {
        // Common api_key verification
        $api_key = $this->input->post('api_key');
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'player_model', 'sms_verification' ]);
            $username       = trim($this->input->post('username', true));
            $verify_code    = trim($this->input->post('verify_code', true));
            $password       = trim($this->input->post('password', true));
            $password_conf  = trim($this->input->post('password_conf', true));

            $request = [ 'api_key' => $api_key, 'username' => $username, 'verify_code' => $verify_code ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_COMMON_INVALID_USERNAME);
            }

            if ($mode == 'sms') {
                $mobile_number = $this->player_model->getPlayerContactNumber($player_id);

                // Check verify_code as SMS verification code
                $code_verify_res = $this->sms_verification->validateVerificationCode($player_id, Sms_verification::SESSION_ID_DEFAULT, $mobile_number, $verify_code, Sms_verification::USAGE_COMAPI_PASSWORD_RECOVERY);
            }
            else {
                // Check verify_code as by-mail password reset code
                $code_verify_res = $this->checkResetCode($verify_code, $player_id);
            }

            if (!$code_verify_res) {
                if ($mode == 'sms') {
                    $this->comapi_log(__METHOD__, 'mode', $mode, 'Code verify failed in',  'Sms_verification::validateVerificationCode()');
                }
                else {
                    $this->comapi_log(__METHOD__, 'mode', $mode, 'Code verify failed in',  'player_password_module::checkResetCode()');
                }
                throw new Exception(lang('Recovery code verify failed'), self::CODE_PREC_VERIFY_CODE_NOT_MATCH);
            }

            if ($password != $password_conf) {
                throw new Exception(lang('Password confirmation does not match'), self::CODE_PREC_PASSWORD_NOT_MATCH);
            }

            $min_password_length = $this->utils->getConfig('default_min_size_password');
            $max_password_length = $this->utils->getConfig('default_max_size_password');

            if (strlen($password) < $min_password_length || strlen($password) > $max_password_length) {
                throw new Exception(lang("Password format invalid, valid length is between [ $min_password_length, $max_password_length ]"), self::CODE_PREC_PASSWORD_FORMAT_INVALID);
            }

            $regex_password = $this->utils->getPasswordReg();

            if (preg_match($regex_password, $password) == false) {
                throw new Exception(lang("Password format invalid, please use only letters and digits"), self::CODE_PREC_PASSWORD_FORMAT_INVALID);
            }

            $des_key = $this->getDeskeyOG();
            $password_reset_res = $this->comapi_lib->reset_password($player_id, $password, $des_key);

            if (!$password_reset_res) {
                throw new Exception(lang("Error while reset password"), self::CODE_PREC_ERROR_WHILE_RESET_PASSWORD);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => 'Password reset successful',
                'result'    => null
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => isset($res['mesg_debug']) ? $res['mesg_debug'] : $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }

    } // End function passwordRecovRecvCommon()

    /**
     * Upload endpoint for player's KYC image attachments, OGP-12843
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    string  POST:id_number      Player ID card number
     * @uses    string  POST:kyc_image      The image file
     *
     * @return  JSON
     */
    public function playerKycImageUpload() {
        // Common api_key verification
        $api_key = $this->input->post('api_key', true);
        $username = $this->input->post('username', true);
        $token = $this->input->post('token', true);
        $id_number = $this->input->post('id_number', true);
        $image = isset($_FILES['kyc_image']) ? $_FILES['kyc_image'] : null;

        if (!$this->__checkKey($api_key)) { return; }

        $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
        $this->comapi_log(__METHOD__, 'request', $request);

        $this->load->model(array('player_model'));
        $playerId = $this->player_model->getPlayerIdByUsername($username);

        //Check player username
        if (empty($playerId)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_COMMON_INVALID_USERNAME,
                'mesg'      => lang('Player username invalid')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        // Check player token
        if (!$this->__isLoggedIn($playerId, $token)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_COMMON_INVALID_TOKEN,
                'mesg'      => lang('Token invalid or player not logged in')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        //Check image not empty
        if(empty($image)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_KIMU_IMAGE_UPLOAD_EMPTY,
                'mesg'      => lang('You didn\'t upload any files.')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        //Check KYC Image
        $this->load->library(['player_security_library', 'player_library']);
        //Verified
        $player_verification = $this->player_security_library->player_verification_info($playerId);
        $verified = $player_verification['verified'];
        if($verified) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_KIMU_IMAGE_VERIFIED,
                'mesg'      => lang('Real Name Verification') . ' ：' . lang('passed')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        //quantity
        $limit_of_upload_attachment = $this->config->item('kyc_limit_of_upload_attachment');
        if(FALSE !== $response = $this->player_security_library->allowUploadAttachment($playerId, BaseModel::Verification_Photo_ID)){
            $ret = [
                'success'   => false,
                'code'      => self::CODE_KIMU_TOO_MANY_IMAGES_ALREADY,
                'mesg'      => sprintf(lang('kyc_attachment.api.upload_file_max_up_to'), $limit_of_upload_attachment)
            ];
            return $this->returnApiResponseByArray($ret);
        }

        if (empty($id_number)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_KIMU_ID_NUMBER_EMPTY,
                'mesg'      => sprintf(lang('gen.error.required'), lang('ID Card Number'))
            ];
            return $this->returnApiResponseByArray($ret);
        }

        if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
            $upload_config = $this->utils->getUploadConfig(null);
            //size
            $allow_filesize = $image['size'][0] < $upload_config['max_size'];
            if(!$allow_filesize){
                $ret = [
                    'success'   => false,
                    'code'      => self::CODE_KIMU_IMAGE_SIZE_INVALID,
                    'mesg'      => sprintf(lang('upload.validation.wrongFileSize'),$upload_config['max_size']/1000000)
                ];
                return $this->returnApiResponseByArray($ret);
            }

            //type
            $ext = explode('.', $image['name'][0]);
            $ext = $ext[count($ext) - 1];
            $ext_allowed = explode("|",$upload_config['allowed_types']);//array("jpg", "jpeg", "gif", "png" , "PNG");
            $in_array = in_array(strtolower($ext), $ext_allowed);
            if(!$in_array){
                $ret = [
                    'success'   => false,
                    'code'      => self::CODE_KIMU_IMAGE_FORMAT_INVALID,
                    'mesg'      => lang('con.aff46')
                ];
                return $this->returnApiResponseByArray($ret);
            }
        }

        //Upload KYC image
        $result = $this->player_security_library->request_upload_realname_verification($playerId, BaseModel::Verification_Photo_ID, $image);
        if(isset($result['status']) && ($result['status'] == 'error')){
            $ret = [
                'success'   => false,
                'code'      => self::CODE_KIMU_UPLOAD_ERROR,
                'mesg'      => $result['msg'],
                'result'    => null
            ];
            return $this->returnApiResponseByArray($ret);
        }

        //Update id number
        $playerdetails['id_card_number'] = $id_number;
        $modifiedFields = $this->player_library->checkModifiedFields($playerId, $playerdetails);
        $this->player_library->editPlayerDetails($playerdetails, $playerId);
        $this->player_library->savePlayerUpdateLog($playerId, lang('lang.edit') . ' ' . lang('lang.playerinfo') . ' (' . $modifiedFields . ')', $this->authentication->getUsername()); // Add log in playerupdatehistory

        // Common return block
        $ret = [
            'success'   => true,
            'code'      => self::CODE_SUCCESS,
            'mesg'      => lang('KYC image uploaded successfully') ,
            'result'    => null
        ];

        $this->comapi_log(__METHOD__, 'response', $result, $ret);

        // Always use this function to return
        $this->returnApiResponseByArray($ret);

    } // End function playerKycImageUpload()

    /**
     * Send player's KYC image attachments list, OGP-12843
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     *
     * @return  JSON
     */
    public function playerKycImageList() {
        // Common api_key verification
        $api_key = $this->input->post('api_key', true);
        $username = $this->input->post('username', true);
        $token = $this->input->post('token', true);

        if (!$this->__checkKey($api_key)) { return; }

        $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
        $this->comapi_log(__METHOD__, 'request', $request);

        $this->load->model(array('player_model'));
        $playerId = $this->player_model->getPlayerIdByUsername($username);

        //Check player username
        if (empty($playerId)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_COMMON_INVALID_USERNAME,
                'mesg'      => lang('Player username invalid')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        // Check player token
        if (!$this->__isLoggedIn($playerId, $token)) {
            $ret = [
                'success'   => false,
                'code'      => self::CODE_COMMON_INVALID_TOKEN,
                'mesg'      => lang('Token invalid or player not logged in')
            ];
            return $this->returnApiResponseByArray($ret);
        }

        // Common return block
        $ret = [
            'success'   => true,
            'code'      => self::CODE_SUCCESS,
            'mesg'      => lang('Send KYC image list successfully') ,
            'result'    => null
        ];

        // (function body here)
        $this->load->library(['player_security_library']);
        $list = $this->player_security_library->get_player_kyc_real_name_image_list($playerId);
        $ret['result']['list'] = $list;
        $ret['result']['id_number'] = null;

        $playerDetails = $this->player_model->getPlayerDetails($playerId);
        if(isset($playerDetails[0]) && !empty($playerDetails[0]['id_card_number'])){
            $ret['result']['id_number'] = $playerDetails[0]['id_card_number'];
        }
        $this->comapi_log(__METHOD__, 'response', $list, $ret);

        // Always use this function to return
        $this->returnApiResponseByArray($ret);

    } // End function playerKycImageList()

    /**
     * Returns attributes of each subwallet: game platform, currency, status
     * Separated from queryPlayerBalance
     * @uses    string  GET/POST:api_key    api key given by system
     * @uses    string  GET/POST:username   Player username
     * @uses    string  GET/POST:token      Effective token for player
     *
     * @uses    comapi_lib::available_subwallet_list()a
     * @uses    comapi_lib::get_mapping_for_subwallets()
     *
     * @return  JSON
     */
    public function playerWalletMappings() {
        $api_key = $this->input->post('api_key', 1);
        if (!$this->__checkKey($api_key)) { return; }
        $res_mesg = '';

        try {
            $this->load->model([ 'player_model', 'wallet_model' ]);

            // Read arguments
            if (empty($token))      { $token    = $this->input->post('token'); }
            if (empty($username))   { $username = $this->input->post('username'); }
            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logchk = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logchk['code'] != 0) {
                throw new Exception($logchk['mesg'], $logchk['code']);
            }

            $subwallet_res = $this->comapi_lib->available_subwallet_list($player_id);

            if ($subwallet_res['success'] == false) {
                throw new Exception($subwallet_res['mesg'], $subwallet_res['code'] + 0x1d7);
            }

            $wallets_mapping = $this->comapi_lib->get_mapping_for_subwallets($subwallet_res['result']['subwallets']);

            $result = $wallets_mapping;

            unset($result['success']);
            unset($result['wallets']);

            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => 'Player wallet mappings query complete' ,
                'result'    => $result
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);

        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];

        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function playerWalletMappings

/**
     * Returns player's balance in all subwallets
     * Separated from queryPlayerBalance
     * queryPlayerBalance = playerWalletMappings + queryPlayerBalance2
     *
     * @uses    string  GET/POST:api_key    api key given by system
     * @uses    string  GET/POST:username   Player username
     * @uses    string  GET/POST:token      Effective token for player
     *
     * @uses    comapi_lib::available_subwallet_list()
     *
     * @return  JSON
     */
    public function queryPlayerBalance2() {
        $api_key = $this->input->post('api_key', 1);
        if (!$this->__checkKey($api_key)) { return; }
        $res_mesg = '';

        try {
            $this->load->model([ 'player_model', 'wallet_model' ]);

            // Read arguments
            if (empty($token))      { $token    = $this->input->post('token'); }
            if (empty($username))   { $username = $this->input->post('username'); }
            $request      = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__FUNCTION__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logchk = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logchk['code'] != 0) {
                throw new Exception($logchk['mesg'], $logchk['code']);
            }

            $subwallet_res = $this->comapi_lib->available_subwallet_list($player_id);

            if ($subwallet_res['success'] == false) {
                throw new Exception($subwallet_res['mesg'], $subwallet_res['code'] + 0x1d7);
            }

            $result = $subwallet_res['result'];

            unset($result['success']);
            unset($result['wallets']);

            $ret = [
                'success'   => true ,
                'code'      => self::CODE_SUCCESS ,
                'mesg'      => 'Player balance query complete' ,
                'result'    => $result
            ];

            $this->comapi_log(__FUNCTION__, 'Response', $ret);

        }
        catch (Exception $ex) {
            $this->comapi_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ]);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];

        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function queryPlayerBalance2

    /**
     * Find affiliate tracking code from dedicated domain or additional domain
     * OGP-14751 ported from xcyl
     *
     * @uses    string  POST: api_key       api key given by system
     * @uses    string  POST: api_domain    Additional domain name
     *
     * @see     (SBE) http://admin.og.local/affiliate_management/userInformation/#aff_additional_domain_list
     * @see     Affiliatemodel::getTrackingCodeFromAffDomain()
     *
     * @return  JSON    General return object [ success, code, message,result ]
     *                      With result = [ affiliate_tracking_code: (code) ] if successful
     */
    public function getAffTrackingCodeByAdditionalDomain() {
        $api_key = $this->input->post('api_key');

        if (!$this->__checkKey($api_key)) { return; }

        try {
            $this->load->model([ 'affiliatemodel' ]);

            $aff_domain = trim($this->input->post('aff_domain'));
            $creds = [ 'api_key' => $api_key, 'aff_domain' => $aff_domain ];
            $this->utils->debug_log(__FUNCTION__, 'request', $creds);

            if (empty($aff_domain)) {
                throw new Exception(lang('aff_domain empty'), self::CODE_ADT_AFF_DOMAIN_EMPTY);
            }

            $aff_domain_clean = $aff_domain;
            if (strpos($aff_domain_clean, 'http://') !== false || strpos($aff_domain_clean, 'https://') !== false) {
                $aff_domain_clean = parse_url($aff_domain_clean, PHP_URL_HOST);
            }

            $aff_tracking_code = $this->affiliatemodel->getTrackingCodeFromAffDomain($aff_domain_clean);

            if (empty($aff_tracking_code)) {
                throw new Exception(lang('Affiliate not found'), self::CODE_ADT_AFFILIATE_NOT_FOUND);
            }

            $ret = [
                'success'   => true,
                'code'      => self::CODE_SUCCESS,
                'mesg'      => 'Affiliate tracking code retrieved',
                'result'    => [ 'affiliate_tracking_code' => $aff_tracking_code ]
            ];

            $this->utils->debug_log(__FUNCTION__, 'response', $ret, $creds);
        }
        catch (Exception $ex) {
            $this->utils->debug_log(__FUNCTION__, 'Exception', [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ], $creds);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    }

    public function getPlayerVipStatus(){
        $api_key    = $this->input->post('api_key'  , 1);
        $username   = $this->input->post('username' , 1);
        $token      = $this->input->post('token'    , 1);
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $request = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token ];
            $this->comapi_log(__METHOD__, 'request', $request);

            // Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception('Player username invalid', self::CODE_INVALID_USER);
            }

            // Check player token
            $logcheck = $this->_isPlayerLoggedIn($player_id, $token);
            if ($logcheck['code'] != 0) {
                throw new Exception($logcheck['mesg'], $logcheck['code']);
            }

            $this->load->library([ 'player_functions' ]);

            // $vip_res = $this->player_functions->getPlayerVipGroupDetails($player_id);
            $vip_full = $this->player_functions->getPlayerVipGroupDetails($player_id, 'force_desktop');

            if (empty($vip_full)) {
                throw new Exception(lang("Error reading player VIP status"), self::CODE_VIPS_ERROR_READING_VIP_STATUS);
            }

            $level_current  = $vip_full['current_vip_level'];
            $level_next     = $vip_full['next_vip_level'];

            $vip_res = [
                'current_group'     => $level_current['vip_group_name'] ,
                'current_level'     => $level_current['vip_lvl_name'] ,
                'current_level_badge'   => $level_current['vip_group_lvl_badge'] ,
                // 'next_level'        => $level_next['vip_group_lvl_name'] ,
                'current_deposit'   => $level_current['current_lvl_deposit_amt'] ,
                'required_deposit'  => $level_current['upgrade_deposit_amt_req'] ,
                'current_bet'       => $level_current['current_lvl_bet_amt'] ,
                'required_bet'      => $level_current['upgrade_bet_amt_req'] ,
                'is_at_max_level'   => $level_current['vip_group_lvl_number'] == $level_current['maxLevel']
            ];

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => "Successfully read player VIP status",
                'result'    => $vip_res
            ];
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__FUNCTION__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => null
            ];
        }
        finally {
            // $this->comapi_log(__FUNCTION__, 'Response', $ret);
            $this->returnApiResponseByArray($ret);
        }
    } // End class getPlayerVipStatus()

    /**
     * All-new transfer, 2020 edition
     * OGP-16295
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int     POST:transfer_from  subwallet to transfer from
     * @uses    int     POST:transfer_to    subwallet to transfer to
     * @uses    float   POST:amount         Amount to transfer
     *
     * @return  JSON    Standard JSON return object
     */
    public function transferTo() {
        $api_key = $this->input->post('api_key', 1);
        if (!$this->__checkKey($api_key)) { return; }

        try {
            $username       = $this->input->post('username', 1);
            $token          = $this->input->post('token', 1);
            $amount         = floatval($this->input->post('amount', 1));
            $transfer_to    = intval($this->input->post('transfer_to', 1));
            $transfer_from  = intval($this->input->post('transfer_from', 1));

            $request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'amount' => $amount, 'transfer_to' => $transfer_to, 'transfer_from' => $transfer_from ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $res_mesg = '';
            $res_data = null;

            // 0a: Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
            }

            // 0b: Check player token
            if (!$this->__isLoggedIn($player_id, $token)) {
                throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
            }

            // 0c: Reject transfer to same wallet
            if ($transfer_to == $transfer_from) {
                throw new Exception(lang('Cannot transfer to same wallet'), self::CODE_XFER_CANNOT_TRANSFER_TO_SAME_WALLET);
            }

            // 1: Reject transfer between subwallets
            if ($transfer_to != 0 && $transfer_from != 0) {
                throw new Exception(lang('Cannot transfer between subwallets'), self::CODE_XFER_NOT_BETWEEN_SUBWALLETS);
            }

            // 2: Check for amount
            // if (empty($amount) || $amount < 0.0) {
            //     throw new Exception(lang('Amount Invalid'), self::CODE_XFER_AMOUNT_INVALID);
            // }

            $this->load->model([ 'external_system' ]);

            $game_platform_id = $transfer_to ?: $transfer_from;
            $bal = $this->comapi_lib->available_subwallet_list($player_id);
            // $this->comapi_log(__METHOD__, 'balance', $bal);

            // 3a: Check for valid subwallet ID (from)
            if ($transfer_from != 0 && !isset($bal['result']['subwallets'][$transfer_from])) {
                throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_from), self::CODE_XFER_INVALID_SUBWALLET_ID);
            }

            // 3b: Check for valid subwallet ID (to)
            if ($transfer_to != 0 && !isset($bal['result']['subwallets'][$transfer_to])) {
                throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_to), self::CODE_XFER_INVALID_SUBWALLET_ID);
            }

            // $this->comapi_log(__METHOD__, ['game_platform_id' => $game_platform_id, 'isGameApiActive' => $this->external_system->isGameApiActive($game_platform_id)]);

            // 4: Check if game platform (sub wallet) is available
            // if ( !$this->external_system->isGameApiActive($game_platform_id) || $this->CI->external_system->isGameApiMaintenance($game_platform_id) ) {
            //     throw new Exception(sprintf(lang('Cannot transfer, sub wallet %d not available'), $game_platform_id), self::CODE_XFER_SUBWALLET_NOT_AVAILABLE);
            // }

            // 5: Check for enough balance
            // $src_balance = -1;
            // if ($transfer_from == 0) {
            //     $src_balance = $bal['result']['mainwallet'];
            // }
            // else {
            //     $src_balance = $bal['result']['subwallets'][$transfer_from];
            // }

            // if ($amount > $src_balance) {
            //     throw new Exception(sprintf(lang('Insufficient balance in wallet %d'), $transfer_from), self::CODE_XFER_INSUFFICIENT_BAL_IN_WALLET);
            // }

            $xfer_res = $this->utils->verifyWalletTransfer($player_id, $username, $transfer_from, $transfer_to, $amount);

            $this->comapi_log(__METHOD__, 'verifyWalletTransfer return', $xfer_res);

            // $res_data = $xfer_res;

            // Post-transfer process
            switch ($xfer_res['code']) {
                // == check 2
                case Utils::VERWALLETXFER_INVALID_AMOUNT :
                    throw new Exception(lang('Amount invalid'), self::CODE_XFER_AMOUNT_INVALID);
                    break;
                // == check 4 + check 3
                case Utils::VERWALLETXFER_GAME_NOT_ACTIVE :
                case Utils::VERWALLETXFER_GAME_MAINTENANCE :
                    throw new Exception(sprintf(lang('Cannot transfer, sub wallet %d not available'), $game_platform_id), self::CODE_XFER_SUBWALLET_NOT_AVAILABLE);
                    break;
                // == check 5
                case Utils::CHKTGTBAL_BALANCE_NOT_ENOUGH :
                    throw new Exception(sprintf(lang('Insufficient balance in wallet %d'), $transfer_from), self::CODE_XFER_INSUFFICIENT_BAL_IN_WALLET);
                    break;
                case Utils::VERWALLETXFER_NOT_TO_AGENCY :
                    throw new Exception(lang('Cannot transfer to agency account'), self::CODE_XFER_NOT_TO_AGENCY);
                    break;
                case Utils::VERWALLETXFER_PLAYER_XFER_DISABLED :
                    throw new Exception(lang('Transfer is disabled for player'), self::CODE_XFER_PLAYER_XFER_DISABLED);
                    break;
                case Utils::XFERWALLET_PLAYER_BLOCKED_IN_GAME :
                    throw new Exception(lang('Player blocked in game'), self::CODE_XFER_PLAYER_BLOCKED_IN_GAME);
                    break;
                // Wagering limits (responsible gaming)
                case Utils::VERWALLETXFER_WAGERING_LIMITS_ACTIVE :
                    throw new Exception(lang('Transfer failed, wagering limits is in effect for player'), self::CODE_XFER_DISALLOWED_BY_WAGERING_LIMITS);
                    break;
                // Transfer conditions
                case Utils::XFERWALLET_XFER_CONDS_ACTIVE :
                    throw new Exception(lang('Transfer failed because of transfer condition'), self::CODE_XFER_DISALLOWED_BY_XFER_COND);
                    break;
                // Withdraw conditions
                case Utils::CHKTGTBAL_WITHDRAW_COND_NOT_FINISHED :
                    throw new Exception(lang('Transfer failed because of withdrawal condition'), self::CODE_XFER_DISALLOWED_BY_WITHDRAW_COND);
                    break;
                // case Utils::XFERWALLET_SUCCESS_AMOUNT_LE_ZERO :
                //     break;
                // successful
                case 0 :
                    // (do nothing)
                    break;
                case Utils::XFERWALLET_PREXFER_FAILED :
                case Utils::VERWALLETXFER_TEST_PLAYERS_ONLY :
                case Utils::XFERWALLET_PLAYER_ID_EMPTY :
                case Utils::XFERWALLET_GENERAL_FAILURE :
                default :
                    if (!empty($xfer_res['message'])) {
                        $res_data = [ 'message' => $xfer_res['message'] ];
                    }
                    throw new Exception(lang('Transfer failed'), self::CODE_XFER_TRANSFER_FAILED);
            }

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => $res_mesg ?: lang('Transfer successful'),
                // 'result'    => $res_data
                'result'    => null
            ];
            $this->comapi_log(__METHOD__, 'Successful response', $ret);
        }
        catch (Exception $ex) {
            $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
            $this->comapi_log(__METHOD__, 'Exception', $ex_log);

            $ret = [
                'success'   => false,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res_data
                // 'result'    => null
            ];
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function transferTo()

    /**
     * Transfer all balance to given wallet
     * Separated from the old transfer to improve logic and maintainability
     * OGP-16295
     * @uses    string  POST:api_key        api key given by system
     * @uses    string  POST:username       Player username
     * @uses    string  POST:token          Effective token for player
     * @uses    int     POST:subwallet_id   Target subwallet, 0 or blank for main wallet
     *
     * @see     Utils::transferAllWallet(), Utils::transferWallets()
     *
     * @return  JSON    Standard JSON return object
     */
    public function transferAllTo() {
        $api_key = $this->input->post('api_key', 1);
        if (!$this->__checkKey($api_key)) { return; }
        $this->load->model([ 'player_preference' ]);

        try {
            $username       = $this->input->post('username', 1);
            $token          = $this->input->post('token', 1);
            $transfer_to    = (int) $this->input->post('transfer_to', 1);

            $request        = [ 'api_key' => $api_key, 'username' => $username, 'token' => $token, 'transfer_to' => $transfer_to ];
            $this->comapi_log(__METHOD__, 'request', $request);

            $res_mesg = '';
            $res_data = null;

            // 0a: Check player username
            $player_id  = $this->player_model->getPlayerIdByUsername($username);
            if (empty($player_id)) {
                throw new Exception(lang('Player username invalid'), self::CODE_COMMON_INVALID_USERNAME);
            }

            // 0b: Check player token
            if (!$this->__isLoggedIn($player_id, $token)) {
                throw new Exception(lang('Token invalid or player not logged in'), self::CODE_COMMON_INVALID_TOKEN);
            }

            // 1a: Check single-wallet switch status (global settings)
            if (!$this->utils->isAllowedAutoTransferOnFeature()) {
                throw new Exception(lang('Single-wallet switch not enabled'), self::CODE_XFER_SINGLE_WALLET_SWITCH_DISABLED);
            }

            // 1b: Check single-wallet switch status (player prefs)
            if ($this->utils->isEnabledFeature('enable_player_prefs_auto_transfer') && !$this->player_preference->isAutoTransferOnGameLaunch($player_id) ) {
                throw new Exception(lang('Auto-transfer disabled in player preference'), self::CODE_XFER_PLAYER_PREF_DISABLED_AUTO_XFER);
            }

            // 2: Check for valid subwallet ID (from)
            $bal = $this->comapi_lib->available_subwallet_list($player_id);

            if ($transfer_to != 0 && !isset($bal['result']['subwallets'][$transfer_to])) {
                throw new Exception(sprintf(lang('%d is not a valid subwallet ID for player'), $transfer_to), self::CODE_XFER_INVALID_SUBWALLET_ID);
            }

            $subwallet_res = $this->comapi_lib->player_balance_summary($player_id);
            $amount_to_xfer_back = $subwallet_res['subwallet_total'];
            if ($transfer_to != 0) {
                $amount_to_xfer_back -= $bal['result']['subwallets'][$transfer_to];
            }

            $this->comapi_log(__METHOD__, 'subwallet_res', $subwallet_res, 'amount_to_xfer_back', $amount_to_xfer_back);

            // 3: All amount are in target subwallet: no need to transfer
            if ($transfer_to != 0 && $subwallet_res['mainwallet'] == 0 && $amount_to_xfer_back == 0) {
                throw new Exception(lang('All balance transferred to given wallet successfully.'), 0);
            }

            $xfer_back_res = $this->utils->transferAllWallet($player_id, $username, $transfer_to);

            $this->comapi_log(__METHOD__, 'transferAllWallet return', $xfer_back_res, 'amount_to_xfer_back', $amount_to_xfer_back);

            // post-process 1
            if ($xfer_back_res['success'] == false) {
                // 1a: success == false, code != XFERWALLET_XFER_CONDS_ACTIVE: total failure
                if (!isset($xfer_back_res['code']) || $xfer_back_res['code'] != Utils::XFERWALLET_XFER_CONDS_ACTIVE) {
                    $res_data = $xfer_back_res;
                    throw new Exception(lang('Failure while transferring all balance'), self::CODE_XFER_XFERALL_FAILED);
                }
                // 1b: success == false, code == XFERWALLET_XFER_CONDS_ACTIVE: transfer condition active, transferAllTo failed
                else {
                    $res_data = $this->comapi_lib->tx_all_failure_reason_format($xfer_back_res);
                    throw new Exception(lang('Failure, transfer condition active for player'), self::CODE_XFER_XFERALL_XFER_COND_ACTIVE);
                }
            }

            // post-process 2: look into summary
            if (isset($xfer_back_res['summary'])) {
                $subw_count = count($xfer_back_res['summary']['subwallets']);
                $subw_fail  = count($xfer_back_res['summary']['subwallets_fail']);
                $amount_xfered = $xfer_back_res['summary']['amount_transferred'];

                if ($amount_to_xfer_back == 0 || $amount_xfered >= $amount_to_xfer_back) {
                    // (1) no amt to xfer back (2) amt xferred > amt to xfer back: total success
                    throw new Exception(lang('All balance transferred to given wallet successfully.'), 0);
                }

                if ($subw_fail == 0) {
                    // no failed subwallet: total success
                    throw new Exception(lang('All balance transferred to given wallet successfully.'), 0);
                }
                else if ($subw_fail < $subw_count) {
                    // partial success (Not an exception)
                    $res_data = [
                        'amount_to_transfer' => $amount_to_xfer_back ,
                        'amount_transferred' => $amount_xfered ,
                        // 'failed_subwallets' => $xfer_back_res['summary']['subwallets_fail']
                    ];
                    throw new Exception(lang('Some amount cannot be transferred because subwallets are under maintenance or disabled.'), 0);
                }
                else {
                    // total failure
                    $res_data = [
                        'amount_to_transfer' => $amount_to_xfer_back ,
                        'amount_transferred' => 0
                    ];
                    throw new Exception(lang('Cannot transfer balance, all amount are in subwallets under maintenance or disabled'), self::CODE_XFER_ALL_SUBWALLETS_DISABLED);
                }

            } // end if (isset($xfer_back_res['summary']))

            $ret = [
                'success'   => true,
                'code'      => 0,
                'mesg'      => $res_mesg,
                'result'    => $res_data
            ];
            $this->comapi_log(__METHOD__, 'Successful response', $ret);
        }
        catch (Exception $ex) {
            $code = $ex->getCode();
            if ($code > 0) {
                $ex_log = [ 'code' => $ex->getCode(), 'message' => $ex->getMessage() ];
                $this->comapi_log(__METHOD__, 'Exception', $ex_log);
            }

            $ret = [
                'success'   => $code > 0 ? false : true,
                'code'      => $ex->getCode(),
                'mesg'      => $ex->getMessage(),
                'result'    => $res_data
            ];

            if ($code <= 0) {
                $this->comapi_log(__METHOD__, 'Successful response', $ret);
            }
        }
        finally {
            $this->returnApiResponseByArray($ret);
        }
    } // End function transferAllTo()

} // End of class api_common
