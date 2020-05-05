<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsetting_20160801 extends CI_Migration {
	private $tableName = 'vipsetting';
	public function up() {
		$fields = array(
			'note' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'note');
	}
}
