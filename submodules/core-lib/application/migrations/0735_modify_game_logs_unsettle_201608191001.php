<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_game_logs_unsettle_201608191001 extends CI_Migration {

	public function up() {
		$this->db->query('alter table game_logs_unsettle modify column id bigint not null AUTO_INCREMENT');
	}

	public function down() {
	}
}
