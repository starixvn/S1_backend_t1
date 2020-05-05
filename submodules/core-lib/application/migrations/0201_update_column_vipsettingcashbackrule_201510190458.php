<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_vipsettingcashbackrule_201510190458 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$fields = array(
			'fsucceeding_dep_type' => array(
				'name' => 'succeeding_dep_type',
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
		$fields = array(
			'succeeding_dep_type' => array(
				'name' => 'fsucceeding_dep_type',
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}