<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerdetails_20170407 extends CI_Migration {

	public function up() {
		$fields = array(
			'duplicate_record_exempted' => array(
				'type' => 'INT',
				'default' => 0,
				'null' => false,
			),
		);
		$this->dbforge->add_column('playerdetails', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('playerdetails', 'duplicate_record_exempted');
	}
}