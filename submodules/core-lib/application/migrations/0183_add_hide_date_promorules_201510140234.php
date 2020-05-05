<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_hide_date_promorules_201510140234 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'hide_date' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'hide_date');
	}
}
