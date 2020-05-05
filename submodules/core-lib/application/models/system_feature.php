<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// require_once dirname(__FILE__) . '/base_model.php';

class System_feature extends CI_Model {

    public $featuresMap=[];

    const DB_TRUE = 1;
    const DB_FALSE = 0;

    const CACHE_TTL = 3600; # 1 hour
    const PUBLIC_TIMEOUT_KEY='public-feature-timeout';
    const ALL_FEATURES_JSON_KEY='all-features-json';

    const FEATURE_TYPE_DEPOSIT    = 'deposit';
    const FEATURE_TYPE_WITHDRAWAL = 'withdrawal';
    const FEATURE_TYPE_BANK       = 'bank';
    const FEATURE_TYPE_PROMO      = 'promo';
    const FEATURE_TYPE_PROFILE    = 'profile';
    const FEATURE_TYPE_AGENCY     = 'agency';
    const FEATURE_TYPE_AFFILIATE  = 'affiliate';
    const FEATURE_TYPE_TRANSFER   = 'transfer';
    const FEATURE_TYPE_KYC_RISKSCORE    = 'kyc_riskscore';
    const FEATURE_TYPE_SMS        = 'sms';
    const FEATURE_TYPE_LOTTERY    = 'lottery';
    const FEATURE_TYPE_OTHER      = 'other';

    const FEATURE_TYPES           = [
        'deposit',
        'withdrawal',
        'transfer',
        'bank',
        'promo',
        'profile',
        'agency',
        'affiliate',
        'kyc_riskscore',
        'sms',
        'lottery',
        'other'
    ];

    private $tableName = 'system_features';

    private $__deposit_features = [
        'add_security_on_deposit_transaction',
        'show_deposit_bank_details',
        'show_deposit_bank_details_first',
        'enable_manual_deposit_detail',
        // 'show_payment_account_image', // always show image
        'confirm_manual_deposit_details',
        'show_sub_total_for_deposit_list_report',
        'show_decimal_amount_hint',
        'ignore_bind_transaction_with_player_promo_when_trigger_collection_account_promo',
        'enable_3dparty_payment_in_modal',
        'disable_player_deposit_bank',
        'hidden_secure_id_in_deposit',
        'hidden_print_deposit_order_button',
        'hide_deposit_approve_decline_button_on_timeout',
        'show_deposit_3rdparty_on_top_bar',
        'default_settled_status_on_player_deposit_list',
        'show_pending_deposit',
        'show_declined_deposit',
        'only_allow_one_pending_deposit',
        'trigger_deposit_list_send_message',
        'enable_manually_deposit_cool_down_time',
        'show_tag_for_unavailable_deposit_accounts',
        'use_self_pick_promotion',
        'force_setup_player_deposit_bank_when_if_it_is_empty',
        'enable_deposit_upload_documents',
        'untick_time_out_deposit_request_if_load_deposit_list',
        'untick_3rd_party_payment_if_load_deposit_list',
        'untick_atm_cashier_if_load_deposit_list',
        'show_time_interval_in_deposit_processing_list',
        'untick_enabled_date_if_load_deposit_list',
        'highlight_deposit_id_in_list',
        'enable_deposit_datetime',
        'enable_deposit_amount_note',
        'enable_manual_deposit_realname',
        'hidden_mobile_deposit_MaxDailyDeposit_field',
        'hidden_mobile_deposit_TimeOfArrival_field',
        'enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit',
        'enable_mobile_3rdparty_deposit_close_1_btn_and_append_redirecturl',
        'enable_pc_player_back_to_dashboard_after_submit_deposit',
        'enable_change_deposit_transaction_ID_start_with_date',
        'show_total_deposit_amount_today',
        'enable_note_input_field_in_the_deposit',
        'enable_using_last_deposit_account',
        'enable_deposit_category_view',
        'disable_agency_player_report_in_sbe',
        'disable_agency_game_report_in_sbe',
        'enable_upload_deposit_receipt',
        'enable_collection_account_delete_button',
        'show_last_manually_deposit_order_status',
        'filter_payment_accounts_by_player_dispatch_account_level',
        'required_deposit_upload_file_1',
        'only_showing_atm_deposit_upload_attachment_in_deposit_list',
        'hide_bank_branch_in_payment_account_detail_player_center',
        'hide_bank_account_full_name_in_payment_account_detail_player_center',
        'hide_deposit_selected_bank_and_text_for_ole777',
        'enable_manual_deposit_input_depositor_name',
        'redirect_immediately_after_manual_deposit',
        'enable_deposit_page_make_manual_deposit_upload_helptext_always_showing',
        'enable_display_manual_deposit_datetime_step_hint',
        'enable_display_manual_deposit_upload_documents_step_hint',
        'enable_display_manual_deposit_note_step_hint',
        'realign_the_dashboard_in_deposit_list_and_make_them_clickable'
    ];

