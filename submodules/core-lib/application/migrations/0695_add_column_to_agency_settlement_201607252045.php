<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_201607252045 extends CI_Migration {
	private $tableName = 'agency_settlement';
	public function up() {
		$fields = array(
			'roll_comm_income' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'roll_comm_income');
	}
}
