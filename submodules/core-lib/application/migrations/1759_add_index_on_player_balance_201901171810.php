<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_on_player_balance_201901171810 extends CI_Migration {
	private $tableName = 'daily_balance';

    public function up() {
        $this->load->model(['player_model']);

        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        $this->player_model->addIndex($this->tableName, 'idx_sub_wallet_id', 'sub_wallet_id');
        $this->player_model->addIndex($this->tableName, 'idx_type', 'type');
    }

    public function down() {}
}
