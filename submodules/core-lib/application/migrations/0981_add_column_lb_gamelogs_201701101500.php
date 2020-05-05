<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_lb_gamelogs_201701101500 extends CI_Migration {
	public function up() {
		$fields = array(
			'bet_platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'bet_previous_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'PlayerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
		);
		$this->dbforge->add_column('lb_game_logs', $fields);

		$alterFields = array(
			'bet_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			)
		);
		$this->dbforge->modify_column('lb_game_logs', $alterFields);
	}

	public function down() {
		$this->dbforge->drop_column('lb_game_logs', 'bet_platform');
		$this->dbforge->drop_column('lb_game_logs', 'bet_previous_id');
		$this->dbforge->drop_column('lb_game_logs', 'Username');
		$this->dbforge->drop_column('lb_game_logs', 'PlayerId');
	}
}