<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_rolling_comm_in_player_201607281855 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'rolling_comm');
	}
}
