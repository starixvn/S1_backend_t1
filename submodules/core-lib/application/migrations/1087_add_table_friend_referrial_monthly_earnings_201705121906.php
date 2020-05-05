<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_friend_referrial_monthly_earnings_201705121906 extends CI_Migration {

	private $tableName = 'friend_referrial_monthly_earnings';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'year_month' => array(
				'type' => 'INT',
				'null' => false,

			),
			'player_id' => array(
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
		$this->db->query('ALTER TABLE `'.$this->tableName.'` ADD UNIQUE `friend_referrial_monthly_earnings_year_month`(`player_id`, `year_month`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}