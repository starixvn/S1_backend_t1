<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_comapi_game_events_20180409 extends CI_Migration {

    private $tableName = 'comapi_game_events';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'site' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'int',
                'null' => true,
            ),
            'player_id' => array(
                'type' => 'int',
                'null' => true,
            ),
            'game_sess_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'event' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'data' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}