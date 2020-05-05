<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_description_20151008 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'html_five_enabled' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'html_five_enabled');
	}
}