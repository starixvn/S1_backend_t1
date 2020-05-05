<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Modify_id_to_bigint_and_playerid_to_nullable_on_hg_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->modify_column("hg_game_logs", [
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			)
		]);
	}

	public function down() {
	}
}