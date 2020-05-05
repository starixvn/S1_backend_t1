<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201707191553 extends CI_Migration {

	private $tableName = 'vipsettingcashbackrule';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'cashback_backtracking_time_length' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'cashback_backtracking_time_length');
	}
}