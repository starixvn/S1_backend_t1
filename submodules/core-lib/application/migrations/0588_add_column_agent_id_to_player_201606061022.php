<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agent_id_to_player_201606061022 extends CI_Migration {

	private $tableName = 'player';

	function up() {
		$this->dbforge->add_column($this->tableName, [
			'agent_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'agent_id');
	}
}
