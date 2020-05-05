<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201811151345 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'allowed_scope_condition' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => true,
                'default' => 0 // 0: or, 1: and
            )
        ];

        if(!$this->db->field_exists('allowed_scope_condition', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('allowed_scope_condition', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'allowed_scope_condition');
        }
    }
}