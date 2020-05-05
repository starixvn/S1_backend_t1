<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_player_20190708 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_email', 'email');
        $this->player_model->addIndex($this->tableName, 'idx_created_on', 'createdOn');
        $this->player_model->addIndex($this->tableName, 'idx_deleted_at', 'deleted_at');
        $this->player_model->addIndex($this->tableName, 'idx_last_login_time', 'lastLoginTime');
    }

    public function down() {

    }
}
