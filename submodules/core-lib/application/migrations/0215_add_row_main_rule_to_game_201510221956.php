<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_main_rule_to_game_201510221956 extends CI_Migration {

	public function up() {
		$this->db->query("INSERT INTO `game` (`gameId`, `game`) VALUES (0, 'MAIN')");
	}

	public function down() {
		$this->db->query('DELETE FROM game WHERE game = "MAIN"');
	}
}