<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_domain_table_201709260201 extends CI_Migration {

	public function up() {
		$fields = array(
			'show_to_agent_type' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_column('agency_domain', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_domain', 'show_to_agent_type');
	}
}
