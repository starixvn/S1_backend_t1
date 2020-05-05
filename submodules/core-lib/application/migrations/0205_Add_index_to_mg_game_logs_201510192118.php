<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_mg_game_logs_201510192118 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_row_id on mg_game_logs(row_id)');
	}

	public function down() {
		$this->db->query('drop index idx_row_id on mg_game_logs');
	}
}

///END OF FILE//////////