<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_daily_balance_report_201711150942 extends CI_Migration {

	public function up() {
		# agency_player_game_platforms ###############################################################################################################
		$this->dbforge->drop_table('daily_balance_report');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'data_date' => array(
				'type' => 'DATE',
				'null' => false,
			),
			'data_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'data_key_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'data_value' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('daily_balance_report');

	}

	public function down() {
		$this->dbforge->drop_table('daily_balance_report');
	}
}
