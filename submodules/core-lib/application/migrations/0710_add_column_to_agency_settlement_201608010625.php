<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_201608010625 extends CI_Migration {
	private $tableName = 'agency_settlement';
	public function up() {
		$fields = array(
			'player_rolling_comm_payment_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
				'default' => '',
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'player_rolling_comm_payment_status');
	}
}
