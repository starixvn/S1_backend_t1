<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_trans_id_fg_game_logs_20180906 extends CI_Migration {
    private $tableName = 'fg_game_logs';

    public function up() {
        //modify column
        $fields = array(
            'trans_id' => array(
                'type' => 'bigint',
                'null' => false,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {}
}
