<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_mglapis_raw_game_logs_201612041438 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('mglapis_raw_game_logs', 'idx_trans_type', 'trans_type');
		$this->player_model->addIndex('mglapis_raw_game_logs', 'idx_trans_time', 'trans_time');
		$this->player_model->addIndex('mglapis_raw_game_logs', 'idx_player_name', 'player_name');
		$this->player_model->addIndex('mglapis_raw_game_logs', 'idx_game_id', 'game_id');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('mglapis_raw_game_logs', 'idx_trans_type');
		$this->player_model->dropIndex('mglapis_raw_game_logs', 'idx_trans_time');
		$this->player_model->dropIndex('mglapis_raw_game_logs', 'idx_player_name');
		$this->player_model->dropIndex('mglapis_raw_game_logs', 'idx_game_id');

	}
}
