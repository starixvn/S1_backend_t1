<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_function_table_20151001 extends CI_Migration {

	function up() {
		// $this->db->query("DELETE FROM `functions`");
		// $this->db->query("INSERT INTO `functions` (`funcId`, `funcName`, `parentId`, `funcCode`, `funcUrl`, `urlTarget`, `sort`, `createTime`, `status`)
		// 			values (1,'System Management',0,'system',NULL,NULL,1,'0000-00-00 00:00:00',1),
		// 		         (2,'Role Management',1,'role',NULL,NULL,2,'0000-00-00 00:00:00',1),
		// 		         (3,'Delete Users',1,'delete_user',NULL,NULL,3,'0000-00-00 00:00:00',1),
		// 		         (4,'Lock/Unlock Users',1,'lock_user',NULL,NULL,4,'0000-00-00 00:00:00',1),
		// 		         (5,'Reset Password',1,'reset_password',NULL,NULL,5,'0000-00-00 00:00:00',1),
		// 		         (6,'IP Rules',1,'ip',NULL,NULL,6,'0000-00-00 00:00:00',1),
		// 		         (7,'Email Setting',1,'email_setting',NULL,NULL,7,'0000-00-00 00:00:00',1),
		// 		         (8,'Currency Setting',1,'currency_setting',NULL,NULL,8,'0000-00-00 00:00:00',1),
		// 		         (9,'User Logs Report',1,'user_logs_report',NULL,NULL,9,'0000-00-00 00:00:00',1),
		// 		         (10,'Affliliate Domain Setting',1,'aff_domain_setting',NULL,NULL,10,'0000-00-00 00:00:00',1),
		// 		         (11,'Duplicate Account Setting',1,'duplicate_account_setting',NULL,NULL,11,'0000-00-00 00:00:00',1),

		// 		         (12,'Member Management',0,'player',NULL,NULL,12,'0000-00-00 00:00:00',1),
		// 		         (13,'All Members',12,'player_list',NULL,NULL,13,'0000-00-00 00:00:00',1),
		// 		         (14,'VIP Setting',12,'vip_group_setting',NULL,NULL,14,'0000-00-00 00:00:00',1),
		// 		         (15,'Tagged Members',12,'tag_player',NULL,NULL,15,'0000-00-00 00:00:00',1),
		// 		         (16,'Tag Management',12,'taggedlist',NULL,NULL,16,'0000-00-00 00:00:00',1),
		// 		         (17,'Batch Create',12,'account_process',NULL,NULL,17,'0000-00-00 00:00:00',1),
		// 		         (18,'Friend Referral',12,'friend_referral_player',NULL,NULL,18,'0000-00-00 00:00:00',1),
		// 		         (19,'Registration Setting',12,'registration_setting',NULL,NULL,19,'0000-00-00 00:00:00',1),
		// 		         (20,'View/Edit Member Password',12,'edit_player_password',NULL,NULL,20,'0000-00-00 00:00:00',1),
		// 		         (21,'Adjust Member Level',12,'adjust_player_level',NULL,NULL,21,'0000-00-00 00:00:00',1),
		// 		         (22,'Lock/Unlock Member in Website',12,'lock_player',NULL,NULL,22,'0000-00-00 00:00:00',1),
		// 		         (23,'Block/Unblock Member in Game',12,'block_player',NULL,NULL,23,'0000-00-00 00:00:00',1),
		// 		         (24,'Kick Member in website',12,'kick_player_in_website',NULL,NULL,24,'0000-00-00 00:00:00',1),
		// 		         (25,'View Member Contact Information (Email)',12,'view_player_contactinfo_em',NULL,NULL,25,'0000-00-00 00:00:00',2),
		// 		         (26,'View Member Contact Information (IM)',12,'view_player_contactinfo_im',NULL,NULL,26,'0000-00-00 00:00:00',2),
		// 		         (27,'View Member Contact Information (Contact #)',12,'view_player_contactinfo_cn',NULL,NULL,27,'0000-00-00 00:00:00',2),
		// 		         (28,'View Member Verification Question',12,'view_player_verification_question',NULL,NULL,28,'0000-00-00 00:00:00',1),
		// 		         (29,'View Member Verification Answer',12,'view_player_verification_answer',NULL,NULL,29,'0000-00-00 00:00:00',1),
		// 		         (30,'Add/Edit/Delete/Enable/Disable Member Bank Info',12,'player_bank_info_control',NULL,NULL,30,'0000-00-00 00:00:00',1),
		// 		         (31,'Edit Member Personal Information',12,'edit_player_personal_info','','',31,'0000-00-00 00:00:00',1),
		// 		         (32,'Player Contact Info',12,'player_contact_info',NULL,NULL,32,'0000-00-00 00:00:00',1),

		// 		         (33,'CS Management',0,'cs',NULL,NULL,33,'0000-00-00 00:00:00',1),
		// 		         (34,'Messages',33,'chat',NULL,NULL,34,'0000-00-00 00:00:00',1),
		// 		         (35,'Message History',33,'message_history',NULL,NULL,35,'0000-00-00 00:00:00',1),

		// 		         (36,'Report Management',0,'report',NULL,NULL,36,'0000-00-00 00:00:00',1),
		// 		         (37,'Summary Report',36,'summary_report','','',37,'0000-00-00 00:00:00',1),
		// 		         (38,'Member Report',36,'player_report','','',38,'0000-00-00 00:00:00',1),
		// 		         (39,'Games Report',36,'game_report','','',39,'0000-00-00 00:00:00',1),
		// 		         (40,'API Issue Report',36,'api_report','','',40,'0000-00-00 00:00:00',1),
		// 		         (41,'Payment Report',36,'payment_report','','',41,'0000-00-00 00:00:00',1),
		// 		         (42,'Promotion Report',36,'promotion_report','','',42,'0000-00-00 00:00:00',1),
		// 		         (43,'Export Report',36,'export_report','','',43,'0000-00-00 00:00:00',1),

		// 		         (44,'Affiliate Management',0,'affiliate','','',44,'0000-00-00 00:00:00',1),
		// 		         (45,'Affiliate List',44,'view_affiliates','','',45,'0000-00-00 00:00:00',1),
		// 		         (46,'Affiliate List (Edit Affiliate Information)',44,'edit_affiliate_info','','',46,'0000-00-00 00:00:00',1),
		// 		         (47,'Affiliate List (Edit Affiliate Term)',44,'edit_affiliate_term','','',47,'0000-00-00 00:00:00',1),
		// 		         (48,'Affiliate List (Create Affiliate Tracking Code)',44,'add_affiliate_code','','',48,'0000-00-00 00:00:00',1),
		// 		         (49,'Affiliate Monthly Earnings',44,'affiliate_earnings','','',49,'0000-00-00 00:00:00',1),
		// 		         (50,'Affiliate Payment',44,'affiliate_payments','','',50,'0000-00-00 00:00:00',1),
		// 		         (51,'Affiliate Banner',44,'banner_settings','','',51,'0000-00-00 00:00:00',1),
		// 		         (52,'Affiliate Tag',44,'affiliate_tag','','',52,'0000-00-00 00:00:00',1),
		// 		         (53,'Affiliate Statistics',44,'affiliate_statistics','','',53,'0000-00-00 00:00:00',1),
		// 		         (54,'Affiliate Terms Default Setup',44,'affiliate_terms','','',54,'0000-00-00 00:00:00',1),

		// 		         (55,'Marketing Management',0,'marketing','','',55,'0000-00-00 00:00:00',1),
		// 		         (56,'Promo Rules Settings',55,'promo_rules_setting',NULL,NULL,56,'0000-00-00 00:00:00',1),
		// 		         (57,'Promo Manager',55,'promocms','','',57,'0000-00-00 00:00:00',1),
		// 		         (58,'Promo Request List (Application)',55,'promoapp_list','','',58,'0000-00-00 00:00:00',1),
		// 		         (59,'Promo Request List (Cancellation)',55,'promocancel_list','','',59,'0000-00-00 00:00:00',1),
		// 		         (60,'Member List With Active Promo',55,'promoplayer_list','','',60,'0000-00-00 00:00:00',1),
		// 		         (61,'Game Logs',55,'gamelogs','','',61,'0000-00-00 00:00:00',1),
		// 		         (62,'Marketing Setting',55,'marketing_setting','','',62,'0000-00-00 00:00:00',1),
		// 		         (63,'Friend Referral Settings',55,'friend_referral_setting',NULL,NULL,63,'0000-00-00 00:00:00',1),
		// 		         (64,'Cashback Settings',55,'cashback_setting',NULL,NULL,64,'0000-00-00 00:00:00',1),
		// 		         (65,'Promo Category Settings',55,'promo_category_setting',NULL,NULL,65,'0000-00-00 00:00:00',1),
		// 		         (66,'Promo Cancellation Settings',55,'promo_cancellation_setting',NULL,NULL,66,'0000-00-00 00:00:00',1),
		// 		         (67,'Duplicate Account Checker Settings',55,'duplicate_account_checker_setting',NULL,NULL,67,'0000-00-00 00:00:00',1),

		// 		         (68,'Payment Management',0,'payment','','',68,'0000-00-00 00:00:00',1),
		// 		         (69,'Transactions',68,'transaction_report',NULL,NULL,69,'0000-00-00 00:00:00',1),
		// 		         (70,'Deposit List',68,'deposit_list','','',70,'0000-00-00 00:00:00',1),
		// 		         (71,'Auto 3rd Party Deposit List',68,'payment_auto3rdparty_deposit_list','','',71,'0000-00-00 00:00:00',2),
		// 		         (72,'Manual 3rd Party Deposit List',68,'payment_manual3rdparty_deposit_list','','',72,'0000-00-00 00:00:00',2),
		// 		         (73,'Withdrawal List',68,'payment_withdrawal_list','','',73,'0000-00-00 00:00:00',1),
		// 		         (74,'Member Adjust Balance',68,'payment_player_adjustbalance','','',74,'0000-00-00 00:00:00',1),
		// 		         (75,'Payment Settings',68,'payment_settings','','',75,'0000-00-00 00:00:00',1),
		// 		         (76,'Collection Account',68,'collection_account','','',76,'0000-00-00 00:00:00',1),
		// 		         (77,'Compensation Setting',68,'compensation_setting','','',77,'0000-00-00 00:00:00',1),
		// 		         (78,'Previous Balances Checking Setting',68,'previous_balances_checking_setting',NULL,NULL,78,'0000-00-00 00:00:00',1),
		// 		         (79,'Non-promo Withdraw Setting',68,'nonpromo_withdraw_setting',NULL,NULL,79,'0000-00-00 00:00:00',1),

		// 		         (80,'CMS Management',0,'cms','','',80,'0000-00-00 00:00:00',1),
		// 		         (81,'News/Announcements Manager',80,'view_news','','',81,'0000-00-00 00:00:00',1),
		// 		         (82,'Email Manager',80,'emailcms','','',82,'0000-00-00 00:00:00',1),
		// 		         (83,'Generate Sites',80,'generate_sites',NULL,NULL,83,'2015-07-07 08:00:00',1);
		// 		         ");
	}

	public function down() {
		// $this->db->query("DELETE FROM `functions`");
	}
}