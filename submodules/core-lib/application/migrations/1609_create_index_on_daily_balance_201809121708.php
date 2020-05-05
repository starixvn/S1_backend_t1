<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_daily_balance_201809121708 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('daily_balance', 'idx_game_date', 'game_date');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('daily_balance', 'idx_game_date');
    }
}
