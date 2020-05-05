<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_sa_game_logs_20190115 extends CI_Migration {

    private $tableName = 'sagaming_game_logs';
    private $playerId  = 'PlayerId';

    public function up() {

        $update_fields = array(
            $this->playerId => array(
                'type' => 'INT',               
                'null' => true,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
    }

    public function down() {}
}
