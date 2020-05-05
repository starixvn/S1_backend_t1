<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agent_and_structure_game_types_table_20171215 extends CI_Migration {

	public function up() {
		
		$this->dbforge->drop_column('agency_structures', 'platform_fee');
		$this->dbforge->drop_column('agency_agents', 'platform_fee');

		$fields = array(
			'platform_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_structure_game_types', $fields);

		$fields = array(
			'platform_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_agent_game_types', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('agency_structure_game_types', 'platform_fee');
		$this->dbforge->drop_column('agency_agent_game_types', 'platform_fee');
	}
	
}
