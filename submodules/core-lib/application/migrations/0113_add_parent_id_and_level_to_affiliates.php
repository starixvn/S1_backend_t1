<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_parent_id_and_level_to_affiliates extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'parentId' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
			'level' => array(
				'type' => 'INT',
				'null' => true,
			),
			'validatedEmail' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'parentId');
		$this->dbforge->drop_column($this->tableName, 'level');
		$this->dbforge->drop_column($this->tableName, 'validatedEmail');
	}
}
///END OF FILE