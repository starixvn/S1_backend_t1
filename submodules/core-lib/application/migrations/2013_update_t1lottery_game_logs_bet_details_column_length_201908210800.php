<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_t1lottery_game_logs_bet_details_column_length_201908210800 extends CI_Migration {
    private $tableName = 't1lottery_game_logs';

    public function up() {
        //modify column size
        $fields = array(
            'bet_details' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
