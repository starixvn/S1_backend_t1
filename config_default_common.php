<?php

$config['RUNTIME_ENVIRONMENT'] = 'live';

//for 3 sites

//database
$config['db.default.hostname'] = '10.4.196.18';
$config['db.default.port'] = '3306';
$config['db.default.username'] = 'root';
$config['db.default.password'] = 'OWQnmpU5abVyEx!';
$config['db.default.database'] = 'ow';
$config['db.default.dbdriver'] = 'mysqli';
$config['db.default.dbprefix'] = '';
$config['db.default.pconnect'] = true;
$config['db.default.db_debug'] = true;
$config['db.default.cache_on'] = false;
$config['db.default.cachedir'] = '';
$config['db.default.char_set'] = 'utf8';
$config['db.default.dbcollat'] = 'utf8_unicode_ci';
$config['db.default.swap_pre'] = '';
$config['db.default.autoinit'] = true;
$config['db.default.stricton'] = false;

//read only, maybe different with main db, maybe delay
$config['db.readonly.hostname'] = '10.4.196.18';
$config['db.readonly.port'] = '3306';
$config['db.readonly.username'] = 'root';
$config['db.readonly.password'] = 'OWQnmpU5abVyEx!';
$config['db.readonly.database'] = 'ow';
$config['db.readonly.dbdriver'] = 'mysqli';
$config['db.readonly.dbprefix'] = '';
$config['db.readonly.pconnect'] = true;
$config['db.readonly.db_debug'] = true;
$config['db.readonly.cache_on'] = false;
$config['db.readonly.cachedir'] = '';
$config['db.readonly.char_set'] = 'utf8';
$config['db.readonly.dbcollat'] = 'utf8_unicode_ci';
$config['db.readonly.swap_pre'] = '';
$config['db.readonly.autoinit'] = true;
$config['db.readonly.stricton'] = false;

//multiple databases, always >= 2
$config['disabled_multiple_database']=true;
$config['multiple_databases']=[];
$config['multiple_databases_default_setting']=[
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_unicode_ci',
	'swap_pre' => '',
	'autoinit' => TRUE,
	'stricton' => FALSE,
];
//always sync object data to mdb, but enable or not
$config['enable_object_to_other_mdb']=[
	'player'=>true, 'user'=>true, 'agency'=>true, 'affiliate'=>true, 'role'=>true,
];
//sample:	'cny'=>['code'=>'CNY', 'name'=>'人民币', 'short_name'=>'元', 'symbol'=>'¥', 'decimals'=>2, 'dec_point'=>'.', 'thousands_sep'=>','],
//same key with multiple_databases
$config['multiple_currency_list']=[];
$config['default_currency_on_super']=[];
// $config['super_on_multiple_databases']=null;
//domain=>currency key
$config['domain_to_currency_key']=[];
$config['ip_country_to_currency_key']=[];
$config['super_default_currency_info']=['code'=>'CNY', 'name'=>'CN Yuan', 'short_name'=>'元', 'symbol'=>'¥', 'decimals'=>2, 'dec_point'=>'.', 'thousands_sep'=>',',
	'player_default_language'=>'Chinese', 'player_default_level_id'=>1,
	'default_language'=>'Chinese',
];

//api constants

//external system settings
$config['default_prefix_for_username'] = '';
$config['external_system_types'] = array(SYSTEM_GAME_API, SYSTEM_PAYMENT);
$config['external_system_map'] = array();

# List the config keys here, and identify the ones that will be used for special database fields
# e.g. 'url' => 'API_URL', meaning $config['API_URL'] will be stored in both live_url and sandbox_url fields
# config keys not indexed by 'url|key|secret|account' will go in the extra_info field
$config['external_system_config_names'] = array();

$config['payment_account_types_all'] = array(
	MANUAL_ONLINE_PAYMENT => array('lang_key' => "pay.manual_online_payment", 'enabled' => true),
	LOCAL_BANK_OFFLINE => array('lang_key' => "pay.local_bank_offline", 'enabled' => true),
	AUTO_ONLINE_PAYMENT => array('lang_key' => "pay.auto_online_payment", 'enabled' => true),
);

$config['payment_account_second_category_types_all'] = array(
	SECOND_CATEGORY_ONLINE_BANK => array('lang_key' => "pay.second_category_online_bank", 'enabled' => true),
	SECOND_CATEGORY_ALIPAY => array('lang_key' => "pay.second_category_alipay", 'enabled' => true),
	SECOND_CATEGORY_WEIXIN => array('lang_key' => "pay.second_category_weixin", 'enabled' => true),
	SECOND_CATEGORY_QQPAY => array('lang_key' => "pay.second_category_qqpay", 'enabled' => true),
	SECOND_CATEGORY_UNIONPAY => array('lang_key' => "pay.second_category_unionpay", 'enabled' => true),
	SECOND_CATEGORY_QUICKPAY => array('lang_key' => "pay.second_category_quickpay", 'enabled' => true),
	SECOND_CATEGORY_JDPAY => array('lang_key' => "pay.second_category_jdpay", 'enabled' => true),
	SECOND_CATEGORY_BANK_TRANSFER => array('lang_key' => "pay.second_category_bank_transfer", 'enabled' => true),
	SECOND_CATEGORY_ATM_TRANSFER => array('lang_key' => "pay.second_category_atm_transfer", 'enabled' => true),
	SECOND_CATEGORY_CRYPTOCURRENCY => array('lang_key' => "pay.second_category_cryptocurrency", 'enabled' => true),
);

$config['payment_account_types'] = array(
	MANUAL_ONLINE_PAYMENT => array('lang_key' => "pay.manual_online_payment", 'enabled' => true),
	AUTO_ONLINE_PAYMENT => array('lang_key' => "pay.auto_online_payment", 'enabled' => true),
	LOCAL_BANK_OFFLINE => array('lang_key' => "pay.local_bank_offline", 'enabled' => true),
);

//password
$config['queue_secret'] = null; // to config_local
$config['DESKEY_OG'] = null; // to config_local

$config['default_3rdparty_payment'] = IPS_PAYMENT_API;

$config['sync_sleep_seconds'] = 30;
$config['sync_balance_sleep_seconds'] = 120;
$config['sync_totals_sleep_seconds'] = 120;

$config['deposit_timeout_seconds'] = 3600;

$config['player_register_uri'] = '/iframe_module/iframe_register';

$config['show_realtime_balance'] = false;

//logs
$config['payment_error_log'] = APPPATH . 'logs/payment_error.log';
$config['queue_error_log'] = APPPATH . 'logs/queue_error.log';
$config['app_debug_log'] = APPPATH . 'logs/app_debug.log';
$config['runtime_debug_log'] = APPPATH . 'logs/runtime_debug.log';
// $config['aff_error_log'] = APPPATH . 'logs/aff_error.log';
$config['app_error_log'] = APPPATH . 'logs/app_error.log';

$config['testing_debug_log'] = APPPATH . 'logs/testing_debug.log';
$config['sync_game_records_log'] = APPPATH . 'logs/sync_game_records.log';
$config['sync_balance_log'] = APPPATH . 'logs/sync_balance.log';

$config['player_server_host'] = 'og.local';
$config['websocket_server_host'] = 'og.local:10080';

$config['enabled_origin'] = false;

$config['safe_origin_list'] = array('http://og.local', 'http://www.og.local', 'http://m.og.local', 'http://admin.og.local', 'http://aff.og.local');

// $config['msg_codes'] = array('login_failed' => 'error.login_failed', 'login_successfully' => 'notify.1',
//  'login_pt_failed' => 'error.login_pt_failed', 'login_pt_wrong_region' => 'login_pt_wrong_region');

//currency
$config['currency_list'] = array(
	'CNY' => 'CNY',
);

$config['default_currency'] = 'CNY';
$config['default_currency_name'] = '人民币';
$config['default_currency_short_name'] = '元';
$config['default_currency_symbol'] = '¥';
$config['default_currency_decimals'] = 2;
$config['default_currency_dec_point'] = '.';
$config['default_currency_thousands_sep'] = ',';

$config['display_currency_order'] = ['currency_name', 'currency_code', 'currency_symbol', 'currency_number', 'currency_short_name'];

$config['custom_deposit_rate'] = 1; #custom_deposit_rate > 1 means player should deposit more
$config['custom_withdrawal_rate'] = 1; #custom_withdrawal_rate < 1 means player will withdraw less
$config['custom_withdrawal_fee'] = 0.05; #fee rate, 0.05 means 5% will be reduced before transfering to player
$config['withdraw_amount_step_limit'] = 0.01;

$config['withdrawal_api_before_submit_dialog'] = array(
    '5107' => array(
        'title' => 'OTP',
        'message' => 'Check google authentication: <input id="player_input" type="text" class="form-control">',
        'confirm_label' => 'Confirm',
        'close_label' => 'Close'
    ),
    '5135' => array(
        'title' => 'OTP',
        'message' => 'Check google authentication: <input id="player_input" type="text" class="form-control">',
        'confirm_label' => 'Confirm',
        'close_label' => 'Close'
    )
);


$config['deposit_api_before_submit_dialog'] = array(
    '5191' => array(
        'title' => '来帐编号',
        'message' => '请输入来帐编号: <input id="player_input" type="text" class="form-control">',
        'confirm_label' => 'Confirm',
        'close_label' => 'Close'
    )
);

$config['ip_city_db_path'] = '/usr/share/GeoIP/GeoLite2-City.mmdb';
$config['ip_county_db_path'] = '/usr/share/GeoIP/GeoLite2-Country.mmdb';
$config['special_payment_list'] = array('bank_type_alipay', 'bank_type_wechat');
$config['default_sync_game_logs_max_time_second'] = 10800; //3 hours

$config['default_lang'] = 'english';

$config['default_items_pre_page'] = 10;

$config['account_image_width'] = 150;
$config['account_image_height'] = 150;

// a list of games can bet on both side , for example bet both on "banker" and "player" in bacarrat game.
$config['can_bet_both_side'] = array(
	PT_API => array(
		'ba', 'pba', 'sb', 'rop', 'ro', 'ro_g', 'ro3d', 'rodz', 'rodz_g', 'rom',
		'rop_g', 'rouk', '7bal', 'bal', 'bjl', 'plba', 'rofl', 'rol', 'sbl', 'vbal',
	),
	AG_API => array(
		'bac', 'cbac', 'link', 'dt', 'shb', 'rou', 'ft', 'lbac',
	),
);

$config['default_network_timeout'] = 30;
$config['default_soap_timeout'] = $config['default_network_timeout'];
$config['default_http_timeout'] = $config['default_network_timeout'];

$config['default_connect_timeout'] = 30;

$config['default_player_bootstrap_css'] = 'themes/bootstrap.yeti.css';

$config['default_site_name'] = 'default';

$config['si_background'] = APPPATH . 'libraries/captcha/backgrounds/bg5.png';

$config['si_active'] = 'si_easy'; // easy, medium, hard

$config['si_general'] = array(
    'namespace' => 'default',
	'ttf_file' => APPPATH . '/libraries/captcha/AHGBold.ttf',
	'image_signature' => '',
	'signature_color' => '#abcdef',
	'case_sensitive' => false,
	'image_height' => 40,
	'image_bg_color' => '#0099CC',
	'text_color' => '#EAEAEA',
	'line_color' => '#EAEAEA',
	'image_type' => 'Securimage::SI_IMAGE_JPEG',
	'use_wordlist' => false,
	'text_transparency_percentage' => 90,
	'use_transparent_text' => false,
	'charset' => '0123456789',
	// 'charset' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ23456789',
);

$config['si_easy'] = array(
	'code_length' => 4,
	'perturbation' => .5,
	'num_lines' => '2',
	'image_width' => 120,
	'noise_level' => 10,
);

$config['si_medium'] = array(
	'code_length' => 7,
	'perturbation' => .82,
	'num_lines' => rand(8, 10),
	'image_width' => 260,
);

$config['si_hard'] = array(
	'code_length' => 9,
	'perturbation' => 1.1,
	'num_lines' => rand(10, 12),
	'image_width' => 320,
);

/**
 * @deprecated
 */
$config['captcha_registration'] = true;
$config['captcha_login'] = false;
$config['captcha_agency_login'] = false;

$config['site_logo_width'] = 80;
$config['site_logo_height'] = 80;

//default is 5000k
//dont change the default file size. thanks.
$config['max_upload_size_byte'] = 5000000; // Ref. to https://talk.chatchat365.com/smartbackend/pl/dpjy5omjd3rpmggpcffgc888uo
$config['upload_image_max_width'] = 500;
$config['upload_image_max_height'] = 100;
$config['upload_promo_csv_max_row'] = 50;
//show game in game tree
$config['show_particular_game_in_tree'] = true;
//show player level in tree
$config['show_player_level_in_tree'] = true;
//only pay > min_cashback_amount
$config['min_cashback_amount'] = 1;

$config['balance_from_api'] = false;

//for checking deposit
$config['checking_deposit_locking'] = true;
$config['checking_withdrawal_locking'] = true;

$config['debug_session_lost'] = false;

$config['ping_time'] = 120000;

$config['enable_profiler'] = false;

$config['color'] = array(
	// Game Log
	"free_game" => "E45072", // bet = 0
	"trans_in_game_log" => "A4E450", // flag = 2
);

$config['defaultMinDepositDaily'] = NULL;
$config['defaultMaxDepositDaily'] = 1000000;
$config['defaultTotalDeposit'] = 10000000;

$config['deploy_token'] = null;
$config['debug_bofo'] = false;

// Affiliate
$config['baseIncomeConfig'] = 1;
$config['subAffiliateLevels'] = 10;
$config['aff_min_withdrawal'] = 100;

$config['dont_save_response_in_api'] = false;
$config['active_only_in_api'] = false;

$config['report_path'] = APPPATH . '/../public/reports';
$config['transaction_report_start_hour'] = '12';
$config['transaction_report_end_hour'] = '11';

$config['debug_promo'] = false;

// monthly commission
$config['show_calculate_button'] = false;
$config['slack_url'] = ''; // https://hooks.slack.com/services/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
$config['slack_user'] = '';
$config['slack_channel'] = '#php_fatal_error';
$config['slack_notify_channel'] = '#service_notification';
$config['payment_account_notify_url'] = '';
$config['payment_account_notify_user'] = 'default';
$config['payment_account_notify_channel'] = '#payment-account-notify';
$config['payment_alert_channel'] = '#payment-alert';

$config['payment_account_over_limit_percentage'] = 90;
$config['debug_data_table_sql'] = false;

$config['debug_print_sql'] = true;
$config['loadjs_timeout'] = 0;

$config['default_affiliate_settings'] = '{"baseIncomeConfig": "2","level_master":"50","minimumPayAmount": "0","paymentDay": "1","admin_fee": "0", "transaction_fee": "0", "bonus_fee": "0","cashback_fee": "100"}';
$config['affiliate_default_terms'] = '{"terms": {"terms_type": "option1","totalactiveplayer": "10","minimumBetting": "10000","minimumDeposit": "1000","provider": []}}';
$config['sub_affiliate_default_terms'] = '{"terms": {"terms_type": "allow","manual_open":"manual","sub_link":"link","sub_level":"5","sub_levels":[25,2,1,0,0]}}';
$config['use_total_hour'] = true;
$config['use_total_minute'] = true;

$config['use_total_summary'] = true;

$config['min_win_amount_for_newest'] = 900;
$config['min_win_amount_for_top10'] = 1000;
$config['enabled_web_push'] = false;

$config['enabled_rescue_promotion'] = true;
$config['rescue_promotion_amount'] = 5.0;

$config['daily_max_random_bonus'] = 3000;
$config['random_bonus_withdraw_condition_times'] = 5;
$config['min_random_bonus_rate'] = 1;
$config['max_random_bonus_rate'] = 5;

