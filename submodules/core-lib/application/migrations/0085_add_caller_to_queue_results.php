<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_caller_to_queue_results extends CI_Migration {

	private $tableName = 'queue_results';

	public function up() {
		$fields = array(
			//1=admin, 2=player, 3=system
			'caller_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'caller' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'caller_type');
		$this->dbforge->drop_column($this->tableName, 'caller');
	}
}

///END OF FILE//////