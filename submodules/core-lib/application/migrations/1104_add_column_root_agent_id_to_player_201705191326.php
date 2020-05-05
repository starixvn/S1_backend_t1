<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_root_agent_id_to_player_201705191326 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'root_agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'root_agent_id');
	}
}