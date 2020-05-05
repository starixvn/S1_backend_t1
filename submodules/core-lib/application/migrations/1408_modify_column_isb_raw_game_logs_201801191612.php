<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_isb_raw_game_logs_201801191612 extends CI_Migration {

    private $tableName = 'isb_raw_game_logs';

    public function up() {
        $fields = array(
            'roundid' => array(
                'type' => 'varchar',
                'constraint' => 100,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {

        // $fields = array(
        //     'roundid' => array(
        //         'type' => 'varchar',
        //         'constraint' => 45,
        //     ),
        // );
        // $this->dbforge->modify_column($this->tableName, $fields);
    }
}