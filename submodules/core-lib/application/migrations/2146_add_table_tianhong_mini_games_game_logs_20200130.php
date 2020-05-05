<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tianhong_mini_games_game_logs_20200130 extends CI_Migration {

    private $tableName = 'tianhong_mini_games_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'idx' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_choose' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'change_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'user_coin' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'is_win' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ),
            'log_date' => array(
                'type' => 'DATETIME',
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
            $this->player_model->addIndex($this->tableName, 'idx_idx', 'idx');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_as_external_uniqueid', 'external_uniqueid');

        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
