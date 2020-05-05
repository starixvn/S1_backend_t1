<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_tcg_game_logs_20190421 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('tcg_game_logs','idx_bet_time' , 'bet_time');
        $this->player_model->addIndex('tcg_game_logs','idx_settlement_time' , 'settlement_time');

    }

    public function down() {
    }
}

///END OF FILE//////////