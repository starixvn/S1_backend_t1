<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_payment_report_daily_201901161017 extends CI_Migration {

    private $tableName='payment_report_daily';

    public function up() {
        $fields = array(
           'second_category_flag' => array(
                'type' => 'INT',
                'null'=> true
            ),
           'first_category_flag' => array(
                'type' => 'INT',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_payment_date', 'payment_date');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'second_category_flag');
        $this->dbforge->drop_column($this->tableName, 'first_category_flag');
    }
}
