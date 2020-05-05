<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_description_201604270137 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'related_game_desc_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
		$this->dbforge->add_column('game_type', [
			'related_game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'related_game_desc_id');
		$this->dbforge->drop_column('game_type', 'related_game_type_id');
	}
}