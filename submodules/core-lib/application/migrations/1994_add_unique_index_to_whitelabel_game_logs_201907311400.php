<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unique_index_to_whitelabel_game_logs_201907311400 extends CI_Migration {
    private $tableName = 'whitelabel_game_logs';

    public function up(){
        $this->load->model(['player_model']);

        // create index
        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
    }

    public function down(){
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_external_uniqueid');
    }
}