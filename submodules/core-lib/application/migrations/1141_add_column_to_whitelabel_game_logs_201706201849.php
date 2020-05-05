<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_whitelabel_game_logs_201706201849 extends CI_Migration {

    private $tableName = 'whitelabel_game_logs';

    public function up() {
        $fields = array(
            'doneTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'doneTime');
    }
}