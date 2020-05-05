<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transactions_201610310137 extends CI_Migration {

    public function up() {
        $fields = array(
            'ignore_promotion_check' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column('transactions', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('transactions', 'ignore_promotion_check');
    }
}
