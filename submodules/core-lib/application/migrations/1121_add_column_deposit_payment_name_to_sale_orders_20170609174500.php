<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_deposit_payment_name_to_sale_orders_20170609174500 extends CI_Migration {

    public function up() {
        $fields = array(
            'deposit_payment_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        );

        $this->dbforge->add_column('sale_orders', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('sale_orders', 'deposit_payment_name');
    }
}