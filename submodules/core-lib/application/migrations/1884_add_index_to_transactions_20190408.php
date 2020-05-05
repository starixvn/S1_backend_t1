<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_transactions_20190408 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex('transactions','idx_order_id', 'order_id');
    }

    public function down() {
    }
}

///END OF FILE//////////