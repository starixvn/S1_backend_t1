<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_logs_201903181551 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('logs','idx_logDate' , 'logDate');
        $this->player_model->addIndex('logs','idx_username' , 'username');
        $this->player_model->addIndex('logs','idx_ip' , 'ip');
    }

    public function down() {
    }
}

///END OF FILE//////////