    private $__withdrawal_features = [
        'enabled_withdrawal_password',
        'show_sub_total_for_withdrawal_list_report',
        'check_withdrawal_conditions',
        'check_withdrawal_conditions_foreach',
        'check_deposit_conditions_foreach_in_withdrawal_conditions',
        'player_cancel_pending_withdraw',
        'use_new_account_for_manually_withdraw',
//        'auto_deduct_withdraw_condition_from_bet',
        'enabled_auto_clear_withdraw_condition', 'enabled_auto_check_withdraw_condition',
        'set_status_of_playerpromo_when_cancel_withdraw_condition',
        'hide_bonus_withdraw_condition_in_vip',
        'create_single_withdraw_condition_even_applied_promo',
        'clear_withdraw_condition_when_add_player_bonus',
        'hide_paid_button_when_condition_is_not_ready',
        'enable_withdrawal_pending_review',
        'highlight_withdrawal_code_in_list',
        'enable_withdrawal_pending_review_in_risk_score',
        'display_locked_status_column',
        'enable_withdrawal_amount_note',
        'separate_approve_decline_withdraw_pending_review_and_request_permission',
        'enable_change_withdrawal_transaction_ID_start_with_date',
        'enabled_display_change_withdrawal_password_message_note',
        'show_total_withdrawal_amount_today',
        'enable_currency_symbol_in_the_withdraw',
        'enable_confirm_birthday_before_setting_up_withdrawal_bank_account',
        'hidden_player_bank_account_number_in_the_withdraw',
        'enable_batch_withdraw_process_apporve_decline',
        'show_player_complete_withdrawal_account_number',
        'enabled_display_withdrawal_password_notification',
        'disabled_withdraw_condition_share_betting_amount'
    ];

    private $__bank_features = [
        'player_bind_one_bank',
        'player_bind_one_address_each_cryptocurrency',
        'player_can_edit_bank_account',
        'player_can_delete_bank_account',
        'disable_chinese_province_city_select',
        'allow_only_bank_account_limit',
        'player_bankAccount_input_numbers_limit',
        'player_bank_show_detail_form_validation_results',
        'duplicate_bank_account_number_verify_status_active',
        'always_duplicate_player_any_bank_when_add',
    ];

    private $__promo_features = [
        'show_promotion_view_all',
        'check_disable_cashback_by_promotion',
        'disabled_show_promo_detail_on_list',
        'enabled_request_promo_now_on_list',
        'enabled_transfer_condition',
        // 'enabled_check_max_bonus_by_cycle'
    ];

