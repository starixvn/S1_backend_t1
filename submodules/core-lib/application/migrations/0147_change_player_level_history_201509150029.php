<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_player_level_history_201509150029 extends CI_Migration {

	private $tableName = 'player_level_history';

	public function up() {

		$this->db->query('ALTER TABLE player_level_history CHANGE COLUMN id id INT(11) NOT NULL AUTO_INCREMENT ');
		$this->db->query('ALTER TABLE player_level_history_game_api_details CHANGE COLUMN id id INT(11) NOT NULL AUTO_INCREMENT ');

	}

	public function down() {
		// $this->dbforge->drop_table($this->tableName);
		// $this->dbforge->drop_table('player_level_history_game_api_details');
	}
}