<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_imslots_gamelogs_201610071418 extends CI_Migration {

	private $tableName = 'imslots_game_logs';

	public function up() {
		//modify column
		$fields = array(
			'PlayerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'RoundId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			)
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
	}
}