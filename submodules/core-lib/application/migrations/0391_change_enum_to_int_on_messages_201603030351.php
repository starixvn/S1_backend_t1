<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_enum_to_int_on_messages_201603030351 extends CI_Migration {

	private $tableName = 'messages';

	public function up() {

		$this->dbforge->drop_column($this->tableName, 'status');

		$this->dbforge->add_column($this->tableName, array(
			'status' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 4,
			),
		));

		$this->dbforge->drop_column('messagesdetails', 'status');

		$this->dbforge->add_column('messagesdetails', array(
			'status' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 1,
			),
		));

	}

	public function down() {
	}
}

///END OF FILE//////////
