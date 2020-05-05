<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_structures_table_20171215 extends CI_Migration {

	public function up() {
		
		$fields = array(
			'platform_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_structures', $fields);

		$fields = array(
			'platform_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('agency_structures', 'platform_fee');
		$this->dbforge->drop_column('agency_agents', 'platform_fee');
	}
	
}
