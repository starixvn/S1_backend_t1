<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_vip_grade_report_201810011437 extends CI_Migration {

    private $tableName = 'vip_grade_report';

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_request_time', 'request_time');
        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        $this->player_model->addIndex($this->tableName, 'idx_updated_by', 'updated_by');
        $this->player_model->addIndex($this->tableName, 'idx_vipsettingId', 'vipsettingId');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_request_time');
        $this->player_model->dropIndex($this->tableName, 'idx_player_id');
        $this->player_model->dropIndex($this->tableName, 'idx_updated_by');
        $this->player_model->dropIndex($this->tableName, 'idx_vipsettingId');
    }
}
