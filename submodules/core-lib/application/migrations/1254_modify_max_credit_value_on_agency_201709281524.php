<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_max_credit_value_on_agency_201709281524 extends CI_Migration {

	public function up() {
		$fields = array(
			'credit_limit' => array(
				'name' => 'credit_limit',
				'type' => 'decimal(20,2)',
				'null' => false,
				'default' => '0.00',
			),
			'available_credit' => array(
				'name' => 'available_credit',
				'type' => 'decimal(20,2)',
				'null' => false,
				'default' => '0.00',
			),
		);
		$this->dbforge->modify_column('agency_agents', $fields);
		$this->dbforge->modify_column('agency_structures', $fields);
	}

	public function down() {
		// $this->dbforge->drop_column('agency_agents', 'can_view_agents_list_and_players_list');
		// $this->dbforge->drop_column('agency_structures', 'can_view_agents_list_and_players_list');
	}
}
