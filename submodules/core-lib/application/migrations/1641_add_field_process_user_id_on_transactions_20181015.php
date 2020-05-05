<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_process_user_id_on_transactions_20181015 extends CI_Migration {

	private $tableName = 'transactions';

	public function up() {
        $field = array(
            'process_user_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('process_user_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }
	}

	public function down() {
        if($this->db->field_exists('process_user_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'process_user_id');
        }
	}
}