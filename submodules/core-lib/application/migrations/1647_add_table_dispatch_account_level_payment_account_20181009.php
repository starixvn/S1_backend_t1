<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dispatch_account_level_payment_account_20181009 extends CI_Migration {

	private $tableName = 'dispatch_account_level_payment_account';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => true,
				'auto_increment' => true,
			),
			'dispatch_account_level_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => false,
			)
		);

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
        }
	}

	public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
	}
}
