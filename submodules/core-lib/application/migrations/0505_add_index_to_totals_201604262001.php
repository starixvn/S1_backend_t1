<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_totals_201604262001 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_date_minute on total_player_game_minute(date_minute)');
		$this->db->query('create index idx_player_id on total_player_game_minute(player_id)');
		$this->db->query('create index idx_date on total_player_game_minute(date)');
		$this->db->query('create index idx_game_platform_id on total_player_game_minute(game_platform_id)');
		$this->db->query('create index idx_game_description_id on total_player_game_minute(game_description_id)');

		$this->db->query('create index idx_date_hour on total_player_game_hour(date_hour)');
		$this->db->query('create index idx_player_id on total_player_game_hour(player_id)');
		$this->db->query('create index idx_date on total_player_game_hour(date)');
		$this->db->query('create index idx_game_platform_id on total_player_game_hour(game_platform_id)');
		$this->db->query('create index idx_game_description_id on total_player_game_hour(game_description_id)');

		$this->db->query('create index idx_player_id on total_player_game_day(player_id)');
		$this->db->query('create index idx_date on total_player_game_day(date)');

		$this->db->query('create index idx_player_id on total_player_game_month(player_id)');
		$this->db->query('create index idx_month on total_player_game_month(month)');

		// $this->db->query('create index idx_player_id on total_player_game_year(player_id)');
		$this->db->query('create index idx_year on total_player_game_year(year)');
	}

	public function down() {
		$this->db->query('drop index idx_date_minute on total_player_game_minute');
		$this->db->query('drop index idx_player_id on total_player_game_minute');
		$this->db->query('drop index idx_date on total_player_game_minute');
		$this->db->query('drop index idx_game_platform_id on total_player_game_minute');
		$this->db->query('drop index idx_game_description_id on total_player_game_minute');

		$this->db->query('drop index idx_date_hour on total_player_game_hour');
		$this->db->query('drop index idx_player_id on total_player_game_hour');
		$this->db->query('drop index idx_date on total_player_game_hour');
		$this->db->query('drop index idx_game_platform_id on total_player_game_hour');
		$this->db->query('drop index idx_game_description_id on total_player_game_hour');

		$this->db->query('drop index idx_player_id on total_player_game_day');
		$this->db->query('drop index idx_date on total_player_game_day');

		$this->db->query('drop index idx_player_id on total_player_game_month');
		$this->db->query('drop index idx_month on total_player_game_month');

		// $this->db->query('drop index idx_player_id on total_player_game_year');
		$this->db->query('drop index idx_year on total_player_game_year');
	}
}

///END OF FILE//////////