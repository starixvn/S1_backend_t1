<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_payment_report_daily_201901221105 extends CI_Migration {

    private $tableName='payment_report_daily';

    public function up() {
        $fields = array(
           'currency_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null'=> true
            ),
           'player_group_and_level' => array(
                'type' => 'VARCHAR',
                'constraint' => '400',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_transaction_type', 'transaction_type');
        $this->player_model->addIndex($this->tableName, 'idx_player_username', 'player_username');
        $this->player_model->addIndex($this->tableName, 'idx_bank_type_name', 'bank_type_name');
        $this->player_model->addIndex($this->tableName, 'idx_external_system_code', 'external_system_code');
        $this->player_model->addIndex($this->tableName, 'idx_player_group_level_name', 'player_group_level_name');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'currency_key');
        $this->dbforge->drop_column($this->tableName, 'player_group_and_level');
    }
}
