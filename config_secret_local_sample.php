<?php
$config['RUNTIME_ENVIRONMENT'] = 'live';

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysqli.  Currently supported:
mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
 */

//database
$config['db.default.hostname'] = '10.4.196.18';
$config['db.default.port'] = '3306';
$config['db.default.username'] = 'root';
$config['db.default.password'] = 'OWQnmpU5abVyEx!';
$config['db.default.database'] = 'ow';
$config['db.default.dbdriver'] = 'mysqli';
$config['db.default.dbprefix'] = '';
$config['db.default.pconnect'] = TRUE;
$config['db.default.db_debug'] = TRUE;
$config['db.default.cache_on'] = FALSE;
$config['db.default.cachedir'] = '';
$config['db.default.char_set'] = 'utf8';
$config['db.default.dbcollat'] = 'utf8_unicode_ci';
$config['db.default.swap_pre'] = '';
$config['db.default.autoinit'] = TRUE;
$config['db.default.stricton'] = FALSE;

//read only
$config['db.readonly.hostname'] = '10.4.196.18';
$config['db.readonly.port'] = '3306';
$config['db.readonly.username'] = 'root';
$config['db.readonly.password'] = 'OWQnmpU5abVyEx!';
$config['db.readonly.database'] = 'ow';
$config['db.readonly.dbdriver'] = 'mysqli';
$config['db.readonly.dbprefix'] = '';
$config['db.readonly.pconnect'] = TRUE;
$config['db.readonly.db_debug'] = TRUE;
$config['db.readonly.cache_on'] = FALSE;
$config['db.readonly.cachedir'] = '';
$config['db.readonly.char_set'] = 'utf8';
$config['db.readonly.dbcollat'] = 'utf8_unicode_ci';
$config['db.readonly.swap_pre'] = '';
$config['db.readonly.autoinit'] = TRUE;
$config['db.readonly.stricton'] = FALSE;

//api constants

//for smartbackend
$config['DESKEY_OG'] = 'diDF*234';
$config['queue_secret'] = '<queue secret key>';
$config['mail_smtp_password'] = '<password for mail>';

$config['default_3rdparty_payment'] = IPS_PAYMENT_API;

//external system settings
$config['default_prefix_for_username'] = '';
$config['external_system_types'] = array(SYSTEM_GAME_API, SYSTEM_PAYMENT);

$config['payment_account_types'] = array(
	MANUAL_ONLINE_PAYMENT => array('lang_key' => "pay.manual_online_payment", 'enabled' => true),
	AUTO_ONLINE_PAYMENT => array('lang_key' => "pay.auto_online_payment", 'enabled' => true),
	LOCAL_BANK_OFFLINE => array('lang_key' => "pay.local_bank_offline", 'enabled' => true),
);

$config['special_payment_list'] = array('bank_type_alipay', 'bank_type_wechat',
	'payment.type.4');

$config['default_network_timeout'] = 20;
$config['default_soap_timeout'] = $config['default_network_timeout'];
$config['default_http_timeout'] = $config['default_network_timeout'];

$config['default_connect_timeout'] = 10;

$config['deploy_token'] = null;
$config['dont_save_response_in_api'] = false;

$config['min_win_amount_for_newest'] = 900;
$config['temp_disabled_game_api'] = array();
$config['print_verbose_game_api'] = false;

$config['rescue_promotion_amount'] = 5.0;
$config['min_loadcard_amount'] = 10;

# Main SMS config in config_default_common.php
## Params used by Sms_api_sunmax.php
$config['Sms_api_sunmax_CorpID'] = '<CorpID>';
$config['Sms_api_sunmax_Pwd'] = '<Pwd>';

## Params used by Sms_api_dingdong.php
$config['Sms_api_dingdong_apikey'] = '<apikey>';

## Params used by Sms_api_santo.php
$config['Sms_api_santo_cpid'] = '<user id>';
$config['Sms_api_santo_cppwd'] = '<password>';

## Params used by Sms_api_ucpaas
$config['Sms_api_ucpaas_sid'] = '<sid>';
$config['Sms_api_ucpaas_appId'] = '<appId>';
$config['Sms_api_ucpaas_token'] = '<token>';

## Params used by Sms_api_luosimao
$config['Sms_api_luosimao_apikey'] = '<apiKey>';

$config['withdraw_verification'] = 'off'; # possible values: off, password, sms

$config['show_point_on_player'] = false;
$config['show_group_level_on_player'] = false;

//url link
$config['aff_sub_affiliate_link'] = 'affiliate/register';

