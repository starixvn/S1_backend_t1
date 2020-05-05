<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_summary2_report_daily_201809102254 extends CI_Migration {

    private $tableName = 'summary2_report_daily';

    public function up() {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'summary_date' => array(
                'type' => 'DATE',
            ),
            'count_new_player' => array(
                'type' => 'INT',
            ),
            'count_all_players' => array(
                'type' => 'INT',
            ),
            'count_first_deposit' => array(
                'type' => 'INT',
            ),
            'count_second_deposit' => array(
                'type' => 'INT',
            ),
            'total_deposit' => array(
                'type' => 'DOUBLE',
            ),
            'total_withdrawal' => array(
                'type' => 'DOUBLE',
            ),
            'total_bonus' => array(
                'type' => 'DOUBLE',
            ),
            'total_cashback' => array(
                'type' => 'DOUBLE',
            ),
            'total_fee' => array(
                'type' => 'DOUBLE',
            ),
            'total_bank_cash_amount' => array(
                'type' => 'DOUBLE',
            ),
            'total_bet' => array(
                'type' => 'DOUBLE',
            ),
            'total_win' => array(
                'type' => 'DOUBLE',
            ),
            'total_loss' => array(
                'type' => 'DOUBLE',
            ),
            'total_payout' => array(
                'type' => 'DOUBLE',
            ),
            'last_update_time' => array(
                'type' => 'DATETIME',
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('summary_date');

        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
