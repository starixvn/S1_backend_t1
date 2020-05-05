<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_game_description_20181126 extends CI_Migration {
    
    public function up() {
        $this->load->model('game_description_model'); # Any model class will do
        $this->game_description_model->addIndex('game_description', 'idx_unique_game_id', 'game_platform_id,external_game_id');
    }

    public function down() {
        $this->load->model('game_description_model');
        $this->game_description_model->dropIndex('game_description', 'idx_unique_game_id');
    }
}
