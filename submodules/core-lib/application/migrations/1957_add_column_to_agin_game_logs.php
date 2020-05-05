<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_agin_game_logs extends CI_Migration {

	public function up() {

		$agin_game_logs_fields = array(
			'subbillno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
		);
		$this->dbforge->add_column('agin_game_logs', $agin_game_logs_fields);
	}

	public function down() {
		$this->dbforge->drop_column('agin_game_logs', $this->up->agin_game_logs_fields);
	}
}