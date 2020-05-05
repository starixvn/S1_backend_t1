<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_lb_bet_win_column_in_lb_game_logs_201512030642 extends CI_Migration {
	private $tableName = 'lb_game_logs';

	public function up() {
		//modify column
		$fields = array(
			'bet_win' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
		$fields = array(
			'bet_win' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}