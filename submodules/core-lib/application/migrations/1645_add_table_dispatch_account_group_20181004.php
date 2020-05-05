<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dispatch_account_group_20181004 extends CI_Migration {

	private $tableName = 'dispatch_account_group';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => true,
				'auto_increment' => true,
			),
			'group_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'group_level_count' => array(
				'type' => 'INT',
				'null' => false,
			),
			'group_description' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
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
