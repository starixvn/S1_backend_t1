<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_monthly_earnings_20161026 extends CI_Migration {

	private $tableName = "monthly_earnings";

	public function up() {
		$fields = array(
			'year_week' => array(
				'type' => 'VARCHAR',
				'null' => TRUE,
				'constraint' => 100,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'year_week');
	}
}
