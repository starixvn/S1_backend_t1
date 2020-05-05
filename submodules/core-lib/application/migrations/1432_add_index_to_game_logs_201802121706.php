<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_game_logs_201802121706 extends CI_Migration {

	public function up() {

		$this->load->model('player_model');
		$this->player_model->addIndex('game_logs', 'idx_player_id_end_at', 'player_id, end_at');

	}

	public function down() {
	}
}
