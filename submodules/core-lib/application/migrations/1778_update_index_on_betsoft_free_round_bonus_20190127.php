<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_index_on_betsoft_free_round_bonus_20190127 extends CI_Migration {

    private $tableName = 'betsoft_free_round_bonus';

    public function up() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_bonus_id');

        //bonus_win bonusId can be called multiple times until that bonusId is ended
        //bonusId may not be unique for each call to bonus_win
        $this->player_model->addIndex($this->tableName, 'idx_bonus_id', 'bonus_id',false); // should not be unique
    }

    public function down() {
    }
}