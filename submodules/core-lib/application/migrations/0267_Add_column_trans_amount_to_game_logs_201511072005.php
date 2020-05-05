<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_trans_amount_to_game_logs_201511072005 extends CI_Migration {

	public function up() {
		$fields = array(
			'trans_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_logs', 'trans_amount');
	}
}