    private $__profile_features = [
        'hidden_avater_upload',
        'eanble_display_mobile_user_icon',
        'hide_contact_on_player_center',
        'enabled_show_player_obfuscated_email',
        'enabled_show_player_obfuscated_phone',
        'enabled_show_player_obfuscated_bank_acctno',
        'enabled_show_player_obfuscated_im',
        'show_game_lobby_in_player_center',
        'hidden_accounthistory_friend_referral_status',
        'show_total_balance_without_pending_withdraw_request',
        'show_player_vip_tab',
        'show_player_messages_tab',
        'player_center_sidebar_transfer', 'player_center_sidebar_deposit', 'player_center_sidebar_message',
        'enable_player_center_live_chat',
        'adjust_domain_to_wwww',
        'enabled_switch_to_mobile_on_www',
        'enabled_switch_www_to_https',
        'enabled_auto_switch_to_mobile_on_www',
        'enabled_switch_to_mobile_dir_on_www',
        'enable_player_center_mobile_footer_menu_games',
        'disabled_send_email_upon_player_registration','disabled_send_email_upon_aff_registration','disabled_send_email_upon_promotion',
        'disabled_send_email_contactus','disabled_send_email_upon_change_withdrawal_password',
        'disabled_player_send_message', 'disabled_player_reply_message',
        'disable_player_multiple_upgrade','hidden_vip_icon_LevelName','hidden_vip_status_ExpBar',
        'enable_player_prefs_auto_transfer' ,'hidden_player_first_login_welcome_popup',
        'show_player_promo_report_note','hidden_referFriend_referralcode',
        'hidden_login_page_contact_customer_service_area',
        'hidden_msg_sender_from_sysadmin','hidden_vip_betting_Amount_part',
        'enable_trasfer_all_quick_transfer',
        'enable_login_go_back_to_homepage',
        'enable_upload_income_notes'  ,
        'player_center_realtime_cashback' ,
        'mobile_player_center_realtime_cashback',
        'enable_username_cross_site_checking',
        'hidden_player_center_pending_withdraw_balance_tab',
        'hidden_player_center_total_deposit_amount_tab',
        'hidden_player_center_total_withdraw_amount_tab',
        'hidden_player_center_promotion_page_title_and_img',
        'disable_display_agent_code_on_player_center_agent_register_page',
        'disable_display_affiliate_code_on_player_center_affiliate_register_page',
        'hidden_affiliate_code_on_player_center_when_exists_referral_code',
        'hidden_agent_code_on_player_center_when_exists_referral_code',
        'enable_player_center_minify_file',
        'enabled_display_placeholder_hint_require',
        'enabled_forgot_withdrawal_password_use_email_to_reset',
        'enabled_forgot_withdrawal_password_use_livechat_to_reset',
        'display_last_login_timezone_in_overview',
        'cashier_custom_error_message',
        'display_total_bet_amount_in_overview',
        'hidden_promotion_on_navigation',
        'hidden_bet_amount_col_on_the_referral_report',
        'hidden_bonus_col_on_the_referral_report',
        'enable_auto_binding_agency_agent_on_player_registration',
        'use_www_game_icon_for_player_center',
        'force_refresh_all_subwallets',
        'enable_communication_preferences',
        'auto_popup_announcements_on_the_first_visit',
        'switch_to_player_center_promo_on_first_popup_after_register',
        'ole777_on_first_popup_after_register',
        'refresh_balance_when_launch_game',
        'enabled_account_fields_display_first_name_input_hint',
        'enabled_player_registration_restrict_min_length_on_first_name_field',
        'enable_player_message_request_form',
        'disabled_display_sub_total_row_in_player_center_game_history_report',
        'disabled_player_to_change_security_question',
        'block_emoji_chars_in_real_name_field' ,
        'enable_mobile_logo_add_link',
    ];

    private $__agency_features = [
        'agency', 'hide_transfer_on_agency', 'show_player_contact_on_agency', 'login_as_agent', 'rolling_comm_for_player_on_agency',
        'hide_bet_limit_on_agency', 'alwasy_create_agency_settlement_on_view', 'agent_tracking_code_numbers_only',
        'agency_tracking_code_numbers_only','agency_count_bonus_and_cashback', 'always_update_subagent_and_player_status',
        'agent_player_cannot_use_deposit_withdraw', 'transfer_rolling_to_player_when_do_settlement_wl',
        'daily_agent_rolling_disbursement', 'deduct_agent_rolling_from_revenue_share', 'agent_settlement_to_wallet',
        'adjust_rolling_for_low_odds', 'agency_hide_sub_agent_list_action', 'set_rev_share_to_readonly_for_level_0_agent','agent_hide_export', 'variable_agent_fee_rate', 'agency_hide_binding_player',
        'show_agent_name_on_game_logs', 'allow_negative_platform_fee', 'agency_information_self_edit',
        'enable_agency_player_report_generator',
        'use_https_for_agent_tracking_links',
        'use_new_agent_tracking_link_format', # OGP-6432
        'enable_agency_support_on_player_center',
        'enable_agency_auto_logon_on_player_center',
        'enable_player_center_style_support_on_agency',
        'enable_reset_player_password_in_agency',
        'enable_create_player_in_agency',
        'hidden_domain_tracking',
        'settlement_include_all_downline', # OGP-6736
        'hide_bonus_group_on_agency',
        'enabled_agency_adjust_player_balance',
        'hide_header_logo_in_agency',
        'disable_agency_player_report_in_sbe', # OGP-7773
        'disable_agency_game_report_in_sbe', # OGP-7773
        'enable_country_blocking_agency',
        'agent_tier_comm_pattern',
        'agent_comm_optional',
        'agent_can_use_balance_wallet',
        'agent_can_have_multiple_bank_accounts',
        'hide_registration_link_in_login_form',
        'use_deposit_withdraw_fee',
        'notify_agent_withdraw',
        'hide_agency_t1lotterry_link',
        'allow_transfer_wallet_balance_to_binding_player',
        'enable_agency_prefix_for_game_account',
        'disable_agent_hierarchy',
        'disable_agent_dashboard',
        'hide_reg_page_for_subagent_link_if_parent_agent_cannot_have_subagents',
        'enabled_readonly_agency',
    ];

