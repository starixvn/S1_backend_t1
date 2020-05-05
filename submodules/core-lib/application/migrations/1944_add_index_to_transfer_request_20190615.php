<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_transfer_request_20190615 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('transfer_request','idx_secure_id' , 'secure_id');

    }

    public function down() {
    }
}

///END OF FILE//////////