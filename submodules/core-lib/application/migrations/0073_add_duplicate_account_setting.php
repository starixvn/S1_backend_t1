<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_duplicate_account_setting extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'item_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'rate_exact' => array(
				'type' => 'INT',
				'default' => '0',
				'null' => true,
			),
			'rate_similar' => array(
				'type' => 'INT',
				'default' => '0',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('duplicate_account_setting');
	}

	public function down() {
		$this->dbforge->drop_table('duplicate_account_setting');
	}
}
