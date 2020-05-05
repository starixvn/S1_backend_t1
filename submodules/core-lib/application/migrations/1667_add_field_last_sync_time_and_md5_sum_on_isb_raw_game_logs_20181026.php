<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_last_sync_time_and_md5_sum_on_isb_raw_game_logs_20181026 extends CI_Migration {

	private $tableName = 'isb_raw_game_logs';

	public function up() {
        $field = array(
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $field);
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
	}
}