    private $__affiliate_features = [
        "enable_aff_custom_css", 'promorules.allowed_affiliates', 'player_list_on_affiliate', 'switch_to_player_secure_id_on_affiliate',
        'show_player_info_on_affiliate', 'show_transactions_history_on_affiliate', 'show_player_contact_on_aff',
        'affiliate_additional_domain', 'individual_affiliate_term', 'affiliate_source_code', 'player_stats_on_affiliate',
        'affiliate_player_report', 'affiliate_game_history', 'affiliate_credit_transactions', 'hide_total_win_loss_on_aff_player_report',
        'notify_affiliate_withdraw', 'affiliate_monthly_earnings', 'parent_aff_code_on_register', 'affiliate_second_password',
        'show_affiliate_list_on_search_player', 'show_cashback_and_bonus_on_aff_player_report', 'dashboard_count_direct_affiliate_player', 'hide_sub_affiliates_on_affiliate',
        'masked_player_username_on_affiliate', 'match_wild_char_on_affiliate_domain', 'enabled_active_affiliate_by_email', 'disable_aff_gross_rev_formula_dep_minus_withdraw',
        'affiliate_tracking_code_numbers_only','show_search_affiliate', 'switch_to_affiliate_daily_earnings', 'show_search_affiliate_tag', 'switch_to_affiliate_platform_earnings',
        'enable_commission_from_subaffiliate', 'aff_show_real_name_on_reports',  'disabled_game_logs_in_aff', "hide_affiliate",
        'affiliate_commision_check_deposit_and_bet', 'aff_no_admin_fee_for_negative_revenue', 'hide_affiliate_message_login_form', 'aff_hide_payment_request_notes_in_cashier',
        'aff_enable_read_only_account', 'aff_hide_changed_balance_in_cashier', 'aff_hide_traffic_stats', 'masked_affiliate_username_on_affiliate',
        'masked_realname_on_affiliate', 'ignore_subaffiliates_with_negative_commission','display_aff_beside_playername_gamelogs', 'display_aff_beside_playername_daily_balance_report',
        'enable_sortable_columns_affiliate_statistic', 'switch_old_aff_stats_report_to_new', 'close_aff_and_agent', 'enable_country_blocking_affiliate',
        'enable_rake_column_in_commission_details',
        'enable_tracking_remarks_field',
        'display_earning_reports_schedule',
        'enabled_edit_affiliate_bank_account',
        'disable_account_name_letter_format',
        'enable_reset_affiliate_list',
        'hide_affiliate_registration_link_in_login_form',
        'display_sub_affiliate_earnings_report',
        'enable_move_up_dashboard_statistic_in_affiliate_backoffice',
        'enable_affiliate_downline_by_level',
        'enable_sub_affiliate_commission_breakdown',
        'enable_affiliate_player_report_generator',
        'enable_exclude_platforms_in_player_report',
        'enable_new_dashboard_statistics',
        'aff_disable_logo_link',
        'hide_affiliate_language_dropdown',
        'only_compute_fees_from_bet_of_valid_game_platforms',
        'hide_aff_cashier_navbar',
    ];

    private $__transfer_features = [
        'disabled_manually_transfer_on_player_center',
        'disable_account_transfer_when_balance_check_fails',
        'enabled_single_wallet_switch',
        'always_auto_transfer_if_only_one_game',  // which means always auto transfer
        'enabled_mobile_transfer_input_amount_button',
        'enable_make_up_transfer_on_adjust_balance',
        'show_inactive_subwallet_in_balance_adjustment',
        'enabled_transfer_all_and_refresh_button_on_new_transfer_ui',
    ];

    private $__kyc_riskscore_features = [
        'show_allowed_withdrawal_status',
        'show_kyc_status',
        'show_risk_score',
        'show_pep_status',
        'enable_pep_gbg_api_authentication',
        'show_pep_authentication',
        'show_upload_documents',
        'show_player_upload_proof_of_address',
        'show_player_upload_proof_of_income',
        'show_player_upload_proof_of_deposit_withdrawal',
        'show_player_upload_realname_verification',
        'show_c6_authentication',
        'enable_c6_acuris_api_authentication',
        'show_c6_status'
    ];

