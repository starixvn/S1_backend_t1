<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agin_game_logs_result_20180206 extends CI_Migration {

    private $tableName = 'agin_game_logs_result';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'data_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'table_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'begin_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'close_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'dealer' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'shoe_code' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'flag' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'banker_point' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'player_point' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'card_num' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'pair' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ),
            'dragon_point' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'tiger_point' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'card_list' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'vid' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'platform_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex('agin_game_logs_result', 'index_game_code', 'game_code');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}