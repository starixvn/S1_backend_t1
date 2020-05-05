<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_unique_index_on_oneworks_game_results_201812121400 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->dropIndex('oneworks_game_result', 'match_id');
        $this->player_model->addIndex('oneworks_game_result', 'idx_match_id', 'match_id', true);
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('oneworks_game_result', 'idx_match_id');
        $this->player_model->addIndex('oneworks_game_result', 'match_id', 'match_id'); # Add back the old non-unique index
    }
}