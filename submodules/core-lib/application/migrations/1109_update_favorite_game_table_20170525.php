<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_favorite_game_table_20170525 extends CI_Migration {

	public function up() {
		$this->db->query('ALTER TABLE favorite_game CHANGE COLUMN name name VARCHAR(255) NOT NULL , CHANGE COLUMN image image VARCHAR(255) NOT NULL , CHANGE COLUMN url url VARCHAR(255) NOT NULL');
	}

	public function down() {
	}
	
}