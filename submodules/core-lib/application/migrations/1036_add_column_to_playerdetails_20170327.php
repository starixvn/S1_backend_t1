<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerdetails_20170327 extends CI_Migration {

	public function up() {
		$fields = array(
			'proof_filename' => array(
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => true,
			),
		);
		$this->dbforge->add_column('playerdetails', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerdetails', 'proof_filename');
	}
}