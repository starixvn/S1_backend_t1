<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_PlayerId_in_spadegaming_game_logs_20190813 extends CI_Migration {

    private $tableName = 'spadegaming_game_logs';

    public function up() {

        $update_fields = array(
            'PlayerId' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
        
    }

    public function down() {}
}
