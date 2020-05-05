<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_vipsettingcashbackrule_201510201147 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		//modify column
		$fields = array(
			'bonus_mode' => array(
				'name' => 'bonus_mode_cashback',
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);

		//add column
		$this->dbforge->add_column($this->tableName, [
			'bonus_mode_deposit' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$fields = array(
			'bonus_mode_cashback' => array(
				'name' => 'bonus_mode',
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
		$this->dbforge->drop_column($this->tableName, 'bonus_mode_deposit');
	}
}