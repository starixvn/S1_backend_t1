<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_agin_game_logs_result_20180510 extends CI_Migration {

    private $tableName = 'agin_game_logs_result';

    public function up() {
        //modify column
        $fields = array(
           'card_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}