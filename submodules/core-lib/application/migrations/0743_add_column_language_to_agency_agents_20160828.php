<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_language_to_agency_agents_20160828 extends CI_Migration {
	private $tableName = 'agency_agents';
	public function up() {
		$fields = array(
			'language' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'language');
	}
}
