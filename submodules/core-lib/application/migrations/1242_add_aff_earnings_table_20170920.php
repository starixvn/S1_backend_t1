<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_aff_earnings_table_20170920 extends CI_Migration {

	private $tableName = 'affiliate_game_platform_earnings';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'period' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false,
			),
			'start_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'end_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'game_platform_shares' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_revenue' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_rate' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_gross_revenue' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_admin_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_other_fees' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_net_revenue' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_commission_rate' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'game_platform_commission_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'paid_flag' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
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
		$this->db->query("DROP TABLE IF EXISTS " . $this->tableName);
		$this->dbforge->create_table($this->tableName);
		$this->db->query("ALTER TABLE `{$this->tableName}` ADD UNIQUE `affiliate_game_platform_earnings`(`affiliate_id`, `game_platform_id`, `period`, `start_date`)");
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}

}