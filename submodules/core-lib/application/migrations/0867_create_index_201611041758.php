<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_201611041758 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('agin_game_logs', 'idx_bettime', 'bettime');
		$this->player_model->addIndex('agin_game_logs', 'idx_gametype', 'gametype');
		$this->player_model->addIndex('agin_game_logs', 'idx_playername', 'playername');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('agin_game_logs', 'idx_bettime');
		$this->player_model->dropIndex('agin_game_logs', 'idx_gametype');
		$this->player_model->dropIndex('agin_game_logs', 'idx_playername');

	}
}
