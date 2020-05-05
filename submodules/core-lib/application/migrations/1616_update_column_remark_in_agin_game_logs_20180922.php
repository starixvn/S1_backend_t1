<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_remark_in_agin_game_logs_20180922 extends CI_Migration {
    private $tableName = 'agin_game_logs';

    public function up() {
        //modify column
        $fields = array(
            'remark' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {

    }
}
