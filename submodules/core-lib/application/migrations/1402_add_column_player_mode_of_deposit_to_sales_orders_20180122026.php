<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_player_mode_of_deposit_to_sales_orders_20180122026 extends CI_Migration {

    protected $tableName = "sale_orders";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'player_mode_of_deposit' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_mode_of_deposit');
    }

}

