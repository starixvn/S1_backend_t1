<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_api_config_201802051843 extends CI_Migration {

    private $tableName = 'api_config';

    public function up() {
        $fields = array(
            'game_platform_id' => array(
                'type' => 'int',
                'null' => false,
            ),
            'api_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ),
            'api_note' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'last_sync_datetime' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'last_sync_index' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'api_config_json' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('game_platform_id', TRUE);
        $this->dbforge->add_key('api_name', TRUE);
        $this->dbforge->create_table($this->tableName);

        //add columns
        $fields = array(

            'player_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'user_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => true,
            ),

        );
        $this->dbforge->add_column('mg_game_logs', $fields);
        $fields = array(

            'sync_index' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),

        );
        $this->dbforge->add_column('game_logs', $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex('vr_game_logs', 'index_external_uniqueid', 'external_uniqueid');
        $this->player_model->addIndex('fishinggame_game_logs', 'index_external_uniqueid', 'external_uniqueid');

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);

        $this->dbforge->drop_column('mg_game_logs', 'player_id');
        $this->dbforge->drop_column('mg_game_logs', 'user_name');

        $this->dbforge->drop_column('game_logs', 'sync_index');
    }
}