<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_copy_table_game_logs_to_game_logs_cache_20200210 extends CI_Migration {

    private $tableName='game_logs_cache';

    public function up() {
        $this->load->model(['player_model']);
        if(!$this->utils->table_really_exists($this->tableName)){
            $this->player_model->runRawUpdateInsertSQL('create table '.$this->tableName.' like game_logs');
            $this->player_model->adjustIdOfGameLogsNew($this->tableName);
        }
    }

    public function down() {
    }
}