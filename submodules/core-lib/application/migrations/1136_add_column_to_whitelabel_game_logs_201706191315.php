<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_whitelabel_game_logs_201706191315 extends CI_Migration {

    private $tableName = 'whitelabel_game_logs';

    public function up() {
        $fields = array(
            'subBet' => array(
                'type' => 'VARCHAR',
                'constraint' => '1100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'subBet');
    }
}