$config['random_bonus_top_img_url'] = 'random_bonus/hongbao_bgtop.png';
$config['random_bonus_center_img_url'] = 'random_bonus/hongbao.png';
$config['random_bonus_result_img_url'] = 'random_bonus/hongbao_bg.jpg';
$config['random_bonus_liondance1_swf_url'] = 'random_bonus/liondance1.swf';
$config['random_bonus_liondance50_swf_url'] = 'random_bonus/liondance50.swf';
$config['random_bonus_result_1_swf_url'] = '/resources/images/random_bonus/liondance1.swf';
$config['random_bonus_result_50_swf_url'] = '/resources/images/random_bonus/liondance50.swf';
$config['random_bonus_result_1_img_url'] = '/resources/images/random_bonus/liondance1.jpg';
$config['random_bonus_result_50_img_url'] = '/resources/images/random_bonus/liondance50.jpg';

$config['oneworks_sportsbook_img_url'] = 'oneworks/oneworks_sportsbook_bg.jpg';

#deposit_loading_gif option 1~6
$config['deposit_loading_gif'] = '/resources/images/loading/loading_player_submit_deposit_img_2.gif';

// const RANDOM_BONUS_MODE_PERCENT_DEPOSIT = 1;
// const RANDOM_BONUS_MODE_COUNTING = 2;
// const RANDOM_BONUS_MODE_FIXED_ITEM = 3;
$config['random_bonus_mode'] = 1;
$config['use_self_pick_group'] = false;
$config['use_self_pick_subwallets'] = false;
$config['big_bonus_trigger_count_number'] = 50;
$config['big_random_bonus_amount'] = 50;
$config['small_bonus_amount'] = 1;
$config['random_bonus_mode_counting_sandbox_mode'] = false;

$config['random_bonus_fixed_items'] = array(
	array('rate_by_deposit' => 8, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 18, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 28, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 38, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 58, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 68, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 78, 'probability' => 12.5, 'limit' => -1),
	array('rate_by_deposit' => 88, 'probability' => 12.5, 'limit' => -1),
);

$config['temp_disabled_game_api'] = array();

$config['auto_set_checking_to_request'] = true;

//default setting of cashback,start from yesterday, don't use number, use string, begin with 0 if only one number
$config['cashback_start_hour'] = '12';
$config['cashback_end_hour'] = '11';
$config['pay_time_hour'] = '14:00';
$config['cashback_days_ago'] = '1';

$config['print_verbose_game_api'] = false;
$config['salt_only_8_keys'] = true;
$config['default_open_search_panel'] = true;
$config['ignore_amount_limit_for_loadcard'] = true;
$config['min_loadcard_amount'] = 10;

// $config['live_chat_url'] = 'https://live.chatchat365.local';
// $config['live_chat_auto_login_uri'] = '/index.php/user/autologin';
// $config['live_chat_secret'] = ''; //from autologin key
// $config['live_chat_encrypt_key1'] = ''; // from start chat key
// $config['live_chat_encrypt_key2'] = ''; // from start chat key
// $config['live_chat_api_key'] = ''; // rest key
// $config['live_chat_api_secret'] = ''; // rest key
// $config['live_chat_options'] = array('widget_height' => 340, 'widget_width' => 300,
//     'popup_height' => 520, 'popup_width' => 500, 'theme'=>1, 'department'=>1);
// $config['live_chat_frontend_host'] = 'live.chatchat365.local';
// $config['live_chat_department'] = 1;

$config['payment_account_hide_bank_info'] = array('bank_type_wechat');
$config['payment_account_hide_bank_type'] = array('bank_type_alipay');
$config['auto_open_payment_account'] = false;

$config['token_timeout'] = 7200;

$config['random_bonus_intro_url'] = 'http://www.og.local/promo/random_bonus_intro.html';
$config['random_bonus_result_url'] = 'http://www.og.local/promo/random_bonus_result.html';
$config['random_bonus_not_available_url'] = 'http://www.og.local/promo/random_bonus_not_available.html';

$config['default_affiliate_id'] = null;

$config['default_manually_deposit_status'] = 'pending'; // or pending
$config['default_manually_withdraw_status'] = 'pending'; // or settled

$config['enable_ping'] = false;

$config['enable_bank_box_for_deposit'] = false;
$config['use_site_url_for_resource'] = true;
$config['aff_analytic_code'] = "";
$config['agency_analytic_code'] = "";
$config['player_analytic_code'] = "";
$config['admin_analytic_code'] = "";

$config['default_site_name'] = 'default';

$config['hide_host_on_url'] = true;
$config['blocked_country_for_admin'] = array();
$config['blocked_country_for_player'] = array();
$config['enable_debugbar'] = false;

$config['always_https'] = false;

$config['default_forbidden_names'] = ['admin', 'moderator', 'hoster', 'administrator', 'mod', 'shit', 'fuck'];

$config['admin_captcha_secret_key'] = null;

$config['record_full_ip'] = false;
$config['hide_player_contact_on_aff'] = false;
$config['disable_transfer_on_credit_aff'] = false;

$config['always_keep_admin_session'] = false;
$config['not_change_session_id_on_update'] = true;
$config['user_game_logs_for_cashback'] = false;
$config['user_game_logs_for_withdraw_condition'] = true;
$config['logout_pt_before_login'] = false;

$config['view_template'] = 'iframe'; // ['ttt'] default : iframe
$config['template_settings'] = []; //logo, icon or css
$config['view_template_extra'] = array();
$config['webet_routing_active'] = false;
$config['affiliate_view_template'] = 'affiliate'; // default : affiliate
$config['aff_contact_has_message_header'] = TRUE;
$config['aff_contact_email'] = 'affiliate@tripleonetech.com';
$config['aff_contact_qq'] = '';
$config['aff_contact_skype'] = '';
$config['aff_contact_whatsapp'] = '';
# If aff_contact_type and aff_contact_type_label are both defined, the qq and skype setup above will be disregarded
$config['aff_contact_type'] = '';
$config['aff_contact_type_label'] = 'QQ联系';
$config['live_chat_used'] = 'default'; // liveperson
$config['deposit_bank_slip_upload'] = false;
//should be link to Code/pub/$hostname/upload
$config['UPLOAD_PATH'] = realpath(APPPATH . '/../public/upload/');
$config['PUBLIC_UPLOAD_PATH'] = '/upload';
$config['PLAYER_INTERNAL_BASE_URL'] = '/player/upload';

$config['mail_smtp_server'] = 'box.tripleonetech.com';
$config['mail_smtp_port'] = 587;
$config['mail_smtp_auth'] = true;
$config['mail_smtp_secure'] = 'tls';
$config['mail_smtp_username'] = 'noreply@tripleonetech.com';
$config['mail_smtp_password'] = ''; // to config_secret_local
$config['mail_from'] = 'noreply@tripleonetech.com'; //'noreply@mail.smartbackend.com';
$config['disable_smtp_ssl_verify'] = true;
$config['launcher_domain'] = 'http://gci.v8k.com:81/';

$config['gearman_server'] = array('127.0.0.1');
$config['gearman_port'] = array('4730');

$config['disable_copy_cut'] = false;
$config['disable_contextmenu'] = false;

// $config['enable_always_active_for_api'] = false;
//top level
$config['disabled_api_list'] = array(); // array(IMPT_API);
$config['enabled_show_password'] = false;

$config['enable_clockwork'] = false;
$config['enable_readonly_db'] = true;

/////for sms
$config['sms_global_max_per_minute'] = 60; # Number of total Verification Code SMS allowed system-wide per minute
$config['sms_cooldown_time'] = 60; # Number of seconds between customer can request to send Verification Code SMS again
$config['sms_cooldown_time_per_ip'] = 2; # per ip
$config['sms_max_per_num_per_day'] = 5; # For a same number, only allow to send X SMS per day
$config['sms_valid_time'] = 600; # Number of seconds a SMS verification code remains valid
$config['sms_api'] = array(); # array('sms_api_luosimao', 'sms_api_ucpaas'); # classname of sms api implementation, also takes an array
$config['sms_from'] = ''; # this will appear at the end of the SMS sent
$config['sms_lang'] = 2; # Empty, or 1, or 2. When it's not empty, sms content will be forced to use the defined language. 1 = en, 2 = zh-cn
$config['sms_default_country'] = '86';
# Note : (Currently sms_restrict_send_num has only player center's phone verification settings)
$config['sms_restrict_send_num'] = false; # false or int, Use enable_restrict_sms_send_num_in_player_center_phone_verification option to enable the function in system feature
$config['sms_content_template'] = '欢迎您加入, 您的验证码： {%s} 更多优惠详情请联系在线客服。';
$config['sms_registration_template'] = '欢迎您加入，请进入 {player_center_url} ，使用用户名 {player_username} 登录';
$config['disabled_sms'] = false;

$config['Sms_api_luosimao_apikey'] = '';
$config['Sms_api_twilio'] = [
	'account_sid' => '',
	'auth_token'  => '',
	'from_phone_number' => ''
];

# For email
$config['email_cooldown_time'] = 30; # Number of seconds between a same player can trigger an email to be sent from the system (forget password, etc)

$config['enabled_player_cancel_pending_withdraw'] = false;
$config['enabled_agency'] = false;

# withdraw_verification defines the verification method used when user submits a withdrawal request.
# possible values: off, withdrawal_password, password, sms
# Note: This setting should be configured under config_secret_local as it's used on both admin and player domain
# password: same as login password
# sms: send an SMS verification code to registered handphone number
# withdrawal_password: separate withdrawal password
$config['withdraw_verification'] = 'off';

$config['withdraw_api'] = /*PAY24K_PAYMENT_API;*/'off'; # possible values: off (means manual withdraw only), or API constant like PAY24K_PAYMENT_API

$config['show_point_on_player'] = false;
$config['show_group_level_on_player'] = false;
$config['responsible_gaming'] = false;
$config['responsible_gaming_self_exclusion_period_list'] = [
    ['value' => 6, 'interval_spec' =>'P6M', 'text_suffix' => 'reg.59'],
    ['value' => 1, 'interval_spec' =>'P1Y', 'text_suffix' => 'reg.14'],
    ['value' => 3, 'interval_spec' =>'P3Y', 'text_suffix' => 'reg.14'],
    ['value' => 5, 'interval_spec' =>'P5Y', 'text_suffix' => 'reg.14'],
];
$config['make_up_transfer_record'] = false;

//url link
$config['admin_url'] = '';
$config['player_site_url'] = '';
$config['aff_site_url'] = '';
$config['aff_sub_affiliate_link'] = 'affiliate/register';

$config['cronjob_email_from'] = 'admin@tripleonetech.com';
$config['cronjob_email_to'] = '';

$config['pubnub_subscribe_key'] = '';
$config['channel_admin_announcement'] = 'admin_announcement';

$config['server_name'] = '';

$config['always_enable_unknown_games_on_callback'] = true;

