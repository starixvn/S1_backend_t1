<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_rtg_game_logs_and_rtg_master_game_logs_20181214 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('rtg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex('rtg_master_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////