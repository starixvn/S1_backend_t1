<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_collation_on_mg_game_logs_201902151611 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->fixCollationOnTable('mg_game_logs', ['account_number', 'display_name',
            'display_game_category', 'external_uniqueid', 'module_id', 'client_id', 'uniqueid',
            'user_name', 'external_game_id', 'game_platform']);
    }

    public function down() {}
}
