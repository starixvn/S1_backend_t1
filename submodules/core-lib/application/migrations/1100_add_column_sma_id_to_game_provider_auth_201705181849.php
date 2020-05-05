<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_sma_id_to_game_provider_auth_201705181849 extends CI_Migration {

	private $tableName = 'game_provider_auth';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'sma_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'sma_id');
	}
}