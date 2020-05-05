<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_notification_setting extends CI_Migration {

	protected $tableName = "notification_setting";

	public function up() {

		$this->dbforge->add_field(array(
			'notification_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'notification_id' => array(
				'type' => 'INT',
				'null' => false,
			)
		));

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////////////