<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_odds_type_to_game_logs_20180521 extends CI_Migration {

    private $tableName = 'game_logs';

    public function up() {
        $fields = array(
            'odds_type' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'odds_type');
    }
}