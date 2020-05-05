<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_isb_game_logs_201609070733 extends CI_Migration {

	private $tableName = 'isb_game_logs';

	public function up() {
		$fields = array(
			'win_flag' => array(
				'type' => 'INT',
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		$this->dbforge->modify_column($this->tableName, array(
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '8',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'win_flag');
	}

}