<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sms_verification_201704192008 extends CI_Migration {

	public function up() {
		$fields = array(
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => true,
			),
		);
		$this->dbforge->add_column('sms_verification', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('sms_verification', 'ip');
	}
}
