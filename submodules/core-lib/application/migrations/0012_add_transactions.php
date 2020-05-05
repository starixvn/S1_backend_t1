<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_transactions extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
				'constraint' => '10',
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'transaction_type' => array(
				'type' => 'INT',
				'null' => false,
				'constraint' => '1',
			),
			'from_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => false,
				'unsigned' => TRUE,
			),
			'from_type' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => false,
			),
			'to_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => false,
				'unsigned' => TRUE,
			),
			'to_type' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => false,
			),
			'external_transaction_id' => array(
				'type' => 'varchar',
				'constraint' => '150',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'note' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'before_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'sub_wallet_id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'unsigned' => TRUE,
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'constraint' => '2',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('transactions');
	}

	public function down() {
		$this->dbforge->drop_table('transactions');
	}
}
