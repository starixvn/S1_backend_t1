<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_field_type_fishinggame_091320161108 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE fishinggame_game_logs MODIFY COLUMN cuuency VARCHAR(50)");
		$this->db->query("ALTER TABLE fishinggame_game_logs MODIFY COLUMN accountno VARCHAR(150)");
 	}
		
	public function down() {
	}
}
