<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_sbtech_new_game_logs_20190328 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);
		$this->player_model->addIndex('sbtech_new_game_logs','idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////