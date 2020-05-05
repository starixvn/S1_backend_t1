<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_banner_201607290151 extends CI_Migration {

	private $tableName = 'banner';

	public function up() {
		$fields = array(
			'file_ext' => array(
				'type' => 'VARCHAR',
				'constraint'=>50,
				'null' => true,
			),
			'last_edit_user' => array(
				'type' => 'INT',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'file_ext');
		$this->dbforge->drop_column($this->tableName, 'last_edit_user');
	}
}
