<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_exception_order extends CI_Migration {

	protected $tableName = "exception_order";

	public function up() {

		$add_cols=array(
			'player_bank_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'player_bank_account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'player_bank_account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'player_bank_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'collection_bank_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'collection_bank_account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'collection_bank_account_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'collection_bank_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'response_content' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		);

		$this->dbforge->add_column($this->tableName, $add_cols);

		$changed_cols=[
			'amount' => array(
				'name' => 'amount',
				'type' => 'DOUBLE',
				'null' => true,
			),
			'external_order_id' => array(
				'name' => 'external_order_id',
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'external_order_datetime' => array(
				'name' => 'external_order_datetime',
				'type' => 'DATETIME',
				'null' => true,
			),
			'sale_order_id' => array(
				'name' => 'sale_order_id',
				'type' => 'INT',
				'null' => true,
			),

		];
		$this->dbforge->modify_column($this->tableName, $changed_cols);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'player_bank_name');
		$this->dbforge->drop_column($this->tableName, 'player_bank_account_name');
		$this->dbforge->drop_column($this->tableName, 'player_bank_account_number');
		$this->dbforge->drop_column($this->tableName, 'player_bank_address');
		$this->dbforge->drop_column($this->tableName, 'collection_bank_name');
		$this->dbforge->drop_column($this->tableName, 'collection_bank_account_name');
		$this->dbforge->drop_column($this->tableName, 'collection_bank_account_number');
		$this->dbforge->drop_column($this->tableName, 'collection_bank_address');
		$this->dbforge->drop_column($this->tableName, 'response_content');
	}
}

///END OF FILE//////////////////