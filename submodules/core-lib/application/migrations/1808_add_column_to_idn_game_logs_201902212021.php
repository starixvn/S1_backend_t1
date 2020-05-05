<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_idn_game_logs_201902212021 extends CI_Migration {

    private $tableName='idn_game_logs';

    public function up() {
        $fields = array(
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
    }
}