$config['cronjob_email_from'] = 'admin@tripleonetech.com';
$config['cronjob_email_to'] = '';

$config['pubnub_subscribe_key'] = '';
$config['channel_admin_announcement'] = 'admin_announcement';
$config['server_name'] = 'og';
$config['always_run_cron_job'] = array();

$config['log_server_enabled'] = false;
$config['log_server_address'] = '';
$config['log_server_port'] = 12201;

$config['default_level_id'] = 1;

$config['player_livechat_url'] = '';

$config['enabled_features'] = array('promorules.allowed_affiliates', 'promorules.allowed_players',
	'player_list_on_affiliate', 'auto_refresh_balance_on_cashier',
	'generate_player_token_login', 'create_ag_demo', 'transaction_request_notification', 'show_admin_support_live_chat',
    'enable_player_center_live_chat', 'player_center_sidebar_message');

$config['hide_player_info_on_aff'] = false;

$config['live_chat'] = [
    ## If external_url is defined, live chat link will be using this URL, all other live chat configurations will be ignored
    # 'external_url' => 'https://www.google.com/',
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
	],
	'www_chat_host' => 'live.chatchat365.local',
	'www_chat_https_enabled' => false,
    'attach_link_on_player_center' => false,
    'show_on_player_center' => false,
    'external_url'=>'',
    'external_onclick'=>'live_chat_3rd_party()',
];

$config['sync_sleep_seconds'] = 30;
$config['sync_balance_sleep_seconds'] = 30;

$config['aff_contact_email'] = 'affiliate@tripleonetech.com';
$config['aff_contact_type'] = 'xxxxxxxx';

$config['main_prefix_of_player'] = ''; //will be added to username, not just show

$config['auto_generate_domain'] = false;
$config['auto_domain_pattern'] = ''; //like {AFFDOMAIN}.xxx.com
$config['auto_domain_start'] = 100;

$config['coutry_rules_mode'] = 'allow_all'; //allow_all, deny_all
$config['blocked_page_url'] = '/block-page.html';

$config['www_white_ip_list'] = ['127.0.0.1'];
$config['www_block_ip_list'] = [];

$config['ignore_undefined_php'] = false;
$config['ignore_unexpect_warning_php'] = false;

$config['hide_confirm_email'] = true; // hide confirm email on registration
$config['fav_icon'] = "/favicon.ico"; // sample replace fav icon on player and admin
$config['site_domain'] = "www.tripleonetech.com"; // sample redirect to site

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

$config['gearman_server'] = array('127.0.0.1');
$config['gearman_port'] = array('4730');

$config['lock_servers'] = [
	['redisserver', 6379, 0.01],
	// ['127.0.0.1', 6389, 0.01],
	// ['127.0.0.1', 6399, 0.01],
];

$config['use_og_config_to_setup'] = false;

$config['debug_version_info'] = true;

$config['current_php_timezone']='Asia/Hong_Kong';

# Defines the page used when game is under maintenance. Disable respective game API and game will redirect to this URL.
$config['maintenance_url'] = array(
    'bbin' => '/others/maintain.html',
    '*' => '/others/maintain2.html',
);

# Defines custom message used in stable_center2 withdraw page. You can use hard-coded msgs or lang key.
# $config['playercenter.memberCenterName'] = 'Cashier Center';
# $config['playercenter.withdrawMsg.success'] = '恭喜您，提交取款成功！我们会尽快审核处理您的取款申请，您的取款将于两小时内到帐，请稍候核实。如有疑问，请联系在线客服。';
# Defines custom message used in deposit page.
# $config['playercenter.deposit.hint'] = '温馨提示：请在完成扫码付款后在留言中备注您的会员账号，如忘记备注请及时联系在线客服。';

//35.187.153.12
$config['redis_server'] = ['server'=>'10.4.196.17', 'port'=>6379, 'password'=>null, 'timeout'=>1, 'retry_timeout'=>10, 'password'=>null];
// $config['redis_server'] = ['server'=>'35.187.153.12', 'port'=>6379, 'password'=>null, 'timeout'=>1, 'retry_timeout'=>10, 'password'=>null];

# Disable caching when set to 1. Default off.
#$config['disable_cache'] = 1;

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

//live database is 1, staging database is 2
$config['queue_redis_server'] = null;
// ['server'=>'10.140.0.9', 'port'=>6379, 'password'=>null, 'timeout'=>1, 'retry_timeout'=>10, 'password'=>null, 'database'=>null];

$config['rabbitmq_server'] = ['host'=>'10.4.196.17', 'port'=>5672, 'username'=>'php', 'password'=>'php']

//END OF FILE/////
