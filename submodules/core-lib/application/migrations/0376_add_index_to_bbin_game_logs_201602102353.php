<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_bbin_game_logs_201602102353 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_wagers_id on bbin_game_logs(wagers_id)');
		$this->db->query('create index idx_wagers_date on bbin_game_logs(wagers_date)');
		$this->db->query('create index idx_external_uniqueid on bbin_game_logs(external_uniqueid)');
		$this->db->query('create index idx_flag on bbin_game_logs(flag)');
	}

	public function down() {
		$this->db->query('drop index idx_wagers_id on bbin_game_logs');
		$this->db->query('drop index idx_wagers_date on bbin_game_logs');
		$this->db->query('drop index idx_external_uniqueid on bbin_game_logs');
		$this->db->query('drop index idx_flag on bbin_game_logs');
	}
}

///END OF FILE//////////