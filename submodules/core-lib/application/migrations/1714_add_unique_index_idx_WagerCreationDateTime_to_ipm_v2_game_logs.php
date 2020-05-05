<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_idx_WagerCreationDateTime_to_ipm_v2_game_logs extends CI_Migration {
	private $tableName = 'ipm_v2_game_logs';
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName,'idx_WagerCreationDateTime','WagerCreationDateTime');
	}

	public function down() {
	}
}

///END OF FILE//////////