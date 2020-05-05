<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_unique_index_on_oneworks_game_logs_201811161500 extends CI_Migration {

    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->dropIndex('oneworks_game_logs', 'idx_external_uniqueid'); # need to drop existing index on the same column
        $this->player_model->addIndex('oneworks_game_logs', 'idx_external_uniqueid', 'external_uniqueid', true); # create unique index
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('oneworks_game_logs', 'idx_external_uniqueid');
        $this->player_model->addIndex('oneworks_game_logs', 'idx_external_uniqueid', 'external_uniqueid');
    }
}
