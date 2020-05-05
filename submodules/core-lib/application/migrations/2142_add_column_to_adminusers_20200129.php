<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_add_column_to_adminusers_20200129 extends CI_Migration
{
    private $tableName = 'adminusers';

    public function up() {

        $fields = array(
            'secure_key' => array(
                'type' => 'varchar',
                'constraint' => '64',
                'null' => true,
            ),
            'sign_key' => array(
                'type' => 'varchar',
                'constraint' => '64',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('secure_key', $this->tableName) && !$this->db->field_exists('sign_key', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('secure_key', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'secure_key');
        }
        if($this->db->field_exists('sign_key', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sign_key');
        }
    }
}