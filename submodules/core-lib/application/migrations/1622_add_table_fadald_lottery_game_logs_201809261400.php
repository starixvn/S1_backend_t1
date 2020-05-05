<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_fadald_lottery_game_logs_201809261400 extends CI_Migration {

    private $tableName = 'fadald_lottery_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'order_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'platform_account_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'rule_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'odds' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00
            ),
            'bet_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'end_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'payout_amount' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00
            ),
            'win_lose' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'winloss_amount' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
                'after' => 'bet_amount'
            ),
            'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'extra' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'play_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'lotto_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'lotto_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'numbers' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cmd' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'play_value' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->db->query('create index idx_round_key on fadald_lottery_game_logs(round_id, platform_account_id)');
    }

    public function down() {
        $this->db->query('drop index idx_round_key on fadald_lottery_game_logs');
        $this->dbforge->drop_table($this->tableName);
    }
}
