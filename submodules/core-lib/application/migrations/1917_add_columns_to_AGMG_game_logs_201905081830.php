<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_AGMG_game_logs_201905081830 extends CI_Migration {

	private $tableName = 'agmg_game_logs';

	public function up() {

		$fields = array(
			'creationtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'extra' => array(
                'type' => 'TEXT', # show $_POST and $_GET data
                'null' => true,
            ),
		);

		$this->dbforge->add_column($this->tableName, $fields);
		
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'creationtime');
		$this->dbforge->drop_column($this->tableName, 'updated_at');
		$this->dbforge->drop_column($this->tableName, 'extra');

	}
}