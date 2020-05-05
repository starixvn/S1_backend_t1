<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_external_uniqueid_to_game_logs extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_game_logs_external_uniqueid on game_logs(external_uniqueid)');
	}

	public function down() {
		$this->db->query('drop index idx_game_logs_external_uniqueid on game_logs');
	}
}

///END OF FILE//////////