<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_trans_type_to_game_logs_201511261152 extends CI_Migration {

	public function up() {
		$fields = array(
			'trans_type' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_logs', 'trans_type');
	}
}