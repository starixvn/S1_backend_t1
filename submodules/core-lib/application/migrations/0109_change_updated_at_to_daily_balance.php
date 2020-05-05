<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_updated_at_to_daily_balance extends CI_Migration {

	private $tableName = 'daily_balance';

	public function up() {

		$this->dbforge->modify_column($this->tableName, array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));

	}

	public function down() {
	}
}
///END OF FILE