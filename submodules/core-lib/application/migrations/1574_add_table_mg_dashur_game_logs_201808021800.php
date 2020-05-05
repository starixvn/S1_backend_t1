<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mg_dashur_game_logs_201808021800 extends CI_Migration {

    private $tableName = 'mg_dashur_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'test' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0, # set to false
            ),
            'wallet_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_ref' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'category' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'sub_category' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'balance_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'meta_data' => array(
                'type' => 'TEXT',
                'null' => true,
            ),

            'mg_id' => array(   # id in orignal
                'type' => 'INT',
                'null' => true,
            ),
            'parent_transaction_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'account_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'account_ext_ref' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'application_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'currency_unit' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'transaction_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'balance' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'pool_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'created_by' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'session' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),

            // from meta field (if player won it has 2 response from api ) category PAYOUT and WAGER
            // need to get game id for duplicate logs
            'game_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
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