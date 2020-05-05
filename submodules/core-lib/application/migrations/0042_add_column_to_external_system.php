<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_external_system extends CI_Migration {

	public function up() {
		$fields = array(
			'last_sync_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'last_sync_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'last_sync_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('external_system', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('external_system', 'last_sync_datetime');
		$this->dbforge->drop_column('external_system', 'last_sync_id');
		$this->dbforge->drop_column('external_system', 'last_sync_details');

	}
}