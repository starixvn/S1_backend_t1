<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_point_transactions_201602220549 extends CI_Migration {

	private $tableName = 'point_transactions';
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'point' => array(
				'type' => 'INT',
				'null' => true,
			),
			'transaction_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'from_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'from_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'to_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'to_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'external_transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
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
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'order_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'player_promo_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'promo_category' => array(
				'type' => 'INT',
				'null' => true,
			),
			'total_before_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'display_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}