    private $__sms_features = [
        'send_sms_after_registration',
        'enabled_send_sms_use_queue_server',
        'enable_player_registered_send_msg',
        'enable_sms_withdrawal_prompt_action_request',
        'enable_sms_withdrawal_prompt_action_success',
        'enable_sms_withdrawal_prompt_action_declined',
        'enable_restrict_sms_send_num_in_player_center_phone_verification',
        'display_all_numbers_of_mobile'
    ];

    private $__lottery_features = [
        'enabled_lottery_agent_navigation',
        'integrate_lottery_agent_to_admin',
        'enable_embedded_lottery_sdk',
        'use_lottery_center_home_on_the_mobile_version',
        'use_lottery_center_home_on_the_desktop_version',
        'hidden_lottery_game_list_on_the_mobile_dashboard',
        'hidden_myfavorite_widget_on_the_mobile_dashboard',
        'enabled_lottery_salary_on_olayer_center',
    ];

    private $__others_features = [
        'promorules.allowed_players',
        'responsible_gaming','disable_responsible_gaming_auto_approve','hide_permanent_self_exclusion_cancel_button',
        'show_admin_support_live_chat', 'transaction_request_notification',
         //'select_promotion_on_deposit',
        'use_self_pick_subwallets', 'use_self_pick_group', 'declined_forever_promotion',
        'show_unsettle_game_logs', 'auto_refresh_balance_on_cashier', 'generate_player_token_login',
        'export_excel_on_queue',
        'deposit_withdraw_transfer_list_on_player_info', 'enabled_feedback_on_admin', 'sync_api_password_on_update',
        'check_player_session_timeout', 'show_bet_detail_on_game_logs',
        'donot_show_registration_verify_email', 'popup_window_on_player_center_for_mobile',
        'enabled_login_password_on_withdrawal', 'only_manually_add_active_promotion',
        'only_allow_one_for_adminuser', 'allow_player_same_number',
        'notification_promo', 'notification_messages', 'notification_local_bank',
        'notification_thirdparty', 'notification_withdraw',
        'iovation_fraud_prevention',
        'create_sale_order_after_player_confirm',
        'enable_cashback_after_withdrawal_deposit', 'set_enabled_permission_all',
        'default_search_all_players', 'enabled_refresh_message_on_player', 'enabled_freespin',
        'notification_thirdparty_settled_on_top_bar',
        'only_use_dropdown_list_for_notification', 'force_disable_all_promotion_dropdown_on_player_center',
        'display_referral_code_in_player_details',
        'disabled_auto_create_game_account_on_registration',
        'create_ag_demo', 'create_agin_demo',
        'enabled_check_frondend_block_status', 'enable_super_report','enable_payment_status_history_report',
        'summary_report_2', 'enabled_whitelist_duplicate_record',
        'hide_retype_email_field_on_registration',
        'kickout_game_when_kickout_player', 'use_mobile_number_as_username',
        'bind_promorules_to_friend_referral', 'player_deposit_reference_number',
        'refresh_player_balance_before_pay_cashback',
        'always_calc_before_pay_cashback', 'enable_upload_depostit_slip', 'switch_to_ibetg_commission',
        'enabled_vipsetting_birthday_bonus', 'enabled_change_lang_tutorial',
        'enable_dynamic_header', 'enable_dynamic_footer',
        'enabled_maintaining_mode', 'notify_cashback_request', 'send_message',
        'only_6_hours_game_records', 'exporting_on_queue', 'add_notes_for_player',
        'enabled_player_center_preloader', 'show_sub_total_for_game_logs_report', 'declined_withdrawal_add_transaction', 'auto_pay_cashback_when_regenerate',
        'auto_fix_2_days_cashback', 'column_visibility_report', 'use_new_player_center_mobile_version',
        'force_refresh_cache', 'enable_dynamic_registration', 'enabled_player_center_spinner_loader',
        'enable_dynamic_javascript', 'enable_shop',
        'www_quick_transfer_sidebar', 'www_deposit_sidebar', 'www_live_chat_sidebar', 'www_sidebar', 'player_main_js_enable_game_preloader', 'try_disable_time_ranger_on_cashback', 'enabled_player_referral_tab',
        'allow_duplicate_contact_number', 'contact_customer_service_for_forgot_password', 'display_vip_upgrade_schedule_in_player', 'enabled_favorites_and_rencently_played_games',
        'enabled_cashback_period_in_vip', 'hide_contact_on_player_center',
        'enable_player_center_mobile_live_chat', 'enable_custom_script', 'enable_custom_script_mobile', 'include_company_name_in_title',
        'enable_contact_us',
        'enabled_realtime_cashback',
        'show_total_for_player_report', 'cashier_multiple_refresh_btn',
        'enable_mobile_acct_login', 'disabled_login_trial_agin_game', 'enable_subwallet_by_category',
        'player_center_hide_time_in_remark', 'register_page_show_login_link','add_close_status',
        'agency_tracking_code_numbers_only','agency_count_bonus_and_cashback','verification_reference_for_player',
        'enable_mobile_copyright_footer', 'notification_new_player',
        'linked_account','enable_batch_approve_and_decline',
        'disable_mobile_access_comp_link', 'hide_taggedlist_email_column','mobile_show_vip_referralcode',
        'hide_dates_filter_in_promo_history', 'show_sports_game_columns_in_game_logs','disable_action_buttons_in_player_list_table',
        'enable_withdrawal_declined_category','enable_adjustment_category','auto_add_reason_in_adjustment_main_wallet_to_player_notes',
        'enable_remove_country_in_list_if_blocked_country_rules','disable_frequently_use_country_in_registration',
        'show_player_address_in_list', 'show_zip_code_in_list', 'show_id_card_number_in_list', 'exclude_ips_in_duplicate_account',
        'enable_registered_show_success_popup', 'ignore_notification_permission', 'link_account_in_duplicate_account_list',
        'hide_second_deposit_in_summary_report',
        'bonus_games__enable_bonus_game_settings',
        'bonus_games__support_bonus_game_in_promo_rules_settings','add_suspended_status',
        'disable_player_change_withdraw_password',
        'mobile_winlost_column',
        "show_new_games_on_top_bar",
        "only_admin_modified_role",
        "switch_ag_round_and_notes",
        "enable_dynamic_mobile_login",
        "show_game_history_of_deleted_player",
        "enable_gamelogs_v2",
        "enable_dynamic_theme_host_template" ,
        "show_bet_time_column",
        'enable_multi_lang_promo_manager',
        "batch_release_promo",
        "batch_decline_promo",
        "batch_finish_promo",
        'enable_friend_referral_cashback',
        'hide_deposit_and_withdraw_on_aff_player_report',
        'enabled_weekly_cashback',
        'enabled_cashback_of_multiple_range',
        'enabled_batch_upload_player',
        'strictly_cannot_login_player_when_block',
        'display_newsletter_subscribe_btn',
        'enable_player_center_search_unsettle',
        'display_player_bets_per_game',
        'display_exclude_player_tag',
        'enable_income_access',
        'enable_default_logic_transaction_period',
        'allow_special_characters_on_account_number',
        'hide_disabled_games_on_game_tree',
        'enabled_sync_game_logs_stream',
        'ignore_player_analysis_permissions',
        'enable_isolated_vip_game_tree_view',
        'enable_isolated_promo_game_tree_view',
        'send_email_after_verification',
        'send_email_promotion_template_after_verification',
        'enable_show_bet_details_gamelogs_report',
        'enabled_show_rake',
        'dont_allow_disabled_game_to_be_launched','close_cashback',
        'close_livechat',
        'close_level_upgrade_downgrade',
        'allow_generate_inactive_game_api_game_lists',
        'allow_to_launch_non_existing_games_on_sbe',
        'enable_tag_column_on_transaction',
        'enable_player_report_2',
        'ole777_wager_sync',
        'hide_old_adjustment_history',
        'disabled_adjust_player_dispatch_account_level',
        'enabled_oneworks_game_report',
        'hide_point_setting_in_vip_level',
        'allow_login_as_player_with_empty_password',
        'hide_empty_game_type_on_game_tree',
        'hide_free_spin_on_game_history',
        'use_role_permission_management_v2',
        'enabled_switch_language_also_set_to_static_site',
        'enabled_sbobet_sports_game_report',
        'use_new_super_report',
        'use_pwa_loader',
        'enable_registered_triggerRegisterEvent_for_xinyan_api',
        'enable_otp_on_adminusers',
        'enable_otp_on_player',
        'enable_otp_on_agency',
        'enable_otp_on_affiliate',
        'enable_currency_permission',
        'enable_daterangepicker_last30days_item',
        'disable_auto_add_cash_back',
        'enable_show_trigger_XinyanApi_validation_btn',
        'enabled_vr_game_report',
        'enabled_afb88_sports_game_report',
        'enabled_backendapi',
    ];


