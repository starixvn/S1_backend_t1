<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_table_amg_game_logs_20200207 extends CI_Migration
{
    private $tableName = 'amg_game_logs';

    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => true,
            ),
            'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'player_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '70',
                'null' => true
            ),
            'player_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '70',
                'null' => true
            ),
            'game_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => true
            ),
            'start_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'update_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'end_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'total_bet' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'total_win' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'ended' => array(
                'type' => 'TINYINT',
                'null' => true
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

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_amg_round_id', 'round_id');
            $this->player_model->addIndex($this->tableName, 'idx_amg_player_name', 'player_name');
            $this->player_model->addIndex($this->tableName, 'idx_amg_player_id', 'player_id');
            $this->player_model->addIndex($this->tableName, 'idx_amg_game_id', 'game_id');
            $this->player_model->addIndex($this->tableName, 'idx_amg_ended', 'ended');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_amg_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table($this->tableName);
    }
}
