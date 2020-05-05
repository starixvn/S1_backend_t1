<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_daily_balance_201711151637 extends CI_Migration {

	public function up() {
		# agency_player_game_platforms ###############################################################################################################
		$this->dbforge->drop_table('player_daily_balance');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'balance_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'total_balance' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('player_daily_balance');

	}

	public function down() {
		$this->dbforge->drop_table('player_daily_balance');
	}
}
