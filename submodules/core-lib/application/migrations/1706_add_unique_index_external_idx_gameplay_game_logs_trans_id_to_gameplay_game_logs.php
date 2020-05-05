<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_external_idx_gameplay_game_logs_trans_id_to_gameplay_game_logs extends CI_Migration {
	private $tableName = 'gameplay_game_logs';
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_gameplay_game_logs_trans_id', 'trans_id',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////