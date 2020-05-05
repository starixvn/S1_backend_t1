<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_delete_to_messages_table_20160719 extends CI_Migration {

	public function up() {

		$fields = array(
			'deleted' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'deleted_at' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'deleted_by' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_column('messages', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('messages', 'deleted');
		$this->dbforge->drop_column('messages', 'deleted_at');
		$this->dbforge->drop_column('messages', 'deleted_by');
	}
}