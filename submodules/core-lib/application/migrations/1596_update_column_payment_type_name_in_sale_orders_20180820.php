<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_payment_type_name_in_sale_orders_20180820 extends CI_Migration {
    private $tableName = 'sale_orders';

    public function up() {
        //modify column
        $fields = array(
            'payment_type_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '1000',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {

    }
}
