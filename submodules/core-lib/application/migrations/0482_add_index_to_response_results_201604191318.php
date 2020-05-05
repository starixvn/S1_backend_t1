<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_response_results_201604191318 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_player_id on response_results(player_id)');
		$this->db->query('create index idx_system_type_id on response_results(system_type_id)');
		$this->db->query('create index idx_created_at on response_results(created_at)');
		$this->db->query('create index idx_sync_id on response_results(sync_id)');
	}

	public function down() {
		$this->db->query('drop index idx_player_id on response_results');
		$this->db->query('drop index idx_system_type_id on response_results');
		$this->db->query('drop index idx_created_at on response_results');
		$this->db->query('drop index idx_sync_id on response_results');
	}
}

///END OF FILE//////////