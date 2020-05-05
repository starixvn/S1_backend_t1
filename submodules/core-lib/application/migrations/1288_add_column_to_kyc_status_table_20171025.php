<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_kyc_status_table_20171025 extends CI_Migration {

	private $tableName = 'kyc_status';

	public function up() {
		$fields = array(
			'target_function' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'target_function');
	}
	
}
