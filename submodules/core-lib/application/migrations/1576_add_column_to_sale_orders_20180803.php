<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sale_orders_20180803 extends CI_Migration {

    private $tableName = 'sale_orders';

    public function up() {
        $fields = array(
            'deposit_receipt_filepath' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('deposit_receipt_filepath', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('deposit_receipt_filepath', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'deposit_receipt_filepath');
        }
    }
}