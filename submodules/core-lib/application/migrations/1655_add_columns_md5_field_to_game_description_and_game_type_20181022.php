<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_md5_field_to_game_description_and_game_type_20181022 extends CI_Migration {

	private $tables = ['game_description','game_type'];

	public function up() {
		$fields = array(
			'md5_fields' => array(
				'type' => 'varchar',
				'constraint' => '32',
				'null' => true,
			),
			'deleted_at' => array(
				'type' => 'datetime',
				'null' => true,
			),

		);

		foreach ($this->tables as $tableName) {
			$this->dbforge->add_column($tableName, $fields);
		}
	}

	public function down() {
		foreach ($this->tables as $tableName) {
			$this->dbforge->drop_column($tableName, 'md5_fields');
			$this->dbforge->drop_column($tableName, 'deleted_at');
		}
	}
}
