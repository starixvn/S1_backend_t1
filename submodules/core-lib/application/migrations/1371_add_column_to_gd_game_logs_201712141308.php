<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gd_game_logs_201712141308 extends CI_Migration {

    private $tableName = 'gd_game_logs';

    public function up() {
        $fields = array(
            'game_id' => array(
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'game_id');
    }
}