$config['country_list'] = [
	'AF' => 'AFGHANISTAN',
	'AL' => 'ALBANIA',
	'DZ' => 'ALGERIA',
	'AS' => 'AMERICAN SAMOA',
	'AD' => 'ANDORRA',
	'AO' => 'ANGOLA',
	'AI' => 'ANGUILLA',
	'AQ' => 'ANTARCTICA',
	'AG' => 'ANTIGUA AND BARBUDA',
	'AR' => 'ARGENTINA',
	'AM' => 'ARMENIA',
	'AW' => 'ARUBA',
	'AU' => 'AUSTRALIA',
	'AT' => 'AUSTRIA',
	'AZ' => 'AZERBAIJAN',
	'BS' => 'BAHAMAS',
	'BH' => 'BAHRAIN',
	'BD' => 'BANGLADESH',
	'BB' => 'BARBADOS',
	'BY' => 'BELARUS',
	'BE' => 'BELGIUM',
	'BZ' => 'BELIZE',
	'BJ' => 'BENIN',
	'BM' => 'BERMUDA',
	'BT' => 'BHUTAN',
	'BO' => 'BOLIVIA',
	'BA' => 'BOSNIA AND HERZEGOVINA',
	'BW' => 'BOTSWANA',
	'BV' => 'BOUVET ISLAND',
	'BR' => 'BRAZIL',
	'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
	'BN' => 'BRUNEI DARUSSALAM',
	'BG' => 'BULGARIA',
	'BF' => 'BURKINA FASO',
	'BI' => 'BURUNDI',
	'KH' => 'CAMBODIA',
	'CM' => 'CAMEROON',
	'CA' => 'CANADA',
	'CV' => 'CAPE VERDE',
	'KY' => 'CAYMAN ISLANDS',
	'CF' => 'CENTRAL AFRICAN REPUBLIC',
	'TD' => 'CHAD',
	'CL' => 'CHILE',
	'CN' => 'CHINA',
	'CX' => 'CHRISTMAS ISLAND',
	'CC' => 'COCOS (KEELING) ISLANDS',
	'CO' => 'COLOMBIA',
	'KM' => 'COMOROS',
	'CG' => 'CONGO',
	'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
	'CK' => 'COOK ISLANDS',
	'CR' => 'COSTA RICA',
	'CI' => 'COTE D IVOIRE',
	'HR' => 'CROATIA',
	'CU' => 'CUBA',
	'CY' => 'CYPRUS',
	'CZ' => 'CZECH REPUBLIC',
	'DK' => 'DENMARK',
	'DJ' => 'DJIBOUTI',
	'DM' => 'DOMINICA',
	'DO' => 'DOMINICAN REPUBLIC',
	'TP' => 'EAST TIMOR',
	'EC' => 'ECUADOR',
	'EG' => 'EGYPT',
	'SV' => 'EL SALVADOR',
	'GQ' => 'EQUATORIAL GUINEA',
	'ER' => 'ERITREA',
	'EE' => 'ESTONIA',
	'ET' => 'ETHIOPIA',
	'FK' => 'FALKLAND ISLANDS (MALVINAS)',
	'FO' => 'FAROE ISLANDS',
	'FJ' => 'FIJI',
	'FI' => 'FINLAND',
	'FR' => 'FRANCE',
	'GF' => 'FRENCH GUIANA',
	'PF' => 'FRENCH POLYNESIA',
	'TF' => 'FRENCH SOUTHERN TERRITORIES',
	'GA' => 'GABON',
	'GM' => 'GAMBIA',
	'GE' => 'GEORGIA',
	'DE' => 'GERMANY',
	'GH' => 'GHANA',
	'GI' => 'GIBRALTAR',
	'GR' => 'GREECE',
	'GL' => 'GREENLAND',
	'GD' => 'GRENADA',
	'GP' => 'GUADELOUPE',
	'GU' => 'GUAM',
	'GT' => 'GUATEMALA',
	'GN' => 'GUINEA',
	'GW' => 'GUINEA-BISSAU',
	'GY' => 'GUYANA',
	'HT' => 'HAITI',
	'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
	'VA' => 'HOLY SEE (VATICAN CITY STATE)',
	'HN' => 'HONDURAS',
	'HK' => 'HONG KONG',
	'HU' => 'HUNGARY',
	'IS' => 'ICELAND',
	'IN' => 'INDIA',
	'ID' => 'INDONESIA',
	'IR' => 'IRAN, ISLAMIC REPUBLIC OF',
	'IQ' => 'IRAQ',
	'IE' => 'IRELAND',
	'IL' => 'ISRAEL',
	'IT' => 'ITALY',
	'JM' => 'JAMAICA',
	'JP' => 'JAPAN',
	'JO' => 'JORDAN',
	'KZ' => 'KAZAKSTAN',
	'KE' => 'KENYA',
	'KI' => 'KIRIBATI',
	'KP' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
	'KR' => 'KOREA REPUBLIC OF',
	'KW' => 'KUWAIT',
	'KG' => 'KYRGYZSTAN',
	'LA' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
	'LV' => 'LATVIA',
	'LB' => 'LEBANON',
	'LS' => 'LESOTHO',
	'LR' => 'LIBERIA',
	'LY' => 'LIBYAN ARAB JAMAHIRIYA',
	'LI' => 'LIECHTENSTEIN',
	'LT' => 'LITHUANIA',
	'LU' => 'LUXEMBOURG',
	'MO' => 'MACAU',
	'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
	'MG' => 'MADAGASCAR',
	'MW' => 'MALAWI',
	'MY' => 'MALAYSIA',
	'MV' => 'MALDIVES',
	'ML' => 'MALI',
	'MT' => 'MALTA',
	'MH' => 'MARSHALL ISLANDS',
	'MQ' => 'MARTINIQUE',
	'MR' => 'MAURITANIA',
	'MU' => 'MAURITIUS',
	'YT' => 'MAYOTTE',
	'MX' => 'MEXICO',
	'FM' => 'MICRONESIA, FEDERATED STATES OF',
	'MD' => 'MOLDOVA, REPUBLIC OF',
	'MC' => 'MONACO',
	'MN' => 'MONGOLIA',
	'MS' => 'MONTSERRAT',
	'MA' => 'MOROCCO',
	'MZ' => 'MOZAMBIQUE',
	'MM' => 'MYANMAR',
	'NA' => 'NAMIBIA',
	'NR' => 'NAURU',
	'NP' => 'NEPAL',
	'NL' => 'NETHERLANDS',
	'AN' => 'NETHERLANDS ANTILLES',
	'NC' => 'NEW CALEDONIA',
	'NZ' => 'NEW ZEALAND',
	'NI' => 'NICARAGUA',
	'NE' => 'NIGER',
	'NG' => 'NIGERIA',
	'NU' => 'NIUE',
	'NF' => 'NORFOLK ISLAND',
	'MP' => 'NORTHERN MARIANA ISLANDS',
	'NO' => 'NORWAY',
	'OM' => 'OMAN',
	'PK' => 'PAKISTAN',
	'PW' => 'PALAU',
	'PS' => 'PALESTINIAN TERRITORY, OCCUPIED',
	'PA' => 'PANAMA',
	'PG' => 'PAPUA NEW GUINEA',
	'PY' => 'PARAGUAY',
	'PE' => 'PERU',
	'PH' => 'PHILIPPINES',
	'PN' => 'PITCAIRN',
	'PL' => 'POLAND',
	'PT' => 'PORTUGAL',
	'PR' => 'PUERTO RICO',
	'QA' => 'QATAR',
	'RE' => 'REUNION',
	'RO' => 'ROMANIA',
	'RU' => 'RUSSIAN FEDERATION',
	'RW' => 'RWANDA',
	'SH' => 'SAINT HELENA',
	'KN' => 'SAINT KITTS AND NEVIS',
	'LC' => 'SAINT LUCIA',
	'PM' => 'SAINT PIERRE AND MIQUELON',
	'VC' => 'SAINT VINCENT AND THE GRENADINES',
	'WS' => 'SAMOA',
	'SM' => 'SAN MARINO',
	'ST' => 'SAO TOME AND PRINCIPE',
	'SA' => 'SAUDI ARABIA',
	'SN' => 'SENEGAL',
	'SC' => 'SEYCHELLES',
	'SL' => 'SIERRA LEONE',
	'SG' => 'SINGAPORE',
	'SK' => 'SLOVAKIA',
	'SI' => 'SLOVENIA',
	'SB' => 'SOLOMON ISLANDS',
	'SO' => 'SOMALIA',
	'ZA' => 'SOUTH AFRICA',
	'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
	'ES' => 'SPAIN',
	'LK' => 'SRI LANKA',
	'SD' => 'SUDAN',
	'SR' => 'SURINAME',
	'SJ' => 'SVALBARD AND JAN MAYEN',
	'SZ' => 'SWAZILAND',
	'SE' => 'SWEDEN',
	'CH' => 'SWITZERLAND',
	'SY' => 'SYRIAN ARAB REPUBLIC',
	'TW' => 'TAIWAN, PROVINCE OF CHINA',
	'TJ' => 'TAJIKISTAN',
	'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
	'TH' => 'THAILAND',
	'TG' => 'TOGO',
	'TK' => 'TOKELAU',
	'TO' => 'TONGA',
	'TT' => 'TRINIDAD AND TOBAGO',
	'TN' => 'TUNISIA',
	'TR' => 'TURKEY',
	'TM' => 'TURKMENISTAN',
	'TC' => 'TURKS AND CAICOS ISLANDS',
	'TV' => 'TUVALU',
	'UG' => 'UGANDA',
	'UA' => 'UKRAINE',
	'AE' => 'UNITED ARAB EMIRATES',
	'GB' => 'UNITED KINGDOM',
	'US' => 'UNITED STATES',
	'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
	'UY' => 'URUGUAY',
	'UZ' => 'UZBEKISTAN',
	'VU' => 'VANUATU',
	'VE' => 'VENEZUELA',
	'VN' => 'VIET NAM',
	'VG' => 'VIRGIN ISLANDS, BRITISH',
	'VI' => 'VIRGIN ISLANDS, U.S.',
	'WF' => 'WALLIS AND FUTUNA',
	'EH' => 'WESTERN SAHARA',
	'YE' => 'YEMEN',
	'YU' => 'YUGOSLAVIA',
	'ZM' => 'ZAMBIA',
	'ZW' => 'ZIMBABWE',
];

$config['always_run_cron_job'] = array();

$config['log_server_enabled'] = false;
$config['log_server_address'] = '';
$config['log_server_port'] = 12201;

$config['default_level_id'] = 1; //VIP LEVEL
$config['default_dispatch_account_group_id'] = 1;
$config['default_dispatch_account_level_id'] = 1;
$config['dispatch_account_batch_move_player_limit'] = 1000;
$config['dispatch_account_min_member_limit'] = 100; //Minimum member of each level

$config['enabled_features'] = array('promorules.allowed_affiliates', 'promorules.allowed_players',
	'player_list_on_affiliate', 'auto_refresh_balance_on_cashier',
	'generate_player_token_login', 'create_ag_demo', 'transaction_request_notification',
	'show_admin_support_live_chat', 'popup_window_on_player_center_for_mobile',
	'hide_deposit_approve_decline_button_on_timeout', 'affiliate_monthly_earnings', 'show_payment_account_image', 'agent_settlement_to_wallet',
    'enable_currency_symbol_in_the_withdraw',
);

$config['deprecated_features'] = [
    'use_default_deposit_flow', // add by elvis
    'hidden_transaction_number_input_in_deposit', // add by elvis
    'hidden_note_input_in_deposit', // add by elvis
    'player_center_sidebar_tour', // add by elvis
    'confirm_manual_deposit_details', // add by vincent
    'enable_3dparty_payment_in_modal', // add by vincent
    'enable_manual_deposit_detail', // add by vincent
	'hide_deposit_approve_decline_button_on_timeout', // add by vincent
	'show_deposit_bank_details_first', // add by vincent
	'ignore_bind_transaction_with_player_promo_when_trigger_collection_account_promo', // add by vincent
	'add_close_status', // add by vincent
	'add_notes_for_player', // add by vincent
	'allow_duplicate_contact_number', // add by vincent
	'auto_fix_2_days_cashback', // add by vincent
	'create_single_withdraw_condition_even_applied_promo', //add by ivan
	'player_cancel_pending_withdraw', //add by ivan
	'set_status_of_playerpromo_when_cancel_withdraw_condition', // add by ivan
	'show_sub_total_for_withdrawal_list_report', //add by ivan
	'check_disable_cashback_by_promotion',//add by ivan
    'redirect_to_player_center_when_go_to_register_page_if_islogin', // add by elvis
    'disabled_manually_transfer_on_player_center', // add by andrew
    'show_sub_total_for_deposit_list_report', // add by andrew
    'show_deposit_bank_details', // add by andrew
    'enabled_preapplication_on_promotion', //add by andrew
    'dpulicate_bank_account_number_verify_status_active', // add by elvis
    'enable_mobile_3rdparty_deposit_close_1_btn_and_append_redirecturl', // add by andrew
    'show_tag_for_unavailable_deposit_accounts', // add by andrew
    'new_promo_manager', // add by andrew & elvis
    'enabled_subwallet_to_subwallet_transfer', // add by elvis
    'enable_currency_symbol_in_the_deposit', // add by elvis
    'hide_pending_3rd_party_deposit', // add by elvis
    'enable_cashback_weekly_period', // add by elvis
    'show_all_pending_deposit_on_top_bar', // add by elvis
    'notification_all_pending_deposit', // add by elvis
    'enable_subwallet_by_category', // add by elvis
    'disabled_adjust_player_dispatch_account_level', //add by noke
    'only_allow_atm_deposit_upload_file', //add by curtis
    'update_player_last_activity', //add by elvis
    'disable_agency_game_report_in_sbe', // added by yunfei, OGP-9805
    'disable_agency_player_report_in_sbe', // added by yunfei, OGP-9805
    'enable_redirect_to_setted_up_config_link', // add by elvis
    'enabled_wrong_password_blocked', // add by elvis
    'ignore_cancelled_withdraw_condition_when_deduct_betting_amount', // add by elvis
    'use_www_css_for_player_center', // add by elvis
    'use_www_js_for_player_center', // add by elvis
    'show_password_recovery_option_by_email', // add by curtis
    'show_password_recovery_option_by_security_question', // add by curtis
    'show_password_recovery_option_by_sms', // add by curtis
    'user_guide_link', // add by elvis
    'support_ticket_link', // add by elvis
    'enable_player_center_mobile_3rd_party_live_chat', // add by elvis
    'pending_deposit_permission', // add by elvis
    'enable_transfer_all_to_main_wallet', // add by elvis
    'enable_affiliate_player_report_generator', // add by bryson
    'force_set_player_to_default_language', // add by elvis
    'enabled_change_lang_tutorial', // add by bryson
    'skip_save_http_request_with_login_from_admin', // add by bryson
    'display_one_week_data_on_withdraw_condition', // added by elvis
    'mobile_allow_edit_IM', // add by bryson
    'disable_player_change_email_no_matter_email_has_already_been_verified_or_not', // add by bryson
    'disable_player_change_password', // added by elvis
    'enable_popup_login', // add by bryson
    'enable_popup_registration', // add by bryson
    'show_vip_group_on_first_login', //add by bryson
    'enable_set_block_status_after_register', // add by bryson
    'enable_first_logon_show_ad_popup', // add by bryson
    'display_profile_update_detail_validation_result', // added by elvis
    'hide_promo_code_on_deposit_page', // add by curtis
    'show_player_messages_tab', // add by jessie
    'disable_player_deposit_bank', // add by jouan
    'hide_bank_account_full_name_in_payment_account_detail_player_center', // add by jouan
    'force_setup_player_deposit_bank_when_if_it_is_empty', // add by jouan
    'hide_bank_branch_in_payment_account_detail_player_center', // add by jouan
    'enable_confirm_birthday_before_setting_up_withdrawal_bank_account', // add by jouan
    'allow_only_bank_account_limit', // add by jouan
    'player_bind_one_bank', // add by jouan
    'player_bankAccount_input_numbers_limit', // add by jouan
    'player_bind_one_address_each_cryptocurrency', // add by jouan
    'always_duplicate_player_any_bank_when_add', // add by jouan
    'player_can_edit_bank_account', // add by jouan
    'player_can_delete_bank_account', // add by jouan
    'player_bank_show_detail_form_validation_results', // add by jouan
    'disabled_send_email_contactus', // add by jouan
    'disabled_send_email_upon_aff_registration', // add by jouan
    'disabled_send_email_upon_change_withdrawal_password', // add by jouan
    'disabled_send_email_upon_player_registration', // add by jouan
    'disabled_send_email_upon_promotion', // add by jouan
    'send_email_after_verification', // add by jouan
    'send_email_promotion_template_after_verification', // add by jouan
    'enabled_active_affiliate_by_email', // add by jouan
    'show_time_interval_in_deposit_processing_list', // add by jouan
    'summary_report_2', // add by jouan
    'enable_player_report_2', // add by jouan
    'use_new_account_for_manually_withdraw', // add by jessie
    'send_message', // add by jouan
    'donot_show_registration_verify_email', // add by jouan
    'enable_trasfer_all_quick_transfer', // add by jouan
    'player_center_sidebar_transfer', // add by jouan
    'enabled_transfer_all_and_refresh_button_on_new_transfer_ui', // add by jouan
    'enable_default_logic_transaction_period',// add by kris
    'show_unsettle_game_logs' , // Added by rupert.chen.php.tw - OGP-14934
	'default_settled_status_on_player_deposit_list' , // Added by rupert.chen.php.tw - OGP-14934
    'declined_forever_promotion', //add by curtis
    'enable_default_logic_transaction_period', // add by kris
    'realign_the_dashboard_in_deposit_list_and_make_them_clickable', // add by kris
    'untick_time_out_deposit_request_if_load_deposit_list', // add by kris
    'untick_3rd_party_payment_if_load_deposit_list', // add by kris
    'untick_atm_cashier_if_load_deposit_list', // add by kris
	'untick_enabled_date_if_load_deposit_list', // add by kris
	'show_disabled_games_on_game_tree', // add by ping
    'agency', // add by jouan
    'iovation_fraud_prevention', // add by jouan
    'enable_super_report', //add by gary
    'www_sidebar', // add by kris
    'www_quick_transfer_sidebar', // add by kris
    'www_deposit_sidebar', // add by kris
	'www_live_chat_sidebar', // add by kris
	'hide_retype_email_field_on_registration', // add by Min
    'show_deposit_3rdparty_on_top_bar', //add by kris
    'notification_thirdparty_settled_on_top_bar', //add by kris
    'notification_local_bank', //add by kris
    'notification_thirdparty', //add by kris
    'notification_withdraw', //add by kris
    'only_use_dropdown_list_for_notification', //add by kris
    'transaction_request_notification', //add by kris
    'ignore_notification_permission', //add by kris
    'disabled_auto_create_game_account_on_registration', //add by kris
    'default_search_all_players', // add by kris
    'display_locked_status_column', //add by gary
    'show_player_address_in_list', // add by jouan
    'show_id_card_number_in_list', // add by jouan
    'show_zip_code_in_list', // add by jouan
    'disable_action_buttons_in_player_list_table', // add by jouan
];

