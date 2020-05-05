<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_playerupdatehistory extends CI_Migration {

	public function up() {
		$fields = array(
			'operator' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		);
		$this->dbforge->add_column('playerupdatehistory', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerupdatehistory', 'operator');
	}
}