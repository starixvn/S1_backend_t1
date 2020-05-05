<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kingrich_summary_reports_201802112340 extends CI_Migration {

    private $tableName = 'kingrich_summary_reports';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'settlement_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'sum_bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'sum_debit_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'sum_credit_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'sum_net_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'sum_number_of_bets' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'kingrich_game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        $this->player_model->addIndex($this->tableName, 'idx_settlement_date', 'settlement_date', true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}