<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_sale_orders_201807031807 extends CI_Migration {

    public function up() {

        $fields = array(
            'created_at' => array(
                'name'=>'created_at',
                'type' => 'DATETIME',
                'null' => false,
            ),
        );
        $this->dbforge->modify_column('sale_orders', $fields);

    }

    public function down() {

    }
}
