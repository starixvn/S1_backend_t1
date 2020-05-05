<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_table_20171006 extends CI_Migration {

	public function up() {
		$fields = array(
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_column('agency_settlement', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_settlement', 'game_platform_id');
		$this->dbforge->drop_column('agency_settlement', 'game_type_id');
	}
	
}
