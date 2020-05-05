<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_created_at_to_mg_quickfire_game_logs_20180309 extends CI_Migration
{
    private $tableName = 'mg_quickfire_game_logs';

    public function up()
    {
        $fields = array(
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'created_at');
    }
}
