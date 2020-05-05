<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_binding_player_id_to_agency_agents_table_201711211340 extends CI_Migration {

	private $tableName = 'agency_agents';

	public function up() {
        $fields = array(
			'binding_player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'binding_player_id');
	}
}
