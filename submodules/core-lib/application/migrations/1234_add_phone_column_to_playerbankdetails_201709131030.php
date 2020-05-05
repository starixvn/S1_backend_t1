<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_phone_column_to_playerbankdetails_201709131030 extends CI_Migration
{
    private $tableName = 'playerbankdetails';

    public function up() {
        $fields = array(
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true
                #'after' => 'branch' # this is not working in current migration
            )
        );

		if (!$this->db->field_exists('phone', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
    }

    public function down() {
		if ($this->db->field_exists('phone', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'phone');
		}
    }
}