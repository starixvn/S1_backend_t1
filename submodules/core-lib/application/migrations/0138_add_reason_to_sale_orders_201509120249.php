<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_reason_to_sale_orders_201509120249 extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {
		$fields = array(
			'reason' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'show_reason_to_player' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'reason');
		$this->dbforge->drop_column($this->tableName, 'show_reason_to_player');
	}
}