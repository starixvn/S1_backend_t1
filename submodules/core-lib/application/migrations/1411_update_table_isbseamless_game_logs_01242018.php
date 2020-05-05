<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_table_isbseamless_game_logs_01242018 extends CI_Migration {

    private $tableName = 'isbseamless_game_logs';

    public function up() {

        $this->dbforge->drop_table($this->tableName);

        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'roundid' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'after_balance' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jpc' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'froundid' => array(
                'type' => 'INT',
                'constraint' => '32',
                'null' => true,
            ),
            'fround_coin_value' => array(
                'type' => 'INT',
                'constraint' => '32',
                'null' => true,
            ),
            'fround_lines' => array(
                'type' => 'INT',
                'constraint' => '32',
                'null' => true,
            ),
            'fround_line_bet' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => '32',
                'null' => true,
            ),
            'closeround' => array(
                'type' => 'BOOLEAN',
                'null' => true,
            ),
            'jpw' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'jpw_from_jpc' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'command' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}