<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_20160910 extends CI_Migration {
	
	private $tableName = 'agency_agents';
	
	public function up() {
		$fields = array(
			'show_bet_limit_template' => array(
				'type' => 'INT',
				'default' => 1,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'show_bet_limit_template');
	}

}
