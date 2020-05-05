<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_isAdmin_to_roles_201605140000 extends CI_Migration {

	private $tableName = 'roles';

	public function up() {
		$fields = array(
			'isAdmin' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => false,
				'default' => '0',
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		$data = array('isAdmin' => 1);
		$this->db->where('roleId', 1); # Default admin: ID = 1
		$this->db->update($this->tableName, $data);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'isAdmin');
	}
}