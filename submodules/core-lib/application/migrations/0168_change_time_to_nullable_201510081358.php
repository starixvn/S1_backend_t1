<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_time_to_nullable_201510081358 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->modify_column($this->tableName, [
			'lastActivityTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'lastLogoutTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'lastLoginTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		]);
	}

	public function down() {
		// $this->dbforge->drop_column($this->tableName, 'external_account_id');
	}
}