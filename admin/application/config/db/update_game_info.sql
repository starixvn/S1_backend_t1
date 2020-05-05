/* PT */
update external_system
set live_url='https://kioskpublicapi.mightypanda88.com/',
sandbox_url='https://kioskpublicapi.mightypanda88.com/',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=1,
extra_info='{"ADMIN_NAME": "", "KIOSK_NAME": "", "CERT_PATH": "", "CERT_KEY": "", "CERT_PEM": "", "create_player_custom02": "", "jackpot_ticker_js": "", "casino": "", "ticker_server": "", "currency": "", "API_PLAY": ""}'
where id=1;

update external_system_list
set live_url='https://kioskpublicapi.mightypanda88.com/',
sandbox_url='https://kioskpublicapi.mightypanda88.com/',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=1,
extra_info='{"ADMIN_NAME": "", "KIOSK_NAME": "", "CERT_PATH": "", "CERT_KEY": "", "CERT_PEM": "", "create_player_custom02": "", "jackpot_ticker_js": "", "casino": "", "ticker_server": "", "currency": "", "API_PLAY": ""}'
where id=1;

/* AG */
update external_system
set live_url='http://gi.hll999.com:81/',
sandbox_url='http://gi.hll999.com:81/',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"ag_game_records_path": "/var/game_platform/agin", "DESKEY_AG": "", "MD5KEY_AG": "", "CAGENT_AG": "", "GCIURL_AG": "http://gci.hll999.com:81/"}'
where id=2;

update external_system_list
set live_url='http://gi.hll999.com:81/',
sandbox_url='http://gi.hll999.com:81/',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"ag_game_records_path": "/var/game_platform/agin", "DESKEY_AG": "", "MD5KEY_AG": "", "CAGENT_AG": "", "GCIURL_AG": "http://gci.hll999.com:81/"}'
where id=2;

/* MG */
update external_system
set live_url='https://entservices.totalegame.net?wsdl',
sandbox_url='https://entservices.totalegame.net?wsdl',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"mg_batch_query_balance_split": "", "mg_betting_profile_id_for_add_account": "", "mg_call_socks5_proxy": "", "mg_call_socks5_proxy_login": "", "mg_call_socks5_proxy_password": "", "mg_currency_for_add_account": "", "mg_currency_for_deposit": "", "mg_game_records_path": "/var/game_platform/mg", "mg_header_url": "https://entservices.totalegame.net", "mg_http_proxy_host": "", "mg_http_proxy_login": "", "mg_http_proxy_password": "", "mg_http_proxy_port": "", "mg_live_game_url_prefix": "", "mg_live_params.CasinoID": "2635", "mg_live_params.ClientID": "4", "mg_live_params.ModuleID": "70004", "mg_live_params.UserType": "0", "mg_live_params.ProductID": "2", "mg_live_params.ActiveCurrency": "Credits", "mg_live_params.VideoQuality": "auto6", "mg_live_params.CustomLDParam": "MultiTableMode^^1||LobbyMode^^C", "mg_live_params.StartingTab": "SPCasinoHoldem", "mg_live_params.ClientType": "1", "mg_login_name": "", "mg_pin_code": "", "mg_rng_game_url_prefix": "", "mg_rng_params.applicationid": "1023", "mg_rng_params.serverid": "2635", "mg_rng_params.csid": "2635", "mg_rng_params.theme": "igamingA", "mg_rng_params.usertype": "0", "mg_server_ip": "127.0.0.1", "mg_web_api_url": "https://tegapi.totalegame.net"}'
where id=6;

update external_system_list
set live_url='https://entservices.totalegame.net?wsdl',
sandbox_url='https://entservices.totalegame.net?wsdl',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"mg_batch_query_balance_split": "", "mg_betting_profile_id_for_add_account": "", "mg_call_socks5_proxy": "", "mg_call_socks5_proxy_login": "", "mg_call_socks5_proxy_password": "", "mg_currency_for_add_account": "", "mg_currency_for_deposit": "", "mg_game_records_path": "/var/game_platform/mg", "mg_header_url": "https://entservices.totalegame.net", "mg_http_proxy_host": "", "mg_http_proxy_login": "", "mg_http_proxy_password": "", "mg_http_proxy_port": "", "mg_live_game_url_prefix": "", "mg_live_params.CasinoID": "2635", "mg_live_params.ClientID": "4", "mg_live_params.ModuleID": "70004", "mg_live_params.UserType": "0", "mg_live_params.ProductID": "2", "mg_live_params.ActiveCurrency": "Credits", "mg_live_params.VideoQuality": "auto6", "mg_live_params.CustomLDParam": "MultiTableMode^^1||LobbyMode^^C", "mg_live_params.StartingTab": "SPCasinoHoldem", "mg_live_params.ClientType": "1", "mg_login_name": "", "mg_pin_code": "", "mg_rng_game_url_prefix": "", "mg_rng_params.applicationid": "1023", "mg_rng_params.serverid": "2635", "mg_rng_params.csid": "2635", "mg_rng_params.theme": "igamingA", "mg_rng_params.usertype": "0", "mg_server_ip": "127.0.0.1", "mg_web_api_url": "https://tegapi.totalegame.net"}'
where id=6;

/* NT */
update external_system
set live_url='http://<nt api>/',
sandbox_url='http://<nt api>/',
live_account='',
live_key='<nt token>',
live_secret='<nt secret key>',
sandbox_account='',
sandbox_key='<nt token>',
sandbox_secret='<nt secret key>',
live_mode=0,
extra_info='{"nt_game_url": "http://<nt game>/?", "nt_format": "json", "nt_default_software": "netent", "nt_default_currency": "CNY", "nt_default_group_id": "", "nt_default_language": "en", "nt_conversion_rate": 100.0 }'
where id=7;

