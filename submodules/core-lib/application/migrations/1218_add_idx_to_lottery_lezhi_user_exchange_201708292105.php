<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_idx_to_lottery_lezhi_user_exchange_201708292105 extends CI_Migration {

    private $tableName = 'lottery_lezhi_user_exchange';

    public function up() {

    	$this->load->model(['player_model']);

        $this->player_model->addIndex($this->tableName, 'idx_player_extcredits_id', 'player_id, extcredits_id');
        $this->player_model->addIndex($this->tableName, 'idx_get_exchanged_num', 'player_id, extcredits_id, created_at');

    }

    public function down() {

    }
}
