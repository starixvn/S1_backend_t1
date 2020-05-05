<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_cq9_game_logs_20181214 extends CI_Migration {
	private $tableName = 'cq9_game_logs';
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////