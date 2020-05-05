<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_vip_grade_report_201810011648 extends CI_Migration {

    private $tableName = 'vip_grade_report';

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_status', 'status');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_status');
    }
}
