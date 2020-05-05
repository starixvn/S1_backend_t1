<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mg_quickfire_game_reports_20200205 extends CI_Migration {

    private $tableName = 'mg_quickfire_game_reports';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'transaction' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'session' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'wagered' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'payout' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'win_loss' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'balance' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'game_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'player_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),

            # SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );


        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_transaction', 'transaction');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_as_external_uniqueid', 'external_uniqueid');

        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
