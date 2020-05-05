<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_vr_game_logs_201807070142 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('vr_game_logs', 'idx_serialNumber', 'serialNumber');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('vr_game_logs', 'idx_serialNumber');
    }
}
