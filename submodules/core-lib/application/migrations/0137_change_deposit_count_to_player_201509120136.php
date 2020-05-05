<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_deposit_count_to_player_201509120136 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'approved_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'declined_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'total_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);

		//update to 0
		$this->db->update($this->tableName, array(
			'approved_deposit_count' => 0,
			'declined_deposit_count' => 0,
			'total_deposit_count' => 0,
		));

	}

	public function down() {
	}
}