    function __construct() {
        parent::__construct();

        // $this->testLoadAllFeatures();

        if($this->utils->getConfig('always_load_system_feature_to_cache')){
            $this->saveAllFeaturesToCache();
        }else{
            $this->cacheFeatureInArray();
        }
    }

    private function getCacheKey($name) {
        return PRODUCTION_VERSION."|$this->tableName|$name";
    }

    function isEnabledFeature($name) {

        if(isset($this->featuresMap[$name])){
            // $this->utils->debug_log('load feature from memory', $name);
            return $this->featuresMap[$name] == self::DB_TRUE;
        }

        return $this->isEnabledFeatureWithoutCache($name);

        // $value = $this->utils->getTextFromCache($this->getCacheKey($name));
        // if($value === false) {
        //     $this->db->select('enabled')->from('system_features')->where('name', $name);
        //     $qry = $this->db->get();

        //     if ($qry && $qry->num_rows() > 0) {

        //         $row=$qry->row_array();
        //         $value=$row['enabled'];
        //         $qry->free_result();

        //     }else{
        //         $value=null;
        //     }

        //     // $value = $this->runOneRowOneField('enabled');
        //     $this->utils->saveTextToCache($this->getCacheKey($name), $value, self::CACHE_TTL);
        // }
        // return $value == self::DB_TRUE;
    }

