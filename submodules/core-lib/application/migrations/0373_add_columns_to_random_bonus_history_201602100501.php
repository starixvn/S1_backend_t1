<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_random_bonus_history_201602100501 extends CI_Migration {

	private $tableName = 'random_bonus_history';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'bonus_mode' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bonus_mode');
	}
}

///END OF FILE//////////