<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_bbin_game_logs_201511110611 extends CI_Migration {

	private $tableName = 'bbin_game_logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'uptime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'order_date' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'uptime');
		$this->dbforge->drop_column($this->tableName, 'order_date');
	}
}

///END OF FILE//////////