//** Adding a function code to deprecate the permissions
$config['deprecated_permissions'] = [
	//added by frans.php.ph
	'settlement',
	'agency_setting',
	'agent_contact_info',
	'edit_agent_term',
	'agent_earnings',
	'edit_agency_settings',
	'agency_player_deposit',
	'agency_player_withdraw',
	'send_message',
	'support_ticket',
	'duplicate_account_checker_setting',
	'vip_settings_group_add',
	'vip_settings_group_edit',
	'vip_settings_group_delete',
	'vip_settings_group_preview',
	'vip_settings_group_export',
	'vip_settings_level_increase_decrease',
	'vip_settings_level_edit',
	'vip_settings_level_export',
	'vip_rebate_rules_template',
	'vip_rebate_rules_template_add',
	'vip_rebate_rules_template_edit',
	'vip_rebate_rules_template_delete',
	'vip_rebate_rules_template_active',
	'vip_rebate_rules_template_export',
	'vip_rebate_values',
	'vip_rebate_values_export',
	'vip_request_list',
	'vip_request_list_export',
	'vip_request_list_approve',
	'vip_request_list_reject',
	'vip_settings_group_level_save',
	'vip_settings_group_save',
	'export_cashback_request',
	//added by frans.php.ph
	// -- start -- added by cholo.php.ph
	'upload_sbe_logo',
	'verified_phone_and_email_info',
	'replacement_transfer_order',
	'promo_cancellation_setting',
	'bannerPromotion',
	'promoplayer_list',
	'affiliate_player_report',
	'api_report',
	'payment_auto3rdparty_deposit_list',
	'payment_manual3rdparty_deposit_list',
	'cancel_member_withdaw_condition',
	'approve_declient_3rdparty_deposit',
	'agency_wl_report',
	'allow_to_do_settlement',
	'agency_player_report',
	'agency_game_report',
	'vip_settings',
	// -- end -- added by cholo.php.ph
	// -- start -- added by andrew.php.ph
	'transactions_report',
	'export_all_username',
	'export_batch_create',
	'force_change_affiliate_of_player',
	// -- end -- added by andrew.php.ph
	// -- start -- added by kris.php.tw
	'deposit_count_list',
	'update_deposit_attachment',
	'upload_deposit_slip',
	// -- end -- added by kris.php.tw
	'payment_settings',
    'message_history', // added by elvis.php.tw
    'game_setting', // added by elvis.php.tw
    'export_report', // added by elvis.php.tw
    'block_game_setting', // added by elvis.php.tw
    'cashback_daily', // added by elvis.php.tw
    'system_api_docs', // added by elvis.php.tw
    'show_kyc_status', // added by elvis.php.tw
    'show_risk_score', // added by elvis.php.tw
	'show_allowed_withdrawal_status', // added by elvis.php.tw
	'deposit_count_setting', // added by jessie.php.tw
	'summary_report', // added by joaun.php.tw
	'export_summary_report', // added by joaun.php.tw
	'compensation_setting', // added by joaun.php.tw
	'system_settings', // added by rupert.chen.php.tw - OGP-14823
	'view_player_list_summary_info', // added by ping.php.tw - OGP-14393 : Enhancement of Conversion Rate Report performance
	'allow_search_player_phone_number_no_view', // added by ping.php.tw - OGP-14393 : Enhancement of Conversion Rate Report performance
	'allow_search_player_email_no_view', // added by ping.php.tw - OGP-14393 : Enhancement of Conversion Rate Report performance
	'allow_search_player_im_no_view', // added by ping.php.tw - OGP-14393 : Enhancement of Conversion Rate Report performance
	'marketing_setting', // added by min.php.tw - OGP-15605 : Deprecate permissions under Marketing Management
	'edit_game_type', // added by min.php.tw - OGP-15605 : Deprecate permissions under Marketing Management
	'export_duplicate_account_report', // added by jessie.php.tw - OGP-15604 : Duplicate Account Report Modification
	'player_communication_preference', // added by joaun.php.tw
];

// for notification [add to enabled_features features]:
//  * transaction_request_notification(all notificaition enabled)
//  * notification_promo (promo)
//  * notification_messages (messages)
//  * notification_local_bank (local_bank)
//  * notification_thirdparty (thirdparty)
//  * notification_withdraw (withdraw)

$config['live_chat'] = [
	'admin_login_url' => 'https://live.chatchat365.local/index.php/user/autologin',
	'admin_login_secret' => '',
	'encrypt_key1' => '',
	'encrypt_key2' => '',
	'api_key' => '',
	'api_secret' => '',
	'www_chat_options' => [
		'widget_height' => 340, 'widget_width' => 300,
		'popup_height' => 520, 'popup_width' => 500,
		'theme' => 1, 'department' => 1, "survey_id" => null,
		'lang' => 'chn',
		'onlylink' => false,
		'load_livechat_link_selector' => '.load_livechat',
		'popup_livechat_link_selector' => '.popup_livechat',
		'pop_embed_livechat_link_selector' => '.pop_embed_livechat',
	],
	'www_chat_host' => 'live.chatchat365.local',
	'www_chat_https_enabled' => false,
	'attach_link_on_player_center' => false,
	'show_on_player_center' => false,
	'external_url'=>'',
	'external_onclick'=>'',
];

$config['hide_player_info_on_aff'] = false;
$config['show_version_on_player'] = true;

$config['editable_tracking_code_on_aff'] = false;

$config['logger_settings'] = [
	'handlers' => array('json'), // 'loggly' valid handlers are file | new_relic | hipchat
	'channel' => $config['RUNTIME_ENVIRONMENT'], // channel name which appears on each line of log
	'threshold' => '5', //'OFF' => '0', 'ERROR' => '1', 'INFO' => '2', 'DEBUG' => '3', 'VERBOSE' => '4', 'ALL' => '5',

	'json_level' => 'debug',
    'json_file' => BASEPATH.'/../application/logs/log-json.log',
    'enabled_json_file' => false,

    'file_logfile' => BASEPATH.'/../application/logs/log-ci.log',
    'max_files' => 10,

    'loggly_logfile' => BASEPATH . '/../application/logs/loggly.log', //'/root/wrong.log', //
    'loggly_level' => 'error',
    'loggly_token' => '',

	// exclusion list for pesky messages which you may wish to temporarily suppress with strpos() match
	'exclusion_list' => array(),
];

$config['default_logger_json_file']=null;

$config['enabled_logger_json_file']=true;
$config['enabled_logger_file_channel']=true;
$config['enabled_debug_redis_logger']=false;

$config['logentries_settings'] = [
	'token' => null,
	'persistent' => true,
	'ssl' => true,
	'severity' => LOG_INFO,
	// 'HOST_ID'=>$config['RUNTIME_ENVIRONMENT'],

	'DATAHUB_ENABLED' => false,
	'DATAHUB_IP_ADDRESS' => null,
	'DATAHUB_PORT' => null,
	'HOST_NAME' => '',
	'HOST_NAME_ENABLED' => true,
	'ADD_LOCAL_TIMESTAMP' => true,

];

$config['log_time_elapsed'] = 'debug';

$config['always_resync_game_logs_for_cashback'] = false;

$config['default_transfer_subwallet_id'] = null;

$config['batch_sync_all_players'] = false;

$config['verbose_log'] = true;

//sould check max limit for real and bonus
$config['big_wallet_rules'] = [
	'enabled_cache_bin_wallet' => false,

	'big_wallet_manually_order' => ['real', 'win_real', 'real_for_bonus', 'bonus', 'win_bonus'],

	'big_wallet_win_order' => ['win_bonus', 'win_real', 'real'], //hide rule: should check >0 before add to win wallet
	'big_wallet_loss_order' => ['real', 'real_for_bonus', 'win_real', 'win_bonus', 'bonus'],
	'big_wallet_transfer_to_main_order' => ['real', 'win_real'], //'real_for_bonus', 'win_bonus', 'bonus'],
	'big_wallet_transfer_from_main_order' => ['real', 'win_real', 'real_for_bonus', 'win_bonus', 'bonus'],

	'dec_frozen_order' => ['real', 'real_for_bonus'],
	'inc_frozen_order' => ['real', 'real_for_bonus'],

	'cashback_wallet_type' => 'bonus',

	'sub_to_main_only_allow' => ['real', 'win_real'],
	'withdraw_main_only_allow' => ['real'],

	'force_real' => true,

];

$config['promotion_rules'] = [
	'enabled_notallow_transaction_type' => true,
	'from_date_order' => ['player_reg_date', 'last_withdraw'],
	'enabled_show_withdraw_condition_detail_betting' => true,
//	'enabled_auto_check_withdraw_condition' => true,
	'enabled_auto_clear_withdraw_condition' => false,
	'enabled_request_without_check' => false,
	'release_on_admin_approve' => true,
	//to control feature of promo rules
	'finish_last_withdraw_condition_before_apply_promotion' => false,
	'allow_manually_promotion' => false,
	// 'allow_auto_trigger_sub_wallet' => false,
	'always_limit_one_promotion' => false,
	'enabled_auto_finish_playerpromo' => true,

	'show_extra_message_on_transfer_promotion' => false,

	'add_withdraw_condition_as_bonus_condition' => true,

	// 'only_real_when_refresh'=>false,

	'only_real_when_release_bonus' => false,

	'allow_decline_on_approved_and_without_release' => false,

	'disable_pre_application_on_release_bonus_first' => false,

	'start_betting_amount_from_player_apply' => false,

	'search_transaction_order' => 'desc',

	// 'enabled_delete_declined_promo'=>true,
	// 'enabled_move_all_to_real'=>true,
];

$config['admin_support_live_chat'] = [
	'encrypt_key1' => '7feb4025e4025bfa93128ce0360a5d6f025bfa93128ce0362',
	'encrypt_key2' => '8feb4ce0360a5d6f025b025e4025bfa93128fa93128ce0360',
	'www_chat_options' => [
		'widget_height' => 340, 'widget_width' => 300,
		'popup_height' => 520, 'popup_width' => 500,
		'theme' => 1, 'department' => 3, "survey_id" => null,
		'lang' => 'chn',
		'onlylink' => false,
		'load_livechat_link_selector' => '.load_livechat',
		'popup_livechat_link_selector' => '.popup_livechat',
	],
	'www_chat_host' => 'live.chatchat365.com',
	'www_chat_https_enabled' => false,
];

$config['use_ci_db_to_lock'] = true;

$config['max_size_duplicate_accounts'] = 30;

$config['silverpop_options'] = array(
	'apiHost' => null,
	'username' => null,
	'password' => null,
);

$config['big_wallet_transfer_to_main_real'] = true;

$config['default_sort_columns'] = [
	'deposit_list' => 4,
];

$config['aff_banner_url'] = null;

$config['pub_api_key'] = '';

$config['operator_unique_name'] = 'homestead';
$config['service_api_login'] = 'service/api/login_by_token';

$config['minus_transaction_fee_from_deposit'] = false;
$config['minus_transaction_fee_from_withdraw'] = false;

$config['show_agency_rev_share_etc'] = false;
$config['email_registration_subject'] = null;
$config['hide_commission_settings'] = true;

$config['record_activity_timeout_seconds'] = 10;

$config['close_db_on_each_request'] = false;
$config['agency_settlement_time'] = '12:00:00';

$config['player_form_registration'] = [
    'terms' => TRUE,
    'newsletter_subscription' => TRUE,
    /* communication_preferences */
    /* See $config['communication_preferences'] */
    'communication_preferences_email' => TRUE,
    'communication_preferences_sms' => TRUE,
    'communication_preferences_phone_call' => TRUE,
    'communication_preferences_post' => TRUE
    /* ========================= */
];

$config['player_validator'] = [
	'username' => ['min' => 6, 'max' => 12, 'default_regex' => '/^[a-z0-9]+$/', 'restrict_regex' => '/^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$/', 'default_regex_js' => '^[a-z0-9]+$', 'restrict_regex_js' => '^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$'],
	'password' => ['min' => 6, 'max' => 20, 'default_regex' =>'/^[A-Za-z0-9]+$/', 'restrict_regex' => '/^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$/'],
	'security_answer' => ['min' => 1, 'max' => 50],
	'first_name' => ['min' => 2, 'max' => 20],
	'last_name' => ['max' => 20],
	'contact_number' => ['min' => 8, 'max' => 12], // extra_length : min == max
];

$config['default_min_size_username'] = $config['player_validator']['username']['min'];
$config['default_max_size_username'] = $config['player_validator']['username']['max'];
$config['default_regex_username'] = $config['player_validator']['username']['default_regex'];
$config['default_regex_username_js'] = $config['player_validator']['username']['default_regex_js'];
$config['default_min_size_password'] = $config['player_validator']['password']['min'];
$config['default_max_size_password'] = $config['player_validator']['password']['max'];
$config['default_regex_password'] = $config['player_validator']['password']['default_regex'];
$config['default_min_size_security_answer'] = $config['player_validator']['security_answer']['min'];
$config['default_max_size_security_answer'] = $config['player_validator']['security_answer']['max'];

$config['default_min_size_first_name'] = $config['player_validator']['first_name']['min'];
$config['default_max_size_first_name'] = $config['player_validator']['first_name']['max'];
$config['default_max_size_last_name'] = $config['player_validator']['last_name']['max'];

$config['restrict_regex_username'] = $config['player_validator']['username']['restrict_regex'];
$config['restrict_regex_username_js'] = $config['player_validator']['username']['restrict_regex_js'];
$config['restrict_regex_password'] = $config['player_validator']['password']['restrict_regex'];

$config['player_myfavorite_limit_count'] = 12;

$config['admin_support_ticket'] = 'http://support.tripleonetech.com';

//seconds
$config['timeout_refresh_queue'] = 10;

$config['update_bet_limit_on_batch_create'] = false;

$config['print_log_to_console'] = false;

$config['feedback_api_key'] = 'ed50cbbd-0c04-4e2a-81f6-2b81980fc5ee';

$config['player_session_timeout_seconds'] = 5 * 60;
$config['check_session_timeout_ms'] = 30000;

$config['default_agency_login_language'] = 'chinese';

$config['app_lock_timeout'] = 180;
//lock failed timeout=lock_retry_delay * retryCount(20)
$config['lock_retry_delay'] = 2000;
$config['use_lock_server_to_lock'] = true;
$config['lock_servers'] = [
	['10.4.196.17', 6379, 0.01],
	// ['127.0.0.1', 6389, 0.01],
	// ['127.0.0.1', 6399, 0.01],
];

$config['default_group_id_on_registration'] = null;

$config['disable_cashback_on_register'] = false;
$config['disable_promotion_on_register'] = false;

$config['debug_sync_balance_in_game_logs_include'] = [];

$config['og_load_testing'] = false;
$config['enabled_registration_captcha'] = true;

$config['ip_headers'] = null;

//default_player_language always uppercase the first letter
$config['default_player_language'] = 'Chinese';

$config['domain_prefer_language'] = NULL;

$config['show_terms_on_player_register'] = false;
$config['popup_deposit_after_login'] = false;

$config['main_prefix_of_player'] = ''; //will be added to username, not just show

$config['default_admin_login_language'] = 'english';

$config['default_datatable_page_length'] = 25;

$config['common_block_page_url'] = '';

$config['auto_generate_domain'] = false;
$config['auto_domain_pattern'] = ''; //like {AFFDOMAIN}.xxx.com
$config['auto_domain_start'] = 100;
$config['bank_address_disabled'] = true;

$config['registration_hint'] = '';

$config['csrf_header_token_name'] = 'XCSRFTOKEN';

$config['coutry_rules_mode'] = 'allow_all'; //allow_all, deny_all
$config['blocked_page_url'] = '/block-page.html';
/**
 * @link https://www.ipip.net/ip.html Country Name Ref
 * @example
 * <code>
 * $config['blocked_page_url_with_locale'] = [
 *     'Taiwan' => 'https://www.og.local/',
 *     'Philippines' => 'https://www.og.local/'
 * ];
 * </code>
 */
$config['blocked_page_url_with_locale'] = [];

$config['www_white_ip_list'] = ['127.0.0.1'];
$config['www_block_ip_list'] = [];

$config['set_success_when_transfer_timeout'] = true;

$config['hide_deposit_lower_limit'] = false;
$config['hide_deposit_upper_limit'] = false;
$config['enabled_sync_in_balance'] = false;

$config['ignore_undefined_php'] = false;
$config['ignore_unexpect_warning_php']=false;
$config['ignore_count_warning']=false;
$config['ignore_php_warning_reference_variable_on_param']=false;
$config['ignore_all_notice_error']=false;
$config['ignore_count_warning']=false;
$config['disabled_all_php_page_error']=false;

$config['hide_confirm_email'] = true; // hide confirm email on registration

//for xcbet profile info form
$config['disabled_username_fld'] = true;
$config['disabled_currency_fld'] = true;
$config['disabled_invitationCode_fld'] = true;
$config['disabled_language_fld'] = true;
$config['disabled_contact_number_fld'] = true;
$config['disabled_email_fld'] = true;
$config['disabled_selection_im_account_fld'] = true;
$config['disabled_address_fld'] = true;
$config['disabled_city_fld'] = true;
$config['disabled_resident_country_fld'] = true;
$config['disabled_country_fld'] = true;
$config['disabled_citizenship_fld'] = true;
$config['disabled_birthdate_fld'] = true;
$config['disabled_birthplace_fld'] = true;

