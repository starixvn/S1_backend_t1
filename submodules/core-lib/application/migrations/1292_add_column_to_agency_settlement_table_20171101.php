<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_table_20171101 extends CI_Migration {

	public function up() {
		$fields = array(
			'transactions' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'admin' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_settlement', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_settlement', 'transactions');
		$this->dbforge->drop_column('agency_settlement', 'admin');
	}
	
}
