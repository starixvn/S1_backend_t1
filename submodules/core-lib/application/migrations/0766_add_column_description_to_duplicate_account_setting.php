<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_description_to_duplicate_account_setting extends CI_Migration {

	public function up() {
		$fields = array(
			'description' => array(
				'type' => 'TEXT',
				'null' => true,
				'constraint' => 200,
			),
		);
		$this->dbforge->add_column('duplicate_account_setting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('duplicate_account_setting', 'description');
	}
}
