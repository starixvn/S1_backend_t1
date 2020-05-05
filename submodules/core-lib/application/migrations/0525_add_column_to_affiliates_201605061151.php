<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliates_201605061151 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'frozen' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'frozen');
	}
}