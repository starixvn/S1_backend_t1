<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_gsmg_game_logs_201609290638 extends CI_Migration {

	private $tableName = 'gsmg_game_logs';

	public function up() {
		$fields = array(
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'game_name');
	}
}
