<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_sale_orders extends CI_Migration {

	protected $tableName = "sale_orders";

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'system_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'payment_kind' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),

		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////////////