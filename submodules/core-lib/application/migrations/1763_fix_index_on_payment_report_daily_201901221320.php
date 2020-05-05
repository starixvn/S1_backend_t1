<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_index_on_payment_report_daily_201901221320 extends CI_Migration {

    private $tableName='payment_report_daily';

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_payment_date');
        $this->player_model->addIndex($this->tableName, 'idx_payment_date', 'payment_date');
    }

    public function down() {
    }
}
