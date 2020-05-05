<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_http_request_201810221136 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('http_request', 'idx_createdat', 'createdat');
        $this->player_model->addIndex('http_request', 'idx_playerId', 'playerId');
        $this->player_model->addIndex('http_request', 'idx_ip', 'ip');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('http_request', 'idx_createdat');
        $this->player_model->dropIndex('http_request', 'idx_playerId');
        $this->player_model->dropIndex('http_request', 'idx_ip');
    }
}
