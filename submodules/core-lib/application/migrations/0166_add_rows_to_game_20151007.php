<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_game_20151007 extends CI_Migration {

	public function up() {
		$this->db->query("INSERT INTO `game` (`gameId`, `game`)
					VALUES
						(6, 'MG'),
						(7, 'NT')
					");
	}

	public function down() {
		$this->db->query('DELETE FROM game WHERE gameId IN(6,7)');
	}
}