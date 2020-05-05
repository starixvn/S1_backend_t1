<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Modify_playerid_to_nullable_on_og_game_logs extends CI_Migration {

	public function up() {
		$this->dbforge->modify_column("og_game_logs", [
			'PlayerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			)
		]);
	}

	public function down() {
	}
}