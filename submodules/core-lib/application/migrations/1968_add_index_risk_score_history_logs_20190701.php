<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_risk_score_history_logs_20190701 extends CI_Migration {

    private $tableName = 'risk_score_history_logs';

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        $this->player_model->addIndex($this->tableName, 'idx_risk_score_category', 'risk_score_category');
    }

    public function down() {
        
    }
}
