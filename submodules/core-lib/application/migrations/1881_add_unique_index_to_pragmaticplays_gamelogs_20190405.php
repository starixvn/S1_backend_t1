<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_pragmaticplays_gamelogs_20190405 extends CI_Migration {
    public function up() {
        $this->load->model('player_model');
        # IDR
        $this->player_model->addIndex('pragmaticplay_idr5_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true);
    }

    public function down() {
    }
}

///END OF FILE//////////