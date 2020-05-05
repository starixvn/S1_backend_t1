<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_aff_daily_earnings_table_20170914 extends CI_Migration {

	private $tableName = 'aff_daily_earnings';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'date' => array(
				'type' => 'DATE',
				'null' => false,

			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,

			),
			'active_players' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'total_players' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'gross_revenue' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'platform_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'bonus_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'cashback_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'transaction_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'admin_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'net_revenue' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'commission_percentage' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'commission_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'commission_from_sub_affiliates' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'total_commission' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'paid_flag' => array(
				'type' => 'INT',
				'null' => true,
			),
			'manual_flag' => array(
				'type' => 'INT',
				'null' => true,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'TIMESTAMP',
				'null' => false,

			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		$this->db->query('ALTER TABLE `aff_daily_earnings` ADD UNIQUE `aff_daily_earnings_date`(`affiliate_id`, `date`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}

}