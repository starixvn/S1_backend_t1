<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_unique_id_to_haba88_game_logs extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_haba88_game_logs_external_uniqueid on haba88_game_logs(external_uniqueid)');
	}

	public function down() {
		$this->db->query('drop index idx_haba88_game_logs_external_uniqueid on haba88_game_logs');
	}
}