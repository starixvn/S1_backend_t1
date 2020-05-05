<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_external_system extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'system_name' => array(
				'type' => 'varchar',
				'constraint' => '200',
				'null' => false,
			),
			'note' => array(
				'type' => 'varchar',
				'constraint' => '1000',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('external_system');
	}

	public function down() {
		$this->dbforge->drop_table('external_system');
	}
}
