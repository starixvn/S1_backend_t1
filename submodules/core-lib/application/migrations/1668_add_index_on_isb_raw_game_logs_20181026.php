<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_on_isb_raw_game_logs_20181026 extends CI_Migration {

	private $tableName = 'isb_raw_game_logs';

	public function up() {
                $this->load->model('player_model'); # Any model class will do
                $this->player_model->addIndex($this->tableName, 'idx_time', 'time');
                $this->player_model->addIndex($this->tableName, 'idx_sessionid', 'sessionid');
                $this->player_model->addIndex($this->tableName, 'idx_roundid', 'roundid');
	}

	public function down() {
                $this->db->query('drop index idx_time on isb_raw_game_logs');
                $this->db->query('drop index idx_sessionid on isb_raw_game_logs');
                $this->db->query('drop index idx_roundid on isb_raw_game_logs');
	}
}