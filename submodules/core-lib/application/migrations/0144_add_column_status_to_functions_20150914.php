<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_status_to_functions_20150914 extends CI_Migration {

	private $tableName = 'functions';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			"status" => array(
				'type' => 'INT',
				'constraint' => '1',
				'null' => false,
				'default' => '1',
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'status');
	}
}