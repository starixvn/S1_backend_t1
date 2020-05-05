<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ip_201607060215 extends CI_Migration {
	private $tableName = 'ip';
	public function up() {
		$fields = array(
			'remarks' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'remarks');
	}
}
