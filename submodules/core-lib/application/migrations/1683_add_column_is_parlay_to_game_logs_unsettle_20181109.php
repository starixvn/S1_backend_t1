<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_is_parlay_to_game_logs_unsettle_20181109 extends CI_Migration {

    private $tableName = 'game_logs_unsettle';

    public function up() {
        $fields = array(
            'is_parlay' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'is_parlay');
    }
}