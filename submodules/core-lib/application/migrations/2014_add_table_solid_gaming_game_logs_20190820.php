<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_solid_gaming_game_logs_20190820 extends CI_Migration {

    private $tableName = 'solid_gaming_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'change_id' => array(
                'type' => 'BIGINT',
                'null' => false,
            ),
            'timestamp' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'transfer_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'delta_amount' => array(
                'type' => 'double',
                'null' => false,
            ),
            'balance' => array(
                'type' => 'double',
                'null' => false,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'server_round' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'round_ended' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            # SBE additional info
            'game_externalid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
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
            $this->player_model->addIndex($this->tableName, 'idx_timestamp', 'timestamp');
            $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
            $this->player_model->addIndex($this->tableName, 'idx_game_externalid', 'game_externalid');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
