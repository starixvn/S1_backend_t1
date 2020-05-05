<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_others_status_to_sale_orders extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'status_bank' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'status_payment_gateway' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'status_bank');
		$this->dbforge->drop_column($this->tableName, 'status_payment_gateway');
		$this->dbforge->drop_column($this->tableName, 'currency');
	}
}
