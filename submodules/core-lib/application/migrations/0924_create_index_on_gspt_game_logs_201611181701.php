<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_gspt_game_logs_201611181701 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('gspt_game_logs', 'idx_gameshortcode', 'gameshortcode');
		$this->player_model->addIndex('gspt_game_logs', 'idx_player_name', 'player_name');
		$this->player_model->addIndex('gspt_game_logs', 'idx_game_date', 'game_date');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('gspt_game_logs', 'idx_gameshortcode');
		$this->player_model->dropIndex('gspt_game_logs', 'idx_player_name');
		$this->player_model->dropIndex('gspt_game_logs', 'idx_game_date');

	}
}
