<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_rwb_game_transactions_20180406 extends CI_Migration
{
    private $tableName = 'rwb_game_transactions';

    public function up()
    {
        $fields = array(
            'status' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'balance' => array(
                'type' => 'double',
                'null' => true,
            ),
            'is_failed' => array(
                'type' => 'SMALLINT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'status');
        $this->dbforge->drop_column($this->tableName, 'balance');
        $this->dbforge->drop_column($this->tableName, 'is_failed');
    }
}
