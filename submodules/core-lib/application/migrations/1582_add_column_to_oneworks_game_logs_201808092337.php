<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_oneworks_game_logs_201808092337 extends CI_Migration {

    private $tableName = 'oneworks_game_logs';

    public function up() {
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'last_updated_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),

        );
        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName,'idx_md5_sum' , 'md5_sum');
        $this->player_model->addIndex($this->tableName,'idx_last_updated_time' , 'last_updated_time');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
        $this->dbforge->drop_column($this->tableName, 'last_updated_time');
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
    }
}
