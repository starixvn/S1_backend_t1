<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsetting_201705132343 extends CI_Migration {

	public function up() {
		$fields = array(
			'image' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		);

		$this->dbforge->add_column('vipsetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('vipsetting', 'image');
	}
}