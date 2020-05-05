<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_columns_in_ag_game_log_to_allow_null extends CI_Migration {

	public function up() {
		$fields = array(
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'agentcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'netamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
		);
		$this->dbforge->modify_column('ag_game_logs', $fields);
	}

	public function down() {
		$fields = array(
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'agentcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'netamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
		);
		$this->dbforge->modify_column('ag_game_logs', $fields);
	}
}