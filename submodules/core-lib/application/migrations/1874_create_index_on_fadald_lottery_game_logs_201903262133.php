<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_fadald_lottery_game_logs_201903262133 extends CI_Migration {

    public function up() {
        $this->load->model('player_model');
        $this->player_model->addIndex('fadald_lottery_game_logs', 'idx_round_key_field', 'round_key');
        $this->player_model->addIndex('ld_lottery_game_logs', 'idx_round_key_field', 'round_key');
    }

    public function down() {
    }
}
