<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_type_2016003072330 extends CI_Migration {

	private $tableName = 'game_type';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'auto_add_new_game' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 1
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'auto_add_new_game');
	}
}
