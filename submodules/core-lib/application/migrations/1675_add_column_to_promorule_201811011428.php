<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorule_201811011428 extends CI_Migration{
    private $tableName = 'promorules';

    public function up(){
        $fields = [
            'donot_allow_any_available_bet_after_deposit ' => [
                'type' => 'INT',
                'default' => 0,
                'null' => FALSE,
            ],
        ];
        if(!$this->db->field_exists('donot_allow_any_available_bet_after_deposit', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down(){
        if($this->db->field_exists('donot_allow_any_available_bet_after_deposit', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'donot_allow_any_available_bet_after_deposit');
        }
    }
}
