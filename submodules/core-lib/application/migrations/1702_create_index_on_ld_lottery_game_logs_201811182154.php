<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_ld_lottery_game_logs_201811182154 extends CI_Migration {

    public function up() {

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('ld_lottery_game_logs', 'idx_bet_time', 'bet_time');
        $this->player_model->addIndex('ld_lottery_game_logs', 'idx_end_time', 'end_time');
        $this->player_model->addIndex('ld_lottery_game_logs', 'idx_round_key', 'round_key');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('ld_lottery_game_logs', 'idx_bet_time');
        $this->player_model->dropIndex('ld_lottery_game_logs', 'idx_end_time');
        $this->player_model->dropIndex('ld_lottery_game_logs', 'idx_round_key');

    }
}
