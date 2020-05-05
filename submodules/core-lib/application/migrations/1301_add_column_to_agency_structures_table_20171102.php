<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_structures_table_20171102 extends CI_Migration {

	public function up() {
		$fields = array(
			'min_rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_structures', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_structures', 'min_rolling_comm');
	}
	
}
