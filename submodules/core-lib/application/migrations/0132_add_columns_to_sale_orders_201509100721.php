<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_to_sale_orders_201509100721 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'geo_location' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'timeout' => array(
				'type' => 'INT',
				'null' => true,
			),
			'timeout_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'payment_flag' => array(
				'type' => 'INT',
				'null' => true,
			),
			'wallet_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'ip');
		$this->dbforge->drop_column($this->tableName, 'geo_location');
		$this->dbforge->drop_column($this->tableName, 'timeout');
		$this->dbforge->drop_column($this->tableName, 'timeout_at');
		$this->dbforge->drop_column($this->tableName, 'payment_account_id');
		$this->dbforge->drop_column($this->tableName, 'payment_flag');
		$this->dbforge->drop_column($this->tableName, 'wallet_id');
	}
}
