<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_notes_registered_by_to_affiliates_201601280018 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('affiliates', array(
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),
			'registered_by' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('affiliates', 'notes');
		$this->dbforge->drop_column('affiliates', 'registered_by');
	}
}