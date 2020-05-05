<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_vipsettingcashbackrule_201510190326 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'firsttime_dep_percentage_upto' => array(
				'type' => 'INT',
				'null' => true,
			),
			'succeeding_dep_percentage_upto' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'firsttime_dep_percentage_upto');
		$this->dbforge->drop_column($this->tableName, 'succeeding_dep_percentage_upto');
	}
}