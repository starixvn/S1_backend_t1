<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_betsoft_game_logs_201809121523 extends CI_Migration {

    private $tableName = 'betsoft_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),

            // from api
            'user_id' => array(                 # game username
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'bet_transid' => array(             # bet_amount|transaction_id
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'win_transid' => array(             # win_amount|transaction_id
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'game_id' => array(
                'type' => 'INT',
            ),
            'is_round_finished' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'hash' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'game_session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'negative_bet' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'client_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),

            // process fields base on api
            'bet_amount' => array(             # bet_transid
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'result_amount' => array(          # based from bet_transid and win_transid
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'external_trans_id' => array(      # from response in callback
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),


            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
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
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('external_uniqueid');
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}