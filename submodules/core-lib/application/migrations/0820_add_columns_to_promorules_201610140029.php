<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promorules_201610140029 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'hide_if_not_allow' => array(
				'type' => 'INT',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'hide_if_not_allow');
	}
}