    function isEnabledFeatureWithoutCache($name) {
        $enabled=false;
        $this->utils->debug_log('does not suppose run this isEnabledFeatureWithoutCache: '.$name);

        $sql='select enabled from system_features where name=?';

        $errInfo=null;
        $row=$this->db->runRawSelectReturnOne($sql, [['type'=>'s', 'value'=>$name]], $errInfo);
        if(!empty($row)){
            $enabled=$row['enabled']==self::DB_TRUE;
        }else{
            $this->utils->error_log('cannot find feature', $name, $errInfo);
        }
        //add to cache
        // $this->featuresMap[$name] == $enabled;

        return $enabled;
    }

    function get() {
        $system_feature = $this->db->get('system_features')->result_array();
        # sort the system feature based on element's name
        usort($system_feature, function($a, $b){
            return strcmp($a['name'], $b['name']);
        });
        return $system_feature;
    }

    function updateFeatures($feature, $data = array()) {
        $this->utils->deleteCache(); # Delete all cache, as we do not have feature name here
        return $this->updateFeaturesWithoutClearCache($feature, $data);
    }

    function updateFeaturesWithoutClearCache($feature, $data = array()) {
        return $this->db->where('id', $feature)->update('system_features', $data);
    }

    public function getFeatureType($system_feature) {
        switch (TRUE) {
            case (in_array($system_feature, $this->__deposit_features)):
                return self::FEATURE_TYPE_DEPOSIT;
                break;
        }

        return self::FEATURE_TYPE_OTHER;
    }

    private function __syncAllFeatures($system_features, $system_feature_type) {
        $enabled_features = $this->utils->getConfig('enabled_features');

        $batch_data = array();

        foreach ($system_features as $key => $system_feature_name) {
            if(is_array($system_feature_name)){
                $scopes = $system_feature_name;
                $system_feature_name = $key;
            }

            $qry=$this->db->from('system_features')->where('name', $system_feature_name)->get();

            $system_feature = null;

            if ($qry && $qry->num_rows() > 0) {
                $system_feature=$qry->row_array();
                $qry->free_result();
            }

            if (!empty($system_feature)) {
                if (isset($system_feature['type']) && $system_feature['type'] != $system_feature_type) {
                    $this->updateFeatures($system_feature['id'], ['type' => $system_feature_type]);
                }
                continue;
            }

            $enabled = in_array($system_feature_name, $enabled_features);

            $data = array(
                'name'    => $system_feature_name,
                'type'    => $system_feature_type,
                'enabled' => $enabled,
            );

            $batch_data[] = $data;
        }

        if (empty($data)) {
            return true;
        }

        $this->db->insert_batch('system_features', $batch_data);
    }

