<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliatepaymenthistory_201605061325 extends CI_Migration {

	private $tableName = 'affiliatepaymenthistory';

	public function up() {

		$this->dbforge->modify_column($this->tableName, [
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		]);

		$this->dbforge->add_column($this->tableName, [
			'processedOn' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'processedBy' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'processedOn');
		$this->dbforge->drop_column($this->tableName, 'processedBy');
	}
}