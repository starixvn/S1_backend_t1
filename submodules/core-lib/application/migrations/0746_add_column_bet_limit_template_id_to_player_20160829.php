<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_bet_limit_template_id_to_player_20160829 extends CI_Migration {
	private $tableName = 'player';

	public function up() {
		$fields = array(
			'bet_limit_template_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bet_limit_template_status' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bet_limit_template_id');
		$this->dbforge->drop_column($this->tableName, 'bet_limit_template_status');
	}
}
