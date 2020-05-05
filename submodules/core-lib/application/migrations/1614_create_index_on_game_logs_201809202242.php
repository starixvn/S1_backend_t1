<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_game_logs_201809202242 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('game_logs', 'idx_updated_at', 'updated_at');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('game_logs', 'idx_updated_at');
    }
}
