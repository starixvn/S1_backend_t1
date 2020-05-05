<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_rg_game_logs_20191014 extends CI_Migration {

    private $tableName = 'rg_game_logs';

    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => true,
            ),
            'live' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'order_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'odds_count' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'total_stake' => array(
                'type' => 'double',
                'null' => true,
            ),
            'total_bonus' => array(
                'type' => 'double',
                'null' => true,
            ),
            'total_bet_bonus' => array(
                'type' => 'double',
                'null' => true,
            ),
            'win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'create_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'settle_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'order_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'detail_comment' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'detail_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'detail_order_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'detail_game_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'detail_live' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'detail_odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'detail_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'detail_match_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
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
            ),
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_create_time', 'create_time');
            $this->player_model->addIndex($this->tableName, 'idx_settle_time', 'settle_time');
            $this->player_model->addIndex($this->tableName, 'idx_detail_game_id', 'detail_game_id');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down()
    {
        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
