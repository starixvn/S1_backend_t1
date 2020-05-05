<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tcg_game_logs_201808201418 extends CI_Migration {

    private $tableName = 'tcg_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_order_no' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'bet_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'trans_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_content_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'play_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'order_num' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'chase' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'numero' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'win_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'net_pnl' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'bet_status' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'settlement_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'product_type' => array(
                'type' => 'INT',
                'null' => true,
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
            'last_updated_time' => array(
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
        $this->dbforge->add_key('last_updated_time');
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}