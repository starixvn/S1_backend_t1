<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sales_orders_201703141153 extends CI_Migration {

    protected $tableName = "sale_orders";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'locked_user_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'locked_user_id');
    }

}

