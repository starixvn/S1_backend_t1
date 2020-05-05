<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_to_pt_game_logs_201511031803 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_playername on pt_game_logs(playername)');
		$this->db->query('create index idx_gamedate on pt_game_logs(gamedate)');
		$this->db->query('create index idx_gameshortcode on pt_game_logs(gameshortcode)');
	}

	public function down() {
		$this->db->query('drop index idx_playername on pt_game_logs');
		$this->db->query('drop index idx_gamedate on pt_game_logs');
		$this->db->query('drop index idx_gameshortcode on pt_game_logs');
	}
}

///END OF FILE//////////