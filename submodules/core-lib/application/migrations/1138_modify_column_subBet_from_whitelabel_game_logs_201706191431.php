<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_subBet_from_whitelabel_game_logs_201706191431 extends CI_Migration {

    private $tableName = 'whitelabel_game_logs';

    public function up() {
        $fields = array(
            'subBet' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->modify_column($this->tableName, 'subBet');
    }
}