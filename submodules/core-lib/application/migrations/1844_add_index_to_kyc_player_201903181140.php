<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_kyc_player_201903181140 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('kyc_player','idx_player_id' , 'player_id');
    }

    public function down() {
    }
}

///END OF FILE//////////