<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerdetails_201706061530 extends CI_Migration {

	public function up() {
		$fields = array(
			'address2' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
			'address3' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
		);
		$this->dbforge->add_column('playerdetails', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerdetails', 'address2');
		$this->dbforge->drop_column('playerdetails', 'address3');
	}
}