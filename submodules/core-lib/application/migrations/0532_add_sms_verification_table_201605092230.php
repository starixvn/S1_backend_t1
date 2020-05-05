<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sms_verification_table_201605092230 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			),
			'create_time' => array(
				'type' => 'DATETIME',
				'null' => false,
				'default' => '0000-00-00 00:00:00',
			),
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '6',
				'null' => false,
			),
			'verified' => array(
				'type' => 'BIT',
				'constraint' => '1',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('sms_verification');
	}

	public function down() {
		$this->dbforge->drop_table('sms_verification');
	}
}
