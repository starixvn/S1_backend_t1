<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ld_lottery_game_logs_201710021312 extends CI_Migration {

    private $tableName = 'ld_lottery_game_logs';

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

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
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

        $this->db->query('create unique index idx_external_uniqueid on ld_lottery_game_logs(external_uniqueid)');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->db->query('drop index idx_external_uniqueid on ld_lottery_game_logs');
    }
}
