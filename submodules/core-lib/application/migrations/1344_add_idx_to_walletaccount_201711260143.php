<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_idx_to_walletaccount_201711260143 extends CI_Migration {

    public function up() {

    	$this->load->model(['player_model']);

        $this->player_model->dropIndex('walletaccount', 'transaction_id', 'transaction_id');

        $this->player_model->addIndex('walletaccount', 'idx_transaction_id', 'transaction_id');
        $this->player_model->addIndex('ibc_game_logs', 'idx_trans_id', 'trans_id');

    }

    public function down() {

    	$this->load->model(['player_model']);

        $this->player_model->dropIndex('walletaccount', 'idx_transaction_id');
        $this->player_model->dropIndex('ibc_game_logs', 'idx_trans_id');

    }
}
