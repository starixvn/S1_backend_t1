<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_manual_subtract_balance_tag_201708211123 extends CI_Migration {

	private $tableName = 'manual_subtract_balance_tag';

	public function up() {
		$fields = array(
			'adjust_tag_description' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,				
				'null' => true,
			),
			'createBy' => array(
				'type' => 'INT',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'adjust_tag_description');
		$this->dbforge->drop_column($this->tableName, 'createBy');
		$this->dbforge->drop_column($this->tableName, 'status');
	}
}

////END OF FILE////