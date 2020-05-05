<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_deposit_count_to_player_201509112103 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'approved_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
			),
			'declined_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
			),
			'total_deposit_count' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'approved_deposit_count');
		$this->dbforge->drop_column($this->tableName, 'declined_deposit_count');
		$this->dbforge->drop_column($this->tableName, 'total_deposit_count');
	}
}