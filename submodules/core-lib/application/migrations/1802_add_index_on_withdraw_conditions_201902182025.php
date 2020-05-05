<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_on_withdraw_conditions_201902182025 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);

        $this->player_model->addIndex('withdraw_conditions', 'idx_player_promo_id', 'player_promo_id');
    }

    public function down() {

    }
}