$config['enabled_promo_pagination'] = true;

$config['blocked_ip_for_aff'] = [];

$config['aff_timeout_second_password'] = 1800;
$config['aff_commission_settings'] = array(
	'baseIncomeConfig' => 1,
	'minimumDeposit' => 0,
	'admin_fee' => 0,
	'transaction_fee' => 0,
	'bonus_fee' => 0,
	'cashback_fee' => 100,
	'minimumPayAmount' => 0,
	'autoTransferToWallet' => 'none',
	'paymentSchedule' => 0,
	'manual_open' => true,
	'sub_link' => true,
	'sub_levels' => array_fill(0, 10, 0),
	'tiers' => array(
		array(
			'active_players' => 0,
			'net_revenue' => 0,
			'commission_percentage' => 0,
		),
	),
);

$config['iovation_api'] = [];
$config['gbg_api_url'] = [
	'wsdl' => 'https://pilot.id3global.com/ID3gWS/ID3global.svc?wsdl',
	'wss_ns' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'];

$config['datatable_error_mode'] = 'throw';
$config['fav_icon'] = "/favicon.ico"; // sample replace fav icon on player and admin
$config['site_domain'] = "default"; // sample replace fav icon on player and admin
// $config['contact_number_show_withdrawal'] = true;

$config['mail_smtp_hostname'] = 'localhost.localdomain';

$config['demo_admin'] = 'superadmin';
$config['demo_player'] = 'demouser';

$config['demo_account_send_mail_address'] = 'marketing@smartbackend.com';

//for isoftbet freespin credentials
$config['isoftbet_api_url'] = 'https://stage-rms-lux.isoftbet.com/api/irs/freeround/management/';
$config['isoftbet_api_licensee'] = '169';
$config['isoftbet_api_secret_key'] = 'nYqJNfHlsCnUQicysfvBtYuRmOAOM87h';

# Tele api setting
$config['tele_api_setting'] = [
	'live_url', 'live_key'
];
$config['default_tele_id'] = '';
$config['call_tele_cooldown'] = 10; # defines number of seconds allowed in between two Tele Marketing API calls

$config['password_recovery_cooldown'] = 10; # defines number of seconds allowed in between two password recovery SMS / Emails

$config['tracking_code_priority_levels'] = array('trackingCodeInRef', 'trackingCodeInDomain', 'trackingCodeInSession');
$config['ANNOUCEMENT_STYLE'] = <<<EOF

<style>
    body {
        margin: 0px;
        height: 38px;
    }
    marquee {
        font: 15px "\5FAE\8F6F\96C5\9ED1", "微软雅黑", "Microsoft YaHei";
        color: #fff;

    }
</style>

EOF;

$config['ANNOUCEMENT_STYLE_MOBILE'] = <<<EOF

<style>
/*for mobile*/
</style>

EOF;

$config['transaction_type_for_disable_cashback'] = [];

$config['agency_player_rolling_settings'] = [
	'min_rolling_comm' => 0,
	'max_rolling_comm' => 3,
];

$config['agency_max_start_rolling'] = '-1 month';

$config['agency_auto_create_from_player'] = [
    'default_parent_agent_id' => 0,
    'username_prefix' => 'ag_',
    'can_have_sub_agent' => 1,
    'can_have_players' => 1,
    'show_bet_limit_template' => 1,
    'show_rolling_commission' => 1,
    'can_view_agents_list_and_players_list' => 1,
    'settlement_period' => 'Weekly',
    'settlement_start_day' => 'Monday'
];

$config['agency_tracking_link_bonus_rate_list'] = [
    ['start' => 'MAX_BONUS_RATE', 'end' => 1800, 'step' => -2],
    // ['start' => 1930, 'end' => 1800, 'step' => -10],
];

$config['set_language_by_subdomain'] = false;

$config['player_center_stat_server'] = 'stat.tripleonetech.net';
$config['player_center_stat_site_id'] = null;
$config['player_center_stat_script'] = <<<EOD
  var _paq = _paq || [];
  var _username='{player_center_stat_user_id}';
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//{player_center_stat_server}/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '{player_center_stat_site_id}']);
    if(_username!=''){
		_paq.push(['setUserId', _username]);
    }
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
EOD;

$config['admin_stat_site_id'] = '4';

$config['hide_select_language'] = false;
$config['real_name_reminder'] = false;
$config['player_deposit_reference_number'] = false;
$config['allow_multiplayer_to_have_existing_bank_account'] = false;
$config['european_address_format'] = false;
$config['debug_version_info'] = false;
$config['hide_bank_address_branch'] = true;

$config['deposit_list_default_page_size'] = 25;
$config['default_datatable_lengthMenu'] = [10, 25, 50, 100];
$config['default_player_report_order'] = 0;

$config['hide_cols_on_transactions'] = [10, 11, 13];
$config['mobile_random_username_size'] = 5;
$config['mobile_fake_email'] = '@fake-email.com';

$config['use_og_config_to_setup'] = false;
$config['player_upload_folder'] = "player";

$config['default_promo_cms_banner_url'] = 'promo_cms/default_promo_cms_1.jpg';
$config['use_old_affiliate_commission_formula'] = false;

$config['fixed_banner_player_url'] = '';

$config['external_login'] = false;
$config['external_login_url'] = null;
$config['external_login_username_field'] = 'loginName';
$config['external_login_password_field'] = 'pwd';
$config['external_login_captcha_field'] = 'captcha';
$config['external_proxy'] = null;
$config['maintaining_page_uri'] = '/maintain';
$config['class_pc_version_www_a_link'] = "._pc_version_www_a_link";
$config['class_mobile_version_m_a_link'] = "._mobile_version_m_a_link";
$config['class_player_center_a_link'] = "._player_center_a_link";
$config['use_new_vip_upgrade_feature'] = false;
$config['recently_played_item_display_limit'] = 12;
$config['use_new_player_center_mobile_version'] = false;
$config['legal_age'] = 18;
$config['current_php_timezone']='Asia/Hong_Kong';

$config['initial_player_center_features'] = array(
	'donot_show_registration_verify_email',
	'enabled_player_center_preloader',
	'enabled_vipsetting_birthday_bonus',
	'enable_dynamic_registration',
);

$config['initial_system_features'] = array(
	'notify_affiliate_withdraw',
	'promorules.allowed_affiliates',
	'promorules.allowed_players',
	'player_list_on_affiliate',
	'show_transactions_history_on_affiliate',
	'show_admin_support_live_chat',
	// 'transaction_request_notification',
	'affiliate_additional_domain',
	'individual_affiliate_term',
	'affiliate_source_code',
	'show_unsettle_game_logs',
	'auto_refresh_balance_on_cashier',
	'generate_player_token_login',
	'export_excel_on_queue',
	'deposit_withdraw_transfer_list_on_player_info',
	'sync_api_password_on_update',
	'show_bet_detail_on_game_logs',
	'popup_window_on_player_center_for_mobile',
	'affiliate_player_report',
	'affiliate_game_history',
	'affiliate_credit_transactions',
	'notification_promo',
	'notification_messages',
	// 'notification_local_bank',
	// 'notification_thirdparty',
	// 'notification_withdraw',
	'affiliate_monthly_earnings',
	'enable_cashback_after_withdrawal_deposit',
	'default_search_all_players',
	'enabled_refresh_message_on_player',
	'show_cashback_and_bonus_on_aff_player_report',
	'force_disable_all_promotion_dropdown_on_player_center',
	'display_referral_code_in_player_details',
	'disabled_auto_create_game_account_on_registration',
	'show_total_balance_without_pending_withdraw_request',
	'enabled_single_wallet_switch',
	'create_ag_demo',
	'create_agin_demo',
	'enabled_check_frondend_block_status',
	'enabled_switch_to_mobile_on_www',
	'show_deposit_bank_details_first',
	'summary_report_2',
	'show_deposit_bank_details',
	'hide_retype_email_field_on_registration',
	'kickout_game_when_kickout_player',
	'create_single_withdraw_condition_even_applied_promo',
	'bind_promorules_to_friend_referral',
	'match_wild_char_on_affiliate_domain',
	//'show_upload_documents',
	'enabled_change_lang_tutorial',
	'show_pending_deposit',
	'show_declined_deposit',
	'set_status_of_playerpromo_when_cancel_withdraw_condition',
	'enable_dynamic_header',
	'enabled_edit_affiliate_bank_account',
	'enable_dynamic_footer',
	//'show_pep_status',
	'display_earning_reports_schedule',
	'exporting_on_queue',
	'add_notes_for_player',
	'show_sub_total_for_game_logs_report',
    'enabled_auto_clear_withdraw_condition',
    'show_promotion_view_all'
);

$config['new_player_center_default_template'] = "stable_center2";

$config['registration_fields_default_order'] = [
    'email',
    'dialing_code',
    'contactNumber',
    'sms_verification_code',
    'imAccount',
    'imAccount2',
    'imAccount3',
    'firstName',
    'lastName',
    'gender',
    'birthdate',
    'birthplace',
    'citizenship',
    'language',
    'zipcode',
    'residentCountry',
    'region',
    'city',
    'address',
    'address2',
    'secretQuestion',
    'invitationCode',
    'affiliateCode',
    'agent_tracking_code',
    'withdrawPassword',
    'id_card_number',
    'player_preference',
    'player_preference_email',
    'player_preference_sms',
    'player_preference_phone_call',
    'player_preference_post',
    'terms',
];

$config['excluded_in_registration_settings'] = [
    'id_card_type', 'bankName', 'bankAccountNumber', 'bankAccountName', 'newsletter_subscription'
];

$config['excluded_in_account_info_settings'] = [
    'id_card_type', 'bankName', 'bankAccountNumber', 'bankAccountName', 'newsletter_subscription',
    'sms_verification_code', 'terms', 'agent_tracking_code', 'invitationCode', 'affiliateCode',
    'secretQuestion', 'residentCountry', 'id_card_number', 'withdrawPassword',
];

# Defines the page used when game is under maintenance. Disable respective game API and game will redirect to this URL.
$config['maintenance_url'] = array(
    'bbin' => '/others/maintain.html',
    '*' => '/others/maintain2.html',
);

$config['get_new_msg_interval'] = 60000;

//35.187.153.12
$config['redis_server'] = null;// ['server'=>'10.140.0.9', 'port'=>6379, 'password'=>null, 'timeout'=>1, 'retry_timeout'=>10];
$config['fake_hostname']='';

$config['debug_print_json_sql']=false;
$config['initial_collection_account_setup'] = array(
	'flag' => 1,
	'payment_account_name' => 'default collection',
	'payment_account_number' => 123456789,
	'payment_branch_name' => '',
	'max_deposit_daily' => 10000,
	'min_deposit_trans' => 100,
	'max_deposit_trans' => 1000,
);

$config['user_terms_url'] = '';
$config['privacy_policy_url'] = '';

$config['blocked_ip_for_player']=[];

$config['promotion_mock']=[
	'get_game_result_amount'=>null,
	'current_player_total_balance'=>null,
    'times_released_bonus_on_this_promo_today'=>null,
    'sum_deposit_amount'=>null,
    'sum_withdrawal_amount'=>null,
    'get_game_betting_amount'=>null,
    'get_game_betting_count'=>null,
    'count_deposit_by_date'=>null,
	'count_withdraw_by_date'=>null,
	'get_available_deposit_amount'=>null,

	'get_date_type_now'=>null,
	'get_date_type_yesterday_start'=>null,
	'get_date_type_yesterday_end'=>null,
	'get_date_type_today_start'=>null,
	'get_date_type_today_end'=>null,
	'get_date_type_last_release_bonus_time'=>null,
	'get_max_date_type'=>null,
	'get_min_date_type'=>null,
	'checkPlayerRegisteredDate'=>null,
	'is_player_filled_first_name'=>null,
	'is_player_filled_last_name'=>null,
	'is_player_at_least_one_withdrawal_bank'=>null,

	'fixed_time_deposit_transaction_id'=>null,
	'fixed_time_deposit_amount'=>null,

	'every_time_deposit_transaction_id'=>null,
	'every_time_deposit_amount'=>null,

	'not_first_time_deposit_transaction_id'=>null,
	'not_first_time_deposit_amount'=>null,

	'already_released_promo_rule'=>null,
	'exists_double_realname'=>null,
	'exists_double_ip'=>null,
	'is_player_verified_mobile'=>null,
	'sum_bonus_amount'=>null,
	'sum_bonus_amount_today'=>null,
	'is_available_vip_level'=>null,
    'get_close_loss_include_game_log_by_date'=>null,
];

$config['max_ogg_file_size']=2*1024*1024;

$config['target_banktypes_order']=[];

$config['mobile_menu_bar'] = [
    [
        'key' => 'under_home',
        'sort' => 1,
        'enable' => true,
        'site' => 'm',
        'url' => '',
        'img' => '/includes/images/under_home.png',
        'lang_code' => 'header.home'
    ],
    [
        'key' => 'under_gift',
        'sort' => 2,
        'enable' => true,
        'site' => 'm',
        'url' => '/promotions.html',
        'img' => '/includes/images/under_gift.png',
        'lang_code' => 'lang.promo'
    ],
    [
        'key' => 'under_chat',
        'sort' => 3,
        'enable' => true,
        'site' => 'player',
        'url' => 'pub/live_chat_link',
        'img' => '/includes/images/under_chat.png',
        'lang_code' => 'customer.service.mobile',
    ],
    [
        'key' => 'under_game',
        'sort' => 4,
        'enable' => true,
        'site' => 'm',
        'url' => '/slot.html',
        'img' => '/includes/images/under_games.png',
        'lang_code' => 'lang.games',
    ],
    [
        'key' => 'under_bank',
        'sort' => 5,
        'enable' => true,
        'site' => 'player',
        'url' => 'player_center2/deposit',
        'img' => '/includes/images/under_bank.png',
        'lang_code' => 'Deposit',
    ],
    [
        'key' => 'under_me',
        'sort' => 6,
        'enable' => true,
        'site' => 'player',
        'url' => 'player_center/menu',
        'img' => '/includes/images/under_me.png',
        'lang_code' => 'Player Center',
    ],
];

$config['custom_lang'] = FALSE;

$config['donot_auto_redirect_to_https_list']=['.+\\.og\\.local'];
$config['auto_redirect_to_https_list']=[];

$config['log_server_link']="http://logs.t1t.link:5601/app/kibana#/dashboard/575ef2b0-5667-11e7-8020-e143669329ac?_a=(query:(query_string:(analyze_wildcard:!t,query:'extra.tags.request_id:{{request_id}}')))";

$config['agent_max_credit_limit']='10000000000001';

//register/www/player_center/promotion/<url>
$config['redirect_agent_tracking_link_to']='register';

//affiliate_only/agent_only/both
$config['player_registration_tracking_code_mode']='affiliate_only';

$config['player_center_custom_header']='';
$config['mobile_player_center_custom_header']='';

$config['player_center_custom_footer']='';
$config['mobile_player_center_custom_footer']='';

$config['affiliate_custom_header']='';
$config['affiliate_custom_footer']='';

$config['affiliate_cron_schedule'] = '01:07:00';

$config['mobile_lgoin_redirect_url'] = '/player_center/menu';

//deposit/withdrawal/transfer
/**
 * @deprecated Change to use promotion setting. by Elvis_Chen at 2018/02/21
 */
$config['deposit_promotion_disabled_transaction_type_list']=['deposit'];

$config['deposit_secure_verify_show_payment_account_counts'] = 1; // FALSE: unlimit

$config['announcement_scrollamount']=5;
$config['announcement_mobile_scrollamount']=5;

$config['locked_ibc_game_url']='';

$config['mobile_redirection_url_prefix']='m';
$config['session_timeout'] = '-1 hours';
$config['default_auto_logout_sess_expiration'] = 30; //minute
$config['sess_expiration_use_custom_setting'] = ['ci_admin_sessions'];


$config['agency_view_template']='agency';
$config['locked_agency_template'] = NULL;

$config['agency_registration_fields']=[
	'username'=>['visible'=>true, 'required'=>true],
	'password'=>['visible'=>true, 'required'=>true],
	'email'=>['visible'=>true, 'required'=>true],
	'firstname'=>['visible'=>true, 'required'=>false],
	'lastname'=>['visible'=>true, 'required'=>false],
	'gender'=>['visible'=>false, 'required'=>false],
	'mobile'=>['visible'=>true, 'required'=>false],
	'im1'=>['visible'=>false, 'required'=>false],
	'im2'=>['visible'=>false, 'required'=>false],
	'note'=>['visible'=>true, 'required'=>false],
	'language'=>['visible'=>true, 'required'=>false],
];

$config['cust_non_lang_translation'] = [
    'Instant Message 1' => '',
    'Instant Message 2' => '',
    'Instant Message 3' => '',
];

//this is for skip isPlayerCompleteProfile field
$config['skip_fields_in_registration_fields'] = ['affiliateCode','withdrawPassword','bankName','bankAccountNumber','bankAccountName'];

//this is for side content for registration template 1
$config['registration_template1_content'] = [
					'Your information is safe with our high-level security structure.',
					'Fast deposit and withdrawal processing',
					'Up to 100% Free Bonus',
					'Join us now and enjoy various promotions and exclusive benefits!'
				];

$config['how_many_hours_from_utc']=8;

//this is for target function lost for auto generate kyc
$config['kyc_target_function_list'] = [
					'player_no_attached_document' => ['lang_format' => "target_function_5"],
					'player_depositor' => ['lang_format' => "target_function_1"],
					'player_identity_verification' => ['lang_format' => "target_function_2"],
					'player_valid_documents' => ['lang_format' => "target_function_3"],
					'player_valid_identity_and_proof_of_address' => ['lang_format' => "target_function_4"],
					'player_valid_proof_of_income' => ['lang_format' => "target_function_6"],
				];

$config['custom_promo_www_url']= [
        'site' => 'player',
        'url' => 'player_center2/promotion'
    ];

$config['verification'] = [
        "no_attach" => [
                'description' => 'No Attachment',
            ],
        "wrong_attach" => [
                'description' => 'Wrong Attachment',
            ],
        "verified" => [
                'description' => 'Verified',
            ],
        "not_verified" => [
                'description' => 'Not Verified',
            ]
    ];

$config['proof_attachment_type'] = [
    "photo_id" => [
        'description' => 'Photo ID',
        'tag' => 'photo_id'
    ],
    "proof_of_address" => [
        'description' => 'Proof of address',
        'tag' => 'address'
    ],
    "proof_of_income" => [
        'description' => 'Proof of source of wealth',
        'tag' => 'income'
    ],
    "proof_of_dep_wd" => [
        'description' => 'Proof of deposit and withdrawal',
        'tag' => 'dep_wd'
    ]
];

$config['allowed_upload_file'] = "jpg|jpeg|png|gif|PNG";
$config['password_adjust_game_report']='575sdfW$ef2b0';


$config['game_logs_hidden_cols'] =[18];
$config['game_logs_hidden_cols_for_aff'] =[2, 18];

$config['game_reports_hidden_cols'] =[];
$config['game_logs_show_row_colors'] = true;

$config['cloudflare_email']='';
$config['cloudflare_api_key']='';

$config['cloudflare_default_source_ip']='';

$config['cloudflare_t1t_email']='';
$config['cloudflare_t1t_api_key']='';
$config['cloudflare_t1t_games_zone_id']='a8ae652430713cdc5c40a2244a40ea57';

$config['id_card_number_validator'] = [
            "char_lenght" => 18
        ];
$config['currency_api_access_key']='af3d77556e504b24910d7a2bacbb0aaa';
$config['currency_api_url']='http://apilayer.net/api/live';
$config['currency_api_historical']='http://apilayer.net/api/historical';
$config['currency_api_currencies']='CNY';
$config['currency_api_source']='USD';

// skip the captcha validation for a given user used by test automation script
$config['is_auto_machine_user_list'] = [
	'testauto', 'testauto2', 'testauto3'
];

//$config['adjust_cashback_by_odds']=['api'=>[IBC_API, PINNACLE_API], 'hk_max_odds'=>0.5, 'eu_max_odds'=>1.5];
$config['adjust_cashback_by_odds']= [];
//$config['pinnacle_odds_format'] = [1 => 'EU', 2 => 'HK'];  // odds format from pinnacle logs

$config['memcached'] = array(
	'hostname' => '10.4.196.17',
	'port'        => 11211,
	'weight'    => 1
);

$config['local_lock_servers'] = [
	['10.4.196.17', 6379, 0.01],
	// ['127.0.0.1', 6389, 0.01],
	// ['127.0.0.1', 6399, 0.01],
];

$config['prefix_website_list']=['player', 'www', 'admin', 'aff', 'agency', 'm', 'pay', 'pay2', 'l8b8mlgb8', 'yjyladmin171120', 't4j4cbk6'];
$config['prefix_admin_domains']=['admin', 'l8b8mlgb8', 'yjyladmin171120', 't4j4cbk6'];

/**
 * Unable to resolve the problem definition, do not use
 *
 * by Elvis
 */
$config['list_of_ips_to_be_excluded_in_duplicate_account'] = [];

/**
 * The item in this array will be calculate
 * Can set column : ["ip","realname","password","email","mobile","address","country","city","cookie","referrer","device"]
 */
$config['duplicate_account_info_enalbed_condition'] = array("ip", "password");

/**
 * Calculate duplicate account reports within a config days
 */
$config['duplicate_account_calc_days'] = 30;

/**
 * Ignore specified IP to avoid duplicate accounts
 */
$config['skip_save_http_request_from_ip_list'] = [
    "104.155.216.142",
    "35.187.147.215",
    "35.185.156.66",
    "180.232.149.98",
    "119.9.106.90",
    "220.135.118.23",
    "180.232.78.2",
    "119.9.106.90",     //PC-SAFE-BROWSER
    "106.75.166.72",    //PC-SAFE-BROWSER
    "35.201.204.2",     //PC-SAFE-BROWSER
    "106.75.166.72",    //MB-SAFE-BROWSER
    "35.201.204.2",     //MB-SAFE-BROWSER
];

$config['password_nginx_stats']='575sdfWsdfsj234ILLef2b0';

//for deposit list in player information and payment management
$config['deposit_list_columnDefs'] = [
	"not_visible_payment_management" => [  8, 12, 14, 17, 22, 23 ],
	"not_visible_player_information" => [ 0, 3, 7, 8, 9, 12, 14, 16, 17, 18, 19, 20, 21, 22, 23],
	"className_text-right_payment_management" => [ 10 ],
	"className_text-right_player_information" => [ 10 ],
];

// for withdrawal list in player information and payment management
$config['withdrawal_list_columnDefs'] = [
	"not_visible_payment_management" => [ 19, 20, 22, 25, 30, 32 ],
	"not_visible_player_information" => [ 2, 3, 8, 9, 10, 18, 19, 20, 21, 24, 29, 30, 31 ],
    "className_text-right_payment_management" => [ 8, 12, 29 ],
	"className_text-right_player_information" => [ 7, 11, 28 ],
];

$config['web_user_terms_url'] = 'aboutus.html';
$config['web_privacy_policy_url'] = 'aboutus.html';

$config['log_use_elasticsearch']=false;

//Mode of Deposit for manual Deposit
$config['mode_of_deposit'] = ['internet_banking','over_the_counter_deposit','atm_transfer','mobile_banking','cash_deposit_machine','wechat','alipay','tenpay','qqpay'];

$config['write_response_result_to_dir']=null;

$config['mobile_player_center_custom_buttons']=null;

$config['always_delete_total_tmp_shell']=true;

$config['game_list_api_url'] = "http://admin.gamegateway.t1t.games";
$config['game_list_image_path_url'] = "http://www.gamegateway.t1t.games";

$config['export_data_white_functions']=[
	'admin'=>['player_reports', 'player_list_reports', 'transaction_details', 'promotion_report',
		'duplicate_account_report', 'game_report', 'game_report_results', 'active_player_report_results',
		'cashback_report', 'affiliate_statistics', 'affiliate_earnings', 'aff_list', 'payment_report', 'gameDescriptionList',
		'queue', 'check_queue', 'depositList', 'withdrawList', 'promoApplicationList', 'smsVerificationCodeReport', 'withdrawCheckingReport', 'depositCheckingReport',
		'affiliateTag', 'gamesHistory', 'affiliatePayment', 'taggedPlayers', 'playerTagManagement',
		'batchCreate', 'friendReferral', 'bankPaymentList', 'transferRequest', 'agency_logs',
		'credit_transactions', 'agent_list', 'structure_list', 'paymentAPI', 'getAllGameType', 'userLogs',
		'viewRoles', 'countryRules', 'exception_order_list', 'export_vip_setting_list', 'export_collection_account',
		'export_task_list', 'export_response_result_list','export_super_player_report','export_super_summary_report','export_super_game_report',
		'export_super_payment_report','export_super_promotion_report','export_super_cashback_report','gamesHistoryV2',
		'agency_game_reports','adminusers_results', 'grade_report', 'exportResponsibleGamingReport', 'playertaggedlist',
        'agency_player_reports', 'player_analysis_report','agency_settlement_list_wl','payment_status_history_report','kingrichApiResponseLogs',
        'exportCommunicationPreferenceReport','exportIncomeAccessSignupReports', 'exportIncomeAccessSalesReports','kingrich_summary_report','stop_queue',
        'player_daily_balance', 'player_realtime_balance', 'player_reports_2', 'exportPlayerAttachmentFileList',
        'kingrich_scheduler_report','kingrich_scheduler_summary_logs','kyc_c6_acuris_by_player_report','conversion_rate_report','gameApiUpdateHistory'],
	'aff'=>['export_game_report_from_aff', 'traffic_statistics_aff', 'queue', 'check_queue','game_report_results','export_game_report_from_aff','stop_queue','gameApiUpdateHistory'],
	'agency'=>['agency_player_reports', 'agency_agent_reports', 'agency_game_reports', 'agency_settlement_list',
		'agency_invoice', 'create_invoice_name','agency_settlement_list_wl','queue', 'check_queue','gamesHistory','stop_queue'],
];

$config['export_data_remote_functions']=[
       'admin' => ['gamesHistory','player_list_reports','transaction_details','depositList','withdrawList','transferRequest','gameReports','payment_report','promotionReport','cashbackReport','playerGradeReports','promoApplicationList','aff_list','aff_daily_earnings','aff_monthly_earnings','player_reports', 'player_daily_balance', 'player_reports_2','gamesHistoryV2','affiliateStatistics','gameApiUpdateHistory','task_list','withdrawCheckingReport','depositCheckingReport'],
       'aff' => ['gameReports'],
       'agency' =>['gamesHistory'],
];

$config['use_export_csv_with_progress']=[
       'admin' => ['gamesHistory','player_list_reports','transaction_details','depositList','withdrawList','transferRequest','gameReports','payment_report','promotionReport','cashbackReport','playerGradeReports','promoApplicationList','aff_list','aff_daily_earnings','aff_monthly_earnings','getUserLogs','player_reports', 'player_daily_balance', 'player_reports_2','gamesHistoryV2','affiliateStatistics','gameApiUpdateHistory','task_list','withdrawCheckingReport','depositCheckingReport'],
       'aff' => ['gameReports'],
       'agency' =>['gamesHistory'],
];

$config['type_of_id_card']=[
	'passport' => '_json:{"1":"Passport","2":"护照","3":"Passport","4":"Passport","5":"Passport"}',
	'drivers_license' => '_json:{"1":"Drivers License","2":"驾照","3":"Drivers License","4":"Drivers License","5":"Drivers License"}'
];

$config['cookie_name_lang'] = "_lang";

$config['default_admin_usernames'] = ['admin','superadmin'];

$config['logger_file_list']=[
 //    'sync_totals' => BASEPATH.'/../application/logs/sync_totals.log',
	// 'sync_game_records'=> BASEPATH.'/../application/logs/sync_game_records.log',
	// 'sync_balance'=> BASEPATH.'/../application/logs/sync_balance.log',
	// 'sync_t1_gamegateway'=> BASEPATH.'/../application/logs/sync_t1_gamegateway.log',
	// 'batch_scan_suspicious_transfer_request_cronjob'=>BASEPATH.'/../application/logs/batch_scan_suspicious_transfer_request_cronjob.log',
	// 'generate_player_report_hourly' => BASEPATH.'/../application/logs/generate_player_report_hourly.log',
    // 'generate_admin_dashboard' => BASEPATH.'/../application/logs/generate_admin_dashboard.log',
    // 'rebuild_totals'=> BASEPATH.'/../application/logs/rebuild_totals.log',
    // 'command' => BASEPATH.'/../application/logs/command.log',
    'queue_server' => BASEPATH.'/../application/logs/queue_server.log',
];

$config['adjust_datetime_minutes_sync_totals']='-60 minutes';

$config['SHARING_PRIVATE_PATH'] = realpath(APPPATH . '/../../sharing_private');

$config['player_report_dt_config'] = [

	"text_right_targets" => ['total-deposit-bonus', 'total-cashback-bonus','total-referral-bonus', 'manual-bonus', 'subtract-bonus', 'total-bonus','total-first-deposit','total-second-deposit', 'total-deposit', 'deposit-times','total-withdrawal','deposit-withdraw','deposit-minus-withdrawal','total-bets','payout','payout-rate',
                    'deposit-and-bonus', 'bonus-over-deposit', 'deposit-minus-withdrawal', 'withdrawal-over-deposit', 'turn-around-time', 'total-win', 'total-loss','total-revenue'],
	"hidden_cols_targets" => ['realname', 'affliate', 'email', 'phone', 'registered-ip', 'last-login-ip', 'last-login-date','last-logout-date','register-date'],
	"disable_cols_order_target" => ['bet-details'] ,
	'default_export_cols' => ['username','risk_level','kyc_level','total_bets','total_payout','payout_rate']

];

$config['currency_list_super_report'] = [
                                            'CNY' => ['label-color' => 'label-warning'],
                                            'IDR' => ['label-color' => 'label-primary'],
                                            'VND' => ['label-color' => 'label-success'],
                                        ];

$config['game_with_no_free_spin'] =  [];

$config['app_api_key'] = 'cdf55701104c0936c6c03d30cd567332';

//default is 60
$config['adjust_sync_datetime']='30';
$config['mattermost_channels'] = [
	'manual_sync' => '',
	'qa_test_players' => '',
	'dev_test_players' => '',
	'itdesk_test_players' => '',
	'auto_test_players' => '',
	't1_players_master' => '',
	'game_list_update' => '',
	'test_mattermost_notif' => '',
	'private_ip_alert' => '',
	'delete_table_data'=> '',
	'system_alert' => '',
	'gw001_gamelogs_export' => '',
	'gw001_sql_export_and_delete' => '',
	'payment_alert' => '',
	'clients_test_players' => '',
];

$config['sbe_default_theme'] = 'yeti';

$config['sbe_contact_info'] = [
    'company_title' => 'TripleOnetech',
    'email' => 'sales@tot.bet',
    'skype' => NULL,
    'website' => [
        'display_name' => "www.tot.bet",
        'url' => "https://www.tot.bet"
    ]
];

//default getSecureId random length
$config['get_secureid_random_length'] = '5';

$config['minify_setting'] = [
	'admin'  => [],
	'player' => [
		'css' => [
			'stable_center2/style.css',
			'stable_center2/styles/base-theme-blue.css',
			'stable_center2/styles/base-theme-green.css',
			'stable_center2/styles/base-theme-lottery.css',
			'stable_center2/styles/base-theme-orange.css',
			'stable_center2/styles/base-theme-purple.css',
			'stable_center2/styles/base-theme-red.css',
			'stable_center2/css/style-mobile-default.css'
		],
		'js'  =>[
			'resources/js/validator.js',
            'common/js/main.js',
            'common/js/main-mobile.js',
            'stable_center2/js/template-script.js',
            'stable_center2/js/template-script-mobile.js'
		]
	]
];

# white list for friend referral cashback
# only referrer will pay.
//$config['allowed_player_for_referral_cashback'] = ['test002','testzai01'];
$config['allowed_player_for_referral_cashback'] = [];
$config['call_gamegateway_api_url']='http://admin.og.local';

//update_status, auto_fix, none
$config['action_of_search_suspicious_transfer_request']='update_status';
$config['registered_image_poup_path'] = 'images/registered-popup.png';



$config['sync_table_receiver_by_date'] = [
  //'newrainbow' => 'http://player.newrainbow.t1t.games/api/sync_table_receiver_by_date/'
  //'newrainbow' => 'http://player.og.local/api/sync_table_receiver_by_date/'
];
$config['sync_table_receiver_by_id'] = [
  //'newrainbow' => 'http://player.newrainbow.t1t.games/api/sync_table_receiver_by_id/'
  //'newrainbow' => 'http://player.og.local/api/sync_table_receiver_by_id/'
];


$config['sync_to_remote_batch_tables'] = [

	// 'newrainbow' => [
	// 	'hourly' => [
	// 		'game_logs' => ['sync_by' => 'date' , 'step_time_interval'=> 30, 'date_field' => 'start_at', 'id_field' => 'id'],
	// 		'total_player_game_hour' => ['sync_by' => 'date','unique_id_field' => 'uniqueid', 'step_time_interval'=> 30, 'date_field' => 'date_hour', 'id_field' => 'id','format_date_by' => 'date_hour'],
	// 		'player' => ['sync_by' => 'date', 'step_time_interval'=> 30, 'date_field' => 'createdOn', 'id_field' => 'playerId' ],
	// 		'playeraccount' =>  ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'playerAccountId' ],
	// 		'tag'=> ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'tagId' ],
	// 		'playertag' => ['sync_by' => 'id', 'row_per_page'=> 50, 'id_field' => 'playerTagId' ],
	// 		'adminusers'=> ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'userId' ],
	// 	],
	// 	'daily' => [
 //                //'tag'=> ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'tagId' ],
 //                //'playertag' => ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'playerTagId' ],
	// 		'playerdetails' => ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'playerDetailsId' ],
	// 		'game_type' => ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'id' ],
	// 		'game_description' => ['sync_by' => 'id', 'row_per_page'=> 30, 'id_field' => 'id' ]
	// 	],
	// 	'weekly' =>[
	// 		'external_system'=> ['sync_by' => 'id', 'row_per_page'=> 10, 'id_field' => 'id' ]
	// 	]
	// ]

 //    ,
	// 'other' =>[]
];

$config['overview'] = [
    // null => not limited, or define list
    'today_total_betting_platforms' => null
];

$config['api_not_allowed_cents'] = [BBIN_API,AGSHABA_API,EBET_BBTECH_API,EBET_GGFISHING_API,EBET_IMPT_API,
		EBET_OPUS_API,EBET_SPADE_GAMING_API,EBET_BBIN_API,GSAG_API,MWG_API,YUNGU_GAME_API, AB_API];

# use for force testing error message
$config['player_for_testing_custom_error_message'] = 'testmaria';

$config['IM_list'] = ['_json:{"1":"Weixin","2":"Weixin"}', '_json:{"1":"QQ","2":"QQ"}', '_json:{"1":"Line","2":"Line"}', '_json:{"1":"Skype","2":"Skype"}', '_json:{"1":"Other IM","2":"其他IM"}'];

$config['register_mobile_number_regex'] = NULL; # Example, China mobile number: '^(13[0-9]|14[579]|15[0-3,5-9]|17[0135678]|18[0-9])\d{8}$'

$config['shorturl'] = [
    'curl' => [
        'timeout_second' => 10,
        'connect_timeout' => 5,
        'skip_ssl_verify' => TRUE
    ],
    'use_service' => 'ft12'
];

$config['top_agent_config'] = [
    'tracking_code' => '_top_agent_0_',
    'bonus_rate' => 1980,
    'rebate_rate' => 0,
    'player_type' => AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT,
    'agent_template_id' => NULL,
];

# add withdrawal api id to show up "Check Withdraw Status" button
$config['enabledCheckWithdrawalStatusIdList'] = ['9997','99', '375', '384', '385', '401', '494', '555', '572', '600', '624', '665', '719', '810', '816', '933', '982', '5002', '5076', '5208', '5220', '5224', '5346', '5385', '5386', '5405', '5413', '5472', '5524'];

# add Deposit api id to show up "Check Deposit Status" button
$config['enabledCheckDepositStatusIdList'] = ['560', '561', '5134', '5145', '5191', '5292'];

$config['t1admin_username_list']=['superadmin', 't1_qaadmin', 't1_autoadmin1', 't1_autoadmin2','t1_qatest','t1_qatest2','t1_qatest3','t1_qamanager1','t1_qamanager2'];

$config['t1_username_list']=['superadmin', 't1_qaadmin', 't1_qatest', 't1_qatest2', 't1_manualqa1', 't1_manualqa2', 't1_manualqa3',
	't1_reporting', 't1_designer', 't1_designer2', 't1_designer3', 't1lottery_master', 't1_autoadmin1', 't1_autoadmin2','t1_qamanager1','t1_qamanager2'];

$config['authorized_view_payment_secret_list'] = ['superadmin'];
$config['game_history_override_handicap_if_zero_platform_ids'] = array();
//30000=30seconds
$config['refresh_balance_interval_millisecond']=30000;

$config['agency_contact_email'] = 'agency@tripleonetech.com';
$config['agency_contact_qq'] = '';
$config['agency_contact_skype'] = '';
$config['agency_contact_type'] = '';
$config['agency_contact_type_label'] = 'QQ联系';

$config['t1games_using_external_game_id'] = [MG_API,ISB_API,GD_API,SUNCITY_API,KYCARD_API];

$config['auto_refresh_cold_down_time_milliseconds']=5000;

$config['turn_off_batch_sync_balance_all']=true;

// -- IOM -- Communication Preferences

$config['communication_preferences'] = array('email' 		=> '_json:{"1":"Email","2":"Email","3":"Email","4":"Email","5":"Email","6":"Email"}',
											 'sms'   		=> '_json:{"1":"SMS","2":"SMS","3":"SMS","4":"SMS","5":"SMS","6":"SMS"}',
											 'phone_call'   => '_json:{"1":"Phone Call","2":"Phone Call","3":"Phone Call","4":"Phone Call","5":"Phone Call","6":"Phone Call"}',
											 'post'   		=> '_json:{"1":"Post","2":"Post","3":"Post","4":"Post","5":"Post","6":"Post"}',
											);

$config['sync_t1_sleep_seconds']=10;


$config['alwaysEnableQueue']=false;
//call unsettle t1 api
$config['enable_sync_t1_unsettle']=true;
$config['enabled_sync_t1_quickly']=false;
//process game_logs_unsettle: processUnsettleGameLogs
$config['enabled_game_logs_unsettle']=true;

$config['always_load_system_feature_to_cache']=true;

//live database is 1, staging database is 2
//35.187.153.12
$config['queue_redis_server'] = null;
// ['server'=>'10.140.0.9', 'port'=>6379, 'password'=>null, 'timeout'=>1, 'retry_timeout'=>10, 'password'=>null, 'database'=>null];
$config['queue_redis_pop_timeout'] = 600;

$config['rabbitmq_server'] = ['host'=>'10.4.196.17', 'port'=>5672, 'username'=>'php', 'password'=>'php']

$config['debug_preprocess_js']=false;

$config['debug_last_version_key']=null;

$config['player_auto_lock_password_failed_attempt'] = 3;


// -- INCOME ACCRESS
$config['ia_sftp_hostname'] = '';
$config['ia_sftp_port'] = '';
$config['ia_sftp_username'] = '';
$config['ia_sftp_password'] = '';


$config['ia_daily_signup_filename_prefix'] = '';
$config['ia_daily_signup_filepath'] = 'Downloads';
$config['ia_daily_signup_file_extension'] = '.csv';
$config['ia_daily_signup_filename_prefix'] = '';

$config['ia_daily_signup_csv_headers'] = array('ACCOUNT_OPENING_DATE', 'BTAG', 'PLAYER_ID', 'USERNAME', 'PLAYER_COUNTRY');


$config['ia_daily_sales_filename_prefix'] = '';
$config['ia_daily_sales_filepath'] = 'Sales';
$config['ia_daily_sales_file_extension'] = '.csv';
$config['ia_daily_sales_csv_headers'] = array('TRANSACTION_DATE','PLAYER_ID','BTAG','PLAYER_COUNTRY','DEPOSITS','CHARGEBACKS','CASINO_BETS','CASINO_REVENUE','CASINO_STAKE','CASINO_BONUS','SPORTSBOOK_BETS','SPORTSBOOK_REVENUE','SPORTSBOOK_STAKE','SPORTSBOOK_BONUS');
$config['ia_daily_sales_filename_prefix'] = '';
$config['ia_daily_sales_products'] = array('CASINO' => 'live_dealer,slots', 'SPORTSBOOK' => 'sports');

$config['enable_change_player_status_pep_authentication'] = false;

$config['manually_deposit_cool_down_minutes'] = 10;

$config['default_importer']='importer_standard';

//enable this to allow admin user to manually adjust the score in pep even it's link in id3
$config['enable_change_player_pep_status_when_binding_ID3'] = false;

$config['deposit_limits_day_options'] = ['1','7','14','30'];
$config['wagering_limits_day_options'] = ['1','7','14','30'];

$config['comapi_getPlayerPasswordPlain_max_time_diff_allowed'] = 60;
$config['SHARING_UPLOAD_PATH'] = realpath(APPPATH . '/../sharing_upload/');


/**
 * NEW SMTP API CONFIGURATIONS
 */
$config['enable_smtp_api'] = false;
$config['current_smtp_api'] = null;
$config['smtp_api_info'] = array();

$config['dt_use_fetch_all_on_csv_export'] = true;

$config['enabled_t1_directly_sync_api_list']=[];
$config['enabled_additional_error_log_file']=true;

$config['disable_cache']=false;

$config['adjust_datetime_minutes_sync_t1_gamegateway']=30;
$config['adjust_datetime_minutes_sync_t1_gamegateway_stream']=0;
$config['always_manually_sync_t1_gamegateway']=false;
$config['enabled_sync_t1_gamegateway_stream']=false;
$config['gamegateway_stream_query_max_limit_seconds']=60;

$config['enabled_rabbitmq_logger']=false;
$config['enabled_redis_logger']=true;

$config['log_recorder_path']=null;

$config['apis_with_bet_limit'] = [EBET_API, EBET2_API];

//call_socks5_proxy, call_http_proxy_host, call_http_proxy_port, ignore_ssl_verify
$config['external_login_settings']=null;
$config['external_login_api_class']=null;

$config['ignore_auto_add_new_game_to_vip_cashback']=false;
$config['manual_game_list_sync']=false;
$config['use_custom_im_account_fields']=false;

$config['get_only_success_grade_report'] = true;
$config['show_non_active_game_api_game_list'] = false;

// This config is for totbet agent master key
$config['agent_master_key'] = "";

$config['monitor_cache_key']=null;
// This config is for to recalculate api by settled time
$config['api_array_when_calc_cashback_by_settled_time'] = [];
// This is for OGLANG-191
$config['contact_number_note'] = false;
$config['allow_batch_game_list_update'] = false;

$config['player_notification_display_time'] = 5;

/**
 * This defines how long can the date range be set when a user searches for game logs.
 * By default, the Marketing/Reports Management Tab > Game Logs can only be searched with a date range not exceeding 2 days.
 * 		- If a player username was supplied in the report's filter, the date range will be allowed to a maximum of 31 days.
 * By default, the Player User Information > Game History can only be searched with a date range not exceeding 7 days.
 * By default, the Affiliate BO > Game History can only be searched with a date range not exceeding 2 days.
 * By default, the Agency BO > Game History can only be searched with a date range not exceeding 2 days.
 *
 * The value is expected to be a positive integer.
 *
 * Set all to null / false to disable restriction
 * OGP-9224 && OGP-9485 && OGP-9647
 */
$config['game_logs_report_date_range_restriction'] = 2;
$config['game_logs_report_with_username_date_range_restriction'] = 31;
$config['player_game_history_date_range_restriction'] = 7;
$config['affiliate_game_logs_report_date_range_restriction'] = 2;
$config['agency_game_logs_report_date_range_restriction'] = 2;

//this config to allow to display oneworks game report
$config['show_oneworks_report'] = false;

$config['monitor_operator_settings']=[];
$config['affiliate_external_login_api_class']=null;
$config['affiliate_external_login_settings']=null;

$config['daily_player_balance_report_limit']=1000;
$config['exempted_game_type_codes_for_sync']=['tip','yoplay'];
$config['is_vue_dev'] = false;
$config['game_provider_with_game_list_api'] = [
	QT_API=>['api_auto_update'=>0],
	PRAGMATICPLAY_API=>['api_auto_update'=>0],
	LIVEGAMING_API=>['api_auto_update'=>0],
];
$config['billing_day']=15;
$config['remote_batch_add_cashback_bonus_max_error_stop'] = 100;

$config['multiple_currency_enabled'] = false;
$config['kyc_limit_of_upload_attachment'] = 8;
$config['player_kyc_access_key'] = '';
$config['player_kyc_valid_time_interval'] = 60; // please make sure this num can divide 60

$config['default_super_settings']=[
	'pageSizeList'=>[10, 25, 50, 100, 500],
	'showSubTotal'=>true,
];
//summary2=>['page_size_list']
$config['super_report_settings']=[];
$config['debug_report_mode']=false;
$config['remote_log_viewing_file_size_limit'] = 10485760; // -- 10mb

$config['management_logs_list'] = ['player_management','user_management','Player Management','payment_management','Payment Management','marketing_management','cms_management','payment_account_management','system_management','cs_management','report_management','theme_management','System Management','Marketing Management','country_rules_management','affiliate_management','vipsetting_management','notification_management','livechat_management','ip_management','role_management','Added IP','Theme Management','VIP Setting Management','agency_management','Add CMS Promo','CMS Promo Management','Marketing Promo Management','Withdraw Condition','Payment Account Management','CashBack Period Setting Management','config_management','CMS Promo Setting Management','CMS Management','Block IP','Delete IP','game_api','Close Message','payment_api','Unlock Tracking Code','Add Domain','Deactivate Domain','Affiliate Tag','Affiliate Management','Affiliate Banner','Delete Domain','Edit Domain','已添加IP','Edit CMS Email Manager','Delete Banner','Enable IP Whitelisting','cmsbanner_management','Reset Password of Affiliate','Add notes to affiliate','dispatch_account_management','Reply Message','Dispatch Account Management','Edit IP','testing_lib_game_rwb_api','Disable IP Whitelisting','Allow IP','Activate Affiliates','export_data'];


$config['enable_kingrich_gametypes_new'] = false;

// -- OGP-10398 | player_model > syncAllPlayersOnlineStatus > chunk limit
$config['sync_all_player_online_status_chunk_limit'] = 100;

// -- OGP-10685 | player_model > syncAllPlayersSummary > chunk limit
$config['sync_all_player_withdraw_deposit_totals_chunk_limit'] = 100;

$config['enabled_remote_async_event']=true;
// -- OGLANG-385 | if set to TRUE, this will use the language full_custom_affiliate_message_after_registration for the message upon affiliate registration
$config['full_custom_affiliate_message_after_registration'] = FALSE;

/* Request Form - start <editor-fold>{{{ */
$config['message_request_form_default_allow_submit_times'] = 5;
$config['message_request_form_default_subject'] = 'message.request_form.default_subject';
/* Request Form - end }}}</editor-fold> */

// -- OGP-10691 | set of languages visible under langauge selections
// -- currently applied under all affiliate pages
$config['visible_options_under_language_selection'] = [1,2,3,4,5,6];

$config['pushover_send_api_url']='https://api.pushover.net/1/messages.json';
$config['pushover_default_priority']=1; //high
$config['pushover_user']=null;
$config['pushover_token']=null;
$config['monitor_event_timeout_seconds']=120;
$config['hb_common_apis'] = [HB_API,HB_IDR1_API,HB_IDR2_API,HB_IDR3_API,HB_IDR4_API,HB_THB2_API,HB_VND2_API,HB_CNY2_API,HB_MYR2_API,HB_THB1_API,HB_VND1_API,HB_CNY1_API,HB_MYR1_API,HB_IDR5_API,HB_IDR6_API,HB_IDR7_API,HB_VND3_API];
$config['exists_incomplete_game_api_list']=[HB_IDR1_API,HB_IDR2_API,HB_IDR3_API,HB_IDR4_API,HB_THB2_API,HB_VND2_API,HB_CNY2_API,HB_MYR2_API,HB_THB1_API,HB_VND1_API,HB_CNY1_API,HB_MYR1_API,HB_IDR5_API,HB_IDR6_API,HB_IDR7_API];
$config['isb_common_apis'] = [ISB_IDR1_API,ISB_IDR2_API,ISB_IDR3_API,ISB_IDR4_API,
							  ISB_IDR5_API,ISB_IDR6_API,ISB_IDR7_API,
							  ISB_THB1_API,ISB_THB2_API,ISB_THB3_API,ISB_THB4_API,
							  ISB_VND1_API,ISB_VND2_API,ISB_VND3_API,ISB_VND4_API,ISB_VND5_API,
							  ISB_CNY1_API,ISB_CNY2_API,ISB_CNY3_API,ISB_CNY4_API,
							  ISB_MYR1_API,ISB_MYR2_API,ISB_MYR3_API,ISB_MYR4_API,
							 ];


$config['tracking_script_with_domain'] = [
    'default' => [
        'player' => [
            'gtm' => [
                'header' => null,
                'footer' => null,
            ],
            'ga' => null
        ],
        'player_mobile' => [
            'gtm' => [
                'header' => null,
                'footer' => null,
            ],
            'ga' => null
        ],
        'aff' => [
            'gtm' => [
                'header' => null,
                'footer' => null,
            ],
            'ga' => null
        ],
        'agency' => [
            'gtm' => [
                'header' => null,
                'footer' => null,
            ],
            'ga' => null
        ],
    ]
];

//testing_url,merchant_code,merchant_prefix,add_prefix_to_player,secure_key,sign_key,currency
$config['gamegateway_testing_merchant']=[];
$config['gamegateway_testing_merchant_group']=[];

//max is 10m
$config['max_allow_response_content']=10*1024*1024;
$config['trim_game_name_on_gamelist_api'] = false;

$config['enable_gamegateway_api'] = false;
$config['otp_settings']=[
	'title'=>'Admin',
	'discrepancy'=>10,
];
$config['external_otp_api_class']='otp_api_google_auth';
//max_compatibility or min_compatibility,
//min_compatibility=check every ip in white ip list from x-forwarded-for
//max_compatibility=only check last ip from x-forwarded-for
$config['admin_white_ip_list_mode']='min_compatibility';
//proxy or cloudflare
$config['default_white_ip_list']=[
	//cloudflare
	'173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22', '141.101.64.0/18',
	'108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20', '197.234.240.0/22', '198.41.128.0/17',
	'162.158.0.0/15', '104.16.0.0/12', '172.64.0.0/13', '131.0.72.0/22',
	//azure2
	'40.83.74.128', '52.229.200.53', '104.43.21.44', '137.116.133.89',
	//rackspace
	'119.9.91.222',
	//google hk
	'34.92.90.235', '40.83.74.128',
	//kgvipenproxysql
	'35.189.109.164',
	//shen168twproxy
	'34.80.150.13',
	//aliyunproxy4
	'47.88.157.195',
	'47.88.175.77',
     //t1tazuresgproxy
	'104.43.21.44',
	'137.116.133.89',
];

$config['retain_vip_grade_report_failed_data_for_specific_days'] = 7;

$config['enable_sync_afterbalance_for_gameplatform'] = [];

$config['kingrich_scheduler_status'] = [
	        '1' => [
		            'label' => 'Pending'
		        ],
	        '2' => [
		            'label' => 'On-going'
		        ],
	        '3' => [
		            'label' => 'Paused'
		        ],
	        '4' => [
		            'label' => 'Stopped'
		        ],
	        '5' => [
		            'label' => 'Done'
		        ],
];
$config['use_new_china_ip_lib']=true;
$config['lock_when_create_player_account_on_gamegateway_api']=false;

$config['disable_player_register_and_redirect_to_login']=false;
$config['allow_to_create_or_update_game_api_by_api']=false;
$config['update_total_deposit_when_create_transactions']=true;
$config['record_balance_history_when_create_transactions']=true;
$config['peronal_information_custom_key_code'] = array(48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 187, 189); //0~9,-,+
$config['allow_to_create_or_update_agent_by_api']=false;
$config['lock_rebuild_reports_range']= [/*'no'=>45, 'time_unit' => 'days|months'*/];
$config['allow_to_update_game_list_by_api']=false;
$config['game_allowed_multiple_login'] = [ONEWORKS_API,PT_API];

$config['enabled_service_status_checker']=false;
$config['print_insert_update_row_for_debug_merge_logs']=false;
$config['enabled_dynamic_class']=true;
$config['enabled_cache_dynamic_class']=true;
$config['password_reset_code_expire_mins'] = 30;
$config['verify_link_expire_mins'] = 720;

$config['table_export_sql_and_deletions_settings'] = [
	// 'client'=>'GW001_table_export_sql_deletions',
	// 'client_url' => 'http://admin.idngame.t1t.in',
	// 'mattermost_key'=>'testnotif',
	// 'sql_back_up_path' => '/backup_db/idngame_export_sqls' // if it is customize | or null
	// 'settings'=>[ 						   // array of tables can be used but it will be queued
	// 	'game_logs'=> [                        // target table to back up delete
	// 		'dry_run'=>false,			       // will run export sql but it will not delete
	// 		'target_db_keys' => ['idr'],       // if multi db if not leave it empty
	// 		'date_field' => 'end_at',          // target date field - table datefield basis
	// 		'number_range' => 45,              // number where you can delete depends on delete_by
	// 		'delete_by'=> 'by_days' ,          // depends on number_range ex. by_days or by_months
	// 		'delete_step'=> '1 minutes',       // steps in deleting ex. 1 minutes |  1 hours | 1 days
	// 		'delete_data_after_backup'=>true,  // if  want to delete at the same time w/ export sql | it is like dry_run
	// 		'only_start_at_max_day' => false,  // if want to start at max day or next 2nd range
	// 		'overwrite_file' => true,          // if want to overrite exported file but if it is deleted already just set to false
	// 		'export_sleep' => 0,               // just in case sleep is needed on export
	// 		'delete_sleep' => 0,               // just in case sleep is needed on delete
	// 		'delete_last_month_dir_not_in_range' => true, //if want to delete old 2nd range -old month
	// 	],
	// ]
];

$config['table_archive_and_deletions_settings'] = [
	// 'client'=>'OLE77cn_table_archive_delete',
	// 'client_url' => 'http://admin.staging.ole777cn.t1t.in',
	// 'mattermost_key'=>'testnotif',
	// 'settings'=>[									 // array of tables can be used but it will be queued
	// 	'game_logs'=> [							     // will run export sql but it will not delete
	// 		'dry_run'=>false,						 // target table to back up delete
	// 		'target_db_keys' => [],					 // if multi db if not leave it empty
	// 		'date_field' => 'end_at', 				 // target date field - table datefield basis
	// 		'number_range' => 3,                     // number where you can delete depends on delete_by
	// 		'save_to_table' => 'game_logs' ,         // archive table name ####_yyyymm
	// 		'delete_by'=> 'by_months' ,              // depends on number_range ex. by_days or by_months
	// 		'delete_step'=> '1 hours',               // steps in deleting ex. 1 minutes |  1 hours | 1 days
	// 		'archive_sleep' => 0,                    // just in case sleep is needed on archive
	// 		'delete_sleep' => 0,                     // just in case sleep is needed on delete
	// 		'only_start_at_max_day' => true,         // if want to start at max day or next 2nd range
	// 		'delete_data_after_backup'=>false,      // if  want to delete at the same time w/ export sql | it is like dry_run
	// 		'drop_last_month_archive_table_not_in_range'=>true,  //if want to delete old 2nd range -old month
 //         'use_index_str' =>  'USE INDEX (IDX_END_AT)' //  if want to use index when selecting before archiving or just set to null
 //        ],
 //    ]
];

#OGP-16006 if enable this config , can set player center register default Dialing Code
$config['enable_default_dialing_code'] = [
	// "country" => "nums",
];

$config['use_new_sbe_color']=true;

$config['max_agency_readonly_account']=10;

$config['COMMON_KEY_FOR_PASSWORD'] = null; // to config_local
$config['payment_api_key'] = null;


$config['limit_the_number_of_words_displayed'] = '50'; #payment notes RFE-3060

$config['allow_lobby_in_provider'] = [];

$config['redis_sentinel_list'] = [];
$config['default_mastername_of_redis_sentinel'] ='defaultmaster';
$config['default_master_redis'] = null;
$config['default_redis_for_id_generator'] = null;

//ci_memcached or redis
$config['cache_driver_type']='ci_memcached';
$config['default_redis_expire_time']=360000;

$config['enabled_explain_function_for_query_stream']=false;
$config['max_limit_of_query_stream']=500000;
$config['max_minutes_limit_for_query_stream']=60;

$config['go_player_login_if_not_login_when_launch_game'] = [];
$config['enabled_unbuffered_mysql_query']=false;
$config['enabled_unbuffered_mysql_query']=true;
$config['golden_race_multiple_currency_domain_mapping']=[];

// 12 hours
$config['force_timeout_of_session_redis']=43200;
//use redis to generate game logs.id
$config['enabled_generate_game_logs_id_from_redis']=false;
$config['enabled_generate_game_logs_unsettled_id_from_redis']=false;
$config['disable_last_transaction_list_on_agent_info_page']=false;

$config['remove_agent_prefix_on_all_game_account']=false;

$config['delete_player_info_settings'] = [

	// 'game_logs_group' => [

	// 	'idr' => [

	// 		'game_logs'=>[
	// 			'player_id_field'=> 'player_id',
	// 			'index_id'=> 'id',
	// 			'has_multi_rows'=>true,
	// 			'sql_delete_limit'=> 1000,
	// 			'save_info_to_csv'=>false,
	// 			'game_logs_originals' => [
	// 				'haba88_idr7_game_logs',
	// 				'mgplus_idr7_game_logs',
	// 				'isb_idr1_game_logs',
	// 	],//idr

	// ],

	// 'balance_history'=>[
	// 	'player_id_field'=> 'player_id',
	// 	'index_id'=> 'id',
	// 	'sql_delete_limit'=> 500,
	// ],
	// 	'player'=>[
// 		'player_id_field'=> 'playerId',
// 		'index_id'=> 'playerId',
// 		'save_info_to_csv'=>true,
// 		'sql_delete_limit'=> 500,
// 	],

];


$config['delete_agent_info_settings'] = [

	//transactions
	// 'transactions_from_type'=>[
	// 	'agent_id_field'=> 'id',
	// 	'index_id'=> 'id',
	// 	'sql_delete_limit'=> 500,
	// 	'save_info_to_csv'=>false,
	// ],
	// 'transactions_to_type'=>[
	// 	'agent_id_field'=> 'id',
	// 	'index_id'=> 'id',
	// 	'sql_delete_limit'=> 500,
	// 	'save_info_to_csv'=>false,
	// ],
	// 'transfer_request'=>[
	// 	'agent_id_field'=> 'agent_id',
	// 	'index_id'=> 'id',
	// 	'sql_delete_limit'=> 500,
	// 	'save_info_to_csv'=> false,
	// ],
	// 'agency_agents'=>[
	// 	'agent_id_field'=> 'agent_id',
	// 	'index_id'=> 'agent_id',
	// 	'sql_delete_limit'=> 500,
	// 	'save_info_to_csv'=> true,
	// ],

];

//for player center api
$config['player_center_api_X-DEBUG-SIGN-KEY']=null;

# add the method & input name you want to skip the xss clean filter
# 'class/method' => ['key_name', 'key_name2'] OR 'class/method' => 'ALL' if you want to skip all input
$config['skip_xss_filter_list']=[
	'cms_management/saveStaticSites' => ['login_template', 'logged_template', 'player_center_css', 'admin_css', 'aff_css'],
	'cms_management/save_custom_script' => ['taCustomScript'],
	'marketing_management/preparePromo' => ['formula_bonus_condition', 'formula_bonus_release', 'formula_withdraw_condition', 'formula_transfer_condition'],
	'payment_api/updatePaymentApi' => ['extra_info','sandbox_extra_info'],
];

//10 times per hour
$config['ip_limit_hourly']=['register'=>10];

//player center internal key
$config['internal_player_center_api_key']='42f77fd0409566c6123d58551e85ee87';
$config['print_debug_on_assert_function']=false;
$config['disabled_sync_game_logs_on_sbe']=false;
$config['disabled_manually_sync_game_logs']=false;

// OGP-15871: Disable 'manual update admin dashboard' in dev functions by default
$config['enable_dev_func_manual_update_admin_dashboard'] = false;
// OGP-16017
$config['enable_contact_number_custom_display'] = false;

/**
 * ['xxx.xxx.xxx.xxx']
 */
$config['backend_api_white_ip_list']=[];
//ignore sign if set this key
$config['backend_api_X-DEBUG-SIGN-KEY']=null;
$config['player_register_item_sequence_mobile'] = [
    'USERNAME',
    'PASSWORD',
    'EMAIL',
    'REGISTRATION_FIELDS',
    'COMMUNICATION_PREFERENCES',
    'REGISTER_BTN',
    'TERMS_AND_CONDITIONS',
    'HAVE_ACCOUNT',
    'LIVE_CHAT',
];

$config['player_register_item_sequence_web'] = [
    'USERNAME',
    'PASSWORD',
    'EMAIL',
    'REGISTRATION_FIELDS',
    'TERMS_AND_CONDITIONS',
    'COMMUNICATION_PREFERENCES',
    'REGISTER_BTN',
    'LOGIN_LINK',
];

$config['use_old_userinformation_page'] = false;
$config['block_user_agent_on_login']=null;
$config['block_x-real-ip_on_login']=null;

$config['cache_app_prefix_on_utils']=true;

require_once dirname(__FILE__) . '/admin/application/config/config_cronjob.php';
require_once dirname(__FILE__) . '/admin/application/config/config_api_acl.php';

///END OF FILE//////////
