<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_list_201602200236 extends CI_Migration {

	private $tableName = 'external_system_list';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'extra_info' => array(
				'type' => 'VARCHAR',
				'constraint' => '4000',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'extra_info');
	}
}

///END OF FILE//////////
