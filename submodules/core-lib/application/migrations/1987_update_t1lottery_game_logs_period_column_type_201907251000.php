<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_t1lottery_game_logs_period_column_type_201907251000 extends CI_Migration {
    private $tableName = 't1lottery_game_logs';

    public function up() {
        //modify column size
        $fields = array(
            'period' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        // not able to rollback due to data type incompatibility
    }
}
