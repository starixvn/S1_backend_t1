<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_play_value_in_ld_lottery_game_logs_20180904 extends CI_Migration {
    private $tableName = 'ld_lottery_game_logs';

    public function up() {
        //modify column
        $fields = array(
            'play_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {}
}
