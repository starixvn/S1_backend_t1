<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transfer_request_201707211337 extends CI_Migration {

	private $tableName = 'transfer_request';

	public function up() {
		$fields = array(
			'external_system_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'external_system_id');
		$this->dbforge->drop_column($this->tableName, 'agent_id');
	}
}

////END OF FILE////