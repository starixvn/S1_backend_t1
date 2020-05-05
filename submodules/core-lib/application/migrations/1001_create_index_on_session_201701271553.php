<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_session_201701271553 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('ci_player_sessions', 'idx_player_id', 'player_id');
		// $this->player_model->addIndex('ci_admin_sessions', 'idx_admin_id', 'admin_id');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('ci_player_sessions', 'idx_player_id');
		// $this->player_model->dropIndex('ci_admin_sessions', 'idx_admin_id');

	}
}
