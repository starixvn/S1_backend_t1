<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_prefix_for_game_account_201902122353 extends CI_Migration {

    private $tableName = 'agency_prefix_for_game_account';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'agent_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'game_platform_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'prefix' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        );


        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        # Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_agent_id', 'agent_id');
        $this->player_model->addIndex($this->tableName, 'idx_game_platform_id', 'game_platform_id');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
