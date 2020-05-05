<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_tables_20190616 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);
        $this->player_model->addIndex('sale_orders','idx_process_time' , 'process_time');
        $this->player_model->addIndex('transaction_notes','idx_transaction_id' , 'transaction_id');
        $this->player_model->addIndex('transaction_notes','idx_transaction' , 'transaction');
        $this->player_model->addIndex('transaction_notes','idx_admin_user_id' , 'admin_user_id');
        $this->player_model->addIndex('walletaccount','idx_transactionType' , 'transactionType');
        $this->player_model->addIndex('walletaccount','idx_dwStatus' , 'dwStatus');
        $this->player_model->addIndex('walletaccount','idx_playerId' , 'playerId');

    }

    public function down() {
    }
}

///END OF FILE//////////