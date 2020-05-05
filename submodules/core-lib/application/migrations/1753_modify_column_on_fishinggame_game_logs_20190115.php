<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_fishinggame_game_logs_20190115 extends CI_Migration {

    private $tableName = 'fishinggame_game_logs';
    private $playerId  = 'PlayerId';
    private $userName  = 'Username';

    public function up() {

        $update_fields = array(
            $this->playerId => array(
                'type' => 'INT',               
                'null' => true,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
    }

    public function down() {
        $update_fields = array(
            $this->playerId => array(
                'type' => 'INT',               
                'null' => FALSE,
            ),
        );

        $this->dbforge->modify_column($this->tableName, $update_fields);
    }
}
