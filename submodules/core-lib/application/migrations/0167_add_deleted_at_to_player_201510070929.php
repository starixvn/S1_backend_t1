<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_deleted_at_to_player_201510070929 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'deleted_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'deleted_at');
	}
}