<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_game_logs_201901011622 extends CI_Migration {
    public function up() {
        $this->load->model('player_model'); # Any model class will do

        # PRAGMATICPLAY GAMELOGS
        $this->player_model->addIndex('game_logs', 'idx_table', '`table`');
    }

    public function down() {
    }
}

///END OF FILE//////////