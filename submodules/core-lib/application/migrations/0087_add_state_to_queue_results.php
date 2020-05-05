<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_state_to_queue_results extends CI_Migration {

	private $tableName = 'queue_results';

	public function up() {
		$fields = array(
			//1=admin, 2=player, 3=system
			'state' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'state');
	}
}

///END OF FILE//////