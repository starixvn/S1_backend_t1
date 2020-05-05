<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_credit_mode_to_players_20161003 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'credit_mode' => array(
				'type' => 'INT',
				'null' => false,
				'default'=> 0,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'credit_mode');

	}
}
