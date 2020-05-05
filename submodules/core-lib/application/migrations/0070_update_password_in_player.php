<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_password_in_player extends CI_Migration {

	public function up() {
		//default password is 159357
		$this->db->query("UPDATE player SET password = 'c3b3enEfLOU=' where playerId <= 110");
	}

	public function down() {
		//$this->db->query('DELETE FROM `player`');
	}
}
