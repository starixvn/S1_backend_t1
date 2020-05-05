<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_logs_stream_201808120135 extends CI_Migration {

    private $tableName = 'game_logs_stream';

    public function up() {
        $fields = array(
            'status' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'status');
    }
}
