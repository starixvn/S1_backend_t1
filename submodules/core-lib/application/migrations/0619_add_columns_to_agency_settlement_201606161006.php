<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_settlement_201606161006 extends CI_Migration {
	private $tableName = 'agency_settlement';
	public function up() {
		$fields = array(
			'settlement_period' => array(
				'type' => 'VARCHAR',
				'constraint' => 36,
				'null' => TRUE,
			),
			'settlement_date_from' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'settlement_date_to' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'settlement_period');
		$this->dbforge->drop_column($this->tableName, 'settlement_date_from');
		$this->dbforge->drop_column($this->tableName, 'settlement_date_to');
	}
}
