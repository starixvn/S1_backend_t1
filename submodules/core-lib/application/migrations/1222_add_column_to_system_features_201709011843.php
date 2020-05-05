<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_system_features_201709011843 extends CI_Migration
{
    private $tableName = 'system_features';

    public function up() 
    {
        $fields = array(
            'type' => array(
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => null,
                'null'       => false
            )
        );

        if (!$this->db->field_exists('type', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields, 'id');
		}
    }

    public function down()
    {
        if ($this->db->field_exists('type', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'type');
		}
    } 
}