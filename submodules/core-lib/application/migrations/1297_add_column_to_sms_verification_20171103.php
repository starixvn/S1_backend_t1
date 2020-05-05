<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_sms_verification_20171103 extends CI_Migration
{
    private $tableName = 'sms_verification';

    public function up() {
        $fields = array(
            'playerId' => array(
                'type' => 'INT',
                'null' => true,
            )
        );

		if (!$this->db->field_exists('playerId', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
    }

    public function down() {
		if ($this->db->field_exists('playerId', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'playerId');
		}
    }
}