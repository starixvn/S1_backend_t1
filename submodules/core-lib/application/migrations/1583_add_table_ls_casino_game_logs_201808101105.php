<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ls_casino_game_logs_201808101105 extends CI_Migration {

    private $tableName = 'ls_casino_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'round_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ),
            'trans_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'round_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'game_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.00
            ),
            'gbpamount' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.00
            ),
            'rate' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0.00
            ),
            'action' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bets' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'license_eid' => array(
                'type' => 'INT',
                'null' => true,
            ),

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
             'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'after_balance' => array(
                'type' => 'DOUBLE',
                'null' => true
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
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),           
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('round_key');
        $this->dbforge->add_key('trans_id');
        $this->dbforge->add_key('round_id');
        $this->dbforge->add_key('created_at');
        $this->dbforge->add_key('updated_at');

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}