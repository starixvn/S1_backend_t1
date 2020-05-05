<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_status_to_t1lottery_game_logs_20180312 extends CI_Migration
{
    private $tableName = 't1lottery_game_logs';

    public function up()
    {
        $fields = array(
            'status' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'status');
    }
}
