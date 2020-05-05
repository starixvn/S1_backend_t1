<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201811141318 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'total_approved_limit' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0
            )
        ];

        if(!$this->db->field_exists('total_approved_limit', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('total_approved_limit', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'total_approved_limit');
        }
    }
}