    function syncAllFeatures() {
        // $this->load->library('utils');

        $this->__syncAllFeatures($this->__deposit_features, self::FEATURE_TYPE_DEPOSIT);
        $this->__syncAllFeatures($this->__withdrawal_features, self::FEATURE_TYPE_WITHDRAWAL);
        $this->__syncAllFeatures($this->__bank_features, self::FEATURE_TYPE_BANK);
        $this->__syncAllFeatures($this->__promo_features, self::FEATURE_TYPE_PROMO);
        $this->__syncAllFeatures($this->__profile_features, self::FEATURE_TYPE_PROFILE);
        $this->__syncAllFeatures($this->__agency_features, self::FEATURE_TYPE_AGENCY);
        $this->__syncAllFeatures($this->__affiliate_features, self::FEATURE_TYPE_AFFILIATE);
        $this->__syncAllFeatures($this->__transfer_features, self::FEATURE_TYPE_TRANSFER);
        $this->__syncAllFeatures($this->__kyc_riskscore_features, self::FEATURE_TYPE_KYC_RISKSCORE);
        $this->__syncAllFeatures($this->__sms_features, self::FEATURE_TYPE_SMS);
        $this->__syncAllFeatures($this->__lottery_features, self::FEATURE_TYPE_LOTTERY);
        $this->__syncAllFeatures($this->__others_features, self::FEATURE_TYPE_OTHER);

        // if($this->utils->getConfig('always_load_system_feature_to_cache')){
            $this->utils->deleteCache(self::ALL_FEATURES_JSON_KEY);
        // }

        return true;
    }

    public function testLoadAllFeatures(){
        $this->utils->debug_log('before load features');
        $this->db->select('name, enabled')->from('system_features');
        $qry=$this->db->get();
        $rows=null;
        if ($qry && $qry->num_rows() > 0) {
            $rows=$qry->result_array();
            $qry->free_result();
            unset($qry);
        }
        $this->featuresMap=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $this->featuresMap[$row['name']]=$row['enabled'];
            }
        }
        unset($rows);

        $this->utils->debug_log('after load features', count($this->featuresMap));

        unset($this->featuresMap);
        $this->utils->debug_log('free features');
    }

    public function cacheFeatureInArray(){
        $this->utils->debug_log('before load features from db');
        $this->db->select('name, enabled')->from('system_features');
        $qry=$this->db->get();
        $rows=null;
        if ($qry && $qry->num_rows() > 0) {
            $rows=$qry->result_array();
            $qry->free_result();
            unset($qry);
        }
        $this->featuresMap=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $this->featuresMap[$row['name']]=$row['enabled'];
            }
        }
        unset($rows);

        $this->utils->debug_log('after load features', count($this->featuresMap));
    }

    public function saveAllFeaturesToCache(){

        $this->featuresMap=$this->utils->getJsonFromCache(self::ALL_FEATURES_JSON_KEY);

        if(empty($this->featuresMap)){
            $this->cacheFeatureInArray();
            //save to cache
            $this->utils->saveJsonToCache(self::ALL_FEATURES_JSON_KEY, $this->featuresMap);
        }else{
            // $this->utils->debug_log('features do not timeout, so ignore loading', count($this->featuresMap));
        }

        // $timeout=$this->utils->getTextFromCache(self::PUBLIC_TIMEOUT_KEY);
        // $t=time();
        // //empty or timeout
        // if(empty($timeout) || intval($timeout)<$t){

        //     //load all features to cache
        //     $this->db->select('name, enabled')->from('system_features');
        //     $qry=$this->db->get();
        //     $rows=null;
        //     if ($qry && $qry->num_rows() > 0) {
        //         $rows=$qry->result_array();
        //         $qry->free_result();
        //     }
        //     if(!empty($rows)){
        //         foreach ($rows as $row) {
        //             $this->utils->saveTextToCache($this->getCacheKey($row['name']), $row['enabled'], self::CACHE_TTL);
        //         }
        //     }

        //     unset($rows);

        //     $this->utils->debug_log('save features to cache');

        //     //save timeout
        //     $this->utils->saveTextToCache(self::PUBLIC_TIMEOUT_KEY, $t+self::CACHE_TTL-60, self::CACHE_TTL);
        // }else{
        //     $this->utils->debug_log('features do not timeout, so ignore loading', $timeout, $t);
        // }
    }

    public function clearDuplicateName(){
        //get duplicate name first
$sql=<<<EOD
select type,name, min(id) as min_id, count(id) as cnt_id from system_features
group by name
having count(id)>1
EOD;
        $qry=$this->db->query($sql);
        if(!empty($qry) && $qry->num_rows()>0){
            foreach ($qry->result_array() as $row){
                $name=$row['name'];
                $min_id=$row['min_id'];
                //delete other id , only keep min id means first id
                $this->db->where('name', $name)->where('id !=', $min_id);
                $this->db->delete('system_features');
                $this->utils->debug_log('delete duplicate system feature', $this->db->affected_rows(), $this->db->last_query());
            }
        }else{
            $this->utils->debug_log('no duplicate system feature');
        }

    }

}

/* End of file system_feature.php */
/* Location: ./application/models/system_feature.php */
