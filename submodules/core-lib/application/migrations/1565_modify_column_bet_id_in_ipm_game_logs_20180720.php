<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_bet_id_in_ipm_game_logs_20180720 extends CI_Migration {
    private $tableName = 'ipm_game_logs';

    public function up() {
        //modify column
        $fields = array(
            'betId' => array(
                'type' => 'BIGINT',
                'null' => false,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {

    }
}
