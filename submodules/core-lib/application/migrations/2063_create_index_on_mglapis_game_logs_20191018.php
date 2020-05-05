<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_mglapis_game_logs_20191018 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('mglapis_game_logs', 'idx_ref_trans_id', 'ref_trans_id');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('mglapis_game_logs', 'idx_ref_trans_id');
    }
}
