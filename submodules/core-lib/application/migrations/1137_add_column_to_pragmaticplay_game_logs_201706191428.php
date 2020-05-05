<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pragmaticplay_game_logs_201706191428 extends CI_Migration {

    private $tableName = 'pragmaticplay_game_logs';

    public function up() {
        $fields = array(
            'related_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'related_uniqueid');
    }
}