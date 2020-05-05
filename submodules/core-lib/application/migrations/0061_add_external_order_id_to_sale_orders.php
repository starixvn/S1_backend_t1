<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_external_order_id_to_sale_orders extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'external_order_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'external_order_id');
	}
}
