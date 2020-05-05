<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_to_sale_orders_201509111208 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {
		$fields = array(
			'original_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payment_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'payment_account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
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
			'processed_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'process_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'player_bank_details_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'player_payment_type_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'player_payment_account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'player_payment_account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'player_payment_branch_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'player_deposit_transaction_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'player_deposit_slip_path' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		//update default
		$this->db->set('currency', $this->config->item('default_currency'));
		$this->db->set('original_amount', 'amount', FALSE);
		$this->db->update($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'currency');
		$this->dbforge->drop_column($this->tableName, 'original_amount');
		$this->dbforge->drop_column($this->tableName, 'payment_type_name');
		$this->dbforge->drop_column($this->tableName, 'payment_account_name');
		$this->dbforge->drop_column($this->tableName, 'payment_account_number');
		$this->dbforge->drop_column($this->tableName, 'payment_branch_name');
		$this->dbforge->drop_column($this->tableName, 'processed_by');
		$this->dbforge->drop_column($this->tableName, 'process_time');
		$this->dbforge->drop_column($this->tableName, 'player_bank_details_id');
		$this->dbforge->drop_column($this->tableName, 'player_payment_type_name');
		$this->dbforge->drop_column($this->tableName, 'player_payment_account_name');
		$this->dbforge->drop_column($this->tableName, 'player_payment_account_number');
		$this->dbforge->drop_column($this->tableName, 'player_payment_branch_name');
		$this->dbforge->drop_column($this->tableName, 'player_deposit_transaction_code');
		$this->dbforge->drop_column($this->tableName, 'player_deposit_slip_path');
	}
}