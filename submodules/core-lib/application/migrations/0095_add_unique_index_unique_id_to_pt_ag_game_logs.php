<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_unique_id_to_pt_ag_game_logs extends CI_Migration {

	public function up() {
		//pt
		$this->db->query('create unique index idx_pt_game_logs_uniqueid on pt_game_logs(uniqueid)');
		$this->db->query('create index idx_pt_game_logs_external_uniqueid on pt_game_logs(external_uniqueid)');
		//ag
		$this->db->query('create unique index idx_ag_game_logs_uniqueid on ag_game_logs(uniqueid)');
		$this->db->query('create index idx_ag_game_logs_external_uniqueid on ag_game_logs(external_uniqueid)');
	}

	public function down() {
		//pt
		$this->db->query('drop index idx_pt_game_logs_uniqueid on pt_game_logs');
		$this->db->query('drop index idx_pt_game_logs_external_uniqueid on pt_game_logs');
		//ag
		$this->db->query('drop index idx_ag_game_logs_uniqueid on ag_game_logs');
		$this->db->query('drop index idx_ag_game_logs_external_uniqueid on ag_game_logs');
	}
}

///END OF FILE//////////