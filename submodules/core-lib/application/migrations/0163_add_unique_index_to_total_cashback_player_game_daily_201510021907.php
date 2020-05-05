<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_total_cashback_player_game_daily_201510021907 extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_player_game_date on total_cashback_player_game_daily(player_id,game_description_id,total_date)');
	}

	public function down() {
		$this->db->query('drop index idx_player_game_date on total_cashback_player_game_daily');
	}
}

///END OF FILE//////////