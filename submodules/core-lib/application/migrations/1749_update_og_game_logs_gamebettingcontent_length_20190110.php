<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_og_game_logs_gamebettingcontent_length_20190110 extends CI_Migration {

	private $tableName = 'og_game_logs';

	public function up() {
		$fields = array(
			'GameBettingContent' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
	}
}