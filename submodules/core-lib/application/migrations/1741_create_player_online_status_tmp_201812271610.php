<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_player_online_status_tmp_201812271610 extends CI_Migration {

    private $tableName = 'player_online_status_tmp';

    public function up() {
        $fields = array(
            'playerId' => array(
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ),
            'online' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('playerId', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}