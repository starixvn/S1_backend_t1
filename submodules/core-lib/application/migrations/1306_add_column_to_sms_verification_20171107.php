<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sms_verification_20171107 extends CI_Migration
{
    private $tableName = 'sms_verification';

    public function up() {
        $fields = array(
            'restrict_area' => array(
                'type' => 'INT',
                'null' => true,
            )
        );

		if (!$this->db->field_exists('restrict_area', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
    }

    public function down() {
		if ($this->db->field_exists('restrict_area', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'restrict_area');
		}
    }
}