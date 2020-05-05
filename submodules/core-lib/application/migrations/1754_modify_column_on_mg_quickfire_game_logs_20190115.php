<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_mg_quickfire_game_logs_20190115 extends CI_Migration {
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('mg_quickfire_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////