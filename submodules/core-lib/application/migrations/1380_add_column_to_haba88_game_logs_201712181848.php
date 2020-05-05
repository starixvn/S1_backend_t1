<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_haba88_game_logs_201712181848 extends CI_Migration {

    private $tableName = 'haba88_game_logs';

    public function up() {
        $fields = array(
            'BalanceAfter' => array(
                'type' => 'DOUBLE',
                'null' => true
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'BalanceAfter');
    } 
}