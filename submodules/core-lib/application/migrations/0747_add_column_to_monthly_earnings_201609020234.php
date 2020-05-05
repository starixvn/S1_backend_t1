<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_monthly_earnings_201609020234 extends CI_Migration {
	private $tableName = 'monthly_earnings';

	public function up() {
		$fields = array(
			'count_active_player' => array(
				'type' => 'INT',
				'null' => true,
			),
			'income_json' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'count_active_player');
		$this->dbforge->drop_column($this->tableName, 'income_json');
	}
}
