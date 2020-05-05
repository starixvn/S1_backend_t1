<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_of_88_to_functions_20151027 extends CI_Migration {

	public function up() {
// 		$this->db->trans_start();

// 		$this->db->query("DELETE FROM `functions`");

// 		$this->db->query("INSERT INTO `functions` (`funcId`, `funcName`, `parentId`, `funcCode`, `funcUrl`, `urlTarget`, `sort`, `createTime`,`status`) VALUES
// ( 1, 'System Management', '0' , 'system' , NULL, NULL, 1, '0000-00-00 00:00:00', '1' ),
// ( 2, 'Role Management', '1' , 'role' , NULL, NULL, 2, '0000-00-00 00:00:00', '1' ),
// ( 3, 'Delete Users', '1' , 'delete_user' , NULL, NULL, 3, '0000-00-00 00:00:00', '1' ),
// ( 4, 'Lock/Unlock Users', '1' , 'lock_user' , NULL, NULL, 4, '0000-00-00 00:00:00', '1' ),
// ( 5, 'Reset Password', '1' , 'reset_password' , NULL, NULL, 5, '0000-00-00 00:00:00', '1' ),
// ( 6, 'IP Rules', '1' , 'ip' , NULL, NULL, 6, '0000-00-00 00:00:00', '1' ),
// ( 8, 'Currency Setting', '1' , 'currency_setting' , NULL, NULL, 8, '0000-00-00 00:00:00', '1' ),
// ( 9, 'User Logs Report', '1' , 'user_logs_report' , NULL, NULL, 9, '0000-00-00 00:00:00', '1' ),
// ( 10, 'Affliliate Domain Setting', '1' , 'aff_domain_setting' , NULL, NULL, 10, '0000-00-00 00:00:00', '1' ),
// ( 11, 'Duplicate Account Setting', '1' , 'duplicate_account_setting' , NULL, NULL, 11, '0000-00-00 00:00:00', '1' ),
// ( 12, 'Game Description', '1' , 'game_description' , NULL, NULL, 12, '0000-00-00 00:00:00', '1' ),
// ( 13, 'Game API', '1' , 'game_api' , NULL, NULL, 13, '0000-00-00 00:00:00', '1' ),
// ( 14, 'Payment API', '1' , 'payment_api' , NULL, NULL, 14, '0000-00-00 00:00:00', '1' ),
// ( 15, 'Member Management', '0' , 'player' , NULL, NULL, 15, '0000-00-00 00:00:00', '1' ),
// ( 16, 'All Members', '15' , 'player_list' , NULL, NULL, 16, '0000-00-00 00:00:00', '1' ),
// ( 17, 'VIP Setting', '15' , 'vip_group_setting' , NULL, NULL, 17, '0000-00-00 00:00:00', '1' ),
// ( 18, 'Tagged Members', '15' , 'tag_player' , NULL, NULL, 18, '0000-00-00 00:00:00', '1' ),
// ( 19, 'Tag Management', '15' , 'taggedlist' , NULL, NULL, 19, '0000-00-00 00:00:00', '1' ),
// ( 20, 'Batch Create', '15' , 'account_process' , NULL, NULL, 20, '0000-00-00 00:00:00', '1' ),
// ( 21, 'Friend Referral', '15' , 'friend_referral_player' , NULL, NULL, 21, '0000-00-00 00:00:00', '1' ),
// ( 22, 'Registration Setting', '15' , 'registration_setting' , NULL, NULL, 22, '0000-00-00 00:00:00', '1' ),
// ( 23, 'View/Edit Member Password', '15' , 'edit_player_password' , NULL, NULL, 23, '0000-00-00 00:00:00', '1' ),
// ( 24, 'Adjust Member Level', '15' , 'adjust_player_level' , NULL, NULL, 24, '0000-00-00 00:00:00', '1' ),
// ( 25, 'Lock/Unlock Member in Website', '15' , 'lock_player' , NULL, NULL, 25, '0000-00-00 00:00:00', '1' ),
// ( 26, 'Block/Unblock Member in Game', '15' , 'block_player' , NULL, NULL, 26, '0000-00-00 00:00:00', '1' ),
// ( 27, 'Kick Member in website', '15' , 'kick_player_in_website' , NULL, NULL, 27, '0000-00-00 00:00:00', '1' ),
// ( 28, 'View Member Contact Information (Email)', '15' , 'view_player_contactinfo_em' , NULL, NULL, 28, '0000-00-00 00:00:00', '2' ),
// ( 29, 'View Member Contact Information (IM)', '15' , 'view_player_contactinfo_im' , NULL, NULL, 29, '0000-00-00 00:00:00', '2' ),
// ( 30, 'View Member Contact Information (Contact #)', '15' , 'view_player_contactinfo_cn' , NULL, NULL, 30, '0000-00-00 00:00:00', '2' ),
// ( 31, 'View Member Verification Question', '15' , 'view_player_verification_question' , NULL, NULL, 31, '0000-00-00 00:00:00', '1' ),
// ( 32, 'View Member Verification Answer', '15' , 'view_player_verification_answer' , NULL, NULL, 32, '0000-00-00 00:00:00', '1' ),
// ( 33, 'Add/Edit/Delete/Enable/Disable Member Bank Info', '15' , 'player_bank_info_control' , NULL, NULL, 33, '0000-00-00 00:00:00', '1' ),
// ( 34, 'Edit Member Personal Information', '15' , 'edit_player_personal_info' , NULL, NULL, 34, '0000-00-00 00:00:00', '1' ),
// ( 35, 'Player Contact Info', '15' , 'player_contact_info' , NULL, NULL, 35, '0000-00-00 00:00:00', '1' ),
// ( 36, 'Add Players Affiliate', '15' , 'assign_member_affiliate' , NULL, NULL, 36, '0000-00-00 00:00:00', '1' ),
// ( 37, 'CS Management', '0' , 'cs' , NULL, NULL, 37, '0000-00-00 00:00:00', '1' ),
// ( 38, 'Messages', '37' , 'chat' , NULL, NULL, 38, '0000-00-00 00:00:00', '1' ),
// ( 39, 'Message History', '37' , 'message_history' , NULL, NULL, 39, '0000-00-00 00:00:00', '1' ),
// ( 40, 'Report Management', '0' , 'report' , NULL, NULL, 40, '0000-00-00 00:00:00', '1' ),
// ( 41, 'Summary Report', '40' , 'summary_report' , NULL, NULL, 41, '0000-00-00 00:00:00', '1' ),
// ( 42, 'Member Report', '40' , 'player_report' , NULL, NULL, 42, '0000-00-00 00:00:00', '1' ),
// ( 43, 'Games Report', '40' , 'game_report' , NULL, NULL,  43, '0000-00-00 00:00:00', '1' ),
// ( 44, 'API Issue Report', '40' , 'api_report' , NULL, NULL, 44, '0000-00-00 00:00:00', '1' ),
// ( 45, 'Payment Report', '40' , 'payment_report' , NULL, NULL, 45, '0000-00-00 00:00:00', '1' ),
// ( 46, 'Promotion Report', '40' , 'promotion_report' , NULL, NULL, 46, '0000-00-00 00:00:00', '1' ),
// ( 47, 'Export Report', '40' , 'export_report' , NULL, NULL, 47, '0000-00-00 00:00:00', '1' ),
// ( 48, 'Affiliate Management', '0' , 'affiliate' , NULL, NULL, 48, '0000-00-00 00:00:00', '1' ),
// ( 49, 'Affiliate List', '48' , 'view_affiliates' , NULL, NULL, 49, '0000-00-00 00:00:00', '1' ),
// ( 50, 'Affiliate List (Edit Affiliate Information)', '48' , 'edit_affiliate_info' , NULL, NULL, 50, '0000-00-00 00:00:00', '1' ),
// ( 51, 'Affiliate List (Edit Affiliate Term)', '48' , 'edit_affiliate_term' , NULL, NULL, 51, '0000-00-00 00:00:00', '1' ),
// ( 52, 'Affiliate List (Create Affiliate Tracking Code)', '48' , 'add_affiliate_code' , NULL, NULL, 52, '0000-00-00 00:00:00', '1' ),
// ( 53, 'Affiliate Monthly Earnings', '48' , 'affiliate_earnings' , NULL, NULL, 53, '0000-00-00 00:00:00', '1' ),
// ( 54, 'Affiliate Payment', '48' , 'affiliate_payments' , NULL, NULL, 54, '0000-00-00 00:00:00', '1' ),
// ( 55, 'Affiliate Banner', '48' , 'banner_settings' , NULL, NULL, 55, '0000-00-00 00:00:00', '1' ),
// ( 56, 'Affiliate Tag', '48' , 'affiliate_tag' , NULL, NULL, 56, '0000-00-00 00:00:00', '1' ),
// ( 57, 'Affiliate Statistics', '48' , 'affiliate_statistics', NULL, NULL, 57, '0000-00-00 00:00:00', '1' ),
// ( 58, 'Affiliate Terms Default Setup', '48' , 'affiliate_terms', NULL, NULL, 58, '0000-00-00 00:00:00', '1' ),
// ( 59, 'Marketing Management', '0' , 'marketing' , NULL, NULL, 59, '0000-00-00 00:00:00', '1' ),
// ( 60, 'Promo Rules Settings', '59' , 'promo_rules_setting' , NULL, NULL, 60, '0000-00-00 00:00:00', '1' ),
// ( 61, 'Promo Manager', '59' , 'promocms' , NULL, NULL, 61, '0000-00-00 00:00:00', '1' ),
// ( 62, 'Promo Request List (Application)', '59' , 'promoapp_list' , NULL, NULL, 62, '0000-00-00 00:00:00', '1' ),
// ( 63, 'Promo Request List (Cancellation)', '59' , 'promocancel_list' , NULL, NULL, 63, '0000-00-00 00:00:00', '1' ),
// ( 64, 'Member List With Active Promo', '59' , 'promoplayer_list' , NULL, NULL, 64, '0000-00-00 00:00:00', '1' ),
// ( 65, 'Game Logs', '59' , 'gamelogs' , NULL, NULL, 65, '0000-00-00 00:00:00', '1' ),
// ( 66, 'Marketing Setting', '59' , 'marketing_setting' , NULL, NULL, 66, '0000-00-00 00:00:00', '1' ),
// ( 67, 'Friend Referral Settings', '59' , 'friend_referral_setting' , NULL, NULL, 67, '0000-00-00 00:00:00', '1' ),
// ( 68, 'Cashback Settings', '59' , 'cashback_setting' , NULL, NULL, 68, '0000-00-00 00:00:00', '1' ),
// ( 69, 'Promo Category Settings', '59' , 'promo_category_setting' , NULL, NULL, 69, '0000-00-00 00:00:00', '1' ),
// ( 70, 'Promo Cancellation Settings', '59' , 'promo_cancellation_setting' , NULL, NULL, 70, '0000-00-00 00:00:00', '1' ),
// ( 71, 'Duplicate Account Checker Settings', '59' , 'duplicate_account_checker_setting', NULL, NULL, 71, '0000-00-00 00:00:00', '1' ),
// ( 72, 'Payment Management', '0' , 'payment' , NULL, NULL, 72, '0000-00-00 00:00:00', '1' ),
// ( 73, 'Transactions', '72' , 'transaction_report' , NULL, NULL, 73, '0000-00-00 00:00:00', '1' ),
// ( 74, 'Deposit List', '72' , 'deposit_list' , NULL, NULL, 74, '0000-00-00 00:00:00', '1' ),
// ( 75, 'Auto 3rd Party Deposit List', '72' , 'payment_auto3rdparty_deposit_list' , NULL, NULL, 75, '0000-00-00 00:00:00', '2' ),
// ( 76, 'Manual 3rd Party Deposit List', '72' , 'payment_manual3rdparty_deposit_list' , NULL, NULL, 76, '0000-00-00 00:00:00', '2' ),
// ( 77, 'Withdrawal List', '72' , 'payment_withdrawal_list' , NULL, NULL, 77, '0000-00-00 00:00:00', '1' ),
// ( 78, 'Member Adjust Balance', '72' , 'payment_player_adjustbalance' , NULL, NULL, 78, '0000-00-00 00:00:00', '1' ),
// ( 79, 'Payment Settings', '72' , 'payment_settings' , NULL, NULL, 79, '0000-00-00 00:00:00', '1' ),
// ( 80, 'Collection Account', '72' , 'collection_account' , NULL, NULL, 80, '0000-00-00 00:00:00', '1' ),
// ( 81, 'Compensation Setting', '72' , 'compensation_setting' , NULL, NULL, 81, '0000-00-00 00:00:00', '1' ),
// ( 82, 'Previous Balances Checking Setting', '72' , 'previous_balances_checking_setting', NULL, NULL, 82, '0000-00-00 00:00:00', '1' ),
// ( 83, 'Non-promo Withdraw Setting', '72' , 'nonpromo_withdraw_setting' , NULL, NULL, 83, '0000-00-00 00:00:00', '1' ),
// ( 84, 'CMS Management', '0' , 'cms' , NULL, NULL, 84, '0000-00-00 00:00:00', '1' ),
// ( 85, 'News/Announcements Manager', '84' , 'view_news' , NULL, NULL, 85, '0000-00-00 00:00:00', '1' ),
// ( 86, 'Email Manager', '84' , 'emailcms' , NULL, NULL, 86, '0000-00-00 00:00:00', '1' ),
// ( 87, 'Generate Sites', '84' , 'generate_sites' , NULL, NULL, 87, '2015-07-07 08:00:00', '1' ),
// ( 88, 'SMTP Setting', '84' , 'smtp_setting' , NULL, NULL, 88, '2015-07-07 08:00:00', '1' )");

// 		#ROLEFUNCTIONS
// 		$sql3 = "SELECT * FROM rolefunctions";
// 		$q3 = $this->db->query($sql3);

// 		$q3res = $q3->result();

// 		$this->db->query("TRUNCATE rolefunctions");

// 		foreach ($q3res as $r) {

// 			if ($r->funcId >= 12 && $r->funcId <= 32) {

// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => ($r->funcId + 3),
// 				);
// 				$this->db->insert('rolefunctions', $row);

// 			} elseif ($r->funcId >= 33 && $r->funcId <= 83) {

// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => ($r->funcId + 4),
// 				);
// 				$this->db->insert('rolefunctions', $row);

// 			} else {
// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => $r->funcId,
// 				);
// 				$this->db->insert('rolefunctions', $row);

// 			}

// 		}

// 		$this->db->insert('rolefunctions', array(
// 			'roleId' => 1,
// 			'funcId' => 12,
// 		));
// 		$this->db->insert('rolefunctions', array(
// 			'roleId' => 1,
// 			'funcId' => 13,
// 		));
// 		$this->db->insert('rolefunctions', array(
// 			'roleId' => 1,
// 			'funcId' => 14,
// 		));
// 		$this->db->insert('rolefunctions', array(
// 			'roleId' => 1,
// 			'funcId' => 36,
// 		));
// 		$this->db->insert('rolefunctions', array(
// 			'roleId' => 1,
// 			'funcId' => 88,
// 		));

// 		#ROLEFUNCTIONS_GIVING
// 		$sql4 = "SELECT * FROM rolefunctions_giving";
// 		$q4 = $this->db->query($sql4);

// 		$q4res = $q4->result();

// 		$this->db->query("TRUNCATE rolefunctions_giving");
// 		foreach ($q4res as $r) {

// 			if ($r->funcId >= 12 && $r->funcId <= 32) {

// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => ($r->funcId + 3),
// 				);
// 				$this->db->insert('rolefunctions_giving', $row);

// 			} elseif ($r->funcId >= 33 && $r->funcId <= 83) {

// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => ($r->funcId + 4),
// 				);
// 				$this->db->insert('rolefunctions_giving', $row);

// 			} else {
// 				$row = array(
// 					'id' => $r->id,
// 					'roleId' => $r->roleId,
// 					'funcId' => $r->funcId,
// 				);
// 				$this->db->insert('rolefunctions_giving', $row);

// 			}

// 		}

// 		$this->db->insert('rolefunctions_giving', array(
// 			'roleId' => 1,
// 			'funcId' => 12,
// 		));
// 		$this->db->insert('rolefunctions_giving', array(
// 			'roleId' => 1,
// 			'funcId' => 13,
// 		));
// 		$this->db->insert('rolefunctions_giving', array(
// 			'roleId' => 1,
// 			'funcId' => 14,
// 		));
// 		$this->db->insert('rolefunctions_giving', array(
// 			'roleId' => 1,
// 			'funcId' => 36,
// 		));
// 		$this->db->insert('rolefunctions_giving', array(
// 			'roleId' => 1,
// 			'funcId' => 88,
// 		));

// 		$this->db->trans_complete();

// 		if ($this->db->trans_status() === FALSE) {
// 			throw new Exception(' failed');
// 		}

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->query("DELETE FROM `functions`");
		// $this->db->query("DELETE FROM rolefunctions");
		// $this->db->query("DELETE FROM rolefunctions_giving");
		// $this->db->trans_complete();

		// if ($this->db->trans_status() === FALSE) {
		// 	throw new Exception('Deletions  failed');
		// }
	}
}
