<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_in_agency_agents_and_agency_structures_201606301500 extends CI_Migration {

	private $tableName1 = 'agency_structures';
	private $tableName2 = 'agency_agents';

	public function up() {
		$fields = array(
			'vip_groups' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => TRUE,
			),
			'vip_levels' => array(
				'type' => 'VARCHAR',
				'constraint' => 400,
				'null' => TRUE,
			),
        );

		$this->dbforge->add_column($this->tableName1, $fields);
		$this->dbforge->add_column($this->tableName2, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName1, 'vip_groups');
		$this->dbforge->drop_column($this->tableName1, 'vip_levels');

		$this->dbforge->drop_column($this->tableName2, 'vip_groups');
		$this->dbforge->drop_column($this->tableName2, 'vip_levels');
	}
}
