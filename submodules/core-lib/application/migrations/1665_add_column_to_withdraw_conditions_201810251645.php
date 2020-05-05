<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdraw_conditions_201810251645 extends CI_Migration {

    private $tableName = 'withdraw_conditions';

    public function up() {
        $fields = [
            'withdraw_condition_type' => [
                'type' => 'TINYINT',
                'null' => false,
                'default' => 1
            ],
        ];

        if(!$this->db->field_exists('withdraw_condition_type', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('withdraw_condition_type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'withdraw_condition_type');
        }
    }
}