<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_dealid_for_n2live_game_logs_20190719 extends CI_Migration {
    
    private $tableName = 'n2live_game_logs';

    public function up() {
        $fields = array(
            'dealid' => array(
                'type' => 'INT',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'dealid');
    }
}
