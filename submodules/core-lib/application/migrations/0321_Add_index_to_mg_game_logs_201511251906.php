<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_mg_game_logs_201511251906 extends CI_Migration {

	public function up() {
		//mg
		$this->db->query('create index idx_account_number on mg_game_logs(account_number)');
		$this->db->query('create index idx_game_end_time on mg_game_logs(game_end_time)');
		$this->db->query('create index idx_display_name on mg_game_logs(display_name)');
		$this->db->query('create index idx_module_id on mg_game_logs(module_id)');
		$this->db->query('create index idx_client_id on mg_game_logs(client_id)');
		//nt
		$this->db->query('create index idx_username on nt_game_logs(username)');
		$this->db->query('create index idx_time on nt_game_logs(time)');
		$this->db->query('create index idx_game_id on nt_game_logs(game_id)');
		//ag
		$this->db->query('create index idx_bettime on ag_game_logs(bettime)');
		$this->db->query('create index idx_gametype on ag_game_logs(gametype)');
	}

	public function down() {
		$this->db->query('drop index idx_account_number on mg_game_logs');
		$this->db->query('drop index idx_game_end_time on mg_game_logs');
		$this->db->query('drop index idx_display_name on mg_game_logs');
		$this->db->query('drop index idx_module_id on mg_game_logs');
		$this->db->query('drop index idx_client_id on mg_game_logs');

		$this->db->query('drop index idx_username on nt_game_logs');
		$this->db->query('drop index idx_time on nt_game_logs');
		$this->db->query('drop index idx_game_id on nt_game_logs');

		$this->db->query('drop index idx_bettime on ag_game_logs');
		$this->db->query('drop index idx_gametype on ag_game_logs');
	}
}

///END OF FILE//////////