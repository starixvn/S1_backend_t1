<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_super_report_201703161437 extends CI_Migration {

	public function up() {

		//===super_summary_report===============================================
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'new_players' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'total_players' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'first_deposit_count' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'second_deposit_count' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'total_deposit' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_deposit' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_withdraw' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_bonus' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_cashback' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_bank_cash_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_bet' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_win' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_loss' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'gross_payout' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'net_payout' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'affiliate_commission' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_summary_report');

		$this->db->query('create index idx_report_date on super_summary_report(report_date)');
		//===super_summary_report===============================================

		//===super_player_report===============================================
		//daily
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'registered_by' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'total_deposit_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_cashback_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_referral_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_manual_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_first_deposit' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_second_deposit' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_deposit' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_withdraw' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_player_report');

		$this->db->query('create index idx_report_date on super_player_report(report_date)');
		$this->db->query('create index idx_player_id on super_player_report(player_id)');
		$this->db->query('create index idx_player_username on super_player_report(player_username)');
		$this->db->query('create index idx_player_level on super_player_report(player_level)');
		//===super_player_report===============================================

		//===super_game_report===============================================
		//hourly
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'report_hour' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => false,
			),
			//YYYYMMDDHH
			'report_date_hour' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'affiliate_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'total_bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_win_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_loss_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'total_result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_game_report');

		$this->db->query('create index idx_report_date_hour on super_game_report(report_date_hour)');
		$this->db->query('create index idx_player_id on super_game_report(player_id)');
		$this->db->query('create index idx_player_username on super_game_report(player_username)');
		$this->db->query('create index idx_affiliate_username on super_game_report(affiliate_username)');
		$this->db->query('create index idx_game_platform_id on super_game_report(game_platform_id)');
		$this->db->query('create index idx_game_type_id on super_game_report(game_type_id)');
		$this->db->query('create index idx_game_description_id on super_game_report(game_description_id)');
		//===super_game_report===============================================

		//===super_payment_report===============================================
		//daily
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'transaction_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'transaction_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'payment_account_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_payment_report');

		$this->db->query('create index idx_report_date on super_payment_report(report_date)');
		$this->db->query('create index idx_player_id on super_payment_report(player_id)');
		$this->db->query('create index idx_player_username on super_payment_report(player_username)');
		$this->db->query('create index idx_transaction_id on super_payment_report(transaction_id)');
		$this->db->query('create index idx_transaction_type on super_payment_report(transaction_type)');
		$this->db->query('create index idx_payment_account_type on super_payment_report(payment_account_type)');
		//===super_payment_report===============================================

		//===super_promotion_report===============================================
		//daily
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'promorule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'promorule_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'promorule_status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_promotion_report');

		$this->db->query('create index idx_report_date on super_promotion_report(report_date)');
		$this->db->query('create index idx_player_id on super_promotion_report(player_id)');
		$this->db->query('create index idx_player_username on super_promotion_report(player_username)');
		$this->db->query('create index idx_promorule_id on super_promotion_report(promorule_id)');
		//===super_promotion_report===============================================

		//===super_cashback_report===============================================
		//daily
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => false,
			),
			'backoffice_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
			'report_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'history_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'withdraw_condition_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'paid_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'paid_flag' => array(
				'type' => 'INT',
				'null' => false,
				'default'=>0,
			),
			'paid_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('super_cashback_report');

		$this->db->query('create index idx_report_date on super_cashback_report(report_date)');
		$this->db->query('create index idx_player_id on super_cashback_report(player_id)');
		$this->db->query('create index idx_player_username on super_cashback_report(player_username)');
		$this->db->query('create index idx_game_platform_id on super_cashback_report(game_platform_id)');
		$this->db->query('create index idx_game_type_id on super_cashback_report(game_type_id)');
		$this->db->query('create index idx_game_description_id on super_cashback_report(game_description_id)');
		//===super_cashback_report===============================================


	}

	public function down() {
		$this->dbforge->drop_table('super_summary_report');
		$this->dbforge->drop_table('super_player_report');
		$this->dbforge->drop_table('super_game_report');
		$this->dbforge->drop_table('super_payment_report');
		$this->dbforge->drop_table('super_promotion_report');
		$this->dbforge->drop_table('super_cashback_report');
	}
}