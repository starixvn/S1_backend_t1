<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_dt_game_logs_20181220 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('dt_game_logs', 'idx_dt_id', 'dt_id',true);
        $this->player_model->addIndex('dt_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////