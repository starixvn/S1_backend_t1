<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_exception_order_20191030 extends CI_Migration {

    private $tableName = 'exception_order';

    public function up() {

        $fields = array(   
            'withdrawal_order_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'remarks' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );

        if($this->db->table_exists($this->tableName)){

            if(! $this->db->field_exists('withdrawal_order_id', $this->tableName) && ! $this->db->field_exists('remarks', $this->tableName)){
                $this->dbforge->add_column($this->tableName, $fields);
            }

        }

    }

    public function down() {

       if($this->db->field_exists('withdrawal_order_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'withdrawal_order_id');
        }

        if($this->db->field_exists('remarks', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'remarks');
        }

    }
}