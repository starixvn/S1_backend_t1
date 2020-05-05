<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_payment_account_201610271520 extends CI_Migration {

    private $tableName = "payment_account";

    public function up() {
        $fields = array(
            'min_deposit_amount_for_bonus' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'auto_bonus_percentage' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'min_deposit_amount_for_bonus');
        $this->dbforge->drop_column($this->tableName, 'auto_bonus_percentage');
    }
}