update external_system_list
set live_url='http://<nt api>/',
sandbox_url='http://<nt api>/',
live_account='',
live_key='<nt token>',
live_secret='<nt secret key>',
sandbox_account='',
sandbox_key='<nt token>',
sandbox_secret='<nt secret key>',
live_mode=0,
extra_info='{"nt_game_url": "http://<nt game>/?", "nt_format": "json", "nt_default_software": "netent", "nt_default_currency": "CNY", "nt_default_group_id": "", "nt_default_language": "en", "nt_conversion_rate": 100.0 }'
where id=7;

/* BBIN */
update external_system
set live_url='http://linkapi.xxxx',
sandbox_url='http://linkapi.xxxx',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"bbin_conversion_rate": 1, "bbin_login_api_url": "http://888.xxx", "bbin_mywebsite": "", "bbin_uppername": "", "bbin_check_member_balance.keyb": "", "bbin_check_member_balance.start_key_len": 9, "bbin_check_member_balance.end_key_len": 6, "bbin_create_member.keyb": "", "bbin_create_member.start_key_len": 5, "bbin_create_member.end_key_len": 2, "bbin_getbet.keyb": "", "bbin_getbet.start_key_len": 1, "bbin_getbet.end_key_len": 7, "bbin_login_member.keyb": "", "bbin_login_member.start_key_len": 8, "bbin_login_member.end_key_len": 1, "bbin_logout_member.keyb": "", "bbin_logout_member.start_key_len": 4, "bbin_logout_member.end_key_len": 6, "bbin_transfer.keyb": "", "bbin_transfer.start_key_len": 2, "bbin_transfer.end_key_len": 7}'
where id=8;

update external_system_list
set live_url='http://linkapi.xxxx',
sandbox_url='http://linkapi.xxxx',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"bbin_conversion_rate": 1, "bbin_login_api_url": "http://888.xxx", "bbin_mywebsite": "", "bbin_uppername": "", "bbin_check_member_balance.keyb": "", "bbin_check_member_balance.start_key_len": 9, "bbin_check_member_balance.end_key_len": 6, "bbin_create_member.keyb": "", "bbin_create_member.start_key_len": 5, "bbin_create_member.end_key_len": 2, "bbin_getbet.keyb": "", "bbin_getbet.start_key_len": 1, "bbin_getbet.end_key_len": 7, "bbin_login_member.keyb": "", "bbin_login_member.start_key_len": 8, "bbin_login_member.end_key_len": 1, "bbin_logout_member.keyb": "", "bbin_logout_member.start_key_len": 4, "bbin_logout_member.end_key_len": 6, "bbin_transfer.keyb": "", "bbin_transfer.start_key_len": 2, "bbin_transfer.end_key_len": 7}'
where id=8;

/* LB */
update external_system
set live_url='http://fund.lsbet8.com',
sandbox_url='http://fund.lsbet8.com',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"lb_api_game_url": "http://testclient.lsbet8.com", "lb_call_socks5_proxy": "ogdev.ddns.net:10010", "lb_call_socks5_proxy_login": "", "lb_call_socks5_proxy_password": "", "lb_currency": "rmb", "lb_language": "zh_cn", "lb_max_transfer": 1000, "lb_member_type": "Cash", "lb_min_transfer": 10, "lb_operator_id": "F021E04C-BBC8-Demo-F8C5-E3C4C792445C", "lb_product_code": "keno", "lb_secret_key": "973AA2F9AC", "lb_site_code": "trylsbet8"}'
where id=10;

update external_system_list
set live_url='http://fund.lsbet8.com',
sandbox_url='http://fund.lsbet8.com',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='',
sandbox_secret='',
live_mode=0,
extra_info='{"lb_api_game_url": "http://testclient.lsbet8.com", "lb_call_socks5_proxy": "ogdev.ddns.net:10010", "lb_call_socks5_proxy_login": "", "lb_call_socks5_proxy_password": "", "lb_currency": "rmb", "lb_language": "zh_cn", "lb_max_transfer": 1000, "lb_member_type": "Cash", "lb_min_transfer": 10, "lb_operator_id": "F021E04C-BBC8-Demo-F8C5-E3C4C792445C", "lb_product_code": "keno", "lb_secret_key": "973AA2F9AC", "lb_site_code": "trylsbet8"}'
where id=10;

/* 188 */
update external_system
set live_url='http://in.lsbet8.com/Sportsbook',
sandbox_url='http://in.lsbet8.com/Sportsbook',
live_account='m1',
live_key='EDC77F4739562A81',
live_secret='',
sandbox_account='m1',
sandbox_key='EDC77F4739562A81',
sandbox_secret='',
live_mode=0,
extra_info='{"one88_lobby_url": "http://s.lsbet8.com/Sportsbook"}'
where id=11;

update external_system_list
set live_url='http://in.lsbet8.com/Sportsbook',
sandbox_url='http://in.lsbet8.com/Sportsbook',
live_account='m1',
live_key='EDC77F4739562A81',
live_secret='',
sandbox_account='m1',
sandbox_key='EDC77F4739562A81',
sandbox_secret='',
live_mode=0,
extra_info='{"one88_lobby_url": "http://s.lsbet8.com/Sportsbook"}'
where id=11;

