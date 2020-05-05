<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_formula_to_promorules_201603130121 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'formula' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'formula');
	}
}
