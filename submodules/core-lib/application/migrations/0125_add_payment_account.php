<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OG-698
 *
 *
 */
class Migration_Add_payment_account extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE,
				'null' => false,
			),
			'payment_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'payment_account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'payment_account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'payment_branch_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'max_deposit_daily' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_deposit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'transaction_fee' => array(
				'type' => 'DOUBLE',
				'default' => 0,
				'null' => true,
			),
			'payment_order' => array(
				'type' => 'INT',
				'default' => 100,
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'default' => 1,
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'logo_link' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => false,
			),
			'external_system_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE,
				'null' => false,
			),
			'player_level_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('payment_account_player_level');
	}

	public function down() {
		$this->dbforge->drop_table('payment_account_player_level');
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE/////