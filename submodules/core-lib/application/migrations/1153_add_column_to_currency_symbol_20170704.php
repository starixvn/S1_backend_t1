<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_currency_symbol_20170704 extends CI_Migration {

    private $tableName = 'currency';

    public function up() {
        $fields = array(
            'currencySymbol' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
                'default' => '¥'
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'currencySymbol');
    }
}