<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pinnacle_game_logs_201707251304 extends CI_Migration {

    private $tableName = 'pinnacle_game_logs';

    public function up() {
        $fields = array(
            'settledDate' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'settledDate');
    }
}