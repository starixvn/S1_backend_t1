<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_verified_email_to_player_201511012032 extends CI_Migration {

	private $tableName = 'player';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'verified_email' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		));
	}

	public function down() {
		$this->dbforget->drop_column($this->tableName, 'verified_email');
	}
}