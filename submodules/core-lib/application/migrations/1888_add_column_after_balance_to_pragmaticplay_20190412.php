<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_after_balance_to_pragmaticplay_20190412 extends CI_Migration {

	public function up() {
		$fields = array(
			'after_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('pragmaticplay_cny1_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_cny2_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_idr1_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_idr2_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_idr3_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_idr4_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_idr5_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_myr1_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_myr2_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_thb1_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_thb2_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_vnd1_game_logs', $fields, 'win');
		$this->dbforge->add_column('pragmaticplay_vnd2_game_logs', $fields, 'win');
	}

	public function down() {
	}
}