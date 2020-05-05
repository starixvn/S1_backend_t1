<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_le_gaming_game_logs_20180905 extends CI_Migration {
    private $tableName = 'le_gaming_game_logs';

    public function up() {
        //modify column
        $fields = array(
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
        $this->dbforge->drop_column($this->tableName, 'created_at');
        $this->dbforge->drop_column($this->tableName, 'updated_at');
    }

    public function down() {
        $fields = array(
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
    }
}
