<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_game_logs_unsettle_20190411 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex('game_logs_unsettle','idx_updated_at', 'updated_at');
    }

    public function down() {
    }
}

///END OF FILE//////////