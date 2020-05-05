<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_mg_game_logs_20190306 extends CI_Migration {

    private $tableName = 'mg_game_logs';

    public function up() {

        $update_fields = array(
            'iso_code' => array(
                'type' => 'varchar',
                'constraint' => '50',               
                'null' => true,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
    }

    public function down() {}
}
