<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_game_logs_201511211336 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_player_id on game_logs(player_id)');
		$this->db->query('create index idx_game_platform_id on game_logs(game_platform_id)');
		$this->db->query('create index idx_game_type_id on game_logs(game_type_id)');
		$this->db->query('create index idx_game_description_id on game_logs(game_description_id)');
		$this->db->query('create index idx_end_at on game_logs(end_at)');
		$this->db->query('create index idx_flag on game_logs(flag)');
	}

	public function down() {
		$this->db->query('drop index idx_player_id on game_logs');
		$this->db->query('drop index idx_game_platform_id on game_logs');
		$this->db->query('drop index idx_game_type_id on game_logs');
		$this->db->query('drop index idx_game_description_id on game_logs');
		$this->db->query('drop index idx_end_at on game_logs');
		$this->db->query('drop index idx_flag on game_logs');
	}
}

///END OF FILE//////////