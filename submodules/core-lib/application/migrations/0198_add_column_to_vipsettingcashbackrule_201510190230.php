<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_vipsettingcashbackrule_201510190230 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'firsttime_dep_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'firsttime_dep_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'firsttime_dep_withdraw_condition' => array(
				'type' => 'INT',
				'null' => true,
			),
			'succeeding_dep_bonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'fsucceeding_dep_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'succeeding_dep_withdraw_condition' => array(
				'type' => 'INT',
				'null' => true,
			),
			'bonus_mode' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'firsttime_dep_bonus');
		$this->dbforge->drop_column($this->tableName, 'firsttime_dep_type');
		$this->dbforge->drop_column($this->tableName, 'firsttime_dep_withdraw_condition');
		$this->dbforge->drop_column($this->tableName, 'succeeding_dep_bonus');
		$this->dbforge->drop_column($this->tableName, 'fsucceeding_dep_type');
		$this->dbforge->drop_column($this->tableName, 'succeeding_dep_withdraw_condition');
		$this->dbforge->drop_column($this->tableName, 'bonus_mode');
	}
}