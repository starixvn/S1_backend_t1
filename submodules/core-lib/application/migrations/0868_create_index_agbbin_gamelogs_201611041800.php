<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_agbbin_gamelogs_201611041800 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('agbbin_game_logs', 'idx_bettime', 'bettime');
		$this->player_model->addIndex('agbbin_game_logs', 'idx_gametype', 'gametype');
		$this->player_model->addIndex('agbbin_game_logs', 'idx_playername', 'playername');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('agbbin_game_logs', 'idx_bettime');
		$this->player_model->dropIndex('agbbin_game_logs', 'idx_gametype');
		$this->player_model->dropIndex('agbbin_game_logs', 'idx_playername');

	}
}
