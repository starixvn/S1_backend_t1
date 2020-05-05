<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_table_20170925 extends CI_Migration {

	public function up() {
		$fields = array(
			'can_view_agents_list_and_players_list' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);
		$this->dbforge->add_column('agency_structures', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'can_view_agents_list_and_players_list');
		$this->dbforge->drop_column('agency_structures', 'can_view_agents_list_and_players_list');
	}
}
