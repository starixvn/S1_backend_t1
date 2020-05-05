<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_lottery_lezhi_user_exchange_201708282126 extends CI_Migration {

    private $tableName = 'lottery_lezhi_user_exchange';

    public function up() {
        $fields = array(
            'before_cost' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            )
        );

		if (!$this->db->field_exists('before_cost', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'num');
		}
        
        $fields = array(
            'after_cost' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            )
        );

		if (!$this->db->field_exists('after_cost', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'before_cost');
		}
    }

    public function down() {
		if ($this->db->field_exists('before_cost', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'before_cost');
		}

		if ($this->db->field_exists('after_cost', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'after_cost');
		}
    } 
}