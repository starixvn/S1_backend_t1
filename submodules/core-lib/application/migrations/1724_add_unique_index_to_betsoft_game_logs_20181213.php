<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_betsoft_game_logs_20181213 extends CI_Migration {
	private $tableName = 'betsoft_game_logs';
	public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_hash', 'hash',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}

///END OF FILE//////////