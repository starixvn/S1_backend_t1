<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tianhao_game_logs_201808241600 extends CI_Migration {

    private $tableName = 'tianhao_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
            ),
            "username" => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => '50',
            ),
            "end_time" => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            "game_type" => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            "room_type" => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            "start_money" => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            "win_money" => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            "end_money" => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            "bank_money" => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            "deal_money" => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            "desk_uuid" => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => false,
            ),
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
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

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_tianhao_game_logs_external_uniqueid', 'external_uniqueid', true);
    }

    public function down() {
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_tianhao_game_logs_external_uniqueid');
        $this->dbforge->drop_table($this->tableName);
    }
}
