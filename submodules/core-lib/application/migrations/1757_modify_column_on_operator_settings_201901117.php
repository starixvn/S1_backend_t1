<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_on_operator_settings_201901117 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		$fields = array(
			'value' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
	}
}