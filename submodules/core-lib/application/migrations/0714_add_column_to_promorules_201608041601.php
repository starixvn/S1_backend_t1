<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201608041601 extends CI_Migration {
	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'add_withdraw_condition_as_bonus_condition' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'expire_days' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'add_withdraw_condition_as_bonus_condition');
		$this->dbforge->drop_column($this->tableName, 'expire_days');
	}
}
