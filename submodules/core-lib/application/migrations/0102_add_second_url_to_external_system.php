<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_second_url_to_external_system extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'second_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'second_url');
	}
}
///END OF FILE