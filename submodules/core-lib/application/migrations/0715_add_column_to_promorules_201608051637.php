<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201608051637 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'donot_allow_other_promotion' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'donot_allow_other_promotion');
	}

}
