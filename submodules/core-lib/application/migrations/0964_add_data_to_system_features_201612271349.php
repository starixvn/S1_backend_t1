<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_data_to_system_features_201612271349 extends CI_Migration {

	private $tableName = 'system_features';

	public function up(){

		// $system_features = $this->utils->getConfig('new_features');
		//  array(
		// 	'promorules.allowed_affiliates', 'promorules.allowed_players',
		// 	'agency', 'responsible_gaming', 'player_list_on_affiliate', 'switch_to_player_secure_id_on_affiliate',
		// 	'show_player_info_on_affiliate', 'show_transactions_history_on_affiliate', 'show_player_contact_on_aff',
		// 	'show_player_contact_on_agency', 'show_admin_support_live_chat', 'transaction_request_notification',
		// 	'affiliate_additional_domain', 'individual_affiliate_term', 'affiliate_source_code', //'select_promotion_on_deposit',
		// 	'use_self_pick_subwallets', 'use_self_pick_group', 'declined_forever_promotion', 'player_cancel_pending_withdraw',
		// 	'show_unsettle_game_logs', 'auto_refresh_balance_on_cashier', 'generate_player_token_login',
		// 	'login_as_agent', 'rolling_comm_for_player_on_agency', 'hide_transfer_on_agency', 'always_update_subagent_and_player_status',
		// 	'use_new_account_for_manually_withdraw', 'player_stats_on_affiliate', 'export_excel_on_queue',
		// 	'deposit_withdraw_transfer_list_on_player_info', 'enabled_feedback_on_admin', 'sync_api_password_on_update',
		// 	'check_player_session_timeout', 'show_bet_detail_on_game_logs', 'player_bind_one_bank', 'check_disable_cashback_by_promotion',
		// 	'donot_show_registration_verify_email', 'popup_window_on_player_center_for_mobile', 'enabled_withdrawal_password',
		// 	'enabled_login_password_on_withdrawal', 'enable_manual_deposit_detail', 'only_manually_add_active_promotion',
		// 	'only_allow_one_for_adminuser', 'allow_player_same_number', 'affiliate_player_report', 'affiliate_game_history',
		// 	'affiliate_credit_transactions', 'notification_promo', 'notification_messages', 'notification_local_bank',
		// 	'notification_thirdparty', 'notification_withdraw','hide_total_win_loss_on_aff_player_report',
		// 	'hide_deposit_approve_decline_button_on_timeout', 'show_deposit_3rdparty_on_top_bar',
		// 	'create_ag_demo'
		// );

		// $enabled_features = $this->utils->getConfig('enabled_features');
		// array(
		// 	'promorules.allowed_affiliates', 'promorules.allowed_players',
		// 	'player_list_on_affiliate', 'auto_refresh_balance_on_cashier',
		// 	'generate_player_token_login', 'create_ag_demo', 'transaction_request_notification',
		// 	'show_admin_support_live_chat', 'popup_window_on_player_center_for_mobile',
		// 	'hide_deposit_approve_decline_button_on_timeout'
		// );

		// $data = array();

		// foreach ($system_features as $key => $value) {

		// 	$flag = 0;
		// 	if( in_array($value, $enabled_features) ) $flag = 1;

		// 	$data[] = array(
		// 			'name' => $value,
		// 			'enabled' => $flag
		// 		);
		// }

		// $this->db->insert_batch($this->tableName, $data);

		// $this->db->trans_complete();
	}

	public function down(){

	}

}