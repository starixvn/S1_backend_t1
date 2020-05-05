<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_bank_type extends CI_Migration {

	public function up() {
		$fields = array(
			'remarks' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		);
		$this->dbforge->add_column('banktype', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('banktype', 'remarks');
	}
}