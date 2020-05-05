<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_afb88_game_logs_20190827 extends CI_Migration {

    private $tableName = 'afb88_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'ventransid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'external_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'last_modified' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'player_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'double',
                'null' => false,
            ),
            'win_amount' => array(
                'type' => 'double',
                'null' => false,
            ),
            'commission' => array(
                'type' => 'double',
                'null' => false,
            ),
            'commission_percent' => array(
                'type' => 'double',
                'null' => false,
            ),
            'league' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'home' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'away' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'odds' => array(
                'type' => 'double',
                'null' => true,
            ),
            'side' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'info' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'half' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'transaction_date' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'work_date' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'match_date' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'running_score' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'score' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'half_time_score' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'flg' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'result' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'sports_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
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
            $this->player_model->addIndex($this->tableName, 'idx_last_modified', 'last_modified');
            $this->player_model->addIndex($this->tableName, 'idx_transaction_date', 'transaction_date');
            $this->player_model->addIndex($this->tableName, 'idx_work_date', 'work_date');
            $this->player_model->addIndex($this->tableName, 'idx_match_date', 'match_date');
            $this->player_model->addIndex($this->tableName, 'idx_ventransid', 'ventransid');
            $this->player_model->addIndex($this->tableName, 'idx_game_externalid', 'game_externalid');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
