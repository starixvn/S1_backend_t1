<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_count_random_bonus_daily_201602100153 extends CI_Migration {

	private $tableName = 'count_random_bonus_daily';
	const NORMAL = 1;
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'count' => array(
				'type' => 'INT',
				'null' => false,
			),
			'date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}