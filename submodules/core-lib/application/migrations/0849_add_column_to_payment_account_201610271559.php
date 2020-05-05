<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_payment_account_201610271559 extends CI_Migration {

    private $tableName = "payment_account";

    public function up() {
        $fields = array(
            'bonus_bet_times' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'deposit_fee_percentage' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'bonus_bet_times');
        $this->dbforge->drop_column($this->tableName, 'deposit_fee_percentage');
    }
}
