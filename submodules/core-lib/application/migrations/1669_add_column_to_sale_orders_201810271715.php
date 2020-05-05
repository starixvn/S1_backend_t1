<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sale_orders_201810271715 extends CI_Migration {

    private $tableName = 'sale_orders';

    public function up() {
        $fields = [
            'is_notify' => [
                'type' => 'TINYINT',
                'null' => false,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('is_notify', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('is_notify', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_notify');
        }
    }
}