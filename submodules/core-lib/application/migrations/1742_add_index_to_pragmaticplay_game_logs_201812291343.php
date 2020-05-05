<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_pragmaticplay_game_logs_201812291343 extends CI_Migration {
    public function up() {
        $this->load->model('player_model'); # Any model class will do

        # PRAGMATICPLAY GAMELOGS
        $this->player_model->addIndex('pragmaticplay_game_logs', 'idx_related_uniqueid', 'related_uniqueid');

    }

    public function down() {
    }
}

///END OF FILE//////////