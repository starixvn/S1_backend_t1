<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_second_password_to_affiliates_201701072109 extends CI_Migration {

	public function up() {
		$fields = array(
			'second_password' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null'=>true,
			),
		);
		$this->dbforge->add_column('affiliates', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('affiliates', 'second_password');
	}
}