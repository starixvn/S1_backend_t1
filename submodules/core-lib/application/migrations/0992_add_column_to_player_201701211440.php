<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201701211440 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $fields = array(
            'is_synced_on_iovation' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'is_synced_on_iovation');
    }
}