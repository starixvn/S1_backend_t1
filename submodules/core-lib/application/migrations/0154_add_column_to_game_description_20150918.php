<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_game_description_20150918 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'no_cash_back' => array(
				'type' => 'VARCHAR',
				'constraint' => 1,
				'null' => true,
			),
			'void_bet' => array(
				'type' => 'VARCHAR',
				'constraint' => 1,
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'no_cash_back');
		$this->dbforge->drop_column($this->tableName, 'void_bet');
	}
}