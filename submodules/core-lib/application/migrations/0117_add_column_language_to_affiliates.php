<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_language_to_affiliates extends CI_Migration {

	public function up() {
		$fields = array(
			'language' => array(
				'type' => 'VARCHAR',
				'constraint' => '9',
				'null' => true,
			),
		);
		$this->dbforge->add_column('affiliates', $fields, 'currency');
	}

	public function down() {
		$this->dbforge->drop_column('affiliates', 'language');
	}
}