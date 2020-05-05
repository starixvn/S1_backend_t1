<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_amount_in_responsible_gaming_201606180319 extends CI_Migration {
	private $tableName = 'responsible_gaming';
	public function up() {
		$fields = array(
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'amount');
	}
}