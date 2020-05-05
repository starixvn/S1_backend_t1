<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_exception_order extends CI_Migration {

	protected $tableName = "exception_order";

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'external_system_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'external_order_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'external_order_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'sale_order_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
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