<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sale_orders_201610210825 extends CI_Migration {

    private $tableName = "sale_orders";

    public function up() {
        $fields = array(
            'player_deposit_method' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_deposit_method');
    }
}
