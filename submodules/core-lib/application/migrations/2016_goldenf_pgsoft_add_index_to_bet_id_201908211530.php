<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_goldenf_pgsoft_add_index_to_bet_id_201908211530 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex('goldenf_pgsoft_game_logs','idx_bet_id' , 'bet_id');
    }

    public function down() {
        $this->player_model->dropIndex('goldenf_pgsoft_game_logs', 'idx_bet_id');
    }
}

///END OF FILE//////////