<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_jumb_game_logs_201706222019 extends CI_Migration {

    private $tableName = 'jumb_game_logs';

    public function up() {
        $fields = array(
            'roomType' => array(
                'type' => 'int',
                'constraint' => '11',
                'null' => true,
            ),
            'beforeBalance' => array(
                'type' => 'double',
                'null' => true,
            ),
            'afterBalance' => array(
                'type' => 'double',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}