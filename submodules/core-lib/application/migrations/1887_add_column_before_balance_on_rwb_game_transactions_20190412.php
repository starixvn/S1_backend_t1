<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_before_balance_on_rwb_game_transactions_20190412 extends CI_Migration {

    private $tableName = 'rwb_game_transactions';

    public function up()
    {
        # Add column
        $fields = array(
            'before_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'before_balance');
    }
}
