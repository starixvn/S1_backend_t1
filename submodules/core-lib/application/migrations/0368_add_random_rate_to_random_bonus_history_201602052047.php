<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_random_rate_to_random_bonus_history_201602052047 extends CI_Migration {

	private $tableName = 'random_bonus_history';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'random_rate' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'random_rate');
	}
}

///END OF FILE//////////