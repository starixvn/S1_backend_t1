<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_is_manual_adjustment_on_transactions_20181012 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {
        $field = array(
            'is_manual_adjustment' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('is_manual_adjustment', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }
	}

	public function down() {
        if($this->db->field_exists('is_manual_adjustment', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_manual_adjustment');
        }
	}
}